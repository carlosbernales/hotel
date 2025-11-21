<?php
header('Content-Type: application/json');
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($booking_id)) {
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        exit;
    }

    try {
        // Start transaction
        mysqli_begin_transaction($con);

        // Get current date for checkout
        $checkout_date = date('Y-m-d');

        // First, get the booking details to check payment status
        $check_booking = "SELECT * FROM bookings WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $check_booking);
        mysqli_stmt_bind_param($stmt, "s", $booking_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $booking = mysqli_fetch_assoc($result);

        if (!$booking) {
            throw new Exception("Booking not found");
        }

        // Update booking status only
        $update_sql = "UPDATE bookings SET status = 'Checked Out' WHERE booking_id = ?";
            
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "s", $booking_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating booking: " . mysqli_error($con));
        }

        // Update room status
        $update_room_sql = "UPDATE rooms r 
            INNER JOIN room_bookings rb ON r.room_type_id = rb.room_type_id 
            SET r.available_rooms = r.available_rooms + 1,
                r.status = 'active'
            WHERE rb.booking_id = ?";
            
        $stmt = mysqli_prepare($con, $update_room_sql);
        mysqli_stmt_bind_param($stmt, "s", $booking_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating room status: " . mysqli_error($con));
        }

        // If payment method is provided and there's a remaining balance, record the payment
        if (!empty($payment_method) && isset($booking['payment_option']) && $booking['payment_option'] === 'downpayment') {
            $remaining_balance = $booking['total_amount'] - ($booking['downpayment_amount'] ?? 0);
            
            if ($remaining_balance > 0) {
                $payment_sql = "INSERT INTO payments (booking_id, payment_method, payment_date, amount) 
                    VALUES (?, ?, ?, ?)";
                    
                $stmt = mysqli_prepare($con, $payment_sql);
                mysqli_stmt_bind_param($stmt, "sssd", $booking_id, $payment_method, $checkout_date, $remaining_balance);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error recording payment: " . mysqli_error($con));
                }
            }
        }

        // Commit transaction
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Checkout processed successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}
?>
