
<?php
session_start();
require_once 'db_con.php';

// If coming back from payment success, get data from session
if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
    // Check if we have booking data in the session
    if (!empty($_SESSION['booking_data'])) {
        $bookingData = $_SESSION['booking_data'];
        
        // Store in a separate session variable to persist across reloads
        $_SESSION['completed_booking'] = $bookingData;
    } 
    // If no booking_data but we have completed_booking, use that
    else if (!empty($_SESSION['completed_booking'])) {
        $bookingData = $_SESSION['completed_booking'];
    }
    
    // Set variables from booking data if it exists
    if (isset($bookingData)) {
        $tables = $bookingData['tables'] ?? [];
        $date = $bookingData['date'] ?? '';
        $time = $bookingData['time'] ?? '';
        $order = $bookingData['order'] ?? [];
        $payment_method = $bookingData['payment_method'] ?? '';
        $payment_option = $bookingData['payment_option'] ?? '';
        $payment_amount = $bookingData['payment_amount'] ?? 0;
        $remaining_amount = $bookingData['remaining_amount'] ?? 0;
        $total_amount = $bookingData['total_amount'] ?? 0;
    }
} else {
    // Get data from GET parameters and store in session
    $tables = isset($_GET['tables']) ? json_decode(urldecode($_GET['tables']), true) : [];
    $date = $_GET['date'] ?? '';
    $time = $_GET['time'] ?? '';
    $order = isset($_GET['order']) ? json_decode(urldecode($_GET['order']), true) : [];
    $payment_method = $_GET['payment_method'] ?? '';
    $payment_option = $_GET['payment_option'] ?? '';
    $payment_amount = $_GET['payment_amount'] ?? 0;
    $remaining_amount = $_GET['remaining_amount'] ?? 0;
    $total_amount = $_GET['total_amount'] ?? 0;
    
    // Store in session for payment success redirect
    $bookingData = [
        'tables' => $tables,
        'date' => $date,
        'time' => $time,
        'order' => $order,
        'payment_method' => $payment_method,
        'payment_option' => $payment_option,
        'payment_amount' => $payment_amount,
        'remaining_amount' => $remaining_amount,
        'total_amount' => $total_amount,
        'timestamp' => time()
    ];
    
    $_SESSION['booking_data'] = $bookingData;
    
    // Clear any old completed booking data when starting a new booking
    if (isset($_SESSION['completed_booking'])) {
        unset($_SESSION['completed_booking']);
    }
}

