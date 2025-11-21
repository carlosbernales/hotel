<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = array('success' => false, 'message' => '', 'data' => null);

try {
    // Log incoming data
    error_log("Starting check-in process...");
    error_log("POST data: " . print_r($_POST, true));
    
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate and sanitize room_type_id first
    if (!isset($_POST['room_type_id']) || trim($_POST['room_type_id']) === '') {
        throw new Exception("Room type ID is required");
    }
    
    $room_type_id = filter_var($_POST['room_type_id'], FILTER_VALIDATE_INT);
    if ($room_type_id === false) {
        throw new Exception("Invalid room type ID format");
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Check room availability first
    $check_room_sql = "SELECT available_rooms, total_rooms 
                      FROM rooms 
                      WHERE room_type_id = ? 
                      FOR UPDATE";  // Lock the row
    
    $stmt = mysqli_prepare($con, $check_room_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare room check statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to check room availability: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $room_data = mysqli_fetch_assoc($result);
    
    if (!$room_data) {
        throw new Exception("Room type not found");
    }
    
    if ($room_data['available_rooms'] <= 0) {
        throw new Exception("Sorry, this room is no longer available");
    }

    // Check if this is a confirmation step
    $is_confirmation = isset($_POST['confirm']) && $_POST['confirm'] === 'true';
    error_log("Is confirmation step: " . ($is_confirmation ? 'yes' : 'no'));

    // Validate required fields
    $required_fields = [
        'firstName', 'lastName', 'contactNumber', 'email',
        'checkOutDate', 'guestCount', 'paymentMethod', 'paymentOption',
        'room_type', 'price'
    ];

    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        throw new Exception("Required fields missing: " . implode(', ', $missing_fields));
    }

    // Sanitize and validate input data
    $room_type = mysqli_real_escape_string($con, $_POST['room_type']);
    $first_name = mysqli_real_escape_string($con, $_POST['firstName']);
    $last_name = mysqli_real_escape_string($con, $_POST['lastName']);
    $contact = mysqli_real_escape_string($con, $_POST['contactNumber']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_in = date('Y-m-d');
    $check_out = mysqli_real_escape_string($con, $_POST['checkOutDate']);
    $guest_count = filter_var($_POST['guestCount'], FILTER_VALIDATE_INT);
    $payment_method = mysqli_real_escape_string($con, $_POST['paymentMethod']);
    $payment_option = mysqli_real_escape_string($con, $_POST['paymentOption']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);

    error_log("Sanitized data: " . json_encode([
        'room_type_id' => $room_type_id,
        'room_type' => $room_type,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'contact' => $contact,
        'email' => $email,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'guest_count' => $guest_count,
        'payment_method' => $payment_method,
        'payment_option' => $payment_option,
        'price' => $price
    ]));

    // Additional validation
    if ($guest_count === false || $guest_count <= 0) {
        throw new Exception("Invalid guest count");
    }
    
    if ($price === false || $price <= 0) {
        throw new Exception("Invalid price");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Calculate nights and total amount
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;

    if ($nights <= 0) {
        throw new Exception("Check-out date must be after check-in date");
    }

    $total_amount = $price * $nights;
    $amount_paid = ($payment_option === 'downpayment') ? ($total_amount * 0.5) : $total_amount;

    error_log("Calculated amounts: total = {$total_amount}, amount_paid = {$amount_paid}, nights = {$nights}");

    // If this is not the confirmation step, just return the summary data
    if (!$is_confirmation) {
        $response['success'] = true;
        $response['show_summary'] = true;
        $response['data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'contact' => $contact,
            'room_type' => $room_type,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'nights' => $nights,
            'guest_count' => $guest_count,
            'payment_method' => $payment_method,
            'payment_option' => $payment_option,
            'total_amount' => $total_amount,
            'amount_paid' => $amount_paid,
            'available_rooms' => $room_data['available_rooms']
        ];
        mysqli_rollback($con); // Rollback the transaction since we're just showing summary
        echo json_encode($response);
        exit;
    }

    error_log("Starting database operations...");

    // Generate a unique booking reference
    $booking_reference = 'BK-' . date('Ymd') . '-' . sprintf('%04d', rand(0, 9999));
    error_log("Generated booking reference: {$booking_reference}");

    // Calculate downpayment and remaining balance
    $downpayment_amount = ($payment_option === 'downpayment') ? ($total_amount * 0.5) : $total_amount;
    $remaining_balance = $total_amount - $downpayment_amount;

    error_log("Payment calculations: downpayment = {$downpayment_amount}, remaining = {$remaining_balance}");

    // Insert booking with correct field names and required fields
    $insert_query = "INSERT INTO bookings (
        booking_reference, room_type_id, first_name, last_name, 
        email, contact, check_in, check_out, 
        number_of_guests, payment_method, payment_option,
        total_amount, booking_type, status, created_at,
        user_id, downpayment_amount, remaining_balance,
        discount_type, discount_amount, discount_percentage
    ) VALUES (
        ?, ?, ?, ?, 
        ?, ?, ?, ?, 
        ?, ?, ?,
        ?, 'Walk-in', 'Checked In', NOW(),
        1, ?, ?,
        '', 0.00, 0.00
    )";

    error_log("Insert query prepared: " . $insert_query);

    $stmt = mysqli_prepare($con, $insert_query);
    if (!$stmt) {
        throw new Exception("Database error in prepare: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "sissssssissddd",
        $booking_reference,
        $room_type_id, 
        $first_name, 
        $last_name,
        $email, 
        $contact, 
        $check_in, 
        $check_out,
        $guest_count, 
        $payment_method, 
        $payment_option,
        $total_amount,
        $downpayment_amount,
        $remaining_balance
    );

    error_log("Parameters bound to query");

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create booking: " . mysqli_stmt_error($stmt));
    }

    $booking_id = mysqli_insert_id($con);
    error_log("Booking created with ID: {$booking_id}");

    if (!$booking_id) {
        throw new Exception("Failed to get booking ID");
    }

    // Update the status to "Checked In"
    $update_status_query = "UPDATE bookings SET status = 'Checked In' WHERE booking_id = ?";
    $stmt = mysqli_prepare($con, $update_status_query);
    if (!$stmt) {
        throw new Exception("Database error in status update: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update booking status: " . mysqli_stmt_error($stmt));
    }

    error_log("Status updated to Checked In");

    // Update room availability
    $update_room_sql = "UPDATE rooms 
                       SET available_rooms = available_rooms - 1,
                           status = CASE 
                               WHEN available_rooms - 1 <= 0 THEN 'Occupied'
                               ELSE status 
                           END
                       WHERE room_type_id = ? 
                       AND available_rooms > 0";
    
    $stmt = mysqli_prepare($con, $update_room_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare room update statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update room availability: " . mysqli_stmt_error($stmt));
    }
    
    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception("Failed to update room availability - room may no longer be available");
    }

    // Log the update
    error_log("Room availability updated for room_type_id: $room_type_id");
    error_log("Previous available rooms: " . $room_data['available_rooms']);
    error_log("New available rooms: " . ($room_data['available_rooms'] - 1));

    // Commit transaction
    mysqli_commit($con);

    // Set success response
    $response['success'] = true;
    $response['message'] = "Check-in completed successfully";
    $response['data'] = [
        'booking_id' => $booking_id,
        'booking_reference' => $booking_reference,
        'amount_paid' => $amount_paid,
        'check_in' => $check_in,
        'check_out' => $check_out,
        'nights' => $nights,
        'status' => 'Checked In',
        'available_rooms' => $room_data['available_rooms'] - 1
    ];

} catch (Exception $e) {
    error_log("Error occurred: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Rollback transaction on error
    if (isset($con) && mysqli_ping($con)) {
        mysqli_rollback($con);
        error_log("Transaction rolled back");
    }

    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Add debug information in development
    if (ini_get('display_errors')) {
        $response['debug'] = [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'post_data' => $_POST
        ];
    }
}

// Close database connection
if (isset($con)) {
    mysqli_close($con);
}

error_log("Final response: " . json_encode($response));

// Send response
echo json_encode($response);
exit; 