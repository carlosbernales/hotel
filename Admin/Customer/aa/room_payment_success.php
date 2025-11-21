<?php
session_start();
require 'db_con.php';

// Get reference number from URL
$reference_number = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '';

// If no reference number, redirect to rooms page
if (empty($reference_number)) {
    header('Location: roomss.php');
    exit();
}

// Here you would typically fetch the payment/booking details from the database
// using the reference number to display to the user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Hotel Booking</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
            padding: 40px 20px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-dashboard {
            background: #ffc107;
            color: #212529;
            font-weight: 600;
            padding: 10px 30px;
            margin-top: 20px;
            border: none;
        }
        .btn-dashboard:hover {
            background: #e0a800;
            color: #212529;
        }
        .reference-number {
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            font-family: monospace;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Successful!</h2>
            <p class="lead">Thank you for your payment.</p>
            
            <div class="reference-number">
                Reference: <?php echo htmlspecialchars($reference_number); ?>
            </div>
            
            <p>Your booking has been confirmed. We've sent a confirmation email with all the details.</p>
            
            <div class="whats-next mt-4">
                <h5>What's Next?</h5>
                <div class="row mt-3">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                            <p class="mb-0">Check your email for confirmation</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                            <p class="mb-0">Prepare for your stay</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 border rounded">
                            <i class="fas fa-question-circle fa-2x text-info mb-2"></i>
                            <p class="mb-0">Need help? Contact us</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="roomss.php" class="btn btn-dashboard">
                <i class="fas fa-home me-2"></i> Back to Home
            </a>
            
            <div class="mt-4 text-muted">
                <small>If you have any questions, please contact our support team at support@hotel.com</small>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
