<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_con.php';

// Debug: Check session contents
error_log("Session contents in profile.php: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first";
    header('Location: login.php');
    exit();
}

try {
    $userid = $_SESSION['user_id'];
    $select_profile = $pdo->prepare("SELECT * FROM userss WHERE id = ?");
    $select_profile->execute([$userid]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

    if (!$fetch_profile) {
        $_SESSION['error'] = "Profile not found";
        header('Location: login.php');
        exit();
    }

    // Ensure CSRF token exists
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];

    // Add verification status check
    $is_verified = $fetch_profile['is_verified'] == 1;

} catch(PDOException $e) {
    error_log("Database error in profile.php: " . $e->getMessage());
    $_SESSION['error'] = "Database error occurred";
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #1a1a1a;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --border-radius: 15px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: var(--text-color);
        }

        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--secondary-color);
        }

        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            padding: 0;
        }

        .profile-sidebar {
            background: linear-gradient(135deg, var(--primary-color), #856f11);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .profile-img-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 1.5rem;
        }

    .profile-img {
            width: 100%;
            height: 100%;
        border-radius: 50%;
        object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .verified-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #2196F3;
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .profile-info {
            padding: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }

        .info-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: var(--secondary-color);
            font-weight: 500;
            font-size: 1.1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-custom {
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-edit:hover {
            background: #856f11;
            transform: translateY(-2px);
        }

        .btn-change-password {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-change-password:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }
</style>
</head>
<body>
 <?php include('nav.php'); ?>
 <?php include 'message_box.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <h2>Profile Management</h2>
            <p class="text-muted">Manage your account information</p>
        </div>

        <div class="profile-card">
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="profile-sidebar">
                        <div class="profile-img-container">
                            <img src="<?php echo isset($fetch_profile['profile_photo']) ? '../../uploads/profile/' . htmlspecialchars($fetch_profile['profile_photo']) : 'images/default-profile.png'; ?>" 
                                alt="Profile Image" class="profile-img">
                            <?php if($is_verified): ?>
                                <div class="verified-badge">
                                    <i class="fas fa-check"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="mb-2"><?php echo htmlspecialchars($fetch_profile['first_name']) . ' ' . htmlspecialchars($fetch_profile['last_name']); ?></h4>
                        <?php if(!$is_verified): ?>
                            <div class="alert alert-warning d-inline-block py-2 px-3 mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Not Verified 
                                <a href="verify.php" class="btn btn-sm btn-light ms-2">Verify Now</a>
                            </div>
                        <?php endif; ?>
                    </div>
        </div>

            <div class="col-md-8">
                    <div class="profile-info">
                        <div class="info-group">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                <i class="fas fa-envelope me-2 text-muted"></i>
                                <?php echo htmlspecialchars($fetch_profile['email']); ?>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value">
                                <i class="fas fa-phone me-2 text-muted"></i>
                                <?php echo htmlspecialchars($fetch_profile['contact_number']); ?>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">
                                <?php if($is_verified): ?>
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-2"></i>Verified Account
                                    </span>
                                <?php else: ?>
                                    <span class="text-warning">
                                        <i class="fas fa-clock me-2"></i>Pending Verification
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn btn-custom btn-edit" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                            <button class="btn btn-custom btn-change-password" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <form id="editProfileForm" action="update_account.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Profile Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                    <input type="hidden" name="id" value="<?php echo $fetch_profile['id']; ?>">

                        <div class="form-group">
                            <label><strong>First Name:</strong></label>
                            <input type="text" name="firstname" value="<?php echo htmlspecialchars($fetch_profile['first_name']); ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><strong>Last Name:</strong></label>
                            <input type="text" name="lastname" value="<?php echo htmlspecialchars($fetch_profile['last_name']); ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><strong>Email:</strong></label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($fetch_profile['email']); ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><strong>Contact Number:</strong></label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($fetch_profile['contact_number']); ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><strong>Profile Photo:</strong></label>
                            <input type="file" name="profile_photo" class="form-control-file">
                            <small class="text-muted">Recommended size: 300x300 pixels</small>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="update_password.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label>Current Password:</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>New Password:</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password:</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="text-center">
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastNotification" class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
        submitButton.disabled = true;

        fetch('update_account.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const toast = document.getElementById('toastNotification');
            const toastInstance = new bootstrap.Toast(toast);
            
            if (data.status === 'success') {
                // Update name
                const nameElement = document.querySelector('.profile-sidebar h4.mb-2');
                if (nameElement) {
                    nameElement.textContent = `${data.firstname} ${data.lastname}`;
                }

                // Update email
                const emailElement = document.querySelector('.info-value i.fa-envelope').parentElement;
                if (emailElement) {
                    emailElement.innerHTML = `<i class="fas fa-envelope me-2 text-muted"></i>${data.email}`;
                }

                // Update phone
                const phoneElement = document.querySelector('.info-value i.fa-phone').parentElement;
                if (phoneElement) {
                    phoneElement.innerHTML = `<i class="fas fa-phone me-2 text-muted"></i>${data.phone}`;
                }

                // Update profile photo if provided
                if (data.profile_photo) {
                    const profileImg = document.querySelector('.profile-img');
                    if (profileImg) {
                        profileImg.src = `../../uploads/profile/${data.profile_photo}`;
                    }
                }

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                modal.hide();

                // Show success message
                toast.classList.remove('bg-danger');
                toast.classList.add('bg-success');
                toast.querySelector('.toast-body').textContent = data.message;
            } else {
                // Show error message
                toast.classList.remove('bg-success');
                toast.classList.add('bg-danger');
                toast.querySelector('.toast-body').textContent = data.message;
            }
            
            toastInstance.show();
        })
        .catch(error => {
            console.error('Error:', error);
            const toast = document.getElementById('toastNotification');
            const toastInstance = new bootstrap.Toast(toast);
            toast.classList.remove('bg-success');
            toast.classList.add('bg-danger');
            toast.querySelector('.toast-body').textContent = 'An error occurred. Please try again.';
            toastInstance.show();
        })
        .finally(() => {
            // Reset button state
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        });
    });
    </script>
</body>
</html>
