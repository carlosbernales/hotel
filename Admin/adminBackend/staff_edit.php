<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $emp_name = $conn->real_escape_string($_POST['emp_name']);
    $staff_type_fk_id = (int) $_POST['staff_type_fk_id'];
    $shift_id = (int) $_POST['shift_id'];
    $id_card_no = $conn->real_escape_string($_POST['id_card_no']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $salary = (int) $_POST['salary'];

    $sql = "UPDATE staff SET 
                emp_name='$emp_name',
                staff_type_fk_id=$staff_type_fk_id,
                shift_id=$shift_id,
                id_card_no='$id_card_no',
                address='$address',
                contact_no='$contact_no',
                salary=$salary,
                updated_at=CURRENT_TIMESTAMP
            WHERE id=$id";

    if ($conn->query($sql)) {
        header('Location: ../index.php?staff-management');
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}
?>