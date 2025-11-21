<?php
// This script will restore your index.php file with a basic admin system entry point
// Run this once to restore your index.php, then you can delete this file

// Security check - only allow from your IP
$allowed_ips = array('180.195.201.183');
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    echo "Access denied.";
    exit;
}

// Basic index.php content for an admin system
$new_index_content = '<?php
// Main Admin Entry Point
session_start();

// Include essential files
require_once "session_check.php";
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"]) && !isset($_GET["login"])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Get page parameter
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$table_packages = isset($_GET["table_packages"]) ? true : false;
$dashboard = isset($_GET["dashboard"]) ? true : false;
$cafe_management = isset($_GET["cafe_management"]) ? true : false;

// Include header
include "header.php";

// Include navigation
include "navigation.php";

// Include content based on page parameter
if ($table_packages) {
    include "table_packages.php";
} elseif ($dashboard) {
    include "dashboard.php";
} elseif ($cafe_management) {
    include "cafe_management.php";
} elseif ($page != "") {
    if (file_exists($page . ".php")) {
        include $page . ".php";
    } else {
        echo "<div class=\'alert alert-danger\'>Page not found.</div>";
    }
} else {
    // Default page
    include "dashboard.php";
}

// Include footer
include "footer.php";
?>';

// Try to backup the current index.php first
if (file_exists('index.php')) {
    copy('index.php', 'index.php.bak');
    echo "<p>Created backup of current index.php as index.php.bak</p>";
}

// Write the new index.php file
if (file_put_contents('index.php', $new_index_content)) {
    echo "<div style='padding: 20px; background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; margin: 20px 0;'>";
    echo "<h2>Success!</h2>";
    echo "<p>index.php has been restored with basic admin functionality.</p>";
    echo "<p>You should now be able to access your admin panel again.</p>";
    echo "<p><a href='index.php' style='padding: 10px 15px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 4px;'>Try the Admin Panel Now</a></p>";
    echo "</div>";
} else {
    echo "<div style='padding: 20px; background-color: #f2dede; border: 1px solid #ebccd1; color: #a94442; margin: 20px 0;'>";
    echo "<h2>Error</h2>";
    echo "<p>Could not write to index.php. Please check file permissions.</p>";
    echo "</div>";
}

// Show the admin emergency access link
echo "<p><a href='admin_emergency.php'>Return to Emergency Admin</a></p>";
?> 