<?php
session_start();
require 'db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderData = json_decode($_POST['order-details'], true);
    
    if (empty($orderData)) {
        echo json_encode(['status' => 'error', 'message' => 'No order data received']);
        exit;
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (total_amount, order_date) VALUES (?, NOW())");
        
        // Calculate total amount
        $totalAmount = array_reduce($orderData, function($total, $item) {
            $itemTotal = $item['price'] * $item['qty'];
            $addonsTotal = array_reduce($item['addons'], function($sum, $addon) {
                return $sum + $addon['price'];
            }, 0) * $item['qty'];
            return $total + $itemTotal + $addonsTotal;
        }, 0);

        $stmt->bind_param("d", $totalAmount);
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity, price, category) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($orderData as $item) {
            $stmt->bind_param("isids", 
                $orderId,
                $item['name'],
                $item['qty'],
                $item['price'],
                $item['category']
            );
            $stmt->execute();
            
            // Get the order item ID
            $orderItemId = $conn->insert_id;
            
            // Insert addons if any
            if (!empty($item['addons'])) {
                $addonStmt = $conn->prepare("INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) VALUES (?, ?, ?)");
                
                foreach ($item['addons'] as $addon) {
                    $addonStmt->bind_param("isd",
                        $orderItemId,
                        $addon['name'],
                        $addon['price']
                    );
                    $addonStmt->execute();
                }
            }
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'order_id' => $orderId]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    exit;
} 