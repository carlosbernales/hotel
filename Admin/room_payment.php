<?php
// Start session and include necessary files
session_start();
require_once 'db_con.php';
print_r($_GET);
// Get all URL parameters
$room_type_ids = isset($_GET['room_type_id']) ? (array)$_GET['room_type_id'] : [];
$room_types = isset($_GET['room_type']) ? (array)$_GET['room_type'] : [];
$check_in = isset($_GET['check_in']) ? htmlspecialchars($_GET['check_in']) : '';
$check_out = isset($_GET['check_out']) ? htmlspecialchars($_GET['check_out']) : '';
$num_nights = isset($_GET['num_nights']) ? intval($_GET['num_nights']) : 0;
$num_adults = isset($_GET['num_adults']) ? intval($_GET['num_adults']) : 0;
$num_children = isset($_GET['num_children']) ? intval($_GET['num_children']) : 0;
$payment_method = isset($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : '';
$payment_option = isset($_GET['payment_option']) ? htmlspecialchars($_GET['payment_option']) : '';
$total_amount = isset($_GET['total_amount']) ? floatval($_GET['total_amount']) : 0;
$payment_amount = isset($_GET['payment_amount']) ? floatval($_GET['payment_amount']) : 0;
$remaining_balance = isset($_GET['remaining_balance']) ? floatval($_GET['remaining_balance']) : 0;

// Fetch room prices from database
$room_prices = [];
$total_calculated = 0;

if (!empty($room_type_ids)) {
    $placeholders = str_repeat('?,', count($room_type_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT room_type_id, price FROM room_types WHERE room_type_id IN ($placeholders)");
    $stmt->execute($room_type_ids);
    $room_prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Calculate total based on actual prices if not provided in URL
    if (empty($total_amount)) {
        foreach ($room_type_ids as $id) {
            if (isset($room_prices[$id])) {
                $total_calculated += $room_prices[$id] * $num_nights;
            }
        }
        $total_amount = $total_calculated;
        // If payment amount is not provided, set it to the total amount
        if (empty($payment_amount)) {
            $payment_amount = $total_amount;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .booking-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .booking-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .booking-body {
            padding: 2rem;
        }
        .booking-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }
        .booking-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .room-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .price-highlight {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e59d9;
        }
        .payment-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="booking-container">
            <div class="booking-header">
                <h2><i class="fas fa-hotel me-2"></i>Booking Confirmation</h2>
                <p class="mb-0">Thank you for your reservation!</p>
            </div>
            
            <div class="booking-body">
                <!-- Booking Details -->
                <div class="booking-section">
                    <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Booking Details</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6>Check-in</h6>
                            <p class="text-muted"><?php echo date('F d, Y', strtotime($check_in)); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6>Check-out</h6>
                            <p class="text-muted"><?php echo date('F d, Y', strtotime($check_out)); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6>Number of Nights</h6>
                            <p class="text-muted"><?php echo $num_nights; ?> night<?php echo $num_nights > 1 ? 's' : ''; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6>Guests</h6>
                            <p class="text-muted">
                                <?php 
                                $guests = [];
                                if ($num_adults > 0) $guests[] = "$num_adults " . ($num_adults > 1 ? 'Adults' : 'Adult');
                                if ($num_children > 0) $guests[] = "$num_children " . ($num_children > 1 ? 'Children' : 'Child');
                                echo implode(', ', $guests);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Room Details -->
                <?php if (!empty($room_types)): ?>
                <div class="booking-section">
                    <h4 class="mb-4"><i class="fas fa-door-open me-2"></i>Room Details</h4>
                    <?php foreach ($room_types as $index => $room_type): ?>
                        <div class="room-item">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($room_type); ?></h5>
                                    <p class="text-muted mb-0">Room Type ID: <?php echo isset($room_type_ids[$index]) ? $room_type_ids[$index] : 'N/A'; ?></p>
                                </div>
                                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                    <span class="price-highlight">
                                        <?php 
                                        
                                        $room_id = $room_type_ids[$index];
                                        $price = isset($room_prices[$room_id]) ? $room_prices[$room_id] : 0;
                                        echo '₱' . number_format($price, 2);
                                        ?>
                                    
                                    </span>
                                    <p class="text-muted mb-0">per night</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Payment Summary -->
                <div class="booking-section">
                    <h4 class="mb-4"><i class="fas fa-credit-card me-2"></i>Payment Summary</h4>
                    <div class="payment-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₱<?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Payment Option:</span>
                            <span class="fw-bold"><?php echo $payment_option; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Payment Method:</span>
                            <span class="text-capitalize"><?php echo $payment_method; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Amount Paid:</span>
                            <span class="fw-bold">₱<?php echo number_format($payment_amount, 2); ?></span>
                        </div>
                        <?php if ($remaining_balance > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Remaining Balance:</span>
                            <span class="text-danger fw-bold">₱<?php echo number_format($remaining_balance, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between border-top pt-3 mt-3">
                            <h5>Total Amount:</h5>
                            <h4 class="text-primary">₱<?php echo number_format($total_amount, 2); ?></h4>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="text-center mt-4">
                    <span class="status-badge status-pending">
                        <i class="fas fa-clock me-1"></i> Pending Confirmation
                    </span>
                    <p class="text-muted mt-3">
                        Your booking is being processed. You will receive a confirmation email shortly.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
