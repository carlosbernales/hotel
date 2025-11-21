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
$filter_start = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$filter_end = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$order_type = isset($_GET['order_type']) && $_GET['order_type'] !== 'all' ? $_GET['order_type'] : null;
$payment_method = isset($_GET['payment_method']) && $_GET['payment_method'] !== 'all' ? $_GET['payment_method'] : null;

// Format dates for SQL query with time
$startDateTime = $filter_start . ' 00:00:00';
$endDateTime = $filter_end . ' 23:59:59';

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
                        WHERE status = 'finished' 
                        AND DATE(order_date) = ?";

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
                     WHERE status = 'finished' 
                     AND DATE(order_date) BETWEEN ? AND ?";

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
                      WHERE status = 'finished' 
                      AND DATE(order_date) BETWEEN ? AND ?";

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
                     WHERE status = 'finished' 
                     AND YEAR(order_date) = YEAR(?)";

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

// Execute queries and store results
try {
    // Daily sales
    $stmt = $conn->prepare($dailySalesQuery);
    
    // Create parameter array for daily sales
    $daily_params = array($filter_start);
    $daily_types = "s";
    
    if ($order_type && $order_type !== 'online' && $order_type !== 'advance' && $order_type !== 'walkin') {
        $daily_params[] = $order_type;
        $daily_types .= "s";
    }
    if ($payment_method) {
        $daily_params[] = $payment_method;
        $daily_types .= "s";
    }
    
    $stmt->bind_param($daily_types, ...$daily_params);
    $stmt->execute();
    $dailyResult = $stmt->get_result();
    $dailySales = $dailyResult->fetch_assoc()['daily_total'];

    // Weekly sales
    $stmt = $conn->prepare($weeklySalesQuery);
    
    // Create parameter array for weekly sales
    $weekly_params = array($filter_start, $filter_end);
    $weekly_types = "ss";
    
    if ($order_type && $order_type !== 'online' && $order_type !== 'advance' && $order_type !== 'walkin') {
        $weekly_params[] = $order_type;
        $weekly_types .= "s";
    }
    if ($payment_method) {
        $weekly_params[] = $payment_method;
        $weekly_types .= "s";
    }
    
    $stmt->bind_param($weekly_types, ...$weekly_params);
    $stmt->execute();
    $weeklyResult = $stmt->get_result();
    $weeklySales = $weeklyResult->fetch_assoc()['weekly_total'];

    // Monthly sales
    $stmt = $conn->prepare($monthlySalesQuery);
    
    // Create parameter array for monthly sales
    $monthly_params = array($filter_start, $filter_end);
    $monthly_types = "ss";
    
    if ($order_type && $order_type !== 'online' && $order_type !== 'advance' && $order_type !== 'walkin') {
        $monthly_params[] = $order_type;
        $monthly_types .= "s";
    }
    if ($payment_method) {
        $monthly_params[] = $payment_method;
        $monthly_types .= "s";
    }
    
    $stmt->bind_param($monthly_types, ...$monthly_params);
    $stmt->execute();
    $monthlyResult = $stmt->get_result();
    $monthlySales = $monthlyResult->fetch_assoc()['monthly_total'];

    // Annual sales
    $stmt = $conn->prepare($annualSalesQuery);
    
    // Create parameter array for annual sales
    $annual_params = array($filter_start);
    $annual_types = "s";
    
    if ($order_type && $order_type !== 'online' && $order_type !== 'advance' && $order_type !== 'walkin') {
        $annual_params[] = $order_type;
        $annual_types .= "s";
    }
    if ($payment_method) {
        $annual_params[] = $payment_method;
        $annual_types .= "s";
    }
    
    $stmt->bind_param($annual_types, ...$annual_params);
    $stmt->execute();
    $annualResult = $stmt->get_result();
    $annualSales = $annualResult->fetch_assoc()['annual_total'];

} catch (Exception $e) {
    error_log("Error calculating sales totals: " . $e->getMessage());
    $dailySales = 0;
    $weeklySales = 0;
    $monthlySales = 0;
    $annualSales = 0;
}

// Update the detailed sales query section
$detailedSalesQuery = "SELECT o.id,
                              o.total_amount,
                              o.payment_method,
                              o.order_date,
                              o.order_type,
                              o.status,
                              GROUP_CONCAT(
                                  CONCAT(
                                      oi.quantity,
                                      'x ',
                                      oi.item_name
                                  ) SEPARATOR ', '
                              ) as items
                       FROM orders o
                       LEFT JOIN order_items oi ON o.id = oi.order_id
                       WHERE o.status = 'finished'
                       AND DATE(o.order_date) BETWEEN ? AND ?";

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
    
    // Initialize parameters array
    $params = array($filter_start, $filter_end);
    $types = "ss";
    
    // Add order type parameter if set and not online/advance/walkin
    if ($order_type && $order_type !== 'online' && $order_type !== 'advance' && $order_type !== 'walkin') {
        $params[] = $order_type;
        $types .= "s";
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $detailedResult = $stmt->get_result();
    
} catch (Exception $e) {
    error_log("Error executing detailed sales query: " . $e->getMessage());
    $detailedResult = false;
}

// Sales history query
$sql = "SELECT o.id, o.total_amount, o.payment_method, o.order_date, o.status, o.order_type,
               GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.item_name) SEPARATOR ', ') as items
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.status = 'finished'
        GROUP BY o.id, o.total_amount, o.payment_method, o.order_date, o.status, o.order_type
        ORDER BY o.order_date DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error loading sales data: " . htmlspecialchars($e->getMessage()) . "</div>";
    $result = false;
}

// Return JSON if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $response = [
        'daily_sales' => $dailySales,
        'weekly_sales' => $weeklySales,
        'monthly_sales' => $monthlySales,
        'annual_sales' => $annualSales,
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
        .sales-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 15px;
        }
        .card-title {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .card-text {
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        .table-responsive {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
        
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
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
                                       value="<?php echo htmlspecialchars($filter_start); ?>" required>
                            </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                <label>End Date:</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?php echo htmlspecialchars($filter_end); ?>" required>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Daily Sales</h5>
                    <h3 class="card-text">₱ <?php echo number_format($dailySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Weekly Sales</h5>
                    <h3 class="card-text">₱ <?php echo number_format($weeklySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Monthly Sales</h5>
                    <h3 class="card-text">₱ <?php echo number_format($monthlySales, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sales-card">
                    <h5 class="card-title">Annual Sales</h5>
                    <h3 class="card-text">₱ <?php echo number_format($annualSales, 2); ?></h3>
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
                                        </tr>
                                    <?php 
                                        }
                                    } else { 
                                    ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No data available for the selected filters</td>
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

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Sales History
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_sales = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $total_sales += $row['total_amount'];
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['items']); ?></td>
                                        <td>₱<?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total Sales:</th>
                                    <th colspan="3">₱<?php echo number_format($total_sales, 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
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
                "order": [[ 3, "desc" ]], // Sort by date column
                "pageLength": 25
            });

            // Initialize date inputs with current date if not set
            var today = new Date().toISOString().split('T')[0];
            if (!$('input[name="start_date"]').val()) {
                $('input[name="start_date"]').val(today);
            }
            if (!$('input[name="end_date"]').val()) {
                $('input[name="end_date"]').val(today);
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
        });
    </script>
</body>
</html>
