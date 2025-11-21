<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
</head>
<body>
    <?php
    // Enable error display for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'includes/init.php';
    require_once 'db.php';
    include 'header.php';
    include 'sidebar.php';

    // Debug: Output current directory and include paths to help with debugging
    error_log("Current directory: " . __DIR__);
    error_log("Include path: " . get_include_path());
    error_log("Database connection status: " . ($con ? "Connected" : "Not connected"));

    // Debug: Let's check the current bookings in the database
    $query = "SELECT booking_id, first_name, last_name, status, user_types, created_at FROM bookings ORDER BY created_at DESC LIMIT 10";
    $result = mysqli_query($con, $query);
    if ($result) {
        error_log("===== DEBUG: RECENT BOOKINGS =====");
        while ($row = mysqli_fetch_assoc($result)) {
            error_log(json_encode($row));
        }
        error_log("=================================");
    } else {
        error_log("Debug query failed: " . mysqli_error($con));
    }

    // Debug: Let's directly look at the booking ID we can see in the database screenshot
    $debugBookingQuery = "SELECT * FROM bookings WHERE booking_id = 1";
    $debugBookingResult = mysqli_query($con, $debugBookingQuery);
    if ($debugBookingResult && mysqli_num_rows($debugBookingResult) > 0) {
        $bookingDetails = mysqli_fetch_assoc($debugBookingResult);
        error_log("===== DEBUG: BOOKING ID 1 DETAILS =====");
        error_log(json_encode($bookingDetails));
        error_log("====================================");
        
        // Test this booking against our query conditions
        $testQuery = "SELECT booking_id, first_name, last_name, status, user_types 
                    FROM bookings 
                    WHERE booking_id = 1 
                    AND (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed' 
                        OR status IN ('pending', 'Pending', 'confirmed', 'Confirmed', 'Walkin', 'walkin'))";
        $testResult = mysqli_query($con, $testQuery);
        error_log("===== DEBUG: DOES THIS BOOKING MATCH FILTER? =====");
        error_log("SQL: $testQuery");
        error_log("Result rows: " . ($testResult ? mysqli_num_rows($testResult) : 'Query failed'));
        if ($testResult && mysqli_num_rows($testResult) > 0) {
            error_log("Yes, booking matches filter criteria");
            error_log(json_encode(mysqli_fetch_assoc($testResult)));
        } else {
            error_log("No, booking does not match filter criteria");
        }
        error_log("=================================================");
    }

    // Initialize variables
    $totalAmount = 0;
    $totalDownpayment = 0;
    $chartData = [];
    $salesData = null;

    // Get view type from POST or set default
    $rangeType = $_POST['range_type'] ?? 'daily';
    $today = date('Y-m-d');

    // Set date ranges based on range type
    switch($rangeType) {
        case 'daily':
            // Today only
            $startDate = $today;
            $endDate = $today;
            $dateCondition = "DATE(created_at) = '$today'";
            $orderDateCondition = "DATE(order_date) = '$today'";
            break;
        case 'weekly':
            // Get the start of the last 7 days (including today)
            $startDate = date('Y-m-d', strtotime('-6 days', strtotime($today)));
            $endDate = $today;
            $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
            $orderDateCondition = "DATE(order_date) >= '$startDate' AND DATE(order_date) <= '$endDate'";
            break;
        case 'monthly':
            // Current month (1st to last day)
            $startDate = date('Y-m-01'); // First day of current month
            $endDate = date('Y-m-t');    // Last day of current month
            $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
            $orderDateCondition = "DATE(order_date) >= '$startDate' AND DATE(order_date) <= '$endDate'";
            break;
        case 'yearly':
            // Get selected year from form or use current year
            $selectedYear = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
            $startDate = "$selectedYear-01-01"; // First day of selected year
            $endDate = "$selectedYear-12-31";   // Last day of selected year
            $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
            $orderDateCondition = "DATE(order_date) >= '$startDate' AND DATE(order_date) <= '$endDate'";
            error_log("Yearly report for $selectedYear ($startDate to $endDate)");
            break;
        default:
            // Fallback to today
            $startDate = $today;
            $endDate = $today;
            $dateCondition = "DATE(created_at) = '$today'";
            $orderDateCondition = "DATE(order_date) = '$today'";
    }

    // Get report type from POST or set default
    $reportType = isset($_POST['report_type']) ? $_POST['report_type'] : 'all';

    // Function to check if a table exists
    function tableExists($tableName) {
        global $con;
        $result = mysqli_query($con, "SHOW TABLES LIKE '$tableName'");
        return $result && mysqli_num_rows($result) > 0;
    }

    // Function to check if a column exists in a table
    function columnExists($table, $column) {
        global $con;
        $result = mysqli_query($con, "SHOW COLUMNS FROM $table LIKE '$column'");
        return $result && mysqli_num_rows($result) > 0;
    }

    function getAdminSales($dateCondition) {
        global $con;
        
        // Debug added date condition
        error_log("Admin query date condition: $dateCondition");
        
        // Check if event_bookings table exists
        $eventBookingsTableExists = tableExists('event_bookings');
        
        // Get total amounts from bookings for admin (including relevant statuses)
        $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                    FROM bookings 
                    WHERE user_types = 'admin'
                    AND ($dateCondition)
                    AND (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')";
        
        // Add event bookings revenue if the table exists
        if ($eventBookingsTableExists) {
            $totalQuery = "SELECT ((" . $totalQuery . ") + 
                        (SELECT COALESCE(SUM(total_amount), 0) 
                        FROM event_bookings 
                        WHERE total_amount = paid_amount 
                        AND user_id = 1
                        AND ($dateCondition)
                        AND (booking_status = 'Confirmed' OR booking_status = 'completed')))
                        as total_revenue";
        }
        
        $totalResult = mysqli_query($con, $totalQuery);
        if (!$totalResult) {
            die("Error executing total query: " . mysqli_error($con));
        }
        $totals = mysqli_fetch_assoc($totalResult);
        
        // Get detailed booking data (including relevant statuses)
        $query = "SELECT 
            'Room Booking' as booking_type,
            booking_id,
            first_name,
            last_name,
            email,
            check_in as booking_date,
            check_out as end_date,
            number_of_guests,
            payment_method,
            payment_option,
            total_amount,
            total_amount as amount_paid,
            0 as change_amount,
            0 as extra_fee,
            status,
            created_at,
            payment_reference,
            payment_proof,
            user_types as source,
            0 as remaining_balance
        FROM bookings 
        WHERE user_types = 'admin'
        AND ($dateCondition)
        AND (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')";
        
        // Add event bookings if the table exists
        if ($eventBookingsTableExists) {
            $query .= " UNION ALL
            SELECT 
                'Event Booking' as booking_type,
                id as booking_id,
                customer_name as first_name,
                '' as last_name,
                '' as email,
                event_date as booking_date,
                " . (columnExists('event_bookings', 'end_time') ? "COALESCE(end_time, event_date)" : "event_date") . " as end_date,
                " . (columnExists('event_bookings', 'number_of_guests') ? "COALESCE(number_of_guests, 0)" : "0") . " as number_of_guests,
                " . (columnExists('event_bookings', 'payment_method') ? "COALESCE(payment_method, '')" : "''") . " as payment_method,
                " . (columnExists('event_bookings', 'event_type') ? "COALESCE(event_type, package_name)" : "package_name") . " as payment_option,
                total_amount,
                paid_amount as amount_paid,
                0 as change_amount,
                " . (columnExists('event_bookings', 'extra_guest_charge') ? "COALESCE(extra_guest_charge, 0)" : "0") . " as extra_fee,
                " . (columnExists('event_bookings', 'booking_status') ? "COALESCE(booking_status, 'Confirmed')" : "'Confirmed'") . " as status,
                " . (columnExists('event_bookings', 'created_at') ? "COALESCE(created_at, reservation_date)" : "reservation_date") . " as created_at,
                " . (columnExists('event_bookings', 'payment_reference') ? "COALESCE(payment_reference, '')" : "''") . " as payment_reference,
                " . (columnExists('event_bookings', 'payment_proof') ? "COALESCE(payment_proof, '')" : "''") . " as payment_proof,
                'admin' as source,
                remaining_balance
            FROM event_bookings
            WHERE user_id = 1
            AND ($dateCondition)
            AND (booking_status = 'Confirmed' OR booking_status = 'completed')
            AND total_amount = paid_amount";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        error_log("Admin sales query: $query");
        
        $details = mysqli_query($con, $query);
        if (!$details) {
            error_log("Error executing admin query: " . mysqli_error($con));
            die("Error executing details query: " . mysqli_error($con) . " - Query: " . $query);
        }

        return [
            'details' => $details,
            'totals' => $totals
        ];
    }

    function getFrontDeskSales($dateCondition) {
        global $con;
        
        // Debug added date condition
        error_log("FrontDesk query date condition: $dateCondition");
        
        // Check if event_bookings table exists
        $eventBookingsTableExists = tableExists('event_bookings');
        
        // Get total amounts from bookings for front desk (including all valid statuses)
        $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                    FROM bookings 
                    WHERE user_types = 'frontdesk' 
                    AND ($dateCondition)
                    AND (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')";
        
        // Add event bookings revenue if the table exists
        if ($eventBookingsTableExists) {
            $totalQuery = "SELECT ((" . $totalQuery . ") + 
                        (SELECT COALESCE(SUM(total_amount), 0) 
                        FROM event_bookings 
                        WHERE total_amount = paid_amount 
                        AND user_id = 2
                        AND ($dateCondition)
                        AND (booking_status = 'Confirmed' OR booking_status = 'completed')))
                        as total_revenue";
        }
        
        $totalResult = mysqli_query($con, $totalQuery);
        if (!$totalResult) {
            die("Error executing total query: " . mysqli_error($con));
        }
        $totals = mysqli_fetch_assoc($totalResult);
        
        // Get detailed booking data (including all valid statuses)
        $query = "SELECT 
            'Room Booking' as booking_type,
            booking_id,
            first_name,
            last_name,
            email,
            check_in as booking_date,
            check_out as end_date,
            number_of_guests,
            payment_method,
            payment_option,
            total_amount,
            total_amount as amount_paid,
            0 as change_amount,
            0 as extra_fee,
            status,
            created_at,
            payment_reference,
            payment_proof,
            user_types as source,
            0 as remaining_balance
        FROM bookings 
        WHERE user_types = 'frontdesk' 
        AND ($dateCondition)
        AND (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')";
        
        // Add event bookings if the table exists
        if ($eventBookingsTableExists) {
            $query .= " UNION ALL
            SELECT 
                'Event Booking' as booking_type,
                id as booking_id,
                customer_name as first_name,
                '' as last_name,
                '' as email,
                event_date as booking_date,
                " . (columnExists('event_bookings', 'end_time') ? "COALESCE(end_time, event_date)" : "event_date") . " as end_date,
                " . (columnExists('event_bookings', 'number_of_guests') ? "COALESCE(number_of_guests, 0)" : "0") . " as number_of_guests,
                " . (columnExists('event_bookings', 'payment_method') ? "COALESCE(payment_method, '')" : "''") . " as payment_method,
                " . (columnExists('event_bookings', 'event_type') ? "COALESCE(event_type, package_name)" : "package_name") . " as payment_option,
                total_amount,
                paid_amount as amount_paid,
                0 as change_amount,
                " . (columnExists('event_bookings', 'extra_guest_charge') ? "COALESCE(extra_guest_charge, 0)" : "0") . " as extra_fee,
                " . (columnExists('event_bookings', 'booking_status') ? "COALESCE(booking_status, 'Confirmed')" : "'Confirmed'") . " as status,
                " . (columnExists('event_bookings', 'created_at') ? "COALESCE(created_at, reservation_date)" : "reservation_date") . " as created_at,
                " . (columnExists('event_bookings', 'payment_reference') ? "COALESCE(payment_reference, '')" : "''") . " as payment_reference,
                " . (columnExists('event_bookings', 'payment_proof') ? "COALESCE(payment_proof, '')" : "''") . " as payment_proof,
                'frontdesk' as source,
                remaining_balance
            FROM event_bookings
            WHERE user_id = 2
            AND ($dateCondition)
            AND (booking_status = 'Confirmed' OR booking_status = 'completed')
            AND total_amount = paid_amount";
        }
        
        $query .= " ORDER BY created_at DESC";

        $details = mysqli_query($con, $query);
        if (!$details) {
            die("Error executing details query: " . mysqli_error($con));
        }

        return [
            'details' => $details,
            'totals' => $totals
        ];
    }

    function getCashierSales($dateCondition) {
        global $con, $orderDateCondition;
        
        error_log("Starting getCashierSales function");
        
        // Base query
        $query = "SELECT 
                    id as booking_id,
                    'POS Order' as booking_type,
                    COALESCE(customer_name, 'Walk-in Customer') as first_name,
                    '' as last_name,
                    '' as email,
                    order_date as booking_date,
                    order_date as end_date,
                    0 as number_of_guests,
                    payment_method,
                    order_type as payment_option,
                    total_amount,
                    amount_paid,
                    change_amount,
                    extra_fee,
                    status,
                    order_date as created_at,
                    payment_reference,
                    '' as payment_proof,
                    'cashier' as source,
                    remaining_balance
                FROM orders 
                WHERE status = 'finished' AND ($orderDateCondition)";
        
        $query .= " ORDER BY order_date DESC";
        
        error_log("Cashier query: $query");
        $result = mysqli_query($con, $query);
        
        if (!$result) {
            $error = "Error executing query: " . mysqli_error($con);
            error_log($error);
            return [
                'details' => false,
                'totals' => ['total_revenue' => 0],
                'error' => $error
            ];
        }
        
        // Get total revenue query
        $totalQuery = "SELECT 
                        COALESCE(SUM(total_amount), 0) as total_revenue,
                        COUNT(*) as total_orders
                      FROM orders
                      WHERE status = 'finished' AND ($orderDateCondition)";
        
        $totalResult = mysqli_query($con, $totalQuery);
        $totals = mysqli_fetch_assoc($totalResult);
        
        return [
            'details' => $result,
            'totals' => [
                'total_revenue' => $totals['total_revenue'] ?? 0,
                'total_orders' => $totals['total_orders'] ?? 0,
                'total_items' => 0 // Not tracking items in this query
            ]
        ];
    }
    
    function getAllSales($dateCondition) {
        global $con, $orderDateCondition;
        
        // Check if orders table exists
        $ordersTableExists = tableExists('orders');
        
        // Check if event_bookings table exists
        $eventBookingsTableExists = tableExists('event_bookings');
        
        // The date conditions ($dateCondition and $orderDateCondition) are already set globally
        // based on the selected rangeType in the main script body. So we will use those directly.
        $bookingsDateCondition = $dateCondition;
        $eventsDateCondition = $dateCondition;

        
        // Get total amounts from bookings, orders, and event_bookings (including all valid statuses)
        $totalQuery = "SELECT 
            (SELECT COALESCE(SUM(total_amount), 0) FROM bookings 
            WHERE (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')
            AND " . $bookingsDateCondition . ")";
        
        if ($ordersTableExists) {
            $totalQuery .= " + (SELECT COALESCE(SUM(total_amount), 0) FROM orders 
                WHERE status = 'finished' AND " . $orderDateCondition . ")";
        }
        
        if ($eventBookingsTableExists) {
            $totalQuery .= " + (SELECT COALESCE(SUM(total_amount), 0) FROM event_bookings 
                WHERE (booking_status = 'Confirmed' OR booking_status = 'completed') AND total_amount = paid_amount AND " . $eventsDateCondition . ")";
        }
        
        $totalQuery .= " as total_revenue";
        
        $totalResult = mysqli_query($con, $totalQuery);
        if (!$totalResult) {
            error_log("Error executing total query: " . mysqli_error($con));
            die("Error executing total query: " . mysqli_error($con));
        }
        $totals = mysqli_fetch_assoc($totalResult);
        
        // Build the base query for bookings
        $queries = [];
        $queries[] = "SELECT 
            'Room Booking' as booking_type,
            booking_id as booking_id,
            first_name,
            last_name,
            email,
            check_in as booking_date,
            check_out as end_date,
            number_of_guests,
            payment_method,
            COALESCE(payment_option, '') as payment_option,
            total_amount,
            total_amount as amount_paid,
            0.00 as change_amount,
            0.00 as extra_fee,
            status,
            created_at,
            payment_reference,
            COALESCE(payment_proof, '') as payment_proof,
            COALESCE(user_types, 'frontdesk') as source,
            0.00 as remaining_balance
        FROM bookings 
        WHERE (status = 'Checked Out' OR status = 'checked_out' OR status = 'completed' OR status = 'Completed')
            AND $bookingsDateCondition";

        // Add orders query if table exists
        if ($ordersTableExists) {
            $queries[] = "SELECT 
                'Food Order' as booking_type,
                id as booking_id,
                COALESCE(customer_name, 'Walk-in Customer') as first_name,
                '' as last_name,
                '' as email,
                order_date as booking_date,
                order_date as end_date,
                0.00 as number_of_guests,
                payment_method,
                order_type as payment_option,
                total_amount,
                amount_paid,
                change_amount,
                extra_fee,
                status,
                order_date as created_at,
                payment_reference,
                '' as payment_proof,
                'cashier' as source,
                remaining_balance
            FROM orders 
            WHERE status = 'finished'
            AND $orderDateCondition";
        }
        
        // Add event bookings query if table exists
        if ($eventBookingsTableExists) {
            $queries[] = "SELECT 
                'Event Booking' as booking_type,
                id as booking_id,
                customer_name as first_name,
                '' as last_name,
                '' as email,
                event_date as booking_date,
                " . (columnExists('event_bookings', 'end_time') ? "COALESCE(end_time, event_date)" : "event_date") . " as end_date,
                " . (columnExists('event_bookings', 'number_of_guests') ? "COALESCE(number_of_guests, 0.00)" : "0.00") . " as number_of_guests,
                " . (columnExists('event_bookings', 'payment_method') ? "COALESCE(payment_method, '')" : "''") . " as payment_method,
                " . (columnExists('event_bookings', 'event_type') ? "COALESCE(event_type, package_name)" : "package_name") . " as payment_option,
                total_amount,
                paid_amount as amount_paid,
                0.00 as change_amount,
                " . (columnExists('event_bookings', 'extra_guest_charge') ? "COALESCE(extra_guest_charge, 0.00)" : "0.00") . " as extra_fee,
                " . (columnExists('event_bookings', 'booking_status') ? "COALESCE(booking_status, 'Confirmed')" : "'Confirmed'") . " as status,
                " . (columnExists('event_bookings', 'created_at') ? "COALESCE(created_at, reservation_date)" : "reservation_date") . " as created_at,
                " . (columnExists('event_bookings', 'payment_reference') ? "COALESCE(payment_reference, '')" : "''") . " as payment_reference,
                " . (columnExists('event_bookings', 'payment_proof') ? "COALESCE(payment_proof, '')" : "''") . " as payment_proof,
                'event' as source,
                remaining_balance
            FROM event_bookings
            WHERE (booking_status = 'Confirmed' OR booking_status = 'completed') AND total_amount = paid_amount
            AND $eventsDateCondition";
        }

        // Combine all queries with UNION ALL
        $query = implode(" UNION ALL ", $queries);
        $query .= " ORDER BY created_at DESC";

        $details = mysqli_query($con, $query);
        if (!$details) {
            error_log("Error executing details query: " . mysqli_error($con));
            die("Error executing details query: " . mysqli_error($con));
        }

        return [
            'details' => $details,
            'totals' => $totals
        ];
    }

    // Function to get sales data based on date range
    function getSalesData($dateCondition) {
        global $reportType, $rangeType, $orderDateCondition;
        
        switch($reportType) {
            case 'admin':
                return getAdminSales($dateCondition);
            case 'frontdesk':
                return getFrontDeskSales($dateCondition);
            case 'cashier':
                return getCashierSales($dateCondition);
            case 'all':
                return getAllSales($dateCondition);
            default:
                return getAllSales($dateCondition);
        }
    }

    // Get sales data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || !isset($_POST['range_type'])) {
        try {
            $result = getSalesData($dateCondition);
            if (is_array($result)) {
                $salesData = $result['details'];
                $totals = $result['totals'];
                $totalAmount = $totals['total_revenue'] ?? 0;
                
                // Process results for chart data
                if ($salesData) {
                    $chartData = [];
                    $chartLabels = [];
                    mysqli_data_seek($salesData, 0);
                    while ($row = mysqli_fetch_assoc($salesData)) {
                        // Get the appropriate date field based on report type
                        $dateField = isset($row['created_at']) ? $row['created_at'] : 
                                (isset($row['order_date']) ? $row['order_date'] : date('Y-m-d'));
                        
                        $date = date('M d', strtotime($dateField));
                        if (!isset($chartData[$date])) {
                            $chartData[$date] = 0;
                            $chartLabels[] = $date;
                        }
                        $chartData[$date] += floatval($row['total_amount']);
                    }
                    mysqli_data_seek($salesData, 0);
                }
            } else {
                $salesData = $result;
                // Process old format for non-admin reports
                if ($salesData) {
                    $chartData = [];
                    $chartLabels = [];
                    while ($row = mysqli_fetch_assoc($salesData)) {
                        $totalAmount += floatval($row['total_amount']);
                        // Get the appropriate date field based on report type
                        $dateField = isset($row['created_at']) ? $row['created_at'] : 
                                (isset($row['order_date']) ? $row['order_date'] : date('Y-m-d'));
                        
                        $date = date('M d', strtotime($dateField));
                        if (!isset($chartData[$date])) {
                            $chartData[$date] = 0;
                            $chartLabels[] = $date;
                        }
                        $chartData[$date] += floatval($row['total_amount']);
                    }
                    mysqli_data_seek($salesData, 0);
                }
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><em class="fa fa-home"></em></a></li>
                <li class="active">Sales Report</li>
            </ol>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                Sales Report
                <div class="pull-right">
                    <button class="btn btn-success" onclick="exportCurrentReport()">
                        <i class="fa fa-download"></i> Export Current Report
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <!-- Filters -->
                <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-danger mr-2" id="exportPdfBtn"><i class="fas fa-file-pdf"></i> Export PDF</button>
                        <button class="btn btn-success" id="exportExcelBtn"><i class="fas fa-file-excel"></i> Export Excel</button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>View Type:</label>
                        <select class="form-control" name="range_type" id="rangeType" onchange="toggleYearSelector()">
                            <option value="daily" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'daily' ? 'selected' : ''; ?>>Daily </option>
                            <option value="weekly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'weekly' ? 'selected' : ''; ?>>Weekly </option>
                            <option value="monthly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'monthly' ? 'selected' : ''; ?>>Monthly </option>
                            <option value="yearly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'yearly' ? 'selected' : ''; ?>>Yearly </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Report Type:</label>
                        <select class="form-control" name="report_type" id="reportType">
                            <option value="all" <?php echo $reportType == 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="admin" <?php echo $reportType == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="frontdesk" <?php echo $reportType == 'frontdesk' ? 'selected' : ''; ?>>Front Desk</option>
                            <option value="cashier" <?php echo $reportType == 'cashier' ? 'selected' : ''; ?>>Cashier (POS)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-refresh"></i> Update Report
                        </button>
                    </div>
                </div>
                </form>

                <!-- Summary Cards -->
                <div class="row summary-cards mb-4">
                    <div class="col-md-12">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>
                                <?php 
                                switch($rangeType) {
                                    case 'daily':
                                        echo "Today's Revenue (" . date('F j, Y') . ")";
                                        break;
                                    case 'weekly':
                                        echo "Weekly Revenue (" . date('M d', strtotime($startDate)) . " - " . date('M d', strtotime($endDate)) . ")";
                                        break;
                                    case 'monthly':
                                        echo "Monthly Revenue (" . date('F Y') . ")";
                                        break;
                                    case 'yearly':
                                        echo "Yearly Revenue (" . date('Y') . ")";
                                        break;
                                }
                                ?>
                                </h5>
                                <h3>₱<?php echo number_format($totalAmount, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Breakdown Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php 
                            switch($rangeType) {
                                case 'daily':
                                    echo "Today's Bookings";
                                    break;
                                case 'weekly':
                                    echo "This Week's Bookings";
                                    break;
                                case 'monthly':
                                    echo "This Month's Bookings";
                                    break;
                                case 'yearly':
                                    echo "This Year's Bookings";
                                    break;
                            }
                            ?>
                        </h5>
                        <div class="table-responsive">
                            <table id="bookingsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $runningTotal = 0;
                                    if ($salesData && mysqli_num_rows($salesData) > 0): 
                                        while ($row = mysqli_fetch_assoc($salesData)): 
                                            $runningTotal += floatval($row['total_amount']);
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['booking_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>₱<?php echo number_format($row['amount_paid'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No bookings found for this period</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Revenue Trend</h5>
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Revenue by Source</h5>
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    function exportCurrentReport() {
        const reportType = document.getElementById('reportType').value;
        const rangeType = document.getElementById('rangeType').value;
        window.location.href = `export_report.php?report_type=${reportType}&range_type=${rangeType}`;
    }

    // Function to toggle year selector visibility
    function toggleYearSelector() {
        const rangeType = document.getElementById('rangeType').value;
        const yearContainer = document.getElementById('yearSelectorContainer');
        
        if (rangeType === 'yearly') {
            yearContainer.style.display = 'block';
        } else {
            yearContainer.style.display = 'none';
        }
    }
    
    // Initialize year selector visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Show year selector if yearly is already selected
        toggleYearSelector();
        <?php
        // Prepare data for Revenue Trend Chart
        $revenueData = [];
        $dateLabels = [];
        
        // Get the date range based on range type
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
        
        // Initialize arrays with dates
        foreach ($dateRange as $date) {
            $dateKey = $date->format('Y-m-d');
            $revenueData[$dateKey] = 0;
            $dateLabels[] = $date->format('M d');
        }
        
        // Calculate daily revenue from the sales data
        if ($salesData) {
            mysqli_data_seek($salesData, 0);
            while ($row = mysqli_fetch_assoc($salesData)) {
                $saleDate = date('Y-m-d', strtotime($row['created_at']));
                if (isset($revenueData[$saleDate])) {
                    $revenueData[$saleDate] += floatval($row['total_amount']);
                }
            }
            mysqli_data_seek($salesData, 0);
        }
        
        // Prepare source distribution data
        $adminTotal = 0;
        $frontdeskTotal = 0;
        $cashierTotal = 0;
        
        if ($salesData) {
            mysqli_data_seek($salesData, 0);
            while ($row = mysqli_fetch_assoc($salesData)) {
                switch($row['source']) {
                    case 'admin':
                        $adminTotal += floatval($row['total_amount']);
                        break;
                    case 'frontdesk':
                        $frontdeskTotal += floatval($row['total_amount']);
                        break;
                    case 'cashier':
                        $cashierTotal += floatval($row['total_amount']);
                        break;
                }
            }
            mysqli_data_seek($salesData, 0);
        }
        
        // Calculate percentages for the distribution chart
        $totalRevenue = $adminTotal + $frontdeskTotal + $cashierTotal;
        $adminPercentage = $totalRevenue > 0 ? round(($adminTotal / $totalRevenue) * 100, 1) : 0;
        $frontdeskPercentage = $totalRevenue > 0 ? round(($frontdeskTotal / $totalRevenue) * 100, 1) : 0;
        $cashierPercentage = $totalRevenue > 0 ? round(($cashierTotal / $totalRevenue) * 100, 1) : 0;
        ?>

        // Revenue Trend Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dateLabels); ?>,
                datasets: [{
                    label: 'Daily Revenue',
                    data: <?php echo json_encode(array_values($revenueData)); ?>,
                    borderColor: '#DAA520',
                    backgroundColor: 'rgba(218, 165, 32, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        bodySpacing: 8,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                return 'Revenue: ₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Distribution Chart
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Admin (<?php echo $adminPercentage; ?>%)',
                    'Front Desk (<?php echo $frontdeskPercentage; ?>%)',
                    'Cashier (<?php echo $cashierPercentage; ?>%)'
                ],
                datasets: [{
                    data: [
                        <?php echo $adminTotal; ?>,
                        <?php echo $frontdeskTotal; ?>,
                        <?php echo $cashierTotal; ?>
                    ],
                    backgroundColor: ['#007bff', '#17a2b8', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 13
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: true,
                        padding: 12,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        bodySpacing: 8,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                return 'Revenue: ₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');
        const title = 'Sales Report';
        const date = new Date().toLocaleString();
        doc.setFontSize(18);
        doc.text(title, 40, 40);
        doc.setFontSize(12);
        doc.text('Generated: ' + date, 40, 60);

        // Get table headers and data
        const table = document.querySelector('.table-responsive table');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
        const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr =>
            Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim())
        ).filter(row => row.length === headers.length);

        // Add table to PDF
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 80,
            styles: { fontSize: 10 },
            headStyles: { fillColor: [218, 165, 32] },
            margin: { left: 40, right: 40 }
        });

        doc.save('sales_report.pdf');
    });

    document.getElementById('exportExcelBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get the table data
        var table = document.getElementById('bookingsTable');
        var rows = Array.from(table.querySelectorAll('tr'));
        
        // Create workbook and worksheet
        var wb = XLSX.utils.book_new();
        var ws_data = [];
        
        // Get headers
        var headers = Array.from(rows[0].querySelectorAll('th')).map(header => header.textContent.trim());
        ws_data.push(headers);
        
        // Get data rows
        rows.slice(1).forEach(row => {
            if (!row.classList.contains('table-info')) { // Skip the total row
                var rowData = Array.from(row.querySelectorAll('td')).map((cell, index) => {
                    var value = cell.textContent.trim();
                    
                    // Handle date column (index 3)
                    if (index === 3) {
                        var date = new Date(value);
                        if (!isNaN(date)) {
                            return date; // Return as date object for Excel
                        }
                    }
                    
                    // Handle amount columns (index 4, 5, 6)
                    if (index >= 4 && index <= 6) {
                        return value.replace('₱', '').replace(/,/g, '');
                    }
                    
                    return value;
                });
                ws_data.push(rowData);
            }
        });
        
        // Create worksheet
        var ws = XLSX.utils.aoa_to_sheet(ws_data);
        
        // Set column widths
        var colWidths = [
            {wch: 15}, // Type
            {wch: 10}, // Source
            {wch: 25}, // Name
            {wch: 15}, // Date
            {wch: 12}, // Amount
            {wch: 12}, // Amount Paid
            {wch: 12}, // Change
            {wch: 15}, // Payment Method
            {wch: 10}  // Status
        ];
        ws['!cols'] = colWidths;
        
        // Format date cells
        var dateRange = XLSX.utils.decode_range(ws['!ref']);
        for (var R = 1; R <= dateRange.e.r; ++R) {
            var dateCell = XLSX.utils.encode_cell({r: R, c: 3}); // Column D (0-based index 3)
            if (ws[dateCell] && ws[dateCell].v) {
                ws[dateCell].z = 'mm/dd/yyyy';
            }
        }
        
        // Format amount cells
        for (var R = 1; R <= dateRange.e.r; ++R) {
            for (var C = 4; C <= 6; ++C) { // Columns E, F, G (amounts)
                var amountCell = XLSX.utils.encode_cell({r: R, c: C});
                if (ws[amountCell] && ws[amountCell].v) {
                    ws[amountCell].z = '#,##0.00';
                }
            }
        }
        
        // Add the worksheet to the workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Sales Report');
        
        // Generate filename with current date
        var today = new Date();
        var filename = 'sales_report_' + today.toISOString().split('T')[0] + '.xlsx';
        
        // Save the file
        XLSX.writeFile(wb, filename);
    });

    $(document).ready(function() {
        // Remove any existing DataTable instance
        if ($.fn.DataTable.isDataTable('#bookingsTable')) {
            $('#bookingsTable').DataTable().destroy();
        }
        
        // Initialize DataTable
        $('#bookingsTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            "searching": true,
            "lengthChange": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "order": [[3, 'desc']], // Sort by date column
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "emptyTable": "No bookings found for this period",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "columnDefs": [
                {
                    "targets": [3, 4, 5], // Amount columns (now one column earlier due to removed columns)
                    "className": 'text-right'
                }
            ]
        });
    });
    </script>

    <style>
    .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-title {
        margin-bottom: 1.25rem;
        font-size: 1.25rem;
        color: #333;
        font-weight: 600;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .bg-primary { 
        background-color: #DAA520 !important; 
    }

    .text-white {
        color: white !important;
    }

    .summary-cards h3 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .summary-cards h5 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .badge {
        padding: 0.4em 0.6em;
        font-size: 85%;
        font-weight: 500;
        border-radius: 0.25rem;
        display: inline-block;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: bold !important;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }

    .table th, 
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        text-align: left;
        border-top: 1px solid #dee2e6;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .main {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        .card {
            break-inside: avoid;
        }
    }

    /* Enhanced chart styles */
    #revenueChart {
        height: 350px !important;
        margin: 1rem 0;
    }

    #distributionChart {
        height: 300px !important;
        margin: 1rem 0;
        max-width: 100%;
    }

    .chart-container {
        position: relative;
        margin: auto;
        height: 100%;
    }

    /* Enhanced form styles */
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, 
                    border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .btn-success {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }

    /* Layout improvements */
    .mb-3, .my-3 {
        margin-bottom: 1rem !important;
    }

    .mb-4, .my-4 {
        margin-bottom: 1.5rem !important;
    }

    .mt-4, .my-4 {
        margin-top: 1.5rem !important;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-4, .col-md-8, .col-md-12 {
        position: relative;
        width: 100%;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
    }

    @media (min-width: 768px) {
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* Custom header style */
    .panel-heading {
        padding: 15px;
        border-bottom: 1px solid #ddd;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .pull-right {
        float: right !important;
    }

    /* Enhanced tooltip styles */
    .chartjs-tooltip {
        opacity: 0;
        position: absolute;
        background: rgba(0, 0, 0, 0.85);
        color: white;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 500;
        pointer-events: none;
        transform: translate(-50%, 0);
        transition: all 0.1s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper {
        padding: 0;
        margin: 0;
    }

    .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_filter input {
        margin-left: 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
    }

    .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        margin: 0 0.5rem;
    }

    .dataTables_info {
        padding-top: 0.85em;
    }

    .dataTables_paginate {
        padding-top: 0.5rem;
    }

    .paginate_button {
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        border: 1px solid #dee2e6;
        background-color: #fff;
    }

    .paginate_button.current {
        background-color: #DAA520 !important;
        border-color: #DAA520 !important;
        color: white !important;
    }

    .paginate_button:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .table.dataTable {
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
    }

    .table.dataTable thead th {
        border-bottom: 2px solid #dee2e6;
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .table.dataTable tbody td {
        vertical-align: middle;
    }

    /* Ensure badges remain visible in DataTables */
    .badge {
        display: inline-block !important;
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 6px 30px 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-left: 5px;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 2px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #DAA520;
        color: white !important;
        border: 1px solid #DAA520;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f0f0f0;
        color: #333 !important;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
    }

    /* Remove the total row from DataTable processing */
    tr.table-info {
        display: none;
    }
    </style>
</body>
</html>