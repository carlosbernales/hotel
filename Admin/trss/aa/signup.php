<?php
require_once 'db_con.php';
require_once '../../auth/AuthManager.php';
require_once 'includes/User.php';
require_once 'includes/Session.php';
require_once 'includes/SeasonalEffects.php';
require_once 'includes/Mailer.php';

// ITEXTMO API Configuration
define('ITEXTMO_API_CODE', 'TR-KYUTI610566_AAUL2'); // Replace with your actual API code
define('ITEXTMO_API_PASSWORD', 'C12345678'); // Replace with your actual API password

function sendSMSVerification($phone, $code) {
    if (empty($phone) || empty($code)) {
        error_log("SMS Error: Empty phone or code");
        return ['success' => false, 'message' => 'Phone number and code are required'];
    }

    // Format phone number (remove any spaces or special characters)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    error_log("Sending SMS to phone: " . $phone);
    
    // Validate phone number format (must be 11 digits starting with 09)
    if (!preg_match('/^09\d{9}$/', $phone)) {
        error_log("SMS Error: Invalid phone format: " . $phone);
        return ['success' => false, 'message' => 'Invalid phone number format'];
    }

    $ch = curl_init();
    $itextmo_url = 'https://www.itextmo.com/php_api/api.php';
    $itextmo_data = array(
        '1' => $phone,
        '2' => "Your E-Akomoda verification code is: $code. Valid for 15 minutes.",
        '3' => ITEXTMO_API_CODE,
        'passwd' => ITEXTMO_API_PASSWORD
    );
    
    error_log("ITEXTMO Request Data: " . json_encode($itextmo_data));
    
    curl_setopt($ch, CURLOPT_URL, $itextmo_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($itextmo_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("ITEXTMO Response: " . $response);
    error_log("ITEXTMO HTTP Code: " . $http_code);
    if ($curl_error) {
        error_log("CURL Error: " . $curl_error);
    }
    
    if ($curl_error) {
        error_log("SMS Error: CURL failed - " . $curl_error);
        return ['success' => false, 'message' => 'SMS sending failed: ' . $curl_error];
    }

    // Interpret the ITEXTMO API response
    $result = ['success' => false, 'message' => ''];
    
    switch ($response) {
        case '0':
            $result = ['success' => true, 'message' => 'Message sent successfully'];
            break;
        case '-1':
            $result = ['success' => false, 'message' => 'Invalid API code'];
            break;
        case '-2':
            $result = ['success' => false, 'message' => 'Invalid API password'];
            break;
        case '-3':
            $result = ['success' => false, 'message' => 'Invalid phone number'];
            break;
        case '-4':
            $result = ['success' => false, 'message' => 'Invalid message'];
            break;
        case '-5':
            $result = ['success' => false, 'message' => 'API code and password do not match'];
            break;
        case '-6':
            $result = ['success' => false, 'message' => 'Insufficient credits'];
            break;
        case '-7':
            $result = ['success' => false, 'message' => 'System error'];
            break;
        default:
            $result = ['success' => false, 'message' => 'Unknown error: ' . $response];
    }
    
    error_log("SMS Result: " . json_encode($result));
    return $result;
}

$authManager = new AuthManager($con);

// Redirect if already logged in
if ($authManager->isLoggedIn()) {
    $authManager->redirectBasedOnRole();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Check if email exists
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM userss WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            throw new Exception("This email address is already registered");
        }

        // Check if phone exists
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM userss WHERE contact_number = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            throw new Exception("This phone number is already registered");
        }

        // Validate input
        if (empty($firstname) || empty($lastname) || empty($phone) || empty($email) || empty($password) || empty($confirmPassword)) {
            throw new Exception("All fields are required");
        }

        // Validate phone number format
        if (!preg_match('/^09\d{9}$/', $phone)) {
            throw new Exception("Phone number must be 11 digits and start with 09");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 8 characters long");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match");
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception('Password must contain at least one lowercase letter');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception('Password must contain at least one number');
        }

        // Generate verification codes
        $email_verification_code = sprintf("%06d", mt_rand(0, 999999));
        $phone_verification_code = sprintf("%06d", mt_rand(0, 999999));
        
        // Store verification codes in session
        session_start();
        $_SESSION['verification'] = [
            'email_code' => $email_verification_code,
            'phone_code' => $phone_verification_code,
            'email' => $email,
            'phone' => $phone,
            'expires' => time() + (15 * 60) // Codes expire in 15 minutes
        ];

        // Send SMS verification code
        $sms_result = sendSMSVerification($phone, $phone_verification_code);
        if (!$sms_result['success']) {
            error_log("Failed to send SMS: " . $sms_result['message']);
            throw new Exception("Failed to send verification code via SMS. Please try again.");
        }

        // Create mailer instance
        $mailer = new Mailer();

        // Email template
        $emailBody = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <h2>Welcome to E Akomoda!</h2>
            <p>Thank you for signing up. Please use the following verification code to complete your registration:</p>
            <h3 style='background-color: #f4f4f4; padding: 10px; text-align: center; font-size: 24px; letter-spacing: 5px;'>$email_verification_code</h3>
            <p>This code will expire in 15 minutes.</p>
            <p>If you didn't request this verification, please ignore this email.</p>
            <br>
            <p>Best regards,</p>
            <p>E Akomoda Team</p>
        </body>
        </html>
        ";

        try {
            // Send verification email
            $mailer->sendMail(
                $email,
                "Email Verification - E Akomoda",
                $emailBody
            );

            // Send SMS verification
            $smsResponse = sendSMSVerification($phone, $phone_verification_code);
            if ($smsResponse != "0") {
                throw new Exception("Failed to send SMS verification code");
            }

            // Store user data in session for later use
            $_SESSION['temp_user_data'] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email,
                'password' => $password
            ];
            
            // Redirect to verification page
            header("Location: verify.php");
            exit();

        } catch (Exception $e) {
            throw new Exception("Failed to send verification codes: " . $e->getMessage());
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle verification code submission
if (isset($_POST['verify'])) {
    $entered_email_code = $_POST['email_verification_code'] ?? '';
    $entered_phone_code = $_POST['phone_verification_code'] ?? '';
    
    if (isset($_SESSION['verification'])) {
        if (time() > $_SESSION['verification']['expires']) {
            $error = "Verification codes have expired. Please sign up again.";
        } elseif ($entered_email_code == $_SESSION['verification']['email_code'] && 
                  $entered_phone_code == $_SESSION['verification']['phone_code']) {
            // Codes are correct, proceed with registration
            $userData = $_SESSION['temp_user_data'];
            
            if ($authManager->register(
                $userData['firstname'],
                $userData['lastname'],
                $userData['phone'],
                $userData['email'],
                $userData['password']
            )) {
                $success = 'Account created successfully! You can now login.';
                // Clear verification and temporary data
                unset($_SESSION['verification']);
                unset($_SESSION['temp_user_data']);
                $show_verification_form = false;
            }
        } else {
            $error = "Invalid verification codes. Please try again.";
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
    <title>Sign Up - E Akomoda</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <style>
:root {
    --primary-color: #d4af37;
    --primary-hover: #c19a32;
            --secondary-color: #2c3e50;
            --text-light: #f8f9fa;
            --text-dark: #343a40;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

body {         
    background: url('images/casa.jpg') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            position: relative;
            padding: 0;
    margin: 0;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1rem;
    position: relative;
            z-index: 1;
        }

        .signup-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-top: 60px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .signup-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.9) 0%, rgba(193, 154, 50, 0.9) 100%);
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .signup-header h2 {
            font-family: 'Playfair Display', serif;
    color: white;
    margin: 0;
            font-size: 2.2rem;
    font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .signup-header p {
    color: rgba(255, 255, 255, 0.9);
            margin-top: 0.5rem;
    font-size: 1.1rem;
}

        .signup-body {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 500;
            margin-bottom: 0.5rem;
            color: white;
            display: block;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

.input-group {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        .input-group-text {
            background-color: rgba(248, 249, 250, 0.8);
            border: none;
            color: #6c757d;
        }

        .form-control {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.8);
        }

.form-control:focus {
            box-shadow: none;
    border-color: var(--primary-color);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .password-toggle {
            background: transparent;
            border: none;
            color:rgb(221, 210, 52);
        }

        .password-strength {
            margin-top: 0.75rem;
        }

        .progress {
            height: 6px;
            border-radius: 3px;
            background-color: rgba(233, 236, 239, 0.5);
}

.btn-primary {
            background: var(--primary-color);
    border: none;
    padding: 0.75rem 1.5rem;
            font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
            background: var(--primary-hover);
    transform: translateY(-2px);
            box-shadow: 0 6px 12px #ffd700;
        }

        .form-check-label {
            color: white;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .form-check-label a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 500;
        }

        .form-check-label a:hover {
            text-decoration: underline;
            color: #ffe44d;
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-footer p {
            margin-bottom: 0;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.auth-footer a {
            color: #ffd700;
            font-weight: 500;
    text-decoration: none;
}

.auth-footer a:hover {
            text-decoration: underline;
            color: #ffe44d;
        }

        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .alert-danger {
            background-color: rgba(248, 215, 218, 0.9);
            color: #721c24;
        }

        .alert-success {
            background-color: rgba(212, 237, 218, 0.9);
            color: #155724;
        }

        .alert-info {
            background-color: rgba(209, 236, 241, 0.9);
            color: #0c5460;
        }

        .form-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Modal styling */
        .modal-content {
    border-radius: var(--border-radius);
    border: none;
            box-shadow: var(--box-shadow);
}

        .modal-header {
    background: var(--primary-color);
    color: white;
    border-bottom: none;
            padding: 1.5rem;
}

        .modal-body {
    padding: 2rem;
        }

        .modal-footer {
            border-top: none;
        padding: 1.5rem;
    }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .signup-header {
                padding: 1.5rem;
            }

            .signup-header h2 {
                font-size: 1.8rem;
            }

            .signup-body {
                padding: 1.5rem;
            }
        }

        /* Verification code styling */
#verification_code {
    letter-spacing: 8px;
    font-size: 1.2rem;
    text-align: center;
            font-weight: 600;
        }

        .verification-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Fix for password strength text */
        #passwordStrength .form-text {
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Fix for checkbox */
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('nav.php'); ?>

    <div class="page-container">
        <div class="signup-container">
            <div class="signup-header">
                <h2>Create Account</h2>
                <p>Join E-Akomoda today</p>
            </div>
            <div class="signup-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <div class="mt-3">
                            <a href="../../login.php" class="btn btn-primary w-100">Proceed to Login</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="signup.php" method="POST" id="signupForm">
                        <div class="row">
                            <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="firstname">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="firstname" name="firstname" required 
                                    placeholder="Enter your first name"
                                    value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>">
                            </div>
                        </div>
                            </div>
                            <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="lastname">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="lastname" name="lastname" required 
                                    placeholder="Enter your last name"
                                    value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>">
                            </div>
                        </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control" id="phone" name="phone" required pattern="^09\d{9}$"
                                    placeholder="09XXXXXXXXX"
                                    title="Phone number must be 11 digits and start with 09"
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-text">Format: 09XXXXXXXXX (11 digits)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email address"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter your password">
                                <button class="btn password-toggle" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-2" id="passwordStrength">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="form-text">Password strength: <span id="strengthText">Very Weak</span></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                    placeholder="Confirm your password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" name="signup" class="btn btn-primary w-100" id="signupBtn">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                <?php endif; ?>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms And Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <h5>Casa Estela Boutique Hotel & Café</h5>
                <p>Welcome to Casa Estela Boutique Hotel & Café's website. By using our website, you agree to these Terms and Conditions. Please review them carefully:</p>
                <ul>
                    <li>You agree to use the website for personal, non-commercial purposes only.</li>
                    <li>All information and content on this website are provided for general informational purposes only.</li>
                    <li>Reservations for rooms and café services must be made through our booking system.</li>
                    <li>Guests are required to provide accurate information during the booking process.</li>
                    <li>Cancellations can be made at any time; however, no refunds will be issued for canceled bookings.</li>
                    <li>Refunds are not applicable under any circumstances for cancellations.</li>
                    <li>Guests are expected to follow Casa Estela's house rules during their stay, which will be provided upon check-in.</li>
                    <li>We respect your privacy. Please review our <a href="#" class="text-primary text-decoration-underline">Privacy Policy</a> to understand how we collect, use, and protect your information.</li>
                    <li>Casa Estela reserves the right to modify these Terms and Conditions at any time. Changes will be posted on this page.</li>
                </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationChoiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Choose Verification Method</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-4">Please choose how you would like to verify your account:</p>
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary" id="verifyEmailBtn">
                            <i class="fas fa-envelope me-2"></i>Verify via Email
                        </button>
                        <button class="btn btn-outline-primary" id="verifyPhoneBtn">
                            <span class="button-text"><i class="fas fa-phone me-2"></i>Verify via SMS</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Toast configuration for SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Phone number validation
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove any non-digit characters
        value = value.replace(/\D/g, '');
        
        // Ensure it starts with '09'
        if (value.length >= 2 && !value.startsWith('09')) {
            value = '09' + value.slice(2);
        }
        
        // Limit to 11 digits
        if (value.length > 11) {
            value = value.slice(0, 11);
        }
        
        // Update the input value
        e.target.value = value;
        
        // Validate the format
        if (value.length === 11 && value.match(/^09\d{9}$/)) {
            e.target.setCustomValidity('');
        } else {
            e.target.setCustomValidity('Phone number must be 11 digits and start with 09');
        }
    });

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const progressBar = document.querySelector('#passwordStrength .progress-bar');
        const strengthText = document.getElementById('strengthText');
        
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 20;
        
        // Uppercase check
        if (password.match(/[A-Z]/)) strength += 20;
        
        // Lowercase check
        if (password.match(/[a-z]/)) strength += 20;
        
        // Number check
        if (password.match(/[0-9]/)) strength += 20;
        
        // Special character check
        if (password.match(/[^A-Za-z0-9]/)) strength += 20;
        
        progressBar.style.width = strength + '%';
        
        // Update color based on strength
        if (strength <= 20) {
            progressBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Very Weak';
        } else if (strength <= 40) {
            progressBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Weak';
        } else if (strength <= 60) {
            progressBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Medium';
        } else if (strength <= 80) {
            progressBar.className = 'progress-bar bg-primary';
            strengthText.textContent = 'Strong';
        } else {
            progressBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Very Strong';
        }
    });

    // Form validation
    document.getElementById('signupForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const terms = document.getElementById('terms');
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        
        if (!phone.match(/^09\d{9}$/)) {
            Swal.fire({
                title: 'Invalid Phone Number',
                text: 'Phone number must be 11 digits and start with 09',
                icon: 'error',
                confirmButtonColor: '#d4af37'
            });
            return;
        }
        
        if (password !== confirmPassword) {
            Swal.fire({
                title: 'Password Mismatch',
                text: 'Passwords do not match',
                icon: 'error',
                confirmButtonColor: '#d4af37'
            });
            return;
        }
        
        if (!terms.checked) {
            Swal.fire({
                title: 'Terms Not Accepted',
                text: 'Please accept the Terms of Service',
                icon: 'warning',
                confirmButtonColor: '#d4af37'
            });
            return;
        }

        // Check if email exists
        try {
            const emailResponse = await fetch('check_existing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            });
            const emailData = await emailResponse.json();
            
            if (emailData.exists) {
                // Close verification modal if it's open
                if (verificationModal._isShown) {
                    verificationModal.hide();
                }
                Swal.fire({
                    title: 'Email Already Registered',
                    text: 'This email address is already in use',
                    icon: 'error',
                    confirmButtonColor: '#d4af37'
                });
                return;
            }

            // Check if phone exists
            const phoneResponse = await fetch('check_existing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ phone: phone })
            });
            const phoneData = await phoneResponse.json();
            
            if (phoneData.exists) {
                // Close verification modal if it's open
                if (verificationModal._isShown) {
                    verificationModal.hide();
                }
                Swal.fire({
                    title: 'Phone Number Already Registered',
                    text: 'This phone number is already in use',
                    icon: 'error',
                    confirmButtonColor: '#d4af37'
                });
                return;
            }

            // Only show verification modal if neither email nor phone exists
            if (!emailData.exists && !phoneData.exists) {
                // Double check that the modal is not already shown
                if (verificationModal._isShown) {
                    verificationModal.hide();
                }
                setTimeout(() => {
                    verificationModal.show();
                }, 100);
            } else {
                // Ensure modal is hidden if there are any validation errors
                if (verificationModal._isShown) {
                    verificationModal.hide();
                }
            }

        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'An error occurred during validation. Please try again.',
                icon: 'error',
                confirmButtonColor: '#d4af37'
            });
        }
    });

    // Handle verification choice modal
    const signupForm = document.getElementById('signupForm');
    const verificationModal = new bootstrap.Modal(document.getElementById('verificationChoiceModal'));
    const verifyPhoneBtn = document.getElementById('verifyPhoneBtn');

    // Remove the old submit event listener
    signupForm.removeEventListener('submit', function(){});

    // Only the async submit handler from earlier will run
    // The verification modal will only show after all validations pass
    // in the async submit handler above

    document.getElementById('verifyEmailBtn').onclick = function() {
        // Get form data
        const formData = {
            firstname: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        // Store form data in session storage
        sessionStorage.setItem('pending_signup_data', JSON.stringify(formData));

        // Hide the verification modal
        verificationModal.hide();

        // Show loading toast
        Toast.fire({
            icon: 'info',
            title: 'Sending verification code...'
        });

        // Send verification code via email
        fetch('send_email_verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: formData.email,
                formData: formData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to verification page
                window.location.href = 'verify.php';
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'Failed to send verification code'
                });
            }
        })
        .catch(error => {
            Toast.fire({
                icon: 'error',
                title: 'Failed to send verification code'
            });
            console.error('Error:', error);
        });
    };

    verifyPhoneBtn.onclick = function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
            firstname: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        // Store form data in session storage immediately
        sessionStorage.setItem('pending_signup_data', JSON.stringify(formData));

        // Hide the verification modal
        verificationModal.hide();

        // Show loading toast
        Toast.fire({
            icon: 'info',
            title: 'Sending verification code...'
        });

        // Redirect immediately
        window.location.href = 'verify_phone.php';

        // Send verification code in the background
        fetch('send_phone_verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phone: formData.phone,
                formData: formData
            })
        });

        return false;
    };
    </script>
</body>
</html>