-- Create tables for Front Desk Sales
CREATE TABLE IF NOT EXISTS `guests` (
    `guest_id` INT PRIMARY KEY AUTO_INCREMENT,
    `first_name` VARCHAR(50),
    `last_name` VARCHAR(50),
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `room_types` (
    `room_type_id` INT PRIMARY KEY AUTO_INCREMENT,
    `room_type_name` VARCHAR(50),
    `rate` DECIMAL(10,2),
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `rooms` (
    `room_id` INT PRIMARY KEY AUTO_INCREMENT,
    `room_number` VARCHAR(10),
    `room_type_id` INT,
    `status` ENUM('Available', 'Occupied', 'Maintenance') DEFAULT 'Available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
);

CREATE TABLE IF NOT EXISTS `booking` (
    `booking_id` INT PRIMARY KEY AUTO_INCREMENT,
    `guest_id` INT,
    `room_id` INT,
    `check_in` DATETIME,
    `check_out` DATETIME,
    `total_price` DECIMAL(10,2),
    `payment_status` ENUM('Pending', 'Paid', 'Cancelled') DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guest_id) REFERENCES guests(guest_id),
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

-- Create tables for Cashier Sales
CREATE TABLE IF NOT EXISTS `items` (
    `item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `item_name` VARCHAR(100),
    `category` VARCHAR(50),
    `price` DECIMAL(10,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_date` DATETIME,
    `order_type` ENUM('café', 'room_service'),
    `total_amount` DECIMAL(10,2),
    `payment_method` VARCHAR(20),
    `status` ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `order_items` (
    `order_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT,
    `item_id` INT,
    `quantity` INT,
    `price` DECIMAL(10,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id)
);

-- Insert sample data for testing
INSERT INTO `room_types` (`room_type_name`, `rate`, `description`) VALUES
('Standard', 2500.00, 'Standard room with basic amenities'),
('Deluxe', 3500.00, 'Deluxe room with additional amenities'),
('Suite', 5000.00, 'Luxury suite with all amenities');

INSERT INTO `rooms` (`room_number`, `room_type_id`) VALUES
('101', 1), ('102', 1), ('103', 1),
('201', 2), ('202', 2),
('301', 3);

INSERT INTO `items` (`item_name`, `category`, `price`) VALUES
('Americano', 'Beverages', 120.00),
('Cappuccino', 'Beverages', 140.00),
('Club Sandwich', 'Food', 180.00),
('Caesar Salad', 'Food', 220.00),
('Chocolate Cake', 'Desserts', 150.00);

-- Add indexes for better performance
ALTER TABLE `booking` ADD INDEX `idx_check_in` (`check_in`);
ALTER TABLE `booking` ADD INDEX `idx_check_out` (`check_out`);
ALTER TABLE `orders` ADD INDEX `idx_order_date` (`order_date`);
ALTER TABLE `orders` ADD INDEX `idx_order_type` (`order_type`); 