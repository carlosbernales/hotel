<?php
require "db.php";

if (!isset($_GET['id'])) {
    header('Location: Order.php');
    exit;
}

$orderId = $_GET['id'];

// Get order details
$sql = "SELECT o.*, oi.item_name, oi.quantity, oi.unit_price, oia.addon_name, oia.addon_price 
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
        WHERE o.id = ?";
        
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

$order = null;
while ($row = $result->fetch_assoc()) {
    if (!$order) {
        $order = [
            'id' => $row['id'],
            'payment_method' => $row['payment_method'],
            'total_amount' => $row['total_amount'],
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }
    
    $itemKey = $row['item_name'] . '_' . $row['quantity'];
    if (!isset($order['items'][$itemKey])) {
        $order['items'][$itemKey] = [
            'name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'unit_price' => $row['unit_price'],
            'addons' => []
        ];
    }
    
    if ($row['addon_name']) {
        $order['items'][$itemKey]['addons'][] = [
            'name' => $row['addon_name'],
            'price' => $row['addon_price']
        ];
    }
}

if (!$order) {
    header('Location: Order.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Edit Order #<?php echo htmlspecialchars($order['id']); ?></h2>
        <form action="update_order.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            
            <div class="form-group">
                <label>Payment Method:</label>
                <select name="payment_method" class="form-control">
                    <option value="cash" <?php echo $order['payment_method'] === 'cash' ? 'selected' : ''; ?>>Cash</option>
                    <option value="card" <?php echo $order['payment_method'] === 'card' ? 'selected' : ''; ?>>Card</option>
                    <option value="gcash" <?php echo $order['payment_method'] === 'gcash' ? 'selected' : ''; ?>>GCash</option>
                </select>
            </div>
            
            <h3>Order Items</h3>
            <?php foreach ($order['items'] as $index => $item): ?>
                <div class="form-group">
                    <label>Item <?php echo $index + 1; ?>:</label>
                    <input type="text" class="form-control" name="items[<?php echo $index; ?>][name]" 
                           value="<?php echo htmlspecialchars($item['name']); ?>" readonly>
                    
                    <label>Quantity:</label>
                    <input type="number" class="form-control" name="items[<?php echo $index; ?>][quantity]" 
                           value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                    
                    <?php if (!empty($item['addons'])): ?>
                        <h4>Add-ons:</h4>
                        <ul>
                            <?php foreach ($item['addons'] as $addon): ?>
                                <li><?php echo htmlspecialchars($addon['name']); ?> - ₱<?php echo htmlspecialchars($addon['price']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn btn-primary">Update Order</button>
            <a href="Order.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html> 