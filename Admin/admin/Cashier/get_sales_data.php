<?php
require_once('db.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get date range parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Debug log
error_log("Start Date: $start_date, End Date: $end_date");

try {
    // First verify database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Query to get orders with items and addons
    $query = "SELECT 
        o.id as order_id,
        o.created_at as order_date,
        o.user_id,
        o.total_amount,
        o.payment_method,
        o.status,
        oi.item_name,
        oi.quantity,
        oia.addon_name
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
    ORDER BY o.created_at DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $start_date, $end_date);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $orders = array();
    $current_order = null;

    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = array(
                'order_id' => $order_id,
                'order_date' => date('M d, Y h:i A', strtotime($row['order_date'])),
                'customer' => 'Walk-in',
                'items' => array(),
                'total_amount' => '₱ ' . number_format($row['total_amount'], 2),
                'payment_method' => $row['payment_method'],
                'status' => $row['status']
            );
        }

        // Add item with its addons
        $item_key = $row['item_name'] . '_' . $row['quantity'];
        if (!isset($orders[$order_id]['items'][$item_key])) {
            $orders[$order_id]['items'][$item_key] = array(
                'name' => $row['item_name'],
                'quantity' => $row['quantity'],
                'addons' => array()
            );
        }

        if ($row['addon_name']) {
            $orders[$order_id]['items'][$item_key]['addons'][] = $row['addon_name'];
        }
    }

    // Format items for display
    $formatted_orders = array();
    foreach ($orders as $order) {
        $items_display = array();
        foreach ($order['items'] as $item) {
            $item_text = $item['quantity'] . 'x ' . $item['name'];
            if (!empty($item['addons'])) {
                $item_text .= ' (+ ' . implode(', ', array_unique($item['addons'])) . ')';
            }
            $items_display[] = $item_text;
        }
        
        $order['items'] = implode('; ', $items_display);
        $formatted_orders[] = $order;
    }

    // Calculate totals
    $totals_query = "SELECT 
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_amount ELSE 0 END) as daily_total,
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) 
            THEN total_amount ELSE 0 END) as monthly_total,
        SUM(CASE WHEN YEAR(created_at) = YEAR(CURDATE()) THEN total_amount ELSE 0 END) as annual_total
    FROM orders
    WHERE status != 'cancelled'";

    $totals_result = $conn->query($totals_query);
    $totals = $totals_result->fetch_assoc();

    // Return the response
    $response = [
        'data' => array_values($formatted_orders),
        'daily_total' => '₱ ' . number_format($totals['daily_total'] ?? 0, 2),
        'monthly_total' => '₱ ' . number_format($totals['monthly_total'] ?? 0, 2),
        'annual_total' => '₱ ' . number_format($totals['annual_total'] ?? 0, 2)
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in get_sales_data.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'An error occurred while fetching sales data',
        'debug_message' => $e->getMessage()
    ]);
}
?> 