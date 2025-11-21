<select name="payment_method" id="payment_method" required>
    <option value="">Select Payment Method</option>
    <option value="gcash">GCash</option>
    <option value="cash">Cash</option>
    <option value="card">Card</option>
    <!-- Add other payment methods as needed -->
</select> 

<form id="tableBookingForm" onsubmit="submitBooking(event)">
    <div class="form-group">
        <label for="time">Time:</label>
        <input type="time" name="time" id="time" required value="<?php echo isset($_POST['time']) ? $_POST['time'] : ''; ?>">
    </div>

    <div class="form-group">
        <label for="number_of_guests">Number of Guests:</label>
        <input type="number" name="number_of_guests" id="number_of_guests" required 
               value="<?php echo isset($_POST['number_of_guests']) ? $_POST['number_of_guests'] : ''; ?>">
    </div>

    <div class="form-group">
        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" class="form-control" required>
            <option value="">Select Payment Method</option>
            <option value="gcash">GCash</option>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
        </select>
    </div>

    <div class="form-group">
        <label for="total_amount">Total Amount:</label>
        <input type="number" name="total_amount" id="total_amount" required readonly>
    </div>

    <div class="form-group">
        <label for="amount_paid">Amount Paid:</label>
        <input type="number" name="amount_paid" id="amount_paid" required>
    </div>

    <div class="form-group">
        <label for="special_requests">Special Requests:</label>
        <textarea name="special_requests" id="special_requests"></textarea>
    </div>

    <div class="button-group">
        <button type="button" class="btn btn-secondary" onclick="closeBookingModal()">Close</button>
        <button type="submit" class="btn btn-primary">Confirm Booking</button>
    </div>
</form> 