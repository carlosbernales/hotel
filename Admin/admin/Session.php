<?php
// Only start session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Common session functions can be defined here

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Set session timeout (e.g., 30 minutes)
function setSessionTimeout($minutes = 30) {
    $_SESSION['timeout'] = time() + ($minutes * 60);
}

// Check if session has timed out
function hasSessionTimedOut() {
    if (isset($_SESSION['timeout']) && $_SESSION['timeout'] < time()) {
        return true;
    }
    return false;
}

// Set user session
function setUserSession($user_id, $user_name, $user_role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_role'] = $user_role;
    setSessionTimeout();
}

// Clear user session
function clearUserSession() {
    session_unset();
    session_destroy();
}
?> 