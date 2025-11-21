<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Check PHP Error Logs</h2>";

// Get the PHP error log path
$phpErrorLogPath = ini_get('error_log');
echo "<p>PHP error_log setting: <code>" . htmlspecialchars($phpErrorLogPath) . "</code></p>";

// Check if our custom log file exists
$customLogPath = 'table_reservation_errors.log';
$customLogExists = file_exists($customLogPath);
echo "<p>Custom log file (" . htmlspecialchars($customLogPath) . "): <strong>" . ($customLogExists ? "Exists" : "Does not exist") . "</strong></p>";

// Check the file permissions
if ($customLogExists) {
    $perms = fileperms($customLogPath);
    $info = stat($customLogPath);
    
    echo "<p>File permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
    echo "<p>File owner: " . $info['uid'] . "</p>";
    echo "<p>File size: " . number_format($info['size']) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', $info['mtime']) . "</p>";
    
    // Try to read the last few lines
    echo "<h3>Last 20 log entries from: " . htmlspecialchars($customLogPath) . "</h3>";
    
    // Get the last 20 lines
    $lines = file($customLogPath);
    $lastLines = array_slice($lines, -20);
    
    if (!empty($lastLines)) {
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
        echo "<pre style='white-space: pre-wrap; max-height: 300px; overflow-y: auto;'>";
        foreach ($lastLines as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre></div>";
    } else {
        echo "<p>Log file is empty.</p>";
    }
    
    // Option to view the entire log
    echo "<p><a href='view_full_log.php?log=" . htmlspecialchars($customLogPath) . "' style='display:inline-block; padding:10px; background:#2196F3; color:white; text-decoration:none;'>View Full Log</a></p>";
} else {
    // Try to create a test log entry to see if we can write to this location
    $testResult = error_log("Test log entry from check_error_logs.php at " . date('Y-m-d H:i:s'), 3, $customLogPath);
    
    if ($testResult) {
        echo "<p style='color:green'>Successfully created a test log entry!</p>";
        echo "<p>Please reload this page to see the log file.</p>";
    } else {
        echo "<p style='color:red'>Failed to create a test log entry. Check directory permissions.</p>";
    }
}

// List possible log files in the directory
echo "<h3>Other Possible Log Files in Directory:</h3>";
$logFiles = glob('*.log');

if (!empty($logFiles)) {
    echo "<ul>";
    foreach ($logFiles as $logFile) {
        $fileSize = filesize($logFile);
        echo "<li><a href='view_full_log.php?log=" . htmlspecialchars($logFile) . "'>" . 
             htmlspecialchars($logFile) . "</a> (" . number_format($fileSize) . " bytes)</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No .log files found in the current directory.</p>";
}

// Show directory info
echo "<h3>Directory Information:</h3>";
echo "<p>Current working directory: " . htmlspecialchars(getcwd()) . "</p>";
echo "<p>Server temp directory: " . htmlspecialchars(sys_get_temp_dir()) . "</p>";

// Add links
echo "<div style='margin-top: 20px;'>";
echo "<a href='check_advance_table.php' style='display:inline-block; padding:10px; margin-right:10px; background:#4CAF50; color:white; text-decoration:none;'>Check Advance Table</a>";
echo "<a href='test_advance_table_insert.php' style='display:inline-block; padding:10px; margin-right:10px; background:#2196F3; color:white; text-decoration:none;'>Test Table Insert</a>";
echo "<a href='table_packages.php' style='display:inline-block; padding:10px; background:#FF9800; color:white; text-decoration:none;'>Back to Table Packages</a>";
echo "</div>";
?> 