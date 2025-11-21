<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_name = trim($_POST['display_name']);

    if (!empty($display_name)) {
        $stmt = $conn->prepare("INSERT INTO menu_categories (name, display_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $display_name, $display_name);

        if ($stmt->execute()) {
            header("Location: ../../Admin/index.php?cafe_management");
            exit;
        } else {
            echo "Error inserting category: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Category name cannot be empty.";
    }
}

$conn->close();
?>