<?php
session_start(); // Start session at the very beginning
require_once 'includes/init.php';
require_once 'db.php';

// Variables to store edit data
$edit_category = null;
$edit_facility = null;

// Check if we're editing a category
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $con->prepare("SELECT * FROM facility_categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
    
    if ($edit_category) {
        // We found the category, now we'll show the modal with JS
        $show_edit_category_modal = true;
    }
}

// Check if we're editing a facility
if (isset($_GET['edit_facility_id']) && is_numeric($_GET['edit_facility_id'])) {
    $edit_facility_id = $_GET['edit_facility_id'];
    $stmt = $con->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $edit_facility_id);
    $stmt->execute();
    $edit_facility = $stmt->get_result()->fetch_assoc();
    
    if ($edit_facility) {
        // We found the facility, now we'll show the modal with JS
        $show_edit_facility_modal = true;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_category':
                $name = $_POST['name'];
                // Get the maximum display order and increment by 1
                $result = $con->query("SELECT MAX(display_order) as max_order FROM facility_categories");
                $row = $result->fetch_assoc();
                $display_order = ($row['max_order'] !== null) ? $row['max_order'] + 1 : 1;
                
                $stmt = $con->prepare("INSERT INTO facility_categories (name, display_order, active) VALUES (?, ?, 1)");
                $stmt->bind_param("si", $name, $display_order);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Category \"" . htmlspecialchars($name) . "\" has been added successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to add category. Please try again.";
                }
                break;

            case 'edit_category':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $display_order = $_POST['display_order'];
                $active = isset($_POST['active']) ? 1 : 0;
                
                // Validate the form data
                if (empty($name) || !is_numeric($display_order)) {
                    $_SESSION['error_message'] = "Please fill in all required fields with valid data.";
                    break;
                }
                
                $stmt = $con->prepare("UPDATE facility_categories SET name = ?, display_order = ?, active = ? WHERE id = ?");
                $stmt->bind_param("siii", $name, $display_order, $active, $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Category \"" . htmlspecialchars($name) . "\" has been updated successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to update category. Please try again.";
                }
                break;

            case 'delete_category':
                $id = $_POST['id'];
                
                // Begin transaction to ensure both operations succeed or fail together
                mysqli_begin_transaction($con);
                
                try {
                    // First delete all facilities in this category
                    $deleteFacilitiesStmt = $con->prepare("DELETE FROM facilities WHERE category_id = ?");
                    $deleteFacilitiesStmt->bind_param("i", $id);
                    $deleteFacilitiesStmt->execute();
                    
                    // Then delete the category
                    $deleteCategoryStmt = $con->prepare("DELETE FROM facility_categories WHERE id = ?");
                    $deleteCategoryStmt->bind_param("i", $id);
                    
                    if ($deleteCategoryStmt->execute()) {
                        // Commit transaction
                        mysqli_commit($con);
                        $_SESSION['success_message'] = "Category and all its facilities have been deleted successfully.";
                    } else {
                        // Rollback on error
                        mysqli_rollback($con);
                        $_SESSION['error_message'] = "Failed to delete category. Please try again.";
                    }
                } catch (Exception $e) {
                    // Rollback on exception
                    mysqli_rollback($con);
                    $_SESSION['error_message'] = $e->getMessage();
                }
                break;

            case 'add_facility':
                $category_id = $_POST['category_id'];
                $name = $_POST['name'];
                // Get the maximum display order for the specific category and increment by 1
                $stmt = $con->prepare("SELECT MAX(display_order) as max_order FROM facilities WHERE category_id = ?");
                $stmt->bind_param("i", $category_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $display_order = ($row['max_order'] !== null) ? $row['max_order'] + 1 : 1;
                
                $stmt = $con->prepare("INSERT INTO facilities (category_id, name, icon, display_order, active) VALUES (?, ?, 'check', ?, 1)");
                $stmt->bind_param("isi", $category_id, $name, $display_order);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Facility \"" . htmlspecialchars($name) . "\" has been added successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to add facility. Please try again.";
                }
                break;

            case 'edit_facility':
                $id = $_POST['id'];
                $category_id = $_POST['category_id'];
                $name = $_POST['name'];
                $display_order = $_POST['display_order'];
                $active = isset($_POST['active']) ? 1 : 0;
                
                // Validate the form data
                if (empty($name) || !is_numeric($display_order)) {
                    $_SESSION['error_message'] = "Please fill in all required fields with valid data.";
                    break;
                }
                
                $stmt = $con->prepare("UPDATE facilities SET category_id = ?, name = ?, display_order = ?, active = ? WHERE id = ?");
                $stmt->bind_param("isiii", $category_id, $name, $display_order, $active, $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Facility \"" . htmlspecialchars($name) . "\" has been updated successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to update facility. Please try again.";
                }
                break;

            case 'delete_facility':
                $id = $_POST['id'];
                $stmt = $con->prepare("DELETE FROM facilities WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Facility has been deleted successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to delete facility. Please try again.";
                }
                break;
        }
        
        // Redirect to prevent form resubmission
        header("Location: facilities_management.php");
        exit();
    }
}

