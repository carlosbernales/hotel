<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fix Advance Table Orders Data Types</h1>";

// Include database connection
require_once 'db.php';

// Check if the table exists
$tableExists = $con->query("SHOW TABLES LIKE 'advance_table_orders'")->num_rows > 0;

echo "<p>Table exists: " . ($tableExists ? 'Yes' : 'No') . "</p>";

if (!$tableExists) {
    echo "<p>Creating table...</p>";
    $sql = file_get_contents('advance_table_orders.sql');
    if ($con->multi_query($sql)) {
        echo "<p>Table created successfully!</p>";
        // Clear results to run more queries
        while ($con->more_results() && $con->next_result()) {
            // consume all results
        }
    } else {
        echo "<p>Error creating table: " . $con->error . "</p>";
    }
}

// Check if we need to manually execute a test insert
if (isset($_GET['test_insert'])) {
    echo "<h2>Manual Test Insert</h2>";
    
    // Get an existing booking ID or create one
    $bookingResult = $con->query("SELECT id FROM table_bookings ORDER BY id DESC LIMIT 1");
    if ($bookingResult->num_rows > 0) {
        $bookingId = $bookingResult->fetch_assoc()['id'];
    } else {
        // Create a test booking
        $con->query("INSERT INTO table_bookings (user_id, package_name, name, booking_date, booking_time, num_guests, created_at) 
                    VALUES (1, 'Test Package', 'Test Customer', CURDATE(), '12:00:00', 2, NOW())");
        $bookingId = $con->insert_id;
    }
    
    // Insert test record
    $customerName = "Test Customer " . time();
    $orderItems = json_encode([["id" => 1, "name" => "Test Item", "price" => 100, "quantity" => 2]]);
    $totalAmount = "200.00";
    $paymentOption = "full";
    $paymentMethod = "cash";
    $amountToPay = "200.00";
    
    // Try direct query first (non-prepared)
    $sql = "INSERT INTO advance_table_orders (
        table_booking_id, 
        customer_name, 
        order_items, 
        total_amount, 
        payment_option, 
        payment_method, 
        amount_to_pay
    ) VALUES (
        $bookingId,
        '$customerName',
        '$orderItems',
        $totalAmount,
        '$paymentOption',
        '$paymentMethod',
        $amountToPay
    )";
    
    echo "<p>Executing: " . htmlspecialchars($sql) . "</p>";
    
    if ($con->query($sql)) {
        $insertId = $con->insert_id;
        echo "<p style='color:green'>Direct insert successful! ID: $insertId</p>";
    } else {
        echo "<p style='color:red'>Direct insert failed: " . $con->error . "</p>";
    }
}

// Find problems with existing data
echo "<h2>Checking Process Table Reservation Code</h2>";

// Check the file
$reservationFile = file_get_contents('process_table_reservation.php');
if ($reservationFile === false) {
    echo "<p>Error: Could not read process_table_reservation.php</p>";
} else {
    // Find the section that inserts into advance_table_orders
    if (preg_match('/advance_table_orders.*?VALUES\s*\(\s*\?\s*,\s*\?\s*,\s*\?\s*,\s*\?\s*,\s*\?\s*,\s*\?\s*,\s*\?\s*\)/s', $reservationFile, $matches)) {
        echo "<p>Found insertion section for advance_table_orders</p>";
        
        // Find bind_param section
        if (preg_match('/bind_param\s*\(\s*[\'"](\w+)[\'"].*?advanceOrderSql\)/s', $reservationFile, $bindMatches)) {
            echo "<p>Found bind_param: " . htmlspecialchars($bindMatches[0]) . "</p>";
            echo "<p>Parameter types: " . htmlspecialchars($bindMatches[1]) . "</p>";
            
            // Check if we have the right number of parameters
            $paramTypes = $bindMatches[1];
            $paramCount = strlen($paramTypes);
            echo "<p>Parameter count: $paramCount</p>";
            
            // Check if the parameter types match what we expect
            $expectedTypes = "issssss"; // integer, string, string, string, string, string, string
            if ($paramTypes !== $expectedTypes) {
                echo "<p style='color:red'>Parameter types don't match expected!</p>";
                echo "<p>Expected: $expectedTypes, Found: $paramTypes</p>";
            } else {
                echo "<p style='color:green'>Parameter types match expected format</p>";
            }
        } else {
            echo "<p style='color:red'>Could not find bind_param section</p>";
        }
        
        // Find where we execute the query
        if (preg_match('/\$stmt->execute\(\).*?advance_table_orders/s', $reservationFile, $executeMatches)) {
            echo "<p>Found execute section</p>";
        } else {
            echo "<p style='color:red'>Could not find execute section for advance_table_orders</p>";
        }
    } else {
        echo "<p style='color:red'>Could not find insertion section for advance_table_orders</p>";
    }
    
    // Check if we're setting $GLOBALS correctly
    if (strpos($reservationFile, '$GLOBALS[\'advanceOrderIdCreated\']') !== false) {
        echo "<p style='color:green'>Found GLOBALS variable usage</p>";
    } else {
        echo "<p style='color:red'>GLOBALS variable not found!</p>";
    }
}

