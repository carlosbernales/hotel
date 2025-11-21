<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Print the POST data
    error_log("POST data: " . print_r($_POST, true));
    
    $table_name = $_POST['table_name'];
    $table_type = $_POST['table_type'];
    $category = $_POST['category'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === 0) {
        $upload_dir = 'uploads/tables/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            error_log("File upload failed: " . print_r($_FILES['image_path']['error'], true));
        }
    }
    
    try {
        // Insert into database
        $query = "INSERT INTO dining_tables (table_name, table_type, category, capacity, price, status, image_path) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }
        
        $stmt->bind_param("sssidss", $table_name, $table_type, $category, $capacity, $price, $status, $image_path);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $_SESSION['success_message'] = "Table added successfully!";
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error_message'] = "Error adding table: " . $e->getMessage();
    }
    
    header('Location: table_management.php');
    exit();
} else {
    error_log("Not a POST request");
}
?>
