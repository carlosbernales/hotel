<?php
// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'room_booking_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'db.php';

// Log received input
error_log("Processing room booking request");
error_log("Content-Type: " . $_SERVER['CONTENT_TYPE']);

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    error_log("Received input: " . $input);
    
    // Decode JSON data
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data received: ' . json_last_error_msg());
    }

    // Debug received data
    error_log("Decoded data: " . print_r($data, true));
    error_log("First name: " . ($data['first_name'] ?? 'not set'));
    error_log("Last name: " . ($data['last_name'] ?? 'not set'));

    // Start transaction
    $con->begin_transaction();

    // Generate booking reference
    $bookingReference = 'BK' . date('YmdHis') . rand(100, 999);

    // Sanitize input data
    $firstName = $con->real_escape_string($data['first_name'] ?? '');
    $lastName = $con->real_escape_string($data['last_name'] ?? '');
    $email = $con->real_escape_string($data['email'] ?? '');
    $contact = $con->real_escape_string($data['contact'] ?? '');
    $checkIn = $con->real_escape_string($data['check_in'] ?? '');
    $checkOut = $con->real_escape_string($data['check_out'] ?? '');

    // Validate dates
    if (empty($checkIn) || empty($checkOut)) {
        throw new Exception('Check-in and check-out dates are required');
    }

    // Validate date format (YYYY-MM-DD)
    $dateFormat = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($dateFormat, $checkIn) || !preg_match($dateFormat, $checkOut)) {
        throw new Exception('Invalid date format. Please use YYYY-MM-DD format');
    }

    // Convert to DateTime objects for comparison
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    // Validate check-in date is not in the past
    if ($checkInDate < $today) {
        throw new Exception('Check-in date cannot be in the past');
    }

    // Validate check-out date is after check-in date
    if ($checkOutDate <= $checkInDate) {
        throw new Exception('Check-out date must be after check-in date');
    }

    $nights = intval($data['nights'] ?? 0);
    $roomTypeId = intval($data['room_type_id'] ?? 0);
    $roomQuantity = intval($data['room_quantity'] ?? 1);
    $paymentOption = $con->real_escape_string($data['payment_option'] ?? 'full');
    $paymentMethod = $con->real_escape_string($data['payment_method'] ?? '');
    $totalAmount = floatval($data['total_amount'] ?? 0);
    $downpaymentAmount = floatval($data['downpayment_amount'] ?? 0);
    $remainingBalance = floatval($data['remaining_balance'] ?? 0);
    $extraBed = $con->real_escape_string($data['extra_bed'] ?? 'no');
    $numberOfGuests = intval($data['number_of_guests'] ?? 0);
    $numAdults = intval($data['num_adults'] ?? 0);
    $numChildren = intval($data['num_children'] ?? 0);
    $status = 'pending';
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    // Insert booking
    $sql = "INSERT INTO bookings (
        booking_reference, user_id, first_name, last_name, email, contact,
        check_in, check_out, nights, room_type_id, room_quantity,
        payment_option, payment_method, total_amount, downpayment_amount,
        remaining_balance, extra_bed, number_of_guests, num_adults,
        num_children, status, created_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
    )";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }

    $stmt->bind_param(
        "sisssssiiiissdddsiiis",
        $bookingReference, $userId, $firstName, $lastName, $email, $contact,
        $checkIn, $checkOut, $nights, $roomTypeId, $roomQuantity,
        $paymentOption, $paymentMethod, $totalAmount, $downpaymentAmount,
        $remainingBalance, $extraBed, $numberOfGuests, $numAdults,
        $numChildren, $status
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $bookingId = $con->insert_id;

    // Commit transaction
    $con->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully',
        'booking_id' => $bookingId,
        'booking_reference' => $bookingReference
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        $con->rollback();
    }

    error_log("Error processing booking: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 