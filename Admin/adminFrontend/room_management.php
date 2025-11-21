<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$room_types = [];
$result = $conn->query("SELECT * FROM room_types");
if ($result) {
    $room_types = $result->fetch_all(MYSQLI_ASSOC);
}

$room_number = [];
$result = $conn->query("SELECT * FROM room_numbers");
if ($result) {
    $room_number = $result->fetch_all(MYSQLI_ASSOC);
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
            <span>Room Management</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Room Types</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                Add Room
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Beds</th>
                        <th>Price/Night</th>
                        <th>Images</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($room_types as $room_type): ?>
                        <tr>
                            <td><?php echo $room_type['room_type']; ?></td>
                            <td><?php echo $room_type['beds']; ?></td>
                            <td><?php echo $room_type['price']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#imagesModal<?php echo $room_type['room_type_id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <td>

                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $room_type['room_type_id']; ?>" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/delete_room_type.php?id=<?php echo $room_type['room_type_id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this room type?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>

                        <div class="modal fade" id="imagesModal<?php echo $room_type['room_type_id']; ?>" tabindex="-1"
                            aria-labelledby="imagesModalLabel<?php echo $room_type['room_type_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="imagesModalLabel<?php echo $room_type['room_type_id']; ?>">
                                            Images of <?php echo $room_type['room_type']; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Collect non-empty images
                                        $images = [];
                                        if (!empty($room_type['image']))
                                            $images[] = $room_type['image'];
                                        if (!empty($room_type['image2']))
                                            $images[] = $room_type['image2'];
                                        if (!empty($room_type['image3']))
                                            $images[] = $room_type['image3'];
                                        ?>

                                        <?php if (!empty($images)): ?>
                                            <div id="carousel<?php echo $room_type['room_type_id']; ?>" class="carousel slide"
                                                data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php $active = 'active'; ?>
                                                    <?php foreach ($images as $img): ?>
                                                        <div class="carousel-item <?php echo $active; ?>">
                                                            <img src="../Admin/adminBackend/room_type_images/<?php echo $img; ?>"
                                                                class="d-block w-100" alt="Room Image">
                                                        </div>
                                                        <?php $active = ''; ?>
                                                    <?php endforeach; ?>
                                                </div>

                                                <?php if (count($images) > 1): ?>
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#carousel<?php echo $room_type['room_type_id']; ?>"
                                                        data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#carousel<?php echo $room_type['room_type_id']; ?>"
                                                        data-bs-slide="next">
                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center">No images available</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="modal fade" id="editModal<?php echo $room_type['room_type_id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $room_type['room_type_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">
                                    <form method="POST" enctype="multipart/form-data"
                                        action="../Admin/adminBackend/edit_room_type.php?id=<?php echo $room_type['room_type_id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold"
                                                id="editModalLabel<?php echo $room_type['room_type_id']; ?>">
                                                Edit Room Type
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="mb-3">
                                                <label class="form-label package-label">Room Type</label>
                                                <input type="text" name="room_type" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($room_type['room_type']); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Beds</label>
                                                <input type="number" name="beds" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($room_type['beds']); ?>">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Price/Night</label>
                                                    <input type="number" step="0.01" name="price"
                                                        class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($room_type['price']); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Capacity</label>
                                                    <input type="number" name="capacity" class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($room_type['capacity']); ?>">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Rating</label>
                                                    <input type="number" step="0.1" min="0" max="5" name="rating"
                                                        class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($room_type['rating'] ?? 0); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Rating Count</label>
                                                    <input type="number" min="0" name="rating_count"
                                                        class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($room_type['rating_count'] ?? 0); ?>">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Description</label>
                                                <textarea name="description"
                                                    class="form-control package-input"><?php echo htmlspecialchars($room_type['description']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Images (optional, max 3)</label>
                                                <input type="file" class="form-control package-input edit_images"
                                                    id="edit_images<?php echo $room_type['room_type_id']; ?>"
                                                    name="images[]" multiple>
                                                <small class="package-help-text">Upload new images to replace existing
                                                    ones.</small>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn package-btn-save">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>


                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="info-card" style="margin-bottom: 40px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Room Number</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addRoomNumberModal">
                Add Room
            </a>
        </div>

        <div class="table-responsive">
            <table id="bookingTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Floor Number</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($room_number as $room_numbers): ?>
                        <tr>
                            <td><?php echo $room_numbers['room_number']; ?></td>
                            <td><?php echo $room_numbers['floor_number']; ?></td>
                            <td><?php echo $room_numbers['status']; ?></td>

                            <td>

                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editRoomNumber<?php echo $room_numbers['room_number_id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/delete_room_number.php?id=<?php echo $room_numbers['room_number_id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this room type?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                        <div class="modal fade" id="editRoomNumber<?php echo $room_numbers['room_number_id']; ?>"
                            tabindex="-1"
                            aria-labelledby="editRoomNumberLabel<?php echo $room_numbers['room_number_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content package-modal">
                                    <form method="POST"
                                        action="../Admin/adminBackend/edit_room_number.php?id=<?php echo $room_numbers['room_number_id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">Edit Room</h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <!-- Room Type Dropdown -->
                                            <div class="mb-3">
                                                <label for="roomType" class="form-label package-label">Room Type</label>
                                                <select class="form-control package-input" id="roomType" name="room_type_id"
                                                    required>
                                                    <option value="" disabled>Select Room Type</option>
                                                    <?php
                                                    $result = $conn->query("SELECT * FROM room_types");
                                                    if ($result) {
                                                        while ($room = $result->fetch_assoc()) {
                                                            $selected = ($room['room_type_id'] == $room_numbers['room_type_id']) ? 'selected' : '';
                                                            echo '<option value="' . $room['room_type_id'] . '" ' . $selected . '>' . $room['room_type'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Room Number -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Room Number</label>
                                                <input type="text" name="room_number" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($room_numbers['room_number']); ?>"
                                                    required>
                                            </div>

                                            <!-- Floor Number -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Floor Number</label>
                                                <input type="number" name="floor_number" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($room_numbers['floor_number']); ?>"
                                                    required>
                                            </div>

                                            <!-- Status -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Status</label>
                                                <select name="status" class="form-control package-input" required>
                                                    <option value="Available" <?php echo ($room_numbers['status'] == 'Available') ? 'selected' : ''; ?>>
                                                        Available</option>
                                                    <option value="Occupied" <?php echo ($room_numbers['status'] == 'Occupied') ? 'selected' : ''; ?>>Occupied
                                                    </option>
                                                    <option value="Maintenance" <?php echo ($room_numbers['status'] == 'Maintenance') ? 'selected' : ''; ?>>
                                                        Maintenance</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn package-btn-save">
                                                    Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="modal fade" id="addRoomNumberModal" tabindex="-1" aria-labelledby="addRoomNumberModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addRoomNumberModalLabel">Add Room Number</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addRoomForm" method="POST" action="../Admin/adminBackend/add_room_numbers.php">

                    <div class="mb-3">
                        <label for="roomType" class="form-label package-label">Room Type</label>
                        <select class="form-control package-input" id="roomType" name="room_type_id" required>
                            <option value="" disabled selected>Select Room Type</option>
                            <?php
                            $room_types = [];
                            $result = $conn->query("SELECT * FROM room_types");
                            if ($result) {
                                $room_types = $result->fetch_all(MYSQLI_ASSOC);
                                foreach ($room_types as $room) {
                                    echo '<option value="' . htmlspecialchars($room['room_type_id']) . '">' . htmlspecialchars($room['room_type']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="roomNumber" class="form-label package-label">Room Number</label>
                        <input type="text" class="form-control package-input" id="roomNumber" name="room_number"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="floorNumber" class="form-label package-label">Floor Number</label>
                        <input type="number" class="form-control package-input" id="floorNumber" name="floor_number"
                            min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label package-label">Status</label>
                        <select class="form-select package-input" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Save Room
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addRoomModalLabel">Add Room Type</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addRoomForm" method="POST" action="../Admin/adminBackend/add_room_type.php"
                    enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="roomType" class="form-label package-label">Room Type</label>
                        <input type="text" class="form-control package-input" id="roomType" name="room_type" required>
                    </div>

                    <div class="mb-3">
                        <label for="beds" class="form-label package-label">Beds</label>
                        <input type="text" class="form-control package-input" id="beds" name="beds" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label package-label">Price/Night</label>
                            <input type="number" class="form-control package-input" id="price" name="price" step="0.01"
                                min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label package-label">Capacity</label>
                            <input type="number" class="form-control package-input" id="capacity" name="capacity"
                                min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rating" class="form-label package-label">Rating</label>
                            <input type="number" class="form-control package-input" id="rating" name="rating" step="0.1"
                                min="0" max="5" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ratingCount" class="form-label package-label">Rating Count</label>
                            <input type="number" class="form-control package-input" id="ratingCount" name="rating_count"
                                min="0" value="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label package-label">Description</label>
                        <textarea class="form-control package-input" id="description" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label package-label">Images (Max 3)</label>
                        <input type="file" class="form-control package-input" id="images" name="images[]" multiple
                            required>
                        <small class="package-help-text">You can upload up to 3 images.</small>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Save Room
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>





<?php include 'adminFrontend/footer.php'; ?>

<script>
    $(document).ready(function () {
        $('#bookingTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search bookings:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ bookings",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>

<script>
    document.getElementById('images').addEventListener('change', function () {
        if (this.files.length > 3) {
            alert('You can only upload up to 3 images.');
            this.value = '';
        }
    });
</script>

<?php foreach ($room_types as $room_type): ?>
    <script>
        document.getElementById('edit_images<?php echo $room_type['room_type_id']; ?>')
            .addEventListener('change', function () {
                if (this.files.length > 3) {
                    alert('You can only upload up to 3 images.');
                    this.value = '';
                }
            });
    </script>
<?php endforeach; ?>