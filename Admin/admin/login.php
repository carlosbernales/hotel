<?php
require_once '../auth/AuthManager.php';
require_once '../includes/db_connection.php';

$authManager = new AuthManager($conn);

// Check if already logged in
if ($authManager->isLoggedIn()) {
    $authManager->redirectBasedOnRole();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!$authManager->validateCSRF($csrf_token)) {
        $error = "Invalid request";
    } else if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $user = $authManager->validateLogin($username, $password);
        if ($user && ($user['user_type'] === 'admin' || $user['user_type'] === 'cashier')) {
            $authManager->startSession($user);
            $authManager->redirectBasedOnRole();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Casa Estela</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 90%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header img {
            max-width: 150px;
            margin-bottom: 1rem;
        }
        .btn-gold {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            border: none;
            color: white;
            font-weight: bold;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            color: white;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="assets/images/logo.png" alt="Casa Estela Logo">
            <h4>Admin Login</h4>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div class="form-group mb-3">
                <label for="username">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-gold w-100">Login</button>
        </form>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 