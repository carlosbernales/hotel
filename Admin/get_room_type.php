<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug_room_type.log');

// Set JSON header
header('Content-Type: application/json');

// Simple response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'debug' => []
];

// Helper function to add debug info
function addDebug($key, $value) {
    global $response;
    if (!isset($response['debug'])) {
        $response['debug'] = [];
    }
    $response['debug'][$key] = $value;
    
    // Also log to file
    $logMessage = date('[Y-m-d H:i:s] ') . $key . ': ' . print_r($value, true) . "\n";
    error_log($logMessage, 3, __DIR__ . '/debug_room_type.log');
}

// Start debug logging
addDebug('start_time', date('Y-m-d H:i:s'));
addDebug('request', $_GET);
addDebug('php_version', phpversion());
addDebug('server_software', $_SERVER['SERVER_SOFTWARE'] ?? 'N/A');

try {
    // Check if ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Valid room type ID is required');
    }
    
    $room_type_id = (int)$_GET['id'];
    
    // Include database connection
    require_once 'includes/init.php';
    
    if (!isset($con) || !($con instanceof mysqli)) {
        throw new Exception('Database connection failed');
    }
    
    // Query to get room type data
    $sql = "SELECT * FROM room_types WHERE room_type_id = ?";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $con->error);
    }
    
    $stmt->bind_param("i", $room_type_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Room type not found');
    }
    
    // Get room type data
    $roomType = $result->fetch_assoc();
    
    // Get amenities for this room type
    $amenities = [];
    try {
        // First check if the amenities tables exist
        $checkTable = $con->query("SHOW TABLES LIKE 'amenities'");
        if ($checkTable->num_rows > 0) {
            $amenityQuery = "SELECT a.amenity_id, a.name 
                           FROM amenities a 
                           JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id 
                           WHERE rta.room_type_id = ?";

            $stmt = $con->prepare($amenityQuery);
            if ($stmt) {
                $stmt->bind_param("i", $room_type_id);
                if ($stmt->execute()) {
                    $amenityResult = $stmt->get_result();
                    while ($row = $amenityResult->fetch_assoc()) {
                        $amenities[] = $row;
                    }
                } else {
                    error_log("Error executing amenities query: " . $stmt->error);
                }
                $stmt->close();
            }
        }
    } catch (Exception $e) {
        error_log("Error fetching amenities: " . $e->getMessage());
        // Don't fail the whole request if amenities fail
    }
    
    // Add amenities to room type data
    $roomType['amenities'] = $amenities;
    
    // Return success response
    $response = [
        'success' => true,
        'data' => $roomType
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(500);
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
