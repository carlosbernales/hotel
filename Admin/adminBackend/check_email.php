<?php
include '../adminBackend/mydb.php';

$email = trim($_POST['email']);

$stmt = $conn->prepare("SELECT id FROM userss WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

echo $stmt->num_rows > 0 ? "exists" : "ok";
