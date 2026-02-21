<?php
require 'db_con.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $paymentMethod = $data['payment_method'] ?? '';

    if (empty($paymentMethod)) {
        throw new Exception('Payment method is required');
    }

    $stmt = $pdo->prepare("
        SELECT name, display_name, qr_code_image, account_name, account_number 
        FROM payment_methods 
        WHERE name = ?
    ");
    $stmt->execute([$paymentMethod]);
    $method = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$method) {
        throw new Exception('Payment method not found');
    }

    echo json_encode([
        'success' => true,
        'qr_code_image' => $method['qr_code_image'],
        'account_name' => $method['account_name'],
        'account_number' => $method['account_number']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 