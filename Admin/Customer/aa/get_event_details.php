<?php
session_start();
require 'db_con.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            eb.*,
            CONCAT(u.firstname, ' ', u.lastname) as contact_person,
            u.email,
            u.contact_number as user_contact
        FROM event_bookings eb
        LEFT JOIN users u ON eb.user_id = u.id
        WHERE eb.id = :id AND eb.user_id = :user_id
    ");
    
    $stmt->execute([
        ':id' => $_GET['id'],
        ':user_id' => $_SESSION['user_id']
    ]);
    
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        // Format the dates and times
        $booking['formatted_reservation_date'] = date('F d, Y', strtotime($booking['reservation_date']));
        $booking['formatted_start_time'] = date('g:i A', strtotime($booking['start_time']));
        $booking['formatted_end_time'] = date('g:i A', strtotime($booking['end_time']));
        $booking['formatted_created_at'] = date('F d, Y g:i A', strtotime($booking['created_at']));
        
        // Calculate payment status
        $booking['payment_status'] = $booking['paid_amount'] >= $booking['total_amount'] ? 'Fully Paid' : 'Partially Paid';
        $booking['remaining_balance'] = max(0, $booking['total_amount'] - $booking['paid_amount']);
        
        echo json_encode([
            'success' => true,
            'booking' => $booking
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 