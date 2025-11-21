<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
include 'header.php';

// Fetch user data
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM userss WHERE id = ?");
    if($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
} else {
    $error_message = "User ID not found in session";
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Handle profile photo upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $uploadDir = 'uploads/profile/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $userId = $_SESSION['user_id'];
        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Use user ID as the file name
            $fileName = $userId . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;
            
            // Delete old profile photo if exists
            $oldFiles = glob($uploadDir . $userId . '.*');
            foreach ($oldFiles as $oldFile) {
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                // Update profile_photo in database
                $updateStmt = $conn->prepare("UPDATE userss SET profile_photo = ? WHERE id = ?");
                $photoPath = $targetFile;
                $updateStmt->bind_param("si", $photoPath, $userId);
                if ($updateStmt->execute()) {
                    $user['profile_photo'] = $photoPath; // Update current session data
                }
                $updateStmt->close();
            }
        }
    }
    
    // Update user information (excluding profile photo which is handled separately)
    $stmt = $conn->prepare("UPDATE userss SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?");
    if($stmt) {
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $contact_number, $address, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
            
            // Update session variables
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM userss WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">My Profile</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">Profile Information</div>
                <div class="panel-body">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="profile-photo-container mb-3">
                                    <?php 
                                    $userId = $_SESSION['user_id'] ?? 0;
                                    $profilePhoto = '';
                                    
                                    // Check for any existing profile photo with the user's ID
                                    if ($userId) {
                                        $uploadDir = 'uploads/profile/';
                                        $matchingFiles = glob($uploadDir . $userId . '.*');
                                        if (!empty($matchingFiles)) {
                                            $profilePhoto = $matchingFiles[0];
                                        }
                                    }
                                    
                                    if (!empty($profilePhoto) && file_exists($profilePhoto)): 
                                    ?>
                                        <img src="<?php echo $profilePhoto; ?>" class="img-circle" alt="Profile Photo" style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <em class="fa fa-user-circle fa-5x" style="font-size: 150px; color: #ddd;"></em>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                <small class="text-muted">Max file size: 5MB</small>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-header{
    
    color: black;
    padding: 30px 0;
    margin-bottom: 30px;
    margin-top: 100px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.profile-photo-container {
    margin-bottom: 20px;
}

.profile-photo-container img,
.profile-photo-container em {
    margin-bottom: 10px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.form-group {
    margin-bottom: 15px;
}

.btn-primary {
    margin-top: 15px;
}

.alert {
    margin-bottom: 20px;
}
</style>

<?php include 'footer.php'; ?> 