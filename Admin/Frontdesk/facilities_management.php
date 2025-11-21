<?php
session_start(); // Start session at the very beginning
require_once 'includes/init.php';
require_once 'db.php';

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
                    $_SESSION['success_message'] = "Category '{$name}' has been added successfully!";
                } else {
                    $_SESSION['error_message'] = "Error adding category. Please try again.";
                }
                break;

            case 'edit_category':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $display_order = $_POST['display_order'];
                $active = isset($_POST['active']) ? 1 : 0;
                $stmt = $con->prepare("UPDATE facility_categories SET name = ?, display_order = ?, active = ? WHERE id = ?");
                $stmt->bind_param("siii", $name, $display_order, $active, $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Category '{$name}' has been updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating category. Please try again.";
                }
                break;

            case 'delete_category':
                $id = $_POST['id'];
                $stmt = $con->prepare("DELETE FROM facility_categories WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Category has been deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting category. Please try again.";
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
                    $_SESSION['success_message'] = "Facility '{$name}' has been added successfully!";
                } else {
                    $_SESSION['error_message'] = "Error adding facility. Please try again.";
                }
                break;

            case 'edit_facility':
                $id = $_POST['id'];
                $category_id = $_POST['category_id'];
                $name = $_POST['name'];
                $display_order = $_POST['display_order'];
                $active = isset($_POST['active']) ? 1 : 0;
                $stmt = $con->prepare("UPDATE facilities SET category_id = ?, name = ?, display_order = ?, active = ? WHERE id = ?");
                $stmt->bind_param("isiii", $category_id, $name, $display_order, $active, $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Facility '{$name}' has been updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating facility. Please try again.";
                }
                break;

            case 'delete_facility':
                $id = $_POST['id'];
                $stmt = $con->prepare("DELETE FROM facilities WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Facility has been deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Error deleting facility. Please try again.";
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

            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); 
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); 
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

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
                                        <button class="btn btn-sm btn-primary edit-category" 
                                                data-id="<?php echo $category['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                data-display-order="<?php echo $category['display_order']; ?>"
                                                data-active="<?php echo $category['active']; ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-category" 
                                                data-id="<?php echo $category['id']; ?>">
                                            Delete
                                        </button>
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
                                        <button class="btn btn-sm btn-primary edit-facility"
                                                data-id="<?php echo $facility['id']; ?>"
                                                data-category-id="<?php echo $facility['category_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($facility['name']); ?>"
                                                data-display-order="<?php echo $facility['display_order']; ?>"
                                                data-active="<?php echo $facility['active']; ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-facility"
                                                data-id="<?php echo $facility['id']; ?>">
                                            Delete
                                        </button>
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
    
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut(500);

            // Edit Category
            $('.edit-category').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var displayOrder = $(this).data('display-order');
                var active = $(this).data('active');

                $('#edit_category_id').val(id);
                $('#edit_category_name').val(name);
                $('#edit_category_display_order').val(displayOrder);
                $('#edit_category_active').prop('checked', active == 1);

                $('#editCategoryModal').modal('show');
            });

            // Delete Category
            $('.delete-category').click(function() {
                var id = $(this).data('id');
                $('#delete_category_id').val(id);
                $('#deleteCategoryModal').modal('show');
            });

            // Edit Facility
            $('.edit-facility').click(function() {
                var id = $(this).data('id');
                var categoryId = $(this).data('category-id');
                var name = $(this).data('name');
                var displayOrder = $(this).data('display-order');
                var active = $(this).data('active');

                $('#edit_facility_id').val(id);
                $('#edit_facility_category_id').val(categoryId);
                $('#edit_facility_name').val(name);
                $('#edit_facility_display_order').val(displayOrder);
                $('#edit_facility_active').prop('checked', active == 1);

                $('#editFacilityModal').modal('show');
            });

            // Delete Facility
            $('.delete-facility').click(function() {
                var id = $(this).data('id');
                $('#delete_facility_id').val(id);
                $('#deleteFacilityModal').modal('show');
            });
        });
    </script>
</body>
</html> 