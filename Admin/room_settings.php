<?php
require_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_room'])) {
        $room_type = mysqli_real_escape_string($con, $_POST['room_type']);
        $room_number = mysqli_real_escape_string($con, $_POST['room_number']);
        $price = floatval($_POST['price']);
        $capacity = intval($_POST['capacity']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        
        $sql = "INSERT INTO rooms (room_type, room_number, price, capacity, description, status) 
                VALUES (?, ?, ?, ?, ?, 'Available')";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdis', $room_type, $room_number, $price, $capacity, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Room added successfully!";
        } else {
            $_SESSION['error'] = "Error adding room: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get existing rooms
$rooms_sql = "SELECT * FROM rooms ORDER BY room_type, room_number";
$rooms_result = mysqli_query($con, $rooms_sql);

if (!$rooms_result) {
    $_SESSION['error'] = "Error fetching rooms: " . mysqli_error($con);
    $rooms_result = false;
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active">Settings</li>
            <li class="active">Room Management</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Room Management</h2>
                    <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addRoomModal">
                        <em class="fa fa-plus"></em> Add New Room
                    </button>
                </div>
                <div class="panel-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Room Number</th>
                                    <th>Price</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($rooms_result && mysqli_num_rows($rooms_result) > 0):
                                    while ($room = mysqli_fetch_assoc($rooms_result)): 
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                        <td>₱<?php echo number_format($room['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($room['status']); ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="editRoom(<?php echo $room['id']; ?>)">Edit</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteRoom(<?php echo $room['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No rooms found or error occurred while fetching rooms.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Room</h4>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Room Type</label>
                        <select name="room_type" class="form-control" required>
                            <option value="Standard Double Room">Standard Double Room</option>
                            <option value="Deluxe Family Room">Deluxe Family Room</option>
                            <option value="Family Room">Family Room</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price per Night</label>
                        <input type="number" name="price" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Capacity (Number of Guests)</label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_room" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle room deletion
    $('.delete-room').click(function() {
        var roomId = $(this).data('id');
        if (confirm('Are you sure you want to delete this room?')) {
            $.ajax({
                url: 'delete_room.php',
                type: 'POST',
                data: { room_id: roomId },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting room: ' + response.error);
                    }
                }
            });
        }
    });

    // Handle room editing (to be implemented)
    $('.edit-room').click(function() {
        var roomId = $(this).data('id');
        // Load room data and show edit modal
    });
});
</script>
