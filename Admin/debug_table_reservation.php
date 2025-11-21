<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up logging
$logFile = 'advance_order_debug.log';
file_put_contents($logFile, "=== Debug Logging Started at " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

echo "<h2>Debug Interceptor for process_table_reservation.php</h2>";
echo "<p>This script will log the data sent to process_table_reservation.php and then forward it to the actual script.</p>";

// Get the raw POST data
$rawData = file_get_contents('php://input');
file_put_contents($logFile, "Raw POST data: " . $rawData . "\n", FILE_APPEND);

// Try to decode JSON data
$decoded = json_decode($rawData, true);
if (json_last_error() === JSON_ERROR_NONE) {
    // Format the JSON data for easier reading
    $prettyJson = json_encode($decoded, JSON_PRETTY_PRINT);
    file_put_contents($logFile, "Decoded JSON data: " . $prettyJson . "\n", FILE_APPEND);
    
    // Check specifically for advance order data
    if (isset($decoded['advanceOrder'])) {
        file_put_contents($logFile, "Found advanceOrder data: " . json_encode($decoded['advanceOrder'], JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
    } else {
        file_put_contents($logFile, "No advanceOrder data found in the request\n", FILE_APPEND);
    }
} else {
    file_put_contents($logFile, "Could not decode JSON data: " . json_last_error_msg() . "\n", FILE_APPEND);
}

// Modify your table_packages.js to point to this interceptor instead of the real endpoint
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>Temporary Setup Instructions:</h3>";
echo "<p>To use this debug interceptor, you need to temporarily modify the AJAX call in <code>table_packages.php</code>:</p>";
echo "<pre style='background: #eee; padding: 10px;'>
// Find this code in table_packages.php in the submitReservation function:
$.ajax({
    url: 'process_table_reservation.php',
    // Change to:
    url: 'debug_table_reservation.php',
</pre>";
echo "</div>";

// Add option to view the log file
echo "<h3>Actions:</h3>";
echo "<a href='view_debug_log.php' style='display:inline-block; padding:10px; margin-right:10px; background:#2196F3; color:white; text-decoration:none;'>View Debug Log</a>";
echo "<a href='check_advance_table.php' style='display:inline-block; padding:10px; margin-right:10px; background:#4CAF50; color:white; text-decoration:none;'>Check Advance Table</a>";
echo "<a href='table_packages.php' style='display:inline-block; padding:10px; background:#FF9800; color:white; text-decoration:none;'>Back to Table Packages</a>";

// Forward to process_table_reservation.php (optional)
if (isset($_GET['forward']) && $_GET['forward'] === 'yes') {
    // Forward the request to the actual process_table_reservation.php
    $ch = curl_init('process_table_reservation.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($rawData)
    ]);
    
    $response = curl_exec($ch);
    file_put_contents($logFile, "Response from process_table_reservation.php: " . $response . "\n", FILE_APPEND);
    
    echo "<h3>Response from process_table_reservation.php:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

echo "<div style='margin-top: 20px; padding: 10px; background: #f8d7da; border-radius: 5px;'>";
echo "<p><strong>Important:</strong> Remember to revert your code to point back to <code>process_table_reservation.php</code> after debugging!</p>";
echo "</div>";
?> 