<?php
// Prevent any output before JSON response
ob_start();

// Set JSON header at the very beginning to ensure correct content type
header('Content-Type: application/json');

try {
    // Include database connection
    require_once 'db.php';

    // Disable displaying errors to prevent non-JSON output
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Changed from 1 to 0
    ini_set('log_errors', 1); // Ensure errors are still logged

    // Verify database connection
    if (!isset($con) || !$con) {
        throw new Exception('Database connection failed: ' . (isset($con) ? mysqli_connect_error() : 'Connection variable not set'));
    }

    if (!isset($_GET['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $booking_id = mysqli_real_escape_string($con, $_GET['booking_id']);

    // Get booking details with room type information and calculate amount paid
    $sql = "SELECT b.*, rt.room_type as room_type_name, rt.price as room_price,
                   COALESCE(b.payment_proof, '') as payment_proof,
                   COALESCE(b.payment_reference, '') as payment_reference,
                   DATEDIFF(b.check_out, b.check_in) as nights,
                   COALESCE(b.amount_paid, 0) + COALESCE(b.downpayment_amount, 0) as total_paid,
                   b.total_amount - (COALESCE(b.amount_paid, 0) + COALESCE(b.downpayment_amount, 0)) as remaining_balance
            FROM bookings b 
            LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id 
            WHERE b.booking_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $booking_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception('Booking not found');
    }

    $booking = mysqli_fetch_assoc($result);
    
    // Debug: Log ALL booking data to the server error log
    error_log("FULL BOOKING DATA: " . print_r($booking, true));

    // Format payment proof path
    $payment_proof = null;
    if (!empty($booking['payment_proof'])) {
        // Get just the filename from the stored path
        $filename = basename($booking['payment_proof']);
        
        // Log the original payment proof value and filename
        error_log("Original payment_proof: " . $booking['payment_proof']);
        error_log("Extracted filename: " . $filename);
        
        // Return just the filename - the frontend will construct the full path
        $payment_proof = $filename;
    }

    // Prepare response data
    $response = [
        'success' => true,
        'data' => [
            'booking_id' => $booking['booking_id'],
            'first_name' => $booking['first_name'],
            'last_name' => $booking['last_name'],
            'email' => $booking['email'],
            'contact' => $booking['contact'],
            'check_in' => date('M j, Y', strtotime($booking['check_in'])),
            'check_out' => date('M j, Y', strtotime($booking['check_out'])),
            'nights' => $booking['nights'],
            'room_type_id' => $booking['room_type_id'],
            'room_type' => $booking['room_type_name'],
            'room_price' => $booking['room_price'],
            'total_amount' => $booking['total_amount'],
            'payment_option' => ucfirst($booking['payment_option']),
            'payment_method' => ucfirst($booking['payment_method']),
            'amount_paid' => $booking['total_paid'],
            'remaining_balance' => $booking['remaining_balance'],
            'discount_type' => $booking['discount_type'] ? ucfirst($booking['discount_type']) : 'Regular',
            'status' => ucfirst($booking['status']),
            'payment_reference' => $booking['payment_reference'],
            'payment_proof' => $payment_proof,
            'number_of_guests' => $booking['number_of_guests']
        ]
    ];

    // Debug: Log the final response to the server error log
    error_log("DEBUG: Final response: " . print_r($response, true));

    // Clear any previous output and send response
    ob_clean();
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in fetch_booking_details.php: " . $e->getMessage());
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();