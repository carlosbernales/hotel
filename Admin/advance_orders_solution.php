<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Page title and header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Advance Orders Fix Solution</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .section { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; }
        .warning { background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; }
        .info { background-color: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; }
        h1, h2, h3 { color: #333; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; cursor: pointer; border: none; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; }
        .step { background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin-bottom: 15px; }
        .step-number { background: #007bff; color: white; padding: 4px 10px; border-radius: 50%; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Advance Table Orders Fix Solution</h1>
    <div class='info'>
        <p>This page will help you fix the issue with advance table orders not being inserted properly. We will:</p>
        <ol>
            <li>Verify the database table exists</li>
            <li>Test inserting directly into the table</li>
            <li>Review and modify the process_table_reservation.php file</li>
            <li>Test the complete reservation process</li>
        </ol>
    </div>";

// Status tracking
$status = [
    'table_exists' => false,
    'insert_test' => false,
    'process_file_fixed' => false
];

// Step 1: Verify the table exists
echo "<div class='section'>
    <h2><span class='step-number'>1</span> Verify Database Table</h2>";

$tableCheck = $con->query("SHOW TABLES LIKE 'advance_table_orders'");
if ($tableCheck->num_rows > 0) {
    $status['table_exists'] = true;
    echo "<div class='success'>The advance_table_orders table exists in the database.</div>";
    
    // Check table structure
    echo "<h3>Table Structure:</h3>";
    $structure = $con->query("DESCRIBE advance_table_orders");
    if ($structure) {
        echo "<table>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>";
        
        while ($column = $structure->fetch_assoc()) {
            echo "<tr>";
            foreach ($column as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>Error getting table structure: " . $con->error . "</div>";
    }
    
    // Check if there are any records
    $countQuery = $con->query("SELECT COUNT(*) as count FROM advance_table_orders");
    if ($countQuery) {
        $count = $countQuery->fetch_assoc()['count'];
        echo "<p>Current record count: " . $count . "</p>";
        
        if ($count > 0) {
            echo "<div class='success'>The table already has records in it!</div>";
            
            // Show the last 5 records
            $records = $con->query("SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 5");
            if ($records && $records->num_rows > 0) {
                echo "<h3>Last 5 Records:</h3>";
                echo "<table><tr>";
                $fields = $records->fetch_fields();
                foreach ($fields as $field) {
                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                echo "</tr>";
                
                // Reset the result pointer
                $records->data_seek(0);
                
                while ($row = $records->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        if ($key === 'order_items') {
                            echo "<td>" . (strlen($value) > 50 ? substr(htmlspecialchars($value), 0, 50) . "..." : htmlspecialchars($value)) . "</td>";
                        } else {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<div class='warning'>The table exists but has no records yet.</div>";
        }
    } else {
        echo "<div class='error'>Error counting records: " . $con->error . "</div>";
    }
} else {
    echo "<div class='error'>The advance_table_orders table does not exist in the database.</div>";
    
    // Create the table
    if (isset($_POST['create_table'])) {
        try {
            $createSql = file_get_contents('advance_table_orders.sql');
            if (!$createSql) {
                $createSql = "CREATE TABLE IF NOT EXISTS `advance_table_orders` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `table_booking_id` int(11) NOT NULL,
                    `customer_name` varchar(255) NOT NULL,
                    `order_items` text NOT NULL,
                    `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
                    `payment_option` varchar(50) NOT NULL DEFAULT 'full',
                    `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
                    `amount_to_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
                    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`),
                    KEY `table_booking_id` (`table_booking_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            }
            
            $result = $con->multi_query($createSql);
            if ($result) {
                // Clear results to run more queries
                while ($con->more_results() && $con->next_result()) {
                    // consume all results
                }
                echo "<div class='success'>Table created successfully! Please refresh the page.</div>";
            } else {
                throw new Exception($con->error);
            }
        } catch (Exception $e) {
            echo "<div class='error'>Error creating table: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<form method='post'>
            <button type='submit' name='create_table' class='btn btn-success'>Create Table</button>
        </form>";
    }
}
echo "</div>";

// Step 2: Test inserting into the table
echo "<div class='section'>
    <h2><span class='step-number'>2</span> Test Direct Database Insertion</h2>";

if (!$status['table_exists']) {
    echo "<div class='warning'>You need to create the table first before testing insertion.</div>";
} else {
    // Handle test insertion
    if (isset($_POST['test_insert'])) {
        try {
            // Get an existing booking ID or create a test one
            $bookingResult = $con->query("SELECT id FROM table_bookings ORDER BY id DESC LIMIT 1");
            
            if ($bookingResult && $bookingResult->num_rows > 0) {
                $bookingId = $bookingResult->fetch_assoc()['id'];
            } else {
                // Create a test booking
                $createBookingSql = "INSERT INTO table_bookings (
                    user_id, package_name, name, contact_number, email_address,
                    booking_date, booking_time, num_guests, total_amount, 
                    payment_status, status, package_type, created_at
                ) VALUES (
                    1, 'Test Package', 'Test Customer', '12345678', 'test@example.com',
                    CURDATE(), '12:00:00', 2, 100.00,
                    'Pending', 'Pending', 'Test', NOW()
                )";
                
                if (!$con->query($createBookingSql)) {
                    throw new Exception("Error creating test booking: " . $con->error);
                }
                
                $bookingId = $con->insert_id;
            }
            
            // Create test data
            $timestamp = date('Y-m-d H:i:s');
            $customerName = "Test Customer " . $timestamp;
            $orderItems = json_encode([
                ["id" => 1, "name" => "Test Food Item", "price" => 75.50, "quantity" => 2],
                ["id" => 2, "name" => "Test Drink Item", "price" => 24.50, "quantity" => 2]
            ]);
            $totalAmount = "200.00";
            $paymentOption = "full";
            $paymentMethod = "cash";
            $amountToPay = "200.00";
            
            // Try direct insert without prepared statement
            $insertSql = "INSERT INTO advance_table_orders (
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
                '" . $con->real_escape_string($orderItems) . "',
                $totalAmount,
                '$paymentOption',
                '$paymentMethod',
                $amountToPay
            )";
            
            if ($con->query($insertSql)) {
                $insertId = $con->insert_id;
                $status['insert_test'] = true;
                echo "<div class='success'>Test insert successful! Inserted record ID: $insertId</div>";
                
                // Verify the record exists
                $verifyResult = $con->query("SELECT * FROM advance_table_orders WHERE id = $insertId");
                if ($verifyResult && $verifyResult->num_rows > 0) {
                    $record = $verifyResult->fetch_assoc();
                    echo "<h3>Inserted Record:</h3>";
                    echo "<table>";
                    foreach ($record as $field => $value) {
                        echo "<tr>
                            <th>" . htmlspecialchars($field) . "</th>
                            <td>" . ($field === 'order_items' ? 
                                htmlspecialchars(substr($value, 0, 100)) . "..." : 
                                htmlspecialchars($value)) . "</td>
                        </tr>";
                    }
                    echo "</table>";
                }
            } else {
                throw new Exception("Direct insert failed: " . $con->error);
            }
        } catch (Exception $e) {
            echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='info'>This will test if we can insert records directly into the advance_table_orders table.</div>";
        echo "<form method='post'>
            <button type='submit' name='test_insert' class='btn'>Run Insert Test</button>
        </form>";
    }
}
echo "</div>";

// Step 3: Check and fix process_table_reservation.php
echo "<div class='section'>
    <h2><span class='step-number'>3</span> Review and Fix process_table_reservation.php</h2>";

// Check if the file exists
if (file_exists('process_table_reservation.php')) {
    $processFile = file_get_contents('process_table_reservation.php');
    $fileModified = false;
    
    if ($processFile === false) {
        echo "<div class='error'>Could not read the file process_table_reservation.php</div>";
    } else {
        // Check for GLOBALS usage
        if (strpos($processFile, '$GLOBALS[\'advanceOrderIdCreated\']') !== false) {
            echo "<div class='success'>The file is using the GLOBALS array to store the advance order ID!</div>";
            $fileModified = true;
        } else {
            echo "<div class='warning'>The file is not using the GLOBALS array to store the advance order ID.</div>";
        }
        
        // Check for direct SQL fallback
        if (strpos($processFile, 'direct SQL insert') !== false || 
            strpos($processFile, 'fallback SQL insert') !== false) {
            echo "<div class='success'>The file includes direct SQL fallback methods!</div>";
            $fileModified = true;
        } else {
            echo "<div class='warning'>The file does not have direct SQL fallback methods.</div>";
        }
        
        // Check for table creation code
        if (strpos($processFile, 'CREATE TABLE IF NOT EXISTS `advance_table_orders`') !== false) {
            echo "<div class='success'>The file has code to create the table if it doesn't exist!</div>";
            $fileModified = true;
        } else {
            echo "<div class='warning'>The file does not have code to create the advance_table_orders table if it doesn't exist.</div>";
        }
        
        // Set overall file status
        $status['process_file_fixed'] = $fileModified;
        
        // Show guidance based on file status
        if ($fileModified) {
            echo "<div class='success'>The process_table_reservation.php file appears to have been modified with the necessary fixes!</div>";
        } else {
            echo "<div class='warning'>The process_table_reservation.php file needs to be updated with our fixes.</div>";
            
            echo "<h3>Required Changes:</h3>
                <ol>
                    <li>Use \$GLOBALS['advanceOrderIdCreated'] instead of a local variable to store the advance order ID</li>
                    <li>Add fallback to direct SQL if prepared statements fail</li>
                    <li>Add code to create the advance_table_orders table if it doesn't exist</li>
                </ol>";
            
            echo "<div class='info'>Use our debug_advance_table_orders.php and fix_data_types.php tools to diagnose and fix these issues.</div>";
            
            if (file_exists('debug_advance_table_orders.php') && file_exists('fix_data_types.php')) {
                echo "<p>
                    <a href='debug_advance_table_orders.php' class='btn' target='_blank'>Run Debug Script</a>
                    <a href='fix_data_types.php' class='btn' target='_blank'>Run Fix Data Types</a>
                </p>";
            }
        }
    }
} else {
    echo "<div class='error'>Could not find process_table_reservation.php</div>";
}
echo "</div>";

// Step 4: Test Full Reservation Process
echo "<div class='section'>
    <h2><span class='step-number'>4</span> Test Full Reservation Process</h2>";

if (!$status['table_exists']) {
    echo "<div class='warning'>You need to create the advance_table_orders table first.</div>";
} elseif (!$status['insert_test']) {
    echo "<div class='warning'>You should verify that direct insertion works before testing the full process.</div>";
} else {
    echo "<div class='info'>
        <p>Now you should test the entire reservation process from the table_packages.php page:</p>
        <ol>
            <li>Go to table_packages.php</li>
            <li>Select a package and make a reservation</li>
            <li>Add some food items to the reservation</li>
            <li>Complete the booking process</li>
            <li>Verify the success modal shows the advance order details</li>
            <li>Check the database to confirm the record was inserted</li>
        </ol>
    </div>";
    
    echo "<p><a href='table_packages.php' class='btn btn-success'>Go to Table Packages</a></p>";
    
    // Create a quick database check link
    echo "<h3>Quick Database Check:</h3>";
    $latestRecords = $con->query("SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 3");
    if ($latestRecords && $latestRecords->num_rows > 0) {
        echo "<table><tr>";
        $fields = $latestRecords->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Reset pointer
        $latestRecords->data_seek(0);
        
        while ($row = $latestRecords->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if ($key === 'order_items') {
                    echo "<td>" . (strlen($value) > 50 ? substr(htmlspecialchars($value), 0, 50) . "..." : htmlspecialchars($value)) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found in advance_table_orders table yet.</p>";
    }
}
echo "</div>";

// Summary and next steps
echo "<div class='section'>
    <h2>Summary</h2>
    <ul>";
    
    if ($status['table_exists']) {
        echo "<li class='success'>The advance_table_orders table exists</li>";
    } else {
        echo "<li class='error'>The advance_table_orders table needs to be created</li>";
    }
    
    if ($status['insert_test']) {
        echo "<li class='success'>Direct insertion into the table works correctly</li>";
    } else {
        echo "<li class='warning'>Direct insertion test needed</li>";
    }
    
    if ($status['process_file_fixed']) {
        echo "<li class='success'>The process_table_reservation.php file has been updated with fixes</li>";
    } else {
        echo "<li class='warning'>The process_table_reservation.php file needs to be updated</li>";
    }
    
    echo "</ul>";
    
    // Overall status
    if ($status['table_exists'] && $status['insert_test'] && $status['process_file_fixed']) {
        echo "<div class='success'>
            <h3>All fixes have been implemented!</h3>
            <p>Your advance order system should now be working correctly. Test it by making a table reservation with advance food orders.</p>
        </div>";
    } else {
        echo "<div class='warning'>
            <h3>Some fixes still need to be applied</h3>
            <p>Follow the steps above to complete the necessary fixes.</p>
        </div>";
    }
    
    echo "<div class='info'>
        <h3>Additional Tools:</h3>
        <p>
            <a href='debug_advance_table_orders.php' class='btn' target='_blank'>Debug Script</a>
            <a href='fix_data_types.php' class='btn' target='_blank'>Fix Data Types</a>
            <a href='test_advance_order.php' class='btn' target='_blank'>Test Advance Order</a>
            <a href='table_packages.php' class='btn btn-success'>Table Packages</a>
        </p>
    </div>
</div>
</body>
</html>";
?> 