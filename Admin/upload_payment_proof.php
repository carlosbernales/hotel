<?php
session_start();
// Set content type to JSON
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'file_path' => ''
];

try {
    // Check if file was uploaded
    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
        // If no file was uploaded but it's optional, just return success with empty path
        if ($_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE) {
            $response['success'] = true;
            $response['message'] = 'No file uploaded, but continuing with booking.';
            $response['file_path'] = '';
            echo json_encode($response);
            exit;
        }
        throw new Exception('No file uploaded or upload error occurred: ' . ($_FILES['payment_proof']['error'] ?? 'No file'));
    }
    
    $file = $_FILES['payment_proof'];
    
    // Debug log - use print_r instead of json_encode for arrays
    error_log("Received file: " . print_r($file, true));
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, GIF images and PDF files are allowed.');
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        throw new Exception('File size exceeds the maximum limit of 5MB.');
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = 'uploads/payment_proofs/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }
    }
    
    // Generate unique filename
    $filename = 'payment_' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest') . '_' . date('YmdHis') . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_path = $upload_dir . $filename;
    
    // Debug log before moving file
    error_log("Attempting to move file from {$file['tmp_name']} to {$file_path}");
    error_log("Current directory: " . getcwd());
    error_log("Upload directory exists: " . (file_exists($upload_dir) ? 'Yes' : 'No'));
    error_log("Upload directory is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No'));
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        $error = error_get_last();
        error_log("Failed to move file. Error: " . print_r($error, true));
        throw new Exception('Failed to move uploaded file. Please check server permissions.');
    }
    
    // Debug log after moving file
    error_log("File uploaded successfully to: " . $file_path);
    error_log("File exists at destination: " . (file_exists($file_path) ? 'Yes' : 'No'));
    
    // Return success response
    $response['success'] = true;
    $response['message'] = 'File uploaded successfully.';
    $response['file_path'] = $filename; // Return just the filename
    
} catch (Exception $e) {
    error_log("Upload error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit; 