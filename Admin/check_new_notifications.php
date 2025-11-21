<?php
require 'db.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the last notification ID that was shown (stored in session)
$lastShownId = isset($_SESSION['last_notification_id']) ? $_SESSION['last_notification_id'] : 0;

// Query for new notifications
$sql = "SELECT id, title, message, type FROM notifications 
        WHERE id > ? AND is_read = 0 
        ORDER BY created_at DESC LIMIT 1";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $lastShownId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Update the last shown notification ID in session
    $_SESSION['last_notification_id'] = $row['id'];
    
    // Get total unread notifications count
    $count_sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
    $count_result = $con->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    
    echo json_encode([
        'hasNew' => true,
        'title' => $row['title'],
        'message' => $row['message'],
        'type' => $row['type'],
        'count' => $count_row['count']
    ]);
} else {
    echo json_encode([
        'hasNew' => false
    ]);
}

$con->close();
?> 