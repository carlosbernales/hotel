<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Debug: Log all POST data
error_log("POST data received: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get the booking ID and action
$booking_id = isset($_POST['booking_id']) ? mysqli_real_escape_string($con, $_POST['booking_id']) : null;
$action = isset($_POST['action']) ? mysqli_real_escape_string($con, $_POST['action']) : null;
$payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($con, $_POST['payment_method']) : null;

if (!$booking_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Start transaction
mysqli_begin_transaction($con);

try {
    // First verify the booking exists and get its details
    $check_sql = "SELECT b.*, rt.room_type, rt.price as room_price
                 FROM bookings b 
                 LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id 
                 WHERE b.booking_id = ?";
    
    $check_stmt = $con->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Failed to prepare check statement: " . $con->error);
    }
    
    $check_stmt->bind_param("s", $booking_id);
    if (!$check_stmt->execute()) {
        throw new Exception("Failed to execute check query: " . $check_stmt->error);
    }
    
    $result = $check_stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Booking not found with ID: " . $booking_id);
    }
    
    $booking = $result->fetch_assoc();
    error_log("Found booking data: " . print_r($booking, true));

    // Different status checks based on action
    switch ($action) {
        case 'confirm':
            // For check-in, verify booking is pending
            if ($booking['status'] !== 'pending') {
                throw new Exception("Booking must be in pending status to check in");
            }
            $new_status = 'Checked in';
            break;
            
        case 'checkout':
            // For check-out, verify booking is checked in
            if ($booking['status'] !== 'Checked in') {
                throw new Exception("Booking must be in Checked in status to check out");
            }
            $new_status = 'Checked Out';
            break;
            
        case 'early_checkout':
            // For early check-out, verify booking is checked in
            if ($booking['status'] !== 'Checked in') {
                throw new Exception("Booking must be in Checked in status for early check-out");
            }
            $new_status = 'Checked Out';
            break;
            
        case 'update_checkout_date':
            try {
                // Get the parameters
                $booking_id = $_POST['booking_id'];
                $new_amount = $_POST['new_amount'];
                $actual_nights = $_POST['actual_nights'];
                $new_checkout_date = $_POST['new_checkout_date'];
                $amount_to_return = $_POST['amount_to_return'];

                // Update the booking with new check-out date and amount
                $update_sql = "UPDATE bookings SET 
                    check_out = ?,
                    total_amount = ?,
                    nights = ?,
                    status = 'Checked Out'
                    WHERE booking_id = ?";
                
                $stmt = mysqli_prepare($con, $update_sql);
                if (!$stmt) {
                    throw new Exception("Failed to prepare update statement: " . mysqli_error($con));
                }

                mysqli_stmt_bind_param($stmt, "sdii", $new_checkout_date, $new_amount, $actual_nights, $booking_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to update booking: " . mysqli_stmt_error($stmt));
                }

                // Create a notification for the early checkout
                $notification_message = "Early checkout processed. Amount to return: ₱" . number_format($amount_to_return, 2);
                $notif_sql = "INSERT INTO notifications (reference_id, message, type, created_at) VALUES (?, ?, 'booking', NOW())";
                
                $notif_stmt = mysqli_prepare($con, $notif_sql);
                if (!$notif_stmt) {
                    throw new Exception("Failed to prepare notification statement: " . mysqli_error($con));
                }

                mysqli_stmt_bind_param($notif_stmt, "is", $booking_id, $notification_message);
                
                if (!mysqli_stmt_execute($notif_stmt)) {
                    throw new Exception("Failed to create notification: " . mysqli_stmt_error($notif_stmt));
                }

                // Commit transaction
                mysqli_commit($con);
                
                // Return success with redirect
                echo json_encode([
                    'success' => true,
                    'redirect' => 'checked_out.php'
                ]);
                exit(); // Add exit to prevent further processing
            } catch (Exception $e) {
                mysqli_rollback($con);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit(); // Add exit to prevent further processing
            }
            break;
            
        default:
            throw new Exception("Invalid action specified");
    }

    // Only process this section for non-early checkout actions
    if ($action !== 'update_checkout_date') {
        // Update booking status
        $update_sql = "UPDATE bookings SET status = ?, payment_method = ? WHERE booking_id = ?";
        $update_stmt = $con->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Failed to prepare update statement: " . $con->error);
        }
        
        $update_stmt->bind_param("sss", $new_status, $payment_method, $booking_id);
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update booking status: " . $update_stmt->error);
        }

        // Create notification
        $message = "Booking #" . $booking_id . " has been " . strtolower($new_status);
        $notif_sql = "INSERT INTO notifications (reference_id, message, type, created_at) VALUES (?, ?, 'booking', NOW())";
        $notif_stmt = $con->prepare($notif_sql);
        if (!$notif_stmt) {
            throw new Exception("Failed to prepare notification statement: " . $con->error);
        }
        
        $notif_stmt->bind_param("ss", $booking_id, $message);
        if (!$notif_stmt->execute()) {
            throw new Exception("Failed to create notification: " . $notif_stmt->error);
        }

        // If we got here, everything succeeded
        mysqli_commit($con);
        
        // Return success response with redirect
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully',
            'redirect' => $new_status === 'Checked Out' ? 'checked_out.php' : ($new_status === 'Checked In' ? 'checked_in.php' : 'booking_status.php')
        ]);
    }

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    error_log("Error in update_booking_status.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($con);
?>
