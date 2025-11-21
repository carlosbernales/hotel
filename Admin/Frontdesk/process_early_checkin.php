<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $booking_id = $_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    $payment_option = $_POST['payment_option'];
    $new_total = floatval(str_replace(['₱', ','], '', $_POST['new_total']));
    $current_date = date('Y-m-d'); // Get current date for early check-in

    // Calculate amount paid based on payment option
    $amount_to_pay = 0;
    if ($payment_option === 'full') {
        $amount_to_pay = $new_total;
    } else { // downpayment
        $amount_to_pay = $new_total * 0.5; // 50% of new total
    }

    // Update query to change all required fields
    $sql = "UPDATE bookings 
            SET status = 'Checked In',
                check_in = '$current_date',
                total_amount = $new_total,
                payment_method = '$payment_method',
                payment_option = '$payment_option',
                downpayment_amount = " . ($payment_option === 'downpayment' ? $amount_to_pay : 'NULL') . "
            WHERE booking_id = '$booking_id'";
    
    if (mysqli_query($con, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Early check-in processed successfully',
            'redirect' => 'checked_in.php'
        ]);
    } else {
        throw new Exception("Failed to update booking: " . mysqli_error($con));
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 