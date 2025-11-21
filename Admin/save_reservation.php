<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not authorized']));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid data received']));
}

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Insert reservation
    $stmt = $con->prepare("INSERT INTO table_reservations (customer_name, contact_number, guest_count, table_type, reservation_datetime, status) VALUES (?, ?, ?, ?, ?, 'confirmed')");
    
    $reservation_datetime = $data['reservationDate'] . ' ' . $data['reservationTime'];
    
    $stmt->bind_param("ssiss", 
        $data['customerName'],
        $data['contactNumber'],
        $data['guestCount'],
        $data['tableType'],
        $reservation_datetime
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error saving reservation: " . $stmt->error);
    }
    
    $reservation_id = $stmt->insert_id;
    $stmt->close();

    // Insert orders
    $stmt = $con->prepare("INSERT INTO reservation_orders (reservation_id, menu_item_id, quantity, notes) VALUES (?, ?, ?, ?)");
    
    $debug_info = [
        'orders_saved' => [],
        'errors' => []
    ];

    foreach ($data['orders'] as $order) {
        $stmt->bind_param("iiis",
            $reservation_id,
            $order['menuItemId'],
            $order['quantity'],
            $order['notes']
        );
        
        if (!$stmt->execute()) {
            $debug_info['errors'][] = [
                'error' => $stmt->error,
                'order' => $order
            ];
            throw new Exception("Error saving order: " . $stmt->error);
        }

        $debug_info['orders_saved'][] = [
            'order_id' => $stmt->insert_id,
            'reservation_id' => $reservation_id,
            'menu_item_id' => $order['menuItemId'],
            'quantity' => $order['quantity'],
            'notes' => $order['notes']
        ];
    }
    
    $stmt->close();
    
    // Commit transaction
    mysqli_commit($con);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Reservation confirmed successfully',
        'reservation_id' => $reservation_id,
        'debug_info' => $debug_info
    ]);

} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage(),
        'debug_info' => isset($debug_info) ? $debug_info : null
    ]);
} 