<?php
require_once 'db.php';
require_once 'email_functions.php';

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
$room_number = isset($_POST['room_number']) ? mysqli_real_escape_string($con, $_POST['room_number']) : null;
$amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;

// Debug: Log the received parameters
error_log("Processing booking_id: " . $booking_id . ", action: " . $action . ", room_number: " . $room_number);

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
    error_log("Current booking status: " . $booking['status']);

    // Different status checks based on action
    switch (strtolower($action)) {
        case 'accept':
            // For accepting a booking, verify it's pending
            if (strtolower($booking['status']) !== 'pending') {
                error_log("Invalid status for accept: " . $booking['status']);
                throw new Exception("Booking must be in 'Pending' status to accept");
            }
            $new_status = 'accepted';
            
            // Prepare booking details for email
            $bookingDetails = [
                'booking_id' => $booking_id,
                'check_in' => $booking['check_in'],
                'check_out' => $booking['check_out'],
                'room_type' => $booking['room_type'],
                'nights' => $booking['nights'],
                'total_amount' => $booking['total_amount'],
                'amount_paid' => $amount_paid,
                'name' => $booking['first_name'] . ' ' . $booking['last_name']
            ];
            
            // Send acceptance email
            $emailSent = sendBookingAcceptanceEmail(
                $booking['email'],
                $booking['first_name'] . ' ' . $booking['last_name'],
                $bookingDetails
            );
            
            if (!$emailSent) {
                error_log("Failed to send acceptance email for booking ID: " . $booking_id);
                // Continue with the status update even if email fails
            }
            break;
            
        case 'confirm':
            // For check-in, verify booking is accepted
            if (strtolower($booking['status']) !== 'accepted') {
                error_log("Invalid status for check-in: " . $booking['status']);
                throw new Exception("Booking must be in 'Accepted' status to check in");
            }
            
            // Verify room number is provided
            if (empty($room_number)) {
                throw new Exception("Room number is required for check-in");
            }
            
            // Update room status to 'unavailable'
            $update_room_sql = "UPDATE room_numbers SET status = 'unavailable' WHERE room_number = ?";
            $update_room_stmt = $con->prepare($update_room_sql);
            if (!$update_room_stmt) {
                throw new Exception("Failed to prepare room status update: " . $con->error);
            }
            
            $update_room_stmt->bind_param("s", $room_number);
            if (!$update_room_stmt->execute()) {
                throw new Exception("Failed to update room status: " . $update_room_stmt->error);
            }
            
            $new_status = 'Checked in';
            break;
            
        case 'checkout':
            // For check-out, verify booking is checked in or extended
            $current_status = strtolower($booking['status']);
            error_log("Processing checkout - Current booking status: " . $current_status);
            
            if ($current_status !== 'checked in' && $current_status !== 'extended') {
                error_log("Invalid status for check-out: " . $booking['status']);
                throw new Exception("Booking must be in 'Checked In' or 'Extended' status to check out");
            }
            $new_status = 'Checked Out';
            
            // Get the room number from the booking
            $room_number = $booking['room_number'];
            error_log("Checkout - Room number from booking: " . ($room_number ?? 'null'));
            
            // Update room status to 'active'
            if (!empty($room_number)) {
                error_log("Attempting to update room {$room_number} status to active");
                
                // First check current room status
                $check_room_sql = "SELECT status FROM room_numbers WHERE room_number = ?";
                $check_room_stmt = $con->prepare($check_room_sql);
                $check_room_stmt->bind_param("s", $room_number);
                $check_room_stmt->execute();
                $room_result = $check_room_stmt->get_result();
                $room_data = $room_result->fetch_assoc();
                error_log("Current room status: " . ($room_data['status'] ?? 'not found'));
                
                // Update room status using case-insensitive comparison
                $update_room_sql = "UPDATE room_numbers SET status = 'active' WHERE LOWER(room_number) = LOWER(?)";
                $update_room_stmt = $con->prepare($update_room_sql);
                if (!$update_room_stmt) {
                    error_log("Failed to prepare room status update: " . $con->error);
                    throw new Exception("Failed to prepare room status update: " . $con->error);
                }
                
                $update_room_stmt->bind_param("s", $room_number);
                $result = $update_room_stmt->execute();
                if (!$result) {
                    error_log("Failed to update room status: " . $update_room_stmt->error);
                    throw new Exception("Failed to update room status: " . $update_room_stmt->error);
                }
                
                $affected_rows = $update_room_stmt->affected_rows;
                error_log("Room status update affected rows: " . $affected_rows);
                
                if ($affected_rows === 0) {
                    // Try to find the room to see if it exists
                    $find_room_sql = "SELECT room_number, status FROM room_numbers WHERE LOWER(room_number) = LOWER(?)";
                    $find_room_stmt = $con->prepare($find_room_sql);
                    $find_room_stmt->bind_param("s", $room_number);
                    $find_room_stmt->execute();
                    $find_result = $find_room_stmt->get_result();
                    
                    if ($find_result->num_rows === 0) {
                        error_log("Room not found in room_numbers table: {$room_number}");
                    } else {
                        $room_info = $find_result->fetch_assoc();
                        error_log("Room found but status not updated. Current status: " . $room_info['status']);
                    }
                } else {
                    error_log("Successfully updated room {$room_number} status to active");
                }
            } else {
                error_log("No room number found in booking record");
            }
            
            // Handle payment and total amount
            $payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($con, $_POST['payment_method']) : 'Cash';
            $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
            $remaining_balance = isset($_POST['remaining_balance']) ? floatval($_POST['remaining_balance']) : 0;
            
            // If total amount is not provided, calculate it from room price and nights
            if ($total_amount <= 0) {
                $room_price = floatval($booking['room_price']);
                $nights = intval($booking['nights']);
                $total_amount = $room_price * $nights;
            }
            
            // If this is a full payment, set amount_paid to total_amount
            if ($remaining_balance <= 0) {
                $amount_paid = $total_amount;
            }
            
            // Update the booking with payment details
            $payment_sql = "UPDATE bookings SET 
                          amount_paid = amount_paid + ?, 
                          payment_method = ?,
                          total_amount = ?,
                          remaining_balance = ?
                          WHERE booking_id = ?";
            
            $payment_stmt = $con->prepare($payment_sql);
            if (!$payment_stmt) {
                throw new Exception("Failed to prepare payment update: " . $con->error);
            }
            
            $payment_stmt->bind_param("dssds", $amount_paid, $payment_method, $total_amount, $remaining_balance, $booking_id);
            if (!$payment_stmt->execute()) {
                throw new Exception("Failed to update payment: " . $payment_stmt->error);
            }
            
            error_log("Updated payment for booking {$booking_id}: +{$amount_paid} of {$total_amount} via {$payment_method}, Remaining: {$remaining_balance}");
            
            break;
            
        case 'early_checkin':
            // For early check-in, verify booking is pending
            if ($booking['status'] !== 'Pending' && $booking['status'] !== 'pending') {
                error_log("Invalid status for early check-in: " . $booking['status']);
                throw new Exception("Booking must be in Pending status for early check-in");
            }
            // For early check-in, we'll redirect to the modal form
            echo json_encode([
                'success' => true,
                'message' => 'Ready for early check-in',
                'action' => 'show_form'
            ]);
            mysqli_commit($con);
            exit; // Exit early to prevent the standard update
            
        case 'early_checkout':
            // For early check-out, verify booking is checked in or extended
            if ($booking['status'] !== 'Checked in' && $booking['status'] !== 'checked_in' && $booking['status'] !== 'Extended') {
                error_log("Invalid status for early check-out: " . $booking['status']);
                throw new Exception("Booking must be in 'Checked in' or 'Extended' status for early check-out");
            }
            
            // Get the room number from the booking
            $room_number = $booking['room_number'];
            error_log("Early Checkout - Room number from booking: " . ($room_number ?? 'null'));
            
            // Update room status to 'active'
            if (!empty($room_number)) {
                error_log("Attempting to update room {$room_number} status to active");
                
                // First check current room status
                $check_room_sql = "SELECT status FROM room_numbers WHERE room_number = ?";
                $check_room_stmt = $con->prepare($check_room_sql);
                $check_room_stmt->bind_param("s", $room_number);
                $check_room_stmt->execute();
                $room_result = $check_room_stmt->get_result();
                $room_data = $room_result->fetch_assoc();
                error_log("Current room status: " . ($room_data['status'] ?? 'not found'));
                
                $update_room_sql = "UPDATE room_numbers SET status = 'active' WHERE LOWER(room_number) = LOWER(?)";
                $update_room_stmt = $con->prepare($update_room_sql);
                if (!$update_room_stmt) {
                    error_log("Failed to prepare room status update: " . $con->error);
                    throw new Exception("Failed to prepare room status update: " . $con->error);
                }
                
                $update_room_stmt->bind_param("s", $room_number);
                $result = $update_room_stmt->execute();
                if (!$result) {
                    error_log("Failed to update room status: " . $update_room_stmt->error);
                    throw new Exception("Failed to update room status: " . $update_room_stmt->error);
                }
                
                $affected_rows = $update_room_stmt->affected_rows;
                error_log("Room status update affected rows: " . $affected_rows);
                
                if ($affected_rows === 0) {
                    error_log("Warning: Room status update didn't affect any rows. Room might not exist: {$room_number}");
                } else {
                    error_log("Successfully updated room {$room_number} status to active");
                }
            } else {
                error_log("No room number found in booking record");
            }
            
            // Process early checkout
            $new_status = 'Checked Out';
            
            // Additional early checkout logic can be added here
            // For example, calculating prorated refunds, etc.
            
            break;
            
        default:
            throw new Exception("Invalid action specified: " . $action);
    }

    error_log("Setting new status to: " . $new_status);

    // Update booking status
    $update_sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $params = array($new_status, $booking_id);
    $types = "ss";
    
    // If room number is provided and we're checking in, add it to the update and mark room as occupied
    if ($room_number && ($action === 'confirm' || $action === 'early_checkin')) {
        $update_sql = "UPDATE bookings SET status = ?, room_number = ? WHERE booking_id = ?";
        $params = array($new_status, $room_number, $booking_id);
        $types = "sss";
        error_log("Adding room number to update: " . $room_number);
        
        // Update room status to Occupied
        $room_update_sql = "UPDATE rooms SET status = 'Occupied' WHERE id = ?";
        $room_update_stmt = $con->prepare($room_update_sql);
        if (!$room_update_stmt) {
            throw new Exception("Failed to prepare room update statement: " . $con->error);
        }
        
        $room_update_stmt->bind_param("i", $room_number);
        if (!$room_update_stmt->execute()) {
            throw new Exception("Failed to update room status: " . $room_update_stmt->error);
        }
        
        error_log("Updated room status to Occupied for room ID: " . $room_number);
    }
    
    // When checking out, set room status back to Available
    if ($action === 'checkout' || $action === 'early_checkout') {
        // Get the room number first
        $get_room_sql = "SELECT room_number FROM bookings WHERE booking_id = ?";
        $get_room_stmt = $con->prepare($get_room_sql);
        if (!$get_room_stmt) {
            error_log("Warning: Failed to prepare get room statement: " . $con->error);
        } else {
            $get_room_stmt->bind_param("s", $booking_id);
            if ($get_room_stmt->execute()) {
                $room_result = $get_room_stmt->get_result();
                if ($room_result->num_rows > 0) {
                    $room_data = $room_result->fetch_assoc();
                    $checkout_room_id = $room_data['room_number'];
                    
                    if ($checkout_room_id) {
                        // Update room status to Available in rooms table (if it exists)
                        $room_update_sql = "UPDATE rooms SET status = 'active' WHERE id = ?";
                        $room_update_stmt = $con->prepare($room_update_sql);
                        if ($room_update_stmt) {
                            $room_update_stmt->bind_param("i", $checkout_room_id);
                            $room_update_stmt->execute(); // Not critical if this fails
                            $room_update_stmt->close();
                        }
                        
                        // Update room status to active in room_numbers table
                        $room_number_update_sql = "UPDATE room_numbers SET status = 'active' WHERE room_number = ?";
                        $room_number_stmt = $con->prepare($room_number_update_sql);
                        if ($room_number_stmt) {
                            $room_number_stmt->bind_param("s", $checkout_room_id);
                            if (!$room_number_stmt->execute()) {
                                error_log("Warning: Failed to update room status in room_numbers table: " . $room_number_stmt->error);
                            } else {
                                error_log("Updated room status to active in room_numbers for room: " . $checkout_room_id);
                            }
                            $room_number_stmt->close();
                        }
                    }
                }
            }
        }
    }
    
    $update_stmt = $con->prepare($update_sql);
    if (!$update_stmt) {
        throw new Exception("Failed to prepare update statement: " . $con->error);
    }
    
    $update_stmt->bind_param($types, ...$params);
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update booking status: " . $update_stmt->error);
    }
    
    $affected_rows = $update_stmt->affected_rows;
    error_log("Update affected " . $affected_rows . " rows");

    // Create notification
    $message = "Booking #" . $booking_id . " has been " . strtolower($new_status);
    
    // Check if notifications table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'notifications'");
    $table_exists = mysqli_num_rows($table_check) > 0;
    
    if ($table_exists) {
        $notif_sql = "INSERT INTO notifications (reference_id, message, type, created_at) VALUES (?, ?, 'booking', NOW())";
        $notif_stmt = $con->prepare($notif_sql);
        if (!$notif_stmt) {
            error_log("Warning: Failed to prepare notification statement: " . $con->error);
            // Continue without notification - don't throw exception
        } else {
            $notif_stmt->bind_param("ss", $booking_id, $message);
            if (!$notif_stmt->execute()) {
                error_log("Warning: Failed to create notification: " . $notif_stmt->error);
                // Continue without notification - don't throw exception
            }
        }
    } else {
        error_log("Notifications table does not exist. Skipping notification creation.");
        // Continue without notification
    }

    // If we got here, everything succeeded
    mysqli_commit($con);
    
    // Build redirect URL with absolute path to ensure compatibility
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/Admin/";
    $redirect_url = $base_url . ($new_status === 'Checked Out' ? 'checked_out.php' : ($new_status === 'Checked in' ? 'checked_in.php' : 'booking_status.php'));
    
    error_log("Redirecting to: " . $redirect_url);
    
    // Return success response with redirect
    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully',
        'redirect' => $redirect_url
    ]);

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
