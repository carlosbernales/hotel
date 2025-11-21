<?php
// Start output buffering
ob_start();

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'message_errors.log');

// Log start of script
error_log("send_message.php started");

// Include database connection
require_once 'db.php';

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Clear any previous output
ob_clean();

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'timestamp' => '',
    'debug' => []
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method. Only POST is allowed.';
    echo json_encode($response);
    exit;
}

// Check if all required parameters are present
if (!isset($_POST['message']) || !isset($_POST['user_id'])) {
    $response['message'] = 'Missing required parameters. Need message and user_id.';
    echo json_encode($response);
    exit;
}

// Get parameters
$message = trim($_POST['message']);
$userId = intval($_POST['user_id']);
$senderType = isset($_POST['sender_type']) ? $_POST['sender_type'] : 'admin';
$replyToId = isset($_POST['reply_to_id']) ? intval($_POST['reply_to_id']) : null;

// Validate parameters
if (empty($message)) {
    $response['message'] = 'Message cannot be empty.';
    echo json_encode($response);
    exit;
}

if ($userId <= 0) {
    $response['message'] = 'Invalid user ID.';
    echo json_encode($response);
    exit;
}

// Validate sender type
if (!in_array($senderType, ['user', 'admin', 'system'])) {
    $senderType = 'admin';
}

try {
    // Check if the user exists
    $checkUserSql = "SELECT id FROM userss WHERE id = ?";
    $checkUserStmt = $con->prepare($checkUserSql);
    $checkUserStmt->bind_param("i", $userId);
    $checkUserStmt->execute();
    $userExists = $checkUserStmt->get_result()->num_rows > 0;
    
    // Add debug info
    $response['debug']['user_exists'] = $userExists;
    
    // Insert message
    $insertSql = "INSERT INTO messages (user_id, message, sender_type, read_status, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
    $insertStmt = $con->prepare($insertSql);
    
    // If sender is admin, mark as read (1), otherwise unread (0)
    $readStatus = ($senderType === 'admin') ? 1 : 0;
    
    $insertStmt->bind_param("issi", $userId, $message, $senderType, $readStatus);
    $success = $insertStmt->execute();
    
    if ($success) {
        // Get the ID of the newly inserted message
        $messageId = $con->insert_id;
        
        // Get the timestamp of the message
        $timestampSql = "SELECT created_at FROM messages WHERE id = ?";
        $timestampStmt = $con->prepare($timestampSql);
        $timestampStmt->bind_param("i", $messageId);
        $timestampStmt->execute();
        $timestampResult = $timestampStmt->get_result();
        
        if ($timestampResult && $row = $timestampResult->fetch_assoc()) {
            $timestamp = strtotime($row['created_at']);
            $formattedTime = date('g:i A', $timestamp);
            $response['timestamp'] = $formattedTime;
        }
        
        // If this is a reply to an existing message, update the replies field
        if ($replyToId) {
            // Get the original message
            $getOriginalSql = "SELECT * FROM messages WHERE id = ?";
            $getOriginalStmt = $con->prepare($getOriginalSql);
            $getOriginalStmt->bind_param("i", $replyToId);
            $getOriginalStmt->execute();
            $originalResult = $getOriginalStmt->get_result();
            
            if ($originalResult && $originalRow = $originalResult->fetch_assoc()) {
                // Format the reply text
                $replyText = "Admin: " . $message;
                
                // Get existing replies
                $existingReplies = $originalRow['replies'] ?? '';
                
                // Add new reply
                $updatedReplies = !empty($existingReplies) ? $existingReplies . "\n" . $replyText : $replyText;
                
                // Update the original message with the new reply
                $updateRepliesSql = "UPDATE messages SET replies = ? WHERE id = ?";
                $updateRepliesStmt = $con->prepare($updateRepliesSql);
                $updateRepliesStmt->bind_param("si", $updatedReplies, $replyToId);
                $updateRepliesStmt->execute();
                
                $response['debug']['reply_updated'] = true;
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Message sent successfully.';
        $response['message_id'] = $messageId;
    } else {
        $response['message'] = 'Failed to send message: ' . $con->error;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    $response['debug']['error'] = $e->getMessage();
}

// Return response
echo json_encode($response);
exit;
?> 