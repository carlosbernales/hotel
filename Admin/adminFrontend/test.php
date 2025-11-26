<style>
    /* Add Table Button */
    .table-add-btn {
        background-color: #C9A961;
        color: #2d2d2d;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
    }

    .table-add-btn:hover {
        background-color: #B8964F;
        color: #2d2d2d;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .table-add-btn i {
        font-size: 1.1rem;
    }

    /* Table Action Buttons */
    .table-action-btn {
        padding: 0.4rem 0.6rem;
        border-radius: 4px;
        border: none;
        margin: 0 0.2rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .table-action-btn i {
        font-size: 1rem;
    }

    /* View Button - Cyan/Info */
    .table-action-view {
        background-color: #17a2b8;
        color: white;
    }

    .table-action-view:hover {
        background-color: #138496;
        color: white;
        transform: scale(1.05);
    }

    /* Edit Button - Mustard/Gold matching your theme */
    .table-action-edit {
        background-color: #C9A961;
        color: #2d2d2d;
    }

    .table-action-edit:hover {
        background-color: #B8964F;
        color: #2d2d2d;
        transform: scale(1.05);
    }

    /* Delete Button - Red/Danger */
    .table-action-delete {
        background-color: #dc3545;
        color: white;
    }

    .table-action-delete:hover {
        background-color: #c82333;
        color: white;
        transform: scale(1.05);
    }

    /* Package Modal Styling */
    .package-modal {
        border: none;
        border-radius: 8px;
    }

    .package-modal-header {
        background-color: #C9A961;
        color: #2d2d2d;
        border-bottom: 2px solid #B8964F;
    }

    .package-modal-close {
        filter: brightness(0.3);
    }

    .package-modal-body {
        background-color: #f8f9fa;
        padding: 2rem;
    }

    .package-label {
        font-weight: 600;
        color: #2d2d2d;
    }

    .package-input {
        border: 1px solid #C9A961;
        border-radius: 4px;
        padding: 0.6rem;
    }

    .package-input:focus {
        border-color: #B8964F;
        box-shadow: 0 0 0 0.2rem rgba(201, 169, 97, 0.25);
    }

    .package-help-text {
        color: #6c757d;
    }

    .package-btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
    }

    .package-btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }

    .package-btn-save {
        background-color: #28a745;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .package-btn-save:hover {
        background-color: #218838;
        color: white;
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
    SELECT room_type_id, room_type_name, price
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
        </div>
        <div class="container mt-4">
            <h3 class="fw-bold">Booking Details</h3>
            <a href="../Admin/index.php?room_booking_list" class="btn btn-secondary btn-sm mb-3">Back</a>

            <!-- BOOKING INFO -->
            <h4 class="mt-3">Booking Information</h4>

            <div class="row">
                <div class="col-md-4 mb-2">
                    <label>Number of Guests</label>
                    <input class="form-control" value="<?= $booking['number_of_guests'] ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Room Quantity</label>
                    <input class="form-control" value="<?= $booking['room_quantity'] ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Adults</label>
                    <input class="form-control" value="<?= $booking['num_adults'] ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Children</label>
                    <input class="form-control" value="<?= $booking['num_children'] ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Extra Bed</label>
                    <select id="extraBed" class="form-select">
                        <option value="0" data-price="0" <?= $booking['extra_bed'] == 0 ? 'selected' : '' ?>>None</option>

                        <?php foreach ($beds as $bed): ?>
                            <?php $selected = ($booking['extra_bed'] == $bed['id']) ? 'selected' : ''; ?>
                            <option value="<?= $bed['id'] ?>" data-price="<?= $bed['price'] ?>" <?= $selected ?>>
                                <?= $bed['item_type'] ?> (<?= number_format($bed['price'], 2) ?> per night)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>



                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Check-in</label>
                        <input type="datetime-local" id="check_in" class="form-control"
                            value="<?= date('Y-m-d\TH:i', strtotime($booking['check_in'])) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Check-out</label>
                        <input type="datetime-local" id="check_out" class="form-control"
                            value="<?= date('Y-m-d\TH:i', strtotime($booking['check_out'])) ?>">
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Remaining Balance</label>
                    <input type="text" id="remainingBalance" class="form-control"
                        value="<?= ($booking['remaining_balance'] > 0) ? number_format($booking['remaining_balance'], 2) : '0' ?>"
                        readonly>
                </div>
            </div>


            <!-- DISCOUNT INFO -->
            <h4 class="mt-4">Discount Details</h4>
            <div class="row">
                <div class="col-md-4 mb-2"><label>Type</label>
                    <input class="form-control" value="<?= $booking['discount_type'] ?>" readonly>
                </div>
                <div class="col-md-4 mb-2"><label>Percentage</label>
                    <input class="form-control" id="discountPercentage" value="<?= $booking['discount_percentage'] ?>"
                        readonly>
                </div>
                <div class="col-md-4 mb-2"><label>Amount</label>
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
            <h4 class="mt-4">Booked Rooms</h4>
            <table class="table table-bordered" id="roomsTable">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $rooms->fetch_assoc()): ?>
                        <?php $rtid = $r['room_type_id']; ?>
                        <tr>
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
                                <?= number_format($roomTypes[$rtid]['price'], 2) ?>
                            </td>

                            <td class="roomCapacity">
                                <?= $roomTypes[$rtid]['capacity'] ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4 class="mt-4">Guest Names</h4>
            <table class="table table-bordered mb-5">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Guest Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($g = $guests->fetch_assoc()): ?>
                        <tr>
                            <td><?= $g['first_name'] ?></td>
                            <td><?= $g['last_name'] ?></td>
                            <td><?= $g['guest_type'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4 class="mt-4">Updated Payment Details</h4>

            <div class="row mb-3">

                <div class="col-md-4 mb-2">
                    <label>New Total Amount</label>
                    <input type="text" id="totalAmountNew" class="form-control"
                        value="<?= number_format($booking['total_amount'], 2) ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Old Total Amount</label>
                    <?php
                    $totalOld = $booking['total_amount'] + $booking['discount_amount'];
                    ?>
                    <input type="text" id="totalAmountOld" class="form-control"
                        value="<?= number_format($totalOld, 2) ?>" readonly>

                </div>

                <div class="col-md-4 mb-2">
                    <label>Down Payment</label>
                    <input type="text" id="downPayment" class="form-control"
                        value="<?= number_format($booking['downpayment_amount'], 2) ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Total Due</label>
                    <input type="text" id="totalDue" class="form-control" value="0" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Payment</label>
                    <input type="number" id="paymentInput" class="form-control" min="0">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Change</label>
                    <input type="text" id="changeAmount" class="form-control" value="0" readonly>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-3 gap-2">
                <button type="button" id="rescheduleBtn" class="btn btn-warning">Reschedule</button>
                <button type="button" id="processBookingBtn" class="btn btn-success">Process Booking</button>
                <button type="button" id="rejectBtn" class="btn btn-danger">Reject</button>
                <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
            </div>


        </div>
    </div>
</div>

<script>
    // -------------------------------
    // Calculate total amount and total due
    // -------------------------------
    function updateTotal() {
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;

        if (!check_in || !check_out) return;

        const start = new Date(check_in);
        const end = new Date(check_out);
        let nights = (end - start) / (1000 * 60 * 60 * 24);
        nights = nights < 1 ? 1 : nights;

        // Room total
        let roomTotal = 0;
        document.querySelectorAll('#roomsTable tbody tr').forEach(tr => {
            const price = parseFloat(tr.querySelector('.roomPrice').textContent.replace(/,/g, '')) || 0;
            roomTotal += price * nights;
        });

        // Extra bed
        const extraBedSelect = document.getElementById('extraBed');
        const extraBedPrice = parseFloat(extraBedSelect.selectedOptions[0].dataset.price) || 0;
        roomTotal += extraBedPrice * nights;

        // Update new total amount
        const totalAmountNew = roomTotal;
        document.getElementById('totalAmountNew').value = totalAmountNew.toFixed(2);

        // Discount
        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
        const discountAmount = totalAmountNew * (discountPercentage / 100);

        // Down payment
        const downPayment = parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;

        // Total due
        const totalDue = totalAmountNew - discountAmount - downPayment;
        document.getElementById('totalDue').value = totalDue.toFixed(2);

        // Update change if payment is entered
        updateChange();
    }

    // -------------------------------
    // Compute change based on payment
    // -------------------------------
    function updateChange() {
        const totalDue = parseFloat(document.getElementById('totalDue').value) || 0;
        const payment = parseFloat(document.getElementById('paymentInput').value) || 0;
        const change = payment - totalDue;
        document.getElementById('changeAmount').value = change.toFixed(2);
    }

    // -------------------------------
    // Room type change event
    // -------------------------------
    document.querySelectorAll('.roomTypeSelect').forEach(select => {
        select.addEventListener('change', function () {
            const tr = this.closest('tr');
            const price = parseFloat(this.selectedOptions[0].dataset.price) || 0;
            const capacity = this.selectedOptions[0].dataset.capacity || 0;

            tr.querySelector('.roomPrice').textContent = price.toFixed(2);
            tr.querySelector('.roomCapacity').textContent = capacity;

            updateTotal();
        });
    });

    // -------------------------------
    // Extra bed, date, and payment event listeners
    // -------------------------------
    document.getElementById('extraBed').addEventListener('change', updateTotal);
    document.getElementById('check_in').addEventListener('change', updateTotal);
    document.getElementById('check_out').addEventListener('change', updateTotal);
    document.getElementById('paymentInput').addEventListener('input', updateChange);

    // -------------------------------
    // Check room availability (optional)
    // -------------------------------
    function checkRoomAvailability() {
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;
        const booking_id = <?= $booking_id ?>;

        if (!check_in || !check_out) return;

        fetch(`../Admin/adminBackend/check_availability_rooms.php?check_in=${check_in}&check_out=${check_out}&booking_id=${booking_id}`)
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll('.roomTypeSelect').forEach(select => {
                    select.querySelectorAll('option').forEach(option => {
                        option.disabled = false;
                        option.text = option.text.replace(/\s*\(.*\)$/, '');
                        if (data.unavailable[option.value]) {
                            option.disabled = true;
                            option.text += ` (${data.unavailable[option.value]})`;
                        }
                    });
                });
            });
    }

    document.getElementById('check_in').addEventListener('change', checkRoomAvailability);
    document.getElementById('check_out').addEventListener('change', checkRoomAvailability);

    // -------------------------------
    // Initialize on page load
    // -------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        updateTotal();
    });

    function submitBooking(action) {
        const booking_id = <?= $booking_id ?>;
        const extra_bed = document.getElementById('extraBed').value;
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;
        const total_amount = parseFloat(document.getElementById('totalAmountNew').value) || 0;
        const downpayment = parseFloat(document.getElementById('downPayment').value) || 0;
        const remaining_balance = parseFloat(document.getElementById('remainingBalance').value) || 0;

        const room_types = {};
        document.querySelectorAll('.roomTypeSelect').forEach((select, index) => {
            const booked_room_id = select.dataset.bookedRoomId; // make sure to add data-booked-room-id="<?= $r['id'] ?>" in PHP
            room_types[booked_room_id] = select.value;
        });

        fetch('../Admin/adminBackend/update_room_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                booking_id,
                action,
                extra_bed,
                check_in,
                check_out,
                total_amount,
                downpayment,
                remaining_balance,
                ...Object.fromEntries(Object.entries(room_types).map(([k, v]) => [`room_types[${k}]`, v]))
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`Booking ${data.status} successfully!`);
                    window.location.reload();
                }
            });
    }

    document.getElementById('processBookingBtn').addEventListener('click', () => submitBooking('process'));
    document.getElementById('rescheduleBtn').addEventListener('click', () => submitBooking('reschedule'));
    document.getElementById('rejectBtn').addEventListener('click', () => submitBooking('reject'));
    document.getElementById('cancelBtn').addEventListener('click', () => submitBooking('cancel'));


</script>





<?php include 'adminFrontend/footer.php'; ?>