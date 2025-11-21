<?php
require_once "db.php";

// Check if order ID is provided
if (!isset($_GET['id'])) {
    die("Order ID is required");
}

$orderId = intval($_GET['id']);

// Get order details
$sql = "SELECT orders.*, 
        CONCAT(users.firstname, ' ', users.lastname) as customer_name,
        users.contact_number,
        order_items.item_name,
        order_items.quantity,
        order_items.unit_price,
        order_item_addons.addon_name,
        order_item_addons.addon_price
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id
        LEFT JOIN order_items ON orders.id = order_items.order_id
        LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
        WHERE orders.id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found");
}

// Group the results
$order = null;
$items = [];

while ($row = $result->fetch_assoc()) {
    if ($order === null) {
        $order = [
            'id' => $row['id'],
            'customer_name' => $row['customer_name'],
            'contact_number' => $row['contact_number'],
            'order_type' => $row['order_type'],
            'payment_method' => $row['payment_method'],
            'total_amount' => $row['total_amount'],
            'discount_type' => $row['discount_type'],
            'discount_amount' => $row['discount_amount'],
            'status' => $row['status'],
            'order_date' => $row['order_date']
        ];
    }

    $itemKey = $row['item_name'];
    if (!isset($items[$itemKey])) {
        $items[$itemKey] = [
            'name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'unit_price' => $row['unit_price'],
            'addons' => []
        ];
    }

    if ($row['addon_name']) {
        $items[$itemKey]['addons'][] = [
            'name' => $row['addon_name'],
            'price' => $row['addon_price']
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Order Details #<?php echo $orderId; ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact_number']); ?></p>
                        <p><strong>Order Type:</strong> <?php echo htmlspecialchars($order['order_type']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($order['status']); ?></span></p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Order Items</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Add-ons</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td>₱<?php echo htmlspecialchars(number_format($item['unit_price'], 2)); ?></td>
                                        <td>
                                            <?php if (!empty($item['addons'])): ?>
                                                <?php foreach ($item['addons'] as $addon): ?>
                                                    <?php echo htmlspecialchars($addon['name']); ?> 
                                                    (₱<?php echo htmlspecialchars(number_format($addon['price'], 2)); ?>)<br>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>₱<?php 
                                            $subtotal = $item['quantity'] * $item['unit_price'];
                                            foreach ($item['addons'] as $addon) {
                                                $subtotal += $addon['price'];
                                            }
                                            echo htmlspecialchars(number_format($subtotal, 2));
                                        ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td>₱<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                </tr>
                                <?php if ($order['discount_amount'] > 0): ?>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Discount (<?php echo htmlspecialchars($order['discount_type']); ?>):</strong></td>
                                        <td>₱<?php echo htmlspecialchars(number_format($order['discount_amount'], 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Final Total:</strong></td>
                                        <td>₱<?php echo htmlspecialchars(number_format($order['total_amount'] - $order['discount_amount'], 2)); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <a href="ProcessingOrder.php" class="btn btn-secondary">Back to Orders</a>
                        <?php if ($order['status'] === 'processing'): ?>
                            <button class="btn btn-success finish-order" data-order-id="<?php echo $orderId; ?>">
                                Mark as Finished
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.finish-order').click(function() {
            var orderId = $(this).data('order-id');
            if(confirm('Are you sure you want to mark this order as finished?')) {
                $.ajax({
                    url: 'finish_order.php',
                    method: 'POST',
                    data: { order_id: orderId },
                    success: function(response) {
                        if(response.success) {
                            alert('Order marked as finished successfully!');
                            window.location.href = 'ProcessingOrder.php';
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing the request.');
                    }
                });
            }
        });
    });
    </script>
</body>
</html> 