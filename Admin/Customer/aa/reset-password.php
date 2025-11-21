<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/User.php';
require_once 'includes/Session.php';
require_once 'includes/Mailer.php';
require_once 'db_con.php';

Session::start();

// Check if user is coming from forgot password
if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit();
}

$email = $_SESSION['reset_email'];

// Handle resend code request
if (isset($_POST['resend_code'])) {
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, email FROM userss WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate new verification code (6-digit number)
            $verification_code = sprintf("%06d", mt_rand(0, 999999));
            $code_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store verification code in userss table
            $stmt = $pdo->prepare("UPDATE userss SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
            $stmt->execute([$verification_code, $code_expires, $user['id']]);
            
            // Send verification code email with HTML template
            $mailer = new Mailer();
            $subject = "Password Reset Verification Code";
            
            // Get the logo and encode it
            $logoPath = __DIR__ . '/images/estela.jpg';
            $logoData = '';
            if (file_exists($logoPath)) {
                $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
            } else {
                // Fallback if logo file doesn't exist
                $logoBase64 = '';
            }
            
            // Create HTML email body
            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Password Reset</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333333;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                    }
                    .header {
                        background-color: #d4af37;
                        padding: 20px;
                        text-align: center;
                    }
                    .header img {
                        max-height: 70px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #ffffff;
                    }
                    .code-container {
                        background-color: #f5f5f5;
                        border-radius: 5px;
                        padding: 15px;
                        margin: 20px 0;
                        text-align: center;
                    }
                    .verification-code {
                        font-size: 32px;
                        font-weight: bold;
                        color: #d4af37;
                        letter-spacing: 5px;
                    }
                    .footer {
                        text-align: center;
                        padding: 20px;
                        font-size: 12px;
                        color: #777777;
                    }
                    .button {
                        display: inline-block;
                        background-color: #d4af37;
                        color: #ffffff;
                        text-decoration: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                    .expiry {
                        color: #ff0000;
                        font-weight: bold;
                    }
                    .header h1 {
                        color: #ffffff;
                        margin: 0;
                        font-size: 28px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <img src="' . $logoBase64 . '" alt="Casa Estela Botique Hotel and Cafe Logo">
                    </div>
                    <div class="content">
                        <h2>Password Reset Request</h2>
                        <p>Hello,</p>
                        <p>We received a request to reset your password for your Casa Estela Botique Hotel and Cafe account. Please use the verification code below to complete the password reset process:</p>
                        
                        <div class="code-container">
                            <div class="verification-code">' . $verification_code . '</div>
                        </div>
                        
                        <p><span class="expiry">This code will expire in 1 hour.</span></p>
                        
                        <p>If you did not request a password reset, please ignore this email or contact our support team if you have concerns.</p>
                        
                        <p>Thank you,<br>The Casa Estela Botique Hotel and Cafe</p>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' Casa Cafe. All rights reserved.</p>
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            // Plain text alternative for email clients that don't support HTML
            $textBody = "Hello,\n\n" .
                      "You have requested to reset your password. Use the verification code below to reset your password:\n\n" .
                      "Verification Code: $verification_code\n\n" .
                      "This code will expire in 1 hour.\n\n" .
                      "If you didn't request this, please ignore this email or contact our support team.\n\n" .
                      "Thank you,\nThe Casa Cafe Team";
            
            $mailer->sendHtmlMail($user['email'], $subject, $htmlBody, $textBody);
            
            Session::setFlash('success', 'A new verification code has been sent to your email.');
            
            // Redirect to prevent form resubmission
            header('Location: reset-password.php');
            exit();
        } else {
            Session::setFlash('error', 'No account found with that email address.');
        }
    } catch (Exception $e) {
        error_log("Resend code error: " . $e->getMessage());
        Session::setFlash('error', 'An error occurred while sending a new code. Please try again.');
    }
}

