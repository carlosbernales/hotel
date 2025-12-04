<style>
    /* Modal Professional Styling */
    #addAmenitiesModal .modal-dialog {
        max-width: 900px;
    }

    #addAmenitiesModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    #addAmenitiesModal .modal-header {
        background: linear-gradient(135deg, #d4a574 0%, #c89858 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-bottom: none;
    }

    #addAmenitiesModal .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    #addAmenitiesModal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    #addAmenitiesModal .btn-close:hover {
        opacity: 1;
    }

    #addAmenitiesModal .modal-body {
        padding: 2rem;
        background-color: #f8f9fa;
    }

    /* Booking Info Card */
    #addAmenitiesModal .mb-3 p {
        background: white;
        padding: 1.25rem;
        border-radius: 8px;
        border-left: 4px solid #d4a574;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        line-height: 1.8;
    }

    #addAmenitiesModal .mb-3 p strong {
        color: #2c3e50;
        min-width: 100px;
        display: inline-block;
    }

    #addAmenitiesModal .mb-3 p span {
        color: #555;
    }

    /* Form Styling */
    #addAmenitiesModal .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    #addAmenitiesModal .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    #addAmenitiesModal .form-select:focus {
        border-color: #d4a574;
        box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
        outline: none;
    }

    /* Table Styling */
    #addAmenitiesModal #selectedAmenitiesTable {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: none;
        margin-top: 1.5rem;
    }

    #addAmenitiesModal #selectedAmenitiesTable thead {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }

    #addAmenitiesModal #selectedAmenitiesTable thead th {
        padding: 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #2c3e50;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody tr:last-child td {
        border-bottom: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody tr:hover {
        background-color: #f8f9fa;
    }

    #addAmenitiesModal #selectedAmenitiesTable tfoot {
        background: #f8f9fa;
        border-top: 2px solid #d4a574;
    }

    #addAmenitiesModal #selectedAmenitiesTable tfoot th {
        padding: 1.25rem 1rem;
        font-size: 1.1rem;
        color: #2c3e50;
        border: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable #subtotal {
        color: #d4a574;
        font-weight: 700;
        font-size: 1.2rem;
    }

    /* Quantity Input */
    #addAmenitiesModal .quantity {
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 0.5rem;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #addAmenitiesModal .quantity:focus {
        border-color: #d4a574;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
    }

    /* Remove Button */
    #addAmenitiesModal .remove-amenity {
        background: #e74c3c;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #addAmenitiesModal .remove-amenity:hover {
        background: #c0392b;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
    }

    /* No Amenities Message */
    #addAmenitiesModal #noAmenitiesMessage {
        padding: 3rem 1rem;
        background: white;
        border-radius: 8px;
        color: #95a5a6;
        font-size: 1.1rem;
        font-style: italic;
    }

    /* Modal Footer */
    #addAmenitiesModal .modal-footer {
        padding: 1.5rem 2rem;
        background: white;
        border-top: 1px solid #e0e0e0;
    }

    #addAmenitiesModal .modal-footer .btn {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    #addAmenitiesModal .modal-footer .btn-secondary {
        background: #95a5a6;
        border: none;
    }

    #addAmenitiesModal .modal-footer .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(149, 165, 166, 0.3);
    }

    #addAmenitiesModal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #d4a574 0%, #c89858 100%);
        border: none;
    }

    #addAmenitiesModal .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #c89858 0%, #b8874a 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(212, 165, 116, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #addAmenitiesModal .modal-dialog {
            margin: 0.5rem;
        }

        #addAmenitiesModal .modal-body {
            padding: 1.25rem;
        }

        #addAmenitiesModal #selectedAmenitiesTable {
            font-size: 0.85rem;
        }

        #addAmenitiesModal #selectedAmenitiesTable thead th,
        #addAmenitiesModal #selectedAmenitiesTable tbody td {
            padding: 0.75rem 0.5rem;
        }

        #addAmenitiesModal .modal-footer .btn {
            padding: 0.6rem 1.5rem;
            font-size: 0.85rem;
        }
    }
</style>

<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$bookings = [];

$sqlBookings = "SELECT * FROM bookings WHERE status = 'checkin'";
$resBookings = $conn->query($sqlBookings);

while ($row = $resBookings->fetch_assoc()) {
    $id = $row['booking_id'];
    $bookings[$id] = [
        "booking" => $row,
        "rooms" => [],
        "guests" => []
    ];
}

