<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid ID.");
    }
    $id = intval($_GET['id']);

    $item_type = trim($_POST['item_type']);
    $price = trim($_POST['price']);

    if (empty($item_type) || empty($price)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("UPDATE beds SET item_type = ?, price = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sii", $item_type, $price, $id); // s = string, i = integer

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?amenity_list");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>