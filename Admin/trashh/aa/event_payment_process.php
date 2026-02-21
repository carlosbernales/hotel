<?php
require_once 'db_con.php';
session_start();

// Store GET parameters in session if they exist
if (!empty($_GET) && !isset($_GET['payment'])) {
    $_SESSION['event_booking'] = [
        'package_id'     => isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0,
        'package_name'   => $_GET['package_name'] ?? '',
        'package_price'  => isset($_GET['package_price']) ? (float)$_GET['package_price'] : 0,
        'event_place'    => $_GET['event_place'] ?? '',
        'event_type'     => $_GET['event_type'] ?? '',
        'event_date'     => $_GET['event_date'] ?? '',
        'event_time'     => $_GET['event_time'] ?? '',
        'guest_count'    => isset($_GET['guest_count']) ? (int)$_GET['guest_count'] : 0,
        'payment_option' => $_GET['payment_option'] ?? '',
        'payment_method' => $_GET['payment_method'] ?? 'paymongo',
        'created_at'     => date('Y-m-d H:i:s')
    ];
}

// Get data from session
$booking = $_SESSION['event_booking'] ?? [];

// Extract variables with default values
$package_id = $booking['package_id'] ?? 0;
$package_name = $booking['package_name'] ?? '';
$package_price = $booking['package_price'] ?? 0;
$event_place = $booking['event_place'] ?? '';
$event_type = $booking['event_type'] ?? '';
$event_date = $booking['event_date'] ?? '';
$event_time = $booking['event_time'] ?? '';
$guest_count = $booking['guest_count'] ?? 0;
$payment_option = $booking['payment_option'] ?? '';
$payment_method = $booking['payment_method'] ?? 'paymongo';

// Calculate payment amounts
$total_amount = $package_price;
$down_payment = $total_amount * 0.5;
$balance = $total_amount - $down_payment;

// Handle payment success
$showSuccess = false;
if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
    $showSuccess = true;
    // Don't clear the session here, we'll do it when user navigates away
}

