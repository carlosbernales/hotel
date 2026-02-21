<?php
// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_con.php';
require_once '../../auth/AuthManager.php';
require_once 'includes/User.php';
require_once 'includes/Session.php';

// Redirect if not in verification process
if (!isset($_SESSION['verification'])) {
    header("Location: signup.php");
    exit();
}

$authManager = new AuthManager($con);
$error = '';
$success = '';

// Handle verification code submission
if (isset($_POST['verify_email'])) {
    $entered_code = trim($_POST['verification_code'] ?? '');
    
    if (isset($_SESSION['verification'])) {
        // Debug logging
        error_log("Entered code: " . $entered_code);
        error_log("Stored code: " . $_SESSION['verification']['email_code']);
        
        if (time() > $_SESSION['verification']['expires']) {
            $error = "Verification code has expired. Please request a new one.";
        } elseif ($entered_code === (string)$_SESSION['verification']['email_code']) {
            // Code is correct, proceed with registration
            $userData = $_SESSION['verification']['formData'];
            
            try {
                if ($authManager->register(
                    $userData['firstname'],
                    $userData['lastname'],
                    $userData['phone'],
                    $userData['email'],
                    $userData['password']
                )) {
                    // Registration successful
                    $success = 'Account created successfully! You can now login.';
                    
                    // Clear verification data
                    unset($_SESSION['verification']);
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                error_log("Registration error: " . $e->getMessage());
            }
        } else {
            $error = "Invalid verification code. Please try again.";
            error_log("Code mismatch - Entered: $entered_code, Expected: " . $_SESSION['verification']['email_code']);
        }
    } else {
        $error = "Verification session expired. Please sign up again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - E Akomoda</title>
    <!-- Include your CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: url('images/casa.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 2;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: #d4af37;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #666;
        }

        .form-control {
            padding: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
            letter-spacing: 0.5rem;
        }

        .btn-primary {
            background-color: #d4af37;
            border-color: #d4af37;
            padding: 0.75rem;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background-color: #c19a32;
            border-color: #c19a32;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Email Verification</h1>
                <p>Enter the code sent to <?php echo htmlspecialchars($_SESSION['verification']['email'] ?? ''); ?></p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h4>Verification Successful!</h4>
                        <p><?php echo $success; ?></p>
                        <div class="mt-3">
                            <a href="login.php" class="btn btn-primary w-100">Proceed to Login</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="verify.php" method="POST">
                        <div class="form-group mb-4">
                            <label class="form-label" for="verification_code">Verification Code</label>
                            <input type="text" class="form-control" id="verification_code" 
                                   name="verification_code" required maxlength="6" 
                                   placeholder="000000">
                            <small class="form-text text-muted">
                                Please check your email for the verification code. 
                                The code will expire in 2 minutes.
                            </small>
                        </div>
                        <button type="submit" name="verify_email" class="btn btn-primary w-100">
                            Verify Email
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('verification_code').addEventListener('input', function(e) {
        // Remove any non-digit characters
        this.value = this.value.replace(/\D/g, '');
        
        // Limit to 6 digits
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });
    </script>
</body>
</html>
