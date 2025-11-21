<?php
require_once 'db.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Enable error reporting and custom log for this file
error_reporting(E_ALL);
ini_set('display_errors', 0); // Do not display errors to the user
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/room_details_errors.log');

// Debug log function specific to this script
function debug_log_details($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message . "\n", 3, __DIR__ . '/room_details_errors.log');
}

debug_log_details("get_room_details_new.php started execution.");

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    debug_log_details("Method not allowed: " . $_SERVER['REQUEST_METHOD']);
    exit;
}

// Validate room_id parameter
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid room ID']);
    debug_log_details("Invalid room ID: " . ($_GET['room_id'] ?? 'not set'));
    exit;
}

$room_id = intval($_GET['room_id']);
debug_log_details("Received room_id: " . $room_id);

try {
    // Get room type details with available rooms
    // Using `rt.image` and `rt.price` as per database dump
    $query = "SELECT 
        rt.room_type_id, 
        rt.room_type, 
        rt.description, 
        rt.price, 
        rt.capacity,
        rt.image,
        GROUP_CONCAT(DISTINCT rn.room_number) as available_room_numbers,
        COUNT(DISTINCT rn.room_number) as total_available_rooms
    FROM room_types rt 
    LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
    WHERE rt.room_type_id = ? AND rn.status = 'active'
    GROUP BY rt.room_type_id";

    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        debug_log_details("Error preparing main query: " . mysqli_error($con));
        throw new Exception("Database query preparation failed.");
    }

    mysqli_stmt_bind_param($stmt, 'i', $room_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        debug_log_details("Error executing main query: " . mysqli_stmt_error($stmt));
        throw new Exception("Database query execution failed.");
    }

    $result = mysqli_stmt_get_result($stmt);

    if ($room = mysqli_fetch_assoc($result)) {
        debug_log_details("Main room details fetched successfully.", $room);

        // Get amenities for this room type
        $amenities = [];
        $amenities_query = "SELECT 
            a.amenity_id as id,
            a.name,
            a.icon
        FROM room_type_amenities ra 
        JOIN amenities a ON ra.amenity_id = a.amenity_id 
        WHERE ra.room_type_id = ?";
        
        $stmt_amenities = mysqli_prepare($con, $amenities_query);
        if ($stmt_amenities) {
            mysqli_stmt_bind_param($stmt_amenities, 'i', $room_id);
            if (!mysqli_stmt_execute($stmt_amenities)) {
                debug_log_details("Error executing amenities query: " . mysqli_stmt_error($stmt_amenities));
            } else {
                $amenities_result = mysqli_stmt_get_result($stmt_amenities);
                if ($amenities_result) {
                    while ($amenity = mysqli_fetch_assoc($amenities_result)) {
                        $amenities[] = [
                            'id' => $amenity['id'],
                            'name' => $amenity['name'],
                            'icon' => $amenity['icon']
                        ];
                    }
                } else {
                    debug_log_details("Error getting amenities result: " . mysqli_error($con));
                }
            }
            mysqli_stmt_close($stmt_amenities);
        } else {
            debug_log_details("Error preparing amenities query: " . mysqli_error($con));
        }
        debug_log_details("Amenities fetched:", $amenities);
        
        // Get room images
        $images = [];
        $images_query = "SELECT 
            image_path as image
        FROM room_images 
        WHERE room_type_id = ?";
        
        $stmt_images = mysqli_prepare($con, $images_query);
        if ($stmt_images) {
            mysqli_stmt_bind_param($stmt_images, 'i', $room_id);
            if (!mysqli_stmt_execute($stmt_images)) {
                debug_log_details("Error executing images query: " . mysqli_stmt_error($stmt_images));
            } else {
                $images_result = mysqli_stmt_get_result($stmt_images);
                if ($images_result) {
                    while ($image = mysqli_fetch_assoc($images_result)) {
                        $images[] = [
                            'path' => htmlspecialchars($image['image'])
                        ];
                    }
                } else {
                    debug_log_details("Error getting images result: " . mysqli_error($con));
                }
            }
            mysqli_stmt_close($stmt_images);
        } else {
            debug_log_details("Error preparing images query: " . mysqli_error($con));
        }
        debug_log_details("Images fetched:", $images);

        // Prepare response data
        $response = [
            'room_id' => $room['room_type_id'],
            'room_type' => $room['room_type'],
            'description' => $room['description'],
            'price' => floatval($room['price']),
            'capacity' => intval($room['capacity']),
            'available_room_numbers' => explode(',', $room['available_room_numbers'] ?? ''),
            'total_available_rooms' => intval($room['total_available_rooms'] ?? 0),
            'amenities' => $amenities,
            'images' => $images,
            'policies' => [], // Ensure policies is an empty array if not fetched
            'created_at' => $room['created_at'] ?? null,
            'updated_at' => $room['updated_at'] ?? null
        ];
        
        debug_log_details("Sending JSON response.", $response);
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Room not found']);
        debug_log_details("Room with ID " . $room_id . " not found.");
    }
} catch (Exception $e) {
    debug_log_details("General error in get_room_details_new.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => 'A server error occurred. Please check logs for details.'
    ]);
} finally {
    if (isset($stmt)) mysqli_stmt_close($stmt);
    if (isset($con)) mysqli_close($con);
}
?> 