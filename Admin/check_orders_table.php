<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h1>Orders Table Check</h1>";

// Check if the table exists
$table_exists = mysqli_query($con, "SHOW TABLES LIKE 'orders'");
if (mysqli_num_rows($table_exists) == 0) {
    echo "<p style='color:red;'>The orders table does not exist!</p>";
    exit;
}

// Get table structure
echo "<h2>Table Structure</h2>";
$structure = mysqli_query($con, "SHOW COLUMNS FROM orders");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($column = mysqli_fetch_assoc($structure)) {
    echo "<tr>";
    echo "<td>{$column['Field']}</td>";
    echo "<td>{$column['Type']}</td>";
    echo "<td>{$column['Null']}</td>";
    echo "<td>{$column['Key']}</td>";
    echo "<td>{$column['Default']}</td>";
    echo "<td>{$column['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check for the order_type column which is needed for advance orders
$order_type_exists = false;
mysqli_data_seek($structure, 0); // Reset the pointer to start of result
while ($column = mysqli_fetch_assoc($structure)) {
    if ($column['Field'] == 'order_type') {
        $order_type_exists = true;
        break;
    }
}

if (!$order_type_exists) {
    echo "<p style='color:red;'>The orders table is missing the 'order_type' column needed for advance orders!</p>";
}

// Get recent orders
echo "<h2>Recent Orders (Last 10)</h2>";
$recent_orders = mysqli_query($con, "SELECT * FROM orders ORDER BY id DESC LIMIT 10");
if (!$recent_orders) {
    echo "<p style='color:red;'>Error querying recent orders: " . mysqli_error($con) . "</p>";
} else {
    if (mysqli_num_rows($recent_orders) == 0) {
        echo "<p>No orders found in the table.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        
        // Get the column names for the header
        $fields = mysqli_fetch_fields($recent_orders);
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        
        // Display each row of data
        mysqli_data_seek($recent_orders, 0); // Reset pointer
        while ($order = mysqli_fetch_assoc($recent_orders)) {
            echo "<tr>";
            foreach ($order as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    }
}

// Specifically check for advance orders
echo "<h2>Advance Orders (Last 10)</h2>";
$advance_orders = mysqli_query($con, "SELECT * FROM orders WHERE order_type = 'advance' ORDER BY id DESC LIMIT 10");
if (!$advance_orders) {
    echo "<p style='color:red;'>Error querying advance orders: " . mysqli_error($con) . "</p>";
} else {
    if (mysqli_num_rows($advance_orders) == 0) {
        echo "<p style='color:orange;'>No advance orders found. This is the issue - advance orders are not being saved to the database.</p>";
    } else {
        echo "<p style='color:green;'>" . mysqli_num_rows($advance_orders) . " advance orders found in the database.</p>";
        
        echo "<table border='1' cellpadding='5'>";
        
        // Get the column names for the header
        $fields = mysqli_fetch_fields($advance_orders);
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        
        // Display each row of data
        mysqli_data_seek($advance_orders, 0); // Reset pointer
        while ($order = mysqli_fetch_assoc($advance_orders)) {
            echo "<tr>";
            foreach ($order as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    }
}

// Check related order items
echo "<h2>Order Items For Advance Orders</h2>";
// First check if order_items table exists
$items_table_exists = mysqli_query($con, "SHOW TABLES LIKE 'order_items'");
if (mysqli_num_rows($items_table_exists) == 0) {
    echo "<p style='color:red;'>The order_items table does not exist!</p>";
} else {
    // Get order items for advance orders
    $sql = "SELECT oi.* FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.order_type = 'advance' 
            ORDER BY oi.id DESC LIMIT 10";
    
    $order_items = mysqli_query($con, $sql);
    if (!$order_items) {
        echo "<p style='color:red;'>Error querying order items: " . mysqli_error($con) . "</p>";
    } else {
        if (mysqli_num_rows($order_items) == 0) {
            echo "<p style='color:orange;'>No order items found for advance orders.</p>";
        } else {
            echo "<p style='color:green;'>" . mysqli_num_rows($order_items) . " order items found for advance orders.</p>";
            
            echo "<table border='1' cellpadding='5'>";
            
            // Get the column names for the header
            $fields = mysqli_fetch_fields($order_items);
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr>";
            
            // Display each row of data
            mysqli_data_seek($order_items, 0); // Reset pointer
            while ($item = mysqli_fetch_assoc($order_items)) {
                echo "<tr>";
                foreach ($item as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        }
    }
}

// Show logs that might have errors
echo "<h2>Recent Logs</h2>";
echo "<h3>Error Log (Last 10 lines)</h3>";
if (file_exists('table_reservation_errors.log')) {
    $error_log = array_slice(file('table_reservation_errors.log'), -10);
    echo "<pre>" . implode('', $error_log) . "</pre>";
} else {
    echo "<p>No error log file found.</p>";
}

echo "<h3>Order Debug (Last 10 lines)</h3>";
if (file_exists('order_success.log')) {
    $order_log = array_slice(file('order_success.log'), -10);
    echo "<pre>" . implode('', $order_log) . "</pre>";
} else {
    echo "<p>No order log file found.</p>";
}

// Database connection status
echo "<h2>Database Connection Status</h2>";
if ($con) {
    echo "<p style='color:green;'>Database connection is working.</p>";
} else {
    echo "<p style='color:red;'>Database connection failed!</p>";
}

// Close the connection
mysqli_close($con);
?> 