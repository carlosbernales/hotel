<?php
require 'db_con.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT name FROM discount_types WHERE status = 'active'");
    $stmt->execute();
    $discountTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Always include 'regular' as an option
    array_unshift($discountTypes, 'regular');
    
    echo json_encode([
        'success' => true,
        'types' => $discountTypes
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => "Error fetching discount types: " . $e->getMessage()
    ]);
}
?> 