<?php
// Start the session
session_start();

// Clear the reservation data from the session
if (isset($_SESSION['reservation_data'])) {
    unset($_SESSION['reservation_data']);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No session data found']);
}

// End the script
exit();
?>
