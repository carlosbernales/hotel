<?php
require_once 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'debug' => []
];

try {
    if (!isset($_POST['order_id']) || !isset($_POST['items'])) {
        throw new Exception('Missing required parameters');
    }

    $orderId = intval($_POST['order_id']);
    $items = json_decode($_POST['items'], true);
    $finalAmount = floatval($_POST['final_amount'] ?? 0);

    if (!is_array($items)) {
        throw new Exception('Invalid items data');
    }

    // Add debug info
    $response['debug']['received_data'] = [
        'order_id' => $orderId,
        'items' => $items,
        'final_amount' => $finalAmount
    ];

        // Start transaction
        $connection->begin_transaction();

    // First, get the existing items for this order
    $stmt = $connection->prepare("SELECT id, item_name FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
    $existingItems = [];
    while ($row = $result->fetch_assoc()) {
        $existingItems[$row['item_name']] = $row['id'];
    }

    // Update or insert items
    foreach ($items as $item) {
        if (isset($existingItems[$item['name']])) {
            // Update existing item
            $stmt = $connection->prepare("
                UPDATE order_items 
                SET quantity = ?, 
                    unit_price = ?
                WHERE id = ?");
            $stmt->bind_param("ddi", 
                $item['quantity'],
                $item['unit_price'],
                $existingItems[$item['name']]
            );
            if (!$stmt->execute()) {
                throw new Exception("Failed to update item: " . $stmt->error);
            }
        } else {
            // Insert new item
            $stmt = $connection->prepare("
                INSERT INTO order_items 
                (order_id, item_name, quantity, unit_price) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isdd",
                $orderId,
                $item['name'],
                $item['quantity'],
                $item['unit_price']
            );
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert item: " . $stmt->error);
            }
        }
    }

    // Remove items that are no longer in the order
    $currentItems = array_column($items, 'name');
    $itemsToRemove = array_diff(array_keys($existingItems), $currentItems);
    
    if (!empty($itemsToRemove)) {
        foreach ($itemsToRemove as $itemName) {
            $itemId = $existingItems[$itemName];
            
            // Delete associated add-ons first
            $stmt = $connection->prepare("DELETE FROM order_item_addons WHERE order_item_id = ?");
            $stmt->bind_param("i", $itemId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete addons: " . $stmt->error);
            }
            
            // Then delete the item
            $stmt = $connection->prepare("DELETE FROM order_items WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete item: " . $stmt->error);
            }
        }
    }

    // Update the order's amounts
    if ($finalAmount > 0) {
        $stmt = $connection->prepare("
            UPDATE orders 
            SET total_amount = ?,
                discount_amount = ?,
                final_total = ?
            WHERE id = ?");
        $stmt->bind_param("dddi", 
            $_POST['subtotal'],
            $_POST['discount_amount'],
            $_POST['final_amount'],
            $orderId
        );
        if (!$stmt->execute()) {
            throw new Exception("Failed to update order total: " . $stmt->error);
        }
    }

    // Commit transaction
            $connection->commit();

            $response['success'] = true;
            $response['message'] = 'Order updated successfully';

        } catch (Exception $e) {
    // Rollback transaction on error
    if (isset($connection) && !$connection->connect_errno) {
            $connection->rollback();
    }
    
    error_log('Error in update_order_items.php: ' . $e->getMessage());
    $response['success'] = false;
    $response['message'] = 'Error updating order: ' . $e->getMessage();
    $response['debug']['error'] = $e->getMessage();
}

// Add debug information
$response['debug']['post_data'] = $_POST;

// Close connection if it exists
if (isset($connection)) {
    $connection->close();
}

// Send response
echo json_encode($response);
exit; 