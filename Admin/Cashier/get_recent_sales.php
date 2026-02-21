<?php
// Include database connection
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Get parameters from POST request
    $dateRange = $_POST['dateRange'] ?? 'recent';
    $fromDate = $_POST['fromDate'] ?? null;
    $toDate = $_POST['toDate'] ?? null;
    $status = $_POST['status'] ?? 'completed';
    $limit = (int)($_POST['limit'] ?? 10);
    
    // Validate limit
    $limit = min(max($limit, 1), 100); // Between 1 and 100 records
    
    // Build the base query
    $query = "
        SELECT 
            o.id,
            o.order_id,
            o.firstname,
            o.lastname,
            o.total as amount,
            o.status,
            o.date_time as date
        FROM orders_table o
        WHERE 1=1
    ";
    
    $params = [];
    
    // Add date filters with validation
    if ($dateRange === 'recent') {
        // Get sales from the last 30 days
        $query .= " AND o.date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } elseif ($dateRange === 'custom' && $fromDate && $toDate) {
        // Validate date format
        $fromDateTime = DateTime::createFromFormat('Y-m-d', $fromDate);
        $toDateTime = DateTime::createFromFormat('Y-m-d', $toDate);
        
        if (!$fromDateTime || !$toDateTime) {
            throw new Exception('Invalid date format. Please use YYYY-MM-DD format.');
        }
        
        if ($fromDateTime > $toDateTime) {
            throw new Exception('From date cannot be later than to date.');
        }
        
        // Check if date range is reasonable (not more than 1 year)
        $interval = $fromDateTime->diff($toDateTime);
        if ($interval->days > 365) {
            throw new Exception('Date range cannot exceed 1 year.');
        }
        
        $query .= " AND DATE(o.date_time) BETWEEN ? AND ?";
        $params[] = $fromDate;
        $params[] = $toDate;
    }
    
    // Add status filter with validation
    $validStatuses = ['completed', 'pending', 'processing', 'cancelled'];
    if ($status !== 'all') {
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid status filter.');
        }
        $query .= " AND o.status = ?";
        $params[] = ucfirst($status);
    }
    
    // Add ordering and limit
    $query .= " ORDER BY o.date_time DESC LIMIT ?";
    $params[] = $limit;
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sales = $stmt->fetchAll();
    
    // Format the data for JSON response
    $formattedSales = [];
    foreach ($sales as $sale) {
        // Clean up customer name (remove extra spaces)
        $customerName = trim($sale['firstname'] . ' ' . $sale['lastname']);
        if (empty($customerName)) {
            $customerName = 'Guest';
        }
        
        // Format table display - simplified since we removed joins
        $tableDisplay = 'N/A';
        
        // Format amount
        $amountDisplay = '₱' . number_format($sale['amount'], 2);
        
        // Format date
        $dateDisplay = date('M j, Y H:i', strtotime($sale['date']));
        
        // Determine status class for CSS
        $statusClass = strtolower($sale['status']);
        
        $formattedSales[] = [
            'id' => $sale['id'],
            'customer' => htmlspecialchars($customerName),
            'table' => $tableDisplay,
            'amount' => $amountDisplay,
            'status' => ucfirst($sale['status']),
            'status_class' => $statusClass,
            'date' => $dateDisplay
        ];
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $formattedSales,
        'count' => count($formattedSales),
        'filters_applied' => [
            'date_range' => $dateRange,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'status' => $status,
            'limit' => $limit
        ]
    ]);
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>