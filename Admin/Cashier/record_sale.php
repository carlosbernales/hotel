<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);
    
    // Get order details
    $orderQuery = "SELECT total_amount, payment_method FROM orders WHERE id = ?";
    $stmt = $connection->prepare($orderQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if ($order) {
        // Insert into sales table
        $insertQuery = "INSERT INTO sales (order_id, total_amount, payment_method) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($insertQuery);
        $stmt->bind_param("ids", $orderId, $order['total_amount'], $order['payment_method']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error recording sale: ' . $connection->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
} 