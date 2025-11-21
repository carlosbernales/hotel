-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 05:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `casadbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `name`, `icon`) VALUES
(1, 'Air Conditioning', 'fa-snowflake-o'),
(2, 'Private Bathroom', 'fa-bath'),
(3, 'Flat-screen TV', 'fa-television'),
(4, 'Free WiFi', 'fa-wifi'),
(5, 'Hot Shower', 'fa-shower');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `arrival_time` time NOT NULL,
  `payment_option` enum('full','downpayment') NOT NULL,
  `payment_method` enum('cash','gcash','maya') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `userid`, `first_name`, `last_name`, `check_in`, `check_out`, `contact_number`, `email`, `number_of_guests`, `arrival_time`, `payment_option`, `payment_method`, `total_price`, `status`, `created_at`) VALUES
(1, 1, '', '', '2025-01-28', '2025-01-31', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:36:52'),
(2, 1, '', '', '2025-01-28', '2025-01-31', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:37:28'),
(3, 1, '', '', '2025-01-28', '2025-01-31', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:38:07'),
(4, 1, '', '', '2025-01-28', '2025-01-31', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:43:05'),
(5, 1, '', '', '2025-01-28', '2025-01-30', '9787654234', 'Christianrealisan@gmail.com', 1, '00:57:00', 'downpayment', 'gcash', 14800.00, 'pending', '2025-01-28 08:58:04'),
(6, 1, '', '', '2025-01-28', '2025-01-31', '8554433546', 'Christianrealisan@gamail.com', 1, '01:13:00', 'downpayment', 'cash', 9600.00, 'pending', '2025-01-28 09:13:34'),
(7, 1, '', '', '2025-01-28', '2025-01-30', '8554433546', 'Christianrealisan@gamail.com', 1, '12:46:00', 'downpayment', 'gcash', 8400.00, 'pending', '2025-01-28 20:46:50'),
(8, 1, 'Christian', 'Realisan', '2025-02-08', '2025-02-10', '01429589458435', 'chano@gmail.com', 2, '00:46:00', 'full', 'cash', 6400.00, 'pending', '2025-02-08 08:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `reservation_date` date NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `remaining_balance` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `user_id`, `package_name`, `package_price`, `reservation_date`, `number_of_guests`, `start_time`, `end_time`, `duration_hours`, `payment_method`, `payment_type`, `total_amount`, `paid_amount`, `remaining_balance`, `booking_status`, `created_at`) VALUES
(2, 1, 'Premium Package', 55000.00, '2025-01-27', 45, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 'cash', 'full', 55000.00, 55000.00, 0.00, 'pending', '2025-01-26 17:14:25'),
(3, 1, 'Premium Package', 55000.00, '2025-01-27', 34, '2025-01-26 17:21:00', '2025-01-26 22:21:00', 5, 'gcash', 'downpayment', 55000.00, 27500.00, 27500.00, 'pending', '2025-01-26 17:21:32'),
(4, 1, 'Premium Package', 55000.00, '2025-01-27', 33, '2025-01-26 17:34:00', '2025-01-26 22:34:00', 5, 'gcash', 'downpayment', 55000.00, 27500.00, 27500.00, 'pending', '2025-01-26 17:34:27'),
(5, 1, 'Package A', 47500.00, '2025-01-27', 22, '2025-01-26 17:35:00', '2025-01-26 16:35:00', -1, 'cash', 'downpayment', 47500.00, 23750.00, 23750.00, 'pending', '2025-01-26 17:35:23'),
(6, 1, 'Venue Rental Only', 20000.00, '2025-01-27', 22, '2025-01-26 17:44:00', '2025-01-26 23:44:00', 6, 'gcash', 'full', 20000.00, 20000.00, 0.00, 'pending', '2025-01-26 17:45:01'),
(7, 1, 'Premium Package', 55000.00, '2025-01-28', 43, '2025-01-28 13:49:00', '2025-01-28 17:49:00', 4, 'cash', 'full', 55000.00, 55000.00, 0.00, 'pending', '2025-01-28 12:49:57'),
(8, 1, 'Package A', 47500.00, '2025-02-08', 7, '2025-02-08 13:37:00', '2025-02-08 19:37:00', 6, 'cash', 'full', 47500.00, 47500.00, 0.00, 'pending', '2025-02-08 13:37:39');

-- --------------------------------------------------------

--
-- Table structure for table `guest_names`
--

CREATE TABLE `guest_names` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `guest_name`, `created_at`) VALUES
(1, 1, 'QQQ', '2025-01-28 08:36:52'),
(2, 2, 'QQQ', '2025-01-28 08:37:28'),
(3, 2, 'QQQ', '2025-01-28 08:37:28'),
(4, 3, 'QQQ', '2025-01-28 08:38:07'),
(5, 3, 'QQQ', '2025-01-28 08:38:07'),
(6, 3, 'QQQ', '2025-01-28 08:38:07'),
(7, 3, 'QQQ', '2025-01-28 08:38:07'),
(8, 4, 'QQQ', '2025-01-28 08:43:05'),
(9, 4, 'QQQ', '2025-01-28 08:43:05'),
(10, 4, 'QQQ', '2025-01-28 08:43:05'),
(11, 4, 'QQQ', '2025-01-28 08:43:05'),
(12, 4, 'QQQ', '2025-01-28 08:43:05'),
(13, 4, 'QQQ', '2025-01-28 08:43:05'),
(14, 4, 'QQQ', '2025-01-28 08:43:05'),
(15, 4, 'QQQ', '2025-01-28 08:43:05'),
(16, 5, 'Pogi ', '2025-01-28 08:58:04'),
(17, 6, 'Pogi ', '2025-01-28 09:13:34'),
(18, 7, 'Good Aftie', '2025-01-28 20:46:50'),
(19, 8, 'Christian', '2025-02-08 08:46:23'),
(20, 8, 'Chano', '2025-02-08 08:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('full','downpayment') NOT NULL,
  `payment_method` enum('cash','gcash','maya') NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_type`, `payment_method`, `payment_status`, `payment_date`, `created_at`) VALUES
