<?php
// Script to add discount_percentage column to bookings table
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if column already exists
$check_column = "SHOW COLUMNS FROM bookings LIKE 'discount_percentage'";
$column_exists = mysqli_query($con, $check_column);

if ($column_exists && mysqli_num_rows($column_exists) > 0) {
    echo "Column 'discount_percentage' already exists in the bookings table.";
} else {
    // Add the column
    $add_column = "ALTER TABLE bookings ADD COLUMN discount_percentage DECIMAL(5,2) DEFAULT 0 AFTER discount_type";
    
    if (mysqli_query($con, $add_column)) {
        echo "Column 'discount_percentage' added successfully to the bookings table.";
    } else {
        echo "Error adding column: " . mysqli_error($con);
    }
}

// Show the updated table structure
$result = mysqli_query($con, "DESCRIBE bookings");
if ($result) {
    echo "<br><br>Updated table structure for bookings:<br>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] === NULL ? 'NULL' : $row['Default']) . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<br><br>Error getting table structure: " . mysqli_error($con);
}

mysqli_close($con);
?> 