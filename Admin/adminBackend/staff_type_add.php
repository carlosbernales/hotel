<?php
include '../adminBackend/mydb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['staff_type']) || empty(trim($_POST['staff_type']))) {
        die("Staff type name is required.");
    }

    $staff_type = trim($_POST['staff_type']);

    $stmt = $conn->prepare("INSERT INTO staff_type (staff_type) VALUES (?)");
    $stmt->bind_param("s", $staff_type);

    if (!$stmt->execute()) {
        die("Insert failed: " . $stmt->error);
    }
}

header("Location: ../index.php?staff-management");
exit;
