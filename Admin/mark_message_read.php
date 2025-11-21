<?php
// Simple database update script - no redirects, just update and return success
require_once 'db.php';

// Set headers
header('Content-Type: application/json');

// Get message ID
$message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

// Validate ID
if ($message_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

// Simply update the read status
$stmt = $con->prepare("UPDATE messages SET read_status = 1 WHERE id = ?");
$stmt->bind_param('i', $message_id);
$result = $stmt->execute();

// Return success/failure
if ($result) {
    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
?> 