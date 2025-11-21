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
    
    $duration = (int)$_POST['duration'];
    
    // Function to handle image uploads
    function uploadImage($file, $upload_dir) {
        if (isset($file) && $file['error'] == 0) {
            $allowed = array('jpg', 'jpeg', 'png', 'gif');
            $filename = $file['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = $upload_dir . '/' . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    return $upload_path;
                }
            }
        }
        return null;
    }

    // Handle image uploads
    $image_path = uploadImage($_FILES['package_image'] ?? null, $upload_dir);
    $image_path2 = uploadImage($_FILES['package_image2'] ?? null, $upload_dir);
    $image_path3 = uploadImage($_FILES['package_image3'] ?? null, $upload_dir);
    
    // Check if at least one image was uploaded
    if (!$image_path && !$image_path2 && !$image_path3) {
        $_SESSION['error_message'] = "At least one image is required.";
        header('Location: add_event_package.php');
        exit();
    }

    // Basic validation
    if (empty($name)) {
        $_SESSION['error_message'] = "Please enter a package name.";
    } else if (!is_numeric($_POST['rate']) || $_POST['rate'] <= 0) {
        $_SESSION['error_message'] = "Please enter a valid rate.";
    } else if (empty($_POST['duration']) || !is_numeric($_POST['duration']) || $_POST['duration'] <= 0) {
        $_SESSION['error_message'] = "Please select a valid duration.";
    } else if (!$image_path) {
        $_SESSION['error_message'] = "Please upload a main package image.";
    } else {
        // Get form values
        $rate = (float)$_POST['rate'];
        $duration = (int)$_POST['duration'];
        
        // Set max_guests based on duration
        $max_guests = ($duration == 5) ? 50 : 30; // 5 hours = 50 guests, 4 hours = 30 guests
        
        $description = !empty($_POST['description']) ? trim($_POST['description']) : '';
        
        // Set default menu if none selected
        if (empty($menu)) {
            $menu = "Standard Menu";
        }
        
        // Insert new package with all fields
        $status = 'Available'; // Default status
        $query = "INSERT INTO event_packages (name, menu_items, price, max_guests, duration, description, image_path, image_path2, image_path3, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        
        if ($stmt === false) {
            $_SESSION['error_message'] = "Database error: " . $con->error;
        } else {
            $stmt->bind_param("ssdiisssss", $name, $menu, $rate, $max_guests, $duration, $description, $image_path, $image_path2, $image_path3, $status);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Package added successfully!";
                header('Location: event_management.php');
                exit();
            } else {
                $_SESSION['error_message'] = "Error adding package: " . $stmt->error;
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
                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Package A" required>
                                </div>
                                
                                

                           
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter package description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Package Images</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="image-upload-container">
                                        <label>Main Image *</label>
                                        <div class="custom-file-upload">
                                            <input type="file" name="package_image" id="package_image" accept="image/*" required>
                                            <i class="fa fa-cloud-upload"></i> Upload Main Image
                                        </div>
                                        <small class="text-muted">Recommended: 800x600px</small>
                                        <img id="image_preview_1" class="image-preview mt-2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="image-upload-container">
                                        <label>Additional Image 1</label>
                                        <div class="custom-file-upload">
                                            <input type="file" name="package_image2" id="package_image2" accept="image/*">
                                            <i class="fa fa-cloud-upload"></i> Upload Image 2
                                        </div>
                                        <small class="text-muted">Optional</small>
                                        <img id="image_preview_2" class="image-preview mt-2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="image-upload-container">
                                        <label>Additional Image 2</label>
                                        <div class="custom-file-upload">
                                            <input type="file" name="package_image3" id="package_image3" accept="image/*">
                                            <i class="fa fa-cloud-upload"></i> Upload Image 3
                                        </div>
                                        <small class="text-muted">Optional</small>
                                        <img id="image_preview_3" class="image-preview mt-2">
                                    </div>
                                </div>
                            </div>
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

    // Function to handle image preview
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('change', function(e) {
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
        }
    }
    
    // Set up previews for all three image uploads
    setupImagePreview('package_image', 'image_preview_1');
    setupImagePreview('package_image2', 'image_preview_2');
    setupImagePreview('package_image3', 'image_preview_3');

    // Menu selection handling
    const menuCheckboxes = document.querySelectorAll('input[name="menu_items[]"]');
    const menuInput = document.getElementById('menu');
    
    function updateMenuInput() {
        const selected = [];
        menuCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selected.push(checkbox.value);
            }
        });
        menuInput.value = selected.join(', ');
    }
    
    menuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateMenuInput);
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const packageImage = document.getElementById('package_image').files[0];
        
        if (!name || !packageImage) {
            e.preventDefault();
            alert('Please fill in all required fields and upload a main package image.');
            return false;
        }
    });
</script>
</body>
</html>