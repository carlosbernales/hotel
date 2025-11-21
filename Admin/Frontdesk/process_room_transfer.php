<?php
require_once 'db.php';
session_start();

// Set the content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get POST data
    $booking_id = $_POST['booking_id'];
    $new_room_id = $_POST['new_room_id'];
    $transfer_reason = $_POST['transfer_reason'];
    $price_difference = floatval($_POST['price_difference']);
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $cash_amount = isset($_POST['cash_amount']) ? floatval($_POST['cash_amount']) : 0;

    // Start transaction
    mysqli_begin_transaction($con);

    // 1. Update the booking with new room type
    $update_booking = "UPDATE bookings 
                      SET room_type_id = ?, 
                          total_amount = total_amount + ?,
                          last_modified = NOW()
                      WHERE booking_id = ?";
    
    $stmt = mysqli_prepare($con, $update_booking);
    mysqli_stmt_bind_param($stmt, "sds", $new_room_id, $price_difference, $booking_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update booking: " . mysqli_error($con));
    }

    // 2. Create a transfer record
    $create_transfer = "INSERT INTO room_transfers 
                       (booking_id, old_room_type_id, new_room_type_id, 
                        transfer_reason, price_difference, transfer_date) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($con, $create_transfer);
    mysqli_stmt_bind_param($stmt, "ssssd", 
        $booking_id, 
        $_POST['current_room_id'],
        $new_room_id, 
        $transfer_reason,
        $price_difference
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create transfer record: " . mysqli_error($con));
    }

    // 3. Create a payment record if there's a price difference
    if ($price_difference > 0 && $payment_method) {
        $create_payment = "INSERT INTO payments 
                          (booking_id, amount, payment_method, payment_date, payment_type) 
                          VALUES (?, ?, ?, NOW(), 'room_transfer')";
        
        $stmt = mysqli_prepare($con, $create_payment);
        mysqli_stmt_bind_param($stmt, "sds", $booking_id, $price_difference, $payment_method);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to create payment record: " . mysqli_error($con));
        }
    }

    // 4. Create notification
    $notification_message = "Room transfer processed for booking #$booking_id. Transferred to new room type.";
    $create_notification = "INSERT INTO notifications 
                          (reference_id, message, type, created_at) 
                          VALUES (?, ?, 'room_transfer', NOW())";
    
    $stmt = mysqli_prepare($con, $create_notification);
    mysqli_stmt_bind_param($stmt, "ss", $booking_id, $notification_message);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create notification: " . mysqli_error($con));
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Room transfer processed successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
mysqli_close($con);
?> 