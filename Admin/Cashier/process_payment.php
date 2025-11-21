<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');
    
    try {
        // Get the payment details
        $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $paymentAmount = isset($_POST['payment_amount']) ? floatval($_POST['payment_amount']) : 0;
        $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
        
        // Validate input
        if (!$orderId) {
            throw new Exception('Invalid order ID');
        }
        if (!$paymentAmount || $paymentAmount <= 0) {
            throw new Exception('Invalid payment amount');
        }
        if (empty($paymentMethod)) {
            throw new Exception('Please select a payment method');
        }
        
        // Start transaction
        $connection->begin_transaction();
        
        // Get current order details
        $stmt = $connection->prepare("SELECT total_amount, amount_paid, remaining_balance FROM orders WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $connection->error);
        }
        
        $stmt->bind_param("i", $orderId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to fetch order details: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        $currentAmountPaid = floatval($order['amount_paid']);
        $remainingBalance = floatval($order['remaining_balance']);
        
        if ($remainingBalance <= 0) {
            throw new Exception('This order is already fully paid');
        }
        
        // Validate payment amount
        if ($paymentAmount > $remainingBalance) {
            throw new Exception('Payment amount (₱' . number_format($paymentAmount, 2) . ') cannot exceed remaining balance (₱' . number_format($remainingBalance, 2) . ')');
        }
        
        // Calculate new values
        $newAmountPaid = $currentAmountPaid + $paymentAmount;
        $newRemainingBalance = $remainingBalance - $paymentAmount;
        
        // Determine payment status
        $paymentStatus = ($newRemainingBalance <= 0) ? 'Paid' : 'Partial';
        
        // Update the order with new payment information
        $updateStmt = $connection->prepare("
            UPDATE orders 
            SET amount_paid = ?,
                remaining_balance = ?,
                payment_status = ?,
                payment_method = CASE 
                    WHEN payment_method IS NULL OR payment_method = '' THEN ?
                    ELSE CONCAT(COALESCE(payment_method, ''), ', ', ?)
                END,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        if (!$updateStmt) {
            throw new Exception('Database error: ' . $connection->error);
        }
        
        $updateStmt->bind_param(
            "ddsssi",
            $newAmountPaid,
            $newRemainingBalance,
            $paymentStatus,
            $paymentMethod,
            $paymentMethod,
            $orderId
        );
        
        if (!$updateStmt->execute()) {
            throw new Exception('Failed to update payment information: ' . $updateStmt->error);
        }
        
        // Insert payment record
        $paymentStmt = $connection->prepare("
            INSERT INTO order_payments (order_id, amount, payment_method, payment_date)
            VALUES (?, ?, ?, NOW())
        ");
        
        if (!$paymentStmt) {
            throw new Exception('Database error: ' . $connection->error);
        }
        
        $paymentStmt->bind_param("ids", $orderId, $paymentAmount, $paymentMethod);
        
        if (!$paymentStmt->execute()) {
            throw new Exception('Failed to record payment: ' . $paymentStmt->error);
        }
        
        // Commit transaction
        $connection->commit();
        
        $response['success'] = true;
        $response['message'] = 'Payment of ₱' . number_format($paymentAmount, 2) . ' processed successfully';
        $response['remaining_balance'] = $newRemainingBalance;
        $response['amount_paid'] = $newAmountPaid;
        $response['payment_status'] = $paymentStatus;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($connection) && $connection->ping()) {
            $connection->rollback();
        }
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    } finally {
        // Close any open statements
        if (isset($stmt)) $stmt->close();
        if (isset($updateStmt)) $updateStmt->close();
        if (isset($paymentStmt)) $paymentStmt->close();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?> 