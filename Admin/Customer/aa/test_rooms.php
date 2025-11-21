<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require 'db_con.php';

echo "<h2>Testing Room Tables and Data</h2>";

try {
    // Check if room_types table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'room_types'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ room_types table exists</p>";
        
        // Count rooms in room_types
        $count = $pdo->query("SELECT COUNT(*) as count FROM room_types WHERE status = 'active'")->fetch();
        echo "<p>Active rooms in room_types: " . $count['count'] . "</p>";
        
        // Show first few rooms
        $rooms = $pdo->query("SELECT room_type_id, room_type, price, status FROM room_types LIMIT 5")->fetchAll();
        echo "<h3>Sample Rooms:</h3>";
        echo "<pre>" . print_r($rooms, true) . "</pre>";
    } else {
        echo "<p>❌ room_types table does not exist</p>";
    }
    
    // Check if room_numbers table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'room_numbers'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ room_numbers table exists</p>";
        
        // Count room numbers
        $count = $pdo->query("SELECT COUNT(*) as count FROM room_numbers")->fetch();
        echo "<p>Total room numbers: " . $count['count'] . "</p>";
    } else {
        echo "<p>❌ room_numbers table does not exist</p>";
    }
    
    // Check for any PDO errors
    $error = $pdo->errorInfo();
    if ($error[0] !== '00000') {
        echo "<h3>PDO Error:</h3>";
        echo "<pre>" . print_r($error, true) . "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
