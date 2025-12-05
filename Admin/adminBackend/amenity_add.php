<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $item_type = trim($_POST['item_type']);
    $price = trim($_POST['price']);

    if (empty($item_type) || empty($price)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("INSERT INTO beds (item_type, price) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $item_type, $price); // s = string, i = integer

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?amenity_list");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>