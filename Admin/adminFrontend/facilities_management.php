<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$facility_category = [];
$result = $conn->query("SELECT * FROM facility_categories");
if ($result) {
    $facility_category = $result->fetch_all(MYSQLI_ASSOC);
}

$facility = [];
$result = $conn->query("SELECT * FROM facilities");
if ($result) {
    $facility = $result->fetch_all(MYSQLI_ASSOC);
}

?>


<style>
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

    .table-action-view {
        background-color: #17a2b8;
        color: white;
    }

    .table-action-view:hover {
        background-color: #138496;
        color: white;
        transform: scale(1.05);
    }

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


<style>
    .btn-casa-gold {
        background-color: #c9a349 !important;
        border-color: #c9a349 !important;
        color: white !important;
    }

    .btn-casa-outline {
        background-color: transparent !important;
        border-color: #c9a349 !important;
        color: #c9a349 !important;
    }

    .btn-casa-gold:hover {
        background-color: #b08d3a;
        border-color: #b08d3a;
        color: #ffffff;
    }


    .btn-casa-outline:hover {
        background-color: #c9a349;
        color: #ffffff;
    }
</style>

<div class="main-content" id="mainContent">
     <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Facility Management</i>
        </div>

        <div class="d-flex gap-2">
            <button id="btnCategory" class="btn btn-casa-gold" onclick="showTable('category')">
                Category
            </button>
            <button id="btnFacilities" class="btn btn-casa-outline" onclick="showTable('facilities')">
                Facilities
            </button>
        </div>
    </div>

    <!-- CATEGORY TABLE -->
    <div id="categoryTableWrapper" class="info-card" style="margin-bottom: 40px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Facility Category</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addFacilityCategory">
                + Add Category
            </a>
        </div>

        <div class="table-responsive">
            <table id="categoryTable" class="table table-hover table-striped">


                <thead>
                    <tr>
                        <th>Display Order</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facility_category as $facility_categories): ?>
                        <tr>
                            <td><?php echo $facility_categories['display_order']; ?></td>
                            <td><?php echo $facility_categories['name']; ?></td>
                            <td>
                                <span
                                    style="
                                    display: inline-block;
                                    padding: 4px 12px; 
                                    border-radius: 50px; 
                                    color: #fff; 
                                    font-size: 11px; 
                                    font-weight: 600;
                                    letter-spacing: 0.5px;
                                    background-color: <?php echo $facility_categories['active'] ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $facility_categories['active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $facility_categories['id']; ?>" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/facility_cat_delete.php?id=<?php echo $facility_categories['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $facility_categories['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $facility_categories['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/facility_categories_edit.php?id=<?php echo $facility_categories['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">
                                                Edit Amenity
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <div class="mb-3">
                                                <label class="form-label package-label">Category Name</label>
                                                <input type="text" name="name" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($facility_categories['name']); ?>"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Display Order</label>
                                                <input type="text" name="display_order" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($facility_categories['display_order']); ?>"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Status</label>
                                                <select name="active" class="form-control package-input" required>
                                                    <option value="1" <?php if ($facility_categories['active'] == 1)
                                                        echo 'selected'; ?>>
                                                        Active
                                                    </option>
                                                    <option value="0" <?php if ($facility_categories['active'] == 0)
                                                        echo 'selected'; ?>>
                                                        Inactive
                                                    </option>
                                                </select>
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

    <!-- FACILITIES TABLE -->
    <div id="facilitiesTableWrapper" class="info-card d-none" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Facilities</h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addFacility">
                + Add Facility
            </a>
        </div>

        <div class="table-responsive">
            <table id="facilitiesTable" class="table table-hover table-striped">

                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Display Order</th>
                        <th>Facility Name</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facility as $facilities): ?>
                        <tr>
                            <td>
                                <?php
                                $cat_name = '';
                                foreach ($facility_category as $cat) {
                                    if ($cat['id'] == $facilities['category_id']) {
                                        $cat_name = $cat['name'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($cat_name);
                                ?>
                            </td>

                            <td><?php echo $facilities['display_order']; ?></td>
                            <td><?php echo $facilities['name']; ?></td>
                            <td>
                                <span style="
                                    display: inline-block;
                                    padding: 4px 12px; 
                                    border-radius: 50px; 
                                    color: #fff; 
                                    font-size: 11px; 
                                    font-weight: 600;
                                    letter-spacing: 0.5px;
                                    background-color: <?php echo $facilities['active'] ? '#28a745' : '#dc3545'; ?>;">
                                    <?php echo $facilities['active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal<?php echo $facilities['id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/facility_delete.php?id=<?php echo $facilities['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this facility?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $facilities['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $facilities['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/facility_edit.php?id=<?php echo $facilities['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">
                                                Edit Facility
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                           <div class="mb-3">
                                                <label class="form-label package-label">Category</label>
                                                <select name="category_id" class="form-control package-input" required>
                                                    <?php foreach ($facility_category as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" 
                                                            <?php if ($cat['id'] == $facilities['category_id']) echo 'selected'; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Category Name</label>
                                                <input type="text" name="name" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($facilities['name']); ?>"
                                                    required>
                                            </div>


                                            <div class="mb-3">
                                                <label class="form-label package-label">Display Order</label>
                                                <input type="text" name="display_order" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($facilities['display_order']); ?>"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Status</label>
                                                <select name="active" class="form-control package-input" required>
                                                    <option value="1" <?php if ($facilities['active'] == 1)
                                                        echo 'selected'; ?>>
                                                        Active
                                                    </option>
                                                    <option value="0" <?php if ($facilities['active'] == 0)
                                                        echo 'selected'; ?>>
                                                        Inactive
                                                    </option>
                                                </select>
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

</div>




<div class="modal fade" id="addFacilityCategory" tabindex="-1" aria-labelledby="addFacilityCategoryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addFacilityCategoryLabel">Add Facility Category</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addPackageForm" method="POST" action="../Admin/adminBackend/facility_category_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="name" class="form-label package-label">Category Name</label>
                        <input type="text" class="form-control package-input" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label package-label">Display Order</label>
                        <input type="text" name="display_order" class="form-control package-input"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    <div class="mb-3">
                        <label for="active" class="form-label package-label">Status</label>
                        <select name="active" class="form-control package-input" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Add Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="addFacility" tabindex="-1" aria-labelledby="addFacilityLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addFacilityLabel">Add Facility</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addPackageForm" method="POST" action="../Admin/adminBackend/facility_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="category_id" class="form-label package-label">Category Name</label>
                        <select class="form-control package-input" id="category_id" name="category_id" required>
                            <?php foreach ($facility_category as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="name" class="form-label package-label">Facility Name</label>
                        <input type="text" class="form-control package-input" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label package-label">Display Order</label>
                        <input type="text" name="display_order" class="form-control package-input"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    <div class="mb-3">
                        <label for="active" class="form-label package-label">Status</label>
                        <select name="active" class="form-control package-input" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Add Facility
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
    let categoryTable;
    let facilitiesTable;

    $(document).ready(function () {

        categoryTable = $('#categoryTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        facilitiesTable = $('#facilitiesTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

    });

    function showTable(type) {
        if (type === 'category') {
            $('#categoryTableWrapper').removeClass('d-none');
            $('#facilitiesTableWrapper').addClass('d-none');

            $('#btnCategory').addClass('btn-casa-gold').removeClass('btn-casa-outline');
            $('#btnFacilities').addClass('btn-casa-outline').removeClass('btn-casa-gold');

            if (typeof categoryTable !== 'undefined') {
                categoryTable.columns.adjust().responsive.recalc();
            }
        } else {
            $('#facilitiesTableWrapper').removeClass('d-none');
            $('#categoryTableWrapper').addClass('d-none');

            $('#btnFacilities').addClass('btn-casa-gold').removeClass('btn-casa-outline');
            $('#btnCategory').addClass('btn-casa-outline').removeClass('btn-casa-gold');

            if (typeof facilitiesTable !== 'undefined') {
                facilitiesTable.columns.adjust().responsive.recalc();
            }
        }
    }
</script>