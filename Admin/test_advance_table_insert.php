<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h1>Advanced Table Orders Diagnostic Tool</h1>";
echo "<p>This tool will diagnose issues with the advance_table_orders table on your hosted server.</p>";

// Step 1: Check if the table exists
echo "<h2>Step 1: Check Table Existence</h2>";
$tableCheck = $con->query("SHOW TABLES LIKE 'advance_table_orders'");
if ($tableCheck->num_rows == 0) {
    echo "<div style='color:red; padding:10px; border:1px solid red;'>";
    echo "<strong>ERROR:</strong> The 'advance_table_orders' table does not exist in the database!";
    echo "</div>";
    
    // Show table creation SQL
    echo "<h3>Table Creation SQL:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('advance_table_orders.sql')) . "</pre>";
    
    echo "<form method='post'>";
    echo "<input type='submit' name='create_table' value='Create Table Now' style='padding:10px; background:#4CAF50; color:white; border:none;'>";
    echo "</form>";
    
    if (isset($_POST['create_table'])) {
        $createSql = file_get_contents('advance_table_orders.sql');
        if ($con->multi_query($createSql)) {
            echo "<div style='color:green; padding:10px; border:1px solid green; margin-top:10px;'>";
            echo "Table created successfully! Please refresh the page.";
            echo "</div>";
            
            // Clear results
            while ($con->more_results() && $con->next_result()) {
                // consume results
            }
        } else {
            echo "<div style='color:red; padding:10px; border:1px solid red; margin-top:10px;'>";
            echo "Error creating table: " . $con->error;
            echo "</div>";
        }
    }
    
} else {
    echo "<div style='color:green; padding:10px; border:1px solid green;'>";
    echo "The 'advance_table_orders' table exists in the database.";
    echo "</div>";
    
    // Step 2: Show table structure
    echo "<h2>Step 2: Table Structure</h2>";
    $structure = $con->query("DESCRIBE advance_table_orders");
    if ($structure) {
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($column = $structure->fetch_assoc()) {
            echo "<tr>";
            foreach ($column as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Step 3: Show existing records
    echo "<h2>Step 3: Existing Records</h2>";
    $records = $con->query("SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 10");
    if ($records) {
        if ($records->num_rows > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
            echo "<tr>";
            $fields = $records->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            // Reset pointer
            $records->data_seek(0);
            
            while ($row = $records->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if ($key === 'order_items') {
                        echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "...</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div style='color:orange; padding:10px; border:1px solid orange;'>";
            echo "The table exists but contains no records.";
            echo "</div>";
        }
    }
    
    // Step 4: Test insertion
    echo "<h2>Step 4: Test Direct Insertion</h2>";
    
    if (isset($_POST['test_insert'])) {
        // Get the booking ID
        $bookingId = $_POST['booking_id'];
        
        // Sample data
        $customerName = "Test Customer " . date('Y-m-d H:i:s');
        $orderItems = json_encode([
            ['id' => 1, 'name' => 'Test Item 1', 'price' => 50.00, 'quantity' => 2],
            ['id' => 2, 'name' => 'Test Item 2', 'price' => 25.00, 'quantity' => 1]
        ]);
        $totalAmount = "125.00";
        $paymentOption = "full";
        $paymentMethod = "cash";
        $amountToPay = "125.00";
        
        echo "<h3>Using prepared statement:</h3>";
        
        try {
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
            
            // Debug parameter types
            echo "<p>Parameter types and values:</p>";
            echo "<pre>";
            echo "table_booking_id: " . gettype($bookingId) . " - " . $bookingId . "\n";
            echo "customer_name: " . gettype($customerName) . " - " . $customerName . "\n";
            echo "order_items: " . gettype($orderItems) . " - length: " . strlen($orderItems) . "\n";
            echo "total_amount: " . gettype($totalAmount) . " - " . $totalAmount . "\n";
            echo "payment_option: " . gettype($paymentOption) . " - " . $paymentOption . "\n";
            echo "payment_method: " . gettype($paymentMethod) . " - " . $paymentMethod . "\n";
            echo "amount_to_pay: " . gettype($amountToPay) . " - " . $amountToPay . "\n";
            echo "</pre>";
            
            $stmt->bind_param("issssss", 
                $bookingId,
                $customerName,
                $orderItems,
                $totalAmount,
                $paymentOption,
                $paymentMethod,
                $amountToPay
            );
            
            if ($stmt->execute()) {
                $insertId = $con->insert_id;
                echo "<div style='color:green; padding:10px; border:1px solid green; margin-top:10px;'>";
                echo "Insert successful! ID: $insertId";
                echo "</div>";
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        } catch (Exception $e) {
            echo "<div style='color:red; padding:10px; border:1px solid red; margin-top:10px;'>";
            echo "Error: " . $e->getMessage();
            echo "</div>";
            
            echo "<h3>Trying direct SQL method:</h3>";
            
            // Escape all values
            $escapedBookingId = $con->real_escape_string($bookingId);
            $escapedCustomerName = $con->real_escape_string($customerName);
            $escapedOrderItems = $con->real_escape_string($orderItems);
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
                $insertId = $con->insert_id;
                echo "<div style='color:green; padding:10px; border:1px solid green; margin-top:10px;'>";
                echo "Direct SQL insert successful! ID: $insertId";
                echo "</div>";
            } else {
                echo "<div style='color:red; padding:10px; border:1px solid red; margin-top:10px;'>";
                echo "Direct SQL insert failed: " . $con->error;
                echo "</div>";
            }
        }
    }
    
    // Get existing bookings for test
    $bookingsQuery = "SELECT id, name, booking_date, booking_time FROM table_bookings ORDER BY id DESC LIMIT 10";
    $bookingsResult = $con->query($bookingsQuery);
    
    if ($bookingsResult && $bookingsResult->num_rows > 0) {
        echo "<form method='post' style='margin-top:20px; padding:15px; background:#f5f5f5;'>";
        echo "<p>Select an existing booking to use for testing:</p>";
        echo "<select name='booking_id' style='padding:5px; width:100%; margin-bottom:10px;'>";
        
        while ($booking = $bookingsResult->fetch_assoc()) {
            echo "<option value='" . $booking['id'] . "'>";
            echo "ID: " . $booking['id'] . " - " . $booking['name'] . " - " . $booking['booking_date'] . " " . $booking['booking_time'];
            echo "</option>";
        }
        
        echo "</select>";
        echo "<input type='submit' name='test_insert' value='Test Insert' style='padding:10px; background:#007BFF; color:white; border:none;'>";
        echo "</form>";
    } else {
        echo "<div style='color:orange; padding:10px; border:1px solid orange;'>";
        echo "No existing bookings found to test with.";
        echo "</div>";
    }
    
    // Step 5: Check permissions
    echo "<h2>Step 5: Database Permissions</h2>";
    try {
        $grants = $con->query("SHOW GRANTS FOR CURRENT_USER()");
        if ($grants) {
            echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
            echo "<tr><th>Grants</th></tr>";
            
            while ($row = $grants->fetch_row()) {
                echo "<tr><td>" . htmlspecialchars($row[0]) . "</td></tr>";
            }
            
            echo "</table>";
        } else {
            echo "<div style='color:orange; padding:10px; border:1px solid orange;'>";
            echo "Unable to check permissions: " . $con->error;
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color:orange; padding:10px; border:1px solid orange;'>";
        echo "Error checking permissions: " . $e->getMessage();
        echo "</div>";
    }
    
    // Step 6: Check process_table_reservation.php
    echo "<h2>Step 6: Check Reservation Process</h2>";
    $processFileExists = file_exists('process_table_reservation.php');
    
    if ($processFileExists) {
        $processFile = file_get_contents('process_table_reservation.php');
        
        echo "<div style='padding:10px; border:1px solid #ddd;'>";
        if (strpos($processFile, '$GLOBALS[\'advanceOrderIdCreated\']') !== false) {
            echo "<p style='color:green'>✓ Using GLOBALS variable to preserve advance order ID</p>";
        } else {
            echo "<p style='color:red'>✗ Not using GLOBALS variable to preserve advance order ID</p>";
        }
        
        if (strpos($processFile, 'advance_table_orders') !== false) {
            echo "<p style='color:green'>✓ Contains reference to advance_table_orders table</p>";
        } else {
            echo "<p style='color:red'>✗ No reference to advance_table_orders table</p>";
        }
        
        if (strpos($processFile, 'direct SQL insert') !== false || strpos($processFile, 'fallback SQL insert') !== false) {
            echo "<p style='color:green'>✓ Has fallback direct SQL insertion methods</p>";
        } else {
            echo "<p style='color:red'>✗ Missing fallback direct SQL insertion methods</p>";
        }
        
        echo "</div>";
        
        echo "<p>You can test the full reservation process by using the <a href='table_packages.php'>Table Packages</a> page.</p>";
    } else {
        echo "<div style='color:red; padding:10px; border:1px solid red;'>";
        echo "process_table_reservation.php file not found!";
        echo "</div>";
    }
}

// Link back to other diagnostic tools
echo "<div style='margin-top:20px; padding:10px; background:#f5f5f5;'>";
echo "<h3>Other Diagnostic Tools:</h3>";
echo "<a href='debug_advance_table_orders.php' style='margin-right:10px;'>Debug Tool</a>";
echo "<a href='fix_data_types.php' style='margin-right:10px;'>Fix Data Types</a>";
echo "<a href='table_packages.php'>Table Packages</a>";
echo "</div>";
?> 