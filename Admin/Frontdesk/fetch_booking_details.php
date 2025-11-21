<?php
// Prevent any output before JSON response
ob_start();

try {
    // Include database connection
    require_once 'db.php';

    // Verify database connection
    if (!isset($con) || !$con) {
        throw new Exception('Database connection failed: ' . (isset($con) ? mysqli_connect_error() : 'Connection variable not set'));
    }

    // Set JSON header
    header('Content-Type: application/json');

    if (!isset($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);

    // Simplified query to get booking details
    $sql = "SELECT * FROM bookings WHERE booking_id = '$booking_id'";
    
    $result = mysqli_query($con, $sql);

    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($con));
    }

    if (mysqli_num_rows($result) === 0) {
        throw new Exception('No booking found with ID: ' . $booking_id);
    }

    $booking = mysqli_fetch_assoc($result);

    // Calculate number of nights
    $check_in = new DateTime($booking['check_in']);
    $check_out = new DateTime($booking['check_out']);
    $nights = $check_out->diff($check_in)->days;

    // Get room type name
    $room_type_name = 'Standard Room';
    switch ($booking['room_type_id']) {
        case '6':
            $room_type_name = 'Deluxe Room';
            $room_price = 2500.00;
            break;
        case '7':
            $room_type_name = 'Premium Room';
            $room_price = 3500.00;
            break;
        case '8':
            $room_type_name = 'Suite Room';
            $room_price = 4500.00;
            break;
        default:
            $room_price = 1500.00;
    }

    // Format payment proof path - ensure it includes the payment_proofs directory
    $payment_proof = null;
    if (!empty($booking['payment_proof'])) {
        $path = $booking['payment_proof'];
        if (strpos($path, 'payment_proofs/') === false) {
            $path = 'payment_proofs/' . basename($path);
        }
        $payment_proof = 'uploads/' . $path;
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

    // Clear any previous output and send response
    ob_clean();
    echo json_encode($response);

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush(); 