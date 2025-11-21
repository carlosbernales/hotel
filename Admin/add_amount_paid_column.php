<?php
require_once 'db.php';

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add amount_paid column to bookings table
$sql = "ALTER TABLE bookings ADD COLUMN amount_paid DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount";

if (mysqli_query($con, $sql)) {
    echo "Successfully added amount_paid column to bookings table";
} else {
    echo "Error adding column: " . mysqli_error($con);
}

mysqli_close($con);
?> 