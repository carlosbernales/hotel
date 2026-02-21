<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
include 'header.php';

// Fetch user data
$user = [];
$error_message = '';
$success_message = '';

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM userss WHERE id = ?");
        if($stmt) {
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error_message = "Error fetching user data: " . $e->getMessage();
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
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Handle profile photo upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $uploadDir = '../../Admin/adminBackend/user_photo/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Validate file size (5MB max)
            $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($_FILES['profile_image']['size'] > $maxFileSize) {
                $error_message = "File size too large. Maximum size is 5MB.";
            } else {
                $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileName = $user_id . '_' . time() . '.' . $fileExtension;
                    $targetFile = $uploadDir . $fileName;
                    
                    // Delete old profile photos if they exist
                    $oldPhotos = glob($uploadDir . $user_id . '_*.*');
                    foreach ($oldPhotos as $oldPhoto) {
                        if (is_file($oldPhoto)) {
                            unlink($oldPhoto);
                        }
                    }
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                        $stmt = $pdo->prepare("UPDATE userss SET profile_photo = ? WHERE id = ?");
                        $stmt->execute([$fileName, $user_id]);
                        $user['profile_photo'] = $fileName;
                        $success_message = "Profile photo updated successfully!";
                    } else {
                        $error_message = "Failed to upload profile photo.";
                    }
                } else {
                    $error_message = "Invalid file type. Please upload JPG, PNG, or GIF.";
                }
            }
        }
        
        // Handle password change if provided
        if (!empty($current_password) && !empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error_message = "New passwords do not match!";
            } else {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM userss WHERE id = ?");
                $stmt->execute([$user_id]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($current_password, $userData['password'])) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE userss SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Current password is incorrect!";
                }
            }
        }
        
        // Update user information
        if (empty($error_message)) {
            $stmt = $pdo->prepare("UPDATE userss SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $contact_number, $address, $user_id]);
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM userss WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update session variables
            $_SESSION['fullname'] = $first_name . ' ' . $last_name;
            
            if (empty($success_message)) {
                $success_message = "Profile updated successfully!";
            }
        }
    } catch(PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!-- Main Content -->
<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">My Profile</h2>
            <p class="text-muted mb-0">Manage your personal information and account settings</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Profile Information Card -->
        <div class="col-lg-8 mb-4">
            <div class="profile-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="profileForm">
                        <div class="row">
                            <!-- Profile Photo -->
                            <div class="col-md-4 text-center">
                                <div class="profile-photo-container">
                                    <div class="profile-photo">
                                        <?php 
                                        if (!empty($user['profile_photo']) && file_exists('../../Admin/adminBackend/user_photo/' . $user['profile_photo'])): 
                                        ?>
                                            <img src="../../Admin/adminBackend/user_photo/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="default-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="photo-upload">
                                        <input type="file" name="profile_image" id="profileImage" accept="image/*" style="display: none;">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('profileImage').click()">
                                            <i class="fas fa-camera me-1"></i>Change Photo
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">JPG, PNG, GIF (Max 5MB)</small>
                                </div>
                            </div>
                            
                            <!-- User Information -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" 
                                               value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" 
                                           value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Account Settings Card -->
        <div class="col-lg-4 mb-4">
            <div class="profile-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="passwordForm">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" id="currentPassword">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" id="newPassword">
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                                </div>
                                <small class="text-muted" id="passwordStrengthText"></small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirmPassword">
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Account Info Card -->
            <div class="profile-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label class="info-label">Username</label>
                        <div class="info-value"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Role</label>
                        <div class="info-value">
                            <span class="badge bg-primary">Cashier</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Member Since</label>
                        <div class="info-value">
                            <?php 
                            if (!empty($user['created_at'])) {
                                echo date('F j, Y', strtotime($user['created_at']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Last Login</label>
                        <div class="info-value">
                            <?php 
                            if (!empty($user['last_login'])) {
                                echo date('M j, Y H:i', strtotime($user['last_login']));
                            } else {
                                echo 'First time login';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: none;
    overflow: hidden;
}

.profile-card .card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    padding: 20px 25px;
    border: none;
}

.profile-card .card-body {
    padding: 25px;
}

.profile-photo-container {
    text-align: center;
}

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin: 0 auto 15px;
    overflow: hidden;
    border: 4px solid var(--primary-color);
    position: relative;
}

.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.default-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-light);
    color: var(--primary-color);
    font-size: 48px;
}

.photo-upload {
    margin-top: 10px;
}

.info-item {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f2f6;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
    display: block;
}

.info-value {
    font-size: 14px;
    color: var(--secondary-color);
    font-weight: 500;
}

.password-strength {
    margin-top: 5px;
}

.progress-bar {
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .profile-photo {
        width: 120px;
        height: 120px;
    }
    
    .default-avatar {
        font-size: 36px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength checker
    const newPassword = document.getElementById('newPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordStrengthText = document.getElementById('passwordStrengthText');
    
    if (newPassword) {
        newPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let strengthText = '';
            let strengthClass = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText = 'Weak';
                    strengthClass = 'bg-danger';
                    passwordStrength.style.width = '20%';
                    break;
                case 2:
                case 3:
                    strengthText = 'Medium';
                    strengthClass = 'bg-warning';
                    passwordStrength.style.width = '60%';
                    break;
                case 4:
                case 5:
                    strengthText = 'Strong';
                    strengthClass = 'bg-success';
                    passwordStrength.style.width = '100%';
                    break;
            }
            
            passwordStrength.className = 'progress-bar ' + strengthClass;
            passwordStrengthText.textContent = strengthText;
        });
    }
    
    // Profile image preview
    const profileImage = document.getElementById('profileImage');
    if (profileImage) {
        profileImage.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const profilePhoto = document.querySelector('.profile-photo');
                    profilePhoto.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

</body>
</html>
