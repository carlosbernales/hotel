-- Create the advance_table_orders table for table_packages.php
CREATE TABLE IF NOT EXISTS `advance_table_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_booking_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `order_items` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_option` varchar(50) NOT NULL DEFAULT 'full',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `amount_to_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `table_booking_id` (`table_booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 