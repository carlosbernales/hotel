<?php
// Load security helper
require_once 'includes/security_helper.php';

session_start();
require 'db_con.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

// Check if user is logged in and is a customer
if (!isLoggedIn()) {
    $response['message'] = 'You must be logged in to make a booking.';
    $response['redirect'] = 'login.php';
    echo json_encode($response);
    exit();
}

// Validate user role - must be customer
if (getUserRole() !== 'customer') {
    $response['message'] = 'Access denied. Only customers can make bookings.';
    echo json_encode($response);
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $response['message'] = 'Invalid CSRF token. Please try again.';
    echo json_encode($response);
    exit();
}

try {
    // Validate required fields
    $required_fields = [
        'package_id' => 'Package ID',
        'package_name' => 'Package Name',
        'booking_date' => 'Booking Date',
        'booking_time' => 'Booking Time',
        'guest_count' => 'Number of Guests',
        'base_price' => 'Base Price',
        'total_price' => 'Total Price'
    ];

    $missing_fields = [];
    foreach ($required_fields as $field => $name) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $name;
        }
    }

    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }

    // Sanitize and validate input
    $package_id = filter_var($_POST['package_id'], FILTER_VALIDATE_INT);
    $package_name = htmlspecialchars(trim($_POST['package_name']), ENT_QUOTES, 'UTF-8');
    $date = filter_var($_POST['booking_date'], FILTER_SANITIZE_STRING);
    $booking_time = filter_var($_POST['booking_time'], FILTER_SANITIZE_STRING);
    $guest_count = filter_var($_POST['guest_count'], FILTER_VALIDATE_INT);
    $base_price = filter_var($_POST['base_price'], FILTER_VALIDATE_FLOAT);
    $total_price = filter_var($_POST['total_price'], FILTER_VALIDATE_FLOAT);
    $user_id = $_SESSION['user_id'];
    $status = 'pending';
    $created_at = date('Y-m-d H:i:s');
    
    // Additional validation
    if ($guest_count <= 0) {
        throw new Exception('Number of guests must be greater than 0');
    }
    
    if ($base_price < 0 || $total_price < 0) {
        throw new Exception('Invalid price value');
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Insert into table_bookings
        $stmt = $pdo->prepare("INSERT INTO table_bookings (
            user_id, 
            package_id,
            package_name, 
            booking_date, 
            booking_time, 
            guest_count, 
            base_price, 
            total_price, 
            status, 
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([
            $user_id,
            $package_id,
            $package_name,
            $date,
            $booking_time,
            $guest_count,
            $base_price,
            $total_price,
            $status,
            $created_at
        ]);

        $booking_id = $pdo->lastInsertId();

        // Commit the transaction
        $pdo->commit();

        $response = [
            'success' => true,
            'message' => 'Your table booking has been confirmed!',
            'booking_id' => $booking_id,
            'redirect' => 'my_bookings.php?booking_id=' . $booking_id
        ];

    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $response['message'] = 'A database error occurred. Please try again later.';
} catch (Exception $e) {
    error_log('Booking Error: ' . $e->getMessage());
    $response['message'] = $e->getMessage() ?: 'An error occurred while processing your booking.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
