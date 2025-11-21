<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if ($orderId <= 0 || empty($items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order ID or no items provided'
    ]);
    exit;
}

try {
    $connection->begin_transaction();

    // Calculate total amount for additional items
    $additionalTotal = 0;
    foreach ($items as $item) {
        // Extract base price from item name (format: "Item Name - ₱price")
        preg_match('/- ₱(\d+(\.\d{2})?)/', $item['item_name'], $matches);
        $basePrice = isset($matches[1]) ? floatval($matches[1]) : 0;
        
        // Calculate item total with quantity
        $itemTotal = $basePrice * intval($item['quantity']);
        
        // Add addons cost if any
        if (!empty($item['addons']) && $item['addons'] !== 'None') {
            $addons = explode(', ', $item['addons']);
            foreach ($addons as $addon) {
                preg_match('/- ₱(\d+(\.\d{2})?)/', $addon, $matches);
                if (isset($matches[1])) {
                    $itemTotal += floatval($matches[1]) * intval($item['quantity']);
                }
            }
        }
        
        $additionalTotal += $itemTotal;

        // Insert order item
        $sql = "INSERT INTO order_items (order_id, item_name, quantity, unit_price) 
                VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('isid', 
            $orderId,
            $item['item_name'],
            $item['quantity'],
            $basePrice
        );
        $stmt->execute();
        
        $orderItemId = $stmt->insert_id;
        
        // Insert add-ons if any
        if (!empty($item['addons']) && $item['addons'] !== 'None') {
            $addons = explode(', ', $item['addons']);
            foreach ($addons as $addon) {
                preg_match('/(.+) - ₱(\d+(\.\d{2})?)/', $addon, $matches);
                if (count($matches) >= 3) {
                    $addonName = trim($matches[1]);
                    $addonPrice = floatval($matches[2]);
                    
                    $sql = "INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) 
                            VALUES (?, ?, ?)";
                    $stmt = $connection->prepare($sql);
                    $stmt->bind_param('isd', 
                        $orderItemId,
                        $addonName,
                        $addonPrice
                    );
                    $stmt->execute();
                }
            }
        }
    }

    // Update order total amount by adding the additional total
    $sql = "UPDATE orders SET total_amount = total_amount + ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('di', $additionalTotal, $orderId);
    $stmt->execute();

    $connection->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Additional orders saved successfully',
        'additional_total' => $additionalTotal
    ]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error saving additional orders: ' . $e->getMessage()
    ]);
}

$connection->close(); 