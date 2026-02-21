<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    require 'db_con.php';

    if (!isset($_POST['datetime']) || trim($_POST['datetime']) === '') {
        sendJsonResponse([
            'success' => false,
            'message' => 'No datetime received',
            'data' => []
        ], 400);
    }

    $datetime = trim($_POST['datetime']);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $datetime)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Invalid datetime format',
            'data' => []
        ], 400);
    }

    // Convert to MySQL format
    $selected = str_replace('T', ' ', $datetime) . ':00';

    // Max booking duration
    $bookingHours = 4;

    /**
     * COUNT AVAILABLE TABLES PER TABLE TYPE
     */
    $sql = "
        SELECT
            tt.id AS table_type_id,
            tt.table_name,
            tt.capacity,
            COUNT(DISTINCT tn.id) AS total_tables,
            COUNT(DISTINCT ott.table_number_fk_id) AS booked_tables,
            (COUNT(DISTINCT tn.id) - COUNT(DISTINCT ott.table_number_fk_id)) AS available_tables
        FROM table_types tt
        LEFT JOIN table_number tn ON tn.table_type_fk_id = tt.id
        LEFT JOIN (
            SELECT ott.table_number_fk_id
            FROM orders_table ot
            JOIN orders_table_type ott ON ott.table_booking_fk_id = ot.id
            WHERE ? >= ot.date_time 
            AND ? < DATE_ADD(ot.date_time, INTERVAL ? HOUR)
        ) AS ott ON ott.table_number_fk_id = tn.id
        GROUP BY tt.id, tt.table_name, tt.capacity
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$selected, $selected, $bookingHours]);

    $available = [];
    $totalBookings = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $available[] = [
            'table_type_id' => $row['table_type_id'],
            'table_name' => $row['table_name'],
            'available_tables' => (int)$row['available_tables']
        ];
        $totalBookings += $row['booked_tables'];
    }

    // Get the total number of bookings for the selected time range
    $countSql = "
        SELECT COUNT(*) as total_bookings
        FROM orders_table ot
        WHERE ot.status = 'confirmed'
        AND ? >= ot.date_time 
        AND ? < DATE_ADD(ot.date_time, INTERVAL ? HOUR)
    ";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$selected, $selected, $bookingHours]);
    $bookingCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total_bookings'];

    sendJsonResponse([
        'success' => true,
        'selected_datetime' => $selected,
        'booking_duration_hours' => $bookingHours,
        'total_bookings' => (int)$bookingCount,
        'data' => $available
    ]);

} catch (Exception $e) {
    error_log('Availability Error: ' . $e->getMessage());
    sendJsonResponse([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage(),
        'data' => []
    ], 500);
}

if (isset($pdo)) {
    $pdo = null;
}
