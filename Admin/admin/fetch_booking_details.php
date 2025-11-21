<?php

// Format payment proof path
$payment_proof = null;
if (!empty($booking['payment_proof'])) {
    // Get the filename from the stored path
    $filename = basename($booking['payment_proof']);
    
    // Construct the correct path relative to the Admin directory
    $payment_proof = '../customer/aa/' . $booking['payment_proof'];
    
    // Log the path for debugging
    error_log("Payment proof path: " . $payment_proof);
    
    // Check if file exists
    if (!file_exists($payment_proof)) {
        error_log("Payment proof file not found at: " . $payment_proof);
        $payment_proof = null;
    }
}

// Prepare response data
$response = [
    'success' => true,
    'data' => [
        'booking_id' => $booking['booking_id'],
        'name' => $booking['first_name'] . ' ' . $booking['last_name'],
        'email' => $booking['email'],
        'contact' => $booking['contact'],
        'check_in' => date('M j, Y', strtotime($booking['check_in'])),
        'check_out' => date('M j, Y', strtotime($booking['check_out'])),
        'nights' => $nights,
        'room_type' => $room_type_name,
        'room_price' => $room_price,
        'total_amount' => $booking['total_amount'],
        'payment_option' => ucfirst($booking['payment_option']),
        'amount_paid' => $booking['downpayment_amount'],
        'payment_method' => ucfirst($booking['payment_method']),
        'discount_type' => $booking['discount_type'] ? ucfirst($booking['discount_type']) : 'Regular',
        'status' => ucfirst($booking['status']),
        'payment_reference' => $booking['payment_reference'],
        'payment_proof' => $payment_proof
    ]
]; 

// Format payment proof path
$payment_proof = null;
if (!empty($booking['payment_proof'])) {
    // Get the filename from the stored path
    $filename = basename($booking['payment_proof']);
    
    // Construct the correct path relative to the Admin directory
    $payment_proof = '../customer/aa/' . $booking['payment_proof'];
    
    // Log the path for debugging
    error_log("Payment proof path: " . $payment_proof);
    
    // Check if file exists
    if (!file_exists($payment_proof)) {
        error_log("Payment proof file not found at: " . $payment_proof);
        $payment_proof = null;
    }
}

// Prepare response data
$response = [
    'success' => true,
    'data' => [
        'booking_id' => $booking['booking_id'],
        'name' => $booking['first_name'] . ' ' . $booking['last_name'],
        'email' => $booking['email'],
        'contact' => $booking['contact'],
        'check_in' => date('M j, Y', strtotime($booking['check_in'])),
        'check_out' => date('M j, Y', strtotime($booking['check_out'])),
        'nights' => $nights,
        'room_type' => $room_type_name,
        'room_price' => $room_price,
        'total_amount' => $booking['total_amount'],
        'payment_option' => ucfirst($booking['payment_option']),
        'amount_paid' => $booking['downpayment_amount'],
        'payment_method' => ucfirst($booking['payment_method']),
        'discount_type' => $booking['discount_type'] ? ucfirst($booking['discount_type']) : 'Regular',
        'status' => ucfirst($booking['status']),
        'payment_reference' => $booking['payment_reference'],
        'payment_proof' => $payment_proof
    ]
]; 