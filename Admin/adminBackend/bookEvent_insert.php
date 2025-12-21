<?php
include '../adminBackend/mydb.php';

$customer_name = trim($_POST['name']);
$number_of_guests = (int) $_POST['guests'];
$package_id = (int) $_POST['package_id'];

$date_time_start = isset($_POST['date_time_start']) ? date('Y-m-d H:i:s', strtotime($_POST['date_time_start'])) : null;
$date_time_end = isset($_POST['date_time_end']) ? date('Y-m-d H:i:s', strtotime($_POST['date_time_end'])) : null;

if (empty($customer_name) || $number_of_guests <= 0 || empty($date_time_start) || empty($date_time_end)) {
    echo 'INVALID_DATA';
    exit;
}

$sqlPackage = "SELECT name, price FROM event_packages WHERE id = ?";
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

$total_amount = $package_price;

$sqlCheck = "SELECT * FROM event_bookings WHERE 
    (date_time_start < ? AND date_time_end > ?) OR
    (date_time_start < ? AND date_time_end > ?) OR
    (date_time_start >= ? AND date_time_end <= ?)";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param(
    "ssssss",
    $date_time_end,
    $date_time_start,
    $date_time_start,
    $date_time_start,
    $date_time_start,
    $date_time_end
);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo 'CONFLICT';
    exit;
}

$sqlInsert = "INSERT INTO event_bookings 
    (customer_name, package_name, package_price, total_amount, date_time_start, date_time_end, number_of_guests)
    VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);

$stmtInsert->bind_param(
    "ssddssi",
    $customer_name,
    $package_name,
    $package_price,
    $total_amount,
    $date_time_start,
    $date_time_end,
    $number_of_guests
);

if ($stmtInsert->execute()) {
    echo 'SUCCESS';
} else {
    echo 'ERROR: ' . $stmtInsert->error;
}
?>