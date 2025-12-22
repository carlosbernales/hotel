<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Event Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/event_booking.css">
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
                <button class="filter-btn active" data-filter="all">All Event Packages</button>
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
                    <div class="event-card" data-status="<?= $status ?>">

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
                            </div>

                            <div class="event-notes">
                                <i class="fas fa-info-circle"></i>
                                <?= nl2br(htmlspecialchars($row['notes'])) ?>
                            </div>

                            <div class="event-actions">
                                <button class="btn-view-menu" data-bs-toggle="modal"
                                    data-bs-target="#menuModal<?= $row['id'] ?>">
                                    <i class="fas fa-utensils"></i> View Menu
                                </button>

                                <?php if ($status === 'available'): ?>
                                    <button class="btn-book-now" data-package-id="<?= $row['id'] ?>"
                                        data-package-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                        data-package-price="<?= number_format($row['price']) ?>">
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
                            <span>Price:</span>
                            <strong id="bookingPackagePrice">₱0</strong>
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

                        <button type="submit" class="btn-confirm-booking">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times-circle"></i> Cancel
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
    <script>
        let availabilityChecked = false;
        let hasConflict = false;
        let selectedPackageId = null;

        const checkBtn = document.getElementById('checkGlobalAvailabilityBtn');
        const bookingDateInput = document.getElementById('globalBookingDateTime');
        const bookNowButtons = document.querySelectorAll('.btn-book-now');
        const availabilityMessage = document.getElementById('availabilityMessage');
        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

        /* CHECK AVAILABILITY */
        checkBtn.addEventListener('click', () => {
            const datetime = bookingDateInput.value;

            if (!datetime) {
                alert('Please select date and time');
                return;
            }

            fetch('../Admin/adminBackend/table_checkOnOrders_availability.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'datetime=' + encodeURIComponent(datetime)
            })
                .then(res => res.text())
                .then(text => {
                    if (!text) throw new Error('Empty response');
                    return JSON.parse(text);
                })
                .then(data => {
                    availabilityChecked = true;
                    availabilityMessage.classList.remove('d-none');

                    if (data.conflict) {
                        hasConflict = true;
                        availabilityMessage.className = 'alert alert-danger';

                        availabilityMessage.innerHTML = `
                            <div style="font-family: sans-serif; border: 1px solid #ffcccc; border-radius: 8px; overflow: hidden;">
                                <div style="background: #fff5f5; color: #c53030; padding: 12px; font-weight: bold; border-bottom: 1px solid #ffcccc;">
                                    🚫 Not Available at this time
                                </div>
                                <div style="padding: 15px; background: white;">
                                    <div style="margin-bottom: 10px; color: #4a5568;">
                                        Existing Booking: <strong>${data.booked_time}</strong>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9em;">
                                        <div style="background: #f0fff4; color: #276749; padding: 8px; border-radius: 4px;">
                                            <small>Available Before</small><br>
                                            <strong>${data.available_before}</strong>
                                        </div>
                                        <div style="background: #f0fff4; color: #276749; padding: 8px; border-radius: 4px;">
                                            <small>Available After</small><br>
                                            <strong>${data.available_after}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        bookNowButtons.forEach(btn => btn.style.display = 'none');
                    } else {
                        hasConflict = false;
                        availabilityMessage.className = 'alert alert-success';
                        availabilityMessage.textContent = data.message;

                        bookNowButtons.forEach(btn => btn.style.display = 'inline-block');
                    }
                });
        });


        /* BOOK NOW */
        bookNowButtons.forEach(btn => {
            btn.addEventListener('click', e => {
                if (!availabilityChecked) {
                    e.preventDefault();
                    alert('Please check availability first');
                    return;
                }

                if (hasConflict) {
                    e.preventDefault();
                    alert('Selected time is unavailable');
                    return;
                }

                selectedPackageId = btn.dataset.packageId;

                document.getElementById('bookingPackageName').textContent = btn.dataset.packageName;
                document.getElementById('bookingPackagePrice').textContent = '₱' + btn.dataset.packagePrice;
                document.getElementById('bookingTotalPrice').textContent = '₱' + btn.dataset.packagePrice;

                const [date, time] = bookingDateInput.value.split('T');
                document.getElementById('eventDate').value = date;
                document.getElementById('eventTime').value = time;

                bookingModal.show();
            });
        });


        /* RESET ON DATE CHANGE */
        bookingDateInput.addEventListener('change', () => {
            availabilityChecked = false;
            hasConflict = false;
            availabilityMessage.classList.add('d-none');
            bookNowButtons.forEach(btn => btn.style.display = 'inline-block');
        });

        /* FINAL SUBMIT */
        document.getElementById('bookingForm').addEventListener('submit', e => {
            e.preventDefault();

            if (!selectedPackageId) {
                alert('Please select a package.');
                return;
            }

            const customerNameVal = document.getElementById('customerName').value.trim();
            const guestCountVal = parseInt(document.getElementById('guestCount').value);
            const eventDateVal = document.getElementById('eventDate').value;
            const eventTimeVal = document.getElementById('eventTime').value;

            if (!customerNameVal || !guestCountVal || !eventDateVal || !eventTimeVal) {
                alert('Please fill in all required fields.');
                return;
            }

            const dateTimeStart = new Date(`${eventDateVal}T${eventTimeVal}:00`);
            const dateTimeEnd = new Date(dateTimeStart.getTime() + 4 * 60 * 60 * 1000);

            const formatDateTime = dt => {
                return dt.getFullYear() + '-' +
                    String(dt.getMonth() + 1).padStart(2, '0') + '-' +
                    String(dt.getDate()).padStart(2, '0') + ' ' +
                    String(dt.getHours()).padStart(2, '0') + ':' +
                    String(dt.getMinutes()).padStart(2, '0') + ':' +
                    String(dt.getSeconds()).padStart(2, '0');
            };

            const formData = new URLSearchParams({
                name: customerNameVal,
                guests: guestCountVal,
                package_id: selectedPackageId,
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
                        alert('Booking failed. Time slot already taken.');
                    } else if (response === 'SUCCESS') {
                        alert('Booking successful!');
                        location.reload();
                    } else {
                        alert('Booking failed. Check console for details.');
                        console.error(response);
                    }
                });
        });
    </script>
</body>

</html>