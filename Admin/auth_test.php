<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection and AuthManager
require_once 'Customer/aa/db_con.php'; // This contains the $con connection variable
require_once 'auth/AuthManager.php';

echo "<h1>Authentication Manager Test</h1>";

// Initialize AuthManager
$authManager = new AuthManager($con);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get login credentials
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Test login
    echo "<h2>Login Test for: " . htmlspecialchars($email) . "</h2>";
    
    // Test validateLogin method
    $user = $authManager->validateLogin($email, $password);
    if ($user) {
        echo "<p style='color: green;'>✓ Authentication successful!</p>";
        echo "<pre>";
        // Display user details but hide password
        $userCopy = $user;
        $userCopy['password'] = '[HIDDEN]';
        $userCopy['actual_password'] = '[HIDDEN]';
        print_r($userCopy);
        echo "</pre>";
        
        echo "<h3>Redirection Test</h3>";
        echo "<p>The user would be redirected to: ";
        
        switch($user['user_type']) {
            case 'admin':
                echo "<strong>../admin/index.php?dashboard</strong>";
                break;
            case 'frontdesk':
                echo "<strong>../Frontdesk/index.php?dashboard</strong>";
                break;
            case 'cashier':
                echo "<strong>../Cashier/index.php?POS</strong>";
                break;
            case 'customer':
                echo "<strong>../Customer/aa/index.php</strong>";
                break;
            default:
                echo "<strong>../login.php</strong>";
        }
        echo "</p>";
    } else {
        echo "<p style='color: red;'>✗ Authentication failed</p>";
        
        // Try to find the user to diagnose the issue
        $stmt = $con->prepare("SELECT * FROM userss WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "<p>User with email <strong>" . htmlspecialchars($email) . "</strong> not found in database.</p>";
        } else {
            $user = $result->fetch_assoc();
            echo "<p>User found in database, but password did not match.</p>";
            echo "<p>User type: " . htmlspecialchars($user['user_type']) . "</p>";
            
            echo "<p>Stored passwords (for reference):</p>";
            echo "<ul>";
            echo "<li>password: " . (!empty($user['password']) ? '(present)' : '(empty or null)') . "</li>";
            echo "<li>actual_password: " . (!empty($user['actual_password']) ? '(present)' : '(empty or null)') . "</li>";
            echo "</ul>";
            
            // Compare passwords (careful not to expose them)
            if ($password === $user['password']) {
                echo "<p style='color: green;'>✓ Your password matches the 'password' field</p>";
            } else {
                echo "<p style='color: red;'>✗ Your password does not match the 'password' field</p>";
            }
            
            if ($password === $user['actual_password']) {
                echo "<p style='color: green;'>✓ Your password matches the 'actual_password' field</p>";
            } else {
                echo "<p style='color: red;'>✗ Your password does not match the 'actual_password' field</p>";
            }
        }
    }
}

// Show some example users from the database
echo "<h2>Sample Users from Database</h2>";
$query = "SELECT id, first_name, last_name, email, user_type FROM userss LIMIT 5";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<p>These users exist in the database:</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_type']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Login test form
echo "<h2>Test Login</h2>";
echo "<form method='post'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='email'>Email: </label>";
echo "<input type='email' name='email' required>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='password'>Password: </label>";
echo "<input type='password' name='password' required>";
echo "</div>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";

echo "<br><p><a href='Customer/aa/login.php'>Go to Login Page</a></p>";
?> 