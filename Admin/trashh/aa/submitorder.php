
<?php
require_once 'db_con.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (empty($data['payment_method']) || empty($data['order_details'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    $userId = $_SESSION['userid']; 
    $paymentMethod = htmlspecialchars($data['payment_method']);
    $orderDetails = $data['order_details'];

    try {
    
        $pdo->beginTransaction();

        $totalPrice = 0;
        foreach ($orderDetails as $item) {
            $itemTotal = $item['price'] * $item['qty'];
            $addonsTotal = array_sum(array_column($item['addons'], 'price'));
            $totalPrice += ($itemTotal + $addonsTotal);
        }

        $orderId = time(); 
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_id, userid, payment_method, total_price, created_at)
            VALUES (:order_id, :userid, :payment_method, :total_price, NOW())
        ");
        $stmt->execute([
            ':order_id' => $orderId,
            ':userid' => $userId, 
            ':payment_method' => $paymentMethod,
            ':total_price' => $totalPrice
        ]);

        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, item_name, category, price, qty, addons)
            VALUES (:order_id, :item_name, :category, :price, :qty, :addons)
        ");
        foreach ($orderDetails as $item) {
            $addonsJson = !empty($item['addons']) ? json_encode($item['addons']) : null;
            $stmt->execute([
                ':order_id' => $orderId,
                ':item_name' => htmlspecialchars($item['name']),
                ':category' => htmlspecialchars($item['category']),
                ':price' => $item['price'],
                ':qty' => $item['qty'],
                ':addons' => $addonsJson
            ]);
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Order saved successfully!',
            'redirect_url' => 'cafes.php'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error saving order: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>