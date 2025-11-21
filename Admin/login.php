<?php
require_once 'db.php';
require_once 'auth/AuthManager.php';

$authManager = new AuthManager($con);

// Check if already logged in
if ($authManager->isLoggedIn()) {
    $authManager->redirectBasedOnRole();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $user = $authManager->validateLogin($username, $password);
        if ($user) {
            $authManager->startSession($user);
            $authManager->redirectBasedOnRole();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Casa Estela - Login</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        height: 100vh;
        background: url('Customer/aa/images/casa.jpg') no-repeat center center fixed;
        background-size: cover;
        position: relative;
        transition: background-color 0.3s, color 5.3s;
    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5);
        z-index: 1;
    }

    body.dark-mode {
        background-color: #121212;
    }

    body.dark-mode::before {
        background: rgba(0, 0, 0, 0.6);
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        position: relative;
        z-index: 2;
    }

    .card-container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 15px;
        border: 2px solid #ffd700;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        text-align: center;
        width: 400px;
    }

    body.dark-mode .card-container {
        background-color: rgba(33, 33, 33, 0.9);
        border: 2px solid #b8860b;
    }

    #profile-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin-bottom: 20px;
        border: 3px solid #ffd700;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        margin-bottom: 15px;
        background-color: rgba(255, 255, 255, 0.9);
    }

    body.dark-mode .form-control {
        background-color: #333;
        color: white;
        border: 1px solid #444;
    }

    label {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        text-align: left;
        display: block;
        margin-bottom: 8px;
    }

    body.dark-mode label {
        color: #fff;
    }

    .btn-primary {
        background-color: #ffd700;
        border: none;
        color: #000;
        font-weight: bold;
        border-radius: 8px;
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #b8860b;
        color: #fff;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 12px;
    }

    .register-link {
        margin-top: 20px;
        color: #666;
    }

    body.dark-mode .register-link {
        color: #ccc;
    }

    .register-link a {
        color: #b8860b;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        color: #ffd700;
    }

    #dark-mode-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 8px 15px;
        cursor: pointer;
        font-size: 14px;
        z-index: 3;
    }

    #dark-mode-toggle:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }
    </style>
</head>
<body>
    <button id="dark-mode-toggle">
        <i class="fas fa-moon"></i> Dark Mode
    </button>

    <div class="container">
        <div class="card-container">
            <!-- Logo Section -->
            <?php if (file_exists('Customer/aa/images/logo.png')): ?>
                <img id="profile-img" src="Customer/aa/images/logo.png" alt="Casa Estela Logo"/>
            <?php else: ?>
                <div id="text-logo" style="
                    font-family: 'Playfair Display', serif;
                    font-size: 2.5rem;
                    color: #b8860b;
                    margin-bottom: 20px;
                    font-weight: 700;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                    Casa Estela
                </div>
            <?php endif; ?>
            <h3 class="mb-3">Welcome Back</h3>
            <p class="text-muted mb-4">Please sign in to continue</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <div class="register-link">
                <p>Don't have an account? <a href="Customer/aa/signup.php">Register here</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('dark-mode-toggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>
