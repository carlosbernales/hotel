<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id'])) {
    die("Invalid ID");
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $staff_type = $_POST['staff_type'];

    $stmt = $conn->prepare("UPDATE staff_type SET staff_type=? WHERE id=?");
    $stmt->bind_param("si", $staff_type, $id);

    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
}

header("Location: ../index.php?staff-management");
exit;

