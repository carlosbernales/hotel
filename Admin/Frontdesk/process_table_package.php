<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $package_name = $_POST['package_name'] ?? '';
        $capacity = $_POST['capacity'] ?? 0;
        $price = $_POST['price'] ?? 0;
        $available_tables = $_POST['available_tables'] ?? 0;
        $description = $_POST['description'] ?? '';
        $id = $_POST['id'] ?? null;

        // Validate required fields
        if (empty($package_name)) {
            throw new Exception("Package name is required");
        }
        if ($capacity <= 0) {
            throw new Exception("Capacity must be greater than 0");
        }
        if ($available_tables < 0) {
            throw new Exception("Available tables cannot be negative");
        }

        // Handle image upload if present
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/table_packages/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            // Check if image file is a actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check === false) {
                throw new Exception('File is not an image');
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            } else {
                throw new Exception('Failed to upload image');
            }
        }

        if ($id) {
            // Update existing package
            $sql = "UPDATE table_packages SET 
                    package_name = ?,
                    capacity = ?,
                    price = ?,
                    available_tables = ?,
                    description = ?";
            
            // Add image to update only if new image was uploaded
            if ($image_path) {
                $sql .= ", image_path = ?";
            }
            
            $sql .= " WHERE id = ?";

            $stmt = $con->prepare($sql);

            if ($image_path) {
                $stmt->bind_param('siidssi', 
                    $package_name,
                    $capacity,
                    $price,
                    $available_tables,
                    $description,
                    $image_path,
                    $id
                );
            } else {
                $stmt->bind_param('siidsi', 
                    $package_name,
                    $capacity,
                    $price,
                    $available_tables,
                    $description,
                    $id
                );
            }
        } else {
            // Insert new package
            $sql = "INSERT INTO table_packages (
                package_name, 
                capacity,
                price,
                available_tables,
                description,
                image_path,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, 'active')";

            $stmt = $con->prepare($sql);
            $stmt->bind_param('siidss', 
                $package_name,
                $capacity,
                $price,
                $available_tables,
                $description,
                $image_path
            );
        }

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => $id ? 'Table package updated successfully' : 'Table package added successfully'
            ]);
        } else {
            throw new Exception($stmt->error);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
