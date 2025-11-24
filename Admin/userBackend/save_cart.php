<?php
session_start();
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $category_fk_id = intval($_POST['category_fk_id']);
    $menu_fk_id = intval($_POST['menu_fk_id']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    $check_sql = "SELECT id, quantity FROM add_to_cart WHERE user_id = ? AND menu_fk_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $menu_fk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update_sql = "UPDATE add_to_cart SET quantity = ?, price = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("idi", $new_qty, $price, $row['id']);
        $update_stmt->execute();
        $cart_id = $row['id'];
    } else {
        $insert_sql = "INSERT INTO add_to_cart (user_id, category_fk_id, menu_fk_id, price, quantity)
                       VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiidi", $user_id, $category_fk_id, $menu_fk_id, $price, $quantity);
        $insert_stmt->execute();
        $cart_id = $insert_stmt->insert_id;
    }

    echo json_encode([
        'status' => 'success',
        'cart_id' => $cart_id,
        'menu_fk_id' => $menu_fk_id,
        'quantity' => $quantity,
        'price' => $price,
        'image' => $_POST['image']
    ]);
}
?>