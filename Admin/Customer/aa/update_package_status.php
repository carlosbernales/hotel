<?php
require_once 'db_con.php';

header('Content-Type: application/json');

try {
    // Update packages where bookings have ended
    $sql = "UPDATE event_packages ep 
            SET status = 'Available' 
            WHERE EXISTS (
                SELECT 1 
                FROM event_bookings eb 
                WHERE eb.package_name = ep.name 
                AND eb.reservation_date <= CURRENT_DATE 
                AND (
                    (eb.reservation_date < CURRENT_DATE) OR 
                    (eb.reservation_date = CURRENT_DATE AND eb.end_time < CURRENT_TIME)
                )
            ) 
            AND ep.status = 'Occupied'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Get list of updated packages
    $updatedSQL = "SELECT name FROM event_packages WHERE status = 'Available'";
    $updatedStmt = $pdo->prepare($updatedSQL);
    $updatedStmt->execute();
    $updated_packages = $updatedStmt->fetchAll(PDO::FETCH_COLUMN);

    // Update bookings that have ended to 'finished' status
    $updateFinishedSQL = "UPDATE event_bookings 
                         SET booking_status = 'finished'
                         WHERE booking_status IN ('pending', 'confirmed')
                         AND (
                             reservation_date < CURRENT_DATE 
                             OR (reservation_date = CURRENT_DATE AND end_time <= NOW())
                         )";
    $pdo->prepare($updateFinishedSQL)->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Package statuses updated successfully',
        'updated_packages' => $updated_packages,
        'is_available' => true
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 