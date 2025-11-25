<!-- CHECKOUT MODAL -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content checkout-modal">
            <div class="modal-header checkout-header">
                <h5 class="modal-title">
                    <i class="fas fa-bookmark"></i> Complete Your Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body checkout-body">
                <form id="checkoutForm" action="../Admin/adminBackend/book_room.php" method="POST"
                    enctype="multipart/form-data">

                    <!-- Guest Information Section -->
                    <div class="section-header">
                        <i class="fas fa-user"></i> Guest Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact</label>
                            <input type="text" name="contact" class="form-control custom-input" required>
                        </div>
                    </div>

                    <!-- Booking Details Section -->
                    <div class="section-header">
                        <i class="fas fa-calendar-check"></i> Booking Details
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Check-in</label>
                            <input type="date" name="check_in" id="modal_check_in" class="form-control custom-input"
                                readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Check-out</label>
                            <input type="date" name="check_out" id="modal_check_out" class="form-control custom-input"
                                readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" name="number_of_guests" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Adults</label>
                            <input type="number" name="num_adults" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Children</label>
                            <input type="number" name="num_children" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Room Quantity</label>
                            <input type="number" name="room_quantity" id="room_quantity"
                                class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Extra Bed</label>
                            <select name="extra_bed" id="extra_bed" class="form-control custom-input">
                                <!-- PHP bed options will be inserted here -->
                            </select>
                        </div>

                        <input type="hidden" id="total_capacity" name="total_capacity">
                    </div>

                    <!-- Guest Details Section -->
                    <div class="section-header">
                        <i class="fas fa-users"></i> Guest Details
                    </div>
                    <div class="col-12 mb-4">
                        <div id="guestList" class="guest-list-container"></div>
                    </div>

                    <!-- Payment Section -->
                    <div class="section-header">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control custom-input" required>
                                <option value="gcash">GCash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="text" name="total_amount" id="total_amount"
                                class="form-control custom-input total-amount" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Discount (%)</label>
                            <input type="text" id="total_discount_percent" name="total_discount_percent"
                                class="form-control custom-input" readonly value="0%">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Discount Amount</label>
                            <input type="text" id="total_discount_amount" name="total_discount_amount"
                                class="form-control custom-input" readonly value="₱0">
                        </div>
                    </div>

                    <!-- Hidden input to store cart items as JSON -->
                    <input type="hidden" name="cart_items" id="cart_items">

                    <div class="col-12 mt-4 text-center">
                        <button type="button" class="btn btn-confirm" onclick="submitCheckout()">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Checkout Modal Styling */
    .checkout-modal {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .checkout-header {
        background: linear-gradient(135deg, #c5a572 0%, #b8935a 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: none;
    }

    .checkout-header .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkout-body {
        padding: 30px;
        background-color: #f8f9fa;
    }

    .section-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: #c5a572;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .section-header i {
        font-size: 1.2rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .custom-input {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    .custom-input:focus {
        border-color: #c5a572;
        box-shadow: 0 0 0 0.2rem rgba(197, 165, 114, 0.25);
        outline: none;
    }

    .custom-input:read-only {
        background-color: #f1f3f5;
        cursor: not-allowed;
    }

    .custom-input.total-amount {
        font-size: 1.2rem;
        font-weight: 700;
        color: #c5a572;
        background-color: #2c3e50;
        border-color: #c5a572;
    }

    .guest-list-container {
        background: white;
        border-radius: 8px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        min-height: 80px;
    }

    .btn-confirm {
        background: linear-gradient(135deg, #c5a572 0%, #b8935a 100%);
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(197, 165, 114, 0.4);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(197, 165, 114, 0.6);
        background: linear-gradient(135deg, #b8935a 0%, #c5a572 100%);
    }

    .btn-confirm:active {
        transform: translateY(0);
    }

    .btn-close-white {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .btn-close-white:hover {
        opacity: 0.8;
    }

    /* Custom scrollbar for modal */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c5a572;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #b8935a;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .checkout-body {
            padding: 20px;
        }

        .section-header {
            font-size: 1rem;
            padding: 10px 15px;
        }

        .checkout-header .modal-title {
            font-size: 1.2rem;
        }

        .btn-confirm {
            width: 100%;
            justify-content: center;
        }
    }
</style>