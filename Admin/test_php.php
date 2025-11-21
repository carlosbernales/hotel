<?php
// Simple test file to verify PHP is working correctly
echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working properly if you can see this message.</p>";
echo "<p>Current PHP version: " . phpversion() . "</p>";
echo "<p>Current date and time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection if needed
if (file_exists('db.php')) {
    echo "<h2>Testing Database Connection</h2>";
    try {
        require_once 'db.php';
        if (isset($con) && $con) {
            echo "<p style='color:green'>Database connection successful!</p>";
            
            // Check if discount_types table exists
            $query = "SHOW TABLES LIKE 'discount_types'";
            $result = mysqli_query($con, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                echo "<p style='color:green'>discount_types table exists!</p>";
                
                // Count discount types
                $count_query = "SELECT COUNT(*) as count FROM discount_types";
                $count_result = mysqli_query($con, $count_query);
                if ($count_result) {
                    $count_row = mysqli_fetch_assoc($count_result);
                    echo "<p>Number of discount types: " . $count_row['count'] . "</p>";
                }
            } else {
                echo "<p style='color:orange'>discount_types table does not exist yet.</p>";
            }
        } else {
            echo "<p style='color:red'>Database connection failed!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}

// Display some PHP info
echo "<h2>PHP Server Information</h2>";
echo "<pre>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "</pre>";

// Check for errors
echo "<h2>PHP Error Information</h2>";
$error_reporting = error_reporting();
echo "<p>Error reporting level: " . $error_reporting . "</p>";
echo "<p>Display errors setting: " . (ini_get('display_errors') ? 'On' : 'Off') . "</p>";
echo "<p>Error log file: " . ini_get('error_log') . "</p>";
?> 