<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Fetch active discount types from database
    $stmt = $pdo->prepare("SELECT type_name, percentage FROM discount_types WHERE status = 'active' ORDER BY type_name");
    $stmt->execute();
    $discountTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'discount_types' => $discountTypes
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching discount types: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
