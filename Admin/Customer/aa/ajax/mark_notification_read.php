<?php
session_start();
require_once '../db_con.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['notification_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->connect();

    $notification_id = (int)$_POST['notification_id'];
    $user_id = (int)$_SESSION['user_id'];

    // Update the notification status
    $query = "UPDATE notifications SET is_read = 1 
              WHERE id = ? AND user_id = ? AND is_read = 0";
    
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([$notification_id, $user_id]);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to mark notification as read'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 