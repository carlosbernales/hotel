<?php
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['image'], $data['filename'])) {
    error_log("No image data or filename received");
    echo "invalid";
    exit;
}

$img = $data['image'];
$filename = basename($data['filename']); // sanitize

$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$decoded = base64_decode($img);

$folder = __DIR__ . '/room_booking_receipt_images/';
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$path = $folder . $filename;

if (file_put_contents($path, $decoded)) {
    error_log("Saved receipt image to: $path");
    echo "success";
} else {
    error_log("Failed to write receipt image to: $path");
    echo "error";
}
