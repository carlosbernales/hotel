<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'db.php';

echo "<h1>Login Diagnostics Tool</h1>";

// Test database connection
echo "<h2>1. Testing Database Connection</h2>";
if ($con) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    exit("Please fix database connection before proceeding");
}

// Check if userss table exists
echo "<h2>2. Checking 'userss' Table</h2>";
$tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'userss'");
if (mysqli_num_rows($tableCheck) > 0) {
    echo "<p style='color: green;'>✓ Table 'userss' exists</p>";
} else {
    echo "<p style='color: red;'>✗ Table 'userss' does not exist</p>";
    exit("The 'userss' table is missing from the database");
}

// Check required columns
echo "<h2>3. Checking Required Columns</h2>";
$requiredColumns = ['id', 'email', 'password', 'actual_password', 'user_type', 'first_name', 'last_name'];
$columnsQuery = mysqli_query($con, "SHOW COLUMNS FROM userss");
$existingColumns = [];

while ($column = mysqli_fetch_assoc($columnsQuery)) {
    $existingColumns[] = $column['Field'];
}

foreach ($requiredColumns as $column) {
    if (in_array($column, $existingColumns)) {
        echo "<p style='color: green;'>✓ Column '$column' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Column '$column' is missing</p>";
    }
}

// Test a sample login
echo "<h2>4. Test Login Functionality</h2>";
echo "<form method='post'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='email'>Email: </label>";
echo "<input type='email' name='email' required>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='password'>Password: </label>";
echo "<input type='password' name='password' required>";
echo "</div>";
echo "<button type='submit' name='test_login'>Test Login</button>";
echo "</form>";

if (isset($_POST['test_login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Login Test Results:</h3>";
    
    // Check if the email exists
    $stmt = $con->prepare("SELECT * FROM userss WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<p style='color: red;'>Email not found in database</p>";
    } else {
        echo "<p style='color: green;'>Email found in database</p>";
        
        $user = $result->fetch_assoc();
        echo "<p>User type: " . htmlspecialchars($user['user_type']) . "</p>";
        
        // Don't display actual passwords, just check if they match
        if ($password === $user['password']) {
            echo "<p style='color: green;'>✓ Password matches 'password' field</p>";
        } else {
            echo "<p style='color: red;'>✗ Password does not match 'password' field</p>";
        }
        
        if ($password === $user['actual_password']) {
            echo "<p style='color: green;'>✓ Password matches 'actual_password' field</p>";
        } else {
            echo "<p style='color: red;'>✗ Password does not match 'actual_password' field</p>";
        }
        
        // Overall result
        if ($password === $user['password'] || $password === $user['actual_password']) {
            echo "<p style='color: green;'>✓ Login would be successful with current logic</p>";
        } else {
            echo "<p style='color: red;'>✗ Login would fail with current logic</p>";
        }
    }
}

echo "<br><br><a href='Customer/aa/login.php'>Return to Login Page</a>";
?> 