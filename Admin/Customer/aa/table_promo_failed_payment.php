<?php
session_start();

// Get invoice ID from URL
$invoiceId = $_GET['invoice_id'] ?? '';

// Clear table promo session variables on failed payment
unset($_SESSION['table_promo_guest_firstname']);
unset($_SESSION['table_promo_guest_lastname']);
unset($_SESSION['table_promo_guest_email']);
unset($_SESSION['table_promo_guest_phone']);
unset($_SESSION['table_promo_guests']);
unset($_SESSION['table_promo_special_requests']);
unset($_SESSION['table_promo_payment_option']);
unset($_SESSION['table_promo_amount']);
unset($_SESSION['table_promo_total_amount']);
unset($_SESSION['table_promo_offer_title']);
unset($_SESSION['table_promo_offer_price']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .failed-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .failed-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .failed-icon {
            width: 100px;
            height: 100px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 40px;
        }
    </style>
</head>
<body>
    <div class="failed-container">
        <div class="failed-card">
            <div class="failed-icon">
                <i class="fas fa-times"></i>
            </div>
            
            <h1 class="mb-3">Payment Failed</h1>
            <p class="text-muted mb-4">
                Unfortunately, your payment could not be processed. Please try again or contact support if the problem persists.
            </p>
            
            <?php if (!empty($invoiceId)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Transaction ID: <?php echo htmlspecialchars($invoiceId); ?>
            </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <h6 class="alert-heading">What to do next:</h6>
                <ul class="text-start mb-0">
                    <li>Check your payment details and try again</li>
                    <li>Ensure you have sufficient funds</li>
                    <li>Contact your bank if there were any issues</li>
                    <li>Reach out to our support team for assistance</li>
                </ul>
            </div>
            
            <div class="d-flex gap-3 justify-content-center">
                <a href="home.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <a href="home.php#best-offers" class="btn btn-outline-primary">
                    <i class="fas fa-redo me-2"></i>Try Again
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
