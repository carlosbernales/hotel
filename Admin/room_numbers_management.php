<?php
require_once 'includes/init.php';
require_once 'db.php';

// Fetch all room types to populate the dropdown/list
$room_types = [];
$result = $con->query("SELECT room_type_id, room_type FROM room_types WHERE status = 'active' OR status IS NULL ORDER BY room_type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_types[] = $row;
    }
}

// Include header and sidebar
include 'header.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Room Numbers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px; /* Width of the sidebar */
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Manage Room Numbers</h2>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    Select Room Type
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="roomTypeSelect">Choose a Room Type:</label>
                        <select class="form-control" id="roomTypeSelect">
                            <option value="">-- Select a Room Type --</option>
                            <?php foreach ($room_types as $type): ?>
                                <option value="<?php echo $type['room_type_id']; ?>">
                                    <?php echo htmlspecialchars($type['room_type']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div id="roomNumbersSection" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 id="selectedRoomTypeName">Room Numbers for: </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm" id="addSingleRoomBtn" data-toggle="modal" data-target="#addSingleRoomModal">
                            <i class="fas fa-plus"></i> Add Room Number
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="addMultipleRoomsBtn" data-toggle="modal" data-target="#addRoomNumberModal">
                            <i class="fas fa-plus"></i> Add Multiple Room Numbers
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Room Numbers
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Floor</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="roomNumbersTableBody">
                                    <!-- Room numbers will be loaded here via AJAX -->
                                    <tr>
                                        <td colspan="5" class="text-center">Select a room type above to view room numbers.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Single Room Number Modal -->
    <div class="modal fade" id="addSingleRoomModal" tabindex="-1" role="dialog" aria-labelledby="addSingleRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSingleRoomModalLabel">Add Room Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addSingleRoomForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_room_number">
                        <input type="hidden" id="add_single_room_type_id" name="room_type_id">
                        
                        <div class="form-group">
                            <label for="single_room_number">Room Number</label>
                            <input type="text" class="form-control" id="single_room_number" name="room_number" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="single_floor">Floor</label>
                                <input type="text" class="form-control" id="single_floor" name="floor" placeholder="Optional">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="single_status">Status</label>
                                <select class="form-control" id="single_status" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="Occupied">Occupied</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="single_description">Description</label>
                            <textarea class="form-control" id="single_description" name="description" rows="2" placeholder="Optional description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Room Number</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Multiple Room Numbers Modal -->
    <div class="modal fade" id="addRoomNumberModal" tabindex="-1" role="dialog" aria-labelledby="addRoomNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomNumberModalLabel">Add Multiple Room Numbers</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addRoomNumberForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_room_numbers">
                        <input type="hidden" id="add_room_type_id" name="room_type_id">
                        
                        <div class="form-group">
                            <label>Room Numbers <small class="text-muted">(One per line or comma-separated)</small></label>
                            <textarea class="form-control" id="room_numbers" name="room_numbers" rows="5" placeholder="E.g.: 101, 102, 103&#10;Or:&#10;201&#10;202&#10;203" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="floor">Floor</label>
                                <input type="text" class="form-control" id="floor" name="floor" placeholder="Optional">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="Occupied">Occupied</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add All Room Numbers</button>
                    
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Number Modal -->
    <div class="modal fade" id="editRoomNumberModal" tabindex="-1" role="dialog" aria-labelledby="editRoomNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomNumberModalLabel">Edit Room Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editRoomNumberForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_room_number">
                        <input type="hidden" id="edit_room_number_id" name="room_id">
                        
                        <div class="form-group">
                            <label for="edit_room_number">Room Number</label>
                            <input type="text" class="form-control" id="edit_room_number" name="room_number" required>
                        </div>

                         <div class="form-group">
                            <label for="edit_floor">Floor</label>
                            <input type="text" class="form-control" id="edit_floor" name="floor">
                        </div>
                        
                         <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Set room type ID for both modals when a room type is selected
            $('#roomTypeSelect').change(function() {
                const roomTypeId = $(this).val();
                const roomTypeName = $(this).find('option:selected').text();
                
                // Update both modals with the selected room type ID
                $('#add_room_type_id, #add_single_room_type_id').val(roomTypeId);
                
                // Show/hide room numbers section based on selection
                if (roomTypeId) {
                    $('#roomNumbersSection').show();
                    $('#selectedRoomTypeName').text('Room Numbers for: ' + roomTypeName);
                    loadRoomNumbers(roomTypeId);
                } else {
                    $('#roomNumbersSection').hide();
                }
            });
            
            // Set room type ID when opening modals (in case user clicks button before selecting room type)
            $('#addSingleRoomBtn, #addMultipleRoomsBtn').click(function() {
                const roomTypeId = $('#roomTypeSelect').val();
                if (!roomTypeId) {
                    alert('Please select a room type first');
                    return false;
                }
                $('#add_room_type_id, #add_single_room_type_id').val(roomTypeId);
            });
            
            // Handle single room form submission
            $('#addSingleRoomForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                
                // Disable button and show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
                
                $.ajax({
                    url: 'process_room_management.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;
                            
                            // Insert alert before the room numbers section
                            $('#roomNumbersSection').before(alertHtml);
                            
                            // Close modal and reload room numbers
                            $('#addSingleRoomModal').modal('hide');
                            loadRoomNumbers($('#roomTypeSelect').val());
                            
                            // Reset form
                            $('#addSingleRoomForm')[0].reset();
                        } else {
                            // Show error message
                            Swal.fire('Error', response.message || 'Failed to add room number', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred while adding the room number', 'error');
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
            
            // Clear modals on close
            $('#addSingleRoomModal').on('hidden.bs.modal', function () {
                $('#addSingleRoomForm')[0].reset();
            });
            
            // Function to load all room numbers
            function loadRoomNumbers() {
                // Show loading spinner
                const $loadingRow = $('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                $('#roomNumbersTableBody').html($loadingRow);
                
                // Clear any previous errors
                $('.alert-danger').remove();
                
                $.ajax({
                    url: 'process_room_management.php',
                    method: 'GET',
                    data: { 
                        action: 'get_room_numbers_by_type',
                        _: new Date().getTime() // Prevent caching
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX response:', response);
                        
                        try {
                            if (!response.success) {
                                throw new Error(response.message || 'Failed to load room numbers');
                            }
                            
                            const $tableBody = $('#roomNumbersTableBody');
                            $tableBody.empty(); // Clear loading row
                            
                            if (response.room_numbers && response.room_numbers.length > 0) {
                                console.log('Processing', response.room_numbers.length, 'room numbers');
                                
                                response.room_numbers.forEach(function(room) {
                                    // Format status badge
                                    const statusClass = {
                                        'available': 'success',
                                        'occupied': 'warning',
                                        'maintenance': 'danger',
                                        'cleaning': 'info'
                                    }[room.status] || 'secondary';
                                    
                                    const statusText = room.status 
                                        ? room.status.charAt(0).toUpperCase() + room.status.slice(1)
                                        : 'Unknown';
                                    
                                    // Create table row
                                    const $row = $(`
                                        <tr>
                                            <td>${room.room_number_id || 'N/A'}</td>
                                            <td>${room.room_number || 'N/A'}</td>
                                            <td>${room.room_type_name || 'No Type'}</td>
                                            <td>${room.floor !== null ? room.floor : 'N/A'}</td>
                                            <td><span class="badge badge-${statusClass}">${statusText}</span></td>
                                            <td>${room.description || 'No description'}</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm edit-room-number" data-id="${room.room_number_id}" data-toggle="modal" data-target="#editRoomNumberModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm delete-room-number ml-1" data-id="${room.room_number_id}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `);
                                    
                                    $tableBody.append($row);
                                });
                                
                                // Initialize tooltips
                                $('[data-toggle="tooltip"]').tooltip();
                                
                            } else {
                                $tableBody.html('<tr><td colspan="7" class="text-center text-muted">No room numbers found in the system.</td></tr>');
                            }
                            
                            // Show the table
                            $('#roomNumbersSection').show();
                            
                        } catch (e) {
                            console.error('Error processing response:', e);
                            const errorMsg = e.message || 'Error displaying room numbers';
                            $('#roomNumbersTableBody').html(`<tr><td colspan="7" class="text-center text-danger">${errorMsg}</td></tr>`);
                            
                            // Show additional error details if available
                            if (response && response.sql) {
                                console.error('SQL Query:', response.sql);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', { status: status, error: error, response: xhr.responseText });
                        let errorMsg = 'Error loading room numbers. Please try again.';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                        }
                        
                        $('#roomNumbersTableBody').html(`<tr><td colspan="7" class="text-center text-danger">${errorMsg}</td></tr>`);
                    },
                    complete: function() {
                        // Any cleanup after request completes
                    }
                });
            }

            // Handle page load and room type selection change
            $(document).ready(function() {
                // Load all room numbers when page loads
                loadRoomNumbers();
                
                // Handle room type selection change
                $('#room_type_filter').on('change', function() {
                    loadRoomNumbers();
                });
            });

            // Handle room type selection change
            $('#roomTypeSelect').change(function() {
                const selectedRoomTypeId = $(this).val();
                const selectedRoomTypeName = $(this).find('option:selected').text();
                $('#selectedRoomTypeName').text('Room Numbers for: ' + selectedRoomTypeName);
                loadRoomNumbers(selectedRoomTypeId);
            });
            
            // Handle form submission for adding multiple room numbers
            $('#addRoomNumberForm').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                
                // Disable button and show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
                
                $.ajax({
                    url: 'process_room_management.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const successMsg = `Successfully added ${response.added_count} room number(s).`;
                            if (response.duplicates && response.duplicates.length > 0) {
                                successMsg += ` ${response.duplicates.length} room number(s) already exist and were skipped.`;
                            }
                            
                            // Show success message
                            const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${successMsg}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;
                            
                            // Insert alert before the room numbers section
                            $('#roomNumbersSection').before(alertHtml);
                            
                            // Close modal and reload room numbers
                            $('#addRoomNumberModal').modal('hide');
                            loadRoomNumbers($('#roomTypeSelect').val());
                            
                            // Reset form
                            $('#addRoomNumberForm')[0].reset();
                        } else {
                            // Show error message
                            alert('Error: ' + (response.message || 'Failed to add room numbers.'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while adding room numbers. Please try again.');
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Handle Edit Room Number button click
            $(document).on('click', '.edit-room-number', function() {
                 const roomId = $(this).data('id');
                 // Fetch room number details
                 $.ajax({
                    url: 'process_room_management.php',
                    method: 'GET',
                    data: { action: 'get_room_number', room_id: roomId },
                    success: function(response) {
                         try {
                             const result = JSON.parse(response);
                             if (result.success) {
                                $('#edit_room_number_id').val(result.room.id);
                                $('#edit_room_number').val(result.room.room_number);
                                $('#edit_floor').val(result.room.floor);
                                $('#edit_description').val(result.room.description);
                                $('#edit_status').val(result.room.status);
                                $('#editRoomNumberModal').modal('show');
                             } else {
                                Swal.fire('Error!', result.message, 'error');
                             }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            Swal.fire('Error!', 'Error loading room number details.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                         Swal.fire('Error!', 'Error fetching room number details: ' + error, 'error');
                    }
                 });
            });

            // Handle Edit Room Number form submission
            $('#editRoomNumberForm').submit(function(e) {
                 e.preventDefault();
                 $.ajax({
                    url: 'process_room_management.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        try {
                             const result = JSON.parse(response);
                             if (result.success) {
                                Swal.fire('Success!', result.message, 'success');
                                $('#editRoomNumberModal').modal('hide');
                                // Reload room numbers for the current room type
                                loadRoomNumbers($('#roomTypeSelect').val());
                             } else {
                                Swal.fire('Error!', result.message, 'error');
                             }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            Swal.fire('Error!', 'Error processing response.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                         Swal.fire('Error!', 'Error updating room number: ' + error, 'error');
                    }
                 });
            });

            // Handle Delete Room Number button click
            $(document).on('click', '.delete-room-number', function() {
                 const roomId = $(this).data('id');
                 Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                 }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'process_room_management.php',
                            method: 'POST',
                            data: { action: 'delete_room_number', room_id: roomId },
                            success: function(response) {
                                try {
                                     const result = JSON.parse(response);
                                     if (result.success) {
                                        Swal.fire('Deleted!', result.message, 'success');
                                        // Reload room numbers for the current room type
                                        loadRoomNumbers($('#roomTypeSelect').val());
                                     } else {
                                        Swal.fire('Error!', result.message, 'error');
                                     }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    Swal.fire('Error!', 'Error processing response.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                 Swal.fire('Error!', 'Error deleting room number: ' + error, 'error');
                            }
                        });
                    }
                 });
            });

            // Clear modals on close
            $('#addRoomNumberModal').on('hidden.bs.modal', function () {
                $('#addRoomNumberForm')[0].reset();
            });
            $('#editRoomNumberModal').on('hidden.bs.modal', function () {
                $('#editRoomNumberForm')[0].reset();
            });

        });

        // Need to add a new case 'get_room_numbers_by_type' in process_room_management.php
        // and also ensure 'get_room_number', 'edit_room_number', 'delete_room_number'
        // correctly interact with the 'rooms' table based on 'room_number' and 'room_type_id'
        // rather than just 'id' if that was the old behavior.

    </script>
</body>
</html>