<?php
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if message_id is provided
if (!isset($_POST['message_id']) || !is_numeric($_POST['message_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

$message_id = (int)$_POST['message_id'];

// First verify the message exists
$check = $con->prepare("SELECT id FROM messages WHERE id = ?");
$check->bind_param('i', $message_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit;
}
$check->close();

// Delete the message
$stmt = $con->prepare("DELETE FROM messages WHERE id = ?");
$stmt->bind_param('i', $message_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete message: ' . $con->error]);
}

$stmt->close();
?> 