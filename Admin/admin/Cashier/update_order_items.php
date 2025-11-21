<?php
require "db.php";

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

try {
    // Check if it's a delete action
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['delete_item_id']) && isset($_POST['order_id'])) {
        $itemId = filter_var($_POST['delete_item_id'], FILTER_VALIDATE_INT);
        $orderId = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);

        // Validate inputs
        if ($itemId === false || $orderId === false) {
            throw new Exception('Invalid item ID or order ID format');
        }

        if ($itemId <= 0 || $orderId <= 0) {
            throw new Exception('Item ID and order ID must be positive numbers');
        }

        // Start transaction
        $connection->begin_transaction();

        try {
            // First, check if the item exists and belongs to the order
            $checkQuery = "SELECT COUNT(*) FROM order_items WHERE id = ? AND order_id = ?";
            $stmt = $connection->prepare($checkQuery);
            $stmt->bind_param("ii", $itemId, $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_row()[0];

            if ($count === 0) {
                throw new Exception('Item not found or does not belong to this order');
            }

            // Delete addons
            $deleteAddonsQuery = "DELETE FROM order_item_addons WHERE order_item_id = ?";
            $stmt = $connection->prepare($deleteAddonsQuery);
            $stmt->bind_param("i", $itemId);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete addons: ' . $stmt->error);
            }

            // Delete the order item
            $deleteItemQuery = "DELETE FROM order_items WHERE id = ? AND order_id = ?";
            $stmt = $connection->prepare($deleteItemQuery);
            $stmt->bind_param("ii", $itemId, $orderId);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete item: ' . $stmt->error);
            }

            // Update the total amount
            $updateTotalQuery = "UPDATE orders o 
                               SET total_amount = (
                                   SELECT COALESCE(SUM(oi.quantity * oi.unit_price), 0) + 
                                          COALESCE((
                                              SELECT SUM(oia.addon_price)
                                              FROM order_item_addons oia
                                              JOIN order_items oi2 ON oia.order_item_id = oi2.id
                                              WHERE oi2.order_id = o.id
                                          ), 0)
                                   FROM order_items oi
                                   WHERE oi.order_id = o.id
                               )
                               WHERE id = ?";
            
            $stmt = $connection->prepare($updateTotalQuery);
            $stmt->bind_param("i", $orderId);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update total: ' . $stmt->error);
            }

            // Commit the transaction
            $connection->commit();

            $response['success'] = true;
            $response['message'] = 'Item removed successfully';

        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }
    } 
    // Handle bulk updates for quantities
    else if (isset($_POST['items']) && isset($_POST['order_id'])) {
        $items = json_decode($_POST['items'], true);
        $orderId = intval($_POST['order_id']);

        // Start transaction
        $connection->begin_transaction();

        try {
            // Update quantities for each item
            $updateQuery = "UPDATE order_items SET quantity = ? WHERE id = ? AND order_id = ?";
            $stmt = $connection->prepare($updateQuery);

            foreach ($items as $item) {
                $quantity = intval($item['quantity']);
                $itemId = intval($item['item_id']);
                $stmt->bind_param("iii", $quantity, $itemId, $orderId);
                $stmt->execute();
            }

            // Update the total amount
            $updateTotalQuery = "UPDATE orders o 
                               SET total_amount = (
                                   SELECT COALESCE(SUM(oi.quantity * oi.unit_price), 0) + 
                                          COALESCE((
                                              SELECT SUM(oia.addon_price)
                                              FROM order_item_addons oia
                                              JOIN order_items oi2 ON oia.order_item_id = oi2.id
                                              WHERE oi2.order_id = o.id
                                          ), 0)
                                   FROM order_items oi
                                   WHERE oi.order_id = o.id
                               )
                               WHERE id = ?";
            
            $stmt = $connection->prepare($updateTotalQuery);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();

            // Commit the transaction
            $connection->commit();

            $response['success'] = true;
            $response['message'] = 'Order updated successfully';
        } catch (Exception $e) {
            // Rollback on error
            $connection->rollback();
            throw $e;
        }
    } else {
        throw new Exception('Invalid request parameters');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
    // Log the error
    error_log('Error in update_order_items.php: ' . $e->getMessage());
}

// Add debug information in development
$response['debug'] = [
    'post_data' => $_POST,
    'item_id' => $itemId ?? null,
    'order_id' => $orderId ?? null
];

// Send response
echo json_encode($response);
?> 