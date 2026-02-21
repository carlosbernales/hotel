<?php
require_once 'db_con.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // If JSON decode failed, try to get from $_POST
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = $_POST;
    }

    // Validate required fields
    if (empty($data['order_data'])) {
        throw new Exception('Order data is required');
    }

    // Decode the order data
    $orderData = is_string($data['order_data']) ? json_decode($data['order_data'], true) : $data['order_data'];
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid order data format');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // 1. Insert into orders_table
        $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);
        $currentDate = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? null;
        
        $orderInsert = $pdo->prepare("
            INSERT INTO orders_table (
                user_id, order_id, order_type, firstname, lastname, 
                contact, email, date_time, total, downpayment, balance, 
                payment, payment_method, payment_option, status
            ) VALUES (
                :user_id, :order_id, :order_type, :firstname, :lastname, 
                :contact, :email, :date_time, :total,
                :downpayment, :balance, 
                :payment, :payment_method, 'downpayment', 'pending'
            )
        ");

        // Calculate values for orders_table
        $total = floatval($orderData['total'] ?? 0);
        
        // Check for downpayment option (case-insensitive and handles different formats)
        $paymentOption = strtolower(trim($orderData['payment_option'] ?? ''));
        $isDownpayment = (strpos($paymentOption, 'downpayment') !== false) || 
                        (strpos($paymentOption, 'down payment') !== false) ||
                        (strpos($paymentOption, '50%') !== false) ||
                        ($paymentOption === 'partial');
        
        // Calculate the total from items if available
        if (isset($orderData['items']) && is_array($orderData['items'])) {
            $calculatedTotal = 0;
            foreach ($orderData['items'] as $item) {
                $itemPrice = floatval($item['price'] ?? 0);
                $itemQty = intval($item['quantity'] ?? 1);
                $calculatedTotal += $itemPrice * $itemQty;
                
                // Add addons to the total if they exist
                if (!empty($item['addons']) && is_array($item['addons'])) {
                    foreach ($item['addons'] as $addon) {
                        $addonPrice = floatval($addon['price'] ?? 0);
                        $addonQty = intval($addon['quantity'] ?? 1);
                        $calculatedTotal += $addonPrice * $addonQty;
                    }
                }
            }
            
            // Use the calculated total if it's greater than 0
            if ($calculatedTotal > 0) {
                $total = $calculatedTotal;
            }
        }
        
        if ($isDownpayment) {
            // For downpayments, amount_paid is half of total, remaining_balance is the other half
            $downpayment = $total / 2;
            $remainingBalance = $total - $downpayment;
            $amountPaid = $downpayment; // Amount paid is the downpayment amount
            
            // Log the downpayment calculation for debugging
            error_log("Downpayment calculation - Total: $total, Downpayment: $downpayment, Remaining: $remainingBalance");
        } else {
            // For full payments, amount_paid equals total, no remaining balance
            $amountPaid = $total;
            $remainingBalance = 0;
            $downpayment = 0;
        }

        // Execute the order insert
        $orderInsert->execute([
            ':user_id' => $userId,
            ':order_id' => $orderNumber,
            ':order_type' => 'Regular Order',
            ':firstname' => $orderData['firstname'] ?? null,
            ':lastname' => $orderData['lastname'] ?? null,
            ':contact' => $orderData['contact'] ?? null,
            ':email' => $orderData['email'] ?? null,
            ':date_time' => $currentDate,
            ':total' => $total,
            ':downpayment' => $downpayment,
            ':balance' => $remainingBalance,
            ':payment' => 'paid',
            ':payment_method' => $orderData['payment_method'] ?? 'cash'
        ]);

        $orderId = $pdo->lastInsertId();

        // 2. Insert order items
        if (!empty($orderData['items']) && is_array($orderData['items'])) {
            $itemInsert = $pdo->prepare("
                INSERT INTO order_items (order_fk_id, item_name, quantity, unit_price)
                VALUES (:order_fk_id, :item_name, :quantity, :unit_price)
            ");

            $addonInsert = $pdo->prepare("
                INSERT INTO order_item_addons (order_item_fk_id, addon_name, price, quantity)
                VALUES (:order_item_fk_id, :addon_name, :price, :quantity)
            ");

            foreach ($orderData['items'] as $item) {
                // Insert the main item
                $itemInsert->execute([
                    ':order_fk_id' => $orderId,
                    ':item_name' => $item['name'] ?? 'Unknown Item',
                    ':quantity' => $item['quantity'] ?? 1,
                    ':unit_price' => $item['price'] ?? 0
                ]);

                $orderItemId = $pdo->lastInsertId();

                // Insert addons if any
                if (!empty($item['addons']) && is_array($item['addons'])) {
                    foreach ($item['addons'] as $addon) {
                        $addonInsert->execute([
                            ':order_item_fk_id' => $orderItemId,
                            ':addon_name' => $addon['name'] ?? 'Addon',
                            ':price' => $addon['price'] ?? 0,
                            ':quantity' => $item['quantity'] ?? 1
                        ]);
                    }
                }
            }
        }

        // 3. Insert notification when order is completed
        if ($userId) {
            $notificationInsert = $pdo->prepare("
                INSERT INTO notifications (user_id, order_id, title, message, type, created_at)
                VALUES (:user_id, :order_id, :title, :message, :type, NOW())
            ");

            $notificationInsert->execute([
                ':user_id' => $userId,
                ':order_id' => $orderId, // Use the database ID for foreign key constraint
                ':title' => 'Order Pending',
                ':message' => "Your order #{$orderNumber} has been paid successfully!",
                ':type' => 'Order'
            ]);
        }


        // Commit the transaction
        $pdo->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $orderNumber,
            'order_data' => $orderData
        ]);

    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process order: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}