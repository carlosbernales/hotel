<?php
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

$today = date('Y-m-d');

// Query for expected check-outs
$query = "SELECT 
    b.booking_id,
    b.first_name,
    b.last_name,
    b.contact,
    b.email,
    b.check_in,
    b.check_out,
    b.number_of_guests,
    b.total_amount,
    b.payment_method,
    b.status,
    rt.room_type,
    DATEDIFF(b.check_out, b.check_in) as nights
FROM bookings b
LEFT JOIN room_bookings rb ON b.booking_id = rb.booking_id
LEFT JOIN room_types rt ON rb.room_type_id = rt.room_type_id
WHERE DATE(b.check_out) = '$today'
AND LOWER(b.status) IN ('confirmed', 'checked in')
ORDER BY b.check_out ASC";

// Add debug output
echo "<!-- Debug Information -->";
echo "<!-- Today's Date: $today -->";
echo "<!-- Query: $query -->";

// Get all bookings to see what's available
$debug_query = "SELECT booking_id, first_name, last_name, check_out, status 
                FROM bookings 
                WHERE check_out >= CURDATE() 
                ORDER BY check_out ASC";
$debug_result = mysqli_query($con, $debug_query);
while ($row = mysqli_fetch_assoc($debug_result)) {
    echo "<!-- Booking: ID=" . $row['booking_id'] . 
         ", Name=" . $row['first_name'] . " " . $row['last_name'] . 
         ", Check-out=" . $row['check_out'] . 
         ", Status=" . $row['status'] . " -->";
}

$result = mysqli_query($con, $query);

// Debug information
if (!$result) {
    echo "<!-- Debug: Query error: " . mysqli_error($con) . " -->";
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-home"></i></a></li>
            <li class="active">Expected Check-outs</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Expected Check-outs for <?php echo date('F d, Y'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Guest Name</th>
                                <th>Contact</th>
                                <th>Room Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Nights</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['booking_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['check_in'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['check_out'])); ?></td>
                                        <td><?php echo $row['nights']; ?></td>
                                        <td><?php echo $row['number_of_guests']; ?></td>
                                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($row['status']) == 'confirmed' ? 'success' : 'warning'; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm" onclick="viewDetails(<?php echo $row['booking_id']; ?>)">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="processCheckout(<?php echo $row['booking_id']; ?>)">
                                                <i class="fa fa-check"></i> Check-out
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No check-outs expected today</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetails(bookingId) {
    $.ajax({
        url: 'get_booking_details.php',
        type: 'POST',
        data: { booking_id: bookingId },
        success: function(response) {
            $('#bookingDetails').html(response);
            $('#detailsModal').modal('show');
        },
        error: function() {
            alert('Error fetching booking details');
        }
    });
}

function processCheckout(bookingId) {
    if (confirm('Are you sure you want to process this check-out?')) {
        $.ajax({
            url: 'process_checkout.php',
            type: 'POST',
            data: { booking_id: bookingId },
            success: function(response) {
                if (response.success) {
                    alert('Check-out processed successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error processing check-out');
            }
        });
    }
}
</script> 