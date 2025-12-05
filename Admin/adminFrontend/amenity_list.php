<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$table_type = [];
$result = $conn->query("SELECT * FROM beds");
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
            <span>Amenities</span>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addPackageModal">+ Amenity
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">

                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_type as $table_types): ?>
                        <tr>
                            <td><?php echo $table_types['item_type']; ?></td>
                            <td><?php echo $table_types['price']; ?></td>

                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal<?php echo $table_types['id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/amenity_delete.php?id=<?php echo $table_types['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this amenity type?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $table_types['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $table_types['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/amenity_edit.php?id=<?php echo $table_types['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold"
                                                id="editModalLabel<?php echo $table_types['id']; ?>">
                                                Edit Amenity
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="mb-3">
                                                <label class="form-label package-label">Item Type</label>
                                                <input type="text" name="item_type" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_types['item_type']); ?>"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Price</label>
                                                <input type="text" name="price" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_types['price']); ?>"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>

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




    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="addPackageModalLabel">Add Amenity</h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body package-modal-body">
                    <form id="addPackageForm" method="POST" action="../Admin/adminBackend/amenity_add.php"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="itemName" class="form-label package-label">Item Name</label>
                            <input type="text" class="form-control package-input" id="itemName" name="item_type"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label package-label">Price</label>
                            <input type="text" name="price" class="form-control package-input"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
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