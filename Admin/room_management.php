<?php
require_once 'includes/init.php';

// Debug output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted: ' . print_r($_POST, true));
    error_log('Files: ' . print_r($_FILES, true));
}

// Handle form submission for adding/editing/deleting rooms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $response = ['success' => false, 'message' => ''];
        
        // Handle update room type
        if ($_POST['action'] === 'update_room_type') {
            try {
                $con->begin_transaction();
                
                // Get form data
                $room_type_id = (int)$_POST['room_type_id'];
                $room_type = $con->real_escape_string(trim($_POST['room_type']));
                $price = (float)$_POST['price'];
                $capacity = (int)$_POST['capacity'];
                $beds = $con->real_escape_string(trim($_POST['beds']));
                $description = $con->real_escape_string(trim($_POST['description'] ?? ''));
                $discount_percent = !empty($_POST['discount_percent']) ? (float)$_POST['discount_percent'] : 0;
                $status = isset($_POST['status']) && $_POST['status'] === 'active' ? 'active' : 'inactive';
                
                // Handle file uploads
                $upload_dir = 'C:/xampp/htdocs/Admin/aa/uploads/rooms/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Process image uploads
                $images = [];
                $image_fields = ['image', 'image2', 'image3'];
                
                foreach ($image_fields as $field) {
                    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === 0) {
                        $file_extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                        $file_name = uniqid('room_') . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {
                            $images[$field] = 'aa/uploads/rooms/' . $file_name;
                        }
                    }
                }
                
                // Build the update query
                $update_fields = [
                    "room_type = '$room_type'",
                    "price = $price",
                    "capacity = $capacity",
                    "beds = '$beds'",
                    "description = '" . $con->real_escape_string($description) . "'",
                    "discount_percent = $discount_percent",
                    "status = '$status'"
                ];
                
                // Add image fields if they were uploaded
                foreach ($images as $field => $path) {
                    $update_fields[] = "$field = '$path'";
                }
                
                $update_query = "UPDATE room_types SET " . implode(', ', $update_fields) . " 
                                WHERE room_type_id = $room_type_id";
                
                if ($con->query($update_query)) {
                    $con->commit();
                    $response = [
                        'success' => true,
                        'message' => 'Room type updated successfully!'
                    ];
                } else {
                    throw new Exception('Failed to update room type: ' . $con->error);
                }
                
            } catch (Exception $e) {
                $con->rollback();
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Handle delete room type
        if ($_POST['action'] === 'delete_room_type') {
            try {
                $con->begin_transaction();
                
                $room_type_id = (int)$_POST['room_type_id'];
                
                // First, check if there are any rooms of this type
                $check_rooms = $con->query("SELECT COUNT(*) as room_count FROM room_numbers WHERE room_type_id = $room_type_id");
                $room_count = $check_rooms->fetch_assoc()['room_count'];
                
                if ($room_count > 0) {
                    throw new Exception('Cannot delete room type because there are rooms assigned to it.');
                }
                
                // Delete from room_type_amenities
                $con->query("DELETE FROM room_type_amenities WHERE room_type_id = $room_type_id");
                
                // Delete the room type
                $result = $con->query("DELETE FROM room_types WHERE room_type_id = $room_type_id");
                
                if ($result) {
                    $con->commit();
                    $response = [
                        'success' => true,
                        'message' => 'Room type deleted successfully!'
                    ];
                } else {
                    throw new Exception('Failed to delete room type: ' . $con->error);
                }
                
            } catch (Exception $e) {
                $con->rollback();
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Handle add room submission (combining room type and room number)
        if ($_POST['action'] === 'add_room') {
            try {
                $con->begin_transaction();
                
                // Get room details from form
                $room_number = $con->real_escape_string(trim($_POST['room_number']));
                $price = (float)$_POST['price'];
                $capacity = (int)$_POST['capacity'];
                $beds = $con->real_escape_string(trim($_POST['beds']));
                $description = $con->real_escape_string(trim($_POST['description'] ?? ''));
                $discount_percent = !empty($_POST['discount_percent']) ? (float)$_POST['discount_percent'] : 0;
                $status = 'active';
                
                // Handle 'Other' room type
                $room_type_id = $_POST['room_type_id'];
                $room_type_name = '';
                
                if ($room_type_id === 'other') {
                    // Create a new room type
                    if (empty(trim($_POST['other_room_type']))) {
                        throw new Exception('Please specify a room type');
                    }
                    
                    $room_type_name = $con->real_escape_string(trim($_POST['other_room_type']));
                    
                    // Check if room type already exists
                    $check_query = "SELECT room_type_id FROM room_types WHERE LOWER(room_type) = LOWER(?)";
                    $stmt = $con->prepare($check_query);
                    $stmt->bind_param("s", $room_type_name);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Use existing room type
                        $row = $result->fetch_assoc();
                        $room_type_id = $row['room_type_id'];
                    } else {
                        // Create new room type
                        $insert_query = "INSERT INTO room_types (room_type, price, capacity, beds, description, status, created_at) 
                                       VALUES (?, ?, ?, ?, ?, 'active', NOW())";
                        $stmt = $con->prepare($insert_query);
                        $stmt->bind_param("sdiss", $room_type_name, $price, $capacity, $beds, $description);
                        $stmt->execute();
                        $room_type_id = $con->insert_id;
                    }
                } else {
                    // Get existing room type
                    $room_type_id = (int)$room_type_id;
                    $room_type_query = "SELECT room_type FROM room_types WHERE room_type_id = ?";
                    $stmt = $con->prepare($room_type_query);
                    $stmt->bind_param("i", $room_type_id);
                    $stmt->execute();
                    $room_type_result = $stmt->get_result();
                    
                    if ($room_type_row = $room_type_result->fetch_assoc()) {
                        $room_type_name = $room_type_row['room_type'];
                    } else {
                        throw new Exception('Selected room type not found');
                    }
                }
                
                // Handle file uploads
                $upload_dir = 'C:/xampp/htdocs/Admin/aa/uploads/rooms/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_path = '';
                $image2_path = null;
                $image3_path = null;
                
                // Process main image
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $file_name = uniqid('room_') . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                        $image_path = 'aa/uploads/rooms/' . $file_name;
                    }
                }
                
                // Process additional images if they exist
                foreach (['image2', 'image3'] as $img) {
                    if (isset($_FILES[$img]) && $_FILES[$img]['error'] === 0) {
                        $file_extension = strtolower(pathinfo($_FILES[$img]['name'], PATHINFO_EXTENSION));
                        $file_name = uniqid('room_') . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES[$img]['tmp_name'], $file_path)) {
                            ${$img . '_path'} = 'aa/uploads/rooms/' . $file_name;
                        }
                    }
                }
                
                // Check if room type with this ID exists, if not create it
                $check_query = "SELECT room_type_id FROM room_types WHERE room_type_id = ?";
                $stmt = $con->prepare($check_query);
                $stmt->bind_param("i", $room_type_id);
                $stmt->execute();
                $exists = $stmt->get_result()->num_rows > 0;
                
                if (!$exists) {
                    // Insert new room type if it doesn't exist
                    $query = "INSERT INTO room_types (
                        room_type_id, room_type, price, capacity, beds, description, 
                        image, image2, image3, discount_percent, status, created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, 
                        ?, " . 
                        ($image2_path ? "?" : "NULL") . ", " . 
                        ($image3_path ? "?" : "NULL") . ", 
                        ?, ?, NOW()
                    )";
                    
                    $stmt = $con->prepare($query);
                    $params = [
                        $room_type_id,
                        $room_type_name,
                        $price,
                        $capacity,
                        $beds,
                        $description,
                        $image_path
                    ];
                    
                    // Add image paths if they exist
                    $types = "isiss";
                    if ($image2_path) {
                        $params[] = $image2_path;
                        $types .= "s";
                    }
                    if ($image3_path) {
                        $params[] = $image3_path;
                        $types .= "s";
                    }
                    
                    // Add remaining parameters
                    $params[] = $discount_percent;
                    $params[] = $status;
                    $types .= "ds";
                    
                    $stmt->bind_param($types, ...$params);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to insert room type: ' . $stmt->error);
                    }
                }
                
                // Insert room number
                $query = "INSERT INTO room_numbers (room_type_id, room_number, status, created_at) 
                         VALUES ($room_type_id, '$room_number', '$status', NOW())";
                
                if (!$con->query($query)) {
                    throw new Exception('Failed to insert room number: ' . $con->error);
                }
                
                $con->commit();
                
                $response = [
                    'success' => true,
                    'message' => 'Room added successfully!',
                    'room_id' => $room_type_id
                ];
                
                // If a new room type was added, include it in the response
                if ($room_type_id === 'other' && !empty($room_type_name)) {
                    // Get the newly created room type ID
                    $new_type_query = $con->query("SELECT room_type_id FROM room_types WHERE room_type = '" . $con->real_escape_string($room_type_name) . "'");
                    if ($new_type = $new_type_query->fetch_assoc()) {
                        $response['new_room_type_id'] = $new_type['room_type_id'];
                        $response['new_room_type_name'] = $room_type_name;
                    }
                }
                
            } catch (Exception $e) {
                $con->rollback();
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        switch($_POST['action']) {
            case 'add_room_type':
                try {
                    $room_type = $_POST['room_type'];
                    $price = (float)$_POST['price'];
                    $capacity = (int)$_POST['capacity'];
                    $description = $_POST['description'];
                    $beds = $_POST['beds'];
                    $rating = (float)$_POST['rating'];
                    $discount_percent = isset($_POST['discount_percent']) ? (int)$_POST['discount_percent'] : 0;
                    $discount_valid_until = !empty($_POST['discount_valid_until']) ? $_POST['discount_valid_until'] : null;

                    // Handle main image upload
                    $image_path = '';
                    $image2_path = null;
                    $image3_path = null;

                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        // Update upload directory path
                        $upload_dir = 'C:/xampp/htdocs/Admin/aa/uploads/rooms/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        // Generate unique filename for main image
                        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $file_name = uniqid('room_type_') . '.' . $file_extension;
                        $file_system_path = $upload_dir . $file_name;
                        $image_path = '../aa/uploads/rooms/' . $file_name;

                        // Validate file type
                        $allowed_types = ['jpg', 'jpeg', 'png'];
                        if (!in_array($file_extension, $allowed_types)) {
                            throw new Exception('Invalid file type. Only JPG and PNG files are allowed.');
                        }

                        // Move uploaded main image
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $file_system_path)) {
                            throw new Exception('Error uploading main image file.');
                        }

                        // Handle additional image 1
                        if (isset($_FILES['image2']) && $_FILES['image2']['error'] === 0) {
                            $file_extension = strtolower(pathinfo($_FILES['image2']['name'], PATHINFO_EXTENSION));
                            if (in_array($file_extension, $allowed_types)) {
                                $file_name = uniqid('room_type_') . '_2.' . $file_extension;
                                $file_system_path = $upload_dir . $file_name;
                                $image2_path = '../aa/uploads/rooms/' . $file_name;
                                move_uploaded_file($_FILES['image2']['tmp_name'], $file_system_path);
                            }
                        }

                        // Handle additional image 2
                        if (isset($_FILES['image3']) && $_FILES['image3']['error'] === 0) {
                            $file_extension = strtolower(pathinfo($_FILES['image3']['name'], PATHINFO_EXTENSION));
                            if (in_array($file_extension, $allowed_types)) {
                                $file_name = uniqid('room_type_') . '_3.' . $file_extension;
                                $file_system_path = $upload_dir . $file_name;
                                $image3_path = '../aa/uploads/rooms/' . $file_name;
                                move_uploaded_file($_FILES['image3']['tmp_name'], $file_system_path);
                            }
                        }

                        $sql = "INSERT INTO room_types (room_type, price, capacity, description, beds, rating, image, image2, image3, discount_percent, discount_valid_until) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("sdissdsssis", 
                            $room_type,
                            $price,
                            $capacity,
                            $description,
                            $beds,
                            $rating,
                            $image_path,
                            $image2_path,
                            $image3_path,
                            $discount_percent,
                            $discount_valid_until
                        );
                        
                        if ($stmt->execute()) {
                            $response['success'] = true;
                            $response['message'] = 'Room type added successfully!';
                        } else {
                            // If insert fails, delete uploaded images
                            if (file_exists($image_path)) {
                                unlink($image_path);
                            }
                            if ($image2_path && file_exists($image2_path)) {
                                unlink($image2_path);
                            }
                            if ($image3_path && file_exists($image3_path)) {
                                unlink($image3_path);
                            }
                            throw new Exception($con->error);
                        }
                    } else {
                        throw new Exception('Main image file is required.');
                    }
                } catch (Exception $e) {
                    $response['message'] = 'Error adding room type: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'add_room':
                try {
                    $room_type_id = (int)$_POST['room_type_id'];
                    $room_number = $con->real_escape_string(trim($_POST['room_number']));
                    $total_rooms = 1; // Since we're adding one room at a time
                    $available_rooms = 1; // New room is available by default
                    $status = 'Available'; // Default status
                    
                    // Insert into rooms table with the correct columns
                    $sql = "INSERT INTO room_numbers (room_type_id, room_number, status) 
                            VALUES (?, ?, ?)";
                    $stmt = $con->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("iss", $room_type_id, $room_number, $status);
                        
                        if ($stmt->execute()) {
                            $response['success'] = true;
                            $response['message'] = 'Room added successfully!';
                        } else {
                            throw new Exception($con->error);
                        }
                    } else {
                        throw new Exception('Failed to prepare statement: ' . $con->error);
                    }
                } catch (Exception $e) {
                    $response['message'] = 'Error adding room: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'edit_room':
                try {
                    $room_id = (int)$_POST['room_id'];
                    $room_type_id = (int)$_POST['room_type_id'];
                    $room_number = $con->real_escape_string(trim($_POST['room_number']));
                    $status = strtolower($con->real_escape_string(trim($_POST['status']))); // Convert status to lowercase
                    
                    // If status is 'available' in UI, save as 'active' in database
                    $db_status = ($status === 'available') ? 'active' : $status;
                    
                    // Update the room in the database
                    $sql = "UPDATE room_numbers SET 
                            room_type_id = ?, 
                            room_number = ?,
                            status = ?
                            WHERE room_number_id = ?";
                    $stmt = $con->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("issi", $room_type_id, $room_number, $db_status, $room_id);
                        
                        if ($stmt->execute()) {
                            $response['success'] = true;
                            $response['message'] = 'Room updated successfully!';
                        } else {
                            throw new Exception($con->error);
                        }
                    } else {
                        throw new Exception('Failed to prepare statement: ' . $con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error updating room: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'update_room':
                try {
                    $room_id = $_POST['room_id'];
                    $room_type_id = $_POST['room_type_id'];
                    $capacity = (int)$_POST['capacity'];

                    // Update the room details
                    $sql = "UPDATE rooms SET 
                            room_type_id = ?, 
                            total_rooms = ?,
                            available_rooms = ?
                            WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("iiii", 
                        $room_type_id, 
                        $capacity, 
                        $capacity,  // Set available rooms to match capacity
                        $room_id
                    );
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Room updated successfully!';
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Error updating room: ' . $con->error;
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error updating room: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'delete_room':
                try {
                    $room_id = $_POST['room_id'];
                    
                    // Delete the room
                    $sql = "DELETE FROM rooms WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $room_id);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Room deleted successfully!';
                    } else {
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error deleting room: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'delete_room_type':
                try {
                    $room_type_id = $_POST['room_type_id'];
                    $result = softDeleteRoomType($room_type_id);
                    
                    $response['success'] = $result['success'];
                    $response['message'] = $result['message'];
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error processing request: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'edit_room_type':
                try {
                    $room_type_id = $_POST['room_type_id'];
                    $room_type = $_POST['room_type'];
                    $price = (float)$_POST['price'];
                    $capacity = (int)$_POST['capacity'];
                    $beds = $_POST['beds'];
                    $description = $_POST['description'];
                    $rating = (float)$_POST['rating'];
                    
                    // First get the current image path
                    $sql = "SELECT image FROM room_types WHERE room_type_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $room_type_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $current_image = '';
                    if ($row = $result->fetch_assoc()) {
                        $current_image = $row['image'];
                    }
                    
                    // Handle image upload if a new image is provided
                    $image_path = $current_image; // Default to current image
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $upload_dir = 'C:/xampp/htdocs/Admin/aa/uploads/rooms/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $file_name = uniqid('room_type_') . '.' . $file_extension;
                        $file_system_path = $upload_dir . $file_name;
                        $new_image_path = '../aa/uploads/rooms/' . $file_name;
                        
                        // Validate file type
                        $allowed_types = ['jpg', 'jpeg', 'png'];
                        if (!in_array($file_extension, $allowed_types)) {
                            throw new Exception('Invalid file type. Only JPG and PNG files are allowed.');
                        }

                        // Move uploaded file using file system path
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_system_path)) {
                            // Delete old image if exists and different from default
                            if (!empty($current_image) && file_exists($current_image) && $current_image != '../aa/uploads/rooms/default.jpg') {
                                unlink($current_image);
                            }
                            $image_path = $new_image_path;
                        } else {
                            throw new Exception('Error uploading new image file.');
                        }
                    }

                    // Update the room type in database
                    $sql = "UPDATE room_types SET 
                            room_type = ?, 
                            price = ?, 
                            capacity = ?, 
                            beds = ?, 
                            description = ?, 
                            rating = ?, 
                            image = ? 
                            WHERE room_type_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("sdissdsi", 
                        $room_type, 
                        $price, 
                        $capacity, 
                        $beds, 
                        $description, 
                        $rating, 
                        $image_path, 
                        $room_type_id
                    );
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Room type updated successfully!';
                    } else {
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error updating room type: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'delete_room':
                try {
                    $room_id = (int)$_POST['room_id'];
                    
                    // First, check if the room exists and is not currently booked
                    $check_query = "SELECT * FROM room_numbers WHERE room_number_id = ?";
                    $stmt = $con->prepare($check_query);
                    $stmt->bind_param('i', $room_id);
                    $stmt->execute();
                    $room = $stmt->get_result()->fetch_assoc();
                    
                    if (!$room) {
                        throw new Exception('Room not found');
                    }
                    
                    // Check if room is currently booked
                    $booking_check = "SELECT * FROM bookings WHERE room_id = ? AND check_out > NOW()";
                    $stmt = $con->prepare($booking_check);
                    $stmt->bind_param('i', $room_id);
                    $stmt->execute();
                    $active_booking = $stmt->get_result()->fetch_assoc();
                    
                    if ($active_booking) {
                        throw new Exception('Cannot delete room with active or future bookings');
                    }
                    
                    // Delete the room
                    $delete_query = "DELETE FROM room_numbers WHERE room_number_id = ?";
                    $stmt = $con->prepare($delete_query);
                    $stmt->bind_param('i', $room_id);
                    
                    if ($stmt->execute()) {
                        $response = [
                            'success' => true,
                            'message' => 'Room deleted successfully'
                        ];
                    } else {
                        throw new Exception('Failed to delete room');
                    }
                } catch (Exception $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Error deleting room: ' . $e->getMessage()
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
                
            case 'edit_room':
                try {
                    $room_id = $_POST['room_id'];
                    $room_type_id = $_POST['room_type_id'];
                    $capacity = (int)$_POST['capacity'];
                    
                    // Begin transaction
                    $con->begin_transaction();

                    // Update the room details
                    $sql = "UPDATE rooms SET 
                            room_type_id = ?, 
                            total_rooms = ?, 
                            available_rooms = ?,
                            status = 'Available'
                            WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("iiii", 
                        $room_type_id, 
                        $capacity, 
                        $capacity,
                        $room_id
                    );
                    
                    if ($stmt->execute()) {
                        // Verify the update
                        $verify_sql = "SELECT * FROM rooms WHERE id = ?";
                        $verify_stmt = $con->prepare($verify_sql);
                        $verify_stmt->bind_param("i", $room_id);
                        $verify_stmt->execute();
                        $updated = $verify_stmt->get_result()->fetch_assoc();

                        // If verification successful, commit transaction
                        $con->commit();

                        $response['success'] = true;
                        $response['message'] = 'Room updated successfully!';
                        $response['debug'] = [
                            'updated_data' => $updated,
                            'params' => [
                                'room_id' => $room_id,
                                'room_type_id' => $room_type_id,
                                'capacity' => $capacity,
                                'available_rooms' => $capacity
                            ]
                        ];
                    } else {
                        // Rollback on error
                        $con->rollback();
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    // Ensure rollback on any error
                    if ($con->connect_errno != 0) {
                        $con->rollback();
                    }
                    $response['success'] = false;
                    $response['message'] = 'Error updating room: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'update_room_amenities':
                try {
                    $room_type_id = isset($_POST['room_type_id']) ? (int)$_POST['room_type_id'] : 0;
                    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];
                    
                    if ($room_type_id <= 0) {
                        throw new Exception('Invalid room type ID');
                    }
                    
                    // First delete existing amenities
                    $deleteSQL = "DELETE FROM room_type_amenities WHERE room_type_id = ?";
                    $deleteStmt = $con->prepare($deleteSQL);
                    $deleteStmt->bind_param("i", $room_type_id);
                    $deleteStmt->execute();
                    
                    // Then insert new amenities if any were selected
                    if (!empty($amenities)) {
                        $insertSQL = "INSERT INTO room_type_amenities (room_type_id, amenity_id) VALUES (?, ?)";
                        $insertStmt = $con->prepare($insertSQL);
                        
                        foreach ($amenities as $amenity_id) {
                            $amenity_id = (int)$amenity_id; // Ensure it's an integer
                            $insertStmt->bind_param("ii", $room_type_id, $amenity_id);
                            $insertStmt->execute();
                        }
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'Room amenities updated successfully!';
                
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error updating amenities: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'add_amenity':
                try {
                    if (empty($_POST['name'])) {
                        throw new Exception('Amenity name is required');
                    }
                    
                    $name = trim($_POST['name']);
                    $icon = !empty($_POST['icon']) ? trim($_POST['icon']) : '';
                    
                    // Ensure icon has proper format (starts with fa/fas/far)
                    if ($icon && !preg_match('/^(fa|fas|far|fab|fal|fad)\s/i', $icon)) {
                        $icon = 'fas ' . $icon;
                    }
                    
                    $sql = "INSERT INTO amenities (name, icon) VALUES (?, ?)";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ss", $name, $icon);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Amenity added successfully!';
                        $response['amenity_id'] = $con->insert_id;
                    } else {
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error adding amenity: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'edit_amenity':
                try {
                    $amenity_id = (int)$_POST['amenity_id'];
                    $name = $_POST['amenity_name'];
                    $icon = $_POST['amenity_icon'] ?? '';
                    
                    // Ensure icon has proper format (starts with fa/fas/far)
                    if ($icon && !preg_match('/^(fa|fas|far|fab|fal|fad)\s/i', $icon)) {
                        $icon = 'fas ' . $icon;
                    }
                    
                    $sql = "UPDATE amenities SET name = ?, icon = ? WHERE amenity_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ssi", $name, $icon, $amenity_id);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Amenity updated successfully!';
                    } else {
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error updating amenity: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
                
            case 'delete_amenity':
                try {
                    $amenity_id = (int)$_POST['amenity_id'];
                    
                    // First delete from room_type_amenities
                    $sql = "DELETE FROM room_type_amenities WHERE amenity_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $amenity_id);
                    $stmt->execute();
                    
                    // Then delete from amenities
                    $sql = "DELETE FROM amenities WHERE amenity_id = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("i", $amenity_id);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Amenity deleted successfully!';
                    } else {
                        throw new Exception($con->error);
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['message'] = 'Error deleting amenity: ' . $e->getMessage();
                }
                echo json_encode($response);
                exit;
        }
        redirect('room_management.php');
    }
}

// Get existing rooms with room type information
$rooms = [];
$result = $con->query("SELECT 
    rn.room_number_id AS id,
    rn.room_type_id,
    1 AS total_rooms,
    CASE WHEN rn.status = 'active' THEN 1 ELSE 0 END AS available_rooms,
    rn.status,
    rt.room_type,
    rt.capacity,
    rt.description,
    rt.beds,
    rt.price
FROM room_numbers rn
LEFT JOIN room_types rt ON rn.room_type_id = rt.room_type_id
WHERE rn.status <> 'maintenance'
ORDER BY rn.room_number_id ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ensure the status reflects the actual availability
        if ($row['available_rooms'] > 0) {
            $row['status'] = 'Available';
        } else if ($row['available_rooms'] == 0) {
            $row['status'] = 'Occupied';
        }
        $rooms[] = $row;
    }
}

// Get room types
$room_types = [];
$result = $con->query("SELECT * FROM room_types WHERE status = 'active' ORDER BY room_type_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_types[] = $row;
    }
}

// Now we can include the header and sidebar
include 'header.php';
include 'sidebar.php';

function getRoomTypes() {
    global $conn;
    
    $query = "SELECT rt.*, GROUP_CONCAT(a.name) as amenities 
              FROM room_types rt 
              LEFT JOIN room_type_amenities rta ON rt.room_type_id = rta.room_type_id
              LEFT JOIN amenities a ON rta.amenity_id = a.amenity_id
              GROUP BY rt.room_type_id";
              
    $result = mysqli_query($conn, $query);
    $rooms = array();
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = array(
                'id' => $row['room_type_id'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'capacity' => $row['capacity'],
                'description' => $row['description'],
                'beds' => $row['beds'],
                'rating' => $row['rating'],
                'image' => $row['image'],
                'discount_percent' => $row['discount_percent'],
                'discount_valid_until' => $row['discount_valid_until'],
                'amenities' => explode(',', $row['amenities'])
            );
        }
    }
    
    return $rooms;
}

// Function to update room details
function updateRoom($roomTypeId, $data) {
    global $conn;
    
    $query = "UPDATE room_types SET 
              room_type = ?,
              price = ?,
              capacity = ?,
              description = ?,
              beds = ?,
              rating = ?,
              image = ?,
              discount_percent = ?,
              discount_valid_until = ?
              WHERE room_type_id = ?";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sdissdsdss', 
        $data['room_type'],
        $data['price'],
        $data['capacity'],
        $data['description'],
        $data['beds'],
        $data['rating'],
        $data['image'],
        $data['discount_percent'],
        $data['discount_valid_until'],
        $roomTypeId
    );
    
    return mysqli_stmt_execute($stmt);
}

// Function to update room amenities
function updateRoomAmenities($roomTypeId, $amenities) {
    global $conn;
    
    // First delete existing amenities for this room
    mysqli_query($conn, "DELETE FROM room_type_amenities WHERE room_type_id = $roomTypeId");
    
    // Insert new amenities
    foreach ($amenities as $amenityId) {
        $query = "INSERT INTO room_type_amenities (room_type_id, amenity_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $roomTypeId, $amenityId);
        mysqli_stmt_execute($stmt);
    }
}

// Get all amenities for dropdown
function getAllAmenities() {
    global $conn;
    
    $query = "SELECT * FROM amenities";
    $result = mysqli_query($conn, $query);
    $amenities = array();
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $amenities[] = $row;
        }
    }
    
    return $amenities;
}

