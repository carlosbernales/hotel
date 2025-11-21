<?php
// Disable error display but log errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Start output buffering to catch any unwanted output
ob_start();

// Start session
session_start();

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    // Clear any previous output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Set the content type header
    header('Content-Type: application/json');
    
    // Send the JSON response
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Function to log errors
function logError($message) {
    error_log('Booking Error: ' . $message);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if JSON decoding was successful
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    sendResponse(false, 'Invalid JSON data: ' . json_last_error_msg());
}

// If no JSON data, try to get from $_POST
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

// Validate required fields
$required_fields = [
    'package_name', 'num_guests', 'booking_date', 
    'booking_time', 'duration', 'total_amount', 'payment_method',
    'payment_option', 'amount_paid'
];

$missing_fields = [];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    sendResponse(false, 'Missing required fields: ' . implode(', ', $missing_fields));
}

try {
    // Include database connection
    require_once 'db_con.php';
    global $pdo;
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Prepare the SQL statement
    $sql = "INSERT INTO table_bookings (
        user_id, package_name, 
        booking_date, booking_time, num_guests, payment_method, total_amount, 
        downpayment_amount, amount_paid, change_amount, payment_status, status, 
        package_type, payment_reference, payment_option, reservation_type, created_at
    ) VALUES (
        :user_id, :package_name,
        :booking_date, :booking_time, :num_guests, :payment_method, :total_amount,
        :downpayment_amount, :amount_paid, :change_amount, :payment_status, :status,
        :package_type, :payment_reference, :payment_option, :reservation_type, NOW()
    )";

    // Calculate values for the booking
    $user_id = $_SESSION['user_id'] ?? 0; // Assuming you have user authentication
    $is_partial = strtolower($data['payment_option']) === 'partial';
    $downpayment_amount = $is_partial ? $data['amount_paid'] : 0;
    $change_amount = 0; // Assuming no change for now
    $payment_status = 'Pending';
    $status = 'Pending';
    $package_type = strpos(strtolower($data['package_name']), 'ultimate') !== false ? 'Ultimate' : 'Standard';
    $payment_reference = 'BOOK-' . strtoupper(uniqid());
    $reservation_type = 'Online';

    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    
    // Calculate values for the booking
    $user_id = $_SESSION['user_id'] ?? 0; // Assuming you have user authentication
    $is_partial = strtolower($data['payment_option']) === 'partial';
    $downpayment_amount = $is_partial ? $data['amount_paid'] : 0;
    $change_amount = 0; // Assuming no change for now
    $payment_status = 'Paid';
    $status = 'Pending';
    $package_type = strpos(strtolower($data['package_name']), 'ultimate') !== false ? 'Ultimate' : 'Standard';
    $payment_reference = 'BOOK-' . strtoupper(uniqid());
    $reservation_type = 'Online';
    
    // Bind parameters
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':package_name', $data['package_name'], PDO::PARAM_STR);
    $stmt->bindParam(':booking_date', $data['booking_date'], PDO::PARAM_STR);
    $stmt->bindParam(':booking_time', $data['booking_time'], PDO::PARAM_STR);
    $stmt->bindParam(':num_guests', $data['num_guests'], PDO::PARAM_INT);
    $stmt->bindParam(':payment_method', $data['payment_method'], PDO::PARAM_STR);
    $stmt->bindParam(':total_amount', $data['total_amount'], PDO::PARAM_STR);
    $stmt->bindParam(':downpayment_amount', $downpayment_amount, PDO::PARAM_STR);
    $stmt->bindParam(':amount_paid', $data['amount_paid'], PDO::PARAM_STR);
    $stmt->bindParam(':change_amount', $change_amount, PDO::PARAM_STR);
    $stmt->bindParam(':payment_status', $payment_status, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':package_type', $package_type, PDO::PARAM_STR);
    $stmt->bindParam(':payment_reference', $payment_reference, PDO::PARAM_STR);
    $stmt->bindParam(':payment_option', $data['payment_option'], PDO::PARAM_STR);
    $stmt->bindParam(':reservation_type', $reservation_type, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();
    $booking_id = $pdo->lastInsertId();
    
    // Commit the transaction
    $pdo->commit();
    
    // Clear any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Send success response
    sendResponse(true, 'Booking created successfully', [
        'booking_id' => $booking_id,
        'reference_number' => $payment_reference
    ]);
    
} catch (PDOException $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage());
}
?>
