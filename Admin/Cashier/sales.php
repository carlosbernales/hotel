<?php
// Include database connection
require_once 'db.php';

// Handle export request
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    try {
        // Get date range from GET parameters
        $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
        $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';
        
        // Prepare date conditions for queries
        $dateCondition = '';
        if ($fromDate && $toDate) {
            $fromDateTime = $fromDate . ' 00:00:00';
            $toDateTime = $toDate . ' 23:59:59';
            $dateCondition = " AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
        } else {
            // Default to last 30 days if no filter
            $dateCondition = " AND date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        // Fetch sales data for export
        $sql = "
            SELECT 
                order_id,
                firstname,
                lastname,
                total,
                status,
                date_time,
                CASE 
                    WHEN payment_method IS NOT NULL THEN payment_method
                    ELSE 'N/A'
                END as payment_method
            FROM orders_table 
            WHERE status = 'Completed'
            $dateCondition
            ORDER BY date_time DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $salesData = $stmt->fetchAll();
        
        // If no data found, create empty CSV with message
        if (empty($salesData)) {
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d_H-i-s') . '.csv"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Sales Report']);
            fputcsv($output, ['No sales data found for the selected period']);
            
            if ($fromDate && $toDate) {
                fputcsv($output, ['Report Period', date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate))]);
            } else {
                fputcsv($output, ['Report Period', 'Last 30 Days']);
            }
            
            fclose($output);
            exit();
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'Order ID',
            'Customer Name',
            'Total Amount',
            'Payment Method',
            'Status',
            'Date & Time'
        ]);
        
        // Add data rows
        foreach ($salesData as $sale) {
            $customerName = trim($sale['firstname'] . ' ' . $sale['lastname']);
            if (empty($customerName)) {
                $customerName = 'Guest';
            }
            
            fputcsv($output, [
                $sale['order_id'],
                $customerName,
                number_format($sale['total'], 2),
                $sale['payment_method'],
                $sale['status'],
                date('Y-m-d H:i:s', strtotime($sale['date_time']))
            ]);
        }
        
        // Calculate and add summary row
        $totalSales = array_sum(array_column($salesData, 'total'));
        $totalOrders = count($salesData);
        
        fputcsv($output, []);
        fputcsv($output, ['SUMMARY REPORT']);
        fputcsv($output, ['Total Orders', $totalOrders]);
        fputcsv($output, ['Total Sales', '₱' . number_format($totalSales, 2)]);
        fputcsv($output, ['Average Order Value', '₱' . number_format($totalSales / max($totalOrders, 1), 2)]);
        
        if ($fromDate && $toDate) {
            fputcsv($output, ['Report Period', date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate))]);
        } else {
            fputcsv($output, ['Report Period', 'Last 30 Days']);
        }
        
        fclose($output);
        exit();
        
    } catch (Exception $e) {
        // Error handling - create error CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export_error_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Export Error']);
        fputcsv($output, ['Error Message', $e->getMessage()]);
        fputcsv($output, ['Time', date('Y-m-d H:i:s')]);
        fclose($output);
        exit();
    }
}

// Get date range from GET parameters
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Prepare date conditions for queries
$dateCondition = '';
if ($fromDate && $toDate) {
    $fromDateTime = $fromDate . ' 00:00:00';
    $toDateTime = $toDate . ' 23:59:59';
    $dateCondition = " AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
}

// Get sales data for dashboard
$todaySales = 0;
$weekSales = 0;
$monthSales = 0;
$yearSales = 0;
$totalOrders = 0;
$todayOrders = 0;

