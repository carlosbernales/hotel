<?php
require_once 'db.php';
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Log the incoming POST data for debugging
    error_log("Early check-in POST data: " . print_r($_POST, true));
    
    if (!isset($_POST['booking_id'])) {
        error_log("Missing booking ID in early check-in request");
        throw new Exception('Booking ID is required');
    }

    if (!isset($_POST['room_number']) || empty($_POST['room_number'])) {
        throw new Exception('Room number is required');
    }

    $booking_id = $_POST['booking_id'];
    $room_number = $_POST['room_number'];
    error_log("Processing early check-in for booking ID: $booking_id with room number: $room_number");
    
    // Get additional booking identifiers to ensure correct record
    $guest_name = isset($_POST['guest_name']) ? $_POST['guest_name'] : '';
    $check_in = isset($_POST['check_in']) ? $_POST['check_in'] : '';
    $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : '';
    
    if (!isset($_POST['payment_method']) || empty($_POST['payment_method'])) {
        throw new Exception('Payment method is required');
    }
    
    if (!isset($_POST['payment_option']) || empty($_POST['payment_option'])) {
        throw new Exception('Payment option is required');
    }
    
    if (!isset($_POST['new_total']) || empty($_POST['new_total'])) {
        throw new Exception('New total amount is required');
    }
    
    $payment_method = $_POST['payment_method'];
    $payment_option = $_POST['payment_option'];
    $new_total = floatval(str_replace(['₱', ','], '', $_POST['new_total']));
    $current_date = date('Y-m-d'); // Get current date for early check-in

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // First update the room status to occupied
        $update_room_sql = "UPDATE room_numbers SET status = 'occupied' WHERE room_number = ?";
        $room_stmt = $con->prepare($update_room_sql);
        if (!$room_stmt) {
            throw new Exception("Database error preparing room update: " . $con->error);
        }
        $room_stmt->bind_param("s", $room_number);
        if (!$room_stmt->execute()) {
            throw new Exception("Failed to update room status: " . $room_stmt->error);
        }

        // Build a query that uses multiple fields to identify the booking
        $query_params = [];
        $query = "SELECT * FROM bookings WHERE 1=1";
        
        if ($booking_id != '0') {
            $query .= " AND booking_id = ?";
            $query_params[] = $booking_id;
        } else {
            // If booking_id is 0, we need alternative identifiers
            if (!empty($guest_name)) {
                $guest_parts = explode(' ', $guest_name);
                if (count($guest_parts) > 1) {
                    $first_name = $guest_parts[0];
                    $last_name = end($guest_parts);
                    $query .= " AND first_name = ? AND last_name = ?";
                    $query_params[] = $first_name;
                    $query_params[] = $last_name;
                }
            }
            if (!empty($check_in)) {
                $query .= " AND check_in = ?";
                $query_params[] = $check_in;
            }
            if (!empty($check_out)) {
                $query .= " AND check_out = ?";
                $query_params[] = $check_out;
            }
        }
        
        $query .= " AND status IN ('pending', 'accepted') LIMIT 1";
        
        error_log("Booking search query: $query with params: " . print_r($query_params, true));
        
        // Prepare and execute the query with parameters
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error preparing statement: " . $con->error);
        }
        
        if (!empty($query_params)) {
            $types = str_repeat('s', count($query_params));
            $stmt->bind_param($types, ...$query_params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Booking not found. Please check the details and try again.");
        }
        
        $booking = $result->fetch_assoc();
        error_log("Found booking: " . print_r($booking, true));
        
        // Now we have the correct booking record, proceed with the update
        $booking_id = $booking['booking_id']; // Use the fetched booking_id

        // Calculate amount paid based on payment option
        $amount_to_pay = 0;
        if ($payment_option === 'full') {
            $amount_to_pay = $new_total;
        } else { // downpayment
            $amount_to_pay = $new_total * 0.5; // 50% of new total
        }

        // Use prepared statement for the update
        $update_sql = "UPDATE bookings 
                SET status = 'Checked In',
                    check_in = ?,
                    total_amount = ?,
                    payment_method = ?,
                    payment_option = ?,
                    downpayment_amount = ?,
                    room_number = ?
                WHERE booking_id = ?";
                
        $update_stmt = $con->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Database error preparing update: " . $con->error);
        }
        
        $downpayment = ($payment_option === 'downpayment') ? $amount_to_pay : NULL;
        $update_stmt->bind_param("sdssssi", $current_date, $new_total, $payment_method, $payment_option, $downpayment, $room_number, $booking_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update booking: " . $update_stmt->error);
        }
        
        $affected_rows = $update_stmt->affected_rows;
        error_log("Update affected $affected_rows rows");
        
        if ($affected_rows === 0) {
            throw new Exception("No booking was updated. The booking may have already been processed.");
        }

        // If everything is successful, commit the transaction
        mysqli_commit($con);
        
        error_log("Successfully updated booking ID $booking_id for early check-in");
        
        echo json_encode([
            'success' => true,
            'message' => 'Early check-in processed successfully',
            'redirect' => 'checked_in.php'
        ]);

    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        mysqli_rollback($con);
        throw $e;
    }

} catch (Exception $e) {
    error_log("Early check-in error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 