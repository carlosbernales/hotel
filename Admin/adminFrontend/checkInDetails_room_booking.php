<style>
    :root {
        --gold: #C9A961;
        --dark-bg: #2C2C2C;
        --card-bg: #FFFFFF;
        --text-dark: #333333;
        --border-color: #E0E0E0;
        --success: #28A745;
        --warning: #FFC107;
        --danger: #DC3545;
        --secondary: #6C757D;
    }

    .main-content {
        background: #F5F5F5;
        min-height: 100vh;
        padding: 20px;
    }

    .breadcrumb-custom {
        background: var(--dark-bg);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .breadcrumb-custom i {
        color: var(--gold);
        margin-right: 8px;
    }

    .info-card {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .section-header {
        border-left: 4px solid var(--gold);
        padding-left: 15px;
        margin: 30px 0 20px 0;
        color: var(--dark-bg);
        font-weight: 600;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.2rem rgba(201, 169, 97, 0.25);
    }

    .form-control:read-only {
        background: #F8F9FA;
        color: #6C757D;
    }

    label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 6px;
        font-size: 14px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .table thead {
        background: var(--dark-bg);
        color: white;
    }

    .table thead th {
        border: none;
        padding: 15px;
        font-weight: 500;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-color: var(--border-color);
    }

    .table tbody tr:hover {
        background: #F8F9FA;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-warning {
        background: var(--warning);
        color: var(--dark-bg);
    }

    .btn-warning:hover {
        background: #E0A800;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #C82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-secondary {
        background: var(--secondary);
        color: white;
    }

    .btn-secondary:hover {
        background: #5A6268;
        transform: translateY(-2px);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 14px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .payment-card {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
    }

    .payment-card label {
        color: rgba(255, 255, 255, 0.9);
    }

    .payment-card .form-control {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
    }

    .payment-card .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid var(--border-color);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-info {
        background: #17A2B8;
        color: white;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
        }
    }
</style>

<?php
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
    WHERE b.status NOT IN ('finished','rejected', 'uncounted')
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
<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"></i>
            <span>Pending Bookings</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0 fw-bold" style="color: var(--dark-bg);">Booking Details</h3>
            <a href="../Admin/index.php?room_booking_list" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="container-fluid">
            <h4 class="section-header">Booking Information</h4>

            <div class="info-grid">

                <div class="info-item">
                    <label><i class="fas fa-receipt"></i> Booking ID</label>
                    <input class="form-control" value="<?= $booking['booking_reference'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-user"></i> Last Name</label>
                    <input class="form-control" value="<?= $booking['last_name'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-user"></i> First Name</label>
                    <input class="form-control" value="<?= $booking['first_name'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-phone-alt"></i> Contact Number</label>
                    <input class="form-control" value="<?= $booking['contact'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-users"></i> Number of Guests</label>
                    <input class="form-control" id="numberGuest" value="<?= $booking['number_of_guests'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-door-open"></i> Room Quantity</label>
                    <input class="form-control" value="<?= $booking['room_quantity'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-user"></i> Adults</label>
                    <input class="form-control" value="<?= $booking['num_adults'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-child"></i> Children</label>
                    <input class="form-control" value="<?= $booking['num_children'] ?>" readonly>
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
                        $displayPrice = (strtolower($row['bedOrNot'] ?? '') === 'yes') ? "₱" . number_format($row['price'], 2) : "";
                        $items[] = $row['amenity_name'] . " x" . $row['quantity'] . ($displayPrice ? " ($displayPrice)" : "");

                        if (strtolower($row['bedOrNot'] ?? '') === 'yes') {
                            $extraBedTotal += $row['quantity'] * $row['price'];
                        }
                    }

                    $amenitiesDisplay = implode("\n", $items);
                }
                ?>

                <?php if ($amenitiesDisplay !== "None"): ?>
                    <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($amenitiesDisplay) ?></textarea>
                <?php endif; ?>

                <script>
                    const extraBedTotal = <?= $extraBedTotal ?>;
                </script>

            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label><i class="fas fa-calendar-check"></i> Check-in</label>
                    <input type="date" id="check_in" class="form-control"
                        value="<?= date('Y-m-d', strtotime($booking['check_in'])) ?>" disabled>
                </div>

                <div class="col-md-6">
                    <label><i class="fas fa-calendar-times"></i> Check-out</label>
                    <input type="date" id="check_out" class="form-control"
                        value="<?= date('Y-m-d', strtotime($booking['check_out'])) ?>">
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
            <h4 class="section-header">Booked Rooms</h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="roomsTable">
                    <thead>
                        <tr>
                            <th>Room Type</th>
                            <th>Room Number</th>
                            <th>Price</th>
                            <th>Capacity</th>
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

                                <td class="roomPrice">₱<?= number_format($roomTypes[$bookedRoomTypeId]['price'], 2) ?></td>
                                <td class="roomCapacity"><?= $roomTypes[$bookedRoomTypeId]['capacity'] ?></td>
                            </tr>
                        <?php endwhile; ?>


                    </tbody>

                </table>
            </div>

            <h4 class="section-header">Guest Names</h4>
            <div class="table-responsive">
                <table class="table table-bordered mb-5">
                    <thead>
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
                                <td><span class="badge badge-info"><?= $g['guest_type'] ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>



            <h4 class="section-header">Payment Details</h4>

            <div class="info-grid">
                <div class="info-item">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-control custom-input" required>
                        <option value="Cash" <?= ($booking['payment_method'] == 'Cash') ? 'selected' : '' ?>>Cash</option>
                        <option value="Credit Card" <?= ($booking['payment_method'] == 'Credit Card') ? 'selected' : '' ?>>
                            Credit Card</option>
                        <option value="Debit Card" <?= ($booking['payment_method'] == 'Debit Card') ? 'selected' : '' ?>>
                            Debit Card</option>
                        <option value="GCash" <?= ($booking['payment_method'] == 'GCash') ? 'selected' : '' ?>>GCash
                        </option>
                        <option value="Paypal" <?= ($booking['payment_method'] == 'Paypal') ? 'selected' : '' ?>>Paypal
                        </option>
                    </select>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-percent"></i> Discount</label>
                    <input class="form-control" id="discountPercentage"
                        value="<?= (int) $booking['discount_percentage'] ?>%" readonly>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-money-bill-wave"></i> Amount Deducted</label>
                    <input class="form-control" id="discount_amount" value="<?= $booking['discount_amount'] ?>"
                        readonly>
                </div>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-calculator"></i> Total Amount</label>
                    <input type="text" id="totalAmountNew" class="form-control"
                        value="<?= number_format($booking['total_amount'], 2) ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-hand-holding-usd"></i> Down Payment</label>
                    <input type="text" id="downPayment" class="form-control"
                        value="<?= number_format($booking['downpayment_amount'], 2) ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-file-invoice-dollar"></i> Total Due</label>
                    <input type="text" id="totalDue" class="form-control" value="0" readonly>
                </div>
            </div>

            <div class="payment-card">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><i class="fas fa-credit-card"></i> Payment Amount / Downpayment</label>
                        <input type="number" id="paymentInput" class="form-control" min="0"
                            placeholder="Enter payment amount">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><i class="fas fa-coins"></i> Change</label>
                        <input type="text" id="changeAmount" class="form-control" value="0" readonly>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" id="processCheckOut" class="btn btn-warning">
                    <i class="fas fa-check-circle"></i> Check Out
                </button>
                <button type="button" id="processExtendStay" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Extend Stay
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function processBooking(status) {
        const roomSelects = document.querySelectorAll('.roomNumberSelect');
        let allSelected = true;
        let hasUnavailable = false;

        roomSelects.forEach(select => {
            const selectedOption = select.selectedOptions[0];

            if (
                !selectedOption ||
                selectedOption.value === "" ||
                selectedOption.text.includes("Select room number") ||
                selectedOption.text.includes("Please choose a room number") ||
                selectedOption.text.includes("No available rooms")
            ) {
                select.classList.add('is-invalid');
                allSelected = false;
            } else {
                select.classList.remove('is-invalid');

                if (status === 'checkin' && selectedOption.text.includes("(Unavailable)")) {
                    hasUnavailable = true;
                }
            }
        });

        if (!allSelected) {
            alert('Please select a room number for all booked rooms.');
            return;
        }

        if (hasUnavailable) {
            alert('One or more rooms are unavailable at the selected dates. Cannot extend stay.');
            return;
        }

        let overrideCheckout = null;
        let finalStatus = status;

        if (status === 'finished') {
            overrideCheckout = new Date().toLocaleString("sv-SE", { timeZone: "Asia/Manila" }).replace('T', ' ');
            finalStatus = 'finished';
        }

        const confirmed = confirm(`Are you sure you want to ${finalStatus === 'finished' ? 'check out' : 'extend stay'} this booking?`);
        if (!confirmed) return;

        const rooms = [];
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            rooms.push({
                id: row.dataset.bookedRoomId,
                room_type_id: parseInt(row.querySelector('.roomTypeSelect').value),
                room_number_fk_id: parseInt(row.querySelector('.roomNumberSelect').value)
            });
        });

        let paymentInput = parseFloat(document.getElementById('paymentInput').value) || 0;
        let totalAmount = parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, ''));

        let downpaymentAmount, remainingBalance;

        if (paymentInput >= totalAmount) {
            downpaymentAmount = totalAmount;
            remainingBalance = 0;
        } else {
            downpaymentAmount = paymentInput;
            remainingBalance = totalAmount - paymentInput;
        }


        const bookingData = {
            booking_id: <?= $booking['booking_id'] ?>,
            check_in: '<?= $booking['check_in'] ?>',
            check_out: overrideCheckout ?? document.getElementById('check_out').value,
            total_amount: totalAmount,
            payment_input: paymentInput,
            downpayment_amount: downpaymentAmount,
            remaining_balance: remainingBalance,
            payment_method: document.querySelector('select[name="payment_method"]').value,
            rooms: rooms,
            status: finalStatus
        };


        fetch('../Admin/adminBackend/update_extendeOrCheckoutRoom_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.text())
            .then(res => {
                if (res === "success") {
                    alert(`Booking ${finalStatus === 'finished' ? 'checked out' : 'extended'} successfully!`);
                    window.location.href = "../Admin/index.php?accepted_room_bookings_list";
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(err => console.error(err));
    }

    document.getElementById('processExtendStay').addEventListener('click', () => processBooking('checkin'));
    document.getElementById('processCheckOut').addEventListener('click', () => processBooking('finished'));
</script>

<script>
    // --- Optional: keep room selection intact and disable unavailable rooms ---
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
                const isBackendDisabled = option.dataset.backendDisabled === "true";
                option.disabled = isBackendDisabled || (option.value && option.value !== currentValue && selectedRoomNumbers.has(option.value));
            });
        });
    }

    function updateRoomNumbers(row, changedByType = false) {
        const roomTypeSelect = row.querySelector('.roomTypeSelect');
        const roomNumberSelect = row.querySelector('.roomNumberSelect');
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const bookingId = <?= $booking_id ?>;
        const defaultNumber = row.dataset.defaultRoomNumber;

        fetch(`../Admin/adminBackend/check_availability_rooms.php?check_in=${checkIn}&check_out=${checkOut}&booking_id=${bookingId}`)
            .then(res => res.json())
            .then(data => {
                const type = roomTypeSelect.value;
                if (data[type] && data[type].length > 0) {
                    const currentValue = roomNumberSelect.value;

                    data[type].forEach(rn => {
                        let option = roomNumberSelect.querySelector(`option[value='${rn.room_number_id}']`);
                        if (!option) {
                            option = document.createElement('option');
                            option.value = rn.room_number_id;
                            roomNumberSelect.appendChild(option);
                        }
                        option.textContent = rn.room_number + (rn.note ? ` (${rn.note})` : '');
                        option.disabled = !!rn.disabled;
                        option.dataset.backendDisabled = rn.disabled ? "true" : "false";

                        if (option.value == currentValue || (!currentValue && defaultNumber == option.value)) {
                            option.selected = true;
                        }
                    });

                    Array.from(roomNumberSelect.options).forEach(opt => {
                        if (!data[type].some(rn => rn.room_number_id == opt.value)) {
                            roomNumberSelect.removeChild(opt);
                        }
                    });
                } else {
                    Array.from(roomNumberSelect.options).forEach(opt => {
                        if (!opt.selected) opt.disabled = true;
                    });
                }

                disableSelectedRoomNumbers();
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
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => updateRoomNumbers(row));

        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.addEventListener('change', function () {

                const row = this.closest('tr');

                updateRoomNumbers(row, true);

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
            alert('Total room capacity is less than the number of guests!');

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
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            updateRoomNumbers(row, true);
        });
    });

    document.getElementById('check_out').addEventListener('change', () => {
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            updateRoomNumbers(row, true);
        });
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
            roomsTotal += parseFloat(selectedOption.dataset.price);
        });

        let extraBedPrice = extraBedTotal;

        let totalBeforeDiscount = (roomsTotal + extraBedPrice) * nights;

        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
        const discountAmount = (discountPercentage / 100) * totalBeforeDiscount;

        const totalAmountNew = totalBeforeDiscount - discountAmount;
        document.getElementById('totalAmountNew').value = totalAmountNew.toFixed(2);

        const downPayment = parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;

        let totalDue;
        if (totalAmountNew < downPayment) {
            totalDue = 0;
        } else {
            totalDue = totalAmountNew - downPayment;
        }

        document.getElementById('totalDue').value = totalDue.toFixed(2);
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
        const totalDue = parseFloat(document.getElementById('totalDue').value) || 0;

        let change = payment - totalDue;
        if (change < 0) change = 0;

        document.getElementById('changeAmount').value = change.toFixed(2);
    });
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
                alert("Check-out cannot be earlier than check-in.");
                checkOut.value = checkIn.value;
            }
        });
    });

</script>


<?php include 'adminFrontend/footer.php'; ?>