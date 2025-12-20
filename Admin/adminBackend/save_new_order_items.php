<?php
include '../adminBackend/mydb.php';

header('Content-Type: application/json');

if (!isset($_POST['order_id']) || !isset($_POST['cartItems'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$orderId = intval($_POST['order_id']);
$cartItems = $_POST['cartItems'];

$conn->begin_transaction();

try {
    $totalNewItems = 0;

    foreach ($cartItems as $itemJson) {
        $item = json_decode($itemJson, true);
        if (!$item)
            continue;

        $itemName = $item['item_name'];
        $unitPrice = floatval($item['unit_price']);
        $quantity = intval($item['quantity']);

        $stmt = $conn->prepare("INSERT INTO order_items (order_fk_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $orderId, $itemName, $quantity, $unitPrice);
        $stmt->execute();
        $orderItemId = $stmt->insert_id;
        $stmt->close();

        $itemTotal = $unitPrice * $quantity;
        $totalNewItems += $itemTotal;

        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $addonName = $addon['addon_name'];
                $addonPrice = floatval($addon['price']);
                $addonQty = intval($addon['quantity']);

                $stmt = $conn->prepare("INSERT INTO order_item_addons (order_item_fk_id, addon_name, price, quantity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isdi", $orderItemId, $addonName, $addonPrice, $addonQty);
                $stmt->execute();
                $stmt->close();

                $totalNewItems += $addonPrice * $addonQty;
            }
        }
    }

    $stmt = $conn->prepare("SELECT total, downpayment FROM orders_table WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->bind_result($currentTotal, $downpayment);
    $stmt->fetch();
    $stmt->close();

    $newTotal = $currentTotal + $totalNewItems;
    $newRemaining = $newTotal - $downpayment;

    $stmt = $conn->prepare("UPDATE orders_table SET total = ?, remaining_balance = ? WHERE id = ?");
    $stmt->bind_param("ddi", $newTotal, $newRemaining, $orderId);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Order updated successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>