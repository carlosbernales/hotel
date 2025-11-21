<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if column exists
function column_exists($conn, $table, $column) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

echo "<h1>Adding Room Number Column</h1>";

// Check if the room_number column already exists
if (column_exists($con, 'bookings', 'room_number')) {
    echo "<p>✅ The 'room_number' column already exists in the bookings table.</p>";
} else {
    // Add the room_number column
    $sql = "ALTER TABLE bookings ADD COLUMN room_number VARCHAR(20) NULL AFTER room_type_id";
    
    if (mysqli_query($con, $sql)) {
        echo "<p>✅ Successfully added 'room_number' column to the bookings table.</p>";
    } else {
        echo "<p>❌ Error adding column: " . mysqli_error($con) . "</p>";
    }
}

// Close the database connection
mysqli_close($con);
?> 