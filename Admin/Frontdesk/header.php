<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Casa Estela Boutique Hotel & Cafe</title>
    
    <!-- Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/reservation.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css" rel="stylesheet">

    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- jQuery and DataTables JS -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/dropdown.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Critical styles for immediate loading */
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            padding-top: 60px; /* Add padding to account for fixed header */
        }
        
        .form-control {
            height: 50px !important;
            font-size: 16px !important;
            padding: 10px 15px !important;
        }
        
        .sidebar {
            background: #333 !important;
            position: fixed;
            top: 60px; /* Start below the header */
            left: 0;
            bottom: 0;
            width: 250px;
            z-index: 999;
        }
        
        .sidebar ul.nav li a {
            color: #fff !important;
            font-size: 15px !important;
            padding: 12px 20px !important;
        }
        
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #DAA520;
            color: #fff;
            padding: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-brand {
            margin-left: 270px; /* Adjusted to align with sidebar */
            font-size: 20px;
            font-weight: 600;
            padding: 0 15px;
            flex-grow: 1;
            text-align: left;
            white-space: nowrap;
        }

        .header-icons {
            display: flex;
            align-items: center;
            padding-right: 30px;
            gap: 20px;
        }

        .header-icons a {
            color: #fff;
            text-decoration: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .header-icons a:hover {
            color: #f8f9fa;
        }

        .header-icons i {
            font-size: 20px;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #fff;
            color: #DAA520;
            font-size: 11px;
            padding: 3px 5px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        /* Improve form readability */
        label {
            font-size: 16px !important;
            font-weight: 500 !important;
            color: #333 !important;
            margin-bottom: 8px !important;
        }
        
        input, select, textarea {
            font-size: 16px !important;
            color: #333 !important;
        }
        
        /* Ensure buttons are readable */
        .btn {
            font-size: 16px !important;
            padding: 12px 25px !important;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #fff;
            color: #DAA520;
            font-size: 11px;
            padding: 3px 5px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .fa {
            font-size: 18px;
        }

        /* Room list modal styles */
        .room-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .room-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .room-item:last-child {
            border-bottom: none;
        }

        #roomListModal .modal-content {
            border-radius: 8px;
        }

        #roomListModal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .total-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .selected-room-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .selected-room-item:last-child {
            border-bottom: none;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .total-amount {
            font-size: 1.2em;
            color: #28a745;
        }

        #bookingFormModal .modal-dialog {
            max-width: 800px;
        }

        #bookingFormModal .form-control {
            height: 45px;
        }

        #bookingFormModal label {
            font-weight: 500;
        }

        .booking-summary {
            margin-top: 20px;
        }

        .room-summary-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .room-summary-item h6 {
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        .guest-list {
            padding-left: 15px;
        }

        .guest-list div {
            margin-bottom: 5px;
            color: #666;
        }

        #bookingSummary .table th {
            width: 30%;
            background-color: #f8f9fa;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 8px;
        }

        .modal-body.scrollable-content {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Selected rooms section */
        .selected-rooms {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .room-item {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Price summary section */
        .price-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .summary-item {
            margin-bottom: 10px;
        }

        .total-section, .downpayment-section {
            padding-top: 10px;
            margin-top: 10px;
            border-top: 1px solid #dee2e6;
        }

        /* Form controls */
        .form-control {
            height: 45px;
            border-radius: 4px;
        }

        /* Buttons */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        /* Add these to your existing styles */
        .room-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .room-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .total-amount, .downpayment {
            font-weight: bold;
            color: #28a745;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin-right: 15px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details h5 {
            margin: 0 0 10px;
        }

        .cart-item-details p {
            margin: 0 0 5px;
            color: #666;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: right;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            font-size: 20px;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin-right: 15px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details h5 {
            margin: 0 0 10px;
        }

        .cart-item-details p {
            margin: 0 0 5px;
            color: #666;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: right;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            font-size: 20px;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .navbar-custom {
            background-color: #D4AF37;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icons a {
            color: white;
            font-size: 18px;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .profile-dropdown {
            display: inline-block;
        }

        .profile-dropdown .dropdown-menu {
            min-width: 200px;
            padding: 10px 0;
            margin-top: 10px;
            right: 0;
            left: auto;
        }

        .profile-dropdown .dropdown-menu li a {
            color: #333;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-dropdown .dropdown-menu li a:hover {
            background-color: #f8f9fa;
            color: #007bff;
            text-decoration: none;
        }

        .profile-dropdown .dropdown-menu .divider {
            margin: 5px 0;
            border-top: 1px solid #eee;
        }

        .dropdown-toggle::after {
            display: none;
        }

        /* Add margin to main content to prevent header overlap */
        .main {
            margin-left: 250px;
            padding: 20px;
            margin-top: 60px; /* Add margin to account for fixed header */
        }

        @media (max-width: 768px) {
            .header-brand {
                margin-left: 15px;
                font-size: 16px;
            }
            
            .main {
                margin-left: 0;
            }
            
            .sidebar {
                display: none;
            }
        }

        /* Style for discount display */
        .summary-item {
            margin-bottom: 10px;
        }
        
        .discount-display {
            color: #28a745;
            font-weight: 500;
            padding: 8px 0;
            margin: 8px 0;
            border-top: 1px dashed #dee2e6;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .room-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="header-brand">
            CASA ESTELA BOUTIQUE HOTEL & CAFE
        </div>
        <div class="header-icons">
            <a href="#" title="Messages">
                <i class="fas fa-envelope"></i>
                <span class="badge">0</span>
            </a>
            <a href="#" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">0</span>
            </a>
            <a href="#" title="User Profile">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>

    <!-- Only one header -->
    <div class="header fixed-top">
        <div class="navbar navbar-custom">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">CASA ESTELA BOUTIQUE HOTEL & CAFE</a>
            </div>
            <div class="navbar-right">
                <div class="header-icons">
                    <?php 
                    // Get current URL path
                    $current_url = $_SERVER['REQUEST_URI'];
                    
                    // Show cart only on reservation page
                    if (strpos($current_url, 'index.php?reservation') !== false): 
                    ?>
                        <a href="#" class="cart-icon" onclick="showCart()">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="cart-count"></span>
                        </a>
                    <?php endif; ?>

                    <a href="message.php" class="message-icon">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <a href="notification.php" class="notification-icon">
                        <i class="fa fa-bell"></i>
                    </a>
                    <div class="dropdown profile-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="my_profile.php"><i class="fa fa-user-circle"></i> My Profile</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Selected Rooms</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="cartItems">
                        <!-- Cart items will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
                    <button type="button" class="btn btn-warning" onclick="proceedToBooking()">Book Selected Rooms</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Room List Modal -->
    <div class="modal fade" id="roomListModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Room List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="roomListContent">
                        <!-- Room list will be populated here -->
                    </div>
                    <div class="total-section" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 id="totalAmount">₱0</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="clearList">Clear List</button>
                    <button type="button" class="btn btn-warning" id="bookNow" disabled>Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Multiple Rooms Modal -->
    <div class="modal fade" id="bookingFormModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Multiple Rooms</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-warning mb-3" onclick="backToCart()">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </button>

                    <form id="multipleBookingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="firstName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="lastName" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact</label>
                                    <input type="tel" class="form-control" name="contact" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Check-in</label>
                                    <input type="date" class="form-control" name="checkIn" required 
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Check-out</label>
                                    <input type="date" class="form-control" name="checkOut" required 
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Discount Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Apply Discount (if applicable)</label>
                                    <select class="form-control" name="discountType" id="multipleDiscountType">
                                        <option value="">No Discount</option>
                                        <?php
                                        // Check if discount_types table exists and fetch active discount types
                                        $table_check = mysqli_query($con, "SHOW TABLES LIKE 'discount_types'");
                                        if ($table_check && mysqli_num_rows($table_check) > 0) {
                                            $discount_query = "SELECT * FROM discount_types WHERE is_active = 1 ORDER BY name ASC";
                                            $discount_result = mysqli_query($con, $discount_query);
                                            
                                            if ($discount_result && mysqli_num_rows($discount_result) > 0) {
                                                while ($discount = mysqli_fetch_assoc($discount_result)) {
                                                    echo '<option value="' . htmlspecialchars($discount['name']) . '" 
                                                        data-percentage="' . htmlspecialchars($discount['percentage']) . '">' 
                                                        . htmlspecialchars(ucfirst($discount['name'])) . ' (' 
                                                        . htmlspecialchars($discount['percentage']) . '%)</option>';
                                                }
                                            }
                                        } else {
                                            // Fallback to default options if table doesn't exist
                                            echo '<option value="senior">Senior Citizen (10%)</option>';
                                            echo '<option value="pwd">PWD (10%)</option>';
                                            echo '<option value="student">Student (10%)</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select class="form-control" name="paymentMethod" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="Cash">Cash</option>
                                        <option value="GCash">GCash</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Option</label>
                                    <select class="form-control" name="paymentOption" required>
                                        <option value="">Select Payment Option</option>
                                        <option value="full">Full Payment</option>
                                        <option value="downpayment">Downpayment (₱1,500)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="selected-rooms mt-4">
                            <h6>Selected Rooms:</h6>
                            <div id="selectedRoomsList">
                                <!-- Room items with guest count inputs will be populated here -->
                            </div>
                        </div>

                        <div class="price-summary mt-4">
                            <h6>Price Summary</h6>
                            <div id="priceSummary"></div>
                            <div class="total-section">
                                <strong>Total Amount:</strong> <span id="bookingTotalAmount">₱0.00</span>
                            </div>
                            <div class="downpayment-section" style="display: none;">
                                <strong>Downpayment (₱1,500):</strong> <span id="downpaymentAmount">₱1,500</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="backToCart()">Back</button>
                    <button type="button" class="btn btn-warning" onclick="confirmBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Summary Modal -->
    <div class="modal fade" id="bookingSummaryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Summary</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="guest-info mb-4">
                        <h6>Guest Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <span id="summaryName"></span></p>
                                <p><strong>Contact:</strong> <span id="summaryContact"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <span id="summaryEmail"></span></p>
                                <p><strong>Payment Method:</strong> <span id="summaryPaymentMethod"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Check-in:</strong> <span id="summaryCheckIn"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Check-out:</strong> <span id="summaryCheckOut"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="selected-rooms mb-4">
                        <h6>Room Details</h6>
                        <div id="summaryRoomsList"></div>
                    </div>

                    <div class="price-summary">
                        <h6>Price Summary</h6>
                        <div id="summaryPriceDetails"></div>
                        <div class="total-section">
                            <strong>Total Amount:</strong> <span id="summaryTotalAmount">₱0.00</span>
                        </div>
                        <div class="downpayment-section" style="display: none;">
                            <strong>Downpayment (₱1,500):</strong> <span id="summaryDownpayment">₱1,500</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="editBooking()">Edit</button>
                    <button type="button" class="btn btn-warning" onclick="processBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookingSummary" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Summary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Please review your booking details before confirming.
                    </div>
                    
                    <div class="booking-summary">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td id="summaryCustomerName"></td>
                                </tr>
                                <tr>
                                    <th>Check-in:</th>
                                    <td id="summaryCheckIn"></td>
                                </tr>
                                <tr>
                                    <th>Check-out:</th>
                                    <td id="summaryCheckOut"></td>
                                </tr>
                                <tr>
                                    <th>Number of Nights:</th>
                                    <td id="summaryNights"></td>
                                </tr>
                                <tr>
                                    <th>Total Guests:</th>
                                    <td id="summaryTotalGuests"></td>
                                </tr>
                                <tr>
                                    <th>Room Details:</th>
                                    <td id="summaryRoomDetails"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td id="summaryPaymentMethod"></td>
                                </tr>
                                <tr>
                                    <th>Payment Option:</th>
                                    <td id="summaryPaymentOption"></td>
                                </tr>
                                <tr id="summaryDiscountRow" style="display:none;" class="table-info">
                                    <th>Discount Applied:</th>
                                    <td id="summaryDiscountInfo"></td>
                                </tr>
                                <tr id="summaryOriginalAmountRow" style="display:none;">
                                    <th>Original Amount:</th>
                                    <td id="summaryOriginalAmountValue"></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Total Amount:</th>
                                    <td id="summaryTotalAmount"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-success" onclick="processBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookingConfirm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Thank you for your reservation!</p>
                    <p>Customer Name: <span id="getCustomerName"></span></p>
                    <p>Check-in: <span id="getCheckIn"></span></p>
                    <p>Check-out: <span id="getCheckOut"></span></p>
                    <p>Total Price: <span id="getTotalPrice"></span></p>
                    <p>Payment Status: <span id="getPaymentStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center"><b>Room Booking Confirmation</b></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert bg-success alert-dismissable" role="alert">
                                <em class="fa fa-lg fa-check-circle"></em>&nbsp; Room Successfully Booked
                            </div>
                            <table class="table table-striped table-bordered table-responsive">
                                <tbody>
                                    <tr>
                                        <td><b>Booking ID</b></td>
                                        <td id="getBookingId"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Customer Name</b></td>
                                        <td id="getCustomerName"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Check-In</b></td>
                                        <td id="getCheckIn"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Check-Out</b></td>
                                        <td id="getCheckOut"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Number of Nights</b></td>
                                        <td id="getNumberOfNights"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Guests</b></td>
                                        <td id="getTotalGuests"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Room Details</b></td>
                                        <td id="getRoomDetails"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Amount</b></td>
                                        <td id="getTotalPrice"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Payment Status</b></td>
                                        <td id="getPaymentStatus"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" style="border-radius: 60px;" onclick="window.location.reload()">
                        <i class="fa fa-check-circle"></i> OK
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookingSummary" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Summary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Please review your booking details before confirming.
                    </div>
                    
                    <div class="booking-summary">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td id="summaryCustomerName"></td>
                                </tr>
                                <tr>
                                    <th>Check-in:</th>
                                    <td id="summaryCheckIn"></td>
                                </tr>
                                <tr>
                                    <th>Check-out:</th>
                                    <td id="summaryCheckOut"></td>
                                </tr>
                                <tr>
                                    <th>Number of Nights:</th>
                                    <td id="summaryNights"></td>
                                </tr>
                                <tr>
                                    <th>Total Guests:</th>
                                    <td id="summaryTotalGuests"></td>
                                </tr>
                                <tr>
                                    <th>Room Details:</th>
                                    <td id="summaryRoomDetails"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td id="summaryPaymentMethod"></td>
                                </tr>
                                <tr>
                                    <th>Payment Option:</th>
                                    <td id="summaryPaymentOption"></td>
                                </tr>
                                <tr id="summaryDiscountRow" style="display:none;" class="table-info">
                                    <th>Discount Applied:</th>
                                    <td id="summaryDiscountInfo"></td>
                                </tr>
                                <tr id="summaryOriginalAmountRow" style="display:none;">
                                    <th>Original Amount:</th>
                                    <td id="summaryOriginalAmountValue"></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Total Amount:</th>
                                    <td id="summaryTotalAmount"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-success" onclick="processBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add to List functionality
    function addToList(room) {
        let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        roomList.push(room);
        localStorage.setItem('roomList', JSON.stringify(roomList));
        updateCartCount();
        
        // Show success message
        alert('Room added to list successfully!');
    }

    // Update cart count
    function updateCartCount() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        $('.cart-count').text(roomList.length);
    }

    // Show room list modal
    function showRoomList() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        const container = $('#selectedRoomsList');
        container.empty();

        roomList.forEach((room, index) => {
            container.append(`
                <div class="room-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${room.type}</strong><br>
                            ${room.beds}<br>
                            ₱${parseFloat(room.price).toFixed(2)} per night
                        </div>
                        <div class="form-group">
                            <label>Number of Guests</label>
                            <input type="number" class="form-control" name="guests_${index}" 
                                   min="1" max="${room.maxCapacity}" required>
                        </div>
                    </div>
                    <div id="guestNames_${index}"></div>
                </div>
            `);

            // Add guest name fields when number of guests changes
            $(`input[name="guests_${index}"]`).on('change', function() {
                const count = parseInt($(this).val());
                const container = $(`#guestNames_${index}`);
                container.empty();
                
                for (let i = 0; i < count; i++) {
                    container.append(`
                        <div class="form-group">
                            <label>Guest ${i + 1} Name</label>
                            <input type="text" class="form-control" name="guest_${index}_${i}" required>
                        </div>
                    `);
                }
                updateTotalAmount();
            });
        });

        updateTotalAmount();
        $('#bookingFormModal').modal('show');
    }

    // Update total amount
    function updateTotalAmount() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        let originalTotal = 0;
        
        roomList.forEach(room => {
            originalTotal += parseFloat(room.price);
        });
        
        // Get discount selection
        const discountSelect = document.getElementById('multipleDiscountType');
        const discountType = discountSelect ? discountSelect.value : '';
        let finalTotal = originalTotal;
        let discountAmount = 0;
        let discountPercentage = 0;
        
        // Get discount percentage from selected option if a discount is selected
        if (discountType && discountSelect) {
            const selectedOption = discountSelect.options[discountSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.percentage) {
                // Get percentage from data attribute
                discountPercentage = parseFloat(selectedOption.dataset.percentage);
            } else {
                // Default to 10% if data attribute is not available
                discountPercentage = 10;
            }
            
            // Calculate discount amount
            discountAmount = originalTotal * (discountPercentage / 100);
            finalTotal = originalTotal - discountAmount;
        }
        
        // Display original amount if discount is applied
        if (discountAmount > 0) {
            // Add the original and discounted prices to the DOM
            $('#bookingTotalAmount').html(`<span style="color: #28a745;">₱${finalTotal.toFixed(2)}</span> <small>(After ${discountPercentage}% ${discountType} discount)</small>`);
            $('#downpaymentAmount').text('₱' + (finalTotal * 0.5).toFixed(2));
        } else {
            // Just show the total if no discount
            $('#bookingTotalAmount').text('₱' + originalTotal.toFixed(2));
            $('#downpaymentAmount').text('₱' + (originalTotal * 0.5).toFixed(2));
        }
        
        return {
            originalTotal: originalTotal,
            finalTotal: finalTotal,
            discountAmount: discountAmount,
            discountType: discountType,
            discountPercentage: discountPercentage
        };
    }

    function showBookingForm() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        const container = $('#selectedRoomsList');
        container.empty();

        roomList.forEach((room, index) => {
            container.append(`
                <div class="room-item mb-3">
                    <h6>${room.type}</h6>
                    <div class="form-group">
                        <label>Number of Guests</label>
                        <input type="number" class="form-control" name="guests_${index}" 
                               min="1" max="${room.maxCapacity}" required>
                    </div>
                    <div id="guestNames_${index}"></div>
                </div>
            `);

            // Add guest name fields when number of guests changes
            $(`input[name="guests_${index}"]`).on('change', function() {
                const count = parseInt($(this).val());
                const container = $(`#guestNames_${index}`);
                container.empty();
                
                for (let i = 0; i < count; i++) {
                    container.append(`
                        <div class="form-group">
                            <label>Guest ${i + 1} Name</label>
                            <input type="text" class="form-control" name="guest_${index}_${i}" required>
                        </div>
                    `);
                }
                updateTotalAmount();
            });
        });

        updateTotalAmount();
        $('#bookingFormModal').modal('show');
    }

    function showBookingSummary() {
        // Collect all guest information
        const guestInfo = [];
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        
        roomList.forEach((room, index) => {
            const guestCount = $(`input[name="guests_${index}"]`).val();
            const guests = [];
            
            for (let i = 0; i < guestCount; i++) {
                guests.push($(`input[name="guest_${index}_${i}"]`).val());
            }
            
            guestInfo.push({
                room: room,
                guests: guests
            });
        });

        // Update summary modal
        const container = $('#summaryRoomsList');
        container.empty();
        
        guestInfo.forEach(info => {
            container.append(`
                <div class="room-summary mb-3">
                    <h6>${info.room.type}</h6>
                    <div>Guests: ${info.guests.join(', ')}</div>
                    <div>Price: ₱${parseFloat(info.room.price).toFixed(2)}</div>
                </div>
            `);
        });

        $('#summaryTotalAmount').text($('#totalAmount').text());
        
        // Calculate number of nights for table modal
        const checkInDate = new Date($('input[name="checkIn"]').val());
        const checkOutDate = new Date($('input[name="checkOut"]').val());
        const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
        $('#summaryNights').text(nights);
        
        // Count total guests and prepare room details string
        let totalGuests = 0;
        let roomDetails = '';
        
        roomList.forEach((room, index) => {
            // For each room, we assume at least 1 guest, could be refined if guest count is stored
            const guestCount = 1; // Replace with actual guest count if available
            totalGuests += guestCount;
            roomDetails += `${room.type} (₱${parseFloat(room.price).toFixed(2)}/night)\n`;
        });
        
        // Update total guests and room details in the summary
        $('#summaryTotalGuests').text(totalGuests);
        $('#summaryRoomDetails').html(roomDetails.replace(/\n/g, '<br>'));
        
        $('#bookingFormModal').modal('hide');
        $('#bookingSummaryModal').modal('show');
    }

    function editBooking() {
        $('#bookingSummaryModal').modal('hide');
        $('#bookingFormModal').modal('show');
    }

    function confirmBooking() {
        // Get form data
        const form = document.getElementById('multipleBookingForm');
        
        // Check if the form is valid
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get price calculations including discount
        const priceInfo = updateTotalAmount();
        
        // Collect guest information
        const firstName = $('input[name="firstName"]').val();
        const lastName = $('input[name="lastName"]').val();
        const fullName = firstName + ' ' + lastName;
        const contact = $('input[name="contact"]').val();
        const email = $('input[name="email"]').val();
        const checkIn = $('input[name="checkIn"]').val();
        const checkOut = $('input[name="checkOut"]').val();
        const paymentMethod = $('select[name="paymentMethod"]').val();
        const paymentOption = $('select[name="paymentOption"]').val();
        const discountType = $('select[name="discountType"]').val();
        
        // Set values in the summary modal
        $('#summaryName').text(fullName);
        $('#summaryContact').text(contact);
        $('#summaryEmail').text(email);
        $('#summaryCheckIn').text(checkIn);
        $('#summaryCheckOut').text(checkOut);
        $('#summaryPaymentMethod').text(paymentMethod);
        
        // Update the table-based booking summary fields (secondary modal)
        $('#summaryCustomerName').text(fullName);
        $('#summaryPaymentMethod').text(paymentMethod);
        $('#summaryPaymentOption').text(paymentOption === 'full' ? 'Full Payment' : 'Downpayment (₱1,500)');
        
        // Calculate number of nights for table modal
        const checkInDate = new Date(checkIn);
        const checkOutDate = new Date(checkOut);
        const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
        $('#summaryNights').text(nights);
        
        // Get rooms info and count total guests
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        let totalGuests = 0;
        let roomDetails = '';
        
        // Update rooms list in the first summary modal
        const roomsContainer = $('#summaryRoomsList');
        roomsContainer.empty();
        
        roomList.forEach((room, index) => {
            // For each room, we assume at least 1 guest, could be refined if guest count is stored
            const guestCount = 1; // Replace with actual guest count if available
            totalGuests += guestCount;
            roomDetails += `${room.type} (₱${parseFloat(room.price).toFixed(2)}/night)\n`;
            
            // Add room to the visual summary
            roomsContainer.append(`
                <div class="room-summary-item">
                    <h6>${room.type}</h6>
                    <p>Price: ₱${parseFloat(room.price).toFixed(2)} per night</p>
                </div>
            `);
        });
        
        // Update total guests and room details in the table summary
        $('#summaryTotalGuests').text(totalGuests);
        $('#summaryRoomDetails').html(roomDetails.replace(/\n/g, '<br>'));
        
        // Display price summary with discount information
        const priceDetailsContainer = $('#summaryPriceDetails');
        priceDetailsContainer.empty();
        
        if (priceInfo.discountAmount > 0) {
            // Show original price, discount, and final price in primary modal
            priceDetailsContainer.append(`
                <div class="summary-item">
                    <div class="d-flex justify-content-between">
                        <span>Original Amount:</span>
                        <span>₱${priceInfo.originalTotal.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between text-success">
                        <span>${priceInfo.discountType.charAt(0).toUpperCase() + priceInfo.discountType.slice(1)} Discount (${priceInfo.discountPercentage}%):</span>
                        <span>-₱${priceInfo.discountAmount.toFixed(2)}</span>
                    </div>
                </div>
            `);
            
            // Update total amount in primary modal
            $('#summaryTotalAmount').html(`<strong style="color: #28a745;">₱${priceInfo.finalTotal.toFixed(2)}</strong>`);
            
            // Show discount information in the table-based summary
            $('#summaryDiscountRow').show();
            $('#summaryDiscountInfo').html(`${priceInfo.discountType.charAt(0).toUpperCase() + priceInfo.discountType.slice(1)} (${priceInfo.discountPercentage}%): -₱${priceInfo.discountAmount.toFixed(2)}`);
            
            // Show original amount row
            $('#summaryOriginalAmountRow').show();
            $('#summaryOriginalAmountValue').text(`₱${priceInfo.originalTotal.toFixed(2)}`);
            $('#summaryTotalAmount').html(`<strong style="color: #28a745;">₱${priceInfo.finalTotal.toFixed(2)}</strong>`);
            
            // Show downpayment if selected
            if (paymentOption === 'downpayment') {
                $('.downpayment-section').show();
                $('#summaryDownpayment').text('₱' + (priceInfo.finalTotal * 0.5).toFixed(2));
            } else {
                $('.downpayment-section').hide();
            }
        } else {
            // Hide discount row if no discount is applied
            $('#summaryDiscountRow').hide();
            $('#summaryOriginalAmountRow').hide();
            
            // Just show the total if no discount
            $('#summaryTotalAmount').text('₱' + priceInfo.originalTotal.toFixed(2));
            
            // Show downpayment if selected
            if (paymentOption === 'downpayment') {
                $('.downpayment-section').show();
                $('#summaryDownpayment').text('₱' + (priceInfo.originalTotal * 0.5).toFixed(2));
            } else {
                $('.downpayment-section').hide();
            }
        }
        
        // Hide booking form and show summary
        $('#bookingFormModal').modal('hide');
        $('#bookingSummaryModal').modal('show');
    }

    function backToList() {
        $('#bookingFormModal').modal('hide');
        $('#roomListModal').modal('show');
    }

    // Initialize when document is ready
    $(document).ready(function() {
        if (!localStorage.getItem('roomList')) {
            localStorage.setItem('roomList', JSON.stringify([]));
        }
        updateCartCount();

        // Cart icon click handler
        $('#roomListBtn').click(function(e) {
            e.preventDefault();
            showRoomList();
            $('#roomListModal').modal('show');
        });

        // Book Now button handler
        $('#bookNow').click(function() {
            $('#roomListModal').modal('hide');
            $('#bookingFormModal').modal('show');
        });

        // Initialize Bootstrap dropdowns
        $('.dropdown-toggle').dropdown();

        // Add change event for discount selection
        $('#multipleDiscountType').on('change', function() {
            updateTotalAmount();
        });
    });

    // Show cart modal
    function showCart() {
        const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
        const cartContainer = document.getElementById('cartItems');
        const cartTotal = document.querySelector('.cart-total');
        
        if (cartItems.length === 0) {
            cartContainer.innerHTML = '<div class="alert alert-info">Your cart is empty</div>';
            cartTotal.innerHTML = '';
            $('#cartModal').modal('show');
            return;
        }
        
        let totalAmount = 0;
        let cartHTML = '<div class="cart-items">';
        
        cartItems.forEach((item, index) => {
            totalAmount += parseFloat(item.price);
            cartHTML += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.type}" onerror="this.src='assets/img/rooms/default.jpg'">
                    </div>
                    <div class="cart-item-details">
                        <h5>${item.type}</h5>
                        <p>Capacity: ${item.capacity} persons</p>
                        <p>Price: ₱${parseFloat(item.price).toLocaleString()}</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        cartHTML += '</div>';
        cartContainer.innerHTML = cartHTML;
        
        cartTotal.innerHTML = `
            <div class="total-section">
                <h4>Total Amount: ₱${totalAmount.toLocaleString()}</h4>
            </div>
        `;
        
        $('#cartModal').modal('show');
    }

    // Remove item from cart
    function removeFromCart(index) {
        let cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
        cartItems.splice(index, 1);
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateCartCount();
        showCart();
    }

    // Clear cart
    function clearCart() {
        Swal.fire({
            title: 'Clear Cart?',
            text: 'Are you sure you want to remove all items from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('cartItems', JSON.stringify([]));
                updateCartCount();
                $('#cartModal').modal('hide');
                
                Swal.fire(
                    'Cleared!',
                    'Your cart has been cleared.',
                    'success'
                );
            }
        });
    }

    // Proceed to booking
    function proceedToBooking() {
        const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
        if (cartItems.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Cart',
                text: 'Please add rooms to your cart before proceeding.'
            });
            return;
        }
        
        $('#cartModal').modal('hide');
        $('#bookingFormModal').modal('show');
    }

    // Update cart count
    function updateCartCount() {
        const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = cartItems.length;
            cartCount.classList.add('pulse');
            setTimeout(() => cartCount.classList.remove('pulse'), 500);
        }
    }

    // Initialize cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('cartItems')) {
            localStorage.setItem('cartItems', JSON.stringify([]));
        }
        updateCartCount();
    });

    // Process booking form submission
    function processBooking() {
        // Create FormData object
        const form = document.getElementById('multipleBookingForm');
        const formData = new FormData(form);
        
        // Get price calculations including discount
        const priceInfo = updateTotalAmount();
        
        // Add discount information to form data
        formData.append('original_total', priceInfo.originalTotal);
        formData.append('discount_amount', priceInfo.discountAmount);
        formData.append('final_total', priceInfo.finalTotal);
        formData.append('discount_type', priceInfo.discountType || '');
        formData.append('discount_percentage', priceInfo.discountAmount > 0 ? 10 : 0);
        
        // Get rooms data
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        formData.append('rooms', JSON.stringify(roomList));
        
        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your booking',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send AJAX request
        $.ajax({
            url: 'process_booking.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close();
                
                try {
                    if (response.success) {
                        // Show success message
                        let successMessage = 'Your booking has been confirmed!';
                        
                        // Add discount info to success message if applicable
                        if (priceInfo.discountAmount > 0) {
                            successMessage += `<br><br>A 10% ${priceInfo.discountType} discount of ₱${priceInfo.discountAmount.toFixed(2)} was applied.`;
                            successMessage += `<br>Original total: ₱${priceInfo.originalTotal.toFixed(2)}`;
                            successMessage += `<br>Final total: ₱${priceInfo.finalTotal.toFixed(2)}`;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Successful',
                            html: successMessage
                        }).then(() => {
                            // Clear the localStorage and redirect
                            localStorage.removeItem('roomList');
                            window.location.href = 'booking_success.php?id=' + response.booking_id;
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Failed',
                            text: response.message || 'An error occurred during booking'
                        });
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        text: 'An error occurred while processing the response'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('AJAX error:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: 'An error occurred while processing your booking'
                });
            }
        });
    }
    </script>

</body>
</html>
