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

    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([$notification_id, $user_id]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 