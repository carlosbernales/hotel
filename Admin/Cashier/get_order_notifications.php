<?php
session_start();
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get unread order notifications count
    $unreadQuery = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND type = 'order' AND is_read = 0";
    $stmt = $pdo->prepare($unreadQuery);
    $stmt->execute([$userId]);
    $unreadCount = $stmt->fetch()['count'];
    
    // Get recent order notifications
    $notificationsQuery = "SELECT id, title, message, created_at, is_read 
                          FROM notifications 
                          WHERE user_id = ? AND type = 'order' 
                          ORDER BY created_at DESC 
                          LIMIT 5";
    $stmt = $pdo->prepare($notificationsQuery);
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll();
    
    // Format notifications for display
    $formattedNotifications = [];
    foreach ($notifications as $notification) {
        $formattedNotifications[] = [
            'id' => $notification['id'],
            'title' => htmlspecialchars($notification['title']),
            'message' => htmlspecialchars($notification['message']),
            'is_read' => (bool)$notification['is_read'],
            'time_ago' => getTimeAgo($notification['created_at'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'unread_count' => (int)$unreadCount,
        'notifications' => $formattedNotifications
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching order notifications: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'unread_count' => 0,
        'notifications' => []
    ]);
}

// Helper function to calculate time ago
function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $time);
    }
}
?>
