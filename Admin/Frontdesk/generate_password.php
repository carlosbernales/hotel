<?php
header('Content-Type: application/json');

function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
}

try {
    $password = generatePassword();
    echo json_encode([
        'success' => true,
        'password' => $password
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error generating password: ' . $e->getMessage()
    ]);
}
?> 