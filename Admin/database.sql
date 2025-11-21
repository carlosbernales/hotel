-- Create database if not exists
CREATE DATABASE IF NOT EXISTS hotelms;
USE hotelms;

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    contact VARCHAR(20) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    number_of_guests INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_option VARCHAR(50),
    total_amount DECIMAL(10,2) NOT NULL,
    downpayment_amount DECIMAL(10,2),
    status ENUM('Pending', 'Confirmed', 'Checked Out', 'Archived', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create room_types table
CREATE TABLE IF NOT EXISTS room_types (
    room_type_id INT AUTO_INCREMENT PRIMARY KEY,
    room_type VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create rooms table
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('Available', 'Occupied', 'Maintenance') DEFAULT 'Available',
    available_rooms INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
);

-- Create room_bookings table
CREATE TABLE IF NOT EXISTS room_bookings (
    booking_id INT NOT NULL,
    room_type_id INT NOT NULL,
    room_name VARCHAR(50),
    room_price DECIMAL(10,2) NOT NULL,
    number_of_nights INT NOT NULL,
    payment_option ENUM('Downpayment', 'Full Payment') NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id),
    PRIMARY KEY (booking_id, room_type_id)
);

-- Create table_bookings table
CREATE TABLE IF NOT EXISTS table_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(200) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    number_of_guests INT NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Checked Out', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create event_bookings table
CREATE TABLE IF NOT EXISTS event_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    booking_date DATE NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    num_guests INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    amount_paid DECIMAL(10,2),
    payment_status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample room types
INSERT INTO room_types (room_type, description, price, capacity) VALUES
('Standard Double Room', 'Comfortable room with double bed', 7400.00, 2),
('Deluxe Family Room', 'Spacious room for families', 2000.00, 4),
('Family Room', 'Perfect for family stays', 10000.00, 6),
('Myra lounge', 'Luxury lounge room', 2000.00, 2);

-- Insert sample rooms
INSERT INTO rooms (room_type_id, room_number, available_rooms) VALUES
(1, '101', 5),
(2, '201', 3),
(3, '301', 2),
(4, '401', 2);

-- Add indexes for better performance
ALTER TABLE bookings ADD INDEX idx_status (status);
ALTER TABLE bookings ADD INDEX idx_check_in (check_in);
ALTER TABLE bookings ADD INDEX idx_check_out (check_out);
ALTER TABLE room_bookings ADD INDEX idx_room_type (room_type_id);
ALTER TABLE table_bookings ADD INDEX idx_booking_date (booking_date);
ALTER TABLE event_bookings ADD INDEX idx_booking_date (booking_date);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Insert sample users
INSERT INTO users (firstname, lastname, email, password, role) VALUES
('Admin', 'User', 'admin@casaestela.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Front', 'Desk', 'frontdesk@casaestela.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'frontdesk'),
('Cash', 'ier', 'cashier@casaestela.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier');

-- Insert sample messages
INSERT INTO messages (sender_id, receiver_id, subject, message, is_read) VALUES
(1, 2, 'Welcome to Front Desk', 'Welcome to the team! Please review the standard operating procedures.', 0),
(1, 3, 'Cash Register Setup', 'Please ensure all cash registers are properly configured for the new day.', 0),
(2, 1, 'Daily Report', 'Here is the daily report for your review.', 1),
(3, 1, 'Cash Count', 'Daily cash count completed and verified.', 1); 