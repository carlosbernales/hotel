<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
require_once 'db.php';

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

    // Required fields for all cases
    $required_fields = ['booking_id', 'new_checkout', 'additional_nights', 'additional_amount', 'payment_option'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Additional required fields when paying now
    if ($_POST['payment_option'] === 'now') {
        if (!isset($_POST['payment_method']) || empty($_POST['payment_method'])) {
            throw new Exception("Payment method is required when paying now");
        }
    }

    // Get form data
    $booking_id = $_POST['booking_id'];
    $new_checkout = $_POST['new_checkout'];
    $additional_nights = intval($_POST['additional_nights']);
    $additional_amount = floatval(str_replace(['₱', ','], '', $_POST['additional_amount']));
    $payment_option = $_POST['payment_option'];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    // Debug log processed data
    debug_log('Processed form data:', [
        'booking_id' => $booking_id,
        'new_checkout' => $new_checkout,
        'additional_nights' => $additional_nights,
        'additional_amount' => $additional_amount,
        'payment_option' => $payment_option,
        'payment_method' => $payment_method
    ]);

    // Start transaction
    mysqli_begin_transaction($con);

    // Get current booking details
    $stmt = $con->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    if (!$stmt) {
        throw new Exception("Error preparing select statement: " . $con->error);
    }
    
    $stmt->bind_param("s", $booking_id);
    if (!$stmt->execute()) {
        throw new Exception("Error executing select query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Booking not found with ID: " . $booking_id);
    }
    
    $booking = $result->fetch_assoc();
    debug_log('Current booking details:', $booking);

    // Calculate new amounts
    $new_total_amount = $booking['total_amount'] + $additional_amount;
    $new_amount_paid = $booking['amount_paid']; // Keep the original amount_paid
    $new_remaining_balance = $booking['remaining_balance'];

    // Update amounts based on payment option
    if ($payment_option === 'later') {
        // If paying later, add to remaining balance
        $new_remaining_balance = $new_total_amount - $new_amount_paid;
    } else {
        // If paying now, add to amount paid and recalculate remaining balance
        $new_amount_paid += $additional_amount;
        $new_remaining_balance = $new_total_amount - $new_amount_paid;
    }

    debug_log('New amounts:', [
        'new_total_amount' => $new_total_amount,
        'new_amount_paid' => $new_amount_paid,
        'new_remaining_balance' => $new_remaining_balance,
        'payment_option' => $payment_option
    ]);

    // Update booking with new check-out date and amounts
    $update_sql = "UPDATE bookings SET 
        check_out = ?,
        total_amount = ?,
        amount_paid = ?,
        remaining_balance = ?,
        status = 'Extended' 
        WHERE booking_id = ?";

    $stmt = $con->prepare($update_sql);
    if (!$stmt) {
        throw new Exception("Error preparing update statement: " . $con->error);
    }

    $stmt->bind_param("sddds", 
        $new_checkout, 
        $new_total_amount,
        $new_amount_paid,
        $new_remaining_balance,
        $booking_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Error updating booking: " . $stmt->error);
    }
    
    $affected_rows = $stmt->affected_rows;
    debug_log('Update affected ' . $affected_rows . ' rows');
    
    if ($affected_rows === 0) {
        throw new Exception("Failed to update booking. No rows affected.");
    }

    // Record payment if paying now
    if ($payment_option === 'now') {
        $payment_sql = "INSERT INTO payments (booking_id, amount, payment_method, payment_date) 
                       VALUES (?, ?, ?, NOW())";
        $stmt = $con->prepare($payment_sql);
        if (!$stmt) {
            throw new Exception("Error preparing payment insert: " . $con->error);
        }
        
        $stmt->bind_param("sds", $booking_id, $additional_amount, $payment_method);
        if (!$stmt->execute()) {
            throw new Exception("Error recording payment: " . $stmt->error);
        }
    }

    // Create a record of the extension in booking logs
    try {
        $log_sql = "INSERT INTO booking_logs (booking_id, action, details, created_at) 
                    VALUES (?, 'extend_stay', ?, NOW())";
        $log_details = json_encode([
            'additional_nights' => $additional_nights,
            'additional_amount' => $additional_amount,
            'new_checkout' => $new_checkout,
            'payment_option' => $payment_option,
            'payment_method' => $payment_method,
            'original_checkout' => $booking['check_out'],
            'previous_total' => $booking['total_amount'],
            'new_total' => $new_total_amount,
            'previous_amount_paid' => $booking['amount_paid'],
            'new_amount_paid' => $new_amount_paid,
            'previous_remaining' => $booking['remaining_balance'],
            'new_remaining' => $new_remaining_balance
        ]);

        $log_stmt = $con->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("ss", $booking_id, $log_details);
            $log_stmt->execute();
        }
    } catch (Exception $log_error) {
        // Just log the error but don't fail the transaction
        debug_log('Warning: Failed to create log entry: ' . $log_error->getMessage());
    }

    // Commit transaction
    mysqli_commit($con);

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
            'new_amount_paid' => $new_amount_paid,
            'new_remaining_balance' => $new_remaining_balance,
            'payment_option' => $payment_option
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($con) && $con->connect_errno === 0) {
        mysqli_rollback($con);
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