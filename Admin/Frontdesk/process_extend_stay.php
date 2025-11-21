<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Debug log function
function debug_log($message, $data = null) {
    error_log(date('Y-m-d H:i:s') . " - " . $message);
    if ($data !== null) {
        error_log(print_r($data, true));
    }
}

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Debug log POST data
    debug_log('Received POST data:', $_POST);

    // Required fields
    $required_fields = ['booking_id', 'new_checkout', 'additional_nights', 'additional_amount', 'total_amount', 'payment_method'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Get form data
    $booking_id = intval($_POST['booking_id']);
    $new_checkout = $_POST['new_checkout'];
    $additional_nights = intval($_POST['additional_nights']);
    $additional_amount = floatval($_POST['additional_amount']);
    $total_amount = floatval($_POST['total_amount']);
    $payment_method = $_POST['payment_method'];

    // Debug log processed data
    debug_log('Processed form data:', [
        'booking_id' => $booking_id,
        'new_checkout' => $new_checkout,
        'additional_nights' => $additional_nights,
        'additional_amount' => $additional_amount,
        'total_amount' => $total_amount,
        'payment_method' => $payment_method
    ]);

    // Database connection
    require_once 'db_connection.php';
    $conn->begin_transaction();

    // Get current booking details
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        throw new Exception("Booking not found");
    }

    debug_log('Current booking details:', $booking);

    // Calculate new amounts
    $current_total = floatval($booking['total_amount']);
    $current_amount_paid = floatval($booking['amount_paid']);
    $new_total_amount = $total_amount;
    $new_remaining_balance = $new_total_amount - $current_amount_paid;

    debug_log('Calculated amounts:', [
        'current_total' => $current_total,
        'current_amount_paid' => $current_amount_paid,
        'new_total_amount' => $new_total_amount,
        'new_remaining_balance' => $new_remaining_balance
    ]);

    // Update booking
    $update_sql = "UPDATE bookings SET 
        check_out = ?, 
        total_amount = ?, 
        remaining_balance = ?, 
        last_payment_method = ?, 
        status = 'checked_in' 
        WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        throw new Exception("Error preparing update statement: " . $conn->error);
    }

    $stmt->bind_param("sddsi", 
        $new_checkout, 
        $new_total_amount, 
        $new_remaining_balance, 
        $payment_method, 
        $booking_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Error updating booking: " . $stmt->error);
    }

    debug_log('Booking updated successfully');

    // Log the extension
    $log_sql = "INSERT INTO booking_logs (booking_id, action, details, created_at) VALUES (?, 'extend_stay', ?, NOW())";
    $log_details = json_encode([
        'additional_nights' => $additional_nights,
        'additional_amount' => $additional_amount,
        'new_checkout' => $new_checkout,
        'payment_method' => $payment_method
    ]);

    $stmt = $conn->prepare($log_sql);
    $stmt->bind_param("is", $booking_id, $log_details);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Stay extended successfully',
        'data' => [
            'booking_id' => $booking_id,
            'new_checkout' => $new_checkout,
            'additional_nights' => $additional_nights,
            'additional_amount' => $additional_amount,
            'new_total_amount' => $new_total_amount,
            'amount_paid' => $current_amount_paid,
            'new_remaining_balance' => $new_remaining_balance
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }

    debug_log('Error occurred:', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 