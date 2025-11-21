<?php
require_once 'db.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $query = "DELETE FROM announcements WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = 'Announcement deleted successfully';
        } else {
            $response['message'] = 'Error deleting announcement';
        }
    } else {
        $title = $_POST['title'];
        $message = $_POST['message'];
        $type = $_POST['type'];
        $valid_until = $_POST['valid_until'];
        
        // Handle QR code uploads if present
        $gcash_qr_path = null;
        $maya_qr_path = null;
        
        if (isset($_FILES['gcash_qr']) && $_FILES['gcash_qr']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/announcements/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $gcash_qr_path = $uploadDir . 'gcash_' . time() . '_' . basename($_FILES['gcash_qr']['name']);
            move_uploaded_file($_FILES['gcash_qr']['tmp_name'], $gcash_qr_path);
        }
        
        if (isset($_FILES['maya_qr']) && $_FILES['maya_qr']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/announcements/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $maya_qr_path = $uploadDir . 'maya_' . time() . '_' . basename($_FILES['maya_qr']['name']);
            move_uploaded_file($_FILES['maya_qr']['tmp_name'], $maya_qr_path);
        }
        
        $query = "INSERT INTO announcements (title, message, type, valid_until, gcash_qr, maya_qr, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $title, $message, $type, $valid_until, $gcash_qr_path, $maya_qr_path);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = 'Announcement posted successfully';
        } else {
            $response['message'] = 'Error posting announcement: ' . mysqli_error($con);
        }
    }
}

echo json_encode($response);
?>
