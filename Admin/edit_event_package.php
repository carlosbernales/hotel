<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location:event_management.php');
    exit();
}

$package_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $max_guests = $_POST['max_guests'];
    $duration = $_POST['duration'];
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['package_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'uploads/event_packages/' . $new_filename;
            
            if (move_uploaded_file($_FILES['package_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                $query = "SELECT image_path FROM event_packages WHERE id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $package_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if (!empty($row['image_path']) && file_exists($row['image_path'])) {
                        unlink($row['image_path']);
                    }
                }
                
                $image_path = $upload_path;
                $query = "UPDATE event_packages SET name = ?, description = ?, price = ?, max_guests = ?, duration = ?, image_path = ? WHERE id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("ssdiisi", $name, $description, $price, $max_guests, $duration, $image_path, $package_id);
            } else {
                $_SESSION['error_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
        }
    } else {
        // No new image uploaded, update without changing image
        $query = "UPDATE event_packages SET name = ?, description = ?, price = ?, max_guests = ?, duration = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssdiis", $name, $description, $price, $max_guests, $duration, $package_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Package updated successfully!";
        header('Location:event_management.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating package: " . $con->error;
    }
}

// Fetch package details
$query = "SELECT * FROM event_packages WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

if (!$package) {
    header('Location:event_management.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event Package - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .section-title {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #DAA520;
        }

        .current-image {
            max-width: 300px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .image-preview {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 4px;
            display: none;
        }

        .custom-file-upload {
            border: 1px solid #ddd;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .custom-file-upload:hover {
            background: #e9ecef;
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
                <li class="active">Edit Package</li>
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

        <div class="card">
            <h3 class="section-title">Edit Package</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Package Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($package['name']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price (PHP)</label>
                            <input type="number" class="form-control" name="price" step="0.01" value="<?php echo htmlspecialchars($package['price']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Maximum Guests</label>
                            <input type="number" class="form-control" name="max_guests" value="<?php echo htmlspecialchars($package['max_guests']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Duration (hours)</label>
                            <input type="number" class="form-control" name="duration" value="<?php echo htmlspecialchars($package['duration']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Package Image</label>
                    <?php if (!empty($package['image_path'])): ?>
                        <div>
                            <p>Current Image:</p>
                            <img src="<?php echo htmlspecialchars($package['image_path']); ?>" alt="Current package image" class="current-image">
                        </div>
                    <?php endif; ?>
                    <div>
                        <label class="custom-file-upload">
                            <input type="file" name="package_image" id="package_image" accept="image/*" style="display: none;">
                            <i class="fa fa-cloud-upload"></i> <?php echo !empty($package['image_path']) ? 'Change Image' : 'Upload Image'; ?>
                        </label>
                        <small class="text-muted ml-2">Recommended size: 800x600 pixels</small>
                    </div>
                    <img id="image_preview" class="image-preview">
                </div>
                
                <div class="form-group">
                    <label>Package Description</label>
                    <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($package['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Package</button>
                    <a href="event_management.php" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    // Image preview functionality
    document.getElementById('package_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image_preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html> 
</html> 