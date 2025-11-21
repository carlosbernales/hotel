<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h1>Table Bookings Structure Check</h1>";

// Check if the table exists
$table_exists = mysqli_query($con, "SHOW TABLES LIKE 'table_bookings'");
if (mysqli_num_rows($table_exists) == 0) {
    echo "<p style='color:red;'>The table_bookings table does not exist!</p>";
    exit;
}

// Get table structure
echo "<h2>Table Structure</h2>";
$structure = mysqli_query($con, "SHOW COLUMNS FROM table_bookings");
if (!$structure) {
    echo "<p style='color:red;'>Error querying table structure: " . mysqli_error($con) . "</p>";
    exit;
}

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

// Show sample data
echo "<h2>Sample Data (Latest 5 Records)</h2>";
$sample_data = mysqli_query($con, "SELECT * FROM table_bookings ORDER BY id DESC LIMIT 5");
if (!$sample_data) {
    echo "<p style='color:red;'>Error querying sample data: " . mysqli_error($con) . "</p>";
    exit;
}

if (mysqli_num_rows($sample_data) == 0) {
    echo "<p>No records found in the table.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    
    // Get the column names for the header
    $fields = mysqli_fetch_fields($sample_data);
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";
    
    // Display each row of data
    mysqli_data_seek($sample_data, 0); // Reset pointer
    while ($row = mysqli_fetch_assoc($sample_data)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
}

// Close the connection
mysqli_close($con);
?> 