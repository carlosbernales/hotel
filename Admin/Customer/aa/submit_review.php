<?php
require_once 'db_con.php';

header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get and validate input
    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
    ]);
    $reviewer_name = trim(filter_input(INPUT_POST, 'reviewer_name', FILTER_SANITIZE_STRING));
    $review_text = trim(filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING));

    // Validate required fields
    if (!$room_id || !$rating || empty($reviewer_name) || empty($review_text)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'All fields are required and must be valid.'
        ]);
        exit;
    }

    // Check if room exists
    $stmt = $pdo->prepare("SELECT room_type_id FROM room_types WHERE room_type_id = ?");
    $stmt->execute([$room_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit;
    }

    // Create reviews table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS room_reviews (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT NOT NULL,
        reviewer_name VARCHAR(100) NOT NULL,
        rating TINYINT NOT NULL,
        review_text TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE,
        CHECK (rating BETWEEN 1 AND 5)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Insert the review
    $stmt = $pdo->prepare("
        INSERT INTO room_reviews (room_id, reviewer_name, rating, review_text)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([$room_id, $reviewer_name, $rating, $review_text]);
    $review_id = $pdo->lastInsertId();

    // Update the room's rating
    $stmt = $pdo->prepare("
        UPDATE room_types rt
        SET 
            rt.rating = (
                SELECT AVG(rating) 
                FROM room_reviews 
                WHERE room_id = rt.room_type_id 
                AND status = 'approved'
            ),
            rt.rating_count = (
                SELECT COUNT(*) 
                FROM room_reviews 
                WHERE room_id = rt.room_type_id 
                AND status = 'approved'
            )
        WHERE rt.room_type_id = ?
    ");
    $stmt->execute([$room_id]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your review! It will be visible after approval.',
        'review_id' => $review_id
    ]);

} catch (Exception $e) {
    error_log('Error submitting review: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while submitting your review. Please try again.'
    ]);
}
