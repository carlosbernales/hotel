<?php
require_once 'includes/init.php';

if (isset($_GET['id'])) {
    $room_type_id = (int)$_GET['id'];
    
    // Get basic room type information
    $sql = "SELECT * FROM room_types WHERE room_type_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $room_type_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $roomType = [
                'room_type_id' => $row['room_type_id'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'capacity' => $row['capacity'],
                'beds' => $row['beds'],
                'description' => $row['description'],
                'rating' => $row['rating'],
                'image' => $row['image'],
                'amenities' => []
            ];
            
            // Get amenities for this room type
            $amenityQuery = "SELECT a.amenity_id, a.name, a.icon 
                            FROM amenities a
                            INNER JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id
                            WHERE rta.room_type_id = ?";
            $amenityStmt = $con->prepare($amenityQuery);
            $amenityStmt->bind_param("i", $room_type_id);
            
            if ($amenityStmt->execute()) {
                $amenityResult = $amenityStmt->get_result();
                while ($amenity = $amenityResult->fetch_assoc()) {
                    $roomType['amenities'][] = [
                        'amenity_id' => $amenity['amenity_id'],
                        'name' => $amenity['name'],
                        'icon' => $amenity['icon']
                    ];
                }
            }
            $amenityStmt->close();
            
            echo json_encode($roomType);
        } else {
            echo json_encode(['error' => 'Room type not found']);
        }
    } else {
        echo json_encode(['error' => 'Database error']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'No room type ID provided']);
}

function getAdminSales($startDate, $endDate) {
    global $con;
    
    // Get daily revenue data for the trend chart
    $trend_sql = "SELECT 
        DATE(check_out) as date,
        SUM(total_amount) as daily_revenue
    FROM bookings 
    WHERE status = 'Checked Out'
    AND DATE(check_out) BETWEEN ? AND ?
    GROUP BY DATE(check_out)
    ORDER BY date ASC";
    
    $stmt = mysqli_prepare($con, $trend_sql);
    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $trend_result = mysqli_stmt_get_result($stmt);
    
    // Store daily revenue data
    $trend_data = array();
    while ($row = mysqli_fetch_assoc($trend_result)) {
        $trend_data[] = array(
            'date' => date('M d', strtotime($row['date'])),
            'revenue' => floatval($row['daily_revenue'])
        );
    }
    
    // If no daily data exists, create data points for each day in range
    if (empty($trend_data)) {
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        while ($current <= $end) {
            $trend_data[] = array(
                'date' => $current->format('M d'),
                'revenue' => 0
            );
            $current->modify('+1 day');
        }
    }
    
    // Get the main summary data as before
    $summary_sql = "SELECT 
        'Room Revenue' as category,
        COALESCE(SUM(total_amount), 0) as revenue,
        COUNT(DISTINCT booking_id) as completed_bookings,
        SUM(number_of_guests) as total_guests
    FROM bookings 
    WHERE status = 'Checked Out'
    AND DATE(check_out) BETWEEN ? AND ?";
    
    $stmt = mysqli_prepare($con, $summary_sql);
    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $currentRoomResult = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    // Add trend data to the result
    $currentRoomResult['trend_data'] = $trend_data;
    
    // Create result array and return
    $results = array($currentRoomResult);
    return createMySQLiResult($results);
}
