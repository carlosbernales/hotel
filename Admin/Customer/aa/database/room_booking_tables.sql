-- Drop existing tables if they exist
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS guest_names;
DROP TABLE IF EXISTS room_bookings;
DROP TABLE IF EXISTS bookings;

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    number_of_guests INT NOT NULL,
    arrival_time TIME NOT NULL,
    payment_option ENUM('full', 'downpayment') NOT NULL,
    payment_method ENUM('cash', 'gcash', 'maya') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    userid INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES users(id)
);

-- Create room_bookings table for individual room entries
CREATE TABLE IF NOT EXISTS room_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    room_name VARCHAR(255) NOT NULL,
    room_price DECIMAL(10,2) NOT NULL,
    room_quantity INT NOT NULL,
    number_of_days INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Create guest_names table for storing multiple guest names
CREATE TABLE IF NOT EXISTS guest_names (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Create payments table for tracking payment details
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('full', 'downpayment') NOT NULL,
    payment_method ENUM('cash', 'gcash', 'maya') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
