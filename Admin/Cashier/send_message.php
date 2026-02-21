<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'send_message':
            $message = trim($_POST['message'] ?? '');
            $recipient_type = $_POST['recipient_type'] ?? 'customer';
            $recipient_id = $_POST['recipient_id'] ?? null;
            
            if (empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                exit();
            }
            
            try {
                // Debug session data
                error_log("Session data: " . print_r($_SESSION, true));
                
                $current_user_id = $_SESSION['user_id'] ?? 0;
                $sender_type = $_SESSION['user_type'] ?? 'cashier';
                
                // Debug logging
                error_log("Attempting to insert message - User ID: $current_user_id, Message: $message, Sender Type: cashier");
                error_log("Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET'));
                error_log("Session User Type: " . ($_SESSION['user_type'] ?? 'NOT SET'));
                
                // Check if user_id is valid
                if ($current_user_id == 0) {
                    echo json_encode(['success' => false, 'message' => 'User not logged in or invalid session']);
                    exit();
                }
                
                // Insert message into messages table with recipient info
                $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, sender_type, read_status, status, created_at) VALUES (?, ?, ?, 0, 'unread', NOW())");
                $result = $stmt->execute([$current_user_id, $message, 'cashier']);
                
                // Debug logging
                error_log("Insert result: " . ($result ? 'SUCCESS' : 'FAILED'));
                error_log("Last insert ID: " . $pdo->lastInsertId());
                
                // If email notification is requested
                if (isset($_POST['send_notification']) && $_POST['send_notification'] === 'true') {
                    error_log("Email notification requested for message: " . $message);
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Message sent successfully!',
                    'message_id' => $pdo->lastInsertId(),
                    'recipient_type' => $recipient_type,
                    'recipient_id' => $recipient_id
                ]);
                
            } catch(PDOException $e) {
                error_log("Database error in send_message: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        case 'mark_read':
            $message_id = $_POST['message_id'] ?? 0;
            $current_user_id = $_SESSION['user_id'] ?? 0;
            $current_user_type = $_SESSION['user_type'] ?? 'cashier';
            
            try {
                // Mark as read for current user
                $stmt = $pdo->prepare("UPDATE messages SET read_status = 1 WHERE id = ? AND user_id = ?");
                $stmt->execute([$message_id, $current_user_id]);
                
                echo json_encode(['success' => true, 'message' => 'Message marked as read']);
                
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        case 'get_messages':
            $current_user_id = $_SESSION['user_id'] ?? 0;
            $current_user_type = $_SESSION['user_type'] ?? 'cashier';
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 50;
            
            try {
                // Get messages for current user
                $stmt = $pdo->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
                $stmt->execute([$current_user_id, $limit]);
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get unread count
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE user_id = ? AND read_status = 0");
                $stmt->execute([$current_user_id]);
                $unread_result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'messages' => $messages,
                    'unread_count' => $unread_result['count']
                ]);
                
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        case 'get_recipients':
            $recipient_type = $_POST['recipient_type'] ?? 'customer';
            
            try {
                $users = [];
                
                if ($recipient_type === 'admin') {
                    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM userss WHERE user_type = 'admin'");
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } elseif ($recipient_type === 'frontdesk') {
                    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM userss WHERE user_type = 'frontdesk'");
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } elseif ($recipient_type === 'customer') {
                    // For customers, get from the users table (not userss) as that's where customers are stored
                    $stmt = $pdo->prepare("SELECT id, first_name as first_name, last_name as last_name FROM userss");
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                
                echo json_encode([
                    'success' => true,
                    'recipients' => $users
                ]);
                
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>