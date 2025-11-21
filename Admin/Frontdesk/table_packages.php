<?php
require_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Fetch only active table packages from database
$sql = "SELECT * FROM table_packages WHERE status = 'active' ORDER BY package_name";
$result = mysqli_query($con, $sql);

// Check if query was successful
if (!$result) {
    die("Error fetching packages: " . mysqli_error($con));
}

// Check if there are any active packages
$num_packages = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Table Packages - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .package-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .package-card:hover {
            transform: translateY(-5px);
        }
        .package-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }
        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .package-content {
            padding: 20px;
        }
        .package-title {
            font-size: 24px;
            color: #333;
            margin: 0 0 10px 0;
        }
        .capacity {
            color: #666;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .capacity i {
            margin-right: 8px;
            color: #d4af37;
        }
        .btn-reserve {
            display: block;
            width: 100%;
            background: #d4af37;
            color: #fff;
            text-align: center;
            padding: 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .btn-reserve:hover {
            background: #c19b2c;
            color: #fff;
            text-decoration: none;
        }
        .description {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .package-card {
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .package-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .package-content {
            padding: 15px;
        }
        .package-title {
            font-size: 16px;
            color: #333;
            margin: 0 0 5px 0;
        }
        .capacity {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .capacity i {
            margin-right: 5px;
            color: #666;
        }
        .price {
            color: #d4af37;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .btn-reserve {
            display: block;
            width: 100%;
            background: #d4af37;
            color: #fff;
            text-align: center;
            padding: 8px;
            border: none;
            border-radius: 3px;
            text-decoration: none;
        }
        .btn-reserve:hover {
            background: #c19b2c;
            color: #fff;
            text-decoration: none;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .card-text {
            color: #666;
            margin-bottom: 1.5rem;
        }
        .card-text i {
            margin-right: 0.5rem;
            color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        /* Add new styles for the reservation modal */
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            background-color: #d4af37;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .guest-count-warning {
            color: #dc3545;
            display: none;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .payment-info {
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
        }
        .payment-info h6 {
            color: #17a2b8;
            margin-bottom: 10px;
        }
        .payment-info ul {
            padding-left: 20px;
        }
        .form-group label {
            font-weight: 500;
        }
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }
        .dropdown-menu {
            padding: 10px;
        }
        .dropdown-menu > li > a {
            padding: 8px 20px;
            color: #333;
        }
        .dropdown-menu > li > a:hover {
            background-color: #f8f9fa;
            color: #d4af37;
        }
        .category-btn.active {
            background-color: #007bff;
            color: white;
        }
        .menu-item-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .menu-item-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .menu-item-details {
            padding: 15px;
        }
        .menu-item-price {
            color: #28a745;
            font-weight: bold;
            font-size: 1.1em;
            margin: 8px 0;
        }
        .order-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .add-ons {
            margin-top: 10px;
            padding-left: 20px;
        }
        .advance-order-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .advance-order-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .advance-order-btn {
            background: #d4af37;
            border: none;
            padding: 15px;
            font-size: 1.1em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .advance-order-btn:hover {
            background: #c19b2c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .alert-info {
            background-color: #e8f4f8;
            border-left: 4px solid #17a2b8;
            border-radius: 5px;
        }
        .advance-order-header-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .advance-order-header-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.4));
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .advance-order-header-overlay h5 {
            font-size: 24px;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .modal-dialog.modal-lg {
            max-width: 800px;
        }
        .category-btn {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        .category-btn.active {
            background-color: #d4af37 !important;
            border-color: #d4af37 !important;
        }
        .menu-categories {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .category-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .category-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-btn {
            flex: 1;
            min-width: 120px;
            max-width: 200px;
            padding: 10px 15px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #495057;
            transition: all 0.3s ease;
        }
        .category-btn:hover {
            background-color: #f8f9fa;
            border-color: #d4af37;
            color: #d4af37;
        }
        .category-btn.active {
            background-color: #d4af37 !important;
            border-color: #d4af37 !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Table Packages</li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Table Packages</h1>
            </div>
        </div>
        
        <div class="row">
            <div class="container mt-4">
                <div class="row">
                    <?php if ($num_packages == 0): ?>
                    <div class="alert alert-info m-3">
                        <h4 class="alert-heading">No Active Packages</h4>
                        <p>There are currently no active table packages available.</p>
                    </div>
                    <?php else: ?>
                    <?php while ($package = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="package-card">
                            <div class="package-image">
                                <?php
                                // Use the correct image path from the database or fallback to default
                                $imagePath = !empty($package['image_path']) ? $package['image_path'] : 'images/default-table.jpg';
                                ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     alt="<?php echo htmlspecialchars($package['package_name']); ?> Package"
                                     onerror="this.src='images/default-table.jpg'; this.onerror=null;">
                            </div>
                            <div class="package-content">
                                <h3 class="package-title"><?php echo htmlspecialchars($package['package_name']); ?></h3>
                                <p class="capacity">
                                    <i class="fa fa-users"></i> Capacity: <?php echo htmlspecialchars($package['capacity']); ?>
                                </p>
                                <?php if (!empty($package['description'])): ?>
                                <p class="description"><?php echo htmlspecialchars($package['description']); ?></p>
                                <?php endif; ?>
                                <button class="btn-reserve" 
                                        data-table-type="<?php echo htmlspecialchars($package['package_name']); ?>" 
                                        data-capacity="<?php echo htmlspecialchars($package['capacity']); ?>">
                                    RESERVE NOW
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                            </div>
                        </div>
                    </div>

    <!-- Reservation Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Table Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                            </div>
                <div class="modal-body">
                    <form id="reservationForm">
                        <input type="hidden" id="tableType" name="table_type">
                        <input type="hidden" id="capacity" name="capacity">
                        
                        <div class="form-group">
                            <label for="customerName">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customerName" name="customer_name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactNumber">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contactNumber" name="contact_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress">Email Address</label>
                                    <input type="email" class="form-control" id="emailAddress" name="email" placeholder="example@email.com">
                                </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reservationDate">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="reservationDate" name="date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arrivalTime">Arrival Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="arrivalTime" name="time" required>
                                    <small class="text-muted">Operating hours: 9:00 AM - 11:00 PM</small>
                            </div>
                        </div>
                    </div>

                        <div class="form-group">
                            <label for="specialRequests">Special Requests</label>
                            <textarea class="form-control" id="specialRequests" name="special_requests" rows="3"></textarea>
                            </div>

                        <!-- Advance Order Section -->
                        <div class="advance-order-section">
                            <h6>Advance Order</h6>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> To secure your reservation, you need to make an advance order
                        </div>
                            <button type="button" class="btn btn-warning btn-block" id="makeAdvanceOrderBtn">
                                <i class="fa fa-cutlery"></i> MAKE ADVANCE ORDER
                            </button>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmReservation()">CONFIRM RESERVATION</button>
                </div>
                </div>
            </div>
        </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Your Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="booking-details">
                        <h6 class="font-weight-bold mb-3">Booking Details</h6>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Package Type:</strong>
                                <p id="confirm-package-type"></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Customer Name:</strong>
                                <p id="confirm-customer-name"></p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Contact Number:</strong>
                                <p id="confirm-contact"></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p id="confirm-email"></p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Date:</strong>
                                <p id="confirm-date"></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Time:</strong>
                                <p id="confirm-time"></p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <strong>Special Requests:</strong>
                                <p id="confirm-special-requests"></p>
            </div>
        </div>
        
                        <!-- Add Advance Order Details Section -->
                        <div class="advance-order-details mt-4">
                            <h6 class="font-weight-bold mb-3">Advance Order Details</h6>
                            <div id="confirm-order-items"></div>
                            <div class="payment-summary mt-3">
        <div class="row">
                                    <div class="col-md-6">
                                        <strong>Payment Method:</strong>
                                        <p id="confirm-payment-method"></p>
                    </div>
                                    <div class="col-md-6">
                                        <strong>Total Amount:</strong>
                                        <p id="confirm-total-amount"></p>
                </div>
            </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Amount to Pay:</strong>
                                        <p id="confirm-amount-to-pay"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="editReservation()">Edit</button>
                    <button type="button" class="btn btn-primary" onclick="submitReservation()">Confirm Reservation</button>
                </div>
                    </div>
                </div>
            </div>
            
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Reservation Successful!</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                <div class="modal-body">
                    <div class="booking-details">
                        <h5 class="mb-4">Booking Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Booking ID:</strong> <span id="booking-id"></span></p>
                                <p><strong>Customer Name:</strong> <span id="customer-name"></span></p>
                                <p><strong>Package Type:</strong> <span id="package-type"></span></p>
                                <p><strong>Guest Count:</strong> <span id="guest-count"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date:</strong> <span id="date"></span></p>
                                <p><strong>Time:</strong> <span id="time"></span></p>
                                <p><strong>Contact Number:</strong> <span id="contact-number"></span></p>
                </div>
            </div>

                        <!-- Advance Order Details Section -->
                        <div class="advance-order-details mt-4">
                            <h5 class="mb-3">Advance Order Details</h5>
                            <div id="success-order-items" class="mb-4">
                                <!-- Order items will be populated here -->
                            </div>

                            <!-- Payment Information -->
                            <div class="payment-info bg-light p-3 rounded">
                                <h6 class="mb-3">Payment Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Payment Method:</strong> <span id="success-payment-method"></span></p>
                                        <p><strong>Total Amount:</strong> <span id="success-total-amount"></span></p>
                            </div>
                                    <div class="col-md-6">
                                        <p><strong>Amount to Pay:</strong> <span id="success-amount-to-pay"></span></p>
                                        <p><strong>Payment Status:</strong> <span id="success-payment-status" class="badge bg-warning">Pending</span></p>
                        </div>
                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Advance Order Modal -->
    <div class="modal fade" id="advanceOrderModal" tabindex="-1" role="dialog" aria-labelledby="advanceOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="advanceOrderModalLabel">Advance Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left side: Menu Categories and Items -->
                        <div class="col-md-8">
                            <!-- Menu Categories -->
                            <div class="menu-categories" id="menuCategories">
                                <!-- Categories will be loaded here -->
                            </div>

                            <!-- Menu Items Grid -->
                            <div class="menu-items-grid" id="menuItems">
                                <!-- Menu items will be loaded here -->
                            </div>
                        </div>
                        
                        <!-- Right side: Order Summary -->
                        <div class="col-md-4">
                            <div class="order-summary">
                                <h6 class="mb-2">Current Order</h6>
                                <div id="orderItems" class="order-items-list">
                                    <!-- Order items will be displayed here -->
                        </div>
                        
                                <div class="order-total">
                                    <strong>Total: ₱<span id="orderTotal">0.00</span></strong>
                        </div>
                        
                                <!-- Payment Options -->
                                <div class="payment-options">
                        <div class="form-group">
                                        <label>Payment Option</label>
                                        <select class="form-control" id="paymentOption">
                                            <option value="">Select Payment Option</option>
                                            <option value="full">Full Payment</option>
                                            <option value="partial">Partial Payment (50%)</option>
                                        </select>
                        </div>
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select class="form-control" id="paymentMethod">
                                            <option value="">Select Payment Method</option>
                                            <option value="cash">Cash</option>
                                            <option value="gcash">GCash</option>
                                            <option value="maya">Maya</option>
                                            <option value="bank">Bank Transfer</option>
                                        </select>
                </div>
                                    <button class="btn btn-warning btn-block" id="placeOrderBtn">PLACE ORDER</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add required scripts -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Initialize current order array
    let currentOrder = [];

    $(document).ready(function() {
        console.log('Document ready');

        // Initialize modals
        $('#reservationModal').modal({
            show: false,
            backdrop: 'static'
        });

        $('#advanceOrderModal').modal({
            show: false,
            backdrop: 'static'
        });

        // Reserve Now button click handler
        $('.btn-reserve').click(function(e) {
                e.preventDefault();
            console.log('Reserve button clicked');
            
            const packageType = $(this).data('table-type');
            const capacity = $(this).data('capacity');
            
            console.log('Package Type:', packageType);
            console.log('Capacity:', capacity);
            
            // Set values in the modal
            $('#tableType').val(packageType);
            $('#capacity').val(capacity);
            
            // Reset form while preserving the package type and capacity
            $('#reservationForm')[0].reset();
            $('#tableType').val(packageType);
            $('#capacity').val(capacity);
                    
                    // Show the modal
                    $('#reservationModal').modal('show');
        });

        // Make Advance Order button click handler
        $('#makeAdvanceOrderBtn').click(function() {
            console.log('Make Advance Order button clicked');
            $('#reservationModal').modal('hide');
            setTimeout(() => {
                $('#advanceOrderModal').modal('show');
                loadMenuCategories();
            }, 500);
        });

        // Set minimum date
        const today = new Date().toISOString().split('T')[0];
        $('#reservationDate').attr('min', today);

        // Form validations
        $('#contactNumber').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });

        $('#emailAddress').on('input', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });

        // Time validation
        $('#arrivalTime').change(function() {
            const selectedTime = this.value;
            const [hours, minutes] = selectedTime.split(':').map(Number);
            const time = hours * 60 + minutes;
            const minTime = 9 * 60; // 9:00 AM
            const maxTime = 23 * 60; // 11:00 PM
            
            if (time < minTime || time > maxTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'Please select a time between 9:00 AM and 11:00 PM'
                });
                this.value = '';
            }
        });

        // Add to cart click handler using event delegation
        $(document).on('click', '.add-to-cart-btn', function() {
            const button = $(this);
            const item = {
                id: button.data('id'),
                name: button.data('name'),
                price: parseFloat(button.data('price'))
            };
            
            console.log('Adding item to cart:', item);
            
            // Find if item already exists in cart
            const existingItem = currentOrder.find(i => i.id === item.id);
            if (existingItem) {
                existingItem.quantity = (existingItem.quantity || 1) + 1;
            } else {
                currentOrder.push({
                    ...item,
                    quantity: 1
                });
            }
            
            // Update the display
            updateOrderDisplay();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Added to Cart',
                text: `${item.name} has been added to your order.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
        });
    });

        // Quantity control handlers
        $(document).on('click', '.decrease-qty', function() {
            const itemId = $(this).data('id');
            updateQuantity(itemId, -1);
        });

        $(document).on('click', '.increase-qty', function() {
            const itemId = $(this).data('id');
            updateQuantity(itemId, 1);
        });

        // Add click handler for the confirm reservation button
        $('.modal-footer .btn-primary').on('click', function(e) {
            e.preventDefault();
            confirmReservation();
        });
    });

    function updateOrderDisplay() {
        console.log('Updating order display, current order:', currentOrder);
        const container = $('#orderItems');
        let html = '';
        let totalAmount = 0;
        
        currentOrder.forEach(item => {
            const itemTotal = item.price * (item.quantity || 1);
            totalAmount += itemTotal;
            
            html += `
                <div class="order-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="item-info">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price text-muted">₱${item.price.toFixed(2)} × ${item.quantity || 1}</div>
                        </div>
                        <div class="quantity-control">
                            <button type="button" class="btn btn-sm btn-outline-secondary decrease-qty" data-id="${item.id}">-</button>
                            <span class="mx-2">${item.quantity || 1}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary increase-qty" data-id="${item.id}">+</button>
                        </div>
                    </div>
                    <div class="text-right mt-1">
                        <strong>₱${itemTotal.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        });

        if (currentOrder.length === 0) {
            html = '<p class="text-muted text-center">Your order is empty</p>';
        } else {
            html += `
                <div class="total mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Amount:</strong>
                        <strong class="text-success">₱${totalAmount.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        }

        container.html(html);
        $('#orderTotal').text(totalAmount.toFixed(2));
    }

    function updateQuantity(itemId, change) {
        console.log('Updating quantity for item:', itemId, 'change:', change);
        const item = currentOrder.find(i => i.id === itemId);
        if (item) {
            const newQuantity = (item.quantity || 1) + change;
            if (newQuantity < 1) {
                currentOrder = currentOrder.filter(i => i.id !== itemId);
            } else {
                item.quantity = newQuantity;
            }
            updateOrderDisplay();
        }
    }

    function loadMenuCategories() {
        console.log('Loading menu categories...');
        $.get('get_menu_data.php', { action: 'categories' }, function(categories) {
            console.log('Categories received:', categories);
            const container = $('#menuCategories');
            container.empty();
            
            categories.forEach((category, index) => {
                container.append(`
                    <button class="category-btn ${index === 0 ? 'active' : ''}" 
                            data-category-id="${category.id}">
                        ${category.display_name || category.name}
                    </button>
                `);
            });
            
            if (categories.length > 0) {
                loadMenuItems(categories[0].id);
            }
        });
    }

    function loadMenuItems(categoryId) {
        console.log('Loading menu items for category:', categoryId);
        $.get('get_menu_data.php', { action: 'items', category_id: categoryId }, function(items) {
            console.log('Items received:', items);
            const container = $('#menuItems');
            container.empty();
            
            items.forEach(item => {
                container.append(`
                    <div class="menu-item-card">
                        <img src="${item.image_path || 'images/default-food.jpg'}" 
                             class="menu-item-image" 
                             alt="${item.name}"
                             onerror="this.src='images/default-food.jpg'">
                        <div class="menu-item-details">
                            <div class="menu-item-title">${item.name}</div>
                            <div class="menu-item-price">₱${parseFloat(item.price).toFixed(2)}</div>
                            <button type="button" 
                                    class="btn btn-warning btn-block add-to-cart-btn" 
                                    data-id="${item.id}"
                                    data-name="${item.name}"
                                    data-price="${item.price}">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                `);
            });
        });
    }

    $('#placeOrderBtn').click(function() {
        const paymentOption = $('#paymentOption').val();
        const paymentMethod = $('#paymentMethod').val();
        
        if (!paymentOption || !paymentMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Information Required',
                text: 'Please select both payment option and payment method.'
            });
            return;
        }

        if (currentOrder.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Order',
                text: 'Please add at least one item to your order.'
            });
            return;
        }

        const totalAmount = calculateTotalAmount();
        const amountToPay = paymentOption === 'partial' ? totalAmount * 0.5 : totalAmount;
        
        // Store order details
        const orderDetails = {
            items: currentOrder,
            paymentOption: paymentOption,
            paymentMethod: paymentMethod,
            totalAmount: `₱${totalAmount.toFixed(2)}`,
            amountToPay: `₱${amountToPay.toFixed(2)}`
        };

        // Add hidden input for order details
        $('#reservationForm').find('input[name="advance_order"]').remove();
        $('#reservationForm').append(`
            <input type="hidden" name="advance_order" value='${JSON.stringify(orderDetails)}'>
        `);
        
        // Close advance order modal and show reservation modal
        $('#advanceOrderModal').modal('hide');
        setTimeout(() => {
            $('#reservationModal').modal('show');
        }, 500);
    });

    function calculateTotalAmount() {
        return currentOrder.reduce((total, item) => {
            const baseAmount = item.price * (item.quantity || 1);
            const addonsAmount = item.selectedAddons ? 
                item.selectedAddons.reduce((sum, addon) => sum + (addon.price * (item.quantity || 1)), 0) : 0;
            return total + baseAmount + addonsAmount;
        }, 0);
    }

    function confirmReservation() {
        // Get all form values
        const customerName = $('#customerName').val().trim();
        const contactNumber = $('#contactNumber').val().trim();
        const email = $('#emailAddress').val().trim();
        const reservationDate = $('#reservationDate').val();
        const arrivalTime = $('#arrivalTime').val();
        const specialRequests = $('#specialRequests').val().trim();
        const tableType = $('#tableType').val();
        
        // Validate required fields
        if (!customerName || !contactNumber || !reservationDate || !arrivalTime) {
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: 'Please fill in all required fields'
            });
            return;
        }

        // Validate contact number
        if (contactNumber.length !== 11) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact Number',
                text: 'Please enter a valid 11-digit phone number'
            });
            return;
        }

        // Format date for display
        const formattedDate = new Date(reservationDate).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Format time for display
        const formattedTime = new Date(`2000-01-01T${arrivalTime}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        // Update confirmation modal with reservation details
        $('#confirm-package-type').text(tableType);
        $('#confirm-customer-name').text(customerName);
        $('#confirm-contact').text(contactNumber);
        $('#confirm-email').text(email || 'Not provided');
        $('#confirm-date').text(formattedDate);
        $('#confirm-time').text(formattedTime);
        $('#confirm-special-requests').text(specialRequests || 'None');

        // Get advance order details if exists
        const advanceOrderInput = $('input[name="advance_order"]').val();
        if (advanceOrderInput) {
            try {
                const orderDetails = JSON.parse(advanceOrderInput);
                let orderItemsHtml = '<div class="order-items-list">';
                
                orderDetails.items.forEach(item => {
                    const itemTotal = item.price * (item.quantity || 1);
                    orderItemsHtml += `
                        <div class="order-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span>${item.name} × ${item.quantity || 1}</span>
                                <span>₱${itemTotal.toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                });
                
                orderItemsHtml += '</div>';
                
                // Update advance order details in confirmation modal
                $('#confirm-order-items').html(orderItemsHtml);
                $('#confirm-payment-method').text(
                    orderDetails.paymentMethod.charAt(0).toUpperCase() + 
                    orderDetails.paymentMethod.slice(1)
                );
                $('#confirm-total-amount').text(orderDetails.totalAmount);
                $('#confirm-amount-to-pay').text(orderDetails.amountToPay);
            } catch (error) {
                console.error('Error parsing advance order:', error);
                $('#confirm-order-items').html('<p class="text-muted">No advance order details available</p>');
            }
        } else {
            $('#confirm-order-items').html('<p class="text-muted">No advance order placed</p>');
        }

        // Hide reservation modal and show confirmation modal
        $('#reservationModal').modal('hide');
        setTimeout(() => {
            $('#confirmationModal').modal('show');
        }, 500);
    }

    // Add function to edit reservation
    function editReservation() {
        $('#confirmationModal').modal('hide');
        setTimeout(() => {
            $('#reservationModal').modal('show');
        }, 500);
    }

    function submitReservation() {
        console.log('Starting reservation submission...');
        
        // Get all form data
        const formData = {
            customerName: $('#customerName').val().trim(),
            contactNumber: $('#contactNumber').val().trim(),
            email: $('#emailAddress').val().trim(),
            packageType: $('#tableType').val(),
            guestCount: parseInt($('#capacity').val()),
            reservationDate: $('#reservationDate').val(),
            reservationTime: $('#arrivalTime').val(),
            specialRequests: $('#specialRequests').val().trim()
        };

        console.log('Form data collected:', formData);

        // Get advance order details if exists
        const advanceOrderInput = $('input[name="advance_order"]').val();
        console.log('Advance order input:', advanceOrderInput);
        
        if (advanceOrderInput) {
            try {
                const orderDetails = JSON.parse(advanceOrderInput);
                console.log('Parsed order details:', orderDetails);
                
                formData.advanceOrder = orderDetails;
                formData.paymentOption = orderDetails.paymentOption;
                formData.paymentMethod = orderDetails.paymentMethod;
                formData.totalAmount = orderDetails.totalAmount.replace('₱', '').replace(',', '');
                formData.amountToPay = orderDetails.amountToPay.replace('₱', '').replace(',', '');
                
                console.log('Updated form data with order details:', formData);
            } catch (error) {
                console.error('Error parsing advance order:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an error processing your order details. Please try again.'
                });
                return;
            }
        }

        // Validate required fields
        const requiredFields = [
            'customerName', 
            'contactNumber', 
            'packageType', 
            'reservationDate', 
            'reservationTime',
            'paymentMethod',
            'totalAmount'
        ];
        const missingFields = requiredFields.filter(field => !formData[field]);
        
        if (missingFields.length > 0) {
            console.error('Missing required fields:', missingFields);
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: `Please fill in all required fields: ${missingFields.join(', ')}`
            });
            return;
        }

        // Show loading state
        $('#confirmationModal .modal-footer').html(`
            <div class="text-center w-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Processing...</span>
                </div>
                <div class="mt-2">Processing your reservation...</div>
            </div>
        `);

        console.log('Sending reservation data:', formData);

        // Submit the reservation
        $.ajax({
            url: 'process_table_reservation.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                console.log('Raw server response:', response);
                
                try {
                    // Parse the response if it's a string
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log('Parsed server response:', result);
                    
                    if (result.success) {
                        console.log('Reservation successful, showing success message');
                        // Update success modal with booking details
                        showSuccessMessage(result.booking_details);
                        
                        // Close confirmation modal
                        $('#confirmationModal').modal('hide');
                    } else {
                        console.error('Server returned error:', result.message);
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Reservation Failed',
                            text: result.message || 'Failed to process reservation'
                        });
                        
                        // Restore confirmation modal footer
                        $('#confirmationModal .modal-footer').html(`
                            <button type="button" class="btn btn-secondary" onclick="editReservation()">Edit</button>
                            <button type="button" class="btn btn-primary" onclick="submitReservation()">Confirm Reservation</button>
                        `);
                    }
                } catch (error) {
                    console.error('Error processing response:', error, response);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Reservation Failed',
                        text: 'An error occurred while processing your reservation. Please try again.'
                    });
                    
                    // Restore confirmation modal footer
                    $('#confirmationModal .modal-footer').html(`
                        <button type="button" class="btn btn-secondary" onclick="editReservation()">Edit</button>
                        <button type="button" class="btn btn-primary" onclick="submitReservation()">Confirm Reservation</button>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    xhr: xhr
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Reservation Failed',
                    text: 'An error occurred while processing your reservation. Please try again.'
                });
                
                // Restore confirmation modal footer
                $('#confirmationModal .modal-footer').html(`
                    <button type="button" class="btn btn-secondary" onclick="editReservation()">Edit</button>
                    <button type="button" class="btn btn-primary" onclick="submitReservation()">Confirm Reservation</button>
                `);
            }
        });
    }

    function showSuccessMessage(details) {
        // Update success message details
        $('#booking-id').text(details.booking_id);
        $('#customer-name').text(details.customer_name);
        $('#package-type').text(details.package_type);
        $('#guest-count').text(details.guest_count);
        $('#date').text(details.date);
        $('#time').text(details.time);
        $('#contact-number').text(details.contact_number);
        $('#success-payment-method').text(details.payment_method);
        $('#success-total-amount').text(`₱${details.total_amount}`);
        $('#success-amount-to-pay').text(`₱${details.amount_to_pay}`);
        
        // Update order items if they exist
        if (details.order_id) {
            let orderItemsHtml = '<div class="order-items-list">';
            const orderDetails = JSON.parse($('input[name="advance_order"]').val());
            
            orderDetails.items.forEach(item => {
                const itemTotal = item.price * (item.quantity || 1);
                orderItemsHtml += `
                    <div class="order-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>${item.name} × ${item.quantity || 1}</span>
                            <span>₱${itemTotal.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            });
            
            orderItemsHtml += '</div>';
            $('#success-order-items').html(orderItemsHtml);
        } else {
            $('#success-order-items').html('<p class="text-muted">No advance order placed</p>');
        }
        
        // Show success modal
        $('#confirmationModal').modal('hide');
        setTimeout(() => {
            $('#successModal').modal('show');
        }, 500);
    }
    </script>
</body>
</html>

