<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'message_debug.log');

// Log start of script
error_log("get_message_data.php started");

// Include database connection
require_once 'db.php';

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if post request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get message ID
$message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

// Log received data
error_log("Request for message ID: " . $message_id);

// Validate message ID
if ($message_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

try {
    // Query the database for the message data
    $stmt = $con->prepare("SELECT * FROM messages WHERE id = ?");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param('i', $message_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Message not found");
    }
    
    $message_data = $result->fetch_assoc();
    $stmt->close();
    
    // Return success response with message data
    echo json_encode([
        'success' => true, 
        'message' => 'Message data retrieved successfully',
        'data' => $message_data
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log("ERROR: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to get message data: ' . $e->getMessage()
    ]);
}

// End script
exit;
?> 