<?php
require_once '../../includes/access_control.php';

// Set page title
$pageTitle = 'Unauthorized Access';

// Include header
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Access Denied</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-5x text-danger mb-4"></i>
                        <h2>Unauthorized Access</h2>
                    </div>
                    <p class="lead">You do not have permission to access this page.</p>
                    <p>If you believe this is an error, please contact the system administrator.</p>
                    
                    <div class="mt-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="home.php" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Return to Dashboard
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                            </a>
                        <?php endif; ?>
                        
                        <a href="javascript:history.back()" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
