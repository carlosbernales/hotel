<?php
require_once 'db.php';
require_once 'includes/header.php';

// Check if booking ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: BookingList.php');
    exit();
}

$booking_id = intval($_GET['id']);

// Fetch booking details with room type information
$sql = "SELECT b.*, 
               rt.room_type as room_type_name, 
               rt.price as room_price,
               b.payment_proof,
               b.payment_reference
        FROM bookings b 
        LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id 
        WHERE b.booking_id = ?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    // No booking found with this ID
    header('Location: BookingList.php?error=Booking not found');
    exit();
}

$booking = mysqli_fetch_assoc($result);

// Calculate nights
$check_in = new DateTime($booking['check_in']);
$check_out = new DateTime($booking['check_out']);
$nights = $check_out->diff($check_in)->days;

// Format dates for display
$formatted_check_in = date('F j, Y', strtotime($booking['check_in']));
$formatted_check_out = date('F j, Y', strtotime($booking['check_out']));
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Booking Details</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Booking #<?php echo htmlspecialchars($booking['booking_id']); ?>
                    <span class="pull-right">
                        <a href="BookingList.php" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Bookings
                        </a>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Guest Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Name</th>
                                    <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td><?php echo htmlspecialchars($booking['contact']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?php echo htmlspecialchars($booking['address']); ?></td>
                                </tr>
                            </table>

                            <h4>Booking Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Check-in Date</th>
                                    <td><?php echo $formatted_check_in; ?></td>
                                </tr>
                                <tr>
                                    <th>Check-out Date</th>
                                    <td><?php echo $formatted_check_out; ?></td>
                                </tr>
                                <tr>
                                    <th>Number of Nights</th>
                                    <td><?php echo $nights; ?></td>
                                </tr>
                                <tr>
                                    <th>Room Type</th>
                                    <td><?php echo htmlspecialchars($booking['room_type_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="label label-<?php 
                                            echo $booking['status'] === 'confirmed' ? 'success' : 
                                                ($booking['status'] === 'pending' ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Payment Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Payment Method</th>
                                    <td><?php echo ucfirst($booking['payment_method'] ?? 'Not specified'); ?></td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        <span class="label label-<?php 
                                            echo $booking['payment_status'] === 'paid' ? 'success' : 'danger'; 
                                        ?>">
                                            <?php echo ucfirst($booking['payment_status'] ?? 'unpaid'); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Room Rate (per night)</th>
                                    <td>₱<?php echo number_format($booking['room_price'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Amount Paid</th>
                                    <td>₱<?php echo number_format($booking['downpayment_amount'] ?? 0, 2); ?></td>
                                </tr>
                                <?php if (!empty($booking['payment_reference'])): ?>
                                <tr>
                                    <th>Payment Reference</th>
                                    <td><?php echo htmlspecialchars($booking['payment_reference']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($booking['payment_proof'])): ?>
                                <tr>
                                    <th>Payment Proof</th>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#paymentProofModal">
                                            <img src="<?php echo 'uploads/payment_proofs/' . htmlspecialchars($booking['payment_proof']); ?>" 
                                                 alt="Payment Proof" 
                                                 style="max-width: 200px; max-height: 200px; cursor: pointer;">
                                        </a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                            
                            <!-- Status Update Form -->
                            <div class="well">
                                <h4>Update Status</h4>
                                <form method="POST" action="" class="form-inline">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                                    <div class="form-group">
                                        <select name="new_status" class="form-control" required>
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog" aria-labelledby="paymentProofModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="paymentProofModalLabel">Payment Proof</h4>
            </div>
            <div class="modal-body text-center">
                <?php if (!empty($booking['payment_proof'])): ?>
                    <img src="<?php echo 'uploads/payment_proofs/' . htmlspecialchars($booking['payment_proof']); ?>" 
                         alt="Payment Proof" 
                         class="img-responsive" 
                         style="max-height: 80vh; margin: 0 auto;">
                <?php else: ?>
                    <p>No payment proof available.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?php if (!empty($booking['payment_proof'])): ?>
                    <a href="<?php echo 'uploads/payment_proofs/' . htmlspecialchars($booking['payment_proof']); ?>" 
                       class="btn btn-primary" download>
                        <i class="fa fa-download"></i> Download
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// Handle status update
if (isset($_POST['update_status'])) {
    $new_status = $_POST['new_status'];
    $update_sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Booking status updated successfully!');
            window.location.href = 'view_booking.php?id=" . $booking_id . "';
        </script>";
    } else {
        echo "<script>alert('Error updating status: " . mysqli_error($con) . "');</script>";
    }
}

require_once 'includes/footer.php'; 
?>
