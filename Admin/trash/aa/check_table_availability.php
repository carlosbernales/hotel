<?php
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $reservationDate = $_POST['reservationDate'] ?? '';
    $reservationTime = $_POST['reservationTime'] ?? '';

    // Validate input
    if (empty($reservationDate) || empty($reservationTime)) {
        throw new Exception('Please provide both date and time for the reservation');
    }

    // Combine date and time into a single datetime string
    $reservationDateTime = $reservationDate . ' ' . $reservationTime;

    // Get all table types with available table counts
    $query = "SELECT 
                tt.id,
                tt.table_name as name,
                tt.capacity,
                tt.description,
                tt.img1 as image,
                (SELECT COUNT(*) 
                 FROM table_number tn 
                 WHERE tn.table_type_fk_id = tt.id 
                 AND tn.status = 'available') as total_tables,
                (SELECT COUNT(DISTINCT tn.id)
                 FROM table_number tn
                 WHERE tn.table_type_fk_id = tt.id
                 AND tn.status = 'available'
                 AND NOT EXISTS (
                     SELECT 1 
                     FROM orders_table ot
                     INNER JOIN orders_table_type ott ON ot.id = ott.table_booking_fk_id
                     WHERE ott.table_number_fk_id = tn.id
                     AND DATE(ot.date_time) = :reservationDate
                     AND ot.order_type = 'Advance Order'
                     AND ot.status IN ('confirmed', 'pending')
                 )) as available_tables
              FROM table_types tt
              ORDER BY tt.capacity ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':reservationDate' => $reservationDate
    ]);
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process the results
    $response = [];
    foreach ($tables as $table) {
        // Format image path
        $image = $table['image'] ?? '';
        if (!empty($image)) {
            // Add '../../' to go up to the root directory from Customer/aa
            $image = '../../' . $image;
        } else {
            // Default image if none is provided
            $image = '../../images/default-table.jpg';
        }

        $response[] = [
            'id' => $table['id'],
            'name' => htmlspecialchars($table['name']),
            'capacity' => $table['capacity'],
            'description' => htmlspecialchars($table['description'] ?? ''),
            'image' => $image,
            'available_tables' => (int)$table['available_tables'],
            'total_tables' => (int)$table['total_tables']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $response
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
