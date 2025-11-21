<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Full Error Log</h1>";

// Define log files to check
$log_files = [
    'table_reservation_errors.log',
    'error_log',
    'php_errors.log',
    'order_errors.log'
];

// Check each log file
foreach ($log_files as $log_file) {
    echo "<h2>$log_file</h2>";
    
    if (file_exists($log_file)) {
        // Get file size
        $size = filesize($log_file);
        echo "<p>File size: " . number_format($size / 1024, 2) . " KB</p>";
        
        // Read file content
        $content = file_get_contents($log_file);
        
        // Search for reservation_date errors
        $reservation_date_errors = preg_match_all('/reservation_date/', $content, $matches);
        echo "<p>Found $reservation_date_errors occurrences of 'reservation_date' errors</p>";
        
        // Show the last 50 lines
        $lines = explode("\n", $content);
        $last_lines = array_slice($lines, -50);
        
        echo "<div style='background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;'>";
        echo "<pre>" . htmlspecialchars(implode("\n", $last_lines)) . "</pre>";
        echo "</div>";
    } else {
        echo "<p>File does not exist</p>";
    }
    
    echo "<hr>";
}

// Also check for all PHP files containing "reservation_date"
echo "<h2>Files Containing 'reservation_date'</h2>";
$found_files = [];

function searchDir($dir, $term) {
    global $found_files;
    
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $path = "$dir/$entry";
                
                if (is_dir($path)) {
                    // Skip large directories like vendor
                    if ($entry != 'vendor' && $entry != 'node_modules') {
                        searchDir($path, $term);
                    }
                } else {
                    // Check only PHP files
                    if (pathinfo($path, PATHINFO_EXTENSION) == 'php') {
                        $content = file_get_contents($path);
                        if (strpos($content, $term) !== false) {
                            $found_files[] = $path;
                        }
                    }
                }
            }
        }
        closedir($handle);
    }
}

searchDir('.', 'reservation_date');

if (count($found_files) > 0) {
    echo "<ul>";
    foreach ($found_files as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No files found containing 'reservation_date'</p>";
}

?> 