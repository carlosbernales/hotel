<?php
// Start session
require_once 'db_con.php';
session_start();

// Get all URL parameters
$params = [];
foreach ($_GET as $key => $value) {
    if (is_array($value)) {
        $params[$key] = array_map('htmlspecialchars', $value);
    } else {
        $params[$key] = htmlspecialchars(urldecode($value), ENT_QUOTES, 'UTF-8');
    }
}

// Extract values with null coalescing for optional parameters
$package_name = $params['package_name'] ?? 'N/A';
$base_price = (float)($params['base_price'] ?? 0);
$is_ultimate = isset($params['is_ultimate']) ? (bool)$params['is_ultimate'] : false;
$num_guests = (int)($params['guest_count'] ?? ($params['num_guests'] ?? 0));
$booking_date = $params['date'] ?? '';
$booking_time = $params['arrival_time'] ?? '';
$duration = (int)($params['duration'] ?? 0);
$payment_option = $params['payment_option'] ?? '';
$payment_method = $params['payment_method'] ?? '';

// Calculate extra guests and their cost
$base_guests = $is_ultimate ? 30 : 20; // Base number of guests included in package
$extra_guests = max(0, $num_guests - $base_guests); // Calculate number of extra guests
$extra_guest_price = 1000; // ₱1,000 per extra guest
$extra_guests_cost = $extra_guests * $extra_guest_price;

// Calculate total amount including extra guests
if (!isset($params['total_amount'])) {
    $total_amount = $base_price + $extra_guests_cost;
} else {
    $total_amount = (float)$params['total_amount'];
}

// Calculate payment amounts
$is_partial = strtolower($payment_option) === 'partial';

if ($is_partial) {
    // For partial payments, calculate 50% of total amount
    $amount_paid = $total_amount * 0.5;
    $remaining_balance = $total_amount - $amount_paid;
} else {
    // For full payment
    $amount_paid = $total_amount;
    $remaining_balance = 0;
}

// Override with URL parameters if provided (for debugging or direct URL access)
if (isset($params['amount_paid'])) {
    $amount_paid = (float)$params['amount_paid'];
}
if (isset($params['remaining_balance'])) {
    $remaining_balance = (float)$params['remaining_balance'];
}

