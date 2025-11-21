<?php
// Include database connection
require_once 'db.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Enable error reporting for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'order_system_test.log');

// Function to sanitize output
function h($str) {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order System Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .result-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .warning {
            color: orange;
        }
        table {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Order System Diagnostic Tool</h1>
        
        <div class="alert alert-info">
            <strong>Purpose:</strong> This tool helps diagnose and fix issues with the order system, especially for table reservations.
        </div>
        
        <?php
        // If the form is submitted to test a specific feature
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Test database tables existence
            if (isset($_POST['test_tables'])) {
                echo '<div class="test-section">';
                echo '<h3>Database Tables Test</h3>';
                
                $tables = ['orders', 'order_items', 'table_bookings'];
                $results = [];
                
                foreach ($tables as $table) {
                    $query = "SHOW TABLES LIKE '{$table}'";
                    $result = $con->query($query);
                    $exists = $result && $result->num_rows > 0;
                    
                    $results[$table] = $exists;
                    
                    if ($exists) {
                        // Get table structure
                        $structure = "DESCRIBE `{$table}`";
                        $structResult = $con->query($structure);
                        $columns = [];
                        if ($structResult) {
                            while ($row = $structResult->fetch_assoc()) {
                                $columns[] = $row;
                            }
                        }
                        $results["{$table}_columns"] = $columns;
                        
                        // Count records
                        $countQuery = "SELECT COUNT(*) as count FROM `{$table}`";
                        $countResult = $con->query($countQuery);
                        if ($countResult && $countResult->num_rows > 0) {
                            $countRow = $countResult->fetch_assoc();
                            $results["{$table}_count"] = $countRow['count'];
                        } else {
                            $results["{$table}_count"] = 0;
                        }
                    }
                }
                
                echo '<div class="result-box">';
                foreach ($tables as $table) {
                    echo '<h4>' . h($table) . ' Table</h4>';
                    if ($results[$table]) {
                        echo '<p class="success">✓ Table exists</p>';
                        echo '<p>Record count: ' . $results["{$table}_count"] . '</p>';
                        
                        echo '<h5>Columns:</h5>';
                        echo '<table class="table table-sm table-bordered">';
                        echo '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($results["{$table}_columns"] as $column) {
                            echo '<tr>';
                            echo '<td>' . h($column['Field']) . '</td>';
                            echo '<td>' . h($column['Type']) . '</td>';
                            echo '<td>' . h($column['Null']) . '</td>';
                            echo '<td>' . h($column['Key']) . '</td>';
                            echo '<td>' . h($column['Default']) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="error">✗ Table does not exist</p>';
                    }
                }
                echo '</div>';
                echo '</div>';
            }
            
            // Test form submission
            if (isset($_POST['test_form_submission'])) {
                echo '<div class="test-section">';
                echo '<h3>Form Submission Test</h3>';
                
                // Create a mock submission to test the form processing
                $mockData = [
                    'packageType' => 'Couples',
                    'customerName' => 'Test Customer',
                    'contactNumber' => '1234567890',
                    'email' => 'test@example.com',
                    'reservationDate' => date('Y-m-d', strtotime('+1 day')),
                    'reservationTime' => '18:00',
                    'guestCount' => 2,
                    'specialRequests' => 'This is a test reservation',
                    'paymentMethod' => 'cash',
                    'paymentOption' => 'full',
                    'totalAmount' => 500,
                    'amountToPay' => 500,
                    'advanceOrder' => [
                        'items' => [
                            [
                                'name' => 'Test Food Item',
                                'price' => 250,
                                'quantity' => 2
                            ]
                        ],
                        'paymentMethod' => 'cash',
                        'paymentOption' => 'full',
                        'totalAmount' => 500,
                        'amountToPay' => 500
                    ]
                ];
                
                echo '<div class="result-box">';
                echo '<h4>Mock Form Data</h4>';
                echo '<pre>' . json_encode($mockData, JSON_PRETTY_PRINT) . '</pre>';
                
                // Log the data for reference
                error_log("Test form submission data: " . json_encode($mockData));
                
                // Test if the order system properly creates an order from this data
                echo '<h4>Creating Test Booking & Order</h4>';
                
                try {
                    // Start transaction
                    $con->begin_transaction();
                    
                    // Insert booking
                    $userId = $_SESSION['user_id'];
                    $packageType = $mockData['packageType'];
                    $name = $mockData['customerName'];
                    $contactNumber = $mockData['contactNumber'];
                    $email = $mockData['email'];
                    $bookingDate = $mockData['reservationDate'];
                    $bookingTime = $mockData['reservationTime'];
                    $guestCount = $mockData['guestCount'];
                    $specialRequests = $mockData['specialRequests'];
                    $totalAmount = $mockData['totalAmount'];
                    $amountToPay = $mockData['amountToPay'];
                    $paymentMethod = $mockData['paymentMethod'];
                    $paymentOption = $mockData['paymentOption'];
                    $paymentStatus = 'Pending';
                    $status = 'Pending';
                    
                    $sql = "INSERT INTO table_bookings (
                        user_id, package_name, name, contact_number, email_address,
                        booking_date, booking_time, num_guests, special_requests,
                        total_amount, amount_to_pay, payment_method, payment_option,
                        payment_status, status, package_type, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param(
                        "issssssissssssss",
                        $userId, $packageType, $name, $contactNumber, $email,
                        $bookingDate, $bookingTime, $guestCount, $specialRequests,
                        $totalAmount, $amountToPay, $paymentMethod, $paymentOption,
                        $paymentStatus, $status, $packageType
                    );
                    
                    if ($stmt->execute()) {
                        $bookingId = $con->insert_id;
                        echo '<p class="success">✓ Booking created successfully (ID: ' . $bookingId . ')</p>';
                        
                        // Create order
                        $orderTotalAmount = 0;
                        foreach ($mockData['advanceOrder']['items'] as $item) {
                            $orderTotalAmount += floatval($item['price']) * intval($item['quantity']);
                        }
                        
                        $currentDate = date('Y-m-d H:i:s');
                        
                        $orderSql = "INSERT INTO orders (
                            customer_name, contact_number, user_id, table_id, order_date, 
                            status, payment_method, total_amount, amount_paid, change_amount, order_type
                        ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, 0, 'advance')";
                        
                        $stmt = $con->prepare($orderSql);
                        $stmt->bind_param(
                            "ssiissdd",
                            $name, $contactNumber, $userId, $bookingId, $currentDate,
                            $paymentMethod, $orderTotalAmount, $orderTotalAmount
                        );
                        
                        if ($stmt->execute()) {
                            $orderId = $con->insert_id;
                            echo '<p class="success">✓ Order created successfully (ID: ' . $orderId . ')</p>';
                            
                            // Add order items
                            $successfulItems = 0;
                            foreach ($mockData['advanceOrder']['items'] as $item) {
                                $itemName = $item['name'];
                                $itemPrice = floatval($item['price']);
                                $itemQuantity = intval($item['quantity']);
                                
                                $itemSql = "INSERT INTO order_items (order_id, item_name, price, quantity) 
                                          VALUES (?, ?, ?, ?)";
                                
                                $stmt = $con->prepare($itemSql);
                                $stmt->bind_param("isdi", $orderId, $itemName, $itemPrice, $itemQuantity);
                                
                                if ($stmt->execute()) {
                                    $successfulItems++;
                                    echo '<p class="success">✓ Added item: ' . h($itemName) . '</p>';
                                } else {
                                    echo '<p class="error">✗ Failed to add item: ' . h($itemName) . ' - ' . $stmt->error . '</p>';
                                }
                            }
                            
                            // Verify the order exists and has the right data
                            $verifySql = "SELECT * FROM orders WHERE id = ?";
                            $stmt = $con->prepare($verifySql);
                            $stmt->bind_param("i", $orderId);
                            $stmt->execute();
                            $verifyResult = $stmt->get_result();
                            
                            if ($verifyResult && $verifyResult->num_rows > 0) {
                                $verifyOrder = $verifyResult->fetch_assoc();
                                echo '<p class="success">✓ Order verification successful</p>';
                                echo '<pre>' . print_r($verifyOrder, true) . '</pre>';
                                
                                // Verify order items
                                $verifyItemsSql = "SELECT * FROM order_items WHERE order_id = ?";
                                $stmt = $con->prepare($verifyItemsSql);
                                $stmt->bind_param("i", $orderId);
                                $stmt->execute();
                                $verifyItemsResult = $stmt->get_result();
                                
                                if ($verifyItemsResult && $verifyItemsResult->num_rows > 0) {
                                    echo '<p class="success">✓ Order items verification successful</p>';
                                    echo '<table class="table table-sm table-bordered">';
                                    echo '<thead><tr><th>ID</th><th>Item Name</th><th>Price</th><th>Quantity</th></tr></thead>';
                                    echo '<tbody>';
                                    while ($item = $verifyItemsResult->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . h($item['id']) . '</td>';
                                        echo '<td>' . h($item['item_name']) . '</td>';
                                        echo '<td>' . h($item['price']) . '</td>';
                                        echo '<td>' . h($item['quantity']) . '</td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<p class="error">✗ No order items found</p>';
                                }
                            } else {
                                echo '<p class="error">✗ Order verification failed</p>';
                            }
                            
                            echo '<p class="success">Test completed successfully! The order system is working correctly.</p>';
                        } else {
                            echo '<p class="error">✗ Failed to create order: ' . $stmt->error . '</p>';
                        }
                    } else {
                        echo '<p class="error">✗ Failed to create booking: ' . $stmt->error . '</p>';
                    }
                    
                    // Commit transaction
                    $con->commit();
                } catch (Exception $e) {
                    // Rollback on error
                    $con->rollback();
                    echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            
            // Fix existing table orders
            if (isset($_POST['fix_existing_orders'])) {
                echo '<div class="test-section">';
                echo '<h3>Fix Existing Table Orders</h3>';
                
                echo '<div class="result-box">';
                
                // Get all table bookings that might have issues
                $sql = "SELECT tb.*, o.id as order_id 
                        FROM table_bookings tb
                        LEFT JOIN orders o ON tb.id = o.table_id
                        WHERE tb.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
                        ORDER BY tb.id DESC";
                
                $result = $con->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    echo '<table class="table table-sm table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Booking ID</th>';
                    echo '<th>Customer</th>';
                    echo '<th>Date/Time</th>';
                    echo '<th>Package</th>';
                    echo '<th>Total Amount</th>';
                    echo '<th>Order ID</th>';
                    echo '<th>Status</th>';
                    echo '<th>Action</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . h($row['id']) . '</td>';
                        echo '<td>' . h($row['name']) . '<br>' . h($row['contact_number']) . '</td>';
                        echo '<td>' . h($row['booking_date']) . '<br>' . h($row['booking_time']) . '</td>';
                        echo '<td>' . h($row['package_type']) . '</td>';
                        echo '<td>₱' . h($row['total_amount']) . '</td>';
                        echo '<td>' . (empty($row['order_id']) ? '<span class="text-danger">Missing</span>' : h($row['order_id'])) . '</td>';
                        echo '<td>' . h($row['status']) . '</td>';
                        echo '<td>';
                        
                        if (empty($row['order_id'])) {
                            // No order exists, provide a button to create one
                            echo '<form method="post" style="display:inline;">';
                            echo '<input type="hidden" name="create_missing_order" value="1">';
                            echo '<input type="hidden" name="booking_id" value="' . h($row['id']) . '">';
                            echo '<button type="submit" class="btn btn-sm btn-primary">Create Order</button>';
                            echo '</form>';
                        } else {
                            // Order exists, provide a link to view it
                            echo '<a href="view_order.php?id=' . h($row['order_id']) . '" class="btn btn-sm btn-info" target="_blank">View Order</a>';
                        }
                        
                        echo '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No recent table bookings found.</p>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            
            // Create missing order for a booking
            if (isset($_POST['create_missing_order'])) {
                echo '<div class="test-section">';
                echo '<h3>Creating Missing Order</h3>';
                
                $bookingId = intval($_POST['booking_id']);
                
                echo '<div class="result-box">';
                
                try {
                    // Start transaction
                    $con->begin_transaction();
                    
                    // Get booking details
                    $sql = "SELECT * FROM table_bookings WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $bookingId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0) {
                        $booking = $result->fetch_assoc();
                        
                        echo '<h4>Booking Details</h4>';
                        echo '<pre>' . print_r($booking, true) . '</pre>';
                        
                        // Create order
                        $customerName = $booking['name'];
                        $contactNumber = $booking['contact_number'];
                        $userId = $booking['user_id'];
                        $totalAmount = $booking['total_amount'];
                        $paymentMethod = $booking['payment_method'];
                        $currentDate = date('Y-m-d H:i:s');
                        
                        $orderSql = "INSERT INTO orders (
                            customer_name, contact_number, user_id, table_id, order_date, 
                            status, payment_method, total_amount, amount_paid, change_amount, order_type
                        ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, 0, 'advance')";
                        
                        $stmt = $con->prepare($orderSql);
                        $stmt->bind_param(
                            "ssiissdd",
                            $customerName, $contactNumber, $userId, $bookingId, $currentDate,
                            $paymentMethod, $totalAmount, $totalAmount
                        );
                        
                        if ($stmt->execute()) {
                            $orderId = $con->insert_id;
                            echo '<p class="success">✓ Order created successfully (ID: ' . $orderId . ')</p>';
                            
                            // Add a placeholder order item since we don't know what was ordered
                            $itemName = "Table Reservation Food";
                            $itemPrice = $totalAmount;
                            $itemQuantity = 1;
                            
                            $itemSql = "INSERT INTO order_items (order_id, item_name, price, quantity) 
                                      VALUES (?, ?, ?, ?)";
                            
                            $stmt = $con->prepare($itemSql);
                            $stmt->bind_param("isdi", $orderId, $itemName, $itemPrice, $itemQuantity);
                            
                            if ($stmt->execute()) {
                                echo '<p class="success">✓ Added placeholder item</p>';
                            } else {
                                echo '<p class="error">✗ Failed to add item: ' . $stmt->error . '</p>';
                            }
                            
                            echo '<p class="success">Order created successfully! You can now view this order in the system.</p>';
                            echo '<a href="view_order.php?id=' . $orderId . '" class="btn btn-info" target="_blank">View Order</a>';
                        } else {
                            echo '<p class="error">✗ Failed to create order: ' . $stmt->error . '</p>';
                        }
                    } else {
                        echo '<p class="error">✗ Booking not found</p>';
                    }
                    
                    // Commit transaction
                    $con->commit();
                } catch (Exception $e) {
                    // Rollback on error
                    $con->rollback();
                    echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
                }
                
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">System Tests</h2>
            </div>
            <div class="card-body">
                <form method="post" class="mb-3">
                    <input type="hidden" name="test_tables" value="1">
                    <button type="submit" class="btn btn-outline-primary">Test Database Tables</button>
                </form>
                
                <form method="post" class="mb-3">
                    <input type="hidden" name="test_form_submission" value="1">
                    <button type="submit" class="btn btn-outline-primary">Test Order Form Submission</button>
                </form>
                
                <form method="post">
                    <input type="hidden" name="fix_existing_orders" value="1">
                    <button type="submit" class="btn btn-outline-warning">Fix Existing Table Orders</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-info text-white">
                <h2 class="h5 mb-0">Troubleshooting Tips</h2>
            </div>
            <div class="card-body">
                <h5>Common Issues and Solutions</h5>
                <ol>
                    <li><strong>Orders not showing up in the database:</strong>
                        <ul>
                            <li>Make sure the order_type is set correctly ('advance' or 'walk-in').</li>
                            <li>Check that the table_id is being properly set to the booking ID.</li>
                            <li>Verify that the required fields match the database structure.</li>
                        </ul>
                    </li>
                    <li><strong>Order items not appearing:</strong>
                        <ul>
                            <li>Ensure the order_items table has the correct columns (price, quantity).</li>
                            <li>Verify that the order_id is correct when inserting items.</li>
                            <li>Check for any SQL errors during insertion.</li>
                        </ul>
                    </li>
                    <li><strong>Order showing in database but not in Cashier page:</strong>
                        <ul>
                            <li>Check the SQL query in Cashier/Order.php to ensure it's not filtering out your orders.</li>
                            <li>Verify the order status is 'pending' (case-sensitive).</li>
                            <li>Ensure the GROUP BY clause is not causing issues with your MySQL version.</li>
                        </ul>
                    </li>
                </ol>
                
                <h5>Manual Fix Commands</h5>
                <div class="bg-light p-3 rounded">
                    <p><strong>To update all table orders to 'walk-in' type:</strong></p>
                    <pre>UPDATE orders SET order_type = 'walk-in' WHERE table_id IS NOT NULL;</pre>
                    
                    <p><strong>To fix missing price/quantity in order items:</strong></p>
                    <pre>UPDATE order_items SET price = 100.00, quantity = 1 WHERE price = 0 OR quantity = 0;</pre>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 