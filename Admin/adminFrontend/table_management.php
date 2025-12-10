<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$table_type = [];
$result = $conn->query("SELECT * FROM table_types");
if ($result) {
    $table_type = $result->fetch_all(MYSQLI_ASSOC);
}

$table_number = [];
$result = $conn->query("SELECT * FROM table_number");
if ($result) {
    $table_number = $result->fetch_all(MYSQLI_ASSOC);
}

$tableTypes = $conn->query("SELECT * FROM table_types");

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
            <span>Table Management</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Table Types</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addTableTypeModal">
                Add Table
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Table Type</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Image</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_type as $table_types): ?>
                        <tr>
                            <td><?php echo $table_types['table_name']; ?></td>
                            <td><?php echo $table_types['capacity']; ?></td>
                            <td><?php echo $table_types['status']; ?></td>
                            <td><?php echo $table_types['reason']; ?></td>

                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-view"
                                    data-bs-toggle="modal" data-bs-target="#imagesModal<?php echo $table_types['id']; ?>"
                                    title="View Images">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal<?php echo $table_types['id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/table_type_delete.php?id=<?php echo $table_types['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this table type?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="imagesModal<?php echo $table_types['id']; ?>" tabindex="-1"
                            aria-labelledby="imagesModalLabel<?php echo $table_types['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imagesModalLabel<?php echo $table_types['id']; ?>">
                                            Images of <?php echo $table_types['table_name']; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        $images = [];
                                        if (!empty($table_types['img1']))
                                            $images[] = $table_types['img1'];
                                        if (!empty($table_types['img2']))
                                            $images[] = $table_types['img2'];
                                        if (!empty($table_types['img3']))
                                            $images[] = $table_types['img3'];
                                        if (!empty($table_types['img4']))
                                            $images[] = $table_types['img4'];
                                        if (!empty($table_types['img5']))
                                            $images[] = $table_types['img5'];
                                        ?>


                                        <?php if (!empty($images)): ?>
                                            <div id="carousel<?php echo $table_types['id']; ?>" class="carousel slide"
                                                data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php $active = 'active'; ?>
                                                    <?php foreach ($images as $img): ?>
                                                        <div class="carousel-item <?php echo $active; ?>">
                                                            <img src="../Admin/adminBackend/table_types_images/<?php echo $img; ?>"
                                                                class="d-block w-100" alt="Room Image">
                                                        </div>
                                                        <?php $active = ''; ?>
                                                    <?php endforeach; ?>
                                                </div>

                                                <?php if (count($images) > 1): ?>
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#carousel<?php echo $table_types['id']; ?>"
                                                        data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#carousel<?php echo $table_types['id']; ?>"
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



                        <div class="modal fade" id="editModal<?php echo $table_types['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $table_types['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">
                                    <form method="POST" enctype="multipart/form-data"
                                        action="../Admin/adminBackend/table_types_edit.php?id=<?php echo $table_types['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold"
                                                id="editModalLabel<?php echo $table_types['id']; ?>">
                                                Edit Table Type
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <!-- Table Name + Capacity -->

                                            <div class="mb-4">
                                                <label class="form-label package-label">Table Name</label>
                                                <input type="text" name="table_name" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_types['table_name']); ?>"
                                                    required>
                                            </div>

                                            <!-- Available Tables + Status -->
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Capacity</label>
                                                    <input type="number" name="capacity" class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($table_types['capacity']); ?>"
                                                        required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Status</label>
                                                    <select name="status" class="form-select package-input" required>
                                                        <option value="active" <?php echo $table_types['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="inactive" <?php echo $table_types['status'] === 'inactive' ? 'selected' : ''; ?>>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Description</label>
                                                <textarea name="description" class="form-control package-input" rows="3"><?php
                                                echo htmlspecialchars($table_types['description'] ?? '');
                                                ?></textarea>
                                            </div>

                                            <!-- Reason -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Reason</label>
                                                <textarea name="reason" class="form-control package-input" rows="2"><?php
                                                echo htmlspecialchars($table_types['reason'] ?? '');
                                                ?></textarea>
                                                <small class="form-text package-help-text">(Optional if status =
                                                    active)</small>
                                            </div>

                                            <!-- Image Upload -->
                                            <div class="mb-4">
                                                <label class="form-label package-label">Update Images (Max 5)</label>
                                                <input type="file" class="form-control package-input" name="images[]"
                                                    multiple accept="image/*">
                                                <small class="form-text package-help-text">Leave empty to keep existing
                                                    images.</small>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn package-btn-save">Save Changes</button>
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




    <div class="modal fade" id="addTableTypeModal" tabindex="-1" aria-labelledby="addTableTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="addTableTypeModalLabel">Add Table</h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body package-modal-body">
                    <form id="addPackageForm" method="POST" action="../Admin/adminBackend/table_type_add.php"
                        enctype="multipart/form-data">


                        <div class="mb-4">
                            <label for="table_name" class="form-label package-label">Table Name</label>
                            <input type="text" class="form-control package-input" id="table_name" name="table_name"
                                required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="capacity" class="form-label package-label">Capacity</label>
                                <input type="number" class="form-control package-input" id="capacity" name="capacity"
                                    min="1" required>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label package-label">Status</label>
                                <select class="form-select package-input" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label package-label">Description</label>
                            <textarea class="form-control package-input" id="description" name="description"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label package-label">Reason</label>
                            <textarea class="form-control package-input" id="reason" name="reason" rows="2"></textarea>
                            <small class="form-text package-help-text">(Optional if status = active)</small>
                        </div>



                        <div class="mb-4">
                            <label class="form-label package-label">Images (Max 5)</label>
                            <input type="file" class="form-control package-input" id="table_image_upload"
                                name="images[]" multiple accept="image/*" required>
                            <small class="form-text package-help-text">You can upload up to 5 images.</small>
                        </div>

                        <div class="modal-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn package-btn-save">
                                    Save Table Type
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


    <div class="info-card" style="margin-bottom: 40px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Table Number</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addTableNumberModal">
                Add Table Number
            </a>
        </div>

        <div class="table-responsive">
            <table id="tableNumber" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Table Number</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_number as $table_numbers): ?>
                        <tr>
                            <td><?php echo $table_numbers['table_number']; ?></td>
                            <td><?php echo $table_numbers['status']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editRoomNumber<?php echo $table_numbers['id']; ?>" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/table_number_delete.php?id=<?php echo $table_numbers['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this table number?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                        <div class="modal fade" id="editRoomNumber<?php echo $table_numbers['id']; ?>" tabindex="-1"
                            aria-labelledby="editRoomNumberLabel<?php echo $table_numbers['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content package-modal">
                                    <form method="POST"
                                        action="../Admin/adminBackend/table_number_edit.php?id=<?php echo $table_numbers['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">Edit Table</h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <div class="mb-3">
                                                <label class="form-label package-label">Table Number</label>
                                                <input type="text" name="table_number" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_numbers['table_number']); ?>"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Table Type</label>
                                                <select name="table_type_fk_id" class="form-control package-input" required>
                                                    <option value="">Select Table Type</option>

                                                    <?php while ($row = $tableTypes->fetch_assoc()): ?>
                                                        <option value="<?= $row['id']; ?>"
                                                            <?= ($row['id'] == $table_numbers['table_type_fk_id']) ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($row['table_name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Status</label>
                                                <select name="status" class="form-control package-input" required>
                                                    <option value="available" <?= ($table_numbers['status'] === 'available') ? 'selected' : ''; ?>>
                                                        Available
                                                    </option>
                                                    <option value="occupied" <?= ($table_numbers['status'] === 'occupied') ? 'selected' : ''; ?>>
                                                        Occupied
                                                    </option>
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



    <div class="modal fade" id="addTableNumberModal" tabindex="-1" aria-labelledby="addTableNumberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content package-modal">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="addTableNumberModalLabel">Add Table Number</h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body package-modal-body">
                    <form id="addRoomForm" method="POST" action="../Admin/adminBackend/table_number_add.php">

                        <div class="mb-3">
                            <label for="tableNumber" class="form-label package-label">Table Number</label>
                            <select class="form-control package-input" id="table_type_fk_id" name="table_type_fk_id"
                                required>
                                <option value="">Select Table Type</option>

                                <?php while ($row = $tableTypes->fetch_assoc()): ?>
                                    <option value="<?= $row['id']; ?>">
                                        <?= $row['table_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                        </div>


                        <div class="mb-3">
                            <label for="tableNumber" class="form-label package-label">Table Number</label>
                            <input type="text" class="form-control package-input" id="tableNumber" name="table_number"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label package-label">Status</label>
                            <select class="form-select package-input" id="status" name="status" required>
                                <option value="available" selected>Available</option>
                                <option value="occupied">Occupied</option>
                            </select>

                        </div>

                        <div class="modal-footer">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn package-btn-save">
                                    Save Table Number
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>







<?php include 'adminFrontend/footer.php'; ?>

<script>
    $(document).ready(function () {
        $('#tableNumber').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
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
    document.getElementById('table_image_upload').addEventListener('change', function () {
        if (this.files.length > 5) {
            alert('You can only upload up to 5 images.');
            this.value = '';
        }
    });
</script>