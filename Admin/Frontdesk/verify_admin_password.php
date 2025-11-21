<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// Get the current admin's password from the session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    // Get the admin's stored password hash
    $query = "SELECT password FROM userss WHERE id = ? AND user_type = 'admin'";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }
    
    $admin = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($_POST['password'], $admin['password'])) {
        $_SESSION['verified'] = true; // Set a verification flag
        echo json_encode(['success' => true, 'message' => 'Password verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} 