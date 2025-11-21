<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming request
error_log("Processing event booking request: " . print_r($_POST, true));

// Create event_bookings table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS event_bookings (
    id varchar(20) NOT NULL,
    user_id int(11) NOT NULL,
    customer_name varchar(100) NOT NULL,
    package_name varchar(100) NOT NULL,
    package_price decimal(10,2) NOT NULL,
    event_date date NOT NULL,
    base_price decimal(10,2) NOT NULL,
    overtime_hours int(11) DEFAULT 0,
    overtime_charge decimal(10,2) DEFAULT 0.00,
    extra_guests int(11) DEFAULT 0,
    extra_guest_charge decimal(10,2) DEFAULT 0.00,
    total_amount decimal(10,2) NOT NULL,
    paid_amount decimal(10,2) NOT NULL,
    remaining_balance decimal(10,2) NOT NULL,
    event_type varchar(50) DEFAULT NULL,
    start_time time NOT NULL,
    end_time time NOT NULL,
    number_of_guests int(11) NOT NULL,
    payment_method varchar(50) NOT NULL,
    payment_type varchar(50) NOT NULL,
    reference_number varchar(50) DEFAULT NULL,
    payment_status varchar(255) DEFAULT NULL,
    booking_status varchar(20) DEFAULT 'pending',
    reserve_type varchar(50) DEFAULT 'Regular',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    booking_source varchar(50) DEFAULT 'Regular Booking',
    PRIMARY KEY (id)
)";

if (!mysqli_query($con, $create_table_sql)) {
    error_log("Error creating event_bookings table: " . mysqli_error($con));
    die(json_encode([
        'status' => 'error',
        'message' => 'Database setup error: ' . mysqli_error($con)
    ]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug POST data at the very beginning
        error_log("RAW POST Data: " . print_r($_POST, true));
        
        // Set default values
        $booking_source = 'walk_in';  // Force walk-in for admin module
        $booking_status = 'pending';   // Set default booking status

        // Get and validate customer name
        if (!isset($_POST['customer_name']) || empty(trim($_POST['customer_name']))) {
            error_log("Missing or empty customer_name");
            throw new Exception("Customer name is required");
        }
        $customer_name = trim($_POST['customer_name']);
        error_log("Customer Name received: " . $customer_name);

        // Get and validate package name
        if (!isset($_POST['package_name']) || empty(trim($_POST['package_name']))) {
            error_log("Missing or empty package_name");
            throw new Exception("Package name is required");
        }
        $package_name = trim($_POST['package_name']);
        error_log("Package Name received: " . $package_name);

        // Generate unique ID
        $unique_id = 'EVT' . date('YmdHis') . rand(100, 999);

        // Calculate charges
        $base_price = floatval($_POST['package_price']);
        $overtime_hours = isset($_POST['overtime_hours']) ? intval($_POST['overtime_hours']) : 0;
        $overtime_charge = $overtime_hours * 2000;
        
        $number_of_guests = isset($_POST['number_of_guests']) ? intval($_POST['number_of_guests']) : 0;
        $max_guests = 50;
        $extra_guests = max(0, $number_of_guests - $max_guests);
        $extra_guest_charge = $extra_guests * 1000;
        
        $total_amount = $base_price + $overtime_charge + $extra_guest_charge;

        // Handle payment type and amounts
        $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'Full Payment';
        if ($payment_type === 'Down Payment') {
            $paid_amount = $total_amount * 0.5;
        } else {
            $paid_amount = $total_amount;
        }
        
        $remaining_balance = $total_amount - $paid_amount;

        // Debug all values before insert
        error_log("Values before insert:");
        error_log("ID: " . $unique_id);
        error_log("Customer Name: [" . $customer_name . "]");
        error_log("Package Name: [" . $package_name . "]");
        error_log("Base Price: " . $base_price);
        error_log("Total Amount: " . $total_amount);
        error_log("Paid Amount: " . $paid_amount);
        error_log("Remaining Balance: " . $remaining_balance);

        // Prepare SQL with explicit column names
        $sql = "INSERT INTO event_bookings (
            id, 
            customer_name,
            package_name,
            package_price,
            base_price,
            overtime_hours,
            overtime_charge,
            extra_guests,
            extra_guest_charge,
            total_amount,
            paid_amount,
            remaining_balance,
            event_type,
            start_time,
            end_time,
            number_of_guests,
            payment_method,
            payment_type,
            booking_source,
            booking_status,
            reservation_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare and check for errors
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $con->error);
            throw new Exception("Database prepare error: " . $con->error);
        }

        // Debug types and values before binding
        error_log("Binding parameters with types 'ssdddddiidddsssssssss'");
        error_log("Values in order: " . json_encode([
            $unique_id, $customer_name, $package_name, $base_price, $base_price,
            $overtime_hours, $overtime_charge, $extra_guests, $extra_guest_charge,
            $total_amount, $paid_amount, $remaining_balance, $_POST['event_type'],
            $_POST['start_time'], $_POST['end_time'], $number_of_guests,
            $_POST['payment_method'], $_POST['payment_type'], $booking_source,
            $booking_status, $_POST['event_date']
        ]));

        // Bind parameters with explicit error checking
        $bind_result = $stmt->bind_param(
            'ssdddddiidddsssssssss',
            $unique_id,
            $customer_name,
            $package_name,
            $base_price,
            $base_price,
            $overtime_hours,
            $overtime_charge,
            $extra_guests,
            $extra_guest_charge,
            $total_amount,
            $paid_amount,
            $remaining_balance,
            $_POST['event_type'],
            $_POST['start_time'],
            $_POST['end_time'],
            $number_of_guests,
            $_POST['payment_method'],
            $_POST['payment_type'],
            $booking_source,
            $booking_status,
            $_POST['event_date']
        );

        if (!$bind_result) {
            error_log("Bind failed: " . $stmt->error);
            throw new Exception("Error binding parameters: " . $stmt->error);
        }

        // Execute with error checking
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            throw new Exception("Error executing query: " . $stmt->error);
        }

        // Verify rows were inserted
        if ($stmt->affected_rows <= 0) {
            error_log("No rows were inserted");
            throw new Exception("Failed to insert booking record");
        }

        $stmt->close();

        // Verify the inserted data
        $verify_sql = "SELECT customer_name, package_name FROM event_bookings WHERE id = ?";
        $verify_stmt = $con->prepare($verify_sql);
        $verify_stmt->bind_param('s', $unique_id);
        $verify_stmt->execute();
        $result = $verify_stmt->get_result();
        $inserted_data = $result->fetch_assoc();
        error_log("Verification of inserted data: " . print_r($inserted_data, true));
        $verify_stmt->close();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Event booking submitted successfully! Your booking ID is ' . $unique_id,
            'booking_id' => $unique_id,
            'debug' => [
                'customer_name' => $customer_name,
                'package_name' => $package_name,
                'inserted_data' => $inserted_data,
                'total_amount' => number_format($total_amount, 2),
                'paid_amount' => number_format($paid_amount, 2),
                'remaining_balance' => number_format($remaining_balance, 2)
            ]
        ]);

    } catch (Exception $e) {
        error_log("Booking Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'details' => [
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD']
    ]);
}