<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$bookings = [];

$sqlBookings = "SELECT * FROM bookings WHERE status = 'pending'";
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

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Pending Bookings</i>
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
                                <a href="../Admin/index.php?book_room_details&id=<?= $id ?>"
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