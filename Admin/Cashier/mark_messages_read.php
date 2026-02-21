<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

try {
    // Mark all messages as read using the actual database structure
    $stmt = $pdo->prepare("UPDATE messages SET read_status = 1, status = 'read' WHERE user_id = ? AND read_status = 0");
    $stmt->execute([$user_id]);
    
    echo json_encode(['success' => true, 'message' => 'All messages marked as read']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