// Calculate payment amounts
$total_amount = $package_price; // Package price is a fixed amount
$down_payment = $total_amount * 0.5; // 50% down payment
$balance = $total_amount - $down_payment;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking Confirmation - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-hover: #c19b2e;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .confirmation-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .confirmation-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .confirmation-header h1 {
            font-weight: 700;
            margin: 0;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
        }
        
        .confirmation-header p {
            opacity: 0.9;
            margin: 0.5rem 0 0;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }
        
        .confirmation-body {
            padding: 2.5rem;
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
            font-size: 1.4rem;
        }
        
        .detail-card {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 0.8rem;
            flex-wrap: wrap;
        }
        
        .detail-label {
            font-weight: 500;
            color: #555;
            width: 200px;
            flex-shrink: 0;
        }
        
        .detail-value {
            flex: 1;
            color: #222;
            font-weight: 500;
        }
        
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .amount-label {
            color: #555;
        }
        
        .amount-value {
            font-weight: 600;
        }
        
        .total-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .confirmation-footer {
            background: #f9f9f9;
            padding: 2rem;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .btn-confirm {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-confirm:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            color: white;
        }
        
        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .confirmation-body {
                padding: 1.5rem;
            }
            
            .detail-label {
                width: 120px;
                margin-bottom: 0.3rem;
            }
            
            .confirmation-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1><i class="fas fa-check-circle me-2"></i>Booking <?php echo $showSuccess ? 'Paid' : 'Confirmation'; ?></h1>
            <p><?php echo $showSuccess ? 'Your Booking has been Paid!' : 'Review your event details before proceeding to payment'; ?></p>
        </div>
        
        <div class="confirmation-body">
            <h3 class="section-title">Event Details</h3>
            
            <div class="detail-card">
                <div class="detail-row">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?php echo $package_name; ?></h4>
                            <p class="text-muted mb-0">Event Package</p>
                        </div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-map-marker-alt me-2"></i>Event Place:</span>
                    <span class="detail-value text-capitalize"><?php echo $event_place; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-calendar-day me-2"></i>Event Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($event_date)); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-clock me-2"></i>Event Time:</span>
                    <span class="detail-value"><?php echo date('h:i A', strtotime($event_time)); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-users me-2"></i>Guest Count:</span>
                    <span class="detail-value"><?php echo number_format($guest_count); ?> persons</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-tag me-2"></i>Event Type:</span>
                    <span class="detail-value text-capitalize"><?php echo $event_type; ?></span>
                </div>
            </div>
            
            <h3 class="section-title mt-5">Payment Details</h3>
            
            <div class="detail-card">
                <div class="detail-row mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 me-2"><?php echo ucfirst(str_replace('_', ' ', $payment_option)); ?></h5>
                                <?php if ($showSuccess): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted mb-0"><?php echo ucfirst($payment_method); ?> Payment</p>
                        </div>
                    </div>
                </div>
                
                <div class="amount-row">
                    <span class="amount-label">Package Price</span>
                    <span class="amount-value">₱<?php echo number_format($total_amount, 2); ?></span>
                </div>
                
                <?php if ($payment_option === 'partial'): ?>
                <div class="amount-row">
                    <span class="amount-label">Down Payment (50%)</span>
                    <span class="amount-value">₱<?php echo number_format($down_payment, 2); ?></span>
                </div>
                
                <div class="amount-row">
                    <span class="amount-label">Balance to be paid on event day</span>
                    <span class="amount-value">₱<?php echo number_format($balance, 2); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="amount-row pt-3 mt-2 border-top">
                    <span class="amount-label total-amount"><?php echo $payment_option === 'full_payment' ? 'Total Amount' : 'Amount to Pay Now'; ?></span>
                    <span class="amount-value total-amount">₱<?php echo number_format($payment_option === 'full_payment' ? $total_amount : $down_payment, 2); ?></span>
                </div>
            </div>
            
            <?php if ($showSuccess): ?>
            <div class="alert alert-success mt-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Payment Successful!</h5>
                        <p class="mb-0">Your payment of <strong>₱<?php echo number_format($payment_option === 'full_payment' ? $total_amount : $down_payment, 2); ?></strong> has been received. A confirmation email has been sent to your registered email address.</p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                Please prepare the payment amount before proceeding. A confirmation email will be sent to you after submission.
            </div>
            <?php endif; ?>
        </div>
        
        <div class="confirmation-footer">
            <div class="d-flex flex-column w-100">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheckbox" disabled >
                    <label class="form-check-label" for="termsCheckbox">
                        I agree to the <a href="#" id="showTermsLink">Terms and Conditions</a>
                    </label>
                </div>
                <div class="d-flex justify-content-center w-100">
                    <?php if ($showSuccess): ?>
                    <div class="d-flex gap-3">
                        
                        <a href="events.php" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <button type="button" class="btn btn-confirm" id="completeBooking">
                            <i class="fas fa-check-circle me-2"></i>Complete booking
                        </button>
                    </div>
                    <?php else: ?>
                    <button type="button" class="btn btn-outline-secondary me-3" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i>Back to Edit
                    </button>
                    <button type="button" class="btn btn-confirm" id="confirmBooking" disabled>
                        <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="termsModalLabel"><i class="fas fa-file-contract me-2"></i>Terms and Conditions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="terms-content mb-4" style="max-height: 400px; overflow-y: auto; padding-right: 15px;">
                        <h5 class="mb-3">Event Booking Terms & Conditions</h5>
                        <p class="text-muted">Last updated: <?php echo date('F j, Y'); ?></p>
                        
                        <h6 class="mt-4">1. Booking Confirmation</h6>
                        <p>Your booking is not confirmed until you receive a confirmation email from us. A 50% down payment is required to secure your booking.</p>
                        
                        <h6>2. Payment Terms</h6>
                        <p>All payments are in Philippine Peso (₱). The balance must be settled at least 7 days before the event date unless otherwise arranged.</p>
                        
                        <h6>3. Cancellation Policy</h6>
                        <p>
                            - Cancellations made 30+ days before the event: Full refund of deposit<br>
                            - Cancellations made 15-29 days before the event: 50% refund of deposit<br>
                            - Cancellations made within 14 days of the event: No refund
                        </p>
                        
                        <h6>4. Changes to Booking</h6>
                        <p>Changes to the event date are subject to availability. Any changes must be requested at least 14 days before the original event date.</p>
                        
                        <h6>5. Damages</h6>
                        <p>The client is responsible for any damages to the venue or equipment caused by the client or their guests during the event.</p>
                        
                        <h6>6. Force Majeure</h6>
                        <p>We are not liable for any failure or delay in performance due to events beyond our reasonable control, including but not limited to natural disasters, acts of government, or other emergencies.</p>
                        
                        <h6>7. Guest Count</h6>
                        <p>The final guest count must be confirmed at least 7 days before the event. Additional guests beyond the confirmed number may not be accommodated.</p>
                        
                        <h6>8. Outside Vendors</h6>
                        <p>Outside food and beverages are not permitted. All catering must be arranged through our approved vendors.</p>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I have read and agree to the terms and conditions stated above
                        </label>
                        <div class="invalid-feedback">
                            You must agree to the terms and conditions to proceed.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBookingBtn">
                        <i class="fas fa-check-circle me-2"></i>I Understand.
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Make PHP session data available to JavaScript
    window.bookingDataFromPHP = <?php 
        echo json_encode([
            'package_id' => $package_id,
            'package_name' => $package_name,
            'package_price' => $package_price,
            'event_place' => $event_place,
            'event_type' => $event_type,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'guest_count' => $guest_count,
            'payment_option' => $payment_option,
            'payment_method' => $payment_method,
            'total_amount' => $total_amount,
            'down_payment' => $down_payment,
            'balance' => $balance
        ]); 
    ?>;
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const completeBookingBtn = document.getElementById('completeBooking');
    const termsCheckbox = document.getElementById('termsCheckbox');

    if (completeBookingBtn) {
        // If payment is successful, enable and check the terms checkbox
        <?php if ($showSuccess): ?>
        if (termsCheckbox) {
            termsCheckbox.disabled = false;
            termsCheckbox.checked = true;
        }
        <?php endif; ?>
        
        // Remove any existing event listeners
        const newCompleteBtn = completeBookingBtn.cloneNode(true);
        completeBookingBtn.parentNode.replaceChild(newCompleteBtn, completeBookingBtn);
        
        newCompleteBtn.addEventListener('click', async function() {
            const originalBtnText = newCompleteBtn.innerHTML;
            newCompleteBtn.disabled = true;
            newCompleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';

            try {
                // Try to get booking data from sessionStorage or PHP session
                let bookingData = null;
                const storedData = sessionStorage.getItem('booking_data');
                
                if (storedData) {
                    try {
                        bookingData = JSON.parse(storedData);
                    } catch (e) {
                        console.error('Error parsing booking data:', e);
                    }
                }
                
                if (!bookingData && window.bookingDataFromPHP) {
                    bookingData = window.bookingDataFromPHP;
                }

                if (!bookingData) {
                    throw new Error('Booking data not found. Please start a new booking.');
                }

                console.log('Submitting booking data:', bookingData);

                const response = await fetch('event_insert_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ booking_data: bookingData })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Server response:', result);

                if (!result.success) {
                    throw new Error(result.message || 'Failed to save booking');
                }

                // Show success message with booking reference
                await Swal.fire({
                    title: 'Success!',
                    html: `Your booking has been successfully completed! The booking infomation has been sent to your email!.<br><br>
                          Booking Reference: <strong>${result.data?.booking_ref || 'N/A'}</strong>`,
                    icon: 'success',
                    confirmButtonColor: '#d4af37'
                });

                // Clear session and redirect
                sessionStorage.removeItem('booking_data');
                window.location.href = 'events.php?booking=completed&ref=' + (result.data?.booking_ref || '');

            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    title: 'Error',
                    text: error.message || 'Failed to process booking. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });

                // Reset button state
                newCompleteBtn.disabled = false;
                newCompleteBtn.innerHTML = originalBtnText;
            }
        });
    } else {
        console.error('Complete booking button not found');
    }
});

        // Show success message if payment was successful
        <?php if ($showSuccess): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-check the terms and conditions checkbox
            const termsCheckbox = document.getElementById('termsCheckbox');
            if (termsCheckbox) {
                termsCheckbox.checked = true;
            }
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful!',
                text: 'Your payment has been processed successfully.',
                showConfirmButton: false,
                timer: 3000
            });
        });
        <?php endif; ?>

        // Initialize modal
        const termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
        const termsCheckbox = document.getElementById('termsCheckbox');
        const confirmBtn = document.getElementById('confirmBooking');
        const showTermsLink = document.getElementById('showTermsLink');
        
        // Show terms modal when clicking the terms link
        showTermsLink.addEventListener('click', function(e) {
            e.preventDefault();
            termsModal.show();
        });
        
        // Enable terms checkbox when clicking the link
        showTermsLink.addEventListener('click', function(e) {
            e.preventDefault();
            termsCheckbox.disabled = false;
            termsModal.show();
        });
        
        // Toggle confirm button state based on terms checkbox
        termsCheckbox.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
        });
        
        // Handle form submission when clicking confirm button
        confirmBtn.addEventListener('click', async function() {
            // Show loading state
            const originalBtnText = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            try {
                // Prepare payment data
                const paymentData = {
                    amount: <?php echo $payment_option === 'full_payment' ? $total_amount * 100 : $down_payment * 100; ?>,
                    currency: 'PHP',
                    description: 'Event Booking - <?php echo addslashes($package_name); ?>',
                    success_url: window.location.href.split('?')[0] + '?payment=success',
                    cancel_url: window.location.href,
                    metadata: {
                        package_id: '<?php echo $package_id; ?>',
                        event_place: '<?php echo addslashes($event_place); ?>',
                        event_type: '<?php echo addslashes($event_type); ?>',
                        event_date: '<?php echo $event_date; ?>',
                        guest_count: <?php echo $guest_count; ?>
                    }
                };
                
                // Call the PayMongo endpoint
                const response = await fetch('event_paymongo_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(paymentData)
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.error);
                }
                
                if (result.checkout_url) {
                    // Redirect to PayMongo checkout
                    window.location.href = result.checkout_url;
                } else {
                    throw new Error('No checkout URL received');
                }
                
            } catch (error) {
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                alertDiv.role = 'alert';
                alertDiv.style.zIndex = '1100';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Payment Error:</strong> ${error.message || 'Failed to process payment'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.body.appendChild(alertDiv);
                
                // Auto-close alert after 5 seconds
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 150);
                }, 5000);
                
                // Reset button state
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalBtnText;
            }
        });
        
        // Handle form submission when confirming in modal
        document.getElementById('confirmBookingBtn').addEventListener('click', function() {
            const agreeCheckbox = document.getElementById('agreeTerms');
            
            if (!agreeCheckbox.checked) {
                agreeCheckbox.classList.add('is-invalid');
                return;
            }
            
            // Check the main terms checkbox and enable the confirm button
            termsCheckbox.checked = true;
            confirmBtn.disabled = false;
            
            // Close the modal and show success message
            termsModal.hide();
            
            // Scroll to the button and show focus state
            confirmBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                confirmBtn.focus();
            }, 500);
        });
        
        // Remove invalid state when user checks the box in modal
        document.getElementById('agreeTerms').addEventListener('change', function() {
            if (this.checked) {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>