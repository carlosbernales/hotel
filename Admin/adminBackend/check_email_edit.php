<?php
include '../adminBackend/mydb.php';

if (isset($_POST['email']) && isset($_POST['id'])) {
    $email = trim($_POST['email']);
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT id FROM userss WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "exists";
    } else {
        echo "available";
    }

    $stmt->close();
}
?>