<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $addCart_fk_id = intval($_POST['addCart_fk_id']);
    $adds_fk_id = intval($_POST['adds_fk_id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Check if same add-on already exists for this cart item
    $check_sql = "SELECT quantity FROM addcart_addson WHERE addCart_fk_id = ? AND adds_fk_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $addCart_fk_id, $adds_fk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing quantity
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update_sql = "UPDATE addcart_addson SET quantity = ? WHERE addCart_fk_id = ? AND adds_fk_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $new_qty, $addCart_fk_id, $adds_fk_id);
        $update_stmt->execute();
        echo json_encode(['status' => 'updated', 'quantity' => $new_qty]);
    } else {
        // Insert new add-on
        $insert_sql = "INSERT INTO addcart_addson (addCart_fk_id, adds_fk_id, quantity, name, price)
                       VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiisd", $addCart_fk_id, $adds_fk_id, $quantity, $name, $price);
        $insert_stmt->execute();
        echo json_encode(['status' => 'inserted', 'quantity' => $quantity]);
    }
}
