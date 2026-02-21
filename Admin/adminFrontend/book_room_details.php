<?php
if (!isset($_SESSION['user_type']) || 
    ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'frontdesk')) {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';
$booking_id = $_GET['id'];

$booking_sql = "SELECT * FROM bookings WHERE booking_id = $booking_id";
$booking = $conn->query($booking_sql)->fetch_assoc();

$rooms_sql = "
    SELECT *
    FROM booked_rooms
    WHERE booking_id = $booking_id
";
$rooms = $conn->query($rooms_sql);

$guests_sql = "
    SELECT first_name, last_name, guest_type
    FROM guest_names
    WHERE booking_id = $booking_id
";
$guests = $conn->query($guests_sql);


$occupied_sql = "
    SELECT br.room_type_id
    FROM booked_rooms br
    JOIN bookings b ON br.booking_id = b.booking_id
    WHERE b.status NOT IN ('finished','rejected','uncounted')
      AND b.booking_id != $booking_id
      AND (
            (b.check_in <= '{$booking['check_out']}' AND b.check_out >= '{$booking['check_in']}')
          )
";
$occupied_res = $conn->query($occupied_sql);
$occupied_rooms = [];
while ($row = $occupied_res->fetch_assoc()) {
    $occupied_rooms[] = $row['room_type_id'];
}

?>

<?php
$beds = [];
$bed_sql = "SELECT * FROM beds";
$bed_res = $conn->query($bed_sql);
while ($b = $bed_res->fetch_assoc()) {
    $beds[] = $b;
}
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/book_room_details.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"></i>
            <span>Pending Bookings</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 fw-bold" style="color: var(--dark-bg);">
                <i class="fas fa-file-alt" style="color: var(--gold);"></i> Booking Details
            </h3>
            <a href="#" class="btn btn-danger" onclick="cancelBooking(<?= $booking['booking_id'] ?>)">

                <i class="fas fa-times-circle"></i> Cancel Booking
            </a>
        </div>

        <div class="container-fluid">
            <!-- Booking Information Section -->
            <div class="section-card mb-4">
                <h4 class="section-header">
                    <i class="fas fa-info-circle"></i> Booking Information
                </h4>

                <div class="info-grid">
                    <div class="info-item">
                        <label><i class="fas fa-receipt"></i> Booking Reference</label>
                        <input class="form-control" value="<?= $booking['booking_reference'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input class="form-control" value="<?= $booking['first_name'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input class="form-control" value="<?= $booking['last_name'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-phone-alt"></i> Contact Number</label>
                        <input class="form-control" value="<?= $booking['contact'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-users"></i> Number of Guests</label>
                        <input class="form-control" id="numberGuest" value="<?= $booking['number_of_guests'] ?>"
                            readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-door-open"></i> Room Quantity</label>
                        <input class="form-control" value="<?= $booking['room_quantity'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-male"></i> Adults</label>
                        <input class="form-control" value="<?= $booking['num_adults'] ?>" readonly>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-child"></i> Children</label>
                        <input class="form-control" value="<?= $booking['num_children'] ?>" readonly>
                    </div>
                </div>

                <?php
                $amenitiesDisplay = "None";
                $bookingId = $booking['booking_id'];

                $amenities_sql = "SELECT * FROM booking_amenities WHERE booking_fk_id = ?";
                $stmt = $conn->prepare($amenities_sql);
                $stmt->bind_param("i", $bookingId);
                $stmt->execute();
                $res = $stmt->get_result();

                $extraBedTotal = 0;

                if ($res->num_rows > 0) {
                    $items = [];
                    while ($row = $res->fetch_assoc()) {
                        $priceDisplay = ($row['price'] > 0) ? " (₱" . number_format($row['price'], 2) . ")" : "";
                        $items[] = $row['amenity_name'] . " x" . $row['quantity'] . $priceDisplay;
                        $extraBedTotal += $row['quantity'] * $row['price'];
                    }
                    $amenitiesDisplay = implode("\n", $items);
                }
                ?>

                <?php if ($amenitiesDisplay !== "None"): ?>
                    <div class="amenities-section mt-3">
                        <label><i class="fas fa-concierge-bell"></i> Booked Amenities</label>
                        <textarea class="form-control" rows="4"
                            readonly><?= htmlspecialchars($amenitiesDisplay) ?></textarea>
                    </div>
                <?php endif; ?>

                <script>
                    const extraBedTotal = <?= $extraBedTotal ?>;
                </script>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label><i class="fas fa-calendar-check"></i> Check-in Date</label>
                        <input type="date" id="check_in" class="form-control"
                            value="<?= date('Y-m-d', strtotime($booking['check_in'])) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><i class="fas fa-calendar-times"></i> Check-out Date</label>
                        <input type="date" id="check_out" class="form-control"
                            value="<?= date('Y-m-d', strtotime($booking['check_out'])) ?>">
                    </div>
                </div>
            </div>

            <?php
            $roomNumbers = [];
            $rn_sql = "SELECT * FROM room_numbers WHERE status='active'";
            $rn_res = $conn->query($rn_sql);
            while ($rn = $rn_res->fetch_assoc()) {
                $roomNumbers[$rn['room_type_id']][] = $rn;
            }

            $roomTypes = [];
            $rt_sql = "SELECT * FROM room_types";
            $rt_res = $conn->query($rt_sql);
            while ($rt = $rt_res->fetch_assoc()) {
                if (isset($roomNumbers[$rt['room_type_id']]) && count($roomNumbers[$rt['room_type_id']]) > 0) {
                    $roomTypes[$rt['room_type_id']] = $rt;
                }
            }
            ?>

            <!-- Booked Rooms Section -->
            <div class="section-card mb-4">
                <h4 class="section-header">
                    <i class="fas fa-bed"></i> Booked Rooms
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="roomsTable">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-door-closed"></i> Room Type</th>
                                <th><i class="fas fa-hashtag"></i> Room Number</th>
                                <th><i class="fas fa-tag"></i> Price</th>
                                <th><i class="fas fa-users"></i> Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $rooms->fetch_assoc()): ?>
                                <?php $bookedRoomTypeId = $r['room_type_id']; ?>
                                <tr data-booked-room-id="<?= $r['id'] ?>" data-default-room-type="<?= $bookedRoomTypeId ?>"
                                    data-default-room-number="<?= $r['room_number_fk_id'] ?>">
                                    <td>
                                        <select class="form-select roomTypeSelect">
                                            <?php foreach ($roomTypes as $rtid => $rt): ?>
                                                <option value="<?= $rtid ?>" data-price="<?= $rt['price'] ?>"
                                                    data-capacity="<?= $rt['capacity'] ?>" <?= ($rtid == $bookedRoomTypeId) ? 'selected' : '' ?>>
                                                    <?= $rt['room_type'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select class="form-select roomNumberSelect">
                                            <?php
                                            if ($r['room_number_fk_id'] && isset($roomNumbers[$bookedRoomTypeId])):
                                                foreach ($roomNumbers[$bookedRoomTypeId] as $rn):
                                                    $selectedRN = ($rn['room_number_id'] == $r['room_number_fk_id']) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= $rn['room_number_id'] ?>" <?= $selectedRN ?>>
                                                        <?= $rn['room_number'] ?>
                                                    </option>
                                                <?php endforeach;
                                            else: ?>
                                                <option value="" selected>Select room number</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>

                                    <td class="roomPrice fw-semibold text-success">
                                        ₱<?= number_format($roomTypes[$bookedRoomTypeId]['price'], 2) ?></td>
                                    <td class="roomCapacity"><span
                                            class="badge bg-secondary"><?= $roomTypes[$bookedRoomTypeId]['capacity'] ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Guest Names Section -->
            <div class="section-card mb-4">
                <h4 class="section-header">
                    <i class="fas fa-address-book"></i> Guest List
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-id-card"></i> First Name</th>
                                <th><i class="fas fa-id-card"></i> Last Name</th>
                                <th><i class="fas fa-user-tag"></i> Guest Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($g = $guests->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $g['first_name'] ?></td>
                                    <td><?= $g['last_name'] ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="fas fa-user"></i> <?= ucfirst($g['guest_type']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="section-card mb-4">
                <h4 class="section-header">
                    <i class="fas fa-file-invoice-dollar"></i> Payment Details
                </h4>

                <div class="info-grid">
                    <div class="info-item">
                        <label><i class="fas fa-credit-card"></i> Payment Method</label>
                        <select name="payment_method" class="form-control custom-input" required>
                            <option value="Cash" <?= ($booking['payment_method'] == 'Cash') ? 'selected' : '' ?>>Cash
                            </option>
                            <option value="Credit Card" <?= ($booking['payment_method'] == 'Credit Card') ? 'selected' : '' ?>>Credit Card</option>
                            <option value="Debit Card" <?= ($booking['payment_method'] == 'Debit Card') ? 'selected' : '' ?>>Debit Card</option>
                            <option value="GCash" <?= ($booking['payment_method'] == 'GCash') ? 'selected' : '' ?>>GCash
                            </option>
                            <option value="Paypal" <?= ($booking['payment_method'] == 'Paypal') ? 'selected' : '' ?>>Paypal
                            </option>
                        </select>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-percent"></i> Discount Applied</label>
                        <input class="form-control" id="discountPercentage"
                            value="<?= (int) $booking['discount_percentage'] ?>%" readonly>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-money-bill-wave"></i> Discount Amount</label>
                        <input class="form-control" id="discount_amount" value="<?= $booking['discount_amount'] ?>"
                            readonly>
                    </div>
                </div>

                <div class="payment-summary mt-4">
                    <div class="info-grid">
                        <div class="info-item">
                            <label><i class="fas fa-calculator"></i> Total Amount</label>
                            <input type="text" id="totalAmountNew" class="form-control fw-bold"
                                value="<?= number_format($booking['total_amount'], 2) ?>" readonly>
                        </div>

                        <div class="info-item">
                            <label><i class="fas fa-hand-holding-usd"></i> Down Payment</label>
                            <input type="text" id="downPayment" class="form-control"
                                value="<?= number_format($booking['downpayment_amount'], 2) ?>" readonly>
                        </div>

                        <div class="info-item">
                            <label><i class="fas fa-file-invoice-dollar"></i> Remaining Balance</label>
                            <input type="text" id="remainingBal" class="form-control fw-bold text-danger-emphasis"
                                value="<?= number_format($booking['remaining_balance'], 2) ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="payment-card">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><i class="fas fa-wallet"></i> Payment Amount / Downpayment</label>
                            <input type="number" id="paymentInput" class="form-control" min="0"
                                placeholder="Enter payment amount" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><i class="fas fa-coins"></i> Change</label>
                            <input type="text" id="changeAmount" class="form-control" value="₱0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="button" id="processReserveBtn" class="btn btn-warning">
                    <i class="fas fa-bookmark"></i> Reserve Booking
                </button>
                <button type="button" id="processCheckinBtn" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Check In Guest
                </button>
            </div>
        </div>
    </div>

</div>


<script>
    function disableSelectedRoomNumbers() {
        const rows = document.querySelectorAll('#roomsTable tbody tr');
        const selectedRoomNumbers = new Set();

        rows.forEach(row => {
            const select = row.querySelector('.roomNumberSelect');
            if (select.value) selectedRoomNumbers.add(select.value);
        });

        rows.forEach(row => {
            const select = row.querySelector('.roomNumberSelect');
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                option.disabled = option.value && option.value !== currentValue && selectedRoomNumbers.has(option.value);
            });
        });
    }

    function updateRoomNumbers(row) {
        const roomTypeSelect = row.querySelector('.roomTypeSelect');
        const roomNumberSelect = row.querySelector('.roomNumberSelect');
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const bookingId = <?= $booking_id ?>;
        const defaultNumber = row.dataset.defaultRoomNumber;

        fetch(`../Admin/adminBackend/check_availability_rooms.php?check_in=${checkIn}&check_out=${checkOut}&booking_id=${bookingId}`)
            .then(res => res.json())
            .then(data => {
                roomNumberSelect.innerHTML = '';
                if (data[roomTypeSelect.value] && data[roomTypeSelect.value].length > 0) {

                    // 🔹 ALWAYS add placeholder
                    const placeholder = document.createElement('option');
                    placeholder.value = "";
                    placeholder.textContent = "Please choose a room number";
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    roomNumberSelect.appendChild(placeholder);

                    data[roomTypeSelect.value].forEach(rn => {
                        const option = document.createElement('option');
                        option.value = rn.room_number_id;
                        option.textContent = rn.room_number + (rn.note ? ` (${rn.note})` : '');

                        if (defaultNumber && rn.room_number_id == defaultNumber) {
                            option.selected = true;
                            placeholder.selected = false; // unselect placeholder
                        }

                        roomNumberSelect.appendChild(option);
                    });

                }
                else {
                    const option = document.createElement('option');
                    option.textContent = 'No available rooms';
                    option.disabled = true;
                    option.selected = true;
                    roomNumberSelect.appendChild(option);
                }

                disableSelectedRoomNumbers();
            })
            .catch(err => {
                console.error('Error loading rooms:', err);
                roomNumberSelect.innerHTML = '<option disabled selected>Error loading rooms</option>';
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => updateRoomNumbers(row));

        document.querySelectorAll('.roomNumberSelect').forEach(select => {
            select.addEventListener('change', () => disableSelectedRoomNumbers());
        });

        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.addEventListener('change', function () {
                const row = this.closest('tr');
                updateRoomNumbers(row);
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => updateRoomNumbers(row));

        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.addEventListener('change', function () {
                const row = this.closest('tr');

                updateRoomNumbers(row);

                const selectedOption = this.selectedOptions[0];
                const priceCell = row.querySelector('.roomPrice');
                const capacityCell = row.querySelector('.roomCapacity');

                priceCell.textContent = `₱${parseFloat(selectedOption.dataset.price).toFixed(2)}`;
                capacityCell.textContent = selectedOption.dataset.capacity;

                checkCapacity();
            });
        });
    });

    function checkCapacity() {
        const numberGuest = parseInt(document.getElementById('numberGuest').value);
        let totalCapacity = 0;

        const rows = document.querySelectorAll('#roomsTable tbody tr');

        rows.forEach(row => {
            const selectedOption = row.querySelector('.roomTypeSelect').selectedOptions[0];
            totalCapacity += parseInt(selectedOption.dataset.capacity);
        });

        if (totalCapacity < numberGuest) {
            CasaEstelaAlert.show(
                'warning',
                'Capacity Mismatch',
                'Total room capacity is less than the number of guests.'
            );


            rows.forEach(row => {
                const roomTypeSelect = row.querySelector('.roomTypeSelect');
                const defaultType = row.dataset.defaultRoomType;
                const defaultNumber = row.dataset.defaultRoomNumber;

                roomTypeSelect.value = defaultType;

                const selectedOption = roomTypeSelect.selectedOptions[0];
                row.querySelector('.roomPrice').textContent = `₱${parseFloat(selectedOption.dataset.price).toFixed(2)}`;
                row.querySelector('.roomCapacity').textContent = selectedOption.dataset.capacity;

                updateRoomNumbers(row);

                const roomNumberSelect = row.querySelector('.roomNumberSelect');
                if (defaultNumber) roomNumberSelect.value = defaultNumber;
            });

            return false;
        }

        return true;
    }

    document.querySelectorAll('.roomTypeSelect').forEach(select => {
        select.addEventListener('change', function () {
            const row = this.closest('tr');

            updateRoomNumbers(row);

            const selectedOption = this.selectedOptions[0];
            const priceCell = row.querySelector('.roomPrice');
            const capacityCell = row.querySelector('.roomCapacity');

            priceCell.textContent = `₱${parseFloat(selectedOption.dataset.price).toFixed(2)}`;
            capacityCell.textContent = selectedOption.dataset.capacity;

            checkCapacity();
        });
    });

    document.getElementById('check_in').addEventListener('change', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => updateRoomNumbers(row));
    });
    document.getElementById('check_out').addEventListener('change', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => updateRoomNumbers(row));
    });

    function calculateTotalAmount() {
        const checkIn = new Date(document.getElementById('check_in').value);
        const checkOut = new Date(document.getElementById('check_out').value);

        if (isNaN(checkIn) || isNaN(checkOut) || checkOut <= checkIn) return;

        const timeDiff = checkOut - checkIn;
        const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

        let roomsTotal = 0;
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            const selectedOption = row.querySelector('.roomTypeSelect').selectedOptions[0];
            roomsTotal += parseFloat(selectedOption.dataset.price) * nights; // multiply by nights
        });

        let extraBedPrice = extraBedTotal * nights; // multiply by nights if applicable

        let totalBeforeDiscount = roomsTotal + extraBedPrice;

        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value.replace('%', '')) || 0;
        const discountAmount = (discountPercentage / 100) * totalBeforeDiscount;

        const totalAmountNew = totalBeforeDiscount - discountAmount;
        document.getElementById('totalAmountNew').value = totalAmountNew.toFixed(2);

        const downPayment = parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;
        let remainingBalance = totalAmountNew - downPayment;
        if (remainingBalance < 0) remainingBalance = 0;
        document.getElementById('remainingBal').value = remainingBalance.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateTotalAmount();

        document.getElementById('check_in').addEventListener('change', calculateTotalAmount);
        document.getElementById('check_out').addEventListener('change', calculateTotalAmount);

        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.addEventListener('change', calculateTotalAmount);
        });
    });

    document.getElementById('paymentInput').addEventListener('input', function () {

        const payment = parseFloat(this.value) || 0;
        const totalAmountNew = parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')) || 0;

        let change = payment - totalAmountNew;

        document.getElementById('changeAmount').value = "₱" + change.toFixed(2);
    });

