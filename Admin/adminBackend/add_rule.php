<?php
include '../adminBackend/mydb.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_name = $conn->real_escape_string($_POST['hotel_name']);
    $title = $conn->real_escape_string($_POST['title']);
    $rule_text = $conn->real_escape_string($_POST['rule_text']);
    $display_order = intval($_POST['display_order']);

    $sql = "INSERT INTO terms_and_conditions (hotel_name, title, rule_text, display_order, is_active) 
            VALUES ('$hotel_name', '$title', '$rule_text', $display_order, 1)";

    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Offer added successfully!';
    } else {
        $response['message'] = 'Database error: ' . $conn->error;
    }
}

$conn->close();
echo json_encode($response);