// Get facility categories
$categories = $con->query("SELECT * FROM facility_categories ORDER BY display_order");

// Get facilities
$facilities = $con->query("SELECT f.*, fc.name as category_name 
                          FROM facilities f 
                          LEFT JOIN facility_categories fc ON f.category_id = fc.id 
                          ORDER BY f.category_id, f.display_order");

// Edit Facility
if (isset($_GET['edit_facility_id']) && is_numeric($_GET['edit_facility_id'])) {
    $edit_id = $_GET['edit_facility_id'];
    $stmt = $con->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $facility = $result->fetch_assoc();

    if ($facility) {
        // Show edit form for facility
        include('header.php');
        include('sidebar.php');
        ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="mb-4">Edit Facility</h2>
                
                <div class="card">
                    <div class="card-header">
                        Edit Facility Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="facilities_management.php">
                            <input type="hidden" name="action" value="edit_facility">
                            <input type="hidden" name="id" value="<?php echo $facility['id']; ?>">
                            
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" name="category_id" id="category_id" required>
                                    <?php
                                    $categories = $con->query("SELECT * FROM facility_categories ORDER BY display_order");
                                    while ($category = $categories->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $facility['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($facility['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" class="form-control" name="display_order" id="display_order" value="<?php echo $facility['display_order']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="active" id="active" <?php echo $facility['active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="facilities_management.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

// Edit Category
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $con->prepare("SELECT * FROM facility_categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();

    if ($category) {
        // Show edit form for category
        include('header.php');
        include('sidebar.php');
        ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="mb-4">Edit Facility Category</h2>
                
                <div class="card">
                    <div class="card-header">
                        Edit Category Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="facilities_management.php">
                            <input type="hidden" name="action" value="edit_category">
                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="display_order">Display Order</label>
                                <input type="number" class="form-control" name="display_order" id="display_order" value="<?php echo $category['display_order']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="active" id="active" <?php echo $category['active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="facilities_management.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .content-wrapper {
            padding: 20px;
        }
        .tab-content {
            padding-top: 20px;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <h2 class="mb-4">Facilities Management</h2>

            <!-- Success and error messages are now displayed via SweetAlert -->

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="categories-tab" data-toggle="tab" href="#categories" role="tab">
                        Facility Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="facilities-tab" data-toggle="tab" href="#facilities" role="tab">
                        Facilities
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <!-- Facility Categories Tab -->
                <div class="tab-pane fade show active" id="categories" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                            Add Category
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Display Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($category = $categories->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo $category['display_order']; ?></td>
                                    <td>
                                        <?php echo $category['active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <form method="GET" action="facilities_management.php" style="display:inline;">
                                            <input type="hidden" name="edit_id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" class="btn btn-warning">Edit</button>
                                        </form>
                                        &nbsp;
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            <input type="hidden" name="action" value="delete_category">
                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Facilities Tab -->
                <div class="tab-pane fade" id="facilities" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFacilityModal">
                            Add Facility
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Display Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($facility = $facilities->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $facility['id']; ?></td>
                                    <td><?php echo htmlspecialchars($facility['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($facility['name']); ?></td>
                                    <td><?php echo $facility['display_order']; ?></td>
                                    <td>
                                        <?php echo $facility['active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <form method="GET" action="facilities_management.php" style="display:inline;">
                                            <input type="hidden" name="edit_facility_id" value="<?php echo $facility['id']; ?>">
                                            <button type="submit" class="btn btn-warning">Edit</button>
                                        </form>
                                        &nbsp;
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this facility?');">
                                            <input type="hidden" name="action" value="delete_facility">
                                            <input type="hidden" name="id" value="<?php echo $facility['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Facility Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_category">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Facility Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_category">
                        <input type="hidden" name="id" id="edit_category_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_category_name" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" class="form-control" name="display_order" id="edit_category_display_order" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="active" id="edit_category_active">
                                <label class="custom-control-label" for="edit_category_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Facility Modal -->
    <div class="modal fade" id="addFacilityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Facility</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_facility">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category_id" required>
                                <?php
                                $categories->data_seek(0);
                                while ($category = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Facility</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Facility Modal -->
    <div class="modal fade" id="editFacilityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Facility</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_facility">
                        <input type="hidden" name="id" id="edit_facility_id">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category_id" id="edit_facility_category_id" required>
                                <?php
                                $categories->data_seek(0);
                                while ($category = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_facility_name" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" class="form-control" name="display_order" id="edit_facility_display_order" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="active" id="edit_facility_active">
                                <label class="custom-control-label" for="edit_facility_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="id" id="delete_category_id">
                        <p>Are you sure you want to delete this category? This will also delete all facilities in this category.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Facility Modal -->
    <div class="modal fade" id="deleteFacilityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Facility</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_facility">
                        <input type="hidden" name="id" id="delete_facility_id">
                        <p>Are you sure you want to delete this facility?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Direct function to edit category
        function editCategory(id, name, displayOrder, active) {
            console.log('Edit category function called with:', {id, name, displayOrder, active});
            
            $('#edit_category_id').val(id);
            $('#edit_category_name').val(name);
            $('#edit_category_display_order').val(displayOrder);
            $('#edit_category_active').prop('checked', active == 1);
            
            $('#editCategoryModal').modal('show');
        }
        
        // Direct function to delete category
        function deleteCategory(id) {
            console.log('Delete category function called with ID:', id);
            
            $('#delete_category_id').val(id);
            $('#deleteCategoryModal').modal('show');
        }
        
        // Direct function to edit facility
        function editFacility(id, categoryId, name, displayOrder, active) {
            console.log('Edit facility function called with:', {id, categoryId, name, displayOrder, active});
            
            $('#edit_facility_id').val(id);
            $('#edit_facility_category_id').val(categoryId);
            $('#edit_facility_name').val(name);
            $('#edit_facility_display_order').val(displayOrder);
            $('#edit_facility_active').prop('checked', active == 1);
            
            $('#editFacilityModal').modal('show');
        }
        
        // Direct function to delete facility
        function deleteFacility(id) {
            console.log('Delete facility function called with ID:', id);
            
            $('#delete_facility_id').val(id);
            $('#deleteFacilityModal').modal('show');
        }
        
        $(document).ready(function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?php echo str_replace("'", "\'", strip_tags($_SESSION['success_message'], '<b><strong>')); ?>',
                    showConfirmButton: true,
                    timer: 3000
                });
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?php echo str_replace("'", "\'", strip_tags($_SESSION['error_message'], '<b><strong>')); ?>',
                    showConfirmButton: true
                });
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut(500);

            console.log('Document ready - initializing event handlers');

            // Edit Category - Using event delegation for better handling
            $(document).on('click', '.edit-category-btn', function(e) {
                e.preventDefault();
                console.log('Edit category clicked');
                
                var id = $(this).data('id');
                var name = $(this).data('name');
                var displayOrder = $(this).data('display-order');
                var active = $(this).data('active');

                console.log('Category data:', { id, name, displayOrder, active });

                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);
                $('#edit_category_display_order').val(displayOrder);
                $('#edit_category_active').prop('checked', active == 1);

                $('#editCategoryModal').modal('show');
            });

            // Delete Category - Using event delegation for better handling
            $(document).on('click', '.delete-category-btn', function(e) {
                e.preventDefault();
                console.log('Delete category clicked');
                
                var id = $(this).data('id');
                console.log('Category ID to delete:', id);
                
                $('#delete_category_id').val(id);
                $('#deleteCategoryModal').modal('show');
            });

            // Edit Facility - Using event delegation for better handling
            $(document).on('click', '.edit-facility-btn', function(e) {
                e.preventDefault();
                console.log('Edit facility clicked');
                
                var id = $(this).data('id');
                var categoryId = $(this).data('category-id');
                var name = $(this).data('name');
                var displayOrder = $(this).data('display-order');
                var active = $(this).data('active');

                console.log('Facility data:', { id, categoryId, name, displayOrder, active });

                $('#edit_facility_id').val(id);
                $('#edit_facility_category_id').val(categoryId);
                $('#edit_facility_name').val(name);
                $('#edit_facility_display_order').val(displayOrder);
                $('#edit_facility_active').prop('checked', active == 1);

                $('#editFacilityModal').modal('show');
            });

            // Delete Facility - Using event delegation for better handling
            $(document).on('click', '.delete-facility-btn', function(e) {
                e.preventDefault();
                console.log('Delete facility clicked');
                
                var id = $(this).data('id');
                console.log('Facility ID to delete:', id);
                
                $('#delete_facility_id').val(id);
                $('#deleteFacilityModal').modal('show');
            });
            
            console.log('Event handlers initialized');
            
            <?php if (isset($show_edit_category_modal) && $show_edit_category_modal && $edit_category): ?>
            // Show edit category modal and populate fields
            $('#edit_category_id').val('<?php echo $edit_category['id']; ?>');
            $('#edit_category_name').val('<?php echo htmlspecialchars($edit_category['name']); ?>');
            $('#edit_category_display_order').val('<?php echo $edit_category['display_order']; ?>');
            $('#edit_category_active').prop('checked', <?php echo $edit_category['active'] ? 'true' : 'false'; ?>);
            $('#editCategoryModal').modal('show');
            <?php endif; ?>
            
            <?php if (isset($show_edit_facility_modal) && $show_edit_facility_modal && $edit_facility): ?>
            // Show edit facility modal and populate fields
            $('#edit_facility_id').val('<?php echo $edit_facility['id']; ?>');
            $('#edit_facility_category_id').val('<?php echo $edit_facility['category_id']; ?>');
            $('#edit_facility_name').val('<?php echo htmlspecialchars($edit_facility['name']); ?>');
            $('#edit_facility_display_order').val('<?php echo $edit_facility['display_order']; ?>');
            $('#edit_facility_active').prop('checked', <?php echo $edit_facility['active'] ? 'true' : 'false'; ?>);
            $('#editFacilityModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html> 