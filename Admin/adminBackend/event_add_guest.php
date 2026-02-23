<?php
include '../adminBackend/mydb.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid order ID"]);
        exit;
    }

    $orderId = (int) $_GET['id'];
    $extraGuests = isset($_POST['extra_guests']) ? (int) $_POST['extra_guests'] : 0;
    $extraGuestCharge = isset($_POST['extra_guest_charge']) ? (float) $_POST['extra_guest_charge'] : 0.0;

    if ($extraGuests < 1 || $extraGuestCharge < 0) {
        echo json_encode(["status" => "error", "message" => "Invalid input values"]);
        exit;
    }

    $sql = "SELECT total_amount, paid_amount, extra_guests 
            FROM event_bookings 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        echo json_encode(["status" => "error", "message" => "Booking not found"]);
        exit;
    }

    $newExtraGuests = $order['extra_guests'] + $extraGuests;
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
    $updateStmt->bind_param("idddi", $newExtraGuests, $extraGuestCharge, $newTotalAmount, $remainingBalance, $orderId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No changes were made"]);
    }
}