<?php
if (
    !isset($_SESSION['user_type']) ||
    ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'frontdesk')
) {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT * FROM event_bookings WHERE booking_status IN ('Finished')";
$result = mysqli_query($conn, $sql);

$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<style>
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--gold) 0%, #b8941f 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-header .modal-title {
        color: #2c2c2c;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 0.5px;
    }

    .modal-section-label {
        display: block;
        color: var(--gold);
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }

    .modal-summary-box {
        background-color: #fcfaf5;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .payment-stat {
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        border: none;
    }

    .payment-stat.paid {
        background-color: #e9f5ec;
        color: #1e7e34;
    }

    .payment-stat.due {
        background-color: #fdf2f2;
        color: #bd2130;
    }

    .modal-body .form-label {
        font-weight: 600;
        color: var(--dark-bg);
        font-size: 0.9rem;
    }

    .modal-body .form-control {
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .modal-body .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }

    .modal-body .form-control[readonly] {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .btn-gold-action {
        background-color: var(--gold);
        border: none;
        color: #2c2c2c;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-gold-action:hover {
        background-color: #b8941f;
        color: #000;
        transform: translateY(-1px);
    }

    .btn-outline-custom {
        border: 1px solid #ccc;
        color: #666;
        background: transparent;
    }

    .btn-outline-custom:hover {
        background: #f8f9fa;
        color: #333;
    }

    .data-highlight {
        color: var(--gold);
        font-weight: 800;
        font-size: 1.1rem;
    }

    .total-amount-display {
        color: #b8941f;
        font-size: 1.5rem;
        font-weight: 800;
    }
</style>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"></i> Completed Event Bookings
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Bookings List</h5>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Fullname</th>
                        <th>Package</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['booking_refId']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                            <td>
                                <?php
                                $start = strtotime($order['date_time_start']);
                                $end = strtotime($order['date_time_end']);
                                echo date('l, M d, Y h:i A', $start) . ' - ' . date('h:i A', $end);
                                ?>
                            </td>
                            <td><span
                                    class="badge bg-warning text-dark"><?php echo htmlspecialchars($order['booking_status']); ?></span>
                            </td>
                            <td><?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <!-- Existing buttons -->
                                <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                    data-bs-target="#viewModal_<?php echo $order['id']; ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>

                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#receiptModal<?= $order['id'] ?>">
                                    <i class="bi bi-receipt"></i> Receipt
                                </button>


                                <!-- New Add Guest Button -->
                            </td>

                        </tr>


                        <!-- Modal for this order -->
                        <div class="modal fade" id="viewModal_<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-calendar-check me-2"></i> Event Booking Details
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <span class="modal-section-label">Client & Event Information</span>
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="text-muted d-block small">Booking Reference</label>
                                                <span
                                                    class="data-highlight"><?php echo htmlspecialchars($order['booking_refId']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="text-muted d-block small">Customer Name</label>
                                                <span
                                                    class="fw-bold text-dark"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="text-muted d-block small">Package & Type</label>
                                                <span
                                                    class="fw-bold"><?php echo htmlspecialchars($order['package_name']); ?></span>
                                                <span
                                                    class="badge rounded-pill bg-light text-dark border ms-1"><?php echo htmlspecialchars($order['event_type']); ?></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="text-muted d-block small">Schedule</label>
                                                <span class="fw-bold text-dark">
                                                    <i class="far fa-clock me-1 text-warning"></i>
                                                    <?php
                                                    echo date('l, M d, Y h:i A', strtotime($order['date_time_start'])) . ' – ' . date('h:i A', strtotime($order['date_time_end']));
                                                    ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="modal-summary-box">
                                            <div class="row text-center">
                                                <div class="col-4 border-end">
                                                    <label class="text-muted d-block small">Max Guests</label>
                                                    <h6 class="mb-0 fw-bold">
                                                        <?php
                                                        if (!empty($order['max_guest'])) {
                                                            echo htmlspecialchars($order['max_guest']);
                                                        }
                                                        ?>
                                                    </h6>
                                                </div>
                                                <div class="col-4 border-end">
                                                    <label class="text-muted d-block small">Actual Guests</label>
                                                    <h6 class="mb-0 fw-bold">
                                                        <?php echo htmlspecialchars($order['number_of_guests']); ?>
                                                    </h6>
                                                </div>
                                                <div class="col-4">
                                                    <label class="text-muted d-block small">Reserve Type</label>
                                                    <h6 class="mb-0 fw-bold text-uppercase small">
                                                        <?php echo htmlspecialchars($order['reserve_type']); ?>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>

                                        <span class="modal-section-label">Financial Summary</span>
                                        <div class="px-2">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Package Base Price</span>
                                                <span
                                                    class="fw-bold text-dark">₱<?php echo number_format($order['package_price'], 2); ?></span>
                                            </div>

                                            <?php if ($order['extra_guests'] > 0): ?>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Extra Guests
                                                        (<?php echo $order['extra_guests']; ?>)</span>
                                                    <span
                                                        class="text-muted">₱<?php echo number_format($order['extra_guest_charge'], 2); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($order['extra_guests'] > 0): ?>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Overtime Hours
                                                        (<?php echo $order['overtime_hours']; ?>)</span>
                                                    <span
                                                        class="text-muted">₱<?php echo number_format($order['overtime_charge'], 2); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <div
                                                class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                                <div>
                                                    <span class="text-muted small">Payment Method:</span><br>
                                                    <span
                                                        class="badge bg-dark"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-muted small">Total Amount</div>
                                                    <span
                                                        class="total-amount-display">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-custom px-4"
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

<style>
    /* ===== RECEIPT BASE STYLE ===== */
    .receipt-modal,
    .receipt-print {
        font-family: 'Courier New', monospace;
        color: #000;
    }

    .receipt-container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 40px;
    }

    /* Header */
    .receipt-container .header {
        text-align: center;
        border-bottom: 2px dashed #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .receipt-container .header h1 {
        font-size: 24px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .receipt-container .header p {
        font-size: 12px;
        margin: 3px 0;
    }

    /* Sections */
    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin: 25px 0 10px;
        border-bottom: 1px solid #333;
        padding-bottom: 4px;
        text-transform: uppercase;
    }

    /* Rows */
    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        padding: 4px 0;
    }

    .detail-label {
        font-weight: bold;
    }

    /* Tables */
    .receipt-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 10px;
    }

    .receipt-container th {
        border-bottom: 1px solid #333;
        text-align: left;
        padding: 6px;
    }

    .receipt-container td {
        border-bottom: 1px dotted #ccc;
        padding: 6px;
    }

    /* Totals */
    .total-section {
        margin-top: 30px;
        border-top: 2px dashed #333;
        padding-top: 15px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 15px;
        padding: 6px 0;
    }

    .total-row.grand-total {
        font-size: 18px;
        font-weight: bold;
        border-top: 2px solid #000;
        padding-top: 10px;
    }

    /* Footer */
    .footer {
        text-align: center;
        font-size: 12px;
        margin-top: 30px;
        border-top: 2px dashed #333;
        padding-top: 15px;
    }

    /* ===== PRINT FIX ===== */
    @media print {
        body {
            margin: 0;
        }

        .modal,
        .modal-header,
        .btn,
        .btn-close {
            display: none !important;
        }

        .receipt-container {
            padding: 0;
            box-shadow: none;
        }
    }
