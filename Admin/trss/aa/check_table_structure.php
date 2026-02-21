<?php
require 'db_con.php';

try {
    $stmt = $pdo->query("DESCRIBE table_types");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>table_types structure:</h3>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // Show sample data
    echo "<h3>Sample data:</h3>";
    $stmt = $pdo->query("SELECT * FROM table_types LIMIT 1");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($sample);
    echo "</pre>";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
