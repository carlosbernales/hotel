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
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
    } else {
        header("Location: login.php");
    }
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);
    
    // Validate message
    if (empty($message)) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        } else {
            $_SESSION['message_status'] = 'error';
            header("Location: messages.php");
        }
        exit();
    }
    
    // Check connection
    if (!isset($con) || !$con) {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        } else {
            $_SESSION['message_status'] = 'error';
            header("Location: messages.php");
        }
        exit();
    }
    
    // Insert message
    $query = "INSERT INTO messages (user_id, message, sender_type, created_at) VALUES (?, ?, 'user', NOW())";
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
        $success = mysqli_stmt_execute($stmt);
        
        if ($success) {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent successfully',
                    'message_id' => mysqli_insert_id($con)
                ]);
            } else {
                $_SESSION['message_status'] = 'success';
                header("Location: messages.php");
            }
        } else {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to send message']);
            } else {
                $_SESSION['message_status'] = 'error';
                header("Location: messages.php");
            }
        }
        
        mysqli_stmt_close($stmt);
    } else {
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
        } else {
            $_SESSION['message_status'] = 'error';
            header("Location: messages.php");
        }
    }
} else {
    if (isAjaxRequest()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    } else {
        header("Location: messages.php");
    }
}

// Helper function to check if request is AJAX
function isAjaxRequest() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
           (isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}
?> 