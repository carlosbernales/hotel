<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create bookings table with the correct schema
$create_bookings = "CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    booking_type VARCHAR(20) DEFAULT 'Online',
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    arrival_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    number_of_guests INT NOT NULL,
    room_type_id INT NOT NULL,
    payment_option VARCHAR(20) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    extra_charges DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'pending',
    nights INT NOT NULL,
    downpayment_amount DECIMAL(10,2) NOT NULL,
    remaining_balance DECIMAL(10,2) DEFAULT 0.00,
    discount_type VARCHAR(50),
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_percentage DECIMAL(5,2) DEFAULT 0.00,
    payment_reference VARCHAR(100),
    payment_proof VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_bookings)) {
    echo "Bookings table created/updated successfully<br>";
} else {
    echo "Error creating/updating bookings table: " . mysqli_error($con) . "<br>";
}

// Create room_bookings table
$create_room_bookings = "CREATE TABLE IF NOT EXISTS room_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    room_type_id INT NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    room_price DECIMAL(10,2) NOT NULL,
    room_quantity INT DEFAULT 1,
    number_of_days INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guest_count INT NOT NULL,
    extra_guest_fee DECIMAL(10,2) DEFAULT 0.00,
    number_of_nights INT NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
)";

if (mysqli_query($con, $create_room_bookings)) {
    echo "Room bookings table created/updated successfully<br>";
} else {
    echo "Error creating/updating room bookings table: " . mysqli_error($con) . "<br>";
}

// Create guest_names table
$create_guest_names = "CREATE TABLE IF NOT EXISTS guest_names (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
)";

if (mysqli_query($con, $create_guest_names)) {
    echo "Guest names table created/updated successfully<br>";
} else {
    echo "Error creating/updating guest names table: " . mysqli_error($con) . "<br>";
}

// Create notifications table
$create_notifications = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_notifications)) {
    echo "Notifications table created/updated successfully<br>";
} else {
    echo "Error creating/updating notifications table: " . mysqli_error($con) . "<br>";
}

// Create rooms table if it doesn't exist
$create_rooms = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    total_rooms INT NOT NULL DEFAULT 1,
    available_rooms INT NOT NULL DEFAULT 1,
    status VARCHAR(20) DEFAULT 'Available'
)";

if (mysqli_query($con, $create_rooms)) {
    echo "Rooms table created/updated successfully<br>";
} else {
    echo "Error creating/updating rooms table: " . mysqli_error($con) . "<br>";
}

// Create room_types table if it doesn't exist
$create_room_types = "CREATE TABLE IF NOT EXISTS room_types (
    room_type_id INT AUTO_INCREMENT PRIMARY KEY,
    room_type VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    beds INT NOT NULL,
    rating DECIMAL(3,1) DEFAULT 5.0,
    image VARCHAR(255),
    available_rooms INT NOT NULL DEFAULT 1,
    total_rooms INT NOT NULL DEFAULT 1
)";

if (mysqli_query($con, $create_room_types)) {
    echo "Room types table created/updated successfully<br>";
    
    // Update existing room types with default values if they haven't been set
    $update_rooms = "UPDATE room_types 
                    SET available_rooms = total_rooms 
                    WHERE available_rooms IS NULL OR available_rooms = 0";
    
    if (mysqli_query($con, $update_rooms)) {
        echo "Updated available_rooms for existing room types<br>";
    } else {
        echo "Error updating available_rooms: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "Error creating/updating room types table: " . mysqli_error($con) . "<br>";
}

// Create discount_types table if it doesn't exist
$create_discount_types = "CREATE TABLE IF NOT EXISTS discount_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_discount_types)) {
    echo "Discount types table created/updated successfully<br>";
} else {
    echo "Error creating/updating discount types table: " . mysqli_error($con) . "<br>";
}

// Insert default discount types if they don't exist
$insert_discounts = "INSERT IGNORE INTO discount_types (name, percentage, description) VALUES
    ('senior', 10.00, 'Senior Citizen Discount'),
    ('pwd', 10.00, 'PWD Discount'),
    ('student', 10.00, 'Student Discount')";

if (mysqli_query($con, $insert_discounts)) {
    echo "Default discount types inserted successfully<br>";
} else {
    echo "Error inserting default discount types: " . mysqli_error($con) . "<br>";
}

echo "<br>Database setup completed. Please refresh your browser and try booking again.";
?> 