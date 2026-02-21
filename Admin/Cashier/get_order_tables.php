<?php
require_once 'db.php';

// Check if user is logged in and has cashier role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    // First, get the actual database ID from order_id
    $stmt = $pdo->prepare("SELECT id FROM orders_table WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $orderRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderRecord) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit();
    }
    
    $actualOrderId = $orderRecord['id'];
    
    // Get currently assigned tables for this order
    $stmt = $pdo->prepare("
        SELECT 
            ott.id,
            ott.table_name,
            ott.table_number,
            ott.table_number_fk_id,
            tn.id as table_number_id,
            tn.status as table_status,
            tt.capacity
        FROM orders_table_type ott
        JOIN table_number tn ON ott.table_number_fk_id = tn.id
        JOIN table_types tt ON tn.table_type_fk_id = tt.id AND ott.table_name COLLATE utf8mb4_unicode_ci = tt.table_name COLLATE utf8mb4_unicode_ci
        WHERE ott.table_booking_fk_id = :order_id
        ORDER BY ott.table_name, ott.table_number
    ");
    $stmt->execute([':order_id' => $actualOrderId]);
    $assignedTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'assigned_tables' => $assignedTables
    ]);

} catch (PDOException $e) {
    error_log("Error fetching assigned tables: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
