<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the request for debugging
file_put_contents('reservation_debug.log', "[" . date('Y-m-d H:i:s') . "] New reservation request: " . file_get_contents('php://input') . "\n", FILE_APPEND);

try {
    require 'db_con.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed']);
    exit;
}

    // Get the raw POST data
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception('No data received');
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // Validate required fields
    $requiredFields = ['packageName', 'reservationDate', 'reservationStartTime', 'reservationCapacity'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Generate a unique booking reference
    $bookingId = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    
    // Get user ID from session (or use 1 as default for guests)
    $userId = $_SESSION['user_id'] ?? 1;
    
    // Prepare the SQL statement
    $sql = "INSERT INTO table_bookings (
        booking_id,
        user_id,
        package_name,
        booking_date,
        booking_time,
        num_guests,
        status,
        created_at
    ) VALUES (
        :booking_id,
        :user_id,
        :package_name,
        :booking_date,
        :booking_time,
        :num_guests,
        'Pending',
        NOW()
    )";

    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':booking_id' => $bookingId,
        ':user_id' => $userId,
        ':package_name' => $data['packageName'],
        ':booking_date' => $data['reservationDate'],
        ':booking_time' => $data['reservationStartTime'],
        ':num_guests' => $data['reservationCapacity']
    ]);

    if ($result) {
        $response = [
            'status' => 'success',
            'message' => 'Reservation successful!',
            'reference' => $bookingId
        ];
        file_put_contents('reservation_success.log', "[" . date('Y-m-d H:i:s') . "] Reservation successful: " . json_encode($response) . "\n", FILE_APPEND);
        echo json_encode($response);
    } else {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('Database error: ' . ($errorInfo[2] ?? 'Unknown error'));
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    $errorResponse = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    
    // Log detailed error
    $logMessage = sprintf(
        "[%s] Error: %s in %s on line %d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    
    file_put_contents('reservation_errors.log', $logMessage, FILE_APPEND);
    
    // Send response
    echo json_encode($errorResponse);
}
?>