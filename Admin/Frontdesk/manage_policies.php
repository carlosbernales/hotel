<?php
require_once 'db.php';  // Updated path to db_con.php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create hotel_policies table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS hotel_policies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    policy_type VARCHAR(50) NOT NULL,
    policy_content TEXT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_table)) {
    die("Error creating table: " . mysqli_error($con));
}

// Fetch existing policies
$policies = [];
$fetch_sql = "SELECT * FROM hotel_policies";
$result = mysqli_query($con, $fetch_sql);
while ($row = mysqli_fetch_assoc($result)) {
    $policies[$row['policy_type']] = $row['policy_content'];
}

// Fetch about content
$about_sql = "SELECT * FROM about_content ORDER BY id DESC LIMIT 1";
$about_result = mysqli_query($con, $about_sql);
$about_content = mysqli_fetch_assoc($about_result);

// Debug output
error_log("Current about content: " . print_r($about_content, true));

// Fetch about_slideshow images
$slideshow_images = [];
$slideshow_sql = "SELECT * FROM about_slideshow WHERE is_active = 1 ORDER BY display_order";
$slideshow_result = mysqli_query($con, $slideshow_sql);
while ($row = mysqli_fetch_assoc($slideshow_result)) {
    $slideshow_images[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type'])) {
        switch ($_POST['form_type']) {
            case 'about_content':
                $title = mysqli_real_escape_string($con, $_POST['title']);
                $description = mysqli_real_escape_string($con, $_POST['description']);
                
                // Debug output
                error_log("Updating about content - Title: " . $title);
                error_log("Description: " . $description);
                
                // Check if record exists
                $check_sql = "SELECT id FROM about_content LIMIT 1";
                $check_result = mysqli_query($con, $check_sql);
                
                if (!$check_result) {
                    error_log("Check query failed: " . mysqli_error($con));
                    $_SESSION['error_message'] = "Database error: " . mysqli_error($con);
                } else {
                    if (mysqli_num_rows($check_result) > 0) {
                        $row = mysqli_fetch_assoc($check_result);
                        $update_sql = "UPDATE about_content SET 
                                     title = '$title', 
                                     description = '$description',
                                     last_updated = CURRENT_TIMESTAMP 
                                     WHERE id = {$row['id']}";
                        
                        if (mysqli_query($con, $update_sql)) {
                            $_SESSION['success_message'] = "About content updated successfully!";
                            error_log("Update successful");
                        } else {
                            $_SESSION['error_message'] = "Error updating content: " . mysqli_error($con);
                            error_log("Update failed: " . mysqli_error($con));
                        }
                    } else {
                        $insert_sql = "INSERT INTO about_content (title, description) VALUES ('$title', '$description')";
                        if (mysqli_query($con, $insert_sql)) {
                            $_SESSION['success_message'] = "About content added successfully!";
                            error_log("Insert successful");
                        } else {
                            $_SESSION['error_message'] = "Error adding content: " . mysqli_error($con);
                            error_log("Insert failed: " . mysqli_error($con));
                        }
                    }
                }
                break;

            case 'slideshow_upload':
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($filetype, $allowed)) {
                        $alt_text = mysqli_real_escape_string($con, $_POST['alt_text']);
                        $display_order = (int)$_POST['display_order'];
                        
                        // Create images directory if it doesn't exist
                        $upload_dir = '../images/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        // Generate unique filename
                        $new_filename = $upload_dir . uniqid() . '.' . $filetype;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $new_filename)) {
                            $db_filename = 'images/' . basename($new_filename);
                            $insert_sql = "INSERT INTO about_slideshow (image_path, alt_text, display_order, is_active) 
                                         VALUES ('$db_filename', '$alt_text', $display_order, 1)";
                            
                            if (mysqli_query($con, $insert_sql)) {
                                $_SESSION['success_message'] = "Image uploaded successfully!";
                            } else {
                                $_SESSION['error_message'] = "Error saving to database: " . mysqli_error($con);
                                if (file_exists($new_filename)) {
                                    unlink($new_filename);
                                }
                            }
                        } else {
                            $_SESSION['error_message'] = "Error uploading file.";
                        }
                    } else {
                        $_SESSION['error_message'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
                    }
                }
                break;

            case 'slideshow_delete':
                $image_id = (int)$_POST['image_id'];
                
                // Get image path before deletion
                $get_image_sql = "SELECT image_path FROM about_slideshow WHERE id = $image_id";
                $image_result = mysqli_query($con, $get_image_sql);
                
                if ($image_result && $image_data = mysqli_fetch_assoc($image_result)) {
                    $image_path = '../' . $image_data['image_path'];
                    
                    // Delete from database
                    $delete_sql = "DELETE FROM about_slideshow WHERE id = $image_id";
                    if (mysqli_query($con, $delete_sql)) {
                        // Delete physical file
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                        
                        // Reorder remaining images
                        mysqli_query($con, "SET @rank := 0");
                        mysqli_query($con, "UPDATE about_slideshow SET display_order = (@rank := @rank + 1) ORDER BY display_order");
                        
                        $_SESSION['success_message'] = "Image deleted successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error deleting image: " . mysqli_error($con);
                    }
                }
                break;
        }
    }
    
    header("Location: manage_policies.php");
    exit();
}

