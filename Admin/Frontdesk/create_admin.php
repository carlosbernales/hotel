<?php
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Admin account details
$email = 'admin@casa.com';
$raw_password = 'admin123';
$password = password_hash($raw_password, PASSWORD_DEFAULT);
$firstname = 'Admin';
$lastname = 'User';
$username = 'admin';
$address = 'Casa Admin Office';
$profile_photo = ''; // Empty for now
$contactnum = '09000000000'; // Default contact number
$usertype = 'admin'; // Set usertype explicitly
$can_order_from_cafe = 0; // Default value

try {
    // Check if user already exists in userss table
    $check_sql = "SELECT * FROM userss WHERE email = ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("An admin account already exists with this email.");
    }

    // Insert into userss table (for login)
    $sql = "INSERT INTO userss (email, password, user_type) VALUES (?, ?, 'admin')";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    
    if (!$stmt->execute()) {
        throw new Exception("Error creating admin account: " . $stmt->error);
    }

    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px;'>";
    echo "<h2 style='color: #4CAF50;'>Admin Account Created Successfully!</h2>";
    echo "<p><strong>Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Password: " . htmlspecialchars($raw_password) . "</li>";
    echo "</ul>";
    echo "<p style='color: #f44336;'><strong>Important Security Notes:</strong></p>";
    echo "<ul>";
    echo "<li>Please save these credentials somewhere safe</li>";
    echo "<li>Delete this file (create_admin.php) immediately after use</li>";
    echo "<li>Change your password after first login</li>";
    echo "</ul>";
    echo "</div>";

} catch (Exception $e) {
    die("<div style='color: #f44336; font-family: Arial, sans-serif; padding: 20px;'><strong>Error:</strong> " . $e->getMessage() . "</div>");
}
?>
