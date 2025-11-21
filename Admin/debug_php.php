<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "<p>" . phpversion() . "</p>";

// Check if files exist
echo "<h2>File Checks</h2>";
$files = ['fix_table_orders.php', 'run_table_orders_fix.php', 'header.php', 'sidebar.php'];
echo "<ul>";
foreach ($files as $file) {
    echo "<li>" . $file . ": " . (file_exists($file) ? "Exists" : "Not Found") . "</li>";
}
echo "</ul>";

// Check if session is working
echo "<h2>Session</h2>";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if role is set
echo "<h2>Role Check</h2>";
if (isset($_SESSION['role'])) {
    echo "<p>Role: " . $_SESSION['role'] . "</p>";
} else {
    echo "<p>Role is not set in session</p>";
}

// Check if we can include files
echo "<h2>Include Test</h2>";
try {
    echo "<p>Attempting to include 'db.php'...</p>";
    include_once 'db.php';
    echo "<p style='color:green'>Successfully included db.php</p>";
    
    if (isset($con)) {
        echo "<p style='color:green'>Database connection exists</p>";
    } else {
        echo "<p style='color:red'>Database connection variable not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Display phpinfo
echo "<h2>PHP Info</h2>";
phpinfo();
?> 