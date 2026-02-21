<?php
session_start();

try {
    // Get database connection
    require 'db_con.php';
    $db = $pdo;
    
    // Fetch all table types with available table counts
    $query = "SELECT tt.*, 
                     (SELECT COUNT(*) FROM table_number tn 
                      WHERE tn.table_type_fk_id = tt.id AND tn.status = 'available') as available_tables
              FROM table_types tt 
              ORDER BY tt.capacity ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Packages - Casa de Alfonso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Floating Cart Icon */
        .floating-cart {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1000;
            background-color: #fff;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .floating-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        .floating-cart i {
            font-size: 24px;
            color: var(--primary-color);
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        :root {
            --primary-color: #b6860a;
            --secondary-color: #2c3e50;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--secondary-color);
            padding-top: 80px;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
            text-align: center;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .package-section {
            padding: 50px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title h2 {
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            margin: 15px auto 0;
        }
        
        .package-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .package-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .package-body {
            padding: 25px;
        }
        
        .package-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        .package-capacity, .package-availability {
            color: var(--dark-gray);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .package-availability {
            font-size: 0.9rem;
        }
        
        .package-availability.text-success {
            color: #198754 !important;
        }
        
        .package-capacity i {
            margin-right: 8px;
        }
        
        .package-description {
            color: var(--dark-gray);
            margin-bottom: 20px;
            min-height: 60px;
        }
        
        .btn-reserve {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-reserve:hover {
            background-color: #9a7209;
            color: white;
            transform: translateY(-2px);
        }
        
        .no-image {
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-gray);
        }
        
        /* Availability Section Styles */
        .availability-card {
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .availability-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(182, 134, 10, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #9a7209;
            border-color: #9a7209;
            transform: translateY(-1px);
        }
        
        /* Form validation styles */
        .is-invalid {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }
            
            .hero-section h1 {
                font-size: 2.2rem;
            }
            
            .package-section {
                padding: 30px 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <!-- Floating Cart Icon -->
    <div class="floating-cart" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge" id="cartItemCount">0</span>
    </div>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Our Table Packages</h1>
            <p class="lead">Experience the perfect dining experience with our exclusive table packages</p>
        </div>
    </section>

    <!-- Availability Checker Section -->
    <section class="availability-section py-5 bg-light">
        <div class="container">
            <div class="availability-card p-4 rounded-3 shadow-sm bg-white">
                <h3 class="text-center mb-4">Check Table Availability</h3>
                <form id="checkAvailabilityForm" class="row g-3">
                    <div class="col-md-10">
                        <label for="reservationDateTime" class="form-label">Date & Time</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            <input type="datetime-local" class="form-control" id="reservationDateTime" name="reservationDateTime" required>
                        </div>
                        <small class="text-muted">Cafe hours: 7:00 AM - 11:00 PM (Monday-Sunday)</small>
                        <!-- Availability Results -->
                        <div id="availabilityResults" class="mt-3" style="display: none;">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="availabilityMessage"></span>
                                <div id="tableAvailability" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" id="checkAvailabilityBtn">
                            <i class="fas fa-search me-2"></i> Check
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="package-section">
        <div class="container">
            <div class="section-title">
                <h2>Choose Your Perfect Table</h2>
                <p>Select from our range of table packages to suit your needs</p>
            </div>
            
            <div class="row">
                <?php if (count($packages) > 0): ?>
                    <?php foreach ($packages as $package): ?>
                        <div class="col-md-4 mb-4">
                            <div class="package-card" data-table-id="<?php echo $package['id']; ?>">
                                <?php if (!empty($package['img1'])): ?>
                                    <img src="../../uploads/table_types/<?php echo htmlspecialchars($package['img1']); ?>" 
                                         alt="<?php echo htmlspecialchars($package['table_name']); ?>" 
                                         class="package-img">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-utensils fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="package-body">
                                    <!-- Availability Badge -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="package-capacity mb-0">
                                            <i class="fas fa-users me-1"></i>
                                            <span>Up to <?php echo htmlspecialchars($package['capacity']); ?> persons</span>
                                        </div>
                                        <?php 
                                        $availableTables = (int)$package['available_tables'];
                                        $isAvailable = $availableTables > 0;
                                        ?>
                                        <span class="badge <?php echo $isAvailable ? 'bg-success' : 'bg-danger'; ?> p-2 availability-badge">
                                            <i class="fas <?php echo $isAvailable ? 'fa-check' : 'fa-times'; ?>-circle me-1"></i>
                                            <?php echo $isAvailable ? 'Available' : 'Unavailable'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Table Availability -->
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-table me-2 text-muted"></i>
                                        <span class="small text-muted table-count">
                                            <?php echo $availableTables; ?> table<?php echo $availableTables != 1 ? 's' : ''; ?> available
                                        </span>
                                    </div>
                                    
                                    <p class="package-description small text-muted mb-3">
                                        <?php echo !empty($package['description']) ? htmlspecialchars($package['description']) : 'Perfect for your dining experience.'; ?>
                                    </p>
                                    
                                    <button class="btn w-100 <?php echo $isAvailable ? 'btn-primary' : 'btn-secondary'; ?> add-to-cart" 
                                            data-package-id="<?php echo $package['id']; ?>"
                                            data-package-name="<?php echo htmlspecialchars($package['table_name']); ?>"
                                            data-capacity="<?php echo $package['capacity']; ?>"
                                            data-available="<?php echo $isAvailable ? '1' : '0'; ?>"
                                            <?php echo $isAvailable ? '' : 'disabled'; ?>>
                                        <i class="fas <?php echo $isAvailable ? 'fa-plus' : 'fa-times'; ?> me-2"></i>
                                        <?php echo $isAvailable ? 'ADD TO LIST' : 'UNAVAILABLE'; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No table packages available at the moment.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="cartModalBody">
                    <div class="text-center py-4" id="emptyCartMessage">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="mb-0">Your cart is empty</p>
                    </div>
                    <div id="cartItemsContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Capacity</th>
                                        <th class="text-center">Qty</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cartItemsList">
                                    <!-- Cart items will be dynamically inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="cartModalFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Browsing</button>
                    <button type="button" class="btn btn-primary" id="proceedToCheckout">Proceed to Form</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Form Modal -->
    <div class="modal fade" id="reservationFormModal" tabindex="-1" aria-labelledby="reservationFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="reservationFormModalLabel">Complete Your Reservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reservationForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="reservationDate" class="form-label">Reservation Date</label>
                                <input type="date" class="form-control" id="reservationDate" required disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="reservationTime" class="form-label">Reservation Time</label>
                                <input type="time" class="form-control" id="reservationTime" min="07:00" max="23:00" required disabled>
                                <small class="text-muted">Cafe hours: 7:00 AM - 11:00 PM</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6>Selected Tables</h6>
                            <div id="selectedTablesList" class="mb-3">
                                <!-- Tables will be dynamically inserted here -->
                                <div class="text-muted">Loading tables from cart...</div>
                            </div>
                            
                            <!-- Order Summary Section -->
                            <div id="orderSummary" class="mb-3">
                                <!-- Order items will be displayed here -->
                            </div>
                            
                            <!-- Payment Section (initially hidden) -->
                            <div id="paymentSection" style="display: none;">
                                <h6 class="mt-4 mb-3">Payment Details</h6>
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">Payment Method</label>
                                    <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                        <option value="">Select Payment Method</option>
                                        
                                        <option value="online">
                                            <i class="fas fa-credit-card me-2"></i>Gcash
                                        </option>
                                        <option value="online">
                                            <i class="fas fa-credit-card me-2"></i>Maya
                                        </option>
                                    </select>
                                </div>
                                <div id="paymentOptions" class="mb-3" style="display: none;">
                                    <label for="paymentOption" class="form-label">Payment Option</label>
                                    <select class="form-select" id="paymentOption" name="paymentOption">
                                        <option value="select">Select Payment Option</option>
                                        <option value="full">Full Payment</option>
                                        <option value="partial">Partial Payment (50% downpayment)</option>
                                        <option value="custom">Custom Payment</option>
                                    </select>
                                    
                                    <!-- Payment Breakdown Section -->
                                    <!-- Payment Breakdown Container -->
                                <div id="paymentBreakdown" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                    <h6 class="mb-3">Payment Breakdown</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <span id="breakdownTotal" class="fw-bold">₱0.00</span>
                                    </div>
                                    <div id="downpaymentRow" class="d-flex justify-content-between mb-2">
                                        <span>50% Downpayment:</span>
                                        <span id="breakdownDownpayment" class="fw-bold text-primary">₱0.00</span>
                                    </div>
                                    <div id="customPaymentRow" class="mb-2" style="display: none;">
                                        <div class="input-group">
                                            <span class="input-group-text">Custom Amount:</span>
                                            <input type="number" class="form-control" id="customAmount" min="1" step="0.01" placeholder="Enter amount">
                                        </div>
                                        <div class="text-danger small mt-1" id="customAmountError"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                        <div>
                                            <span class="fw-bold">Remaining Balance:</span>
                                            <span class="badge bg-warning ms-2">Pending</span>
                                        </div>
                                        <span id="breakdownBalance" class="fw-bold">₱0.00</span>
                                    </div>
                                    <div class="small text-muted mt-2">* Remaining balance to be paid upon arrival</div>
                                </div>
                                </div>
                            </div>
                            

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="confirmBookingBtn">
                        <i class="fas fa-check-circle me-2"></i>Confirm Booking
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <style>
        /* Menu item card hover effect */
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom scrollbar for order items */
        #orderItems {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Quantity input number arrows */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button {  
            -webkit-appearance: none;
            margin: 0; 
        }
        
        input[type=number] {
            -moz-appearance: textfield;
        }
        
        /* Add-ons select styling */
        .item-addon {
            font-size: 0.85rem;
        }
        
        /* Nav tabs styling */
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            border: none;
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 0.25rem 0.25rem 0 0;
        }
        
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
            border-bottom: 2px solid #0d6efd;
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: transparent;
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
            }
            
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 5px;
            }
            
            .nav-tabs .nav-item {
                display: inline-block;
                float: none;
            }
        }
    </style>
    
    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="menuModalLabel">Advance Order - Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Menu Categories Tabs -->
                    <ul class="nav nav-tabs mb-4" id="menuCategories" role="tablist">
                        <!-- Will be populated by JavaScript -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="loading-tab" data-bs-toggle="tab" data-bs-target="#loading" type="button" role="tab" aria-controls="loading" aria-selected="true">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Menu Items -->
                    <div class="tab-content" id="menuItems">
                        <div class="tab-pane fade show active" id="loading" role="tabpanel" aria-labelledby="loading-tab">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading menu items...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div id="orderItems">
                                <p class="text-muted text-center py-3">Your order will appear here</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <h5>Total: <span id="orderTotal">₱0.00</span></h5>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="clearOrderBtn">
                                    <i class="fas fa-trash-alt me-1"></i> Clear Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" id="advanceOrderBtn">
                        <i class="fas fa-calendar-plus me-2"></i>Advance Order
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Global variable to track if availability has been checked
        let availabilityChecked = false;
        
        // Function to load menu data
        function loadMenuData() {
            fetch('table_get_menu_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMenu(data.data);
                    } else {
                        showAlert(data.message || 'Failed to load menu', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading menu:', error);
                    showAlert('Error loading menu. Please try again.', 'error');
                });
        }

        // Function to render menu
        function renderMenu(categories) {
            const categoriesNav = document.getElementById('menuCategories');
            const menuItemsContainer = document.getElementById('menuItems');
            
            // Clear loading state
            categoriesNav.innerHTML = '';
            menuItemsContainer.innerHTML = '';
            
            if (categories.length === 0) {
                menuItemsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        No menu items available. Please check back later.
                    </div>
                `;
                return;
            }
            
            // Create category tabs
            categories.forEach((category, index) => {
                const isFirst = index === 0;
                const tabId = `cat-${category.id}`;
                
                // Create tab button
                const tabButton = document.createElement('li');
                tabButton.className = 'nav-item';
                tabButton.role = 'presentation';
                tabButton.innerHTML = `
                    <button class="nav-link ${isFirst ? 'active' : ''}" 
                            id="${tabId}-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#${tabId}" 
                            type="button" 
                            role="tab" 
                            aria-controls="${tabId}" 
                            aria-selected="${isFirst ? 'true' : 'false'}">
                        ${category.name}
                    </button>
                `;
                categoriesNav.appendChild(tabButton);
                
                // Create tab content
                const tabContent = document.createElement('div');
                tabContent.className = `tab-pane fade ${isFirst ? 'show active' : ''}`;
                tabContent.id = tabId;
                tabContent.role = 'tabpanel';
                tabContent.ariaLabelledby = `${tabId}-tab`;
                
                // Add menu items
                if (category.items && category.items.length > 0) {
                    const row = document.createElement('div');
                    row.className = 'row g-4';
                    
                    category.items.forEach(item => {
                        const itemHtml = `
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100">
                                    <div class="position-relative">
                                        ${item.image_path ? 
                                            `<img src="${item.image_path}" class="card-img-top" alt="${item.name}" style="height: 160px; object-fit: cover;">` : 
                                            `<div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                                <i class="fas fa-utensils fa-3x text-muted"></i>
                                            </div>`
                                        }
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-success">₱${parseFloat(item.price).toFixed(2)}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${item.name}</h5>
                                        ${item.description ? `<p class="card-text text-muted small">${item.description}</p>` : ''}
                                        
                                        ${item.addons && item.addons.length > 0 ? `
                                            <div class="mt-2">
                                                <label class="form-label small text-muted mb-1">Add-ons:</label>
                                                <select class="form-select form-select-sm item-addon" data-item-id="${item.id}">
                                                    <option value="" selected>Select add-on</option>
                                                    ${item.addons.map(addon => 
                                                        `<option value="${addon.id}" data-price="${addon.price}">
                                                            ${addon.name} (+₱${parseFloat(addon.price).toFixed(2)})
                                                        </option>`
                                                    ).join('')}
                                                </select>
                                            </div>
                                        ` : ''}
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary minus-item" type="button" data-item-id="${item.id}">-</button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                    value="0" min="0" data-item-id="${item.id}" 
                                                    style="width: 40px;">
                                                <button class="btn btn-outline-primary plus-item" type="button" data-item-id="${item.id}">+</button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary add-to-order" data-item-id="${item.id}">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        row.innerHTML += itemHtml;
                    });
                    
                    tabContent.appendChild(row);
                } else {
                    tabContent.innerHTML = `
                        <div class="alert alert-info">
                            No items available in this category.
                        </div>
                    `;
                }
                
                menuItemsContainer.appendChild(tabContent);
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize event listeners for the menu
            initializeMenuEventListeners();
        }
        
        // Initialize event listeners for menu items
        function initializeMenuEventListeners() {
            // Quantity controls
            document.querySelectorAll('.plus-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                    input.value = parseInt(input.value) + 1;
                    updateAddButtonState(itemId, input.value);
                });
            });
            
            document.querySelectorAll('.minus-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                    const newValue = Math.max(0, parseInt(input.value) - 1);
                    input.value = newValue;
                    updateAddButtonState(itemId, newValue);
                });
            });
            
            // Quantity input change
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const value = Math.max(0, parseInt(this.value) || 0);
                    this.value = value;
                    updateAddButtonState(itemId, value);
                });
            });
            
            // Add to order button
            document.querySelectorAll('.add-to-order').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    addToOrder(itemId);
                });
            });
        }
        
        // Update add button state based on quantity
        function updateAddButtonState(itemId, quantity) {
            const button = document.querySelector(`.add-to-order[data-item-id="${itemId}"]`);
            if (button) {
                button.disabled = quantity <= 0;
            }
        }
        
        // Add item to order
        function addToOrder(itemId) {
            const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
            const quantity = parseInt(input.value) || 0;
            
            if (quantity <= 0) return;
            
            // Get item details
            const itemCard = input.closest('.card');
            const itemName = itemCard.querySelector('.card-title').textContent;
            const itemPrice = parseFloat(itemCard.querySelector('.badge').textContent.replace('₱', ''));
            
            // Get selected add-on if any
            let addonId = '';
            let addonName = '';
            let addonPrice = 0;
            
            const addonSelect = itemCard.querySelector('.item-addon');
            if (addonSelect && addonSelect.value) {
                addonId = addonSelect.value;
                const selectedOption = addonSelect.options[addonSelect.selectedIndex];
                addonName = selectedOption.text.split(' (')[0];
                addonPrice = parseFloat(selectedOption.getAttribute('data-price'));
            }
            
            // Add to order
            const orderItem = {
                id: `item-${Date.now()}`,
                itemId,
                name: itemName,
                price: itemPrice,
                quantity,
                addonId,
                addonName,
                addonPrice,
                total: (itemPrice + addonPrice) * quantity
            };
            
            // Add to order in localStorage
            let order = JSON.parse(localStorage.getItem('currentOrder')) || [];
            order.push(orderItem);
            localStorage.setItem('currentOrder', JSON.stringify(order));
            
            // Reset input
            input.value = '0';
            updateAddButtonState(itemId, 0);
            
            // Update order summary
            updateOrderSummary();
            
            // Show success message
            showAlert(`${quantity}x ${itemName} ${addonName ? 'with ' + addonName : ''} added to order`, 'success');
        }
        
        // Clear current order
        function clearCurrentOrder() {
            localStorage.removeItem('currentOrder');
            updateOrderSummary();
            showAlert('Order has been cleared', 'info');
        }
        
        // Update order summary
        function updateOrderSummary() {
            const order = JSON.parse(localStorage.getItem('currentOrder')) || [];
            const orderItemsContainer = document.getElementById('orderItems');
            const orderTotalElement = document.getElementById('orderTotal');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const clearOrderBtn = document.getElementById('clearOrderBtn');
            
            if (order.length === 0) {
                orderItemsContainer.innerHTML = '<p class="text-muted text-center py-3">Your order will appear here</p>';
                orderTotalElement.textContent = '₱0.00';
                addToCartBtn.disabled = true;
                if (clearOrderBtn) clearOrderBtn.style.display = 'none';
                return;
            }
            
            // Calculate total
            let total = 0;
            let itemsHtml = '';
            
            order.forEach((item, index) => {
                total += item.total;
                
                itemsHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-0">${item.quantity}x ${item.name}</h6>
                            ${item.addonName ? `<small class="text-muted">Add-on: ${item.addonName} (₱${item.addonPrice.toFixed(2)})</small><br>` : ''}
                            <small class="text-muted">₱${item.price.toFixed(2)} each</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">₱${item.total.toFixed(2)}</div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            orderItemsContainer.innerHTML = itemsHtml;
            orderTotalElement.textContent = `₱${total.toFixed(2)}`;
            addToCartBtn.disabled = false;
            if (clearOrderBtn) clearOrderBtn.style.display = 'inline-flex';
            
            // Add event listeners to remove buttons
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    removeFromOrder(index);
                });
            });
        }
        
        // Remove item from order
        function removeFromOrder(index) {
            let order = JSON.parse(localStorage.getItem('currentOrder')) || [];
            if (index >= 0 && index < order.length) {
                order.splice(index, 1);
                localStorage.setItem('currentOrder', JSON.stringify(order));
                updateOrderSummary();
            }
        }
        
        // Add to cart button click
        document.getElementById('addToCartBtn')?.addEventListener('click', function() {
            const order = JSON.parse(localStorage.getItem('currentOrder')) || [];
            
            if (order.length === 0) {
                showAlert('Your order is empty', 'warning');
                return;
            }
            
            // Get current cart or initialize empty array
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Add order items to cart
            order.forEach(item => {
                cart.push({
                    id: `menu-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
                    type: 'menu',
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    addon: item.addonName ? {
                        name: item.addonName,
                        price: item.addonPrice
                    } : null,
                    total: item.total,
                    timestamp: new Date().toISOString()
                });
            });
            
            // Save updated cart
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Clear current order
            localStorage.removeItem('currentOrder');
            
            // Update UI
            updateOrderSummary();
            updateCartItemCount();
            
            // Show success message
            showAlert('Items added to cart successfully!', 'success');
        });
        
        // View cart button click
        document.getElementById('viewCartBtn')?.addEventListener('click', function() {
            // Close menu modal
            const menuModal = bootstrap.Modal.getInstance(document.getElementById('menuModal'));
            menuModal.hide();
            
            // Open cart modal
            const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
            cartModal.show();
        });
        
        // Clear order button click
        document.getElementById('clearOrderBtn')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Clear Order',
                text: 'Are you sure you want to clear your current order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    clearCurrentOrder();
                }
            });
        });
        
        // Load menu when modal is shown
        document.getElementById('menuModal')?.addEventListener('shown.bs.modal', function() {
            loadMenuData();
            updateOrderSummary();
        });
        
        // Clear order when modal is hidden
        document.getElementById('menuModal')?.addEventListener('hidden.bs.modal', function() {
            // Optionally clear the current order when modal is closed
            // localStorage.removeItem('currentOrder');
        });

        // Function to update cart item count and modal
        function updateCartItemCount() {
            // Get cart from localStorage or initialize empty array
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            // Calculate total quantity of items in cart
            const totalItems = cart.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0);
            // Update the cart badge
            const cartBadge = document.getElementById('cartItemCount');
            if (cartBadge) {
                cartBadge.textContent = totalItems;
                cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
            }
            
            // Update cart modal
            updateCartModal(cart);
        }

        // Function to update cart modal with current cart items
        function updateCartModal(cart) {
            const cartItemsList = document.getElementById('cartItemsList');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const cartItemsContainer = document.getElementById('cartItemsContainer');
            const cartModalFooter = document.getElementById('cartModalFooter');
            const cartSubtotal = document.getElementById('cartSubtotal');
            
            if (cart.length === 0) {
                emptyCartMessage.style.display = 'block';
                cartItemsContainer.style.display = 'none';
                cartModalFooter.style.display = 'none';
                return;
            }
            
            // Show cart items and footer
            emptyCartMessage.style.display = 'none';
            cartItemsContainer.style.display = 'block';
            cartModalFooter.style.display = 'flex';
            
            // Clear existing items
            cartItemsList.innerHTML = '';
            
            // Add each item to the cart
            cart.forEach((item, index) => {
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.name || 'Unnamed Item'}</td>
                    <td class="text-center">${item.capacity || '2'} persons</td>
                    <td class="text-center align-middle">
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-sm btn-outline-secondary decrement-qty" data-index="${index}" style="min-width: 30px;">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" class="form-control form-control-sm text-center mx-1 quantity-input" 
                                   value="${item.quantity || 1}" 
                                   data-index="${index}" 
                                   style="width: 45px;">
                            <button class="btn btn-sm btn-outline-secondary increment-qty" data-index="${index}" style="min-width: 30px;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index}, event)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                cartItemsList.appendChild(row);
            });
            
        }
        
        // Function to remove item from cart
        function removeFromCart(index, event) {
            event.stopPropagation();
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartItemCount();
            
            // Dispatch storage event to update other tabs/windows
            window.dispatchEvent(new Event('storage'));
        }
        
        // Function to update quantity in cart
        function updateCartItemQuantity(index, newQuantity) {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart[index]) {
                // Ensure quantity is at least 1
                newQuantity = Math.max(1, parseInt(newQuantity) || 1);
                cart[index].quantity = newQuantity;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartItemCount();
                window.dispatchEvent(new Event('storage'));
            }
        }

        // Handle quantity changes
        document.addEventListener('click', function(e) {
            // Increment quantity
            if (e.target.closest('.increment-qty')) {
                const button = e.target.closest('.increment-qty');
                const index = parseInt(button.dataset.index);
                const input = button.previousElementSibling;
                const newQty = parseInt(input.value) + 1;
                updateCartItemQuantity(index, newQty);
            }
            
            // Decrement quantity
            if (e.target.closest('.decrement-qty')) {
                const button = e.target.closest('.decrement-qty');
                const index = parseInt(button.dataset.index);
                const input = button.nextElementSibling;
                const newQty = Math.max(1, parseInt(input.value) - 1);
                updateCartItemQuantity(index, newQty);
            }
        });

        // Handle manual input changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                const input = e.target;
                const index = parseInt(input.dataset.index);
                const newQty = Math.max(1, parseInt(input.value) || 1);
                updateCartItemQuantity(index, newQty);
            }
        });

        // Prevent form submission when pressing enter in quantity input
        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('quantity-input') && e.key === 'Enter') {
                e.preventDefault();
                const input = e.target;
                const index = parseInt(input.dataset.index);
                const newQty = Math.max(1, parseInt(input.value) || 1);
                updateCartItemQuantity(index, newQty);
                input.blur(); // Remove focus after updating
            }
        });

        // Update cart count when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCartItemCount();
            
            // Listen for storage events to update cart count when changed in other tabs/windows
            window.addEventListener('storage', function(e) {
                if (e.key === 'cart') {
                    updateCartItemCount();
                }
            });
            
            // Initialize cart modal
            const cartModal = document.getElementById('cartModal');
            if (cartModal) {
                cartModal.addEventListener('show.bs.modal', function () {
                    updateCartItemCount();
                });
            }
        });


        // Function to show alert messages using SweetAlert2
        function showAlert(message, type = 'info', duration = 3000) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: duration,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            
            Toast.fire({
                icon: type,
                title: message
            });
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Set minimum date to today
        // Function to update the summary modal with cart items
        function updateSummaryModal() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const summaryContent = document.getElementById('summaryContent');
            
            if (!summaryContent) return;
            
            if (cart.length === 0) {
                summaryContent.innerHTML = '<p>Your cart is empty.</p>';
                return;
            }
            
            // Build the summary content
            let html = `
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Capacity</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            let total = 0;
            
            cart.forEach((item) => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                total += itemTotal;
                
                html += `
                    <tr>
                        <td>${item.name}</td>
                        <td class="text-end">${item.capacity || 'N/A'}</td>
                        <td class="text-end">${item.quantity}</td>
                        <td class="text-end">₱${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <h5>Total: ₱${total.toFixed(2)}</h5>
                </div>
            `;
            
            summaryContent.innerHTML = html;
        }
        
        // Handle Advance Order button click
        // Toggle payment options based on payment method selection
        let currentOrderTotal = 0;

        function updateOrderSummary() {
            const order = JSON.parse(localStorage.getItem('currentOrder')) || [];
            let itemsHtml = '';
            let total = 0;
            
            order.forEach((item, index) => {
                total += item.total;
                // ... rest of the existing updateOrderSummary code ...
            });
            
            // Store the total for payment breakdown
            currentOrderTotal = total;
            
            // Update the order summary display
            document.getElementById('orderTotal').textContent = `₱${total.toFixed(2)}`;
            document.getElementById('orderItems').innerHTML = itemsHtml;
        }

        document.addEventListener('change', function(e) {
            // Handle payment method change (show/hide payment options)
            if (e.target && e.target.id === 'paymentMethod') {
                const paymentOptions = document.getElementById('paymentOptions');
                const confirmBookingBtn = document.getElementById('confirmBookingBtn');
                
                if (e.target.value === 'online') {
                    paymentOptions.style.display = 'block';
                    // Update button text when a payment method is selected
                    confirmBookingBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Proceed to Payment';
                } else {
                    paymentOptions.style.display = 'none';
                    document.getElementById('paymentBreakdown').style.display = 'none';
                    // Reset button text if no payment method is selected
                    confirmBookingBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Confirm Booking';
                }
            }
            
            // Handle payment option change (show/hide breakdown based on selection)
            if (e.target && e.target.id === 'paymentOption') {
                const paymentBreakdown = document.getElementById('paymentBreakdown');
                const downpaymentRow = document.getElementById('downpaymentRow');
                const customPaymentRow = document.getElementById('customPaymentRow');
                const customAmountInput = document.getElementById('customAmount');
                const customAmountError = document.getElementById('customAmountError');
                
                // Reset error and input
                customAmountError.textContent = '';
                customAmountInput.value = '';
                
                if (e.target.value === 'partial' || e.target.value === 'full') {
                    paymentBreakdown.style.display = 'block';
                    
                    // Show/hide appropriate rows
                    downpaymentRow.style.display = e.target.value === 'partial' ? 'flex' : 'none';
                    customPaymentRow.style.display = 'none';
                    
                    // Calculate and display payment breakdown
                    const downpayment = e.target.value === 'partial' ? currentOrderTotal * 0.5 : currentOrderTotal;
                    const remaining = currentOrderTotal - downpayment;
                    
                    document.getElementById('breakdownTotal').textContent = `₱${currentOrderTotal.toFixed(2)}`;
                    document.getElementById('breakdownDownpayment').textContent = e.target.value === 'partial' ? 
                        `₱${downpayment.toFixed(2)}` : `₱${downpayment.toFixed(2)} (Full Payment)`;
                    document.getElementById('breakdownBalance').textContent = `₱${remaining.toFixed(2)}`;
                } 
                else if (e.target.value === 'custom') {
                    paymentBreakdown.style.display = 'block';
                    downpaymentRow.style.display = 'none';
                    customPaymentRow.style.display = 'block';
                    
                    // Set max value to total amount
                    customAmountInput.max = currentOrderTotal.toFixed(2);
                    customAmountInput.placeholder = `Enter amount (max: ₱${currentOrderTotal.toFixed(2)})`;
                    
                    document.getElementById('breakdownTotal').textContent = `₱${currentOrderTotal.toFixed(2)}`;
                    document.getElementById('breakdownBalance').textContent = `₱${currentOrderTotal.toFixed(2)}`;
                }
                else {
                    paymentBreakdown.style.display = 'none';
                }
            }
            
            // Handle custom amount input
            if (e.target && e.target.id === 'customAmount') {
                const customAmount = parseFloat(e.target.value) || 0;
                const total = currentOrderTotal;
                const customAmountError = document.getElementById('customAmountError');
                
                if (customAmount > total) {
                    customAmountError.textContent = 'Amount cannot exceed total amount';
                    e.target.value = total.toFixed(2);
                    updateCustomPaymentBreakdown(total);
                } else if (customAmount <= 0) {
                    customAmountError.textContent = 'Amount must be greater than 0';
                    updateCustomPaymentBreakdown(0);
                } else {
                    customAmountError.textContent = '';
                    updateCustomPaymentBreakdown(customAmount);
                }
            }
            
            // Function to update the payment breakdown for custom payments
            function updateCustomPaymentBreakdown(amount) {
                const total = currentOrderTotal;
                const remaining = total - amount;
                
                document.getElementById('breakdownTotal').textContent = `₱${total.toFixed(2)}`;
                document.getElementById('breakdownBalance').textContent = `₱${remaining.toFixed(2)}`;
                
                // Update the payment amount display
                const customPaymentDisplay = document.getElementById('breakdownDownpayment');
                customPaymentDisplay.textContent = `₱${amount.toFixed(2)} (Custom Payment)`;
                customPaymentDisplay.style.display = 'block';
            }
        });
        
        document.getElementById('advanceOrderBtn')?.addEventListener('click', function() {
            const menuModal = bootstrap.Modal.getInstance(document.getElementById('menuModal'));
            if (menuModal) menuModal.hide();
            
            // Get the current order from localStorage
            const currentOrder = JSON.parse(localStorage.getItem('currentOrder')) || [];
            
            // Create a hidden input for the order data if it doesn't exist
            let orderInput = document.getElementById('orderData');
            if (!orderInput) {
                orderInput = document.createElement('input');
                orderInput.type = 'hidden';
                orderInput.name = 'order_data';
                orderInput.id = 'orderData';
                document.getElementById('reservationForm').appendChild(orderInput);
            }
            
            // Store the order data in the hidden input
            orderInput.value = JSON.stringify(currentOrder);
            
            // Show the order summary in the form
            const orderSummary = document.getElementById('orderSummary');
            
            // Show payment section for advance orders
            const paymentSection = document.getElementById('paymentSection');
            if (paymentSection) {
                paymentSection.style.display = 'block';
                
                // Set a flag to indicate this is an advance order
                let advanceOrderInput = document.getElementById('advanceOrderInput');
                if (!advanceOrderInput) {
                    advanceOrderInput = document.createElement('input');
                    advanceOrderInput.type = 'hidden';
                    advanceOrderInput.name = 'is_advance_order';
                    advanceOrderInput.id = 'advanceOrderInput';
                    advanceOrderInput.value = '1';
                    document.getElementById('reservationForm').appendChild(advanceOrderInput);
                }
            }
            
            if (orderSummary) {
                let html = '<h6>Your Order:</h6><ul class="list-group mb-3">';
                let total = 0;
                
                currentOrder.forEach(item => {
                    const itemPrice = item.addonPrice ? (item.price + item.addonPrice) : item.price;
                    const itemTotal = itemPrice * item.quantity;
                    total += itemTotal;
                    
                    html += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${item.name} x ${item.quantity}</span>
                                <span class="badge bg-primary rounded-pill">₱${itemTotal.toFixed(2)}</span>
                            </div>`;
                    
                    // Add add-on information if it exists
                    if (item.addonName) {
                        html += `
                            <div class="ms-3 mt-1 text-muted small">
                                + ${item.addonName} (₱${parseFloat(item.addonPrice).toFixed(2)} each)
                            </div>`;
                    }
                    
                    html += `</li>`;
                });
                
                html += `
                    <li class="list-group-item d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span>₱${total.toFixed(2)}</span>
                    </li>
                </ul>`;
                
                orderSummary.innerHTML = html;
            }
            
            // Show the reservation form modal
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationFormModal'));
            reservationModal.show();
        });

        // Handle Confirm Booking/Proceed to Payment button click
        document.getElementById('confirmBookingBtn')?.addEventListener('click', function() {
            const button = this;
            const buttonText = button.textContent.trim();
            
            // If button says "Proceed to Payment", validate payment fields
            if (buttonText.includes('Proceed to Payment')) {
                // Get form elements
                const paymentMethod = document.getElementById('paymentMethod');
                const paymentOption = document.getElementById('paymentOption');
                const customAmount = document.getElementById('customAmount');
                let isValid = true;
                let errorMessage = '';
                
                // Validate payment method
                if (!paymentMethod.value) {
                    isValid = false;
                    errorMessage = 'Please select a payment method';
                } 
                // If payment method is online, validate payment option
                else if (paymentMethod.value === 'online') {
                    if (!paymentOption.value || paymentOption.value === 'select') {
                        isValid = false;
                        errorMessage = 'Please select a payment option';
                        // Reset button state if validation fails
                        if (button.classList.contains('btn-spin')) {
                            button.innerHTML = originalButtonHTML;
                            button.classList.remove('btn-spin');
                            button.disabled = false;
                        }
                    } 
                    // If custom payment, validate amount
                    else if (paymentOption.value === 'custom') {
                        const amount = parseFloat(customAmount.value);
                        if (isNaN(amount) || amount <= 0) {
                            isValid = false;
                            errorMessage = 'Please enter a valid custom payment amount';
                            // Reset button state if validation fails
                            if (button.classList.contains('btn-spin')) {
                                button.innerHTML = originalButtonHTML;
                                button.classList.remove('btn-spin');
                                button.disabled = false;
                            }
                        }
                    }
                }
                
                if (!isValid) {
                    // Reset button state if validation fails
                    if (button.classList.contains('btn-spin')) {
                        button.innerHTML = originalButtonHTML;
                        button.classList.remove('btn-spin');
                        button.disabled = false;
                    }
                    
                    Swal.fire({
                        title: 'Required Fields',
                        text: errorMessage,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                // Add spinning effect to the button
                const originalButtonHTML = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                button.classList.add('btn-spin');
                button.disabled = true;
                
                // Prepare the data to be sent to the payment processor
                const reservationData = window.pendingReservation || {};
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const order = [];
                
                // Get package ID from multiple possible sources
                const urlParams = new URLSearchParams(window.location.search);
                let packageId = 
                    reservationData.package_id || 
                    urlParams.get('package_id') ||
                    (cart[0] && cart[0].package_id) ||
                    (cart[0] && cart[0].id);
                
                // Get the selected tables
                // Get the selected tables
                const selectedTables = [];
                document.querySelectorAll('#selectedTablesList .table-guest-input').forEach(input => {
                    const tableCard = input.closest('.card');
                    const tableName = tableCard.querySelector('h6')?.textContent.trim() || 'Table';
                    const capacity = parseInt(input.value) || 1;
                    
                    selectedTables.push({
                        id: input.dataset.tableId || input.dataset.id || '',
                        name: tableName,
                        capacity: capacity,
                    });
                });

                console.log('Selected tables:', selectedTables); // Debug log
                                
                // Calculate totals
                const totalAmountElement = document.getElementById('breakdownTotal');
                if (!totalAmountElement) {
                    console.error('Could not find totalAmount element');
                    return;
                }
                const totalAmount = parseFloat(totalAmountElement.textContent.replace('₱', '').replace(/,/g, ''));
                let paymentAmount = 0;
                let remainingAmount = 0;
                
                if (paymentOption.value === 'full') {
                    paymentAmount = totalAmount;
                    remainingAmount = 0;
                } else if (paymentOption.value === 'partial') {
                    // 50% downpayment
                    paymentAmount = totalAmount * 0.5;
                    remainingAmount = totalAmount - paymentAmount;
                } else if (paymentOption.value === 'custom') {
                    paymentAmount = parseFloat(customAmount.value);
                    remainingAmount = totalAmount - paymentAmount;
                }
                
                // Prepare order items
                cart.forEach(item => {
                    order.push({
                        id: item.id,
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        type: item.type || 'table_package'
                    });
                });
                
                // Create a form to submit the data
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = 'table_payment_process.php';
                
                // Get the current order from localStorage and prepare it with addons
                const currentOrder = JSON.parse(localStorage.getItem('currentOrder')) || [];
                
                // Process the order to include addons in the correct format
                const processedOrder = currentOrder.map(item => ({
                    id: item.itemId || item.id,
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    addons: item.addonId ? [{
                        id: item.addonId,
                        name: item.addonName,
                        price: item.addonPrice
                    }] : []
                }));
                
                // Add hidden inputs for all the data
                function addHiddenInput(name, value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }
                
                addHiddenInput('tables', JSON.stringify(selectedTables));
                addHiddenInput('date', document.getElementById('reservationDate').value);
                addHiddenInput('time', document.getElementById('reservationTime').value);
                
                // Add order items to the form with addons
                addHiddenInput('order', JSON.stringify(processedOrder));
                
                addHiddenInput('payment_method', paymentMethod.value);
                addHiddenInput('payment_option', paymentOption.value);
                addHiddenInput('payment_amount', paymentAmount.toFixed(2));
                addHiddenInput('remaining_amount', remainingAmount.toFixed(2));
                addHiddenInput('total_amount', totalAmount.toFixed(2));
                
                // Add customer details if available
                if (reservationData.name) addHiddenInput('customer_name', reservationData.name);
                if (reservationData.email) addHiddenInput('customer_email', reservationData.email);
                if (reservationData.phone) addHiddenInput('customer_phone', reservationData.phone);
                
                // Add the form to the document and submit it
                document.body.appendChild(form);
                form.submit();
                
            } else {
                // Original behavior for "Confirm Booking"
                Swal.fire({
                    title: 'Advance Order',
                    text: 'Do you want to make an advance order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, just book the table',
                    showDenyButton: false,
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user clicks Yes, open the menu modal
                            const menuModal = new bootstrap.Modal(document.getElementById('menuModal'));
                        menuModal.show();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // User clicked No, send data to table_without_order.php
                        const reservationData = window.pendingReservation || {};
                        
                        // Get package ID from multiple possible sources
                        const urlParams = new URLSearchParams(window.location.search);
                        const cart = JSON.parse(localStorage.getItem('cart')) || [];
                        
                        // Try to get package ID from different sources in order of priority
                        let packageId = 
                            reservationData.package_id || 
                            urlParams.get('package_id') ||
                            (cart[0] && cart[0].package_id) ||
                            (cart[0] && cart[0].id);
                        
                        console.log('Package ID sources:', {
                            fromReservationData: reservationData.package_id,
                            fromURL: urlParams.get('package_id'),
                            fromCartPackageId: cart[0] && cart[0].package_id,
                            fromCartId: cart[0] && cart[0].id
                        });
                        
                        if (!packageId) {
                            console.error('Package ID not found in any source');
                            throw new Error('Unable to determine package ID. Please try again.');
                        }
                        
                        // Get the selected tables from the form
                        const selectedTables = [];
                        document.querySelectorAll('.table-guest-input').forEach(input => {
                            selectedTables.push({
                                id: input.dataset.tableId || input.dataset.tableId,
                                capacity: parseInt(input.value) || 1
                            });
                        });
                        
                        // Get the reservation date and time from the form
                        const reservationDate = document.getElementById('reservationDate')?.value || reservationData.date;
                        const reservationTime = document.getElementById('reservationTime')?.value || reservationData.time;
                        const partySize = document.getElementById('partySize')?.value || reservationData.party_size || selectedTables.reduce((sum, table) => sum + (table.capacity || 1), 0);
                        
                        // Prepare the data to send in the format expected by the backend
                        const requestData = {
                            tables: [{
                                packageId: packageId,
                                quantity: 1 // Sending as a single table for now
                            }],
                            reservationDate: reservationDate,
                            reservationTime: reservationTime
                        };
                        
                        // If we have selected tables from the cart, use those instead
                        if (selectedTables.length > 0) {
                            requestData.tables = selectedTables.map(table => ({
                                packageId: table.id,
                                quantity: 1
                            }));
                        }
                        
                        console.log('Sending data to table_without_order.php:', requestData);
                        
                        // Send the reservation data to table_without_order.php
                        fetch('table_without_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(requestData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Your table has been reserved successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Redirect or refresh the page
                                    window.location.href = 'mybookings.php';
                                });
                            } else {
                                throw new Error(data.message || 'Failed to process reservation');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to process your reservation. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Handle Proceed to Checkout button
            document.getElementById('proceedToCheckout')?.addEventListener('click', function() {
                // Close the cart modal
                const cartModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
                if (cartModal) cartModal.hide();
                
                // Get cart items
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                if (cart.length === 0) {
                    showAlert('Your cart is empty', 'warning');
                    return;
                }
                
                // Get the selected date and time from the availability check
                const dateTimeStr = document.getElementById('reservationDateTime').value;
                let dateStr, timeStr;
                
                if (dateTimeStr) {
                    // If we have a datetime from the availability check, use it
                    const dateTime = new Date(dateTimeStr);
                    dateStr = dateTime.toISOString().split('T')[0];
                    timeStr = dateTime.getHours().toString().padStart(2, '0') + ':' + 
                             dateTime.getMinutes().toString().padStart(2, '0');
                } else {
                    // Fallback to current date and time if no selection
                    const now = new Date();
                    dateStr = now.toISOString().split('T')[0];
                    timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
                }
                
                // Fill the form with the selected values
                document.getElementById('reservationDate').value = dateStr;
                document.getElementById('reservationTime').value = timeStr;
                
                // Populate tables list
                const tablesList = document.getElementById('selectedTablesList');
                let html = '';
                let totalCapacity = 0;
                
                cart.forEach((item, index) => {
                    const tableName = item.name || `Table ${index + 1}`;
                    const capacity = parseInt(item.capacity) || 1;
                    totalCapacity += capacity;
                    
                    html += `
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <div>
                                    <h6 class="mb-0">${tableName}</h6>
                                    <small class="text-muted">Capacity: ${capacity} guests</small>
                                    <input type="hidden" 
                                           class="table-guest-input" 
                                           value="${capacity}"
                                           data-table-id="${item.id || index}">
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                tablesList.innerHTML = html;
                
                // Show the reservation form modal
                const reservationModal = new bootstrap.Modal(document.getElementById('reservationFormModal'));
                reservationModal.show();
            });
            
      
            
            // Set minimum datetime to now
            const now = new Date();
            // Format as YYYY-MM-DDThh:mm
            const nowStr = now.toISOString().slice(0, 16);
            document.getElementById('reservationDateTime').min = nowStr;
            
            // Function to validate time within cafe hours (7:00 AM - 11:00 PM)
            function isValidDateTime(datetimeStr) {
                const date = new Date(datetimeStr);
                const hours = date.getHours();
                const minutes = date.getMinutes();
                
                // Convert to minutes since midnight for easier comparison
                const totalMinutes = hours * 60 + minutes;
                
                // Check if time is within 7:00 AM to 11:00 PM (23:00)
                const isWithinHours = totalMinutes >= 7 * 60 && totalMinutes <= 23 * 60;
                
                // Allow all days of the week
                return isWithinHours;
            }
            
            // Handle availability form submission
            document.getElementById('checkAvailabilityForm').addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submission started');
                
                const checkButton = document.getElementById('checkAvailabilityBtn');
                const originalButtonText = checkButton.innerHTML;
                
                // Show loading state
                checkButton.disabled = true;
                checkButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Checking...';
                
                const dateTimeStr = document.getElementById('reservationDateTime').value;
                
                console.log('Form values:', { dateTime: dateTimeStr });
                
                if (!dateTimeStr) {
                    console.log('Validation failed: Missing date and time');
                    // Reset button state
                    checkButton.disabled = false;
                    checkButton.innerHTML = originalButtonText;
                    showAlert('Please select date and time', 'danger');
                    return;
                }
                
                // Validate the selected date and time
                if (!isValidDateTime(dateTimeStr)) {
                    // Reset button state
                    checkButton.disabled = false;
                    checkButton.innerHTML = originalButtonText;
                    showAlert('Please select a valid date and time (Monday-Saturday, 11:00 AM - 9:00 PM)', 'danger');
                    return;
                }
                
                // Split the datetime string into date and time components for the API
                const dateTime = new Date(dateTimeStr);
                const date = dateTime.toISOString().split('T')[0];
                const time = dateTime.toTimeString().slice(0, 5); // HH:MM
                
                // Show loading state (only set once)
                if (!checkButton.disabled) {
                    checkButton.disabled = true;
                    checkButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
                }
                
                console.log('Sending request to table_availability.php');
                
                // Make AJAX call to check availability
                fetch('table_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `datetime=${encodeURIComponent(dateTimeStr)}`,
                    credentials: 'same-origin' // Include cookies if needed
                })
                .then(response => {
                    console.log('Response received, status:', response.status);
                    if (!response.ok) {
                        console.error('Response not OK, status:', response.status);
                        return response.text().then(text => {
                            console.error('Response text:', text);
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Availability response:', data);
                    
                    // Reset button state
                    checkButton.disabled = false;
                    checkButton.innerHTML = '<i class="fas fa-search me-2"></i> Check';
                    
                    if (data && data.success && data.data) {
                        // Set the flag to true when availability is successfully checked
                        availabilityChecked = true;
                        const availableTables = data.data;
                        const count = availableTables.filter(table => table.available_tables > 0).length;
                        
                        // Store available tables data for later use
                        window.availableTables = availableTables;
                        
                        if (count > 0) {
                            let message = `Found ${count} table type${count > 1 ? 's' : ''} with available tables for your selected time!`;
                            // Add booking count to the message if available
                            if (data.total_bookings !== undefined) {
                                message += `\nTotal bookings for this time: ${data.total_bookings}`;
                            }
                            console.log(message);
                            
                            // Show success message
                            Swal.fire({
                                title: 'Tables Available!',
                                text: message,
                                icon: 'success',
                                confirmButtonText: 'Great!',
                                confirmButtonColor: '#28a745',
                                timer: 5000,
                                timerProgressBar: true,
                                showCloseButton: true
                            });
                            
                            // Also show the toast notification
                            showAlert(message, 'success', 5000);
                            
                            console.log('Available tables:', availableTables);
                            
                            // Update the packages section with available tables
                            updateAvailableTables(availableTables);
                            
                            // Scroll to packages section
                            document.querySelector('.package-section').scrollIntoView({ 
                                behavior: 'smooth',
                                block: 'start'
                            });
                            
                            // Store available tables data for later use
                            window.availableTables = availableTables;
                            
                        } else {
                            console.log('No tables available');
                            showAlert('No tables available for the selected date and time. Please try a different time.', 'warning');
                        }
                    } else if (data && data.conflict) {
                        // Handle time slot conflict with next available time
                        let message = data.message || 'Time slot not available';
                        if (data.available_after) {
                            message += `. Next available time: ${data.available_after}`;
                        }
                        console.log('Time slot conflict:', message);
                        showAlert(message, 'warning');
                    } else {
                        console.error('Unexpected response format:', data);
                        showAlert(data && data.message ? data.message : 'Error checking availability', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error in fetch:', error);
                    // Reset button state on error
                    checkButton.disabled = false;
                    checkButton.innerHTML = originalButtonText;
                    const errorMessage = error.message || 'An error occurred while checking availability';
                    console.error('Error details:', errorMessage);
                    showAlert(errorMessage, 'danger');
                    // Ensure button is reset even if there's an error in showAlert
                    setTimeout(() => {
                        checkButton.disabled = false;
                        checkButton.innerHTML = originalButtonText;
                    }, 100);
                });
            });
            
            // Function to check if user is logged in
            function checkLogin() {
                return fetch('check_session.php')
                    .then(response => response.json())
                    .then(data => data.logged_in)
                    .catch(error => {
                        console.error('Error checking login status:', error);
                        return false;
                    });
            }

            // Handle add to cart button click
            document.addEventListener('click', async function(e) {
                if (e.target.closest('.add-to-cart')) {
                    e.preventDefault();
                    
                    // Check if user is logged in
                    const isLoggedIn = await checkLogin();
                    if (!isLoggedIn) {
                        showAlert('Please log in to add items to your cart.', 'warning');
                        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                        return;
                    }
                    
                    // Check if availability has been checked
                    if (!availabilityChecked) {
                        showAlert('Please check table availability first by selecting a date and time and clicking the CHECK button.', 'warning');
                        // Optionally, scroll to the availability form
                        document.getElementById('checkAvailabilityForm').scrollIntoView({ behavior: 'smooth' });
                        return;
                    }
                    
                    const button = e.target.closest('.add-to-cart');
                    const tableId = button.dataset.packageId;
                    const item = {
                        id: tableId,
                        name: button.dataset.packageName,
                        quantity: 1,
                        type: 'table',
                        capacity: button.dataset.capacity || '2'
                    };
                    
                    // Get existing cart or initialize empty array
                    const cart = JSON.parse(localStorage.getItem('cart')) || [];
                    
                    // Get current cart quantity for this table type
                    const currentQuantity = cart
                        .filter(cartItem => cartItem.id === tableId && cartItem.type === 'table')
                        .reduce((total, item) => total + (parseInt(item.quantity) || 0), 0);
                    
                    // Find available tables for this table type
                    const tableAvailability = window.availableTables?.find(t => t.table_type_id == tableId);
                    const availableTables = tableAvailability?.available_tables || 0;
                    
                    // Check if adding one more would exceed availability
                    if (currentQuantity >= availableTables) {
                        showAlert(`Sorry, only ${availableTables} ${availableTables === 1 ? 'table is' : 'tables are'} available for ${item.name}.`, 'warning');
                        return;
                    }
                    
                    // Check if item already exists in cart
                    const existingItemIndex = cart.findIndex(cartItem => 
                        cartItem.id === tableId && cartItem.type === 'table'
                    );
                    
                    if (existingItemIndex > -1) {
                        // Update quantity if item exists
                        cart[existingItemIndex].quantity += 1;
                    } else {
                        // Add new item to cart
                        cart.push(item);
                    }
                    
                    // Save back to localStorage
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Update cart UI
                    updateCartItemCount();
                    
                    // Show success message
                    showAlert('Item added to cart!', 'success');
                    
                    // Dispatch storage event to update other tabs/windows
                    window.dispatchEvent(new Event('storage'));
                }
            });
            
            // Handle reservation button click (for existing reservation functionality)
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            const reserveButtons = document.querySelectorAll('.btn-reserve:not(.add-to-cart)');
            
            reserveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const packageId = this.getAttribute('data-package-id');
                    const packageName = this.getAttribute('data-package-name');
                    const capacity = this.getAttribute('data-package-capacity');
                    
                    // Set values in the modal
                    document.getElementById('packageId').value = packageId;
                    document.getElementById('packageName').value = packageName;
                    document.getElementById('partySizeInput').max = capacity;
                    document.getElementById('partySizeInput').value = ''; // Remove default value
                    
                    // Set minimum date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('reservationDateInput').min = today;
                    
                    // Show the modal
                    reservationModal.show();
                });
            });
            
            // Function to show flash alert
            function showAlert(message, type = 'danger') {
                // Remove any existing alerts
                const existingAlert = document.getElementById('formAlert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Also show a toast notification for better visibility
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                
                Toast.fire({
                    icon: type === 'danger' ? 'error' : type,
                    title: message
                });
                
                // Create alert element
                const alertDiv = document.createElement('div');
                alertDiv.id = 'formAlert';
                alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-4`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                // Insert alert at the top of the form
                const form = document.getElementById('reservationForm');
                form.insertBefore(alertDiv, form.firstChild);
                
                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    if (alertDiv) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
            
            // Handle reservation form submission
            document.getElementById('reservationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form elements
                const dateInput = document.getElementById('reservationDateInput');
                const timeInput = document.getElementById('reservationTimeInput');
                const partySizeInput = document.getElementById('partySizeInput');
                
                // Reset any previous error states
                [dateInput, timeInput, partySizeInput].forEach(input => {
                    input.classList.remove('is-invalid');
                });
                
                // Validate required fields
                let isValid = true;
                const errors = [];
                
                if (!dateInput.value) {
                    dateInput.classList.add('is-invalid');
                    errors.push('Please select a date');
                    isValid = false;
                }
                
                if (!timeInput.value) {
                    timeInput.classList.add('is-invalid');
                    errors.push('Please select a time');
                    isValid = false;
                }
                
                if (!partySizeInput.value) {
                    partySizeInput.classList.add('is-invalid');
                    errors.push('Please enter the number of guests');
                    isValid = false;
                } else if (isNaN(parseInt(partySizeInput.value)) || parseInt(partySizeInput.value) < 1) {
                    partySizeInput.classList.add('is-invalid');
                    errors.push('Please enter a valid number of guests (minimum 1)');
                    isValid = false;
                }
                
                if (!isValid) {
                    showAlert(`<i class="fas fa-exclamation-circle me-2"></i>${errors.join('<br>')}`);
                    return;
                }
                
                const selectedTime = timeInput.value;
                const partySize = parseInt(partySizeInput.value);
                const capacity = parseInt(partySizeInput.max);
                
                // Validate time is within cafe hours
                const time = new Date(`2000-01-01T${selectedTime}`);
                const hours = time.getHours();
                const minutes = time.getMinutes();
                const totalMinutes = hours * 60 + minutes;
                
                // Check if time is within 7:00 AM to 11:00 PM
                if (totalMinutes < 7 * 60 || totalMinutes > 23 * 60) {
                    timeInput.classList.add('is-invalid');
                    showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please select a time between 7:00 AM and 11:00 PM. Dining is available for up to 4 hours.');
                    timeInput.focus();
                    return;
                }
                
                // Validate number of guests doesn't exceed capacity
                if (partySize > capacity) {
                    partySizeInput.classList.add('is-invalid');
                    showAlert(`<i class="fas fa-exclamation-circle me-2"></i>Number of guests (${partySize}) exceeds package capacity (${capacity}). Please select a smaller party size.`);
                    partySizeInput.focus();
                    return;
                }
                
                // Get form data
                const formData = {
                    package_id: document.getElementById('packageId').value,
                    package_name: document.getElementById('packageName').value,
                    date: document.getElementById('reservationDateInput').value,
                    time: selectedTime,
                    party_size: partySize
                };
                
                // Store form data for later use
                window.pendingReservation = formData;
                
                // Hide the current modal
                const reservationModal = bootstrap.Modal.getInstance(document.getElementById('reservationModal'));
                reservationModal.hide();
                
                // Show the action selection modal
                const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
                actionModal.show();
            });
        });
        
        // Function to update available tables in the UI
        function updateAvailableTables(availableTables) {
            console.log('Updating available tables:', availableTables);
            
            // Show the availability results section
            const resultsContainer = document.getElementById('availabilityResults');
            const availabilityMessage = document.getElementById('availabilityMessage');
            const tableAvailability = document.getElementById('tableAvailability');
            
            // Clear previous results
            tableAvailability.innerHTML = '';
            
            // Safely get the tables array
            const tablesData = Array.isArray(availableTables) ? availableTables : 
                             (availableTables && Array.isArray(availableTables.data) ? availableTables.data : []);
            
            // Count available table types
            const availableCount = tablesData.filter(table => table.available_tables > 0).length;
            
            if (availableCount > 0) {
                // Update the message
                availabilityMessage.textContent = `Found ${availableCount} table type${availableCount > 1 ? 's' : ''} with available tables!`;
                
                // Create a list of available tables
                const list = document.createElement('div');
                list.className = 'mt-2';
                
                tablesData.forEach(table => {
                    if (table.available_tables > 0) {
                        const item = document.createElement('div');
                        item.className = 'd-flex justify-content-between align-items-center mb-2';
                        item.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-utensils me-2"></i>
                                <span class="fw-medium">${table.table_name || 'Table'}</span>
                            </div>
                            <span class="badge bg-success">${table.available_tables} available</span>
                        `;
                        list.appendChild(item);
                    }
                });
                
                tableAvailability.appendChild(list);
                resultsContainer.style.display = 'block';
            } else {
                availabilityMessage.textContent = 'No tables available for the selected date and time.';
                resultsContainer.style.display = 'block';
            }
            
            // Update all table cards with current availability
            updateTableCards(tablesData);
            
            // Scroll to show the results
            resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Function to update table cards with availability information
        function updateTableCards(availableTables) {
            const tableCards = document.querySelectorAll('.package-card');
            
            if (tableCards.length === 0) {
                console.warn('No table cards found to update');
                return;
            }
            
            // Ensure availableTables is an array
            const tablesData = Array.isArray(availableTables) ? availableTables : [];
            
            tableCards.forEach(card => {
                const tableId = parseInt(card.dataset.tableId);
                const tableData = tablesData.find(t => t.table_type_id === tableId);
                
                if (tableData) {
                    const availableCount = parseInt(tableData.available_tables) || 0;
                    const isAvailable = availableCount > 0;
                    
                    // Update availability badge
                    const availabilityBadge = card.querySelector('.availability-badge');
                    if (availabilityBadge) {
                        availabilityBadge.className = `badge ${isAvailable ? 'bg-success' : 'bg-danger'} p-2 availability-badge`;
                        availabilityBadge.innerHTML = `
                            <i class="fas ${isAvailable ? 'fa-check' : 'fa-times'}-circle me-1"></i>
                            ${isAvailable ? 'Available' : 'Unavailable'}
                        `;
                    }
                    
                    // Update table count
                    const tableCountElement = card.querySelector('.table-count');
                    if (tableCountElement) {
                        tableCountElement.textContent = `${availableCount} table${availableCount !== 1 ? 's' : ''} available`;
                    }
                    
                    // Update add to cart button
                    const addToCartBtn = card.querySelector('.add-to-cart');
                    if (addToCartBtn) {
                        addToCartBtn.disabled = !isAvailable;
                        addToCartBtn.className = `btn w-100 ${isAvailable ? 'btn-primary' : 'btn-secondary'} add-to-cart`;
                        addToCartBtn.innerHTML = `
                            <i class="fas ${isAvailable ? 'fa-plus' : 'fa-times'} me-2"></i>
                            ${isAvailable ? 'ADD TO LIST' : 'UNAVAILABLE'}
                        `;
                        addToCartBtn.dataset.available = isAvailable ? '1' : '0';
                    }
                } else {
                    // If no data for this table, mark as unavailable
                    const availabilityBadge = card.querySelector('.availability-badge');
                    if (availabilityBadge) {
                        availabilityBadge.className = 'badge bg-danger p-2 availability-badge';
                        availabilityBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Unavailable';
                    }
                    
                    const tableCountElement = card.querySelector('.table-count');
                    if (tableCountElement) {
                        tableCountElement.textContent = '0 tables available';
                    }
                    
                    const addToCartBtn = card.querySelector('.add-to-cart');
                    if (addToCartBtn) {
                        addToCartBtn.disabled = true;
                        addToCartBtn.className = 'btn w-100 btn-secondary add-to-cart';
                        addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>UNAVAILABLE';
                        addToCartBtn.dataset.available = '0';
                    }
                }
            });
        }
        
        // Handle action selection
        document.addEventListener('DOMContentLoaded', function() {
            // Proceed to Summary
            document.getElementById('proceedToSummary').addEventListener('click', function() {
                const actionModal = bootstrap.Modal.getInstance(document.getElementById('actionModal'));
                actionModal.hide();
                
                // Populate the summary modal with reservation details
                const res = window.pendingReservation;
                document.getElementById('summaryPackage').textContent = res.package_name;
                
                // Format the date to be more readable (e.g., "Monday, January 1, 2023")
                const date = new Date(res.date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('summaryDate').textContent = date.toLocaleDateString('en-US', options);
                
                // Format the time (e.g., "2:30 PM")
                const time = new Date(`2000-01-01T${res.time}`);
                document.getElementById('summaryTime').textContent = time.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
                
                document.getElementById('summaryGuests').textContent = res.party_size + (res.party_size > 1 ? ' Guests' : ' Guest');
                
                // Show the summary modal
                const summaryModal = new bootstrap.Modal(document.getElementById('summaryModal'));
                summaryModal.show();
            });

            
        });
    </script>
</body>
</html>