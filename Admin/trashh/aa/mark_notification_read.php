<?php
session_start();
require_once 'db_con.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['notification_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$notificationId = $_POST['notification_id'];
$userId = $_SESSION['user_id'];

$query = "UPDATE notifications SET is_read = 1 
          WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $notificationId, $userId);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?> 