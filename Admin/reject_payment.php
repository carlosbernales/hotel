<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['reason'])) {
    header('Location: event_payments.php');
    exit();
}

$payment_id = $_GET['id'];
$reason = $_GET['reason'];

// Get payment details to determine the type
$query = "SELECT booking_type FROM payments WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();

if (!$payment) {
    $_SESSION['error_message'] = "Payment not found.";
    header('Location: event_payments.php');
    exit();
}

// Update payment status and add rejection reason
$query = "UPDATE payments SET status = 'rejected', notes = ? WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("si", $reason, $payment_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Payment rejected successfully!";
} else {
    $_SESSION['error_message'] = "Error rejecting payment: " . $con->error;
}

// Redirect based on payment type
switch ($payment['booking_type']) {
    case 'room':
        header('Location: room_payments.php');
        break;
    case 'table':
        header('Location: table_payments.php');
        break;
    case 'event':
    default:
        header('Location: event_payments.php');
        break;
}
exit(); 