<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'db.php';

// Handle file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if files were uploaded
    if (isset($_FILES["images"])) {
        $uploadDir = 'uploads/rooms/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadedFiles = [];
        $errors = [];
        
        // Loop through each uploaded file
        foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {
            $fileName = $_FILES["images"]["name"][$key];
            $fileSize = $_FILES["images"]["size"][$key];
            $fileType = $_FILES["images"]["type"][$key];
            $fileError = $_FILES["images"]["error"][$key];
            
            // Basic validation
            if ($fileError === UPLOAD_ERR_OK) {
                // Check if it's an image
                if (strpos($fileType, 'image/') === 0) {
                    // Generate unique filename
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = 'room_type_' . uniqid() . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;
                    
                    // Move file to target directory
                    if (move_uploaded_file($tmp_name, $targetPath)) {
                        $uploadedFiles[] = [
                            'original' => $fileName,
                            'new' => $newFileName,
                            'path' => $targetPath
                        ];
                    } else {
                        $errors[] = "Failed to move uploaded file: $fileName";
                    }
                } else {
                    $errors[] = "Invalid file type: $fileName";
                }
            } else {
                $errors[] = "Upload error for file: $fileName";
            }
        }
        
        // Display results
        echo "<h2>Upload Results:</h2>";
        
        if (!empty($uploadedFiles)) {
            echo "<h3>Successfully uploaded files:</h3>";
            echo "<ul>";
            foreach ($uploadedFiles as $file) {
                echo "<li>Original: " . htmlspecialchars($file['original']) . 
                     " → New: " . htmlspecialchars($file['new']) . "</li>";
            }
            echo "</ul>";
        }
        
        if (!empty($errors)) {
            echo "<h3>Errors:</h3>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
    }
}

// Add back link
echo "<p><a href='verify_images.php'>← Back to verification page</a></p>";
?> 