<?php
require_once 'session.php';
require_once 'db_con.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$orderData = [];
$orderSummary = [];
$error = '';

try {
    // Check if we're returning from a successful payment
    $isSuccess = isset($_GET['status']) && $_GET['status'] === 'success';
    
    // Check if we're finishing the order
    if (isset($_GET['finish_order']) && isset($_SESSION['order_data'])) {
        // Clear all session data when finishing the order
        unset($_SESSION['order_data']);
        unset($_SESSION['order_summary']);
        unset($_SESSION['payment_success']);
        unset($_SESSION['cart']); // Clear the cart session
        
        // Redirect to clear the URL parameters
        header('Location: cafes.php?order_completed=1');
        exit();
    }
    
    // If we have order data in session and it's a successful payment, use it
    if (isset($_SESSION['order_data'], $_SESSION['order_summary']) && 
        (isset($_SESSION['payment_success']) || $isSuccess) && 
        !isset($_GET['order_data'])) {
        // Mark payment as successful in session
        $_SESSION['payment_success'] = true;
        
        // Get order data from session
        $orderData = $_SESSION['order_data'];
        $orderSummary = $_SESSION['order_summary'] ?? [];
        $jsonOrderData = json_encode($orderData);
        
        // If this is a fresh success, show success message
        if ($isSuccess) {
            // Set a flag to show success message via JavaScript
            echo '<script>window.paymentSuccess = true;</script>';
        }
    } 
    // Check if order_data is provided in the URL (new order)
    elseif (isset($_GET['order_data'])) {
        // Clear any existing order data from session for a fresh start
        unset($_SESSION['order_data']);
        unset($_SESSION['order_summary']);
        unset($_SESSION['payment_success']);
        
        // Decode the order data from URL
        $orderData = json_decode(urldecode($_GET['order_data']), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid order data format');
        }
        
        // Clear any existing cart data
        unset($_SESSION['cart']);
        
        // Get all data directly from URL parameters
        $subtotal = $_GET['subtotal'] ?? 0;
        $total = $_GET['total'] ?? $subtotal;
        $paymentMethod = $_GET['payment_method'] ?? 'cash';
        $paymentOption = $_GET['payment_option'] ?? 'full';
        $paymentOptionDisplay = $_GET['payment_option_display'] ?? 'Full Payment';
        $itemCount = $_GET['total_items'] ?? 0;
        
        // Calculate remaining balance for downpayment
        $remainingBalance = 0;
        if (stripos($paymentOption, 'downpayment') !== false || $paymentOption === 'partial') {
            $remainingBalance = $total / 2;
            $total = $remainingBalance; // Update total to show only the downpayment amount
        }

        // Format numbers
        $orderSummary = [
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
            'item_count' => $itemCount,
            'payment_method' => $paymentMethod,
            'payment_option' => $paymentOption,
            'payment_option_display' => $paymentOptionDisplay,
            'remaining_balance' => number_format($remainingBalance, 2)
        ];
        
        // Store order data in session for after payment
        $sessionData = array_merge($orderData, [
            'payment_method' => $paymentMethod,
            'payment_option' => $paymentOption,
            'payment_option_display' => $paymentOptionDisplay,
            'subtotal' => $subtotal,
            'total' => $total,
            'total_items' => $itemCount,
            'items' => $orderData['items'] // Ensure items are included
        ]);
        
        $_SESSION['order_data'] = $sessionData;
        $_SESSION['order_summary'] = $orderSummary;
        $_SESSION['payment_success'] = false; // Reset success flag for new orders
        
        // Store all data for JavaScript
        $jsonOrderData = json_encode($_SESSION['order_data']);
    } else {
        throw new Exception('No order data provided');
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information - Cafe Order</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --success-color: #00b894;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .payment-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .order-summary {
            background: white;
            padding: 2rem;
            border-right: 1px solid #eee;
        }
        
        .payment-methods {
            padding: 2rem;
            background: var(--light-gray);
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-details {
            flex-grow: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .item-price {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        
        .summary-total {
            font-size: 1.25rem;
            font-weight: 700;
            border-top: 2px solid #eee;
            margin-top: 1rem;
            padding-top: 1rem;
        }
        
        .payment-method {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
            background: rgba(108, 92, 231, 0.05);
        }
        
        .payment-method.active {
            border-color: var(--primary-color);
            background: rgba(108, 92, 231, 0.1);
        }
        
        .btn-pay-now {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-pay-now:hover {
            background: #5a4fcf;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .order-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-gray);
        }
        
        .order-status {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .payment-container {
                margin: 0;
                border-radius: 0;
            }
            
            .order-summary {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
                <a href="menu.php" class="alert-link">Return to Menu</a>
            </div>
        <?php else: ?>
            <div class="payment-container">
                <div class="row g-0">
                    <!-- Order Summary -->
                    <div class="col-lg-7 order-summary">
                        <div class="order-header">
                            <h2 class="m-0">Order Summary</h2>
                            <span id="orderStatus" class="order-status"><?php echo isset($_GET['status']) && $_GET['status'] === 'success' ? 'Paid' : 'Pending Payment'; ?></span>
                        </div>
                        
                        <div class="order-items mb-4">
                            <?php if (!empty($orderData['items'])): ?>
                                <?php foreach ($orderData['items'] as $item): ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <div class="item-name">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                                <?php if (!empty($item['quantity']) && $item['quantity'] > 1): ?>
                                                    <span class="text-muted">× <?php echo $item['quantity']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['addons'])): ?>
                                                <div class="item-addons text-muted" style="font-size: 0.875rem;">
                                                    <?php 
                                                    $addons = [];
                                                    foreach ($item['addons'] as $addon) {
                                                        $addonText = $addon['name'];
                                                        if (isset($addon['price']) && $addon['price'] > 0) {
                                                            $addonText .= ' (₱' . number_format($addon['price'], 2) . ')';
                                                        }
                                                        $addons[] = $addonText;
                                                    }
                                                    echo 'Add: ' . implode(', ', $addons);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-price">
                                            ₱<?php echo number_format($item['price'] * ($item['quantity'] ?? 1), 2); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-summary-details">
                            <div class="summary-row">
                                <span>Subtotal (<?php echo $orderSummary['item_count'] ?? 0; ?> items)</span>
                                <span>₱<?php echo $orderSummary['subtotal'] ?? '0.00'; ?></span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total</span>
                                <span class="text-primary">₱<?php echo $orderSummary['total'] ?? '0.00'; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="col-lg-5 payment-methods">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h3 class="h5 mb-4"><i class="fas fa-receipt me-2"></i> Order Summary</h3>
                                
                                <!-- Payment Method Card -->
                                <div class="card bg-light mb-4 border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-0 text-muted small">Payment Method</h6>
                                                <div class="h5 mb-0 mt-1">
                                                    <i class="fas fa-credit-card me-2 text-primary"></i>
                                                    <span class="text-capitalize"><?php echo htmlspecialchars($orderSummary['payment_method']); ?></span>
                                                </div>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                                <i class="fas fa-check-circle text-primary"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                            <div>
                                                <h6 class="mb-0 text-muted small">Payment Option</h6>
                                                <div class="fw-medium"><?php echo htmlspecialchars($orderSummary['payment_option_display']); ?></div>
                                            </div>
                                            <?php if (stripos($orderSummary['payment_option'] ?? '', 'downpayment') !== false || ($orderSummary['payment_option'] ?? '') === 'partial'): ?>
                                            <div class="text-end">
                                                <h6 class="mb-0 text-muted small">Amount to Pay</h6>
                                                <div class="h5 text-success mb-0">
                                                    ₱<?php echo number_format(floatval(str_replace(',', '', $orderSummary['total'])), 2); ?>
                                                    <span class="text-muted small fw-normal">/ 50%</span>
                                                </div>
                                                <div class="text-muted small">
                                                    Full Amount: ₱<?php echo number_format(floatval(str_replace(',', '', $orderSummary['total'])) * 2, 2); ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (stripos($orderSummary['payment_option'] ?? '', 'downpayment') !== false || ($orderSummary['payment_option'] ?? '') === 'partial'): ?>
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2 text-warning">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </div>
                                                <div class="small text-muted">
                                                    Remaining balance of ₱<?php echo number_format(floatval(str_replace(',', '', $orderSummary['total'])), 2); ?> 
                                                    will be paid upon order collection.
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        
                        <!-- Payment Action Buttons -->
                        <div id="paymentButtonContainer" class="mt-4">
                            <button id="payNowBtn" class="btn btn-primary btn-lg w-100 py-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-credit-card me-2"></i> 
                                <span>Proceed to Payment</span>
                                <span class="ms-auto">₱<?php echo number_format(floatval(str_replace(',', '', $orderSummary['total'])), 2); ?></span>
                            </button>
                            <p class="text-center text-muted small mt-2 mb-0">
                                <i class="fas fa-lock me-1"></i> Secure payment processing
                            </p>
                        </div>
                        
                        <div id="finishOrderContainer" style="display: none;">
                            <div class="alert alert-success" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div>Payment Successful!</div>
                                </div>
                            </div>
                            <form id="completeOrderForm" action="cafe_insert_order.php" method="post">
                                <input type="hidden" name="order_data" value='<?php echo htmlspecialchars(json_encode($orderData), ENT_QUOTES, 'UTF-8'); ?>'>
                                <input type="hidden" name="current_date" id="currentDateInput" value="">
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                                    <i class="fas fa-check-circle me-2"></i> Complete Order
                                </button>
                            </form>
                            <p class="text-center text-muted small mt-2">
                                Your order has been paid. Click to save the order and return to the menu.
                            </p>
                        </div>
                        
                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="cafes.php" class="text-muted text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Back to Menu
                            </a>
                            <p class="small text-muted mt-2 mb-0">
                                Need help? <a href="#" class="text-primary">Contact support</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Check for successful payment
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('status');
            
            // Check if we should show success state (from URL param or window.paymentSuccess)
            if (paymentStatus === 'success' || window.paymentSuccess) {
                // Update order status to Paid
                document.getElementById('orderStatus').textContent = 'Paid';
                document.getElementById('orderStatus').classList.remove('order-status');
                document.getElementById('orderStatus').classList.add('text-success', 'fw-bold');
                
                // Show success message
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Your payment has been processed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6c5ce7'
                });
                
                // Show finish order button and hide payment button
                const paymentContainer = document.getElementById('paymentButtonContainer');
                const finishContainer = document.getElementById('finishOrderContainer');
                
                if (paymentContainer && finishContainer) {
                    paymentContainer.style.display = 'none';
                    finishContainer.style.display = 'block';
                }
                
                // Clean up URL without reloading the page
                if (window.history && window.history.replaceState && paymentStatus === 'success') {
                    const cleanUrl = window.location.href.split('?')[0];
                    window.history.replaceState({}, document.title, cleanUrl);
                }
            }
        });

        // Handle pay now button click
        document.getElementById('payNowBtn').addEventListener('click', function() {
            const orderData = <?php echo $jsonOrderData; ?>;
            
            // Disable button and show loading state
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Redirect to PayMongo checkout with order data
            const params = new URLSearchParams();
            params.append('order_data', JSON.stringify(orderData));
            
            // Add any additional parameters needed by PayMongo
            if (orderData.payment_method) {
                params.append('payment_method', orderData.payment_method);
            }
            if (orderData.payment_option) {
                params.append('payment_option', orderData.payment_option);
            }
            
            // Redirect to PayMongo checkout with success redirect
            const successUrl = window.location.href.split('?')[0] + '?status=success';
            const checkoutUrl = 'cafe_paymongo_checkout.php?success_url=' + encodeURIComponent(successUrl) + '&' + params.toString();
            window.location.href = checkoutUrl;
        });
        
        // Parse URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
        
        // If order_data is in URL, we don't need to do anything as PHP is handling it
        // This is just for demonstration of URL parameter handling in JS
        const orderDataParam = getUrlParameter('order_data');
        if (orderDataParam) {
            try {
                const orderData = JSON.parse(decodeURIComponent(orderDataParam));
                console.log('Order data from URL:', orderData);
                // You can process the order data here if needed
            } catch (e) {
                console.error('Error parsing order data:', e);
            }
        }
        
        // Set current date when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Format current date as YYYY-MM-DD HH:MM:SS
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            
            // Set the current date in the hidden input
            const dateInput = document.getElementById('currentDateInput');
            if (dateInput) {
                dateInput.value = formattedDate;
            }
            
            // Handle form submission
            const form = document.getElementById('completeOrderForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault(); // Prevent default form submission
                    
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    
                    try {
                        // Get the order data from the hidden input
                        const orderDataInput = form.querySelector('input[name="order_data"]');
                        let orderData;
                        
                        try {
                            // Parse the JSON to ensure it's valid
                            orderData = JSON.parse(orderDataInput.value);
                        } catch (e) {
                            console.error('Error parsing order data:', e);
                            throw new Error('Invalid order data. Please try again.');
                        }
                        
                        // Add current date to the order data
                        const now = new Date();
                        const orderDate = now.toISOString().slice(0, 19).replace('T', ' ');
                        
                        // Create form data
                        const formData = new FormData();
                        formData.append('order_data', JSON.stringify(orderData));
                        formData.append('current_date', orderDate);
                        
                        // Send the request
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData
                        });
                        
                        // Check if response is OK and parse JSON
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Server responded with:', errorText);
                            throw new Error('Server error: ' + response.status);
                        }
                        
                        const data = await response.json().catch(e => {
                            console.error('Error parsing JSON response:', e);
                            throw new Error('Invalid response from server');
                        });
                        
                        if (data.success) {
                            // Show success message
                            await Swal.fire({
                                title: 'Order Complete!',
                                text: data.message || 'Your order has been successfully placed.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#00b894'
                            });
                            
                            // Redirect to order confirmation or menu
                            window.location.href = data.redirect || 'cafes.php?order_completed=1';
                        } else {
                            throw new Error(data.message || 'Failed to process order');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        await Swal.fire({
                            title: 'Error',
                            text: error.message || 'An error occurred while processing your order. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d63031'
                        });
                    } finally {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
            }
        });
    </script>
</body>
</html>