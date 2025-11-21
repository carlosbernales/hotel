<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['booking_id']) || !isset($_POST['action']) || !isset($_POST['reason'])) {
        throw new Exception('Missing required parameters');
    }

    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    $action = mysqli_real_escape_string($con, $_POST['action']);
    $reason = mysqli_real_escape_string($con, $_POST['reason']);

    if ($action !== 'reject') {
        throw new Exception('Invalid action');
    }

    if (empty($reason)) {
        throw new Exception('Rejection reason is required');
    }

    // Update the booking status to rejected and add cancellation reason
    $update_sql = "UPDATE table_bookings SET 
                    status = 'Rejected', 
                    cancellation_reason = ?,
                    cancelled_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
    
    $stmt = mysqli_prepare($con, $update_sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, 'si', $reason, $booking_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update booking: ' . mysqli_stmt_error($stmt));
    }

    if (mysqli_affected_rows($con) === 0) {
        throw new Exception('No booking found with the provided ID');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Booking rejected successfully'
    ]);

} catch (Exception $e) {
    error_log('Error rejecting booking: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 