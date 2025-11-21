<?php
/**
 * Menu Fix Application Script
 * 
 * This script applies the menu fix to the table_packages.php file
 * It adds the menu_fix_direct.js script to the table_packages.php file
 */

// Security check - restrict to allowed IPs or localhost for security
$allowed_ips = ['127.0.0.1', '::1', $_SERVER['REMOTE_ADDR']];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    die("Access denied. Your IP: " . $_SERVER['REMOTE_ADDR']);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$js_file = 'menu_fix_direct.js';
$target_file = 'table_packages.php';

// Check if JavaScript file exists
if (!file_exists($js_file)) {
    die("<h2>Error: JavaScript file not found!</h2><p>The file '$js_file' could not be found. Please make sure it exists.</p>");
}

// Check if target file exists
if (!file_exists($target_file)) {
    die("<h2>Error: Target file not found!</h2><p>The file '$target_file' could not be found. Please make sure it exists.</p>");
}

// Create a backup of the original file
$backup_file = $target_file . '.backup-' . date('Y-m-d-H-i-s') . '.php';
if (!copy($target_file, $backup_file)) {
    die("<h2>Error: Could not create backup file</h2><p>Could not copy '$target_file' to '$backup_file'.</p>");
}

// Read the target file content
$content = file_get_contents($target_file);
if ($content === false) {
    die("<h2>Error: Could not read target file</h2><p>Could not read the contents of '$target_file'.</p>");
}

// Check if the menu fix has already been applied
if (strpos($content, $js_file) !== false) {
    die("<h2>Menu fix already applied</h2><p>The menu fix has already been applied to '$target_file'.</p>");
}

// Create script tag for the menu fix
$script_tag = '<script src="' . $js_file . '"></script>';

// Insert script tag before closing body tag
if (strpos($content, '</body>') !== false) {
    $content = str_replace('</body>', $script_tag . "\n</body>", $content);
} else {
    // If body tag not found, append to the end of the file
    $content .= "\n" . $script_tag . "\n";
}

// Write modified content back to the file
if (file_put_contents($target_file, $content) === false) {
    die("<h2>Error: Could not write to target file</h2><p>Could not write the modified content to '$target_file'.</p>");
}

// Output success message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Fix Applied</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #4CAF50;
        }
        .success {
            background-color: #dff0d8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .backup-note {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .next-steps {
            background-color: #e7f3fe;
            padding: 15px;
            border-radius: 4px;
        }
        code {
            background: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Menu Fix Applied Successfully</h1>
        
        <div class="success">
            <h2>✅ Success!</h2>
            <p>The menu fix has been successfully applied to <code><?php echo htmlspecialchars($target_file); ?></code>.</p>
        </div>
        
        <div class="backup-note">
            <h2>📁 Backup Created</h2>
            <p>A backup of the original file has been created: <code><?php echo htmlspecialchars($backup_file); ?></code></p>
            <p>If you encounter any issues, you can restore this backup file.</p>
        </div>
        
        <div class="next-steps">
            <h2>🚀 Next Steps</h2>
            <ol>
                <li>Go to <a href="table_packages.php">Table Packages</a> page</li>
                <li>Click on <strong>RESERVE NOW</strong> button for any package</li>
                <li>Click on <strong>Make Advance Order</strong> button</li>
                <li>In the modal, click on any menu category</li>
                <li>Verify that menu items are now displaying correctly</li>
            </ol>
        </div>
    </div>
</body>
</html> 