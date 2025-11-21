<?php
require 'db_con.php';
session_start();

if (!isset($_SESSION['userid']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $userid = $_SESSION['userid'];

    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userid]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Current password is incorrect";
        header('location: profile.php');
        exit();
    }

    // Verify new passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match";
        header('location: profile.php');
        exit();
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    
    if ($update_stmt->execute([$hashed_password, $userid])) {
        $_SESSION['success'] = "Password updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update password";
    }
    
    header('location: profile.php');
    exit();
} 