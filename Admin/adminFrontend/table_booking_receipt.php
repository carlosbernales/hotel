<?php
include 'adminBackend/mydb.php';

if (!isset($_GET['id'])) {
    die("<h3>No Order ID provided.</h3>");
}

$order_id = intval($_GET['id']);

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

        .detail-row,
        .total-row {
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
            <p><strong>TABLE BOOKING ACCEPTED RECEIPT</strong></p>
            <p>Order ID: <?= $order['order_id'] ?></p>
            <p>Date: <?= date("F j, Y h:i A", strtotime($order['date_time'])) ?></p>
        </div>

        <!-- CUSTOMER INFO -->
        <div class="section-title">Customer Information</div>
        <div class="detail-row">
            <span>Name:</span><span><?= htmlspecialchars($order['firstname'] . " " . $order['lastname']) ?></span>
        </div>
        <div class="detail-row"><span>Email:</span><span><?= htmlspecialchars($order['email']) ?></span></div>
        <div class="detail-row"><span>Contact:</span><span><?= htmlspecialchars($order['contact']) ?></span></div>

        <!-- TABLE INFO -->
        <div class="section-title">Table Information</div>
        <?php
        $table_sql = "
            SELECT table_name, table_number
            FROM orders_table_type
            WHERE table_booking_fk_id = ?
        ";
        $stmt = $conn->prepare($table_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $tables = $stmt->get_result();
        ?>

        <?php if ($tables->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Table</th>
                    <th>Number</th>
                </tr>
                <?php while ($t = $tables->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['table_name']) ?></td>
                        <td><?= $t['table_number'] ?></td>
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
        ?>

        <?php if ($items->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th style="text-align:right;">Price</th>
                </tr>

                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td style="text-align:right;">₱<?= number_format($item['unit_price'], 2) ?></td>
                    </tr>

                    <?php
                    $addon_sql = "SELECT * FROM order_item_addons WHERE order_item_fk_id = ?";
                    $stmt2 = $conn->prepare($addon_sql);
                    $stmt2->bind_param("i", $item['id']);
                    $stmt2->execute();
                    $addons = $stmt2->get_result();
                    ?>

                    <?php while ($ad = $addons->fetch_assoc()): ?>
                        <tr>
                            <td style="padding-left:30px;">+ <?= htmlspecialchars($ad['addon_name']) ?></td>
                            <td><?= $ad['quantity'] ?></td>
                            <td style="text-align:right;">₱<?= number_format($ad['price'], 2) ?></td>
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
            <span>Total:</span>
            <span>₱<?= number_format($order['total'], 2) ?></span>
        </div>

        <?php if (!empty($order['downpayment'])): ?>
            <div class="total-row">
                <span>Downpayment:</span>
                <span>₱<?= number_format($order['downpayment'], 2) ?></span>
            </div>

            <?php if (!empty($order['dp_payment_method'])): ?>
                <div class="detail-row">
                    <span>DP Payment Method:</span>
                    <span><?= htmlspecialchars($order['dp_payment_method']) ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="total-row grand-total">
            <span>Remaining Balance:</span>
            <span>₱<?= number_format($order['remaining_balance'], 2) ?></span>
        </div>

        <?php if (!empty($order['payment_method'])): ?>
            <div class="detail-row">
                <span>Payment Method:</span>
                <span><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
        <?php endif; ?>


        <!-- FOOTER -->
        <div class="footer">
            <p>Thank you for dining with us!</p>
        </div>
    </div>


    <div style="text-align: center; margin-top: 20px;">
        <button id="saveReceiptBtn" style="padding: 10px 20px;">Accept this booking!</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <div id="loadingOverlay" style="
    position: fixed;
    top: 0; left: 0; 
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
">
        <div id="loadingBox" style="text-align:center; color: white; font-size: 22px;">
            <div class="spinner" style="
                border: 6px solid #f3f3f3;
                border-top: 6px solid #ffffff;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                animation: spin 1s linear infinite;
                margin: auto;
                margin-bottom: 20px;
            ">
            </div>
            <div id="loadingText">Please wait... processing your request</div>
        </div>

        <div id="successBox" style="
            display:none;
            background:#2ecc71;
            padding:25px 40px;
            border-radius:10px;
            color:white;
            text-align:center;
            font-size:22px;
            animation: fadein 0.4s ease-out;
        ">
            <div style="font-size:26px; font-weight:bold; margin-bottom:10px;">
                Request done!
            </div>
            <button id="okBtn" style="
            background:white;
            color:#2ecc71;
            padding:10px 25px;
            border:none;
            border-radius:5px;
            font-size:18px;
            cursor:pointer;
            margin-top:15px;
        ">OK</button>
        </div>
    </div>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <script>
        document.getElementById('saveReceiptBtn').addEventListener('click', async function () {
            const btn = this;
            btn.disabled = true;

            const overlay = document.getElementById('loadingOverlay');
            const loadingBox = document.getElementById('loadingBox');
            const successBox = document.getElementById('successBox');

            overlay.style.display = 'flex';
            loadingBox.style.display = 'block';
            successBox.style.display = 'none';

            try {
                const container = document.querySelector('.receipt-container');
                const canvas = await html2canvas(container, { scale: 2 });
                const dataUrl = canvas.toDataURL('image/png');

                const res = await fetch('../Admin/adminBackend/table_save_receipt_image.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        id: <?= $order_id ?>,
                        image: dataUrl,
                    }),
                    headers: { 'Content-Type': 'application/json' }
                });

                const msg = await res.text();
                console.log(msg);

                loadingBox.style.display = 'none';
                successBox.style.display = 'block';

                document.getElementById('okBtn').onclick = function () {
                    overlay.style.display = 'none';
                    window.location.href = 'index.php?table-booking-pend';
                };
            } catch (err) {
                console.error(err);
                alert('Failed to accept booking.');
                btn.disabled = false;
                overlay.style.display = 'none';
            }
        });

    </script>


</body>

</html>