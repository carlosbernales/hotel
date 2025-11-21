<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'advance_order_errors.log');

// Include database connection
require_once 'db.php';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Log the request 
error_log("get_advance_order.php called with params: " . json_encode($_GET));

try {
    // Check parameters
    $bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : null;
    $advanceOrderId = isset($_GET['advance_order_id']) ? intval($_GET['advance_order_id']) : null;
    
    if (!$bookingId && !$advanceOrderId) {
        throw new Exception('Either booking_id or advance_order_id is required');
    }
    
    // Debug information
    error_log("Looking up advance order with booking_id: $bookingId, advance_order_id: $advanceOrderId");
    
    // Build the query based on which ID was provided
    if ($advanceOrderId) {
        $query = "SELECT * FROM advance_table_orders WHERE id = ?";
        $param = $advanceOrderId;
        error_log("Searching by advance_order_id: $advanceOrderId");
    } else {
        $query = "SELECT * FROM advance_table_orders WHERE table_booking_id = ?";
        $param = $bookingId;
        error_log("Searching by table_booking_id: $bookingId");
    }
    
    // Prepare and execute the query
    $stmt = $con->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        throw new Exception("Database error: " . $con->error);
    }
    
    $stmt->bind_param('i', $param);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        error_log("No records found in advance_table_orders for parameter: $param");
        
        // Try searching in regular orders table as fallback
        if ($bookingId) {
            error_log("Trying to find order information in orders table for table_id: $bookingId");
            $fallbackQuery = "SELECT o.*, oi.item_name, oi.quantity, oi.unit_price 
                            FROM orders o 
                            LEFT JOIN order_items oi ON o.id = oi.order_id
                            WHERE o.table_id = ? AND o.order_type = 'advance'";
            
            $fallbackStmt = $con->prepare($fallbackQuery);
            $fallbackStmt->bind_param('i', $bookingId);
            $fallbackStmt->execute();
            $fallbackResult = $fallbackStmt->get_result();
            
            if ($fallbackResult && $fallbackResult->num_rows > 0) {
                error_log("Found fallback order information in orders table");
                
                // Get booking details
                $bookingInfo = [];
                if ($bookingId) {
                    $bookingQuery = "SELECT * FROM table_bookings WHERE id = ?";
                    $bookingStmt = $con->prepare($bookingQuery);
                    $bookingStmt->bind_param('i', $bookingId);
                    $bookingStmt->execute();
                    $bookingResult = $bookingStmt->get_result();
                    
                    if ($bookingResult && $bookingResult->num_rows > 0) {
                        $bookingInfo = $bookingResult->fetch_assoc();
                    }
                }
                
                // Build order items array from order_items table
                $orderItems = [];
                $totalAmount = 0;
                
                // Reset the pointer
                $fallbackResult->data_seek(0);
                
                // Process the order info
                $orderInfo = null;
                
                while ($row = $fallbackResult->fetch_assoc()) {
                    if (!$orderInfo) {
                        $orderInfo = $row;
                    }
                    
                    if (!empty($row['item_name'])) {
                        $itemPrice = floatval($row['unit_price']);
                        $itemQuantity = intval($row['quantity']);
                        $subtotal = $itemPrice * $itemQuantity;
                        $totalAmount += $subtotal;
                        
                        $orderItems[] = [
                            'id' => count($orderItems) + 1,
                            'name' => $row['item_name'],
                            'price' => $itemPrice,
                            'quantity' => $itemQuantity
                        ];
                    }
                }
                
                // Create response with order data from orders table
                $response = [
                    'success' => true,
                    'message' => 'Order found in orders table (fallback)',
                    'booking_details' => [
                        'booking_id' => $bookingId,
                        'customer_name' => $orderInfo['customer_name'] ?? $bookingInfo['name'] ?? 'Unknown',
                        'total_amount' => $orderInfo['total_amount'] ?? $totalAmount,
                        'payment_option' => $bookingInfo['payment_option'] ?? 'full',
                        'payment_method' => $orderInfo['payment_method'] ?? $bookingInfo['payment_method'] ?? 'cash',
                        'amount_to_pay' => $orderInfo['amount_paid'] ?? $totalAmount,
                        'order_items' => $orderItems,
                        'order_items_count' => count($orderItems),
                        'has_advance_order' => true,
                        'created_at' => $orderInfo['order_date'] ?? date('Y-m-d H:i:s'),
                        'source' => 'orders_table'
                    ]
                ];
                
                echo json_encode($response);
                exit;
            }
            
            // If no fallback order, return booking information
            $bookingQuery = "SELECT * FROM table_bookings WHERE id = ?";
            $bookingStmt = $con->prepare($bookingQuery);
            $bookingStmt->bind_param('i', $bookingId);
            $bookingStmt->execute();
            $bookingResult = $bookingStmt->get_result();
            
            if ($bookingResult && $bookingResult->num_rows > 0) {
                $booking = $bookingResult->fetch_assoc();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'No advance order found, but booking exists',
                    'booking_details' => [
                        'booking_id' => $booking['id'],
                        'customer_name' => $booking['name'],
                        'package_type' => $booking['package_type'],
                        'date' => $booking['booking_date'],
                        'time' => $booking['booking_time'],
                        'guest_count' => $booking['num_guests'],
                        'contact_number' => $booking['contact_number'],
                        'total_amount' => $booking['total_amount'],
                        'amount_to_pay' => $booking['amount_to_pay'],
                        'payment_method' => $booking['payment_method'],
                        'has_advance_order' => false
                    ]
                ]);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'No order information found']);
        exit;
    }
    
    // Get the advance order data
    $advanceOrder = $result->fetch_assoc();
    error_log("Found advance order record: " . json_encode($advanceOrder));
    
    // Parse the JSON order items
    $orderItems = json_decode($advanceOrder['order_items'], true);
    if ($orderItems === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON parse error: " . json_last_error_msg() . " on: " . substr($advanceOrder['order_items'], 0, 100));
        $orderItems = [];
    }
    
    // Get booking details if we have the booking_id
    $bookingDetails = [];
    if ($bookingId || $advanceOrder['table_booking_id']) {
        $bid = $bookingId ?: $advanceOrder['table_booking_id'];
        $bookingQuery = "SELECT * FROM table_bookings WHERE id = ?";
        $bookingStmt = $con->prepare($bookingQuery);
        $bookingStmt->bind_param('i', $bid);
        $bookingStmt->execute();
        $bookingResult = $bookingStmt->get_result();
        
        if ($bookingResult->num_rows > 0) {
            $booking = $bookingResult->fetch_assoc();
            $bookingDetails = [
                'booking_id' => $booking['id'],
                'package_type' => $booking['package_type'],
                'date' => $booking['booking_date'],
                'time' => $booking['booking_time'],
                'guest_count' => $booking['num_guests'],
                'contact_number' => $booking['contact_number'],
                'email' => $booking['email_address'],
                'special_requests' => $booking['special_requests']
            ];
        }
    }
    
    // Construct the response
    $response = [
        'success' => true,
        'booking_details' => array_merge($bookingDetails, [
            'advance_order_id' => $advanceOrder['id'],
            'table_booking_id' => $advanceOrder['table_booking_id'],
            'customer_name' => $advanceOrder['customer_name'],
            'total_amount' => $advanceOrder['total_amount'],
            'payment_option' => $advanceOrder['payment_option'],
            'payment_method' => $advanceOrder['payment_method'],
            'amount_to_pay' => $advanceOrder['amount_to_pay'],
            'order_items' => $orderItems,
            'order_items_count' => count($orderItems),
            'has_advance_order' => true,
            'created_at' => $advanceOrder['created_at'],
            'source' => 'advance_table_orders'
        ])
    ];
    
    // Return the response
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error in get_advance_order.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 