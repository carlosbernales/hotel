<?php
require_once 'db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    // Validate input
    if (!isset($_POST['order_id']) || !isset($_POST['items'])) {
        throw new Exception('Missing required data');
    }

    $orderId = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    if (!$orderId) {
        throw new Exception('Invalid order ID');
    }

    $items = json_decode($_POST['items'], true);
    if (!is_array($items)) {
        throw new Exception('Invalid items data');
    }

    // Start transaction
    $connection->begin_transaction();

    try {
        // First, delete existing order items
        $deleteItems = $connection->prepare("DELETE FROM order_items WHERE order_id = ?");
        $deleteItems->bind_param('i', $orderId);
        if (!$deleteItems->execute()) {
            throw new Exception('Failed to remove old items');
        }

        // Insert new items
        $insertItem = $connection->prepare(
            "INSERT INTO order_items (order_id, item_name, quantity, unit_price) 
             VALUES (?, ?, ?, ?)"
        );

        $totalAmount = 0;
        foreach ($items as $item) {
            $itemName = $item['name'];
            $quantity = $item['quantity'];
            $unitPrice = $item['price'] / $quantity; // Calculate unit price
            
            $insertItem->bind_param('isid', $orderId, $itemName, $quantity, $unitPrice);
            if (!$insertItem->execute()) {
                throw new Exception('Failed to insert item: ' . $itemName);
            }

            $itemId = $insertItem->insert_id;
            $totalAmount += $item['price'];

            // Handle addons if present
            if (!empty($item['addons']) && $item['addons'] !== ['None']) {
                $insertAddon = $connection->prepare(
                    "INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) 
                     VALUES (?, ?, ?)"
                );

                foreach ($item['addons'] as $addon) {
                    // Extract addon name and price from the string "Name (₱Price)"
                    if (preg_match('/(.+) \(₱(\d+\.?\d*)\)/', $addon, $matches)) {
                        $addonName = trim($matches[1]);
                        $addonPrice = floatval($matches[2]);
                        
                        $insertAddon->bind_param('isd', $itemId, $addonName, $addonPrice);
                        if (!$insertAddon->execute()) {
                            throw new Exception('Failed to insert addon: ' . $addonName);
                        }
                    }
                }
                $insertAddon->close();
            }
        }

        // Update order total
        $updateOrder = $connection->prepare(
            "UPDATE orders SET total_amount = ? WHERE id = ?"
        );
        $updateOrder->bind_param('di', $totalAmount, $orderId);
        if (!$updateOrder->execute()) {
            throw new Exception('Failed to update order total');
        }

        // Commit transaction
        $connection->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Order updated successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $connection->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Update Order Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update order: ' . $e->getMessage()
    ]);
}

// Clean up
if (isset($connection)) {
    $connection->close();
} 