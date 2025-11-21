<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = "Package ID not provided.";
    header('Location: event_management.php');
    exit();
}

$package_id = $_GET['id'];

// Get current status and check for active bookings
$query = "SELECT ep.status, 
    (SELECT COUNT(*) FROM event_bookings eb 
     WHERE eb.package_id = ep.id 
     AND eb.status != 'cancelled' 
     AND eb.status != 'finished') as has_bookings 
FROM event_packages ep 
WHERE ep.id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Determine the new status based on bookings and current status
    if ($row['has_bookings'] > 0) {
        $new_status = 'Occupied';
    } else {
        $new_status = ($row['status'] == 'Available') ? 'Occupied' : 'Available';
    }
    
    // Update the status
    $update_query = "UPDATE event_packages SET status = ? WHERE id = ?";
    $update_stmt = $con->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $package_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Package status has been updated to " . $new_status;
    } else {
        $_SESSION['error_message'] = "Failed to update package status.";
    }
} else {
    $_SESSION['error_message'] = "Package not found.";
}

// Redirect back to event management page
header('Location: event_management.php');
exit(); 