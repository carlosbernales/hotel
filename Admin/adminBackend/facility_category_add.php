<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $display_order = intval($_POST['display_order']);
    $active = intval($_POST['active']);

    if ($name === '' || $display_order === '') {
        header("Location: ../index.php?facilities=missing_fields");
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO facility_categories (name, display_order, active)
        VALUES (?, ?, ?)
    ");

    if ($stmt) {
        $stmt->bind_param("sii", $name, $display_order, $active);

        if ($stmt->execute()) {
            header("Location: ../index.php?facilities=category_added");
            exit;
        } else {
            echo "Error executing query.";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
}
?>