// Check database permission issues
echo "<h2>Database User Permissions</h2>";
try {
    $grants = $con->query("SHOW GRANTS FOR CURRENT_USER()");
    if ($grants) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Grants</th></tr>";
        while ($row = $grants->fetch_row()) {
            echo "<tr><td>" . htmlspecialchars($row[0]) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Error checking permissions: " . $con->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p>Exception checking permissions: " . $e->getMessage() . "</p>";
}

// Create a modified version of the insert function that tests for issues
echo "<h2>Test Insert with Detailed Error Handling</h2>";

// Get form data or use defaults
$bookingId = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
$customerName = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
$orderItems = isset($_POST['order_items']) ? $_POST['order_items'] : '';
$totalAmount = isset($_POST['total_amount']) ? $_POST['total_amount'] : '';
$paymentOption = isset($_POST['payment_option']) ? $_POST['payment_option'] : '';
$paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$amountToPay = isset($_POST['amount_to_pay']) ? $_POST['amount_to_pay'] : '';

// If not submitted, populate with sample data
if (!isset($_POST['submit'])) {
    // Get a booking ID from the database
    $bookingResult = $con->query("SELECT id, name, booking_date FROM table_bookings ORDER BY id DESC LIMIT 1");
    if ($bookingResult && $bookingResult->num_rows > 0) {
        $booking = $bookingResult->fetch_assoc();
        $bookingId = $booking['id'];
    } else {
        $bookingId = "1"; // Fallback
    }
    
    $customerName = "Test Customer";
    $orderItems = json_encode([
        ["id" => 1, "name" => "Test Item", "price" => 100, "quantity" => 2]
    ]);
    $totalAmount = "200.00";
    $paymentOption = "full";
    $paymentMethod = "cash";
    $amountToPay = "200.00";
}

if (isset($_POST['submit'])) {
    echo "<h3>Insert Results</h3>";
    
    // Convert form data to the right types
    $bookingId = (int)$bookingId;
    $totalAmount = (string)$totalAmount;
    $amountToPay = (string)$amountToPay;
    
    // Try to insert using prepared statement
    try {
        $insertSql = "INSERT INTO advance_table_orders (
            table_booking_id, 
            customer_name, 
            order_items, 
            total_amount, 
            payment_option, 
            payment_method, 
            amount_to_pay
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($insertSql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }
        
        $types = "issssss";
        echo "<p>Binding parameters with types: $types</p>";
        echo "<pre>";
        var_dump([
            'bookingId' => $bookingId,
            'customerName' => $customerName,
            'orderItems' => $orderItems,
            'totalAmount' => $totalAmount,
            'paymentOption' => $paymentOption,
            'paymentMethod' => $paymentMethod,
            'amountToPay' => $amountToPay
        ]);
        echo "</pre>";
        
        $stmt->bind_param($types, 
            $bookingId,
            $customerName,
            $orderItems,
            $totalAmount,
            $paymentOption,
            $paymentMethod,
            $amountToPay
        );
        
        $result = $stmt->execute();
        if ($result) {
            $insertId = $con->insert_id;
            echo "<p style='color:green'>Insert successful! ID: $insertId</p>";
            
            // Check that the row was inserted
            $checkSql = "SELECT * FROM advance_table_orders WHERE id = ?";
            $checkStmt = $con->prepare($checkSql);
            $checkStmt->bind_param("i", $insertId);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<p style='color:green'>Record found in database!</p>";
                
                echo "<table border='1' cellpadding='5'>";
                foreach ($row as $key => $value) {
                    echo "<tr>";
                    echo "<th>" . htmlspecialchars($key) . "</th>";
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:red'>Record not found after insertion!</p>";
            }
        } else {
            echo "<p style='color:red'>Execute failed: " . $stmt->error . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
    }
}

// Generate a form to test insertion
echo "<h3>Test Insert Form</h3>";
echo "<form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='booking_id'>Booking ID (table_booking_id):</label><br>";
echo "<input type='text' name='booking_id' id='booking_id' value='" . htmlspecialchars($bookingId) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='customer_name'>Customer Name:</label><br>";
echo "<input type='text' name='customer_name' id='customer_name' value='" . htmlspecialchars($customerName) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='order_items'>Order Items (JSON):</label><br>";
echo "<textarea name='order_items' id='order_items' rows='6' cols='50'>" . htmlspecialchars($orderItems) . "</textarea>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='total_amount'>Total Amount:</label><br>";
echo "<input type='text' name='total_amount' id='total_amount' value='" . htmlspecialchars($totalAmount) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='payment_option'>Payment Option:</label><br>";
echo "<input type='text' name='payment_option' id='payment_option' value='" . htmlspecialchars($paymentOption) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='payment_method'>Payment Method:</label><br>";
echo "<input type='text' name='payment_method' id='payment_method' value='" . htmlspecialchars($paymentMethod) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='amount_to_pay'>Amount to Pay:</label><br>";
echo "<input type='text' name='amount_to_pay' id='amount_to_pay' value='" . htmlspecialchars($amountToPay) . "'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<input type='submit' name='submit' value='Test Insert'>";
echo "</div>";
echo "</form>";

// Check existing records if any
echo "<h2>Existing Records in advance_table_orders</h2>";
$countResult = $con->query("SELECT COUNT(*) as total FROM advance_table_orders");
if ($countResult) {
    $count = $countResult->fetch_assoc()['total'];
    echo "<p>Total records: $count</p>";
    
    if ($count > 0) {
        $records = $con->query("SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 10");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        $fields = $records->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        while ($row = $records->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if ($key === 'order_items') {
                    $value = substr($value, 0, 30) . '...';
                }
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p>Error counting records: " . $con->error . "</p>";
}

// Navigation links
echo "<div style='margin-top: 20px;'>";
echo "<a href='debug_advance_table_orders.php' style='margin-right: 10px;'>Run Debug Script</a>";
echo "<a href='test_advance_order.php' style='margin-right: 10px;'>Test Advance Order</a>";
echo "<a href='table_packages.php'>Back to Table Packages</a>";
echo "</div>";
?> 