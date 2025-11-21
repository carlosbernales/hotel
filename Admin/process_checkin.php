<?php
require_once 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log function for debugging
function logError($message) {
    $logFile = __DIR__ . '/checkin_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

try {
    // Get POST data
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : null;
    $room_number = isset($_POST['room_number']) ? $_POST['room_number'] : null;
    $discount_type = isset($_POST['discount_type']) ? $_POST['discount_type'] : '';

    // Validate input
    if (!$booking_id || !$room_number) {
        throw new Exception('Booking ID and room number are required');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    // Check if booking exists and has valid status
    $check_booking = mysqli_prepare($con, "SELECT status FROM bookings WHERE booking_id = ?");
    mysqli_stmt_bind_param($check_booking, "s", $booking_id);
    mysqli_stmt_execute($check_booking);
    $result = mysqli_stmt_get_result($check_booking);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception('Booking not found');
    }

    $booking = mysqli_fetch_assoc($result);
    if (!in_array($booking['status'], ['pending', 'accepted'])) {
        throw new Exception('Cannot check in booking. Current status must be "Pending" or "Accepted"');
    }

    // Check if room is available
    $check_room = mysqli_prepare($con, "SELECT status FROM room_numbers WHERE room_number = ?");
    mysqli_stmt_bind_param($check_room, "s", $room_number);
    mysqli_stmt_execute($check_room);
    $room_result = mysqli_stmt_get_result($check_room);

    if (!$room_result || mysqli_num_rows($room_result) === 0) {
        throw new Exception('Room not found');
    }

    $room = mysqli_fetch_assoc($room_result);
    if ($room['status'] !== 'active') {
        throw new Exception('Selected room is not available for check-in (Status: ' . $room['status'] . ')');
    }

    // First, get the total amount, payment option, downpayment_amount, and amount_paid to calculate discount
    $get_amount = mysqli_prepare($con, "SELECT total_amount, payment_option, downpayment_amount, amount_paid FROM bookings WHERE booking_id = ?");
    mysqli_stmt_bind_param($get_amount, "s", $booking_id);
    mysqli_stmt_execute($get_amount);
    $amount_result = mysqli_stmt_get_result($get_amount);
    $booking_data = mysqli_fetch_assoc($amount_result);

    if ($booking_data) {
        $total_amount = (float)$booking_data['total_amount'];
        $payment_option = strtolower(trim($booking_data['payment_option']));
        $downpayment_amount = (float)$booking_data['downpayment_amount'];
        $amount_paid = (float)$booking_data['amount_paid'];
        $discount_percentage = 20;
        $discount_amount = 0;
        $final_amount = $total_amount;

        if ($payment_option === 'partial payment' || $payment_option === 'custom payment' || $payment_option === 'downpayment') {
            // Calculate 20% discount first
            $discount_amount = $total_amount * 0.20;
            // Subtract discount from total amount
            $new_total = $total_amount - $discount_amount;
            // Subtract downpayment from the discounted amount
            $remaining_balance = $new_total - $downpayment_amount;
            $final_amount = $total_amount; // Keep original total_amount for record

            $update_booking = mysqli_prepare($con, "UPDATE bookings SET 
                status = 'Checked In', 
                room_number = ?,
                discount_type = ?,
                discount_percentage = ?,
                discount_amount = ?,
                remaining_balance = ?
                WHERE booking_id = ?");
            mysqli_stmt_bind_param($update_booking, "ssddds", $room_number, $discount_type, $discount_percentage, $discount_amount, $remaining_balance, $booking_id);
        } elseif ($payment_option === 'full payment' || $payment_option === 'full') {
            // Calculate 20% discount
            $discount_amount = $total_amount * 0.20;
            // Subtract discount from total amount
            $final_amount = $total_amount - $discount_amount;
            $remaining_balance = 0; // No remaining balance for full payment

            $update_booking = mysqli_prepare($con, "UPDATE bookings SET 
                status = 'Checked In', 
                room_number = ?,
                discount_type = ?,
                discount_percentage = ?,
                discount_amount = ?,
                total_amount = ?,
                remaining_balance = ?
                WHERE booking_id = ?");
            mysqli_stmt_bind_param($update_booking, "ssdddss", $room_number, $discount_type, $discount_percentage, $discount_amount, $final_amount, $remaining_balance, $booking_id);
        } else {
            // Default: apply 20% to total_amount
            $discount_amount = $total_amount * 0.20;
            $final_amount = $total_amount - $discount_amount;
            $remaining_balance = $final_amount;

            $update_booking = mysqli_prepare($con, "UPDATE bookings SET 
                status = 'Checked In', 
                room_number = ?,
                discount_type = ?,
                discount_percentage = ?,
                discount_amount = ?,
                total_amount = ?,
                remaining_balance = ?
                WHERE booking_id = ?");
            mysqli_stmt_bind_param($update_booking, "ssdddss", $room_number, $discount_type, $discount_percentage, $discount_amount, $final_amount, $remaining_balance, $booking_id);
        }
    } else {
        throw new Exception('Failed to get booking amount');
    }
    
    if (!mysqli_stmt_execute($update_booking)) {
        throw new Exception('Failed to update booking status');
    }

    // Update room status to occupied
    $update_room = mysqli_prepare($con, "UPDATE room_numbers SET status = 'occupied' WHERE room_number = ?");
    mysqli_stmt_bind_param($update_room, "s", $room_number);
    
    if (!mysqli_stmt_execute($update_room)) {
        throw new Exception('Failed to update room status: ' . mysqli_error($con));
    }

    // Commit transaction
    mysqli_commit($con);

    // Get the updated booking details with payment information
    $get_updated_booking = mysqli_prepare($con, "SELECT 
        b.total_amount, 
        b.discount_type, 
        b.discount_amount, 
        b.downpayment_amount, 
        b.amount_paid,
        b.payment_option,
        b.status,
        rt.room_type,
        b.booking_id
        FROM bookings b
        LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
        WHERE b.booking_id = ?");
    mysqli_stmt_bind_param($get_updated_booking, "s", $booking_id);
    mysqli_stmt_execute($get_updated_booking);
    $updated_booking = mysqli_fetch_assoc(mysqli_stmt_get_result($get_updated_booking));
    
    // Calculate amounts as floats
    $total_amount = (float)$updated_booking['total_amount'];
    $downpayment = (float)$updated_booking['downpayment_amount'];
    $payment_option = strtolower(trim($updated_booking['payment_option'] ?? ''));
    
    // Determine amount paid based on payment option
    if (in_array($payment_option, ['partial payment', 'custom payment', 'downpayment'])) {
        $amount_paid = $downpayment > 0 ? $downpayment : 0;
    } else {
        $amount_paid = (float)$updated_booking['amount_paid'];
    }
    
    // Format amounts with 2 decimal places for display
    $formatted_total = number_format($total_amount, 2, '.', ',');
    $formatted_paid = number_format($amount_paid, 2, '.', ',');

    // Log successful check-in
    logError("Check-in successful for booking ID: $booking_id, Room: $room_number");
    
    // Send success response with updated amount and amount paid
    echo json_encode([
        'success' => true,
        'message' => 'Check-in successful',
        'new_total_amount' => $formatted_total,
        'amount_paid' => $formatted_paid,
        'booking_id' => $booking_id,
        'raw_amounts' => [
            'total' => $total_amount,
            'paid' => $amount_paid
        ]
    ]);

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    // Log the error
    logError("Check-in failed: " . $errorMessage . "\n" . $e->getTraceAsString());
    
    // Rollback transaction on error
    if (isset($con) && mysqli_connect_errno() === 0) {
        mysqli_rollback($con);
    }
    
    // Send error response with more details
    echo json_encode([
        'success' => false,
        'message' => $errorMessage,
        'error_details' => $e->getTraceAsString()
    ]);
}

// Close database connection
if (isset($con)) {
    mysqli_close($con);
}
?>