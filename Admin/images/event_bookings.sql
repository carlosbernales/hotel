-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2025 at 09:17 AM
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
(6, 1, 'Venue Rental Only', 20000.00, '2025-01-27', 22, '2025-01-26 17:44:00', '2025-01-26 23:44:00', 6, 'gcash', 'full', 20000.00, 20000.00, 0.00, 'pending', '2025-01-26 17:45:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD CONSTRAINT `event_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
