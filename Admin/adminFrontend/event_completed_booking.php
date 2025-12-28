<?php
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
                                                        <?php echo htmlspecialchars($order['max_guest']); ?>
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

<?php include 'adminFrontend/footer.php'; ?>