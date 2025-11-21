<?php
// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to make a reservation'
    ]);
    exit;
}

// Create table_bookings table if it doesn't exist
$sql_create_table = "CREATE TABLE IF NOT EXISTS table_bookings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    package_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email_address VARCHAR(100),
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    num_guests INT(11) NOT NULL,
    special_requests TEXT,
    payment_method VARCHAR(20),
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    package_type VARCHAR(50),
    payment_reference VARCHAR(100),
    payment_proof VARCHAR(255),
    advance_order TEXT,
    payment_option VARCHAR(20) DEFAULT 'full',
    amount_to_pay DECIMAL(10,2) DEFAULT 0.00,
    cancellation_reason TEXT,
    cancelled_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_query($con, $sql_create_table)) {
    error_log("Error creating table: " . mysqli_error($con));
    die("Error creating table: " . mysqli_error($con));
}

// Handle confirm and archive actions
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    
    if ($_POST['action'] == 'confirm') {
        $update_sql = "UPDATE table_bookings SET status = 'Confirmed' WHERE id = '$booking_id'";
        if (mysqli_query($con, $update_sql)) {
            echo "<script>alert('Booking confirmed successfully!'); window.location.reload();</script>";
        } else {
            echo "<script>alert('Error confirming booking: " . mysqli_error($con) . "');</script>";
        }
    }
}

// Get JSON input for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Log request details
    error_log("Processing table reservation request");
    error_log("Content-Type: " . $_SERVER['CONTENT_TYPE']);
    error_log("POST data: " . print_r($_POST, true));
    
    try {
        // Start transaction
        error_log("Starting database transaction");
        mysqli_begin_transaction($con);
        
        // Sanitize input data
        $user_id = $_SESSION['user_id'];
        $package_name = mysqli_real_escape_string($con, $_POST['packageType']);
        $contact_number = mysqli_real_escape_string($con, $_POST['contactNumber']);
        $email_address = isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : '';
        $booking_date = mysqli_real_escape_string($con, $_POST['reservationDate']);
        $booking_time = mysqli_real_escape_string($con, $_POST['reservationTime']);
        $num_guests = isset($_POST['guestCount']) ? (int)$_POST['guestCount'] : 0;
        $special_requests = isset($_POST['specialRequests']) ? mysqli_real_escape_string($con, $_POST['specialRequests']) : '';
        $customer_name = mysqli_real_escape_string($con, $_POST['customerName']);
        
        // Payment details
        $payment_option = isset($_POST['paymentOption']) ? mysqli_real_escape_string($con, $_POST['paymentOption']) : 'full';
        $payment_method = mysqli_real_escape_string($con, $_POST['paymentMethod']);
        $total_amount = mysqli_real_escape_string($con, $_POST['totalAmount']);
        $amount_to_pay = mysqli_real_escape_string($con, $_POST['amountToPay']);

        // Set payment status based on payment method
        $payment_status = strtolower($payment_method) === 'cash' ? 'Paid' : 'Pending';
        $amount_paid = strtolower($payment_method) === 'cash' ? $total_amount : '0.00';

        // Check for existing reservations
        $check_sql = "SELECT COUNT(*) as count FROM table_bookings 
                     WHERE booking_date = ? AND booking_time = ? 
                     AND status != 'Cancelled'";
        
        $check_stmt = mysqli_prepare($con, $check_sql);
        if (!$check_stmt) {
            throw new Exception("Error preparing check statement: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($check_stmt, "ss", $booking_date, $booking_time);
        if (!mysqli_stmt_execute($check_stmt)) {
            throw new Exception("Error executing check statement: " . mysqli_stmt_error($check_stmt));
        }
        
        $result = mysqli_stmt_get_result($check_stmt);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            throw new Exception("This time slot is already booked. Please select a different time.");
        }

        // Insert booking
        $insert_sql = "INSERT INTO table_bookings (
            user_id, package_name, contact_number, email_address, booking_date, booking_time,
            num_guests, special_requests, payment_method, total_amount, amount_paid,
            change_amount, payment_status, status, package_type, payment_option, amount_to_pay, name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $insert_sql);
        if (!$stmt) {
            throw new Exception("Error preparing insert statement: " . mysqli_error($con));
        }
        
        $status = 'Pending';
        $change_amount = '0.00';
        
        mysqli_stmt_bind_param($stmt, "isssssississssssss",
            $user_id, $package_name, $contact_number, $email_address, $booking_date, $booking_time,
            $num_guests, $special_requests, $payment_method, $total_amount, $amount_paid,
            $change_amount, $payment_status, $status, $package_name, $payment_option, $amount_to_pay, $customer_name
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing insert statement: " . mysqli_stmt_error($stmt));
        }
        
        $booking_id = mysqli_insert_id($con);
        
        // Handle advance order if exists
        if (isset($_POST['advanceOrder'])) {
            $advance_order_json = $_POST['advanceOrder'];
            if (is_string($advance_order_json)) {
                $update_sql = "UPDATE table_bookings SET advance_order = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($con, $update_sql);
                
                if (!$update_stmt) {
                    throw new Exception("Error preparing advance order update: " . mysqli_error($con));
                }
                
                mysqli_stmt_bind_param($update_stmt, "si", $advance_order_json, $booking_id);
                if (!mysqli_stmt_execute($update_stmt)) {
                    throw new Exception("Error saving advance order: " . mysqli_stmt_error($update_stmt));
                }
            }
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Format success response
        $response = [
            'success' => true,
            'message' => 'Reservation successful!',
            'booking_details' => [
                'booking_id' => $booking_id,
                'customer_name' => $customer_name,
                'package_type' => $package_name,
                'guest_count' => $num_guests,
                'date' => date('F j, Y', strtotime($booking_date)),
                'time' => date('g:i A', strtotime($booking_time)),
                'contact_number' => $contact_number,
                'email' => $email_address,
                'special_requests' => $special_requests,
                'payment_method' => $payment_method ? ucfirst($payment_method) : '',
                'payment_option' => ucfirst($payment_option),
                'total_amount' => $total_amount,
                'amount_to_pay' => $amount_to_pay,
                'payment_status' => $payment_status
            ]
        ];
        
        error_log("Sending success response: " . json_encode($response));
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Error in reservation process: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        mysqli_rollback($con);
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
        if (isset($stmt)) mysqli_stmt_close($stmt);
        if (isset($update_stmt)) mysqli_stmt_close($update_stmt);
    }
    
    exit;
}

