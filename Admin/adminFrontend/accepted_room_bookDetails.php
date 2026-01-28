<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header_nosidebar.php';

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

<link rel="stylesheet" href="../Admin/adminFrontend/css/accepted_room_bookDetails.css">
<link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">


<div class="breadcrumb-custom d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-home"> Booking Details</i>
    </div>
</div>

<div class="info-card" style="margin-bottom: 40px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 fw-bold" style="color: var(--dark-bg);">
            <i class="fas fa-check-circle" style="color: var(--gold);"></i> Accepted Booking Details
        </h3>
        <a href="../Admin/index.php?accepted_room_bookings_list" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
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
                    <input class="form-control" id="numberGuest" value="<?= $booking['number_of_guests'] ?>" readonly>
                </div>

                <div class="info-item">
                    <label><i class="fas fa-moon"></i> Number of Nights</label>
                    <input class="form-control" value="<?= $booking['nights'] ?>" readonly>
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
                    <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($amenitiesDisplay) ?></textarea>
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

            <?php
            $resched_sql = "
                    SELECT *
                    FROM reschedule_bookings
                    WHERE booking_fk_id = {$booking['booking_id']}
                    ORDER BY date_resched ASC
                ";
            $resched_res = $conn->query($resched_sql);

            // Fetch original check-in/out from booking_check_inout
            $ci_co_sql = "
                    SELECT check_in, check_out
                    FROM booking_check_inout
                    WHERE booking_fk_id = {$booking['booking_id']}
                ";
            $ci_co_res = $conn->query($ci_co_sql);
            $original = $ci_co_res->fetch_assoc();

            if ($resched_res->num_rows > 0):
                ?>
                <div class="amenities-section mt-3">
                    <label><i class="fas fa-concierge-bell"></i> Reschedule Details</label>
                    <textarea class="form-control" rows="2" readonly><?php
                    $prevCI = $original['check_in'];
                    $prevCO = $original['check_out'];

                    while ($r = $resched_res->fetch_assoc()) {
                        $newCI = $r['check_in'];
                        $newCO = $r['check_out'];
                        $dateRes = date("F j, Y g:i A", strtotime($r['date_resched']));
                        $reason = $r['reason'];

                        echo "On $dateRes, the guest requested a reschedule, changing the stay from "
                            . date("F j, Y", strtotime($prevCI)) . " - "
                            . date("F j, Y", strtotime($prevCO)) . " to "
                            . date("F j, Y", strtotime($newCI)) . " - "
                            . date("F j, Y", strtotime($newCO)) . " due to the reason: \"$reason\".\n";

                        // Update previous dates for the next iteration
                        $prevCI = $newCI;
                        $prevCO = $newCO;
                    }
                    ?></textarea>
                </div>
            <?php endif; ?>


            <?php
            $transfers_sql = "
                SELECT *
                FROM room_transfers
                WHERE bookings_fk_id = $booking_id
                ORDER BY transfer_date ASC
            ";
            $transfers_res = $conn->query($transfers_sql);

            function getRoomInfo($conn, $room_number_fk_id)
            {
                if (!$room_number_fk_id)
                    return null;

                $sql = "SELECT room_number, floor_number FROM room_numbers WHERE room_number_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $room_number_fk_id);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0) {
                    return $res->fetch_assoc();
                }
                return null;
            }
            ?>

            <?php if ($transfers_res->num_rows > 0): ?>
                <div class="section-card mb-4">
                    <h4 class="section-header">
                        <i class="fas fa-bed"></i> Transfer Rooms (Old Rooms)
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-door-open"></i> Room Type</th>
                                    <th><i class="fas fa-hashtag"></i> Room</th>
                                    <th><i class="fas fa-layer-group"></i> Floor</th>
                                    <th><i class="fas fa-tag"></i> Price</th>
                                    <th><i class="fas fa-calendar-alt"></i> Transfer Date</th>
                                    <th><i class="fas fa-comment"></i> Reason</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($t = $transfers_res->fetch_assoc()): ?>
                                    <?php
                                    $roomInfo = getRoomInfo($conn, $t['room_number_fk_id']);
                                    $roomNumber = $roomInfo['room_number'] ?? '-';
                                    $floorNumber = $roomInfo['floor_number'] ?? '-';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($t['room_type_name']) ?></td>
                                        <td><?= htmlspecialchars($roomNumber) ?></td>
                                        <td><?= htmlspecialchars($floorNumber) ?></td>
                                        <td>₱<?= number_format($t['price'], 2) ?></td>
                                        <td><?= date("F j, Y g:i A", strtotime($t['transfer_date'])) ?></td>
                                        <td><?= htmlspecialchars($t['reason']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>


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
                                <th><i class="fas fa-hashtag"></i> Room</th>
                                <th><i class="fas fa-hashtag"></i> Original Room</th>
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

                                    <td>
                                        <?php
                                        $originalNumber = '-';
                                        if (isset($roomNumbers[$bookedRoomTypeId])) {
                                            foreach ($roomNumbers[$bookedRoomTypeId] as $rnItem) {
                                                if ($rnItem['room_number_id'] == $r['room_number_fk_id']) {
                                                    $originalNumber = $rnItem['room_number'];
                                                    break;
                                                }
                                            }
                                        }
                                        echo $originalNumber;
                                        ?>
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
                            <option value="GCash" <?= ($booking['payment_method'] == 'GCash') ? 'selected' : '' ?>>
                                GCash
                            </option>
                            <option value="Paypal" <?= ($booking['payment_method'] == 'Paypal') ? 'selected' : '' ?>>
                                Paypal
                            </option>
                        </select>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-percent"></i> Discount Type</label>
                        <select id="discountType" class="form-control">
                            <option value="" <?= empty($booking['discount_type']) ? 'selected' : '' ?>>No Discount
                            </option>
                            <option value="PWD" <?= ($booking['discount_type'] === 'PWD') ? 'selected' : '' ?>>PWD (20%)
                            </option>
                            <option value="Senior" <?= ($booking['discount_type'] === 'Senior') ? 'selected' : '' ?>>Senior
                                Citizen (20%)</option>
                        </select>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-percent"></i> Discount Applied</label>
                        <input class="form-control" id="discountPercentage"
                            value="<?= (int) $booking['discount_percentage'] ?>%" readonly>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-money-bill-wave"></i> Discount Amount</label>
                        <input type="hidden" id="discount_amount" value="<?= $booking['discount_amount'] ?>">
                        <input class="form-control" id="discountAmount" value="0" readonly>

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
                                placeholder="Enter payment amount">
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
                <button type="button" id="processReschedBtn" class="btn btn-warning">
                    <i class="fas fa-calendar-alt"></i> Reschedule Booking
                </button>
                <button type="button" id="processCheckinBtn" class="btn btn-success">
                    <i class="fas fa-sign-in-alt"></i> Check In Guest
                </button>
            </div>
        </div>
    </div>

</div>



<div class="modal fade" id="reviewChangesModal" tabindex="-1" aria-labelledby="reviewChangesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header"
                style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border-bottom: 3px solid #c9a961;">
                <h5 class="modal-title text-white" id="reviewChangesLabel">
                    <i class="fas fa-clipboard-check me-2" style="color: #c9a961;"></i>
                    Review Changes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body" style="background-color: #f8f9fa; padding: 25px;">
                <!-- Extension Info Alert -->
                <div id="extendedInfo" class="alert alert-info border-0 shadow-sm mb-4"
                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); display: none;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-calendar-alt me-3 mt-1" style="color: #1976d2; font-size: 1.2rem;"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #1565c0;">Reschedule Details</h6>
                            <p class="mb-0" style="color: #424242; font-size: 0.95rem;"></p>
                        </div>
                    </div>
                </div>

                <!-- Changes Table -->
                <div class="table-responsive" id="reviewChangesTable"
                    style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <table class="table table-hover mb-0" style="background-color: white;">
                        <thead style="background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);">
                            <tr>
                                <th style="color: #c9a961; font-weight: 600; padding: 15px; border: none;">
                                    <i class="fas fa-bed me-2"></i>Original Type
                                </th>

                                <th style="color: #c9a961; font-weight: 600; padding: 15px; border: none;">
                                    <i class="fas fa-door-closed me-2"></i>Original #
                                </th>

                                <th style="color: #c9a961; font-weight: 600; padding: 15px; border: none;">
                                    <i class="fas fa-info-circle me-2"></i>Status
                                </th>

                                <th style="color: #c9a961; font-weight: 600; padding: 15px; border: none;">
                                    <i class="fas fa-bed me-2"></i>New Type
                                </th>

                                <th style="color: #c9a961; font-weight: 600; padding: 15px; border: none;">
                                    <i class="fas fa-door-open me-2"></i>New #
                                </th>

                            </tr>
                        </thead>
                        <tbody id="reviewRoomsBody">

                        </tbody>
                    </table>
                </div>

                <div class="mb-3 mt-3">
                    <label for="reschedReasonInput" class="form-label fw-bold" style="color: #424242;">
                        Reason for Reschedule
                    </label>
                    <textarea id="reschedReasonInput" class="form-control" rows="3"
                        placeholder="Enter reason for reschedule..." required></textarea>
                </div>

                <!-- No Changes Message -->
                <div id="noChangesMessage" class="text-center py-4" style="display:none; color: #757575;">
                    <i class="fas fa-info-circle fa-2x mb-2" style="color: #c9a961;"></i>
                    <p class="mb-0">No changes detected.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 2px solid #e0e0e0; padding: 20px;">
                <button type="button" id="confirmChangesBtn" class="btn btn-success px-4"
                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; font-weight: 500; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-check-circle me-2"></i>Confirm Changes
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

                roomNumberSelect.innerHTML = '';

                const type = roomTypeSelect.value;

                if (data[type] && data[type].length > 0) {

                    if (changedByType) {
                        let opt = document.createElement("option");
                        opt.textContent = "Select room number";
                        opt.disabled = true;
                        opt.selected = true;
                        roomNumberSelect.appendChild(opt);
                    }
                    data[type].forEach(rn => {
                        const option = document.createElement('option');
                        option.value = rn.room_number_id;
                        option.textContent = rn.room_number + (rn.note ? ` (${rn.note})` : '');
                        if (rn.disabled) {
                            option.disabled = true;
                            option.dataset.backendDisabled = "true"; // mark it
                        }

                        if (!changedByType && defaultNumber == rn.room_number_id) {
                            option.selected = true;
                        }
                        roomNumberSelect.appendChild(option);
                    });
                } else {
                    let opt = document.createElement("option");
                    opt.textContent = "No available rooms";
                    opt.disabled = true;
                    opt.selected = true;
                    roomNumberSelect.appendChild(opt);
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
            CasaEstelaAlert.show(
                'warning',
                'Capacity Issue',
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
            roomsTotal += parseFloat(selectedOption.dataset.price) * nights;
        });

        // Extra bed price
        let extraBedPrice = extraBedTotal * nights;

        const totalBeforeDiscount = roomsTotal + extraBedPrice;

        const discountPercentage = parseFloat(document.getElementById('discountPercentage').value.replace('%', '')) || 0;
        const discountAmount = (discountPercentage / 100) * totalBeforeDiscount;

        const totalAmountNew = totalBeforeDiscount - discountAmount;

        const downPayment = parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')) || 0;
        let remainingBalance = totalAmountNew - downPayment;
        if (remainingBalance < 0) remainingBalance = 0;

        document.getElementById('totalAmountNew').value = totalAmountNew.toFixed(2);
        document.getElementById('remainingBal').value = remainingBalance.toFixed(2);
        document.getElementById('discountAmount').value = discountAmount.toFixed(2);
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
        const remainingBal = parseFloat(document.getElementById('remainingBal').value.replace(/,/g, '')) || 0;

        let change = payment - remainingBal;

        document.getElementById('changeAmount').value = "₱" + change.toFixed(2);
    });
