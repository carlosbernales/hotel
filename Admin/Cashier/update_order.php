<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate required fields
if (!isset($data['order_id']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get the actual database ID from order_id
    $stmt = $pdo->prepare("SELECT id FROM orders_table WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $data['order_id']]);
    $orderRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orderRecord) {
        throw new Exception('Order not found');
    }
    
    $actualOrderId = $orderRecord['id'];


    // Update order information
    $updateStmt = $pdo->prepare("
        UPDATE orders_table SET 
            firstname = :firstname,
            lastname = :lastname,
            contact = :contact,
            email = :email,
            type_of_order = :order_type,
            payment_method = :payment_method,
            discount_type = :discount_type,
            discount_percentage = :discount_percentage,
            discount_amount = :discount_amount,
            total = :total,
            downpayment = :downpayment,
            amount_paid = :amount_paid,
            balance = :balance
        WHERE id = :id
    ");

    $orderType = $data['order_type'] ?? 'Dine-in';
    $discountType = $data['discount_type'] ?? '';
    $discountPercentage = floatval($data['discount_percentage'] ?? 0);
    $subtotal = floatval($data['subtotal'] ?? 0);
    
    // Use values calculated on frontend
    $discountAmount = floatval($data['discount_amount'] ?? 0);
    $total = floatval($data['total'] ?? 0);
    $downpayment = floatval($data['downpayment'] ?? 0);
    $amountPaid = floatval($data['amount_paid'] ?? 0);
    $balance = floatval($data['balance'] ?? 0);

    $updateStmt->execute([
        ':firstname' => $data['firstname'] ?? '',
        ':lastname' => $data['lastname'] ?? '',
        ':contact' => $data['contact'] ?? '',
        ':email' => $data['email'] ?? '',
        ':order_type' => $orderType,
        ':payment_method' => $data['payment_method'] ?? 'Cash',
        ':discount_type' => $discountType ?: null,
        ':discount_percentage' => $discountPercentage > 0 ? $discountPercentage : null,
        ':discount_amount' => $discountAmount,
        ':total' => $total,
        ':downpayment' => $downpayment,
        ':amount_paid' => $amountPaid,
        ':balance' => $balance,
        ':id' => $actualOrderId
    ]);

    // Handle multiple table assignments
    $selectedTables = $data['selected_tables'] ?? [];
    
    // Get existing table assignments for this order
    $existingTablesStmt = $pdo->prepare("SELECT * FROM orders_table_type WHERE table_booking_fk_id = :order_id");
    $existingTablesStmt->execute([':order_id' => $actualOrderId]);
    $existingTables = $existingTablesStmt->fetchAll(PDO::FETCH_ASSOC);
    $existingTableIds = array_column($existingTables, 'table_number');

    if ($orderType === 'Dine-in') {
        // Table(s) are required for dine-in orders
        if (empty($selectedTables)) {
            throw new Exception('Table selection is required for dine-in orders');
        }
        
        // Get table IDs from selected tables
        $selectedTableIds = array_column($selectedTables, 'table_id');
        
        // Remove tables that are no longer selected and free them up
        $tablesToRemove = array_diff($existingTableIds, $selectedTableIds);
        foreach ($tablesToRemove as $tableIdToRemove) {
            // Find the existing table record to get the table_number_fk_id
            $existingTableToRemove = null;
            foreach ($existingTables as $existingTable) {
                if ($existingTable['table_number'] == $tableIdToRemove) {
                    $existingTableToRemove = $existingTable;
                    break;
                }
            }
            
            if ($existingTableToRemove) {
                // Remove table assignment
                $deleteTableStmt = $pdo->prepare("DELETE FROM orders_table_type WHERE id = :id");
                $deleteTableStmt->execute([':id' => $existingTableToRemove['id']]);
                
                // Free up the table only if no other active orders are using it
                $checkOtherOrdersStmt = $pdo->prepare("
                    SELECT COUNT(*) as active_orders 
                    FROM orders_table_type ott 
                    JOIN orders_table o ON ott.table_booking_fk_id = o.id 
                    WHERE ott.table_number = :table_id AND o.status != 'Completed'
                ");
                $checkOtherOrdersStmt->execute([':table_id' => $tableIdToRemove]);
                $activeOrders = $checkOtherOrdersStmt->fetchColumn();
                
                if ($activeOrders == 0) {
                    $freeTableStmt = $pdo->prepare("UPDATE table_number SET status = 'available' WHERE id = :table_id");
                    $freeTableStmt->execute([':table_id' => $tableIdToRemove]);
                }
            }
        }
        
        // Add new table assignments for newly selected tables
        $tablesToAdd = array_diff($selectedTableIds, $existingTableIds);
        foreach ($tablesToAdd as $tableIdToAdd) {
            // Find the table details from selected tables
            $selectedTable = null;
            foreach ($selectedTables as $table) {
                if ($table['table_id'] == $tableIdToAdd) {
                    $selectedTable = $table;
                    break;
                }
            }
            
            if ($selectedTable) {
                // Get table type details
                $tableDetailsStmt = $pdo->prepare("
                    SELECT tn.id as table_number_id, tn.table_number, tt.id as table_type_id, tt.table_name
                    FROM table_number tn
                    JOIN table_types tt ON tn.table_type_fk_id = tt.id
                    WHERE tn.id = :table_id
                ");
                $tableDetailsStmt->execute([':table_id' => $selectedTable['table_id']]);
                $tableDetails = $tableDetailsStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($tableDetails) {
                    // Check if table is available (not assigned to other active orders)
                    $checkAvailabilityStmt = $pdo->prepare("
                        SELECT COUNT(*) as conflicts 
                        FROM orders_table_type ott 
                        JOIN orders_table o ON ott.table_booking_fk_id = o.id 
                        WHERE ott.table_number = :table_id AND o.status != 'Completed' AND o.id != :current_order_id
                    ");
                    $checkAvailabilityStmt->execute([
                        ':table_id' => $selectedTable['table_id'],
                        ':current_order_id' => $actualOrderId
                    ]);
                    $conflicts = $checkAvailabilityStmt->fetchColumn();
                    
                    if ($conflicts > 0) {
                        throw new Exception("Table {$tableDetails['table_name']} {$tableDetails['table_number']} is already assigned to another active order");
                    }
                    
                    // Create new table assignment
                    $insertTableStmt = $pdo->prepare("
                        INSERT INTO orders_table_type (
                            table_booking_fk_id, table_type_fk_id, table_number, table_name, table_number_fk_id
                        ) VALUES (
                            :table_booking_fk_id, :table_type_fk_id, :table_number, :table_name, :table_number_fk_id
                        )");
                    $insertTableStmt->execute([
                        ':table_booking_fk_id' => $actualOrderId,
                        ':table_type_fk_id' => $tableDetails['table_type_id'],
                        ':table_number' => $tableDetails['table_number'],
                        ':table_name' => $tableDetails['table_name'],
                        ':table_number_fk_id' => $selectedTable['table_id']
                    ]);
                }
            }
        }
    } else {
        // Not a dine-in order, remove all table assignments and free up tables
        if (!empty($existingTables)) {
            foreach ($existingTables as $existingTable) {
                // Free up the table only if no other active orders are using it
                $checkOtherOrdersStmt = $pdo->prepare("
                    SELECT COUNT(*) as active_orders 
                    FROM orders_table_type ott 
                    JOIN orders_table o ON ott.table_booking_fk_id = o.id 
                    WHERE ott.table_number = :table_id AND o.status != 'Completed'
                ");
                $checkOtherOrdersStmt->execute([':table_id' => $existingTable['table_number']]);
                $activeOrders = $checkOtherOrdersStmt->fetchColumn();
                
                if ($activeOrders == 0) {
                    $freeTableStmt = $pdo->prepare("UPDATE table_number SET status = 'available' WHERE id = :table_id");
                    $freeTableStmt->execute([':table_id' => $existingTable['table_number']]);
                }
            }
            
            // Remove all table assignments
            $deleteTableStmt = $pdo->prepare("DELETE FROM orders_table_type WHERE table_booking_fk_id = :order_id");
            $deleteTableStmt->execute([':order_id' => $actualOrderId]);
        }
    }

    // Delete existing order items and addons
    $deleteItemsStmt = $pdo->prepare("DELETE FROM order_items WHERE order_fk_id = :order_id");
    $deleteItemsStmt->execute([':order_id' => $actualOrderId]);

    // Insert updated order items
    $insertItemStmt = $pdo->prepare("
        INSERT INTO order_items (
            order_fk_id, item_name, quantity, unit_price
        ) VALUES (
            :order_id, :item_name, :quantity, :price
        )
    ");

    $insertAddonStmt = $pdo->prepare("
        INSERT INTO order_item_addons (
            order_item_fk_id, addon_name, price, quantity
        ) VALUES (
            :order_item_id, :addon_name, :price, :quantity
        )
    ");

    foreach ($data['items'] as $item) {
        // Insert order item
        $insertItemStmt->execute([
            ':order_id' => $actualOrderId,
            ':item_name' => $item['item_name'],
            ':quantity' => $item['quantity'],
            ':price' => $item['unit_price']
        ]);

        $orderItemId = $pdo->lastInsertId();

        // Insert addons if any
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $insertAddonStmt->execute([
                    ':order_item_id' => $orderItemId,
                    ':addon_name' => $addon['addon_name'],
                    ':price' => $addon['price'],
                    ':quantity' => 1
                ]);
            }
        }
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully'
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
    
    error_log("Error updating order: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
}
?>
