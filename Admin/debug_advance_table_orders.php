<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up a custom error handler to catch all errors
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color:red; border:1px solid red; padding:10px; margin:10px 0;'>";
    echo "<strong>Error [$errno]:</strong> $errstr<br>";
    echo "File: $errfile, Line: $errline";
    echo "</div>";
    
    // Also log to a file
    error_log("Error [$errno]: $errstr in $errfile:$errline");
    
    // Don't execute PHP's internal error handler
    return true;
}
set_error_handler('customErrorHandler');

// Set exception handler
function customExceptionHandler($exception) {
    echo "<div style='color:red; border:1px solid red; padding:10px; margin:10px 0;'>";
    echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
    echo "File: " . $exception->getFile() . ", Line: " . $exception->getLine() . "<br>";
    echo "Stack Trace:<br>" . nl2br($exception->getTraceAsString());
    echo "</div>";
    
    // Also log to a file
    error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
}
set_exception_handler('customExceptionHandler');

echo "<h1>Debug Advance Table Orders</h1>";

// Step 1: Check database connection
echo "<h2>Step 1: Database Connection</h2>";
try {
    // Include database connection
    require_once 'db.php';
    
    if (!$con) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }
    
    echo "<div style='color:green'>Database connection successful!</div>";
    echo "<p>Connection info: " . $con->host_info . "</p>";
    echo "<p>Server info: " . $con->server_info . "</p>";
} catch (Exception $e) {
    echo "<div style='color:red'>Database connection failed: " . $e->getMessage() . "</div>";
    exit;
}

// Step 2: Check if the table exists
echo "<h2>Step 2: Table Existence Check</h2>";
try {
    $tableCheck = $con->query("SHOW TABLES LIKE 'advance_table_orders'");
    if (!$tableCheck) {
        throw new Exception("Error checking table: " . $con->error);
    }
    
    if ($tableCheck->num_rows == 0) {
        echo "<div style='color:red'>Table 'advance_table_orders' does not exist!</div>";
        echo "<p>Attempting to create the table...</p>";
        
        $createTableSql = file_get_contents('advance_table_orders.sql');
        if (!$createTableSql) {
            throw new Exception("Could not read SQL file");
        }
        
        if ($con->multi_query($createTableSql)) {
            echo "<div style='color:green'>Successfully created the table!</div>";
            
            // Clear results to run more queries
            while ($con->more_results() && $con->next_result()) {
                // consume all results
            }
        } else {
            throw new Exception("Failed to create table: " . $con->error);
        }
    } else {
        echo "<div style='color:green'>Table 'advance_table_orders' exists!</div>";
    }
    
    // Check table structure
    $columns = $con->query("DESCRIBE advance_table_orders");
    if (!$columns) {
        throw new Exception("Error describing table: " . $con->error);
    }
    
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($column = $columns->fetch_assoc()) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<div style='color:red'>Error checking table: " . $e->getMessage() . "</div>";
}

