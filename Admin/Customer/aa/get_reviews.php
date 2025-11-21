<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Debug logging
    error_log("Starting get_reviews.php");
    
    // First verify the database connection
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        error_log("Database connection not established");
        throw new Exception('Database connection not established');
    }

    if (!isset($_GET['room_type_id'])) {
        error_log("Room type ID not provided");
        throw new Exception('Room type ID is required');
    }

    $roomTypeId = intval($_GET['room_type_id']);
    error_log("Fetching reviews for room_type_id: " . $roomTypeId);

    // Create room_reviews table if it doesn't exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS room_reviews (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        room_type_id INT NOT NULL,
        user_id INT,
        rating DECIMAL(2,1) NOT NULL,
        review TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($createTableSQL);
    error_log("Ensured room_reviews table exists");

    // Updated query to match your database structure
    $sql = "SELECT 
                rr.review_id,
                rr.user_id,
                rr.rating,
                rr.review,
                rr.created_at,
                CONCAT(COALESCE(u.firstname, 'Anonymous'), ' ', COALESCE(u.lastname, 'User')) as username
            FROM room_reviews rr
            LEFT JOIN users u ON rr.user_id = u.id
            WHERE rr.room_type_id = :room_type_id
            ORDER BY rr.created_at DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':room_type_id', $roomTypeId, PDO::PARAM_INT);
    $stmt->execute();
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Fetched " . count($reviews) . " reviews");
    
    // Format the reviews
    foreach ($reviews as &$review) {
        $review['formatted_date'] = date('M d, Y', strtotime($review['created_at']));
        $review['rating'] = floatval($review['rating']);
        // Username is now coming from the CONCAT in the SQL query
        $review['review'] = htmlspecialchars($review['review'] ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    error_log("Sending response with " . count($reviews) . " reviews");
    echo json_encode($reviews);
    
} catch(PDOException $e) {
    error_log("PDO Error in get_reviews.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error occurred',
        'details' => $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General Error in get_reviews.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}