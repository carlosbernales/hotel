<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid ID.");
    }
    $id = intval($_GET['id']);

    $item_type = trim($_POST['item_type']);

    if (empty($item_type)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("UPDATE amenities SET name = ? WHERE amenity_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $item_type, $id); 

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?inclusion");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>