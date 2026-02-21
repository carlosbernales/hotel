<?php
require_once 'includes/User.php';
require_once 'includes/Session.php';
require_once 'includes/Mailer.php';

Session::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['email'])) {
    try {
        $email = $_POST['email'] ?? $_GET['email'];
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }

        $user = new User();
        
        // Generate and send new verification code
        if ($user->resendVerificationCode($email)) {
            Session::setFlash('success', 'A new verification code has been sent to your email');
        } else {
            Session::setFlash('error', 'Failed to send verification code');
        }
        
    } catch (Exception $e) {
        Session::setFlash('error', $e->getMessage());
    }
}

// Redirect back to login page
header('Location: login.php');
exit();
