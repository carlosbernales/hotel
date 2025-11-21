<?php
require_once 'db.php';

// Only start session if one doesn't exist already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Get user ID from form or session
    $user_id = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        $_SESSION['error_msg'] = "Error: Cannot identify user for profile update.";
        header("Location: my_profile.php");
        exit();
    }
    
    // Validate inputs
    if (empty($first_name) || empty($last_name)) {
        $_SESSION['error_msg'] = "First name and last name are required.";
        header("Location: my_profile.php");
        exit();
    }
    
    // Update user information in the userss table
    $query = "UPDATE userss SET 
              first_name = ?,
              last_name = ?,
              contact_number = ?,
              address = ?
              WHERE id = ?";
              
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssssi", 
            $first_name, 
            $last_name, 
            $contact_number, 
            $address,
            $user_id
        );
        
        if ($stmt->execute()) {
            // Update was successful
            $_SESSION['success_msg'] = "Profile updated successfully!";
            
            // Update session variables with new data
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
        } else {
            $_SESSION['error_msg'] = "Error updating profile: " . $con->error;
        }
    } else {
        $_SESSION['error_msg'] = "Error preparing query: " . $con->error;
    }
} else {
    // Not a POST request
    $_SESSION['error_msg'] = "Invalid request method.";
}

// Redirect back to profile page
header("Location: my_profile.php");
exit();
?> 