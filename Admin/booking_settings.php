<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Create necessary tables if they don't exist
$create_tables = "
CREATE TABLE IF NOT EXISTS featured_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT,
    start_date DATE,
    end_date DATE,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
);

CREATE TABLE IF NOT EXISTS seasonal_discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    discount_percentage DECIMAL(5,2),
    start_date DATE,
    end_date DATE,
    room_type_id INT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
);";

// Execute table creation with error handling
if (!$con->multi_query($create_tables)) {
    die("Error creating tables: " . $con->error);
}

// Clear out any remaining results
while ($con->more_results()) {
    $con->next_result();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle add featured room
    if (isset($_POST['add_featured_room'])) {
        try {
            $room_type_id = $_POST['room_type_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            
            // Validate room type exists
            $check_room = $con->prepare("SELECT room_type_id FROM room_types WHERE room_type_id = ?");
            $check_room->bind_param("i", $room_type_id);
            $check_room->execute();
            $result = $check_room->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Selected room type does not exist");
            }
            
            // Handle image upload
            $image_path = '';
            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
                $target_dir = "uploads/featured_rooms/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["room_image"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid('featured_') . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                $check = getimagesize($_FILES["room_image"]["tmp_name"]);
                if ($check === false) {
                    throw new Exception("File is not an image.");
                }
                
                if (!move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                    throw new Exception("Sorry, there was an error uploading your file.");
                }
                
                $image_path = $target_file;
            } else {
                throw new Exception("Please upload an image for the featured room.");
            }
            
            // Insert into database
            $stmt = $con->prepare("INSERT INTO featured_rooms (room_type_id, start_date, end_date, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $room_type_id, $start_date, $end_date, $image_path);
            
            if (!$stmt->execute()) {
                throw new Exception("Error saving featured room: " . $stmt->error);
            }
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Featured room has been added successfully'
            ];
            
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Handle remove featured room
    if (isset($_POST['remove_featured'])) {
        $id = $_POST['featured_id'];
        $stmt = $con->prepare("DELETE FROM featured_rooms WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
        
    // Handle remove discount
    if (isset($_POST['remove_discount'])) {
        $id = $_POST['discount_id'];
        
        // First, check if the discount exists
        $check_stmt = $con->prepare("SELECT id FROM seasonal_discounts WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
            // Delete the discount
            $stmt = $con->prepare("DELETE FROM seasonal_discounts WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete discount: " . mysqli_error($con) . "'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Discount has been deleted successfully',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>";
            }
            if (isset($stmt) && $stmt) {
                $stmt->close();
            }
        }
        if (isset($check_stmt) && $check_stmt) {
            $check_stmt->close();
        }
    }

    // Handle add seasonal discount
    if (isset($_POST['add_seasonal_discount'])) {
        try {
            $name = $_POST['discount_name'];
            $percentage = $_POST['discount_percentage'];
            $start_date = $_POST['discount_start_date'];
            $end_date = $_POST['discount_end_date'];
            $room_type_id = !empty($_POST['discount_room_type_id']) ? $_POST['discount_room_type_id'] : NULL;
            $description = $_POST['discount_description'];
            
            // Validate dates
            if (strtotime($end_date) < strtotime($start_date)) {
                throw new Exception("End date cannot be earlier than start date");
            }
            
            // Validate percentage
            if ($percentage <= 0 || $percentage > 100) {
                throw new Exception("Discount percentage must be between 0 and 100");
            }
            
            $stmt = $con->prepare("INSERT INTO seasonal_discounts (name, discount_percentage, start_date, end_date, room_type_id, description, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sdssss", $name, $percentage, $start_date, $end_date, $room_type_id, $description);
            
            if (!$stmt->execute()) {
                throw new Exception("Error saving discount: " . $stmt->error);
            }
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Discount has been added successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = window.location.href;
                });
            </script>";
            exit();
            
        } catch (Exception $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '" . addslashes($e->getMessage()) . "'
                });
            </script>";
        }
    }
    
    // Handle bulk delete discounts
    if (isset($_POST['bulk_delete_discounts'])) {
        if (isset($_POST['selected_discounts']) && is_array($_POST['selected_discounts'])) {
            $success = true;
            $deleted = 0;
            
            foreach ($_POST['selected_discounts'] as $discount_id) {
                $stmt = $con->prepare("DELETE FROM seasonal_discounts WHERE id = ?");
                $stmt->bind_param("i", $discount_id);
                
                if (!$stmt->execute()) {
                    $success = false;
                    break;
                }
                $deleted++;
                $stmt->close();
            }
            
            if ($success) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Successfully deleted " . $deleted . " discount(s)',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete some discounts. Please try again.'
                    });
                </script>";
            }
        }
    }

    // Handle add best offer
    if (isset($_POST['add_best_offer'])) {
        try {
            $title = $_POST['offer_title'];
            $description = $_POST['offer_description'];
            $discount = $_POST['discount'] . '% OFF';
            
            // Handle image upload
            $image = '';
            if (isset($_FILES['offer_image']) && $_FILES['offer_image']['error'] == 0) {
                $target_dir = "uploads/offers/";
                
                // Create directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["offer_image"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid('offer_') . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["offer_image"]["tmp_name"]);
                if ($check !== false) {
                    // Check file size (max 5MB)
                    if ($_FILES["offer_image"]["size"] > 5000000) {
                        throw new Exception("Sorry, your file is too large. Maximum size is 5MB.");
                    }
                    
                    // Allow certain file formats
                    if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
                        throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                    }
                    
                    if (move_uploaded_file($_FILES["offer_image"]["tmp_name"], $target_file)) {
                        $image = $target_file;
                    } else {
                        throw new Exception("Sorry, there was an error uploading your file.");
                    }
                } else {
                    throw new Exception("File is not an image.");
                }
            }
            
            $stmt = $con->prepare("INSERT INTO offers (title, description, image, discount, active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
            $stmt->bind_param("ssss", $title, $description, $image, $discount);
            
            if (!$stmt->execute()) {
                throw new Exception("Error saving offer: " . $stmt->error);
            }
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Best offer has been added successfully'
            ];
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    if (isset($_POST['remove_offer'])) {
        $offer_id = $_POST['offer_id'];
        
        // First, get the image path to delete the file
        $stmt = $con->prepare("SELECT image FROM offers WHERE id = ?");
        $stmt->bind_param("i", $offer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $offer = $result->fetch_assoc();
        
        if ($offer && !empty($offer['image']) && file_exists($offer['image'])) {
            unlink($offer['image']); // Delete the image file
        }
        
        // Delete the offer record
        $stmt = $con->prepare("DELETE FROM offers WHERE id = ?");
        $stmt->bind_param("i", $offer_id);
        
        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Offer has been deleted successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = window.location.href;
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete offer'
                });
            </script>";
        }
        exit();
    }
    
    if (isset($_POST['bulk_delete_offers'])) {
        if (isset($_POST['selected_offers']) && is_array($_POST['selected_offers'])) {
            $success = true;
            $deleted = 0;
            
            foreach ($_POST['selected_offers'] as $offer_id) {
                // Get image path before deleting
                $stmt = $con->prepare("SELECT image FROM offers WHERE id = ?");
                $stmt->bind_param("i", $offer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $offer = $result->fetch_assoc();
                
                // Delete image file if it exists
                if ($offer && !empty($offer['image']) && file_exists($offer['image'])) {
                    unlink($offer['image']);
                }
                
                // Delete offer record
                $stmt = $con->prepare("DELETE FROM offers WHERE id = ?");
                $stmt->bind_param("i", $offer_id);
                
                if ($stmt->execute()) {
                    $deleted++;
                } else {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Successfully deleted " . $deleted . " offer(s)',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete some offers'
                    });
                </script>";
            }
            exit();
        }
    }

    if (isset($_POST['edit_offer'])) {
        try {
            $offer_id = $_POST['offer_id'];
            $title = $_POST['offer_title'];
            $description = $_POST['offer_description'];
            $discount = $_POST['discount'] . '% OFF';
            $active = isset($_POST['active']) ? 1 : 0;
            
            // Handle image upload if new image is selected
            if (isset($_FILES['offer_image']) && $_FILES['offer_image']['error'] == 0) {
                // First, get the old image to delete it
                $stmt = $con->prepare("SELECT image FROM offers WHERE id = ?");
                $stmt->bind_param("i", $offer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_offer = $result->fetch_assoc();
                
                // Delete old image if it exists
                if ($old_offer && !empty($old_offer['image']) && file_exists($old_offer['image'])) {
                    unlink($old_offer['image']);
                }
                
                // Upload new image
                $target_dir = "uploads/offers/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES["offer_image"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid('offer_') . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                $check = getimagesize($_FILES["offer_image"]["tmp_name"]);
                if ($check !== false) {
                    if ($_FILES["offer_image"]["size"] > 5000000) {
                        throw new Exception("Sorry, your file is too large. Maximum size is 5MB.");
                    }
                    
                    if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
                        throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                    }
                    
                    if (move_uploaded_file($_FILES["offer_image"]["tmp_name"], $target_file)) {
                        // Update with new image
                        $stmt = $con->prepare("UPDATE offers SET title = ?, description = ?, image = ?, discount = ?, active = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->bind_param("ssssii", $title, $description, $target_file, $discount, $active, $offer_id);
                    } else {
                        throw new Exception("Sorry, there was an error uploading your file.");
                    }
                } else {
                    throw new Exception("File is not an image.");
                }
            } else {
                // Update without changing image
                $stmt = $con->prepare("UPDATE offers SET title = ?, description = ?, discount = ?, active = ?, updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("sssii", $title, $description, $discount, $active, $offer_id);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating offer: " . $stmt->error);
            }
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Offer has been updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = window.location.href;
                });
            </script>";
            exit();
            
        } catch (Exception $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '" . addslashes($e->getMessage()) . "'
                });
            </script>";
        }
    }
    // End of POST request handling


