<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => ''];

    try {
        switch ($action) {
            case 'add':
                $name = $_POST['name'];
                $price = $_POST['price'];
                
                $query = "INSERT INTO menu_items_addons (name, price) VALUES (?, ?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "sd", $name, $price);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Menu item added successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'edit':
                $menu_item_id = $_POST['menu_item_id'];
                $name = $_POST['name'];
                $price = $_POST['price'];
                
                $query = "UPDATE menu_items_addons SET name = ?, price = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "sdi", $name, $price, $menu_item_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Menu item updated successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'delete':
                $menu_item_id = $_POST['menu_item_id'];
                
                $query = "DELETE FROM menu_items_addons WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Menu item deleted successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'add_category':
                $name = $_POST['name'];
                
                // First, check if category already exists
                $check_query = "SELECT id FROM menu_categories WHERE name = ?";
                $check_stmt = mysqli_prepare($con, $check_query);
                mysqli_stmt_bind_param($check_stmt, "s", $name);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
                
                if (mysqli_num_rows($check_result) > 0) {
                    throw new Exception('Category with this name already exists');
                }
                
                // If no duplicate, insert new category
                $query = "INSERT INTO menu_categories (name) VALUES (?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "s", $name);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Category added successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'edit_category':
                $category_id = $_POST['category_id'];
                $name = $_POST['name'];
                
                // Check if another category already has this name
                $check_query = "SELECT id FROM menu_categories WHERE name = ? AND id != ?";
                $check_stmt = mysqli_prepare($con, $check_query);
                mysqli_stmt_bind_param($check_stmt, "si", $name, $category_id);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
                
                if (mysqli_num_rows($check_result) > 0) {
                    throw new Exception('Another category with this name already exists');
                }
                
                $query = "UPDATE menu_categories SET name = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "si", $name, $category_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Category updated successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'delete_category':
                $category_id = $_POST['category_id'];
                
                $query = "DELETE FROM menu_categories WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $category_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Category deleted successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'get_menu_item_details':
                $menu_item_id = $_POST['menu_item_id'];
                
                // Get category name
                $category_query = "SELECT mc.name as category_name, mc.id as category_id 
                                  FROM menu_items mi 
                                  JOIN menu_categories mc ON mi.category_id = mc.id 
                                  WHERE mi.id = ?";
                $stmt = mysqli_prepare($con, $category_query);
                mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
                mysqli_stmt_execute($stmt);
                $category_result = mysqli_stmt_get_result($stmt);
                $category_data = mysqli_fetch_assoc($category_result);
                
                // Get menu items from same category
                $items_query = "SELECT id, name 
                               FROM menu_items 
                               WHERE category_id = ?";
                $stmt = mysqli_prepare($con, $items_query);
                mysqli_stmt_bind_param($stmt, "i", $category_data['category_id']);
                mysqli_stmt_execute($stmt);
                $items_result = mysqli_stmt_get_result($stmt);
                
                $menu_items = [];
                while ($row = mysqli_fetch_assoc($items_result)) {
                    $menu_items[] = $row;
                }
                
                $response['success'] = true;
                $response['category_name'] = $category_data['category_name'];
                $response['menu_items'] = $menu_items;
                break;

            case 'get_addons':
                $menu_item_id = $_POST['menu_item_id'];
                
                $query = "SELECT * FROM menu_items_addons WHERE menu_item_id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $addons = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $addons[] = $row;
                }
                
                $response['success'] = true;
                $response['addons'] = $addons;
                break;

            case 'add_addon':
                $menu_item_id = $_POST['menu_item_id'];
                $name = $_POST['addon_name'];
                $price = $_POST['addon_price'];
                
                $query = "INSERT INTO menu_items_addons (menu_item_id, name, price) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "isd", $menu_item_id, $name, $price);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Addon added successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'delete_addon':
                $addon_id = $_POST['addon_id'];
                
                $query = "DELETE FROM menu_items_addons WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "i", $addon_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Addon deleted successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'edit_addon':
                $addon_id = $_POST['addon_id'];
                $name = $_POST['addon_name'];
                $price = $_POST['addon_price'];
                
                $query = "UPDATE menu_items_addons SET name = ?, price = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "sdi", $name, $price, $addon_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Addon updated successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            case 'get_menu_items_by_category':
                $category_name = $_POST['category_name'];
                
                $query = "SELECT id, name 
                         FROM menu_items mi 
                         JOIN menu_categories mc ON mi.category_id = mc.id 
                         WHERE mc.name = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "s", $category_name);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $menu_items = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $menu_items[] = $row;
                }
                
                $response['success'] = true;
                $response['menu_items'] = $menu_items;
                break;

            case 'add_menu_item':
                try {
                    // Get the default category if none is specified
                    if (empty($_POST['category_id'])) {
                        $default_cat_query = "SELECT id FROM menu_categories WHERE name = 'General' LIMIT 1";
                        $result = mysqli_query($con, $default_cat_query);
                        $category = mysqli_fetch_assoc($result);
                        $category_id = $category['id'];
                    } else {
                        $category_id = $_POST['category_id'];
                    }

                    $name = $_POST['name'];
                    $price = $_POST['price'];
                    $image_path = ''; // Set default empty image path

                    // Handle image upload if present
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "uploads/menus/";
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $file_name = uniqid('menu_') . '.' . $file_extension;
                        $image_path = $target_dir . $file_name;
                        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
                    }

                    $query = "INSERT INTO menu_items (category_id, name, price, image_path) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "isds", $category_id, $name, $price, $image_path);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $response['success'] = true;
                        $response['message'] = "Menu item added successfully";
                    } else {
                        throw new Exception("Error adding menu item: " . mysqli_error($con));
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = $e->getMessage();
                }
                break;

            case 'edit_menu_item':
                $menu_item_id = $_POST['menu_item_id'];
                $category_id = $_POST['category_id'];
                $name = $_POST['name'];
                $price = $_POST['price'];
                
                // First get the current image path
                $current_image_query = "SELECT image_path FROM menu_items WHERE id = ?";
                $stmt = mysqli_prepare($con, $current_image_query);
                mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $current_image = '';
                if ($row = mysqli_fetch_assoc($result)) {
                    $current_image = $row['image_path'];
                }
                
                // Handle new image upload if provided
                $image_path = $current_image;
                if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === 0) {
                    $upload_dir = 'uploads/menus/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
                    $file_name = uniqid('menu_') . '.' . $file_extension;
                    $target_path = $upload_dir . $file_name;
                    
                    // Validate file type
                    $allowed_types = ['jpg', 'jpeg', 'png'];
                    if (!in_array($file_extension, $allowed_types)) {
                        throw new Exception('Invalid file type. Only JPG and PNG files are allowed.');
                    }
                    
                    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_path)) {
                        // Delete old image if it exists and is not the default image
                        if (!empty($current_image) && file_exists($current_image) && $current_image != 'images/default.jpg') {
                            unlink($current_image);
                        }
                        $image_path = $target_path;
                    } else {
                        throw new Exception('Error uploading new image.');
                    }
                }
                
                // Update menu item
                $query = "UPDATE menu_items SET category_id = ?, name = ?, price = ?, image_path = ? WHERE id = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "isdsi", $category_id, $name, $price, $image_path, $menu_item_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                    $response['message'] = 'Menu item updated successfully';
                } else {
                    throw new Exception(mysqli_error($con));
                }
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?> 