// Calculate subtotal from order items
$subtotal = 0;
if (!empty($order)) {
    foreach ($order as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $itemTotal += $addon['price'] * $item['quantity'];
            }
        }
        $subtotal += $itemTotal;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Booking Confirmation</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #b6860a;
            --primary-dark: #8a6708;
            --primary-light: #f8f3e6;
            --text: #2d3436;
            --text-light: #636e72;
            --border: #e0e0e0;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            color: var(--text);
            line-height: 1.6;
        }

        .booking-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: var(--transition);
        }

        .booking-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .booking-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }

        .booking-header h2 {
            font-weight: 700;
            font-size: 2rem;
            margin: 0 0 0.5rem;
            position: relative;
        }

        .booking-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            margin: 0;
        }

        .booking-body {
            padding: 2.5rem;
        }

        .section-title {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.25rem;
            margin: 2.5rem 0 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            transition: var(--transition);
            background: var(--white);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1.75rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 0.25rem;
            display: block;
        }

        .info-value {
            font-weight: 500;
            color: var(--text);
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }

        .item-quantity {
            color: var(--text-light);
            margin-left: 0.5rem;
            font-size: 0.9em;
        }

        .addon-item {
            font-size: 0.9em;
            color: var(--text-light);
            margin-top: 0.25rem;
            padding-left: 1.25rem;
            position: relative;
        }

        .addon-item::before {
            content: '•';
            position: absolute;
            left: 0.5rem;
            color: var(--primary);
        }

        .item-price {
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            margin-left: 1rem;
        }

        .payment-summary {
            background: var(--primary-light);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .amount-label {
            color: var(--text-light);
        }

        .amount-value {
            font-weight: 500;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 1.5rem 0 0;
            padding-top: 1rem;
            border-top: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-pay-now {
            background: var(--primary);
            color: white;
            font-weight: 600;
            padding: 0.9rem 2.5rem;
            border: none;
            border-radius: 8px;
            transition: var(--transition);
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(182, 134, 10, 0.2);
        }

        .btn-pay-now:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(182, 134, 10, 0.3);
        }

        .btn-pay-now i {
            margin-right: 0.75rem;
            font-size: 1.1em;
        }

        .btn-outline-secondary {
            border: 2px solid var(--border);
            background: transparent;
            color: var(--text);
            font-weight: 500;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .btn-outline-secondary:hover {
            background: var(--bg-light);
            border-color: var(--text-light);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .booking-body {
                padding: 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .section-title {
                font-size: 1.1rem;
                margin: 2rem 0 1.25rem;
            }
            
            .btn-pay-now, .btn-outline-secondary {
                width: 100%;
                margin-bottom: 0.75rem;
            }
            
            .d-md-flex {
                flex-direction: column;
            }
            
            .me-md-2 {
                margin-right: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="booking-container">
            <div class="booking-header">
                <h2><i class="fas fa-check-circle me-2"></i>Booking Confirmation</h2>
                <p class="mb-0">Please review your booking details below</p>
            </div>
            
            <div class="booking-body">
                <!-- Table Booking Details -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="section-title"><i class="fas fa-utensils"></i>Table Booking Details</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Booking Date</span>
                                <div class="info-value">
                                    <i class="far fa-calendar-alt me-2 text-muted"></i>
                                    <?php echo date('F j, Y', strtotime($date)); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Time</span>
                                <div class="info-value">
                                    <i class="far fa-clock me-2 text-muted"></i>
                                    <?php echo date('h:i A', strtotime($time)); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tables Reserved</span>
                                <div class="info-value">
                                    <i class="fas fa-chair me-2 text-muted"></i>
                                    <?php 
                                    $tableNames = array_map(function($table) {
                                        return $table['name'] . ' (' . $table['capacity'] . ')';
                                    }, $tables);
                                    echo implode(', ', $tableNames);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <?php if (!empty($order)): ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="section-title"><i class="fas fa-receipt"></i>Order Summary</h4>
                        <div class="order-items">
                            <?php foreach ($order as $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                                $hasAddons = !empty($item['addons']);
                                if ($hasAddons) {
                                    foreach ($item['addons'] as $addon) {
                                        $itemTotal += $addon['price'] * $item['quantity'];
                                    }
                                }
                            ?>
                                <div class="item-row">
                                    <div class="item-details">
                                        <div class="item-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                                        </div>
                                        <?php if ($hasAddons): ?>
                                            <?php foreach ($item['addons'] as $addon): ?>
                                                <div class="addon-item">
                                                    <?php echo htmlspecialchars($addon['name']); ?>
                                                    <small>(+₱<?php echo number_format($addon['price'], 2); ?>)</small>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-price">
                                        ₱<?php echo number_format($itemTotal, 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment Information -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="section-title mb-0"><i class="fas fa-credit-card"></i> Payment Information</h4>
                            <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Paid</span>
                            <?php endif; ?>
                        </div>
                        <div class="info-grid mb-4">
                            <div class="info-item">
                                <span class="info-label">Payment Method</span>
                                <div class="info-value">
                                    <i class="fas fa-<?php echo $payment_method === 'online' ? 'credit-card' : 'money-bill-wave'; ?> me-2 text-muted"></i>
                                    <?php echo ucfirst($payment_method); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Payment Option</span>
                                <div class="info-value">
                                    <i class="fas fa-<?php echo $payment_option === 'full' ? 'check-circle' : 'money-bill-wave'; ?> me-2 text-muted"></i>
                                    <?php echo ucfirst($payment_option); ?> Payment
                                </div>
                            </div>
                        </div>

                        <div class="payment-summary">
                            <div class="amount-row">
                                <span class="amount-label">Subtotal</span>
                                <span class="amount-value">₱<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <?php if ($payment_option === 'partial'): ?>
                                <div class="amount-row">
                                    <span class="amount-label"> Downpayment (50%)</span>
                                    <span class="amount-value">₱<?php echo number_format($payment_amount, 2); ?></span>
                                </div>
                                <div class="amount-row">
                                    <span class="amount-label">Remaining Balance</span>
                                    <span class="amount-value">₱<?php echo number_format($remaining_amount, 2); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($payment_option === 'custom'): ?>
                                <div class="amount-row">
                                    <span class="amount-label">Amount Paid</span>
                                    <div class="d-flex align-items-center">
                                        <span class="me-1">₱</span>
                                        <span id="customAmountPaid"><?php echo number_format($payment_amount, 2, '.', ''); ?></span>
                                    </div>
                                </div>
                                <div class="amount-row">
                                    <span class="amount-label">Remaining Balance</span>
                                    <span class="amount-value" id="remainingBalance">₱<?php echo number_format($remaining_amount, 2); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="total-amount">
                                <span>Total Amount</span>
                                <span>₱<?php echo number_format($total_amount, 2); ?></span>
                            </div>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-end mt-4">
                            <a href="table.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Booking
                            </a>
                            <button type="button" class="btn btn-pay-now" id="proceedToPayment">
                                <i class="fas fa-credit-card"></i><?php echo (isset($_GET['payment']) && $_GET['payment'] === 'success') ? 'Complete Booking' : 'Proceed to Payment'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Show payment success alert if coming from successful payment
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful!',
                text: 'Your payment has been processed successfully. Thank you for your booking!',
                confirmButtonColor: '#b6860a',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: true
            });
        });
        <?php endif; ?>
        // Function to calculate remaining balance
        function updateRemainingBalance() {
            const totalAmount = parseFloat('<?php echo $total_amount; ?>');
            const amountPaidText = document.getElementById('customAmountPaid').textContent.replace(/[^0-9.-]+/g,"");
            const amountPaid = parseFloat(amountPaidText) || 0;
            const remaining = totalAmount - amountPaid;
            
            document.getElementById('remainingBalance').textContent = '₱' + remaining.toFixed(2);
            
            // Update the payment amount in the URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('payment_amount', amountPaid.toFixed(2));
            urlParams.set('remaining_amount', remaining.toFixed(2));
            
            // Update URL without page reload
            const newUrl = window.location.pathname + '?' + urlParams.toString();
            window.history.replaceState({}, '', newUrl);
            
            return { amountPaid, remaining };
        }
        
        // No longer need input event listener as amount is now static
        
        document.getElementById('proceedToPayment').addEventListener('click', async function() {
            const button = this;
            const isCompleteBooking = button.textContent.trim() === 'Complete Booking';
            const paymentOption = '<?php echo $payment_option; ?>';
            
            // For custom payment, validate the amount
            if (paymentOption === 'custom') {
                const totalAmount = parseFloat('<?php echo $total_amount; ?>');
                const amountPaidText = document.getElementById('customAmountPaid').textContent.replace(/[^0-9.-]+/g,"");
                const amountPaid = parseFloat(amountPaidText) || 0;
                
                if (isNaN(amountPaid) || amountPaid <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Please enter a valid payment amount',
                        confirmButtonColor: '#b6860a'
                    });
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-credit-card"></i>Proceed to Payment';
                    return;
                }
                
                if (amountPaid > totalAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Amount Exceeds Total',
                        text: 'Payment amount cannot be greater than the total amount',
                        confirmButtonColor: '#b6860a'
                    });
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-credit-card"></i>Proceed to Payment';
                    return;
                }
            }
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';

            try {
                // If it's a Complete Booking action
                if (isCompleteBooking) {
                    const response = await fetch('table_finish_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            tables: <?php echo json_encode($tables); ?>,
                            date: '<?php echo $date; ?>',
                            time: '<?php echo $time; ?>',
                            order: <?php echo json_encode($order); ?>,
                            total_amount: <?php echo $total_amount; ?>,
                            payment_amount: <?php echo $payment_amount; ?>,
                            remaining_amount: <?php echo $remaining_amount; ?>,
                            payment_method: '<?php echo $payment_method; ?>',
                            payment_option: '<?php echo $payment_option; ?>',
                            firstname: '<?php echo $_SESSION['firstname'] ?? ''; ?>',
                            lastname: '<?php echo $_SESSION['lastname'] ?? ''; ?>',
                            contact: '<?php echo $_SESSION['contact'] ?? ''; ?>',
                            email: '<?php echo $_SESSION['email'] ?? ''; ?>'
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Get table numbers from the tables array
                        const tableNumbers = <?php echo json_encode(array_map(function($table) {
                            return $table['number'] ?? $table['name'] ?? 'Table';
                        }, $tables)); ?>;
                        const tableText = tableNumbers.length > 0 
                            ? `Table${tableNumbers.length > 1 ? 's' : ''} ${tableNumbers.join(', ')}` 
                            : 'Your table';
                            
                        // Show success message with table numbers
                        await Swal.fire({
                            icon: 'success',
                            title: 'Booking Successful!',
                            html: `
                                <div style="text-align: center;">
                                    <p>${tableText} has been booked successfully!</p>
                                    <p style="margin-top: 10px; font-weight: 600;">
                                        <i class="fas fa-table me-2"></i>${tableText}
                                    </p>
                                    <p style="margin-top: 10px; color: #666; font-size: 0.9em;">
                                        Thank you for your booking!
                                    </p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#b6860a',
                            timer: 5000,
                            timerProgressBar: true
                        });
                        
                        // Clear the session data
                        await fetch('clear_booking_session.php', { 
                            method: 'POST',
                            credentials: 'same-origin'
                        });
                        
                        // Redirect to success page after a short delay
                        setTimeout(() => {
                            window.location.href = 'table.php?order_id=' + result.order_id;
                        }, 1000);
                    } else {
                        throw new Error(result.message || 'Failed to complete booking');
                    }
                    return;
                }
                // Get the current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const tables = urlParams.get('tables') || '';
                const date = urlParams.get('date') || '';
                const time = urlParams.get('time') || '';
                const order = urlParams.get('order') ? JSON.parse(decodeURIComponent(urlParams.get('order'))) : [];
                const paymentMethod = urlParams.get('payment_method') || 'online';
                const paymentOption = urlParams.get('payment_option') || 'full';
                // Use appropriate amount based on payment option
                let paymentAmount;
                if (paymentOption === 'custom') {
                    const amountPaidText = document.getElementById('customAmountPaid').textContent.replace(/[^0-9.-]+/g,"");
                    paymentAmount = parseFloat(amountPaidText) || 0;
                } else if (paymentOption === 'partial') {
                    paymentAmount = parseFloat('<?php echo $payment_amount; ?>');
                } else {
                    paymentAmount = parseFloat('<?php echo $total_amount; ?>');
                }
                
                // Prepare order items description
                let description = 'Table Booking - ';
                if (order.length > 0) {
                    const itemNames = order.map(item => `${item.name} x${item.quantity}`);
                    description += itemNames.join(', ');
                } else {
                    description += 'Table Reservation';
                }

                // Prepare the request data
                const requestData = {
                    amount: paymentAmount * 100, // Convert to centavos
                    description: description,
                    reference_number: 'BOOK-' + Date.now(),
                    metadata: {
                        tables: tables,
                        date: date,
                        time: time,
                        payment_method: paymentMethod,
                        payment_option: paymentOption,
                        order: JSON.stringify(order)
                    },
                    success_url: window.location.href.split('?')[0] + '?payment=success&ref=' + 'BOOK-' + Date.now(),
                    cancel_url: window.location.href.split('?')[0] + '?payment=cancelled'
                };

                // Call the Paymongo checkout endpoint
                const response = await fetch('table_paymongo_checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();

                if (response.ok && data.checkout_url) {
                    // Redirect to Paymongo checkout
                    window.location.href = data.checkout_url;
                } else {
                    throw new Error(data.error || 'Failed to process payment');
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Error processing payment: ' + error.message);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-credit-card"></i>Proceed to Payment';
            }
        });
    </script>
</body>
</html>
