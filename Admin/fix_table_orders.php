<?php
// Script to fix table bookings that don't have corresponding orders

// Check if this file is being accessed directly or included
$is_direct_access = (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__));

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'fix_table_orders.log');

// Function to log messages
function logMessage($message) {
    global $is_direct_access;
    
    // Always log to the error log
    error_log($message);
    
    // Only output to browser if direct access or being included by our tool
    if ($is_direct_access || (isset($_POST['run_fix']) && basename($_SERVER['SCRIPT_FILENAME']) === 'run_table_orders_fix.php')) {
        echo $message . "<br>";
    }
}

// Add basic HTML structure if directly accessed
if ($is_direct_access) {
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Fix Table Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow: auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .button { display: inline-block; background: #0275d8; color: white; padding: 10px 15px; 
                 text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>';
}

// Check if we are in a session and if not, start one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'db.php';

logMessage("Starting fix_table_orders script");

// First, check if the orders table exists
$check_table_sql = "SHOW TABLES LIKE 'orders'";
$table_result = mysqli_query($con, $check_table_sql);

if (!$table_result || mysqli_num_rows($table_result) == 0) {
    logMessage("The orders table does not exist. Please create it first.");
    
    if ($is_direct_access) {
        echo '</body></html>';
    }
    
    exit;
}

// Get all table bookings
$get_bookings_sql = "SELECT * FROM table_bookings";
$bookings_result = mysqli_query($con, $get_bookings_sql);

if (!$bookings_result) {
    logMessage("Error fetching table bookings: " . mysqli_error($con));
    
    if ($is_direct_access) {
        echo '</body></html>';
    }
    
    exit;
}

$counter = [
    'checked' => 0,
    'fixed' => 0,
    'errors' => 0,
    'skipped' => 0,
];

// Process each booking
while ($booking = mysqli_fetch_assoc($bookings_result)) {
    $counter['checked']++;
    $booking_id = $booking['id'];
    
    // Check if this booking already has an order
    $check_order_sql = "SELECT * FROM orders WHERE table_id = $booking_id AND order_type = 'advance'";
    $order_result = mysqli_query($con, $check_order_sql);
    
    if (!$order_result) {
        logMessage("Error checking order for booking #$booking_id: " . mysqli_error($con));
        $counter['errors']++;
        continue;
    }
    
    // If order already exists, skip
    if (mysqli_num_rows($order_result) > 0) {
        logMessage("Booking #$booking_id already has an order entry. Skipping.");
        $counter['skipped']++;
        continue;
    }
    
    // Create a new order entry
    try {
        // Prepare the data
        $customer_name = mysqli_real_escape_string($con, $booking['customer_name'] ?? $booking['name'] ?? '');
        $contact_number = mysqli_real_escape_string($con, $booking['contact_number'] ?? '');
        
        // Format contact number for the database
        $contactNumberInt = preg_replace('/[^0-9]/', '', $contact_number);
        if (strlen($contactNumberInt) > 10) {
            $contactNumberInt = substr($contactNumberInt, 0, 10);
        }
        
        $user_id = isset($booking['user_id']) ? intval($booking['user_id']) : 0;
        $payment_method = mysqli_real_escape_string($con, $booking['payment_method'] ?? 'unknown');
        $total_amount = floatval($booking['total_amount'] ?? 0);
        $amount_paid = floatval($booking['amount_paid'] ?? $booking['amount_to_pay'] ?? 0);
        $change_amount = floatval($booking['change_amount'] ?? 0);
        
        // Convert booking date to order date format if needed
        $order_date = date('Y-m-d H:i:s', strtotime($booking['created_at'] ?? date('Y-m-d H:i:s')));
        
        // Set the status based on booking status
        $status = 'pending';
        if (isset($booking['status'])) {
            if (strtolower($booking['status']) == 'confirmed') {
                $status = 'confirmed';
            } elseif (strtolower($booking['status']) == 'cancelled') {
                $status = 'cancelled';
            } elseif (strtolower($booking['status']) == 'completed') {
                $status = 'finished';
            }
        }
        
        // Insert the order
        $insert_order_sql = "INSERT INTO orders (
            customer_name, contact_number, user_id, table_id,
            order_date, status, payment_method, total_amount,
            amount_paid, change_amount, order_type
        ) VALUES (
            '$customer_name', '$contactNumberInt', $user_id, $booking_id,
            '$order_date', '$status', '$payment_method', $total_amount,
            $amount_paid, $change_amount, 'advance'
        )";
        
        $insert_result = mysqli_query($con, $insert_order_sql);
        
        if ($insert_result) {
            $order_id = mysqli_insert_id($con);
            logMessage("Created order #$order_id for table booking #$booking_id");
            $counter['fixed']++;
        } else {
            logMessage("Error creating order for booking #$booking_id: " . mysqli_error($con));
            $counter['errors']++;
        }
    } catch (Exception $e) {
        logMessage("Exception while processing booking #$booking_id: " . $e->getMessage());
        $counter['errors']++;
    }
}

// Summary report
logMessage("Fix Table Orders Summary:");
logMessage("Checked: " . $counter['checked'] . " bookings");
logMessage("Fixed: " . $counter['fixed'] . " bookings");
logMessage("Skipped: " . $counter['skipped'] . " bookings (already had orders)");
logMessage("Errors: " . $counter['errors'] . " bookings");
logMessage("Fix table orders script completed");

// Close database connection
mysqli_close($con);

// Add closing HTML if directly accessed
if ($is_direct_access) {
    echo '<p><a href="index.php" class="button">Return to Dashboard</a></p>';
    echo '</body></html>';
}
?>

<?php if (!$is_direct_access): ?>
<!-- CSS for when included -->
<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
}
pre {
    background: #f5f5f5;
    padding: 10px;
    border: 1px solid #ddd;
    overflow: auto;
}
.success {
    color: green;
    font-weight: bold;
}
.error {
    color: red;
    font-weight: bold;
}
</style>
<?php endif; ?> 