<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID'
    ]);
    exit;
}

$booking_id = (int)$_GET['booking_id'];

try {
    // Get booking dates to calculate nights for bed amenities
    $booking_query = "SELECT check_in, check_out FROM bookings WHERE booking_id = ?";
    $booking_stmt = mysqli_prepare($con, $booking_query);
    mysqli_stmt_bind_param($booking_stmt, 'i', $booking_id);
    mysqli_stmt_execute($booking_stmt);
    $booking_result = mysqli_stmt_get_result($booking_stmt);
    $booking = mysqli_fetch_assoc($booking_result);
    
    if (!$booking) {
        throw new Exception('Booking not found');
    }
    
    // Calculate number of nights
    $check_in = new DateTime($booking['check_in']);
    $check_out = new DateTime($booking['check_out']);
    $nights = $check_in->diff($check_out)->days;
    
    // Get all amenities for this booking
    $query = "SELECT ba.*, a.name, a.description 
              FROM booking_amenities ba 
              JOIN amenities a ON ba.amenity_id = a.id 
              WHERE ba.booking_id = ?";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $booking_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to fetch amenities: ' . mysqli_error($con));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $amenities = [];
    $total_amount = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        // For bed amenities, calculate total price based on nights
        if (stripos($row['name'], 'bed') !== false) {
            $row['total_price'] = 1000 * $nights; // 1000 PHP per night
        }
        
        $amenities[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'quantity' => $row['quantity'],
            'price_per_unit' => $row['price_per_unit'],
            'total_price' => $row['total_price']
        ];
        
        $total_amount += $row['total_price'];
    }
    
    echo json_encode([
        'success' => true,
        'amenities' => $amenities,
        'total_amount' => $total_amount,
        'nights' => $nights
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 