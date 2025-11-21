<?php
require_once "db.php";
header('Content-Type: application/json');

if (!isset($_GET['reservation_id'])) {
    echo json_encode(['success' => false, 'message' => 'Reservation ID is required']);
    exit;
}

try {
    $stmt = $con->prepare("
        SELECT ro.*, mi.name as item_name 
        FROM reservation_orders ro
        JOIN menu_items mi ON ro.menu_item_id = mi.id
        WHERE ro.reservation_id = ?
    ");
    
    $stmt->bind_param("i", $_GET['reservation_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'item_name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'notes' => $row['notes']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching orders: ' . $e->getMessage()
    ]);
} 