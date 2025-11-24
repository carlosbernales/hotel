<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'];

    // Get current quantity and price
    $sql = "SELECT quantity, price FROM add_to_cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result)
        exit;

    $quantity = $result['quantity'];
    $price = $result['price'];

    if ($action === 'increase')
        $quantity++;
    if ($action === 'decrease')
        $quantity--;
    if ($action === 'remove')
        $quantity = 0;

    if ($quantity > 0) {
        $update = "UPDATE add_to_cart SET quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update);
        $update_stmt->bind_param("ii", $quantity, $cart_id);
        $update_stmt->execute();
    } else {
        // Remove linked add-ons first
        $delete_addons = "DELETE FROM addcart_addson WHERE addCart_fk_id = ?";
        $stmt_addons = $conn->prepare($delete_addons);
        $stmt_addons->bind_param("i", $cart_id);
        $stmt_addons->execute();

        // Remove main cart item
        $delete = "DELETE FROM add_to_cart WHERE id = ?";
        $delete_stmt = $conn->prepare($delete);
        $delete_stmt->bind_param("i", $cart_id);
        $delete_stmt->execute();
    }

    echo json_encode([
        'quantity' => $quantity,
        'price' => $price
    ]);
}
?>