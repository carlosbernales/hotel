<?php
require_once 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the raw input for debugging
$input = file_get_contents('php://input');
error_log('Raw input: ' . $input);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['success' => false, 'message' => 'Invalid request method'];
    error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode($response);
    exit;
}

try {
    $data = json_decode($input, true);
    
    // Log the decoded data
    error_log('Decoded data: ' . print_r($data, true));
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    if (!isset($data['booking_id']) || !isset($data['amenity_id'])) {
        throw new Exception('Missing required parameters. Booking ID: ' . ($data['booking_id'] ?? 'not set') . ', Amenity ID: ' . ($data['amenity_id'] ?? 'not set'));
    }
    
    $booking_id = (int)$data['booking_id'];
    $amenity_id = (int)$data['amenity_id'];
    
    // Get booking details to calculate nights
    $booking_query = "SELECT check_in, check_out, total_amount, remaining_balance, extra_charges 
                     FROM bookings WHERE booking_id = ?";
    error_log('Booking query: ' . $booking_query . ' with ID: ' . $booking_id);
    
    $booking_stmt = mysqli_prepare($con, $booking_query);
    if ($booking_stmt === false) {
        throw new Exception('Failed to prepare booking query: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($booking_stmt, 'i', $booking_id);
    if (!mysqli_stmt_execute($booking_stmt)) {
        throw new Exception('Failed to execute booking query: ' . mysqli_stmt_error($booking_stmt));
    }
    
    $booking_result = mysqli_stmt_get_result($booking_stmt);
    if ($booking_result === false) {
        throw new Exception('Failed to get booking result: ' . mysqli_error($con));
    }
    
    $booking = mysqli_fetch_assoc($booking_result);
    
    if (!$booking) {
        throw new Exception('Booking not found');
    }
    
    // Calculate number of nights
    $check_in = new DateTime($booking['check_in']);
    $check_out = new DateTime($booking['check_out']);
    $nights = $check_in->diff($check_out)->days;
    
    // Debug: Check all amenities in the database
    $debug_query = "SELECT * FROM amenities";
    $debug_result = mysqli_query($con, $debug_query);
    $all_amenities = [];
    while ($row = mysqli_fetch_assoc($debug_result)) {
        $all_amenities[] = $row;
    }
    error_log('All amenities in database: ' . print_r($all_amenities, true));
    
    // Get amenity details
    $amenity_query = "SELECT name, price FROM amenities WHERE amenity_id = ?";
    error_log('Amenity query: ' . $amenity_query . ' with ID: ' . $amenity_id);
    
    $amenity_stmt = mysqli_prepare($con, $amenity_query);
    if ($amenity_stmt === false) {
        throw new Exception('Failed to prepare amenity query: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($amenity_stmt, 'i', $amenity_id);
    if (!mysqli_stmt_execute($amenity_stmt)) {
        throw new Exception('Failed to execute amenity query: ' . mysqli_stmt_error($amenity_stmt));
    }
    
    $amenity_result = mysqli_stmt_get_result($amenity_stmt);
    if ($amenity_result === false) {
        throw new Exception('Failed to get amenity result: ' . mysqli_error($con));
    }
    
    $amenity = mysqli_fetch_assoc($amenity_result);
    error_log('Amenity details: ' . print_r($amenity, true));
    
    if (!$amenity) {
        // Log all available amenities for debugging
        $all_amenities_query = "SELECT * FROM amenities";
        $all_amenities_result = mysqli_query($con, $all_amenities_query);
        $all_amenities = [];
        while ($row = mysqli_fetch_assoc($all_amenities_result)) {
            $all_amenities[] = $row;
        }
        error_log('All available amenities: ' . print_r($all_amenities, true));
        
        throw new Exception('Amenity not found. Amenity ID: ' . $amenity_id . ', Available amenities: ' . json_encode($all_amenities));
    }
    
    // Calculate amenity cost
    $amenity_cost = 0;
    if (isset($amenity['price']) && is_numeric($amenity['price'])) {
        // Use price from amenities table if available
        $amenity_cost = (float)$amenity['price'] * $nights;
    } else if (strtolower($amenity['name']) === 'bed') {
        // Fallback for bed (1000 per night)
        $amenity_cost = 1000 * $nights;
    }
    
    error_log("Amenity cost calculation: {$amenity_cost} = {$amenity['price']} * {$nights} nights");
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // Update the booking with the new extra charges and remaining balance
        $new_extra_charges = (float)$booking['extra_charges'] + (float)$amenity_cost;
        $new_remaining_balance = (float)$booking['remaining_balance'] + (float)$amenity_cost;
        
        $update_query = "UPDATE bookings 
                        SET extra_charges = ?, 
                            remaining_balance = ? 
                        WHERE booking_id = ?";
        error_log('Update query: ' . $update_query);
        error_log("Values: extra_charges={$new_extra_charges}, remaining_balance={$new_remaining_balance}, booking_id={$booking_id}");
        
        $update_stmt = mysqli_prepare($con, $update_query);
        if ($update_stmt === false) {
            throw new Exception('Failed to prepare update query: ' . mysqli_error($con));
        }
        
        $bind_result = mysqli_stmt_bind_param($update_stmt, 'ddi', $new_extra_charges, $new_remaining_balance, $booking_id);
        if ($bind_result === false) {
            throw new Exception('Failed to bind parameters: ' . mysqli_stmt_error($update_stmt));
        }
        
        $execute_result = mysqli_stmt_execute($update_stmt);
        if ($execute_result === false) {
            throw new Exception('Failed to execute update query: ' . mysqli_stmt_error($update_stmt));
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        $response = [
            'success' => true,
            'message' => 'Amenity added successfully',
            'extra_charges' => $new_extra_charges,
            'remaining_balance' => $new_remaining_balance
        ];
        
        error_log('Success response: ' . print_r($response, true));
        echo json_encode($response);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
    
} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($con) && mysqli_connect_errno() === 0) {
        mysqli_rollback($con);
    }
    
    $error_message = $e->getMessage();
    error_log('Error in add_amenities_to_booking.php: ' . $error_message);
    
    $response = [
        'success' => false,
        'message' => 'Failed to add amenity: ' . $error_message,
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ];
    
    error_log('Error response: ' . print_r($response, true));
    echo json_encode($response);
}
?>
