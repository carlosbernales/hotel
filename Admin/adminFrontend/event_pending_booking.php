<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT * FROM event_bookings WHERE booking_status IN ('pending')";
$result = mysqli_query($conn, $sql);

$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/event_accepted_booking.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home">Pending Event Bookings</i>
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
                                <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal"
                                    data-bs-target="#addGuestModal_<?php echo $order['id']; ?>">
                                    <i class="fas fa-user-plus"></i> Add Guest
                                </button>
                            </td>

                        </tr>


                        <!-- Add Guest Modal -->
                        <div class="modal fade" id="addGuestModal_<?php echo $order['id']; ?>" tabindex="-1"
                            aria-labelledby="addGuestModalLabel_<?php echo $order['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addGuestModalLabel_<?php echo $order['id']; ?>">
                                            <i class="fas fa-user-plus me-2"></i> Add Guests
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <form action="adminBackend/event_add_guest.php?id=<?php echo $order['id']; ?>"
                                            method="POST" id="addGuestForm_<?php echo $order['id']; ?>">

                                            <span class="modal-section-label">Guest Adjustments</span>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label for="guestCount_<?php echo $order['id']; ?>"
                                                        class="form-label">Number of Guests</label>
                                                    <input type="number" class="form-control" name="extra_guests"
                                                        id="guestCount_<?php echo $order['id']; ?>" min="1" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="pricePerGuest_<?php echo $order['id']; ?>"
                                                        class="form-label">Price per Guest</label>
                                                    <input type="number" class="form-control" name="extra_guest_charge"
                                                        id="pricePerGuest_<?php echo $order['id']; ?>" min="0" step="0.01"
                                                        value="1200" required>
                                                </div>

                                            </div>

                                            <span class="modal-section-label">Billing Preview</span>

                                            <div class="modal-summary-box mb-4">
                                                <div class="row g-3">
                                                    <div class="col-6 mb-2">
                                                        <label class="text-muted small d-block">Current Paid</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="paidAmount_<?php echo $order['id']; ?>" readonly
                                                            value="<?php echo number_format($order['paid_amount'], 2); ?>">
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <label class="text-muted small d-block">Extra Charge</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="extraAmount_<?php echo $order['id']; ?>" readonly
                                                            value="0.00">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="text-muted small d-block">New Total</label>
                                                        <input type="text" class="form-control form-control-sm fw-bold"
                                                            id="newTotal_<?php echo $order['id']; ?>" readonly
                                                            value="<?php echo number_format($order['total_amount'], 2); ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="text-muted small d-block">Remaining Balance</label>
                                                        <input type="text"
                                                            class="form-control form-control-sm text-danger fw-bold"
                                                            id="remainingBalance_<?php echo $order['id']; ?>" readonly
                                                            value="<?php echo number_format($order['total_amount'] - $order['paid_amount'], 2); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <button type="button" class="btn btn-outline-custom px-4 me-2"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-gold-action px-4">Update
                                                    Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

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

                                        <div class="row mt-4 g-3">
                                            <div class="col-md-6">
                                                <div class="payment-stat paid">
                                                    <span class="small fw-bold text-uppercase d-block">Paid Amount</span>
                                                    <h5 class="mb-0 fw-bold">
                                                        ₱<?php echo number_format($order['paid_amount'], 2); ?></h5>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="payment-stat due">
                                                    <span class="small fw-bold text-uppercase d-block">Balance Due</span>
                                                    <h5 class="mb-0 fw-bold">
                                                        ₱<?php echo number_format($order['remaining_balance'], 2); ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-custom px-4"
                                            data-bs-dismiss="modal">Close</button>

                                        <button type="button" class="btn btn-gold-action px-4"
                                            onclick="setOngoing(<?php echo $order['id']; ?>)">
                                            Reject
                                        </button>
                                        <button type="button" class="btn btn-gold-action px-4"
                                            onclick="acceptEvent(<?= $order['id']; ?>)">
                                            Accept
                                        </button>
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

<script>
    function acceptEvent(id) {
        window.location.href = "index.php?event-receipt-accepted=1&event_id=" + id;
    }
</script>