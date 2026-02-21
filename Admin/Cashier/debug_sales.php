<?php
require_once('db.php');

echo "<h1>Sales Debug Information</h1>";

// Check database connection
if (!$con) {
    echo "<h2 style='color: red;'>Database Connection Failed</h2>";
    echo "Error: " . mysqli_connect_error();
    exit;
} else {
    echo "<h2 style='color: green;'>Database Connection Successful</h2>";
}

// Check if orders_table exists
echo "<h3>Checking orders_table...</h3>";
$tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'orders_table'");
if (mysqli_num_rows($tableCheck) > 0) {
    echo "<p style='color: green;'>✓ orders_table exists</p>";
} else {
    echo "<p style='color: red;'>✗ orders_table does not exist</p>";
    
    // Show all tables
    echo "<h4>Available tables:</h4>";
    $tables = mysqli_query($con, "SHOW TABLES");
    while ($table = mysqli_fetch_array($tables)) {
        echo "- " . $table[0] . "<br>";
    }
    exit;
}

// Check table structure
echo "<h3>orders_table Structure:</h3>";
$structure = mysqli_query($con, "DESCRIBE orders_table");
echo "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while ($col = mysqli_fetch_assoc($structure)) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
}
echo "</table>";

// Check total records
echo "<h3>Total Records:</h3>";
$total = mysqli_query($con, "SELECT COUNT(*) as count FROM orders_table");
$totalRow = mysqli_fetch_assoc($total);
echo "<p>Total orders: " . $totalRow['count'] . "</p>";

// Check distinct statuses
echo "<h3>Status Distribution:</h3>";
$statusQuery = mysqli_query($con, "SELECT status, COUNT(*) as count FROM orders_table GROUP BY status");
echo "<table border='1'><tr><th>Status</th><th>Count</th></tr>";
while ($status = mysqli_fetch_assoc($statusQuery)) {
    echo "<tr><td>{$status['status']}</td><td>{$status['count']}</td></tr>";
}
echo "</table>";

// Check sample data
echo "<h3>Sample Data (first 5 records):</h3>";
$sample = mysqli_query($con, "SELECT * FROM orders_table LIMIT 5");
echo "<table border='1'>";
if ($sample && mysqli_num_rows($sample) > 0) {
    // Get column names
    $fields = mysqli_fetch_fields($sample);
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";
    
    // Reset pointer and show data
    mysqli_data_seek($sample, 0);
    while ($row = mysqli_fetch_assoc($sample)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='100%'>No data found</td></tr>";
}
echo "</table>";

// Test sales queries with different status values
echo "<h3>Testing Sales Queries:</h3>";

$statuses_to_test = ['completed', 'Completed', 'finished', 'Finished', 'paid', 'Paid'];

foreach ($statuses_to_test as $status) {
    echo "<h4>Testing status: '$status'</h4>";
    
    $testQuery = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total FROM orders_table WHERE status = '$status'";
    $result = mysqli_query($con, $testQuery);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>Orders: {$row['count']}, Total: ₱" . number_format($row['total'], 2) . "</p>";
    } else {
        echo "<p style='color: red;'>Query Error: " . mysqli_error($con) . "</p>";
    }
}

// Test today's sales specifically
echo "<h3>Today's Sales Test:</h3>";
$todayQuery = "SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total FROM orders_table WHERE DATE(order_date) = CURDATE()";
$todayResult = mysqli_query($con, $todayQuery);
if ($todayResult) {
    $todayRow = mysqli_fetch_assoc($todayResult);
    echo "<p>Today's orders: {$todayRow['count']}, Total: ₱" . number_format($todayRow['total'], 2) . "</p>";
} else {
    echo "<p style='color: red;'>Today's Query Error: " . mysqli_error($con) . "</p>";
}

?>
