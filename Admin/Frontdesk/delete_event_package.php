<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

// Check if ID was provided
if (!isset($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No package ID provided']);
    exit();
}

$id = (int)$_POST['id'];

// Delete the package
$query = "DELETE FROM event_packages WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $id);

header('Content-Type: application/json');
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $con->error]);
} 