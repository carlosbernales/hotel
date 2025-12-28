<?php
include '../adminBackend/mydb.php';

$customer_name = trim($_POST['name']);
$number_of_guests = (int) $_POST['guests'];
$extra_guests = isset($_POST['additional_guests']) ? (int) $_POST['additional_guests'] : 0;
$price_per_additional = isset($_POST['price_per_additional']) ? (float) $_POST['price_per_additional'] : 0;
$total_amount = isset($_POST['total_amount']) ? (float) $_POST['total_amount'] : 0;
$paid_amount = isset($_POST['paid_amount']) ? (float) $_POST['paid_amount'] : 0;
$remaining_balance = isset($_POST['remaining_balance']) ? (float) $_POST['remaining_balance'] : 0;
$event_type = trim($_POST['event_type']);
$payment_type = trim($_POST['payment_type']);
$payment_method = trim($_POST['payment_method']);
$package_id = (int) $_POST['package_id'];
$booking_status = 'Accepted';


$date_time_start = isset($_POST['date_time_start']) ? date('Y-m-d H:i:s', strtotime($_POST['date_time_start'])) : null;
$date_time_end = isset($_POST['date_time_end']) ? date('Y-m-d H:i:s', strtotime($_POST['date_time_end'])) : null;

if (empty($customer_name) || $number_of_guests <= 0 || empty($date_time_start) || empty($date_time_end)) {
    echo 'INVALID_DATA';
    exit;
}

$sqlPackage = "SELECT name, price, max_guests FROM event_packages WHERE id=?";
$stmt = $conn->prepare($sqlPackage);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo 'INVALID_PACKAGE';
    exit;
}

$package = $result->fetch_assoc();
$package_name = $package['name'];
$package_price = $package['price'];
$max_guests = (int) $package['max_guests'];

$sqlCheck = "SELECT * FROM event_bookings WHERE 
    (date_time_start < ? AND date_time_end > ?) OR
    (date_time_start < ? AND date_time_end > ?) OR
    (date_time_start >= ? AND date_time_end <= ?)";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ssssss", $date_time_end, $date_time_start, $date_time_start, $date_time_start, $date_time_start, $date_time_end);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
if ($resultCheck->num_rows > 0) {
    echo 'CONFLICT';
    exit;
}

function generateUniqueBookingRefId($conn, $length = 6)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    do {
        $refId = '';
        for ($i = 0; $i < $length; $i++) {
            $refId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $booking_refId = 'EVT-' . $refId;

        $stmtCheck = $conn->prepare("SELECT id FROM event_bookings WHERE booking_refId = ?");
        $stmtCheck->bind_param("s", $booking_refId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
    } while ($resultCheck->num_rows > 0);

    return $booking_refId;
}

$booking_refId = generateUniqueBookingRefId($conn);


$sqlInsert = "INSERT INTO event_bookings 
(booking_refId, customer_name, package_name, package_price, total_amount, paid_amount, remaining_balance, date_time_start, date_time_end, number_of_guests, extra_guests, max_guest, extra_guest_charge, event_type, payment_type, payment_method, booking_status)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param(
    "sssddddssiiddssss",
    $booking_refId,
    $customer_name,
    $package_name,
    $package_price,
    $total_amount,
    $paid_amount,
    $remaining_balance,
    $date_time_start,
    $date_time_end,
    $number_of_guests,
    $extra_guests,
    $max_guests,
    $price_per_additional,
    $event_type,
    $payment_type,
    $payment_method,
    $booking_status
);

if ($stmtInsert->execute()) {
    echo 'SUCCESS';
} else {
    echo 'ERROR: ' . $stmtInsert->error;
}
?>