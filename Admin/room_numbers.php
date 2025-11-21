<?php
require_once 'db.php';

// Get all room numbers
$room_numbers = [];
$room_numbers_query = "SELECT r.id, r.room_number, r.floor, r.status, r.room_type_id, rt.room_type 
                       FROM rooms r 
                       JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                       ORDER BY r.room_type_id, r.room_number";
$room_numbers_result = $con->query($room_numbers_query);
if ($room_numbers_result) {
    while ($row = $room_numbers_result->fetch_assoc()) {
        $room_numbers[] = $row;
    }
}

// Get room types for dropdown
$room_types = [];
$result = $con->query("SELECT * FROM room_types WHERE status = 'active' OR status IS NULL ORDER BY room_type_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_types[] = $row;
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i></a></li>
            <li><a href="room_management.php">Room Management</a></li>
            <li class="active">Room Numbers</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Room Numbers Management</h2>
                    <div class="pull-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addRoomNumberModal">
                            <i class="fa fa-plus"></i> Add Room Number
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="alertContainer"></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="roomNumbersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Room Number</th>
                                    <th>Room Type</th>
                                    <th>Floor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($room_numbers as $room): ?>
                                <tr>
                                    <td><?php echo $room['id']; ?></td>
                                    <td><?php echo $room['room_number']; ?></td>
                                    <td><?php echo $room['room_type']; ?></td>
                                    <td><?php echo $room['floor']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $room['status'] == 'Available' ? 'success' : ($room['status'] == 'Occupied' ? 'warning' : 'danger'); ?>">
                                            <?php echo $room['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info editRoomNumberBtn" data-id="<?php echo $room['id']; ?>">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteRoomNumberBtn" data-id="<?php echo $room['id']; ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Number Modal -->
<div class="modal fade" id="addRoomNumberModal" tabindex="-1" role="dialog" aria-labelledby="addRoomNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomNumberModalLabel">Add Room Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addRoomNumberForm">
                    <input type="hidden" name="action" value="add_room_number">
                    
                    <div class="form-group">
                        <label for="room_type_id">Room Type</label>
                        <select class="form-control" id="room_type_id" name="room_type_id" required>
                            <option value="">Select Room Type</option>
                            <?php foreach ($room_types as $type): ?>
                            <option value="<?php echo $type['room_type_id']; ?>"><?php echo $type['room_type']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="room_number">Room Number</label>
                        <input type="text" class="form-control" id="room_number" name="room_number" required placeholder="e.g., 101">
                    </div>
                    
                    <div class="form-group">
                        <label for="floor">Floor</label>
                        <input type="text" class="form-control" id="floor" name="floor" placeholder="e.g., 1st Floor">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="e.g., Corner room with sea view"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="available">Available</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Reserved">Reserved</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRoomNumber">Save Room</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Number Modal -->
<div class="modal fade" id="editRoomNumberModal" tabindex="-1" role="dialog" aria-labelledby="editRoomNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomNumberModalLabel">Edit Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editRoomNumberForm">
                    <input type="hidden" name="action" value="edit_room_number">
                    <input type="hidden" id="edit_room_id" name="room_id">
                    
                    <div class="form-group">
                        <label for="edit_room_type_id">Room Type</label>
                        <select class="form-control" id="edit_room_type_id" name="room_type_id" required>
                            <option value="">Select Room Type</option>
                            <?php foreach ($room_types as $type): ?>
                            <option value="<?php echo $type['room_type_id']; ?>"><?php echo $type['room_type']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
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
                        <select class="form-control" id="edit_status" name="status">
                            <option value="Available">Available</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Occupied">Occupied</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateRoomNumber">Update Room</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable for room numbers
    $('#roomNumbersTable').DataTable({
        "order": [[2, "asc"], [1, "asc"]], // Sort by room type, then room number
        "columnDefs": [
            { "width": "10%", "targets": 5 } // Actions column width
        ]
    });
    
    // Handle adding new room number
    $('#saveRoomNumber').on('click', function() {
        var formData = new FormData($('#addRoomNumberForm')[0]);
        
        // Log the form data to verify it's being captured
        console.log("Form data being submitted:");
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        $.ajax({
            url: 'process_room_management.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log("Response received:", response);
                try {
                    // Handle response as an object
                    if (response && typeof response === 'object') {
                        if (response.success) {
                            $('#addRoomNumberModal').modal('hide');
                            showAlert('success', response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', response.message || 'An error occurred');
                        }
                    } 
                    // Handle response as a string that needs parsing
                    else if (typeof response === 'string') {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $('#addRoomNumberModal').modal('hide');
                            showAlert('success', data.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', data.message || 'An error occurred');
                        }
                    }
                    else {
                        throw new Error('Invalid response format');
                    }
                } catch (e) {
                    console.error("Error processing response:", e);
                    console.error("Raw response:", response);
                    showAlert('error', 'Invalid response from server: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.error("Response Text:", xhr.responseText);
                console.error("Status Code:", xhr.status);
                showAlert('error', 'Server error: ' + error + ' (Status: ' + xhr.status + ')');
            }
        });
    });
    
    // Handle editing room number - use event delegation
    $(document).on('click', '.editRoomNumberBtn', function() {
        var roomId = $(this).data('id');
        console.log("Editing room ID:", roomId);
        
        // Get room details via AJAX
        $.ajax({
            url: 'process_room_management.php',
            type: 'POST',
            data: {
                action: 'get_room_number',
                room_id: roomId
            },
            dataType: 'json',
            success: function(response) {
                console.log("Edit response:", response);
                try {
                    // Handle response as an object
                    if (response && typeof response === 'object') {
                        if (response.success) {
                            var room = response.room;
                            
                            // Populate the form fields
                            $('#edit_room_id').val(room.id);
                            $('#edit_room_type_id').val(room.room_type_id);
                            $('#edit_room_number').val(room.room_number);
                            $('#edit_floor').val(room.floor);
                            $('#edit_description').val(room.description);
                            $('#edit_status').val(room.status);
                            
                            // Show the modal
                            $('#editRoomNumberModal').modal('show');
                        } else {
                            showAlert('error', response.message || 'Failed to retrieve room details');
                        }
                    }
                    // Handle response as a string that needs parsing
                    else if (typeof response === 'string') {
                        var data = JSON.parse(response);
                        if (data.success) {
                            var room = data.room;
                            
                            // Populate the form fields
                            $('#edit_room_id').val(room.id);
                            $('#edit_room_type_id').val(room.room_type_id);
                            $('#edit_room_number').val(room.room_number);
                            $('#edit_floor').val(room.floor);
                            $('#edit_description').val(room.description);
                            $('#edit_status').val(room.status);
                            
                            // Show the modal
                            $('#editRoomNumberModal').modal('show');
                        } else {
                            showAlert('error', data.message || 'Failed to retrieve room details');
                        }
                    }
                    else {
                        throw new Error('Invalid response format');
                    }
                } catch (e) {
                    console.error("Error processing edit response:", e);
                    console.error("Raw response:", response);
                    showAlert('error', 'Invalid response from server while fetching room data: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error on edit:", status, error);
                console.error("Response Text:", xhr.responseText);
                console.error("Status Code:", xhr.status);
                showAlert('error', 'Server error when fetching room data: ' + error + ' (Status: ' + xhr.status + ')');
            }
        });
    });
    
    // Handle updating room number
    $('#updateRoomNumber').on('click', function() {
        var formData = new FormData($('#editRoomNumberForm')[0]);
        
        // Log the update form data
        console.log("Update form data:");
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        $.ajax({
            url: 'process_room_management.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log("Update response:", response);
                try {
                    // Handle response as an object
                    if (response && typeof response === 'object') {
                        if (response.success) {
                            $('#editRoomNumberModal').modal('hide');
                            showAlert('success', response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', response.message || 'Failed to update room');
                        }
                    }
                    // Handle response as a string that needs parsing
                    else if (typeof response === 'string') {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $('#editRoomNumberModal').modal('hide');
                            showAlert('success', data.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert('error', data.message || 'Failed to update room');
                        }
                    }
                    else {
                        throw new Error('Invalid response format');
                    }
                } catch (e) {
                    console.error("Error processing update response:", e);
                    console.error("Raw response:", response);
                    showAlert('error', 'Invalid response from server while updating: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error on update:", status, error);
                console.error("Response Text:", xhr.responseText);
                console.error("Status Code:", xhr.status);
                showAlert('error', 'Server error when updating: ' + error + ' (Status: ' + xhr.status + ')');
            }
        });
    });
    
    // Handle deleting room number - use event delegation
    $(document).on('click', '.deleteRoomNumberBtn', function() {
        var roomId = $(this).data('id');
        console.log("Deleting room ID:", roomId);
        
        if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
            $.ajax({
                url: 'process_room_management.php',
                type: 'POST',
                data: {
                    action: 'delete_room_number',
                    room_id: roomId
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Delete response:", response);
                    try {
                        // Handle response as an object
                        if (response && typeof response === 'object') {
                            if (response.success) {
                                showAlert('success', response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                showAlert('error', response.message || 'Failed to delete room');
                            }
                        }
                        // Handle response as a string that needs parsing
                        else if (typeof response === 'string') {
                            var data = JSON.parse(response);
                            if (data.success) {
                                showAlert('success', data.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                showAlert('error', data.message || 'Failed to delete room');
                            }
                        }
                        else {
                            throw new Error('Invalid response format');
                        }
                    } catch (e) {
                        console.error("Error processing delete response:", e);
                        console.error("Raw response:", response);
                        showAlert('error', 'Invalid response from server while deleting: ' + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error on delete:", status, error);
                    console.error("Response Text:", xhr.responseText);
                    console.error("Status Code:", xhr.status);
                    showAlert('error', 'Server error when deleting: ' + error + ' (Status: ' + xhr.status + ')');
                }
            });
        }
    });
});

function showAlert(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show">' + 
                    message + 
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';
    
    $('#alertContainer').html(alertHtml);
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}
</script>

<?php include 'footer.php'; ?>