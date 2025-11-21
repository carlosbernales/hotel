<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id']) && isset($_SESSION['user_id'])) {
    $message_id = $_POST['message_id'];
    $user_id = $_SESSION['user_id'];

    // Update message as read
    $sql = "UPDATE messages SET is_read = 1 WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $message_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $con->error]);
    }
}
?> 