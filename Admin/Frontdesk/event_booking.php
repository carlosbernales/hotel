<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create event_bookings table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS event_bookings (
    id varchar(20) NOT NULL,
    user_id int(11) NOT NULL,
    customer_name varchar(100) NOT NULL,
    package_name varchar(100) NOT NULL,
    package_price decimal(10,2) NOT NULL,
    event_date date NOT NULL,
    base_price decimal(10,2) NOT NULL,
    overtime_hours int(11) DEFAULT 0,
    overtime_charge decimal(10,2) DEFAULT 0.00,
    extra_guests int(11) DEFAULT 0,
    extra_guest_charge decimal(10,2) DEFAULT 0.00,
    total_amount decimal(10,2) NOT NULL,
    paid_amount decimal(10,2) NOT NULL,
    remaining_balance decimal(10,2) NOT NULL,
    event_type varchar(50) DEFAULT NULL,
    start_time time NOT NULL,
    end_time time NOT NULL,
    number_of_guests int(11) NOT NULL,
    payment_method varchar(50) NOT NULL,
    payment_type varchar(50) NOT NULL,
    reference_number varchar(50) DEFAULT NULL,
    payment_status varchar(255) DEFAULT NULL,
    booking_status varchar(20) DEFAULT 'pending',
    reserve_type varchar(50) DEFAULT 'Regular',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    booking_source varchar(50) DEFAULT 'Regular Booking'
)";
$con->query($create_table);

// Update the query to fetch from event_packages table
$query = "SELECT 
    id,
    name,
    price,
    description,
    COALESCE(image_path, 'uploads/event_packages/default_package.jpg') as image_path,
    max_guests,
    duration,
    menu_items,
    time_limit,
    notes,
    status
FROM event_packages 
WHERE status = 'Available' 
ORDER BY max_guests ASC, price ASC";

$result = $con->query($query);
$packages = [];

// Format packages for display
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Handle image path with fallback
        $image_path = isset($row['image_path']) && !empty($row['image_path']) 
            ? $row['image_path'] 
            : 'uploads/event_packages/default_package.jpg';
        
        // Verify image exists
        if (!file_exists($image_path)) {
            $image_path = 'uploads/event_packages/default_package.jpg';
        }
        
        // Add to packages array
        $packages[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'type' => $row['max_guests'] . ' PAX',
            'menu' => $row['menu_items'],
            'price' => $row['price'],
            'max_guests' => $row['max_guests'],
            'duration' => $row['duration'],
            'notes' => $row['notes'],
            'image_path' => $image_path,
            'time_limit' => $row['time_limit']
        ];
    }
}

