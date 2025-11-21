<?php
require_once 'db.php';

// Only start session if one doesn't exist already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to safely redirect with message
function redirectWithMessage($message, $type = 'error') {
    if ($type === 'error') {
        $_SESSION['error_msg'] = $message;
    } else {
        $_SESSION['success_msg'] = $message;
    }
    header("Location: my_profile.php");
    exit();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    redirectWithMessage("You must be an admin to reset passwords.");
}

// Check if user_id is provided
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    redirectWithMessage("No user specified for password reset.");
}

$user_id = intval($_GET['user_id']);

// Generate a new random password
function generateRandomPassword($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+";
    $password = "";
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

$new_password = generateRandomPassword();

// Update the user's password
$query = "UPDATE userss SET password = ?, actual_password = ? WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ssi", $new_password, $new_password, $user_id);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        redirectWithMessage("Password reset successfully to: " . $new_password, "success");
    } else {
        redirectWithMessage("User not found.");
    }
} else {
    redirectWithMessage("Error resetting password: " . $con->error);
}
?> 