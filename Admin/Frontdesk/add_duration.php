<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS package_durations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hours INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hours = $_POST['hours'];

    if (empty($hours)) {
        $_SESSION['error_message'] = "Hours is required.";
    } else {
        $query = "INSERT INTO package_durations (hours) VALUES (?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $hours);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Duration option added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding duration option: " . $con->error;
        }
    }
}

header('Location: manage_package_options.php');
exit(); 