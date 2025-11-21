<?php
require_once 'db.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_venue'])) {
        $venue_name = mysqli_real_escape_string($con, $_POST['venue_name']);
        $capacity = intval($_POST['capacity']);
        $price = floatval($_POST['price']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        
        $sql = "INSERT INTO event_venues (venue_name, capacity, price, description, status) 
                VALUES (?, ?, ?, ?, 'Available')";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sids', $venue_name, $capacity, $price, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Event venue added successfully!";
        } else {
            $_SESSION['error'] = "Error adding venue: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get existing venues
$venues_sql = "SELECT * FROM event_venues ORDER BY venue_name";
$venues_result = mysqli_query($con, $venues_sql);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active">Settings</li>
            <li class="active">Event Space Management</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Event Space Management</h2>
                    <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addVenueModal">
                        <em class="fa fa-plus"></em> Add New Venue
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
                                    <th>Venue Name</th>
                                    <th>Capacity</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($venue = mysqli_fetch_assoc($venues_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($venue['venue_name']); ?></td>
                                        <td><?php echo htmlspecialchars($venue['capacity']); ?> persons</td>
                                        <td>₱<?php echo number_format($venue['price'], 2); ?></td>
                                        <td>
                                            <span class="label <?php echo $venue['status'] === 'Available' ? 'label-success' : 'label-danger'; ?>">
                                                <?php echo htmlspecialchars($venue['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-venue" data-id="<?php echo $venue['id']; ?>">
                                                <em class="fa fa-edit"></em>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-venue" data-id="<?php echo $venue['id']; ?>">
                                                <em class="fa fa-trash"></em>
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
</div>

<!-- Add Venue Modal -->
<div class="modal fade" id="addVenueModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Event Venue</h4>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Venue Name</label>
                        <input type="text" name="venue_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_venue" class="btn btn-primary">Add Venue</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle venue deletion
    $('.delete-venue').click(function() {
        var venueId = $(this).data('id');
        if (confirm('Are you sure you want to delete this venue?')) {
            $.ajax({
                url: 'delete_venue.php',
                type: 'POST',
                data: { venue_id: venueId },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting venue: ' + response.error);
                    }
                }
            });
        }
    });

    // Handle venue editing (to be implemented)
    $('.edit-venue').click(function() {
        var venueId = $(this).data('id');
        // Load venue data and show edit modal
    });
});
</script>
