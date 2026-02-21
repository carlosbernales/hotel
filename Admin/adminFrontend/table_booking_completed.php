<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT *
        FROM orders_table 
        WHERE status IN ('Completed')
        ORDER BY date_time DESC";

$result = mysqli_query($conn, $sql);

$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>


<style>
    .receipt-modal-dialog {
        max-width: fit-content;
        width: auto;
        margin: 1.75rem auto;
    }

    @media (max-width: 768px) {
        .receipt-modal-dialog {
            max-width: 95vw;
        }
    }

    #receiptModal .modal-content {
        width: auto;
        border-radius: 12px;
    }

    #receiptModal .modal-body {
        padding: 0;
        max-height: 90vh;
        overflow-y: auto;
    }

    .receipt-container {
        width: 100%;
        max-width: 780px;
        margin: 0 auto;
    }

    .receipt-container {
        font-family: "Courier New", monospace;
        background: #fff;
        padding: 40px;
        max-width: 800px;
        margin: auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .receipt-container .header {
        text-align: center;
        border-bottom: 2px dashed #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .receipt-container .section-title {
        font-weight: bold;
        border-bottom: 1px solid #333;
        margin: 25px 0 10px;
    }

    .receipt-container .detail-row,
    .receipt-container .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        padding: 5px 0;
    }

    .receipt-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 10px;
    }

    .receipt-container th,
    .receipt-container td {
        padding: 8px;
        border-bottom: 1px dotted #ccc;
    }

    .receipt-container th {
        text-align: left;
    }

    .receipt-container .grand-total {
        font-size: 18px;
        font-weight: bold;
        border-top: 2px solid #333;
        margin-top: 10px;
        padding-top: 10px;
    }

    .receipt-container .footer {
        text-align: center;
        font-size: 12px;
        border-top: 2px dashed #333;
        margin-top: 40px;
        padding-top: 20px;
    }

    .receipt-container .no-data {
        font-style: italic;
        color: #777;
    }
</style>


