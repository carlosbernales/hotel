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

// Log function for debugging
function logError($message) {
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, "user_errors.log");
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    redirectWithMessage("You must be an admin to add new users.");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    
    // For logging/debugging
    logError("Received form data: " . json_encode($_POST));
    
    // Set default values for other fields
    $address = '';
    $name = "$first_name $last_name";  // concatenate first and last name
    
    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($user_type)) {
        logError("Missing required fields");
        redirectWithMessage("All required fields must be filled.");
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logError("Invalid email format: $email");
        redirectWithMessage("Invalid email format.");
    }
    
    // Check if password is too short
    if (strlen($password) < 6) {
        logError("Password too short");
        redirectWithMessage("Password must be at least 6 characters long.");
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM userss WHERE email = ?";
    $check_stmt = $con->prepare($check_query);
    if (!$check_stmt) {
        logError("Prepare error (check email): " . $con->error);
        redirectWithMessage("Database error: " . $con->error);
    }
    
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result && $check_result->num_rows > 0) {
        logError("Email already exists: $email");
        redirectWithMessage("Email already exists. Please use a different email.");
    }
    
    // Hash the password for secure storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Keeping insert logic simple - only include essential fields
    try {
        // First try without the name field
        $insert_query = "INSERT INTO userss (first_name, last_name, email, password, actual_password, 
                        contact_number, user_type) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $con->prepare($insert_query);
        if (!$insert_stmt) {
            throw new Exception("Prepare error: " . $con->error);
        }
        
        $insert_stmt->bind_param("sssssss", 
            $first_name, 
            $last_name, 
            $email, 
            $hashed_password,  // Store hashed password
            $password,  // Store actual password for visibility in admin panel
            $contact_number,
            $user_type
        );
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Execute error: " . $insert_stmt->error);
        }
        
        // Success!
        logError("User added successfully: $email");
        redirectWithMessage("User added successfully!", "success");
        
    } catch (Exception $e) {
        // Log the error
        logError("Insert failed: " . $e->getMessage());
        
        // Try alternate query if first one failed
        try {
            // Try with fewer fields
            $insert_query = "INSERT INTO userss (first_name, last_name, email, password, user_type) 
                            VALUES (?, ?, ?, ?, ?)";
            
            $insert_stmt = $con->prepare($insert_query);
            if (!$insert_stmt) {
                throw new Exception("Alternate prepare error: " . $con->error);
            }
            
            $insert_stmt->bind_param("sssss", 
                $first_name, 
                $last_name, 
                $email, 
                $hashed_password,  // Store hashed password
                $user_type
            );
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Alternate execute error: " . $insert_stmt->error);
            }
            
            // Success with alternate query
            logError("User added successfully with alternate query: $email");
            redirectWithMessage("User added successfully!", "success");
            
        } catch (Exception $e2) {
            // Both attempts failed
            logError("Both insert attempts failed: " . $e2->getMessage());
            redirectWithMessage("Failed to add user. Please try again or contact support. Error: " . $e2->getMessage());
        }
    }
} else {
    // Not a POST request
    redirectWithMessage("Invalid request method.");
}
?>