// Handle verification code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $verification_code = $_POST['verification_code'];
    
    try {
        // Add debugging to see what's happening
        error_log("Verifying code: $verification_code for email: $email");
        
        // Verify the code - modified query to check more precisely
        $stmt = $pdo->prepare("SELECT id, reset_token, reset_token_expires FROM userss WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            error_log("User found. Stored token: {$user['reset_token']}, Expires: {$user['reset_token_expires']}");
            
            // Check if token matches and is not expired
            $now = date('Y-m-d H:i:s');
            if ($user['reset_token'] === $verification_code && $user['reset_token_expires'] > $now) {
                // Code is valid, show password reset form
                $_SESSION['verified_user_id'] = $user['id'];
                $_SESSION['show_password_form'] = true;
                Session::setFlash('success', 'Code verified successfully. Please set your new password.');
            } else {
                if ($user['reset_token'] !== $verification_code) {
                    error_log("Code mismatch. Entered: $verification_code, Stored: {$user['reset_token']}");
                    Session::setFlash('error', 'Invalid verification code.');
                } else {
                    error_log("Code expired. Expires: {$user['reset_token_expires']}, Now: $now");
                    Session::setFlash('error', 'Verification code has expired. Please request a new one.');
                }
            }
        } else {
            error_log("No user found with email: $email");
            Session::setFlash('error', 'No account found with that email address.');
        }
        
        // Redirect to prevent form resubmission
        header('Location: reset-password.php');
        exit();
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Verification code error: " . $e->getMessage());
        Session::setFlash('error', 'An error occurred. Please try again.');
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_SESSION['verified_user_id'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['verified_user_id'];
    
    if ($password !== $confirm_password) {
        Session::setFlash('error', 'Passwords do not match.');
    } else if (strlen($password) < 8) {
        Session::setFlash('error', 'Password must be at least 8 characters long.');
    } else {
        try {
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE userss SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            
            // Clear session variables
            unset($_SESSION['reset_email']);
            unset($_SESSION['verified_user_id']);
            unset($_SESSION['show_password_form']);
            
            Session::setFlash('success', 'Your password has been reset successfully. You can now login with your new password.');
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Password update error: " . $e->getMessage());
            Session::setFlash('error', 'An error occurred while resetting your password. Please try again.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Casa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            background: rgba(255, 255, 255, 0.75); /* Increased opacity for better readability */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 450px;
            margin: 1rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: #222; /* Darker text for better contrast */
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            font-weight: 600; /* Slightly bolder */
        }

        .auth-header p {
            color: #333; /* Darker text for better contrast */
            margin-bottom: 0;
            font-weight: 500; /* Medium weight for better readability */
        }

        .form-control {
            background: rgba(255, 255, 255, 0.95); /* More opaque background */
            border: 1px solid #ddd; /* Light border for definition */
            padding: 12px;
            border-radius: 8px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.25); /* Gold-tinted focus ring */
            background: #fff;
            border-color: #d4af37;
        }

        .btn-auth {
            background-color: #d4af37;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600; /* Bolder text */
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            background-color: #c4a030;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-footer a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600; /* Bolder for better visibility */
        }

        .auth-footer a:hover {
            color: #c4a030;
            text-decoration: underline; /* Add underline on hover */
        }

        .alert {
            background: rgba(255, 255, 255, 0.95); /* More opaque background */
            border: none;
            margin-bottom: 1rem;
            position: relative;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 500; /* Medium weight for better readability */
        }

        .alert-success {
            color: #0f5132;
            background-color: rgba(209, 231, 221, 0.95);
            border-left: 4px solid #0f5132; /* Left border for emphasis */
        }

        .alert-danger {
            color: #842029;
            background-color: rgba(248, 215, 218, 0.95);
            border-left: 4px solid #842029; /* Left border for emphasis */
        }

        .alert .btn-close {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.5rem;
        }
        
        .resend-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            background-color: rgba(248, 249, 250, 0.8); /* Light background */
            padding: 10px 15px;
            border-radius: 8px;
        }
        
        .resend-text {
            font-size: 0.9rem;
            color: #333; /* Darker text for better contrast */
            font-weight: 500; /* Medium weight for better readability */
        }

        /* Add a subtle overlay to ensure text readability regardless of background image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3); /* Dark overlay */
            z-index: 1;
        }

        /* Ensure form labels are readable */
        .form-floating label {
            color: #555;
            font-weight: 500;
        }

        /* Add these new styles for password visibility toggle */
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #6c757d;
            background: none;
            border: none;
            padding: 0;
            font-size: 1.2rem;
        }
        
        .password-toggle:hover {
            color: #d4af37;
        }
        
        .form-floating .password-toggle {
            top: 32px; /* Adjust for floating label */
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Reset Password</h1>
            <?php if (!isset($_SESSION['show_password_form'])): ?>
                <p>Enter the verification code sent to your email</p>
            <?php else: ?>
                <p>Create a new password</p>
            <?php endif; ?>
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

        <?php if (!isset($_SESSION['show_password_form'])): ?>
            <!-- Verification Code Form -->
            <form action="reset-password.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="verification_code" name="verification_code" 
                           placeholder="Verification Code" required>
                    <label for="verification_code">Verification Code</label>
                </div>

                <button type="submit" class="btn btn-auth">
                    Verify Code
                </button>
            </form>
            
            <!-- Separate form for resend code functionality -->
            <div class="resend-container">
                <span class="resend-text">Didn't receive the code?</span>
                <form action="reset-password.php" method="POST">
                    <button type="submit" name="resend_code" class="btn btn-secondary">
                        Resend Code
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Password Reset Form with eye icons -->
            <form action="reset-password.php" method="POST">
                <div class="form-floating mb-3 password-container">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="New Password" required>
                    <label for="password">New Password</label>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                        <i class="bi bi-eye-slash" id="password-icon"></i>
                    </button>
                </div>

                <div class="form-floating mb-3 password-container">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Confirm New Password" required>
                    <label for="confirm_password">Confirm New Password</label>
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                        <i class="bi bi-eye-slash" id="confirm_password-icon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-auth">
                    Reset Password
                </button>
            </form>
        <?php endif; ?>

        <div class="auth-footer">
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add JavaScript for password visibility toggle -->
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    </script>
</body>
</html> 