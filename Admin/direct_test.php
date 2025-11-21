<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Direct Message Test</h1>";

// Create POST data
$message = "Test message sent directly from PHP at " . date('Y-m-d H:i:s');
$postData = ['message' => $message];

// Use cURL to make a POST request to send_message.php
$ch = curl_init('http://localhost/Admin/send_message.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

// Execute the request
$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

// Display results
echo "<h2>Test Results</h2>";
echo "<p><strong>Message sent:</strong> " . htmlspecialchars($message) . "</p>";

if ($error) {
    echo "<p style='color: red'><strong>cURL Error:</strong> " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p><strong>HTTP Status:</strong> " . $info['http_code'] . "</p>";
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Try to parse the JSON
    $jsonResponse = json_decode($response, true);
    if ($jsonResponse !== null) {
        echo "<h3>Parsed JSON Response:</h3>";
        echo "<pre>";
        print_r($jsonResponse);
        echo "</pre>";
    }
}

// Check log file
$logFile = 'message_errors.log';
if (file_exists($logFile)) {
    echo "<h2>Recent Log Entries</h2>";
    echo "<pre>";
    // Show the last 20 lines from the log file
    $log = file($logFile);
    $log = array_slice($log, max(0, count($log) - 20));
    echo implode('', $log);
    echo "</pre>";
}

// Link back to messages
echo "<p><a href='message.php'>Return to Messages</a></p>";
?> 