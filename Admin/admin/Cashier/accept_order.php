<?php
require "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    // Update order status to accepted
    $sql = "UPDATE orders SET status = 'accepted' WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $orderId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error accepting order']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Order ID is required']);
} 