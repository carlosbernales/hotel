<?php
// Prevent any output before JSON response
ob_start();

// Error reporting should be turned off for JSON responses
error_reporting(0);
ini_set('display_errors', 0);

require_once 'db.php';
session_start();

// Clear any previous output
ob_clean();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient_type = $_POST['recipient_type'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $sender_id = $_SESSION['user_id'] ?? 1;

    try {
        // Start transaction
        mysqli_begin_transaction($con);

        // Determine receiver_id based on recipient_type
        switch ($recipient_type) {
            case 'customer':
                if (!isset($_POST['customer_id'])) {
                    throw new Exception('Customer ID is required');
                }
                
                if ($_POST['customer_id'] === 'all') {
                    // For all customers, set receiver_id to -1 (special case)
                    $receiver_id = -1;
                } else {
                    // Send to specific customer
                    $receiver_id = $_POST['customer_id'];
                }
                break;

            case 'frontdesk':
                // Get frontdesk staff ID
                $query = "SELECT id FROM users WHERE name LIKE '%Front Desk%' LIMIT 1";
                $result = $con->query($query);
                if (!$result || $result->num_rows === 0) {
                    throw new Exception('No front desk user found');
                }
                $receiver_id = $result->fetch_assoc()['id'];
                break;

            case 'cashier':
                // Get cashier ID
                $query = "SELECT id FROM users WHERE name LIKE '%Cashier%' LIMIT 1";
                $result = $con->query($query);
                if (!$result || $result->num_rows === 0) {
                    throw new Exception('No cashier user found');
                }
                $receiver_id = $result->fetch_assoc()['id'];
                break;

            case 'general':
                $receiver_id = 0; // Special ID for general messages
                break;

            default:
                throw new Exception('Invalid recipient type');
        }

        // Insert the message
        $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message, is_read, created_at) 
                VALUES (?, ?, ?, ?, 0, CURRENT_TIMESTAMP)";
        $stmt = $con->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $con->error);
        }
        
        $stmt->bind_param("iiss", $sender_id, $receiver_id, $subject, $message);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to send message: ' . $stmt->error);
        }

        mysqli_commit($con);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Ensure no other output
exit();
?> 