// Query to get table bookings
$sql = "SELECT tb.*, 
        CASE 
            WHEN tb.name IS NOT NULL AND tb.name != '' THEN tb.name
            ELSE CONCAT(u.first_name, ' ', u.last_name)
        END as customer_name,
        tb.payment_proof,
        tb.payment_reference 
        FROM table_bookings tb 
        LEFT JOIN userss u ON tb.user_id = u.id 
        WHERE tb.status NOT IN ('Cancelled', 'Rejected')
        ORDER BY tb.booking_date DESC, tb.booking_time DESC";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("Error in query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Table Bookings - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* Override header styles to ensure left alignment */
        .header-brand,
        .navbar-brand {
            margin-left: 20px !important;
            padding-left: 0 !important;
        }

        /* Keep all other styles unchanged */
        .table td, .table th {
            padding: 12px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        .btn-warning.confirm-btn {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            padding: 4px 8px;
            font-size: 12px;
        }

        .btn-info.view-btn {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
            padding: 4px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        .btn-danger {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Main container */
        .main {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 20px;
        }

        /* Header and dropdown container */
        .header-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .header-container h2 {
            margin: 0;
        }

        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background-color: #ffc107;
            color: #000;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Table container */
        .table-responsive {
            width: 100%;
            overflow-x: scroll;
            margin-bottom: 20px;
        }

        /* Table styles */
        #reservationsTable {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin-bottom: 1rem;
        }

        /* Badge styles */
        .badge {
            padding: 5px 10px;
            font-weight: normal;
        }

        .badge.bg-warning { background-color: #ffc107; color: #000; }
        .badge.bg-success { background-color: #28a745; color: #fff; }
        .badge.bg-danger { background-color: #dc3545; color: #fff; }
        .badge.bg-info { background-color: #17a2b8; color: #fff; }
        .badge.bg-secondary { background-color: #6c757d; color: #fff; }

        /* Button styles */
        .btn-warning.confirm-btn {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            padding: 4px 8px;
            font-size: 12px;
        }

        .btn-info.view-btn {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
            padding: 4px 8px;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #000;
        }

        .payment-details {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .reference-number {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            border-left: 4px solid #17a2b8;
            margin-bottom: 15px;
        }

        .payment-proof-img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #imageError {
            color: red;
            text-align: center;
            margin-top: 10px;
            display: none;
        }

        /* DataTables wrapper */
        .dataTables_wrapper {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* Add these styles for the search bar */
        .dataTables_filter {
            text-align: right;
            margin-bottom: 1em;
            float: right;
            width: 15%;
            position: relative;
            right: 0;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        /* Remove the custom search we added before */
        .custom-search {
            display: none;
        }

        /* Add these styles for the modal */
        .modal-content {
            border-radius: 8px;
        }
        
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-title {
            color: #333;
            font-weight: 500;
        }
        
        .booking-details p {
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .booking-details strong {
            color: #555;
            width: 150px;
            display: inline-block;
        }

        .modal-lg {
            max-width: 800px;
        }
        
        .booking-details p {
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .booking-details strong {
            color: #555;
            width: 150px;
            display: inline-block;
        }
        
        .modal-header.bg-info {
            background-color: #17a2b8 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }

        .booking-confirm-details {
            margin-top: 20px;
            padding: 15px;
        /* Add these styles for SweetAlert2 customization */
        .swal2-popup {
            font-size: 1rem;
        }
        
        .swal2-title {
            color: #28a745;
            font-size: 1.5rem;
        }
        
        .swal2-html-container {
            font-size: 1.1rem;
        }

        /* Updated status badge styles */
        .status-badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-width: 120px;
            justify-content: center;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-done {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .status-badge i {
            font-size: 0.9rem;
        }

        .payment-proof-preview {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .payment-proof-preview img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .booking-details p {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .booking-details strong {
            color: #495057;
            min-width: 150px;
            display: inline-block;
        }

        .reference-number-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
            margin-top: 10px;
        }

        .payment-proof-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .payment-proof-img {
            max-height: 300px;
            width: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 0 auto;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .booking-details {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .booking-details h6 {
            color: #17a2b8;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }

        .booking-details p {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .booking-details strong {
            color: #495057;
            min-width: 150px;
            display: inline-block;
        }

        /* Add these styles */
        .booking-reject-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #dc3545;
        }

        .booking-reject-details p {
            margin-bottom: 10px;
        }

        .booking-reject-details strong {
            min-width: 100px;
            display: inline-block;
            color: #495057;
        }

        #rejectionReason {
            resize: vertical;
            min-height: 100px;
        }

        .form-group label {
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }

        /* Move the hotel name completely to the left */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #DAA520;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            padding: 0;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-right: 20px;
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

        .header-icons .cart-count {
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
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <div class="col-lg-12">
                <div class="dropdown">
                    <button class="dropbtn">Bookings ▼</button>
                    <div class="dropdown-content">
                        <a href="index.php?booking_status">Room Booking</a>
                        <a href="tableData.php">Table Booking</a>
                        <a href="eventData.php">Event Booking</a>
                    </div>
                </div>
                <h2>Table Bookings</h2>
                
                <table id="reservationsTable" class="table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Package</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>
                                <?php if (isset($row['booking_source']) && $row['booking_source'] == 'frontdesk'): ?>
                                    <span class="badge badge-info">Frontdesk</span>
                                <?php elseif (isset($row['booking_source']) && $row['booking_source'] == 'web'): ?>
                                    <span class="badge badge-primary">Web</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['package_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['email_address'] ?? ''); ?></td>
                            <td><?php echo date('F j, Y', strtotime($row['booking_date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($row['booking_time'])); ?></td>
                            <td><?php echo htmlspecialchars($row['num_guests'] ?? ''); ?></td>
                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><span class="badge bg-info"><?php echo ucfirst($row['payment_method'] ?? ''); ?></span></td>
                            <td>
                                <?php
                                    $statusIcon = '';
                                    $statusClass = '';
                                    switch($row['status']) {
                                        case 'Pending':
                                            $statusClass = 'status-pending';
                                            $statusIcon = 'fa-clock-o';
                                            break;
                                        case 'Confirmed':
                                            $statusClass = 'status-confirmed';
                                            $statusIcon = 'fa-check-circle';
                                            break;
                                        case 'Done':
                                            $statusClass = 'status-done';
                                            $statusIcon = 'fa-flag-checkered';
                                            break;
                                    }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <i class="fa <?php echo $statusIcon; ?>"></i>
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'Pending'): ?>
                                    <button type="button" class="btn btn-sm btn-warning confirm-booking" data-toggle="modal" data-target="#confirmBookingModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-customer="<?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>"
                                        data-package="<?php echo htmlspecialchars($row['package_name'] ?? ''); ?>"
                                        data-datetime="<?php echo date('F j, Y g:i A', strtotime($row['booking_date'] . ' ' . $row['booking_time'])); ?>">
                                        <i class="fa fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger reject-booking" data-toggle="modal" data-target="#rejectBookingModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-customer="<?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>"
                                        data-package="<?php echo htmlspecialchars($row['package_name'] ?? ''); ?>"
                                        data-datetime="<?php echo date('F j, Y g:i A', strtotime($row['booking_date'] . ' ' . $row['booking_time'])); ?>">
                                        <i class="fa fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($row['status'] === 'Confirmed'): ?>
                                    <button type="button" class="btn btn-sm btn-info mark-done" data-id="<?php echo $row['id']; ?>" data-toggle="tooltip" title="Mark as Done">
                                        <i class="fa fa-flag-checkered"></i>
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-info view-booking" data-toggle="modal" data-target="#viewBookingModal"
                                    data-customer="<?php echo htmlspecialchars($row['customer_name'] ?? ''); ?>"
                                    data-package="<?php echo htmlspecialchars($row['package_name'] ?? ''); ?>"
                                    data-contact="<?php echo htmlspecialchars($row['contact_number'] ?? ''); ?>"
                                    data-email="<?php echo htmlspecialchars($row['email_address'] ?? ''); ?>"
                                    data-date="<?php echo date('F j, Y', strtotime($row['booking_date'])); ?>"
                                    data-time="<?php echo date('g:i A', strtotime($row['booking_time'])); ?>"
                                    data-guests="<?php echo htmlspecialchars($row['num_guests'] ?? ''); ?>"
                                    data-amount="₱<?php echo number_format($row['total_amount'], 2); ?>"
                                    data-payment="<?php echo ucfirst($row['payment_method'] ?? ''); ?>"
                                    data-status="<?php echo $row['status']; ?>"
                                    data-reference="<?php echo htmlspecialchars($row['payment_reference'] ?? ''); ?>"
                                    data-proof="<?php echo htmlspecialchars($row['payment_proof'] ?? ''); ?>">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <?php if (!empty($row['payment_proof'])): ?>
                                    <button type="button" class="btn btn-sm btn-primary view-proof" onclick="viewPaymentProof({
                                        reference: '<?php echo htmlspecialchars($row['payment_reference'] ?? 'N/A'); ?>', 
                                        path: 'Admin/aa/uploads/payment_proofs/<?php echo htmlspecialchars($row['payment_proof'] ?? ''); ?>'
                                    })">
                                        <i class="fa fa-file-image-o"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="paymentProofModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Payment Proof</h3>
            <div class="payment-details">
                <div class="reference-number mb-3">
                    Reference Number: <span id="referenceNumber"></span>
                </div>
                <div class="payment-proof-container">
                    <img id="paymentProofImage" class="payment-proof-img" src="" alt="Payment Proof" style="max-width: 100%; height: auto;">
                    <div id="imageError" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the modal HTML -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="bookingModalLabel">Booking Details</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="booking-details">
                                <h6 class="text-info mb-3">Customer Information</h6>
                                <p><strong>Customer Name:</strong> <span id="customerName"></span></p>
                                <p><strong>Contact Number:</strong> <span id="contactNumber"></span></p>
                                <p><strong>Email:</strong> <span id="emailAddress"></span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="booking-details">
                                <h6 class="text-info mb-3">Booking Information</h6>
                                <p><strong>Package:</strong> <span id="packageName"></span></p>
                                <p><strong>Date:</strong> <span id="bookingDate"></span></p>
                                <p><strong>Time:</strong> <span id="bookingTime"></span></p>
                                <p><strong>Number of Guests:</strong> <span id="guestCount"></span></p>
                                <p><strong>Total Amount:</strong> ₱<span id="totalAmountInfo"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="booking-details">
                                <h6 class="text-info mb-3">Payment Details</h6>
                                <p><strong>Amount:</strong> <span id="totalAmountPayment"></span></p>
                                <p><strong>Payment Method:</strong> <span id="paymentMethod"></span></p>
                                <p><strong>Payment Option:</strong> <span id="paymentOption"></span></p>
                                <div class="reference-number-section">
                                    <p><strong>Reference Number:</strong> <span id="referenceNumber"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="booking-details">
                                <h6 class="text-info mb-3">Payment Proof</h6>
                                <div id="paymentProofContainer">
                                    <div class="payment-proof-section">
                                        <img id="proofImage" src="" alt="Payment Proof" class="img-fluid payment-proof-img" style="display: none;">
                                        <div id="proofError" class="alert alert-warning mt-2">
                                            No payment proof available
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before the closing body tag -->
    <div class="modal fade" id="confirmBookingModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to confirm this booking?</p>
                    <div class="booking-confirm-details">
                        <p><strong>Customer:</strong> <span id="confirmCustomerName"></span></p>
                        <p><strong>Package:</strong> <span id="confirmPackage"></span></p>
                        <p><strong>Date & Time:</strong> <span id="confirmDateTime"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmBookingBtn">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the Reject Modal -->
    <div class="modal fade" id="rejectBookingModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Booking</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this booking?</p>
                    <div class="booking-reject-details">
                        <p><strong>Customer:</strong> <span id="rejectCustomerName"></span></p>
                        <p><strong>Package:</strong> <span id="rejectPackage"></span></p>
                        <p><strong>Date & Time:</strong> <span id="rejectDateTime"></span></p>
                    </div>
                    <div class="form-group mt-3">
                        <label for="rejectionReason" class="font-weight-bold">Rejection Reason:</label>
                        <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Please provide a reason for rejecting this booking"></textarea>
                        <div id="reasonError" class="invalid-feedback">
                            Please provide a reason for rejection.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="rejectBookingBtn">Reject Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Add SweetAlert2 JS before your script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#reservationsTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "dom": '<"top"f>rt<"bottom"lip>',
            "columnDefs": [
                {
                    "targets": 9, // Status column index (0-based)
                    "type": "string",
                    "render": function(data, type, row) {
                        if (type === 'sort') {
                            // Custom sort order: Pending -> Confirmed -> Done
                            switch($(data).text().trim()) {
                                case 'Pending': return 1;
                                case 'Confirmed': return 2;
                                case 'Done': return 3;
                                default: return 4;
                            }
                        }
                        return data;
                    }
                }
            ]
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // View booking details
        $(document).on('click', '.view-booking', function() {
            var button = $(this);
            
            // Update basic information
            $('#customerName').text(button.data('customer'));
            $('#packageName').text(button.data('package'));
            $('#contactNumber').text(button.data('contact'));
            $('#emailAddress').text(button.data('email'));
            $('#bookingDate').text(button.data('date'));
            $('#bookingTime').text(button.data('time'));
            $('#guestCount').text(button.data('guests'));
            $('#totalAmountInfo').text(button.data('amount'));
            $('#totalAmountPayment').text(button.data('amount'));
            $('#paymentMethod').text(button.data('payment'));
            $('#paymentOption').text(button.data('paymentoption') || 'Full Payment');
            var paymentMethod = (button.data('payment') || '').toLowerCase();
            var referenceNumber = button.data('reference') || 'N/A';
            var paymentProof = button.data('proof');

            // Reference number and payment proof logic
            if (paymentMethod === 'cash') {
                $('.reference-number-section').hide();
                $('#paymentProofContainer').html('<div class="alert alert-info">No payment proof or reference number required for cash payments.</div>');
            } else {
                $('.reference-number-section').show();
                $('#referenceNumber').text(referenceNumber);
                // Handle payment proof
                var proofImage = $('#proofImage');
                var proofError = $('#proofError');
                if (paymentProof) {
                    proofImage.attr('src', paymentProof).show();
                    proofError.hide();
                } else {
                    proofImage.hide();
                    proofError.show();
                }
            }
        });

        // Handle confirm button click
        $(document).on('click', '.confirm-booking', function() {
            var button = $(this);
            $('#confirmCustomerName').text(button.data('customer'));
            $('#confirmPackage').text(button.data('package'));
            $('#confirmDateTime').text(button.data('datetime'));
            $('#confirmBookingBtn').data('bookingId', button.data('id'));
        });

        // Handle final confirmation
        $('#confirmBookingBtn').click(function() {
            var bookingId = $(this).data('bookingId');
            var btn = $(this);
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Confirming...');
            
            $.ajax({
                url: 'confirm_table_booking.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    action: 'confirm'
                },
                success: function(response) {
                    if (response.success) {
                        $('#confirmBookingModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Booking has been confirmed successfully.',
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: {
                                popup: 'animated fadeInDown'
                            }
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message || 'Error confirming booking'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error confirming booking'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html('Confirm Booking');
                }
            });
        });

        // Handle mark as done
        $(document).on('click', '.mark-done', function() {
            var bookingId = $(this).data('id');
            var btn = $(this);
            
            Swal.fire({
                title: 'Mark as Done?',
                text: 'Are you sure you want to mark this booking as done?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as done'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                    
                    $.ajax({
                        url: 'confirm_table_booking.php',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'mark_done'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Booking has been marked as done.',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message || 'Error updating booking status'
                                });
                                btn.prop('disabled', false).html('<i class="fa fa-flag-checkered"></i>');
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Error updating booking status'
                            });
                            btn.prop('disabled', false).html('<i class="fa fa-flag-checkered"></i>');
                        }
                    });
                }
            });
        });

        // Handle reject button click
        $(document).on('click', '.reject-booking', function() {
            var button = $(this);
            $('#rejectCustomerName').text(button.data('customer'));
            $('#rejectPackage').text(button.data('package'));
            $('#rejectDateTime').text(button.data('datetime'));
            $('#rejectBookingBtn').data('bookingId', button.data('id'));
            // Clear previous reason and error state
            $('#rejectionReason').val('').removeClass('is-invalid');
        });

        // Handle final rejection
        $('#rejectBookingBtn').click(function() {
            var bookingId = $(this).data('bookingId');
            var reason = $('#rejectionReason').val().trim();
            var btn = $(this);
            
            // Validate reason
            if (!reason) {
                $('#rejectionReason').addClass('is-invalid');
                return;
            }
            
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Rejecting...');
            
            $.ajax({
                url: 'reject_table_booking.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    action: 'reject',
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        $('#rejectBookingModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Rejected',
                            text: 'The booking has been rejected successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to reject booking'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reject booking'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html('Reject Booking');
                }
            });
        });

        // Handle view proof button click
        $(document).on('click', '.view-proof', function() {
            var button = $(this);
            var reference = button.data('reference') || 'N/A';
            var proofPath = button.data('proof');
            
            if (proofPath) {
                viewPaymentProof({
                    reference: reference,
                    path: proofPath
                });
            }
        });
    });

    function viewPaymentProof(data) {
        var modal = document.getElementById('paymentProofModal');
        var img = document.getElementById('paymentProofImage');
        var errorDiv = document.getElementById('imageError');
        var referenceSpan = document.getElementById('referenceNumber');
        
        // Reset error message and image
        errorDiv.style.display = 'none';
        img.style.display = 'none';
        
        // Update reference number
        referenceSpan.textContent = data.reference;
        
        // Set image source with the correct path
        img.src = data.path;
        
        img.onload = function() {
            errorDiv.style.display = 'none';
            img.style.display = 'block';
        };
        
        img.onerror = function() {
            errorDiv.textContent = 'Unable to load payment proof image';
            errorDiv.style.display = 'block';
            img.style.display = 'none';
        };
        
        modal.style.display = "block";
    }

    function closeModal() {
        var modal = document.getElementById('paymentProofModal');
        var errorDiv = document.getElementById('imageError');
        modal.style.display = "none";
        errorDiv.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('paymentProofModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html>
    