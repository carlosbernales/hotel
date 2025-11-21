<?php
// Diagnostic script for admin system
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin System Diagnostic</h1>";
echo "<p>This script will check your system for common issues that might prevent the admin panel from loading.</p>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    echo "<p style='color: red;'>Warning: Your PHP version is quite old. Consider upgrading to PHP 7.x or higher.</p>";
}

// Check file permissions and existence
echo "<h2>File System Checks</h2>";
$critical_files = array(
    'index.php',
    'session_check.php',
    'connection.php', // Common DB connection file
    'config.php',     // Common config file
    'database.php'    // Alternative DB file
);

foreach ($critical_files as $file) {
    echo "<p>Checking $file: ";
    if (file_exists($file)) {
        echo "<span style='color: green;'>Found</span>";
        echo " (Permissions: " . substr(sprintf('%o', fileperms($file)), -4) . ")";
    } else {
        echo "<span style='color: red;'>Not found</span>";
    }
    echo "</p>";
}

// Check database connection if possible
echo "<h2>Database Connection</h2>";
// Try to include common database connection files
$db_connected = false;

// Try to detect the database connection file
$possible_db_files = array('connection.php', 'config.php', 'database.php', 'db.php');
foreach ($possible_db_files as $db_file) {
    if (file_exists($db_file)) {
        echo "<p>Found possible database file: $db_file</p>";
        
        // Try to read file without including it (which might cause errors)
        $file_content = file_get_contents($db_file);
        
        // Look for common database variables
        $patterns = array(
            'hostname' => '/[\'"]host[\'"]|[\'"]hostname[\'"]|[\'"]server[\'"]|[\'"]db_host[\'"]/i',
            'username' => '/[\'"]user[\'"]|[\'"]username[\'"]|[\'"]db_user[\'"]/i',
            'database' => '/[\'"]database[\'"]|[\'"]db_name[\'"]|[\'"]dbname[\'"]/i'
        );
        
        echo "<p>Database connection parameters detected:</p>";
        $params_found = 0;
        foreach ($patterns as $param => $pattern) {
            if (preg_match($pattern, $file_content)) {
                echo "<span style='color: green;'>✓ $param parameter found</span><br>";
                $params_found++;
            } else {
                echo "<span style='color: red;'>✗ No $param parameter found</span><br>";
            }
        }
        
        if ($params_found >= 3) {
            echo "<p style='color: green;'>Database connection parameters look good.</p>";
        } else {
            echo "<p style='color: red;'>Some database parameters may be missing.</p>";
        }
    }
}

// Attempt to check error logs
echo "<h2>Error Logs</h2>";
$error_log_location = ini_get('error_log');
if ($error_log_location) {
    echo "<p>Error log location: $error_log_location</p>";
    
    // Try to access the error log file if it's in the current directory
    if (file_exists($error_log_location) && is_readable($error_log_location)) {
        echo "<p>Latest error log entries:</p>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; max-height: 200px; overflow: auto;'>";
        $log_content = file_get_contents($error_log_location);
        $lines = explode("\n", $log_content);
        $last_lines = array_slice($lines, -20); // Get last 20 lines
        echo htmlspecialchars(implode("\n", $last_lines));
        echo "</pre>";
    } else {
        echo "<p>Cannot read error log file. It may be in a different location or not accessible.</p>";
    }
} else {
    echo "<p>Error log location not set in PHP configuration.</p>";
}

// Check memory limits
echo "<h2>Server Resources</h2>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Max Execution Time: " . ini_get('max_execution_time') . " seconds</p>";
echo "<p>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Post Max Size: " . ini_get('post_max_size') . "</p>";

// Information about server and environment
echo "<h2>Server Information</h2>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Server Protocol: " . $_SERVER['SERVER_PROTOCOL'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Check if critical PHP modules are enabled
echo "<h2>PHP Modules</h2>";
$required_modules = array('mysqli', 'pdo_mysql', 'gd', 'json', 'session');
$missing_modules = array();

foreach ($required_modules as $module) {
    if (extension_loaded($module)) {
        echo "<p>$module: <span style='color: green;'>Enabled</span></p>";
    } else {
        echo "<p>$module: <span style='color: red;'>Not enabled</span></p>";
        $missing_modules[] = $module;
    }
}

if (!empty($missing_modules)) {
    echo "<p style='color: red;'>Warning: Some required PHP modules are missing. Contact your hosting provider to enable them.</p>";
}

// Session test
echo "<h2>Session Test</h2>";
session_start();
$_SESSION['test'] = 'Test session value';
echo "<p>Session started and test value set.</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Testing session reading: " . ($_SESSION['test'] ?? 'Failed') . "</p>";

// Quick recommendations
echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Check your database settings - the admin panel may not be connecting to the database.</li>";
echo "<li>Look for error messages in the PHP logs (shown above if available).</li>";
echo "<li>Make sure all required files exist and have proper permissions (typically 644 for files, 755 for directories).</li>";
echo "<li>Check if your hosting provider has recently made any changes or updates.</li>";
echo "<li>Contact your hosting support if server issues persist.</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Deploy Maintenance Page</h2>";
echo "<p>In the meantime, you can use the maintenance page to inform users:</p>";
echo "<a href='maintenance.html' target='_blank' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; display: inline-block; margin: 10px 0; border-radius: 5px;'>View Maintenance Page</a>";
?> 