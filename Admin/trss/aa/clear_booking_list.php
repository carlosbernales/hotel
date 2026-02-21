<?php
session_start();
$_SESSION['booking_list'] = [];

echo json_encode([
    'success' => true,
    'message' => 'Booking list cleared successfully'
]);
?> 