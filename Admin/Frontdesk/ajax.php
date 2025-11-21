<?php
session_start();
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Check database connection
if (!isset($con) || $con->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($con->connect_error ?? 'Connection not available')
    ]));
}

// Table Packages Management
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Get package details for editing
    if ($action == "get_package") {
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Table type ID is required']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $sql = "SELECT * FROM table_packages WHERE id = '$id'";
        $result = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Table type not found']);
        }
        exit;
    }

    // Save package (create new or update existing)
    if ($action == "save_package") {
        try {
            $package_name = $_POST['package_name'];
            $capacity = (int)$_POST['capacity'];
            $price = (float)$_POST['price'];
            $available_tables = (int)$_POST['available_tables'];
            $description = $_POST['description'];
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

            // Validate required fields
            if (empty($package_name) || $capacity < 1 || $price < 0 || $available_tables < 0) {
                throw new Exception('Please fill in all required fields with valid values');
            }

            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) {
                    throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
                }

                $upload_dir = 'uploads/table_packages/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_filename = uniqid() . '.' . $ext;
                $image_path = $upload_dir . $new_filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    throw new Exception('Failed to upload image');
                }
            }

            $conn = db_connect();

            if ($id) {
                // Update existing package
                $sql = "UPDATE table_packages SET 
                        package_name = ?, 
                        capacity = ?, 
                        price = ?, 
                        available_tables = ?, 
                        description = ?";
                
                $params = [$package_name, $capacity, $price, $available_tables, $description];
                
                if ($image_path) {
                    $sql .= ", image = ?";
                    $params[] = $image_path;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $id;

                $stmt = $conn->prepare($sql);
                if (!$stmt->execute($params)) {
                    throw new Exception('Failed to update package');
                }
            } else {
                // Insert new package
                $sql = "INSERT INTO table_packages (package_name, capacity, price, available_tables, description";
                $sql .= $image_path ? ", image" : "";
                $sql .= ") VALUES (?, ?, ?, ?, ?";
                $sql .= $image_path ? ", ?" : "";
                $sql .= ")";

                $params = [$package_name, $capacity, $price, $available_tables, $description];
                if ($image_path) {
                    $params[] = $image_path;
                }

                $stmt = $conn->prepare($sql);
                if (!$stmt->execute($params)) {
                    throw new Exception('Failed to save package');
                }
            }

            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            error_log('Error in save_package: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Delete package
    if ($action == "delete_package") {
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Table type ID is required']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        
        // Delete associated image first
        $sql = "SELECT image_path FROM table_packages WHERE id = '$id'";
        $result = mysqli_query($con, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $image_path = $row['image_path'];
            if ($image_path && file_exists($image_path) && $image_path != 'images/default-table.jpg') {
                unlink($image_path);
            }
        }

        $sql = "DELETE FROM table_packages WHERE id = '$id'";
        if (mysqli_query($con, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Table type deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting table type: ' . mysqli_error($con)]);
        }
        exit;
    }

    // Save table type name (create new or update existing)
    if ($action == "save_type_name") {
        $id = isset($_POST['id']) ? mysqli_real_escape_string($con, $_POST['id']) : null;
        $name = mysqli_real_escape_string($con, $_POST['name']);

        if ($id) {
            // Update existing type name
            $sql = "UPDATE table_type_names SET name = '$name' WHERE id = '$id'";
        } else {
            // Insert new type name
            $sql = "INSERT INTO table_type_names (name, is_disabled) VALUES ('$name', 0)";
        }

        if (mysqli_query($con, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Table type name saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error saving table type name: ' . mysqli_error($con)]);
        }
        exit;
    }

    // Toggle table type status
    if ($action == "toggle_type_status") {
        if (!isset($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Table type name ID is required']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $current_status = mysqli_real_escape_string($con, $_POST['status']);
        $new_status = $current_status == '1' ? '0' : '1';
        
        // Update the status
        $sql = "UPDATE table_type_names SET is_disabled = '$new_status' WHERE id = '$id'";
        if (mysqli_query($con, $sql)) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Table type status updated successfully',
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating status: ' . mysqli_error($con)]);
        }
        exit;
    }

    // Toggle package status
    if ($action == "toggle_package_status") {
        if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['status'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $current_status = mysqli_real_escape_string($con, $_POST['status']);
        $new_status = $current_status == '1' ? '0' : '1';
        
        // Update the status in table_type_names
        $sql = "UPDATE table_type_names SET is_disabled = '$new_status' WHERE name = '$name'";
        if (mysqli_query($con, $sql)) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Table type status updated successfully',
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating table type status: ' . mysqli_error($con)]);
        }
        exit;
    }

    // Toggle verification type status
    if ($action == "toggle_verification_type") {
        header('Content-Type: application/json');
        
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $new_status = mysqli_real_escape_string($con, $_POST['status']);
        $message = isset($_POST['message']) ? mysqli_real_escape_string($con, $_POST['message']) : NULL;
        
        // If enabling, clear the disable message
        if ($new_status == 1) {
            $message = NULL;
        }
        
        $sql = "UPDATE verification_types SET is_enabled = ?, disable_message = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql);
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . mysqli_error($con)]);
            exit;
        }
        
        mysqli_stmt_bind_param($stmt, "isi", $new_status, $message, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($con) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => $new_status == 1 ? 'Verification type enabled successfully' : 'Verification type disabled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No changes were made. Verification type may not exist.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating verification type: ' . mysqli_stmt_error($stmt)
            ]);
        }
        
        mysqli_stmt_close($stmt);
        exit;
    }

    // Add new disable reason
    if ($action == "add_disable_reason") {
        if (!isset($_POST['reason'])) {
            echo json_encode(['success' => false, 'message' => 'Reason is required']);
            exit;
        }

        $reason = mysqli_real_escape_string($con, $_POST['reason']);
        
        // Check if reason already exists
        $check_sql = "SELECT COUNT(*) as count FROM disable_reasons WHERE reason = '$reason'";
        $result = mysqli_query($con, $check_sql);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'This reason already exists']);
            exit;
        }
        
        $sql = "INSERT INTO disable_reasons (reason) VALUES ('$reason')";
        if (mysqli_query($con, $sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Reason added successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error adding reason: ' . mysqli_error($con)
            ]);
        }
        exit;
    }

    // Toggle reason status (active/inactive)
    if ($action == "toggle_reason_status") {
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $new_status = mysqli_real_escape_string($con, $_POST['status']);
        
        $sql = "UPDATE disable_reasons SET is_active = '$new_status' WHERE id = '$id'";
        if (mysqli_query($con, $sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Reason status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating reason status: ' . mysqli_error($con)
            ]);
        }
        exit;
    }

    // Update package status
    if ($action == "update_package_status") {
        try {
            if (!isset($_POST['id']) || !isset($_POST['status'])) {
                throw new Exception("Missing required parameters");
            }

            $id = mysqli_real_escape_string($con, $_POST['id']);
            $status = mysqli_real_escape_string($con, $_POST['status']);
            
            // Simple status update query
            $sql = "UPDATE table_packages SET status = '$status' WHERE id = '$id'";
            
            if (mysqli_query($con, $sql)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $status === 'active' ? 'Table type enabled successfully' : 'Table type disabled successfully'
                ]);
            } else {
                throw new Exception("Error updating status: " . mysqli_error($con));
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    switch ($action) {
        case 'get_tables':
            $sql = "SELECT * FROM table_packages ORDER BY id";
            $result = mysqli_query($con, $sql);
            
            if ($result) {
                $tables = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    $tables[] = $row;
                }
                echo json_encode([
                    'status' => 'success',
                    'data' => $tables
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to fetch tables: ' . mysqli_error($con)
                ]);
            }
            exit;

        case 'get_table':
            if (!isset($_POST['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Table ID is required'
                ]);
                exit;
            }

            $id = mysqli_real_escape_string($con, $_POST['id']);
            $sql = "SELECT * FROM table_packages WHERE id = '$id'";
            $result = mysqli_query($con, $sql);
            
            if ($result && $row = mysqli_fetch_assoc($result)) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $row
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to fetch table details: ' . mysqli_error($con)
                ]);
            }
            exit;

        case 'save_table':
            $id = isset($_POST['id']) ? mysqli_real_escape_string($con, $_POST['id']) : null;
            $package_name = mysqli_real_escape_string($con, $_POST['package_name']);
            $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
            $price = mysqli_real_escape_string($con, $_POST['price']);
            $description = mysqli_real_escape_string($con, $_POST['description']);
            $available_tables = mysqli_real_escape_string($con, $_POST['available_tables']);

            // Handle image upload
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = 'uploads/tables/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_path = $upload_dir . time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
            }

            if ($id) {
                // Update existing table
                $sql = "UPDATE table_packages SET 
                        package_name = '$package_name',
                        capacity = '$capacity',
                        price = '$price',
                        description = '$description',
                        available_tables = '$available_tables'";
                
                if ($image_path) {
                    $sql .= ", image_path = '$image_path'";
                }
                
                $sql .= " WHERE id = '$id'";
            } else {
                // Insert new table
                $sql = "INSERT INTO table_packages (package_name, capacity, price, description, available_tables, image_path) 
                        VALUES ('$package_name', '$capacity', '$price', '$description', '$available_tables', " . 
                        ($image_path ? "'$image_path'" : "NULL") . ")";
            }

            if (mysqli_query($con, $sql)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $id ? 'Table package updated successfully' : 'Table package added successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save table package: ' . mysqli_error($con)
                ]);
            }
            exit;

        case 'delete_table':
            if (!isset($_POST['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Table ID is required'
                ]);
                exit;
            }

            $id = mysqli_real_escape_string($con, $_POST['id']);
            $sql = "DELETE FROM table_packages WHERE id = '$id'";
            
            if (mysqli_query($con, $sql)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Table package deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete table package: ' . mysqli_error($con)
                ]);
            }
            exit;

        case 'add_facility':
            try {
                if (empty($_POST['name'])) {
                    throw new Exception('Facility name is required');
                }
                if (empty($_POST['category_id'])) {
                    throw new Exception('Category is required');
                }

                $name = $con->real_escape_string($_POST['name']);
                $category_id = (int)$_POST['category_id'];
                $display_order = !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
                $active = isset($_POST['active']) ? (int)$_POST['active'] : 1;

                // Check if facility name already exists in the same category
                $check_sql = "SELECT id FROM facilities WHERE name = ? AND category_id = ?";
                $stmt = $con->prepare($check_sql);
                $stmt->bind_param("si", $name, $category_id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Facility name already exists in this category');
                }

                // Insert new facility
                $sql = "INSERT INTO facilities (name, category_id, display_order, active) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("siii", $name, $category_id, $display_order, $active);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Facility added successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'edit_facility':
            try {
                if (empty($_POST['id'])) {
                    throw new Exception('Facility ID is required');
                }
                if (empty($_POST['name'])) {
                    throw new Exception('Facility name is required');
                }
                if (empty($_POST['category_id'])) {
                    throw new Exception('Category is required');
                }

                $id = (int)$_POST['id'];
                $name = $con->real_escape_string($_POST['name']);
                $category_id = (int)$_POST['category_id'];
                $display_order = !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
                $active = isset($_POST['active']) ? (int)$_POST['active'] : 1;

                // Check if facility name already exists in the same category (excluding current facility)
                $check_sql = "SELECT id FROM facilities WHERE name = ? AND category_id = ? AND id != ?";
                $stmt = $con->prepare($check_sql);
                $stmt->bind_param("sii", $name, $category_id, $id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Facility name already exists in this category');
                }

                // Update facility
                $sql = "UPDATE facilities SET name = ?, category_id = ?, display_order = ?, active = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("siiii", $name, $category_id, $display_order, $active, $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Facility updated successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'delete_facility':
            try {
                if (empty($_POST['id'])) {
                    throw new Exception('Facility ID is required');
                }

                $id = (int)$_POST['id'];

                // Delete facility
                $sql = "DELETE FROM facilities WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Facility deleted successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'add_category':
            try {
                // Enable error reporting for debugging
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                
                // Log the incoming request
                error_log("Received add_category request: " . print_r($_POST, true));

                if (empty($_POST['name'])) {
                    throw new Exception('Category name is required');
                }

                $name = $con->real_escape_string($_POST['name']);
                $display_order = !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
                $active = isset($_POST['active']) ? (int)$_POST['active'] : 1;

                // Log the processed data
                error_log("Processed data - Name: $name, Display Order: $display_order, Active: $active");

                // Check if category name already exists
                $check_sql = "SELECT id FROM facility_categories WHERE name = ?";
                $stmt = $con->prepare($check_sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }

                $stmt->bind_param("s", $name);
                if (!$stmt->execute()) {
                    throw new Exception("Check query failed: " . $stmt->error);
                }
                
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Category name already exists');
                }

                // Insert new category
                $sql = "INSERT INTO facility_categories (name, display_order, active) VALUES (?, ?, ?)";
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }

                $stmt->bind_param("sii", $name, $display_order, $active);
                if (!$stmt->execute()) {
                    throw new Exception("Insert failed: " . $stmt->error);
                }
                
                $new_id = $stmt->insert_id;
                error_log("Successfully inserted category with ID: $new_id");

                // Verify the insertion
                $verify_sql = "SELECT * FROM facility_categories WHERE id = ?";
                $verify_stmt = $con->prepare($verify_sql);
                $verify_stmt->bind_param("i", $new_id);
                $verify_stmt->execute();
                $result = $verify_stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception("Category was not inserted properly");
                }

                $new_category = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'message' => 'Category added successfully',
                    'category' => $new_category
                ]);
                error_log("Sent success response");
            } catch (Exception $e) {
                error_log("Error in add_category: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'delete_category':
            $id = mysqli_real_escape_string($con, $_POST['id']);
            
            // First check if there are any facilities using this category
            $check_query = "SELECT COUNT(*) as count FROM facilities WHERE category_id = '$id'";
            $result = mysqli_query($con, $check_query);
            $row = mysqli_fetch_assoc($result);
            
            if($row['count'] > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category: There are facilities using this category']);
                break;
            }
            
            $sql = "DELETE FROM facility_categories WHERE id = '$id'";
            
            if (mysqli_query($con, $sql)) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting category: ' . mysqli_error($con)]);
            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            exit;
    }
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email']; // User input email
    $password = $_POST['password']; // User input password

    // Prepare query to get user info including the hashed password
    $query = "SELECT * FROM userss WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $email);  // 's' means the parameter is a string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify the password
    if ($result->num_rows > 0) {
        $userdetails = $result->fetch_assoc();
        $hashed_password = $userdetails['password']; // The hashed password from the database
        $user_type = $userdetails['user_type'];

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Store user information in session
            $_SESSION['user_id'] = $userdetails['id'];
            $_SESSION['email'] = $userdetails['email'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['first_name'] = $userdetails['first_name'];
            $_SESSION['last_name'] = $userdetails['last_name'];

            // Set redirect URL based on user type
            $redirect_url = '';
            if ($user_type === 'admin') {
                $redirect_url = 'index.php?dashboard';
            } else if ($user_type === 'frontdesk') {
                $redirect_url = 'FrontDesk & Cashier/FrontDesk/index.php?dashboard';
            } else if ($user_type === 'cashier') {
                $redirect_url = 'FrontDesk & Cashier/Cashier/index.php?dashboard';
            } else {
                $redirect_url = 'customer_dashboard.php';
            }

            echo json_encode([
                'success' => true,
                'user_type' => $user_type,
                'redirect_url' => $redirect_url
            ]);
            exit;
        }
    }
    
    // If we get here, login failed
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email or password'
    ]);
    exit;
}

