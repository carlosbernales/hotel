<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Test form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Check-in</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Test Check-in Form</h2>
    <form id="testCheckInForm" class="mt-4">
        <div class="mb-3">
            <label>First Name:</label>
            <input type="text" name="firstName" class="form-control" value="Test" required>
        </div>
        <div class="mb-3">
            <label>Last Name:</label>
            <input type="text" name="lastName" class="form-control" value="User" required>
        </div>
        <div class="mb-3">
            <label>Contact Number:</label>
            <input type="text" name="contactNumber" class="form-control" value="1234567890" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="test@example.com" required>
        </div>
        <div class="mb-3">
            <label>Room Type:</label>
            <input type="text" name="room_type" class="form-control" value="Double Occupancy" required>
        </div>
        <div class="mb-3">
            <label>Check Out Date:</label>
            <input type="date" name="checkOutDate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Number of Guests:</label>
            <input type="number" name="guestCount" class="form-control" value="2" required>
        </div>
        <div class="mb-3">
            <label>Payment Method:</label>
            <select name="paymentMethod" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="GCash">GCash</option>
                <option value="Card">Card</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Payment Option:</label>
            <select name="paymentOption" class="form-control" required>
                <option value="full">Full Payment</option>
                <option value="downpayment">Downpayment</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Price:</label>
            <input type="number" name="price" class="form-control" value="3200" required>
        </div>
        <button type="submit" class="btn btn-primary">Test Check-in</button>
    </form>

    <div id="response" class="mt-4"></div>

    <script>
    $(document).ready(function() {
        // Set default check-out date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        $('input[name="checkOutDate"]').val(tomorrow.toISOString().split('T')[0]);

        $('#testCheckInForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            // Add current date as check-in date
            formData.append('checkInDate', new Date().toISOString().split('T')[0]);
            
            // Calculate nights
            const checkOutDate = new Date(formData.get('checkOutDate'));
            const today = new Date();
            const nights = Math.ceil((checkOutDate - today) / (1000 * 60 * 60 * 24));
            formData.append('nights', nights);
            
            // Calculate total amount
            const price = parseFloat(formData.get('price'));
            const totalAmount = price * nights;
            formData.append('totalAmount', totalAmount);
            
            // Debug log
            console.log('Form data being sent:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Send request
            $.ajax({
                url: 'process_advance_checkin.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);
                    $('#response').html(`
                        <div class="alert alert-${response.success ? 'success' : 'danger'}">
                            <h4>${response.success ? 'Success!' : 'Error'}</h4>
                            <p>${response.message}</p>
                            ${response.data ? '<pre>' + JSON.stringify(response.data, null, 2) + '</pre>' : ''}
                        </div>
                    `);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.log('Server response:', xhr.responseText);
                    $('#response').html(`
                        <div class="alert alert-danger">
                            <h4>Error</h4>
                            <p>Failed to process check-in. Please try again.</p>
                            <pre>${error}</pre>
                        </div>
                    `);
                }
            });
        });
    });
    </script>
</body>
</html> 