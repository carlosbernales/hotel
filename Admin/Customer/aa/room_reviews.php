<?php
require_once 'db_con.php';

header('Content-Type: application/json');

if (!isset($_GET['room_type_id']) || !is_numeric($_GET['room_type_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid room type ID']);
    exit;
}

$roomTypeId = (int)$_GET['room_type_id'];

try {
    // Get room details
    $stmt = $pdo->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
    $stmt->execute([$roomTypeId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        throw new Exception('Room not found');
    }
    
    // Get average rating and review count
    $ratingStmt = $pdo->prepare("
        SELECT 
            AVG(r.rating) as average_rating,
            COUNT(r.review_id) as total_reviews
        FROM room_reviews r
        WHERE r.room_type_id = ?
    ");
    $ratingStmt->execute([$roomTypeId]);
    $ratings = $ratingStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get all reviews with user information
    $reviewsStmt = $pdo->prepare("
        SELECT 
            r.*,
            u.firstname,
            u.lastname
        FROM room_reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.room_type_id = ?
        ORDER BY r.created_at DESC
    ");
    $reviewsStmt->execute([$roomTypeId]);
    $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $response = [
        'success' => true,
        'room' => $room,
        'ratings' => [
            'average' => $ratings['average_rating'] ? round($ratings['average_rating'], 1) : 0,
            'total' => $ratings['total_reviews']
        ],
        'reviews' => $reviews
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch room details',
        'message' => $e->getMessage()
    ]);
}
