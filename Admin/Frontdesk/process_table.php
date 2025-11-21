<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'add';
    
    if ($action === 'add') {
        $table_name = $_POST['table_name'];
        $table_type = $_POST['table_type'];
        $capacity = $_POST['capacity'];
        $price = $_POST['price'];
        $status = isset($_POST['status']) ? $_POST['status'] : 'available';
        
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['table_image']) && $_FILES['table_image']['error'] === 0) {
            $upload_dir = 'uploads/tables/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['table_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('table_') . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['table_image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            }
        }
        
        $query = "INSERT INTO dining_tables (table_name, table_type, capacity, price, status, image_path) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssidss", $table_name, $table_type, $capacity, $price, $status, $image_path);
        
        if ($stmt->execute()) {
            header('Location: table_management.php?success=1');
        } else {
            header('Location: table_management.php?error=1');
        }
        
    } elseif ($action === 'edit') {
        $table_id = $_POST['table_id'];
        $table_name = $_POST['table_name'];
        $table_type = $_POST['table_type'];
        $capacity = $_POST['capacity'];
        $price = $_POST['price'];
        $status = $_POST['status'];
        
        // Handle image upload for edit
        if (isset($_FILES['table_image']) && $_FILES['table_image']['error'] === 0) {
            $upload_dir = 'uploads/tables/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['table_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('table_') . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['table_image']['tmp_name'], $target_path)) {
                // Delete old image if exists
                $query = "SELECT image_path FROM dining_tables WHERE id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $table_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if (!empty($row['image_path']) && file_exists($row['image_path'])) {
                        unlink($row['image_path']);
                    }
                }
                
                $query = "UPDATE dining_tables SET table_name = ?, table_type = ?, capacity = ?, 
                         price = ?, status = ?, image_path = ? WHERE id = ?";
                $stmt = $con->prepare($query);
                $stmt->bind_param("ssidssi", $table_name, $table_type, $capacity, $price, 
                                $status, $target_path, $table_id);
            }
        } else {
            $query = "UPDATE dining_tables SET table_name = ?, table_type = ?, capacity = ?, 
                     price = ?, status = ? WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssidsi", $table_name, $table_type, $capacity, $price, 
                            $status, $table_id);
        }
        
        if ($stmt->execute()) {
            header('Location: table_management.php?success=1');
        } else {
            header('Location: table_management.php?error=1');
        }
    } elseif ($action === 'delete') {
        $table_id = $_POST['table_id'];
        
        // Delete the image file first
        $query = "SELECT image_path FROM dining_tables WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $table_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['image_path']) && file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }
        }
        
        // Then delete the record
        $query = "DELETE FROM dining_tables WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $table_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $con->error]);
        }
        exit;
    }
}

// Redirect back if no action was taken
header('Location: table_management.php');
