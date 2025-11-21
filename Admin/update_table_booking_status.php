<?php
require_once 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Log raw POST data
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST array: " . print_r($_POST, true));

try {
    if(isset($_POST['id']) && isset($_POST['status'])) {
        $id = mysqli_real_escape_string($con, $_POST['id']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        
        error_log("Updating table booking - ID: $id, Status: $status");
        
        // First check if the record exists
        $check_query = "SELECT id FROM table_bookings WHERE id = ?";
        $check_stmt = mysqli_prepare($con, $check_query);
        
        if (!$check_stmt) {
            throw new Exception('Failed to prepare check query: ' . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($check_stmt, 'i', $id);
        
        if (!mysqli_stmt_execute($check_stmt)) {
            throw new Exception('Failed to execute check query: ' . mysqli_stmt_error($check_stmt));
        }
        
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) == 0) {
            throw new Exception("Booking not found with ID: $id");
        }
        
        mysqli_stmt_close($check_stmt);
        
        // Update the status using prepared statement
        $update_query = "UPDATE table_bookings SET status = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        
        if (!$update_stmt) {
            throw new Exception('Failed to prepare update query: ' . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($update_stmt, 'si', $status, $id);
        
        if (!mysqli_stmt_execute($update_stmt)) {
            throw new Exception('Failed to execute update query: ' . mysqli_stmt_error($update_stmt));
        }
        
        if (mysqli_stmt_affected_rows($update_stmt) > 0) {
            error_log("Successfully updated booking $id to status $status");
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("No changes made to booking $id");
        }
        
        mysqli_stmt_close($update_stmt);
        
    } else {
        throw new Exception('Missing required parameters: id and status');
    }
} catch (Exception $e) {
    error_log("Error in update_table_booking_status.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
