<?php
session_start();

// Check if order data exists
if (!isset($_SESSION['order_data'])) {
    header('Location: sample_payment_summary.php');
    exit;
}

$orderData = $_SESSION['order_data'];

// PayMongo configuration (Test Keys)
// Replace these with your actual PayMongo test keys from https://dashboard.paymongo.com/
$PAYMONGO_SECRET_KEY = 'sk_test_BQydkK8JdCqWx6rPvLxYFhRk'; // Replace with your actual test secret key
$PAYMONGO_PUBLIC_KEY = 'pk_test_GeQh8kK2JdCqWx6rPvLxYFhRk'; // Replace with your actual test public key

// For testing purposes, you can use these sample PayMongo test credentials:
// Email: test@paymongo.com
// Password: Any password (for test mode)
// Or create an account at https://dashboard.paymongo.com/ to get your own keys

// PayMongo API endpoints
$PAYMONGO_API_URL = 'https://api.paymongo.com/v1';

// Function to create PayMongo checkout session
function createPayMongoCheckout($orderData, $secretKey) {
    global $PAYMONGO_API_URL;
    
    $payload = [
        'data' => [
            'attributes' => [
                'billing' => [
                    'name' => $orderData['customer_name'],
                    'email' => $orderData['customer_email'],
                    'phone' => $orderData['customer_phone']
                ],
                'send_email_receipt' => true,
                'show_description' => true,
                'show_line_items' => true,
                'cancel_url' => 'http://localhost/Admin/Customer/aa/sample_payment_summary.php?payment=cancelled',
                'success_url' => 'http://localhost/Admin/Customer/aa/payment_success.php?order_id=' . $orderData['order_id'],
                'description' => 'Payment for ' . $orderData['package_name'] . ' - ' . $orderData['order_id'],
                'line_items' => [
                    [
                        'currency' => 'PHP',
                        'amount' => (int)($orderData['total_amount'] * 100), // Convert to centavos
                        'description' => $orderData['package_name'],
                        'name' => $orderData['package_name'],
                        'quantity' => 1
                    ]
                ],
                'payment_method_types' => ['gcash', 'paymaya', 'card', 'dob', 'bpi'],
                'reference_number' => $orderData['order_id'],
                'statement_descriptor' => 'CASA ESTELA EVENTS'
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $PAYMONGO_API_URL . '/checkout_sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($secretKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    $result = json_decode($response, true);

    if ($httpCode !== 200) {
        $errorMessage = $result['errors'][0]['detail'] ?? 'Unknown error occurred';
        throw new Exception('PayMongo API Error: ' . $errorMessage);
    }

    return $result['data']['id'];
}

// Function to get checkout session details
function getCheckoutSession($sessionId, $secretKey) {
    global $PAYMONGO_API_URL;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $PAYMONGO_API_URL . '/checkout_sessions/' . $sessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($secretKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    $result = json_decode($response, true);

    if ($httpCode !== 200) {
        $errorMessage = $result['errors'][0]['detail'] ?? 'Unknown error occurred';
        throw new Exception('PayMongo API Error: ' . $errorMessage);
    }

    return $result['data'];
}

// Process checkout
try {
    // Create checkout session
    $checkoutSessionId = createPayMongoCheckout($orderData, $PAYMONGO_SECRET_KEY);
    
    // Get checkout session details to retrieve the checkout URL
    $checkoutSession = getCheckoutSession($checkoutSessionId, $PAYMONGO_SECRET_KEY);
    $checkoutUrl = $checkoutSession['attributes']['checkout_url'];
    
    // Store checkout session ID for reference
    $_SESSION['paymongo_checkout_id'] = $checkoutSessionId;
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $checkoutUrl = null;
    
    // Fallback to test mode for development
    if (strpos($error, 'PayMongo API Error') !== false) {
        // Create a simulated checkout for testing
        $checkoutUrl = 'payment_success.php?order_id=' . $orderData['order_id'] . '&test_mode=true';
        $testMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayMongo Checkout - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://js.paymongo.com/v1/paymongo.js"></script>
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-hover: #c19b2e;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .checkout-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .checkout-body {
            padding: 2rem;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .payment-method.active {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .payment-method i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .btn-pay {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        .order-summary {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h3><i class="fas fa-credit-card me-2"></i>Secure Checkout</h3>
            <p class="mb-0">Complete your payment securely with PayMongo</p>
        </div>

        <div class="checkout-body">
            <?php if (isset($error) && !isset($testMode)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Testing Mode:</strong> Please update your PayMongo API keys in the file to use real payment processing.
                </div>
                <div class="text-center">
                    <a href="sample_payment_summary.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Summary
                    </a>
                </div>
            <?php elseif (isset($testMode)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Test Mode Active:</strong> Using simulated payment flow for development.
                </div>
                
                <div class="order-summary">
                    <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Order ID:</span>
                        <strong><?php echo htmlspecialchars($orderData['order_id']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Package:</span>
                        <strong><?php echo htmlspecialchars($orderData['package_name']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <strong style="color: var(--primary-color);">₱<?php echo number_format($orderData['total_amount'], 2); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-3"><i class="fas fa-lock me-2"></i>Simulated Payment Methods</h6>
                    <div class="payment-methods">
                        <div class="payment-method">
                            <i class="fas fa-mobile-alt"></i>
                            <div>GCash</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-wallet"></i>
                            <div>Maya</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-credit-card"></i>
                            <div>Credit/Debit Card</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-university"></i>
                            <div>BPI Online</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    This is a test payment. Click below to simulate successful payment.
                </div>

                <button type="button" class="btn btn-pay" id="proceedPaymentBtn">
                    <i class="fas fa-play me-2"></i>
                    Simulate Payment Success
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-flask me-1"></i>
                        Test Mode - No actual payment will be processed
                    </small>
                </div>
            <?php elseif ($checkoutUrl): ?>
                <div class="order-summary">
                    <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Order ID:</span>
                        <strong><?php echo htmlspecialchars($orderData['order_id']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Package:</span>
                        <strong><?php echo htmlspecialchars($orderData['package_name']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <strong style="color: var(--primary-color);">₱<?php echo number_format($orderData['total_amount'], 2); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-3"><i class="fas fa-lock me-2"></i>Choose Payment Method</h6>
                    <div class="payment-methods">
                        <div class="payment-method">
                            <i class="fas fa-mobile-alt"></i>
                            <div>GCash</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-wallet"></i>
                            <div>Maya</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-credit-card"></i>
                            <div>Credit/Debit Card</div>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-university"></i>
                            <div>BPI Online</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You will be redirected to PayMongo's secure payment page to complete your transaction.
                </div>

                <button type="button" class="btn btn-pay" id="proceedPaymentBtn">
                    <i class="fas fa-shield-alt me-2"></i>
                    Proceed to Secure Payment
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-lock me-1"></i>
                        Secured by PayMongo • 256-bit SSL encryption
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($testMode)): ?>
                // Test mode handling
                const proceedBtn = document.getElementById('proceedPaymentBtn');
                const paymentMethods = document.querySelectorAll('.payment-method');
                
                // Handle payment method selection
                paymentMethods.forEach(method => {
                    method.addEventListener('click', function() {
                        paymentMethods.forEach(m => m.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Handle simulated payment
                proceedBtn.addEventListener('click', function() {
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border" role="status"></span>Processing...';
                    this.disabled = true;

                    // Simulate payment processing delay
                    setTimeout(() => {
                        window.location.href = '<?php echo $checkoutUrl; ?>';
                    }, 2000);
                });

                // Auto-select first payment method
                if (paymentMethods.length > 0) {
                    paymentMethods[0].classList.add('active');
                }
            <?php elseif ($checkoutUrl): ?>
                // Real PayMongo checkout
                const proceedBtn = document.getElementById('proceedPaymentBtn');
                const paymentMethods = document.querySelectorAll('.payment-method');
                
                // Handle payment method selection
                paymentMethods.forEach(method => {
                    method.addEventListener('click', function() {
                        paymentMethods.forEach(m => m.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Handle proceed to payment
                proceedBtn.addEventListener('click', function() {
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border" role="status"></span>Redirecting...';
                    this.disabled = true;

                    // Redirect to PayMongo checkout
                    setTimeout(() => {
                        window.location.href = '<?php echo $checkoutUrl; ?>';
                    }, 1500);
                });

                // Auto-select first payment method
                if (paymentMethods.length > 0) {
                    paymentMethods[0].classList.add('active');
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
