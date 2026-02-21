<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require 'db_con.php';

echo "<h2>Database Structure Check</h2>";

try {
    // Check if database is connected
    $db_name = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "<h3>Connected to database: " . htmlspecialchars($db_name) . "</h3>";
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p>No tables found in the database.</p>";
    } else {
        echo "<h3>Tables in database:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
        
        // Check room_types table
        if (in_array('room_types', $tables)) {
            echo "<h3>room_types table:</h3>";
            
            // Get table structure
            $columns = $pdo->query("SHOW COLUMNS FROM room_types")->fetchAll(PDO::FETCH_COLUMN);
            echo "<h4>Columns:</h4><pre>" . print_r($columns, true) . "</pre>";
            
            // Get sample data
            $sample = $pdo->query("SELECT * FROM room_types LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Sample data (first 5 rows):</h4>";
            if (empty($sample)) {
                echo "<p>No data found in room_types table.</p>";
            } else {
                echo "<pre>" . print_r($sample, true) . "</pre>";
            }
        } else {
            echo "<p>room_types table does not exist.</p>";
        }
        
        // Check room_numbers table
        if (in_array('room_numbers', $tables)) {
            echo "<h3>room_numbers table:</h3>";
            
            // Get table structure
            $columns = $pdo->query("SHOW COLUMNS FROM room_numbers")->fetchAll(PDO::FETCH_COLUMN);
            echo "<h4>Columns:</h4><pre>" . print_r($columns, true) . "</pre>";
            
            // Get sample data
            $sample = $pdo->query("SELECT * FROM room_numbers LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Sample data (first 5 rows):</h4>";
            if (empty($sample)) {
                echo "<p>No data found in room_numbers table.</p>";
            } else {
                echo "<pre>" . print_r($sample, true) . "</pre>";
                
                // Count by status
                $statusCount = $pdo->query("SELECT status, COUNT(*) as count FROM room_numbers GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h4>Room numbers by status:</h4><pre>" . print_r($statusCount, true) . "</pre>";
            }
        } else {
            echo "<p>room_numbers table does not exist.</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . print_r($pdo->errorInfo(), true) . "</pre>";
}
?>
