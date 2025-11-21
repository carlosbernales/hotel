<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Auto Fix Column Names</h1>";

// Search for files containing 'reservation_date'
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
                        if (stripos($content, $term) !== false) {
                            $found_files[] = $path;
                        }
                    }
                }
            }
        }
        closedir($handle);
    }
}

// Find all files containing 'reservation_date'
searchDir('.', 'reservation_date');

echo "<h2>Found Files</h2>";
echo "<p>Found " . count($found_files) . " PHP files containing 'reservation_date'</p>";

// Create backup directory
$backup_dir = 'column_fix_backups_' . date('Y-m-d_H-i-s');
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Fix each file
$fixed_count = 0;
$error_count = 0;

echo "<h2>Fix Results</h2>";
echo "<ul>";

foreach ($found_files as $file) {
    // Skip this file itself
    if (basename($file) == 'auto_fix_column_names.php' || 
        basename($file) == 'fix_column_names.php' || 
        basename($file) == 'view_error_log.php') {
        echo "<li>Skipped $file (utility file)</li>";
        continue;
    }
    
    // Create backup
    $backup_file = $backup_dir . '/' . str_replace('/', '_', $file) . '.bak';
    copy($file, $backup_file);
    
    // Read file content
    $content = file_get_contents($file);
    
    // Count occurrences before replacement
    $count_before = substr_count(strtolower($content), 'reservation_date');
    
    // Replace reservation_date with booking_date
    $new_content = str_ireplace('reservation_date', 'booking_date', $content);
    
    // Count occurrences after replacement
    $count_after = substr_count(strtolower($new_content), 'reservation_date');
    $replacements = $count_before - $count_after;
    
    // Save the modified content
    if (file_put_contents($file, $new_content)) {
        echo "<li style='color:green;'>✓ Fixed $file - Replaced $replacements occurrences</li>";
        $fixed_count++;
    } else {
        echo "<li style='color:red;'>✗ Failed to write to $file. Check file permissions.</li>";
        $error_count++;
    }
}

echo "</ul>";

echo "<h2>Summary</h2>";
echo "<p>Total files found: " . count($found_files) . "</p>";
echo "<p>Files fixed: $fixed_count</p>";
echo "<p>Errors: $error_count</p>";
echo "<p>Backups created in: $backup_dir</p>";

// Also check the table bookings table structure
echo "<h2>Table Bookings Structure</h2>";

require_once 'db.php';

// Get table structure
$structure = mysqli_query($con, "SHOW COLUMNS FROM table_bookings");
if (!$structure) {
    echo "<p style='color:red;'>Error querying table structure: " . mysqli_error($con) . "</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $booking_date_exists = false;
    
    while ($column = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
        
        if ($column['Field'] == 'booking_date') {
            $booking_date_exists = true;
        }
    }
    echo "</table>";
    
    if ($booking_date_exists) {
        echo "<p style='color:green;'>The 'booking_date' column exists in the table_bookings table. Your fixes should work correctly.</p>";
    } else {
        echo "<p style='color:red;'>Warning: The 'booking_date' column does not exist in the table_bookings table. You may need to add it or use a different column name.</p>";
    }
}

// Close the connection
mysqli_close($con);

echo "<p><a href='index.php'>Return to Dashboard</a></p>";
?> 