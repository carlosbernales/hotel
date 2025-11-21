<?php
// Include database connection
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if table exists first
$tableCheckQuery = "SHOW TABLES LIKE 'table_number'";
$tableCheckResult = $con->query($tableCheckQuery);

if ($tableCheckResult && $tableCheckResult->num_rows == 0) {
    // Table doesn't exist, create it
    $createTableSQL = "CREATE TABLE IF NOT EXISTS table_number (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_number INT NOT NULL UNIQUE,
        status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
        occupied_at TIMESTAMP NULL DEFAULT NULL,
        order_id INT NULL DEFAULT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($con->query($createTableSQL)) {
        // Insert initial data
        $insertSQL = "INSERT INTO table_number (table_number, status) VALUES ";
        $values = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $values[] = "($i, 'available')";
        }
        
        $insertSQL .= implode(", ", $values);
        $con->query($insertSQL);
    }
}

// Fetch all tables with their status
$tables = [];
$tablesQuery = "SELECT * FROM table_number ORDER BY table_number ASC";
$tablesResult = $con->query($tablesQuery);

if ($tablesResult) {
    while($row = $tablesResult->fetch_assoc()) {
        $tables[] = [
            'id' => $row['id'],
            'table_number' => $row['table_number'],
            'is_occupied' => ($row['status'] === 'occupied')
        ];
    }
    echo json_encode(['success' => true, 'tables' => $tables]);
} else {
    // Return error if query fails
    echo json_encode(['success' => false, 'message' => $con->error]);
}

$con->close();
?>
