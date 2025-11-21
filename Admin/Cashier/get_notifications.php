<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated',
        'notifications' => [],
        'count' => 0
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch unread order notifications
$query = "SELECT n.*, u.name as sender_name 
          FROM notifications n 
          LEFT JOIN userss u ON n.user_id = u.id 
          WHERE n.type = 'order' 
          AND n.is_read = 0 
          ORDER BY n.created_at DESC";

$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications)
]);
?> 