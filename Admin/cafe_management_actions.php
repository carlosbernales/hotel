<?php
require_once 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle different actions
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'add_category':
            $name = trim($_POST['name'] ?? '');
            $display_name = trim($_POST['display_name'] ?? $name); // Use name as display_name if not provided
            
            if (empty($name)) {
                throw new Exception('Category name is required');
            }
            
            // Check if category name already exists
            $check_sql = "SELECT id FROM menu_categories WHERE name = ?";
            $check_stmt = mysqli_prepare($con, $check_sql);
            mysqli_stmt_bind_param($check_stmt, 's', $name);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                throw new Exception('A category with this name already exists');
            }
            
            // Insert new category
            $insert_sql = "INSERT INTO menu_categories (name, display_name) VALUES (?, ?)";
            $stmt = mysqli_prepare($con, $insert_sql);
            mysqli_stmt_bind_param($stmt, 'ss', $name, $display_name);
            
            if (mysqli_stmt_execute($stmt)) {
                $response = [
                    'success' => true,
                    'message' => 'Category added successfully',
                    'id' => mysqli_insert_id($con)
                ];
            } else {
                throw new Exception('Failed to add category: ' . mysqli_error($con));
            }
            break;
            
        // Add other actions here as needed
        
        default:
            $response['message'] = 'Invalid action';
            break;
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>