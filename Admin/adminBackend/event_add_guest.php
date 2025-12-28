<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid order ID.");
    }
    $orderId = (int) $_GET['id'];

    $extraGuests = isset($_POST['extra_guests']) ? (int) $_POST['extra_guests'] : 0;
    $extraGuestCharge = isset($_POST['extra_guest_charge']) ? (float) $_POST['extra_guest_charge'] : 0.0;

    if ($extraGuests < 1 || $extraGuestCharge < 0) {
        die("Invalid input values.");
    }

    $sql = "SELECT total_amount, paid_amount, extra_guests, extra_guest_charge 
            FROM event_bookings 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        die("Order not found.");
    }

    $newExtraGuests = $order['extra_guests'] + $extraGuests;
    $newExtraGuestCharge = $extraGuestCharge;

    $additionalAmount = $extraGuests * $extraGuestCharge;
    $newTotalAmount = $order['total_amount'] + $additionalAmount;
    $remainingBalance = $newTotalAmount - $order['paid_amount'];

    $updateSql = "UPDATE event_bookings 
                  SET extra_guests = ?, 
                      extra_guest_charge = ?, 
                      total_amount = ?, 
                      remaining_balance = ? 
                  WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("idddi", $newExtraGuests, $newExtraGuestCharge, $newTotalAmount, $remainingBalance, $orderId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        header("Location: ../index.php?event-acp-list");
        exit;
    } else {
        die("Failed to update order.");
    }
} else {
    die("Invalid request method.");
}
?>