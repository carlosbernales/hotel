<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_text = trim($_POST['display_text']);
    $link = trim($_POST['link']);
    $display_order = intval($_POST['display_order']);
    $active = intval($_POST['active']);
    $is_external = intval($_POST['is_external']);

    $stmt = $conn->prepare("INSERT INTO contact_info (display_text, link, display_order, active, is_external) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $display_text, $link, $display_order, $active, $is_external);

    if ($stmt->execute()) {
        header("Location: ../index.php?contact_management");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>