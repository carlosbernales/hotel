<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "hotelms"; // Make sure this matches your database name

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($con, "utf8");

// Debug connection
error_log("Database connected successfully to: " . $database);

// Create facility_categories table if it doesn't exist
$sql_categories = "CREATE TABLE IF NOT EXISTS facility_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!mysqli_multi_query($con, $sql_categories)) {
    die("Error creating facility_categories table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Create facilities table if it doesn't exist
$sql_facilities = "CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES facility_categories(id)
)";

if (!mysqli_multi_query($con, $sql_facilities)) {
    die("Error creating facilities table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Create table_bookings table if it doesn't exist
$sql_create_table = "CREATE TABLE IF NOT EXISTS table_bookings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    package_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    num_guests INT(11) NOT NULL,
    special_requests TEXT,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    package_type VARCHAR(50),
    payment_reference VARCHAR(100),
    payment_proof VARCHAR(255),
    cancellation_reason TEXT,
    cancelled_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_option VARCHAR(50),
    amount_to_pay VARCHAR(50),
    name VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_multi_query($con, $sql_create_table)) {
    error_log("Error creating table_bookings table: " . mysqli_error($con));
    die("Error creating table_bookings table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Create table_packages table
$sql = "CREATE TABLE IF NOT EXISTS table_packages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(255) NOT NULL,
    capacity VARCHAR(50) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    available_tables INT(11) DEFAULT 1
)";

if (!mysqli_multi_query($con, $sql)) {
    die("Error creating table_packages table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Check if table is empty before inserting default packages
$check_empty = mysqli_query($con, "SELECT COUNT(*) as count FROM table_packages");
$row = mysqli_fetch_assoc($check_empty);

if ($row['count'] == 0) {
    $insert_sql = "INSERT INTO table_packages (package_name, capacity, description, available_tables) VALUES 
        ('Couple', '2 persons', 'Perfect for intimate dining', 5),
        ('Friends', '3-4 persons', 'Ideal for small groups and friends gathering', 8),
        ('Family', '7-10 persons', 'Spacious setting for family gatherings', 6),
        ('Family Table', '7 persons', 'Comfortable dining for medium-sized families', 4)";

    if (!mysqli_multi_query($con, $insert_sql)) {
        die("Error inserting default packages: " . mysqli_error($con));
    }
    // Drain multi_query results
    while (mysqli_more_results($con) && mysqli_next_result($con)) {
        ;
    }
}

// Create contact_info table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS contact_info (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    icon_class VARCHAR(50) NOT NULL,
    display_text VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    is_external TINYINT(1) DEFAULT 0,
    display_order INT(11) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_multi_query($con, $sql)) {
    die("Error creating contact_info table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Set timezone
date_default_timezone_set('Asia/Manila');

// Create room_types table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS room_types (
    room_type_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    room_type VARCHAR(100) NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10,2) NOT NULL,
    capacity INT(11) NOT NULL,
    available_rooms INT(11) NOT NULL DEFAULT 0,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_multi_query($con, $sql)) {
    die("Error creating room_types table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}

// Create room_numbers table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS room_numbers (
    room_number_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT(11) NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    status ENUM('active', 'maintenance', 'booked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_multi_query($con, $sql)) {
    die("Error creating room_numbers table: " . mysqli_error($con));
}
// Drain multi_query results
while (mysqli_more_results($con) && mysqli_next_result($con)) {
    ;
}


?>