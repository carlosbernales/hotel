<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fix Column Names Issue</h1>";

// Function to search for the term in a file and return the context
function findTermInFile($file, $term) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $found = [];
    
    for ($i = 0; $i < count($lines); $i++) {
        if (stripos($lines[$i], $term) !== false) {
            $start = max(0, $i - 5);
            $end = min(count($lines) - 1, $i + 5);
            
            $context = [];
            for ($j = $start; $j <= $end; $j++) {
                $context[] = ($j == $i ? "<strong>" : "") . 
                            "Line " . ($j + 1) . ": " . htmlspecialchars($lines[$j]) . 
                            ($j == $i ? "</strong>" : "");
            }
            
            $found[] = [
                'line' => $i + 1,
                'context' => $context
            ];
        }
    }
    
    return $found;
}

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

searchDir('.', 'reservation_date');

if (count($found_files) > 0) {
    echo "<h2>Files Containing 'reservation_date'</h2>";
    echo "<p>Found " . count($found_files) . " files with the term 'reservation_date'</p>";
    
    foreach ($found_files as $file) {
        echo "<h3>$file</h3>";
        
        $occurrences = findTermInFile($file, 'reservation_date');
        echo "<p>Found " . count($occurrences) . " occurrences of 'reservation_date'</p>";
        
        foreach ($occurrences as $index => $occurrence) {
            echo "<div style='margin-bottom: 20px;'>";
            echo "<h4>Occurrence #" . ($index + 1) . " (Line " . $occurrence['line'] . ")</h4>";
            echo "<div style='background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
            echo "<pre>" . implode("\n", $occurrence['context']) . "</pre>";
            echo "</div>";
            echo "</div>";
        }
        
        // Add form to fix this file - only if it's a file we want to modify
        if (basename($file) != 'fix_column_names.php' && basename($file) != 'view_error_log.php') {
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='file' value='$file'>";
            echo "<button type='submit' name='fix' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer;'>Fix in this file</button>";
            echo "</form>";
        }
    }
} else {
    echo "<p>No files found containing 'reservation_date'</p>";
}

// Handle form submission to fix a file
if (isset($_POST['fix']) && isset($_POST['file'])) {
    $file = $_POST['file'];
    
    if (file_exists($file)) {
        // Read file content
        $content = file_get_contents($file);
        
        // Replace reservation_date with booking_date
        $new_content = str_ireplace('reservation_date', 'booking_date', $content);
        
        // Save the modified content
        if (file_put_contents($file, $new_content)) {
            echo "<div style='background-color: #4CAF50; color: white; padding: 15px; margin-top: 20px;'>";
            echo "<p>Successfully replaced 'reservation_date' with 'booking_date' in $file!</p>";
            echo "</div>";
            
            // Refresh the page to show the updated content
            echo "<script>setTimeout(function() { window.location.reload(); }, 3000);</script>";
        } else {
            echo "<div style='background-color: #f44336; color: white; padding: 15px; margin-top: 20px;'>";
            echo "<p>Failed to write to file $file. Check file permissions.</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background-color: #f44336; color: white; padding: 15px; margin-top: 20px;'>";
        echo "<p>File $file does not exist!</p>";
        echo "</div>";
    }
}

// Extra features: Check the table_bookings structure
echo "<h2>Check table_bookings Structure</h2>";

require_once 'db.php';

// Check if the table exists
$table_exists = mysqli_query($con, "SHOW TABLES LIKE 'table_bookings'");
if (mysqli_num_rows($table_exists) == 0) {
    echo "<p style='color:red;'>The table_bookings table does not exist!</p>";
} else {
    // Get table structure
    $structure = mysqli_query($con, "SHOW COLUMNS FROM table_bookings");
    if (!$structure) {
        echo "<p style='color:red;'>Error querying table structure: " . mysqli_error($con) . "</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        // Collect column names
        $column_names = [];
        
        while ($column = mysqli_fetch_assoc($structure)) {
            $column_names[] = $column['Field'];
            
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show column names as a reference
        echo "<h3>Available Column Names</h3>";
        echo "<ul>";
        foreach ($column_names as $name) {
            echo "<li>$name</li>";
        }
        echo "</ul>";
    }
}

// Close the connection
mysqli_close($con);

echo "<p><a href='index.php'>Return to Dashboard</a></p>";
?> 