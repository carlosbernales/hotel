-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2025 at 08:45 PM
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
-- Database: `casadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `guest_names` text NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival_time` time NOT NULL,
  `payment_option` enum('downpayment','fullypaid') NOT NULL,
  `payment_method` enum('cash','credit_card','online_transfer') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `contact_number`, `address`, `number_of_guests`, `guest_names`, `check_in`, `check_out`, `arrival_time`, `payment_option`, `payment_method`, `total_price`, `created_at`) VALUES
(1, '', '', '', 0, '', '2025-01-20', '2025-01-21', '00:00:00', '', '', 0.00, '2025-01-21 08:47:41'),
(2, '', '', '', 0, '', '2025-01-20', '2025-01-21', '00:00:00', '', '', 0.00, '2025-01-21 08:48:24'),
(3, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:07:20'),
(4, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:07:43'),
(5, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:09:39'),
(6, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:10:32'),
(7, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:14:04'),
(8, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:14:24'),
(9, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:15:30'),
(10, '', '', '', 0, '', '2025-01-21', '2025-01-22', '00:00:00', '', '', 0.00, '2025-01-21 19:17:55'),
(11, '', '', '', 0, '', '2025-01-21', '2025-01-21', '00:00:00', '', '', 0.00, '2025-01-21 19:19:13'),
(12, '', '', '', 0, '', '2025-01-21', '2025-01-21', '00:00:00', '', '', 0.00, '2025-01-21 19:22:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
