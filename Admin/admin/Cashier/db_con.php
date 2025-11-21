<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "hotelms"; // Replace with your actual database name

$con = mysqli_connect($host, $username, $password, $database);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>