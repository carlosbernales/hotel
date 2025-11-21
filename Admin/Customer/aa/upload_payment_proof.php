<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the upload directory
$uploadDir = '../../uploads/payment_proofs/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file was uploaded
if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => 'No file uploaded or upload error occurred',
        'error_code' => $_FILES['payment_proof']['error'] ?? 'No file'
    ]);
    exit;
}

$file = $_FILES['payment_proof'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Only JPG, PNG, and PDF files are allowed.'
    ]);
    exit;
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$uniqueName = uniqid('payment_') . '.' . $extension;
$uploadPath = $uploadDir . $uniqueName;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'file_path' => 'uploads/payment_proofs/' . $uniqueName // Return relative path for database storage
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to move uploaded file',
        'error' => error_get_last()
    ]);
} 