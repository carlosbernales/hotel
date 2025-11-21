<?php
require "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['editOrderId'];
    $paymentMethod = $_POST['editPaymentMethod'];
    $quantities = $_POST['quantity'];
    
    // Start transaction
    $connection->begin_transaction();
    
    try {
        // Update order payment method
        $sql = "UPDATE orders SET payment_method = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $paymentMethod, $orderId);
        $stmt->execute();
        
        // Update quantities
        foreach ($quantities as $itemName => $quantity) {
            $sql = "UPDATE order_items SET quantity = ? WHERE order_id = ? AND item_name = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("iis", $quantity, $orderId, $itemName);
            $stmt->execute();
        }
        
        // Recalculate total amount
        // Add your logic here to recalculate the total based on new quantities
        
        $connection->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $connection->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
} 