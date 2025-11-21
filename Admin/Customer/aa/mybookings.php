<?php 
session_start();
require 'db_con.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ffc107;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #ffdb4d);
            padding: 2rem 0;
            margin-bottom: 2rem;
            margin-top: 55px;
            text-align: center;
            color: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .booking-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .booking-header {
            background: linear-gradient(to right, #f8f9fa, #fff);
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .booking-body {
            padding: 1.5rem;
        }

        .booking-footer {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            border-top: 1px solid #eee;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background-color: var(--warning-color);
            color: #000;
        }

        .status-confirmed {
            background-color: var(--success-color);
            color: #fff;
        }

        .status-cancelled {
            background-color: var(--danger-color);
            color: #fff;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .booking-info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .booking-info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
            color: #333;
        }

        .guest-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .guest-item {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .price-info {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .countdown-timer {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .booking-card {
                margin: 1rem 0;
            }
            
            .booking-header {
                padding: 1rem;
            }
            
            .booking-body {
                padding: 1rem;
            }
        }

        .table-booking {
            border-left: 4px solid #ffc107;
        }

        .table-booking .booking-header {
            background: linear-gradient(to right, rgba(255, 193, 7, 0.1), transparent);
        }

        .table-booking .status-badge {
            font-size: 0.8rem;
            padding: 0.5em 1em;
            border-radius: 50px;
        }

        .table-booking .guest-list {
            max-height: 150px;
            overflow-y: auto;
        }

        .booking-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem;
            background: linear-gradient(to right, rgba(255, 193, 7, 0.1), transparent);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .section-header:hover {
            background: linear-gradient(to right, rgba(255, 193, 7, 0.2), transparent);
        }

        .section-content {
            padding: 0 1.5rem 1.5rem;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
            color: #b6860a;
        }

        .collapsed .toggle-icon {
            transform: rotate(-90deg);
        }

        .badge {
            padding: 0.5em 1em;
            font-size: 0.875rem;
        }

        /* Animate collapse */
        .collapse {
            transition: all 0.3s ease;
        }

        .collapse:not(.show) {
            display: none;
        }

        .collapsing {
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease;
        }

        .filter-empty-state {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            display: none;
        }

        .filter-empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            border-radius: 15px;
        }

        .form-select, .form-control {
            border-radius: 25px;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .booking-details {
            padding: 1rem;
        }

        .booking-details .info-group label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .booking-details .info-group p {
            font-size: 1rem;
            font-weight: 500;
        }

        .modal-lg {
            max-width: 800px;
        }

        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        .booking-details h6 {
            color: #ffc107;
            margin-bottom: 15px;
        }
        .booking-details p {
            margin-bottom: 8px;
        }
        .booking-details .badge {
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .payment-proof-img {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 5px;
            transition: transform 0.2s;
        }
        
        .payment-proof-img:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php include 'message_box.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1 class="mb-0">My Bookings</h1>
            <p class="mb-0">Manage your room reservations and bookings</p>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filter Bookings</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="bookingTypeFilter">
                                    <option value="">All Types</option>
                                    <option value="table">Table Reservations</option>
                                    <option value="room">Room Bookings</option>
                                    <option value="event">Event Bookings</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-warning w-100" onclick="resetFilters()">
                                    <i class="fas fa-undo-alt"></i> Reset Filters
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-secondary w-100" onclick="showBookingHistory()">
                                    <i class="fas fa-history"></i> Booking History
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12 table-reservations">
                <h4 class="text-warning mb-3">
                    <i class="fas fa-utensils me-2"></i>Table Reservations
                </h4>
                <?php
                try {
                    $tableStmt = $pdo->prepare("
                        SELECT 
                            tb.*,
                            CONCAT(u.first_name, ' ', u.last_name) as user_name,
                            u.email,
                            tb.contact_number,
                            tb.email_address,
                            tb.booking_date as reservation_date,
                            tb.booking_time as start_time,
                            tb.booking_time as end_time,
                            tb.num_guests as guest_count,
                            COALESCE(o.payment_method, tb.payment_method) as payment_method,
                            COALESCE(o.total_amount, tb.total_amount) as total_amount,
                            COALESCE(o.amount_paid, tb.amount_paid) as amount_paid,
                            o.remaining_balance,
                            COALESCE(o.status, tb.status) as status,
                            o.order_type,
                            o.id as order_id,
                            tb.created_at as booking_date
                        FROM table_bookings tb
                        LEFT JOIN userss u ON tb.user_id = u.id
                        LEFT JOIN orders o ON tb.id = o.table_id 
                        WHERE tb.user_id = :user_id 
                        AND (o.order_type = 'advance' OR o.order_type IS NULL)
                        ORDER BY tb.created_at DESC
                    ");
                    
                    $tableStmt->execute([':user_id' => $_SESSION['user_id']]);
                    $tableBookings = $tableStmt->fetchAll(PDO::FETCH_ASSOC);

                    // After fetching the table bookings, get order items for each advance order
                    foreach($tableBookings as &$booking) {
                        if(isset($booking['order_id'])) {
                            // Get order items
                            $itemStmt = $pdo->prepare("
                                SELECT 
                                    oi.*,
                                    GROUP_CONCAT(oia.addon_name) as addons,
                                    GROUP_CONCAT(oia.addon_price) as addon_prices
                                FROM order_items oi
                                LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
                                WHERE oi.order_id = :order_id
                                GROUP BY oi.id
                            ");
                            $itemStmt->execute([':order_id' => $booking['order_id']]);
                            $booking['order_items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                    }

                    if (empty($tableBookings)) {
                        echo '
                        <div class="empty-state">
                            <i class="fas fa-utensils"></i>
                            <h3>No Table Reservations</h3>
                            <p class="text-muted">You haven\'t made any table reservations yet.</p>
                            <a href="table.php" class="btn btn-warning btn-action mt-3">
                                <i class="fas fa-plus"></i> Reserve a Table
                            </a>
                        </div>';
                    } else {
                        foreach($tableBookings as $booking):
                            $statusClass = match($booking['status']) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'cancelled' => 'status-cancelled',
                                default => ''
                            };
                    ?>
                            <div class="booking-card table-booking">
                                <div class="booking-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">Table Reservation #<?php echo $booking['id']; ?></h5>
                                        <small class="text-muted" data-reservation-date>
                                            Reserved on <?php echo date('F d, Y', strtotime($booking['booking_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge <?php echo $statusClass; ?>" data-status>
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="booking-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="booking-info-item">
                                                <div class="info-label">Package Details</div>
                                                <div class="info-value">
                                                    <i class="fas fa-utensils me-2"></i>
                                                    <?php echo htmlspecialchars($booking['package_name'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <?php 
                                                    $reservationDate = isset($booking['reservation_date']) ? 
                                                        date('F d, Y', strtotime($booking['reservation_date'])) : 
                                                        'Date not set';
                                                    echo $reservationDate; 
                                                    ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <?php 
                                                    $startTime = isset($booking['start_time']) ? 
                                                        date('g:i A', strtotime($booking['start_time'])) : 
                                                        'Time not set';
                                                    $endTime = isset($booking['end_time']) ? 
                                                        date('g:i A', strtotime($booking['end_time'])) : 
                                                        'Time not set';
                                                    echo $startTime . ' - ' . $endTime; 
                                                    ?>
                                                </div>
                                            </div>

                                            <div class="booking-info-item">
                                                <div class="info-label">Guest Information</div>
                                                <div class="info-value">
                                                    <i class="fas fa-users me-2"></i>
                                                    <?php echo isset($booking['guest_count']) ? $booking['guest_count'] : '0'; ?> Guests
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-user me-2"></i>
                                                    Booked by: <?php echo htmlspecialchars($booking['user_name'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-phone me-2"></i>
                                                    Contact: <?php echo htmlspecialchars($booking['contact_number'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-envelope me-2"></i>
                                                    Email: <?php echo htmlspecialchars($booking['email_address'] ?? $booking['email'] ?? 'N/A'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="booking-info-item">
                                                <div class="info-label">Order Details</div>
                                                <?php if(!empty($booking['order_items'])): ?>
                                                    <?php foreach($booking['order_items'] as $item): ?>
                                                        <div class="order-item">
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
                                                                    <div class="addon-item small text-muted">
                                                                        + <?php echo htmlspecialchars($addon); ?> 
                                                                        (₱<?php echo number_format($addonPrices[$index], 2); ?>)
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted">No order items found</p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="payment-details">
                                                <div class="payment-status">
                                                    <?php if ($booking['payment_option'] === 'Fully Paid'): ?>
                                                        <span class="badge bg-success">Fully Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Partial Payment</span>
                                                        <?php if ($booking['remaining_balance'] > 0 && $booking['status'] !== 'cancelled'): ?>
                                                            <button class="btn btn-warning btn-sm mt-2" 
                                                                    onclick="showPaymentModal(<?php echo $booking['booking_id']; ?>, <?php echo $booking['remaining_balance']; ?>)">
                                                                <i class="fas fa-credit-card me-1"></i> Pay Balance
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="booking-footer d-flex justify-content-between align-items-center">
                                    <div class="countdown-timer">
                                        <?php 
                                        if (isset($booking['reservation_date'])) {
                                            $reservationDate = new DateTime($booking['reservation_date']);
                                            $now = new DateTime();
                                            $daysUntil = $now->diff($reservationDate)->days;
                                            
                                            if ($daysUntil > 0) {
                                                echo "<i class='fas fa-clock me-1'></i> {$daysUntil} days until reservation";
                                            }
                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-action" 
                                                onclick="showBookingDetails(<?php echo htmlspecialchars(json_encode($booking), ENT_QUOTES, 'UTF-8'); ?>)">
                                            <i class="fas fa-info-circle me-1"></i> Check Details
                                        </button>
                                        
                                        <?php if(strtolower($booking['status']) === 'pending'): ?>
                                            <button class="btn btn-danger btn-action" 
                                                    onclick="cancelTableBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endforeach;
                    }
                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Add Event Bookings Section -->
        <div class="row mb-4">
            <div class="col-12 event-bookings">
                <h4 class="text-warning mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>Event Bookings
                </h4>
                <?php
                try {
                    $eventStmt = $pdo->prepare("
                        SELECT 
                            eb.*,
                            CONCAT(u.first_name, ' ', u.last_name) as user_name,
                            u.email,
                            u.contact_number as user_contact
                        FROM event_bookings eb
                        LEFT JOIN userss u ON eb.user_id = u.id
                        WHERE eb.user_id = :user_id 
                        AND eb.booking_status = 'pending'
                        ORDER BY eb.created_at DESC
                    ");
                    
                    $eventStmt->execute([':user_id' => $_SESSION['user_id']]);
                    $eventBookings = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($eventBookings)) {
                        echo '
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Pending Event Bookings</h3>
                            <p class="text-muted">You don\'t have any pending event bookings at the moment.</p>
                            <a href="events.php" class="btn btn-warning btn-action mt-3">
                                <i class="fas fa-plus"></i> Book an Event
                            </a>
                        </div>';
                    } else {
                        foreach($eventBookings as $booking):
                            $statusClass = match($booking['booking_status']) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'cancelled' => 'status-cancelled',
                                default => ''
                            };
                        ?>
                            <div class="booking-card event-booking">
                                <div class="booking-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">Event Booking #<?php echo $booking['id']; ?></h5>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            Booked on <?php echo date('F d, Y', strtotime($booking['created_at'])); ?>
                                        </small>
                                    </div>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($booking['booking_status']); ?>
                                    </span>
                                </div>
                                
                                <div class="booking-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="booking-info-item">
                                                <div class="info-label">Event Details</div>
                                                <div class="info-value">
                                                    <i class="fas fa-calendar-day me-2"></i>
                                                    <?php echo date('F d, Y', strtotime($booking['reservation_date'])); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <?php echo date('g:i A', strtotime($booking['start_time'])); ?> - 
                                                    <?php echo date('g:i A', strtotime($booking['end_time'])); ?>
                                                </div>
                                            </div>

                                            <div class="booking-info-item">
                                                <div class="info-label">Event Information</div>
                                                <div class="info-value">
                                                    <i class="fas fa-calendar-check me-2"></i>
                                                    <?php echo htmlspecialchars($booking['event_type']); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-users me-2"></i>
                                                    <?php echo $booking['number_of_guests']; ?> Guests
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="booking-info-item">
                                                <div class="info-label">Payment Details</div>
                                                <div class="info-value">
                                                    <i class="fas fa-credit-card me-2"></i>
                                                    <?php echo strtoupper($booking['payment_method']); ?>
                                                </div>
                                                <div class="price-info mt-2">
                                                    ₱<?php echo number_format($booking['total_amount'], 2); ?>
                                                </div>
                                                
                                                <?php if($booking['paid_amount'] < $booking['total_amount']): ?>
                                                    <div class="mt-2">
                                                        <div class="text-success">
                                                            Paid: ₱<?php echo number_format($booking['paid_amount'], 2); ?>
                                                        </div>
                                                        <div class="text-danger">
                                                            Balance: ₱<?php echo number_format($booking['remaining_balance'], 2); ?>
                                                        </div>
                                                        <?php if($booking['booking_status'] !== 'cancelled'): ?>
                                                            
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="booking-footer d-flex justify-content-between align-items-center">
                                    <div class="countdown-timer">
                                        <?php 
                                        $eventDate = new DateTime($booking['reservation_date']);
                                        $now = new DateTime();
                                        $daysUntil = $now->diff($eventDate)->days;
                                        
                                        if ($daysUntil > 0) {
                                            echo "<i class='fas fa-clock me-1'></i> {$daysUntil} days until event";
                                        }
                                        ?>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <?php if($booking['booking_status'] === 'pending'): ?>
                                            <button class="btn btn-danger btn-action" 
                                                    onclick="cancelEventBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        endforeach;
                    }
                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Room Bookings Section -->
        <div class="row">
            <div class="col-12 room-bookings">
                <h4 class="text-warning mb-3">
                    <i class="fas fa-bed me-2"></i>Room Bookings
                </h4>
                <?php
                try {
                    // Modify the booking query to use correct column names
                    $stmt = $pdo->prepare("
                        SELECT b.*, rt.room_type, rt.price as room_price,
                               b.downpayment_amount,
                               b.remaining_balance,
                               b.payment_option,
                               b.payment_method,
                               b.total_amount
                        FROM bookings b 
                        LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
                        WHERE b.user_id = :user_id 
                        ORDER BY b.created_at DESC
                    ");
                    
                    $stmt->execute([':user_id' => $_SESSION['user_id']]);
                    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch payment methods
                    $paymentStmt = $pdo->prepare("SELECT * FROM payment_methods");
                    $paymentStmt->execute();
                    $paymentMethods = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($bookings)) {
                        echo '
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Bookings Found</h3>
                            <p class="text-muted">You haven\'t made any room bookings yet.</p>
                            <a href="rooms.php" class="btn btn-warning btn-action mt-3">
                                <i class="fas fa-search"></i> Browse Rooms
                            </a>
                        </div>';
                    } else {
                        foreach($bookings as $booking) {
                            ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title">Booking #<?php echo $booking['booking_id']; ?></h5>
                                        <span class="badge bg-<?php echo $statusClass = match($booking['status']) {
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'finished' => 'info',
                                            default => 'secondary'
                                        }; ?>"><?php echo ucfirst($booking['status']); ?></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><i class="fas fa-calendar-check me-2"></i> <strong>Check-in:</strong> 
                                                <?php echo date('M d, Y', strtotime($booking['check_in'])); ?></p>
                                            <p><i class="fas fa-calendar-times me-2"></i> <strong>Check-out:</strong> 
                                                <?php echo date('M d, Y', strtotime($booking['check_out'])); ?></p>
                                            <p><i class="fas fa-bed me-2"></i> <strong>Room Type:</strong> 
                                                <?php echo htmlspecialchars($booking['room_type']); ?></p>
                                            <p><i class="fas fa-money-bill me-2"></i> <strong>Price per Night:</strong> 
                                                ₱<?php echo number_format($booking['room_price'], 2); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><i class="fas fa-users me-2"></i> <strong>Guests:</strong></p>
                                            <?php
                            $guestStmt = $pdo->prepare("
                                                SELECT first_name, last_name, guest_type 
                                FROM guest_names 
                                WHERE booking_id = :booking_id
                            ");
                            $guestStmt->execute([':booking_id' => $booking['booking_id']]);
                            $guests = $guestStmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach($guests as $guest) {
                                                echo '<div class="guest-item">';
                                                echo htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']);
                                                if(!empty($guest['guest_type'])) {
                                                    echo ' <small class="text-muted">(' . htmlspecialchars($guest['guest_type']) . ')</small>';
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                            
                                            <div class="payment-info mt-3">
                                                <p class="mt-3"><i class="fas fa-credit-card me-2"></i> <strong>Total Amount:</strong> 
                                                    ₱<?php echo number_format($booking['total_amount'], 2); ?></p>
                                                
                                                <?php if($booking['payment_option'] === 'downpayment' || $booking['payment_option'] === 'Partial Payment'): ?>
                                                    <p><i class="fas fa-money-bill me-2"></i> <strong>Payment Option:</strong> 
                                                        <?php echo $booking['payment_option']; ?></p>
                                                    <p><i class="fas fa-money-bill me-2"></i> <strong>Initial Payment:</strong> 
                                                        ₱<?php echo number_format($booking['downpayment_amount'], 2); ?></p>
                                                    
                                                    <?php if($booking['remaining_balance'] > 0): ?>
                                                        <p><i class="fas fa-exclamation-circle me-2 text-warning"></i> <strong>Remaining Balance:</strong> 
                                                            ₱<?php echo number_format($booking['remaining_balance'], 2); ?></p>
                                                    <?php else: ?>
                                                        <p><i class="fas fa-check-circle me-2 text-success"></i> <strong>Payment Status:</strong> Fully Paid</p>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                    <p><i class="fas fa-money-bill me-2"></i> <strong>Payment Option:</strong> 
                                                        Full Payment</p>
                                                    <p><i class="fas fa-check-circle me-2 text-success"></i> <strong>Payment Status:</strong> Fully Paid</p>
                                                <?php endif; ?>

                                                <p><i class="fas fa-money-check me-2"></i> <strong>Payment Method:</strong> 
                                                    <?php echo strtoupper($booking['payment_method']); ?></p>

                                                <?php if(isset($booking['payment_proof']) && !empty($booking['payment_proof'])): ?>
                                                    <button class="btn btn-sm btn-warning text-dark mt-2" onclick="viewPaymentProof('<?php echo htmlspecialchars($booking['payment_proof']); ?>')">
                                                        <i class="fas fa-image me-1"></i> View Payment Proof
                                                    </button>
                                                <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <div class="text-muted small mt-2">
                                            <i class="fas fa-info-circle me-1"></i> Room bookings cannot be cancelled
                                        </div>
                                </div>
                            </div>
                        <?php 
                        }
                    }
                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Add this modal for booking history -->
    <div class="modal fade" id="bookingHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tableHistory">
                                <i class="fas fa-utensils me-2"></i>Table History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#roomHistory">
                                <i class="fas fa-bed me-2"></i>Room History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#eventHistory">
                                <i class="fas fa-calendar-alt me-2"></i>Event History
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3">
                        <div class="tab-pane fade show active" id="tableHistory">
                            <?php
                            try {
                                $tableHistoryStmt = $pdo->prepare("
                                    SELECT tb.*, 
                                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                        u.email
                                    FROM table_bookings tb
                                    LEFT JOIN userss u ON tb.user_id = u.id
                                    WHERE tb.user_id = :user_id 
                                    AND tb.status IN ('completed', 'cancelled')
                                    ORDER BY tb.created_at DESC
                                ");
                                
                                $tableHistoryStmt->execute([':user_id' => $_SESSION['user_id']]);
                                $tableHistory = $tableHistoryStmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($tableHistory)) {
                                    echo '<div class="text-center py-4">
                                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No table booking history found.</p>
                                    </div>';
                                } else {
                                    foreach ($tableHistory as $booking) {
                                        // Display table history items
                                        include('table_history_item.php');
                                    }
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                            }
                            ?>
                        </div>
                        
                        <div class="tab-pane fade" id="roomHistory">
                            <?php
                            try {
                                $roomHistoryStmt = $pdo->prepare("
                                    SELECT 
                                        b.*,
                                        DATEDIFF(b.check_out, b.check_in) as number_of_nights,
                                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                        u.email,
                                        g.first_name as guest_first_name,
                                        g.last_name as guest_last_name,
                                        g.guest_type,
                                        g.id_number
                                    FROM bookings b
                                    LEFT JOIN userss u ON b.user_id = u.id
                                    LEFT JOIN guest_names g ON b.booking_id = g.booking_id
                                    WHERE b.user_id = :user_id 
                                    AND b.status IN ('finished', 'rejected', 'check-out')
                                    ORDER BY b.created_at DESC
                                ");
                                
                                $roomHistoryStmt->execute([':user_id' => $_SESSION['user_id']]);
                                $roomHistory = $roomHistoryStmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($roomHistory)) {
                                    echo '<div class="text-center py-4">
                                        <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No room booking history found.</p>
                                    </div>';
                                } else {
                                    foreach ($roomHistory as $booking) {
                                        // Display room history items
                                        include('room_history_item.php');
                                    }
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                            }
                            ?>
                        </div>
                        
                        <div class="tab-pane fade" id="eventHistory">
                            <?php
                            try {
                                $eventHistoryStmt = $pdo->prepare("
                                    SELECT eb.*, 
                                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                        u.email,
                                        u.contact_number as user_contact
                                    FROM event_bookings eb
                                    LEFT JOIN userss u ON eb.user_id = u.id
                                    WHERE eb.user_id = :user_id 
                                    AND eb.booking_status IN ('finished', 'cancelled')  /* Changed to include both finished and cancelled */
                                    ORDER BY eb.created_at DESC
                                ");
                                
                                $eventHistoryStmt->execute([':user_id' => $_SESSION['user_id']]);
                                $eventHistory = $eventHistoryStmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($eventHistory)) {
                                    echo '<div class="text-center py-4">
                                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No event booking history found.</p>
                                    </div>';
                                } else {
                                    foreach ($eventHistory as $booking) {
                                        // Display event history items
                                        include('event_history_item.php');
                                    }
                                }
                            } catch(PDOException $e) {
                                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal for event details -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Event Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="eventDetailsContent">
                    <!-- Content will be loaded dynamically -->
                    <div class="text-center">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pay Remaining Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Payment Method:</label>
                        <select class="form-select" id="paymentMethodSelect">
                            <option value="">Choose payment method...</option>
                            <?php
                            try {
                                $paymentMethodsStmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1");
                                $paymentMethodsStmt->execute();
                                $paymentMethods = $paymentMethodsStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach($paymentMethods as $method) {
                                    echo '<option value="' . htmlspecialchars($method['name']) . '" 
                                            data-details="' . htmlspecialchars($method['display_name']) . '"
                                            data-number="' . htmlspecialchars($method['account_number']) . '"
                                            data-qr="' . htmlspecialchars($method['qr_code_image']) . '"
                                            data-account-name="' . htmlspecialchars($method['account_name']) . '">
                                            ' . htmlspecialchars(strtoupper($method['display_name'])) . '
                                        </option>';
                                }
                            } catch(PDOException $e) {
                                echo '<option value="">Error loading payment methods</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div id="paymentDetails" class="d-none">
                        <div class="text-center mb-3">
                            <img id="qrCode" src="" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center mb-1" id="accountName"></p>
                        <p class="text-center text-muted small" id="accountNumber"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number:</label>
                        <input type="text" class="form-control" id="referenceNumber" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Payment Screenshot:</label>
                        <input type="file" class="form-control" id="paymentProof" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" onclick="processPayment()">Submit Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="booking-details-content">
                        <!-- Content will be populated dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function isValidDate(date) {
            return date instanceof Date && !isNaN(date);
        }

        function payRemainingBalance(bookingId, amount) {
            Swal.fire({
                title: 'Pay Remaining Balance',
                html: `
                    <p>Remaining Balance: ₱${amount.toLocaleString()}</p>
                    <p>Select Payment Method:</p>
                    <select id="paymentMethod" class="form-select mb-3" onchange="showQRCode(this.value)">
                        <option value="">Select payment method</option>
                        <option value="gcash">GCash</option>
                        <option value="maya">Maya</option>
                    </select>
                    <div id="qrCodeContainer" class="text-center mt-3" style="display: none;">
                        <img id="qrCodeImage" src="" alt="QR Code" style="max-width: 200px; height: auto;">
                        <p class="mt-2 mb-0" id="accountName"></p>
                        <p class="small text-muted" id="accountNumber"></p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Proceed to Payment',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const paymentMethod = document.getElementById('paymentMethod').value;
                    processPayment(bookingId, amount, paymentMethod);
                }
            });
        }

        function processPayment(bookingId, amount, paymentMethod) {
            // Add your payment processing logic here
            fetch('process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    amount: amount,
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: 'Your payment has been processed successfully.',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: error.message,
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        function cancelTableBooking(bookingId) {
            // First modal for cancellation reason
            Swal.fire({
                title: 'Cancellation Reason',
                html: `
                    <p class="mb-3">Please select a reason for cancellation:</p>
                    <div class="text-left">
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="cancelReason" id="tableReason1" value="change_of_plans" required>
                            <label class="form-check-label" for="tableReason1">Change of Plans</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="cancelReason" id="tableReason2" value="emergency">
                            <label class="form-check-label" for="tableReason2">Emergency</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="cancelReason" id="tableReason3" value="booking_mistake">
                            <label class="form-check-label" for="tableReason3">Booking Mistake</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="cancelReason" id="tableReason4" value="found_better_option">
                            <label class="form-check-label" for="tableReason4">Found Better Option</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="cancelReason" id="tableReason5" value="other">
                            <label class="form-check-label" for="tableReason5">Other</label>
                        </div>
                        <div id="tableOtherReasonDiv" class="mt-2 d-none">
                            <input type="text" id="tableOtherReasonText" class="form-control" placeholder="Please specify your reason">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Next',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const selectedReason = document.querySelector('input[name="cancelReason"]:checked');
                    if (!selectedReason) {
                        Swal.showValidationMessage('Please select a reason for cancellation');
                        return false;
                    }
                    let reason = selectedReason.value;
                    if (reason === 'other') {
                        const otherReason = document.getElementById('tableOtherReasonText').value.trim();
                        if (!otherReason) {
                            Swal.showValidationMessage('Please specify your reason');
                            return false;
                        }
                        reason = otherReason;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show second modal for policy confirmation
                    Swal.fire({
                        title: 'Cancellation Policy',
                        html: `
                            <div class="text-start">
                                <p>Please read our cancellation policy carefully:</p>
                                <ul>
                                    <li>Free cancellation up to 24 hours before reservation time</li>
                                    <li>Cancellations within 24 hours will incur a 50% charge</li>
                                    <li>No-shows will be charged the full amount</li>
                                    <li>No Refund</li>
                                </ul>
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="policyConfirm" required>
                                    <label class="form-check-label" for="policyConfirm">
                                        I have read and understand the cancellation policy
                                    </label>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, cancel reservation',
                        cancelButtonText: 'No, keep reservation',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        preConfirm: () => {
                            if (!document.getElementById('policyConfirm').checked) {
                                Swal.showValidationMessage('Please confirm that you have read the cancellation policy');
                                return false;
                            }
                            return true;
                        }
                    }).then((policyResult) => {
                        if (policyResult.isConfirmed) {
                            processCancellation(bookingId, result.value);
                        }
                    });
                }
            });
        }

        function processCancellation(bookingId, reason) {
            fetch('cancel_table_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reservation Cancelled',
                        text: 'Your table reservation has been cancelled successfully.',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Cancellation failed');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Cancellation Failed',
                    text: error.message,
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get filter elements
            const statusFilter = document.getElementById('statusFilter');
            const dateFilter = document.getElementById('dateFilter');
            const bookingTypeFilter = document.getElementById('bookingTypeFilter');

            // Add event listeners
            statusFilter.addEventListener('change', applyFilters);
            dateFilter.addEventListener('change', applyFilters);
            bookingTypeFilter.addEventListener('change', applyFilters);

            function applyFilters() {
                const status = statusFilter.value.toLowerCase();
                const date = dateFilter.value;
                const type = bookingTypeFilter.value;

                // Get all booking cards
                const tableBookings = document.querySelectorAll('.table-booking');
                const roomBookings = document.querySelectorAll('.booking-card:not(.table-booking)');
                const eventBookings = document.querySelectorAll('.event-booking');

                // Filter function
                function filterBooking(booking, isTable, isEvent) {
                    let show = true;

                    // Filter by type
                    if (type) {
                        if ((type === 'table' && !isTable) || (type === 'room' && !isEvent && !isTable) || (type === 'event' && !isEvent)) {
                            show = false;
                        }
                    }

                    // Filter by status
                    if (status && show) {
                        const bookingStatus = booking.querySelector('[data-status]')?.textContent.toLowerCase().trim() ||
                                             booking.querySelector('.status-badge')?.textContent.toLowerCase().trim();
                        if (bookingStatus !== status) {
                            show = false;
                        }
                    }

                    // Filter by date
                    if (date && show) {
                        const bookingDateText = booking.querySelector('[data-reservation-date]')?.textContent || 
                                               booking.querySelector('.text-muted')?.textContent || '';
                        if (bookingDateText) {
                            const bookingDate = new Date(bookingDateText.replace('Reserved on ', '').trim());
                            const filterDate = new Date(date);
                            
                            if (isValidDate(bookingDate) && isValidDate(filterDate)) {
                                if (bookingDate.toDateString() !== filterDate.toDateString()) {
                                    show = false;
                                }
                            }
                        }
                    }

                    booking.style.display = show ? 'block' : 'none';
                }

                // Apply filters to table bookings
                tableBookings.forEach(booking => filterBooking(booking, true, false));
                
                // Apply filters to room bookings
                roomBookings.forEach(booking => filterBooking(booking, false, false));
                
                // Apply filters to event bookings
                eventBookings.forEach(booking => filterBooking(booking, false, true));

                // Show empty state if no results
                checkEmptyState();
            }

            function checkEmptyState() {
                const tableSection = document.querySelector('.table-reservations');
                const roomSection = document.querySelector('.room-bookings');
                const eventSection = document.querySelector('.event-bookings');
                
                [tableSection, roomSection, eventSection].forEach(section => {
                    if (!section) return;
                    
                    const visibleBookings = section.querySelectorAll('.booking-card[style="display: block"]');
                    const emptyState = section.querySelector('.empty-state');
                    const filterEmptyState = section.querySelector('.filter-empty-state');
                    
                    if (visibleBookings.length === 0) {
                        if (filterEmptyState) {
                            filterEmptyState.style.display = 'block';
                        } else {
                            const newEmptyState = document.createElement('div');
                            newEmptyState.className = 'empty-state filter-empty-state';
                            newEmptyState.innerHTML = `
                                <i class="fas fa-filter"></i>
                                <h3>No Results Found</h3>
                                <p class="text-muted">No bookings match your filter criteria.</p>
                            `;
                            section.appendChild(newEmptyState);
                        }
                        if (emptyState) emptyState.style.display = 'none';
                    } else {
                        if (filterEmptyState) filterEmptyState.style.display = 'none';
                        if (emptyState) emptyState.style.display = 'none';
                    }
                });
            }
        });

        function resetFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('bookingTypeFilter').value = '';
            
            // Show all bookings
            const bookings = document.querySelectorAll('.booking-card');
            bookings.forEach(booking => booking.style.display = 'block');
            
            // Reset empty states
            const emptyStates = document.querySelectorAll('.empty-state');
            emptyStates.forEach(state => state.style.display = 'block');
            
            const filterEmptyStates = document.querySelectorAll('.filter-empty-state');
            filterEmptyStates.forEach(state => state.style.display = 'none');
        }

        function updateBookingStatus(bookingId, status) {
            fetch('update_booking_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Booking status updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                        // Reload the page or update the UI
                        location.reload();
                    });
                } else {
                    throw new Error(result.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to update booking status',
                    icon: 'error',
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        function viewEventDetails(bookingId) {
            const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            modal.show();

            fetch(`get_event_details.php?id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('eventDetailsContent');
                    
                    if (data.success) {
                        const booking = data.booking;
                        content.innerHTML = `
                            <div class="booking-details">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Event Information</h6>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Event Type</label>
                                            <p class="mb-1">${booking.event_type}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Event Theme</label>
                                            <p class="mb-1">${booking.event_theme || 'Not specified'}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Event Date & Time</label>
                                            <p class="mb-1">${booking.formatted_reservation_date}</p>
                                            <p class="mb-1">${booking.formatted_start_time} - ${booking.formatted_end_time}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Number of Guests</label>
                                            <p class="mb-1">${booking.number_of_guests} persons</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Venue Setup</label>
                                            <p class="mb-1">${booking.venue_setup || 'Standard setup'}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Contact Information</h6>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Contact Person</label>
                                            <p class="mb-1">${booking.contact_person}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Contact Number</label>
                                            <p class="mb-1">${booking.user_contact || 'Not provided'}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Email</label>
                                            <p class="mb-1">${booking.email}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Payment Details</h6>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Total Amount</label>
                                            <p class="mb-1">₱${parseFloat(booking.total_amount).toLocaleString()}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Amount Paid</label>
                                            <p class="mb-1">₱${parseFloat(booking.paid_amount).toLocaleString()}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Remaining Balance</label>
                                            <p class="mb-1">₱${parseFloat(booking.remaining_balance).toLocaleString()}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Payment Method</label>
                                            <p class="mb-1">${booking.payment_method.toUpperCase()}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Payment Status</label>
                                            <p class="mb-1">
                                                <span class="badge ${booking.payment_status === 'Fully Paid' ? 'bg-success' : 'bg-warning'}">
                                                    ${booking.payment_status}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Additional Information</h6>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Special Requests</label>
                                            <p class="mb-1">${booking.special_requests || 'None'}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Food Preferences</label>
                                            <p class="mb-1">${booking.food_preferences || 'Not specified'}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Additional Services</label>
                                            <p class="mb-1">${booking.additional_services || 'None requested'}</p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Booking Status</label>
                                            <p class="mb-1">
                                                <span class="badge bg-${getStatusColor(booking.booking_status)}">
                                                    ${booking.booking_status.toUpperCase()}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="info-group mb-3">
                                            <label class="text-muted">Booking Date</label>
                                            <p class="mb-1">${booking.formatted_created_at}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Failed to load event details: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('eventDetailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error loading event details: ${error.message}
                        </div>
                    `;
                });
        }

        function getStatusColor(status) {
            switch (status.toLowerCase()) {
                case 'pending':
                    return 'warning';
                case 'confirmed':
                    return 'success';
                case 'cancelled':
                    return 'danger';
                case 'finished':
                    return 'info';
                default:
                    return 'secondary';
            }
        }

        function payEventBalance(bookingId, amount) {
            Swal.fire({
                title: 'Pay Event Balance',
                html: `
                    <div class="mb-3">
                        <p class="mb-2">Remaining Balance: ₱${amount.toLocaleString()}</p>
                        <label class="form-label">Select Payment Method:</label>
                        <select id="eventPaymentMethod" class="form-select mb-3" onchange="showEventQRCode(this.value)">
                        <option value="">Select payment method</option>
                        <option value="gcash">GCash</option>
                        <option value="maya">Maya</option>
                    </select>
                        
                        <div id="eventQRCodeContainer" class="text-center mt-3 d-none">
                            <img id="eventQRCodeImage" src="" alt="QR Code" style="max-width: 200px; height: auto;">
                            <p class="mt-2 mb-0" id="eventAccountName"></p>
                            <p class="small text-muted" id="eventAccountNumber"></p>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Reference Number:</label>
                            <input type="text" id="eventReferenceNumber" class="form-control" placeholder="Enter payment reference number">
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Upload Payment Screenshot:</label>
                            <input type="file" id="eventPaymentProof" class="form-control" accept="image/*">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit Payment',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const paymentMethod = document.getElementById('eventPaymentMethod').value;
                    const referenceNumber = document.getElementById('eventReferenceNumber').value;
                    const paymentProof = document.getElementById('eventPaymentProof').files[0];

                    if (!paymentMethod) {
                        Swal.showValidationMessage('Please select a payment method');
                        return false;
                    }
                    if (!referenceNumber) {
                        Swal.showValidationMessage('Please enter the reference number');
                        return false;
                    }
                    if (!paymentProof) {
                        Swal.showValidationMessage('Please upload the payment proof');
                        return false;
                    }

                    return {
                        paymentMethod,
                        referenceNumber,
                        paymentProof
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('booking_id', bookingId);
                    formData.append('amount', amount);
                    formData.append('payment_method', result.value.paymentMethod);
                    formData.append('reference_number', result.value.referenceNumber);
                    formData.append('payment_proof', result.value.paymentProof);
                    formData.append('payment_type', 'event');
                    formData.append('booking_reference', 'EVENT-' + bookingId);

                    // Show loading state
                    Swal.fire({
                        title: 'Processing Payment',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

            fetch('process_event_payment.php', {
                method: 'POST',
                        body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: 'Your payment has been processed successfully.',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                                location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                            text: error.message || 'An error occurred while processing your payment',
                            confirmButtonColor: '#ffc107'
                        });
                    });
                }
            });
        }

        function showEventQRCode(paymentMethod) {
            const qrContainer = document.getElementById('eventQRCodeContainer');
            const qrImage = document.getElementById('eventQRCodeImage');
            const accountName = document.getElementById('eventAccountName');
            const accountNumber = document.getElementById('eventAccountNumber');

            if (!paymentMethod) {
                qrContainer.classList.add('d-none');
                return;
            }

            // Fetch payment method details from the server
            fetch('get_payment_method.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qrImage.src = data.qr_code_image;
                    accountName.textContent = data.account_name;
                    accountNumber.textContent = `Account Number: ${data.account_number}`;
                    qrContainer.classList.remove('d-none');
                } else {
                    throw new Error(data.message || 'Failed to load QR code');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        function cancelEventBooking(bookingId) {
            // Similar cancellation flow as table and room bookings
            Swal.fire({
                title: 'Cancel Event Booking',
                text: 'Are you sure you want to cancel this event booking?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No, keep it',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('cancel_event_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            booking_id: bookingId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Cancelled',
                                text: 'Your event booking has been cancelled successfully.',
                                confirmButtonColor: '#ffc107'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Cancellation failed');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cancellation Failed',
                            text: error.message,
                            confirmButtonColor: '#ffc107'
                        });
                    });
                }
            });
        }

        function showBookingHistory() {
            const historyModal = new bootstrap.Modal(document.getElementById('bookingHistoryModal'));
            historyModal.show();
        }

        // Update the showQRCode function to show/hide the payment fields
        function showQRCode(paymentMethod) {
            const qrContainer = document.getElementById('qrCodeContainer');
            const qrImage = document.getElementById('qrCodeImage');
            const accountNameElement = document.getElementById('accountName');
            const accountNumberElement = document.getElementById('accountNumber');

            if (!paymentMethod) {
                qrContainer.style.display = 'none';
                return;
            }

            // Fetch payment method details from the server
            fetch('get_payment_method.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qrImage.src = data.qr_code_image;
                    accountNameElement.textContent = data.account_name;
                    accountNumberElement.textContent = `Account Number: ${data.account_number}`;
                    qrContainer.style.display = 'block';
                } else {
                    throw new Error(data.message || 'Failed to load QR code');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        let currentBookingId = null;
        let currentAmount = null;

        function showPaymentModal(bookingId, amount) {
            currentBookingId = bookingId;
            currentAmount = amount;
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        document.getElementById('paymentMethodSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const details = selected.dataset.details;
            const number = selected.dataset.number;
            const qrPath = selected.dataset.qr;
            const accountNameText = selected.dataset.accountName;
            const paymentDetails = document.getElementById('paymentDetails');
            const accountName = document.getElementById('accountName');
            const accountNumber = document.getElementById('accountNumber');
            const qrCode = document.getElementById('qrCode');

            if (this.value) {
                paymentDetails.classList.remove('d-none');
                accountName.textContent = accountNameText || details;
                accountNumber.textContent = `Account Number: ${number}`;
                
                // Set QR code image with error handling
                if (qrPath) {
                    qrCode.onerror = function() {
                        console.error('Failed to load QR code image');
                        this.src = 'assets/images/default-qr.png';
                    };
                    // Simplified path handling
                    qrCode.src = qrPath.includes('/') ? qrPath : `uploads/payment_qr_codes/${qrPath}`;
                } else {
                    qrCode.src = 'assets/images/default-qr.png';
                }
            } else {
                paymentDetails.classList.add('d-none');
            }
        });

        function processPayment() {
            const paymentMethod = document.getElementById('paymentMethodSelect').value;
            const referenceNumber = document.getElementById('referenceNumber').value;
            const paymentProof = document.getElementById('paymentProof').files[0];

            if (!paymentMethod || !referenceNumber || !paymentProof) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill in all payment details',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            const formData = new FormData();
            formData.append('booking_id', currentBookingId);
            formData.append('amount', currentAmount);
            formData.append('payment_method', paymentMethod);
            formData.append('reference_number', referenceNumber);
            formData.append('payment_proof', paymentProof);
            formData.append('payment_type', 'room'); // Add payment type
            formData.append('booking_reference', 'ROOM-' + currentBookingId);

            // Show loading state
            Swal.fire({
                title: 'Processing Payment',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: 'Your payment has been processed successfully.',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                        // Close the payment modal
                        const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                        if (paymentModal) {
                        paymentModal.hide();
                        }
                        
                        // Reload page to show updated status
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: error.message || 'An error occurred while processing your payment',
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        function showBookingDetails(booking) {
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            const content = document.querySelector('.booking-details-content');
            
            // Format the booking details HTML
            let detailsHtml = `
                <div class="booking-details">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Booking Information</h6>
                            <p><strong>Booking ID:</strong> #${booking.id}</p>
                            <p><strong>Status:</strong> <span class="badge ${booking.status === 'pending' ? 'bg-warning' : 'bg-success'}">${booking.status}</span></p>
                            <p><strong>Date:</strong> ${booking.reservation_date}</p>
                            <p><strong>Time:</strong> ${booking.start_time} - ${booking.end_time}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Package Details</h6>
                            <p><strong>Package:</strong> ${booking.package_name}</p>
                            <p><strong>Number of Guests:</strong> ${booking.guest_count}</p>
                            <p><strong>Total Amount:</strong> ₱${parseFloat(booking.total_amount).toLocaleString()}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6>Contact Information</h6>
                            <p><strong>Name:</strong> ${booking.user_name}</p>
                            <p><strong>Email:</strong> ${booking.email_address || booking.email}</p>
                            <p><strong>Contact:</strong> ${booking.contact_number}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Payment Information</h6>
                            <p><strong>Payment Method:</strong> ${booking.payment_method ? booking.payment_method.toUpperCase() : '-'}</p>
                            <p><strong>Reference Number:</strong> ${booking.reference_number || '-'}</p>
                            ${booking.payment_proof ? `
                                <div class="mt-2">
                                    <p><strong>Payment Proof:</strong></p>
                                    <img src="../../uploads/payment_proofs/${booking.payment_proof}" 
                                         alt="Payment Proof" 
                                         class="img-fluid payment-proof-img" 
                                         style="max-width: 300px; cursor: pointer"
                                         onclick="showFullImage(this.src)">
                                </div>
                            ` : '<p class="text-muted">No payment proof uploaded</p>'}
                        </div>
                    </div>
                    ${booking.special_requests ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Special Requests</h6>
                                <p>${booking.special_requests}</p>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            content.innerHTML = detailsHtml;
            modal.show();
        }

        // Add function to show full-size image
        function showFullImage(src) {
            Swal.fire({
                imageUrl: src,
                imageAlt: 'Payment Proof',
                width: '80%',
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function viewPaymentProof(proofImage) {
            Swal.fire({
                imageUrl: `../../uploads/payment_proofs/${proofImage}`,
                imageAlt: 'Payment Proof',
                width: '80%',
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    </script>
</body>
</html> 