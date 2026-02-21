<?php
session_start();
require_once 'db_con.php';

// Set JSON content type header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'orderId' => null,
    'redirect' => ''
];

try {
    // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if there's a pending order in the session
    if (!isset($_SESSION['pending_order'])) {
        throw new Exception('No pending order found in session');
    }

    $orderData = $_SESSION['pending_order']['order_data'] ?? [];
    $orderSummary = $_SESSION['pending_order']['order_summary'] ?? [];
    
    if (empty($orderData) || empty($orderSummary)) {
        throw new Exception('Invalid order data');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert into orders table with all required fields
        $stmt = $pdo->prepare("INSERT INTO orders (
            user_id, 
            customer_name, 
            contact_number, 
            total_amount, 
            amount_paid, 
            change_amount, 
            order_type, 
            payment_method, 
            payment_status, 
            payment_option,
            status, 
            final_total, 
            remaining_balance,
            notification_status,
            order_date,
            table_name,
            type_of_order
        ) VALUES (
            :user_id, 
            :customer_name, 
            :contact_number, 
            :total_amount, 
            :amount_paid, 
            :change_amount, 
            :order_type, 
            :payment_method, 
            :payment_status, 
            :payment_option,
            :status, 
            :final_total, 
            :remaining_balance,
            1, -- notification_status
            NOW(),
            :table_name,
            :type_of_order
        )");
        
        // Get order details from session
        $tableName = isset($orderData['table_name']) ? $orderData['table_name'] : 'N/A';
        $orderType = isset($orderData['order_type']) ? $orderData['order_type'] : 'Dine-in';
        $userId = $_SESSION['user_id'] ?? 0; // Default to 0 if not logged in
        
        // Initialize customer details
        $customerName = 'Walk-in Customer';
        $contactNumber = 'N/A';
        
        // If user is logged in, fetch their details from the database
        if ($userId > 0) {
            $userStmt = $pdo->prepare("SELECT first_name, last_name, contact_number FROM userss WHERE id = ?");
            $userStmt->execute([$userId]);
            $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $customerName = trim($userData['first_name'] . ' ' . $userData['last_name']);
                $contactNumber = $userData['contact_number'] ?? 'N/A';
            }
        }
        
        // Use customer details from order data if provided (overrides user data if exists)
        if (isset($orderData['customer_name'])) {
            $customerName = $orderData['customer_name'];
        }
        if (isset($orderData['contact_number'])) {
            $contactNumber = $orderData['contact_number'];
        }
        
        // Calculate amounts
        // Get payment option (full or downpayment)
        $paymentOption = $orderSummary['payment_option'] ?? 'full';
        
        // Calculate amounts based on payment option
        $totalAmount = $orderSummary['total'];
        $amountPaid = ($paymentOption === 'partial') ? ($totalAmount * 0.5) : $totalAmount;
        $remainingBalance = ($paymentOption === 'partial') ? ($totalAmount - $amountPaid) : 0;
        $changeAmount = 0; // No change for exact payment
        $finalTotal = $totalAmount; // Adjust if there are any discounts
        
        $stmt->execute([
            ':user_id' => $userId,
            ':customer_name' => $customerName,
            ':contact_number' => $contactNumber,
            ':total_amount' => $totalAmount,
            ':amount_paid' => $amountPaid,
            ':change_amount' => $changeAmount,
            ':order_type' => $orderType,
            ':payment_method' => $orderSummary['payment_method'] ?? 'cash',
            ':payment_status' => 'paid',
            ':payment_option' => $paymentOption,
            ':status' => ($paymentOption === 'partial') ? 'Pending' : 'Pending',
            ':final_total' => $finalTotal,
            ':remaining_balance' => $remainingBalance,
            ':table_name' => $tableName,
            ':type_of_order' => $orderType
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Insert order items into order_items table
        if (!empty($orderData['items']) && is_array($orderData['items'])) {
            foreach ($orderData['items'] as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (
                    order_id, 
                    item_name, 
                    quantity, 
                    unit_price
                ) VALUES (
                    :order_id,
                    :item_name,
                    :quantity,
                    :unit_price
                )");
                
                $quantity = $item['quantity'] ?? 1;
                // Get the price from the correct key in the item array
                $unitPrice = $item['price'] ?? ($item['unit_price'] ?? 0);
                
                // Ensure unit price is a valid number
                $unitPrice = is_numeric($unitPrice) ? $unitPrice : 0;
                
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':item_name' => $item['name'],
                    ':quantity' => $quantity,
                    ':unit_price' => $unitPrice
                ]);
                
                $orderItemId = $pdo->lastInsertId();
                
                // Insert addons if any
                if (!empty($item['addons']) && is_array($item['addons'])) {
                    foreach ($item['addons'] as $addon) {
                        $stmt = $pdo->prepare("INSERT INTO order_item_addons (
                            order_item_id, 
                            addon_name, 
                            addon_price
                        ) VALUES (
                            :order_item_id,
                            :addon_name,
                            :addon_price
                        )");
                            
                        $stmt->execute([
                            ':order_item_id' => $orderItemId,
                            ':addon_name' => $addon['name'],
                            ':addon_price' => $addon['price']
                        ]);
                    }
                }
            }
        }
        
        // If we got here, everything is good - commit the transaction
        $pdo->commit();
        
        // Clear only cart-related session data
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        if (isset($_SESSION['order_data'])) {
            unset($_SESSION['order_data']);
        }
        
        // Set success message
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $orderId;
        
        // Set success response
        $response = [
            'success' => true,
            'message' => 'Order completed successfully!',
            'orderId' => $orderId,
            'redirect' => 'cafes.php'
        ];
        
        // If this is not an AJAX request, redirect to cafes.php
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header('Location: ' . $response['redirect']);
            exit;
        }
        
    } catch (Exception $e) {
        // Something went wrong, rollback the transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e; // Re-throw to be caught by the outer catch
    }
    
} catch (Exception $e) {
    // Log the error
    error_log('Order completion error: ' . $e->getMessage());
    
    // Set error response
    $response = [
        'success' => false,
        'message' => 'Error completing order: ' . $e->getMessage(),
        'orderId' => null,
        'redirect' => ''
    ];
    
    // If this is not an AJAX request, show error
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        $_SESSION['error'] = $response['message'];
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }
}

// Return JSON response
echo json_encode($response);
?>
