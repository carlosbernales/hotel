<?php
require_once 'db_con.php';

header('Content-Type: application/json');

$response = ['exists' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['email'])) {
        $email = $data['email'];
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM userss WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $response['exists'] = true;
            $response['message'] = 'Email already exists';
            echo json_encode($response);
            exit;
        }
    }
    
    if (isset($data['phone'])) {
        $phone = $data['phone'];
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM userss WHERE contact_number = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $response['exists'] = true;
            $response['message'] = 'Phone number already exists';
            echo json_encode($response);
            exit;
        }
    }
}

echo json_encode($response);
?> 