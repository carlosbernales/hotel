<?php
include '../adminBackend/mydb.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $query = $conn->prepare("SELECT active FROM contact_info WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    if ($result) {
        $newStatus = $result['active'] ? 0 : 1;

        $update = $conn->prepare("UPDATE contact_info SET active = ? WHERE id = ?");
        $update->bind_param("ii", $newStatus, $id);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'active' => $newStatus]);
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