<?php
session_start();
require_once 'db.php';

// Enable error reporting but prevent display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Ensure we're sending JSON response
header('Content-Type: application/json');

// Check database connection
if (!isset($con) || $con->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . ($con->connect_error ?? 'Connection not available')
    ]));
}

// Function to handle errors
function handleError($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr on line $errline in file $errfile");
    echo json_encode([
        'status' => 'error',
        'message' => 'An internal server error occurred. Please try again.'
    ]);
    exit;
}

// Set error handler
set_error_handler('handleError');

// Table Packages Management
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Get package details for editing
    if ($action == "get_package") {
        try {
            if (!isset($_POST['id'])) {
                throw new Exception('Table type ID is required');
            }

            $id = (int)$_POST['id'];
            
            // Use prepared statement to prevent SQL injection
            $sql = "SELECT * FROM table_packages WHERE id = ?";
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $con->error);
            }

            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $row
                ]);
            } else {
                throw new Exception('Table type not found');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Save package (create new or update existing)
    if ($action == "save_package") {
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Log incoming data
            error_log("Received save_package request: " . print_r($_POST, true));
            if (isset($_FILES)) {
                error_log("Files received: " . print_r($_FILES, true));
            }

            // Validate and sanitize input
            if (!isset($_POST['package_name']) || empty(trim($_POST['package_name']))) {
                throw new Exception('Package name is required');
            }

            $package_name = trim($_POST['package_name']);
            $capacity = isset($_POST['capacity']) ? (int)$_POST['capacity'] : 0;
            $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
            $available_tables = isset($_POST['available_tables']) ? (int)$_POST['available_tables'] : 0;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

            // Log processed data
            error_log("Processed data: " . json_encode([
                'package_name' => $package_name,
                'capacity' => $capacity,
                'price' => $price,
                'available_tables' => $available_tables,
                'description' => $description,
                'id' => $id
            ]));

            // Validate required fields
            if ($capacity < 1) {
                throw new Exception('Capacity must be greater than 0');
            }
            if ($available_tables < 0) {
                throw new Exception('Available tables cannot be negative');
            }

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // Check for duplicate package name
                $check_sql = $id 
                    ? "SELECT id FROM table_packages WHERE package_name = ? AND id != ?"
                    : "SELECT id FROM table_packages WHERE package_name = ?";
                
                $check_stmt = $con->prepare($check_sql);
                if (!$check_stmt) {
                    throw new Exception("Prepare failed for duplicate check: " . $con->error);
                }

                if ($id) {
                    $check_stmt->bind_param('si', $package_name, $id);
                } else {
                    $check_stmt->bind_param('s', $package_name);
                }

                if (!$check_stmt->execute()) {
                    throw new Exception("Failed to check for duplicates: " . $check_stmt->error);
                }

                $result = $check_stmt->get_result();
                if ($result->num_rows > 0) {
                    throw new Exception('A table type with this name already exists');
                }

                // Handle image upload
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $target_dir = "uploads/table_packages/";
                    if (!file_exists($target_dir)) {
                        if (!mkdir($target_dir, 0777, true)) {
                            throw new Exception("Failed to create upload directory");
                        }
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;

                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        throw new Exception("Failed to move uploaded file");
                    }
                    $image_path = $target_file;
                }

                if ($id) {
                    // Update existing package
                    $sql = "UPDATE table_packages SET 
                            package_name = ?,
                            capacity = ?,
                            price = ?,
                            available_tables = ?,
                            description = ?";
                    
                    if ($image_path) {
                        $sql .= ", image_path = ?";
                    }
                    
                    $sql .= " WHERE id = ?";

                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Prepare failed for update: " . $con->error);
                    }

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
                    if (!$stmt) {
                        throw new Exception("Prepare failed for insert: " . $con->error);
                    }

                    $stmt->bind_param('siidss', 
                        $package_name,
                        $capacity,
                        $price,
                        $available_tables,
                        $description,
                        $image_path
                    );
                }

                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

                mysqli_commit($con);

                echo json_encode([
                    'status' => 'success',
                    'message' => $id ? 'Table type updated successfully' : 'Table type added successfully'
                ]);

            } catch (Exception $e) {
                mysqli_rollback($con);
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Error in save_package: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Delete package
    if ($action == "delete_package") {
        try {
            if (!isset($_POST['id'])) {
                throw new Exception('Table type ID is required');
            }

            $id = mysqli_real_escape_string($con, $_POST['id']);
            
            // Start transaction
            mysqli_begin_transaction($con);
            
            try {
                // Delete associated image first
                $sql = "SELECT image_path FROM table_packages WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $image_path = $row['image_path'];
                    if ($image_path && file_exists($image_path) && $image_path != 'images/default-table.jpg') {
                        unlink($image_path);
                    }
                }

                // Delete the package
                $sql = "DELETE FROM table_packages WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('i', $id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete package: " . $stmt->error);
                }

                // Commit transaction
                mysqli_commit($con);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Table type deleted successfully'
                ]);
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($con);
                throw $e;
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
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
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            exit;
        }

        $id = mysqli_real_escape_string($con, $_POST['id']);
        $current_status = mysqli_real_escape_string($con, $_POST['status']);
        $new_status = $current_status == '1' ? '0' : '1';
        
        // Update the status for the specific package by ID
        $sql = "UPDATE table_packages SET status = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('si', $new_status ? 'active' : 'inactive', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Table type status updated successfully',
                'new_status' => $new_status
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating table type status: ' . $stmt->error]);
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

            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

            // Validate status
            if (!in_array($status, ['active', 'inactive'])) {
                throw new Exception("Invalid status value");
            }

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // Update only the specific package by ID
                $sql = "UPDATE table_packages SET status = ?, reason = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }

                $stmt->bind_param('ssi', $status, $reason, $id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update status: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("No package found with ID: " . $id);
                }

                mysqli_commit($con);

                echo json_encode([
                    'status' => 'success',
                    'message' => $status === 'active' ? 'Table type enabled successfully' : 'Table type disabled successfully'
                ]);

            } catch (Exception $e) {
                mysqli_rollback($con);
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Add employee
    if ($action == "add_employee") {
        try {
            // Debug log
            error_log("Received employee data: " . print_r($_POST, true));

            // Validate required fields
            $required_fields = ['staff_type_id', 'shift_start', 'shift_end', 'first_name', 'contact_no', 'address'];
            $missing_fields = [];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || (is_string($_POST[$field]) && empty(trim($_POST[$field])))) {
                    $missing_fields[] = str_replace('_', ' ', $field);
                }
            }
            
            if (!empty($missing_fields)) {
                throw new Exception('Required fields missing: ' . implode(', ', $missing_fields));
            }

            // Sanitize and prepare data
            $staff_type_id = (int)$_POST['staff_type_id'];
            if ($staff_type_id <= 0) {
                throw new Exception('Invalid staff type selected');
            }

            $shift_start = mysqli_real_escape_string($con, trim($_POST['shift_start']));
            $shift_end = mysqli_real_escape_string($con, trim($_POST['shift_end']));
            $first_name = mysqli_real_escape_string($con, trim($_POST['first_name']));
            $last_name = isset($_POST['last_name']) ? mysqli_real_escape_string($con, trim($_POST['last_name'])) : '';
            $contact_no = mysqli_real_escape_string($con, trim($_POST['contact_no']));
            $address = mysqli_real_escape_string($con, trim($_POST['address']));

            // Create full name
            $emp_name = $first_name . ($last_name ? ' ' . $last_name : '');

            // Start transaction
            mysqli_begin_transaction($con);

            try {
                // Insert employee
                $sql = "INSERT INTO staff (emp_name, staff_type_id, shift_start, shift_end, contact_no, address) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }

                $stmt->bind_param('sissss', 
                    $emp_name,
                    $staff_type_id,
                    $shift_start,
                    $shift_end,
                    $contact_no,
                    $address
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to add employee: " . $stmt->error);
                }

                mysqli_commit($con);
                echo json_encode(['status' => 'success', 'message' => 'Employee added successfully']);
                exit;

            } catch (Exception $e) {
                mysqli_rollback($con);
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare query to get user info including the hashed password
    $query = "SELECT * FROM userss WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify the password
    if ($result->num_rows > 0) {
        $userdetails = $result->fetch_assoc();
        $hashed_password = $userdetails['password'];
        $user_type = $userdetails['user_type'];

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Store user information in session
            $_SESSION['user_id'] = $userdetails['id'];
            $_SESSION['email'] = $userdetails['email'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['first_name'] = $userdetails['first_name'];
            $_SESSION['last_name'] = $userdetails['last_name'];

            echo json_encode([
                'success' => true,
                'user_type' => $user_type,
                'message' => 'Login successful'
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

// Handle shift change request
if (isset($_POST['change_shift'])) {
    try {
        if (!isset($_POST['emp_id']) || !isset($_POST['shift_id'])) {
            throw new Exception('Employee ID and Shift ID are required');
        }

        $emp_id = (int)$_POST['emp_id'];
        $shift_id = (int)$_POST['shift_id'];

        // Validate the IDs
        if ($emp_id <= 0 || $shift_id <= 0) {
            throw new Exception('Invalid Employee ID or Shift ID');
        }

        // Start transaction
        mysqli_begin_transaction($con);

        try {
            // Update the employee's shift
            $sql = "UPDATE staff SET shift_id = ? WHERE emp_id = ?";
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $con->error);
            }

            $stmt->bind_param('ii', $shift_id, $emp_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            // Log the shift change
            $log_sql = "INSERT INTO shift_history (emp_id, shift_id, change_date) VALUES (?, ?, NOW())";
            $log_stmt = $con->prepare($log_sql);
            if (!$log_stmt) {
                throw new Exception("Prepare failed for logging: " . $con->error);
            }

            $log_stmt->bind_param('ii', $emp_id, $shift_id);
            if (!$log_stmt->execute()) {
                throw new Exception("Execute failed for logging: " . $log_stmt->error);
            }

            mysqli_commit($con);

            echo json_encode([
                'status' => 'success',
                'message' => 'Shift changed successfully'
            ]);

        } catch (Exception $e) {
            mysqli_rollback($con);
            throw $e;
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Handle adding new staff type
if (isset($_POST['action']) && $_POST['action'] === 'add_staff_type') {
    try {
        // Validate input
        if (!isset($_POST['staff_type']) || empty(trim($_POST['staff_type']))) {
            throw new Exception('Staff type name is required');
        }

        $staff_type = mysqli_real_escape_string($con, trim($_POST['staff_type']));

        // Check if staff type already exists
        $check_sql = "SELECT staff_type_id FROM staff_type WHERE staff_type = ?";
        $check_stmt = $con->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $check_stmt->bind_param('s', $staff_type);
        if (!$check_stmt->execute()) {
            throw new Exception("Failed to check for existing staff type: " . $check_stmt->error);
        }
        
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception('This staff type already exists');
        }

        // Insert new staff type
        $sql = "INSERT INTO staff_type (staff_type) VALUES (?)";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param('s', $staff_type);
        if (!$stmt->execute()) {
            throw new Exception("Failed to add staff type: " . $stmt->error);
        }

        $staff_type_id = $stmt->insert_id;

        echo json_encode([
            'status' => 'success',
            'staff_type_id' => $staff_type_id,
            'message' => 'Staff type added successfully'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Handle adding new shift
if (isset($_POST['action']) && $_POST['action'] === 'add_shift') {
    try {
        // Validate input
        if (!isset($_POST['shift_name']) || empty(trim($_POST['shift_name']))) {
            throw new Exception('Shift name is required');
        }
        if (!isset($_POST['shift_start']) || empty(trim($_POST['shift_start']))) {
            throw new Exception('Shift start time is required');
        }
        if (!isset($_POST['shift_end']) || empty(trim($_POST['shift_end']))) {
            throw new Exception('Shift end time is required');
        }

        $shift_name = mysqli_real_escape_string($con, trim($_POST['shift_name']));
        $shift_start = mysqli_real_escape_string($con, trim($_POST['shift_start']));
        $shift_end = mysqli_real_escape_string($con, trim($_POST['shift_end']));
        
        // Format the shift timing
        $shift_timing = $shift_start . ' - ' . $shift_end;

        // Check if shift already exists
        $check_sql = "SELECT shift_id FROM shift WHERE shift = ? OR shift_timing = ?";
        $check_stmt = $con->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $check_stmt->bind_param('ss', $shift_name, $shift_timing);
        if (!$check_stmt->execute()) {
            throw new Exception("Failed to check for existing shift: " . $check_stmt->error);
        }
        
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception('A shift with this name or timing already exists');
        }

        // Insert new shift
        $sql = "INSERT INTO shift (shift, shift_timing) VALUES (?, ?)";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param('ss', $shift_name, $shift_timing);
        if (!$stmt->execute()) {
            throw new Exception("Failed to add shift: " . $stmt->error);
        }

        $shift_id = $stmt->insert_id;

        echo json_encode([
            'status' => 'success',
            'shift_id' => $shift_id,
            'message' => 'Shift added successfully'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// If no valid action is found
echo json_encode(['error' => true, 'message' => 'Invalid action']);
exit();
?>