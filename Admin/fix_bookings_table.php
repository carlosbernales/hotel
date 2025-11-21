<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the column already exists
$check_column = mysqli_query($con, "SHOW COLUMNS FROM bookings LIKE 'last_modified'");
$column_exists = mysqli_num_rows($check_column) > 0;

echo "<h2>Fixing Bookings Table</h2>";

if (!$column_exists) {
    // Add the missing column
    $add_column = "ALTER TABLE bookings 
                  ADD COLUMN last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    
    if (mysqli_query($con, $add_column)) {
        echo "<p style='color:green'>✓ Successfully added 'last_modified' column to bookings table.</p>";
    } else {
        echo "<p style='color:red'>✗ Error adding column: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>The 'last_modified' column already exists in the bookings table.</p>";
}

// Now let's fix the process_room_transfer.php file to handle the case where room types don't match
echo "<h2>Fixing Room Transfer Process</h2>";

$process_file = 'process_room_transfer.php';
$content = file_get_contents($process_file);

if ($content !== false) {
    // Make a backup
    file_put_contents($process_file . '.bak', $content);
    echo "<p>Created backup of process_room_transfer.php</p>";
    
    // Update the SQL to only update room_type_id and total_amount, removing last_modified
    $updated_content = str_replace(
        "SET room_type_id = ?, \n                          total_amount = total_amount + ?,\n                          last_modified = NOW()",
        "SET room_type_id = ?, \n                          total_amount = total_amount + ?",
        $content
    );
    
    if ($content !== $updated_content) {
        if (file_put_contents($process_file, $updated_content)) {
            echo "<p style='color:green'>✓ Successfully updated process_room_transfer.php to fix the SQL query.</p>";
        } else {
            echo "<p style='color:red'>✗ Error updating file.</p>";
        }
    } else {
        echo "<p>No changes needed to process_room_transfer.php</p>";
    }
} else {
    echo "<p style='color:red'>✗ Could not read process_room_transfer.php</p>";
}

echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";

mysqli_close($con);
?> 