<?php
require_once 'db.php';

// Add description column to menu_items table if it doesn't exist
$sql = "ALTER TABLE menu_items 
        ADD COLUMN IF NOT EXISTS description TEXT NULL DEFAULT NULL AFTER price";

if (mysqli_query($con, $sql)) {
    echo "Description column added successfully to menu_items table\n";
} else {
    echo "Error adding description column: " . mysqli_error($con) . "\n";
}

// Close connection
mysqli_close($con);
?>
