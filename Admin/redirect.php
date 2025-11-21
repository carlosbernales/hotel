<?php
// Simple redirect script for maintenance mode
// Place this in the root directory to redirect all traffic to the maintenance page

// IP addresses that can bypass the maintenance screen (add your own IP)
$allowed_ips = array(
    // Add your IP address here to maintain access to the admin panel
    // Example: '123.456.789.012'
);

// Check if user's IP is in the allowed list
if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    // Allow the IP to access the site normally
    // Do nothing, just let the script finish without redirecting
} else {
    // Redirect to maintenance page
    header('Location: maintenance.html');
    exit;
}
?> 