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
    redirectWithMessage("You must be an admin to delete users.");
}

// Check if user_id is provided
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    redirectWithMessage("No user specified for deletion.");
}

$user_id = intval($_GET['user_id']);

// Don't allow deleting your own account
if ($user_id == $_SESSION['user_id']) {
    redirectWithMessage("You cannot delete your own account.");
}

// Delete the user
$query = "DELETE FROM userss WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        redirectWithMessage("User deleted successfully.", "success");
    } else {
        redirectWithMessage("User not found or already deleted.");
    }
} else {
    redirectWithMessage("Error deleting user: " . $con->error);
}
?> 