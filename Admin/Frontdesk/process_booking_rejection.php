<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'rejection_error.log');

require_once 'db.php';
require_once 'email_functions.php';

// Set JSON header
header('Content-Type: application/json');

try {
    // Validate input
    if (empty($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    
    // First, get the user's email and name
    $select_sql = "SELECT b.*, rt.room_type 
                   FROM bookings b 
                   LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id 
                   WHERE b.booking_id = ? AND b.status = 'pending'";
    $select_stmt = $con->prepare($select_sql);
    
    if (!$select_stmt) {
        throw new Exception("Database error: " . mysqli_error($con));
    }
    
    $select_stmt->bind_param("s", $booking_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Booking not found or not in pending status");
    }
    
    $booking = $result->fetch_assoc();
    
    // Update booking status
    $update_sql = "UPDATE bookings SET status = 'Rejected' WHERE booking_id = ? AND status = 'pending'";
    $stmt = $con->prepare($update_sql);
    
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($con));
    }
    
    $stmt->bind_param("s", $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update booking: " . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        // Prepare email content
        $emailData = [
            'name' => $booking['first_name'] . ' ' . $booking['last_name'],
            'booking_id' => $booking_id,
            'check_in' => date('F j, Y', strtotime($booking['check_in'])),
            'check_out' => date('F j, Y', strtotime($booking['check_out'])),
            'room_type' => $booking['room_type'],
            'total_amount' => number_format($booking['total_amount'], 2),
            'reason' => 'Room Unavailable'
        ];

        // Send email using the email function
        try {
            sendBookingRejectionEmail($booking['email'], $emailData['name'], $emailData);
            echo json_encode([
                'success' => true,
                'message' => 'Booking has been rejected and notification email sent'
            ]);
        } catch (Exception $emailError) {
            error_log("Email error: " . $emailError->getMessage());
            echo json_encode([
                'success' => true,
                'message' => 'Booking rejected but failed to send notification email'
            ]);
        }
    } else {
        throw new Exception("No booking was updated. The booking may not exist or is not in pending status.");
    }

} catch (Exception $e) {
    error_log("Rejection error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
if (isset($con)) {
    mysqli_close($con);
}
?> 