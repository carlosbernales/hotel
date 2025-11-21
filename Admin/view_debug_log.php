<?php
// Set up file to read
$logFile = 'advance_order_debug.log';

echo "<h2>Debug Log</h2>";

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    
    if (!empty($logContent)) {
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
        echo "<pre style='white-space: pre-wrap; max-height: 500px; overflow-y: auto;'>" . htmlspecialchars($logContent) . "</pre>";
        echo "</div>";
        
        // Add option to clear log
        echo "<form method='post'>";
        echo "<button type='submit' name='clear_log' style='background: #dc3545; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;'>Clear Log</button>";
        echo "</form>";
    } else {
        echo "<p>The log file is empty.</p>";
    }
} else {
    echo "<p>No log file found. Run the debug_table_reservation.php script first.</p>";
}

// Clear log if requested
if (isset($_POST['clear_log'])) {
    file_put_contents($logFile, '');
    echo "<script>window.location.reload();</script>";
}

// Add links
echo "<div style='margin-top: 20px;'>";
echo "<a href='debug_table_reservation.php' style='display:inline-block; padding:10px; margin-right:10px; background:#2196F3; color:white; text-decoration:none;'>Debug Interceptor</a>";
echo "<a href='check_advance_table.php' style='display:inline-block; padding:10px; margin-right:10px; background:#4CAF50; color:white; text-decoration:none;'>Check Advance Table</a>";
echo "<a href='table_packages.php' style='display:inline-block; padding:10px; background:#FF9800; color:white; text-decoration:none;'>Back to Table Packages</a>";
echo "</div>";
?> 