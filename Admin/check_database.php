<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check table structure
function show_table_structure($conn, $table_name) {
    echo "<h3>Structure of '$table_name' table:</h3>";
    
    $result = mysqli_query($conn, "DESCRIBE $table_name");
    if (!$result) {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
        return;
    }
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No columns found for table '$table_name'</p>";
    }
}

// Function to check sample data
function show_sample_data($conn, $table_name, $limit = 5) {
    echo "<h3>Sample data from '$table_name' table (up to $limit rows):</h3>";
    
    $result = mysqli_query($conn, "SELECT * FROM $table_name LIMIT $limit");
    if (!$result) {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
        return;
    }
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
        
        // Table header
        $first_row = mysqli_fetch_assoc($result);
        echo "<tr>";
        foreach ($first_row as $column => $value) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        
        // Reset pointer and display data
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No data found in table '$table_name'</p>";
    }
}

// Function to check if column exists
function column_exists($conn, $table, $column) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

echo "<h1>Database Check for Room Booking System</h1>";

// 1. Check bookings table
echo "<h2>Bookings Table Check</h2>";
$bookings_table = mysqli_query($con, "SHOW TABLES LIKE 'bookings'");
if (mysqli_num_rows($bookings_table) > 0) {
    echo "<p style='color:green'>✓ 'bookings' table exists</p>";
    show_table_structure($con, 'bookings');
    show_sample_data($con, 'bookings');
    
    // Check if room_number column exists
    if (column_exists($con, 'bookings', 'room_number')) {
        echo "<p style='color:green'>✓ 'room_number' column already exists in the bookings table</p>";
    } else {
        echo "<p style='color:orange'>⚠ 'room_number' column does not exist in the bookings table and needs to be added</p>";
    }
} else {
    echo "<p style='color:red'>✗ 'bookings' table does not exist!</p>";
}

// 2. Check rooms table
echo "<h2>Rooms Table Check</h2>";
$rooms_table = mysqli_query($con, "SHOW TABLES LIKE 'rooms'");
if (mysqli_num_rows($rooms_table) > 0) {
    echo "<p style='color:green'>✓ 'rooms' table exists</p>";
    show_table_structure($con, 'rooms');
    show_sample_data($con, 'rooms');
} else {
    echo "<p style='color:red'>✗ 'rooms' table does not exist!</p>";
    
    // Check for similar tables
    echo "<p>Checking for similar tables...</p>";
    $result = mysqli_query($con, "SHOW TABLES LIKE '%room%'");
    if (mysqli_num_rows($result) > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_row($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tables with 'room' in the name found.</p>";
    }
}

// 3. Check room_types table
echo "<h2>Room Types Table Check</h2>";
$room_types_table = mysqli_query($con, "SHOW TABLES LIKE 'room_types'");
if (mysqli_num_rows($room_types_table) > 0) {
    echo "<p style='color:green'>✓ 'room_types' table exists</p>";
    show_table_structure($con, 'room_types');
    show_sample_data($con, 'room_types');
} else {
    echo "<p style='color:red'>✗ 'room_types' table does not exist!</p>";
}

// Close connection
mysqli_close($con);
?> 