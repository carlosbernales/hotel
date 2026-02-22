<?php
if (
    !isset($_SESSION['user_type']) ||
    ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'frontdesk')
) {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
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
///////////////////
$amenitiesQuery = "SELECT id, item_type, price FROM beds ORDER BY item_type ASC";
$amenitiesResult = $conn->query($amenitiesQuery);

$amenities = [];
if ($amenitiesResult->num_rows > 0) {
    while ($row = $amenitiesResult->fetch_assoc()) {
        $amenities[] = $row;
    }
}
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/checkInDetails_room_booking.css">

<div class="breadcrumb-custom d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-home"> Booking Details</i>
    </div>
</div>

<div class="info-card" style="margin-bottom: 40px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 fw-bold" style="color: var(--dark-bg);">
            <i class="fas fa-check-circle" style="color: var(--gold);"></i> Checked In Booking Details
        </h3>

        <div class="d-flex gap-2">
            <a href="#" id="backProcess" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>

            <a href="#" class="btn btn-gold btn-sm add-amenities-btn" data-id="<?= $booking['booking_id'] ?>"
                data-bs-toggle="modal" data-bs-target="#addAmenitiesModal">
                <i class="fas fa-plus"></i> Add Amenities
            </a>
        </div>
    </div>


    <div class="modal fade" id="addAmenitiesModal" tabindex="-1" aria-labelledby="addAmenitiesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="addAmenitiesModalLabel"><i class="fas fa-bed"></i> Manage Amenities
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="section-card mb-4 p-3">
                        <p class="mb-0">
                            <strong>Check-in:</strong>
                            <span id="checkInDate" class="fw-semibold">
                                <?= date("F j, Y", strtotime($booking['check_in'])) ?>
                            </span><br>

                            <strong>Check-out:</strong>
                            <span id="checkOutDate" class="fw-semibold">
                                <?= date("F j, Y", strtotime($booking['check_out'])) ?>
                            </span><br>

                            <strong>Total Nights:</strong>
                            <span id="numNights" class="badge bg-secondary"><?= $booking['nights'] ?></span>
                        </p>
                    </div>


                    <form id="amenitiesForm">
                        <div class="mb-3">
                            <label for="amenitySelect" class="form-label"><i class="fas fa-mattress-pillow"></i> Select
                                Bed Type</label>
                            <select class="form-select" id="amenitySelect" name="amenity_id">
                                <option value="">-- Choose a Bed Type --</option>
                                <?php foreach ($amenities as $amenity): ?>
                                    <option value="<?= $amenity['id'] ?>" data-price="<?= $amenity['price'] ?>">
                                        <?= $amenity['item_type'] ?> (₱<?= number_format($amenity['price'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>

                    <h6 class="section-header-small mt-4 mb-3"><i class="fas fa-clipboard-list"></i> Selected Beds &
                        Charges</h6>

                    <table class="table table-bordered table-hover mt-3" id="selectedAmenitiesTable">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-bed"></i> Bed Type</th>
                                <th><i class="fas fa-tags"></i> Price per Night</th>
                                <th><i class="fas fa-cubes"></i> Quantity</th>
                                <th><i class="fas fa-money-bill-wave"></i> Total Amount</th>
                                <th><i class="fas fa-cogs"></i> Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div id="noAmenitiesMessage" class="text-center text-muted p-3"
                                        style="display:block;">
                                        No beds have been added yet.
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th id="subtotal" class="fw-bold text-success">₱0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-gold" id="saveAmenitiesBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
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
                        value="<?= date('Y-m-d', strtotime($booking['check_in'])) ?>" disabled>
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

                        $prevCI = $newCI;
                        $prevCO = $newCO;
                    }
                    ?></textarea>
                </div>
            <?php endif; ?>
        </div>

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
                    <label><i class="fas fa-percent"></i> Discount Type</label>
                    <input class="form-control" value="<?= $booking['discount_type'] ?>" readonly>
                </div>

                <div class=" info-item">
                    <label><i class="fas fa-percent"></i> Discount Applied</label>
                    <input class="form-control" id="discountPercentage"
                        value="<?= (int) $booking['discount_percentage'] ?>%" readonly>
                </div>
                <div class="info-item">
                    <label><i class="fas fa-money-bill-wave"></i> Discount Amount</label>
                    <input class="form-control" id="discountAmount" value="0" readonly>

                    <input type="hidden" id="discount_amount" value="<?= $booking['discount_amount'] ?>">
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
                        <label><i class="fas fa-wallet"></i> Payment Amount</label>
                        <input type="number" id="paymentInput" class="form-control" min="0"
                            placeholder="Enter payment amount">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><i class="fas fa-coins"></i> Change</label>
                        <input type="text" id="changeAmount" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button type="button" id="processTransferRoom" class="btn btn-success">
                <i class="fas fa-exchange-alt"></i> Transfer Room
            </button>

            <button type="button" id="processExtendStay" class="btn btn-success">
                <i class="fas fa-calendar-plus"></i> Extend Stay
            </button>

            <button type="button" id="processCheckOut" class="btn btn-warning">
                <i class="fas fa-sign-out-alt"></i> Process Check-out
            </button>
        </div>


    </div>
</div>


<!-- Transfer Room Modal -->
<div class="modal fade" id="reviewTransferModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Original Type</th>
                                <th>Original #</th>
                                <th>Status</th>
                                <th>New Type</th>
                                <th>New #</th>
                            </tr>
                        </thead>
                        <tbody id="reviewRoomsBodyTransfer"></tbody>
                    </table>
                </div>

                <div class="mb-3 mt-3">
                    <label>Reason for Transfer Room</label>
                    <textarea id="roomTransferReason" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button id="confirmTransferBtn" class="btn btn-success">Confirm Transfer</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="extendStayModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header" style="background: linear-gradient(135deg,#1a1a1a,#2d2d2d);
                       border-bottom:3px solid #c9a961;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calendar-plus me-2" style="color:#c9a961;"></i>
                    Extend Stay Review
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="background:#f8f9fa; padding:25px;">

                <!-- DATE CHANGE INFO -->
                <div id="extendInfoBox" class="alert alert-info border-0 shadow-sm mb-4"
                    style="background:linear-gradient(135deg,#e3f2fd,#bbdefb); display:none;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-calendar-alt me-3 mt-1" style="color:#1976d2; font-size:1.2rem;"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#1565c0;">Extension Details</h6>
                            <p id="extendInfoText" class="mb-0"></p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button id="confirmExtendBtn" class="btn btn-success px-4">
                    <i class="fas fa-check-circle me-2"></i>Confirm Extension
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
                            option.dataset.backendDisabled = "true";
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

        const originalCheckIn = new Date('<?= $booking['check_in'] ?>');
        const originalCheckOut = new Date('<?= $booking['check_out'] ?>');

        const newCheckIn = new Date(document.getElementById('check_in').value);
        const newCheckOut = new Date(document.getElementById('check_out').value);

        if (isNaN(newCheckIn) || isNaN(newCheckOut) || newCheckOut <= newCheckIn) return;

        const originalNights = Math.ceil((originalCheckOut - originalCheckIn) / (1000 * 60 * 60 * 24));
        const newNights = Math.ceil((newCheckOut - newCheckIn) / (1000 * 60 * 60 * 24));

        let originalTotal = parseFloat('<?= $booking['total_amount'] ?>') || 0;
        let extensionNights = newNights - originalNights;

        let extensionTotal = 0;

        if (extensionNights > 0) {
            document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
                const selectedOption = row.querySelector('.roomTypeSelect').selectedOptions[0];
                if (!selectedOption) return;
                const newPrice = parseFloat(selectedOption.dataset.price) || 0;
                extensionTotal += newPrice * extensionNights;
            });
        }

        // extraBedTotal fallback to 0 if not defined
        let extraBedTotal = parseFloat(document.getElementById('extraBedTotal')?.value) || 0;
        let extraBedPrice = extraBedTotal * newNights;

        let totalBeforeDiscount = originalTotal + extensionTotal + extraBedPrice;

        const discountPercentage = parseFloat(
            document.getElementById('discountPercentage')?.value.replace('%', '')
        ) || 0;

        const discountAmount = (discountPercentage / 100) * totalBeforeDiscount;

        const finalTotal = totalBeforeDiscount - discountAmount;

        const downPayment = parseFloat(
            document.getElementById('downPayment')?.value.replace(/,/g, '')
        ) || 0;

        let remainingBalance = finalTotal - downPayment;
        if (remainingBalance < 0) remainingBalance = 0;

        document.getElementById('totalAmountNew').value = finalTotal.toFixed(2);
        document.getElementById('remainingBal').value = remainingBalance.toFixed(2);
        document.getElementById('discountAmount').value = discountAmount.toFixed(2);

        // DEBUG: check computed values in console
        console.log({
            originalTotal,
            extensionTotal,
            extraBedPrice,
            totalBeforeDiscount,
            discountPercentage,
            discountAmount,
            finalTotal,
            downPayment,
            remainingBalance,
            originalNights,
            newNights,
            extensionNights
        });
    }

    // Add listeners
    document.addEventListener('DOMContentLoaded', () => {
        calculateTotalAmount();

        document.getElementById('check_in').addEventListener('change', calculateTotalAmount);
        document.getElementById('check_out').addEventListener('change', calculateTotalAmount);
        document.querySelectorAll('.roomTypeSelect').forEach(select => {
            select.addEventListener('change', calculateTotalAmount);
        });
        document.getElementById('extraBedTotal')?.addEventListener('input', calculateTotalAmount);
        document.getElementById('discountPercentage')?.addEventListener('input', calculateTotalAmount);
        document.getElementById('downPayment')?.addEventListener('input', calculateTotalAmount);
    });

    document.getElementById('paymentInput').addEventListener('input', function () {

        const payment = parseFloat(this.value) || 0;
        const remainingBal = parseFloat(
            document.getElementById('remainingBal').value.replace(/,/g, '')
        ) || 0;

        let change = payment - remainingBal;

        document.getElementById('changeAmount').value = change.toFixed(2);
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

    function processBooking(status, reason = null, silent = false) {
        if (status === 'finished') {
            const payment = parseFloat(document.getElementById('paymentInput').value) || 0;
            const remainingBal = parseFloat(document.getElementById('remainingBal').value.replace(/,/g, '')) || 0;

            if (payment < remainingBal) {
                alert("Payment amount is not enough.");
                return;
            }
        }

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
            } else {
                select.classList.remove('is-invalid');
            }
        });

        if (!allSelected) {
            alert('Please select a room number for all rooms.');
            return;
        }

        if (status === 'finished') {
            const confirmed = confirm('Are you sure you want to check out this booking?');
            if (!confirmed) return;
        }

        const rooms = [];
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            rooms.push({
                id: row.dataset.bookedRoomId,
                room_type_id: parseInt(row.querySelector('.roomTypeSelect').value),
                room_number_fk_id: parseInt(row.querySelector('.roomNumberSelect').value),
                original_room_type_id: parseInt(row.dataset.defaultRoomType),
                original_room_number_fk_id: parseInt(row.dataset.defaultRoomNumber),
                room_type_name: row.querySelector('.roomTypeSelect').selectedOptions[0].textContent.trim(),
                room_number_text: row.querySelector('.roomNumberSelect').selectedOptions[0].textContent.trim(),
                price: parseFloat(row.dataset.price) || 0
            });
        });

        const bookingData = {
            booking_id: <?= $booking['booking_id'] ?>,
            check_in: document.getElementById('check_in').value,
            //check_out: document.getElementById('check_out').value,
            check_out: status === 'finished'
                ? new Date().toLocaleString("sv-SE", { timeZone: "Asia/Manila" }).replace('T', ' ')
                : document.getElementById('check_out').value,

            down_payment: parseFloat(document.getElementById('downPayment').value.replace(/,/g, '')),
            total_amount: parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')),
            payment_input: parseFloat(document.getElementById('paymentInput').value) || 0,

            change_amount: parseFloat(document.getElementById('changeAmount').value) || 0,
            payment_method: document.querySelector('select[name="payment_method"]').value,
            status: status,
            rooms: rooms,
            resched_reason: reason,
            discount_amount: parseFloat(document.getElementById('discountAmount').value) || 0
        };

        fetch('../Admin/adminBackend/update_extendeOrCheckoutRoom_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.json())   // ✅ parse JSON properly
            .then(res => {

                console.log("Server response:", res);

                if (res.success) {

                    if (!silent) {
                        alert(`Booking ${status === 'finished' ? 'checked out' : 'updated'} successfully!`);
                    }

                    if (status === 'finished') {
                        window.location.href =
                            `../Admin/index.php?room_booking_receipt&booking_id=<?= $booking['booking_id'] ?>`;
                    } else {
                        window.location.href = "../Admin/index.php?room_booking_list";
                    }

                } else {
                    alert('Something went wrong. Please try again.');
                    console.log(res.error);
                }
            })
            .catch(err => {
                console.error("Fetch error:", err);
                alert("Something went wrong. Please try again.");
            });
    }

    //////   PROCESS EXTEND STAY → EXTEND MODAL ONLY
    document.getElementById('processExtendStay').addEventListener('click', () => {

        const originalCheckIn = '<?= date('Y-m-d', strtotime($booking['check_in'])) ?>';
        const originalCheckOut = '<?= date('Y-m-d', strtotime($booking['check_out'])) ?>';
        const newCheckIn = document.getElementById('check_in').value;
        const newCheckOut = document.getElementById('check_out').value;

        const extendChanged = originalCheckOut !== newCheckOut || originalCheckIn !== newCheckIn;

        const extendInfo = document.getElementById("extendInfoBox");
        const extendMessage = document.getElementById("extendInfoText");

        if (extendChanged) {
            extendInfo.style.display = "block";

            const originalCheckInFormatted = originalCheckIn
                ? new Date(originalCheckIn).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                : "N/A";
            const originalCheckOutFormatted = originalCheckOut
                ? new Date(originalCheckOut).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                : "N/A";
            const newCheckOutFormatted = newCheckOut
                ? new Date(newCheckOut).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                : "N/A";

            extendMessage.textContent =
                `Originally from ${originalCheckInFormatted} to ${originalCheckOutFormatted}. You want to extend until ${newCheckOutFormatted}.`;
        } else {
            extendInfo.style.display = "block"; // show the section even if no changes
            extendMessage.textContent = "No changes for extension.";
        }
        new bootstrap.Modal(document.getElementById('extendStayModal')).show();
    });
    ///// PROCESS TRANSFER ROOM → TRANSFER MODAL ONLY
    document.getElementById('processTransferRoom').addEventListener('click', () => {
        const tbody = document.getElementById('reviewRoomsBodyTransfer');
        tbody.innerHTML = "";

        let roomChanges = false;

        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            const originalType = row.dataset.defaultRoomType;
            const originalNumber = row.dataset.defaultRoomNumber;

            const newType = row.querySelector('.roomTypeSelect').value;
            const newNumber = row.querySelector('.roomNumberSelect').value;

            if (originalType !== newType || originalNumber !== newNumber) {
                roomChanges = true;

                const originalTypeText =
                    row.querySelector(`.roomTypeSelect option[value="${originalType}"]`)?.text || '-';

                const newTypeText =
                    row.querySelector(`.roomTypeSelect option[value="${newType}"]`)?.text || '-';

                const originalNumText = originalRoomNumbersMap[originalNumber] || '-';
                const newNumText =
                    row.querySelector('.roomNumberSelect').selectedOptions[0].textContent.trim();

                tbody.innerHTML += `
                <tr>
                    <td>${originalTypeText}</td>
                    <td>${originalNumText}</td>
                    <td>Room Transfer</td>
                    <td>${newTypeText}</td>
                    <td>${newNumText}</td>
                </tr>
            `;
            }
        });

        if (!roomChanges) {
            tbody.innerHTML =
                `<tr><td colspan="5" class="text-center text-muted">No room changes detected.</td></tr>`;
        }
        new bootstrap.Modal(document.getElementById('reviewTransferModal')).show();
    });
    /////CONFIRM EXTEND STAY
    document.getElementById('confirmExtendBtn').addEventListener('click', () => {
        const extendMessage = document.getElementById("extendInfoText");

        if (extendMessage.textContent === "No changes for extension.") {
            alert("No changes detected for extension. Nothing to process.");
            return;
        }

        let hasRoomChanges = false;
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            const originalType = row.dataset.defaultRoomType;
            const originalNumber = row.dataset.defaultRoomNumber;
            const newType = row.querySelector('.roomTypeSelect').value;
            const newNumber = row.querySelector('.roomNumberSelect').value;

            if (originalType !== newType || originalNumber !== newNumber) {
                hasRoomChanges = true;
            }
        });

        if (hasRoomChanges) {
            alert("You have changed room types/numbers. Please click the Transfer Room button instead for reasons.");
            return;
        }

        processBooking('checkin', null);
    });
    document.getElementById('confirmTransferBtn').addEventListener('click', () => {

        let hasRoomChange = false;
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            if (
                row.dataset.defaultRoomType !== row.querySelector('.roomTypeSelect').value ||
                row.dataset.defaultRoomNumber !== row.querySelector('.roomNumberSelect').value
            ) {
                hasRoomChange = true;
            }
        });
        const reason = document.getElementById('roomTransferReason').value.trim();

        if (!hasRoomChange && !reason) {
            alert("No room changes detected and reason is required. Transfer cannot be processed.");
            return;
        }
        if (hasRoomChange && !reason) {
            alert("Please enter a reason for room transfer.");
            return;
        }
        processBooking('checkin', reason);
    });

    document.getElementById('processCheckOut')
        .addEventListener('click', () => processBooking('finished'));

    document.getElementById('backProcess')
        .addEventListener('click', (e) => {
            e.preventDefault();
            processBooking('checkin', null, true);
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

<script>
    const amenitySelect = document.getElementById('amenitySelect');
    const amenitiesTableBody = document.querySelector('#selectedAmenitiesTable tbody');
    const amenitiesTable = document.getElementById('selectedAmenitiesTable');
    const noAmenitiesMessage = document.getElementById('noAmenitiesMessage');

    let CURRENT_BOOKING_ID = null;

    function updateNights() {
        const checkIn = new Date('<?= $booking['check_in'] ?>');
        const checkOut = new Date('<?= $booking['check_out'] ?>');
        const diffTime = checkOut - checkIn;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        document.getElementById('numNights').textContent = diffDays;
        return diffDays;
    }


    function addAmenityRow(id, name, price, quantity = 1) {
        const nights = updateNights();

        const existingRow = amenitiesTableBody.querySelector(`tr[data-id="${id}"]`);
        if (existingRow) {
            const qtyInput = existingRow.querySelector('.quantity');
            qtyInput.value = parseInt(qtyInput.value) + quantity;

            const totalCell = existingRow.querySelector('.total');
            totalCell.textContent = '₱' + (price * qtyInput.value * nights).toFixed(2);

            updateSubtotal();
            return;
        }

        const row = document.createElement('tr');
        row.setAttribute('data-id', id);

        row.innerHTML = `
            <td>${name}</td>
            <td>₱${parseFloat(price).toFixed(2)}</td>

            <td>
                <input type="number" class="form-control quantity" value="${quantity}"
                min="1" style="width:80px;">
            </td>

            <td class="total">₱${(price * quantity * nights).toFixed(2)}</td>

            <td>
                <button class="btn btn-danger btn-sm remove-amenity">&times;</button>
            </td>
        `;

        amenitiesTableBody.appendChild(row);

        row.querySelector('.quantity').addEventListener('input', (e) => {
            let qty = parseInt(e.target.value);
            if (qty < 1) qty = 1;

            row.querySelector('.total').textContent =
                '₱' + (price * qty * nights).toFixed(2);

            updateSubtotal();
        });

        row.querySelector('.remove-amenity').addEventListener('click', () => {
            row.remove();
            checkNoAmenities();
            updateSubtotal();
        });

        updateSubtotal();
    }


    function updateSubtotal() {
        const nights = updateNights();
        let subtotal = 0;

        amenitiesTableBody.querySelectorAll('tr').forEach(row => {
            const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('₱', '')) || 0;
            const qty = parseInt(row.querySelector('.quantity').value) || 0;
            subtotal += price * qty * nights;
        });

        document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
    }


    function checkNoAmenities() {
        if (amenitiesTableBody.querySelectorAll('tr').length === 0) {
            noAmenitiesMessage.style.display = 'block';
            amenitiesTable.style.display = 'none';
        } else {
            noAmenitiesMessage.style.display = 'none';
            amenitiesTable.style.display = 'table';
        }
    }


    amenitySelect.addEventListener('change', () => {
        const opt = amenitySelect.selectedOptions[0];
        if (!opt.value) return;

        const id = opt.value;
        const name = opt.text.split(" (₱")[0];
        const price = parseFloat(opt.dataset.price);

        addAmenityRow(id, name, price);

        amenitySelect.value = '';
        checkNoAmenities();
    });

    document.querySelectorAll(".add-amenities-btn").forEach(btn => {
        btn.addEventListener("click", () => {

            CURRENT_BOOKING_ID = btn.getAttribute("data-id");

            amenitiesTableBody.innerHTML = '';
            checkNoAmenities();
            updateNights();

            fetch(`../Admin/adminBackend/get_booking_amenities.php?booking_id=${CURRENT_BOOKING_ID}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        checkNoAmenities();
                    } else {
                        amenitiesTable.style.display = 'table';
                        noAmenitiesMessage.style.display = 'none';

                        data.forEach(item => {
                            addAmenityRow(
                                item.amenities_fk_id,
                                item.amenity_name,
                                parseFloat(item.price),
                                parseInt(item.quantity)
                            );
                        });
                    }
                });
        });
    });

    document.getElementById("saveAmenitiesBtn").addEventListener("click", () => {

        const items = [];

        amenitiesTableBody.querySelectorAll("tr").forEach(row => {
            items.push({
                amenity_id: row.getAttribute("data-id"),
                quantity: row.querySelector(".quantity").value
            });
        });

        fetch("../Admin/adminBackend/booking_add_amenities.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                booking_id: CURRENT_BOOKING_ID,
                items: items
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert("Error saving amenities");
                }
            });
    });
</script>


<?php include 'adminFrontend/footer.php'; ?>