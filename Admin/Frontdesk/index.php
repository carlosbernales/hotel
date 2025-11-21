<?php
require_once "db.php";

// Check if user is logged in before any output
if (!isset($_SESSION['user_id'])) {
    header('Location:../Admin/login.php');
    exit();
}

// Handle event booking redirect before any output
if (isset($_GET['event_booking'])) {
    header('Location: event_booking.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$userQuery = "SELECT * FROM userss WHERE id = '$user_id'";
$result = mysqli_query($con, $userQuery);
$user = mysqli_fetch_assoc($result);

// Include header and sidebar after all possible redirects
include_once "header.php";
include_once "sidebar.php";
?>

<style>
/* Remove the white space between header and content */
body {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* Adjust the main container */
.col-sm-9.col-sm-offset-3.col-lg-10.col-lg-offset-2.main {
    padding-top: 0 !important;
    margin-top: 0 !important;
    background-color: transparent;
}

/* Remove any unwanted margins from rows */
.row {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

/* Adjust content area to remove white space */
.content-wrapper {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

/* Remove default bootstrap container padding */
.container-fluid {
    padding-top: 0 !important;
}

.dashboard-stats {
    padding: 30px;
    margin-bottom: 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dashboard-stats h3 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.stats-number {
    font-size: 48px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.stats-label {
    font-size: 16px;
    color: #666;
}

.stats-row {
    margin-bottom: 30px;
}

.stats-card {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card .number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #333;
}

.stats-card .label {
    font-size: 18px;
    color: #666;
    margin-bottom: 5px;
}

.stats-card .period {
    font-size: 14px;
    color: #999;
}

.revenue-card {
    background: linear-gradient(135deg, #DAA520, #B8860B);
    color: white;
}

.revenue-card .number,
.revenue-card .label,
.revenue-card .period {
    color: white;
}

.reservations {
    margin-top: 40px;
}

.reservations h2 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
}

.table th {
    font-size: 16px;
    font-weight: 600;
}

.table td {
    font-size: 15px;
    vertical-align: middle;
}

.search-box {
    margin-bottom: 30px;
}

.search-box input {
    height: 50px;
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 25px;
}

.search-box .btn {
    height: 50px;
    border-radius: 25px;
    font-size: 16px;
    padding: 0 30px;
}
</style>

<?php
// Default page is dashboard
$page = '../dashboard.php';

// Handle room booking first
if (isset($_GET['room_id'])) {
    $page = "booking_form.php";
}
// Then handle other pages
elseif (isset($_GET['room_mang'])){
    $page = "room_mang.php";
}
elseif (isset($_GET['dashboard'])){
    $page = "dashboard.php";
}
elseif(isset($_GET['UserInfo'])){
    $page = "UserInfo.php";
}
elseif(isset($_GET['Order'])){
    $page = "Order.php";
}
elseif(isset($_GET['BookingList'])){
    $page = "BookingList.php";
}
elseif (isset($_GET['reservation'])){
    $page = "reservation.php";
}
elseif (isset($_GET['table_booking'])){
    $page = "table_booking.php";
}
elseif (isset($_GET['table_packages'])){
    $page = "table_packages.php";
}
elseif (isset($_GET['booking_settings'])){
    $page = "booking_settings.php";
}
elseif (isset($_GET['booking_status'])){
    $page = "booking_status.php";
}
elseif (isset($_GET['staff_mang'])){
    $page = "staff_mang.php";
}
elseif (isset($_GET['add_emp'])){
    $page = "add_emp.php";
}
elseif (isset($_GET['complain'])){
    $page = "Feedback.php";
}
elseif (isset($_GET['statistics']) || isset($_GET['sales_report'])){
    $page = "sales_report.php";
}
elseif (isset($_GET['payment_settings'])){
    $page = "payment_settings.php";
}
elseif (isset($_GET['Paymentss'])){
    $page = "Paymentss.php";
}
elseif (isset($_GET['room_types'])){
    $page = "room_types.php";
}
elseif (isset($_GET['emp_history'])){
    $page = "emp_history.php";
}
elseif (isset($_GET['room_settings'])) {
    $page = "room_settings.php";
} 
elseif (isset($_GET['table_settings'])) {
    $page = "table_settings.php";
} 
elseif (isset($_GET['event_settings'])) {
    $page = "event_settings.php";
} 
elseif (isset($_GET['room_management'])) {
    $page = "room_management.php";
} 
elseif (isset($_GET['page']) && $_GET['page'] == 'table_management') {
    $page = "table_management.php";
}
elseif (isset($_GET['advance_checkin'])) {
    include 'advance_checkin_page.php';
}

// Include the selected page
include_once $page;
include_once "footer.php";