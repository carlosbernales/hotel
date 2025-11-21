<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotelms";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Manila');

// Make connection available globally
$conn = $connection;
$con = $connection;
?>
