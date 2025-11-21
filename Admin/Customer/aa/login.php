<?php
// Add these lines at the very top of login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add this line at the very top to test if PHP is executing
//echo "PHP is working"; // Debug line

require_once 'includes/User.php';
require_once 'includes/Session.php';
require_once 'db_con.php';
require_once 'includes/SeasonalEffects.php';
require_once '../../auth/AuthManager.php';

// Initialize AuthManager
$authManager = new AuthManager($con);

// Initialize seasonal effects
$seasonalEffects = new SeasonalEffects($pdo);
$currentEffects = $seasonalEffects->getCurrentEffects();

// Convert effects to JSON for JavaScript
$effectsJson = json_encode($currentEffects);

// Add a test connection and debug line
try {
    $pdo->query('SELECT 1');
    //echo "Database connection successful"; // Debug line
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

Session::start();

// Redirect if already logged in
if (Session::isLoggedIn()) {
    header('Location: home.php');
    exit();
}

$pendingVerification = Session::get('pending_verification_email');

// Store the current page URL before login
$_SESSION['redirect_url'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        $login = trim($_POST['email']);
        $password = $_POST['password'];

        // Check if input is email or phone
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // Query to find user by email or phone
        $query = $isEmail
            ? "SELECT * FROM userss WHERE email = ?"
            : "SELECT * FROM userss WHERE contact_number = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        // Validate password if user exists
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, proceed with login
        } else {
            // Invalid credentials
            $user = false;
        }

        if ($user) {
            // Start session with user data
            $authManager->startSession($user);

            // Redirect based on user role
            $authManager->redirectBasedOnRole();

            if (isset($_SESSION['user_id'])) {
                // Check for redirect URL in query parameter
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

                // Add JavaScript to handle redirect
                echo "<script>
                    // Check if there's a stored redirect URL
                    const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
                    if (redirectUrl) {
                        sessionStorage.removeItem('redirectAfterLogin');
                        window.location.href = redirectUrl;
                    } else {
                        window.location.href = '$redirect';
                    }
                </script>";
                exit;
            }
        } else {
            // Set specific error message
            $_SESSION['error'] = "Invalid email or password. Please try again.";
            $_SESSION['alert_type'] = "danger"; // For Bootstrap alert styling
            header('Location: login.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred during login. Please try again later.";
        $_SESSION['alert_type'] = "danger";
        error_log("Login error: " . $e->getMessage());
        header('Location: login.php');
        exit();
    }
}

// Get error messages and alert type
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$alertType = isset($_SESSION['alert_type']) ? $_SESSION['alert_type'] : 'danger';

// Clear the messages after retrieving them
unset($_SESSION['error']);
unset($_SESSION['alert_type']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E Akomoda</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        :root {
            --primary-color: #d4af37;
            --primary-hover: #c19a32;
            --text-dark: #2c3e50;
            --bg-light: #f8f9fa;
            --border-radius: 15px;
            --box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('images/casa.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            padding-top: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0;
            width: 100%;
            max-width: 450px;
            margin: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .auth-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .auth-header {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.9) 0%, rgba(193, 154, 50, 0.9) 100%);
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
            transform: skewY(-4deg);
        }

        .auth-header h1 {
            font-family: "Playfair Display", serif;
            font-size: 2.5rem;
            color: white;
            margin: 0;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .auth-body {
            padding: 2rem;
            color: white;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: white;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .input-group {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control {
            background: transparent;
            border: none;
            color: white;
            padding: 0.75rem;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: transparent;
            box-shadow: none;
            color: white;
        }

        .form-check-label {
            color: white;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
        }

        .btn-auth {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.9) 0%, rgba(193, 154, 50, 0.9) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 2.5rem;
            /* Increased horizontal padding */
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            min-width: 200px;
            /* Set minimum width */
            text-align: center;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, rgba(212, 175, 55, 1) 0%, rgba(193, 154, 50, 1) 100%);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .auth-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.1);
        }

        .auth-footer p {
            color: white;
            margin: 0;
        }

        .auth-footer a {
            color: var(--primary-color);
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: white;
            text-decoration: none;
        }

        /* Update alert styling for better visibility on transparent background */
        .alert {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.3);
        }

        /* Add this for better text contrast */
        .forgot-password {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: white;
            text-decoration: underline;
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Update the eye icon button styling */
        .btn-outline-secondary {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
        }

        .btn-outline-secondary:focus {
            box-shadow: none;
            border: none;
            background: transparent;
        }

        /* Prevent the button from changing color when active */
        .btn-outline-secondary:active,
        .btn-outline-secondary:not(:disabled):not(.disabled):active {
            background: transparent;
            border: none;
            color: white;
        }

        .btn-outline-secondary i {
            font-size: 1.1rem;
        }
    </style>

    <!-- Seasonal Effects Scripts -->
    <script>
        // Get effects from PHP
        const currentEffects = <?php echo json_encode($seasonalEffects->getCurrentEffects()); ?>;

        class SeasonalEffects {
            constructor() {
                this.container = document.createElement('div');
                this.container.id = 'seasonal-effects';
                document.body.appendChild(this.container);

                // Apply current effects from database
                this.applyEffects();
            }

            applyEffects() {
                // Clear any existing effects
                this.container.innerHTML = '';

                // Apply each active effect from the database
                currentEffects.forEach(effect => {
                    switch (effect.effect_type) {
                        case 'snow':
                            this.createSnowEffect();
                            break;
                        case 'hearts':
                            this.createHeartsEffect();
                            break;
                        case 'fireworks':
                            this.createFireworksEffect();
                            break;
                    }
                });
            }

            createSnowEffect() {
                const snowflakes = 50;
                for (let i = 0; i < snowflakes; i++) {
                    const snowflake = document.createElement('div');
                    snowflake.className = 'snowflake';
                    snowflake.style.cssText = `
                        position: fixed;
                        color: #fff;
                        font-size: ${Math.random() * 20 + 10}px;
                        left: ${Math.random() * 100}vw;
                        opacity: ${Math.random()};
                        user-select: none;
                        z-index: 1;
                        animation: snowfall ${Math.random() * 3 + 2}s linear infinite;
                    `;
                    snowflake.innerHTML = '❅';
                    this.container.appendChild(snowflake);
                }

                // Add snowfall animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes snowfall {
                        0% {
                            transform: translateY(-100px);
                        }
                        100% {
                            transform: translateY(100vh);
                        }
                    }
                `;
                document.head.appendChild(style);
            }

            createHeartsEffect() {
                const hearts = 30;
                for (let i = 0; i < hearts; i++) {
                    const heart = document.createElement('div');
                    heart.className = 'heart';
                    heart.style.cssText = `
                        position: fixed;
                        color: #ff69b4;
                        font-size: ${Math.random() * 20 + 10}px;
                        left: ${Math.random() * 100}vw;
                        opacity: ${Math.random()};
                        user-select: none;
                        z-index: 1;
                        animation: float ${Math.random() * 3 + 2}s ease-in-out infinite;
                    `;
                    heart.innerHTML = '❤';
                    this.container.appendChild(heart);
                }

                // Add floating animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes float {
                        0%, 100% {
                            transform: translateY(0) rotate(0deg);
                        }
                        50% {
                            transform: translateY(-20px) rotate(180deg);
                        }
                    }
                `;
                document.head.appendChild(style);
            }

            createFireworksEffect() {
                const fireworks = () => {
                    const firework = document.createElement('div');
                    firework.className = 'firework';
                    firework.style.cssText = `
                        position: fixed;
                        left: ${Math.random() * 100}vw;
                        top: ${Math.random() * 100}vh;
                        width: 4px;
                        height: 4px;
                        background: hsl(${Math.random() * 360}, 100%, 50%);
                        border-radius: 50%;
                        animation: explode 1s ease-out forwards;
                    `;
                    this.container.appendChild(firework);

                    setTimeout(() => firework.remove(), 1000);
                };

                // Create fireworks every 500ms
                const interval = setInterval(fireworks, 500);
                setTimeout(() => clearInterval(interval), 10000); // Stop after 10 seconds

                // Add explosion animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes explode {
                        0% {
                            transform: scale(1);
                            opacity: 1;
                        }
                        100% {
                            transform: scale(30);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }

        // Initialize seasonal effects when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const effects = new SeasonalEffects();
        });
    </script>
</head>

<body>
    <?php include('nav.php'); ?>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p id="time-greeting">Sign in to continue to E Akomoda</p>
            </div>

            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (Session::hasFlash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo Session::getFlash('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($pendingVerification): ?>
                    <!-- Verification Section -->
                    <div class="verification-section mb-4">
                        <div class="alert alert-info">
                            Please verify your email address. We've sent a verification code to
                            <?php echo htmlspecialchars($pendingVerification); ?>
                        </div>
                        <form action="verify.php" method="POST" id="verificationForm">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($pendingVerification); ?>">
                            <div class="form-group">
                                <label class="form-label" for="verification_code">Verification Code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="text" class="form-control" id="verification_code" name="verification_code"
                                        required maxlength="6" placeholder="Enter 6-digit code">
                                </div>
                            </div>
                            <button type="submit" name="verify_code" class="btn btn-primary w-100 mt-2">
                                <i class="fas fa-check-circle me-2"></i>Verify Email
                            </button>
                        </form>
                        <div class="text-center mt-2">
                            <small>
                                Didn't receive the code or code expired?
                                <div id="resendSection">
                                    <form action="resend_code.php" method="POST" class="d-inline" id="resendForm">
                                        <input type="hidden" name="email"
                                            value="<?php echo htmlspecialchars($pendingVerification); ?>">
                                        <button type="submit" class="btn btn-link btn-sm p-0" id="resendButton">Resend
                                            Code</button>
                                    </form>
                                    <span id="timer" class="d-none text-muted">
                                        Resend available in <span id="countdown">120</span>s
                                    </span>
                                </div>
                            </small>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login Form -->
                    <form id="loginForm" action="login.php" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="email">Email or Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email or phone number" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                        </div>

                        <div class="btn-container">
                            <button type="submit" name="login" class="btn btn-auth">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
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

        // Time-based greeting
        function updateGreeting() {
            const hour = new Date().getHours();
            let greeting = '';

            if (hour >= 5 && hour < 12) {
                greeting = 'Good Morning';
            } else if (hour >= 12 && hour < 18) {
                greeting = 'Good Afternoon';
            } else {
                greeting = 'Good Evening';
            }

            document.getElementById('time-greeting').innerHTML =
                `${greeting}! Sign in to continue to E Akomoda`;
        }

        // Update greeting when page loads
        document.addEventListener('DOMContentLoaded', updateGreeting);

        // Timer functionality
        let timer;
        const resendButton = document.getElementById('resendButton');
        const timerDisplay = document.getElementById('timer');
        const countdownDisplay = document.getElementById('countdown');
        const resendForm = document.getElementById('resendForm');

        // Check if there's a stored timer end time
        const timerEndTime = localStorage.getItem('verificationTimerEnd');
        if (timerEndTime && new Date().getTime() < timerEndTime) {
            startTimer((timerEndTime - new Date().getTime()) / 1000);
        }

        resendForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Submit the form using fetch
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Start the timer
                        startTimer(120);
                        // Show success message
                        alert(data.message);
                    } else {
                        alert(data.message || 'Failed to resend code');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to resend code');
                });
        });

        function startTimer(duration) {
            // Store timer end time
            const endTime = new Date().getTime() + (duration * 1000);
            localStorage.setItem('verificationTimerEnd', endTime);

            // Show timer, hide resend button
            resendButton.classList.add('d-none');
            timerDisplay.classList.remove('d-none');

            let timeLeft = duration;

            // Clear existing timer if any
            if (timer) clearInterval(timer);

            timer = setInterval(() => {
                timeLeft--;
                countdownDisplay.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    localStorage.removeItem('verificationTimerEnd');
                    resendButton.classList.remove('d-none');
                    timerDisplay.classList.add('d-none');
                }
            }, 1000);
        }

        // Add these new functions
        document.addEventListener('DOMContentLoaded', function () {
            // Add fade-in animation to auth container
            const authContainer = document.querySelector('.auth-container');
            authContainer.classList.add('fade-in');

            // Animate form fields sequentially
            const formFields = document.querySelectorAll('.form-group');
            formFields.forEach((field, index) => {
                field.style.opacity = '0';
                field.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    field.style.transition = 'all 0.5s ease';
                    field.style.opacity = '1';
                    field.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });

            // Enhanced input focus effects
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.closest('.input-group').style.transform = 'scale(1.02)';
                });

                input.addEventListener('blur', function () {
                    this.closest('.input-group').style.transform = 'scale(1)';
                });
            });

            // Smooth button click effect
            const submitBtn = document.querySelector('.btn-auth');
            submitBtn.addEventListener('mousedown', function () {
                this.style.transform = 'scale(0.98)';
            });

            submitBtn.addEventListener('mouseup', function () {
                this.style.transform = 'scale(1)';
            });

            // Dynamic greeting with fade effect
            function updateGreetingWithAnimation() {
                const greetingElement = document.getElementById('time-greeting');
                const hour = new Date().getHours();
                let greeting = '';

                if (hour >= 5 && hour < 12) greeting = 'Good Morning';
                else if (hour >= 12 && hour < 18) greeting = 'Good Afternoon';
                else greeting = 'Good Evening';

                greetingElement.style.opacity = '0';
                setTimeout(() => {
                    greetingElement.innerHTML = `${greeting}! Sign in to continue to E Akomoda`;
                    greetingElement.style.transition = 'opacity 0.5s ease';
                    greetingElement.style.opacity = '1';
                }, 500);
            }

            updateGreetingWithAnimation();
            setInterval(updateGreetingWithAnimation, 60000); // Update every minute
        });
    </script>
</body>

</html>