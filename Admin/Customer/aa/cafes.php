<?php
require_once 'session.php';  // Replace session_start() with this
require_once 'db_con.php';  // Changed to require_once to ensure it's only included once

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Create DateTime objects with Manila timezone
$tz = new DateTimeZone('Asia/Manila');
$current_time = new DateTime('now', $tz);
$opening_time = new DateTime('today 06:30', $tz);
$closing_time = new DateTime('today 23:00', $tz);

// For debugging
error_log("Current time: " . $current_time->format('Y-m-d H:i:s'));
error_log("Opening time: " . $opening_time->format('Y-m-d H:i:s'));
error_log("Closing time: " . $closing_time->format('Y-m-d H:i:s'));

// Check if store is open
$is_store_open = ($current_time >= $opening_time && $current_time <= $closing_time);

// Store hours message with current Manila time
$store_hours_message = sprintf(
    "Operating Hours: 6:30 AM - 11:00 PM<br>Current Time: %s%s",
    $current_time->format('h:i A'),
    $is_store_open ? '' : '<br><span class="text-danger">We are currently closed.</span>'
);

// Calculate minimum pickup time (current time + 1 hour)
$min_pickup_time = new DateTime();
$min_pickup_time->modify('+1 hour');

// Format for HTML time input min attribute
$min_pickup_time_html = $min_pickup_time->format('H:i');

// Calculate maximum pickup time (store closing time)
$max_pickup_time_html = $closing_time->format('H:i');

// Check if database connection exists
if (!isset($con)) {
    die("Database connection not established. Please check db_con.php");
}

// Fetch menu categories
$category_query = "SELECT * FROM menu_categories ORDER BY id";
$category_result = mysqli_query($con, $category_query);
$categories = [];
while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row;
}

// Fetch menu items with their categories and availability
$checkColumnQuery = "SHOW COLUMNS FROM menu_items LIKE 'is_active'";
$isActiveColumnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;

if ($isActiveColumnExists) {
$items_query = "SELECT mi.*, mc.name as category_name, mc.display_name as category_display_name, 
                COALESCE(mi.availability, 1) as availability 
                FROM menu_items mi 
                JOIN menu_categories mc ON mi.category_id = mc.id 
                WHERE mi.is_active = 1
                ORDER BY mi.category_id, mi.name";
} else {
    $items_query = "SELECT mi.*, mc.name as category_name, mc.display_name as category_display_name,
                    COALESCE(mi.availability, 1) as availability 
                    FROM menu_items mi 
                    JOIN menu_categories mc ON mi.category_id = mc.id 
                    ORDER BY mi.category_id, mi.name";
}

$items_result = mysqli_query($con, $items_query);

// Add error checking
if (!$items_result) {
    die("Error fetching menu items: " . mysqli_error($con));
}

$menu_items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    // Fetch addons for this menu item
    $addon_query = "SELECT * FROM menu_items_addons WHERE menu_item_id = " . $row['id'];
    $addon_result = mysqli_query($con, $addon_query);
    $addons = [];
    
    if ($addon_result) {
        while ($addon = mysqli_fetch_assoc($addon_result)) {
            $addons[] = [
                'id' => $addon['id'],
                'name' => $addon['name'],
                'price' => floatval($addon['price'])
            ];
        }
    }
    
    $category_name = $row['category_name'];
    if (!isset($menu_items[$category_name])) {
        $menu_items[$category_name] = [];
    }
    
    $menu_items[$category_name][] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => floatval($row['price']),
        'image_path' => $row['image_path'],
        'availability' => intval($row['availability']),  // Add availability status
        'addons' => $addons  // Include the addons in the menu item data
    ];
}

// Add debugging output
if (empty($menu_items)) {
    error_log("No menu items found in the database");
}

// Fetch active payment methods from database
$payment_methods_query = "SELECT * FROM payment_methods WHERE is_active = 1";
$payment_methods_result = mysqli_query($con, $payment_methods_query);

if (!$payment_methods_result) {
    error_log("Payment methods query error: " . mysqli_error($con));
    $payment_methods = [];
} else {
    $payment_methods = [];
    while ($row = mysqli_fetch_assoc($payment_methods_result)) {
        $payment_methods[] = $row;
    }
}

// Payment methods dropdown is now handled in the HTML form with more detailed options

// Process order placement
if(isset($_POST['place_order'])) {
    try {
        // Get payment method details
        $payment_method_id = $_POST['payment_method'];
        $payment_method_name = '';
        
        foreach ($payment_methods as $method) {
            if ($method['id'] == $payment_method_id) {
                $payment_method_name = $method['name'];
                break;
            }
        }
        
        // Insert order with payment details
        $sql = "INSERT INTO orders (customer_id, item_name, quantity, total_amount, order_date, payment_status, 
                payment_method) 
                VALUES (?, ?, ?, ?, NOW(), 'Pending', ?)";
                
        $stmt = $con->prepare($sql);
        $stmt->bind_param("isidss", 
            $_SESSION['user_id'],
            $_POST['item_name'],
            $_POST['quantity'],
            $_POST['total_amount'],
            $payment_method_name
        );
        $stmt->execute();
        
        // Redirect or show success message
        echo "<script>alert('Order placed successfully!');</script>";
    } catch(Exception $e) {
        echo "<script>alert('Error processing order: " . $e->getMessage() . "');</script>";
    }
}

// At the top of cafes.php, add this code to get URL parameters
$tableDetails = [
    'package_id' => $_GET['package_id'] ?? '',
    'package_name' => $_GET['package_name'] ?? '',
    'date' => $_GET['date'] ?? '',
    'time' => $_GET['time'] ?? '',
    'duration' => $_GET['duration'] ?? '',
    'guests' => $_GET['guests'] ?? '' // Changed from guest_count to guests to match URL parameter
];

// Add debug logging
error_log("Table Details: " . print_r($tableDetails, true));

// Validate if we have table details
$hasTableDetails = !empty($tableDetails['package_id']);

// Initialize variables for extra hours calculation
$extraHours = 0;
$hourlyRate = 0;
$extraFee = 0;

// Calculate extra hours and fee if we have valid table details
if ($hasTableDetails) {
    $duration = intval($tableDetails['duration']);
    $startTime = strtotime($tableDetails['time']);
    
    // Calculate extra hours beyond 4-hour limit
    $extraHours = max(0, $duration - 4);
    
    if ($extraHours > 0) {
        // Check if time is beyond 2 PM (14:00)
        $isAfter2PM = date('H', $startTime) >= 14;
        $hourlyRate = $isAfter2PM ? 3000 : 2000;
        $extraFee = $extraHours * $hourlyRate;
    }
}

