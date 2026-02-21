<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
        exit;
    }

    // Validate inputs
    $room_id = $_POST['room_type_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $review = $_POST['review'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$room_id || !$rating || !$review) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    try {
        // Debug logging
        error_log("Attempting to submit review - Room ID: $room_id, User ID: $user_id");

        // First check if the room_type_id exists
        $check_room_sql = "SELECT room_type_id FROM room_types WHERE room_type_id = ?";
        $check_room_stmt = $pdo->prepare($check_room_sql);
        $check_room_stmt->execute([$room_id]);
        
        $room_exists = $check_room_stmt->fetch();

        if (!$room_exists) {
            echo json_encode(['success' => false, 'message' => 'Invalid room ID']);
            exit;
        }

        // Check if user has already reviewed this room
        $check_sql = "SELECT COUNT(*) FROM room_reviews WHERE room_type_id = ? AND user_id = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$room_id, $user_id]);
        
        if ($check_stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this room']);
            exit;
        }

        // Validate rating value
        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid rating value']);
            exit;
        }

        // Begin transaction
        $pdo->beginTransaction();

        try {
            // Insert the review
            $sql = "INSERT INTO room_reviews (room_type_id, user_id, rating, review) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$room_id, $user_id, $rating, $review]);
            
            // Debug logging
            error_log("Review inserted successfully");

            // Update average rating
            $update_sql = "UPDATE room_types 
                          SET rating = COALESCE((
                              SELECT ROUND(AVG(rating), 1)
                              FROM room_reviews 
                              WHERE room_type_id = ?
                          ), 0),
                          rating_count = (
                              SELECT COUNT(*) 
                              FROM room_reviews 
                              WHERE room_type_id = ?
                          )
                          WHERE room_type_id = ?";
            
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$room_id, $room_id, $room_id]);
            
            // Debug logging
            error_log("Rating updated successfully");

            // Commit transaction
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        error_log("Stack Trace: " . $e->getTraceAsString());
        
        if ($e->getCode() == '23000') {
            echo json_encode(['success' => false, 'message' => 'Database constraint violation. Please check your input.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again later.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 