<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function for debugging
function debug_log($message, $data = null) {
    $log = date('[Y-m-d H:i:s] ') . $message;
    if ($data !== null) {
        $log .= "\nData: " . print_r($data, true);
    }
    $log .= "\n";
    file_put_contents('chat_debug.log', $log, FILE_APPEND);
}

// Log all POST data
debug_log("Incoming request", $_POST);

try {
    require_once 'db_con.php';  // Make sure this points to your database connection file
    debug_log("Database connection successful");
} catch (Exception $e) {
    debug_log("Database connection failed", $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Log errors to a file
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'chat_error.log');
}

// Check if action is set
if (!isset($_POST['action'])) {
    debug_log("No action specified");
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];
debug_log("Action received", $action);

switch ($action) {
    case 'send':
        if (!isset($_SESSION['user_id']) || !isset($_POST['message'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $message = sanitize_input($_POST['message']);
        
        try {
            // Store the user message
            $sql = "INSERT INTO messages (user_id, message, sender_type) VALUES (?, ?, 'user')";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
            $result = mysqli_stmt_execute($stmt);
            
            if (!$result) {
                throw new Exception("Failed to save message");
            }

            // Auto-response has been removed as requested
            
            echo json_encode([
                'success' => true,
                'message' => 'Message sent successfully'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case 'fetch':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $last_time = $_POST['last_time'] ?? '1970-01-01 00:00:00';

        try {
            $sql = "SELECT * FROM messages WHERE user_id = ? AND created_at > ? ORDER BY created_at ASC";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "is", $user_id, $last_time);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $messages = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $messages[] = [
                    'content' => $row['message'],
                    'sender_type' => $row['sender_type'],
                    'timestamp' => $row['created_at']
                ];
            }

            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching messages: ' . $e->getMessage()
            ]);
        }
        break;

    case 'load_history':
        if (!isset($_POST['user_id'])) {
            debug_log("Missing user_id parameter");
            echo json_encode(['success' => false, 'message' => 'Missing user_id parameter']);
            exit;
        }

        try {
            $user_id = sanitize_input($_POST['user_id']);

            // Fetch all messages for this user
            $stmt = $conn->prepare("SELECT message, sender_type, created_at 
                                  FROM chat_messages 
                                  WHERE user_id = ?
                                  ORDER BY created_at ASC");
            $stmt->execute([$user_id]);
            
            $messages = [];
            while ($row = $stmt->fetch()) {
                $messages[] = [
                    'content' => $row['message'],
                    'sender_type' => $row['sender_type'],
                    'timestamp' => $row['created_at']
                ];
            }

            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (PDOException $e) {
            debug_log("Database error loading history", $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error loading message history'
            ]);
        }
        break;

    case 'check_unread':
        try {
            $user_id = $_SESSION['user_id'];
            $query = "SELECT COUNT(*) as count FROM messages 
                      WHERE user_id = ? 
                      AND sender_type IN ('admin', 'system') 
                      AND read_status = 0";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $count = mysqli_fetch_assoc($result)['count'];
            
            echo json_encode([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error checking unread messages'
            ]);
        }
        break;

    case 'mark_read':
        $user_id = $_POST['user_id'];
        // Query to mark messages as read
        $query = "UPDATE chat_messages 
                  SET is_read = 1 
                  WHERE user_id = ? 
                  AND sender_type = 'admin'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        
        echo json_encode([
            'success' => true
        ]);
        break;

    case 'get_messages':
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }

            $user_id = $_SESSION['user_id'];

            // Get recent messages including both user and system/admin messages
            $query = "SELECT m.*, u.first_name, u.last_name 
                     FROM messages m
                     LEFT JOIN users u ON m.user_id = u.id
                     WHERE m.user_id = ?
                     ORDER BY m.created_at DESC 
                     LIMIT 20";
                     
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $messages = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $messages[] = [
                    'id' => $row['id'],
                    'message' => $row['message'],
                    'sender_type' => $row['sender_type'],
                    'sender_name' => $row['sender_type'] === 'user' ? 
                        $row['first_name'] . ' ' . $row['last_name'] : 
                        ($row['sender_type'] === 'admin' ? 'Admin' : 'System'),
                    'created_at' => $row['created_at'],
                    'read_status' => $row['read_status']
                ];
            }

            // Mark messages as read
            $updateQuery = "UPDATE messages 
                           SET read_status = 1 
                           WHERE user_id = ? 
                           AND sender_type IN ('admin', 'system') 
                           AND read_status = 0";
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);

            echo json_encode([
                'success' => true,
                'messages' => array_reverse($messages) // Reverse to show oldest first
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case 'mark_all_read':
        try {
            $query = "UPDATE chat_messages 
                     SET is_read = 1 
                     WHERE sender_type = 'admin'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            echo json_encode([
                'success' => true
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error marking messages as read'
            ]);
        }
        break;

    case 'get_message':
        try {
            if (!isset($_POST['message_id'])) {
                throw new Exception('Message ID not provided');
            }
            
            $message_id = sanitize_input($_POST['message_id']);
            
            $query = "SELECT id, message as message, sender_type, created_at 
                     FROM chat_messages 
                     WHERE id = :message_id";
            $stmt = $conn->prepare($query);
            $stmt->execute([':message_id' => $message_id]);
            
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($message) {
                // Mark the message as read
                $update = "UPDATE chat_messages 
                          SET is_read = 1 
                          WHERE id = :message_id";
                $stmt = $conn->prepare($update);
                $stmt->execute([':message_id' => $message_id]);
                
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                throw new Exception('Message not found');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// In the 'send' case for admin replies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $reply = $_POST['reply'] ?? '';
    $replyToId = $_POST['reply_to_id'] ?? null;
    
    if ($userId && $reply) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, sender_type, reply_to_id) VALUES (?, ?, 'admin', ?)");
        $stmt->bind_param("ssi", $userId, $reply, $replyToId);
        $stmt->execute();
        $stmt->close();
        
        // Mark previous messages as read
        $stmt = $conn->prepare("UPDATE chat_messages SET is_read = 1 WHERE user_id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $stmt->close();
    }
}
?> 