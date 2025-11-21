<?php
session_start();
require_once 'db_con.php';

// Initialize variables
$orderData = [];
$firstItem = [];
$orderSummary = [];
$successMessage = '';
$errorMessage = '';

// Handle order completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order'])) {
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get order data from session
        if (!isset($_SESSION['pending_order'])) {
            throw new Exception('No pending order found in session');
        }
        
        $orderData = $_SESSION['pending_order']['order_data'] ?? [];
        $orderSummary = $_SESSION['pending_order']['order_summary'] ?? [];
        
        if (empty($orderData) || empty($orderSummary)) {
            throw new Exception('Invalid order data');
        }
        
        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (total_amount, order_date, status) VALUES (?, NOW(), 'completed')");
        $stmt->execute([$orderSummary['total']]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        if (!empty($orderData['items']) && is_array($orderData['items'])) {
            foreach ($orderData['items'] as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items 
                    (order_id, item_name, quantity, price, category) 
                    VALUES (?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $orderId,
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $item['category'] ?? 'food' // Default category if not specified
                ]);
                
                $orderItemId = $pdo->lastInsertId();
                
                // Insert addons if any
                if (!empty($item['addons']) && is_array($item['addons'])) {
                    foreach ($item['addons'] as $addon) {
                        $stmt = $pdo->prepare("INSERT INTO order_item_addons 
                            (order_item_id, addon_name, addon_price) 
                            VALUES (?, ?, ?)");
                            
                        $stmt->execute([
                            $orderItemId,
                            $addon['name'],
                            $addon['price']
                        ]);
                    }
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Clear the session after successful order
        unset($_SESSION['pending_order']);
        
        // Set success message
        $successMessage = 'Order completed successfully! Thank you for your purchase.';
        
        // Return success response if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $successMessage]);
            exit;
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $errorMessage = 'Error completing order: ' . $e->getMessage();
        
        // Return error response if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit;
        }
    }
}

// Initialize order items array
$orderItems = [];

// Check if we're coming back from payment
if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
    // Retrieve order data from session
    if (isset($_SESSION['pending_order'])) {
        $orderData = $_SESSION['pending_order']['order_data'] ?? [];
        $orderSummary = $_SESSION['pending_order']['order_summary'] ?? [];
        
        // Get all order items
        if (!empty($orderData['items']) && is_array($orderData['items'])) {
            $orderItems = $orderData['items'];
        }
    }
} 
// Check for order data in URL
else if (isset($_GET['order_data'])) {
    // Decode the order data from URL
    $orderData = json_decode(urldecode($_GET['order_data']), true);
    
    // Get all order items
    if (!empty($orderData['items']) && is_array($orderData['items'])) {
        $orderItems = $orderData['items'];
    }
    
    // Set order summary from URL parameters
    $orderSummary = [
        'subtotal' => $_GET['subtotal'] ?? 0,
        'total' => $_GET['total'] ?? 0,
        'payment_method' => $_GET['payment_method'] ?? '',
        'payment_option' => $_GET['payment_option'] ?? 'full',
        'payment_option_display' => $_GET['payment_option_display'] ?? 'Full Payment'
    ];
    
    // Store in session for after payment
    $_SESSION['pending_order'] = [
        'order_data' => $orderData,
        'order_summary' => $orderSummary,
        'timestamp' => time()
    ];
    }

// Check for new order data in URL
else if (isset($_GET['order_data'])) {
    // Store order data in session
    $orderData = json_decode(urldecode($_GET['order_data']), true);
    
    // Extract first item details
    if (!empty($orderData['items']) && is_array($orderData['items'])) {
        $firstItem = $orderData['items'][0];
    }
    
    // Prepare order summary
    $orderSummary = [
        'total_items' => $_GET['total_items'] ?? 0,
        'subtotal' => $_GET['subtotal'] ?? 0,
        'total' => $_GET['total'] ?? 0,
        'payment_method' => $_GET['payment_method'] ?? '',
        'payment_option' => $_GET['payment_option'] ?? '',
        'payment_option_display' => $_GET['payment_option_display'] ?? 'Full Payment'
    ];
    
    // Store in session for after payment
    $_SESSION['pending_order'] = [
        'order_data' => $orderData,
        'order_summary' => $orderSummary,
        'timestamp' => time()
    ];
}