// Handle form submission for about content
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'about_content') {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    $update_sql = "UPDATE about_content SET title = '$title', description = '$description' WHERE id = " . $about_content['id'];
        if (mysqli_query($con, $update_sql)) {
        $_SESSION['success_message'] = "About content updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating about content: " . mysqli_error($con);
    }
    
    header("Location: manage_policies.php");
    exit();
}

// Handle slideshow image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'slideshow_upload') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $alt_text = mysqli_real_escape_string($con, $_POST['alt_text']);
            $display_order = (int)$_POST['display_order'];
            
            // Create images directory if it doesn't exist
            if (!file_exists('images')) {
                mkdir('images', 0777, true);
            }
            
            // Generate unique filename
            $new_filename = 'images/' . uniqid() . '.' . $filetype;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $new_filename)) {
                $insert_sql = "INSERT INTO about_slideshow (image_path, alt_text, display_order, is_active) 
                             VALUES ('$new_filename', '$alt_text', $display_order, 1)";
                             
                if (mysqli_query($con, $insert_sql)) {
                    $_SESSION['success_message'] = "Image uploaded successfully!";
                } else {
                    $_SESSION['error_message'] = "Error saving image to database: " . mysqli_error($con);
                }
            } else {
                $_SESSION['error_message'] = "Error uploading image file.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
        }
    }
    header("Location: manage_policies.php");
    exit();
}

// Handle slideshow image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'slideshow_delete') {
    $image_id = (int)$_POST['image_id'];
    
    // First get the image path before deleting the record
    $get_image_sql = "SELECT image_path FROM about_slideshow WHERE id = $image_id";
    $image_result = mysqli_query($con, $get_image_sql);
    $image_data = mysqli_fetch_assoc($image_result);
    
    if ($image_data) {
        $image_path = $image_data['image_path'];
        
        // Delete from database
        $delete_sql = "DELETE FROM about_slideshow WHERE id = $image_id";
        if (mysqli_query($con, $delete_sql)) {
            // Delete the physical file if it exists
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            
            // Reorder remaining images
            $reorder_sql = "SET @rank := 0;
                          UPDATE about_slideshow 
                          SET display_order = (@rank := @rank + 1) 
                          ORDER BY display_order;";
            mysqli_multi_query($con, $reorder_sql);
            
            $_SESSION['success_message'] = "Image deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting image: " . mysqli_error($con);
        }
    } else {
        $_SESSION['error_message'] = "Image not found in database.";
    }
    
    header("Location: manage_policies.php");
    exit();
}

