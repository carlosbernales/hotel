<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain');

try {
    // Include database connection
    require 'db_con.php';
    
    echo "Database connection successful!\n\n";
    
    // Check if tables exist
    $tables = ['menu_categories', 'menu_items', 'menu_items_addons'];
    
    foreach ($tables as $table) {
        echo "Checking table: $table\n";
        $checkTable = $pdo->query("SHOW TABLES LIKE '$table'");
        
        if ($checkTable->rowCount() > 0) {
            echo "- Table '$table' exists.\n";
            
            // Get column information
            $columns = $pdo->query("DESCRIBE $table");
            echo "  Columns:\n";
            while ($column = $columns->fetch(PDO::FETCH_ASSOC)) {
                echo "  - {$column['Field']} ({$column['Type']})\n";
            }
            
            // Get row count
            $count = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch(PDO::FETCH_ASSOC);
            echo "  Rows: {$count['count']}\n\n";
        } else {
            echo "- Table '$table' does NOT exist!\n\n";
        }
    }
    
    // Test query for menu_categories
    echo "\nTesting menu_categories query:\n";
    $query = "SELECT * FROM menu_categories";
    
    // Check if is_active column exists
    $hasIsActive = $pdo->query("SHOW COLUMNS FROM menu_categories LIKE 'is_active'")->rowCount() > 0;
    if ($hasIsActive) {
        $query .= " WHERE is_active = 1";
    }
    $query .= " ORDER BY display_order ASC";
    
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($categories) . " categories.\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
