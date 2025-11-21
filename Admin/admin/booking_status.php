<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px;">
                                <strong>Reference Number:</strong> <span id="paymentReference"></span>
                            </div>
                            <div style="border: 1px solid #ddd; padding: 10px; text-align: center;">
                                <img id="paymentProofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto; display: none;">
                                <div id="noProofMessage" style="padding: 20px; color: #666;">
                                    No payment proof available
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Booking Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Booking ID:</th>
                                <td><span id="bookingId"></span></td>
                            </tr>
                            <tr>
                                <th>Check In:</th>
                                <td><span id="checkIn"></span></td>
                            </tr>
                            <tr>
                                <th>Check Out:</th>
                                <td><span id="checkOut"></span></td>
                            </tr>
                            <tr>
                                <th>Nights:</th>
                                <td><span id="nights"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Room Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Room Type:</th>
                                <td><span id="roomType"></span></td>
                            </tr>
                            <tr>
                                <th>Room Price:</th>
                                <td>₱<span id="roomPrice"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Total Amount:</th>
                                <td>₱<span id="totalAmount"></span></td>
                            </tr>
                            <tr>
                                <th>Payment Option:</th>
                                <td><span id="paymentOption"></span></td>
                            </tr>
                            <tr>
                                <th>Amount Paid:</th>
                                <td>₱<span id="amountPaid"></span></td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td><span id="paymentMethod"></span></td>
                            </tr>
                            <tr>
                                <th>Discount Type:</th>
                                <td><span id="discountType"></span></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><span id="status"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to handle view button clicks
function handleViewButtonClick(bookingId) {
    // Show loading state
    $('#bookingDetailsModal').modal('show');
    $('#paymentProofImage').hide();
    $('#noProofMessage').show().text('Loading...');
    
    // Fetch booking details
    fetch('fetch_booking_details.php?booking_id=' + bookingId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const bookingData = data.data;
                
                // Update modal content
                $('#bookingId').text(bookingData.booking_id);
                $('#checkIn').text(bookingData.check_in);
                $('#checkOut').text(bookingData.check_out);
                $('#nights').text(bookingData.nights);
                $('#roomType').text(bookingData.room_type);
                $('#roomPrice').text(parseFloat(bookingData.room_price).toLocaleString());
                $('#totalAmount').text(parseFloat(bookingData.total_amount).toLocaleString());
                $('#paymentOption').text(bookingData.payment_option);
                $('#amountPaid').text(parseFloat(bookingData.amount_paid).toLocaleString());
                $('#paymentMethod').text(bookingData.payment_method);
                $('#discountType').text(bookingData.discount_type);
                $('#status').text(bookingData.status);
                $('#paymentReference').text(bookingData.payment_reference || 'N/A');

                // Handle payment proof display
                const paymentProofImg = $('#paymentProofImage');
                const noProofMsg = $('#noProofMessage');
                
                if (bookingData.payment_proof) {
                    paymentProofImg
                        .attr('src', bookingData.payment_proof)
                        .on('load', function() {
                            $(this).show();
                            noProofMsg.hide();
                        })
                        .on('error', function() {
                            $(this).hide();
                            noProofMsg.show().text('Error loading payment proof image');
                        });
                } else {
                    paymentProofImg.hide();
                    noProofMsg.show().text('No payment proof available');
                }
            } else {
                console.error('Error fetching booking details:', data.message);
                $('#noProofMessage').text('Error loading booking details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#noProofMessage').text('Error loading booking details');
        });
}

// Attach click handler to view buttons
$(document).on('click', '.view-booking', function() {
    const bookingId = $(this).data('booking-id');
    handleViewButtonClick(bookingId);
});
</script> 