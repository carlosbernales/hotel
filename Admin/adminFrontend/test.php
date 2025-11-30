<style>
    :root {
        --primary-gold: #D4AF37;
        --dark-bg: #2C2416;
        --light-gold: #F4E4C1;
        --text-dark: #1a1a1a;
        --border-color: #e0d4b8;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(212, 175, 55, 0.15);
        padding: 30px;
        margin-bottom: 40px;
    }

    .section-header {
        color: var(--dark-bg);
        font-size: 1.3rem;
        font-weight: 600;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-gold);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .info-item label i {
        color: var(--primary-gold);
        margin-right: 8px;
        width: 20px;
    }

    .info-text {
        background: var(--light-gold);
        padding: 12px 15px;
        border-radius: 6px;
        border-left: 3px solid var(--primary-gold);
        color: var(--text-dark);
        font-weight: 500;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    .amenities-box {
        background: var(--light-gold);
        padding: 15px;
        border-radius: 6px;
        border-left: 3px solid var(--primary-gold);
        white-space: pre-line;
        min-height: 100px;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .date-display {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .date-item {
        flex: 1;
    }

    .date-item label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
        font-weight: 500;
        display: block;
    }

    .date-item label i {
        color: var(--primary-gold);
        margin-right: 8px;
    }

    .table-responsive {
        margin: 20px 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    #roomsTable {
        margin-bottom: 0;
    }

    #roomsTable thead {
        background: var(--dark-bg);
        color: white;
    }

    #roomsTable thead th {
        padding: 15px;
        font-weight: 600;
        border: none;
    }

    #roomsTable tbody tr {
        border-bottom: 1px solid var(--border-color);
    }

    #roomsTable tbody tr:hover {
        background: rgba(244, 228, 193, 0.3);
    }

    #roomsTable tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .form-select {
        border: 2px solid var(--border-color);
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .form-select.is-invalid {
        border-color: #dc3545;
    }

    .guest-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .guest-table thead {
        background: var(--dark-bg);
        color: white;
    }

    .guest-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .guest-table tbody tr {
        border-bottom: 1px solid var(--border-color);
    }

    .guest-table tbody tr:hover {
        background: rgba(244, 228, 193, 0.3);
    }

    .guest-table td {
        padding: 12px 15px;
    }

    .badge-info {
        background: var(--primary-gold);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .payment-card {
        background: linear-gradient(135deg, var(--light-gold) 0%, #fff9e6 100%);
        padding: 25px;
        border-radius: 8px;
        border: 2px solid var(--primary-gold);
        margin: 25px 0;
    }

    .payment-card label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 8px;
        font-weight: 500;
        display: block;
    }

    .payment-card label i {
        color: var(--primary-gold);
        margin-right: 8px;
    }

    .payment-card .form-control {
        border: 2px solid var(--border-color);
        padding: 12px 15px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
    }

    .payment-card .form-control:focus {
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-warning {
        background: #ffc107;
        color: var(--dark-bg);
    }

    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        text-decoration: none;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .date-display {
            flex-direction: column;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
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
    WHERE b.status NOT IN ('finished','rejected','rescheduled')
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
            <a href="../Admin/adminBackend/delete_roomBook_cancelBtn.php?booking_id=<?= $booking['booking_id'] ?>"
                class="btn btn-danger"
                onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                <i class="fas fa-arrow-left"></i> Cancel
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
                        $items[] = $row['amenity_name'] . " x" . $row['quantity'] . " (₱" . number_format($row['price'], 2) . ")";
                        if (strtolower($row['bedOrNot'] ?? '') === 'yes') {
                            $extraBedTotal += $row['quantity'] * $row['price'];
                        }
                    }
                    $amenitiesDisplay = implode("\n", $items);
                }
                ?>
                <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($amenitiesDisplay) ?></textarea>
                <script>
                    const extraBedTotal = <?= $extraBedTotal ?>;
                </script>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label><i class="fas fa-calendar-check"></i> Check-in</label>
                    <input type="date" id="check_in" class="form-control"
                        value="<?= date('Y-m-d', strtotime($booking['check_in'])) ?>">
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
                <button type="button" id="processReserveBtn" class="btn btn-warning">
                    <i class="fas fa-check-circle"></i> Reserve
                </button>
                <button type="button" id="processCheckinBtn" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Check In
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
                    if (!defaultNumber || defaultNumber === "0") {
                        const placeholder = document.createElement('option');
                        placeholder.textContent = "Please choose a room number";
                        placeholder.disabled = true;
                        placeholder.selected = true;
                        roomNumberSelect.appendChild(placeholder);
                    }

                    data[roomTypeSelect.value].forEach(rn => {
                        const option = document.createElement('option');
                        option.value = rn.room_number_id;
                        option.textContent = rn.room_number + (rn.note ? ` (${rn.note})` : '');
                        if (defaultNumber && rn.room_number_id == defaultNumber) option.selected = true;
                        roomNumberSelect.appendChild(option);
                    });
                } else {
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
    function processBooking(status) {
        const roomSelects = document.querySelectorAll('.roomNumberSelect');
        let allSelected = true;

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
            }
        });

        if (!allSelected) {
            alert('Please select a room number for all booked rooms.');
            return;
        }

        const confirmed = confirm(`Are you sure you want to ${status === 'checkin' ? 'check in' : 'reserve'} this booking?`);
        if (!confirmed) return;

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
            check_in: status === 'checkin'
                ? new Date().toLocaleString("sv-SE", { timeZone: "Asia/Manila" }).replace('T', ' ')
                : document.getElementById('check_in').value,
            check_out: document.getElementById('check_out').value,
            total_amount: parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')),
            payment_input: parseFloat(document.getElementById('paymentInput').value) || 0,
            payment_method: document.querySelector('select[name="payment_method"]').value,
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
                    alert(`Booking ${status === 'checkin' ? 'checked in' : 'reserved'} successfully!`);
                    window.location.href = "../Admin/index.php?accepted_room_bookings_list";
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(err => console.error(err));
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
                alert("Check-out cannot be earlier than check-in.");
                checkOut.value = checkIn.value;
            }
        });
    });

</script>


<?php include 'adminFrontend/footer.php'; ?>