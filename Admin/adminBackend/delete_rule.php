<?php
include 'mydb.php';
header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM terms_and_conditions WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Rule deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete rule.']);
}

$stmt->close();
$conn->close();