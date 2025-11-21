<!-- Advance Check In Modal -->
<div class="modal fade" id="advanceCheckInModal" tabindex="-1" role="dialog" aria-labelledby="advanceCheckInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advanceCheckInModalLabel">Check In</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="advanceCheckInForm" action="process_advance_checkin.php" method="POST">
                <div class="modal-body">
                    <div class="room-details mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="" alt="Room Image" class="room-image img-fluid rounded">
                            </div>
                            <div class="col-md-7">
                                <h4 class="room-type"></h4>
                                <p class="room-price"></p>
                                <p class="capacity"><i class="fas fa-users"></i> Max capacity: <span id="roomCapacityDisplay"></span> persons</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for room details -->
                    <input type="hidden" id="roomIdInput" name="room_type_id">
                    <input type="hidden" id="roomTypeInput" name="room_type">
                    <input type="hidden" id="roomPriceInput" name="price">
                    <input type="hidden" id="roomCapacity" name="capacity">
                    <input type="hidden" id="discountApplied" name="discount_applied" value="0">
                    <input type="hidden" id="discountPercentage" name="discount_percentage" value="0">
                    <input type="hidden" id="guestCount" name="guestCount" value="1">
                    
                    <!-- Guest Information -->
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="checkOutDate">Check Out Date</label>
                        <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required>
                    </div>
                    <div class="form-group">
                        <label for="specialRequests">Special Requests (Optional)</label>
                        <textarea class="form-control" id="specialRequests" name="specialRequests" rows="3"></textarea>
                    </div>
                    
                    <!-- Discount Selection Section -->
                    <div class="form-group">
                        <label for="discountType">Apply Discount (if applicable)</label>
                        <select class="form-control" id="discountType" name="discountType">
                            <option value="">No Discount</option>
                            <!-- Options will be filled dynamically from PHP -->
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Maya">Maya</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="paymentOption">Payment Option</label>
                        <select class="form-control" id="paymentOption" name="paymentOption" required>
                            <option value="full">Full Payment</option>
                            <option value="downpayment">Downpayment (50%)</option>
                        </select>
                    </div>
                    
                    <!-- Payment QR Code Section (Hidden by default) -->
                    <div class="payment-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="payment-method-title">Payment QR Code</h5>
                                <div class="qr-container">
                                    <div class="qr-code-wrapper text-center">
                                        <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                                    </div>
                                    <div class="payment-details">
                                        <h5>Payment Details</h5>
                                        <div class="total-amount">
                                            <span>Total Amount:</span>
                                            <span id="totalPaymentAmount">₱0.00</span>
                                        </div>
                                        <div id="discountInfo" style="display: none;" class="discount-amount">
                                            <span>Discount:</span>
                                            <span id="discountAmountDisplay">₱0.00</span>
                                        </div>
                                        <div class="payment-type">
                                            <span>Payment Type:</span>
                                            <span id="paymentTypeDisplay">Full Payment</span>
                                        </div>
                                        <div class="amount-to-pay">
                                            <span><strong>Amount to Pay:</strong></span>
                                            <span id="amountToPay"><strong>₱0.00</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="completeCheckInBtn">Complete Check In</button>
                </div>
            </form>
        </div>
    </div>
</div> 