</style>

<?php foreach ($orders as $order): ?>
    <div class="modal fade" id="receiptModal<?= $order['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:800px;">
            <div class="modal-content receipt-modal">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt"></i> Event Receipt – <?= htmlspecialchars($order['booking_refId']) ?>
                    </h5>

                    <div class="ms-auto">
                        <button type="button" class="btn btn-light btn-sm"
                            onclick="printReceipt('receipt-content-<?= $order['id'] ?>')">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <div class="modal-body p-0">

                    <div class="receipt-container" id="receipt-content-<?= $order['id'] ?>">

                        <!-- HEADER -->
                        <div class="header">
                            <h1>Casa Estela Boutique Hotel & Cafe</h1>
                            <p>Gov B Marasigan St, Calapan City</p>
                            <p>Phone: 0908 747 4892</p>

                            <p><strong>EVENT BOOKING RECEIPT</strong></p>
                            <p>Reference: <?= htmlspecialchars($order['booking_refId']) ?></p>
                            <p>Date Issued: <?= date("F j, Y", strtotime($order['created_at'] ?? 'now')) ?></p>
                        </div>

                        <!-- CUSTOMER INFO -->
                        <div class="section-title">Customer Information</div>
                        <div class="detail-row">
                            <span class="detail-label">Customer Name:</span>
                            <span><?= htmlspecialchars($order['customer_name']) ?></span>
                        </div>
                        <?php if (!empty($order['email'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span><?= htmlspecialchars($order['email']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- EVENT INFO -->
                        <div class="section-title">Event Information</div>
                        <div class="detail-row">
                            <span class="detail-label">Package:</span>
                            <span><?= htmlspecialchars($order['package_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Event Type:</span>
                            <span><?= htmlspecialchars($order['event_type']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Schedule:</span>
                            <span><?= date("F j, Y h:i A", strtotime($order['date_time_start'])) ?> -
                                <?= date("h:i A", strtotime($order['date_time_end'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Reserve Type:</span>
                            <span><?= htmlspecialchars($order['reserve_type']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Guests:</span>
                            <span><?= htmlspecialchars($order['number_of_guests']) ?> /
                                <?= htmlspecialchars($order['max_guest']) ?></span>
                        </div>
                        <?php if (!empty($order['place'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Place:</span>
                                <span><?= htmlspecialchars($order['place']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- PAYMENT SUMMARY -->
                        <div class="total-section">
                            <div class="section-title">Payment Summary</div>

                            <div class="total-row">
                                <span>Package Price:</span>
                                <span>₱<?= number_format($order['package_price'], 2) ?></span>
                            </div>

                            <?php if ($order['extra_guests'] > 0): ?>
                                <div class="total-row">
                                    <span>Extra Guests Charge (<?= $order['extra_guests'] ?>):</span>
                                    <span>₱<?= number_format($order['extra_guest_charge'], 2) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($order['overtime_hours'] > 0): ?>
                                <div class="total-row">
                                    <span>Overtime Charge (<?= $order['overtime_hours'] ?> hrs):</span>
                                    <span>₱<?= number_format($order['overtime_charge'], 2) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($order['change_amount'])): ?>
                                <div class="total-row">
                                    <span>Change Amount:</span>
                                    <span>₱<?= number_format($order['change_amount'], 2) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="total-row">
                                <span>Paid Amount:</span>
                                <span>₱<?= number_format($order['paid_amount'] ?? 0, 2) ?></span>
                            </div>

                            <div class="total-row">
                                <span>Remaining Balance:</span>
                                <span>₱<?= number_format($order['remaining_balance'] ?? 0, 2) ?></span>
                            </div>

                            <div class="total-row grand-total">
                                <span>Total Amount:</span>
                                <span>₱<?= number_format($order['total_amount'], 2) ?></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Payment Method:</span>
                                <span><?= htmlspecialchars($order['payment_method']) ?></span>
                            </div>

                            <?php if (!empty($order['payment_type'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Payment Type:</span>
                                    <span><?= htmlspecialchars($order['payment_type']) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span><?= htmlspecialchars($order['booking_status']) ?></span>
                            </div>

                            <?php if (!empty($order['rejection_reason'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Notes:</span>
                                    <span><?= htmlspecialchars($order['rejection_reason']) ?></span>
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- FOOTER -->
                        <div class="footer">
                            <p>Thank you for booking with us!</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>



<script>
    function printReceipt(elementId) {
        const content =
            document.getElementById(elementId).innerHTML;

        const printWindow =
            window.open('', '', 'width=900,height=700');

        printWindow.document.write(`
        <html>
        <head>
            <title>Event Receipt</title>

            <style>

                body{
                    font-family:Courier New, monospace;
                    padding:20px;
                }

                .header{text-align:center;}

                .detail-row,
                .total-row{
                    display:flex;
                    justify-content:space-between;
                    padding:5px 0;
                }

                .section-title{
                    font-weight:bold;
                    margin-top:20px;
                    border-bottom:1px solid black;
                }

                .grand-total{
                    font-weight:bold;
                    font-size:18px;
                    border-top:2px solid black;
                }

            </style>

        </head>

        <body>

            ${content}

        </body>
        </html>
    `);

        printWindow.document.close();

        printWindow.focus();

        printWindow.print();

        printWindow.close();
    }

</script>

<?php include 'adminFrontend/footer.php'; ?>