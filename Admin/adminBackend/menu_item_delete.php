<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_GET['id'])) {
        die("Missing ID");
    }

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT image_path FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    $deleteAddons = $conn->prepare("DELETE FROM menu_items_addons WHERE menu_item_id = ?");
    $deleteAddons->bind_param("i", $id);
    $deleteAddons->execute();

    $deleteItem = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $deleteItem->bind_param("i", $id);
    $deleteItem->execute();

    if (!empty($image_path)) {
        $fullPath = "../../Admin/adminBackend/menu_item_images/" . $image_path;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    header("Location: ../../Admin/index.php?cafe_management");
    exit;
}
?>