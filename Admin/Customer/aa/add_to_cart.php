<?php
session_start();

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add the item to the cart
$_SESSION['cart'][] = [
    'roomName' => $data['roomName'],
    'price' => $data['price'],
    'roomNumber' => $data['roomNumber']
];

// Return success response with cart count
echo json_encode([
    'success' => true,
    'cartCount' => count($_SESSION['cart'])
]);
?>
