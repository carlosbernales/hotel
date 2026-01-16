<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM userss WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../../Admin/index.php?users");

    exit();
} else {
    echo "Error deleting record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>