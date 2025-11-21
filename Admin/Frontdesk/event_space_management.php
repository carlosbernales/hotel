<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch event spaces
$query = "SELECT * FROM event_packages ORDER BY id";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Space Management - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
</head>

<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Event Space Management</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Event Spaces
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addSpaceModal">
                            Add New Space
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="spacesTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>price</th>
                                        <th>Capacity</th>
                                        <th>Description</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                
                                                
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                                <td><?php echo htmlspecialchars($row['max_guests']); ?></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                               
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-space" data-id="<?php echo $row['id']; ?>">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-space" data-id="<?php echo $row['id']; ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Space Modal -->
    <div class="modal fade" id="addSpaceModal" tabindex="-1" role="dialog" aria-labelledby="addSpaceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSpaceModalLabel">Add New Event Space</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addSpaceForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_space">
                        
                        <div class="form-group">
                            <label for="space_name">Space Name</label>
                            <input type="text" class="form-control" id="space_name" name="space_name" required>
                        </div>

                        <div class="form-group">
                            <label for="display_category">Display Category</label>
                            <select class="form-control" id="display_category" name="display_category" required>
                                <option value="venue_rental">Venue Rental Only</option>
                                <option value="event_package">Event Package</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Type">Space Type</label>
                                    <select class="form-control" id="Type" name="Type" required>
                                        <option value="">Select Type...</option>
                                        <option value="conference">Conference Room</option>
                                        <option value="banquet">Banquet Hall</option>
                                        <option value="wedding">Wedding Venue</option>
                                        <option value="meeting">Meeting Room</option>
                                        <option value="party">Party Hall</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category...</option>
                                        <option value="Event Gallery">Event Gallery</option>
                                        <option value="Venue Rental">Venue Rental</option>
                                        <option value="Package Venue">Package Venue</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="capacity">Capacity (persons)</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_per_hour">Price per Hour</label>
                                    <input type="number" step="0.01" class="form-control" id="price_per_hour" name="price_per_hour" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Amenities</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="wifi" name="amenities[]" value="WiFi">
                                        <label class="custom-control-label" for="wifi">WiFi</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="projector" name="amenities[]" value="Projector">
                                        <label class="custom-control-label" for="projector">Projector</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sound" name="amenities[]" value="Sound System">
                                        <label class="custom-control-label" for="sound">Sound System</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="catering" name="amenities[]" value="Catering">
                                        <label class="custom-control-label" for="catering">Catering</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="parking" name="amenities[]" value="Parking">
                                        <label class="custom-control-label" for="parking">Parking</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="stage" name="amenities[]" value="Stage">
                                        <label class="custom-control-label" for="stage">Stage</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">Main Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                        </div>

                        <div class="form-group">
                            <label for="gallery_images">Gallery Images (Multiple)</label>
                            <input type="file" class="form-control-file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="saveSpace" class="btn btn-primary">Save Event Space</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Handle form submission
        $('#saveSpace').click(function(e) {
            e.preventDefault();
            
            // Create FormData object
            var formData = new FormData($('#addSpaceForm')[0]);
            
            // Get selected amenities
            var selectedAmenities = [];
            $('input[name="amenities[]"]:checked').each(function() {
                selectedAmenities.push($(this).val());
            });
            formData.set('amenities', JSON.stringify(selectedAmenities));
            
            $.ajax({
                url: 'process_event_space.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                        if (response.success) {
                            alert(response.message);
                            $('#addSpaceModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                            console.error('Server error:', response.error);
                        }
                    } catch (e) {
                        alert('Error processing response');
                        console.error('Parse error:', e);
                        console.error('Raw response:', response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                    console.error('AJAX error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        });

        // Clear form when modal is closed
        $('#addSpaceModal').on('hidden.bs.modal', function() {
            $('#addSpaceForm')[0].reset();
        });
    });
    </script>
</body>
</html>
