<?php
include '../adminBackend/mydb.php';
session_start();

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE orders_table SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['checkout_success'] = true;
        header("Location: ../index.php?table-booking-acptd");
        exit;
    } else {
        die("Failed to update order status.");
    }
}
?>