// Add this function to get room details for editing
function getRoomById($id) {
    global $con;
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function deleteRoomType($roomTypeId) {
    global $con;
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        // Check for existing bookings
        $check_bookings = "SELECT COUNT(*) as booking_count FROM bookings WHERE room_type_id = ?";
        $stmt = $con->prepare($check_bookings);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['booking_count'] > 0) {
            throw new Exception("Cannot delete room type: There are existing bookings for this room type. Please handle the bookings first.");
        }
        
        // Delete from featured_rooms first (due to foreign key)
        $delete_featured = "DELETE FROM featured_rooms WHERE room_type_id = ?";
        $stmt = $con->prepare($delete_featured);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Delete from seasonal_discounts (if exists)
        $delete_discounts = "DELETE FROM seasonal_discounts WHERE room_type_id = ?";
        $stmt = $con->prepare($delete_discounts);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Finally delete the room type
        $delete_room = "DELETE FROM room_types WHERE room_type_id = ?";
        $stmt = $con->prepare($delete_room);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Commit transaction
        $con->commit();
        return ["success" => true, "message" => "Room type deleted successfully"];
        
    } catch (Exception $e) {
        // Rollback on error
        $con->rollback();
        return ["success" => false, "message" => $e->getMessage()];
    }
}

// Function to soft delete a room type
function softDeleteRoomType($roomTypeId) {
    global $con;
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        // First check if the status column exists, if not create it
        $check_column = "SHOW COLUMNS FROM room_types LIKE 'status'";
        $result = $con->query($check_column);
        if ($result->num_rows === 0) {
            // Add status column if it doesn't exist
            $add_column = "ALTER TABLE room_types ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'";
            $con->query($add_column);
        }
        
        // Update room type status to inactive
        $update_status = "UPDATE room_types SET status = 'inactive' WHERE room_type_id = ?";
        $stmt = $con->prepare($update_status);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Remove from featured rooms
        $delete_featured = "DELETE FROM featured_rooms WHERE room_type_id = ?";
        $stmt = $con->prepare($delete_featured);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Remove from seasonal discounts
        $delete_discounts = "DELETE FROM seasonal_discounts WHERE room_type_id = ?";
        $stmt = $con->prepare($delete_discounts);
        $stmt->bind_param("i", $roomTypeId);
        $stmt->execute();
        
        // Commit transaction
        $con->commit();
        return ["success" => true, "message" => "Room type has been deactivated successfully. Existing bookings are preserved."];
        
    } catch (Exception $e) {
        // Rollback on error
        $con->rollback();
        return ["success" => false, "message" => "Error deactivating room type: " . $e->getMessage()];
    }
}

// Function to ensure default room types exist in database
function ensureDefaultRoomTypes() {
    global $con;
    
    $default_room_types = [
        ['id' => 1, 'name' => 'Standard Double Room', 'price' => 100, 'capacity' => 2],
        ['id' => 2, 'name' => 'Deluxe Room', 'price' => 150, 'capacity' => 2],
        ['id' => 3, 'name' => 'Family Room', 'price' => 200, 'capacity' => 4]
    ];
    
    foreach ($default_room_types as $type) {
        // Check if room type exists
        $check = $con->prepare("SELECT room_type_id FROM room_types WHERE room_type_id = ?");
        $check->bind_param("i", $type['id']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            // Room type doesn't exist, insert minimal required columns per schema
            $insert = $con->prepare("INSERT INTO room_types (room_type_id, room_type, price, capacity, status) VALUES (?, ?, ?, ?, 'active')");
            $insert->bind_param("isdi", $type['id'], $type['name'], $type['price'], $type['capacity']);
            $insert->execute();
        }
    }
}

// Ensure default room types exist
ensureDefaultRoomTypes();

// ... existing code ...

