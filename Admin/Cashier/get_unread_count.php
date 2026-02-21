<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

try {
    // Get unread message count using the actual database structure
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE user_id = ? AND read_status = 0");
    $stmt->execute([$user_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo $count;
} catch(PDOException $e) {
    echo '0';
}
?>
