<?php
require_once 'includes/User.php';
require_once 'includes/Session.php';
require_once 'includes/Mailer.php';
require_once 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

Session::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, email FROM userss WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate verification code (6-digit number)
            $verification_code = sprintf("%06d", mt_rand(0, 999999));
            $code_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store verification code in userss table
            $stmt = $pdo->prepare("UPDATE userss SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
            $stmt->execute([$verification_code, $code_expires, $user['id']]);
            
            // Send verification code email
            $mailer = new Mailer();
            $subject = "Password Reset Verification Code";
            $body = "Hello,\n\nYou have requested to reset your password. Use the verification code below to reset your password:\n\n"
                  . "Verification Code: $verification_code\n\n"
                  . "This code will expire in 1 hour.\n\n"
                  . "If you didn't request this, please ignore this email.";
            
            $mailer->sendEmail($user['email'], $subject, $body);
            
            // Store email in session for the reset page
            $_SESSION['reset_email'] = $user['email'];
            
            // Redirect to verification code entry page
            Session::setFlash('success', 'Verification code has been sent to your email.');
            header('Location: reset-password.php');
            exit();
        } else {
            // Show error if email not found
            Session::setFlash('error', 'No account found with that email address.');
            header('Location: forgot-password.php');
            exit();
        }
        
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Password reset error: " . $e->getMessage());
        Session::setFlash('error', 'An error occurred while processing your request. Please try again later.');
        header('Location: forgot-password.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Casa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/casa.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 450px;
            margin: 1rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: #333;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #555;
            margin-bottom: 0;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px;
            border-radius: 8px;
        }

        .form-control:focus {
            box-shadow: none;
            background: rgba(255, 255, 255, 0.95);
        }

        .btn-auth {
            background-color: #d4af37;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            background-color: #c4a030;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-footer a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            color: #c4a030;
        }

        .alert {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            margin-bottom: 1rem;
            position: relative;
            padding: 1rem;
            border-radius: 8px;
        }

        .alert-success {
            color: #0f5132;
            background-color: rgba(209, 231, 221, 0.95);
        }

        .alert-danger {
            color: #842029;
            background-color: rgba(248, 215, 218, 0.95);
        }

        .alert .btn-close {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Forgot Password</h1>
            <p>Enter your email to reset your password</p>
        </div>

        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?php echo Session::getFlash('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo Session::getFlash('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="forgot-password.php" method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Email Address" required>
                <label for="email">Email Address</label>
            </div>

            <button type="submit" class="btn btn-auth">
                Send Reset Link
            </button>
        </form>

        <div class="auth-footer">
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 