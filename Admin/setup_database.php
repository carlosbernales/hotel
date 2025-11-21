<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create bookings table
$create_bookings = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    contact VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    arrival_time TIME NOT NULL,
    num_guests INT NOT NULL,
    payment_option VARCHAR(20) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_bookings)) {
    echo "Bookings table created successfully<br>";
} else {
    echo "Error creating bookings table: " . mysqli_error($con) . "<br>";
}

// Create booking_guests table
$create_guests = "CREATE TABLE IF NOT EXISTS booking_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
)";

if (mysqli_query($con, $create_guests)) {
    echo "Booking guests table created successfully<br>";
} else {
    echo "Error creating booking guests table: " . mysqli_error($con) . "<br>";
}

// Create table_packages table
$create_packages = "CREATE TABLE IF NOT EXISTS table_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_type VARCHAR(50) NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_packages)) {
    echo "Table packages table created successfully<br>";
} else {
    echo "Error creating table packages table: " . mysqli_error($con) . "<br>";
}

// Create table_bookings table
$create_table_bookings = "CREATE TABLE IF NOT EXISTS table_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE,
    package_type VARCHAR(50),
    customer_name VARCHAR(100),
    booking_date DATE,
    booking_time TIME,
    num_guests INT,
    payment_method VARCHAR(50),
    total_amount DECIMAL(10,2),
    amount_paid DECIMAL(10,2),
    change_amount DECIMAL(10,2),
    special_requests TEXT,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_table_bookings)) {
    echo "Table bookings table created successfully<br>";
} else {
    echo "Error creating table bookings table: " . mysqli_error($con) . "<br>";
}

// Check if tables exist
$tables_result = mysqli_query($con, "SHOW TABLES");
echo "<br>Tables in database:<br>";
while ($table = mysqli_fetch_array($tables_result)) {
    echo "- " . $table[0] . "<br>";
    
    // Show table structure
    $desc_result = mysqli_query($con, "DESCRIBE " . $table[0]);
    echo "Structure:<br>";
    while ($row = mysqli_fetch_assoc($desc_result)) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $row['Field'] . " - " . $row['Type'] . "<br>";
    }
    echo "<br>";
}

// Check for any existing bookings
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM bookings");
$row = mysqli_fetch_assoc($result);
echo "Number of existing bookings: " . $row['count'] . "<br>";
?>
