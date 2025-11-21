<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotelms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'casaestelaboutiquehotelandcafe@gmail.com'); // Use the actual email
define('SMTP_PASSWORD', 'vcagmikptjlcqqrl'); // Use the actual password
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', 'Casa Estela Botique Hotel and Cafe');

// Other Configuration
define('SITE_URL', 'http://localhost/Capstone');
define('DEBUG_MODE', true);

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Time Zone
date_default_timezone_set('Asia/Manila');
