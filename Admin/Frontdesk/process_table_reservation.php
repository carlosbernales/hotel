<?php
// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'table_reservation_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Include database connection
require_once 'db.php';

// Log received input
error_log("Processing table reservation request");
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

    // Start transaction
    $con->begin_transaction();

    // Sanitize input data
    $packageType = $con->real_escape_string($data['packageType'] ?? '');
    $name = $con->real_escape_string($data['customerName'] ?? '');
    $contactNumber = $con->real_escape_string($data['contactNumber'] ?? '');
    $email = $con->real_escape_string($data['email'] ?? '');
    $bookingDate = $con->real_escape_string($data['reservationDate'] ?? '');
    $bookingTime = $con->real_escape_string($data['reservationTime'] ?? '');
    $guestCount = intval($data['guestCount'] ?? 0);
    $specialRequests = $con->real_escape_string($data['specialRequests'] ?? '');
    $userId = intval($_SESSION['user_id']);
    $totalAmount = $con->real_escape_string($data['totalAmount'] ?? '0');
    $amountToPay = $con->real_escape_string($data['amountToPay'] ?? '0');
    $paymentMethod = $con->real_escape_string($data['paymentMethod'] ?? '');
    $paymentOption = $con->real_escape_string($data['paymentOption'] ?? 'full');
    $status = 'Pending';
    $paymentStatus = 'Pending';

    // Check for existing reservations
    $checkSql = "SELECT COUNT(*) as count FROM table_bookings 
                 WHERE booking_date = ? AND booking_time = ? AND status != 'Cancelled'";
    $stmt = $con->prepare($checkSql);
    $stmt->bind_param("ss", $bookingDate, $bookingTime);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception('This time slot is already booked');
    }

    // Insert booking
    $sql = "INSERT INTO table_bookings (
        user_id, package_name, name, contact_number, email_address,
        booking_date, booking_time, num_guests, special_requests,
        total_amount, amount_to_pay, payment_method, payment_option,
        payment_status, status, package_type, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }

    $stmt->bind_param(
        "issssssissssssss",
        $userId, $packageType, $name, $contactNumber, $email,
        $bookingDate, $bookingTime, $guestCount, $specialRequests,
        $totalAmount, $amountToPay, $paymentMethod, $paymentOption,
        $paymentStatus, $status, $packageType
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $bookingId = $con->insert_id;

    // Handle advance orders if they exist
    if (isset($data['advanceOrder']) && isset($data['advanceOrder']['items']) && is_array($data['advanceOrder']['items'])) {
        $orderSql = "INSERT INTO advance_orders (booking_id, menu_item_id, quantity) VALUES (?, ?, ?)";
        $orderStmt = $con->prepare($orderSql);
        
        foreach ($data['advanceOrder']['items'] as $item) {
            $menuItemId = intval($item['id']);
            $quantity = intval($item['quantity'] ?? 1);
            
            $orderStmt->bind_param("iii", $bookingId, $menuItemId, $quantity);
            if (!$orderStmt->execute()) {
                throw new Exception("Failed to save advance order: " . $orderStmt->error);
            }
        }
    }

    // Commit transaction
    $con->commit();

    // Format success response
    $response = [
        'success' => true,
        'message' => 'Reservation successful',
        'booking_details' => [
            'booking_id' => $bookingId,
            'customer_name' => $name,
            'package_type' => $packageType,
            'date' => $bookingDate,
            'time' => $bookingTime,
            'guest_count' => $guestCount,
            'contact_number' => $contactNumber,
            'total_amount' => $totalAmount,
            'amount_to_pay' => $amountToPay,
            'payment_method' => $paymentMethod
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Log the error
    error_log("Error in table reservation: " . $e->getMessage());
    
    // Rollback transaction if active
    if ($con && $con->ping()) {
        $con->rollback();
    }
    
    // Return error message
    echo json_encode([
        'success' => false,
        'message' => 'Reservation failed: ' . $e->getMessage()
    ]);
} 