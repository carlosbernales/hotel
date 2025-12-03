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

$amenitiesQuery = "SELECT * FROM amenity_list ORDER BY amenity_name ASC";
$amenitiesResult = $conn->query($amenitiesQuery);

$amenities = [];
if ($amenitiesResult->num_rows > 0) {
    while ($row = $amenitiesResult->fetch_assoc()) {
        $amenities[] = $row;
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
                            <h5 class="modal-title" id="addAmenitiesModalLabel">Add Amenities</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form id="amenitiesForm">
                                <div class="mb-3">
                                    <label for="amenitySelect" class="form-label">Select Amenity</label>
                                    <select class="form-select" id="amenitySelect" name="amenity_id">
                                        <option value="">-- Select Amenity --</option>
                                        <?php foreach ($amenities as $amenity): ?>
                                            <option value="<?= $amenity['id'] ?>">
                                                <?= $amenity['amenity_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <table class="table table-bordered mt-3" id="selectedAmenitiesTable">
                                <thead>
                                    <tr>
                                        <th>Amenity</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <div id="noAmenitiesMessage" class="text-center text-muted" style="display:none;">
                                        No amenities added for this booking.
                                    </div>
                                    <!-- Dynamically added rows here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveAmenitiesBtn">Save Amenities</button>
                        </div>
                    </div>
                </div>
            </div>




            <script>
                const amenitySelect = document.getElementById('amenitySelect');
                const amenitiesTableBody = document.querySelector('#selectedAmenitiesTable tbody');
                const amenitiesTable = document.getElementById('selectedAmenitiesTable');
                const noAmenitiesMessage = document.getElementById('noAmenitiesMessage');

                function addAmenityRow(id, name, quantity = 1) {
                    const existingRow = amenitiesTableBody.querySelector(`tr[data-id="${id}"]`);
                    if (existingRow) {
                        const qtyInput = existingRow.querySelector('.quantity');
                        qtyInput.value = parseInt(qtyInput.value) + quantity;
                        return;
                    }

                    const row = document.createElement('tr');
                    row.setAttribute('data-id', id);
                    row.innerHTML = `
        <td>${name}</td>
        <td><input type="number" class="form-control quantity" value="${quantity}" min="1" style="width:80px;"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-amenity">Remove</button></td>
    `;
                    amenitiesTableBody.appendChild(row);

                    row.querySelector('.remove-amenity').addEventListener('click', () => {
                        row.remove();
                        checkNoAmenities();
                    });
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

                // Add amenity from select
                amenitySelect.addEventListener('change', () => {
                    const selectedOption = amenitySelect.selectedOptions[0];
                    if (!selectedOption.value) return;

                    const id = selectedOption.value;
                    const name = selectedOption.text;

                    addAmenityRow(id, name);
                    checkNoAmenities();
                    amenitySelect.value = '';
                });

                let CURRENT_BOOKING_ID = null;

                // Open modal and load existing amenities
                document.querySelectorAll(".add-amenities-btn").forEach(btn => {
                    btn.addEventListener("click", () => {
                        CURRENT_BOOKING_ID = btn.getAttribute("data-id");

                        amenitiesTableBody.innerHTML = '';
                        checkNoAmenities();

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
                                        addAmenityRow(item.amenities_fk_id, item.amenity_name, parseInt(item.quantity));
                                    });
                                }
                            });
                    });
                });

                // Save amenities
                document.getElementById("saveAmenitiesBtn").addEventListener("click", () => {
                    const rows = amenitiesTableBody.querySelectorAll("tr");
                    const items = [];

                    rows.forEach(row => {
                        items.push({
                            amenity_id: row.getAttribute("data-id"),
                            amenity_name: row.children[0].textContent,
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
                                alert("Error saving amenities");
                            }
                        });
                });

            </script>

        </div>
    </div>
</div>



<?php include 'adminFrontend/footer.php'; ?>