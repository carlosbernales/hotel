<?php
require_once 'db.php';

if (!isset($_POST['booking_id'])) {
    die(json_encode(['error' => 'Booking ID is required']));
}

$booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);

// Query to get booking details with room information
$sql = "SELECT 
    b.*,
    rb.room_name,
    rb.room_price,
    rt.room_type,
    rt.description
FROM bookings b
LEFT JOIN room_bookings rb ON b.booking_id = rb.booking_id 
LEFT JOIN room_types rt ON rb.room_type_id = rt.room_type_id
WHERE b.booking_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(['error' => 'Booking not found']));
}

$booking = $result->fetch_assoc();
?>

<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Room Booking Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="font-weight-bold">Guest Information</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Guest Name</th>
                        <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td><?php echo htmlspecialchars($booking['contact']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($booking['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Number of Guests</th>
                        <td><?php echo htmlspecialchars($booking['number_of_guests']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold">Room Information</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Room Type</th>
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Room Name</th>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?php echo htmlspecialchars($booking['description']); ?></td>
                    </tr>
                    <tr>
                        <th>Room Price</th>
                        <td>₱<?php echo number_format($booking['room_price'], 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="font-weight-bold">Booking Information</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Check In</th>
                        <td><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></td>
                    </tr>
                    <tr>
                        <th>Check Out</th>
                        <td><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></td>
                    </tr>
                    <tr>
                        <th>Number of Nights</th>
                        <td><?php echo floor((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24)); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-success"><?php echo htmlspecialchars($booking['status']); ?></span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold">Payment Information</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Total Amount</th>
                        <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
                    </tr>
                    <tr>
                        <th>Payment Option</th>
                        <td><?php echo htmlspecialchars($booking['payment_option']); ?></td>
                    </tr>
                    <tr>
                        <th>Downpayment</th>
                        <td>₱<?php echo number_format($booking['downpayment_amount'], 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div> 