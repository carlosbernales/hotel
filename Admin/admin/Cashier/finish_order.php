<?php
require_once "db.php";
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if order_id is provided
if (!isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$orderId = intval($_POST['order_id']);

try {
    // Update order status to 'completed' and set completed_at
    $updateSql = "UPDATE orders SET status = 'finished', completed_at = NOW() WHERE id = ? AND status = 'processing'";
    $stmt = $connection->prepare($updateSql);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();

    // Check if any row was actually updated
    if ($stmt->affected_rows === 0) {
        throw new Exception('Order not found or already completed');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Order marked as completed successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$connection->close(); 