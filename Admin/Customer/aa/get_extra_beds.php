<?php
session_start();
require 'db_con.php';

try {
    // Fetch available extra beds from the database
    $sql = "SELECT bed_id, bed_type, price, available_quantity 
            FROM extra_beds 
            WHERE status = 'Available' 
            AND available_quantity > 0";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $beds = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the results as JSON
    echo json_encode([
        'success' => true,
        'beds' => $beds
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching extra beds: ' . $e->getMessage()
    ]);
}
?> 