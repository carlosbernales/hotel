<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT *
        FROM orders_table 
        WHERE status IN ('confirmed')
        ORDER BY date_time DESC";

$result = mysqli_query($conn, $sql);

$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Pending Table Bookings</i>
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
                            <td><?= htmlspecialchars($order['contact']) ?></td>
                            <td><?= date("F j, Y g:i A", strtotime($order['date_time'])) ?></td>
                            <td>
                                <?php if ($order['status'] === 'confirmed'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Accepted</span>
                                <?php endif; ?>
                            </td>
                            <td class="d-flex gap-2 align-items-center">
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#viewModal<?= $order_id ?>">
                                    <i class="bi bi-eye"></i> View
                                </button>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-success dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-plus"></i> Action
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item text-success"
                                                href="../Admin/adminFrontend/table_booking_receipt.php&id=<?= $order_id ?>">
                                                <i class="bi bi-check-circle"></i> Accept
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger"
                                                href="index.php?add-order-existing-order&id=<?= $order_id ?>&action=reject">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
                                                                <div class="fw-bold fs-5 text-dark">
                                                                    <?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?>
                                                                </div>
                                                                <div class="text-muted small">
                                                                    <?= htmlspecialchars($order['contact']) ?>
                                                                </div>
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
<?php include 'adminFrontend/footer.php'; ?>