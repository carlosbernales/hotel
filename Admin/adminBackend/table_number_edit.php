<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // current record ID
    $table_number = intval($_POST['table_number']);
    $status = $conn->real_escape_string($_POST['status']);

    $check_sql = "SELECT id FROM table_number WHERE table_number = ? AND id != ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $table_number, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    $message = '';

    if ($stmt_check->num_rows > 0) {
        $message = 'Table number already exists.';
    } else {
        $update_sql = "UPDATE table_number SET table_number = ?, status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("isi", $table_number, $status, $id);

        if ($stmt_update->execute()) {
            $message = 'Table number updated successfully!';
        } else {
            $message = 'Error: ' . $stmt_update->error;
        }

        $stmt_update->close();
    }

    $stmt_check->close();

    echo "<script>
            alert('$message');
            window.location.href='../../Admin/index.php?table_management';
          </script>";
    exit;
}

$conn->close();
?>