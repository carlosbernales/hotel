<?php
require_once('db.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get filter parameters
$filter_start = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$filter_end = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;
$order_type = isset($_GET['order_type']) && $_GET['order_type'] !== 'all' ? $_GET['order_type'] : null;
$payment_method = isset($_GET['payment_method']) && $_GET['payment_method'] !== 'all' ? $_GET['payment_method'] : null;

// Format dates for SQL query with time
$startDateTime = $filter_start ? $filter_start . ' 00:00:00' : null;
$endDateTime = $filter_end ? $filter_end . ' 23:59:59' : null;

// Build the order type condition
$order_type_condition = "";
if ($order_type !== null) {
    $order_type_condition = "AND o.order_type = ?";
}

// First, create the sales table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";

try {
    $conn->query($create_table_sql);
    
    // Check if order_date column exists, if not add it
    $check_column = "SHOW COLUMNS FROM sales LIKE 'order_date'";
    $column_exists = $conn->query($check_column);
    
    if ($column_exists->num_rows === 0) {
        $add_column = "ALTER TABLE sales ADD COLUMN order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $conn->query($add_column);
        
        // Update existing records to copy created_at to order_date
        $update_dates = "UPDATE sales SET order_date = created_at WHERE order_date IS NULL";
        $conn->query($update_dates);
    }
} catch (Exception $e) {
    error_log("Error modifying sales table: " . $e->getMessage());
}

// Query for daily sales with filters
$dailySalesQuery = "SELECT COALESCE(SUM(total_amount), 0) as daily_total 
                    FROM orders 
                    WHERE status = 'finished'";
if ($filter_start) {
    $dailySalesQuery .= " AND DATE(order_date) = ?";
} else {
    $dailySalesQuery .= " AND DATE(order_date) = CURDATE()";
}

// Add filters
if ($order_type) {
    if ($order_type === 'online') {
        $dailySalesQuery .= " AND order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $dailySalesQuery .= " AND order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $dailySalesQuery .= " AND order_type = 'walk-in'";
    } else {
        $dailySalesQuery .= " AND order_type = ?";
    }
}
if ($payment_method) {
    $dailySalesQuery .= " AND payment_method = ?";
}

// Query for weekly sales with filters
$weeklySalesQuery = "SELECT COALESCE(SUM(total_amount), 0) as weekly_total 
                     FROM orders 
                     WHERE status = 'finished'";
if ($filter_start && $filter_end) {
    $weeklySalesQuery .= " AND DATE(order_date) BETWEEN ? AND ?";
} else {
    $weeklySalesQuery .= " AND YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)";
}

// Add filters
if ($order_type) {
    if ($order_type === 'online') {
        $weeklySalesQuery .= " AND order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $weeklySalesQuery .= " AND order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $weeklySalesQuery .= " AND order_type = 'walk-in'";
    } else {
        $weeklySalesQuery .= " AND order_type = ?";
    }
}
if ($payment_method) {
    $weeklySalesQuery .= " AND payment_method = ?";
}

// Query for monthly sales with filters
$monthlySalesQuery = "SELECT COALESCE(SUM(total_amount), 0) as monthly_total 
                      FROM orders 
                      WHERE status = 'finished'";
if ($filter_start && $filter_end) {
    $monthlySalesQuery .= " AND DATE(order_date) BETWEEN ? AND ?";
} else {
    $monthlySalesQuery .= " AND YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE())";
}

// Add filters
if ($order_type) {
    if ($order_type === 'online') {
        $monthlySalesQuery .= " AND order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $monthlySalesQuery .= " AND order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $monthlySalesQuery .= " AND order_type = 'walk-in'";
    } else {
        $monthlySalesQuery .= " AND order_type = ?";
    }
}
if ($payment_method) {
    $monthlySalesQuery .= " AND payment_method = ?";
}

// Query for annual sales with filters
$annualSalesQuery = "SELECT COALESCE(SUM(total_amount), 0) as annual_total 
                     FROM orders 
                     WHERE status = 'finished'";
if ($filter_start) {
    $annualSalesQuery .= " AND YEAR(order_date) = YEAR(?)";
} else {
    $annualSalesQuery .= " AND YEAR(order_date) = YEAR(CURDATE())";
}

// Add filters
if ($order_type) {
    if ($order_type === 'online') {
        $annualSalesQuery .= " AND order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $annualSalesQuery .= " AND order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $annualSalesQuery .= " AND order_type = 'walk-in'";
    } else {
        $annualSalesQuery .= " AND order_type = ?";
    }
}
if ($payment_method) {
    $annualSalesQuery .= " AND payment_method = ?";
}

// Query for online orders (regular orders)
$onlineOrdersQuery = "SELECT COALESCE(SUM(total_amount), 0) as online_total 
                     FROM orders 
                     WHERE status = 'finished' 
                     AND order_type = 'regular'";
if ($filter_start && $filter_end) {
    $onlineOrdersQuery .= " AND DATE(order_date) >= DATE(?) AND DATE(order_date) <= DATE(?)";
}

// Query for walk-in orders
$walkinOrdersQuery = "SELECT COALESCE(SUM(total_amount), 0) as walkin_total 
                     FROM orders 
                     WHERE status = 'finished' 
                     AND order_type = 'walk-in'";
if ($filter_start && $filter_end) {
    $walkinOrdersQuery .= " AND DATE(order_date) >= DATE(?) AND DATE(order_date) <= DATE(?)";
}

// Query for advance orders
$advanceOrdersQuery = "SELECT COALESCE(SUM(total_amount), 0) as advance_total 
                      FROM orders 
                      WHERE status = 'finished' 
                      AND order_type = 'advance'";
if ($filter_start && $filter_end) {
    $advanceOrdersQuery .= " AND DATE(order_date) >= DATE(?) AND DATE(order_date) <= DATE(?)";
}

// Query for gross sales
$grossSalesQuery = "SELECT 
    COALESCE(SUM(total_amount + discount_amount), 0) as gross_sales,
    COALESCE(SUM(discount_amount), 0) as total_discounts,
    COALESCE(SUM(total_amount), 0) as net_sales,
    COUNT(*) as transaction_count
    FROM orders 
    WHERE status = 'finished'";

if ($filter_start && $filter_end) {
    $grossSalesQuery .= " AND DATE(order_date) BETWEEN ? AND ?";
} else if ($filter_start) {
    $grossSalesQuery .= " AND DATE(order_date) = ?";
}

// Add order type filter
if ($order_type) {
    if ($order_type === 'online') {
        $grossSalesQuery .= " AND order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $grossSalesQuery .= " AND order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $grossSalesQuery .= " AND order_type = 'walk-in'";
    }
}

// Add payment method filter
if ($payment_method) {
    $grossSalesQuery .= " AND payment_method = ?";
}

// Execute queries and store results
try {
    // Daily sales
    $stmt = $conn->prepare($dailySalesQuery);
    if ($filter_start) {
        $stmt->bind_param("s", $filter_start);
    }
    $stmt->execute();
    $dailyResult = $stmt->get_result();
    $dailySales = $dailyResult->fetch_assoc()['daily_total'];

    // Add this after executing the daily sales query
    error_log("Daily Sales Query: " . $dailySalesQuery);
    error_log("Daily Sales Result: " . $dailySales);

    // Weekly sales
    $stmt = $conn->prepare($weeklySalesQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    }
    $stmt->execute();
    $weeklyResult = $stmt->get_result();
    $weeklySales = $weeklyResult->fetch_assoc()['weekly_total'];

    // Add this after executing the weekly sales query
    error_log("Weekly Sales Query: " . $weeklySalesQuery);
    error_log("Weekly Sales Result: " . $weeklySales);

    // Monthly sales
    $stmt = $conn->prepare($monthlySalesQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    }
    $stmt->execute();
    $monthlyResult = $stmt->get_result();
    $monthlySales = $monthlyResult->fetch_assoc()['monthly_total'];

    // Add this after executing the monthly sales query
    error_log("Monthly Sales Query: " . $monthlySalesQuery);
    error_log("Monthly Sales Result: " . $monthlySales);

    // Annual sales
    $stmt = $conn->prepare($annualSalesQuery);
    if ($filter_start) {
        $stmt->bind_param("s", $filter_start);
    }
    $stmt->execute();
    $annualResult = $stmt->get_result();
    $annualSales = $annualResult->fetch_assoc()['annual_total'];

    // Add this after executing the annual sales query
    error_log("Annual Sales Query: " . $annualSalesQuery);
    error_log("Annual Sales Result: " . $annualSales);

    // Online orders
    $stmt = $conn->prepare($onlineOrdersQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    }
    $stmt->execute();
    $onlineResult = $stmt->get_result();
    $onlineSales = $onlineResult->fetch_assoc()['online_total'];

    // Walk-in orders
    $stmt = $conn->prepare($walkinOrdersQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    }
    $stmt->execute();
    $walkinResult = $stmt->get_result();
    $walkinSales = $walkinResult->fetch_assoc()['walkin_total'];

    // Advance orders
    $stmt = $conn->prepare($advanceOrdersQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    }
    $stmt->execute();
    $advanceResult = $stmt->get_result();
    $advanceSales = $advanceResult->fetch_assoc()['advance_total'];

    // Gross sales
    $stmt = $conn->prepare($grossSalesQuery);
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    } else if ($filter_start) {
        $stmt->bind_param("s", $filter_start);
    }
    
    $stmt->execute();
    $grossResult = $stmt->get_result();
    $salesData = $grossResult->fetch_assoc();
    
    $grossSales = $salesData['gross_sales'] ?? 0;
    $totalDiscounts = $salesData['total_discounts'] ?? 0;
    $netSales = $salesData['net_sales'] ?? 0;
    $transactionCount = $salesData['transaction_count'] ?? 0;
    
    // Add debugging
    error_log("Filter dates: Start = $filter_start, End = $filter_end");
    error_log("Gross Sales Query: $grossSalesQuery");
    error_log("Sales Data: " . print_r($salesData, true));
    
    // Add error logging to help debug
    if ($onlineSales === null || $walkinSales === null || $advanceSales === null) {
        error_log("Sales query returned null: Online: $onlineSales, Walk-in: $walkinSales, Advance: $advanceSales");
    }

} catch (Exception $e) {
    error_log("Error calculating sales totals: " . $e->getMessage());
    $dailySales = 0;
    $weeklySales = 0;
    $monthlySales = 0;
    $annualSales = 0;
    $onlineSales = 0;
    $walkinSales = 0;
    $advanceSales = 0;
    $grossSales = 0;
    $totalDiscounts = 0;
    $netSales = 0;
    $transactionCount = 0;
}

