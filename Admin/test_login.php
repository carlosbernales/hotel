<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'db.php';

// Function to test login
function testLogin($con, $email, $password) {
    echo "<h2>Testing Login for $email</h2>";
    
    // First, check if the email exists
    $stmt = $con->prepare("SELECT id, first_name, last_name, email, password, actual_password, user_type FROM userss WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<p style='color: red;'>Email not found in database.</p>";
        return;
    }
    
    $user = $result->fetch_assoc();
    echo "<p>User found in database: " . $user['first_name'] . " " . $user['last_name'] . " (ID: " . $user['id'] . ")</p>";
    echo "<p>User Type: " . $user['user_type'] . "</p>";
    
    // Check password
    echo "<p>Stored Password: " . ($user['password'] ?? 'NULL') . "</p>";
    echo "<p>Stored Actual Password: " . ($user['actual_password'] ?? 'NULL') . "</p>";
    
    // Test direct match against password
    if ($password === $user['password']) {
        echo "<p style='color: green;'>✓ Password matches the 'password' field directly.</p>";
    } else {
        echo "<p style='color: red;'>✗ Password does not match the 'password' field.</p>";
    }
    
    // Test direct match against actual_password
    if ($password === $user['actual_password']) {
        echo "<p style='color: green;'>✓ Password matches the 'actual_password' field directly.</p>";
    } else {
        echo "<p style='color: red;'>✗ Password does not match the 'actual_password' field.</p>";
    }
    
    // Simulate AuthManager login logic
    if ($password === $user['password'] || $password === $user['actual_password']) {
        echo "<p style='color: green;'>✓ Login would succeed with current logic.</p>";
    } else {
        echo "<p style='color: red;'>✗ Login would fail with current logic.</p>";
    }
}

// HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-form { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        h1, h2 { color: #333; }
    </style>
</head>
<body>
    <h1>Login Test Tool</h1>
    
    <div class='test-form'>
        <h2>Test Existing User Login</h2>
        <form method='post'>
            <div>
                <label for='email'>Email:</label>
                <input type='email' name='email' required>
            </div>
            <div style='margin-top: 10px;'>
                <label for='password'>Password:</label>
                <input type='password' name='password' required>
            </div>
            <div style='margin-top: 15px;'>
                <button type='submit' name='test_login'>Test Login</button>
            </div>
        </form>
    </div>";

// Process login test if submitted
if (isset($_POST['test_login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        testLogin($con, $email, $password);
    }
}

// Debug output - show all users in the database
echo "<h2>All Users in Database (userss table)</h2>";
$query = "SELECT id, first_name, last_name, email, user_type FROM userss LIMIT 10";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>User Type</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_type']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No users found in the database.</p>";
}

echo "</body></html>";
?> 