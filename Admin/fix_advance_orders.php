<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h1>Fix Advance Orders</h1>";

// Function to check if a table exists
function tableExists($con, $tableName) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Function to check if a column exists in a table
function columnExists($con, $tableName, $columnName) {
    $result = mysqli_query($con, "SHOW COLUMNS FROM $tableName LIKE '$columnName'");
    return mysqli_num_rows($result) > 0;
}

// Check if orders table exists
if (!tableExists($con, 'orders')) {
    die("<p style='color:red;'>Error: The 'orders' table does not exist in the database.</p>");
}

// Check for necessary columns in orders table and add them if missing
$requiredColumns = [
    'order_type' => 'VARCHAR(50) DEFAULT NULL',
    'table_id' => 'INT DEFAULT NULL',
    'amount_paid' => 'DECIMAL(10,2) DEFAULT 0.00',
    'change_amount' => 'DECIMAL(10,2) DEFAULT 0.00'
];

$addedColumns = [];
foreach ($requiredColumns as $column => $definition) {
    if (!columnExists($con, 'orders', $column)) {
        $query = "ALTER TABLE orders ADD COLUMN $column $definition";
        if (mysqli_query($con, $query)) {
            $addedColumns[] = $column;
        } else {
            echo "<p style='color:red;'>Error adding column '$column': " . mysqli_error($con) . "</p>";
        }
    }
}

if (!empty($addedColumns)) {
    echo "<p style='color:green;'>Added the following columns to the orders table: " . implode(', ', $addedColumns) . "</p>";
}

// Get table bookings without corresponding orders
$query = "SELECT tb.id, tb.name, tb.email, tb.phone, tb.booking_date, tb.booking_time, tb.guests, 
            tb.table_package_id, tb.status, tb.payment_status, tb.amount, tp.name as package_name
          FROM table_bookings tb
          LEFT JOIN table_packages tp ON tb.table_package_id = tp.id
          LEFT JOIN orders o ON o.table_id = tb.id
          WHERE o.id IS NULL AND tb.status = 'Confirmed'";

$result = mysqli_query($con, $query);

if (!$result) {
    echo "<p style='color:red;'>Error querying table bookings: " . mysqli_error($con) . "</p>";
} else {
    $count = mysqli_num_rows($result);
    echo "<h2>Found $count table bookings without corresponding orders</h2>";

    if ($count > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Booking Date</th><th>Booking Time</th><th>Guests</th><th>Package</th><th>Amount</th><th>Result</th></tr>";

        // Process each table booking
        while ($booking = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$booking['id']}</td>";
            echo "<td>{$booking['name']}</td>";
            echo "<td>{$booking['email']}</td>";
            echo "<td>{$booking['phone']}</td>";
            echo "<td>{$booking['booking_date']}</td>";
            echo "<td>{$booking['booking_time']}</td>";
            echo "<td>{$booking['guests']}</td>";
            echo "<td>{$booking['package_name']}</td>";
            echo "<td>{$booking['amount']}</td>";
            
            // Generate order_id (current date + random number)
            $order_id = date('Ymd') . rand(1000, 9999);
            
            // Check if order_id already exists
            $check_query = "SELECT id FROM orders WHERE order_id = '$order_id'";
            $check_result = mysqli_query($con, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                // If exists, generate a new one
                $order_id = date('Ymd') . rand(10000, 99999);
            }
            
            // Insert into orders table
            $insert_query = "INSERT INTO orders (order_id, customer_name, date_time, total_amount, payment_method, order_type, table_id, amount_paid, change_amount) 
                            VALUES ('$order_id', '{$booking['name']}', NOW(), {$booking['amount']}, 'Card', 'Table Reservation', {$booking['id']}, {$booking['amount']}, 0.00)";
            
            if (mysqli_query($con, $insert_query)) {
                $order_insert_id = mysqli_insert_id($con);
                echo "<td style='color:green;'>Success - Created Order #$order_insert_id</td>";
                
                // Now create an order item for this order
                $item_query = "INSERT INTO order_items (order_id, item_name, quantity, unit_price, total_price) 
                                VALUES ($order_insert_id, 'Table Package: {$booking['package_name']}', 1, {$booking['amount']}, {$booking['amount']})";
                
                if (mysqli_query($con, $item_query)) {
                    // Success
                } else {
                    echo " (Error adding order item: " . mysqli_error($con) . ")";
                }
            } else {
                echo "<td style='color:red;'>Error: " . mysqli_error($con) . "</td>";
            }
            
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check the process_table_reservation.php file for the correct code
echo "<h2>Verifying that process_table_reservation.php correctly adds orders</h2>";

// Read the process_table_reservation.php file if it exists
if (file_exists('process_table_reservation.php')) {
    $content = file_get_contents('process_table_reservation.php');
    
    // Check if it contains code to insert into the orders table
    if (stripos($content, 'INSERT INTO orders') !== false) {
        echo "<p style='color:green;'>The process_table_reservation.php file contains code to insert records into the orders table.</p>";
        
        // Extract the query that inserts into orders
        preg_match('/INSERT INTO orders.*?;/is', $content, $matches);
        if (!empty($matches)) {
            echo "<p>Order insertion query found:</p>";
            echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
        }
    } else {
        echo "<p style='color:red;'>Warning: The process_table_reservation.php file does not appear to insert records into the orders table. You may need to update it.</p>";
    }
} else {
    echo "<p style='color:red;'>Warning: The process_table_reservation.php file does not exist.</p>";
}

// Close the connection
mysqli_close($con);

echo "<p><a href='index.php'>Return to Dashboard</a></p>";
?> 