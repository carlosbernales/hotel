<?php
require_once 'db.php';

// Set header for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($orderId) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
    
    // Update the order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('si', $status, $orderId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$connection->close();
?> 