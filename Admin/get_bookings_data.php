<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get current date
$current_date = date('Y-m-d');

try {
    // Get summary data
    $summary_query = "SELECT 
        (SELECT COUNT(*) FROM bookings WHERE DATE(created_at) = ?) as today_bookings,
        (SELECT COUNT(*) FROM bookings WHERE status = 'Pending') as pending_bookings,
        (SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status NOT IN ('Cancelled', 'Rejected')) as total_revenue";
    
    $stmt = mysqli_prepare($con, $summary_query);
    mysqli_stmt_bind_param($stmt, "s", $current_date);
    mysqli_stmt_execute($stmt);
    $summary_result = mysqli_stmt_get_result($stmt);
    $summary = mysqli_fetch_assoc($summary_result);

    // Get recent bookings
    $bookings_query = "SELECT 
        b.booking_id,
        CONCAT(c.first_name, ' ', c.last_name) as guest_name,
        b.check_in,
        b.check_out,
        b.room_type,
        b.status,
        b.total_amount as amount
        FROM bookings b
        LEFT JOIN customers c ON b.customer_id = c.customer_id
        ORDER BY b.created_at DESC
        LIMIT 10";
    
    $bookings_result = mysqli_query($con, $bookings_query);
    $bookings = [];
    
    while ($row = mysqli_fetch_assoc($bookings_result)) {
        // Format dates
        $row['check_in'] = date('M d, Y', strtotime($row['check_in']));
        $row['check_out'] = date('M d, Y', strtotime($row['check_out']));
        $bookings[] = $row;
    }

    // Prepare response
    $response = [
        'summary' => [
            'today' => (int)$summary['today_bookings'],
            'pending' => (int)$summary['pending_bookings'],
            'revenue' => (float)$summary['total_revenue']
        ],
        'bookings' => $bookings
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?> 