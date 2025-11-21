<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Include database connection
require_once 'db.php';

echo "<h2>Messages Table Update Script</h2>";

// Check if messages table exists
$check_table = $con->query("SHOW TABLES LIKE 'messages'");

if ($check_table->num_rows == 0) {
    echo "<p>Error: The 'messages' table doesn't exist. Please create the messages table first.</p>";
} else {
    echo "<p>The 'messages' table exists. Checking for 'replies' column...</p>";
    
    // Check if the replies column exists
    $check_column = $con->query("SHOW COLUMNS FROM messages LIKE 'replies'");
    
    if ($check_column->num_rows == 0) {
        echo "<p>The 'replies' column doesn't exist. Adding it now...</p>";
        
        // Add the replies column
        $add_column_sql = "ALTER TABLE messages ADD COLUMN replies TEXT AFTER message";
        
        if ($con->query($add_column_sql)) {
            echo "<p>Success! The 'replies' column has been added to the messages table.</p>";
            echo "<p>Column details: TEXT type, positioned after the 'message' column.</p>";
        } else {
            echo "<p>Error adding 'replies' column: " . $con->error . "</p>";
        }
    } else {
        echo "<p>The 'replies' column already exists in the messages table.</p>";
    }
    
    // Show the current table structure
    echo "<h3>Current Messages Table Structure:</h3>";
    $table_info = $con->query("DESCRIBE messages");
    echo "<pre>";
    while ($row = $table_info->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

echo "<p><a href='message.php'>Return to messages page</a></p>";
?> 