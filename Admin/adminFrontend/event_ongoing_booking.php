<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT * FROM event_bookings WHERE booking_status IN ('Ongoing')";
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
            <i class="fas fa-home"></i> Accepted and Reserved Event Bookings
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
                                                        required>
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

                                            <div class="row g-3 mt-3">
                                                <div class="col-md-6">
                                                    <label class="text-muted d-block small">Overtime Hours</label>
                                                    <input type="number" min="0" step="0.1" class="form-control"
                                                        id="overtimeHours_<?php echo $order['id']; ?>"
                                                        value="<?php echo htmlspecialchars($order['overtime_hours']); ?>">

                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted d-block small">Overtime Charge (per
                                                        hour)</label>
                                                    <input type="number" min="0" step="0.01" class="form-control"
                                                        id="overtimeCharge_<?php echo $order['id']; ?>"
                                                        value="<?php echo htmlspecialchars(($order['overtime_charge'] ?? 0) == 0 ? 2000 : $order['overtime_charge']); ?>">

                                                </div>
                                            </div>

                                            <!-- Display total overtime amount -->
                                            <div class="mt-2">
                                                <label class="text-muted d-block small">Overtime Total</label>
                                                <input type="text" class="form-control"
                                                    id="overtimeAmount_<?php echo $order['id']; ?>" readonly value="0.00">
                                            </div>


                                            <div
                                                class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                                <div>
                                                    <span class="text-muted small">Payment Method:</span><br>
                                                    <span
                                                        class="badge bg-dark"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-muted small">Total Amount</div>
                                                    <span class="total-amount-display"
                                                        id="totalAmount_<?php echo $order['id']; ?>">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                                </div>
                                            </div>

                                            <!-- Payment Input -->
                                            <div class="mt-4">
                                                <label class="text-muted d-block small">Enter Payment Amount</label>
                                                <input type="number" min="0" step="0.01" class="form-control"
                                                    id="paymentInput_<?php echo $order['id']; ?>"
                                                    oninput="calculateChange(<?php echo $order['id']; ?>)">
                                                <small class="text-muted">Payment received from client</small>
                                            </div>
                                            <div class="mt-2">
                                                <span class="text-muted small">Change:</span>
                                                <span id="changeDisplay_<?php echo $order['id']; ?>"
                                                    class="fw-bold text-dark">₱0.00</span>
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
                                            Complete
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
    (function () {
        const guestInput = document.getElementById("guestCount_<?php echo $order['id']; ?>");
        const priceInput = document.getElementById("pricePerGuest_<?php echo $order['id']; ?>");
        const extraAmountInput = document.getElementById("extraAmount_<?php echo $order['id']; ?>");
        const newTotalInput = document.getElementById("newTotal_<?php echo $order['id']; ?>");
        const paymentInput = document.getElementById("paymentInput_<?php echo $order['id']; ?>");
        const changeDisplay = document.getElementById("changeDisplay_<?php echo $order['id']; ?>");
        const totalAmountDisplay = document.getElementById("totalAmount_<?php echo $order['id']; ?>");
        const balanceDueDisplay = document.querySelector("#viewModal_<?php echo $order['id']; ?> .payment-stat.due h5");

        const overtimeHoursInput = document.getElementById("overtimeHours_<?php echo $order['id']; ?>");
        const overtimeChargeInput = document.getElementById("overtimeCharge_<?php echo $order['id']; ?>");
        const overtimeAmountInput = document.getElementById("overtimeAmount_<?php echo $order['id']; ?>");

        const originalTotal = parseFloat(<?php echo $order['total_amount']; ?>);
        const paidAmount = parseFloat(<?php echo $order['paid_amount']; ?>); // initial paid amount

        const completeBtn = document.querySelector("#viewModal_<?php echo $order['id']; ?> .btn-gold-action");

        function updateAmounts() {
            let guests = parseInt(guestInput?.value) || 0;
            let price = parseFloat(priceInput?.value) || 0;
            const extraAmount = Math.max(0, guests * price);
            extraAmountInput.value = extraAmount.toFixed(2);

            let overtimeHours = parseFloat(overtimeHoursInput?.value) || 0;
            let overtimeCharge = parseFloat(overtimeChargeInput?.value) || 0;
            const overtimeAmount = Math.max(0, overtimeHours * overtimeCharge);
            overtimeAmountInput.value = overtimeAmount.toFixed(2);

            const newTotal = originalTotal + extraAmount + overtimeAmount;
            newTotalInput.value = newTotal.toFixed(2);
            totalAmountDisplay.textContent = `₱${newTotal.toFixed(2)}`;

            const balanceDue = newTotal - paidAmount;
            if (balanceDueDisplay) balanceDueDisplay.textContent = `₱${balanceDue.toFixed(2)}`;

            const paymentAmount = parseFloat(paymentInput?.value) || 0;
            if (paymentAmount >= balanceDue) {
                completeBtn.disabled = false;
                completeBtn.classList.remove('disabled');
            } else {
                completeBtn.disabled = true;
                completeBtn.classList.add('disabled');
            }

            updateChange();
        }

        function updateChange() {
            const newTotal = parseFloat(newTotalInput.value) || originalTotal;
            const paymentAmount = parseFloat(paymentInput?.value) || 0;
            const change = paymentAmount - newTotal;
            changeDisplay.textContent = `₱${change >= 0 ? change.toFixed(2) : '0.00'}`;
        }

        completeBtn.addEventListener('click', function () {
            const newTotal = parseFloat(newTotalInput.value) || originalTotal;
            const paymentAmount = parseFloat(paymentInput?.value) || 0;
            const balanceDue = newTotal - paidAmount;

            if (paymentAmount < balanceDue) {
                alert('Payment is not enough to complete this booking!');
                return;
            }

            fetch('../Admin/adminBackend/event_mark_completed.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: <?php echo $order['id']; ?>,
                    paid_amount: newTotal,
                    remaining_balance: 0,
                    booking_status: 'Finished',
                    overtime_hours: parseFloat(document.getElementById('overtimeHours_<?php echo $order['id']; ?>').value) || 0,
                    overtime_charge: parseFloat(document.getElementById('overtimeCharge_<?php echo $order['id']; ?>').value) || 0,
                    total_amount: newTotal
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking marked as Completed!');
                        location.reload();
                    } else {
                        alert('Failed to update booking. Try again.');
                    }
                })
                .catch(err => console.error(err));
        });



        guestInput?.addEventListener('input', updateAmounts);
        priceInput?.addEventListener('input', updateAmounts);
        overtimeHoursInput?.addEventListener('input', updateAmounts);
        overtimeChargeInput?.addEventListener('input', updateAmounts);
        paymentInput?.addEventListener('input', updateAmounts);

        updateAmounts();
    })();

</script>