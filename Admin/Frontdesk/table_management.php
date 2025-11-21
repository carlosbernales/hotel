<?php
require_once "db.php";

// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch table packages
$sql = "SELECT * FROM table_packages ORDER BY package_name";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("Error fetching table packages: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Types Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .table-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-action {
            margin: 2px;
        }
        .content-wrapper {
            padding: 20px;
            margin-left: 250px; /* Adjust based on your sidebar width */
        }
        .modal-dialog {
            max-width: 600px;
        }
        /* Add these new styles */
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
        }
        .modal-backdrop {
            z-index: 1040;
        }
        .modal-dialog {
            margin: 1.75rem auto;
            z-index: 1060;
        }
        .modal.fade .modal-dialog {
            transform: translate(0, -25%);
            transition: transform 0.3s ease-out;
        }
        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Table Types Management Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Table Types Management</h2>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#packageModal" id="addNewPackage">
                        <i class="fa fa-plus"></i> Add New Table Type
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Image</th>
                                <th>Table Type</th>
                                <th>Capacity</th>
                                <th>Price</th>
                                <th>Available Tables</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" 
                                         class="table-image" 
                                         alt="<?php echo htmlspecialchars($row['package_name']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                <td><?php echo isset($row['price']) && $row['price'] !== null ? '₱' . number_format((float)$row['price'], 2) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['available_tables']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo ucfirst($row['status'] ?? 'active'); ?>
                                    </span>
                                    <?php if ($row['status'] === 'inactive' && !empty($row['reason'])): ?>
                                        <br>
                                        <small class="text-muted mt-1">
                                            <i class="fa fa-info-circle"></i> 
                                            Reason: <?php echo htmlspecialchars($row['reason']); ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-action btn-sm edit-package" 
                                            data-toggle="modal" 
                                            data-target="#packageModal" 
                                            data-id="<?php echo $row['id']; ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <button class="btn btn-danger btn-sm disable-table"
                                                data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-ban"></i> Disable
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-success btn-sm enable-table"
                                                data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-check"></i> Enable
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add/Edit Table Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="packageForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="packageId" name="id">
                        
                        <div class="form-group">
                            <label for="package_name">Table Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="package_name" name="package_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="capacity">Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (₱)</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="0">
                            <small class="form-text text-muted">Price will be set to 0 for capacity below 30.</small>
                        </div>

                        <div class="form-group">
                            <label for="available_tables">Available Tables <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="available_tables" name="available_tables" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Table Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep existing image when editing.</small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reason Modal -->
    <div class="modal" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disable Table Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="statusForm">
                        <input type="hidden" id="statusPackageId" name="id">
                        <div class="form-group">
                            <label for="unavailableReason">Reason for Disabling</label>
                            <select class="form-control" id="unavailableReason" name="reason" required>
                                <option value="">Select a reason</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                                <option value="Not Available">Not Available</option>
                                <option value="Reserved for Event">Reserved for Event</option>
                                <option value="Seasonal Closure">Seasonal Closure</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group" id="otherReasonDiv" style="display: none;">
                            <label for="otherReason">Specify Other Reason</label>
                            <textarea class="form-control" id="otherReason" name="other_reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="saveStatus">Confirm Disable</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        // Show/hide price field based on capacity
        $('#capacity').on('input', function() {
            var capacity = parseInt($(this).val()) || 0;
            if (capacity >= 30) {
                $('#price').prop('required', true);
                $('#price').prop('readonly', false);
            } else {
                $('#price').val(0);
                $('#price').prop('required', false);
                $('#price').prop('readonly', true);
            }
        });

        // Reset form when adding new package
        $('#addNewPackage').click(function() {
            $('#packageForm')[0].reset();
            $('#packageId').val('');
            $('#modalTitle').text('Add New Table Type');
            $('#price').val(0);
            $('#price').prop('readonly', true);
        });

        // Handle edit button click
        $('.edit-package').click(function() {
            var id = $(this).data('id');
            $('#modalTitle').text('Edit Table Type');
            
            // Show loading state in modal
            $('#packageForm')[0].reset();
            $('#packageModal .modal-body').append('<div class="loading-spinner text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            
            // Fetch package details
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    action: 'get_package',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    $('.loading-spinner').remove();
                    
                    if (response.status === 'success' && response.data) {
                        var data = response.data;
                        $('#packageId').val(data.id);
                        $('#package_name').val(data.package_name);
                        $('#capacity').val(data.capacity);
                        $('#price').val(data.price || 0);
                        $('#available_tables').val(data.available_tables);
                        $('#description').val(data.description);
                        
                        // Handle price field based on capacity
                        if (parseInt(data.capacity) >= 30) {
                            $('#price').prop('required', true);
                            $('#price').prop('readonly', false);
                        } else {
                            $('#price').val(0);
                            $('#price').prop('required', false);
                            $('#price').prop('readonly', true);
                        }
                    } else {
                        alert('Error loading table type details: ' + (response.message || 'Unknown error'));
                        $('#packageModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    $('.loading-spinner').remove();
                    console.error('AJAX Error:', error);
                    console.log('Server response:', xhr.responseText);
                    alert('Error loading table type details. Please try again.');
                    $('#packageModal').modal('hide');
                }
            });
        });

        // Handle form submission
        $('#packageForm').submit(function(e) {
            e.preventDefault();
            
            // Basic validation
            if (!$('#package_name').val().trim()) {
                alert('Please enter a table type name');
                return false;
            }
            
            if (!$('#capacity').val() || $('#capacity').val() < 1) {
                alert('Please enter a valid capacity');
                return false;
            }
            
            if (!$('#available_tables').val() || $('#available_tables').val() < 0) {
                alert('Please enter a valid number of available tables');
                return false;
            }

            var formData = new FormData(this);

            // Show loading state
            var submitBtn = $(this).find('button[type="submit"]');
            var originalText = submitBtn.text();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            // Log form data for debugging
            console.log('Submitting form data:', {
                id: $('#packageId').val(),
                package_name: $('#package_name').val(),
                capacity: $('#capacity').val(),
                price: $('#price').val(),
                available_tables: $('#available_tables').val(),
                description: $('#description').val()
            });

            $.ajax({
                url: 'process_table_package.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);
                    
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.success) {
                            alert(response.message || 'Table type saved successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to save table type'));
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Error processing server response');
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.log('Server response:', xhr.responseText);
                    alert('Error saving table type. Please try again.');
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Handle disable button click
        $(document).on('click', '.disable-table', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#statusPackageId').val(id);
            $('#statusModal').modal('show');
        });

        // Handle enable button click
        $(document).on('click', '.enable-table', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax.php',  // Fixed URL from ajax.com to ajax.php
                type: 'POST',
                data: {
                    action: 'update_package_status',
                    id: id,
                    status: 'active'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });

        // Handle reason selection change
        $('#unavailableReason').change(function() {
            if ($(this).val() === 'Other') {
                $('#otherReasonDiv').show();
                $('#otherReason').prop('required', true);
            } else {
                $('#otherReasonDiv').hide();
                $('#otherReason').prop('required', false);
            }
        });

        // Handle reason save for disable
        $('#saveStatus').click(function() {
            var id = $('#statusPackageId').val();
            var reason = $('#unavailableReason').val();
            
            if (!reason) {
                alert('Please select a reason');
                return;
            }
            
            if (reason === 'Other') {
                var otherReason = $('#otherReason').val().trim();
                if (!otherReason) {
                    alert('Please specify the other reason');
                    return;
                }
                reason = otherReason;
            }
            
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: {
                    action: 'update_package_status',
                    id: id,
                    status: 'inactive',
                    reason: reason
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating status');
                }
            });
            $('#statusModal').modal('hide');
        });

        // Reset modal fields when shown
        $('#statusModal').on('show.bs.modal', function() {
            $('#unavailableReason').val('');
            $('#otherReason').val('');
            $('#otherReasonDiv').hide();
        });
    });
    </script>
</body>
</html>