<?php
// Enable detailed error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'run_table_orders_fix.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session to log file
error_log("Session data: " . print_r($_SESSION, true));

// Process the form submission
$output = '';
$access_allowed = false;
$error_message = '';

// Check user authentication and authorization
if (!isset($_SESSION['user_id'])) {
    $error_message = 'You must be logged in to access this page.';
    error_log("Access denied: No user_id in session");
} else {
    // For debugging, allow access to all users temporarily
    $access_allowed = true;
    
    /* Uncomment for production
    // Real role check
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $error_message = 'You need administrator privileges to access this page.';
        error_log("Access denied: User ID " . $_SESSION['user_id'] . " has insufficient privileges");
    } else {
        $access_allowed = true;
    }
    */
}

// Process the form submission if access is allowed
if ($access_allowed && isset($_POST['run_fix'])) {
    try {
        // Start output buffering
        ob_start();
        
        // Include the fix script
        include_once 'fix_table_orders.php';
        
        // Get the output and end buffering
        $output = ob_get_clean();
        
        error_log("Successfully ran fix_table_orders.php");
    } catch (Exception $e) {
        $output = "Error: " . $e->getMessage();
        error_log("Error running fix_table_orders.php: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Table Orders Fix Tool - Casa Estela</title>
    
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    
    <style>
        .content-container {
            margin-left: 250px; /* To match sidebar width */
            padding: 20px;
            margin-top: 20px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
        }
        @media (max-width: 768px) {
            .content-container {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container content-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fa fa-wrench"></i> Table Orders Fix Tool</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$access_allowed): ?>
                            <div class="alert alert-danger">
                                <h4>Access Denied</h4>
                                <p><?php echo $error_message; ?></p>
                                <a href="index.php" class="btn btn-secondary mt-3">
                                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p><strong>Description:</strong> This tool will ensure that all table bookings and reservations have corresponding entries in the orders table.</p>
                                <p>Use this if you notice that table bookings aren't showing up in your orders listing.</p>
                            </div>
                            
                            <?php if (!empty($output)): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    Fix Results
                                </div>
                                <div class="card-body">
                                    <pre><?php echo htmlspecialchars($output); ?></pre>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <form method="post" action="">
                                <p class="mb-4">Click the button below to run the fix tool:</p>
                                <button type="submit" name="run_fix" class="btn btn-primary">
                                    <i class="fa fa-play"></i> Run Table Orders Fix
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html> 