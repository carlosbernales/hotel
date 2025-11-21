<?php
// Include database connection
require_once 'db.php';

echo "<h2>Advanced Table Orders Debug</h2>";

// Check if table exists
$tableCheck = $con->query("SHOW TABLES LIKE 'advance_table_orders'");
if ($tableCheck->num_rows > 0) {
    echo "<p style='color:green'>Table 'advance_table_orders' exists in database.</p>";
    
    // Check table structure
    $tableStructure = $con->query("DESCRIBE advance_table_orders");
    echo "<h3>Table Structure:</h3>";
    echo "<ul>";
    while ($column = $tableStructure->fetch_assoc()) {
        echo "<li><strong>{$column['Field']}</strong> - {$column['Type']} - {$column['Key']}</li>";
    }
    echo "</ul>";
    
    // Check if there are any records
    $recordsCheck = $con->query("SELECT COUNT(*) as count FROM advance_table_orders");
    $count = $recordsCheck->fetch_assoc()['count'];
    echo "<p>Number of records in table: {$count}</p>";
    
    if ($count > 0) {
        // Show the records
        $records = $con->query("SELECT * FROM advance_table_orders ORDER BY id DESC");
        
        echo "<h3>Records:</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr>
                <th>ID</th>
                <th>Booking ID</th>
                <th>Customer Name</th>
                <th>Order Items</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Created At</th>
              </tr>";
        
        while ($row = $records->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['table_booking_id']}</td>";
            echo "<td>{$row['customer_name']}</td>";
            echo "<td><pre>" . htmlspecialchars(substr($row['order_items'], 0, 100)) . "...</pre></td>";
            echo "<td>{$row['total_amount']}</td>";
            echo "<td>{$row['payment_method']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color:red'>Table 'advance_table_orders' does NOT exist in database.</p>";
    echo "<p>Please run the create_table.php script first.</p>";
    echo "<a href='create_table.php' style='display:inline-block; padding:10px; background:#4CAF50; color:white; text-decoration:none;'>Create Table Now</a>";
}

// Let's also check the process_table_reservation.php file modification status
echo "<h3>process_table_reservation.php Status:</h3>";
$file = 'process_table_reservation.php';
if (file_exists($file)) {
    echo "<p>File exists, last modified: " . date("Y-m-d H:i:s", filemtime($file)) . "</p>";
    
    // Search for our modification in the file
    $fileContent = file_get_contents($file);
    if (strpos($fileContent, 'advance_table_orders') !== false) {
        echo "<p style='color:green'>Modification to save in advance_table_orders table is present.</p>";
    } else {
        echo "<p style='color:red'>Modification to save in advance_table_orders table is NOT present.</p>";
        echo "<p>The modified code to save data in the advance_table_orders table may not have been applied correctly.</p>";
    }
} else {
    echo "<p style='color:red'>File {$file} does not exist!</p>";
}

// Debug information from a sample advance order from POST data
echo "<h3>Debug Logging:</h3>";
echo "<p>Check your error log file for entries containing 'ADVANCE ORDER DETECTED' or 'advance_table_orders'.</p>";

// Add a link to go back to table_packages.php
echo "<p><a href='table_packages.php' style='display:inline-block; padding:10px; background:#2196F3; color:white; text-decoration:none;'>Back to Table Packages</a></p>";
?> 