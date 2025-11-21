-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2025 at 11:11 PM
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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `name` varchar(255) NOT NULL,
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

INSERT INTO `bookings` (`booking_id`, `userid`, `check_in`, `check_out`, `name`, `contact_number`, `email`, `number_of_guests`, `arrival_time`, `payment_option`, `payment_method`, `total_price`, `status`, `created_at`) VALUES
(1, 1, '2025-01-28', '2025-01-31', 'number_of_guests', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:36:52'),
(2, 1, '2025-01-28', '2025-01-31', 'number_of_guests', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:37:28'),
(3, 1, '2025-01-28', '2025-01-31', 'number_of_guests', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:38:07'),
(4, 1, '2025-01-28', '2025-01-31', 'number_of_guests', '01429589458435', 'christubn@gmail.com', 1, '00:36:00', 'downpayment', 'gcash', 9600.00, 'pending', '2025-01-28 08:43:05'),
(5, 1, '2025-01-28', '2025-01-30', 'Chano', '9787654234', 'Christianrealisan@gmail.com', 1, '00:57:00', 'downpayment', 'gcash', 14800.00, 'pending', '2025-01-28 08:58:04'),
(6, 1, '2025-01-28', '2025-01-31', 'Christian', '8554433546', 'Christianrealisan@gamail.com', 1, '01:13:00', 'downpayment', 'cash', 9600.00, 'pending', '2025-01-28 09:13:34'),
(7, 1, '2025-01-28', '2025-01-30', 'Christian', '8554433546', 'Christianrealisan@gamail.com', 1, '12:46:00', 'downpayment', 'gcash', 8400.00, 'pending', '2025-01-28 20:46:50');

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
(7, 1, 'Premium Package', 55000.00, '2025-01-28', 43, '2025-01-28 13:49:00', '2025-01-28 17:49:00', 4, 'cash', 'full', 55000.00, 55000.00, 0.00, 'pending', '2025-01-28 12:49:57');

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
(18, 7, 'Good Aftie', '2025-01-28 20:46:50');

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
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `beds` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `availability_status` enum('available','not_available') DEFAULT 'available',
  `rooms_left` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `price`, `beds`, `description`, `max_capacity`, `room_number`, `availability_status`, `rooms_left`, `created_at`) VALUES
(1, 'Standard Double Room', 3200.00, '2 Single Beds', 'The double room features air conditioning, tumble dryer, a private bathroom, a shower, and a bidet. This room has tiled floors and a flat-screen TV.', 2, '101', 'available', 3, '2025-01-28 19:59:34'),
(2, 'Family Room', 4200.00, '1 Queen Bed + 2 Single Beds', 'Spacious family room perfect for groups or families. Features modern amenities, comfortable beds, and all the essentials for a pleasant stay.', 4, '102', 'available', 1, '2025-01-28 19:59:34'),
(3, 'Deluxe Family Room', 5100.00, '1 King Bed + 2 Single Beds', 'Our premium deluxe family room offers the ultimate comfort for larger families or groups. Featuring upgraded amenities, more space, and a beautiful city view.', 5, '103', 'not_available', 0, '2025-01-28 19:59:34');

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
(8, 7, 'Family Room', 4200.00, 1, 2, 8400.00, '2025-01-28 20:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `feature_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`id`, `room_id`, `feature_name`) VALUES
(1, 1, 'Shower'),
(2, 1, 'Toilet'),
(3, 1, 'Desk'),
(4, 1, 'Flat-screen TV'),
(5, 1, 'Tile/marble floor'),
(6, 1, 'Air conditioning'),
(7, 1, 'Free bottled water'),
(8, 2, 'Shower and bathtub'),
(9, 2, 'Toilet'),
(10, 2, 'Mini fridge'),
(11, 2, 'Flat-screen TV'),
(12, 2, 'Tile/marble floor'),
(13, 2, 'Air conditioning'),
(14, 2, 'Free bottled water'),
(15, 2, 'Coffee/tea maker'),
(16, 3, 'Luxury shower and bathtub'),
(17, 3, 'Separate sitting area'),
(18, 3, 'Mini fridge'),
(19, 3, '50-inch Flat-screen TV'),
(20, 3, 'Premium tile/marble floor'),
(21, 3, 'Climate control'),
(22, 3, 'Free bottled water'),
(23, 3, 'Coffee/tea maker'),
(24, 3, 'City view');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_path`, `display_order`) VALUES
(1, 1, 'images/double.jpg', 1),
(2, 1, 'images/3.jpg', 2),
(3, 1, 'images/4.jpg', 3),
(4, 2, 'images/4.jpg', 1),
(5, 2, 'images/double.jpg', 2),
(6, 2, 'images/3.jpg', 3),
(7, 3, 'images/3.jpg', 1),
(8, 3, 'images/4.jpg', 2),
(9, 3, 'images/double.jpg', 3);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD CONSTRAINT `room_bookings_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `room_features_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
