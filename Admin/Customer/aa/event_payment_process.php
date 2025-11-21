<?php
// Start session
session_start();

// Store booking details in session if coming from URL parameters
if (isset($_GET['packageName'])) {
    $_SESSION['booking_details'] = [
        'packageName' => isset($_GET['packageName']) ? htmlspecialchars(urldecode($_GET['packageName']), ENT_QUOTES, 'UTF-8') : 'N/A',
        'packagePrice' => isset($_GET['packagePrice']) ? floatval($_GET['packagePrice']) : 0,
        'basePrice' => isset($_GET['basePrice']) ? floatval($_GET['basePrice']) : 0,
        'overtimeCharge' => isset($_GET['overtimeCharge']) ? floatval($_GET['overtimeCharge']) : 0,
        'overtimeHours' => isset($_GET['overtimeHours']) ? intval($_GET['overtimeHours']) : 0,
        'extraGuestCharge' => isset($_GET['extraGuestCharge']) ? floatval($_GET['extraGuestCharge']) : 0,
        'paymentMethod' => isset($_GET['paymentMethod']) ? htmlspecialchars($_GET['paymentMethod'], ENT_QUOTES, 'UTF-8') : 'N/A',
        'paymentType' => isset($_GET['paymentType']) ? htmlspecialchars($_GET['paymentType'], ENT_QUOTES, 'UTF-8') : 'N/A',
        'numberOfGuests' => isset($_GET['numberOfGuests']) ? intval($_GET['numberOfGuests']) : 0,
        'timestamp' => time(),
        'eventDate' => isset($_GET['eventDate']) ? htmlspecialchars($_GET['eventDate'], ENT_QUOTES, 'UTF-8') : 'N/A',
        'startTime' => isset($_GET['startTime']) ? htmlspecialchars($_GET['startTime'], ENT_QUOTES, 'UTF-8') : 'N/A',
        'endTime' => isset($_GET['endTime']) ? htmlspecialchars($_GET['endTime'], ENT_QUOTES, 'UTF-8') : 'N/A',
        'eventType' => isset($_GET['eventType']) ? htmlspecialchars($_GET['eventType'], ENT_QUOTES, 'UTF-8') : 'N/A',
    ];
}

// Retrieve booking details from session or set defaults
$bookingDetails = $_SESSION['booking_details'] ?? [
    'packageName' => 'N/A',
    'packagePrice' => 0,
    'basePrice' => 0,
    'overtimeCharge' => 0,
    'overtimeHours' => 0,
    'extraGuestCharge' => 0,
    'paymentMethod' => 'N/A',
    'paymentType' => 'N/A',
    'numberOfGuests' => 0,
    'eventDate' => 'N/A',
    'startTime' => 'N/A',
    'endTime' => 'N/A',
    'eventType' => 'N/A',
];

extract($bookingDetails); // Extract variables for backward compatibility

// Calculate totals and store in session
// Calculate base total using basePrice instead of packagePrice
$baseTotal = $basePrice + $overtimeCharge + $extraGuestCharge;
$isDownpayment = (strtolower($paymentType) === 'downpayment');
$downpayment = $isDownpayment ? $baseTotal * 0.5 : 0; // 50% downpayment if applicable
$remainingBalance = $isDownpayment ? $baseTotal - $downpayment : 0;
$amountToPay = $isDownpayment ? $downpayment : $baseTotal;
$total = $baseTotal; // Keep total as the full amount before downpayment

// Store calculated values in session
$_SESSION['booking_details']['total'] = $total;
$_SESSION['booking_details']['downpayment'] = $downpayment;
$_SESSION['booking_details']['remainingBalance'] = $remainingBalance;
$_SESSION['booking_details']['amountToPay'] = $amountToPay;

