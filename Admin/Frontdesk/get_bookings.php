<?php
require 'db.php';

$query = "SELECT * FROM bookings ORDER BY booking_date DESC";
$result = mysqli_query($con, $query);

while ($booking = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$booking['booking_id']}</td>";
    echo "<td>{$booking['guest_name']}</td>";
    echo "<td>{$booking['room_type']}</td>";
    echo "<td>" . date('M d, Y', strtotime($booking['check_in'])) . "</td>";
    echo "<td>" . date('M d, Y', strtotime($booking['check_out'])) . "</td>";
    echo "<td>₱" . number_format($booking['total_amount'], 2) . "</td>";
    echo "<td><span class='badge badge-" . ($booking['payment_status'] == 'Paid' ? 'success' : 'warning') . "'>{$booking['payment_status']}</span></td>";
    echo "<td>";
    echo "<button class='btn btn-sm btn-info view-receipt' data-booking-id='{$booking['booking_id']}'><i class='fa fa-receipt'></i> Receipt</button>";
    echo "</td>";
    echo "</tr>";
}
?>
