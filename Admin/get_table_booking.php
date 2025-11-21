<?php
include 'db.php';
include 'auth_check.php';

if(isset($_POST['id'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    
    $query = "SELECT * FROM table_bookings WHERE id = $id";
    $result = mysqli_query($con, $query);
    
    if($result && $row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="booking-details">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Booking ID:</strong> <?php echo $row['booking_id']; ?></p>
                    <p><strong>Customer Name:</strong> <?php echo $row['customer_name']; ?></p>
                    <p><strong>Package:</strong> <?php echo $row['package_name']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($row['booking_date'])); ?></p>
                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($row['booking_time'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Number of Guests:</strong> <?php echo $row['num_guests']; ?></p>
                    <p><strong>Contact Number:</strong> <?php echo $row['contact_number']; ?></p>
                    <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                    <p><strong>Total Amount:</strong> ₱<?php echo number_format($row['total_amount'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
                </div>
            </div>
            <?php if($row['special_requests']) { ?>
            <div class="row mt-3">
                <div class="col-12">
                    <p><strong>Special Requests:</strong></p>
                    <p><?php echo nl2br($row['special_requests']); ?></p>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Booking not found</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request</div>';
}
?>