(1, 1, 4800.00, 'downpayment', 'gcash', 'pending', '2025-01-28 08:36:52', '2025-01-28 08:36:52'),
(2, 2, 4800.00, 'downpayment', 'gcash', 'pending', '2025-01-28 08:37:28', '2025-01-28 08:37:28'),
(3, 3, 4800.00, 'downpayment', 'gcash', 'pending', '2025-01-28 08:38:07', '2025-01-28 08:38:07'),
(4, 4, 4800.00, 'downpayment', 'gcash', 'pending', '2025-01-28 08:43:05', '2025-01-28 08:43:05'),
(5, 5, 7400.00, 'downpayment', 'gcash', 'pending', '2025-01-28 08:58:04', '2025-01-28 08:58:04'),
(6, 6, 4800.00, 'downpayment', 'cash', 'pending', '2025-01-28 09:13:34', '2025-01-28 09:13:34'),
(7, 7, 4200.00, 'downpayment', 'gcash', 'pending', '2025-01-28 20:46:50', '2025-01-28 20:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_type_id`, `room_number`, `status`) VALUES
(1, 1, 'R001', 'available'),
(2, 1, 'R002', 'available'),
(3, 2, 'R003', 'available'),
(4, 2, 'R004', 'available'),
(5, 3, 'R005', 'available'),
(6, 3, 'R006', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_price` decimal(10,2) NOT NULL,
  `room_quantity` int(11) NOT NULL,
  `number_of_days` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `booking_id`, `room_name`, `room_price`, `room_quantity`, `number_of_days`, `subtotal`, `created_at`) VALUES
(1, 1, 'Standard Double Room', 3200.00, 1, 3, 9600.00, '2025-01-28 08:36:52'),
(2, 2, 'Standard Double Room', 3200.00, 1, 3, 9600.00, '2025-01-28 08:37:28'),
(3, 3, 'Standard Double Room', 3200.00, 1, 3, 9600.00, '2025-01-28 08:38:07'),
(4, 4, 'Standard Double Room', 3200.00, 1, 3, 9600.00, '2025-01-28 08:43:05'),
(5, 5, 'Standard Double Room', 3200.00, 1, 2, 6400.00, '2025-01-28 08:58:04'),
(6, 5, 'Family Room', 4200.00, 1, 2, 8400.00, '2025-01-28 08:58:04'),
(7, 6, 'Standard Double Room', 3200.00, 1, 3, 9600.00, '2025-01-28 09:13:34'),
(8, 7, 'Family Room', 4200.00, 1, 2, 8400.00, '2025-01-28 20:46:50'),
(9, 8, 'Standard Double Room', 3200.00, 1, 2, 6400.00, '2025-02-08 08:46:23');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `room_type_id` int(11) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `beds` varchar(100) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`) VALUES
(1, 'Standard Double Room', 1500.00, 2, 'Cozy and comfortable, our standard room comes with 2 single beds, ideal for friends or business travelers.', '2 Single Beds', 4.5, 'img/double.jpg'),
(2, 'Deluxe Family Room', 2000.00, 4, 'Our deluxe room offers a queen bed and a single bed, perfect for small families or groups.', '1 Queen Bed, 1 Single Bed', 4.8, 'img/4.jpg'),
(3, 'Family Room', 2500.00, 5, 'Perfect for families, this spacious room features 1 queen bed and 2 single beds.', '1 Queen Bed, 2 Single Beds', 5.0, 'img/5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type_amenities`
--

INSERT INTO `room_type_amenities` (`room_type_id`, `amenity_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_verified`, `verification_code`, `verification_expiry`, `created_at`, `updated_at`) VALUES
(1, 'christian realisan', 'chano@gmail.com', '$2y$10$xNsDtwPC8txQad8WYZp0POGrzstBWrxF637jbqLV7riR841mL0o9O', 0, '587673', '2025-01-28 23:10:24', '2025-01-25 00:05:53', '2025-01-28 22:08:24'),
(2, 'christian realisan', 'chanoa@gmail.com', '$2y$10$w3lS.Bc0IKC4hhLEn5HrWeSIf3a5SdKC9liaTwUvsj97e9JtZTFKi', 0, '829032', '2025-01-25 23:10:12', '2025-01-25 22:08:12', '2025-01-25 22:08:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`room_type_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD CONSTRAINT `event_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD CONSTRAINT `guest_names_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD CONSTRAINT `room_bookings_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `room_type_amenities_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`),
  ADD CONSTRAINT `room_type_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
