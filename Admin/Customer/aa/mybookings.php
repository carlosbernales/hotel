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
            background: linear-gradient(135deg, #ffffffff 0%,  #d2b813ff 100%);
            min-height: 100vh;
            color: #333;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #ffdb4d);
            padding: 2rem 0;
            margin-bottom: 2rem;
            margin-top: 70px;
            text-align: center;
            color: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .booking-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            overflow: hidden;
            position: relative;
            border: none;
        }

        .booking-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #ffdb4d);
        }

        .card-header-custom {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 219, 77, 0.05));
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 193, 7, 0.2);
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-pending {
            background: linear-gradient(135deg, var(--warning-color), #ffdb4d);
            color: #000;
        }

        .status-confirmed {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: #fff;
        }

        .status-cancelled {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            color: #fff;
        }

        .booking-type-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .table-icon {
            background: linear-gradient(135deg, #c8b32bff, #ddc109ff);
        }

        .room-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .event-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .info-item i {
            width: 20px;
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .price-tag {
            background: linear-gradient(135deg, var(--primary-color), #ffdb4d);
            color: #000;
            padding: 0.75rem 1.25rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .section-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.5rem;
        }

        .guest-list {
            max-height: 120px;
            overflow-y: auto;
            background: rgba(248, 249, 250, 0.5);
            border-radius: 10px;
            padding: 0.75rem;
            margin-top: 0.5rem;
        }

        .guest-item {
            padding: 0.25rem 0;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .guest-item:last-child {
            border-bottom: none;
        }

        .countdown-timer {
            background: rgba(255, 193, 7, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #666;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .booking-card {
                margin-bottom: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .booking-reference {
            font-family: 'Courier New', monospace;
            background: rgba(255, 193, 7, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php include 'message_box.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1 class="mb-0"><i class="fas fa-calendar-check me-3"></i>My Bookings</h1>
            <p class="mb-0">Manage all your reservations in one place</p>
        </div>
    </div>

    <div class="container mb-4">
        <div class="filter-section">
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
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Table Reservations -->
        <div class="mb-5">
            <h2 class="section-title"><i class="fas fa-utensils me-2"></i>Table Reservations</h2>
            <div class="row" id="tableBookingsContainer">
                <?php
                try {
                    $tableStmt = $pdo->prepare("
                        SELECT 
                            ot.*,
                            ott.table_name,
                            ott.table_number,
                            CONCAT(ot.firstname, ' ', ot.lastname) as customer_name,
                            ot.contact as customer_contact,
                            ot.email as customer_email
                        FROM orders_table ot
                        LEFT JOIN orders_table_type ott ON ot.id = ott.table_booking_fk_id
                        WHERE ot.user_id = :user_id AND (ot.order_type = 'Table Booking' OR ot.order_type = 'advance')
                        GROUP BY ot.id
                        ORDER BY ot.order_at DESC
                    ");
                    
                    $tableStmt->execute([':user_id' => $_SESSION['user_id']]);
                    $tableBookings = $tableStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($tableBookings)) {
                        echo '<div class="col-12"><div class="empty-state fade-in">
                            <i class="fas fa-utensils"></i>
                            <h3>No Table Reservations</h3>
                            <p class="text-muted">You haven\'t made any table reservations yet.</p>
                            <a href="table-booking.php" class="btn btn-warning btn-action">
                                <i class="fas fa-plus"></i> Reserve a Table
                            </a>
                        </div></div>';
                    } else {
                        foreach($tableBookings as $booking) {
                            $statusClass = match($booking['status']) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'cancelled' => 'status-cancelled',
                                'completed' => 'status-info',
                                default => ''
                            };
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4 booking-item" data-type="table" data-status="<?php echo strtolower($booking['status']); ?>" data-date="<?php echo date('Y-m-d', strtotime($booking['date_time'])); ?>">
                                <div class="booking-card fade-in">
                                    <div class="card-header-custom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="booking-type-icon table-icon">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </div>
                                        <h6 class="mt-2 mb-1">Table Reservation</h6>
                                        <div class="booking-reference">#<?php echo $booking['order_id']; ?></div>
                                    </div>
                                    <div class="card-body-custom">
                                        <div class="info-item">
                                            <i class="fas fa-calendar-day"></i>
                                            <span><?php echo date('M d, Y', strtotime($booking['date_time'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo date('g:i A', strtotime($booking['date_time'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-table"></i>
                                            <span><?php echo htmlspecialchars($booking['table_name'] ?? 'Standard Table'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-hashtag"></i>
                                            <span>Table <?php echo htmlspecialchars($booking['table_number'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></span>
                                        </div>
                                        
                                        <div class="price-tag">
                                            ₱<?php echo number_format($booking['total'], 2); ?>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-action flex-fill" onclick="showTableBookingDetails(<?php echo htmlspecialchars(json_encode($booking), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-info-circle me-1"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } catch(PDOException $e) {
                    echo '<div class="col-12"><div class="alert alert-danger">Error: ' . $e->getMessage() . '</div></div>';
                }
                ?>
            </div>
        </div>

        <!-- Event Bookings -->
        <div class="mb-5">
            <h2 class="section-title"><i class="fas fa-calendar-alt me-2"></i>Event Bookings</h2>
            <div class="row" id="eventBookingsContainer">
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
                        ORDER BY eb.date_time_start DESC
                    ");
                    
                    $eventStmt->execute([':user_id' => $_SESSION['user_id']]);
                    $eventBookings = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($eventBookings)) {
                        echo '<div class="col-12"><div class="empty-state fade-in">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Event Bookings</h3>
                            <p class="text-muted">You haven\'t made any event bookings yet.</p>
                            <a href="events.php" class="btn btn-warning btn-action">
                                <i class="fas fa-plus"></i> Book an Event
                            </a>
                        </div></div>';
                    } else {
                        foreach($eventBookings as $booking) {
                            $statusClass = match($booking['booking_status']) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'cancelled' => 'status-cancelled',
                                default => ''
                            };
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4 booking-item" data-type="event" data-status="<?php echo strtolower($booking['booking_status']); ?>" data-date="<?php echo date('Y-m-d', strtotime($booking['created_at'])); ?>">
                                <div class="booking-card fade-in">
                                    <div class="card-header-custom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="booking-type-icon event-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                <?php echo ucfirst($booking['booking_status']); ?>
                                            </span>
                                        </div>
                                        <h6 class="mt-2 mb-1"><?php echo htmlspecialchars($booking['package_name']); ?></h6>
                                        <div class="booking-reference">#<?php echo $booking['booking_refId']; ?></div>
                                    </div>
                                    <div class="card-body-custom">
                                        <div class="info-item">
                                            <i class="fas fa-calendar-day"></i>
                                            <span><?php echo date('M d, Y', strtotime($booking['date_time_start'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo date('g:i A', strtotime($booking['date_time_end'])); ?> - <?php echo date('g:i A', strtotime($booking['date_time_end'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo htmlspecialchars($booking['event_type']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $booking['number_of_guests']; ?> Guests</span>
                                        </div>
                                        
                                        <div class="price-tag">
                                            ₱<?php echo number_format($booking['total_amount'], 2); ?>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-action flex-fill" onclick="showEventDetails(<?php echo htmlspecialchars(json_encode($booking), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-info-circle me-1"></i> Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } catch(PDOException $e) {
                    echo '<div class="col-12"><div class="alert alert-danger">Error: ' . $e->getMessage() . '</div></div>';
                }
                ?>
            </div>
        </div>

        <!-- Room Bookings -->
        <div class="mb-5">
            <h2 class="section-title"><i class="fas fa-bed me-2"></i>Room Bookings</h2>
            <div class="row" id="roomBookingsContainer">
                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT b.*, rt.room_type, rt.price as room_price,
                               b.downpayment_amount,
                               b.remaining_balance,
                               b.payment_option,
                               b.payment_method,
                               b.total_amount,
                               GROUP_CONCAT(DISTINCT CONCAT(br.room_type_name, ' (', rn.room_number, ')') SEPARATOR ', ') as booked_rooms,
                               GROUP_CONCAT(DISTINCT br.price) as room_prices,
                               COUNT(br.id) as number_of_rooms
                        FROM bookings b 
                        LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
                        LEFT JOIN booked_rooms br ON b.booking_id = br.booking_id
                        LEFT JOIN room_numbers rn ON br.room_number_fk_id = rn.room_number_id
                        WHERE b.user_id = :user_id 
                        GROUP BY b.booking_id
                        ORDER BY b.created_at DESC
                    ");
                    
                    $stmt->execute([':user_id' => $_SESSION['user_id']]);
                    $roomBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($roomBookings)) {
                        echo '<div class="col-12"><div class="empty-state fade-in">
                            <i class="fas fa-bed"></i>
                            <h3>No Room Bookings</h3>
                            <p class="text-muted">You haven\'t made any room bookings yet.</p>
                            <a href="rooms.php" class="btn btn-warning btn-action">
                                <i class="fas fa-search"></i> Browse Rooms
                            </a>
                        </div></div>';
                    } else {
                        foreach($roomBookings as $booking) {
                            $statusClass = match($booking['status']) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'cancelled' => 'status-cancelled',
                                'finished' => 'status-info',
                                default => ''
                            };
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4 booking-item" data-type="room" data-status="<?php echo strtolower($booking['status']); ?>" data-date="<?php echo date('Y-m-d', strtotime($booking['check_in'])); ?>">
                                <div class="booking-card fade-in">
                                    <div class="card-header-custom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="booking-type-icon room-icon">
                                                <i class="fas fa-bed"></i>
                                            </div>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </div>
                                        <h6 class="mt-2 mb-1">Room Booking</h6>
                                        <div class="booking-reference">#<?php echo $booking['booking_id']; ?></div>
                                    </div>
                                    <div class="card-body-custom">
                                        <div class="info-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <span><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-calendar-times"></i>
                                            <span><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-door-open"></i>
                                            <span><?php echo htmlspecialchars($booking['booked_rooms'] ?? $booking['room_type'] ?? 'Room'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-moon"></i>
                                            <span><?php echo (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24); ?> nights</span>
                                        </div>
                                        <?php if($booking['number_of_rooms'] > 1): ?>
                                        <div class="info-item">
                                            <i class="fas fa-bed"></i>
                                            <span><?php echo $booking['number_of_rooms']; ?> rooms</span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="price-tag">
                                            ₱<?php echo number_format($booking['total_amount'], 2); ?>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-action flex-fill" onclick="showRoomBookingDetails(<?php echo htmlspecialchars(json_encode($booking), ENT_QUOTES, 'UTF-8'); ?>)">
                                                <i class="fas fa-info-circle me-1"></i> Details
                                            </button>
                                        </div>
                                        
                                        <div class="countdown-timer mt-2">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Room booking details available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } catch(PDOException $e) {
                    echo '<div class="col-12"><div class="alert alert-danger">Error: ' . $e->getMessage() . '</div></div>';
                }
                ?>
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
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const dateFilter = document.getElementById('dateFilter');
            const bookingTypeFilter = document.getElementById('bookingTypeFilter');

            function applyFilters() {
                const status = statusFilter.value.toLowerCase();
                const date = dateFilter.value;
                const type = bookingTypeFilter.value;

                const allBookings = document.querySelectorAll('.booking-item');

                allBookings.forEach(booking => {
                    let show = true;

                    // Filter by type
                    if (type && booking.dataset.type !== type) {
                        show = false;
                    }

                    // Filter by status
                    if (status && booking.dataset.status !== status) {
                        show = false;
                    }

                    // Filter by date
                    if (date && booking.dataset.date !== date) {
                        show = false;
                    }

                    booking.style.display = show ? 'block' : 'none';
                });

                // Check if any bookings are visible
                const visibleBookings = document.querySelectorAll('.booking-item[style="display: block"]');
                const containers = ['tableBookingsContainer', 'eventBookingsContainer', 'roomBookingsContainer'];
                
                containers.forEach(containerId => {
                    const container = document.getElementById(containerId);
                    const containerVisible = Array.from(container.children).some(child => 
                        child.style.display !== 'none'
                    );
                    
                    if (!containerVisible && container.querySelector('.empty-state') === null) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.className = 'col-12';
                        emptyDiv.innerHTML = '<div class="empty-state"><i class="fas fa-filter"></i><h3>No Results Found</h3><p class="text-muted">No bookings match your filter criteria.</p></div>';
                        container.appendChild(emptyDiv);
                    } else if (containerVisible) {
                        const emptyState = container.querySelector('.empty-state');
                        if (emptyState) {
                            emptyState.parentElement.remove();
                        }
                    }
                });
            }

            statusFilter.addEventListener('change', applyFilters);
            dateFilter.addEventListener('change', applyFilters);
            bookingTypeFilter.addEventListener('change', applyFilters);
        });

        function resetFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('bookingTypeFilter').value = '';
            
            const allBookings = document.querySelectorAll('.booking-item');
            allBookings.forEach(booking => booking.style.display = 'block');
            
            // Remove filter empty states
            document.querySelectorAll('.empty-state').forEach(state => {
                if (state.textContent.includes('No Results Found')) {
                    state.parentElement.remove();
                }
            });
        }

        function showTableBookingDetails(booking) {
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            const content = document.querySelector('.booking-details-content');
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Reservation Information</h6>
                        <p><strong>Reservation ID:</strong> #${booking.order_id || booking.id}</p>
                        <p><strong>Order Type:</strong> ${booking.order_type || booking.type_of_order || 'Table Reservation'}</p>
                        <p><strong>Date:</strong> ${new Date(booking.date_time).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</p>
                        <p><strong>Time:</strong> ${new Date(booking.date_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                        <p><strong>Guests:</strong> ${booking.guest_count || booking.number_of_guests || 'N/A'}</p>
                        <p><strong>Table Type:</strong> ${booking.table_name || 'Standard Package'}</p>
                        <p><strong>Table Number:</strong> ${booking.table_number || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Payment Information</h6>
                        <p><strong>Total Amount:</strong> ₱${parseFloat(booking.total || booking.total_amount).toLocaleString()}</p>
                        <p><strong>Payment Method:</strong> ${booking.payment_method?.toUpperCase() || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning">${booking.status}</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-warning mb-3">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsBody">
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            // Fetch order items
            fetchOrderItems(booking.order_id || booking.id);
            
            modal.show();
        }

        function fetchOrderItems(orderId) {
            fetch('fetch_order_items.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('orderItemsBody');
                tbody.innerHTML = '';
                
                if (data.success && data.items.length > 0) {
                    data.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.item_name}</td>
                            <td>${item.quantity}</td>
                            <td>₱${parseFloat(item.unit_price).toLocaleString()}</td>
                            <td>₱${(parseFloat(item.unit_price) * parseInt(item.quantity)).toLocaleString()}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching order items:', error);
                document.getElementById('orderItemsBody').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading items</td></tr>';
            });
        }

        function showEventDetails(booking) {
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            const content = document.querySelector('.booking-details-content');
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Event Information</h6>
                        <p><strong>Booking Reference:</strong> #${booking.booking_refId}</p>
                        <p><strong>Package Name:</strong> ${booking.package_name}</p>
                        <p><strong>Event Type:</strong> ${booking.event_type}</p>
                        <p><strong>Date:</strong> ${new Date(booking.date_time_start).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</p>
                        <p><strong>Time:</strong> ${new Date(booking.date_time_start).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} - ${new Date(booking.date_time_end).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                        <p><strong>Number of Guests:</strong> ${booking.number_of_guests}</p>
                        <p><strong>Customer Name:</strong> ${booking.customer_name || booking.user_name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${booking.email || 'N/A'}</p>
                        <p><strong>Contact:</strong> ${booking.customer_contact || booking.user_contact || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Payment Information</h6>
                        <p><strong>Package Price:</strong> ₱${parseFloat(booking.package_price || 0).toLocaleString()}</p>
                        <p><strong>Total Amount:</strong> ₱${parseFloat(booking.total_amount).toLocaleString()}</p>
                        <p><strong>Paid Amount:</strong> ₱${parseFloat(booking.paid_amount || 0).toLocaleString()}</p>
                        ${booking.overtime_hours > 0 ? `<p><strong>Overtime Hours:</strong> ${booking.overtime_hours}</p>` : ''}
                        ${booking.overtime_charge > 0 ? `<p><strong>Overtime Charge:</strong> ₱${parseFloat(booking.overtime_charge).toLocaleString()}</p>` : ''}
                        ${booking.extra_guests > 0 ? `<p><strong>Extra Guests:</strong> ${booking.extra_guests}</p>` : ''}
                        ${booking.extra_guest_charge > 0 ? `<p><strong>Extra Guest Charge:</strong> ₱${parseFloat(booking.extra_guest_charge).toLocaleString()}</p>` : ''}
                        <p><strong>Status:</strong> <span class="badge bg-warning">${booking.booking_status}</span></p>
                    </div>
                </div>
            `;
            
            modal.show();
        }

        function showRoomBookingDetails(booking) {
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            const content = document.querySelector('.booking-details-content');
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Booking Information</h6>
                        <p><strong>Booking ID:</strong> #${booking.booking_id}</p>
                        <p><strong>Check-in:</strong> ${new Date(booking.check_in).toLocaleDateString()}</p>
                        <p><strong>Check-out:</strong> ${new Date(booking.check_out).toLocaleDateString()}</p>
                        <p><strong>Room Type:</strong> ${booking.room_type || 'N/A'}</p>
                        <p><strong>Booked Rooms:</strong> ${booking.booked_rooms || 'N/A'}</p>
                        <p><strong>Number of Rooms:</strong> ${booking.number_of_rooms || 1}</p>
                        <p><strong>Nights:</strong> ${Math.ceil((new Date(booking.check_out) - new Date(booking.check_in)) / (1000 * 60 * 60 * 24))}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">Payment Information</h6>
                        <p><strong>Total Amount:</strong> ₱${parseFloat(booking.total_amount).toLocaleString()}</p>
                        <p><strong>Payment Option:</strong> ${booking.payment_option || 'N/A'}</p>
                        <p><strong>Payment Method:</strong> ${booking.payment_method?.toUpperCase() || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge bg-info">${booking.status}</span></p>
                        ${booking.remaining_balance > 0 ? `<p><strong>Remaining Balance:</strong> ₱${parseFloat(booking.remaining_balance).toLocaleString()}</p>` : ''}
                    </div>
                </div>
            `;
            
            modal.show();
        }

        </script>
</body>
</html>