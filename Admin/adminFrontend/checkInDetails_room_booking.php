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

    .info-card {
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 35px;
    }

    .section-card {
        background: #FAFAFA;
        border-radius: 10px;
        padding: 25px;
        border: 1px solid var(--border-color);
    }

    .section-header {
        border-left: 5px solid var(--gold);
        padding-left: 15px;
        margin-bottom: 25px;
        color: var(--dark-bg);
        font-weight: 700;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header i {
        color: var(--gold);
    }

    .form-control,
    .form-select {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.25rem rgba(201, 169, 97, 0.15);
        outline: none;
    }

    .form-control:read-only {
        background: #F8F9FA;
        color: #6C757D;
        cursor: not-allowed;
    }

    label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    label i {
        color: var(--gold);
        width: 16px;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 0;
    }

    .table-dark {
        background: var(--dark-bg);
        color: white;
    }

    .table-dark th {
        border: none;
        padding: 16px;
        font-weight: 600;
        font-size: 14px;
    }

    .table-dark th i {
        color: var(--gold);
        margin-right: 6px;
    }

    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: var(--border-color);
        font-size: 14px;
    }

    .table-hover tbody tr:hover {
        background: #F0F0F0;
        transition: background 0.2s ease;
    }

    .btn {
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn i {
        font-size: 16px;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success) 0%, #218838 100%);
        color: white;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning) 0%, #E0A800 100%);
        color: var(--dark-bg);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #E0A800 0%, #C69500 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
    }

    .btn-secondary {
        background: var(--secondary);
        color: white;
    }

    .btn-secondary:hover {
        background: #5A6268;
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .amenities-section {
        padding: 15px;
        background: white;
        border-radius: 8px;
        border: 2px dashed var(--gold);
    }

    .payment-summary {
        background: white;
        padding: 20px;
        border-radius: 10px;
        border: 2px solid var(--gold);
    }

    .payment-card {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-top: 25px;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .payment-card label {
        color: white;
        font-weight: 600;
    }

    .payment-card label i {
        color: white;
    }

    .payment-card .form-control {
        background: rgba(255, 255, 255, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.4);
        color: white;
        font-weight: 600;
    }

    .payment-card .form-control::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .payment-card .form-control:focus {
        background: rgba(255, 255, 255, 0.35);
        border-color: white;
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.2);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 3px solid var(--gold);
    }

    .badge {
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-info {
        background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
        color: white;
    }

    .text-danger-emphasis {
        color: var(--danger) !important;
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

        .info-card {
            padding: 20px;
        }

        .section-card {
            padding: 15px;
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
<div class="main-content" id="mainContent">
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
                        $items[] = $row['amenity_name'] . " x" . $row['quantity'] . " (₱" . number_format($row['price'], 2) . ")";
                        if (strtolower($row['bedOrNot'] ?? '') === 'yes') {
                            $extraBedTotal += $row['quantity'] * $row['price'];
                        }
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

                if ($resched_res->num_rows > 0):
                    $newCI = date("F j, Y", strtotime($booking['check_in']));
                    $newCO = date("F j, Y", strtotime($booking['check_out']));
                    ?>
                    <div class="amenities-section mt-3">
                        <label><i class="fas fa-concierge-bell"></i> Reschedule Details</label>
                        <textarea class="form-control" rows="2" readonly>
                            <?php
                            while ($r = $resched_res->fetch_assoc()) {
                                $oldCI = date("F j, Y", strtotime($r['check_in']));
                                $oldCO = date("F j, Y", strtotime($r['check_out']));
                                $dateRes = date("F j, Y g:i A", strtotime($r['date_resched']));
                                $reason = $r['reason'];

                                echo "On $dateRes, the guest requested a reschedule, changing the stay from $oldCI - $oldCO to $newCI - $newCO due to the reason: \"$reason\".\n";
                            }
                            ?>
                        </textarea>
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
                                placeholder="Enter payment amount">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><i class="fas fa-coins"></i> Change</label>
                            <input type="text" id="changeAmount" class="form-control" value="₱0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" id="processExtendStay" class="btn btn-success">
                    <i class="fas fa-calendar-plus"></i> Extend Stay/ Transfer Room
                </button>
                <button type="button" id="processCheckOut" class="btn btn-warning">
                    <i class="fas fa-sign-out-alt"></i> Process Check-out
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
                            <h6 class="fw-bold mb-1" style="color: #1565c0;">Extension Details</h6>
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
                            <!-- Dynamic content will be inserted here -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-3 mt-3">
                    <label for="roomTransferReason" class="form-label fw-bold" style="color: #424242;">
                        Reason for Reschedule
                    </label>
                    <textarea id="roomTransferReason" class="form-control" rows="3"
                        placeholder="Enter reason for transfer room..." required></textarea>
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
        const remainingBal = parseFloat(document.getElementById('remainingBal').value.replace(/,/g, '')) || 0;

        let change = payment - remainingBal;

        document.getElementById('changeAmount').value = "₱" + change.toFixed(2);
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
            alert('Please select a room number for all booked rooms.');
            return;
        }

        const confirmed = confirm(`Are you sure you want to ${status === 'finished' ? 'finish' : 'extend/transfer'} this booking?`);
        if (!confirmed) return;

        const rooms = [];
        document.querySelectorAll('#roomsTable tbody tr').forEach(row => {
            const roomTypeSelect = row.querySelector('.roomTypeSelect');
            const roomNumberSelect = row.querySelector('.roomNumberSelect');

            rooms.push({
                id: row.dataset.bookedRoomId, // booked_rooms.id
                room_type_id: parseInt(roomTypeSelect.value),
                room_number_fk_id: parseInt(roomNumberSelect.value),
                // send original values for backend to detect changes
                original_room_type_id: parseInt(row.dataset.defaultRoomType),
                original_room_number_fk_id: parseInt(row.dataset.defaultRoomNumber),
                // optional: for backend insert display info
                room_type_name: roomTypeSelect.selectedOptions[0].textContent.trim(),
                room_number_text: roomNumberSelect.selectedOptions[0].textContent.trim(),
                price: parseFloat(row.dataset.price) || 0
            });
        });

        const bookingData = {
            booking_id: <?= $booking['booking_id'] ?>,
            check_in: document.getElementById('check_in').value,
            check_out: status === 'finished'
                ? new Date().toLocaleString("sv-SE", { timeZone: "Asia/Manila" }).replace('T', ' ')
                : document.getElementById('check_out').value,
            total_amount: parseFloat(document.getElementById('totalAmountNew').value.replace(/,/g, '')),
            payment_input: parseFloat(document.getElementById('paymentInput').value) || 0,
            payment_method: document.querySelector('select[name="payment_method"]').value,
            status: status,
            rooms: rooms,
            resched_reason: reschedReason
        };

        fetch('../Admin/adminBackend/update_extendeOrCheckoutRoom_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
            .then(res => res.text())
            .then(res => {
                if (res === "success") {
                    alert(`Booking ${status === 'finished' ? 'checked out' : 'updated'} successfully!`);
                    window.location.href = "../Admin/index.php?room_booking_list";
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(err => console.error(err));
    }

    document.getElementById('processExtendStay').addEventListener('click', () => {

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
            alert('Please select a room number for all booked rooms.');
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
                `you want to extend it to ${newCheckOut}?`;
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
            const proceed = confirm("No changes detected. Do you still want to proceed?");
            if (proceed) {
                const reason = document.getElementById('roomTransferReason').value.trim();
                processBooking('checkin', reason);
            }
            return;
        }

        const reviewModal = new bootstrap.Modal(document.getElementById('reviewChangesModal'));
        reviewModal.show();
    });

    document.getElementById('confirmChangesBtn').addEventListener('click', () => {
        const transferReason = document.getElementById('roomTransferReason').value.trim();

        if (!transferReason) {
            alert("Please enter a reason for room transfer.");
            return;
        }

        processBooking('checkin', transferReason);
    });

    document.getElementById('processCheckOut')
        .addEventListener('click', () => processBooking('finished'));
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