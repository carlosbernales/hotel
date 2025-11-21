<?php
    // Enable error reporting at the very top
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once 'db.php';
    require_once 'header.php';  // Include the header file
    require_once 'sidebar.php';

    // Start the session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check database connection
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    echo "<!-- Debug: Database connection successful -->";

    // Create amenities table if it doesn't exist
    $create_amenities_table = "CREATE TABLE IF NOT EXISTS amenities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!mysqli_query($con, $create_amenities_table)) {
        die("Error creating amenities table: " . mysqli_error($con));
    }

    // Insert sample amenities if the table is empty
    $check_amenities = mysqli_query($con, "SELECT COUNT(*) as count FROM amenities");
    $row = mysqli_fetch_assoc($check_amenities);
    if ($row['count'] == 0) {
        $sample_amenities = [
            ['Breakfast', 'Delicious breakfast buffet', 250.00],
            ['Airport Transfer', 'One way airport transfer', 500.00],
            ['Spa Access', 'Full day spa access', 800.00],
            ['Laundry Service', 'Same day laundry service', 150.00],
            ['Mini Bar', 'Mini bar restocking', 350.00]
        ];
        
        $stmt = mysqli_prepare($con, "INSERT INTO amenities (name, description, price) VALUES (?, ?, ?)");
        foreach ($sample_amenities as $amenity) {
            mysqli_stmt_bind_param($stmt, 'ssd', $amenity[0], $amenity[1], $amenity[2]);
            mysqli_stmt_execute($stmt);
        }
    }

    // Debug database structure
    $check_bookings_structure = mysqli_query($con, "DESCRIBE bookings");
    if ($check_bookings_structure) {
        error_log("Bookings table structure:");
        while ($field = mysqli_fetch_assoc($check_bookings_structure)) {
            error_log("Field: " . $field['Field'] . " Type: " . $field['Type']);
        }
    } else {
        error_log("Error checking bookings table structure: " . mysqli_error($con));
    }

    $check_rooms_structure = mysqli_query($con, "SHOW COLUMNS FROM rooms");
    if ($check_rooms_structure) {
        echo "<!-- Rooms table columns: -->";
        while ($column = mysqli_fetch_assoc($check_rooms_structure)) {
            echo "<!-- Column: " . $column['Field'] . " -->";
        }
    } else {
        echo "<!-- Error checking rooms table: " . mysqli_error($con) . " -->";
    }

    $check_room_types_structure = mysqli_query($con, "SHOW COLUMNS FROM room_types");
    if ($check_room_types_structure) {
        echo "<!-- Room Types table columns: -->";
        while ($column = mysqli_fetch_assoc($check_room_types_structure)) {
            echo "<!-- Column: " . $column['Field'] . " -->";
        }
    } else {
        echo "<!-- Error checking room_types table: " . mysqli_error($con) . " -->";
    }

    // Check event_bookings table structure
    $check_columns = mysqli_query($con, "SHOW COLUMNS FROM event_bookings");
    if (!$check_columns) {
        // Table doesn't exist, create it
        $create_table_sql = "CREATE TABLE IF NOT EXISTS event_bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            booking_date DATE NOT NULL,  -- Changed from event_date to booking_date
            event_type VARCHAR(100) NOT NULL,
            num_guests INT NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            amount_paid DECIMAL(10,2),
            payment_status VARCHAR(20) DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!mysqli_query($con, $create_table_sql)) {
            die("Error creating event_bookings table: " . mysqli_error($con));
        }
    }

    // Add a test query
    $test_query = mysqli_query($con, "SELECT DATABASE()");
    if ($test_query) {
        $db_name = mysqli_fetch_row($test_query)[0];
        echo "<!-- Debug: Connected to database: " . $db_name . " -->";
    } else {
        die("Could not execute test query: " . mysqli_error($con));
    }

    // Handle early check-out action
    if (isset($_POST['action']) && $_POST['action'] == 'early_checkout' && isset($_POST['booking_id'])) {
        $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
        
        // Start transaction
        $con->begin_transaction();
        
        try {
            // Get the room number from the booking
            $get_room_sql = "SELECT room_number FROM bookings WHERE booking_id = ?";
            $stmt = $con->prepare($get_room_sql);
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();
            $room_number = $booking['room_number'];
            
            // Update booking status
            $update_sql = "UPDATE bookings SET status = 'Checked Out' WHERE booking_id = ?";
            $stmt = $con->prepare($update_sql);
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();
            
            // Update room status to 'active' in room_numbers table
            if (!empty($room_number)) {
                $update_room_sql = "UPDATE room_numbers SET status = 'active' WHERE room_number = ?";
                $stmt = $con->prepare($update_room_sql);
                $stmt->bind_param("s", $room_number);
                $stmt->execute();
            }
            
            // Commit transaction
            $con->commit();
            
            echo "<script>
                alert('Early check-out successful!');
                window.location.reload();
            </script>";
        } catch (Exception $e) {
            // Rollback transaction on error
            $con->rollback();
            echo "<script>alert('Error processing early check-out: " . $e->getMessage() . "');</script>";
        }
    }

    // Handle archive action
    if (isset($_POST['action']) && $_POST['action'] == 'archive' && isset($_POST['booking_id'])) {
        $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
        
        // Start transaction
        $con->begin_transaction();
        
        try {
            // Get the room number from the booking
            $get_room_sql = "SELECT room_number FROM bookings WHERE booking_id = ?";
            $stmt = $con->prepare($get_room_sql);
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();
            $room_number = $booking['room_number'];
            
            // Update booking status to Archived
            $update_sql = "UPDATE bookings SET status = 'Archived' WHERE booking_id = ?";
            $stmt = $con->prepare($update_sql);
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();
            
            // Update room status to 'Available' in room_numbers table if room number exists
            if (!empty($room_number)) {
                $update_room_sql = "UPDATE room_numbers SET status = 'Available' WHERE room_number = ?";
                $stmt = $con->prepare($update_room_sql);
                $stmt->bind_param("s", $room_number);
                $stmt->execute();
            }
            
            // Commit transaction
            $con->commit();
            
            echo "<script>
                alert('Booking archived successfully!');
                window.location.href = 'archived.php';
            </script>";
        } catch (Exception $e) {
            // Rollback transaction on error
            $con->rollback();
            echo "<script>alert('Error archiving booking: " . $e->getMessage() . "');</script>";
        }
    }

    // Query to get checked-in bookings with room and payment details
    $room_sql = "SELECT 
        b.booking_id,
        b.booking_reference,
        CONCAT(b.first_name, ' ', b.last_name) as guest_name,
        b.email,
        b.contact,
        b.check_in,
        b.check_out,
        (b.num_adults + IFNULL(b.num_children, 0)) as number_of_guests,
        rt.room_type,
        b.room_type_id,
        b.total_amount as room_price,
        b.total_amount,
        b.payment_option,
        b.downpayment_amount,
        b.payment_method,
        '' as discount_type,
        0 as discount_amount,
        b.status,
        DATEDIFF(b.check_out, b.check_in) as nights,
        'room' as booking_type,
        b.created_at,
        '' as payment_proof,
        CASE
            WHEN b.payment_option = 'Partial Payment' OR b.payment_option = 'Custom Payment' OR b.payment_option = 'downpayment' 
            THEN COALESCE(b.downpayment_amount, 0.00)
            WHEN b.payment_option = 'Full Payment' OR b.payment_option = 'Full' 
            THEN COALESCE(b.total_amount, 0.00)
            ELSE 0.00
        END as amount_paid,
        CASE
            WHEN b.payment_option = 'Partial Payment' OR b.payment_option = 'Custom Payment' OR b.payment_option = 'downpayment' 
            THEN COALESCE(b.downpayment_amount, 0.00)
            WHEN b.payment_option = 'Full Payment' OR b.payment_option = 'Full' 
            THEN COALESCE(b.total_amount, 0.00)
            ELSE 0.00
        END as amount_paid_display
    FROM bookings b
    LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
    WHERE b.status IN ('Checked in', 'Extended')
    ORDER BY b.check_in DESC";

    // Debug: Print the query
    error_log("SQL Query: " . $room_sql);

    $room_result = mysqli_query($con, $room_sql);
    if (!$room_result) {
        error_log("Error in room query: " . mysqli_error($con));
        die("Error in room query: " . mysqli_error($con));
    }

    // Debug: Output the number of rows returned
    $num_rows = mysqli_num_rows($room_result);
    error_log("Number of rows returned: " . $num_rows);

    // Debug: Output the first row structure to help identify issues
    if ($num_rows > 0) {
        $first_row = mysqli_fetch_assoc($room_result);
        error_log("First row data: " . print_r($first_row, true));
        mysqli_data_seek($room_result, 0);
    }

    echo "<!-- Debug: Room query successful -->";

    $table_sql = "SELECT 
        tr.*,
        'Table Booking' as booking_type,
        DATEDIFF(tr.reservation_datetime, tr.reservation_datetime) as nights
    FROM table_reservations tr 
    WHERE tr.status = 'confirmed' 
    ORDER BY tr.reservation_datetime DESC";

    $table_result = mysqli_query($con, $table_sql);
    if (!$table_result) {
        die("Error in table query: " . mysqli_error($con));
    }
    echo "<!-- Debug: Table query successful -->";

    // Debug: Check table structure
    $check_columns = mysqli_query($con, "SHOW COLUMNS FROM event_bookings");
    if (!$check_columns) {
        die("Error: Table event_bookings does not exist. " . mysqli_error($con));
    }

    echo "<!-- Debug: Table columns: -->\n";
    $columns = [];
    while ($row = mysqli_fetch_assoc($check_columns)) {
        $columns[] = $row['Field'];
        echo "<!-- Column: " . $row['Field'] . " -->\n";
    }

    // Debug: Print all columns
    echo "<!-- Available columns: " . implode(", ", $columns) . " -->\n";

    // Modify query based on existing columns - using correct column name
    $event_sql = "SELECT * FROM event_bookings WHERE booking_status = 'Confirmed' ORDER BY event_date ASC";

    $event_result = mysqli_query($con, $event_sql);
    if (!$event_result) {
        die("Error in event query: " . mysqli_error($con) . "\nQuery: " . $event_sql);
    }
    echo "<!-- Debug: Event query successful -->";

    // Handle actions
    if (isset($_POST['action']) && isset($_POST['booking_id']) && isset($_POST['booking_type'])) {
        $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
        $action = mysqli_real_escape_string($con, $_POST['action']);
        $booking_type = mysqli_real_escape_string($con, $_POST['booking_type']);
        
        if ($action === 'early_checkout') {
            switch ($booking_type) {
                case 'room':
                    $update_sql = "UPDATE bookings SET status = 'Checked Out' WHERE id = '$booking_id'";
                    break;
                case 'table':
                    $update_sql = "UPDATE table_bookings SET status = 'Checked Out' WHERE id = '$booking_id'";
                    break;
                case 'event':
                    $update_sql = "UPDATE event_bookings SET payment_status = 'Checked Out' WHERE id = '$booking_id'";
                    break;
            }
            
            if (isset($update_sql)) {
                if (!mysqli_query($con, $update_sql)) {
                    die("Error updating status: " . mysqli_error($con));
                }
                echo "<script>window.location.reload();</script>";
                exit();
            }
        }
    }
    ?>

    <!-- Add DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">

    <style>
        /* Add DataTables specific styles */
        .main {
            padding-top: 10px !important;
            margin-top: 0 !important;
        }

        .page-header {
            margin: 0 0 15px !important;
            padding: 0 !important;
            border: none !important;
        }

        .panel {
            margin-bottom: 15px !important;
        }

        .panel-body {
            padding: 15px !important;
        }

        .container-fluid {
            padding: 0 !important;
            margin-top: 0 !important;
        }

        .container-fluid h2 {
            margin: 0 0 15px !important;
            padding: 0 !important;
        }

        .table-responsive {
            padding: 15px !important;
            margin-top: 0 !important;
            margin-bottom: 15px !important;
        }

        /* Fix table header and scrollbar positioning */
        .dataTables_wrapper {
            width: 100%;
            margin: 0 !important;
            display: flex;
            flex-direction: column;
        }

        /* Move search and length menu to top */
        .dataTables_length, .dataTables_filter {
            margin-bottom: 10px !important;
        }

        /* Keep header fixed and content scrollable */
        .dataTables_scroll {
            width: 100%;
            margin-top: 5px !important;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .dataTables_scrollHead {
            background: white;
            border-bottom: 2px solid #dee2e6;
        }

        .dataTables_scrollBody {
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
        }

        /* Table styling */
        table.dataTable {
            margin: 0 !important;
            width: 100% !important;
            min-width: 1200px;
        }

        table.dataTable thead th {
            background: white;
            padding: 12px 8px;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            white-space: nowrap;
        }

        table.dataTable tbody td {
            padding: 8px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        /* Custom scrollbar styling */
        .dataTables_scrollBody::-webkit-scrollbar {
            height: 10px;
            width: 8px;
            background-color: #f5f5f5;
        }

        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background-color: #daa520;
            border-radius: 6px;
            border: 2px solid #f5f5f5;
        }

        .dataTables_scrollBody::-webkit-scrollbar-track {
            background-color: #f5f5f5;
            border-radius: 6px;
        }

        /* Ensure action buttons stay visible */
        .action-buttons {
            white-space: nowrap;
            min-width: max-content;
            display: flex;
            gap: 2px;
        }

        .action-buttons .btn {
            padding: 3px 6px;
            font-size: 12px;
            line-height: 1;
            border-radius: 3px;
        }

        .action-buttons .btn i {
            font-size: 11px;
            margin: 0;
        }

        /* Add hover effect for better UX */
        .action-buttons .btn:hover {
            transform: translateY(-1px);
            transition: transform 0.2s;
        }

        /* Style for Add Amenities button */
        .btn-purple {
            background-color: #9c27b0;
            color: white;
            border: 1px solid #8e24aa;
        }
        
        .btn-purple:hover {
            background-color: #8e24aa;
            color: white;
        }
        
        /* Ensure tooltips are visible */
        .tooltip {
            z-index: 1070;
        }

        /* Add these styles for horizontal scrolling */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
            padding-bottom: 15px;
        }

        .table {
            min-width: 1200px; /* Ensure table has minimum width to show all columns */
            width: 100%;
        }

        /* Custom scrollbar styling */
        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #daa520;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #b38a1d;
        }
    </style>

    <head>
        <!-- Add SweetAlert2 CSS and JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Checked In Bookings</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!-- Room Bookings -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                
                                    </div>
                                    
                                            <div class="table-container">
                                                <table id="bookingsTable" class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                        
                                                            <th>Name</th>
                                                            <th>Check Out</th>
                                                            <th>Nights</th>
                                                            <th>Room Type</th>
                                                            <th>Room Number</th>
                                                            <th>Room Price</th>
                                                            <th>Total Amount</th>
                                                            <th>Payment Option</th>
                                                            <th>Amount Paid</th>
                                                            

                                                            
                                                            <th>Extra Charges</th>
                              
                                <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (mysqli_num_rows($room_result) > 0): ?>
                                                            <?php while ($row = mysqli_fetch_assoc($room_result)): ?>
                                                                <!-- Debug output -->
                                                                <?php
                                                                error_log("Checked In Debug - Booking ID: " . ($row['booking_id'] ?? 'N/A') . ", Payment Option: " . ($row['payment_option'] ?? 'N/A') . ", Downpayment Amount: " . ($row['downpayment_amount'] ?? 'N/A') . ", Amount Paid (DB): " . ($row['amount_paid'] ?? 'N/A') . ", Amount Paid Display: " . ($row['amount_paid_display'] ?? 'N/A'));
                                                                ?>
                                                                <tr>
                                                                
                                                                    <td><?php echo htmlspecialchars((string)($row['guest_name'] ?? '')); ?></td>
                                                                
                                                                    <td><?php echo date('M j, Y', strtotime($row['check_out'])); ?></td>
                                                                
                                                                    <td><?php echo $row['nights']; ?></td>
                                                                    <td><?php echo htmlspecialchars((string)($row['room_type'] ?? '')); ?></td>
                                                                    <td><?php echo htmlspecialchars((string)($row['room_number'] ?? 'Not Assigned')); ?></td>
                                                                    <td>₱<?php echo number_format($row['room_price'] ?? 0, 2); ?></td>
                                                                    <td>₱<?php echo number_format($row['total_amount'] ?? 0, 2); ?></td>
                                                                    <td><?php echo ucfirst((string)($row['payment_option'] ?? '')); ?></td>
                                                                    <td>₱<?php echo number_format($row['amount_paid_display'] ?? 0, 2); ?></td>
                                                                    
                                                                <?php
                                                                // Safely get extra_charges with a default of 0 if not set
                                                                $extraCharges = $row['extra_charges'] ?? 0;
                                                                error_log("Debug extra_charges: " . var_export($extraCharges, true) . " (Type: " . gettype($extraCharges) . ")");
                                                                ?>
                                                                    <td class="text-center">
                                                                        ₱<?php echo number_format(floatval($extraCharges), 2); ?>
                                                                    </td>
                                                                   
                                                                    <td class="action-buttons">
                                                                        <!-- Extend Stay Button -->
                                                                        <button class="btn btn-info btn-sm extend-stay-btn"
                                                                                data-toggle="tooltip"
                                                                                title="Extend Stay"
                                                                                data-id="<?php echo htmlspecialchars((string)($row['booking_id'] ?? '')); ?>"
                                                                                data-guest="<?php echo htmlspecialchars((string)($row['guest_name'] ?? '')); ?>"
                                                                                data-checkout="<?php echo htmlspecialchars((string)($row['check_out'] ?? '')); ?>"
                                                                                data-room-type="<?php echo htmlspecialchars((string)($row['room_type'] ?? '')); ?>"
                                                                                data-room-price="<?php echo htmlspecialchars((string)($row['room_price'] ?? '0')); ?>"
                                                                                data-amount-paid="<?php echo htmlspecialchars((string)($row['amount_paid'] ?? '0')); ?>"
                                                                                data-remaining-balance="<?php echo htmlspecialchars((string)($row['remaining_balance'] ?? '0')); ?>"
                                                                                data-checkin="<?php echo htmlspecialchars((string)($row['check_in'] ?? '')); ?>"
                                                                                data-payment-option="<?php echo htmlspecialchars((string)($row['payment_option'] ?? '')); ?>"
                                                                                data-downpayment-amount="<?php echo htmlspecialchars((string)($row['downpayment_amount'] ?? '0')); ?>">
                                                                            <i class="fas fa-calendar-plus"></i>
                                                                        </button>

                                                                        <!-- Regular Check-out Button -->
                                                                        <button class="btn btn-success btn-sm checkout-btn"
                                                                                data-toggle="tooltip"
                                                                                title="Process Check Out"
                                                                                data-id="<?php echo htmlspecialchars((string)($row['booking_id'] ?? '')); ?>"
                                                                                data-guest="<?php echo htmlspecialchars((string)($row['guest_name'] ?? '')); ?>"
                                                                                data-amount="<?php echo htmlspecialchars((string)($row['total_amount'] ?? '0')); ?>"
                                                                                data-paid="<?php echo htmlspecialchars((string)($row['amount_paid'] ?? '0')); ?>"
                                                                                data-remaining-balance="<?php echo htmlspecialchars((string)($row['remaining_balance'] ?? '0')); ?>"
                                                                                data-payment-option="<?php echo htmlspecialchars((string)($row['payment_option'] ?? '')); ?>"
                                                                                data-downpayment-amount="<?php echo htmlspecialchars((string)($row['downpayment_amount'] ?? '0')); ?>">
                                                                            <i class="fas fa-door-open"></i>
                                                                        </button>

                                                                        <!-- Early Check-out Button -->
                                                                        <button class="btn btn-warning btn-sm early-checkout-btn"
                                                                                data-toggle="tooltip"
                                                                                title="Early Check Out"
                                                                                data-id="<?php echo htmlspecialchars((string)($row['booking_id'] ?? '')); ?>"
                                                                                data-guest="<?php echo htmlspecialchars((string)($row['guest_name'] ?? '')); ?>"
                                                                                data-checkin="<?php echo htmlspecialchars((string)($row['check_in'] ?? '')); ?>"
                                                                                data-checkout="<?php echo htmlspecialchars((string)($row['check_out'] ?? '')); ?>"
                                                                                data-room-price="<?php echo htmlspecialchars((string)($row['room_price'] ?? '0')); ?>"
                                                                                data-total-amount="<?php echo htmlspecialchars((string)($row['total_amount'] ?? '0')); ?>"
                                                                                data-paid="<?php echo htmlspecialchars((string)($row['amount_paid'] ?? '0')); ?>"
                                                                                data-amount-paid="<?php echo htmlspecialchars((string)($row['amount_paid'] ?? '0')); ?>"
                                                                                data-extra-charges="<?php echo htmlspecialchars((string)($row['extra_charges'] ?? '0')); ?>"
                                                                                data-remaining-balance="<?php echo htmlspecialchars((string)($row['remaining_balance'] ?? '0')); ?>"
                                                                                data-payment-option="<?php echo htmlspecialchars((string)($row['payment_option'] ?? '')); ?>"
                                                                                data-nights="<?php echo htmlspecialchars((string)($row['nights'] ?? '0')); ?>">
                                                                            <i class="fas fa-clock"></i>
                                                                        </button>

                                                                        <!-- Transfer Room Button -->
                                                                        <button class="btn btn-primary btn-sm transfer-room-btn"
                                                                                data-toggle="tooltip"
                                                                                title="Transfer Room"
                                                                                data-id="<?php echo htmlspecialchars((string)($row['booking_id'] ?? '')); ?>"
                                                                                data-guest="<?php echo htmlspecialchars((string)($row['guest_name'] ?? '')); ?>"
                                                                                data-current-room="<?php echo htmlspecialchars((string)($row['room_type'] ?? '')); ?>"
                                                                                data-current-room-number="<?php echo htmlspecialchars((string)($row['room_number'] ?? 'Not Assigned')); ?>"
                                                                                data-checkin="<?php echo htmlspecialchars((string)($row['check_in'] ?? '')); ?>"
                                                                                data-checkout="<?php echo htmlspecialchars((string)($row['check_out'] ?? '')); ?>"
                                                                                data-downpayment-amount="<?php echo htmlspecialchars((string)($row['downpayment_amount'] ?? '0')); ?>"
                                                                                data-amount-paid="<?php echo htmlspecialchars((string)($row['amount_paid'] ?? '0')); ?>"
                                                                                data-remaining-balance="<?php echo htmlspecialchars((string)($row['remaining_balance'] ?? '0')); ?>"
                                                                                data-nights="<?php echo htmlspecialchars((string)($row['nights'] ?? '0')); ?>">
                                                                            <i class="fas fa-exchange-alt"></i>
                                                                        </button>

                                                                        <!-- Add Amenities Button -->
                                                                        <button class="btn btn-purple btn-sm add-amenities-btn"
                                                                                data-toggle="tooltip"
                                                                                title="Add Amenities"
                                                                                data-id="<?php echo htmlspecialchars($row['booking_id']); ?>"
                                                                                data-guest="<?php echo htmlspecialchars($row['guest_name']); ?>">
                                                                            <i class="fas fa-concierge-bell"></i>
                                                                        </button>

                                                                        <!-- View Details Button -->
                                                                        <button class="btn btn-info btn-sm view-details"
                                                                                data-toggle="tooltip"
                                                                                title="View Details"
                                                                                data-id="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="16" class="text-center">No checked-in bookings found</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Early Check Out Modal -->
    <div class="modal fade" id="earlyCheckoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Early Check Out</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="earlyCheckoutForm">
                    <div class="modal-body">
                        <input type="hidden" id="earlyCheckoutBookingId" name="booking_id">
                        
                        <div class="form-group">
                            <label>Guest Name:</label>
                            <input type="text" class="form-control" id="earlyCheckoutGuestName" readonly>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Check Out Date:</label>
                                    <input type="text" class="form-control" id="originalCheckOutDate" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>New Check Out Date (Early):</label>
                                    <input type="text" class="form-control" id="newCheckOutDate" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Number of Nights:</label>
                                    <input type="text" class="form-control" id="originalNights" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Actual Nights Stayed:</label>
                                    <input type="text" class="form-control" id="actualNights" readonly>
                                </div>
                            </div>
                        </div>
                            
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Original Total Amount:</label>
                                    <input type="text" class="form-control" id="originalAmount" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>New Total Amount:</label>
                                    <input type="text" class="form-control" id="newAmount" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount Already Paid:</label>
                                    <input type="text" class="form-control" id="earlyCheckoutPaidAmount" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> According to our policy, for early check-out with downpayment option, the total amount due is 50% of the original booking amount.
                        </div>

                        <div id="remainingBalanceRow" class="row mt-3" style="display: none;">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Remaining Balance to Pay:</label>
                                    <input type="text" class="form-control" id="remainingBalance" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3" id="earlyCheckoutPaymentSection" style="display: none;">
                            <label>Payment Method for Remaining Balance:</label>
                            <select class="form-control" id="earlyCheckoutPaymentMethod" name="payment_method">
                                <option value="">Select Payment Method</option>
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                                <option value="Maya">Maya</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm Early Check Out</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Room Modal -->
    <div class="modal fade" id="transferRoomModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Room</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="transferRoomForm">
                    <div class="modal-body">
                        <input type="hidden" id="transferBookingId" name="booking_id">
                        <input type="hidden" id="currentRoomId" name="current_room_number">
                        <input type="hidden" id="currentRoomPrice" name="current_room_price">
                        <input type="hidden" id="numberOfNights" name="number_of_nights">
                        <input type="hidden" id="amountPaid" name="amount_paid">
                        
                        <div class="form-group">
                            <label>Guest Name</label>
                            <input type="text" class="form-control" id="transferGuestName" readonly>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="mb-2">Current Room Information</h6>
                            <div class="form-group mb-2">
                                <label>Room Type</label>
                                <input type="text" class="form-control" id="currentRoom" readonly>
                            </div>
                            <div class="form-group mb-0">
                                <label>Room Rate per Night</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₱</span>
                                    </div>
                                    <input type="text" class="form-control" id="displayCurrentPrice" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount Paid</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="displayAmountPaid" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Remaining Balance</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="text" class="form-control" id="displayRemainingBalance" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Check-in Date</label>
                                    <input type="date" class="form-control" id="checkInDate" name="check_in_date" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Check-out Date</label>
                                    <input type="date" class="form-control" id="checkOutDate" name="check_out_date">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Number of Nights</label>
                            <input type="text" class="form-control" id="displayNights" readonly>
                        </div>

                        <div class="form-group">
                            <label>Transfer Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="transferReason" name="transfer_reason" 
                                    rows="3" required></textarea>
                            <small class="text-muted">Please provide a reason for the room transfer</small>
                        </div>

                        <div class="form-group">
                            <label>Select New Room Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="newRoomSelect" name="new_room_number" required>
                                <option value="">Select New Room Number</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Room Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="roomNumberSelect" name="room_number" required>
                                <option value="">Select a room number...</option>
                            </select>
                        </div>

                        <!-- Price Adjustment Details -->
                        <div id="priceChangeWarning" style="display: none;">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Price Adjustment Details</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1">Current Rate: ₱<span id="currentRateDisplay">0.00</span></p>
                                            <p class="mb-1">New Rate: ₱<span id="newRateDisplay">0.00</span></p>
                                            <p class="mb-1">Number of Nights: <span id="nightsDisplay">0</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1">Amount Already Paid: ₱<span id="amountPaidDisplay">0.00</span></p>
                                            <p class="mb-1">Final Amount: ₱<span id="finalAmountDisplay">0.00</span></p>
                                            <div id="paymentNote"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Section -->
                        <div id="paymentSection" style="display: none;">
                            <div class="form-group">
                                <label>Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" id="paymentMethod" name="payment_method">
                                    <option value="">Select payment method...</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="GCash">GCash</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Process Check Out</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="checkoutForm">
                    <div class="modal-body">
                        <input type="hidden" id="checkoutBookingId" name="booking_id">
                        
                        <div class="form-group">
                            <label>Guest Name:</label>
                            <input type="text" class="form-control" id="checkoutGuestName" readonly>
                        </div>

                        <div class="form-group">
                            <label>Total Amount:</label>
                            <input type="text" class="form-control" id="checkoutTotalAmount" readonly>
                        </div>

                        <div class="form-group">
                            <label>Amount Already Paid:</label>
                            <input type="text" class="form-control" id="checkoutPaidAmount" readonly>
                        </div>

                        <div class="form-group">
                            <label>Remaining Balance:</label>
                            <input type="text" class="form-control" id="checkoutRemainingBalance" readonly>
                        </div>

                        <div id="remainingPaymentSection">
                            <div class="form-group">
                                <label>Payment Method for Remaining Balance:</label>
                                <select class="form-control" id="checkoutPaymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Maya">Maya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Check Out</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Booking Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <!-- Guest Information Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Guest Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Name</label>
                                        <div class="h6" id="viewGuestName"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Email</label>
                                        <div class="h6" id="viewEmail"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Contact</label>
                                        <div class="h6" id="viewContact"></div>
                                    </div>
                                    <div class="info-group">
                                        <label class="text-muted mb-1">Number of Guests</label>
                                        <div class="h6" id="viewNumGuests"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Booking Information Section -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Booking Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Check In</label>
                                        <div class="h6" id="viewCheckIn"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Check Out</label>
                                        <div class="h6" id="viewCheckOut"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Nights</label>
                                        <div class="h6" id="viewNights"></div>
                                    </div>
                                    <div class="info-group">
                                        <label class="text-muted mb-1">Room Type</label>
                                        <div class="h6" id="viewRoomType"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Section -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Room Price</label>
                                        <div class="h6" id="viewRoomPrice"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Total Amount</label>
                                        <div class="h6" id="viewTotalAmount"></div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1">Payment Option</label>
                                        <div class="h6" id="viewPaymentOption"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group mb-3">
                                        <label class="text-muted mb-1"></label>
                                        <div class="h6" id="viewRemainingBalance"></div>
                                    </div>
                                    <div class="info-group">
                                        <label class="text-muted mb-1">Discount Type</label>
                                        <div class="h6" id="viewDiscountType"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Extend Stay Modal -->
    <div class="modal fade" id="extendStayModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Extend Stay</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="extendStayForm">
                    <div class="modal-body">
                        <input type="hidden" id="extendBookingId" name="booking_id">
                        <input type="hidden" id="currentRoomPrice" name="room_price">
                        <input type="hidden" id="currentCheckOut" name="current_checkout">
                        
                        <div class="form-group">
                            <label>Guest Name:</label>
                            <input type="text" class="form-control" id="extendGuestName" readonly>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Room Type:</label>
                                    <input type="text" class="form-control" id="extendRoomType" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Room Price per Night:</label>
                                    <input type="text" class="form-control" id="extendRoomPrice" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount Paid:</label>
                                    <input type="text" class="form-control" id="extendAmountPaid" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remaining Balance:</label>
                                    <input type="text" class="form-control" id="extendRemainingBalance" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>New Check-out Date:</label>
                                    <input type="date" class="form-control" id="newCheckoutDate" name="new_checkout" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Additional Nights:</label>
                                    <input type="number" class="form-control" id="additionalNights" name="additional_nights" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Additional Amount:</label>
                                    <input type="text" class="form-control" id="additionalAmount" name="additional_amount" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Option:</label>
                                    <select class="form-control" id="paymentOption" name="payment_option" required>
                                        <option value="later">Pay Upon Check-out</option>
                                        <option value="now">Pay Now</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="paymentMethodSection" class="row mt-3" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Method:</label>
                                    <select class="form-control" id="paymentMethod" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="extendStayBtn">Extend Stay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add DataTables JS before your existing scripts -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable with scrolling
        var table = $('#bookingsTable').DataTable({
            "pageLength": 10,
            "scrollX": true,
            "scrollCollapse": true,
            "columnDefs": [
                {
                    "targets": -1,
                    "orderable": false
                }
            ],
            "dom": '<"top"lf>rt<"bottom"ip><"clear">',
            "language": {
                "search": "Search bookings:",
                "lengthMenu": "Show _MENU_ bookings per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ bookings",
                "infoEmpty": "No bookings available",
                "infoFiltered": "(filtered from _MAX_ total bookings)"
            },
            "drawCallback": function() {
                // Reinitialize tooltips after each table draw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Use event delegation for all action buttons
        $('body')
            // Extend Stay Button
            .on('click', '.extend-stay-btn', function() {
                const bookingId = $(this).data('id');
                const guestName = $(this).data('guest');
                const currentCheckOut = $(this).data('checkout');
                const roomType = $(this).data('room-type');
                const roomPrice = parseFloat($(this).data('room-price'));
                const amountPaidFromData = parseCurrencyValue($(this).data('amount-paid'));
                const remainingBalance = parseCurrencyValue($(this).data('remaining-balance'));
                const paymentOption = $(this).data('payment-option'); // Get payment option
                const downpaymentAmount = parseCurrencyValue($(this).data('downpayment-amount') || 0); // Get downpayment amount

                // Determine actual amount paid for display in Extend Stay modal
                let displayAmountForExtendModal;
                if (paymentOption === 'Partial Payment') {
                    displayAmountForExtendModal = 1500;
                } else if (paymentOption === 'Custom Payment') {
                    displayAmountForExtendModal = downpaymentAmount;
                } else if (paymentOption === 'Full' || paymentOption === 'Full Payment') {
                    displayAmountForExtendModal = amountPaidFromData;
                } else {
                    displayAmountForExtendModal = amountPaidFromData; // Default to actual amount if no specific rule
                }

                // Format the current check-out date for the date input
                const formattedCheckOut = new Date(currentCheckOut).toISOString().split('T')[0];
                
                // Set minimum date for new check-out
                const minDate = new Date(currentCheckOut);
                minDate.setDate(minDate.getDate() + 1);
                
                // Populate the modal fields
                $('#extendBookingId').val(bookingId);
                $('#extendGuestName').val(guestName);
                $('#extendRoomType').val(roomType);
                $('#extendRoomPrice').val('₱' + roomPrice.toFixed(2));
                $('#extendAmountPaid').val('₱' + displayAmountForExtendModal.toFixed(2));
                $('#extendRemainingBalance').val('₱' + remainingBalance.toFixed(2));
                $('#currentCheckOut').val(formattedCheckOut);
                $('#currentRoomPrice').val(roomPrice);
                $('#newCheckoutDate').attr('min', minDate.toISOString().split('T')[0]);
                
                // Clear previous values
                $('#newCheckoutDate').val('');
                $('#additionalNights').val('');
                $('#additionalAmount').val('');
                $('#paymentMethod').val('');
                $('#paymentOption').val('later');
                
                // Show the modal
                $('#extendStayModal').modal('show');
            })
            // Checkout Button
            .on('click', '.checkout-btn', function(e) {
                e.preventDefault();
                const bookingId = $(this).data('id');
                const guestName = $(this).data('guest');
                const totalAmount = parseFloat($(this).data('amount'));
                const extraCharges = parseFloat($(this).data('extra-charges') || 0);
                const remainingBalance = parseFloat($(this).data('remaining-balance'));
                const paymentOption = $(this).data('payment-option');
                const downpaymentAmount = parseFloat($(this).data('downpayment-amount') || 0);
                const amountPaid = parseFloat($(this).data('amount-paid')) || 0;

                // Determine amount paid based on payment option
                // Determine amount paid based on payment option
                if (paymentOption === 'Partial Payment') {
                    actualAmountPaid = 1500; // Fixed amount for partial payment
                } else if (paymentOption === 'Custom Payment') {
                    actualAmountPaid = downpaymentAmount; // Use downpayment_amount for custom payment
                } else if (paymentOption === 'Full' || paymentOption === 'Full Payment') {
                    actualAmountPaid = amountPaid; // Use amount_paid for full payment
                } else {
                    actualAmountPaid = 0; // Default to 0 for unknown payment options
                }

                // Populate the modal fields
                $('#checkoutBookingId').val(bookingId);
                $('#checkoutGuestName').val(guestName);
                $('#checkoutTotalAmount').val('₱' + (totalAmount + extraCharges).toFixed(2));
                $('#checkoutPaidAmount').val('₱' + actualAmountPaid.toFixed(2));
                $('#checkoutRemainingBalance').val('₱' + remainingBalance.toFixed(2));
                
                // Show/hide remaining payment section based on balance
                if (remainingBalance > 0) {
                    $('#remainingPaymentSection').show();
                    $('#checkoutPaymentMethod').prop('required', true);
                } else {
                    $('#remainingPaymentSection').hide();
                    $('#checkoutPaymentMethod').prop('required', false);
                }

                // Show the modal
                $('#checkoutModal').modal('show');
            })
            // View Details Button
            .on('click', '.view-details', function() {
                const $btn = $(this);
                const bookingId = $btn.data('id');
                
                // Show loading state
                $('#viewDetailsModal .modal-body').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Loading booking details...</p></div>');
                
                // Fetch booking details via AJAX
                $.ajax({
                    url: 'get_booking_details.php',
                    type: 'GET',
                    data: { booking_id: bookingId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            const booking = response.data;
                            
                            // Format dates
                            const checkIn = new Date(booking.check_in).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric' 
                            });
                            
                            const checkOut = new Date(booking.check_out).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric' 
                            });
                            
                            // Calculate nights stayed
                            const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
                            const nightsStayed = Math.round(Math.abs((new Date(booking.check_out) - new Date(booking.check_in)) / oneDay));
                            
                            // Format amounts
                            const formatCurrency = (amount) => {
                                return '₱' + parseFloat(amount || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            };
                            
                            // Build the HTML content
                            let html = `
                                <!-- Guest Information Section -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Guest Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Name</label>
                                                    <div class="h6">${booking.guest_name || 'N/A'}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Email</label>
                                                    <div class="h6">${booking.email || 'N/A'}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Contact</label>
                                                    <div class="h6">${booking.contact || 'N/A'}</div>
                                                </div>
                                                <div class="info-group">
                                                    <label class="text-muted mb-1">Number of Guests</label>
                                                    <div class="h6">${booking.number_of_guests || '0'}</div>
                                                    <small class="text-muted">${booking.adults || '0'} Adults, ${booking.children || '0'} Children</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Booking Information Section -->
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Booking Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Check In</label>
                                                    <div class="h6">${checkIn}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Check Out</label>
                                                    <div class="h6">${checkOut}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Nights</label>
                                                    <div class="h6">${nightsStayed}</div>
                                                </div>
                                                <div class="info-group">
                                                    <label class="text-muted mb-1">Room Type</label>
                                                    <div class="h6">${booking.room_type || 'N/A'}</div>
                                                    <small class="text-muted">${booking.room_number ? 'Room Number: ' + booking.room_number : 'Room not assigned yet'}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Room Details Section -->
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-bed me-2"></i>Room Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Room Price per Night</label>
                                                    <div class="h6">${formatCurrency(booking.room_price)}</div>
                                                </div>
                                                <div class="info-group">
                                                    <label class="text-muted mb-1">Bed Type</label>
                                                    <div class="h6">${booking.bed_type || 'N/A'}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group">
                                                    <label class="text-muted mb-1">Room Description</label>
                                                    <div class="h6">${booking.room_description || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Information Section -->
                                <div class="card mt-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Total Amount</label>
                                                    <div class="h6">${formatCurrency(booking.total_amount)}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Remaining Balance</label>
                                                    <div class="h6">${formatCurrency(booking.remaining_balance)}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Payment Method</label>
                                                    <div class="h6">${booking.payment_method ? booking.payment_method.charAt(0).toUpperCase() + booking.payment_method.slice(1).replace('_', ' ') : 'N/A'}</div>
                                                </div>
                                                <div class="info-group mb-3">
                                                    <label class="text-muted mb-1">Discount Type</label>
                                                    <div class="h6">${booking.discount_type || 'N/A'}</div>
                                                </div>
                                                <div class="info-group">
                                                    <label class="text-muted mb-1">Extra Charges</label>
                                                    <div class="h6">${formatCurrency(booking.extra_charges)}</div>
                                                </div>
                                            </div>
                                        </div>
                                        ${booking.special_requests ? `
                                        <div class="info-group mt-3">
                                            <label class="text-muted mb-1">Special Requests</label>
                                            <div class="h6">${booking.special_requests}</div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                            
                            // Update modal content
                            $('#viewDetailsModal .modal-body').html(html);
                            
                            // Show the modal
                            $('#viewDetailsModal').modal('show');
                        } else {
                            // Show error message
                            $('#viewDetailsModal .modal-body').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    ${response.message || 'Failed to load booking details. Please try again.'}
                                </div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            `);
                            $('#viewDetailsModal').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching booking details:', error);
                        $('#viewDetailsModal .modal-body').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                An error occurred while fetching booking details. Please try again later.
                            </div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        `);
                        $('#viewDetailsModal').modal('show');
                    }
                });
            })
            // Print Receipt Button
            .on('click', '.print-receipt-btn', function() {
                const bookingId = $(this).data('id');
                // Your print receipt logic here
                console.log('Print receipt for booking ID:', bookingId);
            })
            // Transfer Room Button
            .on('click', '.transfer-room-btn', function() {
                const $btn = $(this);
                const bookingId = $btn.data('id');
                const guestName = $btn.data('guest');
                const currentRoom = $btn.data('current-room');
                const currentRoomId = $btn.data('current-roomid');
                const currentRoomPrice = parseFloat($btn.data('current-price'));
                const amountPaid = parseFloat($btn.data('amount-paid')) || 0;
                const remainingBalance = parseFloat($btn.data('remaining-balance')) || 0;
                const checkInDate = $btn.data('checkin');
                const checkOutDate = $btn.data('checkout');
                const nights = parseInt($btn.data('nights')) || 0;
                
                // Reset form and sections
                $('#transferRoomForm').trigger('reset');
                $('#priceChangeWarning').hide();
                $('#paymentSection').hide();
                $('#cashPaymentSection').hide();

                // Set the values in the modal
                $('#transferBookingId').val(bookingId);
                $('#transferGuestName').val(guestName);
                $('#currentRoom').val(currentRoom);
                $('#currentRoomId').val(currentRoomId);
                $('#currentRoomPrice').val(currentRoomPrice);
                $('#numberOfNights').val(nights);
                $('#displayCurrentPrice').val(currentRoomPrice.toFixed(2));
                $('#displayAmountPaid').val('₱' + amountPaid.toFixed(2));
                $('#displayRemainingBalance').val('₱' + remainingBalance.toFixed(2));
                $('#amountPaid').val(amountPaid);
                
                // Format dates for the date inputs (YYYY-MM-DD format)
                const formattedCheckIn = new Date(checkInDate).toISOString().split('T')[0];
                const formattedCheckOut = new Date(checkOutDate).toISOString().split('T')[0];
                
                // Set dates and nights
                $('#checkInDate').val(formattedCheckIn);
                $('#checkOutDate').val(formattedCheckOut);
                $('#displayNights').val(nights);
                
                // Show loading state in dropdown
                $('#newRoomSelect').html('<option value="">Loading available rooms...</option>');

                // Show the modal
                $('#transferRoomModal').modal('show');

                // Fetch available rooms
                $.ajax({
                    url: 'get_available_rooms.php',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        current_room_id: currentRoomId
                    },
                    success: function(response) {
                        $('#newRoomSelect').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading rooms:', error);
                        $('#newRoomSelect').html('<option value="">Error loading rooms</option>');
                    }
                });
            });

        // Adjust table columns on window resize
        $(window).on('resize', function() {
            table.columns.adjust();
        });

        // Handle room transfer form submission
        $('#transferRoomForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'process_room_transfer.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Refresh the page after successful transfer
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Room transfer completed successfully. Your booking has been updated.',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Room transfer was successful. Your booking has been updated.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Transfer Room Button click handler - This handler is now the primary one.
        $('body').on('click', '.transfer-room-btn', function() {
            const $btn = $(this);
            const bookingId = $btn.data('id');
            const guestName = $btn.data('guest');
            const currentRoomType = $btn.data('current-room');
            const currentRoomId = $btn.data('current-roomid'); // Correct variable name
            const currentRoomPrice = parseFloat($btn.data('current-price'));
            const amountPaid = parseCurrencyValue($btn.data('amount-paid') || 0);
            const remainingBalance = parseCurrencyValue($btn.data('remaining-balance') || 0);
            const checkInDate = $btn.data('checkin');
            const checkOutDate = $btn.data('checkout');
            const nights = parseInt($btn.data('nights')) || 0;
            const paymentOption = $btn.data('payment-option'); // Get payment option
            const downpaymentAmount = parseCurrencyValue($btn.data('downpayment-amount') || 0); // Get downpayment amount

            // Determine actual amount paid for display in THIS modal (Transfer Room)
            let displayAmountForTransferModal;
            if (paymentOption === 'Partial Payment') {
                displayAmountForTransferModal = 1500;
            } else if (paymentOption === 'Custom Payment') {
                displayAmountForTransferModal = downpaymentAmount;
            } else if (paymentOption === 'Full' || paymentOption === 'Full Payment') {
                displayAmountForTransferModal = amountPaidFromData;
            } else {
                displayAmountForTransferModal = amountPaidFromData; // Default to actual amount if no specific rule
            }

            // Reset form and sections
            $('#transferRoomForm').trigger('reset');
            $('#priceChangeWarning').hide();
            $('#paymentSection').hide();
            $('#cashPaymentSection').hide();

            // Set the values in the modal
            $('#transferBookingId').val(bookingId);
            $('#transferGuestName').val(guestName);
            $('#currentRoom').val(currentRoomType);
            $('#currentRoomId').val(currentRoomId);
            $('#currentRoomPrice').val(currentRoomPrice);
            $('#numberOfNights').val(nights);
            $('#displayCurrentPrice').val(currentRoomPrice.toFixed(2));
            // Apply the new displayAmountForTransferModal here
            $('#displayAmountPaid').val('₱' + displayAmountForTransferModal.toFixed(2));
            $('#displayRemainingBalance').val('₱' + remainingBalance.toFixed(2));
            // The hidden amountPaid input should still store the raw amountPaidFromData for calculations later, if needed
            $('#amountPaid').val(amountPaidFromData);
            
            // Format dates for the date inputs (YYYY-MM-DD format)
            const formattedCheckIn = new Date(checkInDate).toISOString().split('T')[0];
            const formattedCheckOut = new Date(checkOutDate).toISOString().split('T')[0];
            
            // Set dates and nights
            $('#checkInDate').val(formattedCheckIn);
            $('#checkOutDate').val(formattedCheckOut);
            $('#displayNights').val(nights);
            
            // Show loading state in dropdown
            $('#newRoomSelect').html('<option value="">Loading available rooms...</option>');

            // Show the modal
            $('#transferRoomModal').modal('show');

            // Fetch available rooms using currentRoomId consistently
            $.ajax({
                url: 'get_transfer_rooms.php',
                type: 'GET',
                dataType: 'html',
                data: {
                    current_room_id: currentRoomId
                },
                success: function(response) {
                    $('#newRoomSelect').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading rooms:', error);
                    $('#newRoomSelect').html('<option value="">Error loading rooms</option>');
                }
            });
        });

        // Function to view event details
        function viewEventDetails(eventId) {
            $.ajax({
                url: 'get_event_details.php',
                type: 'POST',
                data: { event_id: eventId },
                success: function(response) {
                    $('#eventDetailsModal .modal-content').html(response);
                    $('#eventDetailsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Error loading event details: ' + error);
                }
            });
        }

        // Function to view room details
        function viewRoomDetails(bookingId) {
            $.ajax({
                url: 'get_room_details.php',
                type: 'POST',
                data: { booking_id: bookingId },
                success: function(response) {
                    $('#roomDetailsModal .modal-content').html(response);
                    $('#roomDetailsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Error loading room details: ' + error);
                }
            });
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Handle Add Amenities button click
        $(document).on('click', '.add-amenities-btn', function() {
            const bookingId = $(this).data('id');
            const guestName = $(this).data('guest');
            
            // Show amenities selection modal with static amenities
            Swal.fire({
                title: 'Add Amenities',
                html: `
                    <div class="text-left">
                        <h5>Select amenities for ${guestName}</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="bathRobesAmenity" data-amenity-id="9" data-price="0.00">
                            <label class="form-check-label" for="bathRobesAmenity">
                                <i class="fas fa-bath me-2"></i>Bath Robes (Free)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="bedAmenity" data-amenity-id="8" data-price="1000.00">
                            <label class="form-check-label" for="bedAmenity">
                                <i class="fas fa-bed me-2"></i>Extra Bed (₱1,000.00)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="slippersAmenity" data-amenity-id="10" data-price="0.00">
                            <label class="form-check-label" for="slippersAmenity">
                                <i class="fas fa-shoe-prints me-2"></i>Slippers (Free)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="soapAmenity" data-amenity-id="7" data-price="0.00">
                            <label class="form-check-label" for="soapAmenity">
                                <i class="fas fa-soap me-2"></i>Soap (Free)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="toothbrushAmenity" data-amenity-id="11" data-price="0.00">
                            <label class="form-check-label" for="toothbrushAmenity">
                                <i class="fas fa-toothbrush me-2"></i>Toothbrush (Free)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="toothpasteAmenity" data-amenity-id="12" data-price="0.00">
                            <label class="form-check-label" for="toothpasteAmenity">
                                <i class="fas fa-tooth me-2"></i>Toothpaste (Free)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="towelAmenity" data-amenity-id="13" data-price="0.00">
                            <label class="form-check-label" for="towelAmenity">
                                <i class="fas fa-bath me-2"></i>Extra Towel (Free)
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Selected Amenities',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const selectedAmenities = [];
                    const checkboxes = Swal.getHtmlContainer().querySelectorAll('input[type="checkbox"]:checked');
                    checkboxes.forEach(checkbox => {
                        const amenityId = parseInt(checkbox.dataset.amenityId);
                        const price = parseFloat(checkbox.dataset.price); // Re-added price collection
                        if (!isNaN(amenityId)) {
                            selectedAmenities.push({
                                id: amenityId,
                                price: price // Re-added price to payload
                            });
                        }
                    });
                    if (selectedAmenities.length === 0) {
                        Swal.showValidationMessage('Please select at least one amenity');
                        return false;
                    }
                    return selectedAmenities;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value.length > 0) {
                    // Show loading state
                    Swal.fire({
                        title: 'Adding Amenities',
                        html: 'Please wait while we process your request...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Add each selected amenity one by one
                    const addAmenityPromises = result.value.map(amenity => {
                        console.log('Adding amenity:', { bookingId, amenity }); // Re-added console log
                        return fetch('add_amenities_to_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                booking_id: bookingId,
                                amenity_id: amenity.id,
                                price: amenity.price // Re-added price to payload
                            }),
                            credentials: 'same-origin'
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            console.error('Error adding amenity:', error);
                            throw error;
                        });
                    });

                    // Process all amenity additions
                    Promise.all(addAmenityPromises)
                        .then(results => { // Re-added results to access individual responses
                            // Get the last result to update the UI (since bed is the only one that affects price)
                            const lastResult = results[results.length - 1]; // Re-added last result logic
                            if (lastResult.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Amenities Added',
                                    text: 'The selected amenities have been added and extra charges applied!', // Re-added text
                                    showConfirmButton: true,
                                    allowOutsideClick: false
                                }).then((result) => {
                                    location.reload();
                                });
                            } else {
                                let errorMessage = 'Failed to add some amenities. '; // Re-added error message logic
                                if (lastResult.message) {
                                    errorMessage += lastResult.message;
                                    if (lastResult.debug) {
                                        console.error('Server error details:', lastResult.debug);
                                    }
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: errorMessage.replace(/\n/g, '<br>'), // Re-added html formatting
                                    showConfirmButton: true
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error adding amenities:', error); // Re-added console error
                            let errorMessage = 'An error occurred while adding amenities. Please try again.'; // Re-added error message logic

                            if (error.message) {
                                errorMessage = error.message;
                                try {
                                    const errorData = JSON.parse(error.message.replace(/^[^{]*/,''));
                                    if (errorData.message) {
                                        errorMessage = errorData.message;
                                    }
                                } catch (e) {
                                    // If we can't parse the error as JSON, use the raw message
                                    errorMessage = error.message;
                                }
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMessage.replace(/\n/g, '<br>'), // Re-added html formatting
                                showConfirmButton: true
                            });
                        });
                }
            });
        });

        // Helper function to parse currency values safely
        function parseCurrencyValue(value) {
            if (value === null || value === undefined) return 0;
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                // Remove currency symbol, commas, and any other non-numeric characters except decimal point and negative sign
                const numStr = value.toString().replace(/[^0-9.-]/g, "");
                return parseFloat(numStr) || 0;
            }
            return 0;
        }

        // Format currency with thousand separators and 2 decimal places
        function formatCurrency(amount) {
            if (amount === null || amount === undefined) return '₱0.00';
            const num = typeof amount === 'string' ? parseFloat(amount.toString().replace(/[^0-9.-]/g, "")) : amount;
            return '₱' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Regular Check-out Button Handler
        $(document).on('click', '.checkout-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const bookingId = $btn.data('id');
            const guestName = $btn.data('guest');
            
            // Get all values directly from data attributes
            const totalAmount = parseCurrencyValue($btn.data('amount'));
            const extraCharges = parseCurrencyValue($btn.data('extra-charges') || 0);
            const paymentOption = $btn.data('payment-option');
            const downpaymentAmount = parseFloat($btn.data('downpayment-amount') || 0);
            const amountPaid = parseFloat($btn.data('amount-paid')) || 0;
            const remainingBalanceFromData = parseCurrencyValue($btn.data('remaining-balance') || 0);

            // Determine actual amount paid based on payment option
            let actualAmountPaid;
            if (paymentOption === 'Partial Payment') {
                actualAmountPaid = 1500; // Fixed amount for partial payment
            } else if (paymentOption === 'Custom Payment') {
                actualAmountPaid = downpaymentAmount; // Use downpayment_amount for custom payment
            } else if (paymentOption === 'Full' || paymentOption === 'Full Payment') {
                actualAmountPaid = amountPaid; // Use amount_paid for full payment
            } else {
                actualAmountPaid = 0; // Default to 0 for unknown payment options
            }
            
            // Calculate the remaining balance including extra charges
            // Use the remaining balance from data attribute if it exists, otherwise calculate it
            const remainingBalance = remainingBalanceFromData > 0 ? remainingBalanceFromData : (totalAmount + extraCharges - amountPaid);
            const totalWithExtras = totalAmount + extraCharges;
            
            // Debug log with all values
            console.log('Checkout values:', {
                totalAmount: totalAmount,
                extraCharges: extraCharges,
                amountPaid: amountPaid,
                remainingBalanceFromData: remainingBalanceFromData,
                calculatedRemainingBalance: (totalAmount + extraCharges - amountPaid),
                finalRemainingBalance: remainingBalance,
                calculation: `${totalAmount} + ${extraCharges} - ${amountPaid} = ${remainingBalance}`
            });
            
            // Populate the checkout modal
            $('#checkoutBookingId').val(bookingId);
            $('#checkoutGuestName').val(guestName);
            $('#checkoutTotalAmount').val(formatCurrency(totalWithExtras));
            $('#checkoutPaidAmount').val(formatCurrency(actualAmountPaid));
            $('#checkoutRemainingBalance').val(formatCurrency(remainingBalance));
            
            // Show/hide payment method section based on remaining balance
            if (remainingBalance > 0) {
                $('#remainingPaymentSection').show();
                $('#checkoutPaymentMethod').prop('required', true);
            } else {
                $('#remainingPaymentSection').hide();
                $('#checkoutPaymentMethod').prop('required', false).val('');
            }
            
            // Show the checkout modal
            $('#checkoutModal').modal('show');
        });

        // Handle checkout form submission
        $('#checkoutForm').on('submit', function(e) {
            e.preventDefault();
            
            const bookingId = $('#checkoutBookingId').val();
            const guestName = $('#checkoutGuestName').val();
            const totalWithExtras = parseCurrencyValue($('#checkoutTotalAmount').val());
            const amountPaid = parseCurrencyValue($('#checkoutPaidAmount').val());
            const remainingBalance = parseCurrencyValue($('#checkoutRemainingBalance').val());
            const paymentMethod = $('#checkoutPaymentMethod').val();
            
            // Calculate the amount to pay (remaining balance)
            const amountToPay = Math.max(0, remainingBalance);
            
            console.log('Checkout submission:', {
                totalWithExtras,
                amountPaid,
                remainingBalance,
                amountToPay,
                paymentMethod
            });

            // Validate payment method if there's remaining balance
            if (remainingBalance > 0 && !paymentMethod) {
                Swal.fire({
                    title: 'Payment Method Required',
                    text: 'Please select a payment method for the remaining balance.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Confirm Check Out',
                html: `
                    <div class="text-center">
                        <p>Are you sure you want to check out:</p>
                        <strong>${guestName}</strong>?
                        ${remainingBalance > 0 ? `<p class="mt-2">Remaining Balance: ₱${remainingBalance.toFixed(2)}</p>
                        <p>Payment Method: ${paymentMethod}</p>` : ''}
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Check Out',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                    // Process checkout with full payment amount
                    $.ajax({
                        url: 'update_booking_status.php',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'checkout',
                            payment_method: paymentMethod,
                            amount_paid: amountToPay,
                            remaining_balance: remainingBalance,
                            total_amount: totalWithExtras,
                            total_paid: amountPaid + amountToPay
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Checkout response:', response);
                            
                            if (response.success) {
                                $('#checkoutModal').modal('hide');
                                Swal.fire({
                                    title: 'Check-out Successful!',
                                    html: `
                                        <div class="text-center">
                                            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                                            <p class="mt-3">${guestName} has been checked out.</p>
                                            ${remainingBalance > 0 ? `<p class="mt-2">Final Payment: ₱${remainingBalance.toFixed(2)}</p>
                                            <p>Payment Method: ${paymentMethod}</p>` : ''}
                                            <div class="mt-2">
                                                <small>Thank you for staying at Casa Estela!</small>
                                            </div>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    window.location.href = response.redirect || 'checked_out.php';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to process check-out',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                                submitBtn.prop('disabled', false).text('Confirm Check Out');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            
                            // Try to parse error message from response
                            let errorMessage = 'Failed to process check-out';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                errorMessage = response.message || errorMessage;
                                console.log('Error details:', response);
                            } catch (e) {
                                console.error('Failed to parse error response:', e);
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                            submitBtn.prop('disabled', false).text('Confirm Check Out');
                        }
                    });
                }
            });
        });

        // Early Check-out Button Handler - Delegated event handler for dynamic elements
        $(document).on('click', '.early-checkout-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const bookingId = $btn.data('id');
            const guestName = $btn.data('guest');
            const checkIn = new Date($btn.data('checkin'));
            const checkOut = new Date($btn.data('checkout'));
            const roomPrice = parseCurrencyValue($btn.data('room-price') || 0);
            const totalAmount = parseCurrencyValue($btn.data('total-amount') || 0);
            const extraCharges = parseCurrencyValue($btn.data('extra-charges') || 0);
            const amountPaid = parseCurrencyValue($btn.data('paid') || 0);
            const remainingBalanceFromData = parseCurrencyValue($btn.data('remaining-balance') || 0);
            
            // Calculate amount per night
            const nights = parseInt($btn.data('nights') || 0);
            const amountPerNight = nights > 0 ? totalAmount / nights : 0;
            const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
            
            // Set the current date as the new checkout date
            const today = new Date();
            const formattedToday = today.toISOString().split('T')[0];
            
            // Calculate actual nights stayed
            const actualNights = Math.ceil((today - checkIn) / oneDay);
            
            // Calculate new amount (50% of original booking amount + full extra charges)
            const newBookingAmount = totalAmount * 0.5;
            const newTotalAmount = newBookingAmount + extraCharges;
            
            // Calculate remaining balance including extra charges
            const remainingBalance = remainingBalanceFromData > 0 
                ? remainingBalanceFromData 
                : Math.max(0, newTotalAmount - amountPaid);
            
            // Debug log with all values
            console.log('Early Checkout values:', {
                totalAmount: totalAmount,
                extraCharges: extraCharges,
                amountPaid: amountPaid,
                remainingBalanceFromData: remainingBalanceFromData,
                newBookingAmount: newBookingAmount,
                newTotalAmount: newTotalAmount,
                calculatedRemainingBalance: (newTotalAmount - amountPaid),
                finalRemainingBalance: remainingBalance,
                calculation: `(${newBookingAmount} + ${extraCharges}) - ${amountPaid} = ${remainingBalance}`
            });
            
            // Format dates for display
            const formatDate = (date) => {
                if (!(date instanceof Date) || isNaN(date)) return '';
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            // Populate the early checkout modal
            $('#earlyCheckoutBookingId').val(bookingId);
            $('#earlyCheckoutGuestName').val(guestName);
            $('#originalCheckOutDate').val(formatDate(checkOut));
            $('#newCheckOutDate').val(formattedToday);
            $('#originalNights').val(nights);
            $('#actualNights').val(actualNights);
            $('#originalAmount').val(formatCurrency(totalAmount));
            $('#newAmount').val(formatCurrency(newTotalAmount));
            $('#earlyCheckoutPaidAmount').val(formatCurrency(actualAmountPaid));
            
            // Show/hide payment method section based on remaining balance
            if (remainingBalance > 0) {
                $('#remainingBalanceRow').show();
                $('#remainingBalance').val(formatCurrency(remainingBalance));
                $('#earlyCheckoutPaymentSection').show();
                $('#earlyCheckoutPaymentMethod').prop('required', true);
            } else {
                $('#remainingBalanceRow').hide();
                $('#earlyCheckoutPaymentSection').hide();
                $('#earlyCheckoutPaymentMethod').prop('required', false).val('');
            }

            // Show the modal
            $('#earlyCheckoutModal').modal('show');
        });

        // Update the early checkout form submission handler
        $('#earlyCheckoutForm').on('submit', function(e) {
            e.preventDefault();
            
            const bookingId = $('#earlyCheckoutBookingId').val();
            const newAmount = parseFloat($('#newAmount').val().replace('₱', '').replace(/,/g, ''));
            const actualNights = parseInt($('#actualNights').val());
            const newCheckoutDate = $('#newCheckOutDate').val();

            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Early Check-out',
                html: `
                    <div class="text-left">
                        <p>Please confirm the early check-out with the following details:</p>
                        <ul>
                            <li>New Check-out Date: ${newCheckoutDate}</li>
                            <li>Total Amount Due: ₱${newAmount.toFixed(2)}</li>
                            <li>Actual Nights Stayed: ${actualNights}</li>
                        </ul>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm Check-out',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable submit button and show loading state
                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                    // Send AJAX request
                    $.ajax({
                        url: 'update_booking_status.php',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'early_checkout',
                            new_amount: newAmount,
                            actual_nights: actualNights,
                            new_checkout_date: newCheckoutDate
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            console.log('Sending early checkout request with data:', {
                                booking_id: bookingId,
                                action: 'early_checkout',
                                new_amount: newAmount,
                                actual_nights: actualNights,
                                new_checkout_date: newCheckoutDate
                            });
                        },
                        success: function(response) {
                            console.log('Early checkout response:', response);
                            
                            if (response.success) {
                                Swal.fire({
                                    title: 'Early Check-out Successful!',
                                    html: `
                                        <div class="text-center">
                                            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                                            <p class="mt-3">Guest has been checked out early.</p>
                                            <div class="mt-2">
                                                <small>New check-out date: ${newCheckoutDate}</small>
                                            </div>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                }).then((result) => {
                                    window.location.href = response.redirect || 'checked_out.php';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to process early check-out',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                                submitBtn.prop('disabled', false).text('Confirm Early Check Out');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response text:', xhr.responseText);
                            
                            // Try to parse error response
                            let errorMessage = 'Failed to process early check-out';
                            try {
                                const errorResponse = JSON.parse(xhr.responseText);
                                console.log('Parsed error response:', errorResponse);
                                if (errorResponse.message) {
                                    errorMessage = errorResponse.message;
                                }
                            } catch (e) {
                                console.error('Could not parse error response:', e);
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                            submitBtn.prop('disabled', false).text('Confirm Early Check Out');
                        }
                    });
                }
            });
        });

        // Handle print receipt button
        $('.btn-print').on('click', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const bookingType = $(this).data('type');
            window.open(`print_receipt.php?id=${bookingId}&type=${bookingType}`, '_blank');
        });

        // View Details Button Handler
        $(document).on('click', '.view-details', function() {
            const row = $(this).closest('tr');
            const cells = row.find('td');

            console.log('Row data:', {
                name: cells.eq(1).text().trim(),
                email: cells.eq(2).text().trim(),
                contact: cells.eq(3).text().trim(),
                checkIn: cells.eq(4).text().trim()
            });

            // Guest Information
            const fullName = cells.eq(1).text().trim();
            const email = cells.eq(2).text().trim();
            const contact = cells.eq(3).text().trim();
            const checkIn = cells.eq(4).text().trim();
            const checkOut = cells.eq(5).text().trim();
            const nights = cells.eq(6).text().trim();
            const roomType = cells.eq(7).text().trim();
            const roomPrice = cells.eq(8).text().trim();
            const totalAmount = cells.eq(9).text().trim();
            const paymentOption = cells.eq(10).text().trim();
            const amountPaid = cells.eq(11).text().trim();
            const discountType = cells.eq(12).text().trim();
            const remainingBalance = cells.eq(13).text().trim();

            // Populate Guest Information
            $('#viewGuestName').text(fullName);
            $('#viewEmail').text(email);
            $('#viewContact').text(contact);
            $('#viewNumGuests').text(cells.eq(8).text().trim());

            // Populate Booking Information
            $('#viewCheckIn').text(checkIn);
            $('#viewCheckOut').text(checkOut);
            $('#viewNights').text(nights);
            $('#viewRoomType').text(roomType);

            // Populate Payment Information
            $('#viewRoomPrice').text(roomPrice);
            $('#viewTotalAmount').text(totalAmount);
            $('#viewPaymentOption').text(paymentOption);
            $('#viewRemainingBalance').text(remainingBalance);
            $('#viewDiscountType').text(discountType);

            // Show the modal
            $('#viewDetailsModal').modal('show');
        });

        // Handle archive button
        $('.btn-archive').on('click', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const bookingType = $(this).data('type');

            if (confirm('Are you sure you want to archive this booking?')) {
                $.ajax({
                    url: 'archive_booking.php',
                    type: 'POST',
                    data: {
                        booking_id: bookingId,
                        booking_type: bookingType,
                        action: 'archive'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Booking archived successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to archive booking'));
                        }
                    },
                    error: function() {
                        alert('Error archiving booking');
                    }
                });
            }
        });

        // Handle new room selection change
        $('#newRoomSelect').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const newPrice = parseFloat(selectedOption.data('price'));
                const currentPrice = parseFloat($('#currentRoomPrice').val());
                const currentRoomId = $('#currentRoomId').val();
                const selectedRoomId = selectedOption.val();
                const nights = parseInt($('#numberOfNights').val());
                // Get amount paid from the form field
                const displayAmountPaid = parseCurrencyValue($('#amountPaid').val());
                
                // Allow same room type transfer
                if (currentRoomId === selectedRoomId) {
                    // No validation needed, allowing same room type transfer
                }

                const priceDifference = (newPrice - currentPrice) * nights;
                
                // Update price comparison display
                $('#currentRateDisplay').text('₱' + currentPrice.toFixed(2));
                $('#newRateDisplay').text('₱' + newPrice.toFixed(2));
                $('#nightsDisplay').text(nights);
                $('#amountPaidDisplay').text('₱' + displayAmountPaid.toFixed(2));
                
                if (priceDifference > 0) {
                    // New room is more expensive - additional payment required
                    $('#finalAmountDisplay').text('₱' + priceDifference.toFixed(2));
                    $('#paymentNote').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Additional payment of ₱${priceDifference.toFixed(2)} is required</strong>
                        </div>
                    `);
                    $('#paymentSection').show();
                    $('#paymentMethod').prop('required', true);
                    $('#priceChangeWarning').show();
                } else if (priceDifference < 0) {
                    // New room is cheaper - no refund policy
                    $('#finalAmountDisplay').text('₱' + Math.abs(priceDifference).toFixed(2));
                    $('#paymentNote').html(`
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Price difference: ₱${Math.abs(priceDifference).toFixed(2)}</strong><br>
                            <small class="text-danger">Note: As per our policy, the price difference is non-refundable.</small>
                        </div>
                    `);
                    $('#paymentSection').hide();
                    $('#paymentMethod').prop('required', false);
                    $('#priceChangeWarning').show();
                } else {
                    // Same price
                    $('#priceChangeWarning').hide();
                    $('#paymentSection').hide();
                    $('#paymentMethod').prop('required', false);
                }
            } else {
                $('#priceChangeWarning').hide();
                $('#paymentSection').hide();
                $('#paymentMethod').prop('required', false);
            }
        });

        // Handle transfer room form submission
        $('#transferRoomForm').on('submit', function(e) {
            e.preventDefault();
            
            const selectedRoom = $('#newRoomSelect').find('option:selected');
            const transferReason = $('#transferReason').val();
            
            if (!selectedRoom.val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Selection Required',
                    text: 'Please select a room to transfer to'
                });
                return false;
            }

            if (!transferReason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Transfer Reason Required',
                    text: 'Please provide a reason for the transfer'
                });
                return false;
            }

            const newPrice = parseFloat(selectedRoom.data('price'));
            const currentPrice = parseFloat($('#currentRoomPrice').val());
            const nights = parseInt($('#numberOfNights').val());
            const priceDifference = (newPrice - currentPrice) * nights;
            
            // Validate payment if additional payment is required
            if (priceDifference > 0) {
                const paymentMethod = $('#paymentMethod').val();
                if (!paymentMethod) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Method Required',
                        text: 'Please select a payment method for the additional charge'
                    });
                    return false;
                }
            }

            // Get form data
            const formData = {
                booking_id: $('#transferBookingId').val(),
                new_room_id: selectedRoom.val(),
                current_room_id: $('#currentRoomId').val(),
                room_number: $('#roomNumberSelect').val(),
                transfer_reason: transferReason,
                price_difference: priceDifference,
                payment_method: priceDifference > 0 ? $('#paymentMethod').val() : null
            };

            // Show loading state
            Swal.fire({
                title: 'Processing Transfer',
                text: 'Please wait...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            $.ajax({
                url: 'process_room_transfer.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Room Transfer Successful',
                        text: 'The room has been transferred successfully.',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
    if (result.isConfirmed) {
        location.reload();
    }
});
                },
                error: function(xhr, status, error) {
                    console.error('Transfer error:', error);
                    Swal.fire({
                        icon: 'success',
                        title: 'Room Transfer Successful',
                        text: 'The room has been transferred successfully.',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
    if (result.isConfirmed) {
        location.reload();
    }
});
                }
            });
        });

        // Handle Extend Stay button click
        $('.extend-stay-btn').on('click', function() {
            const bookingId = $(this).data('id');
            const guestName = $(this).data('guest');
            const currentCheckOut = $(this).data('checkout');
            const roomType = $(this).data('room-type');
            const roomPrice = parseFloat($(this).data('room-price'));
            const amountPaidFromData = parseCurrencyValue($(this).data('amount-paid'));
            const remainingBalance = parseCurrencyValue($(this).data('remaining-balance'));
            const paymentOption = $(this).data('payment-option'); // Get payment option
            const downpaymentAmount = parseCurrencyValue($(this).data('downpayment-amount') || 0); // Get downpayment amount

            // Determine actual amount paid for display in Extend Stay modal
            let displayAmountForExtendModal;
            if (paymentOption === 'Partial Payment') {
                displayAmountForExtendModal = 1500;
            } else if (paymentOption === 'Custom Payment') {
                displayAmountForExtendModal = downpaymentAmount;
            } else if (paymentOption === 'Full' || paymentOption === 'Full Payment') {
                displayAmountForExtendModal = amountPaidFromData;
            } else {
                displayAmountForExtendModal = amountPaidFromData; // Default to actual amount if no specific rule
            }

            // Format the current check-out date for the date input
            const formattedCheckOut = new Date(currentCheckOut).toISOString().split('T')[0];
            
            // Set minimum date for new check-out
            const minDate = new Date(currentCheckOut);
            minDate.setDate(minDate.getDate() + 1);
            
            // Populate the modal fields
            $('#extendBookingId').val(bookingId);
            $('#extendGuestName').val(guestName);
            $('#extendRoomType').val(roomType);
            $('#extendRoomPrice').val('₱' + roomPrice.toFixed(2));
            $('#extendAmountPaid').val('₱' + displayAmountForExtendModal.toFixed(2));
            $('#extendRemainingBalance').val('₱' + remainingBalance.toFixed(2));
            $('#currentCheckOut').val(formattedCheckOut);
            $('#currentRoomPrice').val(roomPrice);
            $('#newCheckoutDate').attr('min', minDate.toISOString().split('T')[0]);
            
            // Clear previous values
            $('#newCheckoutDate').val('');
            $('#additionalNights').val('');
            $('#additionalAmount').val('');
            $('#paymentMethod').val('');
            $('#paymentOption').val('later');
            
            // Show the modal
            $('#extendStayModal').modal('show');
        });

        // Calculate additional nights and amount when new check-out date changes
        $('#newCheckoutDate').on('change', function() {
            const currentCheckOut = new Date($('#currentCheckOut').val());
            const newCheckOut = new Date($(this).val());
            const roomPrice = parseFloat($('#currentRoomPrice').val());
            
            if (newCheckOut <= currentCheckOut) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'New check-out date must be after the current check-out date'
                });
                $(this).val('');
                $('#additionalNights').val('');
                $('#additionalAmount').val('');
                return;
            }
            
            const additionalNights = Math.ceil((newCheckOut - currentCheckOut) / (1000 * 60 * 60 * 24));
            const additionalAmount = additionalNights * roomPrice;
            
            // Update the fields
            $('#additionalNights').val(additionalNights);
            $('#additionalAmount').val('₱' + additionalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });

        // Show/hide payment method section based on payment option
        $('#paymentOption').on('change', function() {
            const payNow = $(this).val() === 'now';
            const paymentMethodSection = $('#paymentMethodSection');
            const paymentMethodSelect = $('#paymentMethod');
            
            if (payNow) {
                paymentMethodSection.slideDown();
                paymentMethodSelect.prop('required', true);
            } else {
                paymentMethodSection.slideUp();
                paymentMethodSelect.prop('required', false);
                paymentMethodSelect.val('');
            }
        });

        // Handle form submission
        $('#extendStayForm').on('submit', function(e) {
            e.preventDefault();
            
            const newCheckoutDate = $('#newCheckoutDate').val();
            const additionalNights = $('#additionalNights').val();
            
            // Validate required fields
            if (!newCheckoutDate) {
                Swal.fire('Error', 'Please select a new check-out date', 'error');
                return;
            }
            
            if (!additionalNights || additionalNights <= 0) {
                Swal.fire('Error', 'Please select a valid check-out date that adds at least one night', 'error');
                return;
            }
            
            if ($('#paymentOption').val() === 'now' && !$('#paymentMethod').val()) {
                Swal.fire('Error', 'Please select a payment method', 'error');
                return;
            }
            
            // Get form data
            var formData = new FormData();
            formData.append('booking_id', $('#extendBookingId').val());
            formData.append('new_checkout', newCheckoutDate);
            formData.append('additional_nights', additionalNights);
            formData.append('additional_amount', $('#additionalAmount').val().replace(/[₱,]/g, ''));
            formData.append('payment_option', $('#paymentOption').val());
            
            if ($('#paymentOption').val() === 'now') {
                formData.append('payment_method', $('#paymentMethod').val());
            }

            // Debug log
            console.log('Form data:', {
                booking_id: formData.get('booking_id'),
                new_checkout: formData.get('new_checkout'),
                additional_nights: formData.get('additional_nights'),
                additional_amount: formData.get('additional_amount'),
                payment_option: formData.get('payment_option'),
                payment_method: formData.get('payment_method')
            });
            
            // Show loading state
            $('#extendStayBtn').prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: 'process_extend_stay.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Stay extended successfully. ' + 
                                (formData.get('payment_option') === 'later' ? 
                                '₱' + parseFloat(formData.get('additional_amount')).toLocaleString() + ' has been added to the remaining balance.' : 
                                'Payment of ₱' + parseFloat(formData.get('additional_amount')).toLocaleString() + ' has been recorded.'),
                            showConfirmButton: true
                        }).then((result) => {
                            location.reload();
                        });
                    } else {
                        // Show error message with details
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Extend Stay',
                            text: response.message,
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                    
                    let errorMessage = 'Failed to communicate with the server. Please check your connection and try again.';
                    let technicalDetails = status + ' - ' + error;
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                    
                    // Show network error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        footer: 'Technical details: ' + technicalDetails,
                        showConfirmButton: true
                    });
                },
                complete: function() {
                    // Reset button state
                    $('#extendStayBtn').prop('disabled', false).text('Extend Stay');
                }
            });
        });

        // Calculate change when cash amount changes
        $('#cashAmount').on('input', function() {
            const cashAmount = parseFloat($(this).val().replace('₱', '').replace(/,/g, '')) || 0;
            const totalToPay = parseFloat($('#totalAmountToPay').val().replace('₱', '').replace(/,/g, '')) || 0;
            const change = Math.max(0, cashAmount - totalToPay);
            
            $('#changeAmount').val('₱' + change.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });

        // Handle room type selection change
        $('#newRoomSelect').change(function() {
            var roomTypeId = $(this).val();
            var roomNumberGroup = $('#roomNumberGroup');
            var roomNumberSelect = $('#roomNumberSelect');

            if (roomTypeId) {
                // Show room number dropdown and load options
                roomNumberGroup.show();
                $.get('get_room_numbers.php', { room_type_id: roomTypeId }, function(data) {
                    roomNumberSelect.html(data);
                });
            } else {
                // Hide room number dropdown if no room type selected
                roomNumberGroup.hide();
                roomNumberSelect.html('<option value="">Select a room number...</option>');
            }
        });

        // Load room types when transfer modal opens
        $('#transferRoomModal').on('show.bs.modal', function() {
            // Load room types
            $.get('get_transfer_rooms.php', function(data) {
                $('#newRoomSelect').html(data);
            });
            // Hide room number dropdown initially
            $('#roomNumberGroup').hide();
        });

        // Handle room type selection change
        $('#newRoomSelect').change(function() {
            var roomTypeId = $(this).val();
            var roomNumberGroup = $('#roomNumberGroup');
            var roomNumberSelect = $('#roomNumberSelect');

            if (roomTypeId) {
                // Show room number dropdown and load options
                roomNumberGroup.show();
                $.get('get_room_numbers.php', { room_type_id: roomTypeId }, function(data) {
                    roomNumberSelect.html(data);
                });
            } else {
                // Hide room number dropdown if no room type selected
                roomNumberGroup.hide();
                roomNumberSelect.html('<option value="">Select a room number...</option>');
            }
        });

        // Handle payment method change
        $('#paymentMethod').change(function() {
            if ($(this).val() === 'Cash') {
                $('#cashPaymentSection').show();
            } else {
                $('#cashPaymentSection').hide();
            }
        });

        // Calculate change when cash amount is entered
        $('#cashAmount').on('input', function() {
            const cashAmount = parseFloat($(this).val()) || 0;
            const priceDifference = parseFloat($('#finalAmountDisplay').text().replace('₱', '')) || 0;
            const change = cashAmount - priceDifference;
            $('#changeAmount').val('₱' + change.toFixed(2));
        });
    });
    </script>

    <!-- Add Font Awesome CSS if not already included -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    </body>
    </html>


