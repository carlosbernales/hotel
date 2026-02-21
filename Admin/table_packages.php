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
    <!-- Add Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        /* Menu Categories Styles */
        .menu-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .category-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 20px;
            background: white;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .category-btn:hover {
            background: #f0f0f0;
            border-color: #d4af37;
        }

        .category-btn.active {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
        }

        /* Menu Items Grid Styles */
        .menu-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            max-height: 500px;
            overflow-y: auto;
        }

        .menu-item-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            background: white;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .menu-item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .menu-item-details {
            padding: 12px;
        }

        .menu-item-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .menu-item-price {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Current Order Styles */
        .current-order-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .current-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-control input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
        }

        .order-items-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .order-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        /* Payment Section Styles */
        .payment-options {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .payment-options .form-group {
            margin-bottom: 15px;
        }

        .payment-options label {
            font-weight: 500;
            color: #333;
        }

        #placeOrderBtn {
            margin-top: 10px;
            font-weight: 500;
        }

        /* Scrollbar Styles */
        .menu-items-grid::-webkit-scrollbar {
            width: 8px;
        }

        .menu-items-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .menu-items-grid::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        .menu-items-grid::-webkit-scrollbar-thumb:hover {
            background: #c19b2c;
        }

        /* Updated styles for the advance order modal */
        .modal-xl {
            max-width: 95%;
        }

        .menu-categories {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .category-btn {
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 20px;
            background: white;
            color: #333;
            transition: all 0.3s ease;
        }

        .category-btn:hover {
            background: #f0f0f0;
            border-color: #d4af37;
        }

        .category-btn.active {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
        }

        .menu-items-container {
            height: calc(100vh - 350px);
            overflow-y: auto;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        .menu-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-item-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            background: white;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .menu-item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .menu-item-details {
            padding: 15px;
        }

        .menu-item-title {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .menu-item-price {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-items-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .order-item {
            padding: 15px;
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
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Scrollbar Styles */
        .menu-items-container::-webkit-scrollbar {
            width: 8px;
        }

        .menu-items-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .menu-items-container::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        .menu-items-container::-webkit-scrollbar-thumb:hover {
            background: #c19b2c;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        /* Menu Categories Styles */
        .list-group-item {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .list-group-item:hover {
            background-color: #ffc107;
            color: #000;
        }
        .list-group-item.active {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        /* Menu Items Styles */
        .menu-item-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .menu-item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .menu-item-details {
            padding: 15px;
        }
        .menu-item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .menu-item-price {
            color: #28a745;
            font-weight: bold;
        }

        /* Current Order Styles */
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item-details {
            flex-grow: 1;
        }
        .order-item-name {
            font-weight: bold;
        }
        .order-item-price {
            color: #28a745;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            border: none;
            background: #eee;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .quantity-btn:hover {
            background: #ddd;
        }
        
        /* Modal Styles */
        .modal-lg {
            max-width: 1000px;
        }
        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
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
                        <input type="hidden" id="hiddenPaymentOption" name="paymentOption" value="">
                        <input type="hidden" id="hiddenPaymentMethod" name="paymentMethod" value="">
                        <input type="hidden" id="hiddenTotalAmount" name="totalAmount" value="">
                        
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="advanceOrderModalLabel">Make Advance Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Menu Categories</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div id="menuCategories" class="list-group list-group-flush">
                                        <!-- Categories will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Menu Items</h6>
                                </div>
                                <div class="card-body">
                                    <div id="menuItems" class="row">
                                        <!-- Menu items will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Current Order</h6>
                            </div>
                            <div class="card-body">
                                <div id="currentOrderItems" class="d-none">
                                    <!-- Order items will be displayed here -->
                                </div>
                                <div class="mt-3 d-flex justify-content-between align-items-center" id="newTotalAmountRow">
                                    <h6 class="mb-0">Total Amount (Live):</h6>
                                    <h5 class="mb-0 text-success">₱<span id="newTotalAmount">0.00</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="form-group">
                            <label for="paymentOptionDropdown" class="mb-2">Payment Option</label>
                            <select class="form-control" id="paymentOptionDropdown" name="paymentOption">
                                <option value="">Select payment option</option>
                                <option value="full">Full Payment</option>
                                <option value="down">Down Payment (50%)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select class="form-control" id="paymentMethod">
                                <option value="">Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="card">Credit/Debit Card</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="placeOrderBtn">
                        <i class="fas fa-shopping-cart"></i> PLACE ORDER
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add required scripts -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
    // Initialize current order array
    let currentOrder = [];
    
    // Static list of disabled dates (YYYY-MM-DD format)
    const disabledDates = [
        '2025-11-10',
        '2025-11-15',
        '2025-11-20',
        '2025-11-25',
        '2025-11-30',
        '2025-12-05',
        '2025-12-10',
        '2025-12-15',
        '2025-12-20',
        '2025-12-25',
        '2025-12-31'
    ];
    
    let flatpickrInstance = null;

    // Function to initialize the date picker
    function initializeDatePicker() {
        const today = new Date();
        
        flatpickrInstance = flatpickr("#reservationDate", {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            // Disable weekends and our static disabled dates
            disable: [
                function(date) {
                    // Disable weekends (0 = Sunday, 6 = Saturday)
                    return (date.getDay() === 0 || date.getDay() === 6);
                },
                // Add our static disabled dates
                ...disabledDates
            ]
        });
        
        // Set the minimum time to 1 hour from now
        const timeInput = document.getElementById('arrivalTime');
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        timeInput.min = `${hours}:${minutes}`;
    }

    $(document).ready(function() {
        // Initialize the date picker
        initializeDatePicker();
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

        // Place Order button handler
        $('#placeOrderBtn').off('click').on('click', function() {
            const paymentOption = $('#paymentOptionDropdown').val();
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
            // Set hidden fields for confirmation modal
            $('#hiddenPaymentOption').val(paymentOption);
            $('#hiddenPaymentMethod').val(paymentMethod);
            // Show confirmation modal with order/payment details
            showOrderConfirmationModal();
        });
    });

    function updateOrderDisplay() {
        console.log('Updating order display, current order:', currentOrder);
        const container = $('#orderItems');
        let html = '';
        let totalAmount = 0;
        let itemsHtml = '';
        
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
            // For the currentOrderItems div
            itemsHtml += `
                <div class='d-flex justify-content-between align-items-center mb-2'>
                    <div>${item.name} <span class='badge badge-secondary'>${item.quantity || 1}</span></div>
                    <div>₱${itemTotal.toFixed(2)}</div>
                </div>
            `;
        });

        if (currentOrder.length === 0) {
            html = '<p class="text-muted text-center">Your order is empty</p>';
            itemsHtml = '';
            $('#currentOrderItems').addClass('d-none');
        } else {
            html += `
                <div class="total mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Amount:</strong>
                        <strong class="text-success">₱${totalAmount.toFixed(2)}</strong>
                    </div>
                </div>
            `;
            $('#currentOrderItems').removeClass('d-none');
        }

        container.html(html);
        $('#orderTotal').text(totalAmount.toFixed(2));
        // Update new total amount display
        $('#newTotalAmount').text(totalAmount.toFixed(2));
        // Update the currentOrderItems div with the list of items
        $('#currentOrderItems').html(itemsHtml);
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
                // Fix image path: use 'uploads/menus/' if image_path is present
                let imagePath = item.image_path;
                if (imagePath) {
                    if (!imagePath.startsWith('http') && !imagePath.startsWith('/') && !imagePath.startsWith('uploads/menus/')) {
                        imagePath = 'uploads/menus/' + imagePath.replace(/^uploads[\/]/, '');
                    } else if (imagePath.startsWith('uploads/menus/')) {
                        // already correct
                    } else if (imagePath.startsWith('/')) {
                        imagePath = imagePath.substring(1);
                    }
                } else {
                    imagePath = 'images/default-food.jpg';
                }
                container.append(`
                    <div class="menu-item-card">
                        <img src="${imagePath}" 
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

    function showOrderConfirmationModal() {
        // Gather all details
        const paymentOption = $('#hiddenPaymentOption').val();
        const paymentMethod = $('#hiddenPaymentMethod').val();
        const orderItems = currentOrder;
        const totalAmount = orderItems.reduce((sum, item) => sum + item.price * (item.quantity || 1), 0);
        const amountToPay = paymentOption === 'down' ? totalAmount * 0.5 : totalAmount;

        // Build confirmation HTML
        let itemsHtml = '<ul class="list-group mb-3">';
        orderItems.forEach(item => {
            itemsHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${item.name} × ${item.quantity || 1}</span>
                <span>₱${(item.price * (item.quantity || 1)).toFixed(2)}</span>
            </li>`;
        });
        itemsHtml += '</ul>';

        let confirmHtml = `
            <div class="modal fade" id="orderConfirmModal" tabindex="-1" role="dialog" aria-labelledby="orderConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderConfirmModalLabel">Confirm Your Advance Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Order Items</h6>
                            ${itemsHtml}
                            <div class="mb-2"><strong>Total Amount:</strong> ₱${totalAmount.toFixed(2)}</div>
                            <div class="mb-2"><strong>Payment Option:</strong> ${paymentOption === 'down' ? 'Down Payment (50%)' : 'Full Payment'}</div>
                            <div class="mb-2"><strong>Amount to Pay:</strong> ₱${amountToPay.toFixed(2)}</div>
                            <div class="mb-2"><strong>Payment Method:</strong> ${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmOrderBtn">Confirm Order</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove any existing confirmation modal
        $('#orderConfirmModal').remove();
        // Append and show the modal
        $('body').append(confirmHtml);
        $('#orderConfirmModal').modal('show');

        // Confirm button handler
        $('#confirmOrderBtn').off('click').on('click', function() {
            // Build the advance order details object
            const paymentOption = $('#hiddenPaymentOption').val();
            const paymentMethod = $('#hiddenPaymentMethod').val();
            const orderItems = currentOrder;
            const totalAmount = orderItems.reduce((sum, item) => sum + item.price * (item.quantity || 1), 0);
            const amountToPay = paymentOption === 'down' ? totalAmount * 0.5 : totalAmount;
            const orderDetails = {
                items: orderItems,
                paymentOption: paymentOption,
                paymentMethod: paymentMethod,
                totalAmount: totalAmount.toFixed(2),
                amountToPay: amountToPay.toFixed(2)
            };
            // Set the global variable and hidden input value
            window.latestAdvanceOrder = orderDetails;
            $('input[name="advance_order"]').val(JSON.stringify(orderDetails));
            // Set main form hidden fields for validation
            $('#hiddenPaymentMethod').val(paymentMethod);
            $('#hiddenTotalAmount').val(totalAmount.toFixed(2));
            $('#orderConfirmModal').modal('hide');
            setTimeout(() => {
                $('#advanceOrderModal').modal('hide');
                $('#reservationModal').modal('show');
            }, 500);
        });
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
        const paymentOption = $('#hiddenPaymentOption').val() || $('#paymentOptionDropdown').val();
        const paymentMethod = $('#hiddenPaymentMethod').val() || $('#paymentMethod').val();
        
        // Validate required fields
        const requiredFields = [
            { name: 'customerName', value: customerName },
            { name: 'contactNumber', value: contactNumber },
            { name: 'reservationDate', value: reservationDate },
            { name: 'arrivalTime', value: arrivalTime },
            { name: 'tableType', value: tableType },
            { name: 'paymentOption', value: paymentOption },
            { name: 'paymentMethod', value: paymentMethod }
        ];
        const missingFields = requiredFields.filter(field => !field.value);
        
        if (missingFields.length > 0) {
            let debugHtml = '<div class="alert alert-danger"><strong>Required Fields Missing</strong><br><pre>';
            missingFields.forEach(field => {
                debugHtml += `${field.name}: "${field.value}"
`;
            });
            debugHtml += '</pre></div>';
            // Show this debug info at the top of the reservation modal
            if ($('#reservationModal .modal-body .alert-danger').length) {
                $('#reservationModal .modal-body .alert-danger').replaceWith(debugHtml);
            } else {
                $('#reservationModal .modal-body').prepend(debugHtml);
            }
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
        let orderDetails = window.latestAdvanceOrder;
        if (!orderDetails) {
            const advanceOrderInput = $('input[name="advance_order"]').val();
            if (advanceOrderInput) {
                try {
                    orderDetails = JSON.parse(advanceOrderInput);
                } catch (error) { orderDetails = null; }
            }
        }

        // Store advance order in a hidden field in confirmation modal
        if (orderDetails && orderDetails.items && orderDetails.items.length > 0) {
            // Create or update a hidden field for confirmation modal
            if ($('#confirmationModal input[name="confirm_advance_order"]').length) {
                $('#confirmationModal input[name="confirm_advance_order"]').val(JSON.stringify(orderDetails));
            } else {
                $('#confirmationModal .modal-body').append(`
                    <input type="hidden" name="confirm_advance_order" value='${JSON.stringify(orderDetails)}'>
                `);
            }
            
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
            // Add payment info
            orderItemsHtml += `
                <div class="mt-2"><strong>Payment Option:</strong> ${orderDetails.paymentOption === 'down' ? 'Down Payment (50%)' : 'Full Payment'}</div>
                <div><strong>Payment Method:</strong> ${orderDetails.paymentMethod.charAt(0).toUpperCase() + orderDetails.paymentMethod.slice(1)}</div>
                <div><strong>Total Amount:</strong> ₱${orderDetails.totalAmount}</div>
                <div><strong>Amount to Pay:</strong> ₱${orderDetails.amountToPay}</div>
            `;
            // Update advance order details in confirmation modal
            $('#confirm-order-items').html(orderItemsHtml);
            $('#confirm-payment-method').text(
                orderDetails.paymentMethod.charAt(0).toUpperCase() + 
                orderDetails.paymentMethod.slice(1)
            );
            $('#confirm-total-amount').text(orderDetails.totalAmount);
            $('#confirm-amount-to-pay').text(orderDetails.amountToPay);
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

        // Get advance order details
        let advanceOrder = null;
        
        // First try to get from confirmation modal
        const confirmOrderInput = $('#confirmationModal input[name="confirm_advance_order"]').val();
        if (confirmOrderInput) {
            try {
                advanceOrder = JSON.parse(confirmOrderInput);
                console.log('Got advance order from confirmation modal:', advanceOrder);
            } catch (error) {
                console.error('Error parsing confirmation modal order:', error);
            }
        }
        
        // If not found in confirmation modal, try form input
        if (!advanceOrder) {
            const advanceOrderInput = $('input[name="advance_order"]').val();
            if (advanceOrderInput) {
                try {
                    advanceOrder = JSON.parse(advanceOrderInput);
                    console.log('Got advance order from form input:', advanceOrder);
                } catch (error) {
                    console.error('Error parsing form input order:', error);
                }
            }
        }
        
        // If not found in either place, try global variable
        if (!advanceOrder && window.currentAdvanceOrder) {
            advanceOrder = window.currentAdvanceOrder;
            console.log('Got advance order from global variable:', advanceOrder);
        }
        
        // If we have advance order data, add it to form data
        if (advanceOrder && advanceOrder.items && advanceOrder.items.length > 0) {
            console.log('Adding advance order to form data:', advanceOrder);
            formData.advanceOrder = {
                items: advanceOrder.items,
                totalAmount: advanceOrder.totalAmount,
                paymentOption: advanceOrder.paymentOption,
                paymentMethod: advanceOrder.paymentMethod,
                amountToPay: advanceOrder.amountToPay
            };
            formData.paymentOption = advanceOrder.paymentOption;
            formData.paymentMethod = advanceOrder.paymentMethod;
            formData.totalAmount = advanceOrder.totalAmount;
            formData.amountToPay = advanceOrder.amountToPay;
        } else {
            console.log('No advance order data found');
            // Set default payment values if no advance order
            formData.paymentMethod = $('#paymentMethod').val();
            formData.paymentOption = 'full';
            formData.totalAmount = '0.00';
            formData.amountToPay = '0.00';
        }

        // Validate required fields
        const requiredFields = [
            'customerName', 
            'contactNumber', 
            'packageType', 
            'reservationDate', 
            'reservationTime',
            'paymentMethod'
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

        console.log('Sending final form data:', formData);

        // Submit the reservation
        $.ajax({
            url: 'process_table_reservation.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                console.log('Server response:', response);
                
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log('Parsed response:', result);
                    
                    if (result.success) {
                        console.log('Reservation successful:', result);
                        
                        // Store the advance order details for the success modal
                        if (formData.advanceOrder) {
                            window.currentAdvanceOrder = formData.advanceOrder;
                        }
                        
                        // Close confirmation modal and show success
                        $('#confirmationModal').modal('hide');
                        setTimeout(() => {
                            showSuccessMessage(result.booking_details);
                        }, 500);
                    } else {
                        console.error('Reservation failed:', result.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Reservation Failed',
                            text: result.message || 'Failed to process reservation'
                        });
                        restoreConfirmationButtons();
                    }
                } catch (error) {
                    console.error('Error processing response:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your reservation'
                    });
                    restoreConfirmationButtons();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', { xhr, status, error });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit reservation. Please try again.'
                });
                restoreConfirmationButtons();
            }
        });
    }

    function restoreConfirmationButtons() {
        $('#confirmationModal .modal-footer').html(`
            <button type="button" class="btn btn-secondary" onclick="editReservation()">Edit</button>
            <button type="button" class="btn btn-primary" onclick="submitReservation()">Confirm Reservation</button>
        `);
    }

    function showSuccessMessage(details) {
        console.log("Showing success message with details:", details);
        
        // First update with the details we already have
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
        
        // Check if we have an advance_order_id
        const advanceOrderId = details.advance_order_id;
        const bookingId = details.booking_id;
        
        if (advanceOrderId || bookingId) {
            // Make an AJAX call to get the complete advance order details
            $.ajax({
                url: 'get_advance_order.php',
                type: 'GET',
                data: {
                    advance_order_id: advanceOrderId || null,
                    booking_id: bookingId || null
                },
                success: function(response) {
                    console.log("Retrieved advance order details:", response);
                    
                    if (response.success && response.booking_details) {
                        // Update booking details with any new information
                        const bookingDetails = response.booking_details;
                        
                        // Update customer name if we got a more accurate one
                        if (bookingDetails.customer_name) {
                            $('#customer-name').text(bookingDetails.customer_name);
                        }
                        
                        // Process order items if available
                        if (bookingDetails.order_items && bookingDetails.order_items.length > 0) {
                            console.log("Rendering order items from advance_table_orders");
                            let orderItemsHtml = processOrderItems(
                                bookingDetails.order_items,
                                bookingDetails.payment_option || 'full',
                                bookingDetails.payment_method,
                                bookingDetails.total_amount,
                                bookingDetails.amount_to_pay
                            );
                            $('#success-order-items').html(orderItemsHtml);
                        } else {
                            // If no order items found, fall back to previous approach
                            checkForFallbackOrderItems(details);
                        }
                    } else {
                        // If API call failed, fall back to previous approach
                        console.log("API call failed, using fallback method");
                        checkForFallbackOrderItems(details);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error retrieving advance order details:", error);
                    // Fallback to the original method
                    checkForFallbackOrderItems(details);
                }
            });
        } else {
            // No advance order ID, fallback to the original method
            checkForFallbackOrderItems(details);
        }
        
        // Helper function to check for order items using the original approach
        function checkForFallbackOrderItems(details) {
            console.log("Using fallback method to find order details");
            
            // Check for order details using various methods
            console.log("Checking for order details:");
            console.log("- order_id:", details.order_id);
            console.log("- order_created:", details.order_created);
            console.log("- order_items:", details.order_items);
            console.log("- Global currentAdvanceOrder:", window.currentAdvanceOrder);
            console.log("- Confirmation modal hidden field:", $('#confirmationModal input[name="confirm_advance_order"]').val());
            console.log("- Reservation form input:", $('input[name="advance_order"]').val());
            
            // Initialize orderItemsHtml variable
            let orderItemsHtml = '';
            
            // If order items are directly in the response
            if (details.order_items && details.order_items.length > 0) {
                console.log("Using order items from response");
                orderItemsHtml = processOrderItems(details.order_items, 
                    details.order_payment_option || 'full',
                    details.order_payment_method || details.payment_method,
                    details.order_total_amount || details.total_amount,
                    details.order_amount_to_pay || details.amount_to_pay);
            } 
            // If we have advance order data in the global variable
            else if (window.currentAdvanceOrder && window.currentAdvanceOrder.items && window.currentAdvanceOrder.items.length > 0) {
                console.log("Using order items from global variable");
                orderItemsHtml = processOrderItems(window.currentAdvanceOrder.items,
                    window.currentAdvanceOrder.paymentOption,
                    window.currentAdvanceOrder.paymentMethod,
                    window.currentAdvanceOrder.totalAmount,
                    window.currentAdvanceOrder.amountToPay);
            }
            // If we have advance order data in the confirmation modal
            else if ($('#confirmationModal input[name="confirm_advance_order"]').length) {
                const confirmOrderInput = $('#confirmationModal input[name="confirm_advance_order"]').val();
                if (confirmOrderInput) {
                    try {
                        const orderDetails = JSON.parse(confirmOrderInput);
                        console.log("Order details from confirmation modal:", orderDetails);
                        
                        if (orderDetails && orderDetails.items && orderDetails.items.length > 0) {
                            console.log("Using order items from confirmation modal");
                            orderItemsHtml = processOrderItems(orderDetails.items,
                                orderDetails.paymentOption,
                                orderDetails.paymentMethod,
                                orderDetails.totalAmount,
                                orderDetails.amountToPay);
                        }
                    } catch (error) {
                        console.error("Error parsing confirmation modal order details:", error);
                    }
                }
            }
            // If we have advance order data in the form
            else {
                const advanceOrderInput = $('input[name="advance_order"]').val();
                if (advanceOrderInput) {
                    try {
                        const orderDetails = JSON.parse(advanceOrderInput);
                        console.log("Order details from input:", orderDetails);
                        
                        if (orderDetails && orderDetails.items && orderDetails.items.length > 0) {
                            console.log("Using order items from input");
                            orderItemsHtml = processOrderItems(orderDetails.items,
                                orderDetails.paymentOption,
                                orderDetails.paymentMethod,
                                orderDetails.totalAmount,
                                orderDetails.amountToPay);
                        } else {
                            console.log("No items in parsed order details");
                            orderItemsHtml = '<p class="text-muted">Order details missing items</p>';
                        }
                    } catch (error) {
                        console.error("Error parsing input order details:", error);
                        orderItemsHtml = '<p class="text-muted">Error processing order details</p>';
                    }
                } else if (details.order_id) {
                    // If we have an order ID but no details, show a generic message indicating an order was made
                    console.log("Order ID exists but no details available");
                    orderItemsHtml = '<p>Advance order was placed successfully (Order ID: ' + details.order_id + ')</p>';
                } else {
                    console.log("No order information found");
                    orderItemsHtml = '<p class="text-muted">No advance order placed</p>';
                }
            }
            
            // Set the HTML content
            $('#success-order-items').html(orderItemsHtml);
        }
        
        // Show success modal
        $('#confirmationModal').modal('hide');
        setTimeout(() => {
            $('#successModal').modal('show');
        }, 500);
    }

    // Helper function to process order items into HTML
    function processOrderItems(items, paymentOption, paymentMethod, totalAmount, amountToPay) {
        let html = '<div class="order-items-list">';
        
        items.forEach(item => {
            const itemTotal = parseFloat(item.price) * (parseInt(item.quantity) || 1);
            html += `
                <div class="order-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>${item.name} × ${item.quantity || 1}</span>
                        <span>₱${itemTotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        html += `
            <div class="mt-2"><strong>Payment Option:</strong> ${paymentOption === 'down' ? 'Down Payment (50%)' : 'Full Payment'}</div>
            <div><strong>Payment Method:</strong> ${(paymentMethod && paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)) || 'Cash'}</div>
            <div><strong>Total Amount:</strong> ₱${totalAmount}</div>
            <div><strong>Amount to Pay:</strong> ₱${amountToPay}</div>
        `;
        
        return html;
    }

    // Event delegation for category button clicks
    $('#menuCategories').on('click', '.category-btn', function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        loadMenuItems($(this).data('category-id'));
    });
    </script>
<script src="menu_fix.js"></script>
</body>
</html>

