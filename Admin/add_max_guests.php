<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS package_max_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $capacity = $_POST['capacity'];

    if (empty($capacity)) {
        $_SESSION['error_message'] = "Capacity is required.";
    } else {
        $query = "INSERT INTO package_max_guests (capacity) VALUES (?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $capacity);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Maximum guests option added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding maximum guests option: " . $con->error;
        }
    }
}

header('Location: manage_package_options.php');
exit(); 