// Format currency
function format_currency($amount) {
    return '₱' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Hide debug section by default - show with ?debug=1 */
    .debug-section { display: none; }
    .show-debug .debug-section { display: block; }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .summary-card {
            max-width: 800px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .card-body {
            padding: 2rem;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
            font-weight: 500;
        }
        .price-breakdown {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2575fc;
        }
        .payment-info {
            background-color: #f0f7ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .btn-print {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            margin-top: 1.5rem;
        }
        .btn-print:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card summary-card">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-receipt me-2"></i>Booking Summary</h2>
                <p class="mb-0">Your booking details are shown below</p>
            </div>
            <div class="card-body">
                <!-- Package Info -->
                <div class="mb-4">
                    <h4 class="text-center mb-4"><?php echo $package_name; ?></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-tag me-2"></i>Base Price</span>
                                <span class="detail-value"><?php echo format_currency($base_price); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-crown me-2"></i>Ultimate Package</span>
                                <span class="detail-value"><?php echo $is_ultimate ? 'Yes' : 'No'; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><i class="far fa-calendar-alt me-2"></i>Date</span>
                                <span class="detail-value"><?php echo date('l, F j, Y', strtotime($booking_date)); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-users me-2"></i>Guest Count</span>
                                <span class="detail-value"><?php echo $num_guests; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><i class="far fa-clock me-2"></i>Time</span>
                                <span class="detail-value"><?php echo date('g:i A', strtotime($booking_time)); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-hourglass-half me-2"></i>Duration</span>
                                <span class="detail-value"><?php echo $duration; ?> hours</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-credit-card me-2"></i>Payment Method</span>
                                <span class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $payment_method)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="payment-info">
                    <h5 class="mb-4 text-center">Payment Information</h5>
                    <div class="detail-item">
                        <span class="detail-label">Payment Option</span>
                        <span class="detail-value"><?php echo $payment_option; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Method</span>
                        <span class="detail-value"><?php echo $payment_method; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Amount</span>
                        <span class="detail-value fw-bold"><?php echo format_currency($total_amount); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Amount to Pay</span>
                        <span class="detail-value text-primary fw-bold"><?php echo format_currency($amount_paid); ?></span>
                    </div>
                    <?php if ($remaining_balance > 0): ?>
                    <div class="detail-item">
                        <span class="detail-label">Remaining Balance</span>
                        <span class="detail-value text-danger fw-bold"><?php echo format_currency($remaining_balance); ?></span>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        The remaining balance of <?php echo format_currency($remaining_balance); ?> will be settled on the event date.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4" id="action-buttons">
                    <a href="table.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                    <button id="payWithPaymongo" class="btn btn-primary" <?php echo isset($_GET['payment']) && $_GET['payment'] === 'success' ? 'style="display:none;"' : ''; ?>>
                        <i class="fas fa-credit-card me-2"></i>Pay with PayMongo
                    </button>
                    <button id="finish-booking-button" class="btn btn-success" <?php echo !isset($_GET['payment']) || $_GET['payment'] !== 'success' ? 'style="display:none;"' : ''; ?>>
                        <i class="fas fa-check-circle me-2"></i>Finish Booking
                    </button>
                </div>
                
                <!-- PayMongo Script -->
                <script src="https://js.paymongo.com/v1/paymongo.js"></script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        Payment successful! Your booking is now confirmed.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'cancelled'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Payment was cancelled. Your booking is not yet confirmed.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <!-- Payment Processing Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('payWithPaymongo');
        const finishButton = document.getElementById('finish-booking-button');
        
        // Check URL for payment success
        const urlParams = new URLSearchParams(window.location.search);
        const paymentStatus = urlParams.get('payment');
        
        // Handle finish booking button click
        if (finishButton) {
            finishButton.addEventListener('click', function() {
                // Show loading state
                const originalText = finishButton.innerHTML;
                finishButton.disabled = true;
                finishButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
                // Prepare booking data
                const bookingData = {
                    package_name: '<?php echo addslashes($package_name); ?>',
                    num_guests: <?php echo $num_guests; ?>,
                    booking_date: '<?php echo $booking_date; ?>',
                    booking_time: '<?php echo $booking_time; ?>',
                    duration: <?php echo $duration; ?>,
                    total_amount: '<?php echo $total_amount; ?>',
                    payment_method: '<?php echo $payment_method; ?>',
                    payment_option: '<?php echo $payment_option; ?>',
                    amount_paid: '<?php echo $amount_paid; ?>',
                    
                };
                
                // Show loading message
                Swal.fire({
                    title: 'Processing Booking',
                    html: 'Please wait while we process your booking...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send booking data to server
                fetch('table_finish_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(bookingData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        // Close loading message
                        Swal.close();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Successful!',
                            html: `
                                <div class="text-center">
                                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                    <h4>Your booking has been confirmed!</h4>
                                    <p class="lead">Reference #: <strong>${data.data.reference_number}</strong></p>
                                    <p>Thank you for your booking.!</p>
                                </div>
                            `,
                            confirmButtonText: 'Back to Tables',
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary px-4 py-2'
                            },
                            allowOutsideClick: false
                        }).then((result) => {
                            // Redirect to home or confirmation page
                            window.location.href = 'table.php?booking=success&ref=' + data.data.reference_number;
                        });
                    } else {
                        throw new Error(data && data.message ? data.message : 'Failed to process booking');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    let errorMessage = 'An error occurred while processing your booking. Please try again.';
                    
                    try {
                        const errorData = JSON.parse(error.message);
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        if (error.message) {
                            errorMessage = error.message;
                        }
                    }
                    
                    // Close any open dialogs
                    Swal.close();
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                                <h4>Oops! Something went wrong</h4>
                                <p>${errorMessage}</p>
                            </div>
                        `,
                        confirmButtonText: 'Try Again',
                        confirmButtonClass: 'btn btn-danger',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger px-4 py-2'
                        }
                    });
                    
                    // Reset button state
                    finishButton.disabled = false;
                    finishButton.innerHTML = originalText;
                });
            });
        }
        
        if (payButton) {
            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                processPayment();
            });
        }
        
        function processPayment() {
            // Show loading state
            const payButton = document.getElementById('payWithPaymongo');
            const originalText = payButton.innerHTML;
            payButton.disabled = true;
            payButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Prepare payment data
            const paymentData = {
                amount: <?php echo $amount_paid * 100; ?>, // Convert to centavos
                description: 'Payment for <?php echo addslashes($package_name); ?>',
                metadata: {
                    package_name: '<?php echo addslashes($package_name); ?>',
                    guest_count: '<?php echo $num_guests; ?>',
                    booking_date: '<?php echo $booking_date; ?>',
                    booking_time: '<?php echo $booking_time; ?>',
                    duration: '<?php echo $duration; ?> hours',
                    amount_paid: '<?php echo $amount_paid; ?>',
                    payment_option: '<?php echo $payment_option; ?>',
                    is_ultimate: '<?php echo $is_ultimate ? 'Yes' : 'No'; ?>'
                },
                success_url: window.location.href + '&payment=success',
                cancel_url: window.location.href + '&payment=cancelled'
            };
            
            // Make AJAX call to create PayMongo checkout session
            fetch('table_paymongo_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.checkout_url) {
                    // Store payment data in session storage to show success UI when returning
                    sessionStorage.setItem('paymentInProgress', 'true');
                    // Redirect to PayMongo checkout page
                    window.location.href = data.checkout_url;
                } else {
                    throw new Error('No checkout URL received');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing payment: ' + (error.message || 'Unknown error occurred'));
                payButton.disabled = false;
                payButton.innerHTML = originalText;
            });
        }
    });
    </script>
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
   
</body>
</html>
