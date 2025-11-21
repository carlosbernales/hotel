<?php
require_once 'db.php';

// Set correct headers based on the response type
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if it's an AJAX request based on the presence of the 'action' parameter
if (isset($_POST['action']) || isset($_GET['action'])) {
    // Disable displaying errors and set error reporting to 0 for AJAX requests
    ini_set('display_errors', 0);
    error_reporting(0);
    // Optionally, you can log errors to a file here
    // ini_set('log_errors', 1);
    // ini_set('error_log', '/path/to/your/php-error.log'); // Specify your log file path
}

// Default response
$response = [
    'success' => false,
    'message' => 'No action specified'
];

// Process the action
$action = '';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}
    
    // Log the action for debugging
    error_log("Processing action: " . $action);
    error_log("POST data: " . print_r($_POST, true));
    
    switch ($action) {
        // ... existing code ...
        
        case 'add_room_number':
            try {
                // Get form data
                $room_type_id = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
                $room_number = isset($_POST['room_number']) ? mysqli_real_escape_string($con, $_POST['room_number']) : '';
                $floor = isset($_POST['floor']) ? mysqli_real_escape_string($con, $_POST['floor']) : '';
                $description = isset($_POST['description']) ? mysqli_real_escape_string($con, $_POST['description']) : '';
                // Always set status to 'active' for new rooms
                $status = 'active';
                
                // Validate input
                if (empty($room_type_id) || empty($room_number)) {
                    throw new Exception('Room type and room number are required');
                }
                
                // Check if room number already exists
                $check_sql = "SELECT room_number_id FROM room_numbers WHERE room_number = ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param('s', $room_number);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    throw new Exception('Room number already exists');
                }
                
                // Verify room type exists
                $type_sql = "SELECT room_type_id FROM room_types WHERE room_type_id = ?";
                $type_stmt = $con->prepare($type_sql);
                $type_stmt->bind_param('i', $room_type_id);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                
                if ($type_result->num_rows === 0) {
                    throw new Exception('Room type not found');
                }
                
                // Set status to 'active' as default
                $status = 'active';
                
                // Insert the room number into room_numbers table
                $sql = "INSERT INTO room_numbers (
                    room_number, 
                    room_type_id, 
                    floor_number, 
                    status
                ) VALUES (?, ?, ?, ?)";
                
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare statement: ' . $con->error);
                }
                
                // Changed 'i' to 's' for status since it's now a string
                $stmt->bind_param('ssss', 
                    $room_number,
                    $room_type_id,
                    $floor_number,
                    $status
                );
                
                // Log the query for debugging
                error_log("Inserting room number: $room_number, type: $room_type_id, floor: $floor, status: $status");
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Room number added successfully';
                } else {
                    throw new Exception('Error adding room number: ' . $stmt->error);
                }
                
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
            
        case 'get_room_number':
            try {
                $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
                
                if (empty($room_id)) {
                    throw new Exception('Room ID is required');
                }
                
                $sql = "SELECT * FROM rooms WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('i', $room_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $response['success'] = true;
                    $response['room'] = $result->fetch_assoc();
                } else {
                    throw new Exception('Room not found');
                }
                
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
            
        case 'edit_room_number':
            try {
                // Get form data
                $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
                $room_type_id = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
                $room_numbers_input = isset($_POST['room_numbers']) ? trim($_POST['room_numbers']) : '';
                $floor = isset($_POST['floor']) ? mysqli_real_escape_string($con, $_POST['floor']) : '';
                $description = isset($_POST['description']) ? mysqli_real_escape_string($con, $_POST['description']) : '';
                // Always set status to 'active' for new rooms
                $status = 'active';
                
                // Validate input
                if (empty($room_id) || empty($room_type_id) || empty($room_numbers_input)) {
                    throw new Exception('Room ID, room type, and room number are required');
                }
                
                // Check if room number already exists for another room
                $check_sql = "SELECT id FROM rooms WHERE room_number = ? AND id != ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param('si', $room_number, $room_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    throw new Exception('Room number already exists');
                }
                
                // Get room type details
                $type_sql = "SELECT room_type, price, capacity, description, beds, image, image2, image3 
                            FROM room_types 
                            WHERE room_type_id = ?";
                $type_stmt = $con->prepare($type_sql);
                $type_stmt->bind_param('i', $room_type_id);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                
                if ($type_result->num_rows === 0) {
                    throw new Exception('Room type not found');
                }
                
                $room_type = $type_result->fetch_assoc();
                
                // Update the room number with room type details
                $sql = "UPDATE rooms SET 
                        room_number = ?, 
                        room_type_id = ?, 
                        floor = ?, 
                        description = ?, 
                        status = ?,
                        room_type = ?,
                        price = ?,
                        capacity = ?,
                        beds = ?,
                        image = ?,
                        image2 = ?,
                        image3 = ?
                        WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('sissssdsssssi', 
                    $room_number,
                    $room_type_id,
                    $floor,
                    $description,
                    $status,
                    $room_type['room_type'],
                    $room_type['price'],
                    $room_type['capacity'],
                    $room_type['beds'],
                    $room_type['image'],
                    $room_type['image2'],
                    $room_type['image3'],
                    $room_id
                );
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Room updated successfully';
                } else {
                    throw new Exception('Error updating room: ' . $stmt->error);
                }
                
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
            
        case 'delete_room_number':
            try {
                $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
                
                if (empty($room_id)) {
                    throw new Exception('Room ID is required');
                }
                
                // Check if room is currently occupied
                $check_sql = "SELECT status FROM rooms WHERE id = ?";
                $check_stmt = $con->prepare($check_sql);
                $check_stmt->bind_param('i', $room_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    $room_status = $check_result->fetch_assoc()['status'];
                    if ($room_status == 'Occupied') {
                        throw new Exception('Cannot delete an occupied room');
                    }
                }
                
                // Delete the room
                $sql = "DELETE FROM rooms WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('i', $room_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Room deleted successfully';
                } else {
                    throw new Exception('Error deleting room: ' . $stmt->error);
                }
                
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
            
        case 'get_room_numbers_by_type':
            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Set JSON header
            header('Content-Type: application/json');
            
            // Initialize response
            $response = [
                'success' => false,
                'message' => 'Unknown error occurred',
                'debug' => []
            ];
            
            try {
                // Get room type ID from request
                $room_type_id = isset($_GET['room_type_id']) ? intval($_GET['room_type_id']) : 0;
                
                if (empty($room_type_id)) {
                    throw new Exception('Room type ID is required');
                }
                
                // Debug info
                $response['debug']['room_type_id'] = $room_type_id;
                
                // Query to get room numbers from the room_numbers table
                $sql = "SELECT 
                            rn.room_number_id, 
                            rn.room_number, 
                            rn.room_type_id, 
                            COALESCE(rt.room_type, 'No Type') as room_type_name, 
                            rn.floor_number, 
                            rn.status, 
                            rn.description, 
                            rn.created_at 
                        FROM room_numbers rn
                        LEFT JOIN room_types rt ON rn.room_type_id = rt.room_type_id
                        WHERE rn.room_type_id = ?
                        ORDER BY rn.room_number";
                
                $response['debug']['sql'] = $sql;
                
                // Prepare and execute query
                $stmt = $con->prepare($sql);
                if ($stmt === false) {
                    throw new Exception('Failed to prepare statement: ' . $con->error);
                }
                
                $stmt->bind_param('i', $room_type_id);
                $executed = $stmt->execute();
                
                if (!$executed) {
                    throw new Exception('Query execution failed: ' . $stmt->error);
                }
                
                $result = $stmt->get_result();
                if ($result === false) {
                    throw new Exception('Failed to get result set: ' . $stmt->error);
                }
                
                // Fetch results
                $room_numbers = [];
                while ($row = $result->fetch_assoc()) {
                    $room_numbers[] = [
                        'id' => $row['room_number_id'],
                        'room_number' => $row['room_number'],
                        'room_type_id' => $row['room_type_id'],
                        'room_type_name' => $row['room_type_name'],
                        'floor' => $row['floor_number'] ?? null,
                        'status' => $row['status'] ?? 'active', // Default to lowercase 'active'
                        'description' => $row['description'] ?? null,
                        'created_at' => $row['created_at']
                    ];
                }
                
                // Successful response
                $response = [
                    'success' => true,
                    'room_numbers' => $room_numbers,
                    'debug' => [
                        'room_type_id' => $room_type_id,
                        'found_rows' => count($room_numbers),
                        'sample_data' => !empty($room_numbers) ? $room_numbers[0] : 'No data'
                    ]
                ];
                
            } catch (Exception $e) {
                // Error response
                $response = [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'debug' => [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]
                ];
                
                // Log the error
                error_log('Room numbers error: ' . $e->getMessage());
                error_log('Trace: ' . $e->getTraceAsString());
            }
            
            // Return the response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            
        case 'add_room_numbers':
            try {
                // Get form data
                $room_type_id = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
                $room_numbers_input = isset($_POST['room_numbers']) ? trim($_POST['room_numbers']) : '';
                $floor = isset($_POST['floor']) ? mysqli_real_escape_string($con, $_POST['floor']) : '';
                $description = isset($_POST['description']) ? mysqli_real_escape_string($con, $_POST['description']) : '';
                // Always set status to 'active' for new rooms
                $status = 'active';
                
                // Validate input
                if (empty($room_type_id) || empty($room_numbers_input)) {
                    throw new Exception('Room type and room numbers are required');
                }
                
                // Get room type details
                $type_sql = "SELECT room_type, price, capacity, description, beds, image, image2, image3 
                            FROM room_types 
                            WHERE room_type_id = ?";
                $type_stmt = $con->prepare($type_sql);
                $type_stmt->bind_param('i', $room_type_id);
                $type_stmt->execute();
                $type_result = $type_stmt->get_result();
                
                if ($type_result->num_rows === 0) {
                    throw new Exception('Room type not found');
                }
                
                $room_type = $type_result->fetch_assoc();
                
                // Parse room numbers (support both comma-separated and newline-separated)
                $room_numbers = [];
                $lines = preg_split("/[\n,]+/", $room_numbers_input);
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        // Split by comma if there are multiple numbers in one line
                        $numbers_in_line = array_map('trim', explode(',', $line));
                        foreach ($numbers_in_line as $number) {
                            if (!empty($number)) {
                                $room_numbers[] = $number;
                            }
                        }
                    }
                }
                
                if (empty($room_numbers)) {
                    throw new Exception('No valid room numbers provided');
                }
                
                // Prepare the insert statement for room_numbers table
                $sql = "INSERT INTO room_numbers (
                    room_number, 
                    room_type_id, 
                    floor_number, 
                    description,
                    status
                ) VALUES (?, ?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                
                $added_count = 0;
                $duplicates = [];
                
                // Begin transaction for batch insert
                $con->begin_transaction();
                
                try {
                    foreach ($room_numbers as $room_number) {
                        // Skip empty room numbers
                        if (empty($room_number)) continue;
                        
                        // Check if room number already exists
                        $check_sql = "SELECT id FROM rooms WHERE room_number = ?";
                        $check_stmt = $con->prepare($check_sql);
                        $check_stmt->bind_param('s', $room_number);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();
                        
                        if ($check_result->num_rows > 0) {
                            $duplicates[] = $room_number;
                            continue; // Skip duplicate room numbers
                        }
                        
                        // Bind parameters and execute insert
                        $stmt->bind_param('sissssdsssss', 
                            $room_number,
                            $room_type_id,
                            $floor,
                            $description,
                            $status,
                            $room_type['room_type'],
                            $room_type['price'],
                            $room_type['capacity'],
                            $room_type['beds'],
                            $room_type['image'],
                            $room_type['image2'],
                            $room_type['image3']
                        );
                        
                        if ($stmt->execute()) {
                            $added_count++;
                        } else {
                            throw new Exception('Error adding room number ' . $room_number . ': ' . $stmt->error);
                        }
                    }
                    
                    // Commit the transaction if all inserts were successful
                    $con->commit();
                    
                    $response['success'] = true;
                    $response['message'] = 'Room numbers added successfully';
                    $response['added_count'] = $added_count;
                    if (!empty($duplicates)) {
                        $response['duplicates'] = $duplicates;
                    }
                    
                } catch (Exception $e) {
                    // Rollback the transaction on error
                    $con->rollback();
                    throw $e;
                }
                
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            echo json_encode($response);
            exit;
    }


// The rest of the script should not be reached in AJAX requests due to exit;
// However, we can keep the final json_encode for non-AJAX calls or as a fallback, 
// although with the new structure it's less likely to be used for AJAX.
if (!isset($_POST['action']) && !isset($_GET['action'])) {
    // Handle non-AJAX requests or initial page load if this file is also used for that
    // For this file, it seems primarily an AJAX endpoint, so this block might be minimal or absent.
    // If you have rendering logic for non-AJAX requests here, keep it.
} else {
    // This final json_encode is now redundant for AJAX calls due to exit; in each case.
    // However, we will keep the ob_clean() just in case.
    if (ob_get_length()) ob_clean();
    // error_log("Sending final fallback response (should not happen for AJAX): " . print_r($response, true));
    // echo json_encode($response);
} 