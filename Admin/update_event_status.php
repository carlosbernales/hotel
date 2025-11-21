<?php
require_once 'db.php';

// Set header to return JSON response
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if required parameters are provided
if (!isset($_POST['event_id']) || !isset($_POST['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$event_id = mysqli_real_escape_string($con, $_POST['event_id']);
$action = mysqli_real_escape_string($con, $_POST['action']);

// Start transaction
mysqli_begin_transaction($con);

try {
    // Get the event details first
    $query = "SELECT * FROM event_bookings WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);

    if (!$event) {
        throw new Exception('Event booking not found');
    }

    // Update the event status based on the action
    $new_status = '';
    switch ($action) {
        case 'confirm':
            $new_status = 'confirmed';
            break;
        case 'reject':
            $new_status = 'rejected';
            break;
        case 'complete':
            $new_status = 'completed';
            break;
        default:
            throw new Exception('Invalid action');
    }

    // Update the event status - Note the capital S in Status
    $update_query = "UPDATE event_bookings SET booking_status = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "ss", $new_status, $event_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update event status');
    }

    // If everything is successful, commit the transaction
    mysqli_commit($con);

    // Send email notification (you can implement this later)
    // sendEventStatusNotification($event, $new_status);

    echo json_encode([
        'success' => true,
        'message' => 'Event status updated successfully',
        'redirect' => 'booking_status.php'
    ]);

} catch (Exception $e) {
    // If there's an error, rollback the transaction
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
mysqli_close($con);
?> 