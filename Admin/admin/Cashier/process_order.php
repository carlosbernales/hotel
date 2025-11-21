<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Get the raw POST data and decode it
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    // Start transaction
    $conn->begin_transaction();

    // 1. Insert into orders table
    $orderQuery = "INSERT INTO orders (
        total_amount, discount_type, id_number, discount_amount,
        payment_method, payment_status, status, order_type,
        contact_number, order_date, amount_paid, change_amount
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

    $orderStmt = $conn->prepare($orderQuery);
    
    // Set default values
    $totalAmount = $data['total_amount'];
    $discountType = $data['discount_type'] ?? 'none';
    $idNumber = $data['id_number'] ?? '';
    $discountAmount = $data['discount_amount'] ?? 0;
    $paymentMethod = $data['payment_method'];
    $paymentStatus = 'pending';
    // Set status based on order type
    $status = $data['booking_type'] === 'walk-in' ? 'processing' : 'pending';
    $orderType = $data['booking_type'];
    $contactNumber = $data['contact_number'];
    $amountPaid = $data['amount_paid'] ?? $totalAmount;
    $changeAmount = $data['change_amount'] ?? 0;
    
    // Debug log
    error_log("Order Data: " . print_r([
        'totalAmount' => $totalAmount,
        'discountType' => $discountType,
        'idNumber' => $idNumber,
        'discountAmount' => $discountAmount,
        'paymentMethod' => $paymentMethod,
        'paymentStatus' => $paymentStatus,
        'status' => $status,
        'orderType' => $orderType,
        'contactNumber' => $contactNumber,
        'amountPaid' => $amountPaid,
        'changeAmount' => $changeAmount
    ], true));
    
    $orderStmt->bind_param(
        "dssdsssssdd",
        $totalAmount,
        $discountType,
        $idNumber,
        $discountAmount,
        $paymentMethod,
        $paymentStatus,
        $status,
        $orderType,
        $contactNumber,
        $amountPaid,
        $changeAmount
    );
    
    if (!$orderStmt->execute()) {
        throw new Exception('Failed to insert order: ' . $orderStmt->error);
    }

    $orderId = $conn->insert_id;

    // 2. Insert order items
    $itemQuery = "INSERT INTO order_items (
        order_id,
        item_name,
        quantity,
        unit_price
    ) VALUES (?, ?, ?, ?)";

    $itemStmt = $conn->prepare($itemQuery);

    foreach ($data['items'] as $item) {
        $itemStmt->bind_param(
            "isid",
            $orderId,
            $item['item_name'],
            $item['quantity'],
            $item['unit_price']
        );
        
        if (!$itemStmt->execute()) {
            throw new Exception('Failed to insert order item: ' . $itemStmt->error);
        }

        $orderItemId = $conn->insert_id;

        // 3. Insert addons if any
        if (!empty($item['addons'])) {
            $addonQuery = "INSERT INTO order_item_addons (
                order_item_id,
                addon_name,
                addon_price
            ) VALUES (?, ?, ?)";

            $addonStmt = $conn->prepare($addonQuery);

            foreach ($item['addons'] as $addon) {
                $addonStmt->bind_param(
                    "isd",
                    $orderItemId,
                    $addon['addon_name'],
                    $addon['addon_price']
                );
                
                if (!$addonStmt->execute()) {
                    throw new Exception('Failed to insert addon: ' . $addonStmt->error);
                }
            }
        }
    }

    // If everything is successful, commit the transaction
    $conn->commit();

    // Send success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);

} catch (Exception $e) {
    // If there's an error, rollback the transaction
    if (isset($conn)) {
        $conn->rollback();
    }

    // Send error response
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 