<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Completed Table Bookings</i>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Fullname</th>
                        <th>Contact</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $order_id = $order['id'];

                        // GET TABLE INFO
                        $stmtTable = $conn->prepare("
                            SELECT table_name, table_number
                            FROM orders_table_type
                            WHERE table_booking_fk_id = ?
                        ");
                        $stmtTable->bind_param("i", $order_id);
                        $stmtTable->execute();
                        $tableRes = $stmtTable->get_result();

                        $tables = [];
                        while ($tRow = $tableRes->fetch_assoc()) {
                            $tables[] = $tRow;
                        }

                        // ORDER ITEMS
                        $stmtItems = $conn->prepare("
                            SELECT id, item_name, quantity, unit_price
                            FROM order_items
                            WHERE order_fk_id = ?
                        ");
                        $stmtItems->bind_param("i", $order_id);
                        $stmtItems->execute();
                        $itemsRes = $stmtItems->get_result();
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?></td>
                            <td><?= htmlspecialchars($order['contact'] ?? '') ?></td>

                            <td><?= date("F j, Y g:i A", strtotime($order['date_time'])) ?></td>
                            <td>
                                <?php if ($order['status'] === 'Completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Accepted</span>
                                <?php endif; ?>
                            </td>

                            <?php
                            // TABLES
                            $stmt = $conn->prepare("
                                SELECT table_name, table_number
                                FROM orders_table_type
                                WHERE table_booking_fk_id = ?
                            ");
                            $stmt->bind_param("i", $order_id);
                            $stmt->execute();
                            $tables = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                            // ITEMS + ADDONS
                            $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_fk_id = ?");
                            $stmt->bind_param("i", $order_id);
                            $stmt->execute();
                            $itemRes = $stmt->get_result();

                            $items = [];
                            while ($item = $itemRes->fetch_assoc()) {
                                $stmt2 = $conn->prepare("SELECT * FROM order_item_addons WHERE order_item_fk_id = ?");
                                $stmt2->bind_param("i", $item['id']);
                                $stmt2->execute();
                                $item['addons'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                                $items[] = $item;
                            }

                            ?>
                            <td class="d-flex gap-2 align-items-center">
                                <!-- View Order Summary -->
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#viewModal<?= $order_id ?>">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <button class="btn btn-sm btn-warning open-receipt" data-bs-toggle="modal"
                                    data-bs-target="#receiptModal"
                                    data-order='<?= json_encode($order, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                    data-tables='<?= json_encode($tables, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                    data-items='<?= json_encode($items, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                    <i class="bi bi-receipt"></i> Receipt
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="viewModal<?= $order_id ?>" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-dark text-white border-0 py-3">
                                        <h5 class="modal-title d-flex align-items-center fw-bold">
                                            <i class="bi bi-receipt-cutoff text-warning me-3 fs-4"></i>
                                            <span>Order Summary <small
                                                    class="text-warning ms-2">#<?= $order['order_id'] ?></small></span>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4" style="background-color: #f4f7f6;">
                                        <div class="row g-4">
                                            <div class="col-lg-4">
                                                <div class="card border-0 shadow-sm mb-4">
                                                    <div class="card-header bg-white border-0 pt-3">
                                                        <h6 class="text-uppercase text-muted fw-bold small mb-0">Customer
                                                            Details</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div
                                                                class="bg-warning-subtle text-warning rounded-circle p-2 me-3">
                                                                <i class="bi bi-person-fill fs-5"></i>
                                                            </div>
                                                            <div>
                                                                <?php if (!empty($order['firstname']) || !empty($order['lastname'])): ?>
                                                                    <div class="fw-bold fs-5 text-dark">
                                                                        <?= htmlspecialchars(trim(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? ''))) ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($order['contact'])): ?>
                                                                    <div class="text-muted small">
                                                                        <?= htmlspecialchars($order['contact']) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-header bg-white border-0 pt-3">
                                                        <h6 class="text-uppercase text-muted fw-bold small mb-0">Seating
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if (!empty($tables)): ?>
                                                            <?php foreach ($tables as $t): ?>
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center p-2 mb-2 rounded bg-light border">
                                                                    <span
                                                                        class="text-dark fw-medium"><?= htmlspecialchars($t['table_name']) ?></span>
                                                                    <span class="badge bg-dark rounded-pill">No.
                                                                        <?= htmlspecialchars($t['table_number']) ?></span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <div class="text-center py-2 text-muted italic small">No table
                                                                assigned</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>



                                                <div class="card border-0 shadow-sm mt-4">
                                                    <div class="card-header bg-white border-0 pt-3">
                                                        <h6 class="text-uppercase text-muted fw-bold small mb-0">Financial
                                                            Overview</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <ul class="list-group list-group-flush">

                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="bg-dark-subtle text-dark rounded-circle p-2 me-3"
                                                                        style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                                                        <i class="bi bi-wallet2"></i>
                                                                    </div>
                                                                    <span
                                                                        class="text-muted small fw-bold text-uppercase">Total</span>
                                                                </div>
                                                                <span
                                                                    class="fw-bold text-dark">₱<?= number_format($order['total'] ?? 0, 2) ?></span>
                                                            </li>

                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="bg-info-subtle text-info rounded-circle p-2 me-3"
                                                                        style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                                                        <i class="bi bi-cash-stack"></i>
                                                                    </div>
                                                                    <span
                                                                        class="text-muted small fw-bold text-uppercase">Deposit</span>
                                                                </div>
                                                                <span
                                                                    class="fw-bold text-info">₱<?= number_format($order['downpayment'] ?? 0, 2) ?></span>
                                                            </li>

                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center py-3 border-top">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="bg-danger-subtle text-danger rounded-circle p-2 me-3"
                                                                        style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                                                        <i class="bi bi-exclamation-circle"></i>
                                                                    </div>
                                                                    <span
                                                                        class="text-muted small fw-bold text-uppercase">Balance</span>
                                                                </div>
                                                                <span
                                                                    class="fw-bold text-danger fs-5">₱<?= number_format($order['remaining_balance'] ?? 0, 2) ?></span>
                                                            </li>


                                                            <?php if (!empty($order['reject_reason'])): ?>
                                                                <div class="card border-0 shadow-sm mt-4">
                                                                    <div class="card-header bg-white border-0 pt-3">
                                                                        <h6
                                                                            class="text-uppercase text-muted fw-bold small mb-0">
                                                                            Reject Reason
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <p class="text-danger fw-medium mb-0">
                                                                            <?= htmlspecialchars($order['reject_reason']) ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-lg-8">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div
                                                        class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                                                        <h6 class="text-uppercase text-muted fw-bold small mb-0">Items
                                                            Ordered</h6>
                                                        <span
                                                            class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                            <?= $itemsRes->num_rows ?> Items
                                                        </span>
                                                    </div>

                                                    <div class="card-body">
                                                        <?php if ($itemsRes->num_rows > 0): ?>
                                                            <?php while ($item = $itemsRes->fetch_assoc()):
                                                                $item_subtotal = $item['quantity'] * $item['unit_price'];
                                                                $stmtAddon = $conn->prepare("SELECT addon_name, quantity, price FROM order_item_addons WHERE order_item_fk_id = ?");
                                                                $stmtAddon->bind_param("i", $item['id']);
                                                                $stmtAddon->execute();
                                                                $addonRes = $stmtAddon->get_result();
                                                                ?>
                                                                <div class="p-3 mb-3 border rounded-3 bg-white hover-shadow-sm">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-6">
                                                                            <h6 class="mb-0 fw-bold text-dark">
                                                                                <?= htmlspecialchars($item['item_name']) ?>
                                                                            </h6>
                                                                            <div class="text-muted small mt-1">
                                                                                ₱<?= number_format($item['unit_price'], 2) ?> x
                                                                                <?= $item['quantity'] ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 text-md-end">
                                                                            <span
                                                                                class="fs-5 fw-bold text-success">₱<?= number_format($item_subtotal, 2) ?></span>
                                                                        </div>
                                                                    </div>

                                                                    <?php if ($addonRes->num_rows > 0): ?>
                                                                        <div class="mt-2 pt-2 border-top">
                                                                            <div class="text-uppercase text-muted extra-small fw-bold mb-1"
                                                                                style="font-size: 0.7rem;">Extras / Add-ons</div>
                                                                            <?php while ($addon = $addonRes->fetch_assoc()): ?>
                                                                                <div
                                                                                    class="d-flex justify-content-between small text-secondary ps-2 border-start border-2 ms-1">
                                                                                    <span>+ <?= htmlspecialchars($addon['addon_name']) ?>
                                                                                        (x<?= $addon['quantity'] ?>)</span>
                                                                                    <span>₱<?= number_format($addon['price'] * $addon['quantity'], 2) ?></span>
                                                                                </div>
                                                                            <?php endwhile; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endwhile; ?>

                                                        <?php else: ?>
                                                            <div class="text-center py-5">
                                                                <div class="mb-3">
                                                                    <i class="bi bi-cart-x text-muted"
                                                                        style="font-size: 3rem;"></i>
                                                                </div>
                                                                <h5 class="text-muted">No items found</h5>
                                                                <p class="text-secondary small">This order doesn't seem to have
                                                                    any items listed.</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light fw-bold px-4"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered receipt-modal-dialog">

        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body">

                <div class="receipt-container">

                    <!-- HEADER -->
                    <div class="header">
                        <h2>Casa Estela Boutique Hotel & Cafe</h2>
                        <p>Gov B Marasigan St, Calapan City</p>
                        <p><strong>TABLE BOOKING ACCEPTED RECEIPT</strong></p>
                        <p>Order ID: <span id="r-order-id"></span></p>
                        <p>Date: <span id="r-date"></span></p>
                    </div>

                    <div class="section-title">Customer Information</div>
                    <div class="detail-row"><span>Name:</span><span id="r-name"></span></div>
                    <div class="detail-row"><span>Email:</span><span id="r-email"></span></div>
                    <div class="detail-row"><span>Contact:</span><span id="r-contact"></span></div>

                    <div class="section-title">Table Information</div>
                    <table id="r-tables"></table>

                    <div class="section-title">Order Items</div>
                    <table id="r-items"></table>

                    <div id="r-payment"></div>

                    <div class="footer">
                        <p>Thank you for dining with us!</p>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0">
                <button class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<?php include 'adminFrontend/footer.php'; ?>
<script>
    document.querySelectorAll('.open-receipt').forEach(btn => {
        btn.addEventListener('click', () => {

            const order = JSON.parse(btn.dataset.order);
            const tables = JSON.parse(btn.dataset.tables);
            const items = JSON.parse(btn.dataset.items);

            // Header
            r('r-order-id', order.order_id);
            r('r-date', new Date(order.date_time).toLocaleString());

            // Customer
            r('r-name', `${order.firstname} ${order.lastname}`);
            r('r-email', order.email);
            r('r-contact', order.contact);

            let tableHTML = '';
            if (tables.length) {
                tableHTML += `<tr><th>Table</th><th>Number</th></tr>`;
                tables.forEach(t => {
                    tableHTML += `<tr><td>${t.table_name}</td><td>${t.table_number}</td></tr>`;
                });
            } else {
                tableHTML = `<tr><td class="no-data">No table assigned.</td></tr>`;
            }
            document.getElementById('r-tables').innerHTML = tableHTML;

            let itemHTML = '';
            if (items.length) {
                itemHTML += `<tr><th>Item</th><th>Qty</th><th style="text-align:right;">Price</th></tr>`;
                items.forEach(i => {
                    itemHTML += `
                    <tr>
                        <td>${i.item_name}</td>
                        <td>${i.quantity}</td>
                        <td style="text-align:right;">₱${Number(i.unit_price).toFixed(2)}</td>
                    </tr>
                `;
                    i.addons.forEach(ad => {
                        itemHTML += `
                        <tr>
                            <td style="padding-left:30px;">+ ${ad.addon_name}</td>
                            <td>${ad.quantity}</td>
                            <td style="text-align:right;">₱${Number(ad.price).toFixed(2)}</td>
                        </tr>
                    `;
                    });
                });
            } else {
                itemHTML = `<tr><td class="no-data">No items ordered.</td></tr>`;
            }
            document.getElementById('r-items').innerHTML = itemHTML;

            let paymentHTML = '';

            if (order.total && Number(order.total) > 0) {

                const total = Number(order.total) || 0;
                const downpayment = Number(order.downpayment) || 0;
                const remaining = Number(order.remaining_balance) || 0;
                const amountPaid = Number(order.amount_paid) || 0;
                const change = Number(order.change_amount) || 0;

                paymentHTML += `
                    <div class="section-title">Payment Summary</div>

                    <div class="total-row">
                        <span>Total:</span>
                        <span>₱${total.toFixed(2)}</span>
                    </div>
                `;

                if (downpayment > 0) {
                    paymentHTML += `
                    <div class="total-row">
                        <span>Downpayment:</span>
                        <span>₱${downpayment.toFixed(2)}</span>
                    </div>
                `;

                    if (order.dp_payment_method) {
                        paymentHTML += `
                        <div class="detail-row">
                            <span>DP Payment Method:</span>
                            <span>${order.dp_payment_method}</span>
                        </div>
                    `;
                    }
                }

                if (amountPaid > 0) {
                    paymentHTML += `
                        <div class="total-row">
                            <span>Amount Paid:</span>
                            <span>₱${amountPaid.toFixed(2)}</span>
                        </div>
                    `;
                }

                if (change > 0) {
                    paymentHTML += `
                        <div class="total-row">
                            <span>Change:</span>
                            <span style="color:green;">₱${change.toFixed(2)}</span>
                        </div>
                    `;
                }

                if (remaining > 0) {
                    paymentHTML += `
                    <div class="total-row grand-total">
                        <span>Remaining Balance:</span>
                        <span style="color:red;">₱${remaining.toFixed(2)}</span>
                    </div>
                `;
                } else {
                    paymentHTML += `
                    <div class="total-row grand-total">
                        <span>Status:</span>
                        <span style="color:green; font-weight:bold;">FULLY PAID</span>
                    </div>
                `;
                }

                if (order.payment_method) {
                    paymentHTML += `
                    <div class="detail-row">
                        <span>Payment Method:</span>
                        <span>${order.payment_method}</span>
                    </div>
                `;
                }
            }

            document.getElementById('r-payment').innerHTML = paymentHTML;

        });
    });

    function r(id, val) {
        document.getElementById(id).textContent = val ?? '-';
    }
</script>