// Include header and sidebar after all POST processing is done
include 'header.php';
include 'sidebar.php';

// Get room types for dropdowns
$room_types_query = "
    SELECT rt.room_type_id, rt.room_type, rt.price, rt.capacity, rt.image
    FROM room_types rt
    ORDER BY rt.room_type";
$room_types_result = $con->query($room_types_query);
if (!$room_types_result) {
    die("Error in room types query: " . $con->error);
}

$room_types = [];
while($row = $room_types_result->fetch_assoc()) {
    $room_types[] = $row;
}

// Get current featured rooms with room type details
$featured_rooms_query = "
    SELECT fr.id, fr.room_type_id, fr.start_date, fr.end_date, fr.image_path,
           rt.room_type, rt.price, rt.capacity, rt.image as room_image,
           rt.description, rt.beds
    FROM featured_rooms fr 
    INNER JOIN room_types rt ON fr.room_type_id = rt.room_type_id 
    ORDER BY fr.created_at DESC";

$featured_rooms = $con->query($featured_rooms_query);
if (!$featured_rooms) {
    die("Error in featured rooms query: " . $con->error);
}

// Get current seasonal discounts with proper ordering
$discounts_query = "
    SELECT sd.*, rt.room_type 
    FROM seasonal_discounts sd 
    LEFT JOIN room_types rt ON sd.room_type_id = rt.room_type_id 
    WHERE sd.is_active = 1 
    ORDER BY sd.start_date DESC";
