<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $data = null, $message = '') {
    $response = [
        'success' => $success,
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    if ($message !== '') {
        $response['message'] = $message;
    }
    
    echo json_encode($response);
    exit;
}

// Check if booking_id is provided
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    error_log("No booking_id in GET data. GET contents: " . print_r($_GET, true));
    sendResponse(false, null, 'No booking ID provided');
}

// Get and sanitize booking_id
$booking_id = mysqli_real_escape_string($con, $_GET['booking_id']);

// Check database connection
if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    sendResponse(false, null, 'Database connection failed');
}

// Prepare and execute the query to get booking details
$query = "SELECT 
    b.booking_id,
    b.first_name,
    b.last_name,
    CONCAT(b.first_name, ' ', b.last_name) as guest_name,
    b.email,
    b.contact,
    b.check_in,
    b.check_out,
    b.number_of_guests,
    b.room_number,
    b.total_amount,
    b.amount_paid,
    b.remaining_balance,
    b.payment_option,
    b.payment_method,
    b.status,
    b.adults,
    b.children,
    b.special_requests,
    b.created_at,
    b.updated_at,
    b.discount_type,
    b.discount_amount,
    b.extra_charges,
    b.actual_checkin,
    b.actual_checkout,
    rt.room_type,
    rt.room_name,
    rt.price as room_price,
    rt.description as room_description,
    rt.capacity as room_capacity,
    rt.bed_type
FROM bookings b
LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
WHERE b.booking_id = ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    error_log("Prepare statement failed: " . mysqli_error($con));
    sendResponse(false, null, 'Database error: ' . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "s", $booking_id);

if (!mysqli_stmt_execute($stmt)) {
    error_log("Execute failed: " . mysqli_stmt_error($stmt));
    sendResponse(false, null, 'Error executing query: ' . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    error_log("Get result failed: " . mysqli_error($con));
    sendResponse(false, null, 'Error fetching results: ' . mysqli_error($con));
}

if ($row = mysqli_fetch_assoc($result)) {
    // Format the data for the response
    $response_data = [
        // Basic Booking Info
        'booking_id' => $row['booking_id'],
        'guest_name' => $row['guest_name'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'email' => $row['email'],
        'contact' => $row['contact'],
        'check_in' => $row['check_in'],
        'check_out' => $row['check_out'],
        'number_of_guests' => $row['number_of_guests'],
        'adults' => $row['adults'] ?? null,
        'children' => $row['children'] ?? null,
        'special_requests' => $row['special_requests'] ?? null,
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
        
        // Room Information
        'room_type' => $row['room_type'],
        'room_name' => $row['room_name'],
        'room_number' => $row['room_number'],
        'room_price' => $row['room_price'],
        'room_description' => $row['room_description'] ?? null,
        'room_capacity' => $row['room_capacity'] ?? null,
        'bed_type' => $row['bed_type'] ?? null,
        
        // Payment Information
        'total_amount' => $row['total_amount'],
        'amount_paid' => $row['amount_paid'],
        'remaining_balance' => $row['remaining_balance'],
        'payment_option' => $row['payment_option'],
        'payment_method' => $row['payment_method'],
        'discount_type' => $row['discount_type'] ?? null,
        'discount_amount' => $row['discount_amount'] ?? 0,
        'extra_charges' => $row['extra_charges'] ?? 0,
        
        // Status and Timestamps
        'status' => $row['status'],
        'actual_checkin' => $row['actual_checkin'] ?? null,
        'actual_checkout' => $row['actual_checkout'] ?? null
    ];
    
    sendResponse(true, $response_data);
} else {
    error_log("No data found for booking ID: " . $booking_id);
    sendResponse(false, null, 'No booking found with ID: ' . $booking_id);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
