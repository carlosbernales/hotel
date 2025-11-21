<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query to get archived bookings
$room_sql = "SELECT *, 'room' as booking_type FROM bookings WHERE status = 'Archived' ORDER BY check_in DESC";
$table_sql = "SELECT *, 'table' as booking_type FROM table_bookings WHERE status = 'Archived' ORDER BY date DESC";
$event_sql = "SELECT *, 'event' as booking_type FROM event_bookings WHERE booking_status = 'Archived' ORDER BY booking_date DESC";

$room_result = mysqli_query($con, $room_sql);
$table_result = mysqli_query($con, $table_sql);
$event_result = mysqli_query($con, $event_sql);

if (!$room_result || !$table_result || !$event_result) {
    error_log("Error in query: " . mysqli_error($con));
    die("Error in query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Archived Bookings - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Archived Bookings</li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <!-- Room Bookings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Archived Room Bookings</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
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
                                            <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_in'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['check_out'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['num_guests']); ?></td>
                                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center">No archived room bookings found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table Bookings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Archived Table Bookings</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Number of Guests</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($table_result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($table_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                                            <td><?php echo htmlspecialchars($row['num_guests']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">No archived table bookings found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Event Bookings -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Archived Event Bookings</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Event Date</th>
                                    <th>Event Type</th>
                                    <th>Package</th>
                                    <th>Guests</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($event_result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($event_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['package_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['num_guests']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">No archived event bookings found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
