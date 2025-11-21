<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $currentDate = date('Y-m-d');
    
    // First get total number of rooms
    $totalRoomsQuery = "SELECT COUNT(DISTINCT room_id) as total FROM room WHERE deleteStatus = 0";
    $totalResult = $con->query($totalRoomsQuery);
    $totalRooms = 0;
    
    if ($totalResult) {
        $row = $totalResult->fetch_assoc();
        $totalRooms = $row['total'];
    }
    
    // Then get number of occupied rooms
    $occupiedQuery = "SELECT COUNT(DISTINCT room_id) as count 
                     FROM booking 
                     WHERE DATE(check_in) <= '$currentDate' 
                     AND DATE(check_out) >= '$currentDate'";
    $occupiedResult = $con->query($occupiedQuery);
    $occupiedRooms = 0;
    
    if ($occupiedResult) {
        $row = $occupiedResult->fetch_assoc();
        $occupiedRooms = $row['count'];
    }
    
    // Available rooms is total minus occupied
    echo ($totalRooms - $occupiedRooms);
?>