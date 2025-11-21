<?php
require 'db_con.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT payment_option_id, option_name, percentage FROM payment_options WHERE status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'options' => $options]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch payment options']);
}
?>
