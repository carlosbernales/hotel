<?php
require_once 'db.php';

header('Content-Type: application/json');

// Add error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Log incoming data
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate input
    if (!isset($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }
    
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method'] ?? 'Cash');
    
    // Debug: Log processed values
    error_log("Processed values: booking_id=$booking_id, payment_method=$payment_method");

    // Debug: Log connection status
    if (!$con) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }

    // Begin transaction
    mysqli_begin_transaction($con);
    
    // Update booking status
    $update_sql = "UPDATE bookings SET 
                   status = 'Checked Out',
                   payment_method = ?
                   WHERE booking_id = ?";
    
    $stmt = $con->prepare($update_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param("ss", $payment_method, $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("No booking found with ID: " . $booking_id);
    }
    
    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode([
        'success' => true,
        'message' => 'Check out processed successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con) && $con->ping()) {
        mysqli_rollback($con);
    }
    
    error_log("Checkout Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($con)) {
    mysqli_close($con);
}
?> 