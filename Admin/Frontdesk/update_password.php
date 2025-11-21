<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error_msg'] = "New passwords do not match!";
        header("Location: my_profile.php");
        exit();
    }
    
    // Verify current password
    $query = "SELECT password FROM admin WHERE username = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if (password_verify($current_password, $admin['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE admin SET password = ? WHERE username = ?";
            
            if ($update_stmt = $con->prepare($update_query)) {
                $update_stmt->bind_param("ss", $hashed_password, $username);
                
                if ($update_stmt->execute()) {
                    $_SESSION['success_msg'] = "Password updated successfully!";
                } else {
                    $_SESSION['error_msg'] = "Error updating password: " . $con->error;
                }
            }
        } else {
            $_SESSION['error_msg'] = "Current password is incorrect!";
        }
    }
}

header("Location: my_profile.php");
exit();
?> 