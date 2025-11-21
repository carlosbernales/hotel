<?php
require 'db.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mark all notifications as read
$sql = "UPDATE notifications SET is_read = 1";

if ($con->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $con->error]);
}

$con->close();
?>
