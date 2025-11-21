<?php
require 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Create bookings table
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        contact VARCHAR(20) NOT NULL,
        booking_type VARCHAR(50) DEFAULT 'Online',
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        number_of_guests INT NOT NULL,
        room_type_id INT NOT NULL,
        payment_option VARCHAR(20) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        nights INT NOT NULL,
        downpayment_amount DECIMAL(10,2) NOT NULL,
        remaining_balance DECIMAL(10,2) NOT NULL,
        discount_type VARCHAR(50),
        discount_amount DECIMAL(10,2),
        discount_percentage DECIMAL(5,2),
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating bookings table: " . mysqli_error($con));
    }
    echo "Bookings table created/verified successfully<br>";

    // Create room_bookings table
    $sql = "CREATE TABLE IF NOT EXISTS room_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        room_type_id INT NOT NULL,
        room_name VARCHAR(100) NOT NULL,
        room_price DECIMAL(10,2) NOT NULL,
        number_of_days INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        guest_count INT NOT NULL,
        extra_guest_fee DECIMAL(10,2) DEFAULT 0.00,
        number_of_nights INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating room_bookings table: " . mysqli_error($con));
    }
    echo "Room bookings table created/verified successfully<br>";

    // Create guest_names table
    $sql = "CREATE TABLE IF NOT EXISTS guest_names (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        guest_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating guest_names table: " . mysqli_error($con));
    }
    echo "Guest names table created/verified successfully<br>";

    // Create notifications table
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating notifications table: " . mysqli_error($con));
    }
    echo "Notifications table created/verified successfully<br>";

    // Create rooms table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_type_id INT NOT NULL,
        total_rooms INT NOT NULL DEFAULT 0,
        available_rooms INT NOT NULL DEFAULT 0,
        status VARCHAR(50) DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating rooms table: " . mysqli_error($con));
    }
    echo "Rooms table created/verified successfully<br>";

    // Create room_types table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS room_types (
        room_type_id INT AUTO_INCREMENT PRIMARY KEY,
        room_type VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        beds VARCHAR(100),
        rating DECIMAL(3,1) DEFAULT 5.0,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Error creating room_types table: " . mysqli_error($con));
    }
    echo "Room types table created/verified successfully<br>";

    // Commit transaction
    mysqli_commit($con);
    echo "All tables created/verified successfully!<br>";

    // Now check if tables have data
    $tables = ['bookings', 'room_bookings', 'guest_names', 'notifications', 'rooms', 'room_types'];
    foreach ($tables as $table) {
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM $table");
        $row = mysqli_fetch_assoc($result);
        echo "$table table has {$row['count']} records<br>";
    }

} catch (Exception $e) {
    mysqli_rollback($con);
    echo "Error: " . $e->getMessage();
} finally {
    mysqli_close($con);
}
?>
