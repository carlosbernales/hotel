<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in by verifying session
$loggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

echo json_encode([
    'logged_in' => $loggedIn,
    'user_id' => $loggedIn ? $_SESSION['user_id'] : null
]);
?>
