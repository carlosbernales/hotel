<?php
session_start();
require 'db_con.php';

if (!isset($_GET['id'])) {
    header('Location: mybookings.php');
    exit();
}

$bookingId = $_GET['id'];
$userId = $_SESSION['user_id'];

try {
    // Get booking details with order information
    $stmt = $pdo->prepare("
        SELECT 
            tb.*,
            CONCAT(u.firstname, ' ', u.lastname) as user_name,
            u.email,
            o.order_type,
            o.total_amount,
            o.amount_paid,
            o.remaining_balance,
            o.payment_method,
            o.payment_reference,
            o.payment_proof,
            o.status as order_status,
            o.payment_status
        FROM table_bookings tb
        LEFT JOIN users u ON tb.user_id = u.id
        LEFT JOIN orders o ON tb.id = o.table_id
        WHERE tb.id = ? AND tb.user_id = ?
    ");
    $stmt->execute([$bookingId, $userId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        header('Location: mybookings.php');
        exit();
    }

    // Get order items
    $itemStmt = $pdo->prepare("
        SELECT 
            oi.*,
            GROUP_CONCAT(oia.addon_name) as addons,
            GROUP_CONCAT(oia.addon_price) as addon_prices
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
        WHERE o.table_id = ? AND o.order_type = 'advance'
        GROUP BY oi.id
    ");
    $itemStmt->execute([$bookingId]);
    $orderItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle error
    header('Location: mybookings.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservation Details - E Akomoda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .booking-details {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }

        .booking-header {
            background: linear-gradient(to right, rgba(255, 193, 7, 0.1), transparent);
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
            border-left: 4px solid #ffc107;
        }

        .booking-body {
            padding: 1.5rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
        }

        .status-badge {
            padding: 0.5em 1em;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .order-items {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .payment-proof {
            max-width: 300px;
            border-radius: 10px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="container">
        <div class="booking-details">
            <div class="booking-header d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="mb-1">Table Reservation #<?php echo $booking['id']; ?></h4>
                    <p class="text-muted mb-0">
                        Reserved on <?php echo date('F d, Y', strtotime($booking['created_at'])); ?>
                    </p>
                </div>
                <span class="status-badge bg-<?php 
                    echo match($booking['status']) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    };
                ?>">
                    <?php echo ucfirst($booking['status']); ?>
                </span>
            </div>

            <div class="booking-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <h5 class="mb-3">Reservation Details</h5>
                            <div class="mb-2">
                                <div class="info-label">Date</div>
                                <div class="info-value">
                                    <?php echo date('F d, Y', strtotime($booking['booking_date'])); ?>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">Time</div>
                                <div class="info-value">
                                    <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">Number of Guests</div>
                                <div class="info-value"><?php echo $booking['num_guests']; ?></div>
                            </div>
                            <?php if (!empty($booking['special_requests'])): ?>
                                <div class="mb-2">
                                    <div class="info-label">Special Requests</div>
                                    <div class="info-value"><?php echo htmlspecialchars($booking['special_requests']); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="info-group">
                            <h5 class="mb-3">Contact Information</h5>
                            <div class="mb-2">
                                <div class="info-label">Booked by</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['user_name']); ?></div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['contact_number']); ?></div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['email']); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-group">
                            <h5 class="mb-3">Order Details</h5>
                            <?php if (!empty($orderItems)): ?>
                                <div class="order-items">
                                    <?php foreach($orderItems as $item): ?>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo htmlspecialchars($item['item_name']); ?> x <?php echo $item['quantity']; ?></span>
                                                <span>₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></span>
                                            </div>
                                            <?php if(!empty($item['addons'])): ?>
                                                <?php 
                                                $addons = explode(',', $item['addons']);
                                                $addonPrices = explode(',', $item['addon_prices']);
                                                foreach($addons as $index => $addon): 
                                                ?>
                                                    <div class="small text-muted ms-3">
                                                        + <?php echo htmlspecialchars($addon); ?> 
                                                        (₱<?php echo number_format($addonPrices[$index], 2); ?>)
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="mt-3 pt-2 border-top">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Amount:</strong>
                                            <strong>₱<?php echo number_format($booking['total_amount'], 2); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No order items found</p>
                            <?php endif; ?>
                        </div>

                        <div class="info-group">
                            <h5 class="mb-3">Payment Information</h5>
                            <div class="mb-2">
                                <div class="info-label">Payment Method</div>
                                <div class="info-value"><?php echo strtoupper($booking['payment_method'] ?? 'Not specified'); ?></div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label">Payment Status</div>
                                <div class="info-value">
                                    <span class="badge bg-<?php 
                                        echo match($booking['payment_status']) {
                                            'Paid' => 'success',
                                            'Partially Paid' => 'warning',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo $booking['payment_status']; ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (!empty($booking['payment_reference'])): ?>
                                <div class="mb-2">
                                    <div class="info-label">Reference Number</div>
                                    <div class="info-value"><?php echo htmlspecialchars($booking['payment_reference']); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($booking['payment_proof'])): ?>
                                <div class="mb-2">
                                    <div class="info-label">Payment Proof</div>
                                    <img src="<?php echo htmlspecialchars($booking['payment_proof']); ?>" 
                                         alt="Payment Proof" 
                                         class="payment-proof">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <a href="mybookings.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to My Bookings
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 