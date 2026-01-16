<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_name = $conn->real_escape_string($_POST['emp_name']);
    $staff_type_fk_id = (int) $_POST['staff_type_fk_id'];
    $shift_id = (int) $_POST['shift_id'];
    $id_card_no = $conn->real_escape_string($_POST['id_card_no']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $salary = (int) $_POST['salary'];

    $sql = "INSERT INTO staff 
            (emp_name, staff_type_fk_id, shift_id, id_card_no, address, contact_no, salary)
            VALUES 
            ('$emp_name', $staff_type_fk_id, $shift_id, '$id_card_no', '$address', '$contact_no', $salary)";

    if ($conn->query($sql)) {
        header('Location: ../index.php?staff-management');

        exit();
    } else {
        die("Error: " . $conn->error);
    }
}
?>