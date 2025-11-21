<?php
require_once 'db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if admin is verified
if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please verify your password first']);
    exit;
}

try {
    // Get all users
    $query = "SELECT id, first_name, last_name, email, user_type FROM userss ORDER BY user_type, first_name";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Generate a new temporary password for each user
        $temp_password = generateTemporaryPassword();
        
        // Update the user's password in the database
        $update_query = "UPDATE userss SET password = ? WHERE id = ?";
        $update_stmt = $con->prepare($update_query);
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
        $update_stmt->bind_param('si', $hashed_password, $row['id']);
        $update_stmt->execute();
        
        $users[] = [
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email'],
            'user_type' => ucfirst($row['user_type']),
            'password' => $temp_password
        ];
    }
    
    // Clear the verification flag after use
    unset($_SESSION['verified']);
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function generateTemporaryPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
} 