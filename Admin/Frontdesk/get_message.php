<?php
require_once 'db.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Set JSON header

if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];
    
    // Simple query to get the message
    $query = "SELECT * FROM messages WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($message = $result->fetch_assoc()) {
        echo json_encode($message);
    } else {
        echo json_encode(['error' => 'Message not found']);
    }
} else {
    echo json_encode([
        'error' => 'No message ID provided',
        'get_data' => $_GET
    ]);
}
?> 