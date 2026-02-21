<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if table_type_id is provided
if (!isset($_GET['table_type_id']) || !is_numeric($_GET['table_type_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid table type ID']);
    exit;
}

$tableTypeId = (int)$_GET['table_type_id'];

try {
    // Query to get available tables of the selected type
    $stmt = $pdo->prepare("
        SELECT tn.id, tn.table_number, tn.status 
        FROM table_number tn
        WHERE tn.table_type_fk_id = :table_type_id 
        AND tn.status = 'available'
        ORDER BY tn.table_number
    ");
    
    $stmt->execute([':table_type_id' => $tableTypeId]);
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'tables' => $tables
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching available tables: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch available tables'
    ]);
}
?>