// Add this near the top of the file after getting $tableDetails
error_log("URL Parameters: " . print_r($_GET, true));
error_log("Table Details: " . print_r($tableDetails, true));

// Add this where the guest count should be displayed
echo "<!-- Debug - Guests: " . htmlspecialchars($tableDetails['guests']) . " -->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale</title>
    <!-- Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: "Poppins", serif;
            background-color: #f5f5f5;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            position: sticky;
            top: 80px;
            z-index: 1000;
            background-color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .sidebar h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        .list-group-item {
            margin-bottom: 10px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 3px 6px rgba(0,0,0,0.08);
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .list-group-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.12);
        }

        .list-group-item.active {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Menu Items Section */
        .menu-items {
            margin-top: 55px;
            padding: 30px;
        }

        .menu-items h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            font-size: 1.8rem;
        }

        /* Menu Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .card-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .price {
            font-weight: 600;
            color: #28a745;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        /* Current Order Section */
        .current-order {
            overflow-y: auto;
            background-color: #fff;
            z-index: 900;
            padding: 25px;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: calc(100vh - 100px);
        }

        .current-order h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
            margin-top: 40px;
            text-align: center;
            font-size: 1.5rem;
        }

        .order-list {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .order-list-item {
            background-color: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .order-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-item-name {
            font-weight: 600;
            color: #333;
        }

        .order-item-price {
            color: #28a745;
            font-weight: 500;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-button {
            background-color: #f8f9fa;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #333;
        }

        .qty-button:hover {
            background-color: #e9ecef;
        }

        .qty-button.increment {
            background-color: #28a745;
            color: white;
        }

        .qty-button.decrement {
            background-color: #dc3545;
            color: white;
        }

        .qty-display {
            font-weight: 500;
            color: #333;
            min-width: 30px;
            text-align: center;
        }

        .order-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-top: auto;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        }

        .order-summary p {
            margin-bottom: 10px;
            color: #333;
            font-weight: 500;
        }

        .order-summary strong {
            color: #28a745;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Add-ons styles */
        .add-ons {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        .add-on-list {
            font-size: 0.85rem;
            color: #666;
            margin-left: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                max-height: 60vh;
                padding: 10px 15px;
                margin-bottom: 15px;
            }

            .menu-items {
                margin-left: 0;
                margin-top: 33px;
                padding: 15px;
            }

            .current-order {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: 85%;
                max-width: 350px;
                height: 100vh;
                z-index: 1030;
                border-radius: 0;
                margin: 0;
                padding-top: 60px;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
            }

            .current-order.show {
                transform: translateX(0);
            }

            .card-img-top {
                height: 150px;
            }

            /* Adjust spacing for mobile dropdown */
            .sidebar .d-md-none {
                margin: 10px 0;
            }

            #menu-categories-mobile {
                border-radius: 8px;
                padding: 10px;
                margin: 5px 0;
            }

            /* Hide the cart overlay by default */
            .cart-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 1025;
            }

            .cart-overlay.show {
                display: block;
            }

            /* Add close button for mobile cart */
            .mobile-cart-close {
                position: absolute;
                top: 15px;
                right: 15px;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: #333;
                cursor: pointer;
                padding: 5px;
            }
        }

        .order-item-addons {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .addon-checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .addon-checkbox-container input[type="checkbox"] {
            margin-right: 8px;
        }

        .addon-checkbox-container label {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
            flex-grow: 1;
            cursor: pointer;
        }

        .addon-price {
            color: #28a745;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .order-items-summary {
            max-height: 300px;
            overflow-y: auto;
        }

        .summary-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .summary-item-name {
            font-weight: 500;
        }

        .summary-item-price {
            color: #28a745;
        }

        .summary-item-details {
            font-size: 0.9rem;
            color: #666;
        }

        .summary-totals {
            padding-top: 10px;
        }

        .payment-methods img {
            max-height: 24px;
            object-fit: contain;
        }

        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .modal-dialog-centered.modal-lg {
            max-width: 800px;
        }

        .alert-info {
            background-color: #e8f4f8;
            border-color: #d1e7f1;
        }

        #payment-method {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        #payment-method:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .payment-info {
            margin-top: 15px;
        }

        .alert-info p:last-child {
            margin-bottom: 0;
        }

        .pickup-notice {
            background-color: #e8f4f8;
            border-left: 4px solid #17a2b8;
        }

        #pickup-notes {
            resize: vertical;
            min-height: 60px;
        }

        .swal2-popup {
            font-family: "Poppins", serif;
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert i {
            margin-right: 8px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
        }

        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }

        /* Mobile dropdown styles */
        #menu-categories-mobile {
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        #menu-categories-mobile:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        /* Add smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Improve card layout */
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-body .btn {
            margin-top: auto;
        }

        /* Mobile cart button styles */
        .btn-primary.position-relative {
            position: relative;
        }

        .btn-primary.position-relative .badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            border-radius: 50%;
            padding: 0.25em 0.5em;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
            background-color: red;
        }

        /* Mobile cart button styles */
        #mobile-cart-btn {
            padding: 0.8rem;
            border-radius: 12px;
            font-size: 1.2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        #mobile-cart-badge {
            font-size: 0.7rem;
            transform: translate(-50%, -30%);
        }

        .addons {
            margin-top: 10px;
        }

        .addons-info {
            margin-top: 5px;
            color: #6c757d;
        }

        .addon-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 8px;
        }

        .addon-quantity, .addon-total {
            font-weight: 500;
            color: #28a745;
        }

        .item-total {
            font-weight: 600;
        }

        .addons-section {
            margin-top: 8px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .toggle-addons {
            margin-bottom: 10px;
        }

        .modal .addon-checkbox-container {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }

        .modal .addon-checkbox-container:hover {
            background-color: #e9ecef;
        }

        .modal .addon-checkbox-container label {
            margin-bottom: 0;
            margin-left: 10px;
            cursor: pointer;
        }

        .modal .total-section {
            margin-top: 15px;
        }

        #modal-total-price {
            font-weight: bold;
            font-size: 1.1em;
        }

        .table-details p {
            margin-bottom: 0.5rem;
        }

        .table-details p:last-child {
            margin-bottom: 0;
        }

        .table-details strong {
            color: #495057;
            min-width: 80px;
            display: inline-block;
        }

        /* Add these styles to your existing CSS */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card {
            border-radius: 10px;
        }

        .summary-totals {
            font-size: 0.95rem;
        }

        .form-select {
            border-radius: 8px;
            padding: 10px;
        }

        .alert {
            border-radius: 8px;
        }

        .order-items-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Custom scrollbar */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Add to your existing styles */
        .payment-info .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .payment-info .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .payment-info .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .payment-info .text-danger {
            font-weight: bold;
        }

        #payment_proof {
            padding: 0.375rem;
            font-size: 0.9rem;
        }

        .payment-info small.text-muted {
            font-size: 0.85rem;
        }

        /* Add to your existing styles */
        .policy-agreement .card {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .policy-agreement ul {
            padding-left: 1.2rem;
            margin-bottom: 1rem;
            color: #666;
        }

        .policy-agreement ul li {
            margin-bottom: 0.3rem;
        }

        .policy-agreement .form-check {
            margin-bottom: 0;
        }

        .policy-agreement .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .policy-agreement .form-check-label {
            color: #495057;
        }
    </style>
</head>
<body>
 <!-- Navigation Bar -->
 <?php include('nav.php'); ?>
 <?php include 'message_box.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Mobile Categories (shown only on mobile) -->
            <div class="d-md-none w-100 px-3 py-2 bg-light sticky-top" style="top: 60px; z-index: 1020;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1 me-3">
                        <select class="form-select form-select-lg" id="menu-categories-mobile">
                            <?php foreach ($categories as $index => $category): ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>" 
                                        <?php echo $index === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary position-relative" id="mobile-cart-btn" onclick="toggleMobileCart()">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="mobile-cart-badge">
                            
                        </span>
                    </button>
                </div>
            </div>

            <!-- Desktop Sidebar (hidden on mobile) -->
            <div class="col-md-3 d-none d-md-block sidebar bg-light">
                <h3 class="text-center py-3">Menu Categories</h3>
                <ul class="list-group" id="menu-categories-desktop">
                    <?php foreach ($categories as $index => $category): ?>
                        <li class="list-group-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-category="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Menu Items Section -->
            <div class="col-12 col-md-6 menu-items">
                <!-- Store Hours Alert -->
                <div class="alert <?php echo $is_store_open ? 'alert-success' : 'alert-warning'; ?> text-center mb-4 mt-2">
                    <h6 class="mb-1"><i class="fas fa-clock me-2"></i>Store Hours</h6>
                    <p class="mb-0"><?php echo $store_hours_message; ?></p>
                    <?php if (!$is_store_open): ?>
                        <p class="mb-0 mt-2 text-danger">
                            <strong>We are currently closed.</strong>
                        </p>
                        <?php 
                        // Calculate next opening time
                        $now = new DateTime();
                        $next_opening = clone $opening_time;
                        
                        if ($now > $closing_time) {
                            // If after closing, set to next day's opening
                            $next_opening->modify('+1 day');
                        }
                        
                        $time_until = $now->diff($next_opening);
                        ?>
                        <p class="mb-0 mt-1 text-muted">
                            <small>
                                Orders will be available again at 6:30 AM<?php echo $now > $closing_time ? ' tomorrow' : ''; ?>.
                                <br>
                                Please come back during our operating hours to place your order.
                            </small>
                        </p>
                    <?php endif; ?>
                </div>

                <h3 class="text-center py-3 category-title">SMALL PLATES</h3>
                <div class="row g-3" id="menu-items">
                    <!-- Menu items will be dynamically inserted here -->
                </div>
            </div>

            <!-- Current Order Section -->
            <div class="col-12 col-md-3">
                <div class="current-order sticky-top" style="top: 80px;">
                    <!-- Mobile Close Button (only visible on mobile) -->
                    <button class="mobile-cart-close d-md-none" onclick="toggleMobileCart()">
                        <i class="fas fa-times"></i>
                    </button>
                    <h3>Current Order</h3>
                    <div class="order-list" id="order-list"></div>
                    <div class="order-summary">
                        <p><strong>Total Items:</strong> <span id="total-items">0</span></p>
                        <p><strong>Total Amount:</strong> ₱ <span id="total-amount">0.00</span></p>
                        <button class="btn btn-success w-100" onclick="submitOrder()">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary Modal -->
    <div class="modal fade" id="orderSummaryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Order Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Table Reservation Details -->
                    <?php if ($hasTableDetails): ?>
                    <div class="reservation-details mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                                        <h6 class="mb-3">Reservation Details</h6>
                                        <p class="mb-2"><strong>Package:</strong> <?php echo htmlspecialchars($tableDetails['package_name']); ?></p>
                                        <p class="mb-2"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($tableDetails['date'])); ?></p>
                                        <p class="mb-2"><strong>Time:</strong> <?php echo date('g:i A', strtotime($tableDetails['time'])); ?></p>
                                        <p class="mb-2"><strong>Duration:</strong> <?php echo htmlspecialchars($tableDetails['duration']); ?> hours</p>
                                        <p class="mb-2"><strong>Number of Guests:</strong> <?php echo htmlspecialchars($tableDetails['guests']); ?></p>
                        </div>

                                    <?php if ($extraHours > 0): ?>
                        <div class="col-md-6">
                                        <div class="alert alert-warning mt-3">
                                            <h6 class="mb-2">Extended Hours Fee</h6>
                                            <p class="mb-1"><strong>Extra Hours:</strong> <?php echo $extraHours; ?> hour(s)</p>
                                            <p class="mb-1"><strong>Rate:</strong> ₱<?php echo number_format($hourlyRate, 2); ?>/hour</p>
                                            <p class="mb-0"><strong>Fee:</strong> ₱<?php echo number_format($extraFee, 2); ?></p>
                            </div>
                            </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Order Details -->
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="mb-3">Order Details</h6>
                                    <div class="order-items-list mb-3">
                                        <!-- Items will be populated here -->
                                    </div>
                                    <?php if (!$hasTableDetails): ?>
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-clock me-2"></i>
                                            Please note: Orders require at least 1 hour preparation time.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            </div>

                        <!-- Payment and Totals -->
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="mb-3">Payment Details</h6>
                                    
                                    <!-- Payment Option Selection -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">Payment Option</label>
                                        <select class="form-select" id="payment-option" name="payment_option" onchange="updatePaymentSummary()">
                                            <option value="">Select Payment Option</option>
                                            <option value="full">Full Payment</option>
                                            <option value="partial">50% Downpayment</option>
                                        </select>
                                    </div>

                                    <!-- Payment Method Selection -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment-method" name="payment_method" onchange="updatePaymentDetails(this.value)">
                                            <option value="">Select Payment Method</option>
                                            <?php foreach ($payment_methods as $method): ?>
                                                <option value="<?php echo htmlspecialchars($method['id']); ?>"
                                                        data-name="<?php echo htmlspecialchars($method['name']); ?>">
                                            <?php echo htmlspecialchars($method['display_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                        
                                      

                                    <!-- Order Summary -->
                                    <div class="summary-totals">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Items:</span>
                                            <span class="modal-total-items">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>₱<span class="modal-total-amount">0.00</span></span>
                                        </div>
                                        <?php if ($hasTableDetails && $extraHours > 0): ?>
                                        <div class="d-flex justify-content-between mb-2 text-warning">
                                            <span>Extra Hours Fee:</span>
                                            <span>₱<?php echo number_format($extraFee, 2); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="border-top pt-2 mt-2">
                                            <div class="d-flex justify-content-between">
                                                <strong>Amount to pay :</strong>
                                                <strong class="text-success">₱<span class="modal-final-total">0.00</span></strong>
                                            </div>
                                            <div id="partial-payment-details" style="display: none;" class="mt-2">
                                                <div class="d-flex justify-content-between text-danger">
                                                    <span>Downpayment (50%):</span>
                                                    <span>₱<span class="modal-downpayment">0.00</span></span>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted">
                                                    <small>Remaining Balance:</small>
                                                    <small>₱<span class="modal-remaining">0.00</span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            </div>

                            <!-- Order Note -->
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php if ($hasTableDetails): ?>
                                    This is for table advance orders. Your order will be served at your reserved table.
                                <?php else: ?>
                                    This is for pickup orders only. Please collect your order at our counter.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmOrder()">
                        Confirm Order
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>

    <!-- Add overlay div -->
    <div class="cart-overlay" id="cart-overlay" onclick="toggleMobileCart()"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Replace the static menu data with the PHP data
    const menuData = <?php echo json_encode($menu_items, JSON_PRETTY_PRINT); ?>;

    // Handle Add to Cart
    let order = [];

    document.body.addEventListener("click", function(e) {
        if (e.target.classList.contains("add-to-cart") || e.target.closest('.add-to-cart')) {
            e.preventDefault();
            const button = e.target.classList.contains("add-to-cart") ? e.target : e.target.closest('.add-to-cart');
            const itemName = button.getAttribute("data-item");
            const itemPrice = parseFloat(button.getAttribute("data-price"));
            const categoryName = button.getAttribute("data-category");
            const hasAddons = button.getAttribute("data-has-addons") === "true";
            const isAvailable = button.getAttribute("data-available") === "true";

            // Prevent adding out of stock items
            if (!isAvailable) {
                Swal.fire({
                    icon: 'error',
                    title: 'Out of Stock',
                    text: 'This item is currently out of stock',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // Find the item in menuData
            const item = menuData[categoryName].find(item => item.name === itemName);
            
            if (hasAddons && item.addons && item.addons.length > 0) {
                // Show add-ons modal
                showAddOnsModal(itemName, itemPrice, categoryName, item.addons);
            } else {
                // Directly add to cart if no add-ons
                addToOrder(itemName, itemPrice, categoryName, []);
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Item has been added to your cart',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
    });

    // Update the addToOrder function to check login first
    function addToOrder(name, price, category, addons = []) {
        // Check login status first
        fetch('check_login.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                // User is logged in, proceed with adding to cart
                const existingItem = order.find(item => {
                    if (item.name === name && item.category === category) {
                        const existingAddons = item.addons.map(a => a.name).sort().join(',');
                        const newAddons = addons.map(a => a.name).sort().join(',');
                        return existingAddons === newAddons;
                    }
                    return false;
                });

                if (existingItem) {
                    existingItem.qty++;
                } else {
                    const validatedAddons = addons.map(addon => ({
                        name: addon.name,
                        price: parseFloat(addon.price)
                    }));
                    
                    order.push({
                        name,
                        price: parseFloat(price),
                        category,
                        qty: 1,
                        addons: validatedAddons
                    });
                }
                updateOrder();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: 'Item has been added to your cart',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                // User is not logged in, show login prompt
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please login to add items to cart',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Login Now',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Store current page URL in session storage
                        sessionStorage.setItem('redirectAfterLogin', window.location.href);
                        // Redirect to login page with return URL
                        window.location.href = data.redirect;
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking login status:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        });
    }

    function updateOrder() {
        const orderList = document.getElementById("order-list");
        const totalItems = document.getElementById("total-items");
        const totalAmount = document.getElementById("total-amount");

        orderList.innerHTML = order
            .map((item, index) => {
                // Calculate item total including addons
                const itemBaseTotal = item.price * item.qty;
                const itemAddonsTotal = item.addons.reduce((sum, addon) => sum + addon.price, 0) * item.qty;
                const itemTotal = itemBaseTotal + itemAddonsTotal;

                return `
                    <div class="order-list-item">
                        <div class="order-item-header">
                            <span class="order-item-name">${item.name}</span>
                            <span class="order-item-price">₱${itemTotal.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Base Price: ₱${item.price.toFixed(2)}</small>
                            <div class="qty-controls">
                                <button class="qty-button decrement" onclick="decrementQty(${index})">-</button>
                                <span class="qty-display">${item.qty}</span>
                                <button class="qty-button increment" onclick="incrementQty(${index})">+</button>
                            </div>
                        </div>
                        ${item.addons && item.addons.length > 0 ? `
                            <div class="addons-section">
                                <small class="text-muted d-block mb-2">Add-ons:</small>
                                ${item.addons.map(addon => `
                                    <div class="addon-item">
                                        <small>• ${addon.name}</small>
                                        <small class="addon-price">+₱${addon.price.toFixed(2)}</small>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            })
            .join("");

        // Calculate totals including addons
        const totalQty = order.reduce((total, item) => total + item.qty, 0);
        const totalPrice = order.reduce((total, item) => {
            const itemTotal = item.price * item.qty;
            const addonsTotal = item.addons.reduce((sum, addon) => sum + addon.price, 0) * item.qty;
            return total + itemTotal + addonsTotal;
        }, 0);

        totalItems.textContent = totalQty;
        totalAmount.textContent = totalPrice.toFixed(2);

        // Update mobile cart badge
        const mobileBadge = document.getElementById('mobile-cart-badge');
        mobileBadge.textContent = totalQty;
        mobileBadge.style.display = totalQty > 0 ? 'block' : 'none';
    }

    function incrementQty(index) {
        order[index].qty++;
        updateOrder();
    }

    function decrementQty(index) {
        if (order[index].qty > 1) {
            order[index].qty--;
        } else {
            order.splice(index, 1);
        }
        updateOrder();
    }

    function updateItemAddons(itemIndex, addonName, addonPrice, isChecked) {
        const item = order[itemIndex];
        
        if (isChecked) {
            if (!item.addons.some(addon => addon.name === addonName)) {
                item.addons.push({ name: addonName, price: addonPrice });
            }
        } else {
            item.addons = item.addons.filter(addon => addon.name !== addonName);
        }
        
        updateOrder();
    }

    function updateMenuItems(category) {
        const menuItemsContainer = document.getElementById("menu-items");
        menuItemsContainer.innerHTML = "";

        if (!menuData || !menuData[category]) {
            console.error('No menu items found for category:', category);
            menuItemsContainer.innerHTML = '<div class="col-12 text-center"><p>No items available in this category</p></div>';
            return;
        }

        menuData[category].forEach((item) => {
            const isAvailable = item.availability !== 0; // Check if item is available
            const card = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 position-relative">
                        <img src="/Admin/${item.image_path}" 
                             class="card-img-top ${!isAvailable ? 'opacity-50' : ''}" 
                             alt="${item.name}" 
                             onerror="this.src='../../uploads/menus/default-menu-item.jpg'" 
                             style="height: 200px; object-fit: cover;">
                        ${!isAvailable ? `
                            <div class="position-absolute top-50 start-50 translate-middle w-100 text-center">
                                <span class="bg-danger text-white px-3 py-2 rounded fw-bold">OUT OF STOCK</span>
                            </div>
                        ` : ''}
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title">${item.name}</h5>
                            <p class="card-text">₱${item.price.toFixed(2)}</p>
                            ${isAvailable ? 
                                `<button class="btn btn-warning add-to-cart w-100 mt-auto" 
                                        data-item="${item.name}" 
                                        data-price="${item.price}" 
                                        data-category="${category}"
                                        data-has-addons="${item.addons && item.addons.length > 0}"
                                        data-available="${isAvailable}">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>` : ''
                            }
                        </div>
                    </div>
                </div>
            `;
            menuItemsContainer.innerHTML += card;
        });
    }

    // Update the DOMContentLoaded event listener
    document.addEventListener("DOMContentLoaded", function() {
        // Get the first category from menuData
        const firstCategory = Object.keys(menuData)[0];
        
        // Set initial active category in desktop view
        const firstCategoryItem = document.querySelector('.list-group-item');
        if (firstCategoryItem) {
            firstCategoryItem.classList.add('active');
        }
        
        // Set initial value for mobile dropdown
        const mobileSelect = document.getElementById('menu-categories-mobile');
        if (mobileSelect) {
            mobileSelect.value = firstCategory;
        }
        
        // Load initial menu items
        updateMenuItems(firstCategory);
        
        // Desktop category selection
        document.getElementById("menu-categories-desktop")?.addEventListener("click", function(e) {
            if (e.target && e.target.matches("li")) {
                document.querySelectorAll('.list-group-item').forEach((item) => {
                    item.classList.remove("active");
                });
                e.target.classList.add("active");
                updateMenuItems(e.target.getAttribute("data-category"));
            }
        });

        // Mobile category selection
        document.getElementById("menu-categories-mobile")?.addEventListener("change", function(e) {
            updateMenuItems(this.value);
        });
    });

    // Replace the submitOrder function
    function submitOrder() {
        // Get order type from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const isAdvanceOrder = urlParams.has('package_id'); // Check if this is an advance order
        
        // Only check store hours for regular orders
        if (!isAdvanceOrder) {
            const isStoreOpen = <?php echo json_encode($is_store_open); ?>;
            
            if (!isStoreOpen) {
                const now = new Date();
                const openingTime = '6:30 AM';
                const closingTime = '11:00 PM';
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Store Closed',
                    html: `
                        <p>We are currently closed.</p>
                        <p><strong>Operating Hours: ${openingTime} - ${closingTime}</strong></p>
                        <p>Orders can only be placed during operating hours.<br>
                        Please come back ${now.getHours() >= 23 ? 'tomorrow at ' + openingTime : 'between ' + openingTime + ' and ' + closingTime}.</p>
                    `,
                });
                return;
            }
        }

        if (order.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Cart',
                text: 'Please add items to your cart before placing an order.',
            });
            return;
        }

        // Populate the modal with order details
        const orderItemsContainer = document.querySelector('.order-items-list');
        const modalTotalItems = document.querySelector('.modal-total-items');
        const modalTotalAmount = document.querySelector('.modal-total-amount');

        // Clear previous content
        orderItemsContainer.innerHTML = '';

        // Add each item to the modal
        order.forEach(item => {
            const itemBaseTotal = item.price * item.qty;
            const addonTotal = item.addons.reduce((sum, addon) => sum + parseFloat(addon.price), 0) * item.qty;
            const totalPrice = itemBaseTotal + addonTotal;

            const itemHtml = `
                <div class="summary-item">
                    <div class="summary-item-header">
                        <span class="summary-item-name">${item.name} (×${item.qty})</span>
                        <span class="summary-item-price">₱${totalPrice.toFixed(2)}</span>
                    </div>
                    <div class="summary-item-details">
                        <div>Base Price: ₱${item.price.toFixed(2)}</div>
                        ${item.addons.length > 0 ? `
                            <div class="mt-1">Add-ons:
                                ${item.addons.map(addon => 
                                    `<div class="ms-3">- ${addon.name}: ₱${parseFloat(addon.price).toFixed(2)}</div>`
                                ).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            orderItemsContainer.innerHTML += itemHtml;
        });

        // Update totals
        const totals = updateOrderSummary();

        // Show the modal
        const orderSummaryModal = new bootstrap.Modal(document.getElementById('orderSummaryModal'));
        orderSummaryModal.show();
    }

    // Update the total calculation function
    function updateOrderSummary() {
        // Calculate order subtotal including addons
        let subtotal = order.reduce((total, item) => {
            const itemPrice = item.price * item.qty;
            const addonPrice = item.addons.reduce((sum, addon) => sum + parseFloat(addon.price), 0) * item.qty;
            return total + itemPrice + addonPrice;
        }, 0);

        // Get extra hours fee if it exists
        const extraFee = <?php echo $extraFee ?? 0; ?>; // Get extra fee from PHP

        // Calculate final total
        const finalTotal = subtotal + extraFee;

        // Update displays
        document.querySelector('.modal-total-items').textContent = order.reduce((total, item) => total + item.qty, 0);
        document.querySelector('.modal-total-amount').textContent = subtotal.toFixed(2);
        document.querySelector('.modal-final-total').textContent = finalTotal.toFixed(2);

        // Update payment details if payment option is selected
        const paymentOption = document.getElementById('payment-option').value;
        if (paymentOption) {
            updatePaymentSummary();
        }

        // Update guest count display
        const guestCountElement = document.querySelector('.reservation-details .guest-count');
        if (guestCountElement) {
            const guests = <?php echo json_encode($tableDetails['guests']); ?>;
            guestCountElement.textContent = guests;
        }

        return {
            subtotal: subtotal,
            extraFee: extraFee,
            finalTotal: finalTotal
        };
    }

    // Add the updatePaymentSummary function
    function updatePaymentSummary() {
        const paymentOption = document.getElementById('payment-option').value;
        const partialPaymentDetails = document.getElementById('partial-payment-details');
        const finalTotal = parseFloat(document.querySelector('.modal-final-total').textContent);

        if (paymentOption === 'partial') {
            const downpayment = finalTotal * 0.5;
            const remainingBalance = finalTotal - downpayment;

            document.querySelector('.modal-downpayment').textContent = downpayment.toFixed(2);
            document.querySelector('.modal-remaining').textContent = remainingBalance.toFixed(2);
            partialPaymentDetails.style.display = 'block';
        } else {
            partialPaymentDetails.style.display = 'none';
        }
    }

    // Update the confirmOrder function
    function confirmOrder() {
        const paymentOption = document.getElementById('payment-option');
        const paymentMethod = document.getElementById('payment-method');

        // Validate payment option
        if (!paymentOption.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Option Required',
                text: 'Please select a payment option to continue.'
            });
            return false;
        }

        // Validate payment method
        if (!paymentMethod.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Payment Method Required',
                text: 'Please select a payment method to continue.'
            });
            return false;
        }

        // Get selected payment method details
        const selectedOption = paymentMethod.options[paymentMethod.selectedIndex];
        const paymentMethodName = selectedOption.getAttribute('data-name');
        const totals = updateOrderSummary();

        // Create FormData object
        const formData = new FormData();

        // Prepare order data
        const orderData = {
            items: order.map(item => ({
                name: item.name,
                price: parseFloat(item.price),
                quantity: parseInt(item.qty),
                addons: item.addons || []
            })),
            payment_option: paymentOption.value,
            payment_method: paymentMethodName,
            total_amount: parseFloat(totals.subtotal),
            extra_fee: parseFloat(totals.extraFee || 0),
            final_total: parseFloat(totals.finalTotal),
            order_type: <?php echo $hasTableDetails ? "'advance'" : "'regular'"; ?>,
            special_requests: document.getElementById('special_requests')?.value || '',
            table_details: <?php echo $hasTableDetails ? json_encode($tableDetails) : 'null'; ?>
        };

        // Debug log
        console.log('Order Data:', orderData);

        // Add order data to FormData
        formData.append('order_data', JSON.stringify(orderData));

        // Online payments without additional requirements

        // Show loading state
        Swal.fire({
            title: 'Processing Order',
            text: 'Redirecting to payment...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Determine the correct processing endpoint based on order type
        const processingEndpoint = 'payment_information.php';
        console.log('Redirecting to payment page');

        // Convert order data to URL parameters
        const params = new URLSearchParams();
        params.append('order_data', JSON.stringify(orderData));
        
        // Add individual items for better URL readability
        params.append('total_items', orderData.items.reduce((total, item) => total + item.quantity, 0));
        params.append('subtotal', orderData.total_amount);
        params.append('total', orderData.final_total);
        params.append('payment_method', orderData.payment_method);
        params.append('payment_option', orderData.payment_option);
        // Add payment option display name (e.g., 'Full Payment' or 'Partial Payment')
        const paymentOptionElement = document.getElementById('payment-option');
        const paymentOptionText = paymentOptionElement ? 
            paymentOptionElement.options[paymentOptionElement.selectedIndex].text : 'Full Payment';
        params.append('payment_option_display', paymentOptionText);
        
        // Add first item details for display
        if (orderData.items.length > 0) {
            const firstItem = orderData.items[0];
            params.append('first_item_name', firstItem.name);
            params.append('first_item_qty', firstItem.quantity);
            params.append('first_item_price', firstItem.price);
        }
        
        // Redirect to payment page with URL parameters
        window.location.href = `${processingEndpoint}?${params.toString()}`;

        return false;
    }

    // Add this function to create a notification
    function createNotification(orderId) {
        // Ensure orderId exists
        if (!orderId) {
            console.error('Order ID is missing');
            return;
        }

        const notificationData = {
            user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>,
            title: 'New Order Placed',
            message: `Order #${orderId} has been successfully placed and is being processed.`,
            type: 'order',
            reference_id: orderId.toString(), // Convert to string to match database field
            icon: 'fa-shopping-cart'
        };

        console.log('Sending notification data:', notificationData); // Debug log

        fetch('create_notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(notificationData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Notification response:', data); // Debug log
            if (data.status === 'success') {
                console.log('Notification created successfully');
            } else {
                console.error('Failed to create notification:', data.message);
            }
        })
        .catch(error => {
            console.error('Error creating notification:', error);
        });
    }

    // Replace the existing payment method change handler with this dynamic version
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment-method');
        const paymentDetails = document.getElementById('payment-details');
        const paymentMethods = <?php echo json_encode($payment_methods); ?>;

        paymentSelect.addEventListener('change', function() {
            paymentDetails.style.display = 'block';
            
            // Hide all payment details first
            paymentMethods.forEach(method => {
                document.getElementById(`${method.name}-details`).style.display = 'none';
            });

            // Show selected payment method details
            if (this.value) {
                document.getElementById(`${this.value}-details`).style.display = 'block';
            } else {
                paymentDetails.style.display = 'none';
            }
        });
    });

    // Add these functions to your existing JavaScript
    function toggleMobileCart() {
        // Only handle mobile cart toggle if we're in mobile view
        if (window.innerWidth <= 768) {
            const currentOrder = document.querySelector('.current-order');
            const overlay = document.getElementById('cart-overlay');
            currentOrder.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = currentOrder.classList.contains('show') ? 'hidden' : '';
        }
    }

    // Update the click outside handler
    document.addEventListener('click', function(e) {
        const currentOrder = document.querySelector('.current-order');
        const cartBtn = document.getElementById('mobile-cart-btn');
        const mobileCloseBtn = document.querySelector('.mobile-cart-close');
        
        if (window.innerWidth <= 768 && 
            !currentOrder.contains(e.target) && 
            !cartBtn.contains(e.target) &&
            !mobileCloseBtn.contains(e.target) &&
            currentOrder.classList.contains('show')) {
            toggleMobileCart();
        }
    });

    // Add resize handler to manage cart visibility on screen size changes
    window.addEventListener('resize', function() {
        const currentOrder = document.querySelector('.current-order');
        const overlay = document.getElementById('cart-overlay');
        
        if (window.innerWidth > 768) {
            // Remove mobile-specific classes when switching to desktop
            currentOrder.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    });

    function updateStoreStatus() {
        // Get current time in Manila
        const now = new Date();
        const manilaTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        
        // Get hours and minutes
        const hours = manilaTime.getHours();
        const minutes = manilaTime.getMinutes();
        
        // Format time for display
        const timeString = manilaTime.toLocaleString('en-US', {
            timeZone: 'Asia/Manila',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Check if this is an advance order
        const urlParams = new URLSearchParams(window.location.search);
        const isAdvanceOrder = urlParams.has('package_id');
        
        // Check if store is open (between 6:30 AM and 11:00 PM)
        const isOpen = isAdvanceOrder || (
            (hours === 6 && minutes >= 30) || // After 6:30 AM
            (hours > 6 && hours < 23) ||      // Between 7 AM and 10:59 PM
            (hours === 23 && minutes === 0)   // At 11:00 PM exactly
        );

        // Update store status alert
        const alertDiv = document.querySelector('.alert');
        if (alertDiv) {
            alertDiv.className = `alert ${isOpen ? 'alert-success' : 'alert-warning'} text-center mb-4 mt-2`;
            
            if (isAdvanceOrder) {
                // For advance orders, always show as open
                alertDiv.innerHTML = `
                    <h6 class="mb-1"><i class="fas fa-clock me-2"></i>Store Hours</h6>
                    <p class="mb-0">Operating Hours: 6:30 AM - 11:00 PM<br>Current Time: ${timeString}</p>
                    <p class="mb-0 mt-2 text-success">
                        <strong>Advance orders are accepted 24/7</strong>
                    </p>
                `;
            } else {
                // For regular orders, show normal store hours message
                let nextOpeningTime;
                if (hours >= 23 || hours < 6 || (hours === 6 && minutes < 30)) {
                    nextOpeningTime = "6:30 AM" + (hours >= 23 ? " tomorrow" : " today");
                }

                alertDiv.innerHTML = `
                    <h6 class="mb-1"><i class="fas fa-clock me-2"></i>Store Hours</h6>
                    <p class="mb-0">Operating Hours: 6:30 AM - 11:00 PM<br>Current Time: ${timeString}</p>
                    ${!isOpen ? `
                        <p class="mb-0 mt-2 text-danger">
                            <strong>We are currently closed for regular orders.</strong>
                        </p>
                        <p class="mb-0 mt-1 text-muted">
                            <small>
                                Regular orders will be available again at ${nextOpeningTime}.
                                <br>
                                Please come back during our operating hours to place your order.
                            </small>
                        </p>
                    ` : ''}
                `;
            }
        }

        // Update store open status for order validation
        window.isStoreOpen = isOpen;
    }

    // Update time every second
    updateStoreStatus();
    setInterval(updateStoreStatus, 1000);

    // Add this function to handle add-ons selection and display
    function showAddOnsModal(itemName, itemPrice, category, addons) {
        const modalHtml = `
            <div class="modal fade" id="addOnsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${itemName} - Add-ons</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <p class="mb-2">Base Price: ₱${itemPrice.toFixed(2)}</p>
                                <hr>
                                <p class="mb-2"><strong>Select Add-ons:</strong></p>
                                ${addons.map(addon => `
                                    <div class="addon-checkbox-container mb-2">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input addon-checkbox"
                                                       id="modal-addon-${addon.id}"
                                                       data-name="${addon.name}"
                                                       data-price="${addon.price}">
                                                <label class="form-check-label" for="modal-addon-${addon.id}">
                                                    ${addon.name}
                                                </label>
                                            </div>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-decrement" type="button" data-addon-id="${addon.id}">-</button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       id="quantity-${addon.id}" value="1" min="1" max="10" 
                                                       data-addon-id="${addon.id}" disabled>
                                                <button class="btn btn-outline-secondary btn-increment" type="button" data-addon-id="${addon.id}">+</button>
                                            </div>
                                            <span class="text-success addon-price" id="price-${addon.id}">₱${addon.price.toFixed(2)}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="total-section border-top pt-3">
                                <h6 class="d-flex justify-content-between">
                                    <span>Total:</span>
                                    <span class="text-success" id="modal-total-price">₱${itemPrice.toFixed(2)}</span>
                                </h6>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="confirmAddToCart('${itemName}', ${itemPrice}, '${category}')">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('addOnsModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to document
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Initialize modal
        const modal = new bootstrap.Modal(document.getElementById('addOnsModal'));
        modal.show();

        // Add event listeners for add-ons checkboxes
        document.querySelectorAll('#addOnsModal .addon-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateModalTotal);
        });

        // Initial total calculation
        updateModalTotal();
    }

    // Add function to update total in modal
    function updateModalTotal() {
        const itemPriceElement = document.querySelector('#addOnsModal .mb-2');
        const basePrice = parseFloat(itemPriceElement.textContent.replace('Base Price: ₱', ''));
        const selectedAddons = document.querySelectorAll('#addOnsModal .addon-checkbox:checked');
        
        let addonsTotal = 0;
        selectedAddons.forEach(checkbox => {
            const addonId = checkbox.id.replace('modal-addon-', '');
            const quantity = parseInt(document.getElementById(`quantity-${addonId}`).value) || 0;
            addonsTotal += parseFloat(checkbox.dataset.price) * quantity;
        });
        
        const total = basePrice + addonsTotal;
        document.querySelector('#modal-total-price').textContent = `₱${total.toFixed(2)}`;
    }
    
    // Handle increment/decrement buttons
    document.addEventListener('click', function(e) {
        // Handle increment button
        if (e.target.classList.contains('btn-increment')) {
            const addonId = e.target.getAttribute('data-addon-id');
            const input = document.getElementById(`quantity-${addonId}`);
            const currentValue = parseInt(input.value) || 0;
            if (currentValue < 10) { // Limit to 10
                input.value = currentValue + 1;
                updateAddonPrice(addonId);
                updateModalTotal();
            }
        }
        
        // Handle decrement button
        if (e.target.classList.contains('btn-decrement')) {
            const addonId = e.target.getAttribute('data-addon-id');
            const input = document.getElementById(`quantity-${addonId}`);
            const currentValue = parseInt(input.value) || 0;
            if (currentValue > 1) { // Don't go below 1
                input.value = currentValue - 1;
                updateAddonPrice(addonId);
                updateModalTotal();
            }
        }
    });
    
    // Handle checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('addon-checkbox')) {
            const addonId = e.target.id.replace('modal-addon-', '');
            const quantityInput = document.getElementById(`quantity-${addonId}`);
            quantityInput.disabled = !e.target.checked;
            if (!e.target.checked) {
                quantityInput.value = 1; // Reset to default quantity when unchecked
            }
            updateAddonPrice(addonId);
            updateModalTotal();
        }
    });
    
    // Update individual addon price display
    function updateAddonPrice(addonId) {
        const checkbox = document.getElementById(`modal-addon-${addonId}`);
        const quantity = parseInt(document.getElementById(`quantity-${addonId}`).value) || 0;
        const price = parseFloat(checkbox.dataset.price);
        const totalPrice = price * quantity;
        document.getElementById(`price-${addonId}`).textContent = `₱${totalPrice.toFixed(2)}`;
    }

    // Add function to handle add to cart confirmation
    function confirmAddToCart(itemName, basePrice, category) {
        // Get all checked add-ons with quantities
        const selectedAddons = [];
        document.querySelectorAll('#addOnsModal .addon-checkbox:checked').forEach(checkbox => {
            const addonId = checkbox.id.replace('modal-addon-', '');
            const quantity = parseInt(document.getElementById(`quantity-${addonId}`).value) || 1;
            for (let i = 0; i < quantity; i++) {
                selectedAddons.push({
                    name: checkbox.dataset.name,
                    price: parseFloat(checkbox.dataset.price)
                });
            }
        });

        // Add to order with selected add-ons
        addToOrder(itemName, basePrice, category, selectedAddons);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addOnsModal'));
        modal.hide();

        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Added to Cart!',
            text: 'Item has been added to your cart',
            timer: 1500,
            showConfirmButton: false
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get table details from session storage
        const tableDetails = JSON.parse(sessionStorage.getItem('tableDetails'));
        
        if (tableDetails) {
            const tableDetailsHtml = `
                <div class="table-details">
                    <p><strong>Package:</strong> ${tableDetails.packageName}</p>
                    <p><strong>Date:</strong> ${formatDate(tableDetails.date)}</p>
                    <p><strong>Time:</strong> ${formatTime(tableDetails.time)}</p>
                    <p><strong>Duration:</strong> ${tableDetails.duration} hours</p>
                    <p><strong>Guests:</strong> ${tableDetails.guestCount}</p>
                </div>
            `;
            document.getElementById('tableDetails').innerHTML = tableDetailsHtml;
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function formatTime(timeString) {
            const options = { hour: 'numeric', minute: 'numeric', hour12: true };
            return new Date(`2000/01/01 ${timeString}`).toLocaleTimeString(undefined, options);
        }

        // Add this to your existing cart display function
        function updateCartDisplay() {
            // ... existing cart display code ...

            // Add a hidden input with table details when submitting the order
            if (cart.length > 0) {
                const tableDetailsInput = document.createElement('input');
                tableDetailsInput.type = 'hidden';
                tableDetailsInput.name = 'table_details';
                tableDetailsInput.value = JSON.stringify(tableDetails);
                document.getElementById('orderForm').appendChild(tableDetailsInput);
            }
        }
    });

    function updatePaymentDetails(selectedValue) {
        const paymentDetails = document.getElementById('payment-details');
        const selectedOption = document.querySelector(`#payment-method option[value="${selectedValue}"]`);
        
        if (selectedOption && selectedOption.value !== '') {
            const name = selectedOption.getAttribute('data-name');
            const paymentInfo = document.querySelector('.payment-info');
            
            // Get order type from PHP
            const isAdvanceOrder = <?php echo $hasTableDetails ? 'true' : 'false' ?>;
            
            let detailsHtml = `
                <h6 class="mb-2">${selectedOption.text} Details:</h6>
                ${!isAdvanceOrder ? '<div class="alert alert-warning mb-3">Please note: Orders require at least 1 hour preparation time.</div>' : ''}
            `;
            
            if (name === 'cash') {
                detailsHtml += `
                    <p class="mb-0">Please prepare the exact amount.</p>
                `;
            } else if (name === 'gcash' || name === 'maya') {
                detailsHtml += `

                   
                `;
            }
            
            paymentInfo.innerHTML = detailsHtml;
            paymentDetails.style.display = 'block';
        } else {
            paymentDetails.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Check for pending booking
        const pendingBooking = sessionStorage.getItem('pendingBooking');
        if (pendingBooking) {
            const bookingDetails = JSON.parse(pendingBooking);
            
            // You can use these details to pre-fill forms or display information
            // Store them in hidden inputs if needed
            document.getElementById('bookingId').value = bookingDetails.packageId;
            // ... set other relevant fields ...
        }
    });

    // After successful advance order submission
    function completeBooking() {
        const pendingBooking = JSON.parse(sessionStorage.getItem('pendingBooking'));
        
        // Combine booking details with advance order details
        const formData = new FormData();
        Object.keys(pendingBooking).forEach(key => {
            formData.append(key, pendingBooking[key]);
        });
        
        // Add advance order details
        // ... add your advance order items ...

        // Submit everything to server
        fetch('table_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Clear the pending booking
                sessionStorage.removeItem('pendingBooking');
                // Redirect to bookings page
                window.location.href = 'mybookings.php';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: data.message,
                    confirmButtonColor: '#b6860a'
                });
            }
        });
    }

    // Make sure the table details are properly passed when submitting the order
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        // ... existing code ...
        
        const tableDetailsInput = document.createElement('input');
        tableDetailsInput.type = 'hidden';
        tableDetailsInput.name = 'table_details';
        tableDetailsInput.value = JSON.stringify({
            ...<?php echo json_encode($tableDetails); ?>,
            guests: <?php echo json_encode($tableDetails['guests']); ?>
        });
        this.appendChild(tableDetailsInput);
    });
    </script>
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Add this near the end of your body tag -->
    <div class="modal fade" id="addOnsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Content will be dynamically inserted here -->
            </div>
</div>
    </div>

    <!-- Update your order form -->
    <form id="orderForm" method="POST" action="process_order.php" enctype="multipart/form-data">
        <?php if ($hasTableDetails): ?>
            <input type="hidden" name="extra_hours" value="<?php echo $extraHours; ?>">
            <input type="hidden" name="hourly_rate" value="<?php echo $hourlyRate ?? 0; ?>">
            <input type="hidden" name="extra_fee" value="<?php echo $extraFee; ?>">
        <?php endif; ?>
        <input type="hidden" name="payment_method" id="selected_payment_method">
        <!-- The rest of your form fields will be added dynamically -->
    </form>

</body>
</html>
