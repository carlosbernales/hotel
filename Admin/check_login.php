<?php
session_start();

header('Content-Type: application/json');

// Get the current page URL from the referer
$currentPage = isset($_SERVER['HTTP_REFERER']) ? basename($_SERVER['HTTP_REFERER']) : 'index.php';

$response = [
    'loggedIn' => isset($_SESSION['user_id']),
    'message' => isset($_SESSION['user_id']) ? 'User is logged in' : 'User is not logged in',
    'redirect' => "login.php?redirect=$currentPage"
];

echo json_encode($response);
exit; 