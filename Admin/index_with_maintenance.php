<?php
// Maintenance mode flag - set to true to activate maintenance mode
$maintenance_mode = true;

// Your IP address to allow you to bypass the maintenance page
$your_ip = $_SERVER['REMOTE_ADDR'];

// IP addresses that can bypass the maintenance screen
$allowed_ips = array(
    $your_ip, // Your current IP address is automatically added
    // Add other IPs if needed
);

// Check if maintenance mode is on and IP is not allowed
if ($maintenance_mode && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    // Redirect to maintenance page
    header('Location: maintenance.html');
    exit;
}

// If we get here, either maintenance mode is off or the IP is allowed
// Include the original index.php code below this point

// The rest of your index.php file goes here
// You can copy and paste the contents of your original index.php file here

// If the index.php merely includes another file, keep that include:
// include('your_main_file.php');

// For now, let's just show a simple admin panel link
if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    echo '<div style="padding: 20px; background-color: #ffdddd; border: 1px solid #ff0000; margin: 20px 0; text-align: center;">';
    echo '<strong>MAINTENANCE MODE IS ACTIVE</strong><br>';
    echo 'Only your IP address (' . $_SERVER['REMOTE_ADDR'] . ') can see this page.<br>';
    echo 'Other visitors are being redirected to the maintenance page.<br>';
    echo '<a href="maintenance.html" target="_blank">View maintenance page</a>';
    echo '</div>';
}

// Include your original code or just forward to the original index.php
// include('original_index.php');
// or
// require_once('original_index.php');
?> 