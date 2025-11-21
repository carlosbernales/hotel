<?php
// Include database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$orderId = intval($_GET['order_id']);

try {
    // Get order details
    $orderQuery = "SELECT o.*, tb.booking_date, tb.booking_time, tb.name as customer_name 
                  FROM orders o 
                  LEFT JOIN table_bookings tb ON o.table_id = tb.id 
                  WHERE o.id = ?";
    
    $stmt = $con->prepare($orderQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param('i', $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    $order = $result->fetch_assoc();
    
    // Get order items
    $itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $con->prepare($itemsQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed for items query: " . $con->error);
    }
    
    $stmt->bind_param('i', $orderId);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed for items query: " . $stmt->error);
    }
    
    $itemsResult = $stmt->get_result();
    $orderItems = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $orderItems[] = $item;
    }
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'order' => $order,
        'order_items' => $orderItems
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log("Error retrieving order details: " . $e->getMessage());
    
    // Return error message
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving order details: ' . $e->getMessage()
    ]);
} 