try {
    // If date filter is applied, calculate totals for the filtered period
    if ($fromDate && $toDate) {
        // Filtered period sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE status = 'Completed' AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $todaySales = $result['total'];
        $weekSales = $result['total'];
        $monthSales = $result['total'];
        $yearSales = $result['total'];
        
        // Filtered period orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'Completed' AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayOrders = $result['count'];
        $totalOrders = $result['count'];
        
        // Set comparison values to 0 for filtered data
        $yesterdaySales = 0;
        $lastWeekSales = 0;
        $lastMonthSales = 0;
        $lastYearSales = 0;
        $yesterdayOrders = 0;
    } else {
        // Today's sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE DATE(date_time) = CURDATE() AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $todaySales = $result['total'];
        
        // Yesterday's sales for comparison
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE DATE(date_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $yesterdaySales = $result['total'];
        
        // Week's sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE YEARWEEK(date_time) = YEARWEEK(CURDATE()) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $weekSales = $result['total'];
        
        // Last week's sales for comparison
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE YEARWEEK(date_time) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastWeekSales = $result['total'];
        
        // Month's sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE MONTH(date_time) = MONTH(CURDATE()) AND YEAR(date_time) = YEAR(CURDATE()) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $monthSales = $result['total'];
        
        // Last month's sales for comparison
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE MONTH(date_time) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(date_time) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastMonthSales = $result['total'];
        
        // Year's sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE YEAR(date_time) = YEAR(CURDATE()) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $yearSales = $result['total'];
        
        // Last year's sales for comparison
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) as total FROM orders_table WHERE YEAR(date_time) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastYearSales = $result['total'];
        
        // Total orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalOrders = $result['count'];
        
        // Today's orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE DATE(date_time) = CURDATE() AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayOrders = $result['count'];
        
        // Yesterday's orders for comparison
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE DATE(date_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status = 'Completed'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $yesterdayOrders = $result['count'];
    }
    
    // Fetch recent sales for the table
    $recentSales = [];
    try {
        $sql = "
            SELECT 
                o.order_id,
                o.firstname,
                o.lastname,
                o.total,
                o.status,
                o.date_time
            FROM orders_table o
            WHERE o.status = 'Completed'
            $dateCondition
            ORDER BY o.date_time DESC
            LIMIT 10
        ";
        
        // If no date filter is set, use the default 30-day filter
        if (!$fromDate && !$toDate) {
            $sql = "
                SELECT 
                    o.order_id,
                    o.firstname,
                    o.lastname,
                    o.total,
                    o.status,
                    o.date_time
                FROM orders_table o
                WHERE o.status = 'Completed'
                AND o.date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY o.date_time DESC
                LIMIT 10
            ";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $recentSales = $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error fetching recent sales: " . $e->getMessage());
    }
    
    // Fetch top selling items
    $topItems = [];
    try {
        $sql = "
            SELECT 
                oi.item_name as name,
                mi.image_path,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.unit_price) as total_revenue
            FROM order_items oi
            INNER JOIN orders_table o ON oi.order_fk_id = o.id
            LEFT JOIN menu_items mi ON oi.item_name = mi.name
            WHERE o.status = 'Completed'
            $dateCondition
            GROUP BY oi.item_name, mi.image_path
            ORDER BY total_quantity DESC
            LIMIT 5
        ";
        
        // If no date filter is set, use the default 30-day filter
        if (!$fromDate && !$toDate) {
            $sql = "
                SELECT 
                    oi.item_name as name,
                    mi.image_path,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.unit_price) as total_revenue
                FROM order_items oi
                INNER JOIN orders_table o ON oi.order_fk_id = o.id
                LEFT JOIN menu_items mi ON oi.item_name = mi.name
                WHERE o.status = 'Completed'
                AND o.date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY oi.item_name, mi.image_path
                ORDER BY total_quantity DESC
                LIMIT 5
            ";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $topItems = $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error fetching top items: " . $e->getMessage());
    }
    
    // Calculate percentage changes
    $todaySalesChange = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;
    $weekSalesChange = $lastWeekSales > 0 ? (($weekSales - $lastWeekSales) / $lastWeekSales) * 100 : 0;
    $monthSalesChange = $lastMonthSales > 0 ? (($monthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
    $yearSalesChange = $lastYearSales > 0 ? (($yearSales - $lastYearSales) / $lastYearSales) * 100 : 0;
    $todayOrdersChange = $yesterdayOrders > 0 ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;
    
} catch(PDOException $e) {
    error_log("Error fetching sales data: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Casa Estela Boutique Hotel & Cafe</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #b8860b;
            --primary-hover: #9a7209;
            --primary-light: rgba(184, 134, 11, 0.1);
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #1abc9c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            margin: 0;
            padding: 0;
            padding-left: 250px;
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        /* Main Content */
        .main-content {
            padding: 90px 25px 25px;
            min-height: 100vh;
        }
        
        /* Sales Cards */
        .sales-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .sales-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .sales-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
        }
        
        .sales-card.success::before {
            background: var(--success-color);
        }
        
        .sales-card.warning::before {
            background: var(--warning-color);
        }
        
        .sales-card.info::before {
            background: var(--info-color);
        }
        
        .sales-card.danger::before {
            background: var(--danger-color);
        }
        
        .sales-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .sales-icon.primary {
            background: var(--primary-light);
            color: var(--primary-color);
        }
        
        .sales-icon.success {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .sales-icon.warning {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .sales-icon.info {
            background: rgba(26, 188, 156, 0.1);
            color: var(--info-color);
        }
        
        .sales-icon.danger {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .sales-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .sales-label {
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sales-change {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .sales-change.positive {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .sales-change.negative {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        /* Table Styles */
        .sales-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .table-btn {
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .table-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
        
        .custom-table {
            margin: 0;
        }
        
        .custom-table thead th {
            background: #f8f9fa;
            border: none;
            padding: 15px;
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .custom-table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f2f6;
            font-size: 14px;
        }
        
        .custom-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.completed {
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .status-badge.pending {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .status-badge.processing {
            background: rgba(26, 188, 156, 0.1);
            color: var(--info-color);
        }
        
        .amount {
            font-weight: 600;
            color: var(--success-color);
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .filter-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        
        .filter-btn {
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        /* Top Items */
        .top-items-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .top-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f2f6;
        }
        
        .top-item:last-child {
            border-bottom: none;
        }
        
        .top-item-rank {
            width: 30px;
            height: 30px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }
        
        .top-item-info {
            flex: 1;
            margin-left: 15px;
        }
        
        .top-item-name {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 3px;
        }
        
        .top-item-quantity {
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .top-item-revenue {
            text-align: right;
        }
        
        .top-item-amount {
            font-weight: 600;
            color: var(--success-color);
            font-size: 16px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }
            
            .main-content {
                padding: 90px 15px 15px;
            }
            
            .sales-value {
                font-size: 24px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
        }
        
        /* Dark theme specific styles */
        body.dark-theme {
            color: #e9ecef !important;
        }
        
        body.dark-theme .sales-card {
            background: var(--card-bg);
            color: #e9ecef !important;
        }
        
        body.dark-theme .filter-section {
            background: var(--card-bg);
            color: #e9ecef !important;
        }
        
        body.dark-theme .sales-table {
            background: var(--card-bg);
            color: #e9ecef !important;
        }
        
        body.dark-theme .top-items-card {
            background: var(--card-bg);
            color: #e9ecef !important;
        }
        
        body.dark-theme .filter-input {
            background: var(--bg-secondary);
            border-color: var(--border-color);
            color: #e9ecef !important;
        }
        
        body.dark-theme .filter-input:focus {
            border-color: var(--primary-color);
        }
        
        body.dark-theme .custom-table thead th {
            background: var(--bg-secondary);
            color: #e9ecef !important;
        }
        
        body.dark-theme .custom-table tbody td {
            border-color: var(--border-color);
            color: #e9ecef !important;
        }
        
        body.dark-theme .custom-table tbody tr:hover {
            background: var(--bg-secondary);
        }
        
        body.dark-theme .table-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        }
        
        body.dark-theme .top-item {
            border-color: var(--border-color);
        }
        
        body.dark-theme .sales-label {
            color: #adb5bd !important;
        }
        
        body.dark-theme .nav-title {
            color: #e9ecef !important;
        }
        
        body.dark-theme .nav-subtitle {
            color: #adb5bd !important;
        }
        
        body.dark-theme h2, 
        body.dark-theme h3, 
        body.dark-theme h4, 
        body.dark-theme h5 {
            color: #e9ecef !important;
        }
        
        body.dark-theme p {
            color: #adb5bd !important;
        }
    </style>
</head>
<body>
    <!-- Include Header and Sidebar -->
    <?php include 'header.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="mb-4">
            <div>
                <h2 class="mb-1">Sales Dashboard</h2>
                <p class="text-muted mb-0">Monitor your restaurant's performance and revenue</p>
            </div>
        </div>
        
        <!-- Date Filter Section -->
        <div class="filter-section">
            <form method="GET" id="dateFilterForm">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="from_date" class="filter-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="filter-input" value="<?= htmlspecialchars($fromDate) ?>">
                    </div>
                    <div class="filter-group">
                        <label for="to_date" class="filter-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="filter-input" value="<?= htmlspecialchars($toDate) ?>">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter me-2"></i>Apply Filter
                        </button>
                        <button type="button" class="filter-btn" onclick="clearFilter()" style="background: #6c757d; margin-left: 10px;">
                            <i class="fas fa-times me-2"></i>Clear
                        </button>
                        <button type="button" class="filter-btn" onclick="exportReport()" style="background: #28a745; margin-left: 10px;">
                            <i class="fas fa-download me-2"></i>Export Report
                        </button>
                    </div>
                </div>
                <?php if ($fromDate && $toDate): ?>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing sales from <strong><?= date('M j, Y', strtotime($fromDate)) ?></strong> to <strong><?= date('M j, Y', strtotime($toDate)) ?></strong>
                        </small>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Sales Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sales-card">
                    <div class="sales-icon primary">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="sales-value">₱<?php echo number_format($todaySales, 2); ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Sales';
                        } else {
                            echo "Today's Sales";
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change <?php echo $todaySalesChange >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo $todaySalesChange >= 0 ? 'up' : 'down'; ?> me-1"></i>
                        <?php echo number_format(abs($todaySalesChange), 1); ?>% from yesterday
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sales-card success">
                    <div class="sales-icon success">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="sales-value">₱<?php echo number_format($weekSales, 2); ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Sales';
                        } else {
                            echo 'This Week';
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change <?php echo $weekSalesChange >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo $weekSalesChange >= 0 ? 'up' : 'down'; ?> me-1"></i>
                        <?php echo number_format(abs($weekSalesChange), 1); ?>% from last week
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sales-card warning">
                    <div class="sales-icon warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="sales-value">₱<?php echo number_format($monthSales, 2); ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Sales';
                        } else {
                            echo 'This Month';
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change <?php echo $monthSalesChange >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo $monthSalesChange >= 0 ? 'up' : 'down'; ?> me-1"></i>
                        <?php echo number_format(abs($monthSalesChange), 1); ?>% from last month
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="sales-card info">
                    <div class="sales-icon info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="sales-value">₱<?php echo number_format($yearSales, 2); ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Sales';
                        } else {
                            echo 'This Year';
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change <?php echo $yearSalesChange >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo $yearSalesChange >= 0 ? 'up' : 'down'; ?> me-1"></i>
                        <?php echo number_format(abs($yearSalesChange), 1); ?>% from last year
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Additional Stats Row -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="sales-card">
                    <div class="sales-icon primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="sales-value"><?php echo $todayOrders; ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Orders';
                        } else {
                            echo "Today's Orders";
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change <?php echo $todayOrdersChange >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-arrow-<?php echo $todayOrdersChange >= 0 ? 'up' : 'down'; ?> me-1"></i>
                        <?php echo number_format(abs($todayOrdersChange), 1); ?>% from yesterday
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="sales-card success">
                    <div class="sales-icon success">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="sales-value"><?php echo $totalOrders; ?></div>
                    <div class="sales-label">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Filtered Period Orders';
                        } else {
                            echo 'Total Completed Orders';
                        }
                        ?>
                    </div>
                    <?php if (!$fromDate && !$toDate): ?>
                    <div class="sales-change positive">
                        <i class="fas fa-arrow-up me-1"></i>All time
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sales Graph -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="sales-card">
                    <div class="table-header">
                        <h5 class="table-title">
                            <?php 
                                if ($fromDate && $toDate) {
                                    echo 'Sales Trend (' . date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate)) . ')';
                                } else {
                                    echo 'Sales Trend (Last 30 Days)';
                                }
                                ?>
                        </h5>
                        <div class="table-actions">
                            <a href="#" class="table-btn" onclick="refreshChart()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </a>
                            <a href="#" class="table-btn" onclick="changeChartType()">
                                <i class="fas fa-chart-line me-1"></i>Change Type
                            </a>
                        </div>
                    </div>
                    <div class="p-3">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables Row -->
        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="sales-table">
                    <div class="table-header">
                        <h5 class="table-title">
                            <?php 
                            if ($fromDate && $toDate) {
                                echo 'Filtered Sales (' . date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate)) . ')';
                            } else {
                                echo 'Recent Sales (Last 30 Days)';
                            }
                            ?>
                        </h5>
                        <div class="table-actions">
                            <a href="#" class="table-btn" onclick="refreshTable()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </a>
                            <a href="#" class="table-btn" onclick="viewAllSales()">
                                <i class="fas fa-eye me-1"></i>View All
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Table</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentSales)): ?>
                                    <?php foreach ($recentSales as $sale): ?>
                                    <tr>
                                        <td>#<?php echo $sale['order_id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(trim($sale['firstname'] . ' ' . $sale['lastname'])) ?: 'Walkin Customers'; ?>
                                        </td>
                                        <td>N/A</td>
                                        <td class="amount">₱<?php echo number_format($sale['total'], 2); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($sale['status']); ?>">
                                                <?php echo ucfirst($sale['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y H:i', strtotime($sale['date_time'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No recent sales data available. Sales will appear here once orders are completed.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-3">
                <div class="top-items-card">
                    <h5 class="mb-3">
                        <?php 
                        if ($fromDate && $toDate) {
                            echo 'Top Selling Items (' . date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate)) . ')';
                        } else {
                            echo 'Top Selling Items (Last 30 Days)';
                        }
                        ?>
                    </h5>
                    <?php if (!empty($topItems)): ?>
                        <?php foreach ($topItems as $index => $item): ?>
                        <div class="top-item">
                            <div class="top-item-rank"><?php echo $index + 1; ?></div>
                            <div class="top-item-info">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!empty($item['image_path']) && file_exists('../../Admin/adminBackend/menu_item_images/' . $item['image_path'])): ?>
                                        <img src="../../Admin/adminBackend/menu_item_images/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 10px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: var(--primary-light); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                            <i class="fas fa-utensils text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="top-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                </div>
                                <div class="top-item-quantity"><?php echo $item['total_quantity']; ?> items sold</div>
                            </div>
                            <div class="top-item-revenue">
                                <div class="top-item-amount">₱<?php echo number_format($item['total_revenue'], 2); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>No top selling items data available yet.</p>
                            <small>Items will appear here once sales are recorded.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    // Custom JavaScript
    <script>
    function showAlert(message, type = 'info') {
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Initialize Charts
    let salesChart = null;
    let currentChartType = 'daily'; // 'daily', 'weekly', 'monthly'
    
    document.addEventListener('DOMContentLoaded', function() {
        initializeSalesChart();
    });
    
    function initializeSalesChart() {
        fetchSalesChartData();
    }
    
    function fetchSalesChartData() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;
        
        console.log('Fetching chart data:', { from, to, chartType: currentChartType });
        
        $.ajax({
            url: 'get_sales_chart_data.php',
            method: 'POST',
            data: {
                from_date: from,
                to_date: to,
                chart_type: currentChartType
            },
            dataType: 'json',
            success: function(response) {
                console.log('Chart data response:', response);
                if (response.success) {
                    renderSalesChart(response);
                } else {
                    showAlert('Error loading chart data: ' + response.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Error loading chart data. Please try again.', 'error');
                console.error('Chart data error:', error);
            }
        });
    }
    
    function renderSalesChart(data) {
        console.log('Rendering sales chart with data:', data);
        
        const ctx = document.getElementById('salesChart');
        if (!ctx) {
            console.error('Chart canvas not found!');
            return;
        }
        
        const ctx2d = ctx.getContext('2d');
        if (!ctx2d) {
            console.error('Could not get 2D context from chart canvas!');
            return;
        }
        
        // Destroy existing chart if it exists
        if (salesChart) {
            salesChart.destroy();
            salesChart = null;
        }
        
        // Prepare chart data based on current type
        let chartConfig;
        
        if (currentChartType === 'daily') {
            chartConfig = {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Daily Sales',
                        data: data.sales,
                        borderColor: '#b8860b',
                        backgroundColor: 'rgba(184, 134, 11, 0.1)',
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
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
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
                                }
                            }
                        }
                    }
                }
            };
        } else if (currentChartType === 'weekly') {
            chartConfig = {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Weekly Sales',
                        data: data.sales,
                        backgroundColor: [
                            'rgba(184, 134, 11, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(26, 188, 156, 0.8)',
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(243, 156, 18, 0.8)'
                        ],
                        borderColor: [
                            '#b8860b',
                            '#2ecc71',
                            '#f39c12',
                            '#1abc9c',
                            '#e74c3c',
                            '#3498db',
                            '#f1c40f'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
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
                                }
                            }
                        }
                    }
                }
            };
        } else if (currentChartType === 'monthly') {
            chartConfig = {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: data.sales,
                        borderColor: '#b8860b',
                        backgroundColor: 'rgba(184, 134, 11, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
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
                                }
                            }
                        }
                    }
                }
            };
        }
        
        console.log('Creating chart with config:', chartConfig);
        
        try {
            salesChart = new Chart(ctx2d, chartConfig);
            console.log('Chart created successfully:', salesChart);
        } catch (error) {
            console.error('Error creating chart:', error);
            showAlert('Error creating chart: ' + error.message, 'error');
        }
    }
    
    function refreshChart() {
        showAlert('Refreshing chart data...', 'info');
        fetchSalesChartData();
    }
    
    function changeChartType() {
        // Cycle through chart types
        if (currentChartType === 'daily') {
            currentChartType = 'weekly';
        } else if (currentChartType === 'weekly') {
            currentChartType = 'monthly';
        } else {
            currentChartType = 'daily';
        }
        
        showAlert('Changed to ' + currentChartType + ' view', 'info');
        fetchSalesChartData();
    }
    
    function updateSalesTable(salesData) {
        const tbody = document.querySelector('.custom-table tbody');
        tbody.innerHTML = '';
        
        if (salesData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No sales data found for the selected filters.</td>
                </tr>
            `;
            return;
        }
        
        salesData.forEach(function(sale) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${sale.id}</td>
                <td>${sale.customer}</td>
                <td>${sale.table}</td>
                <td class="amount">${sale.amount}</td>
                <td>
                    <span class="status-badge ${sale.status_class}">
                        ${sale.status}
                    </span>
                </td>
                <td>${sale.date}</td>
            `;
            tbody.appendChild(row);
        });
    }
    
    function refreshTable() {
        showAlert('Refreshing sales data...', 'info');
        
        // Make AJAX call to get recent data
        $.ajax({
            url: 'get_recent_sales.php',
            method: 'POST',
            data: {
                dateRange: 'recent',
                status: 'completed',
                limit: 10
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateSalesTable(response.data);
                    showAlert('Sales data refreshed!', 'success');
                } else {
                    showAlert('Error refreshing data: ' + response.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Error refreshing data. Please try again.', 'error');
                console.error('AJAX error:', error);
            }
        });
    }
    
    function viewAllSales() {
        showAlert('Redirecting to detailed sales view...', 'info');
        // Here you would redirect to a detailed sales page
    }
    
    function clearFilter() {
        window.location.href = 'sales.php';
    }
    
    function exportReport() {
        console.log('Export PDF function called');
        
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;
        
        console.log('From date:', from);
        console.log('To date:', to);
        
        // Build URL for PDF generation
        let url = 'sales_export.php?export=pdf';
        if (from) url += '&from_date=' + encodeURIComponent(from);
        if (to) url += '&to_date=' + encodeURIComponent(to);
        
        console.log('Export PDF URL:', url);
        
        // Show loading message
        showAlert('Generating PDF report...', 'info');
        
        // Open in new window for PDF generation
        const pdfWindow = window.open(url, '_blank');
        
        // Check if popup was blocked
        if (!pdfWindow || pdfWindow.closed || typeof pdfWindow.closed === 'undefined') {
            showAlert('Please allow popups for this site to generate PDF reports', 'warning');
            // Fallback: try direct download
            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            // Show success message after a short delay
            setTimeout(() => {
                showAlert('PDF report generated successfully!', 'success');
            }, 2000);
        }
    }
    
        </script>
</body>
</html>