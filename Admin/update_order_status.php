<?php
// Include database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['order_id']) || empty($_POST['order_id']) || !isset($_POST['status']) || empty($_POST['status'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit;
}

$orderId = intval($_POST['order_id']);
$status = $_POST['status'];

// Validate status
$validStatuses = ['pending', 'finished', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

try {
    // Update order status
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param('si', $status, $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    if ($stmt->affected_rows === 0) {
        // Order not found or status already set
        $verifyQuery = "SELECT id, status FROM orders WHERE id = ?";
        $verifyStmt = $con->prepare($verifyQuery);
        $verifyStmt->bind_param('i', $orderId);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyResult->num_rows === 0) {
            throw new Exception("Order not found");
        } else {
            $orderData = $verifyResult->fetch_assoc();
            if ($orderData['status'] === $status) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Order status was already set to ' . $status]);
                exit;
            } else {
                throw new Exception("Failed to update order status");
            }
        }
    }
    
    // Log the status change
    $userId = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');
    $logQuery = "INSERT INTO order_status_logs (order_id, status, changed_by, changed_at) VALUES (?, ?, ?, ?)";
    
    // Check if the table exists first
    $checkTable = $con->query("SHOW TABLES LIKE 'order_status_logs'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $logStmt = $con->prepare($logQuery);
        if ($logStmt) {
            $logStmt->bind_param('isis', $orderId, $status, $userId, $now);
            $logStmt->execute();
        }
    }
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully',
        'order_id' => $orderId,
        'status' => $status
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log("Error updating order status: " . $e->getMessage());
    
    // Return error message
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error updating order status: ' . $e->getMessage()
    ]);
} 