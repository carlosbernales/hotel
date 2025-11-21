<?php
// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'table_reservation_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug incoming request
error_log("=== NEW TABLE RESERVATION REQUEST ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Content Type: " . $_SERVER['CONTENT_TYPE']);

// Get JSON input and log it
$input = file_get_contents('php://input');
error_log("Raw Input: " . $input);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Error: User not logged in");
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Include database connection
require_once 'db.php';

// Log received input
error_log("Processing table reservation request");
error_log("Content-Type: " . $_SERVER['CONTENT_TYPE']);

// Debug database connection first
try {
    if (!$con) {
        error_log("CRITICAL ERROR: Database connection failed");
    } else {
        error_log("Database connection successful");
        // Check if orders table exists
        $tableCheckResult = $con->query("SHOW TABLES LIKE 'orders'");
        if ($tableCheckResult) {
            error_log("ORDERS TABLE CHECK: Exists = " . ($tableCheckResult->num_rows > 0 ? 'Yes' : 'No'));
            
            // Check orders table structure
            $structureResult = $con->query("DESCRIBE `orders`");
            if ($structureResult) {
                error_log("ORDERS TABLE STRUCTURE:");
                while ($row = $structureResult->fetch_assoc()) {
                    error_log(" - " . $row['Field'] . " (" . $row['Type'] . ")");
                }
            }
        }
    }
} catch (Exception $e) {
    error_log("Error checking database: " . $e->getMessage());
}

