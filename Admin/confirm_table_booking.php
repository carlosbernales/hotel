<?php
// Ensure no output before headers
ob_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display_errors to prevent HTML errors

require_once 'db.php';

// Set headers
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        $new_status = '';
        
        switch($_POST['action']) {
            case 'confirm':
                $new_status = 'Confirmed';
                break;
            case 'mark_done':
                $new_status = 'Done';
                break;
            default:
                throw new Exception('Invalid action');
        }
        
        // Update booking status
        $update_sql = "UPDATE table_bookings SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_sql);
        
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
        }
        
        // Check if any rows were affected
        if (mysqli_stmt_affected_rows($stmt) === 0) {
            throw new Exception("No booking found with ID: " . $booking_id);
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        $message = $new_status === 'Confirmed' ? 'Booking confirmed successfully' : 'Booking marked as done successfully';
        
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    // Close statement
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
