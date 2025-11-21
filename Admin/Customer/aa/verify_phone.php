<?php
session_start();
require_once 'includes/Session.php';
require_once 'db_con.php';
require_once '../../auth/AuthManager.php';

// Debug log
error_log("Session data: " . print_r($_SESSION, true));

// Redirect if no verification in progress
if (!isset($_SESSION['phone_verification'])) {
    error_log("No phone verification session found. Redirecting to signup.php");
    header('Location: signup.php');
    exit();
}

$error = '';
$success = '';
$phone = $_SESSION['phone_verification']['phone'] ?? '';

// Debug log
error_log("Phone number from session: " . $phone);

// Handle verification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['verification_code'] ?? '';
    $verification = $_SESSION['phone_verification'];

    error_log("Entered code: " . $entered_code);
    error_log("Expected code: " . $verification['code']);

    // Validate verification state
    if (time() > $verification['expires']) {
        $error = "Verification code has expired. Please request a new one.";
    } elseif ($verification['attempts'] >= 3) {
        $error = "Too many attempts. Please request a new code.";
    } elseif ($entered_code === (string)$verification['code']) {
        try {
            // Get user data from session storage
            $userData = json_decode($_COOKIE['pending_signup_data'] ?? '', true);
            
            if (!$userData) {
                throw new Exception("Registration data not found. Please try signing up again.");
            }

            // Initialize AuthManager
            $authManager = new AuthManager($con);

            // Register the user
            if ($authManager->register(
                $userData['firstname'],
                $userData['lastname'],
                $userData['phone'],
                $userData['email'],
                $userData['password']
            )) {
                $success = "Phone number verified and account created successfully!";
                // Clear verification and temporary data
                unset($_SESSION['phone_verification']);
                setcookie('pending_signup_data', '', time() - 3600, '/'); // Delete the cookie
            } else {
                throw new Exception("Failed to create account. Please try again.");
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = $e->getMessage();
        }
    } else {
        $_SESSION['phone_verification']['attempts']++;
        $error = "Invalid verification code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Verification - E Akomoda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: url('images/casa.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .verification-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .verification-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .verification-header i {
            font-size: 48px;
            color: #d4af37;
            margin-bottom: 20px;
            display: block;
        }

        .verification-header h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: 600;
        }

        .verification-header p {
            color: #666;
            font-size: 16px;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .code-input-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 30px 0;
            padding: 0 20px;
        }

        .code-input {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #ddd;
            border-radius: 12px;
            background: white;
            transition: all 0.3s ease;
        }

        .code-input:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            outline: none;
        }

        .verify-btn {
            background: #d4af37;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            margin-top: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .verify-btn:hover {
            background: #c19a32;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .timer {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .timer strong {
            color: #333;
            font-weight: 600;
        }

        .resend-link {
            color: #d4af37;
            text-decoration: none;
            font-weight: 500;
            display: block;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .resend-link:not(.disabled):hover {
            color: #c19a32;
            text-decoration: underline;
        }

        .resend-link.disabled {
            color: #999;
            pointer-events: none;
        }

        .alert {
            margin: 20px auto;
            max-width: 100%;
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 15px;
        }

        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }

        @media (max-width: 480px) {
            .verification-container {
                padding: 30px 20px;
            }

            .code-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }

            .code-input-group {
                gap: 8px;
                padding: 0 10px;
            }

            .verification-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <div class="verification-header">
                <i class="fas fa-mobile-alt"></i>
                <h2>Phone Verification</h2>
                <?php if (isset($_SESSION['phone_verification']['phone'])): ?>
                    <p>Enter the 6-digit code sent to <strong><?php echo htmlspecialchars($_SESSION['phone_verification']['phone']); ?></strong></p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success; ?>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-primary">Proceed to Login</a>
                </div>
            <?php else: ?>
                <form method="POST" id="verificationForm">
                    <div class="code-input-group">
                        <?php for($i = 1; $i <= 6; $i++): ?>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        <?php endfor; ?>
                        <input type="hidden" name="verification_code" id="verificationCode">
                    </div>
                    <button type="submit" class="verify-btn">
                        <span class="button-text">Verify Code</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
                <div class="timer">
                    Time remaining: <strong><span id="timer">2:00</span></strong>
                </div>
                <a href="#" class="resend-link disabled" id="resendLink">Resend Code</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to store user data in a cookie
        function storeUserData(data) {
            const jsonData = JSON.stringify(data);
            // Set cookie to expire in 1 hour
            document.cookie = `pending_signup_data=${jsonData};path=/;max-age=3600`;
        }

        // Get user data from sessionStorage and store in cookie when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const userData = sessionStorage.getItem('pending_signup_data');
            if (userData) {
                storeUserData(JSON.parse(userData));
                // Clear from sessionStorage since we've moved it to cookie
                sessionStorage.removeItem('pending_signup_data');
            }
        });

        // Handle verification code input
        const inputs = document.querySelectorAll('.code-input');
        const form = document.getElementById('verificationForm');
        const verificationCodeInput = document.getElementById('verificationCode');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Combine all inputs into one code
            const code = Array.from(inputs).map(input => input.value).join('');
            verificationCodeInput.value = code;

            // Show loading state
            const button = form.querySelector('.verify-btn');
            const buttonText = button.querySelector('.button-text');
            const spinner = button.querySelector('.spinner-border');
            
            buttonText.style.opacity = '0';
            spinner.classList.remove('d-none');
            button.disabled = true;

            // Submit the form
            form.submit();
        });

        // Timer functionality
        let timeLeft = 120; // 2 minutes in seconds
        const timerDisplay = document.getElementById('timer');
        const resendLink = document.getElementById('resendLink');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft === 0) {
                resendLink.classList.remove('disabled');
                return;
            }
            
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }

        updateTimer();

        // Handle resend code
        resendLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (!this.classList.contains('disabled')) {
                // Show loading toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Sending new verification code...',
                    showConfirmButton: false,
                    timer: 3000
                });

                // Reset timer
                timeLeft = 120;
                this.classList.add('disabled');
                updateTimer();

                // Get phone number from session
                const userData = JSON.parse(document.cookie.split('; ')
                    .find(row => row.startsWith('pending_signup_data='))
                    ?.split('=')[1] || '{}');

                // Send request for new code
                fetch('send_phone_verification.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        phone: userData.phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'New verification code sent!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        throw new Error(data.message || 'Failed to send verification code');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: error.message || 'Failed to send verification code',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
            }
        });
    </script>
</body>
</html>