<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$type = $_POST['type'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/qr_codes/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file upload based on type
    if ($type === 'gcash') {
        $file = $_FILES['gcash_qr'];
        $accountName = $_POST['gcash_name'] ?? '';
        $accountNumber = $_POST['gcash_number'] ?? '';
        $fieldPrefix = 'gcash';
    } elseif ($type === 'maya') {
        $file = $_FILES['maya_qr'];
        $accountName = $_POST['maya_name'] ?? '';
        $accountNumber = $_POST['maya_number'] ?? '';
        $fieldPrefix = 'maya';
    } else {
        throw new Exception('Invalid payment type');
    }

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }

    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = $type . '_qr_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $newFilename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Update database
    $sql = "INSERT INTO payment_qr_codes ({$fieldPrefix}_qr, {$fieldPrefix}_name, {$fieldPrefix}_number, updated_at) 
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            {$fieldPrefix}_qr = VALUES({$fieldPrefix}_qr),
            {$fieldPrefix}_name = VALUES({$fieldPrefix}_name),
            {$fieldPrefix}_number = VALUES({$fieldPrefix}_number),
            updated_at = NOW()";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("sss", $targetPath, $accountName, $accountNumber);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update database: ' . $stmt->error);
    }

    // Delete old QR code file if exists
    $oldQrQuery = "SELECT {$fieldPrefix}_qr FROM payment_qr_codes WHERE id != LAST_INSERT_ID() LIMIT 1";
    $oldQrResult = $con->query($oldQrQuery);
    if ($oldQrResult && $oldQr = $oldQrResult->fetch_assoc()) {
        $oldFile = $oldQr["{$fieldPrefix}_qr"];
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    $response['success'] = true;
    $response['message'] = 'QR Code updated successfully';

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response); 