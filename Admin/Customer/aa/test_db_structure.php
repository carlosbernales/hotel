<?php
// Test script to check database structure
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Check bookings table structure
    $stmt = $pdo->query("DESCRIBE bookings");
    $bookingsColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check room_types table structure
    $stmt = $pdo->query("DESCRIBE room_types");
    $roomTypesColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check room_numbers table structure
    $stmt = $pdo->query("DESCRIBE room_numbers");
    $roomNumbersColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sample booking data
    $stmt = $pdo->query("SELECT * FROM bookings LIMIT 3");
    $sampleBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'bookings_structure' => $bookingsColumns,
        'room_types_structure' => $roomTypesColumns,
        'room_numbers_structure' => $roomNumbersColumns,
        'sample_bookings' => $sampleBookings
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