// Add this debugging code temporarily after the queries
error_log("Filter dates: Start = $filter_start, End = $filter_end");
error_log("Sales amounts: Online = $onlineSales, Walk-in = $walkinSales, Advance = $advanceSales");

// Update the detailed sales query section
$detailedSalesQuery = "SELECT o.id,
                              o.total_amount,
                              o.payment_method,
                              o.order_date,
                              o.order_type,
                              o.status,
                              o.change_amount,
                              CONCAT(u.first_name, ' ', u.last_name) as processed_by,
                              GROUP_CONCAT(
                                  CONCAT(
                                      oi.quantity,
                                      'x ',
                                      oi.item_name
                                  ) SEPARATOR ', '
                              ) as items
                       FROM orders o
                       LEFT JOIN order_items oi ON o.id = oi.order_id
                       LEFT JOIN userss u ON o.user_id = u.id
                       WHERE o.status = 'finished'";

// Only add date filter if both dates are set
if ($filter_start && $filter_end) {
    $detailedSalesQuery .= " AND DATE(o.order_date) BETWEEN ? AND ?";
}

// Add payment method filter with specific handling
if ($payment_method) {
    switch($payment_method) {
        case 'gcash':
            $detailedSalesQuery .= " AND o.payment_method = 'gcash'";
            break;
        case 'maya':
            $detailedSalesQuery .= " AND o.payment_method = 'maya'";
            break;
        case 'bank':
            $detailedSalesQuery .= " AND o.payment_method = 'bank'";
            break;
        case 'cash':
            $detailedSalesQuery .= " AND o.payment_method = 'cash'";
            break;
    }
}

