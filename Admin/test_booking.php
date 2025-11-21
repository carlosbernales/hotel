<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Include required files
require 'Customer/aa/db_con.php';
require 'Customer/aa/process_booking.php';

// Create test data
$testData = [
    'firstName' => 'Test',
    'lastName' => 'User',
    'contact' => '09123456789',
    'email' => 'test@example.com',
    'checkIn' => date('Y-m-d', strtotime('+1 day')),
    'checkOut' => date('Y-m-d', strtotime('+3 days')),
    'discountType' => 'none',
    'paymentMethod' => 'Cash',
    'paymentOption' => 'full',
    'rooms' => json_encode([
        [
            'id' => 1,
            'type' => 'Standard Room',
            'price' => 1000,
            'capacity' => '1 Double Bed',
            'image' => 'test.jpg',
            'guestCount' => 1,
            'guestNames' => ['Test Guest'],
            'nights' => 2,
            'totalPrice' => 2000,
            'room_type_id' => 1
        ]
    ])
];

// Simulate POST request
$_POST = $testData;

// Run the booking process
try {
    // Call the booking process function
    require 'Customer/aa/process_booking.php';
} catch (Exception $e) {
    // Detailed error handling
    $errorDetails = [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    // Log the error
    error_log("Test Booking Error: " . print_r($errorDetails, true));
    
    // Display error details
    echo "<pre>";
    echo "Error Details:\n";
    print_r($errorDetails);
    echo "\n\nTest Data:\n";
    print_r($testData);
    echo "</pre>";
}
?>
