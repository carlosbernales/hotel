<?php
// Set secure session cookie parameters before starting the session
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    $secure = false; // Set to true in production with HTTPS
} else {
    $secure = true;
}

$cookieParams = session_get_cookie_params();
$cookieParams = [
    'lifetime' => $cookieParams['lifetime'],
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Strict'
];

// Set session parameters
session_set_cookie_params($cookieParams);

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session security headers
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Include the main access control file
require_once __DIR__ . '/../../../includes/access_control.php';

// Require customer role for all customer pages
requireRole('customer');
?>
