<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'casaestelaboutiquehotelandcafe@gmail.com');
define('SMTP_PASSWORD', 'gryu ejyi smxn kkle');
define('SMTP_PORT', 465);  // Port for explicit SSL
define('SMTP_ENCRYPTION', 'ssl');  // Using SSL instead of TLS
define('SMTP_FROM_NAME', 'Casa Estela Boutique Hotel');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_error.log');

// Time Zone
date_default_timezone_set('Asia/Manila');

// Define the base paths
define('BASE_PATH', dirname(__FILE__));
define('DB_PATH', BASE_PATH . '/db.php');

// Database configuration
$host = "localhost";
$username = "u429956055_admin";  // Your hosting username
$password = "Admin@123";         // Your hosting password
$database = "u429956055_hotelms"; // Your hosting database name

?> 