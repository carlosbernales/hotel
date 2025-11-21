<?php
session_start();
require_once 'db.php';

// Check if required parameters are set
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Check if user is logged in and is a cashier
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please log in as cashier.'
    ]);
    exit;
}

// Get and sanitize parameters
$orderId = intval($_POST['order_id']);
$status = mysqli_real_escape_string($connection, $_POST['status']);
$rejectReason = $_POST['reject_reason'] ?? '';
$cashierId = $_SESSION['user_id'];

// Validate status
$allowedStatuses = ['processing', 'finished', 'rejected'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status'
    ]);
    exit;
}

// Get cashier's name
$cashierName = '';
$nameQuery = "SELECT first_name, last_name FROM userss WHERE id = ?";
$nameStmt = $connection->prepare($nameQuery);
$nameStmt->bind_param('i', $cashierId);
$nameStmt->execute();
$nameResult = $nameStmt->get_result();

if ($nameRow = $nameResult->fetch_assoc()) {
    $cashierName = trim($nameRow['first_name'] . ' ' . $nameRow['last_name']);
}
$nameStmt->close();

// Update the order status and processed_by field if status is processing (order accepted)
if ($status === 'processing') {
    $sql = "UPDATE orders SET status = ?, reject_reason = ?, processed_by = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('sssi', $status, $rejectReason, $cashierName, $orderId);
} else {
    $sql = "UPDATE orders SET status = ?, reject_reason = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('ssi', $status, $rejectReason, $orderId);
}

if ($stmt->execute()) {
    // If order is rejected, create notification
    if ($status === 'rejected' && !empty($rejectReason)) {
        // Get user_id from orders table
        $userQuery = "SELECT user_id FROM orders WHERE id = ?";
        $userStmt = $connection->prepare($userQuery);
        $userStmt->bind_param("i", $orderId);
        $userStmt->execute();
        $result = $userStmt->get_result();
        $order = $result->fetch_assoc();
        
        if ($order) {
            // Create notification
            $notifSql = "INSERT INTO notifications (user_id, message, type, created_at) VALUES (?, ?, 'order_rejected', NOW())";
            $message = "Your order #" . $orderId . " has been rejected. Reason: " . $rejectReason;
            $notifStmt = $connection->prepare($notifSql);
            $notifStmt->bind_param("is", $order['user_id'], $message);
            $notifStmt->execute();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating order status: ' . $connection->error
    ]);
}

$stmt->close();
$connection->close();
?> 