<?php
// Start session and include database connection
session_start();

// Disable display errors to prevent HTML in JSON response
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Function to send JSON response and exit
function sendJsonResponse($success, $message = '', $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Log all POST data
error_log("POST Data received: " . print_r($_POST, true));

// Validate required fields
$requiredFields = [
    'room_type_id', 'roomNumber', 'firstName', 'lastName', 
    'email', 'contactNumber', 'checkOutDate', 'paymentMethod', 'paymentOption'
];

$missingFields = [];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    sendJsonResponse(false, 'Missing required fields: ' . implode(', ', $missingFields), null, 400);
}

// Add PHPMailer imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files with correct paths
require 'phpmailer/PHPMailer-6.8.0/src/Exception.php';
require 'phpmailer/PHPMailer-6.8.0/src/PHPMailer.php';
require 'phpmailer/PHPMailer-6.8.0/src/SMTP.php';

// Database connection configuration
$is_production = strpos($_SERVER['HTTP_HOST'], 'e-akomoda.site') !== false;

if ($is_production) {
    // Production credentials from db.php
    $host = "localhost";
    $username = "u429956055_Eakomoda";
    $password = "Casaestela2025";
    $database = "u429956055_Hotelms";
} else {
    // Local development credentials
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "hotelms";
}

try {
    // Create connection
    $con = mysqli_connect($host, $username, $password, $database);
    
    if (!$con) {
        error_log("Database connection failed: " . mysqli_connect_error());
        throw new Exception("Database connection failed. Please try again later.");
    }

    // Set charset to utf8 (matching db.php)
    mysqli_set_charset($con, "utf8");
    
    // Debug connection
    error_log("Database connected successfully to: " . $database);
    
    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Validate and sanitize input
        $room_type_id = filter_var($_POST['room_type_id'], FILTER_VALIDATE_INT);
        $first_name = mysqli_real_escape_string($con, $_POST['firstName']);
        $last_name = mysqli_real_escape_string($con, $_POST['lastName']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $contact = mysqli_real_escape_string($con, $_POST['contactNumber']);
        $check_in = date('Y-m-d');
        $check_out = mysqli_real_escape_string($con, $_POST['checkOutDate']);
        $payment_method = mysqli_real_escape_string($con, $_POST['paymentMethod']);
        $payment_option = mysqli_real_escape_string($con, $_POST['paymentOption']);
        
        // Generate booking reference
        $booking_reference = 'BK-' . date('Ymd') . '-' . substr(uniqid(), -5);
        
        // Get selected room number from the form
        $room_number = isset($_POST['roomNumber']) ? mysqli_real_escape_string($con, $_POST['roomNumber']) : null;
        
        if (empty($room_number)) {
            throw new Exception("Please select a room number");
        }
        
        // Check room type and price
        $check_room_type_sql = "SELECT price FROM room_types WHERE room_type_id = ?";
        $stmt = mysqli_prepare($con, $check_room_type_sql);
        mysqli_stmt_bind_param($stmt, "i", $room_type_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to check room type: " . mysqli_error($con));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $room_type_data = mysqli_fetch_assoc($result);
        
        if (!$room_type_data) { }
        
        // Check if the selected room is available and not already booked
        $check_room_availability = "SELECT rn.*, rt.price 
                                 FROM room_numbers rn
                                 JOIN room_types rt ON rn.room_type_id = rt.room_type_id
                                 WHERE rn.room_number = ? COLLATE utf8mb4_unicode_ci
                                 AND rn.room_type_id = ?
                                 AND rn.status IN ('active', 'available')
                                 AND NOT EXISTS (
                                     SELECT 1 FROM bookings b 
                                     WHERE b.room_number = rn.room_number COLLATE utf8mb4_unicode_ci
                                     AND b.status IN ('confirmed', 'checked_in')
                                     AND (
                                         (b.check_in <= ? AND b.check_out > ?) OR
                                         (b.check_in < ? AND b.check_out >= ?) OR
                                         (b.check_in >= ? AND b.check_out <= ?)
                                     )
                                 )
                                 FOR UPDATE";
        
        // Log the query for debugging
        error_log("Checking room availability with query: " . $check_room_availability);
        error_log("Check-in date: $check_in, Check-out date: $check_out");
        
        $stmt = mysqli_prepare($con, $check_room_availability);
        if (!$stmt) {
            $error = "Prepare failed: (" . $con->errno . ") " . $con->error;
            error_log($error);
            throw new Exception("Failed to prepare room availability check: " . $error);
        }
        
        // Bind parameters for the date range check
        // Note: The date parameters must be in the same order as the placeholders in the query
        $bound = mysqli_stmt_bind_param($stmt, "sissssss", 
            $room_number, 
            $room_type_id,
            $check_out, $check_in,  // First date range check
            $check_out, $check_in,  // Second date range check
            $check_in, $check_out   // Third date range check
        );
        
        if (!$bound) {
            $error = "Failed to bind parameters: " . mysqli_stmt_error($stmt);
            error_log($error);
            throw new Exception($error);
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            $error = "Failed to execute room availability check: " . mysqli_stmt_error($stmt);
            error_log($error);
            throw new Exception($error);
        }
        
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            $error = "Failed to get result set: " . mysqli_error($con);
            error_log($error);
            throw new Exception($error);
        }
        
        $room_data = mysqli_fetch_assoc($result);
        
        if (!$room_data) {
            $error = "Room not available. Room: $room_number, Type: $room_type_id, Check-in: $check_in, Check-out: $check_out";
            error_log($error);
            throw new Exception("The selected room is not available for the selected dates. Please choose another room or different dates.");
        }
        
        // Calculate nights and amounts
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        if ($nights < 1) $nights = 1;
        
        $total_amount = $room_data['price'] * $nights;
        $downpayment_amount = ($payment_option === 'downpayment') ? ($total_amount * 0.5) : $total_amount;
        $remaining_balance = ($payment_option === 'downpayment') ? ($total_amount * 0.5) : 0;
        
        // Initialize missing variables
        $number_of_guests = 1; // Default to 1 guest if not specified
        $status = 'Checked in';
        $created_at = date('Y-m-d H:i:s');
        $booking_type = 'Walk-in';
        $room_quantity = 1;

        // Insert booking with all required fields from the database schema
        $insert_sql = "INSERT INTO bookings (
            booking_reference, room_type_id, room_number, first_name, last_name,
            email, contact, check_in, check_out,
            number_of_guests, payment_method, payment_option,
            total_amount, status, created_at,
            nights, downpayment_amount, remaining_balance,
            booking_type, room_quantity, user_types, num_adults, num_children, extra_charges
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, 'frontdesk', 1, 0, 0.00
        )";
        
        $stmt = mysqli_prepare($con, $insert_sql);
        if (!$stmt) {
            $error = "Prepare failed: (" . $con->errno . ") " . $con->error;
            error_log($error);
            throw new Exception("Failed to prepare booking statement: " . $error);
        }
        
        // Debug: Log the values being bound
        error_log("Booking Reference: " . $booking_reference);
        error_log("Room Type ID: " . $room_type_id);
        error_log("Room Number: " . $room_number);
        error_log("First Name: " . $first_name);
        error_log("Last Name: " . $last_name);
        error_log("Email: " . $email);
        error_log("Contact: " . $contact);
        error_log("Check In: " . $check_in);
        error_log("Check Out: " . $check_out);
        error_log("Number of Guests: " . $number_of_guests);
        error_log("Payment Method: " . $payment_method);
        error_log("Payment Option: " . $payment_option);
        error_log("Total Amount: " . $total_amount);
        error_log("Status: " . $status);
        error_log("Nights: " . $nights);
        error_log("Downpayment Amount: " . $downpayment_amount);
        error_log("Remaining Balance: " . $remaining_balance);
        error_log("Booking Type: " . $booking_type);
        error_log("Room Quantity: " . $room_quantity);
        
        // Bind parameters with correct types and order
        $bound = mysqli_stmt_bind_param($stmt, "sisssssssissdssddsi",
            $booking_reference,
            $room_type_id,
            $room_number,
            $first_name,
            $last_name,
            $email,
            $contact,
            $check_in,
            $check_out,
            $number_of_guests,
            $payment_method,
            $payment_option,
            $total_amount,
            $status,
            $created_at,
            $nights,
            $downpayment_amount,
            $remaining_balance,
            $booking_type,
            $room_quantity
        );
        
        if (!$bound) {
            $error = "Failed to bind parameters: " . mysqli_stmt_error($stmt);
            error_log($error);
            throw new Exception($error);
        }
        
        // Debug: Log the values being bound
        error_log("Booking Reference: " . $booking_reference);
        error_log("Room Type ID: " . $room_type_id);
        error_log("Room Number: " . $room_number);
        error_log("First Name: " . $first_name);
        error_log("Last Name: " . $last_name);
        error_log("Email: " . $email);
        error_log("Contact: " . $contact);
        error_log("Check In: " . $check_in);
        error_log("Check Out: " . $check_out);
        error_log("Number of Guests: " . $number_of_guests);
        error_log("Payment Method: " . $payment_method);
        error_log("Payment Option: " . $payment_option);
        error_log("Total Amount: " . $total_amount);
        error_log("Status: " . $status);
        error_log("Created At: " . $created_at);
        error_log("Nights: " . $nights);
        error_log("Downpayment Amount: " . $downpayment_amount);
        error_log("Remaining Balance: " . $remaining_balance);
        error_log("Booking Type: " . $booking_type);
        error_log("Room Quantity: " . $room_quantity);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to create booking: " . mysqli_stmt_error($stmt));
        }
        
        // Update room status to occupied
        $update_room_status = "UPDATE room_numbers 
                             SET status = 'occupied', 
                                 updated_at = NOW() 
                             WHERE room_number = ? 
                             AND room_type_id = ?
                             AND status IN ('active', 'available', 'booked')";
        
        $stmt = mysqli_prepare($con, $update_room_status);
        if (!$stmt) {
            $error = "Prepare failed: (" . $con->errno . ") " . $con->error;
            error_log($error);
            throw new Exception("Failed to prepare room status update statement: " . $error);
        }
        
        $bound = mysqli_stmt_bind_param($stmt, "si", $room_number, $room_type_id);
        if (!$bound) {
            $error = "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
            error_log($error);
            throw new Exception("Failed to bind room status update parameters: " . $error);
        }
        
        $executed = mysqli_stmt_execute($stmt);
        if (!$executed) {
            $error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            error_log($error);
            throw new Exception("Failed to update room status: " . $error);
        }
        
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        error_log("Room status update affected rows: " . $affected_rows);
        
        if ($affected_rows === 0) {
            // Check current status of the room
            $check_status = "SELECT status FROM room_numbers WHERE room_number = ? AND room_type_id = ?";
            $stmt = mysqli_prepare($con, $check_status);
            if (!$stmt) {
                $error = "Prepare failed: (" . $con->errno . ") " . $con->error;
                error_log($error);
                throw new Exception("Failed to prepare room status check statement: " . $error);
            }
            
            $bound = mysqli_stmt_bind_param($stmt, "si", $room_number, $room_type_id);
            if (!$bound) {
                $error = "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
                error_log($error);
                throw new Exception("Failed to bind room status check parameters: " . $error);
            }
            
            $executed = mysqli_stmt_execute($stmt);
            if (!$executed) {
                $error = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                error_log($error);
                throw new Exception("Failed to check room status: " . $error);
            }
            
            $result = mysqli_stmt_get_result($stmt);
            if (!$result) {
                $error = "Get result failed: (" . $con->errno . ") " . $con->error;
                error_log($error);
                throw new Exception("Failed to get room status result: " . $error);
            }
            
            $current_status = mysqli_fetch_assoc($result);
            
            throw new Exception(sprintf(
                "Failed to update room status. Current status: %s. The room may have been modified by another process or is not in an updatable state.",
                $current_status ? $current_status['status'] : 'unknown'
            ));
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Return success response
        $response = [
            'success' => true,
            'message' => 'Check-in successful',
            'data' => [
                'booking_reference' => $booking_reference,
                'booking_id' => mysqli_insert_id($con)
            ]
        ];
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in process_advance_checkin.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    sendJsonResponse(
        false, 
        'An error occurred while processing your request. Please try again.',
        ['error' => $e->getMessage()],
        500
    );
}

// Close connection
if ($con) {
    mysqli_close($con);
}

// This line should never be reached as we exit in the sendJsonResponse function
sendJsonResponse(false, 'Unexpected error occurred', null, 500); 