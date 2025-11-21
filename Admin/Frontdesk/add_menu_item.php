<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];

    if (empty($item_name)) {
        $_SESSION['error_message'] = "Item name is required.";
    } else {
        $query = "INSERT INTO package_menu_items (item_name) VALUES (?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $item_name);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Menu item added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding menu item: " . $con->error;
        }
    }
}

header('Location: manage_package_options.php');
exit(); 