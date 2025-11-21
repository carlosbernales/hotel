<?php
require "db.php";   

if (!isset($_GET['order_id'])) {
    die("Order ID is required");
}

$orderId = $_GET['order_id'];

// Get order details
$sql = "SELECT 
    orders.*,
    COALESCE(NULLIF(CONCAT(userss.first_name, ' ', userss.last_name), ' '), 'Walk-in Customer') as customer_name
    FROM orders 
    LEFT JOIN userss ON orders.user_id = userss.id
    WHERE orders.id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderResult = $stmt->get_result()->fetch_assoc();

// Get order items
$itemsSql = "SELECT 
    order_items.*,
    GROUP_CONCAT(DISTINCT CONCAT(order_item_addons.addon_name, ' (₱', order_item_addons.addon_price, ')') SEPARATOR ', ') as addons
    FROM order_items 
    LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
    WHERE order_items.order_id = ?
    GROUP BY order_items.id";

$stmtItems = $connection->prepare($itemsSql);
$stmtItems->bind_param("i", $orderId);
$stmtItems->execute();
$items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            width: 300px;
        }
        .receipt {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .items {
            width: 100%;
            margin-bottom: 20px;
        }
        .total {
            text-align: right;
            margin-top: 10px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>CASA ESTELA</h2>
            <p>Boutique Hotel & Cafe</p>
            <p>Order Receipt</p>
        </div>

        <div class="order-info">
            <p>Order #: <?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></p>
            <p>Date: <?php echo date('Y-m-d H:i:s', strtotime($orderResult['order_date'])); ?></p>
            <p>Customer: <?php echo htmlspecialchars($orderResult['customer_name']); ?></p>
            <p>Type: <?php echo ucfirst($orderResult['order_type']); ?></p>
        </div>

        <div class="divider"></div>

        <table class="items">
            <tr>
                <th style="text-align: left;">Item</th>
                <th style="text-align: right;">Amount</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($item['item_name']); ?><br>
                        <?php echo $item['quantity']; ?> x ₱<?php echo number_format($item['unit_price'], 2); ?>
                        <?php if (!empty($item['addons'])): ?>
                            <br><small>Add-ons: <?php echo htmlspecialchars($item['addons']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        ₱<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="divider"></div>

        <div class="total">
            <p>Subtotal: ₱<?php echo number_format($orderResult['total_amount'], 2); ?></p>
            <?php if (!empty($orderResult['discount_type'])): ?>
                <p>Discount (<?php echo $orderResult['discount_type'] == 'senior_citizen' ? 'Senior Citizen' : 'PWD'; ?>): 
                   ₱<?php echo number_format($orderResult['discount_amount'], 2); ?></p>
            <?php endif; ?>
            <p><strong>Total Amount: ₱<?php echo number_format($orderResult['total_amount'] - ($orderResult['discount_amount'] ?? 0), 2); ?></strong></p>
            <?php if ($orderResult['amount_paid'] > 0): ?>
                <p>Amount Paid: ₱<?php echo number_format($orderResult['amount_paid'], 2); ?></p>
                <p>Change: ₱<?php echo number_format($orderResult['change_amount'], 2); ?></p>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Thank you for dining with us!</p>
            <p>Please come again</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        // Automatically open print dialog when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>