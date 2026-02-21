<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$orderId = $_GET['order_id'] ?? $_POST['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    // Get order details including balance
    $stmt = $pdo->prepare("
        SELECT 
            id,
            order_id,
            total,
            amount_paid,
            balance,
            downpayment,
            status
        FROM orders_table 
        WHERE order_id = :order_id
    ");
    
    $stmt->execute([':order_id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    // Calculate balance if not stored or if it needs recalculation
    $total = floatval($order['total']);
    $amountPaid = floatval($order['amount_paid']);
    $downpayment = floatval($order['downpayment']);
    $balance = floatval($order['balance']);
    
    // If balance is null or needs recalculation, calculate it
    if ($balance === null || $balance === '') {
        $balance = $total - ($amountPaid + $downpayment);
    }
    
    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'order_id' => $order['order_id'],
            'total' => $total,
            'amount_paid' => $amountPaid,
            'downpayment' => $downpayment,
            'balance' => $balance,
            'status' => $order['status']
        ],
        'has_balance' => $balance > 0
    ]);
    
} catch (PDOException $e) {
    error_log("Error checking order balance: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
