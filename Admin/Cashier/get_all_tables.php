<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Get all tables with their assignment status
    $stmt = $pdo->prepare("
        SELECT 
            tn.id as table_id,
            tn.table_number,
            tn.status as table_status,
            tt.table_name,
            tt.id as table_type_id,
            tt.capacity,
            'Available' as assignment_status
        FROM table_number tn
        LEFT JOIN table_types tt ON tn.table_type_fk_id = tt.id
        ORDER BY tt.table_name, CAST(tn.table_number AS UNSIGNED), tn.table_number
    ");
    
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group tables by table name
    $groupedTables = [];
    foreach ($tables as $table) {
        $tableName = $table['table_name'] ?? 'Unknown Type';
        if (!isset($groupedTables[$tableName])) {
            $groupedTables[$tableName] = [
                'table_type_id' => $table['table_type_id'],
                'capacity' => $table['capacity'],
                'table_numbers' => []
            ];
        }
        $groupedTables[$tableName]['table_numbers'][] = [
            'table_id' => $table['table_id'],
            'table_number' => $table['table_number'],
            'table_status' => $table['table_status'],
            'assignment_status' => $table['assignment_status']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'tables' => $groupedTables
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching tables: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch tables'
    ]);
}
?>
