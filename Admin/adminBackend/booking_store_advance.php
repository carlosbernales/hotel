<?php
session_start();

$first = $_POST['first'] ?? '';
$last = $_POST['last'] ?? '';
$email = $_POST['email'] ?? '';
$contact = $_POST['contact'] ?? '';
$datetime = $_POST['datetime'] ?? '';
$tableTypes = $_POST['tableTypes'] ?? [];
$tables = $_POST['tables'] ?? [];

$_SESSION['advance_order'] = [
    'first' => $first,
    'last' => $last,
    'email' => $email,
    'contact' => $contact,
    'datetime' => $datetime,
    'tableTypes' => $tableTypes,
    'tables' => $tables
];

echo json_encode([
    'status' => 'success',
    'redirect' => '../Admin/index.php?advance-orders'
]);
