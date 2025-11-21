<?php
require_once "db.php"; // Include the database connection file

// Query to fetch customer information from the userss table
$sql = "SELECT name, email, is_verified, created_at FROM userss WHERE role = 'customer'";
$stmt = $con->query($sql);
?> 