<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    
    // Get current user's ID from session
    $user_id = $_SESSION['user_id'];
    
    if (!$user_id) {
        $_SESSION['error_msg'] = "Error: No user is currently logged in.";
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
            $_SESSION['success_msg'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Error updating profile: " . $con->error;
        }
    } else {
        $_SESSION['error_msg'] = "Error preparing query: " . $con->error;
    }
}

header("Location: my_profile.php");
exit();

?> 