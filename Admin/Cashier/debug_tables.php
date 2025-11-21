<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Output header for proper JSON content type
header('Content-Type: application/json');

// Check database connection
echo json_encode([
    'database_connected' => ($con && !$con->connect_error),
    'connection_error' => $con->connect_error ?? 'None',
    'database_name' => $dbname ?? 'Unknown'
]);

// Check if table exists
$tableCheckQuery = "SHOW TABLES LIKE 'table_number'";
$tableCheckResult = $con->query($tableCheckQuery);

if ($tableCheckResult === false) {
    echo "\n\nQuery error: " . $con->error;
    exit;
}

$tableExists = ($tableCheckResult && $tableCheckResult->num_rows > 0);

echo "\n\nTable exists: " . ($tableExists ? 'Yes' : 'No');

// If table doesn't exist, try to create it
if (!$tableExists) {
    $createTableSQL = "CREATE TABLE IF NOT EXISTS table_number (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_number INT NOT NULL UNIQUE,
        status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
        occupied_at TIMESTAMP NULL DEFAULT NULL,
        order_id INT NULL DEFAULT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $createResult = $con->query($createTableSQL);
    echo "\n\nTable creation result: " . ($createResult ? 'Success' : 'Failed - ' . $con->error);
    
    if ($createResult) {
        // Insert initial data
        $insertSQL = "INSERT INTO table_number (table_number, status) VALUES ";
        $values = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $values[] = "($i, 'available')";
        }
        
        $insertSQL .= implode(", ", $values);
        $insertResult = $con->query($insertSQL);
        
        echo "\n\nData insertion result: " . ($insertResult ? 'Success' : 'Failed - ' . $con->error);
    }
}

// Check table contents
$tablesQuery = "SELECT * FROM table_number ORDER BY table_number ASC";
$tablesResult = $con->query($tablesQuery);

if ($tablesResult === false) {
    echo "\n\nQuery error: " . $con->error;
    exit;
}

$tableCount = $tablesResult ? $tablesResult->num_rows : 0;
echo "\n\nNumber of table records: $tableCount";

$tables = [];
if ($tablesResult && $tablesResult->num_rows > 0) {
    while($row = $tablesResult->fetch_assoc()) {
        $tables[] = $row;
    }
}

echo "\n\nTable data: " . json_encode($tables, JSON_PRETTY_PRINT);

$con->close();
?>
