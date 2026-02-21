<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['order_id'])) {
        throw new Exception('Order ID is required');
    }
    
    $orderId = $data['order_id'];
    
    // Fetch order items from order_items table with LEFT JOIN to orders_table
    $stmt = $pdo->prepare("
        SELECT 
            oi.item_name,
            oi.quantity,
            oi.unit_price,
            ot.order_id
        FROM order_items oi
        LEFT JOIN orders_table ot ON oi.order_fk_id = ot.order_id
        WHERE oi.order_fk_id = :order_fk_id
        ORDER BY oi.id ASC
    ");
    
    $stmt->execute([':order_fk_id' => $orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
