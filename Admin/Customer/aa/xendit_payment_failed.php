<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Get invoice ID from URL parameter
$invoiceId = $_GET['invoice_id'] ?? '';

// You can verify the payment status with Xendit API here
// For now, we'll just show a failure message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - E Akomoda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .failure-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .failure-icon {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% {transform: translateX(0);}
            25% {transform: translateX(-10px);}
            75% {transform: translateX(10px);}
        }
        .invoice-id {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-family: monospace;
            font-weight: bold;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="failure-container">
        <div class="failure-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1 class="mb-3">Payment Failed</h1>
        <p class="text-muted mb-4">We're sorry, but your payment could not be processed. Please try again or contact support.</p>
        
        <?php if ($invoiceId): ?>
        <div class="mb-4">
            <p class="text-muted mb-2">Transaction ID:</p>
            <div class="invoice-id"><?php echo htmlspecialchars($invoiceId); ?></div>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            If you were charged, please contact our support team with your transaction ID for assistance.
        </div>
        
        <div class="d-grid gap-2">
            <button onclick="history.back()" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Try Again
            </button>
            <a href="home.php" class="btn btn-outline-primary">
                <i class="fas fa-home me-2"></i>Return to Home
            </a>
            <a href="#" class="btn btn-outline-secondary" onclick="showSupport()">
                <i class="fas fa-headset me-2"></i>Contact Support
            </a>
        </div>
    </div>

    <script>
        function showSupport() {
            alert('Support Email: support@ekomoda.com\nSupport Hotline: +63 2 1234 5678\n\nPlease have your transaction ID ready.');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
