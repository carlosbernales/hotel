<?php
ob_start();
session_start();

// Debug session variables
error_log("Session variables: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: ../../login.php");
    exit();
}

// Redirect customers to customer index
if ($_SESSION['user_type'] === 'customer') {
    header("Location: ../Customer/aa/index.php");
    exit();
}

// Only allow cashier users
if ($_SESSION['user_type'] !== 'cashier') {
    header("Location: ../../login.php");
    exit();
}

require_once 'db.php';
require_once '../Customer/aa/includes/security_helper.php';

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

// Set session variables including profile photo
$_SESSION['profile_photo'] = $user['profile_photo'] ?? '';

include_once "header.php";
include_once "sidebar.php";

// Handle export request
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    // Include the export logic from sales.php
    include_once "sales_export.php";
    exit();
}

// Handle both page=pos and direct ?pos URL formats
$page = '';
if (isset($_GET['page'])) {
    $page = strtolower($_GET['page']);
} else {
    // Check for direct parameters like ?pos, ?order, etc.
    $directPages = ['pos', 'pending_orders', 'processingorder', 'occupiedtables', 'sales', 'my_profile'];
    foreach ($directPages as $directPage) {
        if (isset($_GET[$directPage])) {
            $page = $directPage;
            break;
        }
    }
    
    // If no direct page found, check if date parameters are present (indicating sales page with filters)
    if (empty($page) && (isset($_GET['from_date']) || isset($_GET['to_date']))) {
        $page = 'sales';
    }
}

switch($page) {
    case 'pos':
        include_once "pos.php";
        break;
    case 'pending_orders':
        include_once "pending_orders.php";
        break;
    case 'processingorder':
        include_once "ProcessingOrder.php";
        break;
    case 'occupiedtables':
        include_once "OccupiedTables.php";
        break;
    case 'sales':
        include_once "sales.php";
        break;
    case 'messages':
        include_once "messages.php";
        break;
    case 'my_profile':
        include_once "my_profile.php";
        break;
    default:
        break;
}

?>