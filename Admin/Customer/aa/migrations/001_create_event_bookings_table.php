<?php
require_once 'db_con.php';

try {
    // Create event_bookings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `event_bookings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `reference_number` VARCHAR(50) NOT NULL UNIQUE,
            `user_id` INT DEFAULT NULL,
            `event_date` DATE NOT NULL,
            `event_type` VARCHAR(100) NOT NULL,
            `number_of_guests` INT NOT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
            `payment_method` VARCHAR(50) DEFAULT NULL,
            `payment_reference` VARCHAR(100) DEFAULT NULL,
            `payment_date` DATETIME DEFAULT NULL,
            `payment_type` ENUM('full', 'partial', 'downpayment') DEFAULT 'full',
            `remaining_balance` DECIMAL(10, 2) DEFAULT 0.00,
            `metadata` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_reference` (`reference_number`),
            INDEX `idx_user` (`user_id`),
            INDEX `idx_status` (`payment_status`),
            INDEX `idx_event_date` (`event_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
        // Create payment_transactions table for tracking payment history
        CREATE TABLE IF NOT EXISTS `payment_transactions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `booking_id` INT DEFAULT NULL,
            `reference_number` VARCHAR(50) DEFAULT NULL,
            `transaction_id` VARCHAR(100) NOT NULL,
            `amount` DECIMAL(10, 2) NOT NULL,
            `currency` CHAR(3) DEFAULT 'PHP',
            `payment_method` VARCHAR(50) NOT NULL,
            `status` VARCHAR(50) NOT NULL,
            `payment_type` ENUM('full', 'partial', 'downpayment') DEFAULT 'full',
            `metadata` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_booking` (`booking_id`),
            INDEX `idx_reference` (`reference_number`),
            INDEX `idx_transaction` (`transaction_id`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    echo "Migration completed successfully. Tables created: event_bookings, payment_transactions\n";
    
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
