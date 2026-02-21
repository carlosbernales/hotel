<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to return JSON
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($status, $message, $data = []) {
    http_response_code($status);
    echo json_encode([
        'status' => $status === 200 ? 'success' : 'error',
        'message' => $message,
        'data' => $data,
        'debug' => $status !== 200 ? [
            'post_data' => $_POST,
            'timestamp' => date('Y-m-d H:i:s')
        ] : null
    ], JSON_PRETTY_PRINT);
    exit;
}

try {
    // Include database connection
    if (!@include('db_con.php')) {
        throw new Exception("Failed to include database connection file (db_con.php)");
    }
    
    // Check if PDO connection is successful
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception("PDO connection not properly initialized in db_con.php");
    }
    
    // Test the connection
    $pdo->query('SELECT 1')->fetch();

    // Get the selected date and time from the request
    $selectedDateTime = isset($_POST['event_datetime']) ? trim($_POST['event_datetime']) : '';
    
    // Validate input
    if (empty($selectedDateTime)) {
        sendResponse(400, 'Please select a date and time.');
    }

    // Convert the selected date/time to a timestamp
    $selectedTimestamp = strtotime($selectedDateTime);
    if ($selectedTimestamp === false) {
        sendResponse(400, 'Invalid date/time format.');
    }

    // Check if the selected time is within allowed hours (7:00 AM to 11:00 PM)
    $selectedTime = date('H:i', $selectedTimestamp);
    $startTime = '07:00';
    $endTime = '23:00';

    if ($selectedTime < $startTime || $selectedTime > $endTime) {
        sendResponse(200, 'Events can only be scheduled between 7:00 AM to 11:00 PM.', [
            'status' => 'Closed',
            'event_start' => date('Y-m-d H:i:s', $selectedTimestamp),
            'event_end' => date('Y-m-d H:i:s', strtotime('+4 hours', $selectedTimestamp))
        ]);
    }

    // Calculate event end time (4-hour duration)
    $eventEndTimestamp = strtotime('+4 hours', $selectedTimestamp);
    $eventStart = date('Y-m-d H:i:s', $selectedTimestamp);
    $eventEnd = date('Y-m-d H:i:s', $eventEndTimestamp);

    // Check for any overlapping events and get the booked package names
    $sql = "SELECT DISTINCT package_name 
            FROM event_bookings 
            WHERE 
                (date_time_start < :event_end AND date_time_end > :event_start)
                AND booking_status NOT IN ('finished', 'cancelled')";
    
    $stmt = $pdo->prepare($sql);
    
    $params = [
        ':event_end' => $eventEnd,
        ':event_start' => $eventStart
    ];
    
    // Log the query and parameters for debugging
    error_log("Checking availability from $eventStart to $eventEnd");
    
    $stmt->execute($params);
    $bookedPackages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Check total number of overlapping events
    $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM event_bookings 
                              WHERE (date_time_start < :event_end AND date_time_end > :event_start)
                              AND booking_status NOT IN ('finished', 'cancelled')");
    $countStmt->execute($params);
    $eventCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($eventCount > 0) {
        // There are overlapping events
        // Find the next available time slot with 2-hour preparation time
        // Get the latest date_time_end from existing events
        $latestEndTime = $pdo->query("SELECT MAX(date_time_end) as latest_end FROM event_bookings 
                                     WHERE date_time_end > NOW()
                                     AND booking_status NOT IN ('finished', 'cancelled')")->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($latestEndTime['latest_end'])) {
            // Calculate next available time by adding 2 hours to the latest end time
            $nextAvailable = $pdo->query("SELECT DATE_ADD('{$latestEndTime['latest_end']}', INTERVAL 2 HOUR) as next_available")->fetch(PDO::FETCH_ASSOC);
        } else {
            // No existing events, next available is 2 hours from now
            $nextAvailable = $pdo->query("SELECT DATE_ADD(NOW(), INTERVAL 2 HOUR) as next_available")->fetch(PDO::FETCH_ASSOC);
        }
        
        $suggestion = '';
        if (!empty($nextAvailable['next_available'])) {
            $nextTime = new DateTime($nextAvailable['next_available']);
            $suggestion = ' The next available time is ' . $nextTime->format('g:i A, F j, Y') . '.';
        }
        
        sendResponse(200, 'Some packages are already booked for the selected time. Please review the availability below.', [
            'status' => 'partial',
            'booked_packages' => $bookedPackages,
            'event_start' => $eventStart,
            'event_end' => $eventEnd,
            'next_available' => $nextAvailable ? $nextAvailable['next_available'] : null,
            'message' => 'Some packages are already booked for the selected time.'
        ]);
    } else {
        // If we get here, the time slot is available for all packages
        sendResponse(200, 'All packages are available for the selected time!', [
            'status' => 'available',
            'booked_packages' => [],
            'event_start' => $eventStart,
            'event_end' => $eventEnd
        ]);
    }

} catch (PDOException $e) {
    // Log the error for debugging
    error_log("PDO Error in events_check_availability.php: " . $e->getMessage());
    sendResponse(500, 'Database error occurred while checking availability.');
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error in events_check_availability.php: " . $e->getMessage());
    sendResponse(500, 'An error occurred while checking availability. Please try again later.');
}