<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    
    $sql = "DELETE FROM messages WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $message_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $con->error]);
    }
}
?> 