$sqlRooms = "
    SELECT 
        br.booking_id,
        br.room_type_id,
        br.room_type_name,
        br.price,
        br.room_number_fk_id,
        rn.room_number,
        rn.floor_number
    FROM booked_rooms br
    LEFT JOIN room_numbers rn 
        ON br.room_number_fk_id = rn.room_number_id
";
$resRooms = $conn->query($sqlRooms);

while ($r = $resRooms->fetch_assoc()) {
    $id = $r['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['rooms'][] = $r;
    }
}

$sqlGuests = "
    SELECT *
    FROM guest_names
";
$resGuests = $conn->query($sqlGuests);

while ($g = $resGuests->fetch_assoc()) {
    $id = $g['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['guests'][] = $g;
    }
}

$bedsQuery = "SELECT id, item_type, price FROM beds ORDER BY item_type ASC";
$bedsResult = $conn->query($bedsQuery);

$beds = [];
if ($bedsResult->num_rows > 0) {
    while ($row = $bedsResult->fetch_assoc()) {
        $beds[] = $row;
    }
}

?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Checked In Bookings</i>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Fullname</th>
                        <th>Contact</th>
                        <th>Schedule</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $id => $data): ?>
                        <?php $b = $data['booking']; ?>
                        <tr>
                            <td><?= $b['booking_reference'] ?></td>
                            <td><?= $b['first_name'] . ' ' . $b['last_name'] ?></td>
                            <td><?= $b['contact'] ?></td>
                            <td>
                                <?= date("F j, Y", strtotime($b['check_in'])) ?> -
                                <?= date("F j, Y", strtotime($b['check_out'])) ?>
                            </td>
                            <td>
                                <!-- Dropdown Button -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton<?= $id ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $id ?>">
                                        <li>
                                            <a class="dropdown-item"
                                                href="../Admin/index.php?checkInDetails_room_booking&id=<?= $id ?>">
                                                Modify
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item add-amenities-btn" href="#" data-id="<?= $id ?>"
                                                data-bs-toggle="modal" data-bs-target="#addAmenitiesModal">
                                                Add Amenities
                                            </a>
                                        </li>
                                    </ul>

                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#viewModal<?= $id ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>





            <?php
            function getRoomInfo($conn, $room_number_fk_id)
            {
                if (!$room_number_fk_id)
                    return null;
                $sql = "SELECT room_number, floor_number FROM room_numbers WHERE room_number_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $room_number_fk_id);
                $stmt->execute();
                $res = $stmt->get_result();
                return $res->num_rows > 0 ? $res->fetch_assoc() : null;
            }
            ?>

            <?php foreach ($bookings as $id => $data): ?>
                <?php $b = $data['booking']; ?>

                <?php
                // Fetch transfer rooms for this booking
                $transfers_res = null;
                if (!empty($b['booking_id'])) {
                    $stmt = $conn->prepare("SELECT * FROM room_transfers WHERE bookings_fk_id = ? ORDER BY transfer_date ASC");
                    $stmt->bind_param("i", $b['booking_id']);
                    $stmt->execute();
                    $transfers_res = $stmt->get_result();
                }
                ?>

                <!-- MODAL HERE -->
                <div class="modal fade" id="viewModal<?= $id ?>" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-file-text"></i> Booking Details – <?= $b['booking_reference'] ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4">

                                <!-- GUEST INFORMATION -->
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-header bg-dark text-warning">
                                        <h6 class="mb-0"><i class="bi bi-person-fill"></i> Guest Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="text-muted small">First Name</label>
                                                <p class="mb-0 fw-semibold"><?= $b['first_name'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Last Name</label>
                                                <p class="mb-0 fw-semibold"><?= $b['last_name'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Email</label>
                                                <p class="mb-0 fw-semibold"><?= $b['email'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Contact</label>
                                                <p class="mb-0 fw-semibold"><?= $b['contact'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOOKING DETAILS -->
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-header bg-dark text-warning">
                                        <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Booking Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="text-muted small">Check-in</label>
                                                <p class="mb-0 fw-semibold"><?= date('M d, Y', strtotime($b['check_in'])) ?>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Check-out</label>
                                                <p class="mb-0 fw-semibold">
                                                    <?= date('M d, Y', strtotime($b['check_out'])) ?>
                                                </p>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="text-muted small">Nights</label>
                                                <p class="mb-0 fw-semibold"><?= $b['nights'] ?></p>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="text-muted small">Adults</label>
                                                <p class="mb-0 fw-semibold"><?= $b['num_adults'] ?></p>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="text-muted small">Children</label>
                                                <p class="mb-0 fw-semibold"><?= $b['num_children'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Total Guests</label>
                                                <p class="mb-0 fw-semibold"><?= $b['number_of_guests'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Room Quantity</label>
                                                <p class="mb-0 fw-semibold"><?= $b['room_quantity'] ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Payment Method</label>
                                                <p class="mb-0 fw-semibold"><?= $b['payment_method'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PAYMENT INFORMATION -->
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-header bg-dark text-warning">
                                        <h6 class="mb-0"><i class="bi bi-cash-stack"></i> Payment Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="text-muted small">Total Amount</label>
                                                <p class="mb-0 fw-bold text-success">
                                                    ₱<?= number_format($b['total_amount'], 2) ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Downpayment</label>
                                                <p class="mb-0 fw-semibold">
                                                    ₱<?= number_format($b['downpayment_amount'], 2) ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Remaining Balance</label>
                                                <p class="mb-0 fw-bold text-danger">
                                                    ₱<?= number_format($b['remaining_balance'], 2) ?>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">Discount</label>
                                                <p class="mb-0 fw-semibold">₱<?= number_format($b['discount_amount'], 2) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DISCOUNT INFORMATION -->
                                <?php if ($b['discount_type'] != 'None' && $b['discount_percentage'] > 0): ?>
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-header bg-dark text-warning">
                                            <h6 class="mb-0"><i class="bi bi-percent"></i> Discount Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="text-muted small">Discount Type</label>
                                                    <p class="mb-0 fw-semibold"><?= $b['discount_type'] ?></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted small">Discount Percentage</label>
                                                    <p class="mb-0 fw-semibold"><?= $b['discount_percentage'] ?>%</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted small">Discount Amount</label>
                                                    <p class="mb-0 fw-semibold text-success">
                                                        ₱<?= number_format($b['discount_amount'], 2) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($transfers_res && $transfers_res->num_rows > 0): ?>
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-header bg-dark text-warning">
                                            <h6 class="mb-0"><i class="fas fa-bed"></i> Transfer Rooms (Old Rooms)</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Room Type</th>
                                                            <th>Room</th>
                                                            <th>Floor</th>
                                                            <th>Price</th>
                                                            <th>Transfer Date</th>
                                                            <th>Reason</th>
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
                                    </div>
                                <?php endif; ?>


                                <?php
                                // Fetch reschedules safely
                                $resched_res = null;
                                if (!empty($b['booking_id'])) {
                                    $stmt = $conn->prepare("SELECT * FROM reschedule_bookings WHERE booking_fk_id = ? ORDER BY date_resched ASC");
                                    $stmt->bind_param("i", $b['booking_id']);
                                    $stmt->execute();
                                    $resched_res = $stmt->get_result();
                                }

                                if ($resched_res && $resched_res->num_rows > 0):
                                    $newCI = date("F j, Y", strtotime($b['check_in']));
                                    $newCO = date("F j, Y", strtotime($b['check_out']));
                                    ?>
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-header bg-dark text-warning">
                                            <h6 class="mb-0"><i class="fas fa-concierge-bell"></i> Reschedule Details</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <textarea class="form-control border-0" rows="3" readonly>
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
                                    </div>
                                <?php endif; ?>

                                <!-- BOOKED ROOMS TABLE -->
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-header bg-dark text-warning">
                                        <h6 class="mb-0"><i class="bi bi-door-open"></i> Booked Rooms</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Room Type</th>
                                                        <th>Price</th>
                                                        <th>Room Number</th>
                                                        <th>Floor Number</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($data['rooms'])): ?>
                                                        <?php foreach ($data['rooms'] as $room): ?>
                                                            <tr>
                                                                <td class="fw-semibold"><?= $room['room_type_name'] ?></td>
                                                                <td>₱<?= number_format($room['price'], 2) ?></td>
                                                                <td><?= $room['room_number'] ?? 'N/A' ?></td>
                                                                <td><?= $room['floor_number'] ?? 'N/A' ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted py-3">No rooms found
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- GUEST LIST TABLE -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-dark text-warning">
                                        <h6 class="mb-0"><i class="bi bi-people-fill"></i> Guest List</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Guest Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($data['guests'])): ?>
                                                        <?php foreach ($data['guests'] as $guest): ?>
                                                            <tr>
                                                                <td><?= $guest['first_name'] ?></td>
                                                                <td><?= $guest['last_name'] ?></td>
                                                                <td><span
                                                                        class="badge bg-secondary"><?= ucfirst($guest['guest_type']) ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-3">No guests found
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


            <div class="modal fade" id="addAmenitiesModal" tabindex="-1" aria-labelledby="addAmenitiesModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAmenitiesModalLabel">Manage Bed Amenities</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Booking Information -->
                            <div class="mb-3">
                                <p>
                                    <strong>Check-in:</strong> <span
                                        id="checkInDate"><?= date("F j, Y", strtotime($b['check_in'])) ?></span><br>
                                    <strong>Check-out:</strong> <span
                                        id="checkOutDate"><?= date("F j, Y", strtotime($b['check_out'])) ?></span><br>
                                    <strong>Total Nights:</strong> <span id="numNights">0</span>
                                </p>
                            </div>

                            <form id="amenitiesForm">
                                <div class="mb-3">
                                    <label for="amenitySelect" class="form-label">Select Bed Type</label>
                                    <select class="form-select" id="amenitySelect" name="amenity_id">
                                        <option value="">-- Choose a Bed --</option>
                                        <?php foreach ($beds as $bed): ?>
                                            <option value="<?= $bed['id'] ?>" data-price="<?= $bed['price'] ?>">
                                                <?= $bed['item_type'] ?> (₱<?= number_format($bed['price'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <table class="table table-bordered mt-3" id="selectedAmenitiesTable">
                                <thead>
                                    <tr>
                                        <th>Bed Type</th>
                                        <th>Price per Night</th>
                                        <th>Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <div id="noAmenitiesMessage" class="text-center text-muted" style="display:none;">
                                        No beds have been added to this booking yet.
                                    </div>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <th id="subtotal">₱0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="saveAmenitiesBtn">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    const amenitySelect = document.getElementById('amenitySelect');
    const amenitiesTableBody = document.querySelector('#selectedAmenitiesTable tbody');
    const amenitiesTable = document.getElementById('selectedAmenitiesTable');
    const noAmenitiesMessage = document.getElementById('noAmenitiesMessage');

    let CURRENT_BOOKING_ID = null;

    function updateNights() {
        const checkIn = new Date('<?= $b['check_in'] ?>');
        const checkOut = new Date('<?= $b['check_out'] ?>');
        const diffTime = Math.abs(checkOut - checkIn);
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
            totalCell.textContent = '₱' + (parseFloat(price) * parseInt(qtyInput.value) * nights).toFixed(2);
            updateSubtotal();
            return;
        }

        const row = document.createElement('tr');
        row.setAttribute('data-id', id);
        row.innerHTML = `
            <td>${name}</td>
            <td>₱${parseFloat(price).toFixed(2)}</td>
            <td><input type="number" class="form-control quantity" value="${quantity}" min="1" style="width:80px;"></td>
            <td class="total">₱${(parseFloat(price) * quantity * nights).toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-danger remove-amenity">Remove</button></td>
        `;
        amenitiesTableBody.appendChild(row);

        row.querySelector('.quantity').addEventListener('input', (e) => {
            const qty = parseInt(e.target.value);
            row.querySelector('.total').textContent = '₱' + (parseFloat(price) * qty * nights).toFixed(2);
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
        const selectedOption = amenitySelect.selectedOptions[0];
        if (!selectedOption.value) return;

        const id = selectedOption.value;
        const name = selectedOption.text.split(" (₱")[0];
        const price = selectedOption.dataset.price;

        addAmenityRow(id, name, price);
        checkNoAmenities();
        amenitySelect.value = '';
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
                        noAmenitiesMessage.style.display = 'block';
                        amenitiesTable.style.display = 'none';
                    } else {
                        noAmenitiesMessage.style.display = 'none';
                        amenitiesTable.style.display = 'table';
                        data.forEach(item => {
                            addAmenityRow(item.amenities_fk_id, item.amenity_name, item.price, parseInt(item.quantity));
                        });
                    }
                });
        });
    });

    document.getElementById("saveAmenitiesBtn").addEventListener("click", () => {
        const rows = amenitiesTableBody.querySelectorAll("tr");
        const items = [];

        rows.forEach(row => {
            items.push({
                amenity_id: row.getAttribute("data-id"),
                quantity: row.querySelector(".quantity").value
            });
        });

        fetch("../Admin/adminBackend/booking_add_amenities.php", {
            method: "POST",
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
                    alert("Error saving beds");
                }
            });
    });
</script>

<?php include 'adminFrontend/footer.php'; ?>