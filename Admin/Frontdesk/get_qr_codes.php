<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM payment_qr_codes ORDER BY updated_at DESC LIMIT 1";
    $result = $con->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            'gcash_qr' => $row['gcash_qr'] ?? '',
            'gcash_name' => $row['gcash_name'] ?? '',
            'gcash_number' => $row['gcash_number'] ?? '',
            'maya_qr' => $row['maya_qr'] ?? '',
            'maya_name' => $row['maya_name'] ?? '',
            'maya_number' => $row['maya_number'] ?? ''
        ]);
    } else {
        echo json_encode([
            'gcash_qr' => '',
            'gcash_name' => '',
            'gcash_number' => '',
            'maya_qr' => '',
            'maya_name' => '',
            'maya_number' => ''
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
} 