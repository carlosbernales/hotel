<?php
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get POST data
$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';
$chartType = $_POST['chart_type'] ?? 'daily';

try {
    // Prepare date condition
    $dateCondition = '';
    if ($fromDate && $toDate) {
        $fromDateTime = $fromDate . ' 00:00:00';
        $toDateTime = $toDate . ' 23:59:59';
        $dateCondition = " AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
    } else {
        $dateCondition = " AND date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
    
    // Prepare query based on chart type
    $sql = "";
    $labels = [];
    $salesData = [];
    
    if ($chartType === 'daily') {
        // Daily sales for last 30 days
        $sql = "
            SELECT 
                DATE(date_time) as date_label,
                SUM(total) as daily_sales
            FROM orders_table 
            WHERE status = 'Completed'
            $dateCondition
            GROUP BY DATE(date_time)
            ORDER BY date_time ASC
            LIMIT 30
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $labels[] = date('M j, Y', strtotime($row['date_label']));
            $salesData[] = (float)$row['daily_sales'];
        }
        
    } elseif ($chartType === 'weekly') {
        // Weekly sales for last 8 weeks
        $sql = "
            SELECT 
                CONCAT('Week ', WEEK(date_time)) as date_label,
                SUM(total) as weekly_sales
            FROM orders_table 
            WHERE status = 'Completed'
            $dateCondition
            GROUP BY WEEK(date_time)
            ORDER BY WEEK(date_time) DESC
            LIMIT 8
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $labels[] = $row['date_label'];
            $salesData[] = (float)$row['weekly_sales'];
        }
        
    } elseif ($chartType === 'monthly') {
        // Monthly sales for last 12 months
        $sql = "
            SELECT 
                DATE_FORMAT(date_time, '%M %Y') as date_label,
                SUM(total) as monthly_sales
            FROM orders_table 
            WHERE status = 'Completed'
            $dateCondition
            GROUP BY DATE_FORMAT(date_time, '%M %Y')
            ORDER BY YEAR(date_time) ASC, MONTH(date_time) ASC
            LIMIT 12
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $labels[] = $row['date_label'];
            $salesData[] = (float)$row['monthly_sales'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'sales' => $salesData,
        'chart_type' => $chartType
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
