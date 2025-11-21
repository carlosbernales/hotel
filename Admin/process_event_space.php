<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $space_name = $_POST['space_name'];
        $Type = $_POST['Type'];
        $category = $_POST['category'];
        $capacity = $_POST['capacity'];
        $price_per_hour = $_POST['price_per_hour'];
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $status = $_POST['status'];

        // Debug output
        error_log("Received POST data: " . print_r($_POST, true));
        error_log("Received FILES data: " . print_r($_FILES, true));
        
        // Handle amenities
        $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : '[]';
        // If amenities is already a JSON string, use it as is, otherwise encode it
        $amenities_json = is_string($amenities) ? $amenities : json_encode($amenities);

        // Create uploads directory if it doesn't exist
        $target_dir = "uploads/spaces/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Handle main image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid('space_') . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                throw new Exception('File is not an image.');
            }

            // Check file size (5MB limit)
            if ($_FILES['image']['size'] > 5000000) {
                throw new Exception('File is too large. Maximum size is 5MB.');
            }

            // Allow certain file formats
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                throw new Exception('Failed to upload image.');
            }
        }

        // Handle gallery images upload
        $gallery_images = [];
        if (isset($_FILES['gallery_images'])) {
            $total = count($_FILES['gallery_images']['name']);
            
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_extension = strtolower(pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION));
                    $new_filename = uniqid('gallery_') . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;

                    // Check if image file is actual image
                    $check = getimagesize($_FILES['gallery_images']['tmp_name'][$i]);
                    if ($check === false) {
                        continue; // Skip invalid images
                    }

                    // Check file size
                    if ($_FILES['gallery_images']['size'][$i] > 5000000) {
                        continue; // Skip large files
                    }

                    // Check file type
                    if (!in_array($file_extension, $allowed_types)) {
                        continue; // Skip invalid file types
                    }

                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_file)) {
                        $gallery_images[] = $target_file;
                    }
                }
            }
        }

        // Convert gallery images array to JSON
        $gallery_images_json = json_encode($gallery_images);

        // Insert into database
        $sql = "INSERT INTO event_spaces (
            space_name,
            Type,
            category,
            capacity,
            price_per_hour,
            description,
            amenities,
            image_path,
            gallery_images,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param(
            "sssiisssss",
            $space_name,
            $Type,
            $category,
            $capacity,
            $price_per_hour,
            $description,
            $amenities_json,
            $image_path,
            $gallery_images_json,
            $status
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Event space added successfully!'
        ]);

    } catch (Exception $e) {
        error_log("Error in process_event_space.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
