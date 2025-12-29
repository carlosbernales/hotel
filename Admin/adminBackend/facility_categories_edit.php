<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {

    $id = intval($_GET['id']);
    $name = trim($_POST['name']);
    $display_order = intval($_POST['display_order']);
    $active = intval($_POST['active']);

    $stmt = $conn->prepare("
        UPDATE facility_categories
        SET name = ?, display_order = ?, active = ?
        WHERE id = ?
    ");

    $stmt->bind_param("siii", $name, $display_order, $active, $id);

    if ($stmt->execute()) {
        header("Location: ../index.php?facilities");
        exit;
    } else {
        echo "Error updating category.";
    }
}
