<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $table_number = intval($_POST['table_number']);
    $status = $conn->real_escape_string($_POST['status']);
    $table_type_fk_id = intval($_POST['table_type_fk_id']);

    $check_sql = "SELECT id FROM table_number WHERE table_number = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("i", $table_number);
    $stmt_check->execute();
    $stmt_check->store_result();

    $message = '';

    if ($stmt_check->num_rows > 0) {
        $message = 'Table number already exists.';
    } else {

        $sql = "INSERT INTO table_number (table_number, status, table_type_fk_id) 
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $table_number, $status, $table_type_fk_id);

        if ($stmt->execute()) {
            $message = 'Table number added successfully!';
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
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