try {
    // Decode JSON data
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        throw new Exception('Invalid JSON data received: ' . json_last_error_msg());
    }

    // Log decoded data
    error_log("Decoded Data:");
    error_log("- Package Type: " . ($data['packageType'] ?? 'Not set'));
    error_log("- Customer Name: " . ($data['customerName'] ?? 'Not set'));
    error_log("- Booking Date: " . ($data['reservationDate'] ?? 'Not set'));
    error_log("- Booking Time: " . ($data['reservationTime'] ?? 'Not set'));
    
    // Log advance order data if present
    if (isset($data['advanceOrder'])) {
        error_log("Advance Order Present:");
        error_log("- Items Count: " . (isset($data['advanceOrder']['items']) ? count($data['advanceOrder']['items']) : 0));
        error_log("- Items Data: " . json_encode($data['advanceOrder']['items']));
        error_log("- Total Amount: " . ($data['advanceOrder']['totalAmount'] ?? 'Not set'));
        error_log("- Payment Option: " . ($data['advanceOrder']['paymentOption'] ?? 'Not set'));
    } else {
        error_log("No Advance Order Data Present");
    }

    // Start transaction
    $con->begin_transaction();
    error_log("Transaction started");

    // Sanitize input data
    $packageType = $con->real_escape_string($data['packageType'] ?? '');
    $name = $con->real_escape_string($data['customerName'] ?? '');
    $contactNumber = $con->real_escape_string($data['contactNumber'] ?? '');
    $email = $con->real_escape_string($data['email'] ?? '');
    $bookingDate = $con->real_escape_string($data['reservationDate'] ?? '');
    $bookingTime = $con->real_escape_string($data['reservationTime'] ?? '');
    $guestCount = intval($data['guestCount'] ?? 0);
    $specialRequests = $con->real_escape_string($data['specialRequests'] ?? '');
    $userId = intval($_SESSION['user_id']);
    $totalAmount = $con->real_escape_string($data['totalAmount'] ?? '0');
    $amountToPay = $con->real_escape_string($data['amountToPay'] ?? '0');
    $paymentMethod = $con->real_escape_string($data['paymentMethod'] ?? '');
    $paymentOption = $con->real_escape_string($data['paymentOption'] ?? 'full');
    $status = 'Pending';
    $paymentStatus = 'Pending';

    // Check for existing reservations
    $checkSql = "SELECT COUNT(*) as count FROM table_bookings 
                 WHERE booking_date = ? AND booking_time = ? AND status != 'Cancelled'";
    $stmt = $con->prepare($checkSql);
    if (!$stmt) {
        error_log("Prepare failed for reservation check: " . $con->error);
        throw new Exception("Database error while checking reservations");
    }
    
    $stmt->bind_param("ss", $bookingDate, $bookingTime);
    if (!$stmt->execute()) {
        error_log("Execute failed for reservation check: " . $stmt->error);
        throw new Exception("Failed to check existing reservations");
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception('This time slot is already booked');
    }

    // Insert booking
    $sql = "INSERT INTO table_bookings (
        user_id, package_name, name, contact_number, email_address,
        booking_date, booking_time, num_guests, special_requests,
        total_amount, amount_to_pay, payment_method, payment_option,
        payment_status, status, package_type, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed for booking insert: " . $con->error);
        throw new Exception("Database error while creating booking");
    }

    $stmt->bind_param(
        "issssssissssssss",
        $userId, $packageType, $name, $contactNumber, $email,
        $bookingDate, $bookingTime, $guestCount, $specialRequests,
        $totalAmount, $amountToPay, $paymentMethod, $paymentOption,
        $paymentStatus, $status, $packageType
    );

    if (!$stmt->execute()) {
        error_log("Execute failed for booking insert: " . $stmt->error);
        throw new Exception("Failed to create booking");
    }

    $bookingId = $con->insert_id;
    error_log("Successfully created booking with ID: " . $bookingId);

    // Handle advance orders if they exist
    if (isset($data['advanceOrder']) && isset($data['advanceOrder']['items']) && is_array($data['advanceOrder']['items'])) {
        error_log("=== ADVANCE ORDER PROCESSING START ===");
        error_log("Processing advance order for booking ID: " . $bookingId);
        
        try {
            // First verify the table exists
            $createTableSql = "CREATE TABLE IF NOT EXISTS `advance_table_orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `table_booking_id` int(11) NOT NULL,
                `customer_name` varchar(255) NOT NULL,
                `order_items` text NOT NULL,
                `total_amount` decimal(10,2) NOT NULL,
                `payment_option` varchar(50) NOT NULL,
                `payment_method` varchar(50) NOT NULL,
                `amount_to_pay` decimal(10,2) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `table_booking_id` (`table_booking_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            if (!$con->query($createTableSql)) {
                error_log("Failed to create/verify advance_table_orders table: " . $con->error);
            }
            
            // Prepare the data
            $orderItemsJson = json_encode($data['advanceOrder']['items']);
            if (!$orderItemsJson) {
                error_log("Failed to encode order items: " . json_last_error_msg());
                $orderItemsJson = '[]';
            }
            
            // Use explicit values from the order
            $advanceTotalAmount = $data['advanceOrder']['totalAmount'] ?? $totalAmount;
            $advancePaymentOption = $data['advanceOrder']['paymentOption'] ?? $paymentOption;
            $advancePaymentMethod = $data['advanceOrder']['paymentMethod'] ?? $paymentMethod;
            $advanceAmountToPay = $data['advanceOrder']['amountToPay'] ?? $amountToPay;
            
            error_log("Inserting advance order with data:");
            error_log("Booking ID: " . $bookingId);
            error_log("Customer: " . $name);
            error_log("Total: " . $advanceTotalAmount);
            
            // Direct SQL insertion
            $sql = "INSERT INTO `advance_table_orders` 
                   (`table_booking_id`, `customer_name`, `order_items`, `total_amount`, 
                    `payment_option`, `payment_method`, `amount_to_pay`) 
                   VALUES 
                   (" . intval($bookingId) . ", 
                    '" . $con->real_escape_string($name) . "',
                    '" . $con->real_escape_string($orderItemsJson) . "',
                    " . floatval($advanceTotalAmount) . ",
                    '" . $con->real_escape_string($advancePaymentOption) . "',
                    '" . $con->real_escape_string($advancePaymentMethod) . "',
                    " . floatval($advanceAmountToPay) . ")";
            
            error_log("Executing SQL: " . $sql);
            
            if ($con->query($sql)) {
                $advanceOrderId = $con->insert_id;
                error_log("Successfully inserted advance order with ID: " . $advanceOrderId);
                $GLOBALS['advanceOrderIdCreated'] = $advanceOrderId;
                
                // Verify the insertion
                $verify = $con->query("SELECT * FROM advance_table_orders WHERE id = " . $advanceOrderId);
                if ($verify && $verify->num_rows > 0) {
                    $data = $verify->fetch_assoc();
                    error_log("Verified advance order exists with data: " . json_encode($data));
                } else {
                    error_log("WARNING: Could not verify advance order after insertion");
                }
            } else {
                error_log("ERROR: Failed to insert advance order: " . $con->error);
                // Try alternative insertion method
                $altSql = "INSERT INTO advance_table_orders SET 
                          table_booking_id = " . intval($bookingId) . ",
                          customer_name = '" . $con->real_escape_string($name) . "',
                          order_items = '" . $con->real_escape_string($orderItemsJson) . "',
                          total_amount = " . floatval($advanceTotalAmount) . ",
                          payment_option = '" . $con->real_escape_string($advancePaymentOption) . "',
                          payment_method = '" . $con->real_escape_string($advancePaymentMethod) . "',
                          amount_to_pay = " . floatval($advanceAmountToPay);
                
                if ($con->query($altSql)) {
                    $advanceOrderId = $con->insert_id;
                    error_log("Successfully inserted advance order using alternative method. ID: " . $advanceOrderId);
                    $GLOBALS['advanceOrderIdCreated'] = $advanceOrderId;
                } else {
                    error_log("CRITICAL: Both insertion methods failed. Last error: " . $con->error);
                }
            }
        } catch (Exception $e) {
            error_log("Exception in advance order processing: " . $e->getMessage());
        }
        
        error_log("=== ADVANCE ORDER PROCESSING END ===");
    } else {
        error_log("No advance order data to process");
    }

    // Commit transaction
    $con->commit();

    // Format success response
    $response = [
        'success' => true,
        'message' => 'Reservation successful',
        'booking_details' => [
            'booking_id' => $bookingId,
            'customer_name' => $name,
            'package_type' => $packageType,
            'date' => $bookingDate,
            'time' => $bookingTime,
            'guest_count' => $guestCount,
            'contact_number' => $contactNumber,
            'total_amount' => $totalAmount,
            'amount_to_pay' => $amountToPay,
            'payment_method' => $paymentMethod
        ]
    ];

    // Add advance order ID to response if it was created
    if (isset($GLOBALS['advanceOrderIdCreated']) && $GLOBALS['advanceOrderIdCreated']) {
        $response['booking_details']['advance_order_id'] = $GLOBALS['advanceOrderIdCreated'];
        error_log("Added advance_order_id to response: " . $GLOBALS['advanceOrderIdCreated']);
    }

    // Check if order was created and add order details
    if (isset($orderId) && $orderId) {
        $response['booking_details']['order_id'] = $orderId;
        $response['booking_details']['order_created'] = true;
        $response['booking_details']['order_items_count'] = count($data['advanceOrder']['items'] ?? []);
        
        // Include the actual order items in the response to ensure they're available
        if (isset($data['advanceOrder']) && isset($data['advanceOrder']['items'])) {
            $response['booking_details']['order_items'] = $data['advanceOrder']['items'];
            $response['booking_details']['order_payment_option'] = $data['advanceOrder']['paymentOption'] ?? $paymentOption;
            $response['booking_details']['order_payment_method'] = $data['advanceOrder']['paymentMethod'] ?? $paymentMethod;
            $response['booking_details']['order_total_amount'] = $data['advanceOrder']['totalAmount'] ?? $totalAmount;
            $response['booking_details']['order_amount_to_pay'] = $data['advanceOrder']['amountToPay'] ?? $amountToPay;
        }
        
        $response['debug_info'] = [
            'order_inserted' => true,
            'order_id' => $orderId,
            'table_booking_id' => $bookingId,
            'order_sql_executed' => $orderSql ?? 'N/A'
        ];
    } else {
        $response['booking_details']['order_created'] = false;
        $response['booking_details']['order_error'] = 'No order ID was generated';
        error_log("WARNING: No order ID was generated for booking ID $bookingId");
    }

    error_log("Final response: " . json_encode($response));

} catch (Exception $e) {
    // Rollback transaction in case of error
    $con->rollback();
    
    error_log("Error in table reservation: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => $e->getMessage()
    ];
}

// Always ensure we insert to orders table for table packages, even if no advance order
// This ensures every table booking appears in the orders table
if (isset($response['success']) && $response['success'] === true && !isset($response['booking_details']['order_id'])) {
    try {
        // Insert into orders table
        $customerName = mysqli_real_escape_string($con, $name);
        $contactNum = mysqli_real_escape_string($con, $contactNumber);
        $paymentMethodEsc = mysqli_real_escape_string($con, $paymentMethod);
        $currentDate = date('Y-m-d H:i:s');
        
        // Insert the table package reservation into orders table
        $orderSql = "INSERT INTO orders (customer_name, contact_number, user_id, table_id, order_date, status, payment_method, total_amount, amount_paid, change_amount, order_type) 
                    VALUES ('$customerName', '$contactNum', $userId, $bookingId, '$currentDate', 'pending', '$paymentMethodEsc', $totalAmount, $amountToPay, 0, 'advance')";
        
        error_log("EXECUTING ORDER SQL: $orderSql");
        
        $orderResult = mysqli_query($con, $orderSql);
        
        if ($orderResult) {
            $orderId = mysqli_insert_id($con);
            error_log("SUCCESS: Created order ID $orderId for table package without advance order");
            
            // Only insert menu items if they exist in advance order
            if (isset($data['advanceOrder']) && isset($data['advanceOrder']['items']) && is_array($data['advanceOrder']['items'])) {
                error_log("Found advance order items: " . json_encode($data['advanceOrder']['items']));
                
                // Insert menu items
                $itemSql = "INSERT INTO order_items (order_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?)";
                $itemStmt = $con->prepare($itemSql);
                
                foreach ($data['advanceOrder']['items'] as $item) {
                    $itemName = $item['name'];
                    $itemQuantity = intval($item['quantity'] ?? 1);
                    $itemPrice = floatval($item['price']);
                    
                    $itemStmt->bind_param("isid", 
                        $orderId,
                        $itemName,
                        $itemQuantity,
                        $itemPrice
                    );
                    
                    if ($itemStmt->execute()) {
                        error_log("Added menu item $itemName to order $orderId");
                        
                        // Get the order item ID for potential addons
                        $orderItemId = $con->insert_id;
                        
                        // Add addons if any
                        if (!empty($item['addons'])) {
                            $addonSql = "INSERT INTO order_item_addons (order_item_id, addon_name, addon_price) VALUES (?, ?, ?)";
                            $addonStmt = $con->prepare($addonSql);
                            
                            foreach ($item['addons'] as $addon) {
                                $addonName = $addon['name'];
                                $addonPrice = floatval($addon['price']);
                                
                                $addonStmt->bind_param("isd",
                                    $orderItemId,
                                    $addonName,
                                    $addonPrice
                                );
                                
                                if ($addonStmt->execute()) {
                                    error_log("Added addon $addonName to item $itemName");
                                } else {
                                    error_log("Failed to add addon: " . $addonStmt->error);
                                }
                            }
                        }
                    } else {
                        error_log("Failed to add menu item: " . $itemStmt->error);
                    }
                }
            } else {
                // If no advance order items, insert the package type as a placeholder
                $packageItemSql = "INSERT INTO order_items (order_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?)";
                $packageStmt = $con->prepare($packageItemSql);
                $quantity = 1;
                
                $packageStmt->bind_param("isid", 
                    $orderId,
                    $packageType,
                    $quantity,
                    $totalAmount
                );
                
                if ($packageStmt->execute()) {
                    error_log("Added package type $packageType as placeholder for order $orderId");
                } else {
                    error_log("Failed to add package type: " . $packageStmt->error);
                }
            }
            
            // Add order ID to response
            $response['booking_details']['order_id'] = $orderId;
            $response['booking_details']['order_created'] = true;
            
            // Verify the order was created
            $verifyOrderSql = "SELECT o.*, oi.* FROM orders o 
                             LEFT JOIN order_items oi ON o.id = oi.order_id 
                             WHERE o.id = ?";
            $verifyStmt = $con->prepare($verifyOrderSql);
            $verifyStmt->bind_param("i", $orderId);
            $verifyStmt->execute();
            $verifyResult = $verifyStmt->get_result();
            
            if ($verifyResult && $verifyResult->num_rows > 0) {
                $verifyOrder = mysqli_fetch_assoc($verifyResult);
                error_log("VERIFICATION SUCCESS: Order #$orderId exists in database with items");
                
                $response['booking_details']['order_verified'] = true;
                $response['booking_details']['order_data'] = $verifyOrder;
            }
        } else {
            error_log("ERROR: Failed to create order for table package. " . mysqli_error($con));
        }
    } catch (Exception $orderEx) {
        error_log("Exception creating order for table package: " . $orderEx->getMessage());
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response); 