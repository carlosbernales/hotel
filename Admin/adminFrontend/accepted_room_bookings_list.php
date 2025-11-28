<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$bookings = [];

$sqlBookings = "SELECT * FROM bookings WHERE status = 'accepted'";
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
    SELECT booking_id, room_type_id, room_type_name, price
    FROM booked_rooms
";
$resRooms = $conn->query($sqlRooms);

while ($r = $resRooms->fetch_assoc()) {
    $id = $r['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['rooms'][] = $r;
    }
}

$sqlGuests = "
    SELECT booking_id, first_name, last_name, guest_type
    FROM guest_names
";
$resGuests = $conn->query($sqlGuests);

while ($g = $resGuests->fetch_assoc()) {
    $id = $g['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['guests'][] = $g;
    }
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
<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"></i>
            <span>Accepted Bookings</span>
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
                        <th>Status</th>
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
                            <td><?= $b['check_in'] ?> - <?= $b['check_out'] ?></td>
                            <td><?= $b['status'] ?></td>
                            <td>
                                <a href="../Admin/index.php?accepted_room_bookDetails&id=<?= $id ?>"
                                    class="btn btn-sm table-action-btn">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
<?php foreach ($bookings as $id => $data): ?>
    <?php $b = $data['booking']; ?>

    <div class="modal fade" id="editModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">

                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- SINGLE BOOKING INFO -->
                    <h5 class="fw-bold">Booking Information</h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Number of Guests</label>
                            <input class="form-control" value="<?= $b['number_of_guests'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Room Quantity</label>
                            <input class="form-control" value="<?= $b['room_quantity'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Total Amount</label>
                            <input class="form-control" value="<?= $b['total_amount'] ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Adults</label>
                            <input class="form-control" value="<?= $b['num_adults'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Children</label>
                            <input class="form-control" value="<?= $b['num_children'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Extra Bed</label>
                            <input class="form-control" value="<?= $b['extra_bed'] ?>" readonly>
                        </div>
                    </div>

                    <!-- DISCOUNT -->
                    <h5 class="fw-bold mt-4">Discount Details</h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Type</label>
                            <input class="form-control" value="<?= $b['discount_type'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Percentage</label>
                            <input class="form-control" value="<?= $b['discount_percentage'] ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Amount</label>
                            <input class="form-control" value="<?= $b['discount_amount'] ?>" readonly>
                        </div>
                    </div>

                    <!-- ROOMS -->
                    <h5 class="fw-bold mt-4">Booked Rooms</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Room Type ID</th>
                                <th>Room Type</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['rooms'] as $r): ?>
                                <tr>
                                    <td><?= $r['room_type_id'] ?></td>
                                    <td><?= $r['room_type_name'] ?></td>
                                    <td><?= $r['price'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- GUESTS -->
                    <h5 class="fw-bold mt-4">Guest Names</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Guest Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['guests'] as $g): ?>
                                <tr>
                                    <td><?= $g['first_name'] ?></td>
                                    <td><?= $g['last_name'] ?></td>
                                    <td><?= $g['guest_type'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>






<?php include 'adminFrontend/footer.php'; ?>