<?php
require 'db.php';
require 'header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h4 class="page-title">Table Management</h4>
                <button class="btn btn-primary float-right" style="margin-bottom: 20px;" data-toggle="modal" data-target="#addTableModal">
                    + Add New Table
                </button>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Table Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Price/Night</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM dining_tables ORDER BY table_type";
                            $result = $con->query($query);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($table = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $table['image_path'] ? $table['image_path'] : 'img/default-table.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($table['table_name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($table['table_name']); ?></td>
                                <td><?php echo htmlspecialchars($table['table_type']); ?></td>
                                <td><?php echo $table['capacity']; ?></td>
                                <td>₱<?php echo number_format($table['price'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $table['status'] == 'available' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($table['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-table" 
                                            data-id="<?php echo $table['id']; ?>"
                                            data-toggle="modal" 
                                            data-target="#editTableModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-table" 
                                            data-id="<?php echo $table['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No tables found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Table</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addTableForm" method="POST" action="process_table.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Table Name</label>
                        <input type="text" class="form-control" name="table_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Table Type</label>
                        <select class="form-control" name="table_type" required>
                            <option value="">Select Type</option>
                            <option value="Couple">Couple</option>
                            <option value="Friends">Friends</option>
                            <option value="Family">Family</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" class="form-control" name="capacity" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Table Image</label>
                        <input type="file" class="form-control" name="table_image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Table</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Table</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editTableForm" method="POST" action="process_table.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="table_id" id="edit_table_id">
                <div class="modal-body">
                    <!-- Form fields will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.main-content {
    margin-left: 240px;
    padding: 20px;
    background: #f4f6f9;
}

.page-title {
    margin-bottom: 20px;
    display: inline-block;
}

.table {
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.table th {
    border-top: none;
    background: #f8f9fa;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
}

.badge-success {
    background: #28a745;
}

.badge-warning {
    background: #ffc107;
    color: #000;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
</style>

<script>
$(document).ready(function() {
    // Handle edit button click
    $('.edit-table').click(function() {
        var tableId = $(this).data('id');
        // Fetch table data via AJAX
        $.ajax({
            url: 'get_table_data.php',
            type: 'GET',
            data: { id: tableId },
            success: function(response) {
                var table = JSON.parse(response);
                $('#edit_table_id').val(table.id);
                // Populate form fields
                var modalBody = $('#editTableForm .modal-body');
                modalBody.html(`
                    <div class="form-group">
                        <label>Table Name</label>
                        <input type="text" class="form-control" name="table_name" value="${table.table_name}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Table Type</label>
                        <select class="form-control" name="table_type" required>
                            <option value="Couple" ${table.table_type === 'Couple' ? 'selected' : ''}>Couple</option>
                            <option value="Friends" ${table.table_type === 'Friends' ? 'selected' : ''}>Friends</option>
                            <option value="Family" ${table.table_type === 'Family' ? 'selected' : ''}>Family</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" class="form-control" name="capacity" min="1" value="${table.capacity}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" min="0" step="0.01" value="${table.price}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Image</label><br>
                        <img src="${table.image_path || 'img/default-table.jpg'}" style="max-width: 100px; margin-bottom: 10px;">
                        <input type="file" class="form-control" name="table_image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="available" ${table.status === 'available' ? 'selected' : ''}>Available</option>
                            <option value="maintenance" ${table.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                        </select>
                    </div>
                `);
            }
        });
    });

    // Handle delete button click
    $('.delete-table').click(function() {
        if (confirm('Are you sure you want to delete this table?')) {
            var tableId = $(this).data('id');
            $.ajax({
                url: 'process_table.php',
                type: 'POST',
                data: { 
                    action: 'delete',
                    table_id: tableId
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });
});
</script>
