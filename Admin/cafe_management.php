<?php
require_once 'db.php';

// Create tables if they don't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_table_sql)) {
    die("Error creating menu_categories table: " . mysqli_error($con));
}

$create_menu_items_sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
)";

if (!mysqli_query($con, $create_menu_items_sql)) {
    die("Error creating menu_items table: " . mysqli_error($con));
}

// Create table if it doesn't exist
$create_addons_table_sql = "CREATE TABLE IF NOT EXISTS menu_items_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
)";

if (!mysqli_query($con, $create_addons_table_sql)) {
    die("Error creating table: " . mysqli_error($con));
}

include 'header.php';
include 'sidebar.php';

// Fetch menu categories
$categories_query = "SELECT * FROM menu_categories ORDER BY id";
$categories_result = mysqli_query($con, $categories_query);

// Fetch menu items/addons
$menu_query = "SELECT * FROM menu_items_addons ORDER BY id";
$menu_result = mysqli_query($con, $menu_query);

if (!$categories_result || !$menu_result) {
    die("Query failed: " . mysqli_error($con));
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-home"></i></a></li>
            <li class="active">Cafe Management</li>
        </ol>
    </div>

    <!-- Menu Categories Section -->
    <div class="panel panel-default">
        <div class="panel-heading">
            Menu Categories
            <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Add Category
            </button>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset the result pointer
                        mysqli_data_seek($categories_result, 0);

                        while ($category = mysqli_fetch_assoc($categories_result)):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name'] ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-category"
                                        data-id="<?php echo $category['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($category['name'] ?? ''); ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-category"
                                        data-id="<?php echo $category['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Menu Items Section -->
    <div class="panel panel-default">
        <div class="panel-heading">
            Menu Items
            <button class="btn btn-warning pull-right" data-toggle="modal" data-target="#addMenuItemModal">
                <i class="fa fa-plus"></i> Add Menu Item
            </button>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="menuItemsTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $menu_items_query = "SELECT mi.*, mc.name as category_name, 
                                           COALESCE(mi.availability, 1) as availability 
                                           FROM menu_items mi 
                                           LEFT JOIN menu_categories mc ON mi.category_id = mc.id";

                        // Ensure availability column exists and is properly set
                        $check_column = mysqli_query($con, "SHOW COLUMNS FROM menu_items LIKE 'availability'");
                        if (mysqli_num_rows($check_column) == 0) {
                            if (!mysqli_query($con, "ALTER TABLE menu_items ADD COLUMN availability TINYINT(1) DEFAULT 1")) {
                                error_log("Failed to add availability column: " . mysqli_error($con));
                            } else {
                                // Update all existing items to be available by default (1 = available, 0 = out of stock)
                                mysqli_query($con, "UPDATE menu_items SET availability = 1");
                            }
                        }
                        $menu_items_result = mysqli_query($con, $menu_items_query);
                        while ($item = mysqli_fetch_assoc($menu_items_result)):
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image_path'])): ?>
                                        <?php $imgSrc = (strpos($item['image_path'], 'http') === 0 || $item['image_path'][0] === '/') ? $item['image_path'] : '/Admin/' . $item['image_path']; ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc ?? ''); ?>"
                                            alt="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                        <div style="font-size:10px; color:#c00; word-break:break-all;">
                                            <?php echo htmlspecialchars($imgSrc ?? ''); ?>
                                        </div>
                                    <?php else: ?>
                                        <img src="/Admin/uploads/menus/default.jpg" alt="Default"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                        <div style="font-size:10px; color:#c00;">No image_path</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['category_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <button type="button"
                                        class="btn btn-xs btn-<?php echo ($item['availability'] == 1) ? 'success' : 'danger'; ?> status-label"
                                        style="min-width: 100px;" data-id="<?php echo $item['id']; ?>"
                                        data-status="<?php echo $item['availability']; ?>">
                                        <?php echo ($item['availability'] == 1) ? 'Available' : 'Out of Stock'; ?>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-addons" data-id="<?php echo $item['id']; ?>"
                                        data-category="<?php echo htmlspecialchars($item['category_name'] ?? ''); ?>"
                                        data-name="<?php echo htmlspecialchars($item['name'] ?? ''); ?>">
                                        <i class="fa fa-list"></i> Addons
                                    </button>
                                    <button class="btn btn-primary btn-sm edit-menu-item"
                                        data-id="<?php echo $item['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"
                                        data-description="<?php echo htmlspecialchars($item['description'] ?? ''); ?>"
                                        data-price="<?php echo $item['price']; ?>"
                                        data-category="<?php echo $item['category_id']; ?>"
                                        data-image="<?php echo htmlspecialchars($item['image_path'] ?? ''); ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-menu-item"
                                        data-id="<?php echo $item['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Menu Category</h4>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Add Category</button>
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Menu Category</h4>
                </div>
                <form id="editCategoryForm">
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="edit_category_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_category_name" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Menu Item Modal -->
    <div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Menu Item</h4>
                </div>
                <form id="addMenuItemForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category:</label>
                            <select class="form-control" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                mysqli_data_seek($categories_result, 0);
                                while ($category = mysqli_fetch_assoc($categories_result)):
                                    ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name'] ?? ''); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Price:</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea class="form-control" name="description" rows="3"
                                placeholder="Enter item description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Item Image:</label>
                            <input type="file" class="form-control" name="image" id="itemImage" accept="image/*">
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img src="" alt="Preview"
                                    style="max-width: 100%; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Menu Item Modal -->
    <div class="modal fade" id="editMenuItemModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Menu Item</h4>
                </div>
                <form id="editMenuItemForm" enctype="multipart/form-data">
                    <input type="hidden" name="menu_item_id" id="edit_item_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category:</label>
                            <input type="hidden" name="category_id" id="edit_category_id">
                            <p class="form-control-static" id="edit_category_display"></p>
                        </div>
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" class="form-control" name="name" id="edit_item_name" required>
                        </div>
                        <div class="form-group">
                            <label>Price:</label>
                            <input type="number" class="form-control" name="price" id="edit_item_price" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea class="form-control" name="description" id="edit_item_description" rows="3"
                                placeholder="Enter item description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Image:</label>
                            <div id="currentImagePreview" class="mt-2">
                                <img src="" alt="Current"
                                    style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>New Image (optional):</label>
                            <input type="file" class="form-control" name="image" id="editItemImage" accept="image/*">
                            <div id="newImagePreview" class="mt-2" style="display: none;">
                                <img src="" alt="Preview"
                                    style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this modal for managing addons -->
    <div class="modal fade" id="viewAddonsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Addon for <span id="selected_item_name"></span></h4>
                </div>
                <div class="modal-body">
                    <form id="addAddonForm">
                        <input type="hidden" id="selected_menu_item_id" name="menu_item_id">
                        <div class="form-group">
                            <label for="addon_name">Addon Name:</label>
                            <input type="text" class="form-control" id="addon_name" name="addon_name" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary" id="addAddonBtn">Add Addon</button>
                    </form>

                    <hr>

                    <h4>Current Addons</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="addons_list">
                            <!-- Addons will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    // Toggle menu item status
    function toggleMenuItemStatus(itemId, currentStatus) {
        console.log('Toggling status for item:', itemId, 'Current status:', currentStatus);
        var newStatus = currentStatus === '1' ? '0' : '1';

        $.ajax({
            url: 'update_menu_item_status.php',
            type: 'POST',
            data: {
                id: itemId,
                is_available: newStatus
            },
            success: function (response) {
                console.log('Server response:', response);
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result && result.success) {
                        var $statusLabel = $('.status-label[data-id="' + itemId + '"]');
                        $statusLabel.data('status', newStatus);
                        if (newStatus === '1') {
                            $statusLabel.removeClass('label-danger').addClass('label-success').text('Available');
                        } else {
                            $statusLabel.removeClass('label-success').addClass('label-danger').text('Out of Stock');
                        }
                        toastr.success('Menu item status updated successfully');
                    } else {
                        var errorMsg = result && result.message ? result.message : 'Unknown error';
                        console.error('Server returned error:', errorMsg);
                        toastr.error('Error: ' + errorMsg);
                    }
                } catch (e) {
                    console.error('Error parsing server response:', e);
                    console.error('Raw response:', response);
                    toastr.error('Error processing server response');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                toastr.error('Error updating menu item status. Please try again.');
            }
        });
    }

    $(document).ready(function () {
        // Handle status toggle
        $(document).on('click', '.status-label', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this);
            var itemId = $this.data('id');
            var currentStatus = $this.data('status');

            console.log('Status button clicked');
            console.log('Item ID:', itemId, 'Current status:', currentStatus);

            toggleMenuItemStatus(itemId, currentStatus);
            return false;
        });

        // Add Category
        $('#addCategoryForm').submit(function (e) {
            e.preventDefault();

            var name = $(this).find('input[name="name"]').val();

            $.ajax({
                url: 'cafe_management_actions.php',
                type: 'POST',
                data: {
                    action: 'add_category',
                    name: name,
                    display_name: name
                },
                success: function (response) {
                    if (response.success) {
                        alert('Category added successfully!');
                        $('#addCategoryModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error adding category');
                }
            });
        });

        // Edit Category
        $('.edit-category').click(function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var displayName = $(this).data('display-name');

            $('#edit_category_id').val(id);
            $('#edit_category_name').val(name);
            $('#edit_category_display_name').val(displayName);
            $('#editCategoryModal').modal('show');
        });

        $('#editCategoryForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'cafe_management_actions.php',
                type: 'POST',
                data: $(this).serialize() + '&action=edit_category',
                success: function (response) {
                    if (response.success) {
                        alert('Category updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Delete Category
        $('.delete-category').click(function () {
            if (confirm('Are you sure you want to delete this category?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'cafe_management_actions.php',
                    type: 'POST',
                    data: {
                        action: 'delete_category',
                        category_id: id
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Category deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            }
        });

        // Function to show Add Menu Item Modal
        window.showAddMenuItemModal = function () {
            $('#addMenuItemModal').modal('show');
        };

        // Image preview
        $('#itemImage').change(function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });

        // Add Menu Item Form Submit
        $('#addMenuItemForm').submit(function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('action', 'add_menu_item');

            $.ajax({
                url: 'cafe_management_actions.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        alert('Menu item added successfully!');
                        $('#addMenuItemModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error adding menu item');
                }
            });
        });

        // Clear form and preview when modal is closed
        $('#addMenuItemModal').on('hidden.bs.modal', function () {
            $('#addMenuItemForm')[0].reset();
            $('#imagePreview').hide();
        });

        // Edit Menu Item
        // Use event delegation for dynamically loaded elements
        $(document).on('click', '.edit-menu-item', function () {
            var $row = $(this).closest('tr');
            var id = $(this).data('id');
            var name = $(this).data('name');
            var price = $(this).data('price');
            var categoryId = $(this).data('category');
            var categoryName = $row.find('td:eq(1)').text().trim();
            var image = $(this).data('image');
            var description = $(this).data('description') || '';

            console.log('Editing menu item:', { id, name, price, categoryId, categoryName });

            // Populate the edit form
            $('#edit_item_id').val(id);
            $('#edit_item_name').val(name);
            $('#edit_item_description').val(description);
            $('#edit_item_price').val(price);
            $('#edit_category_id').val(categoryId);
            $('#edit_category_display').text(categoryName);

            // Show current image
            if (image) {
                $('#currentImagePreview img').attr('src', image);
                $('#currentImagePreview').show();
            } else {
                $('#currentImagePreview img').attr('src', 'images/default.jpg');
            }

            // Show the modal
            $('#editMenuItemModal').modal('show');
        });

        // New image preview for edit form
        $('#editItemImage').change(function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#newImagePreview img').attr('src', e.target.result);
                    $('#newImagePreview').show();
                }
                reader.readAsDataURL(file);
            } else {
                $('#newImagePreview').hide();
            }
        });

        // Edit Menu Item Form Submit
        $('#editMenuItemForm').submit(function (e) {
            e.preventDefault();

            // Get the form data
            var formData = new FormData(this);
            var categoryId = $('#edit_category_id').val();

            // Ensure category_id is included
            formData.append('action', 'edit_menu_item');
            formData.append('category_id', categoryId);

            console.log('Submitting form with category_id:', categoryId);

            $.ajax({
                url: 'cafe_management_actions.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('Server response:', response);
                    if (response.success) {
                        alert('Menu item updated successfully!');
                        $('#editMenuItemModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error updating menu item: ' + error);
                }
            });
        });

        // Clear form and preview when modal is closed
        $('#editMenuItemModal').on('hidden.bs.modal', function () {
            $('#editMenuItemForm')[0].reset();
            $('#newImagePreview').hide();
        });

        // Delete Menu Item
        $(document).on('click', '.delete-menu-item', function (e) {
            e.preventDefault(); // Prevent any default action
            console.log('Delete button clicked!');

            var itemId = $(this).data('id');
            console.log('Attempting to delete item with ID:', itemId);

            if (!itemId) {
                console.error('No item ID found!');
                alert('Error: Could not determine which item to delete.');
                return;
            }

            if (confirm('Are you sure you want to delete this menu item?')) {
                $.ajax({
                    url: 'cafe_management_actions.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete',
                        menu_item_id: itemId
                    },
                    success: function (response) {
                        console.log('Delete response:', response);
                        if (response.success) {
                            alert('Menu item deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);

                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            alert('Error deleting menu item: ' + (jsonResponse.message || error));
                        } catch (e) {
                            alert('Error deleting menu item: ' + error);
                        }
                    }
                });
            }
        });

        // View Addons Button Click
        $('.view-addons').click(function () {
            var menuItemId = $(this).data('id');
            var categoryName = $(this).data('category');
            var itemName = $(this).data('name');

            $('#selected_menu_item_id').val(menuItemId);
            $('#selected_item_name').text(itemName);
            $('#category_display').val(categoryName);

            // Load existing addons
            loadAddons(menuItemId);

            $('#viewAddonsModal').modal('show');
        });

        // Load Addons Function
        function loadAddons(menuItemId) {
            $.ajax({
                url: 'cafe_management_actions.php',
                type: 'POST',
                data: {
                    action: 'get_addons',
                    menu_item_id: menuItemId
                },
                success: function (response) {
                    if (response.success) {
                        var tbody = $('#addons_list');
                        tbody.empty();

                        response.addons.forEach(function (addon) {
                            tbody.append(`
                            <tr>
                                <td>${addon.name}</td>
                                <td>₱${parseFloat(addon.price).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-addon" data-id="${addon.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        });
                    }
                }
            });
        }

        // Handle Add Addon form submission
        $('#addAddonForm').on('submit', function (e) {
            e.preventDefault();

            const formData = {
                menu_item_id: $('#selected_menu_item_id').val(),
                addon_name: $('#addon_name').val(),
                price: $('#price').val()
            };

            $.ajax({
                url: 'add_addon.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Addon added successfully',
                            showConfirmButton: true
                        }).then((result) => {
                            // Clear form
                            $('#addAddonForm')[0].reset();
                            // Reload the addons list
                            loadAddons($('#selected_menu_item_id').val());
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add addon'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error details:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add addon. Please try again.'
                    });
                }
            });
        });

        // Delete Addon Click
        $(document).on('click', '.delete-addon', function () {
            if (confirm('Are you sure you want to delete this addon?')) {
                var addonId = $(this).data('id');
                var menuItemId = $('#selected_menu_item_id').val();

                $.ajax({
                    url: 'cafe_management_actions.php',
                    type: 'POST',
                    data: {
                        action: 'delete_addon',
                        addon_id: addonId
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Addon deleted successfully!');
                            loadAddons(menuItemId);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            }
        });

        // Categories Table
        $('#categoriesTable').DataTable({
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Menu Items Table
        $('#menuItemsTable').DataTable({
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Debug to see if the script is loaded
        console.log('Cafe management script loaded');

        // Debug to count delete buttons
        console.log('Delete buttons found:', $('.delete-menu-item').length);

        // Test click event directly
        $('.delete-menu-item').each(function () {
            console.log('Delete button ID:', $(this).data('id'));
        });
    });
</script>

<style>
    .panel-heading {
        position: relative;
        height: 50px;
        padding: 10px 15px;
        background: #333;
        color: white;
    }

    .panel-heading .btn-primary {
        position: absolute;
        right: 15px;
        top: 8px;
        background-color: #DAA520;
        border-color: #DAA520;
    }

    .panel-heading .btn-primary:hover {
        background-color: #B8860B;
        border-color: #B8860B;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .btn-sm {
        margin: 0 2px;
    }

    /* Add these styles to customize DataTables appearance */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 5px;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 15px;
    }

    .dataTables_wrapper .dataTables_info {
        float: left;
        margin-top: 15px;
    }

    .table.dataTable {
        clear: both;
        margin-top: 15px !important;
        margin-bottom: 15px !important;
    }

    /* Update DataTables styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-buttons .dt-button {
        background-color: #DAA520 !important;
        color: white !important;
        border: none !important;
        padding: 5px 15px !important;
        margin-right: 5px !important;
        border-radius: 3px !important;
    }

    .dt-buttons .dt-button:hover {
        background-color: #B8860B !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #DAA520 !important;
        color: white !important;
        border: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #B8860B !important;
        color: white !important;
        border: none !important;
    }

    table.dataTable thead th {
        background-color: #333;
        color: white;
        padding: 10px;
    }

    table.dataTable tbody td {
        padding: 8px;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
    }

    /* Add these styles for the image preview */
    #imagePreview {
        margin-top: 10px;
        text-align: center;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }

    #imagePreview img {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
    }

    /* Update the dropdown styles */
    .submenu {
        display: none;
        background: #2b2b2b;
        padding: 0;
        margin: 0;
    }

    .has-dropdown {
        position: relative;
        cursor: pointer;
    }

    .has-dropdown .fa-chevron-down {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        transition: transform 0.3s ease;
    }

    .has-dropdown .fa-chevron-down.rotate {
        transform: translateY(-50%) rotate(180deg);
    }

    .parent {
        position: relative;
    }

    .parent>a {
        padding-right: 30px !important;
    }

    .submenu a {
        padding: 10px 20px 10px 50px !important;
        font-size: 14px;
        color: #999;
        display: block;
        transition: all 0.3s ease;
    }

    .submenu a:hover,
    .submenu .active {
        background: #333;
        color: #DAA520 !important;
    }

    .submenu a em {
        margin-right: 10px;
        color: #999;
    }

    .submenu a:hover em,
    .submenu .active em {
        color: #DAA520;
    }

    /* Remove any max-height constraints */
    .submenu {
        max-height: none !important;
        transition: none !important;
    }
</style>