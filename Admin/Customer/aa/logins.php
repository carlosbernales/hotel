<?php
session_start();
require 'db_con.php'; // Ensure your db_con.php file is properly included

if (isset($_POST['loginUser'])) {
    $inputUsername = trim($_POST['username']);
    $inputPassword = trim($_POST['password']);
    $loginError = ''; // Initialize the error message variable

    try {
        // Check if the fields are empty
        if (empty($inputUsername) || empty($inputPassword)) {
            $loginError = 'Please enter both username and password.';
        } else {
            // Prepare the SQL query to get the user data based on username
            $sql = 'SELECT * FROM users WHERE username = :username';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $inputUsername]);
            $user = $stmt->fetch();

            // If user is found
            if ($user) {
                // Verify the password
                if (password_verify($inputPassword, $user['password'])) {
                    // Store user information in session variables
                    $_SESSION['userid'] = $user['userid'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_logged_in'] = true;

                    // Redirect to home.php after successful login
                    header('Location: home.php');
                    exit;
                } else {
                    // Incorrect password
                    $loginError = 'Incorrect password. Please try again.';
                }
            } else {
                // User not found
                $loginError = 'User does not exist.';
            }
        }
    } catch (PDOException $e) {
        // Database error
        $loginError = 'Database error: ' . $e->getMessage();
    }
}
?>
