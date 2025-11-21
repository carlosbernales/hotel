<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Get the raw POST data and decode it
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    // Add this after getting the $data from json_decode
    error_log("Received discount data: " . print_r([
        'discount_type' => $data['discount_type'] ?? 'not set',
        'id_number' => $data['id_number'] ?? 'not set',
        'discount_amount' => $data['discount_amount'] ?? 'not set'
    ], true));

    // Replace the table name query with direct table number assignment
    $tableName = null;
    if ($data['table_package_id'] && $data['order_type'] === 'dine-in') {
        $tableName = 'Table ' . $data['table_package_id'];
    }

    // Start transaction
    $conn->begin_transaction();

    // 1. Insert into orders table
    $orderQuery = "INSERT INTO orders (
        total_amount, 
        contact_number,
        customer_name,
        payment_method, 
        discount_type, 
        id_number, 
        discount_amount, 
        amount_paid, 
        change_amount, 
        status, 
        order_type, 
        table_id,
        table_name,
        type_of_order,
        user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($orderQuery);
    
    // Set default values
    $totalAmount = $data['total_amount'];
    $discountType = isset($data['discount_type']) && !empty($data['discount_type']) ? $data['discount_type'] : 'none';
    $idNumber = isset($data['id_number']) ? $data['id_number'] : '';
    $discountAmount = isset($data['discount_amount']) ? $data['discount_amount'] : 0;
    $paymentMethod = $data['payment_method'];
    $paymentStatus = 'pending';
    $status = 'processing';
    $orderType = 'walk-in';
    $bookingType = 'walk-in';
    $contactNumber = $data['contact_number'];
    $amountPaid = isset($data['amount_paid']) ? $data['amount_paid'] : $totalAmount;
    $changeAmount = isset($data['change_amount']) ? $data['change_amount'] : 0;
    $tablePackageId = isset($data['table_package_id']) ? $data['table_package_id'] : null;
    $typeOfOrder = isset($data['order_type']) ? $data['order_type'] : null;
    $customerName = isset($data['nickname']) && !empty($data['nickname']) ? $data['nickname'] : '';
    
    // Check if user_id exists and throw error if not provided
    if (!isset($data['user_id']) || empty($data['user_id'])) {
        throw new Exception('User ID is required');
    }
    $userId = $data['user_id'];

    // Add debug log for user_id specifically
    error_log("User ID for order: " . $userId);

    // Debug log
    error_log("Order Data: " . print_r([
        'totalAmount' => $totalAmount,
        'discountType' => $discountType,
        'customerName' => $customerName,
        'idNumber' => $idNumber,
        'discountAmount' => $discountAmount,
        'paymentMethod' => $paymentMethod,
        'paymentStatus' => $paymentStatus,
        'status' => $status,
        'bookingType' => $bookingType,
        'orderType' => $orderType,
        'contactNumber' => $contactNumber,
        'amountPaid' => $amountPaid,
        'changeAmount' => $changeAmount,
        'tablePackageId' => $tablePackageId,
        'tableName' => $tableName,
        'typeOfOrder' => $typeOfOrder,
        'userId' => $userId
    ], true));
    
    $stmt->bind_param(
        "dsssssdddssissi",
        $totalAmount,
        $contactNumber,
        $customerName,
        $paymentMethod,
        $discountType,
        $idNumber,
        $discountAmount,
        $amountPaid,
        $changeAmount,
        $status,
        $orderType,
        $tablePackageId,
        $tableName,
        $typeOfOrder,
        $userId
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert order: ' . $stmt->error);
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