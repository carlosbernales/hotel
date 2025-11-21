<?php
require_once 'db.php';
session_start();

debug_log("process_room_transfer.php started execution.");

// Set the content type to JSON
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'room_transfer_errors.log');

// Debug log function
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message . "\n", 3, 'room_transfer_errors.log');
}

// Function to check if a table exists
function tableExists($con, $tableName) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Log incoming data
debug_log('Room transfer request received', $_POST);

try {
    // Get POST data
    $booking_id = $_POST['booking_id'] ?? null;
    $new_room_type_id = $_POST['new_room_id'] ?? null;
    $new_room_number = $_POST['room_number'] ?? null;
    $transfer_reason = $_POST['transfer_reason'] ?? null;
    $current_room_id = $_POST['current_room_id'] ?? null;
    
    // Log received data
    debug_log("Received transfer request", [
        'booking_id' => $booking_id,
        'new_room_type_id' => $new_room_type_id,
        'new_room_number' => $new_room_number,
        'transfer_reason' => $transfer_reason,
        'current_room_id' => $current_room_id
    ]);

    // Validate required fields
    if (!$booking_id || !$new_room_type_id || !$new_room_number || !$transfer_reason || !$current_room_id) {
        debug_log("Missing required fields", [
            'booking_id' => $booking_id,
            'new_room_type_id' => $new_room_type_id,
            'new_room_number' => $new_room_number,
            'transfer_reason' => $transfer_reason,
            'current_room_id' => $current_room_id
        ]);
        throw new Exception("Missing required fields");
    }

    // Get current booking details
    $stmt = mysqli_prepare($con, "SELECT room_type_id, room_number, status FROM bookings WHERE booking_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $booking_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($booking_result) === 0) {
        debug_log("Booking not found", ['booking_id' => $booking_id]);
        throw new Exception("Booking not found");
    }
    
    $booking = mysqli_fetch_assoc($booking_result);
    $current_room_type_id = $booking['room_type_id'];
    $current_room_number = $booking['room_number'];
    
    // Verify booking status (case-insensitive check)
    $booking_status_lower = strtolower(trim($booking['status']));
    if ($booking_status_lower !== 'checked in' && $booking_status_lower !== 'extended') {
        debug_log("Invalid booking status", ['status' => $booking['status']]);
        throw new Exception("Booking must be in 'Checked in' or 'Extended' status");
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // 1. Update old room status to active (keep it active since it's still being used)
        if ($current_room_number) {
            $update_old_room = "UPDATE room_numbers SET status = 'active' WHERE room_number = ?";
            $stmt = mysqli_prepare($con, $update_old_room);
            mysqli_stmt_bind_param($stmt, "s", $current_room_number);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update old room status: " . mysqli_stmt_error($stmt));
            }
            debug_log("Updated old room status to active", ['room_number' => $current_room_number]);
        }

        // 2. Update new room status to occupied
        $update_new_room = "UPDATE room_numbers SET status = 'occupied' WHERE room_number = ?";
        $stmt = mysqli_prepare($con, $update_new_room);
        mysqli_stmt_bind_param($stmt, "s", $new_room_number);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update new room status: " . mysqli_stmt_error($stmt));
        }
        debug_log("Updated new room status to occupied", ['room_number' => $new_room_number]);

        // 3. Update booking with new room details
        $update_booking = "UPDATE bookings SET 
                          room_type_id = ?,
                          room_number = ?,
                          status = 'Checked in'
                          WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $update_booking);
        mysqli_stmt_bind_param($stmt, "isi", $new_room_type_id, $new_room_number, $booking_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update booking: " . mysqli_stmt_error($stmt));
        }
        debug_log("Updated booking with new room details", [
            'booking_id' => $booking_id,
            'new_room_type_id' => $new_room_type_id,
            'new_room_number' => $new_room_number
        ]);

        // Commit transaction
        mysqli_commit($con);
        debug_log("Room transfer completed successfully");
        
        $response_data = [
            'success' => true,
            'message' => 'Room transfer completed successfully'
        ];
        debug_log("Sending success response", $response_data);
        echo json_encode($response_data);

    } catch (Exception $e) {
        mysqli_rollback($con);
        $error_message = $e->getMessage();
        debug_log("Transaction failed", [
            'error' => $error_message,
            'trace' => $e->getTraceAsString()
        ]);
        
        $response_data = [
            'success' => false,
            'message' => $error_message
        ];
        debug_log("Sending error response", $response_data);
        echo json_encode($response_data);
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
    debug_log("Top-level catch: " . $error_message);
    echo json_encode([
        'success' => false,
        'message' => $error_message
    ]);
}

// Close database connection
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
if (isset($con)) {
    mysqli_close($con);
}
?> 