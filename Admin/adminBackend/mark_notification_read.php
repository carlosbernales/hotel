<?php
include '../adminBackend/mydb.php';

if (isset($_POST['notif_id'])) {
    $notifId = intval($_POST['notif_id']);

    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notifId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>