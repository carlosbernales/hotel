<?php
session_start(); // Add session start
require_once "db.php";
header('Content-Type: application/json');

// Check if user is logged in and is a cashier
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

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
$userId = $_SESSION['user_id']; // Get user ID from session

try {
    // Update order status to 'completed', set completed_at and user_id
    $updateSql = "UPDATE orders SET 
        status = 'finished', 
        completed_at = NOW(),
        user_id = ?,
        updated_at = NOW() 
        WHERE id = ? AND status = 'processing'";
        
    $stmt = $connection->prepare($updateSql);
    $stmt->bind_param('ii', $userId, $orderId);
    $stmt->execute();

    // Check if any row was actually updated
    if ($stmt->affected_rows === 0) {
        throw new Exception('Order not found or already completed');
    }

    // Fetch processor name
    $nameQuery = "SELECT CONCAT(first_name, ' ', last_name) as processor_name 
                 FROM userss WHERE id = ?";
    $nameStmt = $connection->prepare($nameQuery);
    $nameStmt->bind_param('i', $userId);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    $processorName = $nameResult->fetch_assoc()['processor_name'];

    echo json_encode([
        'success' => true,
        'message' => 'Order marked as completed successfully',
        'processed_by' => $processorName
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$connection->close();