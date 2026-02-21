<?php
require_once 'db.php';

header('Content-Type: application/json');

$chartType = $_GET['chart_type'] ?? 'revenue';
$period = $_GET['period'] ?? 'daily';

try {
    $data = [];
    
    switch($chartType) {
        case 'revenue':
            $data = getRevenueData($period);
            break;
        case 'category':
            $data = getCategoryData();
            break;
        case 'hourly':
            $data = getHourlyData($period);
            break;
        case 'payment':
            $data = getPaymentData();
            break;
        case 'table_performance':
            $data = getTablePerformanceData();
            break;
        case 'customer_growth':
            $data = getCustomerGrowthData();
            break;
        case 'avg_order':
            $data = getAvgOrderData();
            break;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getRevenueData($period) {
    global $pdo;
    
    $query = "";
    $labels = [];
    
    switch($period) {
        case 'daily':
            $query = "SELECT DAYNAME(order_at) as day, COALESCE(SUM(total), 0) as revenue 
                     FROM orders_table 
                     WHERE status = 'Completed' 
                     AND order_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                     GROUP BY DAYNAME(order_at), DAYOFWEEK(order_at)
                     ORDER BY DAYOFWEEK(order_at)";
            $labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            break;
            
        case 'weekly':
            $query = "SELECT CONCAT('Week ', WEEK(order_at)) as week, COALESCE(SUM(total), 0) as revenue 
                     FROM orders_table 
                     WHERE status = 'Completed' 
                     AND order_at >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
                     GROUP BY WEEK(order_at)
                     ORDER BY WEEK(order_at)";
            break;
            
        case 'monthly':
            $query = "SELECT DATE_FORMAT(order_at, '%b') as month, COALESCE(SUM(total), 0) as revenue 
                     FROM orders_table 
                     WHERE status = 'Completed' 
                     AND order_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                     GROUP BY DATE_FORMAT(order_at, '%b'), MONTH(order_at)
                     ORDER BY MONTH(order_at)";
            break;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $revenues = [];
    
    foreach($results as $row) {
        if ($period === 'daily') {
            $data[] = $row['revenue'];
        } else {
            $labels[] = $row[$period === 'weekly' ? 'week' : 'month'];
            $revenues[] = $row['revenue'];
        }
    }
    
    if ($period === 'daily') {
        // Fill missing days with 0
        $fullData = [];
        for($i = 0; $i < 7; $i++) {
            $fullData[] = 0;
        }
        foreach($results as $row) {
            $dayIndex = date('N', strtotime('last monday + ' . (date('N') - 1) . ' days')) - 1;
            $fullData[$dayIndex] = $row['revenue'];
        }
        return ['labels' => $labels, 'data' => $fullData];
    }
    
    return ['labels' => $labels, 'data' => $revenues];
}

function getCategoryData() {
    global $pdo;
    
    $query = "SELECT c.name as category, COALESCE(SUM(oi.quantity * oi.price), 0) as revenue
             FROM order_items oi
             JOIN menu_items mi ON oi.menu_item_id = mi.id
             JOIN menu_categories c ON mi.category_id = c.id
             JOIN orders_table o ON oi.order_id = o.id
             WHERE o.status = 'Completed'
             AND o.order_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY c.id, c.name
             ORDER BY revenue DESC
             LIMIT 5";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    foreach($results as $row) {
        $labels[] = $row['category'];
        $data[] = $row['revenue'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getHourlyData($period) {
    global $pdo;
    
    $query = "SELECT HOUR(order_at) as hour, COALESCE(SUM(total), 0) as revenue
             FROM orders_table 
             WHERE status = 'Completed' 
             AND order_at >= DATE_SUB(CURDATE(), INTERVAL " . ($period === 'today' ? '1' : '7') . " DAY)
             GROUP BY HOUR(order_at)
             ORDER BY HOUR(order_at)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    // Fill all hours with 0
    for($i = 6; $i <= 22; $i += 2) {
        $labels[] = ($i <= 12 ? $i : $i - 12) . ($i < 12 ? 'AM' : 'PM');
        $data[] = 0;
    }
    
    foreach($results as $row) {
        $hour = $row['hour'];
        if ($hour >= 6 && $hour <= 22 && $hour % 2 === 0) {
            $index = ($hour - 6) / 2;
            $data[$index] = $row['revenue'];
        }
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getPaymentData() {
    global $pdo;
    
    $query = "SELECT payment_method, COALESCE(SUM(total), 0) as revenue
             FROM orders_table 
             WHERE status = 'Completed' 
             AND order_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY payment_method
             ORDER BY revenue DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    foreach($results as $row) {
        $labels[] = ucfirst($row['payment_method']);
        $data[] = $row['revenue'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getTablePerformanceData() {
    global $pdo;
    
    $query = "SELECT t.table_number, COUNT(o.id) as orders, COALESCE(SUM(o.total), 0) as revenue
             FROM orders_table o
             JOIN tables t ON o.table_id = t.id
             WHERE o.status = 'Completed'
             AND o.order_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY t.id, t.table_number
             ORDER BY revenue DESC
             LIMIT 5";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    foreach($results as $row) {
        $labels[] = 'Table ' . $row['table_number'];
        $data[] = $row['revenue'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getCustomerGrowthData() {
    global $pdo;
    
    $query = "SELECT DATE(order_at) as date, COUNT(DISTINCT user_id) as customers
             FROM orders_table 
             WHERE status = 'Completed' 
             AND order_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(order_at)
             ORDER BY DATE(order_at)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    foreach($results as $row) {
        $labels[] = date('M j', strtotime($row['date']));
        $data[] = $row['customers'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}

function getAvgOrderData() {
    global $pdo;
    
    $query = "SELECT DATE(order_at) as date, COALESCE(AVG(total), 0) as avg_order
             FROM orders_table 
             WHERE status = 'Completed' 
             AND order_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(order_at)
             ORDER BY DATE(order_at)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $data = [];
    
    foreach($results as $row) {
        $labels[] = date('M j', strtotime($row['date']));
        $data[] = $row['avg_order'];
    }
    
    return ['labels' => $labels, 'data' => $data];
}
?>