// Step 3: Test inserting a record
echo "<h2>Step 3: Test Direct Insert</h2>";
try {
    // Create a timestamp to make this insertion unique
    $timestamp = date('Y-m-d H:i:s');
    
    // Find an existing table_booking_id to use
    $bookingQuery = "SELECT id FROM table_bookings ORDER BY id DESC LIMIT 1";
    $bookingResult = $con->query($bookingQuery);
    
    if (!$bookingResult) {
        throw new Exception("Error finding table booking: " . $con->error);
    }
    
    if ($bookingResult->num_rows == 0) {
        // Create a test booking if none exists
        $createBookingSql = "INSERT INTO table_bookings (
            user_id, package_name, name, contact_number, email_address,
            booking_date, booking_time, num_guests, total_amount, 
            payment_status, status, package_type, created_at
        ) VALUES (
            1, 'Debug Test Package', 'Debug Customer', '12345678', 'debug@test.com',
            CURDATE(), '14:00:00', 2, 100.00,
            'Pending', 'Pending', 'Debug Test', NOW()
        )";
        
        if (!$con->query($createBookingSql)) {
            throw new Exception("Error creating test booking: " . $con->error);
        }
        
        $bookingId = $con->insert_id;
        echo "<div style='color:green'>Created a test booking with ID: $bookingId</div>";
    } else {
        $booking = $bookingResult->fetch_assoc();
        $bookingId = $booking['id'];
        echo "<div>Using existing booking ID: $bookingId</div>";
    }
    
    // Test data
    $testOrderItems = json_encode([
        [
            'id' => 1,
            'name' => 'Debug Food Item',
            'price' => 75.50,
            'quantity' => 2
        ],
        [
            'id' => 2,
            'name' => 'Debug Drink Item',
            'price' => 24.50,
            'quantity' => 2
        ]
    ]);
    
    $customerName = "Debug Customer $timestamp";
    $totalAmount = 200.00;
    $paymentOption = 'full';
    $paymentMethod = 'cash';
    $amountToPay = 200.00;
    
    // Show exactly what we're inserting
    echo "<h3>Insertion Details:</h3>";
    echo "<pre>";
    echo "table_booking_id: $bookingId\n";
    echo "customer_name: $customerName\n";
    echo "order_items: " . $testOrderItems . "\n";
    echo "total_amount: $totalAmount\n";
    echo "payment_option: $paymentOption\n";
    echo "payment_method: $paymentMethod\n";
    echo "amount_to_pay: $amountToPay\n";
    echo "</pre>";
    
    // Try a direct insertion using mysqli prepared statement
    echo "<h3>Method 1: mysqli prepare/bind/execute:</h3>";
    $sql = "INSERT INTO advance_table_orders (
        table_booking_id, 
        customer_name, 
        order_items, 
        total_amount, 
        payment_option, 
        payment_method, 
        amount_to_pay
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    // Check that variable types match parameters
    var_dump([
        'bookingId' => $bookingId,
        'bookingId_type' => gettype($bookingId),
        'customerName' => $customerName,
        'customerName_type' => gettype($customerName),
        'testOrderItems' => $testOrderItems,
        'testOrderItems_type' => gettype($testOrderItems),
        'totalAmount' => $totalAmount,
        'totalAmount_type' => gettype($totalAmount),
        'paymentOption' => $paymentOption,
        'paymentOption_type' => gettype($paymentOption),
        'paymentMethod' => $paymentMethod,
        'paymentMethod_type' => gettype($paymentMethod),
        'amountToPay' => $amountToPay,
        'amountToPay_type' => gettype($amountToPay)
    ]);
    
    $stmt->bind_param(
        "issssss",
        $bookingId,
        $customerName,
        $testOrderItems,
        $totalAmount,
        $paymentOption,
        $paymentMethod,
        $amountToPay
    );
    
    $result = $stmt->execute();
    if (!$result) {
        echo "<div style='color:red'>Execute failed: " . $stmt->error . "</div>";
        
        // Try alternate method if this one fails
        echo "<h3>Method 2: Direct query:</h3>";
        
        // Escape everything properly
        $escapedBookingId = $con->real_escape_string($bookingId);
        $escapedCustomerName = $con->real_escape_string($customerName);
        $escapedOrderItems = $con->real_escape_string($testOrderItems);
        $escapedTotalAmount = $con->real_escape_string($totalAmount);
        $escapedPaymentOption = $con->real_escape_string($paymentOption);
        $escapedPaymentMethod = $con->real_escape_string($paymentMethod);
        $escapedAmountToPay = $con->real_escape_string($amountToPay);
        
        $directSql = "INSERT INTO advance_table_orders (
            table_booking_id, 
            customer_name, 
            order_items, 
            total_amount, 
            payment_option, 
            payment_method, 
            amount_to_pay
        ) VALUES (
            '$escapedBookingId', 
            '$escapedCustomerName', 
            '$escapedOrderItems', 
            '$escapedTotalAmount', 
            '$escapedPaymentOption', 
            '$escapedPaymentMethod', 
            '$escapedAmountToPay'
        )";
        
        echo "<p>Executing SQL: <code>" . htmlspecialchars($directSql) . "</code></p>";
        
        if ($con->query($directSql)) {
            $directInsertId = $con->insert_id;
            echo "<div style='color:green'>Direct insert successful! ID: $directInsertId</div>";
        } else {
            echo "<div style='color:red'>Direct insert failed: " . $con->error . "</div>";
        }
    } else {
        $insertId = $con->insert_id;
        echo "<div style='color:green'>Insert successful! ID: $insertId</div>";
        
        // Verify record exists
        $verifySql = "SELECT * FROM advance_table_orders WHERE id = ?";
        $verifyStmt = $con->prepare($verifySql);
        $verifyStmt->bind_param("i", $insertId);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyResult && $verifyResult->num_rows > 0) {
            echo "<div style='color:green'>Record verified in database!</div>";
            
            $record = $verifyResult->fetch_assoc();
            echo "<h3>Inserted Record:</h3>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            foreach ($record as $field => $value) {
                echo "<tr>";
                echo "<th>" . htmlspecialchars($field) . "</th>";
                if ($field == 'order_items') {
                    // Pretty print JSON
                    $prettyJson = json_encode(json_decode($value), JSON_PRETTY_PRINT);
                    echo "<td><pre>" . htmlspecialchars($prettyJson) . "</pre></td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div style='color:red'>Record verification failed!</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color:red'>Error during insert test: " . $e->getMessage() . "</div>";
}

// Step 4: Check existing records
echo "<h2>Step 4: Current Table Contents</h2>";
try {
    $countSql = "SELECT COUNT(*) as total FROM advance_table_orders";
    $countResult = $con->query($countSql);
    
    if (!$countResult) {
        throw new Exception("Error counting records: " . $con->error);
    }
    
    $count = $countResult->fetch_assoc()['total'];
    echo "<p>Total records in table: $count</p>";
    
    if ($count > 0) {
        $recordsSql = "SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 10";
        $recordsResult = $con->query($recordsSql);
        
        if (!$recordsResult) {
            throw new Exception("Error fetching records: " . $con->error);
        }
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr>";
        
        $fields = $recordsResult->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        while ($row = $recordsResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $field => $value) {
                if ($field == 'order_items') {
                    // Show abbreviated JSON
                    echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "...</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found in the table.</p>";
    }
} catch (Exception $e) {
    echo "<div style='color:red'>Error checking records: " . $e->getMessage() . "</div>";
}

// Step 5: Check permissions
echo "<h2>Step 5: Database Permissions</h2>";
try {
    $grants = $con->query("SHOW GRANTS FOR CURRENT_USER()");
    if (!$grants) {
        throw new Exception("Unable to check permissions: " . $con->error);
    }
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Grants</th></tr>";
    
    while ($row = $grants->fetch_row()) {
        echo "<tr><td>" . htmlspecialchars($row[0]) . "</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<div style='color:red'>Error checking permissions: " . $e->getMessage() . "</div>";
}

// Links
echo "<div style='margin-top: 20px;'>";
echo "<a href='table_packages.php' style='display:inline-block; padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:4px; margin-right:10px;'>Back to Table Packages</a>";
echo "<a href='test_advance_order.php' style='display:inline-block; padding:10px 15px; background:#28a745; color:white; text-decoration:none; border-radius:4px;'>Try Test Advance Order</a>";
echo "</div>";
?> 