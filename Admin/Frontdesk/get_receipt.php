<?php
require 'db.php';

$booking_id = $_GET['booking_id'];
$query = "SELECT * FROM bookings WHERE booking_id = '$booking_id'";
$result = mysqli_query($con, $query);
$booking = mysqli_fetch_assoc($result);

// Calculate nights
$check_in = new DateTime($booking['check_in']);
$check_out = new DateTime($booking['check_out']);
$nights = $check_out->diff($check_in)->days;

?>
<div class="receipt-content">
    <div class="receipt-header text-center">
        <h3>Casa Estela Boutique Hotel</h3>
        <p>Booking Receipt</p>
        <p>Date: <?php echo date('M d, Y'); ?></p>
    </div>

    <div class="receipt-details">
        <h4>Booking Information</h4>
        <table class="table table-borderless">
            <tr>
                <td width="40%"><strong>Booking ID:</strong></td>
                <td><?php echo $booking['booking_id']; ?></td>
            </tr>
            <tr>
                <td><strong>Guest Name:</strong></td>
                <td><?php echo $booking['guest_name']; ?></td>
            </tr>
            <tr>
                <td><strong>Room Type:</strong></td>
                <td><?php echo $booking['room_type']; ?></td>
            </tr>
            <tr>
                <td><strong>Room Number:</strong></td>
                <td><?php echo isset($booking['room_number']) ? $booking['room_number'] : 'Not Assigned'; ?></td>
            </tr>
            <tr>
                <td><strong>Check-in:</strong></td>
                <td><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></td>
            </tr>
            <tr>
                <td><strong>Check-out:</strong></td>
                <td><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></td>
            </tr>
            <tr>
                <td><strong>Number of Nights:</strong></td>
                <td><?php echo $nights; ?></td>
            </tr>
            <tr>
                <td><strong>Number of Guests:</strong></td>
                <td><?php echo $booking['num_guests']; ?></td>
            </tr>
        </table>

        <h4 class="mt-4">Payment Information</h4>
        <table class="table table-borderless">
            <tr>
                <td width="40%"><strong>Total Amount:</strong></td>
                <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
            </tr>
            <?php if ($booking['payment_option'] == 'downpayment'): ?>
            <tr>
                <td><strong>Downpayment (50%):</strong></td>
                <td>₱<?php echo number_format($booking['total_amount'] * 0.5, 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td><?php echo ucfirst($booking['payment_method']); ?></td>
            </tr>
            <tr>
                <td><strong>Payment Status:</strong></td>
                <td><span class="badge badge-<?php echo $booking['payment_status'] == 'Paid' ? 'success' : 'warning'; ?>">
                    <?php echo $booking['payment_status']; ?>
                </span></td>
            </tr>
        </table>
    </div>

    <div class="receipt-total mt-4">
        <p class="text-right">
            <strong>Total Paid: ₱<?php echo number_format($booking['payment_option'] == 'downpayment' ? $booking['total_amount'] * 0.5 : $booking['total_amount'], 2); ?></strong>
            <?php if ($booking['payment_option'] == 'downpayment'): ?>
            <br>
            <strong>Balance Due: ₱<?php echo number_format($booking['total_amount'] * 0.5, 2); ?></strong>
            <?php endif; ?>
        </p>
    </div>

    <div class="receipt-footer mt-4 text-center">
        <p>Thank you for choosing Casa Estela Boutique Hotel!</p>
        <p>For inquiries, please contact us at: (contact details)</p>
    </div>
</div>

<style>
.receipt-content {
    padding: 20px;
    font-size: 14px;
}

.receipt-header {
    margin-bottom: 30px;
}

.receipt-header h3 {
    margin-bottom: 5px;
    color: #333;
}

.table-borderless td {
    padding: 5px 0;
    border: none;
}

.receipt-total {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.badge {
    padding: 5px 10px;
    font-size: 12px;
}

@media print {
    .receipt-content {
        padding: 0;
    }
}
</style>
