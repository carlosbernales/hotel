<?php
require_once 'includes/Session.php';
Session::start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Casa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <?php include('nav.php'); ?>
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Error</h1>
            </div>
            <div class="auth-body">
                <?php if (Session::hasFlash('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo Session::getFlash('error'); ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        An unexpected error occurred. Please try again later.
                    </div>
                <?php endif; ?>
                <a href="login.php" class="btn btn-primary">Return to Login</a>
            </div>
        </div>
    </div>
</body>
</html> 