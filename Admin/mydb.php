<?php
$host = "localhost";
$username = "u763377220_casaestela";
$password = "Casaestela@2025";
$database = "u763377220_hotelms";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
