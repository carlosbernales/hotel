<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['id'])) {
        die("Menu item ID is missing.");
    }
    $menu_item_id = intval($_GET['id']);

    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);

    $sql = "INSERT INTO menu_items_addons (menu_item_id, name, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isd", $menu_item_id, $name, $price);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?cafe_management");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>