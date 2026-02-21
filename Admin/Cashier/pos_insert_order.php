<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to the user
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/pos_errors.log');

require_once 'db.php';
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Function to send JSON response and exit
function sendJsonResponse($data) {
    echo json_encode($data);
    exit;
}

// Function to handle errors
function handleError($message, $code = 400) {
    http_response_code($code);
    sendJsonResponse(['status' => 'error', 'message' => $message]);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Invalid request method', 405);
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check for JSON parsing errors
if (json_last_error() !== JSON_ERROR_NONE) {
    handleError('Invalid JSON data: ' . json_last_error_msg(), 400);
}

// Validate required fields
if (!isset($data['items']) || empty($data['items'])) {
    handleError('No items in order');
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        throw new Exception('User not logged in or session expired');
    }

    // Calculate order total
    $subtotal = $data['subtotal'] ?? 0;
    $discount = $data['discount'] ?? 0;
    $total = $data['total'] ?? 0;

    // Insert order header into orders_table
    $stmt = $pdo->prepare("
        INSERT INTO orders_table (
            user_id,
            order_id,
            order_type,
            total,
            discount_amount,
            discount_type,
            discount_percentage,
            amount_paid,
            change_amount,
            payment_method,
            type_of_order,
            status,
            date_time,
            order_at,
            id_number
        ) VALUES (
            :user_id,
            :order_id,
            :order_type,
            :total,
            :discount_amount,
            :discount_type,
            :discount_percentage,
            :amount_paid,
            :change_amount,
            :payment_method,
            :type_of_order,
            'processing',
            NOW(),
            NOW(),
            :id_number
        )
    ");

    // Generate order ID (format: ORD-YYYYMMDD-XXXX)
    $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Get discount information based on guest type
    $guestType = $data['guest_type'] ?? 'regular';
    $discountPercentage = 0;
    $discountAmount = 0;
    $discountTypeName = null;
    
    // Only process discount if guest type is not regular
    if ($guestType !== 'regular') {
        // First try to match by exact name
        $discountStmt = $pdo->prepare("SELECT id, name, percentage FROM discount_types WHERE name = :name AND is_active = 1 LIMIT 1");
        $discountStmt->execute([':name' => $guestType]);
        $discountInfo = $discountStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($discountInfo) {
            $discountTypeName = $discountInfo['name'];
            $discountPercentage = (float)$discountInfo['percentage'];
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        } else {
            // If no exact match, try to match by normalized guest type values
            $guestTypeMapping = [
                'pwd' => ['name' => 'PWD', 'percentage' => 10],
                'senior_citizen' => ['name' => 'Senior Citizen', 'percentage' => 10],
                'student' => ['name' => 'Student', 'percentage' => 10]
            ];
            
            if (isset($guestTypeMapping[$guestType])) {
                $mapped = $guestTypeMapping[$guestType];
                // Try to find this discount type in the database
                $discountStmt = $pdo->prepare("SELECT id, name, percentage FROM discount_types WHERE name = :name AND is_active = 1 LIMIT 1");
                $discountStmt->execute([':name' => $mapped['name']]);
                $discountInfo = $discountStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($discountInfo) {
                    $discountTypeName = $discountInfo['name'];
                    $discountPercentage = (float)$discountInfo['percentage'];
                } else {
                    $discountTypeName = $mapped['name'];
                    $discountPercentage = $mapped['percentage'];
                }
                $discountAmount = ($subtotal * $discountPercentage) / 100;
            }
        }
    }
    
    // If discount amount is explicitly provided and is greater than calculated amount, use it
    if (isset($data['discount']) && $data['discount'] > 0) {
        $providedDiscountAmount = (float)$data['discount'];
        if ($providedDiscountAmount > $discountAmount) {
            $discountAmount = $providedDiscountAmount;
            if ($subtotal > 0) {
                $discountPercentage = ($discountAmount / $subtotal) * 100;
            }
        }
    }

    $orderData = [
        ':user_id' => $userId,
        ':order_id' => $orderNumber,
        ':order_type' => 'Walkin',
        ':total' => $total,
        ':discount_amount' => $discountAmount,
        ':discount_type' => $discountTypeName ?: null,
        ':discount_percentage' => $discountPercentage > 0 ? $discountPercentage : null,
        ':amount_paid' => $data['amount_received'] ?? $total,
        ':change_amount' => $data['change'] ?? 0,
        ':payment_method' => $data['payment_method'] ?? 'cash',
        ':type_of_order' => $data['order_type'] ?? 'dine-in',
        ':id_number' => $data['id_number'] ?? null
    ];

    $stmt->execute($orderData);
    $orderId = $pdo->lastInsertId();

    // Insert into orders_table_type if it's a dine-in order with table information
    if (($data['order_type'] ?? '') === 'dine-in' && !empty($data['table_type_id']) && !empty($data['table_number'])) {
        // Get table type information
        $tableTypeStmt = $pdo->prepare("SELECT * FROM table_types WHERE id = :table_type_id");
        $tableTypeStmt->execute([':table_type_id' => $data['table_type_id']]);
        $tableType = $tableTypeStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get table number information
        $tableNumberStmt = $pdo->prepare("SELECT * FROM table_number WHERE id = :table_number_id");
        $tableNumberStmt->execute([':table_number_id' => $data['table_number']]);
        $tableNumberInfo = $tableNumberStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tableType && $tableNumberInfo) {
            // Insert the booking with the correct table information
            $insertStmt = $pdo->prepare("
                INSERT INTO orders_table_type (
                    table_booking_fk_id, table_type_fk_id, table_number_fk_id, table_name, table_number
                ) VALUES (
                    :table_booking_fk_id, :table_type_fk_id, :table_number_fk_id, :table_name, :table_number
                )");
            
            $insertStmt->execute([
                ':table_booking_fk_id' => $orderId,
                ':table_type_fk_id' => $data['table_type_id'],
                ':table_number_fk_id' => $data['table_number'],
                ':table_name' => $tableType['table_name'],
                ':table_number' => $tableNumberInfo['table_number']
            ]);
        }
    }

    // Insert order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items (
            order_fk_id,
            item_name,
            quantity,
            unit_price
        ) VALUES (
            :order_id,
            :item_name,
            :quantity,
            :price
        )
    ");

    // Insert order item addons
    $addonStmt = $pdo->prepare("
        INSERT INTO order_item_addons (
            order_item_fk_id,
            addon_name,
            price,
            quantity
        ) VALUES (
            :order_item_id,
            :addon_name,
            :price,
            :quantity
        )
    ");

    foreach ($data['items'] as $item) {
        // Insert order item
        $stmt->execute([
            ':order_id' => $orderId,
            ':item_name' => $item['name'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);

        $orderItemId = $pdo->lastInsertId();

        // Insert addons if any
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $addonStmt->execute([
                    ':order_item_id' => $orderItemId,
                    ':addon_name' => $addon['name'],
                    ':price' => $addon['price'],
                    ':quantity' => 1 // Each entry in item['addons'] represents one addon instance
                ]);
            }
        }
    }


    // Commit transaction
    $pdo->commit();

    // Return success response
    sendJsonResponse([
        'status' => 'success',
        'message' => 'Order placed successfully',
        'order_id' => $orderId,
        'order_number' => $orderNumber
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
    
    $errorMessage = 'Failed to process order: ' . $e->getMessage();
    error_log($errorMessage . '\n' . $e->getTraceAsString());
    
    // Ensure we're not sending HTML errors
    if (headers_sent()) {
        // If headers were already sent, we can't change them anymore
        error_log('Headers already sent, could not send JSON response');
        exit;
    }
    
    handleError($errorMessage, 500);
}