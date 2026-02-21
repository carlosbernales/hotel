<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_con.php';

// Only start session if one isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Get the last message ID from the request
$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
$userId = $_SESSION['user_id'];

// Check connection
if (!isset($con) || !$con) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get new messages
$query = "SELECT m.*, 
         CASE 
            WHEN m.sender_type = 'admin' THEN 'Admin' 
            WHEN m.sender_type = 'system' THEN 'System' 
            WHEN m.sender_type = 'user' THEN 'You'
            ELSE m.sender_type
         END as sender_name,
         CASE
            WHEN m.sender_type = 'admin' THEN 'primary'
            WHEN m.sender_type = 'system' THEN 'info'
            WHEN m.sender_type = 'user' THEN 'secondary'
            ELSE 'dark'
         END as sender_color
         FROM messages m
         WHERE m.user_id = ? AND m.id > ?
         ORDER BY m.created_at ASC";

$stmt = mysqli_prepare($con, $query);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit();
}

mysqli_stmt_bind_param($stmt, "ii", $userId, $lastId);

if (!mysqli_stmt_execute($stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Query execution failed']);
    exit();
}

$result = mysqli_stmt_get_result($stmt);
$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

// Mark messages as read
if (!empty($messages)) {
    $markReadQuery = "UPDATE messages SET read_status = 1 WHERE user_id = ? AND id > ? AND sender_type != 'user'";
    $markReadStmt = mysqli_prepare($con, $markReadQuery);
    
    if ($markReadStmt) {
        mysqli_stmt_bind_param($markReadStmt, "ii", $userId, $lastId);
        mysqli_stmt_execute($markReadStmt);
        mysqli_stmt_close($markReadStmt);
    }
}

mysqli_stmt_close($stmt);

// Return the messages as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'messages' => $messages]); 