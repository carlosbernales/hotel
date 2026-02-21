<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load security helper
require_once 'includes/security_helper.php';

// For home page, we allow access but will show different content based on login status
// If user is logged in but not a customer, redirect them to their proper area
if (isLoggedIn() && getUserRole() !== 'customer') {
    $userRole = getUserRole();
    switch($userRole) {
        case 'admin':
            header('Location: /Admin/index.php?dashboard');
            break;
        case 'cashier':
            header('Location: /Admin/Cashier/index.php?pos');
            break;
        case 'frontdesk':
            header('Location: /Admin/Frontdesk/index.php?dashboard');
            break;
        default:
            header('Location: /login.php');
    }
    exit();
}

require_once 'db_con.php';

// Initialize rooms array
$rooms = [];

// Check if we have room availability in session
if (isset($_SESSION['room_availability']['rooms'])) {
    // Use rooms from session if available
    $rooms = $_SESSION['room_availability']['rooms'];
} else {
    // Otherwise fetch all active room types
    $stmt = $pdo->query("
        SELECT 
            room_type_id,
            room_type,
            price,
            beds,
            capacity,
            description,
            image,
            discount_percent,
            discount_valid_until
        FROM room_types 
        WHERE status = 'active'
        ORDER BY room_type
    ");
    
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="roomss.css">
</head>
<body>
<?php require_once 'nav.php'; ?>
<div class="container mt-4">
    <?php require_once 'room_check_availability.php'; ?>
    <?php require_once 'room_cards.php'; ?>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Booking List JS -->
    <?php include 'room_booking_list.php'; ?>

    <script>
        function bookRoom(roomTypeId, roomType) {
            // You can add your booking logic here
            alert('Booking ' + roomType);
            // Example: window.location.href = 'booking.php?room_type_id=' + roomTypeId;
        }
        
        // Make showBookingList available globally
        window.showBookingList = showBookingList;
    </script>
</body>
</html>