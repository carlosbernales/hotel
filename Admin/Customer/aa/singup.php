<?php
session_start();
require 'db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fname = $_POST['firstname'] ?? '';
    $lname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $address = $_POST['address'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    try {
        if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($username) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
            exit;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match!']);
            exit;
        }

        if (strlen($password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long!']);
            exit;
        }

        // Check if username or email already exists
        $checkSql = 'SELECT COUNT(*) FROM users WHERE username = :username OR email = :email';
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username or Email already exists!']);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
        $sql = 'INSERT INTO users (firstname, lastname, email, phone, username, address, password)
                VALUES (:fname, :lname, :email, :phone, :username, :address, :password)';
        $stmt = $pdo->prepare($sql);
        $data = [
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'phone' => $phone,
            'username' => $username,
            'address' => $address,
            'password' => $hashedPassword
        ];
        $stmt->execute($data);

        echo json_encode(['status' => 'success', 'message' => 'Account successfully created!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}

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
