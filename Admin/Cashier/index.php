<?php
ob_start();
session_start();

// Debug session variables
error_log("Session variables: " . print_r($_SESSION, true));

// Check if user is logged in and is a cashier
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'cashier') {
    header("Location: ../../login.php");
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Database connection check
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify user exists and has appropriate role
$userQuery = "SELECT * FROM userss WHERE id = ? AND user_type = 'cashier'";
$stmt = mysqli_prepare($con, $userQuery);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    header("Location: ../../login.php");
    exit();
}

include_once "header.php";
include_once "sidebar.php";

if (isset($_GET['POS'])){
    include_once "POS.php";
}
elseif(isset($_GET['Order'])){
    include_once "Order.php";
}
elseif(isset($_GET['ProcessingOrder'])){
    include_once "ProcessingOrder.php";
}
elseif(isset($_GET['OccupiedTables'])){
    include_once "OccupiedTables.php";
}
elseif (isset($_GET['sales'])){
    include_once "sales.php";
}

include_once "footer.php";
?>