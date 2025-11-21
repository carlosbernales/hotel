<?php
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to return JSON response
header('Content-Type: application/json');

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Get POST data
    $booking_id = $_POST['booking_id'] ?? '';
    $checkout_date = date('Y-m-d H:i:s');

    if (empty($booking_id)) {
        throw new Exception("Booking ID is required");
    }

    // Get booking details
    $booking_sql = "SELECT b.*, rt.room_type 
                   FROM bookings b 
                   JOIN room_types rt ON b.room_type_id = rt.room_type_id 
                   WHERE b.booking_id = ?";
                   
    $stmt = mysqli_prepare($con, $booking_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare booking query: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "s", $booking_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to get booking details: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);

    if (!$booking) {
        throw new Exception("Booking not found");
    }

    // Check if booking status is valid for checkout
    $current_status = $booking['status'];
    if ($current_status !== 'Checked In' && $current_status !== 'Extended') {
        throw new Exception("Booking must be in 'Checked In' or 'Extended' status to check out");
    }

    // Get payment details from POST
    $payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($con, $_POST['payment_method']) : null;
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    $amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;
    $remaining_balance = isset($_POST['remaining_balance']) ? floatval($_POST['remaining_balance']) : 0;

    // If there's a remaining balance, require payment method
    if ($remaining_balance > 0 && empty($payment_method)) {
        throw new Exception("Payment method is required for remaining balance");
    }

    // Update booking status and payment details
    $update_booking_sql = "UPDATE bookings 
                          SET status = 'Checked Out',
                              actual_checkout = ?,
                              payment_method = ?,
                              amount_paid = amount_paid + ?,
                              remaining_balance = 0
                          WHERE booking_id = ?";
                          
    $stmt = mysqli_prepare($con, $update_booking_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare booking update: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "ssds", $checkout_date, $payment_method, $remaining_balance, $booking_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update booking status: " . mysqli_stmt_error($stmt));
    }

    // Update room status to active
    $update_room_sql = "UPDATE room_numbers SET status = 'active' WHERE room_number = ?";
    $stmt = mysqli_prepare($con, $update_room_sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare room update: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "s", $booking['room_number']);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update room availability: " . mysqli_stmt_error($stmt));
    }

    // Commit the transaction
    mysqli_commit($con);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Check-out processed successfully',
        'redirect' => 'checked_out.php'
    ]);

} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Close database connection
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>
