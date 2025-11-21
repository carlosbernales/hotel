<?php
require_once 'db.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    $uploadDir = 'uploads/qr_codes/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $gcash_qr_path = null;
    $maya_qr_path = null;

    // Handle GCash QR upload
    if (isset($_FILES['gcash_qr']) && $_FILES['gcash_qr']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['gcash_qr']['name'], PATHINFO_EXTENSION);
        $gcash_qr_path = $uploadDir . 'gcash_qr.' . $extension;
        
        // Delete old file if exists
        if (file_exists($gcash_qr_path)) {
            unlink($gcash_qr_path);
        }
        
        if (!move_uploaded_file($_FILES['gcash_qr']['tmp_name'], $gcash_qr_path)) {
            throw new Exception('Failed to upload GCash QR code');
        }
    }

    // Handle Maya QR upload
    if (isset($_FILES['maya_qr']) && $_FILES['maya_qr']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['maya_qr']['name'], PATHINFO_EXTENSION);
        $maya_qr_path = $uploadDir . 'maya_qr.' . $extension;
        
        // Delete old file if exists
        if (file_exists($maya_qr_path)) {
            unlink($maya_qr_path);
        }
        
        if (!move_uploaded_file($_FILES['maya_qr']['tmp_name'], $maya_qr_path)) {
            throw new Exception('Failed to upload Maya QR code');
        }
    }

    // Update database
    $query = "INSERT INTO payment_settings (gcash_qr, maya_qr, updated_at) 
              VALUES (?, ?, NOW())
              ON DUPLICATE KEY UPDATE 
              gcash_qr = COALESCE(?, gcash_qr),
              maya_qr = COALESCE(?, maya_qr),
              updated_at = NOW()";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssss", 
        $gcash_qr_path, 
        $maya_qr_path,
        $gcash_qr_path,
        $maya_qr_path
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update database: ' . mysqli_error($con));
    }

    $response['success'] = true;
    $response['message'] = 'QR codes updated successfully';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response); 