// Handle slideshow image order update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'slideshow_order') {
    $orders = $_POST['display_orders'];
    foreach ($orders as $id => $order) {
        $id = (int)$id;
        $order = (int)$order;
        $update_sql = "UPDATE about_slideshow SET display_order = $order WHERE id = $id";
        mysqli_query($con, $update_sql);
    }
    $_SESSION['success_message'] = "Display order updated successfully!";
    header("Location: manage_policies.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Hotel Policies - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .policy-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .policy-header {
            border-bottom: 2px solid #DAA520;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-save {
            background-color: #DAA520;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
        }
        .btn-save:hover {
            background-color: #B8860B;
            color: white;
        }
        .policy-type {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .last-updated {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .formatting-toolbar {
            margin-bottom: 10px;
            padding: 5px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .formatting-toolbar button {
            background: none;
            border: 1px solid #ccc;
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 3px;
            cursor: pointer;
        }
        .formatting-toolbar button:hover {
            background: #e9ecef;
        }
        .policy-textarea {
            width: 100%;
            min-height: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
        }
        .preview-section {
            margin-top: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .preview-section h4 {
            margin-bottom: 10px;
            color: #666;
        }
        .about-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .about-header {
            border-bottom: 2px solid #DAA520;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        .about-preview {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .slideshow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .slideshow-item {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .slideshow-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .slideshow-item .controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .slideshow-item .controls input {
            width: 50px;
            padding: 2px;
            text-align: center;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .upload-section {
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
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
                <li>Settings</li>
                <li class="active">Hotel Policies</li>
            </ol>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Manage Hotel Policies</h2>
                    </div>
                    <div class="panel-body">
                        <!-- About Casa Estela Information Management -->
                        <div class="about-section">
                            <div class="about-header">
                                <h3>About Casa Estela Content Management</h3>
                                <p class="text-muted">Edit the content that appears on the About page</p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="form_type" value="about_content">
                                <div class="form-group">
                                    <label for="about_title">Title:</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="about_title" 
                                           name="title" 
                                           value="<?php echo htmlspecialchars($about_content['title'] ?? ''); ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="about_description">Description:</label>
                                    <div class="formatting-toolbar">
                                        <button type="button" onclick="formatText('bold')"><i class="fa fa-bold"></i></button>
                                        <button type="button" onclick="formatText('italic')"><i class="fa fa-italic"></i></button>
                                        <button type="button" onclick="formatText('bullet')"><i class="fa fa-list-ul"></i></button>
                                        <button type="button" onclick="formatText('number')"><i class="fa fa-list-ol"></i></button>
                                    </div>
                                    <textarea name="description" 
                                              id="about_description" 
                                              class="policy-textarea" 
                                              rows="8" 
                                              onkeyup="updatePreview(this)" 
                                              required><?php echo htmlspecialchars($about_content['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="preview-section">
                                    <h4>Preview:</h4>
                                    <div class="preview-content"></div>
                                </div>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </form>
                        </div>

                        <!-- Slideshow Management -->
                        <div class="about-section">
                            <div class="about-header">
                                <h3>Slideshow Images Management</h3>
                                <p class="text-muted">Manage the images that appear in the slideshow on the About page</p>
                            </div>
                            
                            <!-- Upload new image -->
                            <div class="upload-section">
                                <h4>Upload New Image</h4>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="form_type" value="slideshow_upload">
                                    <div class="form-group">
                                        <label for="image">Select Image:</label>
                                        <input type="file" class="form-control" id="image" name="image" required accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label for="alt_text">Image Description:</label>
                                        <input type="text" class="form-control" id="alt_text" name="alt_text" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="display_order">Display Order:</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" 
                                               min="1" value="<?php echo count($slideshow_images) + 1; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-save">Upload Image</button>
                                </form>
                            </div>
                            
                            <!-- Existing images -->
                            <h4>Current Slideshow Images</h4>
                            <div class="slideshow-grid">
                                <?php 
                                foreach ($slideshow_images as $image): 
                                ?>
                                <div class="slideshow-item">
                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                                    <div class="controls">
                                        <input type="number" 
                                               name="display_orders[<?php echo $image['id']; ?>]" 
                                               value="<?php echo $image['display_order']; ?>" 
                                               min="1" 
                                               class="order-input"
                                               onchange="updateOrder(<?php echo $image['id']; ?>, this.value)">
                                        <button type="button" 
                                                class="delete-btn" 
                                                onclick="deleteImage(<?php echo $image['id']; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Hidden form for delete operations -->
                            <form id="deleteForm" method="POST">
                                <input type="hidden" name="form_type" value="slideshow_delete">
                                <input type="hidden" name="image_id" id="delete_image_id">
                            </form>
                        </div>

                        <!-- Check-in/Check-out Policy -->
                        <div class="policy-section">
                            <div class="policy-header">
                                <h3>Check-in/Check-out Policy</h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="policy_type" value="checkin_checkout">
                                <div class="formatting-toolbar">
                                    <button type="button" onclick="formatText('bold')"><i class="fa fa-bold"></i></button>
                                    <button type="button" onclick="formatText('italic')"><i class="fa fa-italic"></i></button>
                                    <button type="button" onclick="formatText('bullet')"><i class="fa fa-list-ul"></i></button>
                                    <button type="button" onclick="formatText('number')"><i class="fa fa-list-ol"></i></button>
                                </div>
                                <div class="form-group">
                                    <textarea name="policy_content" class="policy-textarea" rows="8" 
                                              onkeyup="updatePreview(this)"><?php echo htmlspecialchars($policies['checkin_checkout'] ?? ''); ?></textarea>
                                </div>
                                <div class="preview-section">
                                    <h4>Preview:</h4>
                                    <div class="preview-content"></div>
                                </div>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </form>
                        </div>

                        <!-- Cancellation Policy -->
                        <div class="policy-section">
                            <div class="policy-header">
                                <h3>Cancellation Policy</h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="policy_type" value="cancellation">
                                <div class="formatting-toolbar">
                                    <button type="button" onclick="formatText('bold')"><i class="fa fa-bold"></i></button>
                                    <button type="button" onclick="formatText('italic')"><i class="fa fa-italic"></i></button>
                                    <button type="button" onclick="formatText('bullet')"><i class="fa fa-list-ul"></i></button>
                                    <button type="button" onclick="formatText('number')"><i class="fa fa-list-ol"></i></button>
                                </div>
                                <div class="form-group">
                                    <textarea name="policy_content" class="policy-textarea" rows="8" 
                                              onkeyup="updatePreview(this)"><?php echo htmlspecialchars($policies['cancellation'] ?? ''); ?></textarea>
                                </div>
                                <div class="preview-section">
                                    <h4>Preview:</h4>
                                    <div class="preview-content"></div>
                                </div>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </form>
                        </div>

                        <!-- Payment Policy -->
                        <div class="policy-section">
                            <div class="policy-header">
                                <h3>Payment Policy</h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="policy_type" value="payment">
                                <div class="formatting-toolbar">
                                    <button type="button" onclick="formatText('bold')"><i class="fa fa-bold"></i></button>
                                    <button type="button" onclick="formatText('italic')"><i class="fa fa-italic"></i></button>
                                    <button type="button" onclick="formatText('bullet')"><i class="fa fa-list-ul"></i></button>
                                    <button type="button" onclick="formatText('number')"><i class="fa fa-list-ol"></i></button>
                                </div>
                                <div class="form-group">
                                    <textarea name="policy_content" class="policy-textarea" rows="8" 
                                              onkeyup="updatePreview(this)"><?php echo htmlspecialchars($policies['payment'] ?? ''); ?></textarea>
                                </div>
                                <div class="preview-section">
                                    <h4>Preview:</h4>
                                    <div class="preview-content"></div>
                                </div>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </form>
                        </div>

                        <!-- General Rules -->
                        <div class="policy-section">
                            <div class="policy-header">
                                <h3>General Rules</h3>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="policy_type" value="general_rules">
                                <div class="formatting-toolbar">
                                    <button type="button" onclick="formatText('bold')"><i class="fa fa-bold"></i></button>
                                    <button type="button" onclick="formatText('italic')"><i class="fa fa-italic"></i></button>
                                    <button type="button" onclick="formatText('bullet')"><i class="fa fa-list-ul"></i></button>
                                    <button type="button" onclick="formatText('number')"><i class="fa fa-list-ol"></i></button>
                                </div>
                                <div class="form-group">
                                    <textarea name="policy_content" class="policy-textarea" rows="8" 
                                              onkeyup="updatePreview(this)"><?php echo htmlspecialchars($policies['general_rules'] ?? ''); ?></textarea>
                                </div>
                                <div class="preview-section">
                                    <h4>Preview:</h4>
                                    <div class="preview-content"></div>
                                </div>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    function formatText(type) {
        const textarea = document.activeElement;
        if (!textarea || textarea.tagName.toLowerCase() !== 'textarea') return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let format = '';
        
        switch(type) {
            case 'bold':
                format = '**';
                break;
            case 'italic':
                format = '_';
                break;
            case 'bullet':
                const lines = text.substring(start, end).split('\n');
                const bulletList = lines.map(line => '• ' + line).join('\n');
                textarea.value = text.substring(0, start) + bulletList + text.substring(end);
                updatePreview(textarea);
                return;
            case 'number':
                const numberedLines = text.substring(start, end).split('\n');
                const numberedList = numberedLines.map((line, index) => `${index + 1}. ${line}`).join('\n');
                textarea.value = text.substring(0, start) + numberedList + text.substring(end);
                updatePreview(textarea);
                return;
        }
        
        if (format) {
            const selectedText = text.substring(start, end);
            textarea.value = text.substring(0, start) + format + selectedText + format + text.substring(end);
            updatePreview(textarea);
        }
    }

    function updatePreview(textarea) {
        const previewDiv = textarea.closest('.policy-section').querySelector('.preview-content');
        let content = textarea.value;
        
        // Convert markdown-like syntax to HTML
        content = content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/_(.*?)_/g, '<em>$1</em>')
            .replace(/^• (.*)$/gm, '<li>$1</li>')
            .replace(/^\d+\. (.*)$/gm, '<li>$1</li>')
            .replace(/\n/g, '<br>');
        
        previewDiv.innerHTML = content;
    }

    // Initialize previews on page load
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('.policy-textarea');
        textareas.forEach(textarea => updatePreview(textarea));
    });

    function deleteImage(imageId) {
        if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
            document.getElementById('delete_image_id').value = imageId;
            document.getElementById('deleteForm').submit();
        }
    }

    function updateOrder(imageId, newOrder) {
        const formData = new FormData();
        formData.append('form_type', 'slideshow_order');
        formData.append('display_orders[' + imageId + ']', newOrder);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(() => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating display order');
        });
    }
    </script>
</body>
</html> 