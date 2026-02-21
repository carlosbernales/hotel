

<?php
include ('nav.php'); 
require_once 'db_con.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit;
}

$userId = $_SESSION['userid'];

try {
    // Fetch orders for the logged-in user
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.payment_method, o.total_price, o.created_at, 
               oi.item_name, oi.category, oi.price, oi.qty, oi.addons
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.userid = :userid
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([':userid' => $userId]);

    $orders = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orderId = $row['order_id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'order_id' => $orderId,
                'payment_method' => $row['payment_method'],
                'total_price' => $row['total_price'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }
        $orders[$orderId]['items'][] = [
            'item_name' => $row['item_name'],
            'category' => $row['category'],
            'price' => $row['price'],
            'qty' => $row['qty'],
            'addons' => json_decode($row['addons'], true)
        ];
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching orders: ' . $e->getMessage()]);
}
?>
