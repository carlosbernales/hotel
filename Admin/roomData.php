<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch room bookings
$room_sql = "SELECT * FROM bookings ORDER BY booking_date DESC";
$room_result = mysqli_query($con, $room_sql);

if (!$room_result) {
    die("Error in room query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Bookings - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <h2>Room Bookings</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Guest Name</th>
                    <th>Room Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Guests</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($room_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($room_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['check_in'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['check_out'])); ?></td>
                            <td><?php echo htmlspecialchars($row['num_guests']); ?></td>
                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">No room bookings found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
