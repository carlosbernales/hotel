<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

// Function to update package status when booking ends
function updatePackageStatusOnEnd($packageName) {
    global $pdo;
    
    try {
        // Check if there are any active bookings for the package
        $checkBookingsSQL = "SELECT COUNT(*) as active_bookings 
                            FROM event_bookings 
                            WHERE package_name = :package_name 
                            AND booking_status IN ('pending', 'confirmed')
                            AND reservation_date = CURRENT_DATE
                            AND end_time > NOW()";
        
        $stmt = $pdo->prepare($checkBookingsSQL);
        $stmt->execute(['package_name' => $packageName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no active bookings, update package status to Available
        if ($result['active_bookings'] == 0) {
            $updateSQL = "UPDATE event_packages 
                         SET status = 'Available', is_available = 1 
                         WHERE name = :package_name";
            $stmt = $pdo->prepare($updateSQL);
            $stmt->execute(['package_name' => $packageName]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating package status: " . $e->getMessage());
        return false;
    }
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$json = file_get_contents('php://input');
$input = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit();
}

// Define required fields
$required_fields = [
    'customer_name', 'package_name', 'package_price', 'base_price',
    'total_amount', 'paid_amount', 'remaining_balance', 'reservation_date',
    'event_date', 'start_time', 'end_time', 'number_of_guests',
    'payment_method', 'payment_type', 'event_type'
];

$data = [];
$errors = [];

// Validate required fields
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
        $errors[] = "$field is required";
    } else {
        $data[$field] = is_string($input[$field]) ? trim($input[$field]) : $input[$field];
    }
}

// Validate numeric fields
$numeric_fields = [
    'package_price', 'base_price', 'total_amount', 'paid_amount', 'remaining_balance',
    'number_of_guests', 'overtime_hours', 'overtime_charge', 'extra_guests', 'extra_guest_charge'
];

foreach ($numeric_fields as $field) {
    if (isset($input[$field])) {
        if (!is_numeric($input[$field])) {
            $errors[] = "$field must be a number";
        } else {
            $data[$field] = (float)$input[$field];
        }
    }
}

// Set default values for optional fields
$data['overtime_hours'] = $data['overtime_hours'] ?? 0;
$data['overtime_charge'] = $data['overtime_charge'] ?? 0.00;
$data['extra_guests'] = $data['extra_guests'] ?? 0;
$data['extra_guest_charge'] = $data['extra_guest_charge'] ?? 0.00;
$data['user_id'] = $_SESSION['user_id'] ?? null;
$data['booking_status'] = 'pending';
$data['reserve_type'] = $data['reserve_type'] ?? 'Regular';
$data['booking_source'] = $data['booking_source'] ?? 'Website Booking';

// Ensure all required fields have values
$requiredFields = [
    'customer_name', 'package_name', 'package_price', 'base_price',
    'total_amount', 'paid_amount', 'remaining_balance', 'reservation_date',
    'event_date', 'start_time', 'end_time', 'number_of_guests',
    'payment_method', 'payment_type', 'event_type'
];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        $errors[] = "Required field missing: $field";
    }
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors]);
    exit();
}

try {
    // Prepare the SQL query
    $sql = "INSERT INTO event_bookings (
        user_id, customer_name, package_name, package_price, base_price,
        reservation_date, event_date, start_time, end_time, 
        number_of_guests, payment_method, payment_type,
        total_amount, paid_amount, remaining_balance, booking_status,
        event_type, booking_source, overtime_hours,
        overtime_charge, extra_guests, extra_guest_charge
    ) VALUES (
        :user_id, :customer_name, :package_name, :package_price, :base_price,
        :reservation_date, :event_date, :start_time, :end_time,
        :number_of_guests, :payment_method, :payment_type,
        :total_amount, :paid_amount, :remaining_balance, :booking_status,
        :event_type, :booking_source, :overtime_hours,
        :overtime_charge, :extra_guests, :extra_guest_charge
    )";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception('Failed to prepare SQL statement: ' . implode(' ', $pdo->errorInfo()));
    }
    
    // Calculate duration in hours
    $start = new DateTime($data['start_time']);
    $end = new DateTime($data['end_time']);
    $duration = $start->diff($end)->h + ($start->diff($end)->i / 60);

    // Bind and execute the statement
    $bindResult = $stmt->execute([
        ':user_id' => $data['user_id'] ?? null,
        ':customer_name' => $data['customer_name'],
        ':package_name' => $data['package_name'],
        ':package_price' => $data['package_price'],
        ':base_price' => $data['base_price'],
        ':reservation_date' => $data['reservation_date'],
        ':event_date' => $data['event_date'],
        ':start_time' => $data['start_time'],
        ':end_time' => $data['end_time'],
        ':number_of_guests' => $data['number_of_guests'],
        ':payment_method' => $data['payment_method'],
        ':payment_type' => $data['payment_type'],
        ':total_amount' => $data['total_amount'],
        ':paid_amount' => $data['paid_amount'],
        ':remaining_balance' => $data['remaining_balance'],
        ':booking_status' => $data['booking_status'],
        ':event_type' => $data['event_type'],
        ':booking_source' => $data['booking_source'],
        ':overtime_hours' => $data['overtime_hours'],
        ':overtime_charge' => $data['overtime_charge'],
        ':extra_guests' => $data['extra_guests'],
        ':extra_guest_charge' => $data['extra_guest_charge']
    ]);

    if ($bindResult) {
        $bookingId = $pdo->lastInsertId();
        
        // Update package status if booking has ended
        $bookingEndTime = $data['end_time'];
        if (strtotime($bookingEndTime) <= time()) {
            updatePackageStatusOnEnd($data['package_name']);
        }
        
        // Update the package availability for the specific date
        $updatePackageStmt = $pdo->prepare("
            UPDATE event_packages 
            SET is_available = 0, status = 'Occupied'
            WHERE name = :package_name
        ");
        $updatePackageStmt->execute([':package_name' => $data['package_name']]);
        
        // Send booking confirmation email
        try {
            require_once 'includes/Mailer.php';
            $mailer = new Mailer();
            
            // Prepare email data
            // Get user email from database if not provided in input
            $userEmail = $input['email'] ?? '';
            if (empty($userEmail) && !empty($data['user_id'])) {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                $stmt->execute([$data['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && !empty($user['email'])) {
                    $userEmail = $user['email'];
                }
            }
            
            $emailData = [
                'first_name' => explode(' ', $data['customer_name'])[0],
                'last_name' => implode(' ', array_slice(explode(' ', $data['customer_name']), 1)),
                'email' => $userEmail,
                'event_date' => date('F j, Y', strtotime($data['event_date'])),
                'start_time' => date('g:i A', strtotime($data['start_time'])),
                'end_time' => date('g:i A', strtotime($data['end_time'])),
                'package_name' => $data['package_name'],
                'number_of_guests' => $data['number_of_guests'],
                'total_amount' => $data['total_amount'],
                'paid_amount' => $data['paid_amount'],
                'remaining_balance' => $data['remaining_balance'],
                'payment_method' => $data['payment_method'],
                'payment_type' => $data['payment_type']
            ];
            
            // Send email
            $mailer->sendEventBookingConfirmation($emailData);
            
        } catch (Exception $e) {
            // Log the error but don't fail the booking
            error_log('Failed to send booking confirmation email: ' . $e->getMessage());
        }
        
        // Return success response with booking ID
        echo json_encode([  
            'status' => 'success',
            'message' => 'Booking completed successfully',
            'booking_id' => $bookingId,
            'booking_details' => $data
        ]);
    } else {
        throw new Exception('Failed to insert booking');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