// Clear session data if no order data found
if (empty($orderData) && isset($_SESSION['pending_order'])) {
    unset($_SESSION['pending_order']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4a6cf7;
            --primary-hover: #3a5ce4;
            --success-color: #28a745;
            --success-hover: #218838;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fc;
            color: #2d3748;
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            color: var(--primary-color);
            transform: translateX(-2px);
        }
        
        .back-btn i {
            margin-right: 8px;
            transition: var(--transition);
        }
        
        .payment-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary-color), #6c5ce7);
            color: white;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        
        .payment-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .payment-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
        }
        
        @media (max-width: 768px) {
            .payment-body {
                grid-template-columns: 1fr;
            }
        }
        
        .order-summary {
            background: #f8fafc;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .order-summary h4 {
            margin-top: 0;
            color: #1a202c;
            font-size: 1.25rem;
            font-weight: 600;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .item-name {
            font-weight: 500;
            color: #2d3748;
        }
        
        .item-price {
            font-weight: 600;
            color: #2d3748;
        }
        
        .addon-list {
            margin-top: 0.5rem;
            padding-left: 1rem;
            border-left: 2px solid #e2e8f0;
        }
        
        .addon-item {
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 0.25rem;
            display: flex;
            justify-content: space-between;
        }
        
        .order-totals {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .total-row:last-child {
            margin-bottom: 0;
            padding-top: 0.75rem;
            border-top: 1px dashed #e2e8f0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #1a202c;
        }
        
        .payment-method {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .payment-method p {
            margin: 0.5rem 0;
            display: flex;
            justify-content: space-between;
        }
        
        .payment-method p strong {
            color: #4a5568;
            font-weight: 500;
        }
        
        .payment-form {
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
        }
        
        .payment-form h4 {
            margin-top: 0;
            color: #1a202c;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .btn-pay {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 1rem;
        }
        
        .btn-pay i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-pay-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-pay-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-pay-success {
            background-color: var(--success-color);
            color: white;
            display: none;
        }
        
        .btn-pay-success:hover {
            background-color: var(--success-hover);
        }
        
        .payment-note {
            font-size: 0.85rem;
            color: #718096;
            text-align: center;
            margin-top: 1.5rem;
            line-height: 1.5;
        }
        
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            background: #edf2f7;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .payment-method-badge i {
            margin-right: 8px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    
    <div class="container">
        <a href="cafes.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Menu
        </a>
        
        <div class="payment-container">
            <div class="payment-header">
                <h2>Payment Information</h2>
            </div>
            
            <div class="payment-body">
                <div class="order-summary">
                    <h4>Order Summary</h4>
                    <div id="orderSummary">
                        <?php if (!empty($orderItems)): ?>
                            <?php foreach ($orderItems as $item): ?>
                                <div class="order-item">
                                    <div class="item-details">
                                        <div class="item-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <span class="text-muted">×<?php echo $item['quantity']; ?></span>
                                        </div>
                                        <?php if (!empty($item['addons'])): ?>
                                            <div class="addon-list">
                                                <?php foreach ($item['addons'] as $addon): ?>
                                                    <div class="addon-item">
                                                        <span>+ <?php echo htmlspecialchars($addon['name']); ?></span>
                                                        <span>₱<?php echo number_format($addon['price'], 2); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-price">
                                        ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-totals">
                                <div class="total-row">
                                    <span>Subtotal</span>
                                    <span>₱<?php echo number_format($orderSummary['subtotal'] ?? 0, 2); ?></span>
                                </div>
                                <?php 
                                $isDownpayment = isset($orderSummary['payment_option']) && $orderSummary['payment_option'] === 'partial';
                                if ($isDownpayment && isset($orderSummary['total'])) {
                                    $downpaymentAmount = $orderSummary['total'] * 0.5;
                                    $remainingBalance = $orderSummary['total'] - $downpaymentAmount;
                                    echo '<div class="total-row">';
                                    echo '<span>Downpayment (50%)</span>';
                                    echo '<span>₱' . number_format($downpaymentAmount, 2) . '</span>';
                                    echo '</div>';
                                    echo '<div class="total-row">';
                                    echo '<span>Remaining Balance</span>';
                                    echo '<span>₱' . number_format($remainingBalance, 2) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="total-row">
                                    <span>Total Amount</span>
                                    <span>₱<?php echo number_format($orderSummary['total'] ?? 0, 2); ?></span>
                                </div>
                            </div>
                            
                            <div class="payment-method">
                                <div class="payment-method-badge">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Paying with <?php echo htmlspecialchars(ucfirst($orderSummary['payment_method'] ?? '')); ?></span>
                                </div>
                                <p class="mt-2">
                                    <strong>Payment Option:</strong>
                                    <span class="float-end"><?php echo htmlspecialchars($orderSummary['payment_option_display'] ?? 'Full Payment'); ?></span>
                                </p>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No order details available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="payment-form">
                    <h4>Complete Your Payment</h4>
                    <div id="paymentButtonContainer">
                        <?php if (!isset($_GET['payment']) || $_GET['payment'] !== 'success'): ?>
                            <button type="button" id="payWithPaymongo" class="btn-pay btn-pay-primary" onclick="processPaymongoPayment()">
                                <i class="fas fa-credit-card"></i> Pay with PayMongo
                            </button>
                        <?php else: ?>
                            <div class="payment-success-message mb-3">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> Payment successful! Please complete your order below.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="payment-note">
                        <?php echo isset($_GET['payment']) && $_GET['payment'] === 'success' 
                            ? 'Thank you for your payment. Please complete your order.' 
                            : 'You\'ll be redirected to a secure payment page to complete your transaction.'; ?>
                    </p>
                    
                    <!-- Complete Order Form (initially hidden) -->
                    <form id="completeOrderForm" method="POST" style="display: <?php echo (isset($_GET['payment']) && $_GET['payment'] === 'success') ? 'block' : 'none'; ?>;">
                        <button type="button" id="finishOrderBtn" class="btn btn-success w-100 py-3">
                            <i class="fas fa-check-circle"></i> Complete Order
                        </button>
                    </form>
                    
                    <script>
                    // Function to handle PayMongo payment
                    function processPaymongoPayment() {
                        const payButton = document.getElementById('payWithPaymongo');
                        const originalText = payButton.innerHTML;
                        
                        // Disable button and show loading
                        payButton.disabled = true;
                        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        
                        // Get the payment amount from the order summary
                        const totalAmount = parseFloat('<?php echo number_format($orderSummary['total'] ?? 0, 2, '.', ''); ?>');
                        const isPartial = '<?php echo ($orderSummary['payment_option'] ?? '') === 'partial' ? 'true' : 'false'; ?>' === 'true';
                        
                        // Calculate the amount to charge (full or 50% for partial)
                        const amountToCharge = isPartial ? (totalAmount * 0.5) : totalAmount;
                        
                        // Prepare the payment data
                        const paymentData = {
                            amount: amountToCharge, // Amount in pesos
                            description: 'Payment for Order #' + Date.now(),
                            order_data: '<?php echo urlencode(json_encode($orderData ?? [])); ?>',
                            payment_option: '<?php echo $orderSummary['payment_option'] ?? 'full'; ?>',
                            amount_in_pesos: amountToCharge.toFixed(2) // For reference
                        };
                        
                        // Send request to PayMongo checkout
                        fetch('cafe_paymongo_checkout.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(paymentData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.checkout_url) {
                                // Redirect to PayMongo checkout
                                window.location.href = data.checkout_url;
                            } else {
                                throw new Error(data.error || 'Failed to process payment');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Re-enable button
                            payButton.disabled = false;
                            payButton.innerHTML = originalText;
                            
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Error',
                                text: error.message || 'Failed to process payment. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                    
                    document.addEventListener('DOMContentLoaded', function() {
                        const finishOrderBtn = document.getElementById('finishOrderBtn');
                        
                        if (finishOrderBtn) {
                            finishOrderBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                
                                // Show loading state
                                const originalText = finishOrderBtn.innerHTML;
                                finishOrderBtn.disabled = true;
                                finishOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                                
                                // Submit to complete_order.php
                                fetch('complete_order.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: 'complete_order=1'
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => { throw err; });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        // Show success message and redirect
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Order Completed!',
                                            text: data.message,
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            // Redirect to confirmation page
                                            if (data.redirect) {
                                                // Check if order_id is available in the response
                                                const orderId = data.orderId || (data.order_id ? data.order_id : '');
                                                const redirectUrl = data.redirect + (orderId ? '?order_id=' + orderId : '');
                                                window.location.href = redirectUrl;
                                            } else {
                                                // Default redirect if no specific redirect URL is provided
                                                window.location.href = 'order_confirmation.php' + (data.orderId ? '?order_id=' + data.orderId : '');
                                            }
                                        });
                                    } else {
                                        throw new Error(data.message || 'Failed to complete order');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Re-enable button
                                    finishOrderBtn.disabled = false;
                                    finishOrderBtn.innerHTML = originalText;
                                    
                                    // Show error message
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: error.message || 'An error occurred while completing your order. Please try again.'
                                    });
                                });
                            });
                        }
                    });
                    </script>
                </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Function to handle order completion
        function completeOrder() {
            const finishBtn = document.getElementById('finishOrderBtn');
            
            // Disable button to prevent multiple clicks
            finishBtn.disabled = true;
            finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Submit to complete_order.php via AJAX
            fetch('complete_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'complete_order=1'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Completed!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to order confirmation page
                            if (data.redirect) {
                                window.location.href = data.redirect + '?order_id=' + data.orderId;
                            } else {
                                window.location.href = 'cafes.php';
                            }
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to complete order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Re-enable button
                finishBtn.disabled = false;
                finishBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Order';
                
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while completing your order. Please try again.'
                });
            });
        }
        
        // Handle complete order button click
        document.addEventListener('DOMContentLoaded', function() {
            const finishOrderBtn = document.getElementById('finishOrderBtn');
            
            if (finishOrderBtn) {
                finishOrderBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    completeOrder();
                });
            }
            
            // If we came back from payment with success, show the complete order button
            if (window.location.search.includes('payment=success')) {
                const completeOrderForm = document.getElementById('completeOrderForm');
                if (completeOrderForm) {
                    completeOrderForm.style.display = 'block';
                    
                    // Show success message
                    if (!document.querySelector('.swal2-container')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful!',
                            text: 'Your payment was processed successfully. Please click Complete Order to finalize your order.',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            }
                                // Redirect to order confirmation page
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.href = 'order_confirmation.php';
                                }
                            });
                        } else {
                            throw new Error(data.message || 'Failed to complete order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Re-enable button on error
                        finishOrderBtn.disabled = false;
                        finishOrderBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Order';
                        
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to complete order. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    });
                });
                
                // Show the form if we're coming back from a successful payment
                if (window.location.search.includes('payment=success')) {
                    completeOrderForm.style.display = 'block';
                }
            }
        });
                        // Add click handler if not already added
                        const newClickHandler = function() {
                    // Show loading state
                    Swal.fire({
                        title: 'Completing Order',
                        text: 'Please wait while we finalize your booking...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            
                            // Get order data from URL
                            const urlParams = new URLSearchParams(window.location.search);
                            const orderData = urlParams.get('order_data');
                            
                            // Send data to process the order
                            fetch('finish_order.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'order_data=' + encodeURIComponent(orderData) + '&action=complete_booking'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Booking Complete!',
                                        html: `
                                            <div class="text-center">
                                                <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                                                <h3>Thank You!</h3>
                                                <p>Your booking has been confirmed.</p>
                                                <p class="mb-0">Booking Reference: <strong>${data.booking_ref}</strong></p>
                                            </div>
                                        `,
                                        confirmButtonText: 'Return to Home',
                                        allowOutsideClick: false
                                    }).then((result) => {
                                        // Redirect to home page when user clicks the button
                                        window.location.href = 'cafes.php';
                                    });
                                } else {
                                    throw new Error(data.message || 'Failed to complete booking');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Failed to complete booking. Please contact support.',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                };

        // Function to handle PayMongo payment
        function processPaymongoPayment() {
            // Show loading state
            Swal.fire({
                title: 'Processing Payment',
                text: 'Preparing your payment...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    // Store the current order data in session storage before redirecting
                    const orderData = <?php echo json_encode($orderData ?? []); ?>;
                    if (orderData) {
                        sessionStorage.setItem('pendingOrder', JSON.stringify(orderData));
                    }
                    
                    // Get the order total from the page or calculate it
                    const orderTotal = <?php echo isset($orderSummary['total']) ? $orderSummary['total'] : '0'; ?>;
                    
                    // Build success URL with current URL and payment=success parameter
                    const currentUrl = new URL(window.location.href);
                    // Remove any existing payment parameter to avoid duplicates
                    currentUrl.searchParams.delete('payment');
                    // Add payment=success parameter
                    currentUrl.searchParams.set('payment', 'success');
                    // Preserve all existing URL parameters
                    const successUrl = currentUrl.toString();
                    
                    // Store the current URL parameters in session storage for later use
                    const currentParams = new URLSearchParams(window.location.search);
                    sessionStorage.setItem('originalUrlParams', currentParams.toString());
                    
                    // Call your PayMongo checkout endpoint
                    // First store the success URL with all parameters in session storage
                    const successUrlObj = new URL(successUrl);
                    // Restore original URL parameters
                    const originalParams = sessionStorage.getItem('originalUrlParams');
                    if (originalParams) {
                        const params = new URLSearchParams(originalParams);
                        // Add back all original parameters except 'payment'
                        params.forEach((value, key) => {
                            if (key !== 'payment') {
                                successUrlObj.searchParams.set(key, value);
                            }
                        });
                    }
                    const finalSuccessUrl = successUrlObj.toString();
                    
                    fetch('cafe_paymongo_checkout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            amount: orderTotal,
                            description: 'Cafe Order Payment',
                            order_data: JSON.stringify(<?php echo json_encode($orderData ?? []); ?>),
                            success_url: finalSuccessUrl,
                            metadata: {
                                order_id: '<?php echo $orderSummary['reference_number'] ?? uniqid(); ?>',
                                timestamp: new Date().toISOString(),
                                session_id: '<?php echo session_id(); ?>',
                                original_url: window.location.href
                            }
                        })
                    })
                    .then(async response => {
                        const responseText = await response.text();
                        console.log('Raw response:', responseText); // Log raw response
                        
                        // Check if the response is JSON
                        let responseData;
                        try {
                            responseData = JSON.parse(responseText);
                            console.log('Parsed response data:', responseData); // Log parsed data
                        } catch (e) {
                            // If not JSON, log the actual response for debugging
                            console.error('Non-JSON response:', responseText);
                            throw new Error('Invalid response format from server. ' + 
                                         'Status: ' + response.status + ' ' + response.statusText);
                        }
                        
                        if (!response.ok) {
                            console.error('Error response:', responseData);
                            throw new Error(responseData.error || 
                                         responseData.message || 
                                         'Payment request failed with status ' + response.status);
                        }
                        
                        return responseData;
                    })
                    .then(data => {
                        if (data && data.status === 'success' && data.checkout_url) {
                            console.log('Redirecting to checkout URL:', data.checkout_url);
                            // Store the success URL with the reference number
                            const successUrl = new URL(window.location.href);
                            successUrl.searchParams.set('payment', 'success');
                            if (data.reference_number) {
                                successUrl.searchParams.set('ref', data.reference_number);
                            }
                            // Preserve all URL parameters
                            const originalParams = sessionStorage.getItem('originalUrlParams');
                            if (originalParams) {
                                const params = new URLSearchParams(originalParams);
                                params.forEach((value, key) => {
                                    if (key !== 'payment') {
                                        successUrl.searchParams.set(key, value);
                                    }
                                });
                            }
                            const finalSuccessUrl = successUrl.toString();
                            // Store the success URL in session storage
                            sessionStorage.setItem('successUrl', finalSuccessUrl);
                            
                            // Redirect to PayMongo checkout
                            window.location.href = data.checkout_url;
                        } else if (data && (data.checkout_url || (data.data && data.data.attributes && data.data.attributes.checkout_url))) {
                            // Fallback for older response format
                            const checkoutUrl = data.checkout_url || 
                                             (data.data && data.data.attributes && data.data.attributes.checkout_url);
                            if (checkoutUrl) {
                                window.location.href = checkoutUrl;
                            } else {
                                throw new Error('No valid checkout URL found in the response');
                            }
                        } else {
                            console.error('Invalid response data:', data);
                            throw new Error('No valid checkout URL found in the response');
                        }
                    })
                    .catch(error => {
                        console.error('Payment Error:', error);
                        
                        // Log additional error details
                        if (error instanceof TypeError) {
                            console.error('Network error or CORS issue. Check if the server is running and accessible.');
                        }
                        
                        // Default error message
                        let errorMessage = 'Failed to process payment. Please try again.';
                        if (error.message) {
                            errorMessage += ' (' + error.message + ')';
                        }
                        
                        // Try to extract a more specific error message
                        if (error.message) {
                            errorMessage = error.message;
                            
                            // Clean up common error messages
                            if (errorMessage.includes('Failed to fetch')) {
                                errorMessage = 'Unable to connect to the payment server. Please check your internet connection.';
                            } else if (errorMessage.includes('NetworkError')) {
                                errorMessage = 'Network error occurred. Please check your connection and try again.';
                            } else if (errorMessage.includes('Invalid response format')) {
                                errorMessage = 'Received an invalid response from the server. Please try again later.';
                            }
                        }
                        
                        // Close any existing loading dialogs
                        Swal.close();
                        
                        // Show error to user
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Error',
                            html: `
                                <div style="text-align: left;">
                                    <p>${errorMessage}</p>
                                    <p class="small text-muted mt-2">
                                        If the problem persists, please try again later or contact support.
                                    </p>
                                </div>
                            `,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545',
                            allowOutsideClick: false
                        });
                    });
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're coming back from a successful payment
            const urlParams = new URLSearchParams(window.location.search);
            const isPaymentSuccess = urlParams.get('payment') === 'success';
            
            // If we're coming back from a successful payment, show the Finish Order button
            if (isPaymentSuccess) {
                enableFinishOrder(true);
            } else {
                // Hide the Finish Order button initially for new payments
                const finishBtn = document.getElementById('finishOrderBtn');
                if (finishBtn) {
                    finishBtn.style.display = 'none';
                }
            }
            
            // Use the PHP order data that was already processed at the top of the file
            const orderData = <?php echo json_encode($orderData ?? null); ?>;
            
            // If no order data is found, show an error and redirect
            if (!orderData || Object.keys(orderData).length === 0) {
                // Only show the error if we're not in a payment success flow
                if (!urlParams.get('payment') === 'success') {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Order Found',
                        text: 'No pending order found. Please start your order again.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'cafes.php';
                    });
                }
                return;
            }
            
            // Display order summary
            const orderSummary = document.getElementById('orderSummary');
            let summaryHTML = `
                <div class="mb-3">
                    <p><strong>Order Type:</strong> ${orderData.order_type === 'advance' ? 'Advance Order' : 'Regular Order'}</p>
                    <p><strong>Payment Method:</strong> ${orderData.payment_method}</p>
                    <p><strong>Subtotal:</strong> ₱${parseFloat(orderData.total_amount).toFixed(2)}</p>
                    ${orderData.extra_fee > 0 ? `<p><strong>Extra Fee:</strong> ₱${parseFloat(orderData.extra_fee).toFixed(2)}</p>` : ''}
                    <h5>Total: ₱${parseFloat(orderData.final_total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                </div>
                <div class="order-items">
                    <h6>Items:</h6>
                    <ul class="list-group">
            `;
            
            orderData.items.forEach(item => {
                summaryHTML += `
                    <li class="list-group-item">
                        ${item.quantity}x ${item.name} - ₱${parseFloat(item.price).toFixed(2)}
                        ${item.addons && item.addons.length > 0 ? 
                            `<div class="ms-3"><small class="text-muted">Add-ons: ${item.addons.map(a => a.name).join(', ')}</small></div>` : ''}
                    </li>
                `;
            });
            
            summaryHTML += `
                    </ul>
                </div>
            `;
            
            orderSummary.innerHTML = summaryHTML;
            
            // Add event listener for the finish order button
            const finishOrderBtn = document.getElementById('finishOrderBtn');
            if (finishOrderBtn) {
                finishOrderBtn.addEventListener('click', function() {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing',
                        text: 'Completing your order...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            
                            // Get order data from session storage or PHP variable
                            const orderData = JSON.parse(sessionStorage.getItem('pendingOrder') || '<?php echo addslashes(json_encode($orderData ?? [])); ?>');
                            
                            // Add timestamp and additional required fields
                            const orderPayload = {
                                ...orderData,
                                customer_name: orderData.customer_name || 'Walk-in Customer',
                                customer_email: orderData.customer_email || 'walkin@example.com',
                                customer_phone: orderData.customer_phone || 'N/A',
                                order_status: 'completed',
                                payment_status: 'paid',
                                created_at: new Date().toISOString()
                            };
                            
                            // Submit the form to complete the order
                            fetch('complete_order.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(orderPayload)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => {
                                        // If we got a JSON error response, use it
                                        const error = new Error(err.message || 'Failed to complete order');
                                        error.details = err.error || '';
                                        throw error;
                                    }).catch(() => {
                                        // If response is not JSON, throw with status text
                                        throw new Error(`Server responded with status: ${response.status} ${response.statusText}`);
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    // Clear the pending order from session storage
                                    sessionStorage.removeItem('pendingOrder');
                                    
                                    // Redirect to success page with order reference if available
                                    const successUrl = 'order_success.php?order_id=' + (data.order_id || '');
                                    if (data.order_reference) {
                                        successUrl += '&ref=' + encodeURIComponent(data.order_reference);
                                    }
                                    window.location.href = successUrl;
                                } else {
                                    const error = new Error(data.message || 'Failed to complete order');
                                    error.details = data.error || '';
                                    throw error;
                                }
                            })
                            .catch(error => {
                                console.error('Order completion error:', error);
                                
                                // Prepare error message
                                let errorMessage = 'Failed to complete order.';
                                let errorDetails = '';
                                
                                if (error.message) {
                                    errorMessage = error.message;
                                }
                                
                                if (error.details) {
                                    errorDetails = `<br><br><small class="text-muted">${error.details}</small>`;
                                } else if (error.message && error.message.includes('Failed to fetch')) {
                                    errorDetails = '<br><br><small class="text-muted">Unable to connect to the server. Please check your internet connection and try again.</small>';
                                }
                                
                                // Show error in a more user-friendly way
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Order Error',
                                    html: `${errorMessage}${errorDetails}`,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-danger',
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    // Re-enable the finish order button
                                    const finishBtn = document.getElementById('finishOrderBtn');
                                    if (finishBtn) {
                                        finishBtn.disabled = false;
                                    }
                                });
                            });
                        }
                    });
                });
            }
            
            // Handle form submission
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                Swal.fire({
                    title: 'Processing Payment',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Simulate payment processing
                setTimeout(() => {
                    // Here you would typically send the payment details to your server
                    // and process the payment through a payment gateway
                    
                    // For demo purposes, we'll simulate a successful payment
                    const formData = new FormData();
                    formData.append('order_data', JSON.stringify(orderData));
                    formData.append('payment_status', 'completed');
                    
                    // Determine the correct processing endpoint based on order type
                    const processingEndpoint = orderData.order_type === 'advance' ? 'process_order.php' : 'process_orders.php';
                    
                    // Submit order with payment status
                    fetch(processingEndpoint, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Clear the pending order from sessionStorage
                            sessionStorage.removeItem('pendingOrder');
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Successful!',
                                text: 'Your order has been placed successfully.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Redirect to order confirmation page or home
                                window.location.href = 'cafes.php';
                            });
                        } else {
                            throw new Error(data.message || 'Failed to process order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Failed',
                            text: error.message || 'An error occurred while processing your payment. Please try again.'
                        });
                    });
                }, 2000);
            });
        });
    </script>
</body>
</html>
