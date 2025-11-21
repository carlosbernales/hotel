<?php
require "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the order ID and items
$orderId = $_POST['order_id'] ?? null;
$items = $_POST['items'] ?? [];
$addons = $_POST['addons'] ?? [];

if (!$orderId || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    // Start transaction
    $connection->begin_transaction();

    // Verify order exists and is in processing status
    $stmt = $connection->prepare("SELECT * FROM orders WHERE id = ? AND status = 'processing'");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Order not found or not in processing status');
    }

    $order = $result->fetch_assoc();
    $currentTotal = $order['total_amount'];

    // Insert new order items
    $stmt = $connection->prepare("INSERT INTO order_items (order_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?)");
    
    $additionalTotal = 0;
    
    foreach ($items as $item) {
        $stmt->bind_param("isid", $orderId, $item['name'], $item['quantity'], $item['price']);
        $stmt->execute();
        $orderItemId = $connection->insert_id;
        
        $itemTotal = $item['quantity'] * $item['price'];
        $additionalTotal += $itemTotal;

        // Insert addons if any
        if (isset($addons[$item['name']])) {
            $addonStmt = $connection->prepare("INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) VALUES (?, ?, ?)");
            
            foreach ($addons[$item['name']] as $addon) {
                $addonStmt->bind_param("isd", $orderItemId, $addon['name'], $addon['price']);
                $addonStmt->execute();
                $additionalTotal += $addon['price'];
            }
        }
    }

    // Update order total
    $newTotal = $currentTotal + $additionalTotal;
    $stmt = $connection->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
    $stmt->bind_param("di", $newTotal, $orderId);
    $stmt->execute();

    // Commit transaction
    $connection->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Additional orders added successfully',
        'new_total' => $newTotal
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $connection->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error processing additional orders: ' . $e->getMessage()
    ]);
}

$connection->close(); 