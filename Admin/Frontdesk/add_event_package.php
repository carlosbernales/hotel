<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/event_packages';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fetch max guest options from database
$max_guests_query = "SELECT * FROM package_max_guests ORDER BY capacity ASC";
$max_guests_result = $con->query($max_guests_query);

// Fetch duration options from database
$duration_query = "SELECT * FROM package_durations ORDER BY hours ASC";
$duration_result = $con->query($duration_query);

// Fetch menu items from database
$menu_items_query = "SELECT * FROM package_menu_items ORDER BY item_name ASC";
$menu_items_result = $con->query($menu_items_query);

// Create menu items table if it doesn't exist
$create_menu_items_table = "CREATE TABLE IF NOT EXISTS package_menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_menu_items_table);

// Create event_packages table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    menu TEXT NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    max_guests INT NOT NULL,
    duration INT NOT NULL,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

// Check if image_path column exists, add it if it doesn't
$check_column = "SHOW COLUMNS FROM event_packages LIKE 'image_path'";
$result = $con->query($check_column);
if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $add_column = "ALTER TABLE event_packages ADD COLUMN image_path VARCHAR(255) NULL";
    $con->query($add_column);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $menu = $_POST['menu'];
    $rate = (double)$_POST['rate'];
    $max_guests = (int)$_POST['max_guests'];
    $duration = (int)$_POST['duration'];
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['package_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . '/' . $new_filename;
            
            if (move_uploaded_file($_FILES['package_image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                $_SESSION['error_message'] = "Error uploading image.";
                header('Location: add_event_package.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
            header('Location: add_event_package.php');
            exit();
        }
    }

    // Basic validation
    if (empty($name) || empty($rate) || empty($max_guests) || empty($duration)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        // Get menu value - from hidden field or build from checkboxes
        if (empty($menu) && isset($_POST['menu_items']) && is_array($_POST['menu_items'])) {
            $menu = implode(', ', $_POST['menu_items']);
        }
        
        // If menu is still empty, set a default value
        if (empty($menu)) {
            $menu = "Standard Menu";
        }
        
        // Insert new package with image
        if ($image_path) {
            $query = "INSERT INTO event_packages (name, menu, rate, max_guests, duration, image_path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            
            if ($stmt === false) {
                $_SESSION['error_message'] = "Prepare error: " . $con->error;
            } else {
                $stmt->bind_param("ssdiis", $name, $menu, $rate, $max_guests, $duration, $image_path);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Package added successfully!";
                    header('Location: event_booking.php');
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error adding package: " . $stmt->error;
                }
            }
        } else {
            $query = "INSERT INTO event_packages (name, menu, rate, max_guests, duration) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            
            if ($stmt === false) {
                $_SESSION['error_message'] = "Prepare error: " . $con->error;
            } else {
                $stmt->bind_param("ssdii", $name, $menu, $rate, $max_guests, $duration);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Package added successfully!";
                    header('Location: event_booking.php');
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error adding package: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event Package - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .form-section {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #DAA520;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        
        .form-control:focus {
            border-color: #DAA520;
            box-shadow: 0 0 0 0.2rem rgba(218,165,32,0.25);
        }
        
        .btn-submit {
            background: #DAA520;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            background: #b8860b;
            color: white;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .form-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .custom-file-upload {
            border: 1px solid #DAA520;
            display: inline-block;
            padding: 8px 20px;
            cursor: pointer;
            border-radius: 5px;
            color: #DAA520;
            background: #fff;
            transition: all 0.3s ease;
        }
        
        .custom-file-upload:hover {
            background: #DAA520;
            color: #fff;
        }
        
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Style for menu items multi-select */
        select[multiple] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }
        
        select[multiple] option {
            padding: 8px;
            margin-bottom: 2px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        select[multiple] option:hover {
            background-color: #f8f9fa;
        }
        
        select[multiple] option:checked {
            background-color: #DAA520;
            color: white;
        }
        
        /* Style for menu checkboxes */
        .menu-checkboxes {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .menu-checkboxes .checkbox {
            margin-bottom: 8px;
        }
        
        .menu-checkboxes .checkbox label {
            display: flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .menu-checkboxes .checkbox label:hover {
            background-color: #f0f0f0;
        }
        
        .menu-checkboxes .checkbox input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .menu-checkboxes .checkbox input[type="checkbox"]:checked + span {
            font-weight: 600;
            color: #DAA520;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li><a href="event_management.php">Event Management</a></li>
                <li class="active">Add Package</li>
            </ol>
        </div>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="form-section">
                    <div class="section-header">
                        <h2>Add New Event Package</h2>
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Package Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Package A*" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_guests">Maximum Guests</label>
                                    <select class="form-control" id="max_guests" name="max_guests" required>
                                        <option value="">Select PAX</option>
                                        <?php if ($max_guests_result && $max_guests_result->num_rows > 0): ?>
                                            <?php while ($option = $max_guests_result->fetch_assoc()): ?>
                                                <option value="<?php echo $option['capacity']; ?>"><?php echo $option['capacity']; ?> PAX</option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="30">30 PAX</option>
                                            <option value="50">50 PAX</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Package Image</label>
                            <div>
                                <label class="custom-file-upload">
                                    <input type="file" name="package_image" id="package_image" accept="image/*" style="display: none;">
                                    <i class="fa fa-cloud-upload"></i> Upload Image
                                </label>
                                <small class="text-muted ml-2">Recommended size: 800x600 pixels</small>
                            </div>
                            <img id="image_preview" class="image-preview">
                        </div>

                        <div class="form-group">
                            <label for="menu">Menu Details</label>
                            <div class="menu-checkboxes" style="max-height:250px; overflow-y:auto; border:1px solid #ccc; border-radius:5px; padding:15px; background:#f9f9f9;">
                                <?php if ($menu_items_result && $menu_items_result->num_rows > 0): ?>
                                    <?php while ($item = $menu_items_result->fetch_assoc()): ?>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="menu_items[]" value="<?php echo htmlspecialchars($item['item_name']); ?>">
                                                <span><?php echo htmlspecialchars($item['item_name']); ?></span>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="1 Appetizer"> <span>1 Appetizer</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="2 Appetizers"> <span>2 Appetizers</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="3 Appetizers"> <span>3 Appetizers</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="2 Pasta"> <span>2 Pasta</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="2 Mains"> <span>2 Mains</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="3 Mains"> <span>3 Mains</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="Salad Bar"> <span>Salad Bar</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="Rice"> <span>Rice</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="1 Dessert"> <span>1 Dessert</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="2 Desserts"> <span>2 Desserts</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="Drinks"> <span>Drinks</span></label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="menu_items[]" value="Wagyu Steak Station"> <span>Wagyu Steak Station</span></label></div>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="menu" name="menu" value="Standard Menu">
                            <small class="text-muted mt-2">Select at least one menu item, or a default "Standard Menu" will be used.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rate">Rate (PHP)</label>
                                    <input type="number" class="form-control" id="rate" name="rate" step="0.01" placeholder="e.g., 28000" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duration">Duration (Hours)</label>
                                    <select class="form-control" id="duration" name="duration" required>
                                        <option value="">Select Duration</option>
                                        <?php if ($duration_result && $duration_result->num_rows > 0): ?>
                                            <?php while ($option = $duration_result->fetch_assoc()): ?>
                                                <option value="<?php echo $option['hours']; ?>"><?php echo $option['hours']; ?> Hours</option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="4">4 Hours (30 PAX)</option>
                                            <option value="5">5 Hours (50 PAX)</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <a href="event_management.php" class="btn btn-cancel">Cancel</a>
                            <button type="submit" class="btn btn-submit">Add Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    // Auto-select duration based on max guests
    document.getElementById('max_guests').addEventListener('change', function() {
        var maxGuests = parseInt(this.value);
        var duration = document.getElementById('duration');
        
        // Get all available duration options
        var durationOptions = duration.options;
        
        // Default duration mapping based on max guests
        if (maxGuests <= 30) {
            // For smaller groups (30 or less), try to select 4 hours
            for (var i = 0; i < durationOptions.length; i++) {
                if (durationOptions[i].value === '4') {
                    duration.selectedIndex = i;
                    break;
                }
            }
        } else if (maxGuests <= 50) {
            // For medium groups (31-50), try to select 5 hours
            for (var i = 0; i < durationOptions.length; i++) {
                if (durationOptions[i].value === '5') {
                    duration.selectedIndex = i;
                    break;
                }
            }
        } else {
            // For larger groups (50+), try to select the highest duration
            if (durationOptions.length > 1) {
                duration.selectedIndex = durationOptions.length - 1;
            }
        }
    });

    // Image preview functionality
    document.getElementById('package_image').addEventListener('change', function(e) {
        const preview = document.getElementById('image_preview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Update menu field with selected items without preventing form submission
    document.querySelector('form').addEventListener('submit', function() {
        var checkboxes = document.querySelectorAll('input[name="menu_items[]"]:checked');
        var selectedItems = [];
        
        checkboxes.forEach(function(checkbox) {
            selectedItems.push(checkbox.value);
        });
        
        if (selectedItems.length > 0) {
            document.getElementById('menu').value = selectedItems.join(', ');
        }
        // If no items selected, the default value in the hidden field will be used
    });
    </script>
</body>
</html> 