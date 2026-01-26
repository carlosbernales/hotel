<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Event Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/event_booking.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">

</head>

<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="#"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </nav>

    <?php
    include 'adminBackend/mydb.php';

    $sql = "SELECT * FROM event_packages";
    $result = mysqli_query($conn, $sql);
    ?>

    <main class="main-content">
        <div class="position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close"
                onclick="window.location.href='../Admin/index.php?room_booking'">
            </button>

            <div class="filter-section d-flex flex-wrap align-items-center gap-2 mb-3">
                <button class="filter-btn active" data-filter="cafe">Cafe</button>
                <button class="filter-btn" data-filter="garden">Garden</button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <input type="datetime-local" id="globalBookingDateTime" class="form-control"
                        style="max-width: 250px;">


                    <button class="btn btn-info me-5" id="checkGlobalAvailabilityBtn" style="width: 200px;">
                        Check Availability
                    </button>
                </div>
            </div>
        </div>
        <div id="availabilityMessage" class="alert d-none"></div>
        <div class="row" id="eventsContainer">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $status = strtolower($row['status']);
                $badgeClass = $status === 'available' ? 'status-available' : 'status-unavailable';
                $menuItems = array_map('trim', explode(',', $row['menu_items']));
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="event-card" data-status="<?= $status ?>" data-place="<?= strtolower($row['place']) ?>">


                        <div class="event-image-gallery">
                            <img src="<?= $row['image_path'] ?>" class="main-event-image" id="mainImg<?= $row['id'] ?>">

                            <span class="event-status-badge <?= $badgeClass ?>">
                                <?= strtoupper($row['status']) ?>
                            </span>

                            <div class="image-thumbnails">
                                <?php if ($row['image_path']): ?>
                                    <img src="<?= $row['image_path'] ?>" class="thumbnail-img active">
                                <?php endif; ?>
                                <?php if ($row['image_path2']): ?>
                                    <img src="<?= $row['image_path2'] ?>" class="thumbnail-img">
                                <?php endif; ?>
                                <?php if ($row['image_path3']): ?>
                                    <img src="<?= $row['image_path3'] ?>" class="thumbnail-img">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="event-body">
                            <h5 class="event-name"><?= htmlspecialchars($row['name']) ?></h5>

                            <div class="event-price">
                                <span class="currency">₱</span>
                                <span><?= number_format($row['price']) ?></span>
                            </div>

                            <p class="event-description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>

                            <div class="event-details-grid">
                                <div class="event-detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?= $row['max_guests'] ?> Max Guests</span>
                                </div>
                                <div class="event-detail-item">
                                    <i class="fas fa-user-friends"></i>
                                    <span><?= $row['max_pax'] ?> Max Pax</span>
                                </div>
                                <div class="event-detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= $row['time_limit'] ?> Hours</span>
                                </div>
                                <div class="event-detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        <?= ucfirst($row['place']) ?> Event
                                    </span>
                                </div>

                            </div>

                            <?php if (!empty($row['notes'])): ?>
                                <div class="event-notes">
                                    <i class="fas fa-info-circle"></i>
                                    <?= nl2br(htmlspecialchars($row['notes'])) ?>
                                </div>
                            <?php endif; ?>


                            <div class="event-actions">
                                <button class="btn-view-menu" data-bs-toggle="modal"
                                    data-bs-target="#menuModal<?= $row['id'] ?>">
                                    <i class="fas fa-utensils"></i> View Menu
                                </button>

                                <?php if ($status === 'available'): ?>
                                    <button class="btn-book-now" data-package-id="<?= $row['id'] ?>"
                                        data-place="<?= $row['place'] ?>"
                                        data-package-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                        data-package-price="<?= number_format($row['price']) ?>"
                                        data-package-max-guests="<?= $row['max_guests'] ?>">

                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </button>

                                <?php endif; ?>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal fade" id="menuModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-utensils"></i>
                                    <?= htmlspecialchars($row['name']) ?> Menu
                                </h5>
                                <button type="button" class="close-cart" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <?php if (!empty($menuItems)): ?>
                                    <?php $count = 0;
                                    $total = count($menuItems); ?>
                                    <?php foreach ($menuItems as $item):
                                        $count++; ?>
                                        <div class="menu-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?= htmlspecialchars($item) ?>
                                        </div>
                                        <?php if ($count < $total): ?>
                                            <div class="menu-separator"></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center">No menu items available.</p>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </main>


    <div class="modal fade booking-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check"></i> Complete Your Booking
                    </h5>
                    <button type="button" class="close-cart" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="booking-summary">
                        <h6><i class="fas fa-info-circle"></i> Event Details</h6>
                        <div class="booking-summary-item">
                            <span>Package:</span>
                            <strong id="bookingPackageName">-</strong>
                        </div>
                        <div class="booking-summary-item">
                            <span>Max Guest:</span>
                            <strong id="bookingMaxGuests">-</strong>
                        </div>

                        <div class="booking-summary-item">
                            <span>Place:</span>
                            <strong id="bookingPlaceEvent">-</strong>
                        </div>
                        <div class="booking-summary-item">
                            <span>Base Price:</span>
                            <strong id="bookingPackagePrice">₱0</strong>
                        </div>
                        <div class="booking-summary-item">
                            <span>Additional Guests:</span>
                            <strong id="bookingAdditionalGuests">0</strong>
                        </div>
                        <div class="booking-summary-item">
                            <span>Price per Additional Guest:</span>
                            <strong id="bookingAdditionalGuestPrice">₱1200</strong>
                        </div>
                        <div class="booking-summary-item total-price">
                            <span>Total Amount:</span>
                            <span id="bookingTotalPrice">₱0</span>
                        </div>
                    </div>

                    <form id="bookingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-plus"></i> Additional Guests
                                </label>
                                <input type="number" class="form-control" id="additionalGuests" min="0" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Price per Additional Guest
                                </label>
                                <input type="number" class="form-control" id="pricePerAdditionalGuest" value="1200"
                                    min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" class="form-control" id="customerName" required
                                    placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" class="form-control" id="customerEmail" required
                                    placeholder="your.email@example.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Contact Number *
                                </label>
                                <input type="tel" class="form-control" id="customerPhone" required
                                    placeholder="+63 XXX XXX XXXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Event Date *
                                </label>
                                <input type="date" class="form-control" id="eventDate" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Event Time *
                                </label>
                                <input type="time" class="form-control" id="eventTime" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-users"></i> Number of Guests *
                                </label>
                                <input type="number" class="form-control" id="guestCount" required
                                    placeholder="e.g., 50" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i> Event Type *
                                </label>
                                <select class="form-control" id="eventType" required>
                                    <option value="">Select Event Type</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-check-alt"></i> Payment Type *
                                </label>
                                <select class="form-control" id="paymentType" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Custom">Custom</option>
                                    <option value="Half">Half</option>
                                    <option value="Full">Full</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-wallet"></i> Remaining Balance
                                </label>
                                <input type="number" class="form-control" id="remainingBalance" value="0" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Paid Amount *
                                </label>
                                <input type="number" class="form-control" id="paidAmount" value="0" min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card"></i> Payment Method *
                                </label>
                                <select class="form-control" id="paymentMethod" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-confirm-booking">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalTitle">
                        <i class="fas fa-utensils"></i> Event Menu
                    </h5>
                    <button type="button" class="close-cart" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" id="menuModalBody">

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let availabilityChecked = false;
        let hasConflict = false;
        let selectedPackageId = null;
        let selectedPlace = null;


        const checkBtn = document.getElementById('checkGlobalAvailabilityBtn');
        const bookingDateInput = document.getElementById('globalBookingDateTime');
        const bookNowButtons = document.querySelectorAll('.btn-book-now');
        const availabilityMessage = document.getElementById('availabilityMessage');
        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

        const guestCountInput = document.getElementById('guestCount');
        const additionalGuestsInput = document.getElementById('additionalGuests');
        const pricePerAdditionalInput = document.getElementById('pricePerAdditionalGuest');
        const paidAmountInput = document.getElementById('paidAmount');
        const remainingBalanceInput = document.getElementById('remainingBalance');
        const eventTypeSelect = document.getElementById('eventType');
        const paymentTypeSelect = document.getElementById('paymentType');
        const paymentMethodSelect = document.getElementById('paymentMethod');

        function updateTotal() {
            let basePrice = parseFloat(document.getElementById('bookingPackagePrice').textContent.replace('₱', '').replace(/,/g, '')) || 0;
            basePrice = parseFloat(basePrice.toFixed(2));

            const additionalGuests = parseInt(additionalGuestsInput.value) || 0;
            let pricePerAdditional = parseFloat(pricePerAdditionalInput.value) || 0;
            pricePerAdditional = parseFloat(pricePerAdditional.toFixed(2));

            document.getElementById('bookingAdditionalGuests').textContent = additionalGuests;
            document.getElementById('bookingAdditionalGuestPrice').textContent = '₱' + pricePerAdditional.toLocaleString();

            const additionalTotal = pricePerAdditional * additionalGuests;
            const total = parseFloat((basePrice + additionalTotal).toFixed(2));
            document.getElementById('bookingTotalPrice').textContent = `₱${total.toLocaleString()}`;

            updatePaidAmountByPaymentType();
        }

        function updateRemainingBalance() {
            const totalAmount = parseFloat(document.getElementById('bookingTotalPrice').textContent.replace('₱', '').replace(/,/g, '')) || 0;
            const paidAmount = parseFloat(paidAmountInput.value) || 0;
            const remaining = totalAmount - paidAmount;
            remainingBalanceInput.value = remaining >= 0 ? remaining.toFixed(2) : 0;
        }

        function updatePaidAmountByPaymentType() {
            const totalAmount = parseFloat(document.getElementById('bookingTotalPrice').textContent.replace('₱', '').replace(/,/g, '')) || 0;
            const type = paymentTypeSelect.value;

            if (type === 'Full') {
                paidAmountInput.value = totalAmount.toFixed(2);
                paidAmountInput.readOnly = true;
            } else if (type === 'Half') {
                paidAmountInput.value = (totalAmount / 2).toFixed(2);
                paidAmountInput.readOnly = true;
            } else {
                paidAmountInput.value = 0;
                paidAmountInput.readOnly = false;
            }
            updateRemainingBalance();
        }


        /* CHECK AVAILABILITY */
        checkBtn.addEventListener('click', () => {
            const datetime = bookingDateInput.value;
            if (!datetime) {
                CasaEstelaAlert.show('warning', 'Select Date & Time', 'Please select date and time before checking availability.');
                return;
            }

            fetch('../Admin/adminBackend/table_checkOnOrders_availability.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'datetime=' + encodeURIComponent(datetime)
            })
                .then(res => res.text())
                .then(text => { if (!text) throw new Error('Empty response'); return JSON.parse(text); })
                .then(data => {
                    availabilityChecked = true;
                    availabilityMessage.classList.remove('d-none');

                    if (data.conflict && data.places) {
                        hasConflict = true;
                        availabilityMessage.className = 'alert alert-danger';

                        let html = '<div style="font-family: sans-serif; border: 1px solid #ffcccc; border-radius: 8px; overflow: hidden;">';
                        html += '<div style="background: #fff5f5; color: #c53030; padding: 12px; font-weight: bold; border-bottom: 1px solid #ffcccc;">';
                        html += '🚫 The following places are not available: ' +
                            data.places.map(p => p.charAt(0).toUpperCase() + p.slice(1)).join(', ') +
                            '</div>';
                        html += '<div style="padding: 15px; background: white;">';

                        data.bookings.forEach(b => {
                            html += `<div style="margin-bottom: 10px; color: #4a5568;">
                                        ${b.place.charAt(0).toUpperCase() + b.place.slice(1)} Booking:
                                        <strong>${b.booked_time}</strong>
                                    </div>`;
                        });

                        html += '</div></div>';
                        availabilityMessage.innerHTML = html;

                        bookNowButtons.forEach(btn => {
                            if (data.places.includes(btn.dataset.place)) {
                                btn.disabled = true;
                                btn.classList.add('disabled');
                                btn.textContent = 'Not Available';
                            } else {
                                btn.disabled = false;
                                btn.classList.remove('disabled');
                                btn.textContent = 'Book Now';
                            }
                        });

                    } else if (data.conflict) {
                        hasConflict = true;
                        availabilityMessage.className = 'alert alert-danger';

                        availabilityMessage.innerHTML = `
                            <strong>🚫 Café Not Available</strong><br>
                            ${data.message}<br><br>
                            <small>
                                Available before: <b>${data.available_before}</b><br>
                                Available after: <b>${data.available_after}</b>
                            </small>
                        `;

                        bookNowButtons.forEach(btn => {
                            if (btn.dataset.place === 'cafe') {
                                btn.disabled = true;
                                btn.classList.add('disabled');
                                btn.textContent = 'Not Available';
                            } else {
                                btn.disabled = false;
                                btn.classList.remove('disabled');
                                btn.textContent = 'Book Now';
                            }
                        });

                    } else {
                        hasConflict = false;
                        availabilityMessage.className = 'alert alert-success';
                        availabilityMessage.textContent = data.message;

                        bookNowButtons.forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('disabled');
                            btn.textContent = 'Book Now';
                        });
                    }

                });
        });

        /* BOOK NOW */
        bookNowButtons.forEach(btn => {
            btn.addEventListener('click', e => {
                const dateTimeVal = bookingDateInput.value;

                if (btn.disabled) {
                    return; // silently ignore
                }


                if (!dateTimeVal) {
                    CasaEstelaAlert.show('warning', 'Select Date & Time', 'Please select a booking date and time first.');
                    return;
                }

                if (!availabilityChecked) {
                    CasaEstelaAlert.show('warning', 'Check Availability', 'Please click "Check Availability" before booking.');
                    return;
                }

                selectedPackageId = btn.dataset.packageId;
                selectedPlace = btn.dataset.place;
                const place = btn.dataset.place;
                document.getElementById('bookingPlaceEvent').textContent =
                    place.charAt(0).toUpperCase() + place.slice(1);



                bookingMaxGuestsValue = parseInt(btn.dataset.packageMaxGuests);

                guestCountInput.max = bookingMaxGuestsValue;

                document.getElementById('bookingPackageName').textContent = btn.dataset.packageName;
                document.getElementById('bookingPackagePrice').textContent = '₱' + btn.dataset.packagePrice;
                document.getElementById('bookingTotalPrice').textContent = '₱' + btn.dataset.packagePrice;
                document.getElementById('bookingMaxGuests').textContent = btn.dataset.packageMaxGuests;

                additionalGuestsInput.value = 0;
                pricePerAdditionalInput.value = 1200;
                paidAmountInput.value = 0;
                paidAmountInput.readOnly = false;
                remainingBalanceInput.value = btn.dataset.packagePrice;
                eventTypeSelect.value = '';
                paymentTypeSelect.value = '';
                paymentMethodSelect.value = '';

                updateTotal();

                const [date, time] = dateTimeVal.split('T');
                document.getElementById('eventDate').value = date;
                document.getElementById('eventTime').value = time;

                bookingModal.show();
            });
        });

        guestCountInput.addEventListener('input', () => {
            const value = parseInt(guestCountInput.value) || 0;

            if (value > bookingMaxGuestsValue) {
                guestCountInput.value = bookingMaxGuestsValue;
                CasaEstelaAlert.show('error', 'Guest Limit Exceeded', `Guest count cannot exceed ${bookingMaxGuestsValue}.`);
            }
        });
        guestCountInput.addEventListener('input', updateTotal);
        additionalGuestsInput.addEventListener('input', updateTotal);
        pricePerAdditionalInput.addEventListener('input', updateTotal);
        paidAmountInput.addEventListener('input', updateRemainingBalance);
        paymentTypeSelect.addEventListener('change', updatePaidAmountByPaymentType);

        /* RESET ON DATE CHANGE */
        bookingDateInput.addEventListener('change', () => {
            availabilityChecked = false;
            hasConflict = false;
            availabilityMessage.classList.add('d-none');

            bookNowButtons.forEach(btn => {
                btn.style.display = 'inline-block';
                btn.disabled = false;
                btn.classList.remove('disabled');
                btn.textContent = 'Book Now';
            });

            if (!bookingDateInput.value) return;

        });


        /* FINAL SUBMIT */
        document.getElementById('bookingForm').addEventListener('submit', e => {
            e.preventDefault();
            if (!selectedPackageId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Package',
                    text: 'Please select a package before booking.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const customerNameVal = document.getElementById('customerName').value.trim();
            const customerEmailVal = document.getElementById('customerEmail').value.trim();
            const customerPhoneVal = document.getElementById('customerPhone').value.trim();
            const guestCountVal = parseInt(guestCountInput.value);

            if (guestCountVal > bookingMaxGuestsValue) {
                Swal.fire({
                    icon: 'error',
                    title: 'Guest Limit Exceeded',
                    text: `Guest count cannot exceed ${bookingMaxGuestsValue}.`,
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const eventDateVal = document.getElementById('eventDate').value;
            const eventTimeVal = document.getElementById('eventTime').value;
            const eventTypeVal = eventTypeSelect.value;
            const paymentTypeVal = paymentTypeSelect.value;
            const paymentMethodVal = paymentMethodSelect.value;
            const totalAmount = parseFloat(document.getElementById('bookingTotalPrice').textContent.replace('₱', '').replace(/,/g, '')) || 0;

            if (!customerNameVal || !customerEmailVal || !customerPhoneVal || !guestCountVal || !eventDateVal || !eventTimeVal || !eventTypeVal || !paymentTypeVal || !paymentMethodVal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Form',
                    text: 'Please fill in all required fields.',
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'OK'
                });
                return;
            }

            CasaEstelaModal.confirm(
                'Confirm Booking?',
                `<p>Package: <strong>${document.getElementById('bookingPackageName').textContent}</strong></p>
                <p>Total: <strong>${document.getElementById('bookingTotalPrice').textContent}</strong></p>`,
                () => {
                    const dateTimeStart = new Date(`${eventDateVal}T${eventTimeVal}:00`);
                    const dateTimeEnd = new Date(dateTimeStart.getTime() + 4 * 60 * 60 * 1000);
                    const formatDateTime = dt => dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0') + '-' + String(dt.getDate()).padStart(2, '0') + ' ' + String(dt.getHours()).padStart(2, '0') + ':' + String(dt.getMinutes()).padStart(2, '0') + ':' + String(dt.getSeconds()).padStart(2, '0');

                    const formData = new URLSearchParams({
                        name: customerNameVal,
                        email: customerEmailVal,
                        phone: customerPhoneVal,
                        guests: guestCountVal,
                        additional_guests: parseInt(additionalGuestsInput.value) || 0,
                        price_per_additional: parseFloat(pricePerAdditionalInput.value) || 0,
                        total_amount: totalAmount,
                        paid_amount: parseFloat(paidAmountInput.value) || 0,
                        remaining_balance: parseFloat(remainingBalanceInput.value) || 0,
                        event_type: eventTypeVal,
                        payment_type: paymentTypeVal,
                        payment_method: paymentMethodVal,
                        package_id: selectedPackageId,
                        place: selectedPlace,
                        date_time_start: formatDateTime(dateTimeStart),
                        date_time_end: formatDateTime(dateTimeEnd)
                    });

                    fetch('../Admin/adminBackend/bookEvent_insert.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.text())
                        .then(response => {
                            if (response === 'CONFLICT') {
                                CasaEstelaModal.show('error', 'Booking Failed', 'Time slot already taken.');
                            } else if (response === 'SUCCESS') {
                                CasaEstelaModal.show(
                                    'success',
                                    'Booking Confirmed',
                                    `Your booking has been successfully made.<br>
                                        Package: <strong>${document.getElementById('bookingPackageName').textContent}</strong><br>
                                        Total: <strong>${document.getElementById('bookingTotalPrice').textContent}</strong>`,
                                    () => location.reload()
                                );
                            } else {
                                CasaEstelaModal.show('error', 'Booking Failed', 'Something went wrong. Check console.');
                                console.error(response);
                            }
                        });

                }
            );

        });

    </script>

    <script>
        const filterButtons = document.querySelectorAll('.filter-btn');
        const eventCards = document.querySelectorAll('.event-card');

        function applyFilter(filter) {
            eventCards.forEach(card => {
                const col = card.closest('.col-lg-4');
                if (card.dataset.place === filter) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyFilter('cafe');
        });

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;
                applyFilter(filter);
            });
        });
    </script>

    <script>
        document.querySelectorAll('.event-card').forEach(card => {
            const mainImage = card.querySelector('.main-event-image');
            const thumbnails = card.querySelectorAll('.thumbnail-img');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    mainImage.src = thumb.src;

                    thumbnails.forEach(t => t.classList.remove('active'));

                    thumb.classList.add('active');
                });
            });
        });
    </script>


    <script>
        // ---------------- CASA ESTELA ALERT SYSTEM ----------------
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

        // ---------------- CASA ESTELA MODAL SYSTEM ----------------
        const CasaEstelaModal = {
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
                if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') {
                    btn.ceConfirmCallback();
                }
                this.close(btn);
            },

            handleCancel: function (btn) {
                if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') {
                    btn.ceCancelCallback();
                }
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

        CasaEstelaModal.show = function (type, title, message, onClose = null) {
            const overlay = document.createElement('div');
            overlay.className = 'cea-modal-overlay';
            overlay.innerHTML = `
                <div class="cea-modal-dialog cea-modal-${type}">
                    <div class="cea-modal-body">
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            <button class="cea-modal-button cea-modal-button-primary">OK</button>
                        </div>
                    </div>
                </div>
            `;
            const okBtn = overlay.querySelector('.cea-modal-button-primary');
            okBtn.addEventListener('click', () => {
                CasaEstelaModal.close(overlay);
                if (onClose) onClose();
            });
            document.body.appendChild(overlay);
        };

    </script>


</body>

</html>