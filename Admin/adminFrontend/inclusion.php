<?php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}

include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$table_type = [];
$result = $conn->query("SELECT * FROM amenities");
if ($result) {
    $table_type = $result->fetch_all(MYSQLI_ASSOC);
}

?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/amenity_list.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Inclusions</i>
            
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addPackageModal">+ Inclusions
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">

                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_type as $table_types): ?>
                        <tr>
                            <td><?php echo $table_types['name']; ?></td>

                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal<?php echo $table_types['amenity_id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/inclusion_delete.php?id=<?php echo $table_types['amenity_id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this inclusion?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $table_types['amenity_id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $table_types['amenity_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/inclusion_edit.php?id=<?php echo $table_types['amenity_id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold"
                                                id="editModalLabel<?php echo $table_types['amenity_id']; ?>">
                                                Edit Inclusions
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="mb-3">
                                                <label class="form-label package-label">Name</label>
                                                <input type="text" name="item_type" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($table_types['name']); ?>"
                                                    required>
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
                    <form id="addPackageForm" method="POST" action="../Admin/adminBackend/inclusion_add.php"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="itemName" class="form-label package-label">Name</label>
                            <input type="text" class="form-control package-input" id="itemName" name="name"
                                required>
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