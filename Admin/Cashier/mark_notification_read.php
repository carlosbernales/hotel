<?php
session_start();
require_once 'db.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = $data['notification_id'] ?? null;

if ($notificationId) {
    // Update the notification status to read (is_read = 1)
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $notificationId);
    
    if ($stmt->execute()) {
        // Query to check remaining unread notifications
        $countQuery = "SELECT COUNT(*) as count FROM notifications WHERE type = 'order' AND is_read = 0";
        $result = $con->query($countQuery);
        $remainingCount = $result->fetch_assoc()['count'];
        
        echo json_encode([
            'success' => true,
            'remaining_count' => $remainingCount,
            'message' => 'Notification marked as read successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update notification status',
            'sql_error' => $con->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid notification ID'
    ]);
}

// Close the database connection
$stmt->close();
$con->close();
?> 