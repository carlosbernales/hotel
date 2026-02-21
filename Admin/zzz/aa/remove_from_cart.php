<?php
session_start();

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$index = $data['index'];

if (isset($_SESSION['cart'][$index])) {
    // Remove the item from the cart
    array_splice($_SESSION['cart'], $index, 1);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
}
?>
