<?php
require_once 'db.php';
require_once 'auth/AuthManager.php';

// Initialize error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize AuthManager
$authManager = new AuthManager($con);

// Test admin user details
$firstname = "Test";
$lastname = "Admin";
$phone = "09876543210";
$email = "testadmin@example.com";
$password = "TestAdmin123";  // This would be a secure password in production

try {
    // Create admin user
    $result = $authManager->createAdminUser($firstname, $lastname, $phone, $email, $password);
    
    echo "<h1>Test Results</h1>";
    
    if ($result) {
        echo "<p style='color: green;'>Admin user created successfully!</p>";
        echo "<p>Details:</p>";
        echo "<ul>";
        echo "<li>Name: $firstname $lastname</li>";
        echo "<li>Email: $email</li>";
        echo "<li>Password: $password</li>";
        echo "</ul>";
        
        // Verify the user was added by querying the database
        $stmt = $con->prepare("SELECT * FROM userss WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            echo "<p style='color: green;'>User verified in database with ID: " . $user['id'] . "</p>";
            
            // Optional: Test login with the created user
            $login_test = $authManager->validateLogin($email, $password);
            if ($login_test) {
                echo "<p style='color: green;'>Login test passed!</p>";
            } else {
                echo "<p style='color: red;'>Login test failed! Check password hashing.</p>";
            }
        } else {
            echo "<p style='color: red;'>Error: User not found in database after creation!</p>";
        }
    } else {
        echo "<p style='color: red;'>Admin user creation failed!</p>";
    }
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p style='color: red;'>Failed to create admin user: " . $e->getMessage() . "</p>";
    
    // Check if email already exists
    $stmt = $con->prepare("SELECT * FROM userss WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p>A user with this email already exists:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Name: " . $user['first_name'] . " " . $user['last_name'] . "</li>";
        echo "<li>User Type: " . $user['user_type'] . "</li>";
        echo "</ul>";
    }
}

// Add a link to go back to the admin panel
echo "<p><a href='my_profile.php'>Back to Admin Panel</a></p>";
?> 