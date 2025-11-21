<?php
require_once 'db.php';

// Set response header to JSON
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'rating' => 0,
    'message' => ''
];

// Check if room_type_id is provided
if (!isset($_GET['room_type_id']) || empty($_GET['room_type_id'])) {
    $response['message'] = 'Room type ID is required';
    echo json_encode($response);
    exit;
}

$roomTypeId = (int)$_GET['room_type_id'];

try {
    // Get average rating from room_reviews table
    $sql = "SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as review_count 
            FROM room_reviews 
            WHERE room_type_id = ?";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database error: " . $con->error);
    }
    
    $stmt->bind_param("i", $roomTypeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $rating = $row['avg_rating'] ? (float)$row['avg_rating'] : 0;
        $reviewCount = (int)$row['review_count'];
        
        $response['success'] = true;
        $response['rating'] = $rating;
        $response['review_count'] = $reviewCount;
        $response['message'] = 'Rating data retrieved successfully';
    } else {
        $response['message'] = 'No rating data found';
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response); 