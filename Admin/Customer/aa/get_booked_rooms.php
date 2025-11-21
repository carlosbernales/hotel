<?php
require_once 'db_con.php';
header('Content-Type: application/json');

try {
    // Get room type ID from request if provided
    $room_type_id = isset($_GET['room_type_id']) ? (int)$_GET['room_type_id'] : null;
    
    // Query to get all room types if no specific room type is selected
    $room_types_query = "SELECT id FROM room_types WHERE status = 1";
    $room_types_params = [];
    
    if ($room_type_id) {
        $room_types_query .= " AND id = :room_type_id";
        $room_types_params[':room_type_id'] = $room_type_id;
    }
    
    $stmt = $pdo->prepare($room_types_query);
    $stmt->execute($room_types_params);
    $room_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($room_types)) {
        throw new Exception('No active room types found');
    }
    
    // Get date range (next 365 days)
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+365 days'));
    
    // Initialize array to store fully booked dates
    $fully_booked_dates = [];
    
    // For each room type, check availability for each date in the range
    foreach ($room_types as $rt_id) {
        // Get total rooms of this type
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_rooms 
            FROM room_numbers
            WHERE room_type_id = ? AND status = 'active'
        ");
        $stmt->execute([$rt_id]);
        $total_rooms = (int)$stmt->fetchColumn();
        
        if ($total_rooms === 0) continue;
        
        // Get all bookings for this room type within the date range
        $stmt = $pdo->prepare("
            SELECT 
                DATE(check_in) as check_in_date,
                DATE(check_out) as check_out_date
            FROM bookings b
            WHERE b.room_type_id = ?
            AND b.booking_status IN ('confirmed', 'pending', 'checked_in')
            AND (
                (b.check_in <= ? AND b.check_out >= ?) OR
                (b.check_in <= ? AND b.check_out >= ?) OR
                (b.check_in >= ? AND b.check_out <= ?)
            )
        ");
        
        $stmt->execute([
            $rt_id,
            $end_date, $start_date,  // First condition
            $start_date, $end_date,   // Second condition
            $start_date, $end_date    // Third condition
        ]);
        
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // For each date in the range, check if it's fully booked
        $current_date = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        while ($current_date <= $end) {
            $date_str = $current_date->format('Y-m-d');
            $booked_rooms = 0;
            
            // Count how many rooms are booked for this date
            foreach ($bookings as $booking) {
                $check_in = new DateTime($booking['check_in_date']);
                $check_out = new DateTime($booking['check_out_date']);
                
                if ($current_date >= $check_in && $current_date < $check_out) {
                    $booked_rooms++;
                }
            }
            
            // If all rooms are booked, add to fully booked dates
            if ($booked_rooms >= $total_rooms) {
                $fully_booked_dates[] = $date_str;
            }
            
            $current_date->modify('+1 day');
        }
    }
    
    // Remove duplicates and sort
    $fully_booked_dates = array_unique($fully_booked_dates);
    sort($fully_booked_dates);
    
    echo json_encode([
        'success' => true,
        'booked_dates' => $fully_booked_dates
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>