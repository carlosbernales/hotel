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

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    redirectWithMessage("You must be logged in to change your password.");
}

// Get the user ID
$user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        redirectWithMessage("All fields are required.");
    }
    
    // Check if new password and confirmation match
    if ($new_password !== $confirm_password) {
        redirectWithMessage("New password and confirmation do not match.");
    }
    
    // Check if the new password is too short
    if (strlen($new_password) < 6) {
        redirectWithMessage("New password must be at least 6 characters long.");
    }
    
    // Get the user's current password from database
    $query = "SELECT password, actual_password FROM userss WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the current password matches
        $stored_password = $user['actual_password'] ?? $user['password'];
        
        if ($current_password !== $stored_password) {
            // If we have hashed passwords, we would use password_verify() here
            redirectWithMessage("Current password is incorrect.");
        }
        
        // Update the password
        $update_query = "UPDATE userss SET password = ?, actual_password = ? WHERE id = ?";
        $update_stmt = $con->prepare($update_query);
        $update_stmt->bind_param("ssi", $new_password, $new_password, $user_id);
        
        if ($update_stmt->execute()) {
            redirectWithMessage("Password changed successfully!", "success");
        } else {
            redirectWithMessage("Failed to update password: " . $con->error);
        }
    } else {
        redirectWithMessage("User not found.");
    }
} else {
    // Not a POST request
    redirectWithMessage("Invalid request method.");
}
?> 