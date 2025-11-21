<?php
require_once 'db.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');

// Function to send JSON response
function send_response($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Check database connection
if (!$con) {
    error_log("Database connection not available");
    send_response(false, 'Database connection error');
}

// Verify connection is still alive
if (!mysqli_ping($con)) {
    error_log("Database connection lost");
    // Try to reconnect
    mysqli_close($con);
    require_once 'db.php';
    if (!$con) {
        send_response(false, 'Lost database connection and failed to reconnect');
    }
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    send_response(false, 'Invalid request method');
}

// Check if booking_id is provided
if (!isset($_POST['booking_id'])) {
    error_log("Missing booking_id in POST data");
    error_log("POST data received: " . print_r($_POST, true));
    send_response(false, 'Missing booking ID');
}

$booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
error_log("Processing check-in for booking_id: " . $booking_id);

// Get additional booking identifiers if they exist
$guest_name = isset($_POST['guest_name']) ? mysqli_real_escape_string($con, $_POST['guest_name']) : '';
$check_in = isset($_POST['check_in']) ? mysqli_real_escape_string($con, $_POST['check_in']) : '';
$check_out = isset($_POST['check_out']) ? mysqli_real_escape_string($con, $_POST['check_out']) : '';
$email = isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : '';

try {
    // Start transaction
    if (!mysqli_begin_transaction($con)) {
        error_log("Failed to start transaction: " . mysqli_error($con));
        throw new Exception("Could not start transaction");
    }
    error_log("Transaction started successfully");

    // Build a query that can handle cases where booking_id might be 0
    $query_params = [];
    $check_sql = "SELECT b.*, rt.room_type, rt.price as room_price 
                 FROM bookings b
                 LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
                 WHERE 1=1";

    if (!empty($booking_id) && $booking_id != '0') {
        $check_sql .= " AND b.booking_id = ?";
        $query_params[] = $booking_id;
    } else {
        // We need alternative ways to identify the booking
        // First try to use row number if this is in the context of results from a query
        error_log("Booking ID is 0 or empty, attempting to use alternative identifiers");
        
        // If any of these other identifiers exist, use them
        if (!empty($email)) {
            $check_sql .= " AND b.email = ?";
            $query_params[] = $email;
        }
        
        if (!empty($check_in)) {
            $check_sql .= " AND b.check_in = ?";
            $query_params[] = $check_in;
        }
        
        if (!empty($check_out)) {
            $check_sql .= " AND b.check_out = ?";
            $query_params[] = $check_out;
        }
        
        if (!empty($guest_name)) {
            // Try to extract first and last name
            $name_parts = explode(' ', $guest_name);
            if (count($name_parts) > 1) {
                $first_name = $name_parts[0];
                $last_name = end($name_parts);
                
                $check_sql .= " AND b.first_name = ? AND b.last_name = ?";
                $query_params[] = $first_name;
                $query_params[] = $last_name;
            }
        }
    }
    
    // Only look for pending bookings and limit to one result
    $check_sql .= " AND b.status = 'pending' LIMIT 1";
    
    error_log("Executing query: " . $check_sql . " with params: " . print_r($query_params, true));
    
    $stmt = mysqli_prepare($con, $check_sql);
    if (!$stmt) {
        error_log("Failed to prepare check statement: " . mysqli_error($con));
        throw new Exception("Error preparing check statement: " . mysqli_error($con));
    }
    
    if (!empty($query_params)) {
        $types = str_repeat('s', count($query_params));
        $stmt->bind_param($types, ...$query_params);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Failed to execute check statement: " . mysqli_error($con));
        throw new Exception("Error checking booking status: " . mysqli_error($con));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    
    if (!$booking) {
        error_log("No booking found with the provided details");
        throw new Exception("Booking not found. Please verify the booking details.");
    }
    
    error_log("Booking found: " . print_r($booking, true));
    
    if (strtolower($booking['status']) !== 'pending') {
        error_log("Invalid booking status: " . $booking['status']);
        throw new Exception("Invalid booking status for check-in. Current status: " . $booking['status']);
    }

    // Get the actual booking ID from the result
    $booking_id = $booking['booking_id'];
    
    // Update booking status to Checked in
    $update_sql = "UPDATE bookings SET status = 'Checked in' WHERE booking_id = ? AND status = 'pending'";
    error_log("Executing update query: " . $update_sql . " with booking_id: " . $booking_id);
    
    $stmt = mysqli_prepare($con, $update_sql);
    if (!$stmt) {
        error_log("Failed to prepare update statement: " . mysqli_error($con));
        throw new Exception("Error preparing update statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $booking_id);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Failed to execute update statement: " . mysqli_error($con));
        throw new Exception("Error updating booking status: " . mysqli_error($con));
    }
    
    $affected_rows = mysqli_affected_rows($con);
    error_log("Affected rows from update: " . $affected_rows);
    
    if ($affected_rows === 0) {
        error_log("No rows were updated in the database");
        throw new Exception("No booking was updated. It may have been already processed.");
    }

    // Commit transaction
    if (!mysqli_commit($con)) {
        error_log("Failed to commit transaction: " . mysqli_error($con));
        throw new Exception("Could not commit transaction");
    }
    error_log("Transaction committed successfully");

    // Try to send email, but don't let email failures affect the booking confirmation
    try {
        $to = $booking['email'];
        $subject = "Check-in Confirmation - Casa Estela Boutique Hotel & Cafe";
        
        $message = "Dear " . htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) . ",\n\n";
        $message .= "You have successfully checked in!\n\n";
        $message .= "Booking Details:\n";
        $message .= "Booking ID: " . $booking['booking_id'] . "\n";
        $message .= "Room Type: " . $booking['room_type'] . "\n";
        $message .= "Check-in: " . date('F j, Y', strtotime($booking['check_in'])) . "\n";
        $message .= "Check-out: " . date('F j, Y', strtotime($booking['check_out'])) . "\n";
        $message .= "Number of Guests: " . $booking['number_of_guests'] . "\n";
        $message .= "Total Amount: ₱" . number_format($booking['total_amount'], 2) . "\n";
        if ($booking['payment_option'] === 'downpayment') {
            $message .= "Amount Paid: ₱" . number_format($booking['downpayment_amount'], 2) . "\n";
            $message .= "Remaining Balance: ₱" . number_format($booking['total_amount'] - $booking['downpayment_amount'], 2) . "\n";
        }
        $message .= "\nWe hope you enjoy your stay at Casa Estela Boutique Hotel & Cafe!\n";
        
        $headers = "From: Casa Estela <noreply@e-akomoda.site>\r\n";
        $headers .= "Reply-To: info@e-akomoda.site\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        mail($to, $subject, $message, $headers);
    } catch (Exception $e) {
        // Log email error but don't affect the response
        error_log("Email sending failed: " . $e->getMessage());
    }
    
    send_response(true, 'Guest has been checked in successfully', ['redirect' => 'checked_in.php']);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    error_log("Check-in error: " . $e->getMessage());
    send_response(false, $e->getMessage());
}

// Close database connection
mysqli_close($con);
?> 