<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if log parameter provided
if (!isset($_GET['log']) || empty($_GET['log'])) {
    echo "<p>No log file specified. <a href='check_error_logs.php'>Go back</a>.</p>";
    exit;
}

// Validate log filename to prevent directory traversal
$logFile = basename($_GET['log']);
if ($logFile !== $_GET['log']) {
    echo "<p>Invalid log file name. <a href='check_error_logs.php'>Go back</a>.</p>";
    exit;
}

// Check if file exists
if (!file_exists($logFile)) {
    echo "<p>Log file does not exist: " . htmlspecialchars($logFile) . "</p>";
    echo "<p><a href='check_error_logs.php'>Go back</a>.</p>";
    exit;
}

// Get file info
$fileSize = filesize($logFile);
$lastModified = date('Y-m-d H:i:s', filemtime($logFile));

echo "<h2>Log File: " . htmlspecialchars($logFile) . "</h2>";
echo "<p>Size: " . number_format($fileSize) . " bytes</p>";
echo "<p>Last Modified: " . $lastModified . "</p>";

// Read and display the file
if ($fileSize > 0) {
    $contents = file_get_contents($logFile);
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<pre style='white-space: pre-wrap; max-height: 70vh; overflow-y: auto;'>" . htmlspecialchars($contents) . "</pre>";
    echo "</div>";
    
    // Add Clear Log button
    echo "<form method='post'>";
    echo "<button type='submit' name='clear_log' style='background: #dc3545; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; margin-right: 10px;'>Clear Log</button>";
    
    // Add download button
    echo "<a href='download_log.php?log=" . htmlspecialchars($logFile) . "' style='display:inline-block; padding:10px; background:#17a2b8; color:white; text-decoration:none; border-radius: 5px;'>Download Log</a>";
    echo "</form>";
    
    // Clear log if requested
    if (isset($_POST['clear_log'])) {
        file_put_contents($logFile, '');
        echo "<script>window.location.reload();</script>";
    }
} else {
    echo "<p>Log file is empty.</p>";
}

// Add navigation links
echo "<div style='margin-top: 20px;'>";
echo "<a href='check_error_logs.php' style='display:inline-block; padding:10px; margin-right:10px; background:#2196F3; color:white; text-decoration:none;'>Back to Logs</a>";
echo "<a href='check_advance_table.php' style='display:inline-block; padding:10px; margin-right:10px; background:#4CAF50; color:white; text-decoration:none;'>Check Advance Table</a>";
echo "<a href='table_packages.php' style='display:inline-block; padding:10px; background:#FF9800; color:white; text-decoration:none;'>Back to Table Packages</a>";
echo "</div>";
?> 