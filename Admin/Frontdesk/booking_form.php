<?php
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    
    // Hardcoded room data
    $rooms = [
        '101' => [
            'room_type' => 'Standard Double Room',
            'price' => 1500,
            'capacity' => 2,
            'description' => 'Cozy and comfortable room with 2 single beds'
        ],
        '201' => [
            'room_type' => 'Deluxe Family Room',
            'price' => 2000,
            'capacity' => 4,
            'description' => 'Spacious room with 1 queen bed and 1 single bed'
        ],
        '301' => [
            'room_type' => 'Family Room',
            'price' => 2500,
            'capacity' => 5,
            'description' => 'Our largest room with 1 queen bed and 2 single beds'
        ]
    ];
    
    if (isset($rooms[$room_id])) {
        $room = $rooms[$room_id];
    } else {
        echo "<script>alert('Room not found or is no longer available.'); window.location.href='index.php?reservation';</script>";
        exit;
    }
} else {
    header("Location: index.php?reservation");
    exit;
}
?>

<div class="panel panel-default">
    <div class="panel-heading">Room Reservation
        <a class="btn btn-secondary pull-right" href="index.php?reservation">Back to Rooms</a>
    </div>
    <div class="panel-body">
        <form id="bookingForm" method="POST" action="process_booking.php">
            <!-- Room Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Room Type</label>
                        <input type="text" class="form-control" value="<?php echo $room['room_type']; ?>" readonly>
                        <input type="hidden" name="room_type" value="<?php echo $room['room_type']; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Room Number</label>
                        <input type="text" class="form-control" value="<?php echo $room_id; ?>" readonly>
                        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
                    </div>
                </div>
            </div>

            <!-- Guest Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Guest Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label></label>
                        <input type="hidden" class="form-control" name="booking_type" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Number of Guests</label>
                        <input type="number" class="form-control" name="guests" min="1" max="<?php echo $room['capacity']; ?>" value="1" required>
                        <small class="form-text text-muted">Maximum capacity: <?php echo $room['capacity']; ?> guests</small>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Check-in Date</label>
                        <input type="date" class="form-control" name="check_in" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Check-out Date</label>
                        <input type="date" class="form-control" name="check_out" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Payment Option</label>
                        <select class="form-control" name="payment_option" required>
                            <option value="full">Full Payment</option>
                            <option value="downpayment">Downpayment (₱1,500)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Price Summary</h4>
                            <p class="card-text">Price per night: ₱<span id="price_per_night"><?php echo number_format($room['price'], 2); ?></span></p>
                            <p class="card-text">Number of nights: <span id="total_nights">0</span></p>
                            <p class="card-text">Total amount: ₱<span id="total_amount">0.00</span></p>
                            <input type="hidden" name="total_amount" id="total_amount_input" value="0.00">
                            <p class="card-text" id="downpayment_text" style="display: none;">Downpayment (₱1,500): ₱<span id="downpayment_amount">0.00</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Book Now</button>
                        <a href="index.php?reservation" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="room-card">
    <button class="btn btn-warning add-to-list" 
        onclick="addToCart(
            '<?php echo $room['room_id']; ?>', 
            '<?php echo $room['room_type']; ?>', 
            <?php echo $room['price']; ?>, 
            '<?php echo $room['capacity']; ?>'
        )">
        <i class="fas fa-cart-plus"></i> Add to List
    </button>
</div>

<script>
$(document).ready(function() {
    // Calculate total amount when dates change
    function calculateTotal() {
        var checkIn = new Date($('input[name="check_in"]').val());
        var checkOut = new Date($('input[name="check_out"]').val());
        var pricePerNight = <?php echo $room['price']; ?>;
        
        if (checkIn && checkOut && checkOut > checkIn) {
            var nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            var total = nights * pricePerNight;
            var remainingBalance = 0;
            
            $('#total_nights').text(nights);
            $('#total_amount').text(total.toFixed(2));
            $('#total_amount_input').val(total.toFixed(2));
            
            if ($('select[name="payment_option"]').val() === 'downpayment') {
                var downpayment = 1500;
                remainingBalance = total - downpayment;
                $('#downpayment_amount').text(downpayment.toFixed(2));
                $('#downpayment_text').show();
                // Add remaining balance display
                if (!$('#remaining_balance_text').length) {
                    $('#downpayment_text').after('<p class="card-text" id="remaining_balance_text">Remaining Balance: ₱<span id="remaining_balance">0.00</span></p>');
                }
                $('#remaining_balance').text(remainingBalance.toFixed(2));
                $('#remaining_balance_text').show();
            } else {
                $('#downpayment_text').hide();
                $('#remaining_balance_text').hide();
            }
        }
    }

    // Calculate total when dates change
    $('input[name="check_in"], input[name="check_out"]').change(calculateTotal);
    
    // Show/hide downpayment when payment option changes
    $('select[name="payment_option"]').change(calculateTotal);
    
    // Initialize datepicker with minimum dates
    var today = new Date().toISOString().split('T')[0];
    $('input[name="check_in"]').attr('min', today);
    $('input[name="check_out"]').attr('min', today);
    
    // Ensure check-out is after check-in
    $('input[name="check_in"]').change(function() {
        $('input[name="check_out"]').attr('min', $(this).val());
    });

    // Handle form submission
    $('#bookingForm').submit(function(e) {
        e.preventDefault();
        
        // Create FormData object
        var form = $(this)[0];
        var formData = new FormData(form);
        
        // Log form data
        console.log('Form data entries:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Convert FormData to URL-encoded string
        var serializedData = $(this).serialize();
        console.log('Serialized data:', serializedData);
        
        $.ajax({
            url: 'process_booking.php',
            type: 'POST',
            data: serializedData,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                if (response.success) {
                    // Show confirmation modal
                    $('#getCustomerName').text($('input[name="name"]').val());
                    $('#getRoomType').text($('input[name="room_type"]').val());
                    $('#getRoomNo').text($('input[name="room_id"]').val());
                    $('#getCheckIn').text($('input[name="check_in"]').val());
                    $('#getCheckOut').text($('input[name="check_out"]').val());
                    $('#getTotalPrice').text('₱' + $('#total_amount').text());
                    $('#getPaymentStaus').text($('select[name="payment_option"]').val() === 'full' ? 'Full Payment' : '₱1,500 Down Payment');
                    
                    $('#bookingConfirm').modal('show');
                } else {
                    console.error('Booking error:', response.error);
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                alert('Error processing booking. Please try again.');
            }
        });
    });
});
</script>
