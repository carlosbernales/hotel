<?php
require_once 'includes/conn.php';

// Check for new orders
function checkNewOrders() {
    global $conn;
    
    $sql = "SELECT o.*, c.firstname, c.lastname 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            WHERE o.notification_status = 0 
            AND (o.order_type = 'online' OR o.order_type = 'advance')
            ORDER BY o.order_date DESC";
            
    $query = $conn->query($sql);
    
    $notifications = array();
    while($row = $query->fetch_assoc()) {
        $notifications[] = array(
            'order_id' => $row['id'],
            'customer_name' => $row['firstname'] . ' ' . $row['lastname'],
            'order_type' => $row['order_type'],
            'order_date' => $row['order_date'],
            'total_amount' => $row['total_amount']
        );
    }
    
    echo json_encode($notifications);
}

// If this file is called directly via AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    checkNewOrders();
}
?> 