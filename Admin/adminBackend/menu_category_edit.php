<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id'])) {
    die("No category ID provided.");
}

$cat_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_name = $conn->real_escape_string($_POST['display_name']);

    $sql = "UPDATE menu_categories SET name='$display_name', display_name='$display_name' WHERE id=$cat_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../Admin/index.php?cafe_management");

        exit;
    } else {
        echo "Error updating category: " . $conn->error;
    }
}
?>