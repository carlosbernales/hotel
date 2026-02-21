<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$staff_types = [];
$result = $conn->query("SELECT * FROM staff_type");
if ($result) {
    $staff_types = $result->fetch_all(MYSQLI_ASSOC);
}

$staffs = [];
$result = $conn->query("
    SELECT s.*, st.staff_type AS staff_type_name, sh.shift AS shift_name, sh.shift_timing
    FROM staff s
    JOIN staff_type st ON s.staff_type_fk_id = st.id
    JOIN shift sh ON s.shift_id = sh.id
");
if ($result) {
    $staffs = $result->fetch_all(MYSQLI_ASSOC);
}


$shifts = [];
$result = $conn->query("SELECT * FROM shift");
if ($result) {
    $shifts = $result->fetch_all(MYSQLI_ASSOC);
}

?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/amenity_list.css">


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
            <i class="fas fa-home"> Staff Management</i>
        </div>

        <div class="d-flex gap-2">
            <button id="btnCategory" class="btn btn-casa-gold" onclick="showTable('category')">
                Staffs
            </button>
            <button id="btnFacilities" class="btn btn-casa-outline" onclick="showTable('facilities')">
                Types
            </button>
        </div>
    </div>

    <div id="categoryTableWrapper" class="info-card" style="margin-bottom: 40px;">


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addStaffModal">+ Add Staff</a>
        </div>

        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Staff Type</th>
                        <th>Shift</th>
                        <th>Contact</th>
                        <th>Salary</th>
                        <th>Joining Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffs as $staff): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($staff['id_card_no']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($staff['emp_name']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($staff['staff_type_name']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($staff['shift_name'] . ' (' . $staff['shift_timing'] . ')') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($staff['contact_no']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($staff['salary']) ?>
                            </td>
                            <td>
                                <?= date('F d, Y', strtotime($staff['joining_date'])) ?>
                            </td>

                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editStaffModal<?= $staff['id'] ?>" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST" action="../Admin/adminBackend/staff_delete.php?id=<?= $staff['id'] ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this staff?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editStaffModal<?= $staff['id'] ?>" tabindex="-1"
                            aria-labelledby="editStaffModalLabel<?= $staff['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">
                                    <form method="POST"
                                        action="../Admin/adminBackend/staff_edit.php?id=<?= $staff['id'] ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold" id="editStaffModalLabel<?= $staff['id'] ?>">
                                                Edit Staff
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="mb-3">
                                                <label class="form-label package-label">Name</label>
                                                <input type="text" name="emp_name" class="form-control package-input"
                                                    value="<?= htmlspecialchars($staff['emp_name']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Staff Type</label>
                                                <select name="staff_type_fk_id" class="form-control package-input" required>
                                                    <?php foreach ($staff_types as $type): ?>
                                                        <option value="<?= $type['id'] ?>"
                                                            <?= $staff['staff_type_fk_id'] == $type['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($type['staff_type']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Shift</label>
                                                <select name="shift_id" class="form-control package-input" required>
                                                    <?php foreach ($shifts as $shift): ?>
                                                        <option value="<?= $shift['id'] ?>" <?= $staff['shift_id'] == $shift['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($shift['shift']) ?>
                                                            (
                                                            <?= htmlspecialchars($shift['shift_timing']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">ID Card No</label>
                                                <input type="text" name="id_card_no" class="form-control package-input"
                                                    value="<?= htmlspecialchars($staff['id_card_no']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Address</label>
                                                <input type="text" name="address" class="form-control package-input"
                                                    value="<?= htmlspecialchars($staff['address']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Contact No</label>
                                                <input type="text" name="contact_no" class="form-control package-input"
                                                    value="<?= htmlspecialchars($staff['contact_no']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Salary</label>
                                                <input type="number" name="salary" class="form-control package-input"
                                                    value="<?= htmlspecialchars($staff['salary']) ?>" required>
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

    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">
                <form method="POST" action="../Admin/adminBackend/staff_add.php">

                    <div class="modal-header package-modal-header">
                        <h5 class="modal-title fw-bold" id="addStaffModalLabel">Add Staff</h5>
                        <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body package-modal-body">
                        <div class="mb-3">
                            <label class="form-label package-label">Name</label>
                            <input type="text" name="emp_name" class="form-control package-input" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Staff Type</label>
                            <select name="staff_type_fk_id" class="form-control package-input" required>
                                <?php foreach ($staff_types as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= htmlspecialchars($type['staff_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Shift</label>
                            <select name="shift_id" class="form-control package-input" required>
                                <?php foreach ($shifts as $shift): ?>
                                    <option value="<?= $shift['id'] ?>">
                                        <?= htmlspecialchars($shift['shift']) ?> (
                                        <?= htmlspecialchars($shift['shift_timing']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">ID Card No</label>
                            <input type="text" name="id_card_no" class="form-control package-input" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Address</label>
                            <input type="text" name="address" class="form-control package-input" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Contact No</label>
                            <input type="text" name="contact_no" class="form-control package-input" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Salary</label>
                            <input type="number" name="salary" class="form-control package-input" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn package-btn-save">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="facilitiesTableWrapper" class="info-card d-none">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0">Staff Type Management</h5>
            <button class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addStaffTypeModal">
                + Add Staff Type
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Staff Type</th>
                        <th width="120"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff_types as $type): ?>
                        <tr>
                            <td><?= htmlspecialchars($type['staff_type']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editStaffTypeModal<?= $type['id'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/staff_type_delete.php?id=<?= $type['id'] ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Delete this staff type?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Staff Type Modal -->
                        <div class="modal fade" id="editStaffTypeModal<?= $type['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content package-modal">
                                    <form method="POST"
                                        action="../Admin/adminBackend/staff_type_edit.php?id=<?= $type['id'] ?>">


                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">
                                                Edit Staff Type
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="mb-3">
                                                <label class="form-label package-label">
                                                    Staff Type Name
                                                </label>
                                                <input type="text" name="staff_type" class="form-control package-input"
                                                    value="<?= htmlspecialchars($type['staff_type']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn package-btn-save">
                                                Save Changes
                                            </button>
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


    <div class="modal fade" id="addStaffTypeModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content package-modal">
                <form method="POST" action="../Admin/adminBackend/staff_type_add.php">

                    <div class="modal-header package-modal-header">
                        <h5 class="modal-title fw-bold">
                            Add Staff Type
                        </h5>
                        <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body package-modal-body">
                        <div class="mb-3">
                            <label class="form-label package-label">
                                Staff Type Name
                            </label>
                            <input type="text" name="staff_type" class="form-control package-input" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn package-btn-save">
                            Add Staff Type
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>


<script>
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

<?php include 'adminFrontend/footer.php'; ?>