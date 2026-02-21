<?php

function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}


function isLoggedIn() {
    ensureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserRole() {
    ensureSession();
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

function getUserId() {
    ensureSession();
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function requireRole($requiredRoles, $redirectUrl = null) {
    ensureSession();
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        if ($redirectUrl) {
            $_SESSION['error'] = "Please login first";
            header("Location: $redirectUrl");
            exit();
        }
        return false;
    }
    
    // Convert single role to array for uniform handling
    if (!is_array($requiredRoles)) {
        $requiredRoles = [$requiredRoles];
    }
    
    $userRole = getUserRole();
    
    // Check if user's role is in the allowed roles
    if (!in_array($userRole, $requiredRoles)) {
        // Log security violation with detailed information
        $logMessage = sprintf(
            "Security violation - User ID: %s, Role: %s, IP: %s, URL: %s, Required roles: %s",
            getUserId() ?? 'unknown',
            $userRole ?? 'unknown',
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['REQUEST_URI'] ?? 'unknown',
            implode(', ', $requiredRoles)
        );
        error_log($logMessage);
        
        if ($redirectUrl) {
            $_SESSION['error'] = "Access denied. You don't have permission to access this area.";
            header("Location: $redirectUrl");
            exit();
        }
        return false;
    }
    
    return true;
}

function requireCustomer($redirectUrl = 'login.php') {
    return requireRole('customer', $redirectUrl);
}

function requireAdmin($redirectUrl = '/Admin/Customer/aa/login.php') {
    return requireRole('admin', $redirectUrl);
}

function requireCashier($redirectUrl = '/Admin/Customer/aa/login.php') {
    return requireRole('cashier', $redirectUrl);
}

function requireFrontdesk($redirectUrl = '/Admin/Customer/aa/login.php') {
    return requireRole('frontdesk', $redirectUrl);
}

function requireStaff($redirectUrl = '/Admin/Customer/aa/login.php') {
    return requireRole(['admin', 'cashier', 'frontdesk'], $redirectUrl);
}

function getCSRFToken() {
    ensureSession();
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRF($token) {
    ensureSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
