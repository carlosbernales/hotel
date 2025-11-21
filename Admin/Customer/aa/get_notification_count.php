<?php
session_start();
require_once 'db_con.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $pdo = $db->connect();

    $user_id = $_SESSION['user_id'] ?? 0;
    
    $query = "SELECT COUNT(*) as count FROM notifications 
              WHERE user_id = ? AND is_read = 0";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['count' => $result['count']]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
} 