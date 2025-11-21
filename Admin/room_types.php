<?php
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify database connection
if (!isset($con)) {
    die("Database connection not available");
}

// Check if table exists
$table_exists = mysqli_query($con, "SHOW TABLES LIKE 'room_type'");
if (mysqli_num_rows($table_exists) == 0) {
    // Create table if it doesn't exist
    $create_table_query = "CREATE TABLE room_type (
        room_type_id INT AUTO_INCREMENT PRIMARY KEY,
        room_type VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT NOT NULL DEFAULT '',
        deleteStatus TINYINT(1) DEFAULT 0
    )";
    
    if (!mysqli_query($con, $create_table_query)) {
        die("Error creating table: " . mysqli_error($con));
    }
    
    // Insert default room types
    $insert_defaults = "INSERT INTO room_type (room_type, price, description) VALUES 
        ('Standard Room', 1000.00, 'A comfortable standard room'),
        ('Deluxe Room', 1500.00, 'A spacious deluxe room'),
        ('Suite', 2000.00, 'A luxury suite')";
    mysqli_query($con, $insert_defaults);
} else {
    // Check and add missing columns if needed
    $columns = mysqli_query($con, "SHOW COLUMNS FROM room_type");
    $column_names = array();
    while ($column = mysqli_fetch_assoc($columns)) {
        $column_names[] = $column['Field'];
    }
    
    if (!in_array('description', $column_names)) {
        mysqli_query($con, "ALTER TABLE room_type ADD COLUMN description TEXT NOT NULL DEFAULT ''");
    }
    
    if (!in_array('deleteStatus', $column_names)) {
        mysqli_query($con, "ALTER TABLE room_type ADD COLUMN deleteStatus TINYINT(1) DEFAULT 0");
    }
}
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                    <em class="fa fa-home"></em>
                </a></li>
            <li class="active">Room Types</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <?php
            if(isset($_GET['success'])) {
                $success = $_GET['success'];
                if($success == 1) echo '<div class="alert alert-success">Room type added successfully!</div>';
                else if($success == 2) echo '<div class="alert alert-success">Room type updated successfully!</div>';
                else if($success == 3) echo '<div class="alert alert-success">Room type deleted successfully!</div>';
            }
            if(isset($_GET['error'])) {
                $error = $_GET['error'];
                if($error == 1) echo '<div class="alert alert-danger">Failed to add room type!</div>';
                else if($error == 2) echo '<div class="alert alert-danger">Failed to update room type!</div>';
                else if($error == 3) echo '<div class="alert alert-danger">Cannot delete room type that is in use!</div>';
                else if($error == 4) echo '<div class="alert alert-danger">Failed to delete room type!</div>';
            }
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">Room Types
                    <button class="btn btn-secondary pull-right" style="border-radius:0%" data-toggle="modal" data-target="#addRoomType">Add Room Type</button>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $room_query = "SELECT * FROM room_type WHERE deleteStatus = 0 OR deleteStatus IS NULL";
                                $result = mysqli_query($con, $room_query);
                                
                                if ($result === false) {
                                    echo '<tr><td colspan="4">Error executing query: ' . mysqli_error($con) . '</td></tr>';
                                } else if (mysqli_num_rows($result) > 0) {
                                    while ($room_types = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($room_types['room_type']); ?></td>
                                            <td><?php echo number_format($room_types['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($room_types['description'] ?? ''); ?></td>
                                            <td>
                                                <button title="Edit Room Type" style="border-radius:60px;" data-toggle="modal"
                                                        data-target="#editRoomType" data-id="<?php echo $room_types['room_type_id']; ?>"
                                                        class="btn btn-primary"><i class="fa fa-edit"></i></button>
                                                <a href="ajax.php?delete_room_type=<?php echo $room_types['room_type_id']; ?>"
                                                   class="btn btn-danger" style="border-radius:60px;" 
                                                   onclick="return confirm('Are you sure you want to delete this room type?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center">No room types found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Type Modal -->
<div id="addRoomType" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Room Type</h4>
            </div>
            <div class="modal-body">
                <form id="addRoomTypeForm" action="ajax.php" method="post">
                    <div class="form-group">
                        <label>Room Type</label>
                        <input type="text" class="form-control" name="room_type" placeholder="Room Type" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" placeholder="Price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" placeholder="Description"></textarea>
                    </div>
                    <input type="hidden" name="add_room_type" value="1">
                    <button type="submit" class="btn btn-primary">Add Room Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Type Modal -->
<div id="editRoomType" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Room Type</h4>
            </div>
            <div class="modal-body">
                <form id="editRoomTypeForm" action="ajax.php" method="post">
                    <input type="hidden" id="edit_room_type_id" name="room_type_id">
                    <div class="form-group">
                        <label>Room Type</label>
                        <input type="text" class="form-control" id="edit_room_type_name" name="room_type" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="edit_room_type" value="1">
                    <button type="submit" class="btn btn-primary">Update Room Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle Edit Room Type Modal
    $('#editRoomType').on('show.bs.modal', function(e) {
        var roomTypeId = $(e.relatedTarget).data('id');
        
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
                get_room_type: true,
                room_type_id: roomTypeId
            },
            dataType: 'json',
            success: function(response) {
                if (!response.error) {
                    $('#edit_room_type_id').val(response.room_type_id);
                    $('#edit_room_type_name').val(response.room_type);
                    $('#edit_price').val(response.price);
                    $('#edit_description').val(response.description);
                } else {
                    alert('Error: ' + (response.message || 'Failed to load room type data'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading room type data');
            }
        });
    });

    // Handle form submissions
    $('#addRoomTypeForm, #editRoomTypeForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                window.location.reload();
            },
            error: function() {
                alert('Error processing form');
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
