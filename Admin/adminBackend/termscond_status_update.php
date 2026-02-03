<?php
include '../adminBackend/mydb.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("SELECT is_active FROM terms_and_conditions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $newStatus = $result['is_active'] ? 0 : 1;

        $update = $conn->prepare("UPDATE terms_and_conditions SET is_active = ? WHERE id = ?");
        $update->bind_param("ii", $newStatus, $id);

        if ($update->execute()) {
            echo json_encode(['success' => true, 'is_active' => $newStatus]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Update failed']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Record not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>