// Room management actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_room':
            try {
                $room_type_id = $_POST['room_type_id'];
                $room_no = $_POST['room_no'];

                if (empty($room_no)) {
                    throw new Exception('Please enter a room number');
                }

                // Check if room number already exists
                $check_sql = "SELECT room_id FROM room WHERE room_no = ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param("s", $room_no);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows > 0) {
                    throw new Exception('Room number already exists');
                }

                // Insert new room
                $sql = "INSERT INTO room (room_type_id, room_no) VALUES (?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("is", $room_type_id, $room_no);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Room added successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error adding room: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'edit_room':
            try {
                $room_id = $_POST['room_id'];
                $room_type_id = $_POST['room_type_id'];
                $room_no = $_POST['room_no'];

                if (empty($room_no)) {
                    throw new Exception('Please enter a room number');
                }

                // Check if room number exists for another room
                $check_sql = "SELECT room_id FROM room WHERE room_no = ? AND room_id != ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param("si", $room_no, $room_id);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows > 0) {
                    throw new Exception('Room number already exists');
                }

                // Update room
                $sql = "UPDATE room SET room_type_id = ?, room_no = ? WHERE room_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("isi", $room_type_id, $room_no, $room_id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Room updated successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error updating room: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'delete_room':
            try {
                $room_id = $_POST['room_id'];
                
                // Check if room can be deleted (not in use)
                $check_sql = "SELECT status FROM room WHERE room_id = ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param("i", $room_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception('Room not found');
                }
                
                $room = $result->fetch_assoc();
                if ($room['status'] !== NULL) {
                    throw new Exception('Cannot delete room that is currently in use');
                }

                // Soft delete the room
                $sql = "UPDATE room SET deleteStatus = '1' WHERE room_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $room_id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Room deleted successfully'
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error deleting room: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'get_room':
            try {
                $room_id = $_POST['room_id'];
                
                $sql = "SELECT r.*, rt.room_type, rt.price 
                        FROM room r 
                        LEFT JOIN room_type rt ON r.room_type_id = rt.room_type_id 
                        WHERE r.room_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $room_id);
                
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    if ($room = $result->fetch_assoc()) {
                        echo json_encode([
                            'success' => true,
                            'room' => $room
                        ]);
                    } else {
                        throw new Exception('Room not found');
                    }
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error fetching room: ' . $e->getMessage()
                ]);
            }
            exit;

        case 'get_rooms_by_type':
            try {
                $room_type_id = $_POST['room_type_id'];
                
                $sql = "SELECT * FROM room 
                        WHERE room_type_id = ? 
                        AND status IS NULL 
                        AND deleteStatus = '0'";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $room_type_id);
                
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    $rooms = [];
                    while ($room = $result->fetch_assoc()) {
                        $rooms[] = $room;
                    }
                    echo json_encode([
                        'success' => true,
                        'rooms' => $rooms
                    ]);
                } else {
                    throw new Exception($con->error);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error fetching rooms: ' . $e->getMessage()
                ]);
            }
            exit;
    }
}

// If no valid action is found
echo json_encode(['error' => true, 'message' => 'Invalid action']);
exit();
?>