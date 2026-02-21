<?php
// Make sure no output is sent before headers
ob_start();
session_start();

// Include the database connection
require_once 'db_con.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Failed to read input data');
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }

    if (empty($data)) {
        throw new Exception('No data received');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Debug: Log incoming data
    error_log('Incoming data: ' . print_r($data, true));
    error_log('Session user_id: ' . ($_SESSION['user_id'] ?? 'not set'));

    // Debug: Log payment related data
    error_log('total_amount: ' . ($data['total_amount'] ?? 'not set'));
    error_log('payment_amount: ' . ($data['payment_amount'] ?? 'not set'));
    error_log('payment_option: ' . ($data['payment_option'] ?? 'not set'));

    // Calculate values with additional validation
    $total = filter_var($data['total_amount'] ?? 0, FILTER_VALIDATE_FLOAT, [
        'options' => ['default' => 0, 'min_range' => 0]
    ]);
    
    $amount_paid = filter_var($data['payment_amount'] ?? 0, FILTER_VALIDATE_FLOAT, [
        'options' => ['default' => 0, 'min_range' => 0]
    ]);
    
    $payment_option = $data['payment_option'] ?? 'full';
    $downpayment = ($payment_option === 'partial') ? $amount_paid : 0;
    $balance = $total - $amount_paid;
    $status = 'confirmed';

    // Debug: Log calculated values
    error_log('Calculated values - Total: ' . $total . 
              ', Amount Paid: ' . $amount_paid . 
              ', Downpayment: ' . $downpayment . 
              ', Balance: ' . $balance . 
              ', Status: ' . $status);

    // Get booking date and time from session
    $bookingDate = $_SESSION['booking_data']['date'] ?? date('Y-m-d');
    $bookingTime = $_SESSION['booking_data']['time'] ?? '12:00:00';
    $bookingDateTime = date('Y-m-d H:i:s', strtotime("$bookingDate $bookingTime"));

    $orderStmt = $pdo->prepare("
    INSERT INTO orders_table (
        user_id, order_id, order_type, firstname, lastname, contact, 
        email, date_time, total, balance, downpayment, 
        amount_paid, payment_option, payment_method, status
    ) VALUES (
        :user_id, :order_id, :order_type, :firstname, :lastname, :contact, 
        :email, :date_time, :total, :balance, :downpayment, 
        :amount_paid, :payment_option, :payment_method, :status
    )
");

// Generate a unique order ID
$order_id = 'ORD-' . time() . '-' . mt_rand(1000, 9999);

try {
    $params = [
        ':user_id' => $_SESSION['user_id'] ?? null,
        ':order_id' => $order_id,
        ':order_type' => 'advance',
        ':firstname' => $data['firstname'] ?? '',
        ':lastname' => $data['lastname'] ?? '',
        ':contact' => $data['contact'] ?? '',
        ':email' => $data['email'] ?? '',
        ':date_time' => $bookingDateTime,
        ':total' => $total,
        ':balance' => $balance,
        ':downpayment' => $downpayment,
        ':amount_paid' => $amount_paid,
        ':payment_option' => $payment_option,
        ':payment_method' => $data['payment_method'] ?? 'online',
        ':status' => $status
    ];
    
    // Debug: Log the parameters being bound
    error_log('SQL Parameters: ' . print_r($params, true));
    
    $result = $orderStmt->execute($params);
    
    if (!$result) {
        $errorInfo = $orderStmt->errorInfo();
        throw new Exception('Database error: ' . ($errorInfo[2] ?? 'Unknown error'));
    }
    
    error_log('Order inserted successfully. Order ID: ' . $order_id);
    
} catch (Exception $e) {
    error_log('Error executing order insert: ' . $e->getMessage());
    throw $e; // Re-throw to be caught by the outer try-catch
}

    $orderId = $pdo->lastInsertId();

    // 2. Insert into orders_table_type for each table
    if (!empty($data['tables'])) {
        $tableStmt = $pdo->prepare("
            INSERT INTO orders_table_type (
                table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number
            ) VALUES (
                :table_booking_fk_id, :table_type_fk_id, :table_number_fk_id, :table_name, :table_number
            )
        ");

        foreach ($data['tables'] as $table) {
            // Get available table of the selected type
            $tableStmt = $pdo->prepare("
                SELECT tn.id, tn.table_number, tt.table_name 
                FROM table_number tn
                JOIN table_types tt ON tn.table_type_fk_id = tt.id
                WHERE tn.table_type_fk_id = :type_id 
                AND tn.status = 'available'
                LIMIT 1
                FOR UPDATE
            ");
            
            $tableStmt->execute([':type_id' => $table['id']]);
            $availableTable = $tableStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$availableTable) {
                throw new Exception('No available tables of the selected type');
            }
            
            // Insert the booking with the correct table number
            $insertStmt = $pdo->prepare("
                INSERT INTO orders_table_type (
                    table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number
                ) VALUES (
                    :table_booking_fk_id, :table_type_fk_id, :table_number_fk_id, :table_name, :table_number
                )");
            
            $insertStmt->execute([
                ':table_booking_fk_id' => $orderId,
                ':table_type_fk_id' => $table['id'],
                ':table_number_fk_id' => $availableTable['id'],
                ':table_name' => $availableTable['table_name'],
                ':table_number' => $availableTable['table_number']
            ]);
      
        }
    }

    // 3. Insert order items
    if (!empty($data['order'])) {
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (
                order_fk_id, item_name, quantity, unit_price
            ) VALUES (
                :order_fk_id, :item_name, :quantity, :unit_price
            )
        ");

        $addonStmt = $pdo->prepare("
            INSERT INTO order_item_addons (
                order_item_fk_id, addon_name, price, quantity
            ) VALUES (
                :order_item_fk_id, :addon_name, :price, :quantity
            )
        ");

        foreach ($data['order'] as $item) {
            $itemStmt->execute([
                ':order_fk_id' => $orderId,
                ':item_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['price']
            ]);
            
            $orderItemId = $pdo->lastInsertId();

            // Insert addons if any
            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addon) {
                    $addonStmt->execute([
                        ':order_item_fk_id' => $orderItemId,
                        ':addon_name' => $addon['name'],
                        ':price' => $addon['price'],
                        ':quantity' => $addon['quantity'] ?? 1
                    ]);
                }
            }
        }
    }

    // Commit the transaction
    $pdo->commit();

    // Clear session data after successful booking
    if (isset($_SESSION['booking_data'])) {
        unset($_SESSION['booking_data']);
    }
    if (isset($_SESSION['completed_booking'])) {
        unset($_SESSION['completed_booking']);
    }

    $response = [
        'success' => true,
        'message' => 'Booking completed successfully',
        'order_id' => $orderId
    ];

} catch (Exception $e) {
    // Rollback the transaction if it was started
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log the error
    error_log('Booking Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    $response = [
        'success' => false,
        'message' => 'Error processing booking: ' . $e->getMessage()
    ];
}

// Clear any output that might have been generated
ob_clean();

// Send the JSON response
echo json_encode($response);
exit;