<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $payment_amount = floatval($_POST['payment_amount']);
    $payment_method = $_POST['payment_method'];

    // Get current booking details
    $query = "SELECT total_amount, amount_paid FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);

    if ($booking) {
        $total_amount = floatval($booking['total_amount']);
        $current_amount_paid = floatval($booking['amount_paid']);
        $new_amount_paid = $current_amount_paid + $payment_amount;

        // Check if payment is valid
        if ($payment_amount <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid payment amount']);
            exit;
        }

        if ($new_amount_paid > $total_amount) {
            echo json_encode(['success' => false, 'error' => 'Payment amount exceeds remaining balance']);
            exit;
        }

        // Update the booking with new payment amount and status
        $payment_status = ($new_amount_paid >= $total_amount) ? 'Paid' : 'Partial';
        $update_query = "UPDATE bookings SET amount_paid = ?, payment_status = ? WHERE booking_id = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, "dss", $new_amount_paid, $payment_status, $booking_id);

        if (mysqli_stmt_execute($update_stmt)) {
            // Insert into payment history
            $payment_date = date('Y-m-d H:i:s');
            $history_query = "INSERT INTO payment_history (booking_id, payment_amount, payment_method, payment_date) VALUES (?, ?, ?, ?)";
            $history_stmt = mysqli_prepare($con, $history_query);
            mysqli_stmt_bind_param($history_stmt, "sdss", $booking_id, $payment_amount, $payment_method, $payment_date);
            mysqli_stmt_execute($history_stmt);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error updating payment']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Booking not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
