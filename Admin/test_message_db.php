<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h2>Database Connection Test</h2>";

// Check if messages table exists
$check_table = $con->query("SHOW TABLES LIKE 'messages'");

if ($check_table->num_rows == 0) {
    echo "<p>The 'messages' table doesn't exist. Creating it now...</p>";
    
    // Create messages table
    $create_table_sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL DEFAULT 0,
        subject VARCHAR(255),
        message TEXT NOT NULL,
        sender_type VARCHAR(20) NOT NULL,
        read_status TINYINT(1) NOT NULL DEFAULT 0,
        status VARCHAR(20) NOT NULL DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($con->query($create_table_sql)) {
        echo "<p>Messages table created successfully!</p>";
    } else {
        echo "<p>Error creating messages table: " . $con->error . "</p>";
    }
} else {
    echo "<p>The 'messages' table exists. Checking structure...</p>";
    
    // Get table structure
    $table_info = $con->query("DESCRIBE messages");
    echo "<h3>Table Structure:</h3>";
    echo "<pre>";
    while ($row = $table_info->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
    
    // Try inserting a test message
    echo "<h3>Testing message insertion:</h3>";
    
    $test_message = "This is a test message from the database checker script.";
    $test_subject = "Test Message";
    $user_id = 0;
    $sender_type = "system";
    
    $stmt = $con->prepare("INSERT INTO messages (user_id, subject, message, sender_type, read_status, status, created_at) 
                          VALUES (?, ?, ?, ?, 0, 'unread', NOW())");
    
    if (!$stmt) {
        echo "<p>Prepare failed: " . $con->error . "</p>";
    } else {
        $stmt->bind_param('isss', $user_id, $test_subject, $test_message, $sender_type);
        
        if ($stmt->execute()) {
            echo "<p>Test message inserted successfully!</p>";
        } else {
            echo "<p>Failed to insert test message: " . $stmt->error . "</p>";
        }
        
        $stmt->close();
    }
}

echo "<p><a href='message.php'>Return to messages page</a></p>";
?> 