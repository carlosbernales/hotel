<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$table_type = [];
$result = $conn->query("SELECT * FROM table_packages");
if ($result) {
    $table_type = $result->fetch_all(MYSQLI_ASSOC);
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
            <span>Table Management</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Table Types</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                Add Table
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Table Type</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Available Tables</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_type as $table_types): ?>
                        <tr>
                            <td><?php echo $table_types['package_name']; ?></td>
                            <td><?php echo $table_types['capacity']; ?></td>
                            <td><?php echo $table_types['available_tables']; ?></td>
                            <td><?php echo $table_types['description']; ?></td>
                            <td><?php echo $table_types['status']; ?></td>
                            <td><?php echo $table_types['price']; ?></td>

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
                                    action="../Admin/adminBackend/delete_table_type.php?id=<?php echo $table_types['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this room type?')"
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
                                            Images of <?php echo $table_types['package_name']; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        $images = [];
                                        if (!empty($table_types['image1']))
                                            $images[] = $table_types['image1'];
                                        if (!empty($table_types['image2']))
                                            $images[] = $table_types['image2'];
                                        if (!empty($table_types['image3']))
                                            $images[] = $table_types['image3'];
                                        if (!empty($table_types['image4']))
                                            $images[] = $table_types['image4'];
                                        if (!empty($table_types['image5']))
                                            $images[] = $table_types['image5'];
                                        ?>


                                        <?php if (!empty($images)): ?>
                                            <div id="carousel<?php echo $table_types['id']; ?>" class="carousel slide"
                                                data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php $active = 'active'; ?>
                                                    <?php foreach ($images as $img): ?>
                                                        <div class="carousel-item <?php echo $active; ?>">
                                                            <img src="../Admin/adminBackend/table_packages_image/<?php echo $img; ?>"
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
                                        action="../Admin/adminBackend/edit_table_package.php?id=<?php echo $table_types['id']; ?>">
                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold"
                                                id="editModalLabel<?php echo $table_types['id']; ?>">
                                                Edit Package
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <!-- Package Name (single row) -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Package Name</label>
                                                <input type="text" name="package_name" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_types['package_name']); ?>"
                                                    required>
                                            </div>

                                            <!-- Price and Capacity (two per row) -->
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Price</label>
                                                    <input type="number" step="0.01" name="price"
                                                        class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($table_types['price']); ?>"
                                                        required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Capacity</label>
                                                    <input type="number" name="capacity" class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($table_types['capacity']); ?>"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Available Tables and Status (two per row) -->
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Available Tables</label>
                                                    <input type="number" name="available_tables"
                                                        class="form-control package-input"
                                                        value="<?php echo htmlspecialchars($table_types['available_tables']); ?>"
                                                        required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Status</label>
                                                    <select name="status" class="form-select package-input" required>
                                                        <option value="active" <?php echo $table_types['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="inactive" <?php echo $table_types['status'] == 'inactive' ? 'selected' : ''; ?>>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Description (single row) -->
                                            <div class="mb-3">
                                                <label class="form-label package-label">Description</label>
                                                <textarea name="description" class="form-control package-input" rows="3"
                                                    required><?php echo htmlspecialchars($table_types['description']); ?></textarea>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label package-label">Images (Max 5)</label>
                                                <input type="file" class="form-control package-input"
                                                    id="table_image_upload" name="images[]" multiple accept="image/*">
                                                <small class="form-text package-help-text">You can upload up to 5
                                                    images.</small>
                                            </div>

                                            <div class="modal-footer">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="submit" class="btn package-btn-save">
                                                        Save Changes
                                                    </button>
                                                </div>
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




    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="addPackageModalLabel">Add Package</h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body package-modal-body">
                    <form id="addPackageForm" method="POST" action="../Admin/adminBackend/add_table_package.php"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="packageName" class="form-label package-label">Package Name</label>
                            <input type="text" class="form-control package-input" id="packageName" name="package_name"
                                required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label package-label">Price</label>
                                <input type="number" class="form-control package-input" id="price" name="price"
                                    step="0.01" min="0" required>
                            </div>

                            <div class="col-md-6">
                                <label for="capacity" class="form-label package-label">Capacity</label>
                                <input type="number" class="form-control package-input" id="capacity" name="capacity"
                                    min="1" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="availableTables" class="form-label package-label">Available Tables</label>
                                <input type="number" class="form-control package-input" id="availableTables"
                                    name="available_tables" min="1" required>
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
                            <textarea class="form-control package-input" id="description" name="description" rows="3"
                                required></textarea>
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
                                    Save Package
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
    document.getElementById('table_image_upload').addEventListener('change', function () {
        if (this.files.length > 5) {
            alert('You can only upload up to 5 images.');
            this.value = '';
        }
    });
</script>