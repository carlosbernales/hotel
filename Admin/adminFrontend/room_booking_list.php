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
    SELECT *
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
            <i class="fas fa-home"></i>
            <span>Checked In Bookings</span>
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
                        <th>Actions</th>
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
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item add-amenities-btn" href="#" data-id="<?= $id ?>"
                                                data-bs-toggle="modal" data-bs-target="#addAmenitiesModal">
                                                Add Amenities
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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