<?php
// First, start output buffering to catch any unwanted output
ob_start();

// Set the content type header first
header('Content-Type: application/json');

// Now enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display_errors
ini_set('log_errors', 1);
error_log("Processing event details request");

require_once 'db.php';

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authorized');
    }

    if (!isset($_POST['event_id'])) {
        throw new Exception('Event ID not provided');
    }

    if (!$con) {
        throw new Exception('Database connection failed');
    }

    $event_id = mysqli_real_escape_string($con, $_POST['event_id']);

    // Debug log
    error_log("Fetching details for event ID: " . $event_id);

    $query = "SELECT eb.*, 
                     COALESCE(ep.name, eb.package_name) as display_package_name,
                     ep.menu_items as package_menu,
                     CASE 
                         WHEN eb.booking_source = 'walk_in' THEN eb.customer_name
                         ELSE CONCAT(u.firstname, ' ', u.lastname)
                     END as customer_name
              FROM event_bookings eb 
              LEFT JOIN event_packages ep ON eb.package_name = ep.name 
              LEFT JOIN users u ON eb.user_id = u.id AND eb.booking_source = 'Regular Booking'
              WHERE eb.id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        error_log("Raw data from database: " . print_r($row, true)); // Debug log

        // Format the response data
        $response = [
            'success' => true,
            'details' => [
                'package_name' => $row['display_package_name'],
                'package_menu' => $row['package_menu'],
                'package_price' => $row['package_price'],
                'event_type' => $row['event_type'],
                'customer_name' => $row['customer_name'],
                'event_date' => date('M j, Y', strtotime($row['reservation_date'])),
                'time' => $row['start_time'] . ' - ' . $row['end_time'],
                'number_of_guests' => $row['number_of_guests'],
                'total_amount' => $row['total_amount'],
                'payment_method' => $row['payment_method'],
                'payment_type' => $row['payment_type'],
                'reference_number' => $row['reference_number'] ?? 'N/A',
                'payment_status' => $row['payment_status'] ?? 'Pending',
                'booking_status' => $row['booking_status']
            ]
        ];

        error_log("Formatted data being sent: " . print_r($response, true)); // Debug log
        
        ob_clean();
        echo json_encode($response);
    } else {
        throw new Exception('Event not found');
    }

} catch (Exception $e) {
    error_log('Event Details Error: ' . $e->getMessage());
    // Clear any output and send JSON error response
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

// Clean up
if (isset($stmt)) {
    $stmt->close();
}

// End output buffering and flush
ob_end_flush();
?> 