<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start or continue session
session_start();

// Set content type
header('Content-Type: application/json');

// Log the request details
$log_file = 'post_debug.log';
$timestamp = date('Y-m-d H:i:s');

// Build log entry
$log_entry = "==== POST Request at {$timestamp} ====\n";
$log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
$log_entry .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
$log_entry .= "Referer: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'None') . "\n";

// Log POST data
$log_entry .= "POST Data:\n";
if (empty($_POST)) {
    $log_entry .= "  No POST data received\n";
    
    // Try to read raw POST data
    $raw_post = file_get_contents('php://input');
    if (!empty($raw_post)) {
        $log_entry .= "Raw POST data:\n" . $raw_post . "\n";
    } else {
        $log_entry .= "No raw POST data\n";
    }
} else {
    foreach ($_POST as $key => $value) {
        // If the value is an array, convert to JSON for logging
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $log_entry .= "  {$key}: {$value}\n";
    }
}

// Save log entry
file_put_contents($log_file, $log_entry . "\n", FILE_APPEND);

// Return success response with POST data
echo json_encode([
    'success' => true,
    'message' => 'POST data received and logged successfully',
    'timestamp' => $timestamp,
    'post_data' => $_POST,
    'raw_post' => isset($raw_post) ? $raw_post : null
]);
?> 