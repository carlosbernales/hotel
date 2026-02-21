<?php
session_start();

// Get order ID from URL
$orderId = $_GET['order_id'] ?? '';
$testMode = isset($_GET['test_mode']) && $_GET['test_mode'] === 'true';

// Clear order data from session after successful payment
unset($_SESSION['order_data']);
unset($_SESSION['paymongo_checkout_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --success-color: #28a745;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            margin: 20px;
            text-align: center;
        }

        .success-header {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            padding: 3rem 2rem;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .success-body {
            padding: 2rem;
        }

        .success-title {
            color: var(--success-color);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .order-details {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: left;
        }

        .btn-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), #c19b2e);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }

        .receipt-info {
            background-color: #e8f5e8;
            border-left: 4px solid var(--success-color);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Successful!</h2>
            <p class="mb-0">Your event booking has been confirmed</p>
        </div>

        <div class="success-body">
            <?php if ($testMode): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-flask me-2"></i>
                    <strong>Test Mode:</strong> This was a simulated payment transaction. No actual payment was processed.
                </div>
            <?php endif; ?>

            <div class="receipt-info">
                <i class="fas fa-envelope me-2"></i>
                <strong>Payment receipt has been sent to your email.</strong>
            </div>

            <div class="order-details">
                <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Information</h6>
                <?php if ($orderId): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Order ID:</span>
                        <strong><?php echo htmlspecialchars($orderId); ?></strong>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>Payment Status:</span>
                    <strong style="color: var(--success-color);">Completed</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Date:</span>
                    <strong><?php echo date('F d, Y'); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Time:</span>
                    <strong><?php echo date('h:i A'); ?></strong>
                </div>
            </div>

            <h5 class="success-title">What's Next?</h5>
            <p class="text-muted mb-4">
                Our team will contact you within 24 hours to confirm the details of your event. 
                You can also reach us at <strong>events@casastela.com</strong> or call us at <strong>(02) 1234-5678</strong>.
            </p>

            <div class="btn-actions">
                <a href="events.php" class="btn-primary-custom">
                    <i class="fas fa-calendar me-2"></i>
                    Book Another Event
                </a>
                <a href="index.php" class="btn-secondary-custom">
                    <i class="fas fa-home me-2"></i>
                    Back to Home
                </a>
            </div>

            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Need help? Contact our customer support team
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-redirect after 10 seconds
            setTimeout(() => {
                if (confirm('Would you like to book another event?')) {
                    window.location.href = 'events.php';
                } else {
                    window.location.href = 'index.php';
                }
            }, 10000);
        });
    </script>
</body>
</html>
