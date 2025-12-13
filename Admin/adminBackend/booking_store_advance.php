<?php
session_start();

$first = $_POST['first'] ?? '';
$last = $_POST['last'] ?? '';
$contact = $_POST['contact'] ?? '';
$datetime = $_POST['datetime'] ?? '';
$tableTypes = $_POST['tableTypes'] ?? [];
$tables = $_POST['tables'] ?? [];

$_SESSION['advance_order'] = [
    'first' => $first,
    'last' => $last,
    'contact' => $contact,
    'datetime' => $datetime,
    'tableTypes' => $tableTypes,
    'tables' => $tables
];

echo json_encode(['status' => 'success', 'redirect' => '../Admin/index.php?advance-orders']);
