<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addon_id = intval($_POST['addon_id']);
    $action = $_POST['action'];

    // Get current quantity and price
    $sql = "SELECT quantity, price, addCart_fk_id FROM addcart_addson WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $addon_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result)
        exit;

    $quantity = $result['quantity'];
    $price = $result['price']; // unit price
    $addCart_fk_id = $result['addCart_fk_id'];

    if ($action === 'increase')
        $quantity++;
    if ($action === 'decrease')
        $quantity--;
    if ($action === 'remove')
        $quantity = 0;

    if ($quantity > 0) {
        $update = "UPDATE addcart_addson SET quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update);
        $update_stmt->bind_param("ii", $quantity, $addon_id);
        $update_stmt->execute();
    } else {
        $delete = "DELETE FROM addcart_addson WHERE id = ?";
        $delete_stmt = $conn->prepare($delete);
        $delete_stmt->bind_param("i", $addon_id);
        $delete_stmt->execute();
    }

    echo json_encode([
        'status' => 'success',
        'quantity' => $quantity,
        'price' => $price,
        'addCart_fk_id' => $addCart_fk_id
    ]);
}
?>