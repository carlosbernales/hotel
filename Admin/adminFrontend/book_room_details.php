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
$beds = [];
$bed_sql = "SELECT item_type, price FROM beds";
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
                    <label>Total Amount</label>
                    <input class="form-control" value="<?= number_format($booking['total_amount'], 2) ?>" readonly>
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
                    <input class="form-control" value="<?= $booking['extra_bed'] ?>" readonly>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Extra Bed</label>
                    <select id="extraBed" class="form-select">
                        <option value="0" data-price="0">None</option>
                        <?php foreach ($beds as $bed): ?>
                            <option value="<?= $bed['item_type'] ?>" data-price="<?= $bed['price'] ?>">
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
                    <input class="form-control" value="<?= $booking['discount_amount'] ?>" readonly>
                </div>
            </div>

            <?php
            $roomTypes = [];
            $rt_sql = "SELECT room_type_id, room_type, price, capacity FROM room_types";
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

            <h4 class="mt-3">Total Amount:</h4>
            <input type="text" id="totalAmount" class="form-control"
                value="<?= number_format($booking['total_amount'], 2) ?>" readonly>
            <!-- GUESTS -->
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
        </div>
    </div>
</div>

<script>
    function updateTotal() {
        let total = 0;
        let roomCount = 0;

        // Get check-in and check-out
        const check_in = document.getElementById('check_in').value;
        const check_out = document.getElementById('check_out').value;

        if (!check_in || !check_out) return;

        const start = new Date(check_in);
        const end = new Date(check_out);
        let nights = (end - start) / (1000 * 60 * 60 * 24);
        nights = nights < 1 ? 1 : nights;

        // Extra bed price from selected option
        const extraBedSelect = document.getElementById('extraBed');
        const extraBedPrice = parseFloat(extraBedSelect.selectedOptions[0].dataset.price) || 0;

        // Sum rooms
        document.querySelectorAll('#roomsTable tbody tr').forEach(tr => {
            let priceText = tr.querySelector('.roomPrice').textContent.replace(/,/g, '');
            let price = parseFloat(priceText) || 0;

            total += price * nights;
            roomCount++;
        });

        // Add extra bed cost
        total += extraBedPrice * nights;

        // Apply discount
        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
        const discountAmount = total * (discountPercentage / 100);

        const netTotal = total - discountAmount;

        // Update UI
        document.getElementById('totalAmount').value = netTotal.toFixed(2);
        document.querySelector('input[readonly][value="<?= $booking['room_quantity'] ?>"]').value = roomCount;
    }
    document.getElementById('check_in').addEventListener('change', updateTotal);
    document.getElementById('check_out').addEventListener('change', updateTotal);
    document.getElementById('extraBed').addEventListener('change', updateTotal);
    document.querySelectorAll('.roomTypeSelect').forEach(select => {
        select.addEventListener('change', updateTotal);
    });

    // Change room type
    document.querySelectorAll('.roomTypeSelect').forEach(select => {
        select.addEventListener('change', function () {

            let price = this.selectedOptions[0].getAttribute('data-price');
            let capacity = this.selectedOptions[0].getAttribute('data-capacity');

            let tr = this.closest('tr');

            tr.querySelector('.roomPrice').textContent = parseFloat(price).toFixed(2);
            tr.querySelector('.roomCapacity').textContent = capacity;

            updateTotal();
        });
    });


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
                        const val = option.value;

                        // Reset
                        option.disabled = false;
                        option.text = option.text.replace(/\s*\(.*\)$/, '');

                        // Mark unavailable rooms
                        if (data.unavailable[val]) {
                            option.disabled = true;
                            option.text = option.text + ` (${data.unavailable[val]})`;
                        }
                    });
                });
            });
    }

    document.getElementById('check_in').addEventListener('change', checkRoomAvailability);
    document.getElementById('check_out').addEventListener('change', checkRoomAvailability);

</script>





<?php include 'adminFrontend/footer.php'; ?>