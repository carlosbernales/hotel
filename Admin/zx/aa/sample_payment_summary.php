<?php
session_start();

// Sample order data for testing
$orderData = [
    'package_name' => 'Premium Event Package',
    'package_price' => 25000.00,
    'event_date' => '2025-03-15',
    'event_time' => '18:00',
    'guest_count' => 50,
    'event_place' => 'garden',
    'customer_name' => 'John Doe',
    'customer_email' => 'john.doe@example.com',
    'customer_phone' => '09123456789',
    'payment_option' => 'full_payment',
    'down_payment' => 0,
    'balance' => 0,
    'total_amount' => 25000.00,
    'order_id' => 'ORD-' . time() . '-' . rand(1000, 9999)
];

// Store order data in session for checkout processing
$_SESSION['order_data'] = $orderData;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-hover: #c19b2e;
            --primary-light: rgba(212, 175, 55, 0.1);
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .payment-summary-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .summary-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .summary-header h2 {
            margin: 0;
            font-weight: 600;
        }

        .summary-body {
            padding: 2rem;
        }

        .summary-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .summary-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .summary-section h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .summary-row.total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 2px solid var(--primary-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .btn-proceed-pay {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-proceed-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        }

        .btn-proceed-pay:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .badge-payment {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .order-info {
            background-color: var(--primary-light);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .order-info h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-summary-container">
            <div class="summary-header">
                <h2><i class="fas fa-receipt me-2"></i>Payment Summary</h2>
                <p class="mb-0">Please review your order details before proceeding to payment</p>
            </div>

            <div class="summary-body">
                <!-- Order Information -->
                <div class="order-info">
                    <h6><i class="fas fa-hashtag me-2"></i>Order ID</h6>
                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($orderData['order_id']); ?></p>
                </div>

                <!-- Package Details -->
                <div class="summary-section">
                    <h5><i class="fas fa-box me-2"></i>Package Details</h5>
                    <div class="summary-row">
                        <span>Package Name:</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($orderData['package_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Event Date:</span>
                        <span class="fw-bold"><?php echo date('F d, Y', strtotime($orderData['event_date'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Event Time:</span>
                        <span class="fw-bold"><?php echo date('h:i A', strtotime($orderData['event_time'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Number of Guests:</span>
                        <span class="fw-bold"><?php echo $orderData['guest_count']; ?> guests</span>
                    </div>
                    <div class="summary-row">
                        <span>Event Place:</span>
                        <span class="fw-bold"><?php echo ucfirst($orderData['event_place']); ?></span>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="summary-section">
                    <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
                    <div class="summary-row">
                        <span>Full Name:</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($orderData['customer_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Email Address:</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($orderData['customer_email']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Phone Number:</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($orderData['customer_phone']); ?></span>
                    </div>
                </div>

                <!-- Payment Breakdown -->
                <div class="summary-section">
                    <h5><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
                    <div class="summary-row">
                        <span>Payment Type:</span>
                        <span class="badge badge-payment">
                            <?php echo $orderData['payment_option'] === 'down_payment' ? 'Down Payment (50%)' : 'Full Payment'; ?>
                        </span>
                    </div>
                    
                    <?php if ($orderData['payment_option'] === 'down_payment'): ?>
                        <div class="summary-row">
                            <span>Package Price:</span>
                            <span>₱<?php echo number_format($orderData['package_price'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Down Payment (50%):</span>
                            <span>₱<?php echo number_format($orderData['down_payment'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Remaining Balance:</span>
                            <span>₱<?php echo number_format($orderData['balance'], 2); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row total">
                        <span>Total Amount Due:</span>
                        <span>₱<?php echo number_format($orderData['total_amount'], 2); ?></span>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="summary-section">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">
                            I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> and understand that this payment is non-refundable.
                        </label>
                    </div>
                </div>

                <!-- Proceed to Payment Button -->
                <div class="d-grid">
                    <button type="button" class="btn btn-proceed-pay" id="proceedToPayBtn" disabled>
                        <i class="fas fa-lock me-2"></i>
                        Proceed to Pay with PayMongo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const termsCheck = document.getElementById('termsCheck');
            const proceedBtn = document.getElementById('proceedToPayBtn');

            // Enable/disable button based on terms checkbox
            termsCheck.addEventListener('change', function() {
                proceedBtn.disabled = !this.checked;
            });

            // Handle proceed to payment button click
            proceedBtn.addEventListener('click', function() {
                // Show loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<span class="spinner-border" role="status"></span>Processing...';
                this.disabled = true;

                // Redirect to PayMongo checkout
                setTimeout(() => {
                    window.location.href = 'test_paymongo_checkout.php';
                }, 1500);
            });
        });
    </script>
</body>
</html>
