<?php
require_once('db.php');

if (!isset($_POST['start_date']) || !isset($_POST['end_date'])) {
    die(json_encode(['error' => 'Date range is required']));
}

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

try {
    // Get daily sales (for the selected date)
    $daily_query = "SELECT 
                    COALESCE(SUM(total_amount), 0) as daily_sales,
                    COUNT(*) as daily_orders,
                    COALESCE(AVG(total_amount), 0) as average_order_value
                    FROM orders 
                    WHERE DATE(order_date) = ?";
    $stmt = $conn->prepare($daily_query);
    $stmt->bind_param("s", $end_date);
    $stmt->execute();
    $daily_result = $stmt->get_result()->fetch_assoc();

    // Get monthly sales (for the current month)
    $monthly_query = "SELECT 
                      COALESCE(SUM(total_amount), 0) as monthly_sales,
                      COUNT(*) as monthly_orders,
                      COALESCE(AVG(total_amount), 0) as monthly_average
                      FROM orders 
                      WHERE YEAR(order_date) = YEAR(?) 
                      AND MONTH(order_date) = MONTH(?)";
    $stmt = $conn->prepare($monthly_query);
    $stmt->bind_param("ss", $end_date, $end_date);
    $stmt->execute();
    $monthly_result = $stmt->get_result()->fetch_assoc();

    // Get annual sales (for the current year)
    $annual_query = "SELECT 
                     COALESCE(SUM(total_amount), 0) as annual_sales,
                     COUNT(*) as annual_orders,
                     COALESCE(AVG(total_amount), 0) as annual_average,
                     MAX(total_amount) as highest_sale
                     FROM orders 
                     WHERE YEAR(order_date) = YEAR(?)";
    $stmt = $conn->prepare($annual_query);
    $stmt->bind_param("s", $end_date);
    $stmt->execute();
    $annual_result = $stmt->get_result()->fetch_assoc();

    // Get top selling items
    $top_items_query = "SELECT 
                        oi.item_name,
                        SUM(oi.quantity) as total_quantity,
                        SUM(oi.subtotal) as total_revenue
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.order_id
                        WHERE DATE(o.order_date) BETWEEN ? AND ?
                        GROUP BY oi.item_name
                        ORDER BY total_quantity DESC
                        LIMIT 5";
    $stmt = $conn->prepare($top_items_query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $top_items_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get payment method breakdown
    $payment_query = "SELECT 
                      payment_method,
                      COUNT(*) as count,
                      SUM(total_amount) as total
                      FROM orders
                      WHERE DATE(order_date) BETWEEN ? AND ?
                      GROUP BY payment_method";
    $stmt = $conn->prepare($payment_query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $payment_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $response = array(
        'daily_sales' => number_format($daily_result['daily_sales'], 2),
        'monthly_sales' => number_format($monthly_result['monthly_sales'], 2),
        'annual_sales' => number_format($annual_result['annual_sales'], 2),
        'statistics' => array(
            'daily_orders' => $daily_result['daily_orders'],
            'monthly_orders' => $monthly_result['monthly_orders'],
            'annual_orders' => $annual_result['annual_orders'],
            'average_order_value' => number_format($daily_result['average_order_value'], 2),
            'monthly_average' => number_format($monthly_result['monthly_average'], 2),
            'annual_average' => number_format($annual_result['annual_average'], 2),
            'highest_sale' => number_format($annual_result['highest_sale'], 2)
        ),
        'top_items' => $top_items_result,
        'payment_methods' => $payment_result
    );

    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error in get_sales_summary.php: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while fetching sales summary']);
} 