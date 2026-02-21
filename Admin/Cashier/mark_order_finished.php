<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$orderId = $_POST['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get the actual database ID from order_id
    $stmt = $pdo->prepare("SELECT id FROM orders_table WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $orderRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderRecord) {
        throw new Exception('Order not found');
    }
    
    $actualOrderId = $orderRecord['id'];

    // Update order status to 'Completed'
    $stmt = $pdo->prepare("UPDATE orders_table SET status = 'Completed' WHERE id = :id");
    $stmt->execute([':id' => $actualOrderId]);
    
    // Get all table assignments for this order
    $tableStmt = $pdo->prepare("SELECT * FROM orders_table_type WHERE table_booking_fk_id = :order_id");
    $tableStmt->execute([':order_id' => $actualOrderId]);
    $tableAssignments = $tableStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tableAssignments) {
        // Free up all tables assigned to this order
        foreach ($tableAssignments as $tableAssignment) {
            // Check if no other active orders are using this table
            $checkOtherOrdersStmt = $pdo->prepare("
                SELECT COUNT(*) as active_orders 
                FROM orders_table_type ott 
                JOIN orders_table o ON ott.table_booking_fk_id = o.id 
                WHERE ott.table_number = :table_id AND o.status != 'Completed'
            ");
            $checkOtherOrdersStmt->execute([':table_id' => $tableAssignment['table_number_fk_id']]);
            $activeOrders = $checkOtherOrdersStmt->fetchColumn();
            
            if ($activeOrders == 0) {
                // Mark the table as available
                $freeTableStmt = $pdo->prepare("UPDATE table_number SET status = 'available' WHERE id = :table_id");
                $freeTableStmt->execute([':table_id' => $tableAssignment['table_number_fk_id']]);
            }
        }
    }

    // Update notification: set is_completed=1, update message, set is_read=0
    // First, let's check what notification exists for this order
    $checkStmt = $pdo->prepare("SELECT id, type, title FROM notifications WHERE order_id = ?");
    $checkStmt->execute([$actualOrderId]);
    $existingNotification = $checkStmt->fetch(PDO::FETCH_ASSOC);
    error_log("Existing notification for order_id {$actualOrderId}: " . var_export($existingNotification, true));
    
    if ($existingNotification) {
        // Update the specific notification by ID
        $notificationStmt = $pdo->prepare("
            UPDATE notifications 
            SET is_completed = 1, 
                message = 'Your order #{$orderId} has been completed successfully!', 
                is_read = 0,
                is_processing = 0,
                is_rejected = 0
            WHERE id = ?
        ");
        $notificationStmt->execute([$existingNotification['id']]);
        $affectedRows = $notificationStmt->rowCount();
        error_log("Notification update by ID {$existingNotification['id']}, Affected Rows: {$affectedRows}");
    } else {
        error_log("No notification found for order_id: {$actualOrderId}");
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order marked as completed successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        try {
            $pdo->rollBack();
        } catch (Exception $rollbackEx) {
            error_log('Error during transaction rollback: ' . $rollbackEx->getMessage());
        }
    }
    
    error_log("Error marking order as finished: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to mark order as finished: ' . $e->getMessage()]);
}
?>
