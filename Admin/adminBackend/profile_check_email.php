<?php
session_start();
include '../adminBackend/mydb.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

$userId = intval($_SESSION['user_id'] ?? 0);
$email = trim($_POST['email'] ?? '');

if (!$userId || !$email) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM userss WHERE email = ? AND id != ?");
if (!$stmt) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt->bind_param("si", $email, $userId);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;

echo json_encode(['exists' => $exists]);
exit;