// Format currency values
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2, '.', ',');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Booking - <?php echo $packageName; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4bb543;
            --light-bg: #f8f9ff;
            --border-color: #e0e0e0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }
        
        .payment-container {
            max-width: 1200px;
            margin: 2rem auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-radius: 15px;
            overflow: hidden;
            background: white;
            margin: 30px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 25px 30px;
            margin-bottom: 20px;
        }
        .booking-summary {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .price-details {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            position: sticky;
            top: 2rem;
        }
        
        .price-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #eee;
        }
        
        .price-item:last-child {
            border-bottom: none;
        }
        
        .price-total {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .btn-pay-now {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            margin-top: 1.5rem;
        }
        
        .btn-pay-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(67, 97, 238, 0.3);
        }
        
        .payment-method {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
            position: relative;
            padding-bottom: 0.75rem;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        .total-amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .payment-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-option:hover, .payment-option.active {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .payment-option i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .payment-container {
                margin: 0;
                border-radius: 0;
            }
            
            .price-details {
                position: static;
                margin-top: 2rem;
            }
        }
        
        .form-label {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="payment-container p-4 p-md-5">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3">Complete Your Booking</h1>
                <div class="d-flex justify-content-center align-items-center">
                    <div class="progress" style="width: 80%; height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 66%" 
                             aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <p class="text-muted mt-3">Review your booking details and complete your payment</p>
            </div>
            
            <div class="row">
                <!-- Left Column: Booking Details -->
                <div class="col-lg-8">
                    <!-- Package Details -->
                    <div class="booking-summary">
                        <h3 class="section-title">
                            <i class="fas fa-box-open me-2"></i>Package Details
                        </h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-light p-3 rounded-circle me-3">
                                        <i class="fas fa-gift text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo $packageName; ?></h5>
                                        <small class="text-muted">Package Selected</small>
                                        <?php if ($numberOfGuests > 0): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Number of Guests: </small>
                                            <strong><?php echo $numberOfGuests; ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($_GET['eventDate'])): ?>
                                        <div class="mt-1">
                                            <small class="text-muted">Event Date: </small>
                                            <strong><?php echo date('F j, Y', strtotime($_GET['eventDate'])); ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($_GET['startTime']) && !empty($_GET['endTime'])): ?>
                                        <div class="mt-1">
                                            <small class="text-muted">Time: </small>
                                            <strong><?php echo date('g:i A', strtotime($_GET['startTime'])); ?> - <?php echo date('g:i A', strtotime($_GET['endTime'])); ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($_GET['eventType'])): ?>
                                        <div class="mt-1">
                                            <small class="text-muted">Event Type: </small>
                                            <strong><?php echo htmlspecialchars(ucfirst($_GET['eventType'])); ?></strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-3 rounded-circle me-3">
                                        <i class="fas fa-tag text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo formatCurrency($basePrice); ?></h5>
                                        <small class="text-muted">Package Price</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($overtimeCharge > 0): 
                            $overtimeHours = isset($_GET['overtimeHours']) ? floatval($_GET['overtimeHours']) : 0;
                        ?>
                        <div class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2"></i>
                                <div>
                                    <strong>Overtime Added:</strong> 
                                    <?php echo formatCurrency($overtimeCharge); ?> for <?php echo $overtimeHours; ?> extended hour(s)
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="booking-summary">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card me-2"></i>Payment Method
                        </h3>
                        <div class="payment-method">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light p-3 rounded-circle me-3">
                                    <i class="fas fa-<?php echo strtolower($paymentMethod) === 'credit card' ? 'credit-card' : 'money-bill-wave'; ?> text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?php echo $paymentMethod; ?></h5>
                                    <small class="text-muted">Payment Method</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-light p-3 rounded-circle me-3">
                                    <i class="fas fa-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?php echo $paymentType; ?></h5>
                                    <small class="text-muted">Payment Option</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Price Summary -->
                <div class="col-lg-4">
                    <div class="price-details">
                        <h3 class="section-title">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h3>
                        
                        <div class="price-item">
                            <span>Package Price</span>
                            <span><?php echo formatCurrency($basePrice); ?></span>
                        </div>
                        
                        <?php if ($overtimeCharge > 0): 
                            $overtimeHours = isset($_GET['overtimeHours']) ? floatval($_GET['overtimeHours']) : 0;
                        ?>
                        <div class="price-item">
                            <span>Overtime Charge (<?php echo $overtimeHours; ?> hours)</span>
                            <span><?php echo formatCurrency($overtimeCharge); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['extraGuestCharge']) && (float)$_GET['extraGuestCharge'] > 0): ?>
                        <div class="price-item">
                            <span>Extra Guest Charge (<?php echo (int)($numberOfGuests - 50); ?> guests)</span>
                            <span><?php echo formatCurrency((float)$_GET['extraGuestCharge']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($isDownpayment): ?>
                        <div class="price-item">
                            <span>Downpayment (50%)</span>
                            <span class="text-success">-<?php echo formatCurrency($downpayment); ?></span>
                        </div>
                        <div class="price-item">
                            <span>Remaining Balance</span>
                            <span class="text-muted"><?php echo formatCurrency($remainingBalance); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="price-item price-total mt-3 pt-3 border-top">
                            <span>Total Amount to Pay</span>
                            <span class="total-amount"><?php echo formatCurrency($amountToPay); ?></span>
                        </div>
                        
                        <form id="paymentForm" style="display: none;">
                            <input type="hidden" name="amount" value="<?php echo number_format($amountToPay, 2, '.', ''); ?>">
                            <input type="hidden" name="description" value="Booking for <?php echo htmlspecialchars($packageName); ?>">
                            <input type="hidden" name="metadata[package_name]" value="<?php echo htmlspecialchars($packageName); ?>">
                            <input type="hidden" name="metadata[event_date]" value="<?php echo htmlspecialchars($_GET['eventDate'] ?? ''); ?>">
                            <input type="hidden" name="metadata[start_time]" value="<?php echo htmlspecialchars($_GET['startTime'] ?? ''); ?>">
                            <input type="hidden" name="metadata[end_time]" value="<?php echo htmlspecialchars($_GET['endTime'] ?? ''); ?>">
                            <input type="hidden" name="metadata[number_of_guests]" value="<?php echo (int)($_GET['numberOfGuests'] ?? 0); ?>">
                            <input type="hidden" name="metadata[event_type]" value="<?php echo htmlspecialchars($_GET['eventType'] ?? ''); ?>">
                            <input type="hidden" name="metadata[is_downpayment]" value="<?php echo $isDownpayment ? '1' : '0'; ?>">
                        </form>
                        
                        <button type="button" class="btn btn-primary btn-pay-now w-100" id="proceedToPayment">
                            <i class="fas fa-lock me-2"></i>
                            <?php echo $isDownpayment ? 'Pay Downpayment' : 'Complete Payment'; ?>
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i> Secure payment. Your information is encrypted.
                            </small>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <img src="https://via.placeholder.com/200x40?text=Secure+Payment" alt="Secure Payment" class="img-fluid" style="max-height: 40px;">
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check for success parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const paymentStatus = urlParams.get('payment');
        
        // Function to show success message and update UI
        function showPaymentSuccess() {
            const payButton = document.getElementById('proceedToPayment');
            const finishButton = document.createElement('button');
            
            // Hide payment button and show success message
            if (payButton) {
                payButton.style.display = 'none';
                
                // Show SweetAlert success message
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Click Ok to finish booking.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4361ee',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                
                // Create and show finish button
                finishButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Finish Booking';
                finishButton.className = 'btn btn-success w-100 mt-3';
                finishButton.id = 'finishBookingBtn';
                
                // Insert finish button after the payment button
                payButton.parentNode.insertBefore(finishButton, payButton.nextSibling);
                
                // Add click handler for finish button
                document.getElementById('finishBookingBtn').addEventListener('click', async function() {
                    try {
                        // Show loading state
                        finishButton.disabled = true;
                        finishButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                        
                        // Get booking details from PHP session data with proper escaping and defaults
                        const bookingData = {
                            customer_name: <?php echo json_encode(isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest'); ?>,
                            package_name: <?php echo json_encode($packageName ?? ''); ?>,
                            package_price: <?php echo json_encode(isset($packagePrice) ? (float)$packagePrice : 0); ?>,
                            base_price: <?php echo json_encode(isset($basePrice) ? (float)$basePrice : 0); ?>,
                            total_amount: <?php echo json_encode(isset($total) ? (float)$total : 0); ?>,
                            paid_amount: <?php echo json_encode(isset($amountToPay) ? (float)$amountToPay : 0); ?>,
                            remaining_balance: <?php echo json_encode(isset($remainingBalance) ? (float)$remainingBalance : 0); ?>,
                            reservation_date: <?php echo json_encode(date('Y-m-d')); ?>,
                            event_date: <?php echo json_encode($eventDate ?? date('Y-m-d')); ?>,
                            start_time: <?php echo json_encode($startTime ?? '00:00:00'); ?>,
                            end_time: <?php echo json_encode($endTime ?? '23:59:59'); ?>,
                            number_of_guests: <?php echo json_encode(isset($numberOfGuests) ? (int)$numberOfGuests : 1); ?>,
                            payment_method: <?php echo json_encode($paymentMethod ?? 'gcash'); ?>,
                            payment_type: <?php echo json_encode($paymentType ?? 'Full Payment'); ?>,
                            event_type: <?php echo json_encode($eventType ?? 'Regular'); ?>,
                            overtime_hours: <?php echo json_encode(isset($overtimeHours) ? (int)$overtimeHours : 0); ?>,
                            overtime_charge: <?php echo json_encode(isset($overtimeCharge) ? (float)$overtimeCharge : 0); ?>,
                            extra_guests: 0,
                            extra_guest_charge: 0,
                            booking_status: 'Confirmed',
                            reserve_type: 'Regular',
                            booking_source: 'Website Booking'
                        };

                        console.log('Sending booking data:', bookingData);

                        // Calculate remaining balance if not set
                        if (typeof bookingData.remaining_balance === 'undefined') {
                            bookingData.remaining_balance = bookingData.total_amount - bookingData.paid_amount;
                        }


                        // Validate required fields
                        const requiredFields = ['customer_name', 'package_name', 'event_date', 'start_time', 'end_time'];
                        const missingFields = [];
                        
                        requiredFields.forEach(field => {
                            if (!bookingData[field]) {
                                missingFields.push(field);
                            }
                        });

                        if (missingFields.length > 0) {
                            throw new Error(`Missing required fields: ${missingFields.join(', ')}`);
                        }


                        // Send data to server
                        const response = await fetch('events_finish_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(bookingData)
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error(`Server responded with status ${response.status}: ${errorText}`);
                        }

                        const result = await response.json();
                        
                        if (result.status !== 'success') {
                            throw new Error(result.message || 'Unknown error occurred');
                        }

                        // Show success message and redirect
                        alert('Booking successfully completed!');
                        window.location.href = 'events.php?booking_id=' + result.booking_id;
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                        finishButton.disabled = false;
                        finishButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Try Again';
                    }
                });
            }
        }
        
        // Check if payment was successful on page load
        if (paymentStatus === 'success' || sessionStorage.getItem('showPaymentSuccess') === 'true') {
            // Clear the flag
            sessionStorage.removeItem('showPaymentSuccess');
            // Update URL to clean it up
            if (window.history.replaceState) {
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
            // Small delay to ensure DOM is fully loaded
            setTimeout(showPaymentSuccess, 100);
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle payment button click
            const payButton = document.getElementById('proceedToPayment');
            if (payButton) {
                payButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    const originalText = payButton.innerHTML;
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                    
                    // Get form data
                    const form = document.getElementById('paymentForm');
                    const formData = new FormData(form);
                    const payload = {
                        amount: parseFloat(formData.get('amount')) * 100, // Convert to centavos
                        description: formData.get('description'),
                        metadata: {
                            package_name: formData.get('metadata[package_name]'),
                            event_date: formData.get('metadata[event_date]'),
                            start_time: formData.get('metadata[start_time]'),
                            end_time: formData.get('metadata[end_time]'),
                            number_of_guests: formData.get('metadata[number_of_guests]'),
                            event_type: formData.get('metadata[event_type]'),
                            is_downpayment: formData.get('metadata[is_downpayment]')
                        },
                        success_url: window.location.href.split('?')[0] + '?payment=success',
                        cancel_url: window.location.href.split('?')[0].replace('event_payment_process.php', 'events.php') + '?payment=cancelled'
                    };

                    // Call PayMongo API
                    fetch('event_paymongo_process.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        if (data.checkout_url) {
                            // Store booking data in sessionStorage before redirecting
                            sessionStorage.setItem('showPaymentSuccess', 'true');
                            window.location.href = data.checkout_url;
                        } else {
                            throw new Error('No checkout URL received');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const errorAlert = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error processing payment: ${error.message || 'Unknown error occurred'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>`;
                        payButton.closest('.price-details').insertAdjacentHTML('afterbegin', errorAlert);
                        payButton.disabled = false;
                        payButton.innerHTML = originalText;
                    });
                });
            }
            
            // Add animation to price items on scroll
            const animateOnScroll = () => {
                const priceItems = document.querySelectorAll('.price-item');
                priceItems.forEach((item, index) => {
                    const itemTop = item.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    
                    if (itemTop < windowHeight - 50) {
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'translateX(0)';
                        }, 100 * index);
                    }
                });
            };
            
            // Set initial styles for animation
            document.querySelectorAll('.price-item').forEach(item => {
                item.style.transition = 'all 0.5s ease-out';
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
            });
            
            // Run once on load
            animateOnScroll();
            
            // Run on scroll
            window.addEventListener('scroll', animateOnScroll);
            
            // Add loading state to form submission
            document.getElementById('paymentForm').addEventListener('submit', function() {
                const button = document.getElementById('proceedToPayment');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                }
            });
        });
    </script>
</body>
</html>
