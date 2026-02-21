<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$orderId = $_POST['order_id'] ?? null;
$amountPaid = floatval($_POST['amount_paid'] ?? 0);
$paymentMethod = $_POST['payment_method'] ?? 'Cash';

if (!$orderId || $amountPaid <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get the actual database ID from order_id
    $stmt = $pdo->prepare("SELECT id, total, amount_paid, balance, downpayment FROM orders_table WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $orderRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderRecord) {
        throw new Exception('Order not found');
    }
    
    $actualOrderId = $orderRecord['id'];
    $total = floatval($orderRecord['total']);
    $currentAmountPaid = floatval($orderRecord['amount_paid']);
    $downpayment = floatval($orderRecord['downpayment']);
    $currentBalance = floatval($orderRecord['balance']);
    
    // Calculate new amounts - include downpayment in total amount paid
    $newAmountPaid = $currentAmountPaid + $amountPaid + $downpayment;
    $newBalance = $total - $newAmountPaid;
    
    // Update order with payment information
    $updateStmt = $pdo->prepare("
        UPDATE orders_table SET 
            amount_paid = :amount_paid,
            balance = :balance,
            payment_method = :payment_method,
            change_amount = :change_amount,
            status = :status
        WHERE id = :id
    ");
    
    $changeAmount = max(0, $amountPaid - $currentBalance);
    $status = $newBalance <= 0 ? 'Completed' : 'processing';
    
    $updateStmt->execute([
        ':amount_paid' => $newAmountPaid,
        ':balance' => $newBalance,
        ':payment_method' => $paymentMethod,
        ':change_amount' => $changeAmount,
        ':status' => $status,
        ':id' => $actualOrderId
    ]);
    
    // If order is fully paid, free up the table and update notifications
    if ($status === 'Completed') {
        // Update notifications table to set is_completed = 1 and is_processing = 0, and update title and message
        $notificationStmt = $pdo->prepare("
            UPDATE notifications 
            SET is_completed = 1, is_processing = 0, title = 'Order Completed', message = CONCAT('Your order #', :order_id_string, ' has been completed.')
            WHERE order_id = :order_id AND is_completed = 0
        ");
        $notificationStmt->execute([':order_id' => $actualOrderId, ':order_id_string' => $orderId]);
        
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
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'order_fully_paid' => $newBalance <= 0,
        'new_balance' => $newBalance,
        'change_amount' => $changeAmount
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
    
    error_log("Error processing payment: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to process payment: ' . $e->getMessage()]);
}
?>