// If no packages found, use default packages
if (empty($packages)) {
    $packages = [
        // 30 PAX Packages
        [
            'id' => '30A',
            'name' => 'Package A',
            'type' => '30 PAX',
            'menu' => '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks',
            'price' => 28000,
            'max_guests' => 30,
            'duration' => 4,
            'notes' => '*This package is only available up to 2:00pm only',
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ],
        [
            'id' => '30B',
            'name' => 'Package B',
            'type' => '30 PAX',
            'menu' => '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks',
            'price' => 33000,
            'max_guests' => 30,
            'duration' => 4,
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ],
        [
            'id' => '30C',
            'name' => 'Package C',
            'type' => '30 PAX',
            'menu' => '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station**, Salad Bar, Rice, 2 Desserts, Drinks',
            'price' => 46000,
            'max_guests' => 30,
            'duration' => 4,
            'notes' => '**Assumes 3,000g (100g per person) of Wagyu steak will be served',
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ],
        // 50 PAX Packages
        [
            'id' => '50A',
            'name' => 'Package A',
            'type' => '50 PAX',
            'menu' => '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks',
            'price' => 47500,
            'max_guests' => 50,
            'duration' => 5,
            'notes' => '*This package is only available up to 2:00pm only',
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ],
        [
            'id' => '50B',
            'name' => 'Package B',
            'type' => '50 PAX',
            'menu' => '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks',
            'price' => 55000,
            'max_guests' => 50,
            'duration' => 5,
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ],
        [
            'id' => '50C',
            'name' => 'Package C',
            'type' => '50 PAX',
            'menu' => '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station**, Salad Bar, Rice, 2 Desserts, Drinks',
            'price' => 76800,
            'max_guests' => 50,
            'duration' => 5,
            'notes' => '**Assumes 5,000g (100g per person) of Wagyu steak will be served',
            'image_path' => 'uploads/event_packages/default_package.jpg'
        ]
    ];
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    // Enable error reporting for debugging
    error_log("Received booking submission");
    error_log("POST data: " . print_r($_POST, true));
    
    $user_id = $_SESSION['user_id'];
    $customer_name = mysqli_real_escape_string($con, $_POST['customer_name']);
    $package_name = mysqli_real_escape_string($con, $_POST['package_name']);
    $package_price = floatval($_POST['package_price']);
    $event_date = mysqli_real_escape_string($con, $_POST['event_date']);
    $base_price = $package_price;
    $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($con, $_POST['end_time']);
    $number_of_guests = intval($_POST['number_of_guests']);
    $payment_method = mysqli_real_escape_string($con, $_POST['payment_method']);
    $payment_type = mysqli_real_escape_string($con, $_POST['payment_type']);
    $event_type = mysqli_real_escape_string($con, $_POST['event_type']);
    
    // Calculate any additional charges
    $overtime_hours = isset($_POST['overtime_hours']) ? intval($_POST['overtime_hours']) : 0;
    $overtime_charge = $overtime_hours * 2000; // ₱2,000 per hour
    
    $extra_guests = max(0, $number_of_guests - 50); // Assuming base package is for 50 guests
    $extra_guest_charge = $extra_guests * 500; // ₱500 per additional guest
    
    // Calculate total amount
    $total_amount = $base_price + $overtime_charge + $extra_guest_charge;
    
    // Set initial paid amount and remaining balance
    $paid_amount = 0;
    $remaining_balance = $total_amount;
    
    // Generate unique ID
    $unique_id = 'EVT' . date('YmdHis') . rand(100, 999);
    
    // Insert booking into database
    $sql = "INSERT INTO event_bookings (id, user_id, customer_name, package_name, package_price, event_date, base_price, 
            overtime_hours, overtime_charge, extra_guests, extra_guest_charge, total_amount, paid_amount, 
            remaining_balance, event_type, start_time, end_time, number_of_guests, payment_method, 
            payment_type, booking_source, booking_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Regular Booking', 'pending')";
    
    $stmt = $con->prepare($sql);
    
    // Add more detailed error logging
    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        if (isset($_POST['ajax'])) {
            echo json_encode(['status' => 'error', 'message' => "Error preparing statement: " . $con->error]);
            exit;
        }
        $_SESSION['error_message'] = "Error preparing statement: " . $con->error;
        header('Location: event_booking.php');
        exit();
    }
    
    // Log the values being bound
    error_log("Binding parameters for booking:");
    error_log("Unique ID: " . $unique_id);
    error_log("User ID: " . $user_id);
    error_log("Customer Name: " . $customer_name);
    error_log("Package Name: " . $package_name);
    error_log("Package Price: " . $package_price);
    error_log("Event Date: " . $event_date);
    error_log("Event Type: " . $event_type);
    error_log("Start Time: " . $start_time);
    error_log("End Time: " . $end_time);
    error_log("Number of Guests: " . $number_of_guests);
    
    $stmt->bind_param(
        'sissddsddiidddsssss',
        $unique_id, $user_id, $customer_name, $package_name, $package_price, $event_date, $base_price,
        $overtime_hours, $overtime_charge, $extra_guests, $extra_guest_charge,
        $total_amount, $paid_amount, $remaining_balance,
        $event_type, $start_time, $end_time, $number_of_guests,
        $payment_method, $payment_type
    );
    
    // Add more error logging
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        if (isset($_POST['ajax'])) {
            echo json_encode(['status' => 'error', 'message' => "Error submitting booking: " . $stmt->error]);
            exit;
        }
        $_SESSION['error_message'] = "Error submitting booking: " . $stmt->error;
        header('Location: event_booking.php');
        exit();
    } else {
        error_log("Booking inserted successfully with ID: " . $unique_id);
        
        // Verify the insertion
        $verify_sql = "SELECT * FROM event_bookings WHERE id = ?";
        $verify_stmt = $con->prepare($verify_sql);
        if ($verify_stmt) {
            $verify_stmt->bind_param('s', $unique_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            if ($verify_row = $verify_result->fetch_assoc()) {
                error_log("Verified inserted booking data:");
                error_log(print_r($verify_row, true));
            } else {
                error_log("Could not find inserted booking with ID: " . $unique_id);
            }
            $verify_stmt->close();
        }
        
        if (isset($_POST['ajax'])) {
            echo json_encode(['status' => 'success', 'message' => "Event booking submitted successfully! Your booking ID is " . $unique_id]);
            exit;
        }
        $_SESSION['success_message'] = "Event booking submitted successfully! Your booking ID is " . $unique_id;
        header('Location: event_booking.php');
        exit();
    }
    
    $stmt->close();
    
    if (!isset($_POST['ajax'])) {
        header('Location: event_booking.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Booking - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .gallery-section {
            padding: 4rem 0;
            background-color: #f8f9fa;
        }
        
        .gallery-container {
            margin-top: 2rem;
        }
        
        .gallery-item {
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
        }
        
        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .space-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .space-card:hover {
            transform: translateY(-5px);
        }

        .space-image {
            height: 200px;
            overflow: hidden;
        }

        .space-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .space-content {
            padding: 20px;
        }

        .space-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .space-details {
            margin: 15px 0;
        }

        .space-details ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .space-details li {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .space-details i {
            margin-right: 10px;
            color: #ffc107;
        }

        .price {
            font-size: 1.8rem;
            color: #ffc107;
            font-weight: 600;
            margin: 15px 0;
        }

        .btn-reserve {
            background: #ffc107;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-reserve:hover {
            background: #e0a800;
        }
        
        .package-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .package-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .package-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
        }
        
        .package-content {
            padding: 20px;
        }
        
        .package-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .package-price {
            color: #DAA520;
            font-size: 1.8rem;
            font-weight: bold;
            margin: 15px 0;
        }
        
        .package-details {
            margin: 15px 0;
        }

        .package-details ul {
            list-style: none;
            padding: 0;
        }

        .package-details li {
            margin-bottom: 8px;
        }

        .package-details i {
            color: #DAA520;
            margin-right: 10px;
        }

        .btn-view {
            background: transparent;
            border: 2px solid #DAA520;
            color: #DAA520;
            width: 100%;
        }
        
        .btn-book {
            background: #DAA520;
            border: none;
            color: white;
            width: 100%;
        }
        
        .price-banner {
            background-color: #DAA520 !important;
        }
        
        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .section-title {
            color: #333;
            font-weight: 600;
        }

        .text-warning {
            color: #DAA520 !important;
        }

        .badge-success {
            background-color: #28a745;
            font-size: 1rem;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-content {
            border-radius: 8px;
        }

        .price-tag {
            background-color: #DAA520;
            border-radius: 4px;
        }

        .list-unstyled li {
            line-height: 1.8;
        }

        .card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .text-warning {
            color: #DAA520 !important;
        }

        .btn-warning {
            background-color: #DAA520;
            border-color: #DAA520;
            color: white;
        }

        .btn-outline-warning {
            border-color: #DAA520;
            color: #DAA520;
        }

        .btn-outline-warning:hover {
            background-color: #DAA520;
            color: white;
        }

        .modal-content {
            border: none;
            border-radius: 8px;
        }

        .price-tag {
            background-color: #DAA520;
            border-radius: 4px;
        }

        .section-title {
            color: #333;
            font-weight: 600;
        }

        .badge-success {
            background-color: #28a745;
            font-size: 0.9rem;
        }

        .list-unstyled li {
            line-height: 1.8;
        }

        .modal-header {
            padding: 1rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-image-container {
            width: 100%;
            height: 250px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .modal-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-width: 100%;
            max-height: 250px;
        }

        .page-title {
            color: #333;
            font-weight: 600;
        }

        .section-title {
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: #DAA520;
        }

        .package-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .package-card:hover {
            transform: translateY(-5px);
        }

        .package-image {
            position: relative;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .package-title {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .package-price {
            color: #DAA520;
            font-size: 1.75rem;
            font-weight: bold;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .package-features li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .package-features i {
            margin-right: 10px;
            color: #DAA520;
        }

        .text-warning {
            color: #DAA520 !important;
        }

        .btn-warning {
            background-color: #DAA520;
            border-color: #DAA520;
            color: white;
        }

        .btn-outline-warning {
            border-color: #DAA520;
            color: #DAA520;
        }

        .btn-outline-warning:hover {
            background-color: #DAA520;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 pb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item active">Event Booking</li>
                        </ol>
                    </nav>
                    <h1 class="page-title h3 mb-4">Our Event Packages</h1>
                </div>
            </div>

            <!-- Venue Rental Only -->
            <div class="package-section mb-5">
                <h2 class="section-title mb-4">VENUE RENTAL ONLY</h2>
                <div class="row">
                    <?php
                    foreach ($packages as $package):
                        if ($package['name'] == 'Venue Rental Only'):
                    ?>
                    <div class="col-md-4">
                        <div class="package-card">
                            <div class="package-image">
                                <img src="<?php echo htmlspecialchars($package['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($package['name']); ?>"
                                     class="img-fluid w-100"
                                     style="height: 250px; object-fit: cover;">
                                <span class="badge badge-success position-absolute" style="top: 15px; right: 15px;">Available</span>
                            </div>
                            <div class="package-content p-4">
                                <h3 class="package-title"><?php echo htmlspecialchars($package['name']); ?></h3>
                                <div class="package-price mb-3">₱<?php echo number_format($package['price'], 2); ?></div>
                                <ul class="package-features">
                                    <li><i class="fa fa-check text-warning"></i> <?php echo htmlspecialchars($package['duration']); ?> hours venue rental</li>
                                    <li><i class="fa fa-check text-warning"></i> Tables and Tiffany chairs</li>
                                </ul>
                                <div class="package-buttons mt-4">
                                    <button type="button" class="btn btn-outline-warning btn-block mb-2" 
                                            onclick="viewPackageDetails(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        <i class="fa fa-eye"></i> View Package
                                    </button>
                                    <button type="button" class="btn btn-warning btn-block" 
                                            onclick="openBookingModal(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Intimate Food Package -->
            <div class="package-section mb-5">
                <h2 class="section-title mb-4">INTIMATE FOOD PACKAGE FOR 30 PAX</h2>
                <div class="row">
                    <?php
                    foreach ($packages as $package):
                        if ($package['max_guests'] == 30 && $package['name'] != 'Venue Rental Only'):
                    ?>
                    <div class="col-md-4">
                        <div class="package-card">
                            <div class="package-image">
                                <img src="<?php echo htmlspecialchars($package['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($package['name']); ?>"
                                     class="img-fluid w-100"
                                     style="height: 250px; object-fit: cover;">
                                <span class="badge badge-success position-absolute" style="top: 15px; right: 15px;">Available</span>
                            </div>
                            <div class="package-content p-4">
                                <h3 class="package-title"><?php echo htmlspecialchars($package['name']); ?></h3>
                                <div class="package-price mb-3">₱<?php echo number_format($package['price'], 2); ?></div>
                                <ul class="package-features">
                                    <li><i class="fa fa-check text-warning"></i> <?php echo htmlspecialchars($package['duration']); ?> hours venue rental</li>
                                    <li><i class="fa fa-check text-warning"></i> Tables and Tiffany chairs</li>
                                    <?php if (!empty($package['menu'])): ?>
                                    <li><i class="fa fa-check text-warning"></i> <?php echo htmlspecialchars($package['menu']); ?></li>
                                    <?php endif; ?>
                                </ul>
                                <div class="package-buttons mt-4">
                                    <button type="button" class="btn btn-outline-warning btn-block mb-2" 
                                            onclick="viewPackageDetails(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        <i class="fa fa-eye"></i> View Package
                                    </button>
                                    <button type="button" class="btn btn-warning btn-block" 
                                            onclick="openBookingModal(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Garden Food Package -->
            <div class="package-section mb-5">
                <h2 class="section-title mb-4">THE GARDEN INTIMATE FOOD PACKAGES FOR 50 PAX</h2>
                <div class="row">
                    <?php
                    foreach ($packages as $package):
                        if ($package['max_guests'] == 50 && $package['name'] != 'Venue Rental Only'):
                    ?>
                    <div class="col-md-4">
                        <div class="package-card">
                            <div class="package-image">
                                <img src="<?php echo htmlspecialchars($package['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($package['name']); ?>"
                                     class="img-fluid w-100"
                                     style="height: 250px; object-fit: cover;">
                                <span class="badge badge-success position-absolute" style="top: 15px; right: 15px;">Available</span>
                            </div>
                            <div class="package-content p-4">
                                <h3 class="package-title"><?php echo htmlspecialchars($package['name']); ?></h3>
                                <div class="package-price mb-3">₱<?php echo number_format($package['price'], 2); ?></div>
                                <ul class="package-features">
                                    <li><i class="fa fa-check text-warning"></i> <?php echo htmlspecialchars($package['duration']); ?> hours venue rental</li>
                                    <li><i class="fa fa-check text-warning"></i> Tables and Tiffany chairs</li>
                                    <?php if (!empty($package['menu'])): ?>
                                    <li><i class="fa fa-check text-warning"></i> <?php echo htmlspecialchars($package['menu']); ?></li>
                                    <?php endif; ?>
                                </ul>
                                <div class="package-buttons mt-4">
                                    <button type="button" class="btn btn-outline-warning btn-block mb-2" 
                                            onclick="viewPackageDetails(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        <i class="fa fa-eye"></i> View Package
                                    </button>
                                    <button type="button" class="btn btn-warning btn-block" 
                                            onclick="openBookingModal(<?php echo htmlspecialchars(json_encode($package), ENT_QUOTES, 'UTF-8'); ?>)">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bookingModalLabel">Event Booking Form</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="process_event_booking.php" id="bookingForm">
                        <input type="hidden" name="submit_booking" value="1">
                        <input type="hidden" name="package_name" id="selected_package_name">
                        <input type="hidden" name="package_price" id="package_price">
                        <input type="hidden" name="max_guests" id="selected_max_guests">
                        
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                    <label class="required-field">Customer Name</label>
                                    <input type="text" class="form-control" name="customer_name" required>
                                </div>
                            </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="required-field">Event Type</label>
                                <select class="form-control" name="event_type" id="event_type" required>
                                    <option value="">Select Event Type</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Other">Other</option>
                                </select>
                                </div>
                            <div class="form-group col-md-6">
                                <label class="required-field">Event Date</label>
                                <input type="date" class="form-control" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="required-field">Start Time</label>
                                <input type="time" class="form-control" name="start_time" required>
                                </div>
                            <div class="form-group col-md-6">
                                <label class="required-field">End Time</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                    <label class="required-field">Number of Guests</label>
                                <input type="number" class="form-control" name="number_of_guests" id="number_of_guests" required min="1">
                                <small class="form-text text-muted" id="guest_note">Maximum guests: <span id="max_guests_display">0</span></small>
                                <div class="alert alert-warning mt-2 d-none" id="additional_guest_warning"></div>
                                </div>
                            <div class="form-group col-md-6">
                                <label>Overtime Hours</label>
                                <input type="number" class="form-control" name="overtime_hours" readonly style="background-color: #e9ecef;" value="0">
                                <small class="form-text text-muted">Automatically calculated based on selected times</small>
                            </div>
                        </div>

                        <!-- Fee Summary - Only shows when guests exceed maximum -->
                        <div id="fee_summary_container" class="d-none">
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="fee-summary mt-3 p-3 bg-light rounded">
                                        <h5>Fee Summary</h5>
                                        <div class="d-flex justify-content-between">
                                            <span>Package Price:</span>
                                            <span>₱<span id="display_package_price">0.00</span></span>
                                </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Additional Guest Fee:</span>
                                            <span>₱<span id="additional_guest_fee">0.00</span></span>
                            </div>
                                        <hr>
                                        <div class="d-flex justify-content-between font-weight-bold">
                                            <span>Total Amount:</span>
                                            <span>₱<span id="total_amount">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="form-group col-md-6">
                                <label class="required-field">Payment Method</label>
                                <select class="form-control" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Credit Card">Credit Card</option>
                                    </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="required-field">Payment Type</label>
                                <select class="form-control" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Full Payment">Full Payment</option>
                                    <option value="Down Payment">Down Payment</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="bookingForm" class="btn btn-primary">Submit Booking</button>
                </div>
                                </div>
                            </div>
                        </div>

    <!-- Package Details Modal -->
    <div class="modal fade" id="packageDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title" id="modalPackageName"></h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="modal-image-container mb-3">
                        <img id="modalPackageImage" src="" alt="Package Image" class="modal-image">
                    </div>
                    
                    <div class="price-tag text-center py-3 mb-3" style="background-color: #DAA520;">
                        <h3 class="text-white mb-0"><i class="fa fa-tag"></i> ₱<span id="modalPackagePrice">0</span></h3>
                    </div>

                    <div class="package-details mb-4">
                        <h5 class="section-title border-bottom pb-2">Package Details</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2"><i class="fa fa-clock-o text-warning"></i> <span id="modalDuration"></span> hours venue rental</li>
                            <li class="mb-2"><i class="fa fa-users text-warning"></i> Up to <span id="modalMaxGuests"></span> guests</li>
                            <li class="mb-2"><i class="fa fa-chair text-warning"></i> Tables and Tiffany chairs</li>
                            <li class="mb-2"><i class="fa fa-snowflake-o text-warning"></i> Air-conditioned venue</li>
                            <li class="mb-2" id="modalMenu"></li>
                        </ul>
                    </div>

                    <div class="important-notes">
                        <h5 class="section-title border-bottom pb-2">Important Notes</h5>
                        <ul class="list-unstyled mt-3">
                            <li class="mb-2">• Operating hours: 6:30 AM to 11:00 PM</li>
                            <li class="mb-2">• Exclusive use of air-conditioned tent area for 5 hours</li>
                            <li class="mb-2">• Corkage fee applies for outside food and beverages</li>
                            <li class="mb-2">• 50% non-refundable down payment required</li>
                            <li class="mb-2">• Extension rate: ₱2,000/hour (₱3,000/hour after midnight)</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <span class="badge badge-success px-4 py-2">Available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Booking Summary Modal -->
    <div class="modal fade" id="eventBookingSummaryModal" tabindex="-1" role="dialog" aria-labelledby="eventBookingSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="eventBookingSummaryModalLabel">Event Booking Summary</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                        </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Event Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Customer Name:</strong></td>
                                    <td id="event_summary_customer_name"></td>
                                </tr>
                                <tr>
                                    <td><strong>Event Type:</strong></td>
                                    <td id="event_summary_event_type"></td>
                                </tr>
                                <tr>
                                    <td><strong>Event Date:</strong></td>
                                    <td id="event_summary_event_date"></td>
                                </tr>
                                <tr>
                                    <td><strong>Event Time:</strong></td>
                                    <td id="event_summary_time"></td>
                                </tr>
                                <tr>
                                    <td><strong>Number of Guests:</strong></td>
                                    <td id="event_summary_guests"></td>
                                </tr>
                                <tr>
                                    <td><strong>Overtime Hours:</strong></td>
                                    <td id="event_summary_overtime_hours"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Package & Payment Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Package Name:</strong></td>
                                    <td id="event_summary_package"></td>
                                </tr>
                                <tr>
                                    <td><strong>Package Price:</strong></td>
                                    <td>₱<span id="event_summary_package_price">0.00</span></td>
                                </tr>
                                <tr id="event_summary_additional_guest_row">
                                    <td><strong>Additional Guest Fee:</strong></td>
                                    <td>₱<span id="event_summary_additional_guest_fee">0.00</span></td>
                                </tr>
                                <tr id="event_summary_overtime_row">
                                    <td><strong>Overtime Fee:</strong></td>
                                    <td>₱<span id="event_summary_overtime_fee">0.00</span></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td><strong>Total Amount:</strong></td>
                                    <td>₱<span id="event_summary_total_amount">0.00</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td id="event_summary_payment_method"></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td id="event_summary_payment_type"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="editEventBooking()">Edit</button>
                    <button type="button" class="btn btn-primary" id="confirmEventBooking">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    function number_format(number, decimals) {
        return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function viewPackageDetails(package) {
        console.log("View Package clicked:", package); // Debug log
        
        try {
            // If package is a string, parse it
            if (typeof package === 'string') {
                package = JSON.parse(package);
            }
            
            // Update modal content
            $('#modalPackageName').text(package.name);
            $('#modalPackagePrice').text(number_format(package.price, 2));
            $('#modalDuration').text(package.duration);
            $('#modalMaxGuests').text(package.max_guests);
            
            // Update menu if exists
            if (package.menu) {
                $('#modalMenu').html(`<i class="fa fa-utensils text-warning"></i> ${package.menu}`).show();
            } else {
                $('#modalMenu').hide();
            }
            
            // Update the image source
            $('#modalPackageImage').attr('src', package.image_path)
                .on('error', function() {
                    this.src = 'uploads/event_packages/default_package.jpg';
                });
            
            // Show modal
            $('#packageDetailsModal').modal('show');
        } catch (error) {
            console.error("Error in viewPackageDetails:", error);
        }
    }

    function openBookingModal(package) {
        // Set hidden fields
        $('#selected_package_name').val(package.name);
        $('#selected_max_guests').val(package.max_guests);
        $('#package_price').val(package.price);
        
        // Set modal title and max guests display
        $('#bookingModalLabel').text('Book ' + package.name);
        $('#max_guests_display').text(package.max_guests);
        
        // Reset form and hide fee summary
        $('#bookingForm')[0].reset();
        $('#additional_guest_warning').addClass('d-none');
        $('#fee_summary_container').addClass('d-none');
        $('#guest_note').removeClass('d-none');
        
        // Initialize price display
        $('#display_package_price').text(number_format(package.price, 2));
        $('#total_amount').text(number_format(package.price, 2));
        
        // Show booking modal
        $('#bookingModal').modal('show');
    }

    function editEventBooking() {
        // Get all the values from the summary modal
        const customerName = $('#event_summary_customer_name').text();
        const eventType = $('#event_summary_event_type').text();
        const eventDate = $('#event_summary_event_date').text();
        const timeRange = $('#event_summary_time').text().split(' - ');
        const startTime = timeRange[0];
        const endTime = timeRange[1];
        const numberOfGuests = $('#event_summary_guests').text();
        const paymentMethod = $('#event_summary_payment_method').text();
        const paymentType = $('#event_summary_payment_type').text();
        
        // Hide summary modal
        $('#eventBookingSummaryModal').modal('hide');
        
        // Wait for modal to hide before showing booking modal
        setTimeout(() => {
            // Set the values back in the booking form
            $('input[name="customer_name"]').val(customerName);
            $('#event_type').val(eventType);
            $('input[name="event_date"]').val(eventDate);
            $('input[name="start_time"]').val(startTime);
            $('input[name="end_time"]').val(endTime);
            $('#number_of_guests').val(numberOfGuests);
            $('select[name="payment_method"]').val(paymentMethod);
            $('select[name="payment_type"]').val(paymentType);
            
            // Trigger the number of guests input event to recalculate fees
            $('#number_of_guests').trigger('input');
            
            // Show the booking modal
            $('#bookingModal').modal('show');
        }, 500);
    }

    $(document).ready(function() {
        // Initialize all modals
        $('#bookingModal').modal({
            show: false,
            backdrop: 'static',
            keyboard: false
        });
        
        $('#packageDetailsModal').modal({
            show: false
        });
        
        $('#eventBookingSummaryModal').modal({
            show: false,
            backdrop: 'static',
            keyboard: false
        });

        // Ensure proper modal cleanup on hide
        $('.modal').on('hidden.bs.modal', function () {
            if($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });

        // Handle event type selection
        $('#event_type').change(function() {
            if ($(this).val() === 'Other') {
                $('#other_event_type_div').show();
                $('#other_event_type').prop('required', true);
            } else {
                $('#other_event_type_div').hide();
                $('#other_event_type').prop('required', false);
            }
        });

        // Handle number of guests changes
        $('#number_of_guests').on('input', function() {
            const maxGuests = parseInt($('#selected_max_guests').val());
            const currentGuests = parseInt($(this).val()) || 0;
            const packagePrice = parseFloat($('#package_price').val());
            
            // Reset displays first
            $('#fee_summary_container').addClass('d-none');
            $('#additional_guest_warning').addClass('d-none');
            $('#guest_note').removeClass('d-none');
            
            if (currentGuests > maxGuests && currentGuests > 0) {
                const additionalGuests = currentGuests - maxGuests;
                const additionalGuestFee = additionalGuests * 1000;
                
                // Show warning with calculation
                $('#additional_guest_warning').removeClass('d-none')
                    .html(`Additional guest fee will apply: ₱1,000 × ${additionalGuests} = ₱${number_format(additionalGuestFee, 2)}`);
                
                // Show and update fee summary
                $('#fee_summary_container').removeClass('d-none');
                $('#display_package_price').text(number_format(packagePrice, 2));
                $('#additional_guest_fee').text(number_format(additionalGuestFee, 2));
                
                // Get current overtime charges if any
                const overtimeHours = parseInt($('input[name="overtime_hours"]').val()) || 0;
                const overtimeCharge = overtimeHours * 2000;
                
                // Calculate total with both additional guests and overtime
                const totalAmount = packagePrice + additionalGuestFee + overtimeCharge;
                $('#total_amount').text(number_format(totalAmount, 2));
                
                // Hide the maximum guests note when showing fee summary
                $('#guest_note').addClass('d-none');
            } else {
                // Reset to base package price if within limits
                const overtimeHours = parseInt($('input[name="overtime_hours"]').val()) || 0;
                const overtimeCharge = overtimeHours * 2000;
                $('#total_amount').text(number_format(packagePrice + overtimeCharge, 2));
            }
        });

        // Handle start and end time changes
        $('input[name="start_time"], input[name="end_time"]').on('change', function() {
            const startTime = $('input[name="start_time"]').val();
            const endTime = $('input[name="end_time"]').val();
            
            if (!startTime || !endTime) return;
            
            // Validate operating hours (6:30 AM to 11:00 PM)
            const start = new Date(`2000-01-01 ${startTime}`);
            const end = new Date(`2000-01-01 ${endTime}`);
            const minTime = new Date(`2000-01-01 06:30`);
            const maxTime = new Date(`2000-01-01 23:00`);
            
            if (start < minTime || end > maxTime) {
                Swal.fire({
                    title: 'Operating Hours',
                    text: 'Our operating hours are from 6:30 AM to 11:00 PM only.',
                    icon: 'warning',
                    confirmButtonColor: '#DAA520'
                });
                $(this).val('');
                return;
            }
            
            if (start >= end) {
                Swal.fire({
                    title: 'Invalid Time',
                    text: 'End time must be after start time.',
                    icon: 'warning',
                    confirmButtonColor: '#DAA520'
                });
                $(this).val('');
                return;
            }

            // Get package duration (base hours)
            const packageDuration = 5; // Default to 5 hours if not specified
            
            // Calculate total duration in hours
            const durationHours = (end - start) / (1000 * 60 * 60);
            
            // Calculate overtime hours
            const overtimeHours = Math.max(0, Math.ceil(durationHours - packageDuration));
            
            // Update overtime hours input
            $('input[name="overtime_hours"]').val(overtimeHours);
            
            // Calculate overtime charges
            if (overtimeHours > 0) {
                const overtimeCharge = overtimeHours * 2000; // ₱2,000 per hour
                const packagePrice = parseFloat($('#package_price').val());
                const currentGuests = parseInt($('#number_of_guests').val()) || 0;
                const maxGuests = parseInt($('#selected_max_guests').val());
                const additionalGuests = Math.max(0, currentGuests - maxGuests);
                
                // Update fee summary with overtime charges
                updateFeeSummaryWithOvertime(packagePrice, additionalGuests, overtimeHours, overtimeCharge);

                // Show overtime notification
                Swal.fire({
                    title: 'Overtime Notice',
                    text: `Your selected duration exceeds the package hours by ${overtimeHours} hour(s). Additional charge of ₱2,000 per hour will apply.`,
                    icon: 'info',
                    confirmButtonColor: '#DAA520'
                });
            } else {
                // Reset overtime display if no overtime
                $('input[name="overtime_hours"]').val(0);
                const packagePrice = parseFloat($('#package_price').val());
                const currentGuests = parseInt($('#number_of_guests').val()) || 0;
                const maxGuests = parseInt($('#selected_max_guests').val());
                const additionalGuests = Math.max(0, currentGuests - maxGuests);
                updateFeeSummaryWithOvertime(packagePrice, additionalGuests, 0, 0);
            }
        });

        function updateFeeSummaryWithOvertime(packagePrice, additionalGuests, overtimeHours, overtimeCharge) {
            // Reset displays
            $('#fee_summary_container').removeClass('d-none');
            
            // Calculate fees
            const additionalGuestFee = additionalGuests * 1000;
            const totalAmount = parseFloat(packagePrice) + additionalGuestFee + overtimeCharge;
            
            // Update display
            $('#display_package_price').text(number_format(packagePrice, 2));
            $('#additional_guest_fee').text(number_format(additionalGuestFee, 2));
            
            // Add or update overtime fee display
            if ($('#overtime_fee_row').length === 0 && overtimeHours > 0) {
                $('#fee_summary_container .fee-summary').append(`
                    <div id="overtime_fee_row" class="d-flex justify-content-between">
                        <span>Overtime Fee (${overtimeHours} hour${overtimeHours > 1 ? 's' : ''}):</span>
                        <span>₱<span id="overtime_fee">0.00</span></span>
                    </div>
                `);
            } else if (overtimeHours === 0 && $('#overtime_fee_row').length > 0) {
                $('#overtime_fee_row').remove();
            } else if (overtimeHours > 0) {
                $('#overtime_fee_row span:first').text(`Overtime Fee (${overtimeHours} hour${overtimeHours > 1 ? 's' : ''}):`);
            }
            
            if (overtimeHours > 0) {
                $('#overtime_fee').text(number_format(overtimeCharge, 2));
            }
            
            $('#total_amount').text(number_format(totalAmount, 2));
            
            // Show additional guest warning if applicable
            if (additionalGuests > 0) {
                $('#additional_guest_warning').removeClass('d-none')
                    .html(`Additional guest fee will apply: ₱1,000 × ${additionalGuests} = ₱${number_format(additionalGuestFee, 2)}`);
                $('#guest_note').addClass('d-none');
            } else {
                $('#additional_guest_warning').addClass('d-none');
                $('#guest_note').removeClass('d-none');
            }
        }

        // Handle form submission
        $('#bookingForm').on('submit', function(e) {
                e.preventDefault();
            
            // Get form values
            const customerName = $('input[name="customer_name"]').val();
            const eventType = $('#event_type').val();
            const eventDate = $('input[name="event_date"]').val();
            const startTime = $('input[name="start_time"]').val();
            const endTime = $('input[name="end_time"]').val();
            const numberOfGuests = $('#number_of_guests').val();
            const packageName = $('#selected_package_name').val();
            const packagePrice = parseFloat($('#package_price').val());
            const paymentMethod = $('select[name="payment_method"]').val();
            const paymentType = $('select[name="payment_type"]').val();
            const overtimeHours = parseInt($('input[name="overtime_hours"]').val()) || 0;

            // Calculate additional fees
            const maxGuests = parseInt($('#selected_max_guests').val());
            let additionalGuestFee = 0;
            if (numberOfGuests > maxGuests) {
                additionalGuestFee = (numberOfGuests - maxGuests) * 1000;
            }

            // Calculate overtime fee
            let overtimeFee = overtimeHours * 2000; // ₱2,000 per hour

            // Calculate total amount
            const totalAmount = packagePrice + additionalGuestFee + overtimeFee;

            // Format the event date
            const formattedDate = new Date(eventDate);
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDateStr = formattedDate.toLocaleDateString('en-US', dateOptions);

            // Update summary modal
            $('#event_summary_customer_name').text(customerName);
            $('#event_summary_event_type').text(eventType);
            $('#event_summary_event_date').text(formattedDateStr);
            $('#event_summary_time').text(`${startTime} - ${endTime}`);
            $('#event_summary_guests').text(numberOfGuests);
            $('#event_summary_overtime_hours').text(overtimeHours + ' hour(s)');
            $('#event_summary_package').text(packageName);
            $('#event_summary_package_price').text(number_format(packagePrice, 2));
            $('#event_summary_additional_guest_fee').text(number_format(additionalGuestFee, 2));
            $('#event_summary_overtime_fee').text(number_format(overtimeFee, 2));
            $('#event_summary_total_amount').text(number_format(totalAmount, 2));
            $('#event_summary_payment_method').text(paymentMethod);
            $('#event_summary_payment_type').text(paymentType);

            // Show/hide additional fee rows based on values
            if (additionalGuestFee > 0) {
                $('#event_summary_additional_guest_row').show();
            } else {
                $('#event_summary_additional_guest_row').hide();
            }

            if (overtimeFee > 0) {
                $('#event_summary_overtime_row').show();
            } else {
                $('#event_summary_overtime_row').hide();
            }

            // Hide booking modal and show summary modal
            $('#bookingModal').modal('hide');
            setTimeout(() => {
                $('#eventBookingSummaryModal').modal('show');
            }, 500);
        });

        // Handle confirm booking button click
        $('#confirmEventBooking').on('click', function() {
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we submit your booking.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Format date properly
            const eventDate = $('#event_summary_event_date').text();
            const formattedDate = new Date(eventDate).toISOString().split('T')[0];

            // Format times properly
            const timeRange = $('#event_summary_time').text().split(' - ');
            const startTime = timeRange[0].trim();
            const endTime = timeRange[1].trim();

            // Get all the values from the summary modal
            const formData = {
                submit_booking: 1,
                ajax: 1,
                customer_name: $('#event_summary_customer_name').text().trim(),
                event_type: $('#event_summary_event_type').text().trim(),
                event_date: formattedDate,
                start_time: startTime,
                end_time: endTime,
                number_of_guests: parseInt($('#event_summary_guests').text()) || 0,
                package_name: $('#event_summary_package').text().trim(),
                package_price: parseFloat($('#event_summary_package_price').text().replace(/[^0-9.-]+/g,"")) || 0,
                payment_method: $('#event_summary_payment_method').text().trim(),
                payment_type: $('#event_summary_payment_type').text().trim(),
                overtime_hours: parseInt($('#event_summary_overtime_hours').text().split(' ')[0]) || 0,
                total_amount: parseFloat($('#event_summary_total_amount').text().replace(/[^0-9.-]+/g,"")) || 0,
                booking_source: 'walk_in'
            };

            // Log the data being sent
            console.log('Sending booking data:', formData);

            // Submit the form data via AJAX
            $.ajax({
                type: 'POST',
                url: 'process_event_booking.php',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#eventBookingSummaryModal').modal('hide');
                    console.log('Server response:', response);
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#DAA520'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Booking Error',
                            html: '<div class="text-left">' +
                                  '<p>Error details:</p>' +
                                  '<pre style="text-align: left; background: #f8f9fa; padding: 10px; margin-top: 10px;">' +
                                  (response.message || 'Unknown error occurred') +
                                  '</pre>' +
                                  '<p>Please try again or contact support if the issue persists.</p>' +
                                  '</div>',
                            icon: 'error',
                            confirmButtonColor: '#DAA520'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#eventBookingSummaryModal').modal('hide');
                    console.error("AJAX Error:", status, error);
                    console.error("Response Text:", xhr.responseText);
                    
                    let errorMessage = 'There was an error submitting your booking.';
                    let technicalDetails = '';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                        if (response.details) {
                            technicalDetails = `\nFile: ${response.details.file}\nLine: ${response.details.line}`;
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        technicalDetails = `\nStatus: ${status}\nError: ${error}\nResponse: ${xhr.responseText}`;
                    }

                    Swal.fire({
                        title: 'Submission Error',
                        html: '<div class="text-left">' +
                              '<p>Technical details:</p>' +
                              '<pre style="text-align: left; background: #f8f9fa; padding: 10px; margin-top: 10px;">' +
                              errorMessage +
                              technicalDetails +
                              '</pre>' +
                              '<p>Please check the following:</p>' +
                              '<ul class="text-left">' +
                              '<li>All required fields are filled</li>' +
                              '<li>Date and time are valid</li>' +
                              '<li>Number of guests is within limits</li>' +
                              '</ul>' +
                              '</div>',
                        icon: 'error',
                        confirmButtonColor: '#DAA520'
                    });
                }
            });
        });

        // Set minimum date for event date
        var today = new Date().toISOString().split('T')[0];
        $('input[name="event_date"]').attr('min', today);

        // Clear the form and reset displays when modal is hidden
        $('#bookingModal').on('hidden.bs.modal', function () {
            $('#bookingForm')[0].reset();
            const packagePrice = parseFloat($('#package_price').val());
            $('#fee_summary_container').addClass('d-none');
            $('#additional_guest_warning').addClass('d-none');
            $('#guest_note').removeClass('d-none');
            $('#display_package_price').text(number_format(packagePrice, 2));
            $('#additional_guest_fee').text('0.00');
            $('#total_amount').text(number_format(packagePrice, 2));
        });
    });
    </script>
</body>
</html>
<?php
// Close database connection
if (isset($con)) {
    $con->close();
}
?>
