<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_con.php';
header('Content-Type: application/json');

// Log the request
file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['package_name'])) {
        throw new Exception('Package name is required');
    }

    $packageName = $data['package_name'];
    
    // First, check if the package exists
    $checkStmt = $pdo->prepare("SELECT name FROM event_packages WHERE name = :package_name");
    $checkStmt->execute([':package_name' => $packageName]);
    
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Package not found');
    }

    // Get all bookings for this package
    $query = "
        SELECT 
            event_date as formatted_date,
            DATE_FORMAT(event_date, '%M %e, %Y') as display_date,
            start_time,
            end_time,
            booking_status
        FROM event_bookings 
        WHERE booking_status IN ('pending', 'confirmed')
        AND package_name = :package_name
        AND event_date >= CURRENT_DATE
        ORDER BY event_date ASC, start_time ASC
    ";
    
    file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] Executing query: " . $query . "\n", FILE_APPEND);
    file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] Package name: " . $packageName . "\n", FILE_APPEND);

    $stmt = $pdo->prepare($query);
    $stmt->execute([':package_name' => $packageName]);
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] Found " . count($bookings) . " bookings\n", FILE_APPEND);
    file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] Bookings: " . print_r($bookings, true) . "\n", FILE_APPEND);

    // Format the response
    $response = [
        'success' => true,
        'bookings' => $bookings
    ];

    // If no bookings, return an empty array
    if (empty($bookings)) {
        $response['message'] = 'No upcoming bookings found for this package.';
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    $error = [
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    file_put_contents('get_booked_dates.log', "[" . date('Y-m-d H:i:s') . "] ERROR: " . print_r($error, true) . "\n", FILE_APPEND);
    
    http_response_code(400);
    echo json_encode($error, JSON_PRETTY_PRINT);
}
?>