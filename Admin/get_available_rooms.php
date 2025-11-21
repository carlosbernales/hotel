<?php
// Enable error reporting and display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Include database connection
require_once 'db.php';

// Log the start of the script
error_log("=== Starting get_available_rooms.php ===");

// Get and validate room type ID
$room_type_id = isset($_GET['room_type_id']) ? intval($_GET['room_type_id']) : 0;

// Log the received parameters
error_log("Received parameters: " . print_r($_GET, true));

if ($room_type_id <= 0) {
    $error = 'Invalid room type ID';
    error_log("Error: $error");
    echo json_encode([
        'success' => false,
        'message' => $error
    ]);
    exit;
}

try {
    // First, get room type details to verify it exists
    $typeSql = "SELECT * FROM room_types WHERE room_type_id = ?";
    $typeStmt = $con->prepare($typeSql);
    if (!$typeStmt) {
        throw new Exception("Failed to prepare type query: " . $con->error);
    }
    
    $typeStmt->bind_param("i", $room_type_id);
    if (!$typeStmt->execute()) {
        throw new Exception("Failed to execute type query: " . $typeStmt->error);
    }
    
    $typeResult = $typeStmt->get_result();
    if ($typeResult->num_rows === 0) {
        throw new Exception("Room type not found with ID: $room_type_id");
    }
    $typeStmt->close();
    
    // Get all active rooms for this room type
    $sql = "SELECT 
                room_number_id, 
                room_number, 
                COALESCE(floor_number, 0) as floor_number 
            FROM room_numbers 
            WHERE room_type_id = ? 
            AND status = 'active' 
            ORDER BY room_number";
    
    error_log("Executing query: $sql with room_type_id: $room_type_id");
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param("i", $room_type_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $rooms = [];
    
    while ($row = $result->fetch_assoc()) {
        $rooms[] = [
            'room_number_id' => (int)$row['room_number_id'],
            'room_number' => $row['room_number'],
            'floor' => (int)$row['floor_number']
        ];
    }
    
    $stmt->close();
    
    error_log("Found " . count($rooms) . " active rooms for type $room_type_id");
    
    if (empty($rooms)) {
        error_log("No active rooms found for room type ID: $room_type_id");
    }

    // Prepare the response
    $response = [
        'success' => true,
        'rooms' => $rooms,
        'message' => count($rooms) > 0 ? 'Rooms loaded successfully' : 'No active rooms found for this room type'
    ];
    
    // Add debug info if requested
    if (isset($_GET['debug'])) {
        $response['debug'] = [
            'room_type_id' => $room_type_id,
            'total_rooms' => count($rooms),
            'room_numbers' => array_column($rooms, 'room_number')
        ];
    }
    
    // Send the response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    error_log("Response sent with " . count($rooms) . " rooms: " . json_encode($response));

} catch (Exception $e) {
    error_log("Error in get_available_rooms.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch available rooms: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} finally {
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>