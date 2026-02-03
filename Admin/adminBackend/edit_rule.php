<?php
include '../adminBackend/mydb.php';


$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $hotel_name = $conn->real_escape_string($_POST['hotel_name']);
    $title = $conn->real_escape_string($_POST['title']);
    $rule_text = $conn->real_escape_string($_POST['rule_text']);
    $display_order = intval($_POST['display_order']);

    $sql = "UPDATE terms_and_conditions 
            SET hotel_name='$hotel_name', title='$title', rule_text='$rule_text', display_order=$display_order
            WHERE id=$id";

    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Offer updated successfully!';
    } else {
        $response['message'] = 'Database error: ' . $conn->error;
    }
}

$conn->close();
echo json_encode($response);
