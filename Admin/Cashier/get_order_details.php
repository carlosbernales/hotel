<?php
require_once 'db.php';

// Check if user is logged in and has cashier role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    // First, get the actual database ID from order_id
    $stmt = $pdo->prepare("SELECT id FROM orders_table WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $orderRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderRecord) {
        echo json_encode(['success' => false, 'message' => 'Order not found with ID: ' . $orderId]);
        exit();
    }
    
    $actualOrderId = $orderRecord['id'];
    
    // Fetch order details
    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            CONCAT(u.first_name, ' ', u.last_name) as cashier_name
        FROM orders_table o
        LEFT JOIN userss u ON o.user_id = u.id
        WHERE o.id = :id
    ");
    $stmt->execute([':id' => $actualOrderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order details not found for ID: ' . $actualOrderId]);
        exit();
    }

    // Fetch detailed table information
    $stmt = $pdo->prepare("
        SELECT 
            ott.id,
            ott.table_name,
            ott.table_number,
            ott.table_number_fk_id,
            tn.id as table_number_id,
            tn.status as table_status,
            tt.capacity
        FROM orders_table_type ott
        JOIN table_number tn ON ott.table_number_fk_id = tn.id
        JOIN table_types tt ON tn.table_type_fk_id = tt.id AND ott.table_name COLLATE utf8mb4_unicode_ci = tt.table_name COLLATE utf8mb4_unicode_ci
        WHERE ott.table_booking_fk_id = :order_id
        ORDER BY ott.table_name, ott.table_number
    ");
    $stmt->execute([':order_id' => $actualOrderId]);
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order items
    $stmt = $pdo->prepare("
        SELECT 
            oi.id,
            oi.item_name,
            oi.quantity,
            oi.unit_price
        FROM order_items oi
        WHERE oi.order_fk_id = :order_id
    ");
    $stmt->execute([':order_id' => $actualOrderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order item addons
    $stmt = $pdo->prepare("
        SELECT 
            oia.order_item_fk_id,
            oia.addon_name,
            oia.price
        FROM order_item_addons oia
        JOIN order_items oi ON oia.order_item_fk_id = oi.id
        WHERE oi.order_fk_id = :order_id
    ");
    $stmt->execute([':order_id' => $actualOrderId]);
    $addons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group addons by order item
    $groupedAddons = [];
    foreach ($addons as $addon) {
        $itemId = $addon['order_item_fk_id'];
        if (!isset($groupedAddons[$itemId])) {
            $groupedAddons[$itemId] = [];
        }
        $groupedAddons[$itemId][] = $addon;
    }

    // Add addons to items
    foreach ($items as &$item) {
        $item['addons'] = $groupedAddons[$item['id'] ?? 0] ?? [];
    }

    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'order_id' => $order['order_id'],
            'date_time' => $order['date_time'],
            'cashier_name' => $order['cashier_name'],
            'tables' => $tables,
            'type_of_order' => $order['order_type'],
            'payment_method' => $order['payment_method'],
            'total' => (float)$order['total'],
            'discount_amount' => (float)$order['discount_amount'],
            'discount_type' => $order['discount_type'],
            'discount_percentage' => $order['discount_percentage'],
            'amount_paid' => (float)$order['amount_paid'],
            'change_amount' => (float)$order['change_amount'],
            'status' => $order['status'],
            'contact' => $order['contact'],
            'email' => $order['email'],
            'balance' => $order['balance'],
            'downpayment' => $order['downpayment'],
            'payment_option' => $order['payment_option'],
            'items' => $items
        ]
    ]);

} catch (PDOException $e) {
    error_log("Error fetching order details: " . $e->getMessage());
    error_log("Order ID requested: " . $orderId);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>