// Handle room type deletion
if (isset($_GET['delete_room'])) {
    $roomTypeId = $_GET['delete_room'];
    $result = deleteRoomType($roomTypeId);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    header("Location: index.php?room_management");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        .modal {
            background: rgba(0, 0, 0, 0.5);
        }
        .modal-backdrop {
            display: none;
        }
        .modal-dialog {
            margin: 1.75rem auto;
            max-width: 500px;
        }
        
        /* Add styles for main content area */
        .main-content {
            margin-left: 250px; /* Width of the sidebar */
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: #fff;
            border-collapse: collapse;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .room-type-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        .room-type-table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        .room-type-table .price {
            font-weight: 600;
            color: #28a745;
        }

        .room-type-table .capacity {
            color: #666;
        }

        .room-type-table .rating {
            color: #ffc107;
        }

        .room-type-table .discount {
            color: #dc3545;
        }

        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .room-image {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            object-fit: cover;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Add these modal styles */
        .modal-content {
            border-radius: 0.3rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            border-radius: 0.25rem;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .modal-dialog {
            max-width: 500px;
        }

        /* Add these to your existing styles */
        .img-thumbnail {
            max-width: 100%;
            height: auto;
        }

        #imagePreview {
            text-align: center;
        }

        .form-control-file {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            background-color: #fff;
        }
        
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
    </style>
</head>
<body>
    <!-- Wrap all content in main-content div -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Room Management</h2>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <!-- Room Types Table Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Room Types</h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRoomTypeModal">
                            <i class="fas fa-plus"></i> Add Room Type
                        </button>
                    </div>
                    
                    <div class="table-responsive mb-5">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Type</th>
                                    <th>Description</th>
                                    <th>Beds</th>
                                    <th>Price/Night</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $room_types_query = "SELECT * FROM room_types WHERE status = 'active' ORDER BY room_type";
                                $room_types_result = $con->query($room_types_query);
                                
                                if ($room_types_result && $room_types_result->num_rows > 0) {
                                    while ($room_type = $room_types_result->fetch_assoc()) {
                                        $status_class = $room_type['status'] == 'active' ? 'success' : 'secondary';
                                        $status_text = ucfirst($room_type['status']);
                                        echo "
                                        <tr>
                                            <td>{$room_type['room_type']}</td>
                                            <td>" . (!empty($room_type['description']) ? substr($room_type['description'], 0, 50) . (strlen($room_type['description']) > 50 ? '...' : '') : 'No description') . "</td>
                                            <td>{$room_type['beds']}</td>
                                            <td>₱" . number_format($room_type['price'], 2) . "</td>
                                            <td><span class='badge bg-{$status_class}'>{$status_text}</span></td>
                                            <td class='text-nowrap'>
                                                <button class='btn btn-sm btn-info view-room-type me-1' 
                                                    onclick='viewRoomType({$room_type['room_type_id']})' 
                                                    title='View Details' 
                                                    data-bs-toggle='tooltip' 
                                                    data-bs-placement='top'>
                                                    <i class='fas fa-eye'></i>
                                                </button>
                                                <button class='btn btn-sm btn-warning edit-room-type me-1' data-id='{$room_type['room_type_id']}' title='Edit' data-bs-toggle='tooltip' data-bs-placement='top'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button class='btn btn-sm btn-danger delete-room-type' 
                                                    data-id='{$room_type['room_type_id']}' 
                                                    title='Delete' 
                                                    data-bs-toggle='tooltip' 
                                                    data-bs-placement='top'
                                                    onclick='return confirm(\"Are you sure you want to delete this room type? This action cannot be undone.\")'>
                                                    <i class='fas fa-trash'></i>
                                                </button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No room types found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Room Numbers Table Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Room Numbers</h4>
                        <span class="badge bg-primary">Total Active Rooms: <?php 
                            $count_query = "SELECT COUNT(*) as total FROM room_numbers rn 
                                         JOIN room_types rt ON rn.room_type_id = rt.room_type_id 
                                         WHERE rn.status = 'Active'";
                            $count_result = $con->query($count_query);
                            $total_rooms = $count_result ? $count_result->fetch_assoc()['total'] : 0;
                            echo $total_rooms;
                        ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Number</th>
                                    <th>Room Type</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query to get room numbers with room types
                                $room_query = "SELECT rn.*, rt.room_type 
                                             FROM room_numbers rn
                                             JOIN room_types rt ON rn.room_type_id = rt.room_type_id
                                             ORDER BY rt.room_type, rn.room_number ASC";
                                $room_result = $con->query($room_query);

                                if ($room_result && $room_result->num_rows > 0) {
                                    while ($room = $room_result->fetch_assoc()) {
                                        $status_class = '';
                                        $status_text = ucfirst($room['status']);
                                        
                                        // Set status badge class and text based on status
                                        switch(strtolower($room['status'])) {
                                            case 'active':
                                            case 'available': // For backward compatibility
                                                $status_class = 'success';
                                                $status_text = 'Active';
                                                break;
                                            case 'occupied':
                                                $status_class = 'danger';
                                                $status_text = 'Occupied';
                                                break;
                                            case 'maintenance':
                                                $status_class = 'warning';
                                                $status_text = 'Under Maintenance';
                                                break;
                                            case 'cleaning':
                                                $status_class = 'info';
                                                $status_text = 'Cleaning';
                                                break;
                                            case 'reserved':
                                                $status_class = 'info';
                                                $status_text = 'Reserved';
                                                break;
                                            default:
                                                $status_class = 'secondary';
                                        }
                                        
                                        // Format last updated time
                                        $last_updated = !empty($room['updated_at']) ? date('M d, Y h:i A', strtotime($room['updated_at'])) : 'N/A';
                                        
                                        echo "
                                        <tr>
                                            <td>{$room['room_number']}</td>
                                            <td>{$room['room_type']}</td>
                                            <td><span class='badge bg-{$status_class}'>{$status_text}</span></td>
                                            <td>{$last_updated}</td>
                                            <td>
                                                <div class='btn-group btn-group-sm' role='group'>
                                                    <button type='button' class='btn btn-info view-room' 
                                                            data-id='{$room['room_number_id']}' 
                                                            title='View Details'>
                                                        <i class='fas fa-eye'></i>
                                                    </button>
                                                    <button type='button' class='btn btn-warning edit-room' 
                                                            data-id='{$room['room_number_id']}'
                                                            title='Edit Room'>
                                                        <i class='fas fa-edit'></i>
                                                    </button>
                                                    <button type='button' class='btn btn-danger delete-room' 
                                                            data-id='{$room['room_number_id']}'
                                                            data-room-number='{$room['room_number']}'
                                                            title='Delete Room'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4'>No rooms found. <a href='#' class='text-primary' data-toggle='modal' data-target='#addRoomModal'>Add your first room</a></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Unified Room Management Modal -->
    <div class="modal fade" id="roomManagementModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomManagementModalLabel">Manage Room</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="roomManagementForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="room_id" id="roomId">
                    <input type="hidden" name="room_type_id" id="room_type_id" value="1">
                    
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="roomTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">Details</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-3" id="roomTabsContent">
                            <div class="tab-pane fade show active" id="details" role="tabpanel">
                                <div class="form-group">
                                    <label for="room_number">Room Number *</label>
                                    <input type="text" class="form-control" id="room_number" name="room_number" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="available">Active</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger mr-auto" id="deleteRoomBtn" style="display: none;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveRoomBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Room Type Modal (old) -->
    <div class="modal fade" id="addRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="addRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomTypeModalLabel">Add New Room Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addRoomTypeForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_room_type">
                        
                        <div class="form-group">
                            <label for="room_type">Room Type Name</label>
                            <input type="text" class="form-control" id="room_type" name="room_type" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Price per Night (₱)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="capacity">Capacity (Persons)</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>

                        <div class="form-group">
                            <label for="beds">Beds Configuration</label>
                            <input type="text" class="form-control" id="beds" name="beds" 
                                   placeholder="e.g., 1 Queen Bed, 2 Single Beds" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" 
                                   step="0.1" min="0" max="5" required>
                        </div>

                        <div class="form-group">
                            <label for="image">Room Main Image</label>
                            <input type="file" class="form-control-file" id="image" name="image" 
                                   accept="image/*" required>
                            <small class="form-text text-muted">Upload the main image of the room (JPG, PNG formats)</small>
                        </div>

                        <div class="form-group">
                            <label for="image2">Additional Image 1</label>
                            <input type="file" class="form-control-file" id="image2" name="image2" 
                                   accept="image/*">
                            <small class="form-text text-muted">Upload an additional image for the room carousel (Optional)</small>
                            <div id="image2Preview" class="mt-2"></div>
                        </div>

                        <div class="form-group">
                            <label for="image3">Additional Image 2</label>
                            <input type="file" class="form-control-file" id="image3" name="image3" 
                                   accept="image/*">
                            <small class="form-text text-muted">Upload another additional image for the room carousel (Optional)</small>
                            <div id="image3Preview" class="mt-2"></div>
                        </div>

                        <div class="form-group">
                            <label for="discount_percent">Discount Percentage (%)</label>
                            <input type="number" class="form-control" id="discount_percent" 
                                   name="discount_percent" min="0" max="100">
                        </div>

                        <div class="form-group">
                            <label for="discount_valid_until">Discount Valid Until</label>
                            <input type="date" class="form-control" id="discount_valid_until" 
                                   name="discount_valid_until">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Room Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Room Type Modal -->
    <div class="modal fade" id="viewRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="viewRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewRoomTypeModalLabel">Room Type Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 id="viewRoomTypeName"></h5>
                                <p class="text-muted mb-0" id="viewRoomTypeDescription"></p>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Price/Night:</strong></p>
                                    <h4 id="viewRoomTypePrice"></h4>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Beds:</strong></p>
                                    <h5 id="viewRoomTypeBeds"></h5>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="mb-1"><strong>Status:</strong></p>
                                <span class="badge" id="viewRoomTypeStatus"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="room-image-container">
                                <img id="viewRoomTypeImage" src="" alt="Room Image" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Amenities:</h6>
                        <div class="d-flex flex-wrap gap-2" id="viewRoomTypeAmenities">
                            <!-- Amenities will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="saveRoomForm" method="POST" action="room_management.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_room">
                    
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="roomTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="details-tab" data-toggle="tab" href="#roomDetails" role="tab">Room Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="images-tab" data-toggle="tab" href="#roomImages" role="tab">Images</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-3" id="roomTabsContent">
                            <!-- Room Details Tab -->
                            <div class="tab-pane fade show active" id="roomDetails" role="tabpanel">
                                <div class="form-group">
                                    <label for="add_room_room_type">Room Type *</label>
                                    <select class="form-control" id="add_room_room_type" name="room_type_id" required onchange="toggleOtherRoomType()">
                                        <option value="">-- Select Room Type --</option>
                                        <?php
                                        $room_types = $con->query("SELECT room_type_id, room_type FROM room_types WHERE status = 'active' ORDER BY room_type");
                                        while ($type = $room_types->fetch_assoc()) {
                                            echo "<option value='" . $type['room_type_id'] . "'>" . htmlspecialchars($type['room_type']) . "</option>";
                                        }
                                        ?>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <!-- Add this new div for custom room type input -->
                                <div class="form-group" id="add_room_other_room_type_container" style="display: none;">
                                    <label for="add_room_other_room_type">Specify Room Type *</label>
                                    <input type="text" class="form-control" id="add_room_other_room_type" name="other_room_type" placeholder="Enter custom room type">
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="room_number">Room Number *</label>
                                            <input type="text" class="form-control" id="room_number" name="room_number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price per Night (₱) *</label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="capacity">Capacity *</label>
                                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="beds">Beds *</label>
                                    <input type="text" class="form-control" id="beds" name="beds" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="discount_percent">Discount Percent (%)</label>
                                            <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status *</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Images Tab -->
                            <div class="tab-pane fade" id="roomImages" role="tabpanel">
                                <div class="form-group">
                                    <label>Main Image *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*" required>
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">This will be the primary image for the room.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Additional Image 1</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image2" name="image2" accept="image/*">
                                        <label class="custom-file-label" for="image2">Choose file</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Additional Image 2</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image3" name="image3" accept="image/*">
                                        <label class="custom-file-label" for="image3">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveRoomBtn" onclick="event.preventDefault(); document.getElementById('saveRoomForm').submit();">Save Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Type Modal -->
    <div class="modal fade" id="editRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="editRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editRoomTypeModalLabel">Edit Room Type</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="editRoomTypeContent">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading room type details...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog" aria-labelledby="editRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomModalLabel">Edit Room</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editRoomForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_room">
                        <input type="hidden" id="edit_room_id" name="room_id">
                        
                        <div class="form-group">
                            <label for="edit_room_type_select">Room Type</label>
                            <select class="form-control" id="edit_room_type_select" name="room_type_id" required>
                                <?php foreach ($room_types as $type): ?>
                                <option value="<?php echo $type['room_type_id']; ?>">
                                    <?php echo htmlspecialchars($type['room_type']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_room_capacity">Number of Rooms</label>
                            <input type="number" class="form-control" id="edit_room_capacity" name="capacity" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_room_status">Status</label>
                            <select class="form-control" id="edit_room_status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Amenities Modal -->
    <div class="modal fade" id="editAmenitiesModal" tabindex="-1" role="dialog" aria-labelledby="editAmenitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAmenitiesModalLabel">Edit Room Amenities</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editAmenitiesForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_room_amenities">
                        <input type="hidden" id="amenities_room_type_id" name="room_type_id">
                        
                        <div class="room-type-name mb-3">
                            <h5 id="amenities_room_type_name"></h5>
                        </div>
                        
                        <div class="form-group">
                            <label>Select Amenities</label>
                            <div id="amenities_container" class="d-flex flex-wrap">
                                <!-- Amenities will be loaded here via JavaScript -->
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Amenities</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Amenities Modal -->
    <div class="modal fade" id="manageAmenitiesModal" tabindex="-1" role="dialog" aria-labelledby="manageAmenitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageAmenitiesModalLabel">Manage Amenities</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Available Amenities</h6>
                        <button type="button" class="btn btn-sm btn-success" id="addNewAmenityBtn">
                            <i class="fas fa-plus"></i> Add New Amenity
                        </button>
                    </div>
                    
                    <div id="amenitiesList" class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Icon</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Amenities will be loaded here -->
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Add/Edit Amenity Form (hidden by default) -->
                    <div id="amenityForm" style="display: none;">
                        <hr>
                        <h6 id="amenityFormTitle">Add New Amenity</h6>
                        <form id="addEditAmenityForm">
                            <input type="hidden" id="amenity_id" name="amenity_id" value="0">
                            <input type="hidden" id="amenity_action" name="action" value="add_amenity">
                            
                            <div class="form-group">
                                <label for="amenity_name">Amenity Name</label>
                                <input type="text" class="form-control" id="amenity_name" name="amenity_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="amenity_icon">Icon Class (FontAwesome)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i id="iconPreview" class="fas fa-check"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="amenity_icon" name="amenity_icon" 
                                           placeholder="e.g., fas fa-wifi" onkeyup="updateIconPreview()">
                                </div>
                                <small class="form-text text-muted">
                                    Enter a FontAwesome icon class. You can find icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com</a>
                                </small>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-secondary" onclick="cancelAmenityForm()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Amenity</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include required JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
    /* Ensure modal is visible */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 1.75rem auto;
        max-width: 800px;
    }
    
    .modal-content {
        position: relative;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 0.3rem;
        outline: 0;
    }
    
    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }
    
    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 1rem;
        border-top: 1px solid #e9ecef;
        border-bottom-right-radius: 0.3rem;
        border-bottom-left-radius: 0.3rem;
    }
    </style>
    
    <script>
    // Function to load room type data
    function loadRoomTypeData(roomTypeId, $modal) {
        console.log('=== LOADING ROOM TYPE DATA ===');
        console.log('Room Type ID:', roomTypeId);
        
        // Show loading state
        $modal.find('.modal-body').html(`
            <div class="text-center p-4">
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6>Available Amenities</h6>
                    <button type="button" class="btn btn-sm btn-success" id="addNewAmenityBtn">
                        <i class="fas fa-plus"></i> Add New Amenity
                    </button>
                </div>
                
                <div id="amenitiesList" class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Amenities will be loaded here -->
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Add/Edit Amenity Form (hidden by default) -->
                <div id="amenityForm" style="display: none;">
                    <hr>
                    <h6 id="amenityFormTitle">Add New Amenity</h6>
                    <form id="addEditAmenityForm">
                        <input type="hidden" id="amenity_id" name="amenity_id" value="0">
                        <input type="hidden" id="amenity_action" name="action" value="add_amenity">
                        
                        <div class="form-group">
                            <label for="amenity_name">Amenity Name</label>
                            <input type="text" class="form-control" id="amenity_name" name="amenity_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="amenity_icon">Icon Class (FontAwesome)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i id="iconPreview" class="fas fa-check"></i></span>
                                </div>
                                <input type="text" class="form-control" id="amenity_icon" name="amenity_icon" 
                                       placeholder="e.g., fas fa-wifi" onkeyup="updateIconPreview()">
                            </div>
                            <small class="form-text text-muted">
                                Enter a FontAwesome icon class. You can find icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com</a>
                            </small>
                        </div>
                        
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-secondary" onclick="cancelAmenityForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Amenity</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include required JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
/* Ensure modal is visible */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}
    
.modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 800px;
}
    
.modal-content {
    position: relative;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem;
    outline: 0;
}
    
.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    border-top-left-radius: 0.3rem;
    border-top-right-radius: 0.3rem;
}
    
.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}
    
.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    border-bottom-right-radius: 0.3rem;
    border-bottom-left-radius: 0.3rem;
}
        dataType: 'json',
        cache: false,
        timeout: 30000, // 30 second timeout
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    updateStatus(`Loading: ${Math.round((e.loaded / e.total) * 100)}%`);
                }
            });
            return xhr;
        },
        beforeSend: function() {
            updateStatus('Connecting to server...');
        },
        success: function(response) {
            console.log('AJAX Success:', response);
            
            if (response && response.success && response.data) {
                updateStatus('Rendering form...');
                if (typeof renderRoomTypeForm === 'function') {
                    renderRoomTypeForm(response.data, $modal);
                } else {
                    throw new Error('renderRoomTypeForm function not found');
                }
            } else {
                throw new Error(response && response.message || 'No valid data received from server');
            }
        },
        error: function(xhr, status, error) {
            if (status === 'timeout') {
                handleError('Request timed out. The server is taking too long to respond.', { status: 'timeout' });
            } else {
                let errorMessage = 'Failed to load room type data';
                let errorDetails = null;
                
                try {
                    const response = xhr.responseJSON || (xhr.responseText ? JSON.parse(xhr.responseText) : null);
                    if (response && response.message) {
                        errorMessage = response.message;
                        errorDetails = response;
                    } else {
                        errorMessage = `Server returned ${xhr.status}: ${xhr.statusText || 'Unknown error'}`;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                    errorMessage = `Server returned ${xhr.status}: ${xhr.statusText || 'Unknown error'}`;
                }
                
                handleError(errorMessage, errorDetails || error);
            }
        },
        complete: function() {
            updateStatus('Request completed');
        }
    });
}

// Check for required dependencies
function checkDependencies() {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return false;
    }
    if (typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap JS is not loaded');
        return false;
    }
    return true;
}

// Make sure jQuery is loaded
if (typeof jQuery == 'undefined') {
    document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
}

// Global function to show alerts
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove any existing alerts
    $('.alert-dismissible').alert('close');
    
    // Add new alert
    $('.main-content').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}

// Update file input labels
$('.custom-file-input').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass('selected').html(fileName);
    
    // Show image preview
    const previewId = $(this).attr('id') + 'Preview';
    const preview = $('#' + previewId);
    $('[data-toggle="tooltip"]').tooltip();
});

// Handle edit room type button click
$(document).on('click', '.edit-room-type', function(e) {
    console.log('=== EDIT BUTTON CLICKED ===');
    e.preventDefault();
    e.stopPropagation();
    
    const roomTypeId = $(this).data('id');
    const $modal = $('#editRoomTypeModal');
    
    console.log('Initializing modal for room type ID:', roomTypeId);
    
    // Ensure modal is properly initialized
    try {
        $modal.modal({backdrop: 'static', keyboard: false});
        console.log('Modal initialized');
    } catch (err) {
        console.error('Error initializing modal:', err);
    }
    
    // Show loading state
    const loadingHtml = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading room type details...</p>
            <div class="debug-info small text-muted mt-2">
                <div>Fetching data for room type ID: ${roomTypeId}</div>
                <div id="debugStatus">Status: Initializing request...</div>
                <div id="debugResponse" class="text-left small mt-2"></div>
            </div>
        </div>
    `;
    
    try {
        $modal.find('.modal-body').html(loadingHtml);
        console.log('Set loading content in modal');
    } catch (err) {
        console.error('Error setting loading content:', err);
    }
    
    // Show the modal with proper classes
    try {
        $modal.modal('show');
        $modal.addClass('show').css('display', 'block');
        $('body').addClass('modal-open');
        $('.modal-backdrop').addClass('show');
        console.log('Modal shown');
    } catch (err) {
        console.error('Error showing modal:', err);
    }
    
    // Add debug info updater
    window.updateDebugInfo = function(message) {
        const $debug = $modal.find('#debugResponse');
        const timestamp = new Date().toLocaleTimeString();
        $debug.append(`<div>[${timestamp}] ${message}</div>`);
        $debug.scrollTop($debug[0].scrollHeight);
    };
    
    // Load room type data via AJAX
    console.log('Calling loadRoomTypeData');
    updateDebugInfo('Starting to load room type data...');
    loadRoomTypeData(roomTypeId, $modal);
});

// Initialize the room management modal
const roomModal = $('#roomManagementModal');

// Handle manage room button click
$(document).on('click', '#manageRoomBtn', function() {
    const action = $(this).data('action');
    const roomId = $(this).data('id');
    const roomTypeId = $(this).data('room-type-id');
    
    // Reset form
    $('#roomManagementForm')[0].reset();
    $('.image-preview').html('');
    
    // Set form action and title
    $('#formAction').val(action);
    $('#roomId').val(roomId || '');
    $('#roomTypeId').val(roomTypeId || '');
    
    // Update modal title and buttons based on action
    if (action === 'add') {
        $('#roomManagementModalLabel').text('Add New Room');
        $('#saveRoomBtn').text('Add Room');
        $('#deleteRoomBtn').hide();
    } else {
        $('#roomManagementModalLabel').text('Edit Room');
        $('#saveRoomBtn').text('Update Room');
        $('#deleteRoomBtn').show();
        
        // Load room data via AJAX
        $.get('get_room.php', { id: roomId }, function(data) {
            if (data.success) {
                const room = data.room;
                $('#room_type').val(room.room_type);
                $('#price').val(room.price);
                $('#capacity').val(room.capacity);
                $('#beds').val(room.beds);
                $('#description').val(room.description);
                
                // Show image previews if they exist
                if (room.image) {
                    $('#mainImagePreview').html(`<img src="${room.image}" class="img-thumbnail" style="max-height: 100px;">`);
                }
                if (room.image2) {
                    $('#image2Preview').html(`<img src="${room.image2}" class="img-thumbnail" style="max-height: 100px;">`);
                }
                if (room.image3) {
                    $('#image3Preview').html(`<img src="${room.image3}" class="img-thumbnail" style="max-height: 100px;">`);
                }
                
                // Load amenities
                loadAmenities(room.room_type_id);
            }
        }, 'json');
    }
    
    // Show the modal
    roomModal.modal('show');
});

// Handle form submission
$('#roomManagementForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const action = $('#formAction').val();
    
    $.ajax({
        url: 'process_room.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const result = typeof response === 'string' ? JSON.parse(response) : response;
            if (result.success) {
                showAlert('success', result.message);
                roomModal.modal('hide');
                // Refresh the room list
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('danger', result.message || 'An error occurred');
            }
        },
        error: function() {
            showAlert('danger', 'An error occurred while processing your request');
        }
    });
});

// Handle delete button click
$('#deleteRoomBtn').on('click', function() {
    if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
        const roomId = $('#roomId').val();
        
        $.ajax({
            url: 'room_management.php',
            type: 'POST',
            data: {
                action: 'delete_room',
                room_id: roomId,
                _token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message and remove the row
                    showAlert('success', response.message);
                    $('#roomManagementModal').modal('hide');
                    // Refresh the page to show updated room list
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    // Show error message
                    showAlert('danger', response.message || 'An error occurred while deleting the room');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while processing your request');
            }
        });
    }
});
            console.error('jQuery is not loaded');
            return false;
        }
        if (typeof $.fn.modal === 'undefined') {
            console.error('Bootstrap JS is not loaded');
            return false;
        }
        return true;
    }
    
    // Make sure jQuery is loaded
    if (typeof jQuery == 'undefined') {
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
    }
    
    // Global function to show alerts
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove any existing alerts
        $('.alert-dismissible').alert('close');
        
        // Add new alert
        $('.main-content').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
    
    // Update file input labels
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass('selected').html(fileName);
        
        // Show image preview
                const action = $(this).data('action');
                const roomId = $(this).data('id');
                const roomTypeId = $(this).data('room-type-id');
                
                // Reset form
                $('#roomManagementForm')[0].reset();
                $('.image-preview').html('');
                
                // Set form action and title
                $('#formAction').val(action);
                $('#roomId').val(roomId || '');
                $('#roomTypeId').val(roomTypeId || '');
                
                // Update modal title and buttons based on action
                if (action === 'add') {
                    $('#roomManagementModalLabel').text('Add New Room');
                    $('#saveRoomBtn').text('Add Room');
                    $('#deleteRoomBtn').hide();
                } else {
                    $('#roomManagementModalLabel').text('Edit Room');
                    $('#saveRoomBtn').text('Update Room');
                    $('#deleteRoomBtn').show();
                    
                    // Load room data via AJAX
                    $.get('get_room.php', { id: roomId }, function(data) {
                        if (data.success) {
                            const room = data.room;
                            $('#room_type').val(room.room_type);
                            $('#price').val(room.price);
                            $('#capacity').val(room.capacity);
                            $('#beds').val(room.beds);
                            $('#description').val(room.description);
                            
                            // Show image previews if they exist
                            if (room.image) {
                                $('#mainImagePreview').html(`<img src="${room.image}" class="img-thumbnail" style="max-height: 100px;">`);
                            }
                            if (room.image2) {
                                $('#image2Preview').html(`<img src="${room.image2}" class="img-thumbnail" style="max-height: 100px;">`);
                            }
                            if (room.image3) {
                                $('#image3Preview').html(`<img src="${room.image3}" class="img-thumbnail" style="max-height: 100px;">`);
                            }
                            
                            // Load amenities
                            loadAmenities(room.room_type_id);
                        }
                    }, 'json');
                }
                
                // Show the modal
                roomModal.modal('show');
            });
            
            // Handle form submission
            $('#roomManagementForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = $('#formAction').val();
                
                $.ajax({
                    url: 'process_room.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.success) {
                            showAlert('success', result.message);
                            roomModal.modal('hide');
                            // Refresh the room list
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showAlert('danger', result.message || 'An error occurred');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'An error occurred while processing your request');
                    }
                });
            });
            
            // Handle delete button click
            $('#deleteRoomBtn').on('click', function() {
                if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                    const roomId = $('#roomId').val();
                    
                    $.ajax({
                        url: 'room_management.php',
                        type: 'POST',
                        data: {
                            action: 'delete_room',
                            room_id: roomId,
                            _token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Show success message and remove the row
                                showAlert('success', response.message);
                                $('#roomManagementModal').modal('hide');
                                // Refresh the page to show updated room list
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'An error occurred while deleting the room');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            showAlert('danger', 'An error occurred while processing your request');
                        }
                    });
                }
            });
            
            // Load amenities for the room type
            function loadAmenities(roomTypeId) {
                $.get('get_amenities.php', { room_type_id: roomTypeId }, function(data) {
                    const container = $('#amenitiesContainer');
                    container.empty();
                    
                    if (data.length > 0) {
                        data.forEach(amenity => {
                            container.append(`
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="amenity_${amenity.id}" 
                                               name="amenities[]" value="${amenity.id}" ${amenity.selected ? 'checked' : ''}>
                                        <label class="custom-control-label" for="amenity_${amenity.id}">
                                            ${amenity.name}
                                        </label>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        container.html('<div class="col-12">No amenities found.</div>');
                    }
                }, 'json');
            }
            
            // Show alert message
            function showAlert(type, message) {
                const alert = $(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
                
                $('.main-content').prepend(alert);
                
                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    alert.alert('close');
                }, 5000);
            }
            
            // Update file input labels
            $('.custom-file-input').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass('selected').html(fileName);
                
                // Show image preview
                const previewId = $(this).attr('id') + 'Preview';
                const preview = $('#' + previewId);
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.html(`<img src="${e.target.result}" class="img-thumbnail mt-2" style="max-height: 100px;">`);
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            console.log('jQuery is working');
            // Test button click
            $('#addRoomTypeBtn').on('click', function() {
                console.log('Button clicked');
                $('#addRoomTypeModal').modal('show');
            });

            // Add Room button click handler
            $('#addRoomBtn').on('click', function() {
                $('#addRoomModal').modal('show');
            });

            // Form submission handlers
            $('#addRoomTypeForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'room_management.php',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert(result.message);
                                $('#addRoomTypeModal').modal('hide');
                                location.reload();
                            } else {
                                alert(result.message || 'Error adding room type');
                            }
                        } catch (e) {
                            alert('Error processing response');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error adding room type: ' + error);
                    }
                });
            });


            // Clear form when modal is closed
            $('#addRoomTypeModal, #addRoomModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
            });

            // Image preview
            $('#image, #image2, #image3').change(function() {
                const file = this.files[0];
                const previewId = this.id + 'Preview';
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (!$('#' + previewId).length) {
                            $('<div id="' + previewId + '" class="mt-2">' +
                              '<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 200px">' +
                              '</div>').insertAfter('#' + this.id);
                        } else {
                            $('#' + previewId + ' img').attr('src', e.target.result);
                        }
                    }.bind(this);
                    reader.readAsDataURL(file);
                }
            });

            // Clear image previews when modal is closed
            $('#addRoomTypeModal').on('hidden.bs.modal', function() {
                $('#imagePreview, #image2Preview, #image3Preview').remove();
            });
        });
        
        // Function to show alert message
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
            
            // Remove any existing alerts
            $('.alert-dismissible').alert('close');
            
            // Add new alert
            $('body').prepend(alertHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                $('.alert-dismissible').alert('close');
            }, 5000);
        }

        // Debug: Log when document is ready
        console.log('Document ready - Room Management');
        
        // Check if required libraries are loaded
        console.log('jQuery version:', $.fn.jquery);
        console.log('Bootstrap version:', $.fn.tooltip ? 'Loaded' : 'Not loaded');
        console.log('Bootstrap Modal:', $.fn.modal ? 'Loaded' : 'Not loaded');
        
        // Debug: Log all elements with edit-room-type class
        console.log('Edit buttons found:', $('.edit-room-type').length);
        $('.edit-room-type').each(function() {
            console.log('Edit button data:', $(this).data());
        });
        
        // Debug: List all modals in the DOM
        console.log('Modals in DOM:');
        $('.modal').each(function() {
            console.log('Modal ID:', $(this).attr('id'));
        });
        
        // Single event handler for edit room type button
        $(document).on('click', '.edit-room-type', function(e) {
            console.log('=== EDIT BUTTON CLICKED ===');
            console.log('Event target:', e.target);
            console.log('Button data:', $(this).data());
            
            e.preventDefault();
            e.stopPropagation();
            
            const roomTypeId = $(this).data('id');
            const $modal = $('#editRoomTypeModal');
            
            // Show the modal immediately
            $modal.modal('show').css('display', 'block').addClass('show');
            
            // Load room type data
            loadRoomTypeData(roomTypeId, $modal);
        });
        
        // Function to load room type data
        function loadRoomTypeData(roomTypeId, $modal) {
            console.log('Loading room type data for ID:', roomTypeId);
            
            // No loading state - proceed directly to AJAX call
            
            $.ajax({
                url: 'get_room_type.php',
                type: 'GET',
                data: { id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    
                    if (response && response.success && response.data) {
                        console.log('Room type data received:', response.data);
                        renderRoomTypeForm(response.data, $modal);
                    } else {
                        const errorMsg = response ? response.message : 'No response from server';
                        console.error('Error in response:', errorMsg);
                        throw new Error(errorMsg || 'Failed to load room type data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    let errorMsg = 'Error loading room type: ' + error;
                    if (xhr.responseText) {
                        try {
                            const jsonResponse = JSON.parse(xhr.responseText);
                            errorMsg = jsonResponse.message || errorMsg;
                        } catch (e) {
                            errorMsg += '\nResponse: ' + xhr.responseText.substring(0, 200);
                        }
                    }
                    
                    showAlert('danger', errorMsg);
                    $modal.modal('hide');
                }
            });
        }
        
        // Function to render the room type edit form
        function renderRoomTypeForm(roomType, $modal) {
            console.log('Rendering room type form for:', roomType);
            
            // Format price for display
            const price = parseFloat(roomType.price || 0).toFixed(2);
            const adultExtra = parseFloat(roomType.adult_extra_bed_charge || 0).toFixed(2);
            const childExtra = parseFloat(roomType.child_extra_bed_charge || 0).toFixed(2);
            
            // Build the form HTML with all editable fields
            let formHtml = `
                <form id="editRoomTypeForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_room_type">
                    <input type="hidden" name="room_type_id" value="${roomType.room_type_id}">
                    
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Basic Information</h5>
                            <span class="badge ${roomType.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                ${roomType.status || 'inactive'}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Room Type Name *</label>
                                        <input type="text" class="form-control" name="room_type" value="${escapeHtml(roomType.room_type || '')}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active" ${roomType.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="inactive" ${roomType.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                            <option value="maintenance" ${roomType.status === 'maintenance' ? 'selected' : ''}>Under Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price per Night (₱) *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="price" value="${price}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="discount_percent" value="${roomType.discount_percent || 0}" min="0" max="100">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Capacity (Adults) *</label>
                                        <input type="number" class="form-control" name="capacity" value="${roomType.capacity || 1}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Max Extra Beds</label>
                                        <input type="number" class="form-control" name="max_extra_beds" value="${roomType.max_extra_beds || 0}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Room Size (sqm)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="size" value="${roomType.size || ''}" step="0.01" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3">${escapeHtml(roomType.description || '')}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Adult Extra Bed Charge (₱)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="adult_extra_bed_charge" value="${adultExtra}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Child Extra Bed Charge (₱)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="child_extra_bed_charge" value="${childExtra}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Number of Beds</label>
                                        <input type="number" class="form-control" name="beds" value="${roomType.beds || 1}" min="1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Room Size (sqm)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="size" value="${roomType.size || ''}" step="0.01" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3">${escapeHtml(roomType.description || '')}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Pricing</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Price per Night (₱) *</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="price" value="${roomType.price || '0'}" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Adult Extra Bed (₱)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="adult_extra_bed_charge" value="${roomType.adult_extra_bed_charge || '0'}" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Child Extra Bed (₱)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₱</span>
                                            </div>
                                            <input type="number" class="form-control" name="child_extra_bed_charge" value="${roomType.child_extra_bed_charge || '0'}" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount (%)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="discount_percent" value="${roomType.discount_percent || '0'}" min="0" max="100" step="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Max Extra Beds</label>
                                        <input type="number" class="form-control" name="max_extra_beds" value="${roomType.max_extra_beds || '0'}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Room Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Capacity (Adults) *</label>
                                        <input type="number" class="form-control" name="capacity" value="${roomType.capacity || '1'}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Max Children</label>
                                        <input type="number" class="form-control" name="max_children" value="${roomType.max_children || '0'}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Room View</label>
                                        <select class="form-control" name="view">
                                            <option value="" ${!roomType.view ? 'selected' : ''}>Select View</option>
                                            <option value="garden" ${roomType.view === 'garden' ? 'selected' : ''}>Garden View</option>
                                            <option value="pool" ${roomType.view === 'pool' ? 'selected' : ''}>Pool View</option>
                                            <option value="ocean" ${roomType.view === 'ocean' ? 'selected' : ''}>Ocean View</option>
                                            <option value="mountain" ${roomType.view === 'mountain' ? 'selected' : ''}>Mountain View</option>
                                            <option value="city" ${roomType.view === 'city' ? 'selected' : ''}>City View</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Max Occupancy</label>
                                        <input type="number" class="form-control" name="max_occupancy" value="${roomType.max_occupancy || roomType.capacity || '1'}" min="1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Floor</label>
                                        <input type="text" class="form-control" name="floor" value="${escapeHtml(roomType.floor || '')}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Beds</label>
                                        <input type="text" class="form-control" name="beds" value="${escapeHtml(roomType.beds || '')}" placeholder="e.g., 1 Queen Bed">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Room Size (sqm)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="size" value="${roomType.size || ''}" step="0.01">
                                            <div class="input-group-append">
                                                <span class="input-group-text">sqm</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>View</label>
                                <input type="text" class="form-control" name="view" value="${escapeHtml(roomType.view || '')}" placeholder="e.g., Ocean view, City view">
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Images</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Current Image</label>
                                <div class="mb-2">
                                    ${roomType.image ? 
                                        `<img src="${escapeHtml(roomType.image)}" class="img-thumbnail" style="max-height: 150px;" alt="Room Image" id="currentImage">` : 
                                        '<span class="text-muted">No image available</span>'}
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                    <label class="custom-file-label" for="image">Choose new image (optional)</label>
                                </div>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Amenities</h5>
                        </div>
                        <div class="card-body">
                            <div id="amenitiesContainer" class="row">
                                <!-- Amenities will be loaded here -->
                                <div class="col-12">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    Loading amenities...
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                    
                    <!-- Amenities Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Amenities</h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="amenitiesContainer">
                                <!-- Amenities will be loaded here via AJAX -->
                                <div class="col-12 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p>Loading amenities...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Images Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Room Images</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Main Image -->
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-2 text-center">
                                        <img id="mainImagePreview" src="${roomType.image ? 'uploads/room_types/' + roomType.image : 'assets/img/no-image.png'}" 
                                             class="img-fluid mb-2" style="max-height: 150px; width: auto;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="mainImage" name="main_image" accept="image/*">
                                            <label class="custom-file-label" for="mainImage">Change Main Image</label>
                                        </div>
                                        <small class="form-text text-muted">Main display image (recommended: 800x600px)</small>
                                    </div>
                                </div>
                                
                                <!-- Additional Images -->
                                <div class="col-md-6">
                                    <div class="border rounded p-2 text-center">
                                        <div id="additionalImagesPreview" class="mb-2">
                                            ${roomType.image2 || roomType.image3 || roomType.image4 ? 
                                                `
                                                <div class="row">
                                                    ${roomType.image2 ? `
                                                        <div class="col-6 mb-2">
                                                            <img src="uploads/room_types/${roomType.image2}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                                            <div class="form-check mt-1">
                                                                <input type="checkbox" class="form-check-input" name="delete_images[]" value="${roomType.image2}" id="deleteImage2">
                                                                <label class="form-check-label small" for="deleteImage2">Delete</label>
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                                    ${roomType.image3 ? `
                                                        <div class="col-6 mb-2">
                                                            <img src="uploads/room_types/${roomType.image3}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                                            <div class="form-check mt-1">
                                                                <input type="checkbox" class="form-check-input" name="delete_images[]" value="${roomType.image3}" id="deleteImage3">
                                                                <label class="form-check-label small" for="deleteImage3">Delete</label>
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                                    ${roomType.image4 ? `
                                                        <div class="col-6 mb-2">
                                                            <img src="uploads/room_types/${roomType.image4}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                                            <div class="form-check mt-1">
                                                                <input type="checkbox" class="form-check-input" name="delete_images[]" value="${roomType.image4}" id="deleteImage4">
                                                                <label class="form-check-label small" for="deleteImage4">Delete</label>
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                                </div>
                                                ` : 
                                                '<p class="text-muted mb-0">No additional images</p>'
                                            }
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="additionalImages" name="additional_images[]" multiple accept="image/*">
                                            <label class="custom-file-label" for="additionalImages">Add More Images</label>
                                        </div>
                                        <small class="form-text text-muted">Additional images (max 3, 800x600px recommended)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Terms & Conditions</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Check-in/Check-out Policy</label>
                                <textarea class="form-control" name="check_in_out_policy" rows="2">${escapeHtml(roomType.check_in_out_policy || '')}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Cancellation Policy</label>
                                <textarea class="form-control" name="cancellation_policy" rows="2">${escapeHtml(roomType.cancellation_policy || '')}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Additional Information</label>
                                <textarea class="form-control" name="additional_info" rows="2">${escapeHtml(roomType.additional_info || '')}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>
                        <div>
                            <button type="button" class="btn btn-danger mr-2" id="deleteRoomTypeBtn">
                                <i class="fas fa-trash-alt mr-1"></i> Delete
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
                
                <script>
                // Load amenities for this room type
                function loadAmenities() {
                    $.ajax({
                        url: 'get_amenities.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.data) {
                                let amenitiesHtml = '';
                                const roomAmenities = ${JSON.stringify(roomType.amenities || [])};
                                const amenityIds = roomAmenities.map(a => parseInt(a.id));
                                
                                response.data.forEach(amenity => {
                                    const isChecked = amenityIds.includes(parseInt(amenity.amenity_id));
                                    amenitiesHtml += `
                                        <div class="col-md-4 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="amenity_${amenity.amenity_id}" 
                                                       name="amenities[]" 
                                                       value="${amenity.amenity_id}" 
                                                       ${isChecked ? 'checked' : ''}>
                                                <label class="custom-control-label" for="amenity_${amenity.amenity_id}">
                                                    <i class="${amenity.icon_class || 'fas fa-check'} mr-1"></i> ${amenity.name}
                                                </label>
                                            </div>
                                        </div>`;
                                });
                                
                                $('#amenitiesContainer').html(amenitiesHtml);
                            } else {
                                $('#amenitiesContainer').html('<div class="col-12 text-danger">Failed to load amenities. ' + (response.message || '') + '</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading amenities:', error);
                            $('#amenitiesContainer').html('<div class="col-12 text-danger">Error loading amenities. Please try again.</div>');
                        }
                    });
                }
                
                // Initialize image previews
                function initImagePreviews() {
                    // Main image preview
                    $('#mainImage').on('change', function() {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('#mainImagePreview').attr('src', e.target.result);
                            }
                            reader.readAsDataURL(file);
                            $(this).next('.custom-file-label').text(file.name);
                        }
                    });
                    
                    // Additional images preview
                    $('#additionalImages').on('change', function() {
                        const files = this.files;
                        let previews = '';
                        
                        if (files.length > 0) {
                            for (let i = 0; i < Math.min(files.length, 3); i++) {
                                const file = files[i];
                                const reader = new FileReader();
                                
                                reader.onload = (function(file) {
                                    return function(e) {
                                        const previewId = 'preview-' + Date.now() + '-' + i;
                                        const newPreview = `
                                            <div class="col-6 mb-2" id="${previewId}">
                                                <img src="${e.target.result}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                                <div class="form-check mt-1">
                                                    <input type="checkbox" class="form-check-input" name="remove_previews[]" value="${previewId}" id="remove_${previewId}">
                                                    <label class="form-check-label small" for="remove_${previewId}">Remove</label>
                                                </div>
                                            </div>`;
                                        
                                        if ($('#additionalImagesPreview').find('.row').length === 0) {
                                            $('#additionalImagesPreview').html('<div class="row"></div>');
                                        }
                                        
                                        $('#additionalImagesPreview .row').append(newPreview);
                                        
                                        // Handle remove preview
                                        $(`#remove_${previewId}`).on('change', function() {
                                            if (this.checked) {
                                                $(`#${previewId}`).remove();
                                            }
                                        });
                                    };
                                })(file);
                                
                                reader.readAsDataURL(file);
                            }
                            
                            if (files.length > 3) {
                                showAlert('warning', 'Only the first 3 images will be uploaded. Maximum of 3 additional images allowed.');
                            }
                            
                            $(this).next('.custom-file-label').text(`${files.length} file(s) selected`);
                        }
                    });
                }
                
                // Initialize form
                $(document).ready(function() {
                    // Load amenities
                    loadAmenities();
                    
                    // Initialize image previews
                    initImagePreviews();
                    
                    // Handle delete room type button
                    $('#deleteRoomTypeBtn').on('click', function() {
                        if (confirm('Are you sure you want to delete this room type? This action cannot be undone.')) {
                            // Add delete action here
                            console.log('Deleting room type:', ${roomType.room_type_id});
                            // You can add AJAX call to delete the room type
                        }
                    });
                    
                    // Handle form submission
                    $('#editRoomTypeForm').on('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        
                        // Show loading state
                        const submitBtn = $(this).find('button[type="submit"]');
                        const originalBtnText = submitBtn.html();
                        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                        
                        // Submit form via AJAX
                        $.ajax({
                            url: 'room_management.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                try {
                                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                                    if (result.success) {
                                        showAlert('success', 'Room type updated successfully!');
                                        // Reload the page or update the UI as needed
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        throw new Error(result.message || 'Failed to update room type');
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                    showAlert('danger', 'Error processing response: ' + e.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating room type:', error);
                                showAlert('danger', 'Error updating room type: ' + error);
                            },
                            complete: function() {
                                submitBtn.prop('disabled', false).html(originalBtnText);
                            }
                        });
                    });
                });
                </script>
            `;
            
            // Set the form HTML in the modal
            $modal.find('.modal-body').html(formHtml);
            
            // Initialize file input
            $('.custom-file-input').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
                
                // Show image preview
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html(`
                            <div class="mt-2">
                                <p class="mb-1">New Image Preview:</p>
                                <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;" alt="Preview">
                            </div>
                        `);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Handle form submission
            $('#editRoomTypeForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const formData = new FormData(form);
                const submitBtn = $(form).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                
                // Show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                
                // Submit the form via AJAX
                $.ajax({
                    url: 'room_management.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            showAlert('success', response.message || 'Room type updated successfully');
                            $modal.modal('hide');
                            // Reload the page to reflect changes
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', response.message || 'Failed to update room type');
                            submitBtn.prop('disabled', false).html(originalBtnText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating room type:', error);
                        showAlert('danger', 'Error updating room type: ' + error);
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
            
            // Load amenities for this room type
            loadAmenities(roomType.room_type_id);
        }
        
        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        
        // Function to load amenities for a room type
        function loadAmenities(roomTypeId) {
            const $container = $('#amenitiesContainer');
            
            // Show loading state
            $container.html(`
                <div class="col-12">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    Loading amenities...
                </div>
            `);
            
            // Fetch amenities for this room type
            $.ajax({
                url: 'get_amenities.php',
                type: 'GET',
                data: { room_type_id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    console.log('Amenities response:', response);
                    
                    if (response && response.success && Array.isArray(response.data)) {
                        const amenities = response.data;
                        let html = '';
                        
                        if (amenities.length > 0) {
                            amenities.forEach(amenity => {
                                const isChecked = amenity.is_selected ? 'checked' : '';
                                html += `
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="amenity_${amenity.amenity_id}" 
                                                   name="amenities[]" value="${amenity.amenity_id}" ${isChecked}>
                                            <label class="custom-control-label" for="amenity_${amenity.amenity_id}">
                                                <i class="${amenity.icon || 'fas fa-check-circle'}"></i> ${escapeHtml(amenity.name)}
                                            </label>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = '<div class="col-12"><p class="text-muted">No amenities found. Add amenities in the Amenities section first.</p></div>';
                        }
                        
                        $container.html(html);
                    } else {
                        throw new Error(response.message || 'Failed to load amenities');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading amenities:', error);
                    $container.html(`
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle"></i> Failed to load amenities. ${error}
                            </div>
                        </div>
                    `);
                }
            });
        }
            
            // This duplicate code has been removed as it's now handled by the renderRoomTypeForm function
                            <form id="editRoomTypeForm" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update_room_type">
                                <input type="hidden" name="room_type_id" value="${roomType.room_type_id}">
                                
                                <div class="form-group">
                                    <label for="edit_room_type">Room Type Name</label>
                                    <input type="text" class="form-control" id="edit_room_type" name="room_type" value="${roomType.room_type || ''}" required>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="edit_price">Price per Night (₱)</label>
                                        <input type="number" class="form-control" id="edit_price" name="price" min="0" step="0.01" value="${roomType.price || '0'}" required>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label for="edit_capacity">Capacity</label>
                                        <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" value="${roomType.capacity || '1'}" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_beds">Beds</label>
                                    <input type="text" class="form-control" id="edit_beds" name="beds" value="${roomType.beds || ''}" required>
                                    <small class="form-text text-muted">Example: 1 Queen Bed or 2 Single Beds</small>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="edit_discount_percent">Discount Percent</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="edit_discount_percent" name="discount_percent" min="0" max="100" value="${roomType.discount_percent || '0'}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-6">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" id="edit_status" name="status" required>
                                            <option value="Active" ${roomType.status === 'Active' ? 'selected' : ''}>Active</option>
                                            <option value="Inactive" ${roomType.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_description">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3" required>${roomType.description || ''}</textarea>
                                <div class="form-group">
                                    <label for="edit_capacity">Capacity</label>
                                    <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" value="${roomType.capacity || '1'}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_beds">Beds</label>
                            <input type="text" class="form-control" id="edit_beds" name="beds" value="${roomType.beds || ''}" placeholder="e.g., 1 Queen Bed, 2 Single Beds">
                        </div>
                                        <label class="custom-control-label" for="amenity_${amenity.amenity_id}">
                                            <i class="${amenity.icon || 'fas fa-check-circle'}"></i> ${amenity.name}
                                        </label>
                                    </div>
                                </div>`;
                            $(this).next('.custom-file-label').addClass('selected').html(fileName);
                            
                            // Show image preview
                            const previewId = $(this).attr('id') + '_preview';
                            const file = this.files[0];
                            
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    $(`#${previewId}`).html(`
                                        <img src="${e.target.result}" class="img-thumbnail mt-2" style="max-height: 200px;">
                                    `);
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                        
                        // Handle form submission
                        $('#editRoomTypeForm').on('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(this);
                            
                            $.ajax({
                                url: 'room_management.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    try {
                                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                                        if (result.success) {
                                            showAlert('success', result.message || 'Room type updated successfully!');
                                            modal.modal('hide');
                                            setTimeout(() => location.reload(), 1000);
                                        } else {
                                            showAlert('danger', result.message || 'Failed to update room type');
                                        }
                                    } catch (e) {
                                        showAlert('danger', 'Error processing response: ' + e.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    showAlert('danger', 'Error: ' + error);
                                }
                            });
                        });
                        
                    } else {
                        throw new Error(response.message || 'Failed to load room type data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading room type:', error);
                    showAlert('danger', 'Error loading room type: ' + error);
                    $('#editRoomTypeModal').modal('hide');
                },
                complete: function() {
                    // Restore button state
                    $button.html(originalText).prop('disabled', false);
                }
            });
        });
        
        // Function to load amenities for a room type
        function loadAmenities(roomTypeId) {
            $.ajax({
                url: 'get_amenities.php',
                type: 'GET',
                data: { room_type_id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success && response.data) {
                        const container = $('#amenitiesContainer');
                        let html = '';
                        
                        response.data.forEach(amenity => {
                            html += `
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="amenity_${amenity.amenity_id}" 
                                               name="amenities[]" 
                                               value="${amenity.amenity_id}"
                                               ${amenity.selected ? 'checked' : ''}>
                                        <label class="custom-control-label" for="amenity_${amenity.amenity_id}">
                                            <i class="${amenity.icon || 'fas fa-check-circle'}"></i> ${amenity.name}
                                        </label>
                                    </div>
                                </div>`;
                        });
                        
                        container.html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading amenities:', error);
                }
            });
        }
        
        // Handle delete room type button click
        $(document).on('click', '.delete-room-type', function(e) {
            e.preventDefault();
            
            const roomTypeId = $(this).data('id');
            const button = $(this);
            const originalText = button.html();
            
            if (!confirm('Are you sure you want to delete this room type? This action cannot be undone.')) {
                return;
            }
            
            // Show loading state on the button
            button.html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);
            
            // Send delete request
            $.ajax({
                url: 'room_management.php',
                type: 'POST',
                data: {
                    action: 'delete_room_type',
                    room_type_id: roomTypeId
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        showAlert('success', 'Room type deleted successfully!');
                        // Remove the row from the table
                        button.closest('tr').fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        showAlert('danger', response.message || 'Failed to delete room type');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting room type:', error);
                    showAlert('danger', 'Error deleting room type: ' + error);
                },
                complete: function() {
                    button.html(originalText).prop('disabled', false);
                }
            });
        });
        
        // Helper function to show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            // Remove any existing alerts
            $('.alert-dismissible').alert('close');
            
            // Add new alert
            $('.main-content').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $('.alert-dismissible').alert('close');
            }, 5000);
        }
    </script>
    
    <!-- View Room Type Modal -->
    <div class="modal fade" id="viewRoomTypeModal" tabindex="-1" aria-labelledby="viewRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewRoomTypeModalLabel">View Room Type</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                            <div class="mb-3">
                                <label class="fw-bold">Room Type:</label>
                                <p class="room-type"></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Description:</label>
                                <p class="description"></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Price per Night:</label>
                                <p class="price"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Details</h5>
                            <div class="mb-3">
                                <label class="fw-bold">Beds:</label>
                                <p class="beds"></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Capacity:</label>
                                <p class="capacity"></p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Status:</label>
                                <p class="status"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2">Amenities</h5>
                            <div class="amenities p-3 bg-light rounded">
                                <!-- Filled by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2">Images</h5>
                            <div class="row room-images">
                                <!-- Filled by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Room Modal -->
    <div class="modal fade" id="viewRoomModal" tabindex="-1" aria-labelledby="viewRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewRoomModalLabel">View Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">Room Number:</label>
                        <p class="room-number"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Room Type:</label>
                        <p class="room-type"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <p class="status"></p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Last Updated:</label>
                        <p class="last-updated"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .room-image-container {
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .room-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .amenity-badge {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.85rem;
        }
    </style>
    <script>
        // Function to handle view room type
        function viewRoomType(roomTypeId) {
            console.log('Viewing room type ID:', roomTypeId);
            
            // Show loading state
            const modalBody = document.querySelector('#viewRoomTypeModal .modal-body');
            modalBody.innerHTML = '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Show the modal immediately with loading state
            const viewModal = new bootstrap.Modal(document.getElementById('viewRoomTypeModal'));
            viewModal.show();
            
            // Fetch room type details via AJAX
            $.ajax({
                url: 'get_room_type.php',
                type: 'GET',
                data: { id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    
                    if (response && response.success) {
                        const roomType = response.data;
                        console.log('Room Type Data:', roomType);
                        
                        // Update modal content
                        document.getElementById('viewRoomTypeName').textContent = roomType.room_type || 'N/A';
                        document.getElementById('viewRoomTypeDescription').textContent = roomType.description || 'No description available';
                        document.getElementById('viewRoomTypePrice').textContent = '₱' + parseFloat(roomType.price || 0).toFixed(2);
                        document.getElementById('viewRoomTypeBeds').textContent = roomType.beds || 'N/A';
                        
                        // Set status badge
                        const statusClass = roomType.status === 'active' ? 'bg-success' : 'bg-secondary';
                        const statusText = roomType.status ? roomType.status.charAt(0).toUpperCase() + roomType.status.slice(1) : 'Inactive';
                        const statusBadge = document.getElementById('viewRoomTypeStatus');
                        statusBadge.className = 'badge ' + statusClass;
                        statusBadge.textContent = statusText;
                        
                        // Set image
                        const imageElement = document.getElementById('viewRoomTypeImage');
                        const imageUrl = roomType.image ? 'uploads/room_types/' + roomType.image : 'assets/img/no-image.png';
                        imageElement.src = imageUrl;
                        imageElement.alt = roomType.room_type || 'Room Image';
                        
                        // Set amenities
                        const amenitiesContainer = document.getElementById('viewRoomTypeAmenities');
                        amenitiesContainer.innerHTML = '';
                        
                        if (roomType.amenities && roomType.amenities.length > 0) {
                            roomType.amenities.forEach(function(amenity) {
                                const amenityBadge = document.createElement('span');
                                amenityBadge.className = 'amenity-badge';
                                amenityBadge.textContent = amenity.name || amenity.amenity_name || 'Unnamed Amenity';
                                amenitiesContainer.appendChild(amenityBadge);
                            });
                        } else {
                            const noAmenities = document.createElement('span');
                            noAmenities.className = 'text-muted';
                            noAmenities.textContent = 'No amenities added';
                            amenitiesContainer.appendChild(noAmenities);
                        }
                        
                        console.log('Modal content updated successfully');
                    } else {
                        const errorMsg = response && response.message ? response.message : 'Unknown error';
                        console.error('Error in response:', errorMsg);
                        alert('Failed to load room type details: ' + errorMsg);
                        viewModal.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                    alert('Error loading room type details. Please check console for details.');
                    viewModal.hide();
                }
            });
        }
        
        // Initialize tooltips and modals when document is ready
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize view room type modal
            var viewRoomTypeModal = new bootstrap.Modal(document.getElementById('viewRoomTypeModal'));

            // Handle edit room type
            $(document).on('click', '.edit-room-type', function() {
                const roomTypeId = $(this).data('id');
                window.location.href = `edit_room_type.php?id=${roomTypeId}`;
            });

            // Handle delete room type
            $(document).on('click', '.delete-room-type', function(e) {
                e.preventDefault();
                const roomTypeId = $(this).data('id');
                
                if (confirm('Are you sure you want to delete this room type? This action cannot be undone.')) {
                    $.ajax({
                        url: 'delete_room_type.php',
                        type: 'POST',
                        data: { 
                            room_type_id: roomTypeId,
                            action: 'delete_room_type'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message || 'Room type deleted successfully');
                                // Reload the page to reflect changes
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showAlert('danger', response.message || 'Failed to delete room type');
                            }
                        },
                        error: function() {
                            showAlert('danger', 'An error occurred while deleting the room type');
                        }
                    });
                }
            });

            // Handle view room
            $(document).on('click', '.view-room', function() {
                const roomId = $(this).data('id');
                $.ajax({
                    url: 'get_room.php',
                    type: 'GET',
                    data: { id: roomId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Populate view modal
                            $('#viewRoomModal .modal-title').text('View Room: ' + response.data.room_number);
                            $('#viewRoomModal .room-number').text(response.data.room_number);
                            $('#viewRoomModal .room-type').text(response.data.room_type);
                            
                            // Show status badge
                            let statusClass = '';
                            switch(response.data.status.toLowerCase()) {
                                case 'active': statusClass = 'success'; break;
                                case 'occupied': statusClass = 'danger'; break;
                                case 'maintenance': statusClass = 'warning'; break;
                                case 'cleaning': statusClass = 'info'; break;
                                default: statusClass = 'secondary';
                            }
                            $('#viewRoomModal .status').html(`<span class="badge bg-${statusClass}">${response.data.status}</span>`);
                            
                            // Show last updated
                            const lastUpdated = response.data.updated_at 
                                ? new Date(response.data.updated_at).toLocaleString() 
                                : 'N/A';
                            $('#viewRoomModal .last-updated').text(lastUpdated);
                            
                            // Show the modal
                            $('#viewRoomModal').modal('show');
                        } else {
                            showAlert('danger', response.message || 'Failed to load room details');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'An error occurred while fetching room details');
                    }
                });
            });

            // Handle edit room
            $(document).on('click', '.edit-room', function() {
                const roomId = $(this).data('id');
                window.location.href = `edit_room.php?id=${roomId}`;
            });

            // Handle delete room
            $(document).on('click', '.delete-room', function(e) {
                e.preventDefault();
                const roomId = $(this).data('id');
                const roomNumber = $(this).data('room-number');
                
                if (confirm(`Are you sure you want to delete room ${roomNumber}? This action cannot be undone.`)) {
                    $.ajax({
                        url: 'delete_room.php',
                        type: 'POST',
                        data: { 
                            room_id: roomId,
                            action: 'delete_room'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message || 'Room deleted successfully');
                                // Reload the page to reflect changes
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showAlert('danger', response.message || 'Failed to delete room');
                            }
                        },
                        error: function() {
                            showAlert('danger', 'An error occurred while deleting the room');
                        }
                    });
                }
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Show alert function
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                
                // Remove any existing alerts
                $('.alert-dismissible').alert('close');
                
                // Add new alert
                $('.container-fluid').prepend(alertHtml);
                
                // Auto-remove alert after 5 seconds
                setTimeout(() => {
                    $('.alert-dismissible').alert('close');
                }, 5000);
            }
        });
        
        // Function to load room type data with debug information
        function loadRoomTypeData(roomTypeId, $modal) {
            console.log('Loading room type data for ID:', roomTypeId);
            
            // Initialize modal with proper focus management
            $modal.modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
            
            // Set aria-hidden to false before showing
            $modal.removeAttr('aria-hidden');
            
            // Set focus to the first focusable element when shown
            $modal.one('shown.bs.modal', function() {
                const $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible').first();
                if ($focusable.length) {
                    $focusable.trigger('focus');
                }
            });
            
            // Handle modal hide to restore aria attributes
            $modal.one('hide.bs.modal', function() {
                $modal.attr('aria-hidden', 'true');
            });
            
            // Fetch room type data
            fetch(`get_room_type.php?id=${roomTypeId}&_=${new Date().getTime()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.success && data.data) {
                            // Render the form directly with the data
                            if (typeof renderRoomTypeForm === 'function') {
                                renderRoomTypeForm(data.data, $modal);
                            } else {
                                console.error('renderRoomTypeForm function not found');
                            }
                        } else {
                            console.error('Invalid response format:', data);
                            $modal.find('.modal-body').html(`
                                <div class="alert alert-danger">
                                    <h5>Error Loading Room Type</h5>
                                    <p>Invalid response format from server</p>
                                    <button class="btn btn-secondary mt-2" data-dismiss="modal">Close</button>
                                </div>
                            `);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading room type:', error);
                        $modal.find('.modal-body').html(`
                            <div class="alert alert-danger">
                                <h5>Error Loading Room Type</h5>
                                <p>${error.message || 'Failed to load room type data'}</p>
                                <button class="btn btn-secondary mt-2" data-dismiss="modal">Close</button>
                            </div>
                        `);
                    });
                    .catch(error => {
                        const errorMsg = `❌ Test request failed: ${error.message || 'Unknown error'}`;
                        debugLog(errorMsg, true);
                        showErrorInModal('Connection Error', 
                            `Cannot connect to the server. Please check if the server is running and accessible.\n\n` +
                            `URL: ${testUrl}\n` +
                            `Error: ${error.message}`,
                            { error: error.toString(), stack: error.stack }
                        );
                    });
                
                // Helper function to show errors in the modal
                function showErrorInModal(title, message, errorDetails = null) {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5>${title}</h5>
                            <p>${message}</p>
                            ${errorDetails ? `<pre class="small mt-3 p-2 bg-dark text-light rounded">${JSON.stringify(errorDetails, null, 2)}</pre>` : ''}
                            <div class="mt-3">
                                <button class="btn btn-primary" onclick="window.location.reload()">
                                    <i class="fas fa-sync-alt"></i> Refresh Page
                                </button>
                                <button class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                    `;
                    $modal.find('.modal-body').html(errorHtml);
                }
                
                // Function to make the actual room type request
                function makeRoomTypeRequest() {
                    debugLog(`Initiating AJAX request to: ${url}`);
                    
                    // Show loading state
                    $modal.find('.modal-body').html(`
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Loading room type details...</p>
                            <div class="debug-info small text-muted mt-2 text-left">
                                <div>Fetching data for room type ID: ${roomTypeId}</div>
                                <div id="debugStatus">Status: Sending request to server...</div>
                                <div id="debugResponse" class="mt-2 p-2 bg-light rounded" style="max-height: 200px; overflow-y: auto; font-family: monospace;"></div>
                            </div>
                        </div>
                    `);
                    
                    // Make the actual AJAX request
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        timeout: 30000, // 30 second timeout
                        beforeSend: function(xhr) {
                            debugLog('AJAX request started');
                        },
                        success: function(response, status, xhr) {
                            try {
                                debugLog('✅ AJAX request successful');
                                debugLog(`Response status: ${xhr.status} ${xhr.statusText}`);
                                debugLog('Response data:', response);
                                
                                if (!response) {
                                    throw new Error('Empty response received from server');
                                }
                                
                                if (response.success && response.data) {
                                    debugLog('Rendering room type form with valid data');
                                    // Call the render function if it exists
                                    if (typeof renderRoomTypeForm === 'function') {
                                        renderRoomTypeForm(response.data, $modal);
                                    } else {
                                        throw new Error('renderRoomTypeForm function not found');
                                    }
                                } else {
                                    throw new Error(response.message || 'No valid data received from server');
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                debugLog(`❌ Error: ${error.message}`, true);
                                showErrorInModal('Processing Error', 'An error occurred while processing the server response.', {
                                    error: error.message,
                                    stack: error.stack
                                });
                            }
                    },
                        error: function(xhr, status, error) {
                            try {
                                let errorMessage = 'Failed to load room type data';
                                let errorDetails = null;
                                
                                debugLog(`❌ AJAX Error: ${status} - ${error || 'Unknown error'}`, true);
                                
                                try {
                                    const response = xhr.responseJSON || (xhr.responseText ? JSON.parse(xhr.responseText) : null);
                                    if (response && response.message) {
                                        errorMessage = response.message;
                                        errorDetails = response;
                                    } else {
                                        errorMessage = `Server returned ${xhr.status}: ${xhr.statusText || 'No response'}`;
                                        errorDetails = {
                                            status: xhr.status,
                                            statusText: xhr.statusText,
                                            responseText: xhr.responseText
                                        };
                                    }
                                } catch (e) {
                                    errorMessage = `Error parsing response: ${e.message}`;
                                    errorDetails = {
                                        parsingError: e.message,
                                        status: xhr.status,
                                        statusText: xhr.statusText
                                    };
                                }
                                
                                debugLog(`❌ ${errorMessage}`, true);
                                showErrorInModal('Request Failed', errorMessage, errorDetails);
                                
                            } catch (e) {
                                console.error('Error in error handler:', e);
                                debugLog(`❌ Critical error in error handler: ${e.message}`, true);
                                showErrorInModal('Critical Error', 'An unexpected error occurred while handling the error.', {
                                    error: e.message,
                                    stack: e.stack
                                });
                            }
                        },
                        complete: function(xhr, status) {
                            debugLog(`Request completed with status: ${status}`);
                        }
                    });
                } // End of makeRoomTypeRequest
                
            } catch (error) {
                console.error('Error in loadRoomTypeData:', error);
                debugLog(`❌ Unhandled exception: ${error.message}`, true);
                
                showErrorInModal('Unexpected Error', 'An unexpected error occurred while loading the room type.', {
                    error: error.message,
                    stack: error.stack
                });
            }
        
        // Debug: Check if Bootstrap modal is available
        console.log('Bootstrap modal:', typeof $.fn.modal === 'function' ? 'Available' : 'Not available');
        
        // Function to handle edit room type
        function handleEditRoomType(roomTypeId) {
            console.log('Editing room type ID:', roomTypeId);
            const $modal = $('#editRoomTypeModal');
            
            // Show loading state
            $modal.find('.modal-body').html(`
                <div class="text-center p-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading room type details...</p>
                    <div class="debug-info small text-muted mt-2">
                        <div>Fetching data for room type ID: ${roomTypeId}</div>
                        <div id="debugStatus">Status: Loading...</div>
                    </div>
                </div>
            `);
            
            // Show the modal
            $modal.modal('show').css('display', 'block').addClass('show');
            
            // Load room type data via AJAX
            $.ajax({
                url: 'get_room_type.php',
                type: 'GET',
                data: { id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    console.log('Room type data loaded:', response);
                    
                    if (response && response.success && response.data) {
                        // Render the edit form with the room type data
                        renderRoomTypeForm(response.data, $modal);
                    } else {
                        const errorMsg = response ? response.message : 'Failed to load room type data';
                        showError(errorMsg);
                        $modal.modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading room type:', error);
                    showError('Error loading room type: ' + error);
                    $modal.modal('hide');
                }
            });
        }
        
        // Function to render the room type form
        function renderRoomTypeForm(roomType, $modal) {
            // Format price for display
            const price = parseFloat(roomType.price || 0).toFixed(2);
            
            // Create the form HTML
            const formHtml = `
                <form id="editRoomTypeForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_room_type">
                    <input type="hidden" name="room_type_id" value="${roomType.room_type_id}">
                    
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit Room Type</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="room_type_name">Room Type Name</label>
                                <input type="text" class="form-control" id="room_type_name" name="room_type_name" 
                                       value="${escapeHtml(roomType.room_type || '')}" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price per Night</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" value="${price}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">
                                    ${escapeHtml(roomType.description || '')}
                                </textarea>
                            </div>
                            
                            <div class="text-right mt-4">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            `;
            
            // Set the form HTML in the modal
            $modal.find('.modal-body').html(formHtml);
            
            // Initialize any plugins or event handlers
            initializeRoomTypeForm();
        }
        
        // Function to initialize the room type form
        function initializeRoomTypeForm() {
            // Initialize any form plugins here
            if ($.fn.select2) {
                $('.select2').select2();
            }
            
            // Handle form submission
            $('#editRoomTypeForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');
                const originalBtnText = $submitBtn.html();
                
                // Show loading state
                $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                
                // Submit form via AJAX
                const formData = new FormData($form[0]);
                
                $.ajax({
                    url: 'process_room_type.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Form submission response:', response);
                        
                        if (response && response.success) {
                            showAlert('success', response.message || 'Room type updated successfully!');
                            
                            // Close the modal and refresh the page
                            setTimeout(function() {
                                $('#editRoomTypeModal').modal('hide');
                                window.location.reload();
                            }, 1500);
                        } else {
                            const errorMsg = response ? response.message : 'Failed to update room type';
                            showAlert('danger', errorMsg);
                            $submitBtn.prop('disabled', false).html(originalBtnText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Form submission error:', error);
                        showAlert('danger', 'An error occurred while updating the room type.');
                        $submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
        }
        
        // Helper function to show error messages
        function showError(message) {
            console.error(message);
            showAlert('danger', message);
        }
        
        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        
        // Function to show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            // Remove any existing alerts
            $('.alert-dismissible').alert('close');
            
            // Add new alert
            $('.main-content').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $('.alert-dismissible').alert('close');
            }, 5000);
        }
        
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Debug: Check if edit button exists
            console.log('Edit buttons found:', $('.edit-room-type').length);
            
            // Debug: Check if modal exists
            console.log('Edit modal exists:', $('#editRoomTypeModal').length > 0);

            // Handle edit room type button click
            $(document).on('click', '.edit-room-type', function(e) {
                e.preventDefault();
                
                const roomTypeId = $(this).data('id');
                
                // Reset the form
                const $form = $('#editRoomTypeForm');
                $form[0].reset();
                
                // Set the room type ID in the form
                $form.find('input[name="room_type_id"]').val(roomTypeId);
                
                // Update modal title
                $('#editRoomTypeModalLabel').text('Edit Room Type');
                
                // Show the modal
                $('#editRoomTypeModal').modal('show');
                
                // Fetch room type details
                $.ajax({
                    url: 'get_room_type.php',
                    type: 'GET',
                    data: { id: roomTypeId },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success && response.data) {
                            const roomType = response.data;
                            
                            // Populate form fields directly
                            $('#edit_room_type').val(roomType.room_type || '');
                            $('#edit_price').val(roomType.price || '0.00');
                            $('#edit_capacity').val(roomType.capacity || '1');
                            $('#edit_beds').val(roomType.beds || '');
                            $('#edit_description').val(roomType.description || '');
                            $('#edit_discount_percent').val(roomType.discount_percent || '0');
                            
                            // Update status checkbox
                            if (roomType.status === 'active') {
                                $('#edit_status').prop('checked', true);
                            } else {
                                $('#edit_status').prop('checked', false);
                            }
                            
                            // Update image previews
                            const $imagesPreview = $('#current_images_preview');
                            $imagesPreview.empty();
                            
                            // Add main image preview if exists
                            if (roomType.image) {
                                $imagesPreview.append(`
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="${roomType.image}" class="card-img-top" style="height: 100px; object-fit: cover;" alt="Room Image">
                                            <div class="card-body p-2">
                                                <p class="card-text small mb-0">Main Image</p>
                                            </div>
                                        </div>
                                    </div>`);
                            }
                            
                            // Add second image preview if exists
                            if (roomType.image2) {
                                $imagesPreview.append(`
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="${roomType.image2}" class="card-img-top" style="height: 100px; object-fit: cover;" alt="Room Image 2">
                                            <div class="card-body p-2">
                                                <p class="card-text small mb-0">Image 2</p>
                                            </div>
                                        </div>
                                    </div>`);
                            }
                            
                            // Add third image preview if exists
                            if (roomType.image3) {
                                $imagesPreview.append(`
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="${roomType.image3}" class="card-img-top" style="height: 100px; object-fit: cover;" alt="Room Image 3">
                                            <div class="card-body p-2">
                                                <p class="card-text small mb-0">Image 3</p>
                                            </div>
                                        </div>
                                    </div>`);
                            }
                            
                            // Initialize file input previews
                            $('.custom-file-input').on('change', function() {
                                const fileName = $(this).val().split('\\').pop();
                                $(this).next('.custom-file-label').addClass('selected').html(fileName);
                                
                                // Show image preview
                                const previewId = $(this).attr('id') + '_preview';
                                const file = this.files[0];
                                
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        $('#' + previewId).html(`
                                            <img src="${e.target.result}" class="img-fluid mt-2" style="max-height: 100px;">
                                        `);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                            
                            // Show the modal with options
                            console.log('Showing edit room type modal');
                            try {
                                $modal.modal({
                                    backdrop: 'static',
                                    keyboard: false
                                });
                            } catch (modalError) {
                                console.error('Error showing modal:', modalError);
                                alert('Error opening the edit form. Please try again.');
                            }
                        } else {
                            const errorMsg = response.message || 'Error loading room type details';
                            console.error('Server error:', errorMsg);
                            alert(errorMsg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });
                        alert('Error loading room type details. Please check your connection and try again.');
                    }
                });
                
                // For testing AJAX later
                /*
                const $button = $(this);
                const roomTypeId = $button.data('id');
                const originalText = $button.html();
                
                // Show loading state
                $button.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                
                // Fetch room type details via AJAX
                $.ajax({*/
                    url: 'get_room_type.php',
                    type: 'GET',
                    data: { id: roomTypeId },
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX Success - Response:', response);
                        
                        if (!response) {
                            console.error('Empty response from server');
                            alert('Empty response from server');
                            return;
                        }
                        
                        if (response.success) {
                            console.log('Success response received');
                            const roomType = response.data;
                            
                            if (!roomType) {
                                console.error('No room type data in response');
                                alert('No room type data received');
                                return;
                            }
                            
                            console.log('Populating form with data:', roomType);
                            
                            // Populate the edit form
                            $('#edit_room_type_id').val(roomType.room_type_id);
                            $('#edit_room_type').val(roomType.room_type);
                            $('#edit_price').val(roomType.price);
                            $('#edit_capacity').val(roomType.capacity);
                            $('#edit_beds').val(roomType.beds || '');
                            $('#edit_description').val(roomType.description || '');
                            $('#edit_rating').val(roomType.rating || 0);
                            
                            // Handle image preview if available
                            const $imagePreview = $('#current_image_preview img');
                            if (roomType.image && roomType.image !== '') {
                                console.log('Setting image preview:', roomType.image);
                                $imagePreview.attr('src', roomType.image);
                                $imagePreview.show();
                            } else {
                                console.log('No image path provided, hiding preview');
                                $imagePreview.hide();
                            }
                            
                            // Show the modal
                            console.log('Showing edit modal');
                            $('#editRoomTypeModal').modal('show');
                        } else {
                            console.error('Error in response:', response.message || 'Unknown error');
                            alert(response.message || 'Error loading room type details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error Details:');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response Text:', xhr.responseText);
                        console.error('Status Code:', xhr.status);
                        
                        let errorMsg = 'Error loading room type details';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                        }
                        
                        alert(errorMsg);
                    },
                    complete: function() {
                        $button.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Handle edit room type form submission
            $('#editRoomTypeForm').on('submit', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');
                const originalBtnText = $submitBtn.html();
                
                // Show loading state
                $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                // Create FormData object to handle file uploads
                const formData = new FormData(this);
                
                // Submit form via AJAX
                $.ajax({
                    url: 'room_management.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        let result = response;
                        if (typeof response === 'string') {
                            try {
                                result = JSON.parse(response);
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                alert('Error processing server response');
                                return;
                            }
                        }
                        
                        if (result.success) {
                            // Show success message and reload the page
                            alert(result.message || 'Room type updated successfully');
                            location.reload();
                        } else {
                            alert(result.message || 'Error updating room type');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error updating room type. Please try again.');
                    },
                    complete: function() {
                        $submitBtn.html(originalBtnText).prop('disabled', false);
                    }
                });
            });

            // Clear form when edit modal is closed
            $('#editRoomTypeModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            // Handle delete room type
            $(document).on('click', '.delete-room-type', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to delete this room type? This action cannot be undone.')) {
                    return;
                }
                
                const roomTypeId = $(this).data('id');
                const $deleteBtn = $(this);
                const originalText = $deleteBtn.html();
                
                // Show loading state
                $deleteBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                
                // Send delete request
                $.ajax({
                    url: 'room_management.php',
                    type: 'POST',
                    data: {
                        action: 'delete_room_type',
                        room_type_id: roomTypeId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message || 'Room type deleted successfully');
                            location.reload();
                        } else {
                            alert(response.message || 'Error deleting room type');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error deleting room type');
                    },
                    complete: function() {
                        $deleteBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Handle edit room type button click
            
                // Fetch room type details
                $.ajax({
                    url: 'get_room_type.php',
                    method: 'GET',
                    data: { id: roomTypeId },
                    success: function(response) {
                        try {
                            const roomType = JSON.parse(response);
                            
                            // Populate the edit modal
                            $('#edit_room_type_id').val(roomType.room_type_id);
                            $('#edit_room_type').val(roomType.room_type);
                            $('#edit_price').val(roomType.price);
                            $('#edit_capacity').val(roomType.capacity);
                            $('#edit_beds').val(roomType.beds);
                            $('#edit_description').val(roomType.description);
                            $('#edit_rating').val(roomType.rating);
                            
                            // Update image preview
                            if (roomType.image) {
                                const $preview = $('#current_image_preview img');
                                $preview.attr('src', roomType.image).show();
                            } else {
                                $('#current_image_preview img').hide();
                            }
                            
                            // Show the modal
                            $('#editRoomTypeModal').modal('show');
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            console.log('Response:', response);
                            alert('Error loading room type details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.log('Status:', status);
                        console.log('Response:', xhr.responseText);
                        alert('Error fetching room type details: ' + error);
                    }
                });
            });

            // Preview new image when selected
            $('#edit_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#current_image_preview img').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Handle room type form submission
            $('#editRoomTypeForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                
                $.ajax({
                    url: 'room_management.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert('Room type updated successfully!');
                                location.reload();
                            } else {
                                alert('Error updating room type: ' + result.message);
                                submitBtn.prop('disabled', false).text('Save Changes');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error updating room type. Please try again.');
                            submitBtn.prop('disabled', false).text('Save Changes');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error updating room type: ' + error);
                        submitBtn.prop('disabled', false).text('Save Changes');
                    }
                });
            });

            // Handle delete room button click
            $(document).on('click', '.delete-room', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const roomId = $button.data('id');
                
                if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                    // Show loading state
                    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
                    
                    // Send AJAX request to delete the room
                    $.ajax({
                        url: 'room_management.php',
                        method: 'POST',
                        data: {
                            action: 'delete_room',
                            room_id: roomId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Show success message and remove the row
                                alert('Room deleted successfully!');
                                $button.closest('tr').fadeOut(400, function() {
                                    $(this).remove();
                                });
                            } else {
                                // Show error message
                                alert(response.message || 'Error deleting room. Please try again.');
                                $button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting room:', error);
                            alert('An error occurred while deleting the room. Please try again.');
                            $button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                        }
                    });
                }
            });
            
            // Handle edit room button click
            $(document).on('click', '.edit-room', function(e) {
                console.log('=== EDIT BUTTON CLICKED ===');
                e.preventDefault();
                
                const $button = $(this);
                const roomId = $button.data('id');
                const roomNumber = $button.data('room-number');
                const roomTypeId = $button.data('room-type-id') || 1; // Get room type ID or default to 1
                const roomType = $button.data('room-type');
                const roomStatus = $button.data('status');
                
                console.log('Room data:', {
                    roomId: roomId,
                    roomNumber: roomNumber,
                    roomTypeId: roomTypeId,
                    roomType: roomType,
                    roomStatus: roomStatus
                });
                
                // Check if modal exists
                const $modal = $('#roomManagementModal');
                console.log('Modal element found:', $modal.length > 0);
                
                if ($modal.length === 0) {
                    console.error('❌ Modal #roomManagementModal not found in DOM');
                    alert('Error: Could not find the edit modal. Please refresh the page and try again.');
                    return;
                }
                
                try {
                    // Reset the form first
                    const $form = $('#roomManagementForm');
                    console.log('Form found:', $form.length > 0);
                    if ($form.length > 0) {
                        $form[0].reset();
                    }
                    
                    // Set the room ID and room type ID in the form
                    $('#roomId').val(roomId);
                    $('#room_type_id').val(roomTypeId); // Set the room type ID
                    $('#formAction').val('edit_room');
                    
                    // Populate the form with existing data
                    $('#room_number').val(roomNumber);
                    $('#status').val(roomStatus);
                    
                    // Show the delete button
                    $('#deleteRoomBtn').show();
                    
                    // Update modal title
                    $('#roomManagementModalLabel').text('Edit Room #' + roomNumber);
                    
                    // Show the modal with debug info
                    console.log('About to show modal');
                    
                    // First, ensure the modal is hidden before showing it
                    $modal.modal('hide');
                    
                    // Then show it with options
                    $modal.modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    
                    console.log('Modal shown command executed');
                    
                } catch (error) {
                    console.error('Error showing modal:', error);
                    alert('Error opening the edit form. Please check the console for details.');
                }
            });

            // Handle room form submission
            $('#roomManagementForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const roomId = $('#roomId').val();
                const roomTypeId = $('#room_type_id').val() || 1; // Ensure we have a default value
                const action = $('#formAction').val();
                
                console.log('Form submission:', {
                    action: action,
                    roomId: roomId,
                    formData: Object.fromEntries(formData)
                });

                if (action === 'edit_room' && !roomId) {
                    alert('Error: Room ID is missing');
                    return;
                }

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

                // Add action to form data
                formData.append('action', action);
                if (roomId) {
                    formData.append('room_id', roomId);
                }

                $.ajax({
                    url: 'room_management.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Server response:', response);
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                // Show success message
                                const successMsg = action === 'edit_room' ? 'Room updated successfully!' : 'Room added successfully!';
                                alert(successMsg);
                                // Reload the page to show changes
                                window.location.reload();
                            } else {
                                alert(result.message || 'Error processing your request');
                                console.error('Operation failed:', result);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error updating room. Please try again.');
                        }
                        submitBtn.prop('disabled', false).text('Save Changes');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {xhr, status, error});
                        alert('Error updating room: ' + error);
                        submitBtn.prop('disabled', false).text('Save Changes');
                    }
                });
            });

            // Initialize the room management modal
            const roomModal = $('#roomManagementModal');
            
            // Reset form when modal is closed
            roomModal.on('hidden.bs.modal', function () {
                roomModal.find('form')[0].reset();
                $('#deleteRoomBtn').hide();
                $('#roomManagementModalLabel').text('Add New Room');
                $('#formAction').val('add');
            });

            // Handle delete room type with confirmation
            $('.delete-room-type').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this room type? This will also delete all associated rooms.')) {
                    const roomTypeId = $(this).data('id');
                    
                    $.ajax({
                        url: 'room_management.php',
                        method: 'POST',
                        data: {
                            action: 'delete_room_type',
                            room_type_id: roomTypeId
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.success) {
                                    alert('Room type deleted successfully!');
                                    location.reload();
                                } else {
                                    alert(result.message || 'Error deleting room type');
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                alert('Error deleting room type');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                            alert('Error deleting room type: ' + error);
                        }
                    });
                }
            });

            // Handle delete room button click
            $('#deleteRoomBtn').on('click', function() {
                if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                    const roomId = $('#roomId').val();
                    
                    $.ajax({
                        url: 'room_management.php',
                        method: 'POST',
                        data: {
                            action: 'delete_room',
                            room_id: roomId
                        },
                        success: function(response) {
                            try {
                                const result = typeof response === 'string' ? JSON.parse(response) : response;
                                if (result.success) {
                                    alert('Room deleted successfully!');
                                    location.reload();
                                } else {
                                    alert(result.message || 'Error deleting room');
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                alert('Error processing delete request');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                            alert('Error deleting room: ' + error);
                        }
                    });
                }
            });
            
            // Handle edit amenities button click
            $('.edit-amenities').on('click', function(e) {
                e.preventDefault();
                const roomTypeId = $(this).data('id');
                const roomTypeName = $(this).data('name');
                
                // Set room type ID and name in the modal
                $('#amenities_room_type_id').val(roomTypeId);
                $('#amenities_room_type_name').text('Amenities for: ' + roomTypeName);
                
                // Clear previous amenities
                $('#amenities_container').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');
                
                // Load all available amenities
                $.ajax({
                    url: 'get_amenities.php',
                    method: 'GET',
                    success: function(response) {
                        const allAmenities = JSON.parse(response);
                        
                        // Get the current room type with its amenities
                        $.ajax({
                            url: 'get_room_type.php',
                            method: 'GET',
                            data: { id: roomTypeId },
                            success: function(roomResponse) {
                                const roomData = JSON.parse(roomResponse);
                                const roomAmenities = roomData.amenities || [];
                                
                                // Create amenity checkboxes
                                let amenitiesHtml = '';
                                
                                allAmenities.forEach(function(amenity) {
                                    // Check if this amenity is assigned to the room
                                    const isChecked = roomAmenities.some(a => parseInt(a.amenity_id) === parseInt(amenity.amenity_id));
                                    
                                    amenitiesHtml += `
                                        <div class="custom-control custom-checkbox m-2" style="width: 45%;">
                                            <input type="checkbox" class="custom-control-input" 
                                                id="amenity_${amenity.amenity_id}" 
                                                name="amenities[]" 
                                                value="${amenity.amenity_id}" 
                                                ${isChecked ? 'checked' : ''}>
                                            <label class="custom-control-label" for="amenity_${amenity.amenity_id}">
                                                ${amenity.name}
                                            </label>
                                        </div>
                                    `;
                                });
                                
                                // Update the container with the checkboxes
                                $('#amenities_container').html(amenitiesHtml);
                                
                                // Show the modal if it wasn't already shown
                                if (!$('#editAmenitiesModal').hasClass('show')) {
                                    $('#editAmenitiesModal').modal('show');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching room type amenities:', error);
                                alert('Error loading room amenities. Please try again.');
                                $('#amenities_container').html('<div class="alert alert-danger">Error loading amenities</div>');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching amenities:', error);
                        alert('Error loading amenities. Please try again.');
                        $('#amenities_container').html('<div class="alert alert-danger">Error loading amenities</div>');
                    }
                });
            });
            
            // Handle amenities form submission
            $('#editAmenitiesForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                
                $.ajax({
                    url: 'room_management.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert('Room amenities updated successfully!');
                                $('#editAmenitiesModal').modal('hide');
                                // Reload the page to update the amenities display in the table
                                location.reload();
                            } else {
                                alert('Error updating amenities: ' + result.message);
                                submitBtn.prop('disabled', false).text('Save Amenities');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error updating amenities. Please try again.');
                            submitBtn.prop('disabled', false).text('Save Amenities');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error updating amenities: ' + error);
                        submitBtn.prop('disabled', false).text('Save Amenities');
                    }
                });
            });

            // Handle manage amenities button
            $('#manageAmenitiesBtn').on('click', function() {
                loadAllAmenities();
            });
            
            // Load all amenities
            function loadAllAmenities() {
                $.ajax({
                    url: 'get_amenities.php',
                    method: 'GET',
                    success: function(response) {
                        try {
                            const amenities = JSON.parse(response);
                            let tableRows = '';
                            
                            if (amenities.length === 0) {
                                tableRows = '<tr><td colspan="3" class="text-center">No amenities found</td></tr>';
                            } else {
                                amenities.forEach(function(amenity) {
                                    tableRows += `
                                        <tr>
                                            <td>${amenity.amenity_id}</td>
                                            <td>${amenity.name}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-amenity" 
                                                        data-id="${amenity.amenity_id}" 
                                                        data-name="${amenity.name}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-amenity" 
                                                        data-id="${amenity.amenity_id}" 
                                                        data-name="${amenity.name}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });
                            }
                            
                            $('#amenitiesList tbody').html(tableRows);
                            
                            // Set up event handlers for edit/delete buttons
                            $('.edit-amenity').on('click', function() {
                                const id = $(this).data('id');
                                const name = $(this).data('name');
                                
                                // Fill the form
                                $('#amenity_id').val(id);
                                $('#amenity_name').val(name);
                                
                                $('#amenity_action').val('edit_amenity');
                                $('#amenityFormTitle').text('Edit Amenity');
                                
                                // Show the form
                                $('#amenityForm').show();
                            });
                            
                            $('.delete-amenity').on('click', function() {
                                const id = $(this).data('id');
                                const name = $(this).data('name');
                                
                                if (confirm(`Are you sure you want to delete the amenity "${name}"? This will remove it from all rooms.`)) {
                                    deleteAmenity(id);
                                }
                            });
                            
                        } catch (e) {
                            console.error('Error parsing amenities:', e);
                            $('#amenitiesList tbody').html('<tr><td colspan="3" class="text-center text-danger">Error loading amenities</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#amenitiesList tbody').html('<tr><td colspan="3" class="text-center text-danger">Error loading amenities</td></tr>');
                    }
                });
            }
            
            // Simple test function to check if jQuery is working
            console.log('jQuery version:', $.fn.jquery);
            
            // Test click handler for the save button
            $(document).on('click', '#saveRoomBtn', function(e) {
                console.log('Save button clicked!');
                e.preventDefault();
                
                // Simple validation
                // let isValid = true;
                // $('#saveRoomForm [required]').each(function() {
                //     if (!$(this).val()) {
                //         console.log('Missing field:', $(this).attr('name'));
                //         isValid = false;
                //     }
                // });
                
                // if (!isValid) {
                //     alert('Please fill in all required fields!');
                //     return false;
                // }
                
                console.log('Form is valid, preparing to submit...');
                
                // Show loading state
                const submitBtn = $(this);
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
                
                // Submit form the traditional way for now
                $('#saveRoomForm').off('submit').submit();
                
                // Re-enable button after 3 seconds if still there
                setTimeout(() => {
                    submitBtn.html(originalText).prop('disabled', false);
                }, 3000);
            });
            
            // Also handle form submission directly
            // $('#saveRoomForm').on('submit', function(e) {
            //     console.log('Form submit event triggered');
            //     return true; // Allow form to submit normally
            // });
            
            // Handle save room form submission via AJAX
            $('#saveRoomForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                console.log('Save Room form submission intercepted');
                
                const form = this;
                const formData = new FormData(form);
                const submitBtn = $(form).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Simple client-side validation
                let isValid = true;
                $(form).find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    showAlert('warning', 'Please fill in all required fields!');
                    return false;
                }

                // Show loading state
                submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: 'room_management.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success response:', response);
                        if (response.success) {
                            // If a new room type was added, update the dropdown
                            if (response.new_room_type_id && response.new_room_type_name) {
                                // Add the new room type to the dropdown
                                const newOption = new Option(response.new_room_type_name, response.new_room_type_id);
                                const otherOption = $('#add_room_room_type option[value="other"]');
                                $(newOption).insertBefore(otherOption);
                                // Select the newly added room type
                                $('#add_room_room_type').val(response.new_room_type_id);
                            }
                            
                            // Hide the add room modal
                            $('#addRoomModal').modal('hide');
                            // Reset the form
                            form.reset();
                            // Show success modal
                            $('#successModal').modal('show');
                            // Refresh the page after a delay to show updated room list
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showAlert('danger', response.message || 'Failed to save room. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', xhr.responseText, status, error);
                        showAlert('danger', 'An error occurred while saving the room. Please try again.');
                    },
                    complete: function() {
                        // Re-enable the button
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
                
                return false; // Prevent form from submitting normally
            });
            });

            
            // Helper function to show alerts
            function showAlert(type, message) {
                const alertHtml = `
                        console.error('AJAX Error:', error);
                        alert('Error saving amenity: ' + error);
                    }
                });
            });
            
            // Function to delete an amenity
            function deleteAmenity(amenityId) {
                $.ajax({
                    url: 'room_management.php',
                    method: 'POST',
                    data: {
                        action: 'delete_amenity',
                        amenity_id: amenityId
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                alert(result.message);
                                loadAllAmenities();
                                // Reload the page after a short delay to update amenities in room table
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                alert('Error: ' + result.message);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error deleting amenity. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error deleting amenity: ' + error);
                    }
                });
            }
            
            // Function to update icon preview
            window.updateIconPreview = function() {
                const iconClass = $('#amenity_icon').val();
                if (iconClass) {
                    // Ensure proper format for preview
                    let previewClass = iconClass;
                    if (!previewClass.match(/^(fa|fas|far|fab|fal|fad)\s/i)) {
                        previewClass = 'fas ' + previewClass;
                    }
                    $('#iconPreview').attr('class', previewClass);
                } else {
                    $('#iconPreview').attr('class', 'fas fa-check');
                }
            };
            
            // Function to cancel amenity form
            window.cancelAmenityForm = function() {
                $('#amenityForm').hide();
            };
        });
    </script>
    </div>

    <!-- Add Amenity Modal -->
    <div class="modal fade" id="addAmenityModal" tabindex="-1" role="dialog" aria-labelledby="addAmenityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAmenityModalLabel">Add New Amenity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addAmenityForm" method="POST" action="room_management.php">
                    <input type="hidden" name="action" value="add_amenity">
                    <input type="hidden" name="amenity_id" id="edit_amenity_id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Amenity Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Amenity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Room Management Modal -->
    <div id="roomManagementModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1050; overflow-y: auto; padding: 20px 0;">
        <div class="modal-content" style="background: white; margin: 30px auto; padding: 25px; width: 90%; max-width: 500px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                <h3 style="margin: 0; font-size: 1.4rem; color: #333;">Edit Room <span id="debugRoomNumber"></span></h3>
                <button type="button" onclick="document.getElementById('roomManagementModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>
            </div>
            <div id="debugInfo" style="color: #e74c3c; margin-bottom: 20px; padding: 8px; background: #fde8e8; border-radius: 4px; display: none;"></div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Room Number</label>
                <input type="text" id="room_number" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; box-sizing: border-box;">
            </div>
            
            <div style="margin-top: 20px; margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Status</label>
                <select id="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; background-color: white; cursor: pointer; box-sizing: border-box;">
                    <option value="Available">Available</option>
                    <option value="Occupied">Occupied</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 15px; border-top: 1px solid #eee;">
                <button type="button" onclick="document.getElementById('roomManagementModal').style.display='none'" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Cancel</button>
                <button type="button" id="saveRoomBtn" style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Save Changes</button>
            </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Success!</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h4>Room Added Successfully!</h4>
                    <p class="mb-0">The new room has been added to the system.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-dismiss="modal">
                        <i class="fas fa-check mr-1"></i> OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Move the function outside of document.ready to make it globally accessible
    function toggleOtherRoomType() {
        console.log('toggleOtherRoomType function called');
        const roomTypeSelect = document.getElementById('add_room_room_type'); // Updated ID
        const otherContainer = document.getElementById('add_room_other_room_type_container'); // Updated ID
        const otherInput = document.getElementById('add_room_other_room_type'); // Updated ID
        
        console.log('Selected value:', roomTypeSelect ? roomTypeSelect.value : 'Element not found');
        
        if (roomTypeSelect && roomTypeSelect.value === 'other') {
            console.log('Showing other room type input');
            otherContainer.style.display = 'block';
            otherInput.required = true;
        } else if (otherContainer) { // Added check for container existence
            console.log('Hiding other room type input');
            otherContainer.style.display = 'none';
            otherInput.required = false;
        }
    }

    // Handle edit room button click
    $(document).on('click', '.edit-room', function(e) {
        console.log('=== EDIT BUTTON CLICKED ===');
        e.preventDefault();
        
        const $button = $(this);
        const roomId = $button.data('id');
        const roomNumber = $button.data('room-number');
        const roomTypeId = $button.data('room-type-id') || 1; // Default to 1 if not set
        const roomStatus = $button.data('status');
        
        console.log('Room data:', {roomId, roomNumber, roomTypeId, roomStatus});
        
        // Get the modal and form elements
        const $modal = $('#roomManagementModal');
        const $form = $('#roomManagementForm');
        
        if ($modal.length === 0) {
            console.error('❌ Modal #roomManagementModal not found in DOM');
            alert('Error: Could not find the edit modal. Please refresh the page and try again.');
            return;
        }
        
        // Reset the form
        if ($form.length > 0) {
            $form[0].reset();
        }
        
        // Set the form values
        $('#roomId').val(roomId);
        $('#formAction').val('edit_room');
        $('#room_number').val(roomNumber);
        $('#status').val(roomStatus);
        $('#room_type_id').val(roomTypeId);
        
        // Show the delete button
        $('#deleteRoomBtn').show();
        
        // Update modal title
        $('#roomManagementModalLabel').text('Edit Room #' + roomNumber);
        
        // Show the modal with debug info
        console.log('About to show modal');
        
        // First, ensure the modal is hidden before showing it
        $modal.modal('hide');
        
        // Then show it with options
        $modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        
        console.log('Modal shown command executed');
    });

    $(document).ready(function() {
        console.log('Document ready');
        
        // Initialize the other room type container on modal open
        $('#addRoomModal').on('shown.bs.modal', function () {
            console.log('Add Room Modal shown');
            toggleOtherRoomType();
        });

        // Add event listener to the room type select within the modal
        $('#addRoomModal').on('change', '#add_room_room_type', function() {
            console.log('Room type changed in Add Room Modal');
            toggleOtherRoomType();
        });
        
        // Handle form submission for room management
        $('#roomManagementForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const roomId = $('#roomId').val();
            const action = $('#formAction').val();
            
            console.log('Form submission:', {
                action: action,
                roomId: roomId,
                formData: Object.fromEntries(formData)
            });

            if (action === 'edit_room' && !roomId) {
                alert('Error: Room ID is missing');
                return;
            }
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            
            // Show loading state
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            // Send AJAX request
            $.ajax({
                url: 'room_management.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.success) {
                        // Show success message and reload the page
                        alert(action === 'edit_room' ? 'Room updated successfully!' : 'Room added successfully!');
                        location.reload();
                    } else {
                        // Show error message
                        alert(response.message || 'Error processing your request');
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error: ' + error);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    });
    </script>
    
    <!-- jQuery first, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Debug: Log when script loads
    console.log('Room management script loaded');
    
    // Function to show modal with room data
    function showRoomModal(roomData) {
        console.log('showRoomModal called with:', roomData);
        
        // Update debug info
        const debugInfo = document.getElementById('debugInfo');
        if (debugInfo) {
            debugInfo.textContent = 'Editing Room ID: ' + roomData.id;
            debugInfo.style.display = 'block';
        }
        
        const roomNumberSpan = document.getElementById('debugRoomNumber');
        if (roomNumberSpan) {
            roomNumberSpan.textContent = '#' + roomData.roomNumber;
        }
        
        // Set form values
        const roomNumberInput = document.getElementById('room_number');
        const statusSelect = document.getElementById('status');
        
        if (roomNumberInput) roomNumberInput.value = roomData.roomNumber || '';
        if (statusSelect) statusSelect.value = roomData.roomStatus || 'Available';
        
        // Show the modal with smooth animation
        const modal = document.getElementById('roomManagementModal');
        if (modal) {
            modal.style.display = 'block';
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.2s ease-in-out';
            setTimeout(() => { modal.style.opacity = '1'; }, 10);
            
            // Close modal when clicking outside
            modal.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            };
        }
    }
    
    // Handle edit button clicks
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-room');
        if (!editBtn) return;
        
        e.preventDefault();
        console.log('Edit button clicked');
        
        // Get room data from data attributes
        const roomData = {
            id: editBtn.dataset.id,
            roomNumber: editBtn.dataset.roomNumber,
            roomType: editBtn.dataset.roomType,
            roomStatus: editBtn.dataset.status
        };
        
        console.log('Room data:', roomData);
        showRoomModal(roomData);
    });
    
    // Handle save button click
    document.getElementById('saveRoomBtn').addEventListener('click', function() {
        console.log('Save button clicked');
        // Add your save logic here
        alert('Save functionality will be implemented here');
    });
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');
    });

    // Handle Add Amenity form submission
    $(document).on('submit', '#addAmenityForm', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const $submitBtn = $('#addAmenityForm button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        // Show loading state
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        // Log the form data being sent
        console.log('Form data being sent:', formData);
        
        $.ajax({
            url: 'room_management.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response, status, xhr) {
                console.log('AJAX Success - Status:', status);
                console.log('Response:', response);
                
                // Try to parse response if it's a string
                let responseData = response;
                if (typeof response === 'string') {
                    try {
                        responseData = JSON.parse(response);
                    } catch (e) {
                        console.error('Failed to parse response as JSON:', response);
                        throw new Error('Invalid server response format');
                    }
                }
                
                if (responseData.success) {
                    // Show success message
                    alert('Amenity added successfully!');
                    // Close the modal
                    $('#addAmenityModal').modal('hide');
                    // Reset the form
                    $('#addAmenityForm')[0].reset();
                    // Refresh the page to show the new amenity
                    location.reload();
                } else {
                    const errorMsg = responseData.message || 'Failed to add amenity';
                    console.error('Server error:', errorMsg);
                    alert('Error: ' + errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusText: xhr.statusText
                });
                
                let errorMsg = 'An error occurred while saving the amenity.\n\n';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg += 'Server says: ' + (response.message || 'Unknown error');
                } catch (e) {
                    errorMsg += 'Details: ' + (xhr.responseText || error);
                }
                alert(errorMsg);
            },
            complete: function() {
                // Re-enable the button
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    </script>

    <!-- Edit Room Type Modal -->
    <div class="modal fade" id="editRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="editRoomTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editRoomTypeModalLabel">Edit Room Type</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editRoomTypeForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_room_type">
                    <input type="hidden" name="room_type_id" id="edit_room_type_id">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_room_type">Room Type Name</label>
                                    <input type="text" class="form-control" id="edit_room_type" name="room_type" required>
                                </div>

                                <div class="form-group">
                                    <label for="edit_price">Price per Night (₱)</label>
                                    <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label for="edit_capacity">Capacity (Persons)</label>
                                    <input type="number" class="form-control" id="edit_capacity" name="capacity" required>
                                </div>

                                <div class="form-group">
                                    <label for="edit_beds">Number of Beds</label>
                                    <input type="text" class="form-control" id="edit_beds" name="beds" required>
                                </div>

                                <div class="form-group">
                                    <label for="edit_discount_percent">Discount Percent (%)</label>
                                    <input type="number" class="form-control" id="edit_discount_percent" name="discount_percent" min="0" max="100" value="0">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Images</label>
                                    <div class="row" id="current_images_preview">
                                        <!-- Images will be loaded here -->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="edit_image">Main Image</label>
                                    <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                                    <small class="form-text text-muted">Leave empty to keep current image</small>
                                </div>

                                <div class="form-group">
                                    <label for="edit_image2">Additional Image 1</label>
                                    <input type="file" class="form-control-file" id="edit_image2" name="image2" accept="image/*">
                                </div>

                                <div class="form-group">
                                    <label for="edit_image3">Additional Image 2</label>
                                    <input type="file" class="form-control-file" id="edit_image3" name="image3" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Include required JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // Make sure jQuery is loaded
        if (typeof jQuery == 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        }
        
        // Initialize tooltips
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
            
            // Debug: Log when document is ready
            console.log('Document ready');
        });
    </script>
</body>
</html>