$discounts = mysqli_query($con, $discounts_query);

// Add this right before the closing PHP tag, but after all other PHP code
?>

<!-- Add this right after the SweetAlert2 script include -->
<script>
<?php
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Clear the message after retrieving it
    echo "
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '" . $message['type'] . "',
            title: '" . ucfirst($message['type']) . "',
            text: '" . addslashes($message['message']) . "',
            timer: 1500,
            showConfirmButton: false
        });
    });";
}
?>
</script>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;"></a></li>
            <li class="active">Booking Settings</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Featured Rooms Management</div>
                <div class="panel-body">
                    <div class="mb-4">
                        <h3>Featured Rooms Management</h3>
                        <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="room_type_id" class="form-label">Select Room Type</label>
                                <select name="room_type_id" id="room_type_id" class="form-select" required>
                                    <?php foreach ($room_types as $room): ?>
                                        <option value="<?php echo $room['room_type_id']; ?>">
                                            <?php echo $room['room_type'] . ' - ₱' . number_format($room['price'], 2); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="room_image" class="form-label">Room Image</label>
                                <input type="file" class="form-control" id="room_image" name="room_image">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_featured_room" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Room Image</th>
                                    <th>Room Type</th>
                                    <th>Price</th>
                                    <th>Capacity</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $featured_rooms_query = "
                                    SELECT fr.*, rt.room_type, rt.price, rt.capacity, rt.description
                                    FROM featured_rooms fr
                                    INNER JOIN room_types rt ON fr.room_type_id = rt.room_type_id
                                    ORDER BY fr.created_at DESC";
                                $featured_rooms_result = $con->query($featured_rooms_query);
                                
                                while ($room = $featured_rooms_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($room['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($room['image_path']); ?>" alt="Room Image" style="max-width: 100px;">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                    <td>₱<?php echo number_format($room['price'], 2); ?></td>
                                    <td><?php echo $room['capacity']; ?> guests</td>
                                    <td><?php echo htmlspecialchars($room['description']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($room['start_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($room['end_date'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="featured_id" value="<?php echo $room['id']; ?>">
                                            <button type="submit" name="remove_featured" class="btn btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Seasonal Discounts Management</div>
                <div class="panel-body">
                    <form method="POST" class="mb-4">
                        <input type="hidden" name="add_seasonal_discount" value="1">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Discount Name</label>
                                    <input type="text" name="discount_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Discount %</label>
                                    <input type="number" name="discount_percentage" class="form-control" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="discount_start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="discount_end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Apply to Room Type (Optional)</label>
                                    <select name="discount_room_type_id" class="form-control">
                                        <option value="">All Rooms</option>
                                        <?php foreach ($room_types as $room): ?>
                                            <option value="<?php echo $room['room_type_id']; ?>">
                                                <?php echo $room['room_type']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="discount_description" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form method="POST" id="discountsForm">
                        <div class="mb-3">
                            <button type="button" class="btn btn-danger" onclick="deleteSelected()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleAllCheckboxes()">
                                <i class="fas fa-check-square"></i> Toggle All
                            </button>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th>Name</th>
                                    <th>Discount</th>
                                    <th>Room Type</th>
                                    <th>Period</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($discount = mysqli_fetch_assoc($discounts)): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_discounts[]" 
                                                   value="<?php echo $discount['id']; ?>" 
                                                   class="discount-checkbox">
                                        </td>
                                        <td><?php echo htmlspecialchars($discount['name']); ?></td>
                                        <td><?php echo htmlspecialchars($discount['discount_percentage']); ?>%</td>
                                        <td><?php echo $discount['room_type'] ?? 'All Rooms'; ?></td>
                                        <td>
                                            <?php echo date('M j, Y', strtotime($discount['start_date'])); ?> -
                                            <?php echo date('M j, Y', strtotime($discount['end_date'])); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($discount['description']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete(<?php echo $discount['id']; ?>)">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="bulk_delete_discounts" value="1">
                    </form>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Best Offers Management</div>
                <div class="panel-body">
                    <form method="POST" class="mb-4" enctype="multipart/form-data">
                        <input type="hidden" name="add_best_offer" value="1">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="offer_title" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Discount %</label>
                                    <input type="number" name="discount" class="form-control" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Offer Image</label>
                                    <input type="file" name="offer_image" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="offer_description" class="form-control" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form method="POST" id="offersForm">
                        <div class="mb-3">
                            <button type="button" class="btn btn-danger" onclick="deleteSelectedOffers()">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleAllOfferCheckboxes()">
                                <i class="fas fa-check-square"></i> Toggle All
                            </button>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <input type="checkbox" id="selectAllOffers" onchange="toggleAllOfferCheckboxes()">
                                    </th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Discount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $offers_query = "SELECT * FROM offers ORDER BY created_at DESC";
                                $offers = mysqli_query($con, $offers_query);
                                while ($offer = mysqli_fetch_assoc($offers)): 
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_offers[]" 
                                                   value="<?php echo $offer['id']; ?>" 
                                                   class="offer-checkbox">
                                        </td>
                                        <td>
                                            <?php if (!empty($offer['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($offer['image']); ?>" 
                                                     alt="Offer Image" style="max-width: 100px; max-height: 100px;">
                                            <?php else: ?>
                                                No image
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($offer['title']); ?></td>
                                        <td><?php echo htmlspecialchars($offer['description']); ?></td>
                                        <td><?php echo htmlspecialchars($offer['discount']); ?></td>
                                        <td><?php echo $offer['active'] ? 'Active' : 'Inactive'; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDeleteOffer(<?php echo $offer['id']; ?>)">
                                                Remove
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($offer)); ?>)">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="bulk_delete_offers" value="1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Offer Modal -->
<div class="modal fade" id="editOfferModal" tabindex="-1" role="dialog" aria-labelledby="editOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editOfferForm">
                <div class="modal-body">
                    <input type="hidden" name="edit_offer" value="1">
                    <input type="hidden" name="offer_id" id="edit_offer_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="offer_title" id="edit_offer_title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Discount %</label>
                                <input type="number" name="discount" id="edit_offer_discount" class="form-control" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="offer_description" id="edit_offer_description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>New Image (leave empty to keep current image)</label>
                                <input type="file" name="offer_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Current Image</label>
                                <img id="edit_offer_current_image" src="" alt="Current Offer Image" 
                                     style="max-width: 100px; max-height: 100px; display: block;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_offer_active" name="active">
                            <label class="custom-control-label" for="edit_offer_active">Active</label>
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

<script>
function confirmDelete(discountId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit the form
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="remove_discount" value="1">
                <input type="hidden" name="discount_id" value="${discountId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.discount-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = !selectAll.checked;
    });
    selectAll.checked = !selectAll.checked;
}

function deleteSelected() {
    const selectedDiscounts = document.querySelectorAll('.discount-checkbox:checked');
    if (selectedDiscounts.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Discounts Selected',
            text: 'Please select at least one discount to delete.'
        });
        return;
    }

    Swal.fire({
        title: 'Delete Selected Discounts?',
        text: `You are about to delete ${selectedDiscounts.length} discount(s). This cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('discountsForm').submit();
        }
    });
}

function confirmDeleteOffer(offerId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="remove_offer" value="1">
                <input type="hidden" name="offer_id" value="${offerId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function toggleAllOfferCheckboxes() {
    const selectAll = document.getElementById('selectAllOffers');
    const checkboxes = document.querySelectorAll('.offer-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = !selectAll.checked;
    });
    selectAll.checked = !selectAll.checked;
}

function deleteSelectedOffers() {
    const selectedOffers = document.querySelectorAll('.offer-checkbox:checked');
    if (selectedOffers.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Offers Selected',
            text: 'Please select at least one offer to delete.'
        });
        return;
    }

    Swal.fire({
        title: 'Delete Selected Offers?',
        text: `You are about to delete ${selectedOffers.length} offer(s). This cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('offersForm').submit();
        }
    });
}

function openEditModal(offer) {
    // Remove "% OFF" from discount and convert to number
    let discountPercent = parseInt(offer.discount);
    
    // Set values in the form
    document.getElementById('edit_offer_id').value = offer.id;
    document.getElementById('edit_offer_title').value = offer.title;
    document.getElementById('edit_offer_description').value = offer.description;
    document.getElementById('edit_offer_discount').value = discountPercent;
    document.getElementById('edit_offer_active').checked = offer.active == 1;
    
    // Set current image
    const currentImageElement = document.getElementById('edit_offer_current_image');
    if (offer.image) {
        currentImageElement.src = offer.image;
        currentImageElement.style.display = 'block';
    } else {
        currentImageElement.style.display = 'none';
    }
    
    // Show the modal
    $('#editOfferModal').modal('show');
}

// Add validation before form submission
document.getElementById('editOfferForm').addEventListener('submit', function(e) {
    const discountInput = document.getElementById('edit_offer_discount');
    const discount = parseInt(discountInput.value);
    
    if (discount < 0 || discount > 100) {
        e.preventDefault();
        alert('Discount percentage must be between 0 and 100');
        return false;
    }
});
</script>

<style>
.panel {
    margin-top: 20px;
}
.mb-4 {
    margin-bottom: 1.5rem;
}
.form-group {
    margin-bottom: 1rem;
}
.discount-checkbox, #selectAll {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
.btn-danger {
    margin-right: 10px;
}
.table th, .table td {
    vertical-align: middle;
}
.modal-lg {
    max-width: 800px;
}
.custom-switch {
    padding-left: 2.25rem;
    margin-top: 1rem;
}
.btn-sm {
    margin: 2px;
}
</style>