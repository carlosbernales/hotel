<?php
// Security - only allow this to run on localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die("This script can only be run locally for security reasons.");
}

require_once 'db.php';
require_once 'auth/AuthManager.php';

// Only run if approved
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$confirmed) {
    echo "<h1>Create Admin User</h1>";
    echo "<p>This script will create an admin user. Click the link below to confirm.</p>";
    echo "<p><a href='?confirm=yes'>Create Admin User</a></p>";
    exit;
}

// Initialize AuthManager
$authManager = new AuthManager($con);

// Admin user details
$firstname = "Admin";
$lastname = "User";
$phone = "09123456789";
$email = "admin@example.com";
$password = "Admin123";  // You should change this to a secure password

try {
    // Create admin user
    $authManager->createAdminUser($firstname, $lastname, $phone, $email, $password);
    echo "<h1>Success!</h1>";
    echo "<p>Admin user created successfully with these credentials:</p>";
    echo "<ul>";
    echo "<li>Email: $email</li>";
    echo "<li>Password: $password</li>";
    echo "</ul>";
    echo "<p>Please login using these credentials and then change your password immediately.</p>";
    echo "<p><a href='Customer/aa/login.php'>Go to login page</a></p>";
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Failed to create admin user: " . $e->getMessage() . "</p>";
}
?>
