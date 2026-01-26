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
<link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">

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
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?php echo ucfirst(htmlspecialchars($order['booking_status'])); ?>
                                </span>
                            </td>

                            <td><?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                    data-bs-target="#viewModal_<?php echo $order['id']; ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
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
                                        <button type="button" class="btn btn-danger px-4"
                                            onclick="rejectEvent(<?php echo $order['id']; ?>)">
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

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="fas fa-times-circle me-2"></i> Reject Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <input type="hidden" id="rejectEventId" name="event_id">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for rejection</label>
                        <textarea id="rejectReason" name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">Submit</button>
            </div>
        </div>
    </div>
</div>


<?php include 'adminFrontend/footer.php'; ?>

</script>


<script>
    function acceptEvent(id) {
        CasaEstelaModal.confirm(
            'Accept Booking',
            'Are you sure you want to accept this booking?',
            function () {
                window.location.href = "index.php?event-receipt-accepted=1&event_id=" + id;
            }
        );
    }

    function rejectEvent(id) {
        document.getElementById('rejectEventId').value = id;
        document.getElementById('rejectReason').value = '';

        var rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        rejectModal.show();
    }

    function submitRejection() {
        var id = document.getElementById('rejectEventId').value;
        var reason = document.getElementById('rejectReason').value.trim();

        if (reason === '') {
            CasaEstelaModal.show(
                'warning',
                'Missing Information',
                'Please enter a reason for rejection.'
            );
            return;
        }

        CasaEstelaModal.show(
            'info',
            'Processing Rejection',
            'Please wait while we update the booking status...',
            null
        );

        setTimeout(function () {
            window.location.href = "../Admin/adminBackend/event_reject.php?event_id=" + id + "&reason=" + encodeURIComponent(reason) + "&callback=success";
        }, 800);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('callback') === 'success') {
            CasaEstelaModal.show(
                'success',
                'Booking Updated',
                'The booking status has been successfully updated.',
                function () {
                    window.location.href = 'index.php?event-bookings';
                }
            );
        }
    });
</script>


<script>
    // ----- Casa Estela Inline Alerts -----
    const CasaEstelaAlert = {
        show: function (type, title, message, duration = 5000) {
            const icons = {
                success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            const alert = document.createElement('div');
            alert.className = `cea-inline-alert cea-inline-alert-${type}`;
            alert.innerHTML = `
                <div class="cea-inline-alert-icon">${icons[type]}</div>
                <div class="cea-inline-alert-content">
                    <div class="cea-inline-alert-title">${title}</div>
                    <div class="cea-inline-alert-message">${message}</div>
                </div>
                <button class="cea-inline-alert-close" onclick="this.parentElement.classList.add('cea-inline-alert-closing'); setTimeout(() => this.parentElement.remove(), 300)">×</button>
            `;

            document.body.appendChild(alert);

            if (duration > 0) {
                setTimeout(() => {
                    alert.classList.add('cea-inline-alert-closing');
                    setTimeout(() => alert.remove(), 300);
                }, duration);
            }
        }
    };

    // ----- Casa Estela Modal System -----
    const CasaEstelaModal = {
        show: function (type, title, message, onConfirm = null, showCancel = false) {
            const icons = {
                success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            const overlay = document.createElement('div');
            overlay.className = 'cea-modal-overlay';
            overlay.innerHTML = `
                <div class="cea-modal-dialog">
                    <div class="cea-modal-body">
                        <div class="cea-modal-icon-wrapper cea-modal-icon-wrapper-${type}">
                            ${icons[type]}
                        </div>
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            ${showCancel ? '<button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.close(this)">Cancel</button>' : ''}
                            <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">${showCancel ? 'Confirm' : 'OK'}</button>
                        </div>
                    </div>
                </div>
            `;
            overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
            document.body.appendChild(overlay);

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) CasaEstelaModal.close(overlay);
            });
        },

        confirm: function (title, message, onConfirm, onCancel = null) {
            const overlay = document.createElement('div');
            overlay.className = 'cea-modal-overlay';
            overlay.innerHTML = `
                <div class="cea-modal-dialog cea-modal-confirm">
                    <div class="cea-modal-body">
                        <div class="cea-modal-icon-wrapper">
                            <svg class="cea-icon-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            <button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.handleCancel(this)">Cancel</button>
                            <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">Confirm</button>
                        </div>
                    </div>
                </div>
            `;
            overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
            overlay.querySelector('.cea-modal-button-secondary').ceCancelCallback = onCancel;
            document.body.appendChild(overlay);
        },

        handleConfirm: function (btn) {
            if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') btn.ceConfirmCallback();
            this.close(btn);
        },

        handleCancel: function (btn) {
            if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') btn.ceCancelCallback();
            this.close(btn);
        },

        close: function (element) {
            const overlay = element.closest ? element.closest('.cea-modal-overlay') : element;
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 200);
            }
        }
    };
</script>