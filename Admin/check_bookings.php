<?php
require_once 'db.php';

echo "<pre>";

// Check if tables exist
$tables = array('table_bookings', 'bookings');
foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = mysqli_query($con, $sql);
    echo "Table '$table' exists: " . (mysqli_num_rows($result) > 0 ? "Yes" : "No") . "\n";
}

echo "\n";

// Check table structure
foreach ($tables as $table) {
    $sql = "SHOW COLUMNS FROM $table";
    $result = mysqli_query($con, $sql);
    if ($result) {
        echo "Columns in $table:\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
        echo "\n";
    } else {
        echo "Error getting columns for $table: " . mysqli_error($con) . "\n\n";
    }
}

// Check table bookings count
$sql = "SELECT COUNT(*) as count FROM table_bookings";
$result = mysqli_query($con, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($resut);
    echo "Table bookings count: " . $row['count'] . "\n";
} else {
    echo "Error counting table_bookings: " . mysqli_error($con) . "\n";
}

// Check room bookings count
$sql = "SELECT COUNT(*) as count FROM bookings";
$result = mysqli_query($con, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Room bookings count: " . $row['count'] . "\n";
} else {
    echo "Error counting bookings: " . mysqli_error($con) . "\n";
}

echo "</pre>";
