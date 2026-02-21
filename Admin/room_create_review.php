<?php
// Start session
session_start();

// Include database connection
require_once 'db_con.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'redirect' => ''
];

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate input
    $bookingRef = $_POST['booking_ref'] ?? '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $message = trim($_POST['message'] ?? '');
    $guestId = $_SESSION['user_id'] ?? 0; // Assuming user is logged in and ID is stored in session

    // Basic validation
    if (empty($bookingRef)) {
        throw new Exception('Booking reference is required');
    }

    if ($rating < 1 || $rating > 5) {
        throw new Exception('Please provide a valid rating between 1 and 5');
    }

    // Get booking and room type details
    $stmt = $pdo->prepare("
        SELECT b.booking_id, br.room_type_id 
        FROM bookings b
        JOIN booked_rooms br ON b.booking_id = br.booking_id
        WHERE b.booking_reference = ?
        LIMIT 1
    ");
    $stmt->execute([$bookingRef]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Invalid booking reference');
    }

    // Check if review already exists for this specific booking by this user
    $stmt = $pdo->prepare("SELECT review_id FROM room_reviews WHERE room_type_id = ? AND user_id = ?");
    $stmt->execute([$booking['booking_id'], $guestId]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('You have already submitted a review for this specific booking');
    }

    // Insert review with booking reference
    $stmt = $pdo->prepare("
        INSERT INTO room_reviews 
        (room_type_id, user_id, rating, review, created_at) 
        VALUES ( ?, ?, ?, ?, NOW())
    ");

    $success = $stmt->execute([
        $booking['room_type_id'],
        $guestId,
        $rating,
        $message
    ]);

    if ($success) {
        $response['success'] = true;
        $response['message'] = 'Thank you for your review!';
        $response['redirect'] = 'roomss.php?review=success';
    } else {
        throw new Exception('Failed to save review. Please try again.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400); // Bad request
}

// Return JSON response
echo json_encode($response);
?>
