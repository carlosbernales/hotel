<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $item_type = trim($_POST['name']);

    if (empty($item_type)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("INSERT INTO amenities (name) VALUES (?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $item_type);
    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?inclusion");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>