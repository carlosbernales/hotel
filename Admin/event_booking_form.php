<?php
include 'db.php';

if (isset($_GET['space_id'])) {
    $space_id = $_GET['space_id'];
    
    // Get event space data from database
    $stmt = $con->prepare("SELECT * FROM event_spaces WHERE id = ?");
    $stmt->bind_param("i", $space_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $space = $result->fetch_assoc();
    } else {
        echo "<script>alert('Event space not found or is no longer available.'); window.location.href='index.php?events';</script>";
        exit;
    }
    $stmt->close();
} else {
    header("Location: index.php?events");
    exit;
}
?>

<div class="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="booking-form">
                    <div class="booking-header">
                        <h3>Event Space Booking Form</h3>
                        <a href="index.php?events" class="btn btn-secondary">Back to Event Spaces</a>
                    </div>

                    <form id="eventBookingForm" method="POST" action="process_event_booking.php">
                        <input type="hidden" name="space_id" value="<?php echo $space_id; ?>">
                        <input type="hidden" name="space_type" value="<?php echo htmlspecialchars($space['space_type']); ?>">
                        <input type="hidden" name="total_amount" id="hiddenTotalAmount" value="0">
                        
                        <!-- Event Space Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Space Type</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($space['space_type']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Space Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($space['space_name']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input type="text" class="form-control" name="customer_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" name="email_address" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Number of Guests</label>
                                    <input type="number" class="form-control" name="num_guests" min="1" max="<?php echo $space['capacity']; ?>" required>
                                    <small class="text-muted">Maximum capacity: <?php echo $space['capacity']; ?> persons</small>
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Event Date</label>
                                    <input type="date" class="form-control" name="event_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Event Time</label>
                                    <select class="form-control" name="event_time" required>
                                        <option value="">Select time...</option>
                                        <option value="Morning (8:00 AM - 12:00 PM)">Morning (8:00 AM - 12:00 PM)</option>
                                        <option value="Afternoon (1:00 PM - 5:00 PM)">Afternoon (1:00 PM - 5:00 PM)</option>
                                        <option value="Evening (6:00 PM - 10:00 PM)">Evening (6:00 PM - 10:00 PM)</option>
                                        <option value="Full Day (8:00 AM - 10:00 PM)">Full Day (8:00 AM - 10:00 PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Event Type</label>
                                    <select class="form-control" name="event_type" required>
                                        <option value="">Select event type...</option>
                                        <option value="Wedding">Wedding</option>
                                        <option value="Birthday">Birthday</option>
                                        <option value="Corporate">Corporate Event</option>
                                        <option value="Conference">Conference</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Package Type</label>
                                    <select class="form-control" name="package_type" required>
                                        <option value="">Select package...</option>
                                        <option value="Basic">Basic Package</option>
                                        <option value="Standard">Standard Package</option>
                                        <option value="Premium">Premium Package</option>
                                        <option value="Custom">Custom Package</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Additional Services</label>
                            <div class="checkbox">
                                <label><input type="checkbox" name="additional_services[]" value="Catering"> Catering Service</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="additional_services[]" value="Decoration"> Decoration</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="additional_services[]" value="Sound"> Sound System</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="additional_services[]" value="Photography"> Photography</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Confirm Booking</button>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="booking-summary">
                    <h4>Booking Summary</h4>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span>Space Type:</span>
                            <span><?php echo htmlspecialchars($space['space_type']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Space Name:</span>
                            <span><?php echo htmlspecialchars($space['space_name']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Price per Hour:</span>
                            <span>₱<?php echo number_format($space['price_per_hour'], 2); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Maximum Capacity:</span>
                            <span><?php echo $space['capacity']; ?> persons</span>
                        </div>
                        <div class="summary-item total">
                            <span>Total Amount:</span>
                            <span id="totalAmount">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const pricePerHour = <?php echo $space['price_per_hour']; ?>;
    
    function updateTotal() {
        const eventTime = $('select[name="event_time"]').val();
        let hours = 0;
        
        switch(eventTime) {
            case 'Morning (8:00 AM - 12:00 PM)':
            case 'Afternoon (1:00 PM - 5:00 PM)':
                hours = 4;
                break;
            case 'Evening (6:00 PM - 10:00 PM)':
                hours = 4;
                break;
            case 'Full Day (8:00 AM - 10:00 PM)':
                hours = 14;
                break;
        }
        
        const baseTotal = hours * pricePerHour;
        let additionalCost = 0;
        
        // Add costs for additional services
        $('input[name="additional_services[]"]:checked').each(function() {
            switch($(this).val()) {
                case 'Catering':
                    additionalCost += 5000;
                    break;
                case 'Decoration':
                    additionalCost += 3000;
                    break;
                case 'Sound':
                    additionalCost += 2000;
                    break;
                case 'Photography':
                    additionalCost += 4000;
                    break;
            }
        });
        
        const total = baseTotal + additionalCost;
        $('#totalAmount').text('₱' + total.toFixed(2));
        $('#hiddenTotalAmount').val(total);
    }
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('input[name="event_date"]').attr('min', today);
    
    // Update total when relevant fields change
    $('select[name="event_time"]').change(updateTotal);
    $('input[name="additional_services[]"]').change(updateTotal);
    
    // Form validation and submission
    $('#eventBookingForm').submit(function(e) {
        e.preventDefault();
        
        const numGuests = parseInt($('input[name="num_guests"]').val());
        const maxCapacity = <?php echo $space['capacity']; ?>;
        
        if (numGuests > maxCapacity) {
            alert('Number of guests cannot exceed the maximum capacity of ' + maxCapacity + ' persons.');
            return false;
        }
        
        // Submit form using AJAX
        $.ajax({
            type: 'POST',
            url: 'process_event_booking.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = 'index.php?booking_status&booking_id=' + response.booking_id;
                } else {
                    alert('Booking failed: ' + (response.error || 'Unknown error'));
                }
            },
            error: function() {
                alert('An error occurred while processing your booking. Please try again.');
            }
        });
        
        return false;
    });
});</script>
