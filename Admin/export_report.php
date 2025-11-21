<?php
require_once 'includes/init.php';
require_once 'db.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Sales_Report_' . date('Y-m-d_H-i-s') . '.xls"');

// Get parameters from URL
$reportType = $_GET['report_type'] ?? 'all';
$rangeType = $_GET['range_type'] ?? 'daily';
$today = date('Y-m-d');

// Set date ranges based on range type
switch($rangeType) {
    case 'daily':
        $startDate = $today;
        $endDate = $today;
        $dateCondition = "DATE(updated_at) = '$today'";
        $periodText = "Daily Report - " . date('F j, Y');
        break;
    case 'weekly':
        $startDate = date('Y-m-d', strtotime('last sunday'));
        if (date('w') == 0) {
            $startDate = $today;
        }
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        $dateCondition = "DATE(updated_at) >= '$startDate' AND DATE(updated_at) <= '$endDate'";
        $periodText = "Weekly Report - " . date('M d', strtotime($startDate)) . " to " . date('M d, Y', strtotime($endDate));
        break;
    case 'monthly':
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $dateCondition = "DATE(updated_at) >= '$startDate' AND DATE(updated_at) <= '$endDate'";
        $periodText = "Monthly Report - " . date('F Y');
        break;
    case 'yearly':
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-31');
        $dateCondition = "DATE(updated_at) >= '$startDate' AND DATE(updated_at) <= '$endDate'";
        $periodText = "Yearly Report - " . date('Y');
        break;
    default:
        $startDate = $today;
        $endDate = $today;
        $dateCondition = "DATE(updated_at) = '$today'";
        $periodText = "Daily Report - " . date('F j, Y');
}

// Build the SQL query based on report type
$userTypeCondition = "";
if ($reportType !== 'all') {
    $userTypeCondition = "AND user_type = '$reportType'";
}

$query = "SELECT 
    booking_id,
    first_name,
    last_name,
    booking_type,
    email,
    contact,
    check_in,
    check_out,
    arrival_time,
    number_of_guests,
    payment_option,
    payment_method,
    discount_type,
    total_amount,
    status,
    created_at,
    updated_at,
    user_type
FROM bookings 
WHERE $dateCondition $userTypeCondition
ORDER BY updated_at DESC";

$result = mysqli_query($con, $query);

// Calculate totals
$totalQuery = "SELECT 
    COUNT(*) as total_bookings,
    COALESCE(SUM(total_amount), 0) as total_revenue,
    COALESCE(SUM(CASE WHEN payment_option = 'full' THEN total_amount ELSE 0 END), 0) as full_payments,
    COALESCE(SUM(CASE WHEN payment_option = 'downpayment' THEN total_amount ELSE 0 END), 0) as downpayments
FROM bookings 
WHERE $dateCondition $userTypeCondition";

$totalResult = mysqli_query($con, $totalQuery);
$totals = mysqli_fetch_assoc($totalResult);

// Start generating the Excel content
echo "<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid black; padding: 5px; }
    .header { font-size: 16px; font-weight: bold; }
    .subheader { font-size: 14px; }
    .total-row { font-weight: bold; background-color: #f0f0f0; }
</style>
</head>
<body>
<table>
    <tr>
        <td colspan='9' class='header'>CASA ESTELA BOUTIQUE HOTEL & CAFE</td>
    </tr>
    <tr>
        <td colspan='9' class='subheader'>$periodText</td>
    </tr>
    <tr>
        <td colspan='9' class='subheader'>Report Type: " . ucfirst($reportType) . "</td>
    </tr>
    <tr>
        <td colspan='9'></td>
    </tr>
    <tr>
        <td colspan='9' class='header'>SUMMARY</td>
    </tr>
    <tr>
        <td colspan='4'>Total Number of Bookings:</td>
        <td colspan='5'>" . $totals['total_bookings'] . "</td>
    </tr>
    <tr>
        <td colspan='4'>Total Revenue:</td>
        <td colspan='5'>₱" . number_format($totals['total_revenue'], 2) . "</td>
    </tr>
    <tr>
        <td colspan='4'>Full Payment Total:</td>
        <td colspan='5'>₱" . number_format($totals['full_payments'], 2) . "</td>
    </tr>
    <tr>
        <td colspan='4'>Downpayment Total:</td>
        <td colspan='5'>₱" . number_format($totals['downpayments'], 2) . "</td>
    </tr>
    <tr>
        <td colspan='9'></td>
    </tr>
    <tr>
        <td colspan='9' class='header'>DETAILED BOOKING INFORMATION</td>
    </tr>
    <tr>
        <th>Booking ID</th>
        <th>Guest Name</th>
        <th>Contact</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Payment Option</th>
        <th>Payment Method</th>
        <th>Status</th>
        <th>Amount</th>
    </tr>";

$totalAmount = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $totalAmount += $row['total_amount'];
    echo "<tr>
        <td>" . $row['booking_id'] . "</td>
        <td>" . $row['first_name'] . ' ' . $row['last_name'] . "</td>
        <td>" . $row['contact'] . "</td>
        <td>" . date('M d, Y', strtotime($row['check_in'])) . "</td>
        <td>" . date('M d, Y', strtotime($row['check_out'])) . "</td>
        <td>" . ucfirst($row['payment_option']) . "</td>
        <td>" . $row['payment_method'] . "</td>
        <td>" . $row['status'] . "</td>
        <td>₱" . number_format($row['total_amount'], 2) . "</td>
    </tr>";
}

echo "<tr class='total-row'>
        <td colspan='8' align='right'>Total Amount:</td>
        <td>₱" . number_format($totalAmount, 2) . "</td>
    </tr>
</table>

<table style='margin-top: 20px;'>
    <tr>
        <td colspan='9' class='header'>REPORT INFORMATION</td>
    </tr>
    <tr>
        <td colspan='4'>Generated Date:</td>
        <td colspan='5'>" . date('F j, Y h:i:s A') . "</td>
    </tr>
    <tr>
        <td colspan='4'>Generated By:</td>
        <td colspan='5'>Administrator</td>
    </tr>
</table>

</body>
</html>"; 