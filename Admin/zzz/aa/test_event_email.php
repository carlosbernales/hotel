<?php
// Test script for event booking email functionality
require_once 'db_con.php';
require_once 'includes/event_mailer.php';

session_start();

// Mock user session for testing
$_SESSION['user_id'] = 1; // Assuming user ID 1 exists

// Test booking data
$testBookingData = [
    'booking_refId' => 'EVT-TEST-' . date('Ymd-His'),
    'email' => 'chanomabalo@gmail.com', // Replace with actual test email
    'customer_name' => 'Test Customer',
    'package_name' => 'Wedding Package',
    'event_date' => date('Y-m-d', strtotime('+7 days')),
    'event_time' => '18:00:00',
    'event_place' => 'Main Hall',
    'number_of_guests' => 50,
    'total_amount' => 15000.00,
    'paid_amount' => 7500.00,
    'remaining_balance' => 7500.00
];

try {
    $eventMailer = new EventMailer();
    $result = $eventMailer->sendEventBookingConfirmation($testBookingData);
    
    echo "<h2>Event Email Test Results</h2>";
    echo "<p><strong>Status:</strong> " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "</p>";
    echo "<p><strong>Message:</strong> " . $result['message'] . "</p>";
    echo "<p><strong>Booking Reference:</strong> " . $testBookingData['booking_refId'] . "</p>";
    
    if ($result['success']) {
        echo "<p style='color: green;'>✓ Email sent successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Email sending failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<p><a href="events.php">Back to Events</a></p>