// Add order type filter
if ($order_type) {
    if ($order_type === 'online') {
        $detailedSalesQuery .= " AND o.order_type = 'regular'";
    } else if ($order_type === 'advance') {
        $detailedSalesQuery .= " AND o.order_type = 'advance'";
    } else if ($order_type === 'walkin') {
        $detailedSalesQuery .= " AND o.order_type = 'walk-in'";
    } else {
        $detailedSalesQuery .= " AND o.order_type = ?";
    }
}

$detailedSalesQuery .= " GROUP BY o.id, o.total_amount, o.payment_method, o.order_date, o.order_type, o.status
                         ORDER BY o.order_date DESC";

// Execute the detailed sales query
try {
    $stmt = $conn->prepare($detailedSalesQuery);
    
    if ($filter_start && $filter_end) {
        $stmt->bind_param("ss", $filter_start, $filter_end);
    } else if ($filter_start) {
        $stmt->bind_param("s", $filter_start);
    }
    
    $stmt->execute();
    $detailedResult = $stmt->get_result();
    
} catch (Exception $e) {
    error_log("Error executing detailed sales query: " . $e->getMessage());
    $detailedResult = false;
}

// Return JSON if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $response = [
        'daily_sales' => $dailySales,
        'weekly_sales' => $weeklySales,
        'monthly_sales' => $monthlySales,
        'annual_sales' => $annualSales,
        'online_sales' => $onlineSales,
        'walkin_sales' => $walkinSales,
        'advance_sales' => $advanceSales,
        'detailed_sales' => []
    ];
    
    while ($row = $detailedResult->fetch_assoc()) {
        $response['detailed_sales'][] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Replace the PDF generation code with a download link
if (isset($_POST['generate_report'])) {
    $_SESSION['report_start_date'] = $filter_start;
    $_SESSION['report_end_date'] = $filter_end;
    $_SESSION['report_data'] = [
        'orders' => $detailedResult,
        'daily_total' => $dailySales,
        'monthly_total' => $monthlySales,
        'annual_total' => $annualSales
    ];
    
    // Set success message with download link
    $success_message = "Report data ready. <a href='download_report.php' class='btn btn-sm btn-primary'>Download PDF</a>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Report - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <style>
        /* Sidebar and main content layout */
        .main-content {
            transition: all 0.3s ease;
            margin-left: 200px;
            padding: 20px;
            width: auto;
            min-height: 100vh;
            background: linear-gradient(135deg, #f1e5c4 0%, #e6d5b8 100%);
        }

        /* When sidebar is collapsed */
        body.sidebar-collapsed .main-content {
            margin-left: 50px;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 50px;
            }
        }

        /* Updated card styles */
        .sales-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .sales-card:hover {
            transform: translateY(-5px);
        }

        .sales-card .card-title {
            color: #666;
            margin: 0;
            font-size: 16px;
        }

        .sales-card .card-text {
            color: #333;
            margin: 10px 0 0 0;
            font-size: 24px;
            font-weight: bold;
        }

        /* Updated table styles */
        .table-responsive {
            background: linear-gradient(145deg, #ffffff, #f8f3e8);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(218, 165, 32, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(145deg, #DAA520, #B8860B);
            color: white;
            border-bottom: none;
            padding: 12px;
        }

        .table tbody td {
            padding: 12px;
            border-color: rgba(218, 165, 32, 0.1);
        }

        /* Filter panel styles */
        .panel {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .panel-body {
            padding: 20px;
        }

        /* Form control styles */
        .form-control {
            border: 1px solid rgba(218, 165, 32, 0.2);
            border-radius: 6px;
            padding: 8px 12px;
        }

        .form-control:focus {
            border-color: #DAA520;
            box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
        }

        /* Button styles */
        .btn-primary {
            background: linear-gradient(145deg, #DAA520, #B8860B);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #B8860B, #8B6914);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(145deg, #6c757d, #5a6268);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(145deg, #5a6268, #4e555b);
            transform: translateY(-2px);
        }

        /* Page header styles */
        .page-header {
            border-bottom: none;
            margin: 0;
            padding: 0;
            font-size: 24px;
            font-weight: bold;
        }

        /* DataTable custom styles */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(145deg, #DAA520, #B8860B);
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(145deg, #B8860B, #8B6914);
            color: white !important;
            border: none;
        }

        /* Add styles for notification and profile dropdowns */
        .dropdown-menu {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.1);
        }

        .dropdown-menu .dropdown-item {
            padding: 8px 16px;
            color: #333;
        }

        .dropdown-menu .dropdown-item:hover {
            background: #f8f9fa;
        }

        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 10px;
        }

        /* Profile dropdown toggle */
        .profile-dropdown {
            cursor: pointer;
        }

        /* Add these to your existing styles */
        .sales-summary {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item h5 {
            color: #666;
            margin: 0;
            font-size: 16px;
        }

        .summary-item h3 {
            color: #333;
            margin: 10px 0 0 0;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
        
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Sales Report</h1>
            </div>
        </div>

        <!-- Date Filter Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inline">
                            <div class="row mb-3">
                                <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date:</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?php echo $filter_start ? htmlspecialchars($filter_start) : ''; ?>">
                            </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                <label>End Date:</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?php echo $filter_end ? htmlspecialchars($filter_end) : ''; ?>">
                            </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                <label>Order Type:</label>
                                <select name="order_type" class="form-control">
                                            <option value="all">All Orders</option>
                                            <option value="online" <?php echo ($order_type === 'online') ? 'selected' : ''; ?>>Online Orders</option>
                                            <option value="advance" <?php echo ($order_type === 'advance') ? 'selected' : ''; ?>>Advance Orders</option>
                                            <option value="walkin" <?php echo ($order_type === 'walkin') ? 'selected' : ''; ?>>Walk-in Orders</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Method:</label>
                                        <select name="payment_method" class="form-control">
                                            <option value="all">All Methods</option>
                                            <option value="gcash" <?php echo ($payment_method === 'gcash') ? 'selected' : ''; ?>>GCash</option>
                                            <option value="maya" <?php echo ($payment_method === 'maya') ? 'selected' : ''; ?>>Maya</option>
                                            <option value="bank" <?php echo ($payment_method === 'bank') ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="cash" <?php echo ($payment_method === 'cash') ? 'selected' : ''; ?>>Cash</option>
                                </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Apply Filters
                            </button>
                                    <a href="sales.php" class="btn btn-secondary">
                                        <i class="fa fa-refresh"></i> Reset Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success" style="margin-top: 10px;">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Add this after the filter form, inside the panel-body div -->
                        <div class="row">
                            <div class="col-md-12">
                                <form method="POST" action="generate_report.php" id="report-form" class="mt-3">
                                    <input type="hidden" name="start_date" value="<?php echo $filter_start; ?>">
                                    <input type="hidden" name="end_date" value="<?php echo $filter_end; ?>">
                                    <input type="hidden" name="order_type" value="<?php echo $order_type; ?>">
                                    <input type="hidden" name="payment_method" value="<?php echo $payment_method; ?>">

                                    <button type="submit" name="generate_report" class="btn btn-outline-primary ml-2">
                                        <i class="fa fa-file-pdf-o"></i> Export All to PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Cards -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="page-header" style="margin-top: 0;">Sales</h2>
                                <p style="color: #666;">
                                    <?php 
                                    if (!empty($filter_start)) {
                                        echo date('F d, Y', strtotime($filter_start)); 
                                    } else {
                                        echo date('F d, Y');
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="sales-summary">
                                    <div class="summary-item">
                                        <h5>Gross Sales</h5>
                                        <h3>₱<?php echo number_format($grossSales, 2); ?></h3>
                                    </div>
                                    <div class="summary-item">
                                        <h5>Discounts</h5>
                                        <h3>₱<?php echo number_format($totalDiscounts, 2); ?></h3>
                                    </div>
                                    <div class="summary-item">
                                        <h5>Net Sales</h5>
                                        <h3>₱<?php echo number_format($netSales, 2); ?></h3>
                                    </div>
                                    <div class="summary-item">
                                        <h5>Transaction Count</h5>
                                        <h3><?php echo $transactionCount; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Sales Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Daily Sales</h5>
                    <h3 class="card-text">₱<?php echo number_format($dailySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Weekly Sales</h5>
                    <h3 class="card-text">₱<?php echo number_format($weeklySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Monthly Sales</h5>
                    <h3 class="card-text">₱<?php echo number_format($monthlySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Annual Sales</h5>
                    <h3 class="card-text">₱<?php echo number_format($annualSales, 2); ?></h3>
                </div>
            </div>
        </div>

        <!-- Online, Walk-in, and Advance Sales -->
        <div class="row">
            <div class="col-md-4">
                <div class="sales-card">
                    <h5 class="card-title">Online Orders</h5>
                    <h3 class="card-text">₱ <?php echo number_format($onlineSales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sales-card">
                    <h5 class="card-title">Walk-in Orders</h5>
                    <h3 class="card-text">₱ <?php echo number_format($walkinSales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sales-card">
                    <h5 class="card-title">Advance Orders</h5>
                    <h3 class="card-text">₱ <?php echo number_format($advanceSales, 2); ?></h3>
                </div>
            </div>
        </div>

        <!-- Sales Details Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                        <th>Order Type</th>
                                        <th>Status</th>
                                        <th>Change Amount</th>
                                        <th>Cashier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($detailedResult && $detailedResult->num_rows > 0) {
                                        while ($row = $detailedResult->fetch_assoc()) { 
                                    ?>
                                        <tr>
                                            
                                            <td><?php echo htmlspecialchars($row['items']); ?></td>
                                            <td>₱ <?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td><?php 
                                                $payment = htmlspecialchars($row['payment_method']);
                                                switch(strtolower($payment)) {
                                                    case 'gcash':
                                                        echo 'GCash';
                                                        break;
                                                    case 'maya':
                                                        echo 'Maya';
                                                        break;
                                                    case 'bank':
                                                        echo 'Bank Transfer';
                                                        break;
                                                    case 'cash':
                                                        echo 'Cash';
                                                        break;
                                                    default:
                                                        echo $payment;
                                                }
                                            ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($row['order_type'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                            <td>₱ <?php echo number_format($row['change_amount'], 2); ?></td>

                                            <td><?php echo htmlspecialchars($row['processed_by'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php 
                                        }
                                    } else { 
                                    ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No data available for the selected filters</td>
                                        </tr>
                                    <?php 
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/chart-data.js"></script>
    <script src="js/easypiechart.js"></script>
    <script src="js/easypiechart-data.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/custom.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                "order": [[ 4, "desc" ]], // Updated column index due to new checkbox column
                "pageLength": 25
            });

            // Initialize date inputs with zeros instead of current date
            if (!$('input[name="start_date"]').val()) {
                $('input[name="start_date"]').val('00000000');
            }
            if (!$('input[name="end_date"]').val()) {
                $('input[name="end_date"]').val('00000000');
            }

            // Validate date range
            $('form').on('submit', function(e) {
                var startDate = new Date($('input[name="start_date"]').val());
                var endDate = new Date($('input[name="end_date"]').val());
                
                if (startDate > endDate) {
                    e.preventDefault();
                    alert('Start date cannot be later than end date');
                    return false;
                }
            });

            // Updated sidebar toggle handler
            $('.sidebar-toggle').on('click', function(e) {
                e.preventDefault();
                $('body').toggleClass('sidebar-collapsed');
                
                // Optional: Save state to localStorage
                localStorage.setItem('sidebarState', 
                    $('body').hasClass('sidebar-collapsed') ? 'collapsed' : 'expanded'
                );
            });

            // Check saved state on page load
            if (localStorage.getItem('sidebarState') === 'collapsed') {
                $('body').addClass('sidebar-collapsed');
            }

            // Improved responsive handling
            function handleResize() {
                if ($(window).width() < 768) {
                    $('body').addClass('sidebar-collapsed');
                } else {
                    // Only remove class if it wasn't explicitly collapsed by user
                    if (localStorage.getItem('sidebarState') !== 'collapsed') {
                        $('body').removeClass('sidebar-collapsed');
                    }
                }
            }

            // Handle window resize
            $(window).resize(function() {
                handleResize();
            });

            // Initial check on page load
            handleResize();

            // Enable Bootstrap dropdowns
            $('.dropdown-toggle').dropdown();

            // Handle notification clicks
            $('.notification-toggle').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).parent().toggleClass('show');
                $(this).next('.dropdown-menu').toggleClass('show');
            });

            // Handle profile dropdown
            $('.profile-dropdown').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).parent().toggleClass('show');
                $(this).next('.dropdown-menu').toggleClass('show');
            });

            // Close dropdowns when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                    $('.dropdown').removeClass('show');
                }
            });

            // Fetch notifications (if you have an endpoint for this)
            function fetchNotifications() {
                $.ajax({
                    url: 'get_notifications.php',
                    method: 'GET',
                    success: function(response) {
                        if(response.count > 0) {
                            $('.notification-badge').text(response.count).show();
                        } else {
                            $('.notification-badge').hide();
                        }
                        // Update notification dropdown content if needed
                    }
                });
            }

            // Fetch notifications periodically
            setInterval(fetchNotifications, 30000); // Every 30 seconds
            fetchNotifications(); // Initial fetch

            // Handle select all checkbox
            $('#select-all').on('change', function() {
                $('.order-select').prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });
            
            // Handle individual checkboxes
            $(document).on('change', '.order-select', function() {
                updateSelectedCount();
                
                // Update select all checkbox state
                if ($('.order-select:checked').length === $('.order-select').length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });
            
            // Update the selected count and enable/disable button
            function updateSelectedCount() {
                const selectedCount = $('.order-select:checked').length;
                const $btn = $('#generate-selected-report');
                
                if (selectedCount > 0) {
                    $btn.prop('disabled', false);
                    $btn.text(`Export Selected (${selectedCount}) to PDF`);
                } else {
                    $btn.prop('disabled', true);
                    $btn.text('Export Selected to PDF');
                }
            }
            
            // Handle the export selected button click
            $('#generate-selected-report').on('click', function() {
                // Get all selected order IDs
                const selectedIds = [];
                $('.order-select:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });
                
                if (selectedIds.length === 0) {
                    alert('Please select at least one order to export.');
                    return;
                }
                
                // Add the selected order IDs to the form
                $('#report-form').append('<input type="hidden" name="selected_orders" value="' + selectedIds.join(',') + '">');
                $('#report-form').submit();
            });
        });
    </script>
</body>
</html>
