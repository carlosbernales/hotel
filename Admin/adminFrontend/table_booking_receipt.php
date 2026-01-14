<?php
include 'adminBackend/mydb.php';

if (!isset($_GET['booking_id'])) {
    die("<h3>No Order ID provided.</h3>");
}

$order_id = intval($_GET['booking_id']);

/* ============================
   FETCH ORDER
============================ */
$sql = "SELECT * FROM orders_table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("<h3>Order not found.</h3>");
}

$order = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Table Booking Receipt</title>
    <style>
        body {
            font-family: Courier New, monospace;
            background: #f5f5f5;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            background: #fff;
            padding: 40px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 30px
        }

        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #333;
            margin: 25px 0 10px
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 5px 0
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px dotted #ccc
        }

        th {
            text-align: left
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            padding: 6px 0
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px
        }

        .footer {
            text-align: center;
            font-size: 12px;
            border-top: 2px dashed #333;
            margin-top: 40px;
            padding-top: 20px
        }

        .no-data {
            font-style: italic;
            color: #777
        }
    </style>
</head>

<body>
    <div class="receipt-container">

        <!-- HEADER -->
        <div class="header">
            <h2>Casa Estela Boutique Hotel & Cafe</h2>
            <p>Gov B Marasigan St, Calapan City</p>
            <p><strong>TABLE BOOKING RECEIPT</strong></p>
            <p>Order Ref:
                <?= htmlspecialchars($order['order_id']) ?>
            </p>
            <p>Date:
                <?= date("F j, Y h:i A", strtotime($order['date_time'])) ?>
            </p>
        </div>

        <!-- CUSTOMER INFO -->
        <div class="section-title">Customer Information</div>
        <div class="detail-row"><span>Name:</span><span>
                <?= htmlspecialchars($order['firstname'] . " " . $order['lastname']) ?>
            </span></div>
        <div class="detail-row"><span>Email:</span><span>
                <?= htmlspecialchars($order['email']) ?>
            </span></div>
        <div class="detail-row"><span>Contact:</span><span>
                <?= htmlspecialchars($order['contact']) ?>
            </span></div>

        <!-- TABLE INFO -->
        <div class="section-title">Table Information</div>
        <?php
        $table_sql = "SELECT table_name, table_number 
              FROM orders_table_type 
              WHERE table_booking_fk_id = ?";
        $stmt = $conn->prepare($table_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $tables = $stmt->get_result();

        if ($tables->num_rows > 0):
            ?>
            <table>
                <tr>
                    <th>Table</th>
                    <th>Number</th>
                </tr>
                <?php while ($t = $tables->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($t['table_name']) ?>
                        </td>
                        <td>
                            <?= $t['table_number'] ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No table assigned.</p>
        <?php endif; ?>

        <!-- ORDER ITEMS -->
        <div class="section-title">Order Items</div>
        <?php
        $item_sql = "SELECT * FROM order_items WHERE order_fk_id = ?";
        $stmt = $conn->prepare($item_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items = $stmt->get_result();

        if ($items->num_rows > 0):
            ?>
            <table>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th style="text-align:right;">Price</th>
                </tr>

                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['item_name']) ?>
                        </td>
                        <td>
                            <?= $item['quantity'] ?>
                        </td>
                        <td style="text-align:right;">₱
                            <?= number_format($item['unit_price'], 2) ?>
                        </td>
                    </tr>

                    <?php
                    $addon_sql = "SELECT * FROM order_item_addons WHERE order_item_fk_id = ?";
                    $stmt2 = $conn->prepare($addon_sql);
                    $stmt2->bind_param("i", $item['id']);
                    $stmt2->execute();
                    $addons = $stmt2->get_result();

                    while ($ad = $addons->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="padding-left:30px;">+
                                <?= htmlspecialchars($ad['addon_name']) ?>
                            </td>
                            <td>
                                <?= $ad['quantity'] ?>
                            </td>
                            <td style="text-align:right;">₱
                                <?= number_format($ad['price'], 2) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No items ordered.</p>
        <?php endif; ?>

        <!-- PAYMENT SUMMARY -->
        <div class="section-title">Payment Summary</div>
        <div class="total-row">
            <span>Subtotal:</span>
            <span>₱
                <?= number_format($order['total'] + $order['discount_amount'], 2) ?>
            </span>
        </div>

        <?php if ($order['discount_percentage'] > 0): ?>
            <div class="total-row">
                <span>Discount (
                    <?= $order['discount_percentage'] ?>%)
                </span>
                <span>-₱
                    <?= number_format($order['discount_amount'], 2) ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>₱
                <?= number_format($order['total'], 2) ?>
            </span>
        </div>

        <div class="total-row">
            <span>Downpayment:</span>
            <span>₱
                <?= number_format($order['downpayment'], 2) ?>
            </span>
        </div>

        <div class="total-row">
            <span>Remaining Balance:</span>
            <span>₱
                <?= number_format($order['remaining_balance'], 2) ?>
            </span>
        </div>

        <div class="detail-row">
            <span>Payment Method:</span>
            <span>
                <?= htmlspecialchars($order['payment_method']) ?>
            </span>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Status: <strong>
                    <?= strtoupper($order['status']) ?>
                </strong></p>
            <p>Thank you for dining with us!</p>
        </div>

    </div>
</body>

</html>