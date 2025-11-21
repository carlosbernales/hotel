<?php
// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $host = 'localhost';
    $username = "root";
    $password = "";
    $dbname = "hotelms"; // Make sure this matches your database name


    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // For backwards compatibility
    $con = mysqli_connect($host, $username, $password, $dbname);
    if (!$con) {
        throw new Exception("mysqli connection failed: " . mysqli_connect_error());
    }

    // Set charset to ensure proper encoding
    mysqli_set_charset($con, "utf8mb4");
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}


?>