</script>

<script>
    document.getElementById('discountType').addEventListener('change', function () {
        let percentage = 0;

        if (this.value === 'PWD' || this.value === 'Senior') {
            percentage = 20;
        }

        document.getElementById('discountPercentage').value = percentage + '%';
        calculateTotalAmount();
    });
</script>


<script>
    const originalRoomNumbersMap = {};
    document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
        const roomNumberSelect = row.querySelector('.roomNumberSelect');
        Array.from(roomNumberSelect.options).forEach(opt => {
            originalRoomNumbersMap[opt.value] = opt.textContent.trim();
        });
    });
    function processBooking(status, reschedReason = null) {
        const roomSelects = document.querySelectorAll('.roomNumberSelect');
        let allSelected = true;

        roomSelects.forEach(select => {
            const selectedOption = select.selectedOptions[0];
            if (!selectedOption || selectedOption.value === "" ||
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
            CasaEstelaAlert.show(
                'warning',
                'Room Selection Required',
                'Please select a room number for all booked rooms.'
            );

            return;
        }
        CasaEstelaModal.confirm(
            'Casa Estela Confirmation',
            `Are you sure you want to ${status === 'checkin' ? 'check in' : 'reschedule'} this booking?
     Please make sure the payment amount is correct.`,
            () => {
                submitBooking(status, reschedReason);
            }
        );
        return;

        const rooms = [];
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            rooms.push({
                id: row.dataset.bookedRoomId,
                room_type_id: parseInt(row.querySelector('.roomTypeSelect').value),
                room_number_fk_id: parseInt(row.querySelector('.roomNumberSelect').value)
            });
        });
        const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;

        const bookingData = {
            booking_id: <?= $booking['booking_id'] ?>,
            check_in: status === 'checkin'
                ? new Date().toLocaleString("sv-SE", { timeZone: "Asia/Manila" }).replace('T', ' ')
                : document.getElementById('check_in').value,
            // check_in: document.getElementById('check_in').value,
            check_out: document.getElementById('check_out').value,

            discount_type: document.getElementById('discountType').value,
            discount_percentage: document.getElementById('discountPercentage').value,
            discount_amount: document.getElementById('discountAmount').value,

            total_amount: parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')),
            payment_input: parseFloat(document.getElementById('paymentInput').value) || 0,
            payment_method: document.querySelector('select[name="payment_method"]').value,
            rooms: rooms,
            status: status,
            resched_reason: reschedReason
        };

        fetch('../Admin/adminBackend/reschedOrCheckin_book_rooms.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.text())
            .then(res => {
                if (res === "success") {
                    CasaEstelaModal.show(
                        'success',
                        'Booking Successful',
                        status === 'checkin'
                            ? 'The guest has been successfully checked in.'
                            : 'The booking has been successfully updated.',
                        () => {
                            window.location.href = "../Admin/index.php?accepted_room_bookings_list";
                        }
                    );
                } else {
                    CasaEstelaModal.show(
                        'error',
                        'Process Failed',
                        'Something went wrong. Please try again.'
                    );
                }
            })

            .catch(err => console.error(err));
    }
    document.getElementById('processReschedBtn').addEventListener('click', () => {

        const roomSelects = document.querySelectorAll('.roomNumberSelect');
        let allSelected = true;
        roomSelects.forEach(select => {
            const selectedOption = select.selectedOptions[0];
            if (!selectedOption || selectedOption.value === "" ||
                selectedOption.text.includes("Select room number") ||
                selectedOption.text.includes("Please choose a room number") ||
                selectedOption.text.includes("No available rooms")) {
                select.classList.add('is-invalid');
                allSelected = false;
            } else select.classList.remove('is-invalid');
        });

        if (!allSelected) {
            CasaEstelaAlert.show(
                'warning',
                'Room Selection Required',
                'Please select a room number for all booked rooms.'
            );

            return;
        }
        const originalCheckInRaw = '<?= date('Y-m-d', strtotime($booking['check_in'])) ?>';
        const originalCheckOutRaw = '<?= date('Y-m-d', strtotime($booking['check_out'])) ?>';
        const newCheckInRaw = document.getElementById('check_in').value;
        const newCheckOutRaw = document.getElementById('check_out').value;

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const originalCheckIn = new Date(originalCheckInRaw).toLocaleDateString('en-US', options);
        const originalCheckOut = new Date(originalCheckOutRaw).toLocaleDateString('en-US', options);
        const newCheckIn = new Date(newCheckInRaw).toLocaleDateString('en-US', options);
        const newCheckOut = new Date(newCheckOutRaw).toLocaleDateString('en-US', options);

        const checkInChanged = originalCheckInRaw !== newCheckInRaw;
        const checkOutChanged = originalCheckOutRaw !== newCheckOutRaw;

        const tbody = document.getElementById('reviewRoomsBody');
        tbody.innerHTML = '';

        let roomChangesExist = false;
        let changesExist = checkInChanged || checkOutChanged;

        const extendedInfo = document.getElementById('extendedInfo');
        const reviewTable = document.getElementById('reviewChangesTable');
        const noChangesMessage = document.getElementById('noChangesMessage');

        noChangesMessage.style.display = "none";
        reviewTable.style.display = "";
        extendedInfo.style.display = changesExist ? "" : "none";

        if (checkInChanged || checkOutChanged) {
            extendedInfo.querySelector('p').textContent =
                `This booking was originally scheduled from ${originalCheckIn} to ${originalCheckOut}, ` +
                `you want to reschedule it to ${newCheckIn} to ${newCheckOut}.`;
        }

        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            const originalType = row.dataset.defaultRoomType;
            const originalNumberId = row.dataset.defaultRoomNumber;
            const selectedType = row.querySelector('.roomTypeSelect').value;
            const roomNumberSelect = row.querySelector('.roomNumberSelect');

            const originalNumberText = originalRoomNumbersMap[originalNumberId] || '-';
            const selectedNumberText = roomNumberSelect.selectedOptions[0].textContent.trim();

            if (originalType !== selectedType || originalNumberId != roomNumberSelect.value) {
                roomChangesExist = true;
                changesExist = true;

                const originalTypeText = row.querySelector(`.roomTypeSelect option[value="${originalType}"]`)?.text || '-';
                const selectedTypeText = row.querySelector(`.roomTypeSelect option[value="${selectedType}"]`)?.text || '-';

                tbody.innerHTML += `
                <tr>
                    <td>${originalTypeText}</td>
                    <td>${originalNumberText}</td>
                    <td>Room Transfer</td>
                    <td>${selectedTypeText}</td>
                    <td>${selectedNumberText}</td>
                </tr>
            `;
            }
        });

        if (!roomChangesExist && !checkInChanged && !checkOutChanged) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No changes made.</td></tr>`;
        }

        if (!changesExist) {
            CasaEstelaModal.confirm(
                'No Changes Detected',
                'No changes were made. Do you still want to proceed?',
                () => {
                    const reason = document.getElementById('reschedReasonInput').value.trim();
                    submitBooking('rescheduled', reason);
                }
            );
            return;

            if (proceed) {
                const reason = document.getElementById('reschedReasonInput').value.trim();
                processBooking('rescheduled', reason);
            }
            return;
        }

        const reviewModal = new bootstrap.Modal(document.getElementById('reviewChangesModal'));
        reviewModal.show();
    });

    document.getElementById('confirmChangesBtn').addEventListener('click', () => {
        const reschedReason = document.getElementById('reschedReasonInput').value.trim();

        if (!reschedReason) {
            CasaEstelaAlert.show(
                'warning',
                'Reason Required',
                'Please enter a reason for reschedule.'
            );

            return;
        }

        processBooking('rescheduled', reschedReason);
    });
    document.getElementById('processCheckinBtn')
        .addEventListener('click', () => processBooking('checkin'));

    function submitBooking(status, reschedReason = null) {
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
            discount_type: document.getElementById('discountType').value,
            discount_percentage: document.getElementById('discountPercentage').value,
            discount_amount: document.getElementById('discountAmount').value,
            total_amount: parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')),

            payment_input: parseFloat(document.getElementById('paymentInput').value) || 0,
            payment_method: document.querySelector('select[name="payment_method"]').value,
            rooms: rooms,
            status: status,
            resched_reason: reschedReason
        };

        fetch('../Admin/adminBackend/reschedOrCheckin_book_rooms.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.text())
            .then(res => {
                if (res === "success") {
                    CasaEstelaModal.show(
                        'success',
                        'Booking Successful',
                        status === 'checkin'
                            ? 'The guest has been successfully checked in.'
                            : 'The booking has been successfully updated.',
                        () => {
                            window.location.href = "../Admin/index.php?accepted_room_bookings_list";
                        }
                    );
                } else {
                    CasaEstelaModal.show(
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
                    'Unable to reach the server. Please try again.'
                );
            });
    }

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
                    'warning',
                    'Invalid Date',
                    'Check-out cannot be earlier than check-in.'
                );

                checkOut.value = checkIn.value;
            }
        });
    });
</script>

<script>
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


<?php include 'adminFrontend/footer.php'; ?>