</script>
<script>
    function processBooking(status) {

        // ---------- VALIDATIONS ----------
        const roomSelects = document.querySelectorAll('.roomNumberSelect');
        let allSelected = true;

        roomSelects.forEach(select => {
            const opt = select.selectedOptions[0];
            if (!opt || opt.value === "") {

                select.classList.add('is-invalid');
                allSelected = false;
            } else {
                select.classList.remove('is-invalid');
            }
        });

        if (!allSelected) {
            CasaEstelaAlert.show(
                'warning',
                'Room Selection Required',
                'Please select a room number for all booked rooms.'
            );
            return;
        }

        const paymentInput = document.getElementById('paymentInput');
        const paymentValue = parseFloat(paymentInput.value);

        if (!paymentValue || paymentValue <= 0) {
            CasaEstelaAlert.show(
                'warning',
                'Payment Required',
                'Please enter a downpayment amount before proceeding.'
            );
            paymentInput.focus();
            return;
        }

        // ---------- CASA ESTELA CONFIRMATION ----------
        CasaEstelaModal.confirm(
            "Casa Estela Confirmation",
            `Are you sure you want to ${status === 'checkin' ? 'check in' : 'reserve'
            } this booking?`,
            () => submitBooking(status)
        );
    }

    function submitBooking(status) {

        const totalAmountNew =
            parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')) || 0;

        const oldDownpayment =
            parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;

        const paymentInput =
            parseFloat(document.getElementById('paymentInput').value) || 0;

        let newDownPayment = oldDownpayment + paymentInput;
        let remainingBal = Math.max(totalAmountNew - newDownPayment, 0);

        if (newDownPayment > totalAmountNew) {
            newDownPayment = totalAmountNew;
        }

        const rooms = [];
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            rooms.push({
                id: row.dataset.bookedRoomId,
                room_type_id: parseInt(row.querySelector('.roomTypeSelect').value),
                room_number_fk_id: parseInt(row.querySelector('.roomNumberSelect').value)
            });
        });

        const bookingData = {
            booking_id: <?= $booking['booking_id'] ?>,
            check_in: document.getElementById('check_in').value,
            check_out: document.getElementById('check_out').value,
            total_amount: totalAmountNew,
            downpayment_amount: newDownPayment,
            remaining_balance: remainingBal,
            payment_input: paymentInput,
            payment_method: document.querySelector('[name="payment_method"]').value,
            rooms: rooms,
            status: status
        };

        fetch('../Admin/adminBackend/update_room_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.text())
            .then(res => {
                if (res === "success") {
                    window.location.href = "../Admin/index.php?room_booking";

                } else {
                    CasaEstelaAlert.show(
                        'error',
                        'Process Failed',
                        'Something went wrong. Please try again.'
                    );
                }
            })
            .catch(() => {
                CasaEstelaAlert.show(
                    'error',
                    'Network Error',
                    'Unable to connect to the server.'
                );
            });
    }

    document.getElementById('processCheckinBtn').addEventListener('click', () => processBooking('checkin'));
    document.getElementById('processReserveBtn').addEventListener('click', () => processBooking('reserved'));
</script>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        const today = new Date().toISOString().split('T')[0];

        checkIn.min = today;
        checkOut.min = today;

        checkIn.addEventListener("change", () => {
            checkOut.min = checkIn.value;

            if (checkOut.value < checkIn.value) {
                checkOut.value = checkIn.value;
            }
        });

        checkOut.addEventListener("change", () => {
            if (checkOut.value < checkIn.value) {
                CasaEstelaAlert.show(
                    'error',
                    'Invalid Date',
                    'Check-out cannot be earlier than check-in.'
                );

                checkOut.value = checkIn.value;
            }
        });
    });

</script>

<script>
    function cancelBooking(bookingId) {
        CasaEstelaModal.confirm(
            "Casa Estela Confirmation",
            "Are you sure you want to cancel this booking? This action cannot be undone.",
            () => {
                window.location.href =
                    `../Admin/adminBackend/delete_roomBook_cancelBtn.php?booking_id=${bookingId}`;
            }
        );
    }
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
</script>



<?php include 'adminFrontend/footer.php'; ?>