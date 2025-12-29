<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $display_order = intval($_POST['display_order']);
    $active = intval($_POST['active']);

    if (empty($name)) {
        die('Facility name is required.');
    }

    $stmt = $conn->prepare("INSERT INTO facilities (category_id, name, display_order, active) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $category_id, $name, $display_order, $active);

    if ($stmt->execute()) {
        header("Location: ../index.php?facilities");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>