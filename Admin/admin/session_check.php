<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: /Admin/login.php');
    exit();
}

// Check if user has admin privileges
if ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'frontdesk' && $_SESSION['user_type'] !== 'cashier') {
    header('Location: /Admin/login.php?error=unauthorized');
    exit();
}

// Set a constant to prevent direct script access
define('ADMIN_PANEL', true);
?> 