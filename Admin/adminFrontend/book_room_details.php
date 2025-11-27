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

// 1. Fetch booking
$booking_sql = "SELECT * FROM bookings WHERE booking_id = $booking_id";
$booking = $conn->query($booking_sql)->fetch_assoc();

// 2. Fetch rooms
$rooms_sql = "
    SELECT *
    FROM booked_rooms
    WHERE booking_id = $booking_id
";
$rooms = $conn->query($rooms_sql);

// 3. Fetch guest names
$guests_sql = "
    SELECT first_name, last_name, guest_type
    FROM guest_names
    WHERE booking_id = $booking_id
";
$guests = $conn->query($guests_sql);


// Step 1: Fetch all occupied room types for the selected dates
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
            <a href="../Admin/index.php?room_booking_list" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="container-fluid">
            <!-- BOOKING INFO -->
            <h4 class="section-header">Booking Information</h4>

            <div class="info-grid">
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

                <div class="info-item">
                    <label><i class="fas fa-bed"></i> Extra Bed</label>
                    <?php
                    $extraBedDisplay = "None";
                    foreach ($beds as $bed) {
                        if ($booking['extra_bed'] == $bed['id']) {
                            $extraBedDisplay = $bed['item_type'] . " (" . number_format($bed['price'], 2) . " per night)";
                            break;
                        }
                    }
                    ?>
                    <input type="text" class="form-control" value="<?= $extraBedDisplay ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-wallet"></i> Remaining Balance</label>
                    <input type="text" id="remainingBalance" class="form-control"
                        value="<?= ($booking['remaining_balance'] > 0) ? number_format($booking['remaining_balance'], 2) : '0' ?>"
                        readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label><i class="fas fa-calendar-check"></i> Check-in</label>
                    <input type="datetime-local" id="check_in" class="form-control"
                        value="<?= date('Y-m-d\TH:i', strtotime($booking['check_in'])) ?>">
                </div>
                <div class="col-md-6">
                    <label><i class="fas fa-calendar-times"></i> Check-out</label>
                    <input type="datetime-local" id="check_out" class="form-control"
                        value="<?= date('Y-m-d\TH:i', strtotime($booking['check_out'])) ?>">
                </div>
            </div>

            <!-- DISCOUNT INFO -->
            <h4 class="section-header">Discount Details</h4>
            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-tag"></i> Type</label>
                    <input class="form-control" value="<?= $booking['discount_type'] ?>" readonly>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-percent"></i> Percentage</label>
                    <input class="form-control" id="discountPercentage" value="<?= $booking['discount_percentage'] ?>"
                        readonly>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-money-bill-wave"></i> Amount</label>
                    <input class="form-control" id="discount_amount" value="<?= $booking['discount_amount'] ?>"
                        readonly>
                </div>
            </div>

            <?php
            $roomTypes = [];
            $rt_sql = "SELECT * FROM room_types";
            $rt_res = $conn->query($rt_sql);

            while ($rt = $rt_res->fetch_assoc()) {
                $roomTypes[$rt['room_type_id']] = $rt;
            }
            ?>

            <!-- ROOMS -->
            <h4 class="section-header">Booked Rooms</h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="roomsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hotel"></i> Room Type</th>
                            <th><i class="fas fa-peso-sign"></i> Price</th>
                            <th><i class="fas fa-users"></i> Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $rooms->fetch_assoc()): ?>
                            <?php $rtid = $r['room_type_id']; ?>
                            <tr data-booked-room-id="<?= $r['id'] ?>">
                                <td>
                                    <select class="form-select roomTypeSelect">
                                        <?php foreach ($roomTypes as $rt): ?>
                                            <?php
                                            if ($rt['status'] !== 'active')
                                                continue;

                                            $disabled = in_array($rt['room_type_id'], $occupied_rooms) ? 'disabled' : '';
                                            $selected = $rt['room_type_id'] == $rtid ? 'selected' : '';
                                            ?>
                                            <option value="<?= $rt['room_type_id'] ?>" data-price="<?= $rt['price'] ?>"
                                                data-capacity="<?= $rt['capacity'] ?>" <?= $selected ?>         <?= $disabled ?>>
                                                <?= $rt['room_type'] ?>
                                                <?= $disabled ? '(Occupied)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="roomPrice">
                                    ₱<?= number_format($roomTypes[$rtid]['price'], 2) ?>
                                </td>
                                <td class="roomCapacity">
                                    <?= $roomTypes[$rtid]['capacity'] ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- GUESTS -->
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

            <!-- PAYMENT -->
            <h4 class="section-header">Payment Details</h4>

            <div class="info-grid">
                <div class="info-item">
                    <label><i class="fas fa-calculator"></i> New Total Amount</label>
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
                        <label><i class="fas fa-credit-card"></i> Payment Amount</label>
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
                <button type="button" id="rescheduleBtn" class="btn btn-warning">
                    <i class="fas fa-calendar-alt"></i> Reschedule
                </button>
                <button type="button" id="processBookingBtn" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Process Booking
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateRoomOptions() {
        const selectedValues = Array.from(document.querySelectorAll('.roomTypeSelect'))
            .map(select => select.value)
            .filter(val => val !== "");

        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.querySelectorAll('option').forEach(option => {
                const val = option.value;

                if (val === "") return;

                const isOccupied = option.dataset.occupied === "true";

                if (isOccupied) {
                    option.disabled = true;
                } else if (selectedValues.includes(val) && val !== select.value) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    function checkRoomAvailability() {
        const check_in = document.getElementById('check_in').value;
        const booking_id = <?= $booking_id ?>;

        if (!check_in) return;

        fetch(`../Admin/adminBackend/check_availability_rooms.php?check_in=${check_in}&booking_id=${booking_id}`)
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll('.roomTypeSelect').forEach(select => {
                    select.querySelectorAll('option').forEach(option => {
                        const val = option.value;

                        option.text = option.text.replace(/\s*\(.*\)$/, '');
                        option.dataset.occupied = "false";

                        if (data.unavailable[val]) {
                            option.disabled = true;
                            option.dataset.occupied = "true";
                            option.text += ` (${data.unavailable[val]})`;
                        }
                    });
                });

                updateRoomOptions();
            });
    }

    function updateTotal() {
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;

        if (!check_in || !check_out) return;

        const start = new Date(check_in);
        const end = new Date(check_out);
        let nights = (end - start) / (1000 * 60 * 60 * 24);
        nights = nights < 1 ? 1 : nights;

        let roomTotal = 0;

        document.querySelectorAll('#roomsTable tbody tr').forEach(tr => {
            let priceText = tr.querySelector('.roomPrice').textContent.replace(/,/g, '').replace('₱', '');
            let price = parseFloat(priceText) || 0;
            roomTotal += price;
        });

        let extraBedPrice = 0;
        const extraBedText = "<?= $extraBedDisplay ?>";
        const bedMatch = extraBedText.match(/\((.*?) per night\)/);

        if (bedMatch) {
            extraBedPrice = parseFloat(bedMatch[1].replace(/,/g, "")) || 0;
        }

        let subtotal = (roomTotal + extraBedPrice) * nights;

        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
        const discountAmount = subtotal * (discountPercentage / 100);

        const netTotal = subtotal - discountAmount;

        document.getElementById('totalAmountNew').value = netTotal.toFixed(2);

        const downPayment = parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;

        let totalDue = netTotal - downPayment;
        if (totalDue < 0) totalDue = 0;

        document.getElementById('totalDue').value = totalDue.toFixed(2);

        const payment = parseFloat(document.getElementById('paymentInput').value.replace(/,/g, '')) || 0;
        document.getElementById('changeAmount').value = (payment - totalDue).toFixed(2);
    }


    document.getElementById('check_in').addEventListener('change', function () {
        checkRoomAvailability();
        updateTotal();
    });

    document.getElementById('check_out').addEventListener('change', updateTotal);

    document.getElementById('paymentInput').addEventListener('input', updateTotal);

    document.getElementById('discountPercentage').addEventListener('input', updateTotal);
    document.getElementById('downPayment').addEventListener('input', updateTotal);

    document.querySelectorAll('.roomTypeSelect').forEach(select => {
        select.addEventListener('change', function () {
            const tr = this.closest('tr');
            const price = parseFloat(this.selectedOptions[0].dataset.price) || 0;
            const capacity = this.selectedOptions[0].dataset.capacity;

            tr.querySelector('.roomPrice').textContent = '₱' + price.toFixed(2);
            tr.querySelector('.roomCapacity').textContent = capacity;

            updateRoomOptions();
            updateTotal();
        });
    });

    checkRoomAvailability();
    updateTotal();


    document.addEventListener("DOMContentLoaded", () => {
        const checkIn = document.getElementById("check_in");
        const checkOut = document.getElementById("check_out");

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const minDate = now.toISOString().slice(0, 16);

        checkIn.min = minDate;
        checkOut.min = minDate;

        function validateDates() {
            const inDate = new Date(checkIn.value);
            const outDate = new Date(checkOut.value);

            if (checkIn.value && checkOut.value && inDate >= outDate) {
                alert("Check-out must be later than Check-in.");
                checkOut.value = "";
            }

            updateTotal();
        }

        checkIn.addEventListener("change", () => {
            checkOut.min = checkIn.value;
            validateDates();
        });

        checkOut.addEventListener("change", validateDates);
    });



    document.getElementById('processBookingBtn').addEventListener('click', () => {
        processBooking('accepted');
    });

    document.getElementById('rescheduleBtn').addEventListener('click', () => {
        processBooking('rescheduled');
    });

    function processBooking(status) {
        const bookingId = <?= $booking_id ?>;
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const totalAmount = parseFloat(document.getElementById('totalAmountNew').value) || 0;

        const rooms = Array.from(document.querySelectorAll('#roomsTable tbody tr')).map(tr => {
            const roomId = tr.dataset.bookedRoomId;
            const select = tr.querySelector('.roomTypeSelect');
            const selectedOption = select.selectedOptions[0];

            return {
                id: roomId,
                room_type_id: select.value,
                room_type_name: selectedOption.text.replace(/\s*\(.*\)$/, ''),
                price: parseFloat(selectedOption.dataset.price) || 0
            };
        });


        fetch('../Admin/adminBackend/update_room_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                bookingId,
                checkIn,
                checkOut,
                totalAmount,
                rooms,
                status
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Booking updated successfully!');
                    window.location.href = '../Admin/index.php?room_booking_list';
                } else {
                    alert('Error: ' + data.message);
                }
            })

            .catch(err => console.error(err));
    }

</script>



<?php include 'adminFrontend/footer.php'; ?>