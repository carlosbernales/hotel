<?php
session_start();
include('includes/session.php');
require 'includes/database.php';

try {
    // Create database connection
    $dsn = "mysql:host=localhost;dbname=casadbs";
    $username = "root";
    $password = "";
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch room bookings with guest names
    $stmt = $pdo->prepare("
        SELECT b.*, rb.room_name, rb.room_quantity, rb.room_price, rb.subtotal,
               GROUP_CONCAT(DISTINCT gn.guest_name) as guest_names,
               p.amount as downpayment_amount, p.payment_status
        FROM bookings b 
        LEFT JOIN room_bookings rb ON b.booking_id = rb.booking_id
        LEFT JOIN guest_names gn ON b.booking_id = gn.booking_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.user_id = :user_id 
        GROUP BY b.booking_id, rb.id
        ORDER BY b.created_at DESC
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $room_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .booking-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            border-radius: 8px 8px 0 0;
        }
        .booking-body {
            padding: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .status-pending { background-color: #ffd700; color: #000; }
        .status-confirmed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">My Reservations</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($room_bookings)): ?>
            <div class="alert alert-info">
                You don't have any reservations yet. 
                <a href="roomss.php" class="alert-link">Book a room now!</a>
            </div>
        <?php else: ?>
            <?php foreach ($room_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Booking #<?php echo $booking['booking_id']; ?></h5>
                            <small class="text-muted">Booked on <?php echo date('F j, Y g:i A', strtotime($booking['created_at'])); ?></small>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    <div class="booking-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Room Details</h6>
                                <p>
                                    <?php echo $booking['room_name']; ?> x <?php echo $booking['room_quantity']; ?><br>
                                    Price per night: ₱<?php echo number_format($booking['room_price'], 2); ?><br>
                                    Subtotal: ₱<?php echo number_format($booking['subtotal'], 2); ?>
                                </p>
                                
                                <h6>Stay Information</h6>
                                <p>
                                    Check-in: <?php echo date('F j, Y', strtotime($booking['check_in'])); ?><br>
                                    Check-out: <?php echo date('F j, Y', strtotime($booking['check_out'])); ?><br>
                                    Arrival Time: <?php echo date('g:i A', strtotime($booking['arrival_time'])); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Guest Information</h6>
                                <p>
                                    Primary Guest: <?php echo htmlspecialchars($booking['name']); ?><br>
                                    Contact: <?php echo htmlspecialchars($booking['contact_number']); ?><br>
                                    Email: <?php echo htmlspecialchars($booking['email']); ?><br>
                                    Number of Guests: <?php echo $booking['number_of_guests']; ?>
                                </p>
                                
                                <?php if (!empty($booking['guest_names'])): ?>
                                <h6>Additional Guests</h6>
                                <p><?php echo str_replace(',', '<br>', htmlspecialchars($booking['guest_names'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Payment Information</h6>
                                <p>
                                    Total Amount: ₱<?php echo number_format($booking['total_price'], 2); ?><br>
                                    Payment Option: <?php echo ucfirst($booking['payment_option']); ?><br>
                                    <?php if ($booking['payment_option'] === 'downpayment'): ?>
                                        Downpayment Amount: ₱<?php echo number_format($booking['downpayment_amount'], 2); ?><br>
                                        Payment Status: <?php echo ucfirst($booking['payment_status']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <?php if ($booking['status'] !== 'cancelled'): ?>
                                    <a href="download_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <a href="cancel_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to cancel this booking?');">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
