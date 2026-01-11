<?php
$host = "localhost";
$database = "u763377220_hotelms";
$username = "u763377220_casaestela";
$password = "Casaestela@2025";


$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>