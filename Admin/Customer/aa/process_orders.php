<?php
require_once 'session.php';
require_once 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set proper content type for JSON response
header('Content-Type: application/json');

/**
 * Send an error response and exit
 * 
 * @param string $message Error message
 * @param int $code HTTP status code
 * @return void
 */
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

/**
 * Process a regular order (non-advance)
 * 
 * @param array $orderData Order data
 * @param PDO $pdo Database connection
 * @return array Response with status and message
 */
function processRegularOrder($orderData, $pdo) {
    // Debug log the received data
    error_log("Processing regular order: " . print_r($orderData, true));
    
    try {
        // Validate required fields for regular orders
        $requiredFields = ['items', 'payment_method', 'payment_option', 'total_amount', 'final_total'];
        foreach ($requiredFields as $field) {
            if (!isset($orderData[$field]) || empty($orderData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        
        // Calculate payment amount based on payment option
        $totalAmount = floatval($orderData['final_total']);
        $paymentAmount = $orderData['payment_option'] === 'partial' ? ($totalAmount * 0.5) : $totalAmount;
        
        // Calculate remaining balance for partial payments
        $remainingBalance = $orderData['payment_option'] === 'partial' ? ($totalAmount * 0.5) : 0;
        
        // Set payment status based on payment method and option
        if ($orderData['payment_method'] === 'cash') {
            $paymentStatus = 'Pending';
        } else if (in_array($orderData['payment_method'], ['gcash', 'maya'])) {
            $paymentStatus = $orderData['payment_option'] === 'partial' ? 'Partially Paid' : 'Processing';
        } else {
            $paymentStatus = 'Pending';
        }
        
    
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert the order
        $orderSql = "INSERT INTO orders (
            user_id, total_amount, extra_fee, order_type,
            payment_method, payment_reference, payment_status, status,
            final_total, order_date, pickup_notes, payment_proof, remaining_balance
        ) VALUES (
            :user_id, :total_amount, :extra_fee, :order_type,
            :payment_method, :payment_reference, :payment_status, :status,
            :final_total, NOW(), :pickup_notes, :payment_proof, :remaining_balance
        )";
        
        $orderParams = [
            ':user_id' => $_SESSION['user_id'],
            ':total_amount' => $totalAmount,
            ':extra_fee' => floatval($orderData['extra_fee'] ?? 0),
            ':order_type' => 'regular',
            ':payment_method' => $orderData['payment_method'],
            ':payment_status' => $paymentStatus,
            ':status' => 'Pending',
            ':final_total' => $totalAmount,
            ':pickup_notes' => $orderData['special_requests'] ?? null,
            ':payment_proof' => null,
            ':remaining_balance' => $remainingBalance
        ];
        
        $orderStmt = $pdo->prepare($orderSql);
        if (!$orderStmt->execute($orderParams)) {
            throw new Exception("Failed to insert order: " . implode(", ", $orderStmt->errorInfo()));
        }
        
        $orderId = $pdo->lastInsertId();
        error_log("Created regular order with ID: " . $orderId);
        
        // Insert order items
        insertOrderItems($orderId, $orderData['items'], $pdo);
        
        // Commit transaction
        $pdo->commit();
        
        return [
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'order_id' => $orderId
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Delete uploaded file if it exists
        if ($paymentProofPath) {
            $fullPath = '../../uploads/payment_proofs/' . $paymentProofPath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        // Log the error and rethrow
        error_log("Regular order processing error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Process payment for an order
 * 
 * @param array $orderData Order data
 * @param PDO $pdo Database connection
 * @return array Response with status, message, and payment URL if applicable
 * @throws Exception If payment processing fails
 */
function processPaymentOrder($orderData, $pdo) {
    // Debug log the received data
    error_log("Processing payment order: " . print_r($orderData, true));
    
    try {
        // Validate required fields for payment orders
        $requiredFields = ['items', 'payment_method', 'total_amount', 'final_total'];
        foreach ($requiredFields as $field) {
            if (!isset($orderData[$field]) || empty($orderData[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        
        // Calculate total amount
        $totalAmount = floatval($orderData['final_total']);
        
        // Set payment status based on payment method
        $paymentStatus = 'pending';
        $status = 'Pending';
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert the order
        $orderSql = "INSERT INTO orders (
            user_id, total_amount, extra_fee, order_type,
            payment_method, payment_status, status,
            final_total, order_date, pickup_notes, remaining_balance
        ) VALUES (
            :user_id, :total_amount, :extra_fee, :order_type,
            :payment_method, :payment_status, :status,
            :final_total, NOW(), :pickup_notes, :remaining_balance
        )";
        
        $orderParams = [
            ':user_id' => $_SESSION['user_id'],
            ':total_amount' => $totalAmount,
            ':extra_fee' => floatval($orderData['extra_fee'] ?? 0),
            ':order_type' => 'regular',
            ':payment_method' => $orderData['payment_method'],
            ':payment_status' => $paymentStatus,
            ':status' => $status,
            ':final_total' => $totalAmount,
            ':pickup_notes' => $orderData['special_requests'] ?? null,
            ':remaining_balance' => 0 // No remaining balance for online payments
        ];
        
        $orderStmt = $pdo->prepare($orderSql);
        if (!$orderStmt->execute($orderParams)) {
            throw new Exception("Failed to insert order: " . implode(", ", $orderStmt->errorInfo()));
        }
        
        $orderId = $pdo->lastInsertId();
        error_log("Created payment order with ID: " . $orderId);
        
        // Insert order items
        insertOrderItems($orderId, $orderData['items'], $pdo);
        
        // Commit transaction
        $pdo->commit();
        
        // Prepare response
        $response = [
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'order_id' => $orderId,
            'payment_status' => $paymentStatus,
            'order_type' => 'payment'
        ];
        
        // If payment method is online, we'll handle the payment processing
        if (in_array(strtolower($orderData['payment_method']), ['gcash', 'card', 'grab_pay'])) {
            require_once 'create_paymongo_checkout.php';
            
            // Create description for the payment
            $description = "Order #$orderId - " . date('M d, Y h:i A');
            
            // Create success and cancel URLs
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $successUrl = "$baseUrl/Admin/Customed/aa/complete_order.php?order_id=$orderId&payment=success";
            $cancelUrl = "$baseUrl/Admin/Customed/aa/payment_information.php?order_id=$orderId&payment=cancelled";
            
            // Create checkout session
            $checkoutResult = createPaymongoCheckout(
                $totalAmount,
                $description,
                $successUrl,
                $cancelUrl,
                $orderId
            );
            
            if (isset($checkoutResult['data']['attributes']['checkout_url'])) {
                $response['payment_url'] = $checkoutResult['data']['attributes']['checkout_url'];
                $response['requires_payment'] = true;
            } else {
                throw new Exception('Failed to create payment checkout');
            }
        }
        
        return $response;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log the error and rethrow
        error_log("Payment order processing error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Insert order items and their addons
 * 
 * @param int $orderId The order ID
 * @param array $items Array of order items
 * @param PDO $pdo Database connection
 * @return void
 * @throws Exception If insertion fails
 */
function insertOrderItems($orderId, $items, $pdo) {
    foreach ($items as $item) {
        $itemSql = "INSERT INTO order_items (order_id, item_name, quantity, unit_price) 
                   VALUES (:order_id, :item_name, :quantity, :unit_price)";
        
        $itemParams = [
            ':order_id' => $orderId,
            ':item_name' => $item['name'],
            ':quantity' => intval($item['quantity']),
            ':unit_price' => floatval($item['price'])
        ];
        
        $itemStmt = $pdo->prepare($itemSql);
        if (!$itemStmt->execute($itemParams)) {
            throw new Exception("Failed to insert order item: " . implode(", ", $itemStmt->errorInfo()));
        }
        
        $orderItemId = $pdo->lastInsertId();
        
        // Insert addons if any
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $addonSql = "INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) 
                            VALUES (:order_item_id, :addon_name, :addon_price)";
                
                $addonParams = [
                    ':order_item_id' => $orderItemId,
                    ':addon_name' => $addon['name'],
                    ':addon_price' => floatval($addon['price'])
                ];
                
                $addonStmt = $pdo->prepare($addonSql);
                if (!$addonStmt->execute($addonParams)) {
                    throw new Exception("Failed to insert addon: " . implode(", ", $addonStmt->errorInfo()));
                }
            }
        }
    }
}

/**
 * Send a JSON response and exit
 * 
 * @param array $data Response data
 * @param int $statusCode HTTP status code
 * @return void
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Set CORS headers if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Main execution block
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate content type
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false && 
            strpos($contentType, 'application/x-www-form-urlencoded') === false) {
            throw new Exception('Content-Type must be application/json or application/x-www-form-urlencoded', 415);
        }

        // Check database connection
        if (!isset($pdo)) {
            global $con;
            if (!isset($con)) {
                throw new Exception('Database connection failed');
            }
            
            // Create PDO connection using mysqli connection details
            $host = 'localhost';
            $dbname = 'hotelms';
            $username = 'root';
            $password = '';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                error_log("Connection failed: " . $e->getMessage());
                throw new Exception('Database connection failed: ' . $e->getMessage(), 500);
            }
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Please log in to place an order', 401);
        }

        // Get and validate order data
        $rawData = file_get_contents('php://input');
        if (strpos($contentType, 'application/json') !== false) {
            $orderData = json_decode($rawData, true);
        } else {
            parse_str($rawData, $postData);
            $orderData = json_decode($postData['order_data'] ?? '{}', true);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid order data format: ' . json_last_error_msg(), 400);
        }

        if (empty($orderData)) {
            throw new Exception('No order data provided', 400);
        }
        
        // Process the order based on the order type
        $response = [];
        
        try {
            if (isset($orderData['order_type']) && $orderData['order_type'] === 'payment') {
                // Process payment order (online payment)
                $result = processPaymentOrder($orderData, $pdo);
                
                if (isset($result['payment_url']) && $result['requires_payment']) {
                    $response = [
                        'status' => 'redirect',
                        'redirect_url' => $result['payment_url'],
                        'order_id' => $result['order_id']
                    ];
                } else {
                    $response = [
                        'status' => 'success',
                        'message' => $result['message'],
                        'order_id' => $result['order_id']
                    ];
                }
            } else if (!isset($orderData['order_type']) || $orderData['order_type'] === 'regular') {
                // Process regular order (cash on delivery)
                $result = processRegularOrder($orderData, $pdo);
                $response = $result;
            } else {
                throw new Exception('Invalid order type', 400);
            }
            
            // Log successful order processing
            error_log('Order processed successfully: ' . ($response['order_id'] ?? 'unknown'));
            
            // Send success response
            sendJsonResponse($response);
            
        } catch (Exception $e) {
            // Log the error with context
            error_log('Order processing error: ' . $e->getMessage() . 
                     '\nOrder data: ' . print_r($orderData, true));
            throw $e;
        }
        
    } catch (Exception $e) {
        $statusCode = method_exists($e, 'getCode') && $e->getCode() >= 400 ? $e->getCode() : 500;
        sendJsonResponse([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $statusCode
        ], $statusCode);
    }
} else {
    sendJsonResponse([
        'status' => 'error',
        'message' => 'Method not allowed',
        'allowed_methods' => ['POST']
    ], 405);
}


