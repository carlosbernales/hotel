-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2025 at 08:56 AM
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
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `checkin` date NOT NULL,
  `checkout` date NOT NULL,
  `message` text DEFAULT NULL,
  `meal` varchar(100) DEFAULT 'None',
  `total_price` decimal(10,2) NOT NULL,
  `time_arrival` time NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_option` enum('downpayment','fullyPaid') NOT NULL,
  `downpayment` decimal(10,2) DEFAULT 0.00,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `userid`, `room_name`, `firstname`, `lastname`, `contact_number`, `address`, `price`, `checkin`, `checkout`, `message`, `meal`, `total_price`, `time_arrival`, `payment_method`, `payment_option`, `downpayment`, `remaining_balance`, `created_at`) VALUES
(1, 5, 'Standard Double Room', '', '', 0, '', 3600.00, '0000-00-00', '0000-00-00', '', 'None', 0.00, '19:26:00', 'maya', 'fullyPaid', 0.00, 0.00, '2024-12-01 07:30:19'),
(32, 9, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-01', '2024-12-05', '', '', 14400.00, '22:10:00', 'Gcash', 'downpayment', 7200.00, 7200.00, '2024-12-02 05:10:43'),
(33, 10, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-30', '2025-01-09', '', '', 36000.00, '16:46:00', 'Gcash', 'downpayment', 18000.00, 18000.00, '2024-12-02 07:47:40'),
(35, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-03', '2024-12-06', '', '', 10800.00, '03:06:00', 'Maya', 'downpayment', 5400.00, 5400.00, '2024-12-03 11:06:25'),
(36, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-03', '2024-12-05', '', '', 7200.00, '00:13:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-03 19:13:51'),
(37, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-03', '2024-12-05', '', '', 7200.00, '00:13:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-03 19:13:51'),
(38, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-03', '2024-12-05', '', '', 7200.00, '11:26:00', 'Gcash', 'downpayment', 3600.00, 3600.00, '2024-12-03 19:24:09'),
(39, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-03', '2024-12-06', '', '', 10800.00, '20:04:00', 'Maya', 'downpayment', 5400.00, 5400.00, '2024-12-04 03:04:48'),
(40, 17, 'Standard Double Room', 'Christian', 'Realisan', 2147483647, 'tawagan calapan city', 3600.00, '2024-12-29', '2025-01-01', '', '', 10800.00, '23:14:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-06 03:14:47'),
(41, 1, 'Standard Double Room', 'Bella', 'Aw aw', 2147483647, 'tawagan calapan city', 3600.00, '2025-01-16', '2025-01-17', '', '', 3600.00, '23:47:00', 'Gcash', 'downpayment', 1800.00, 1800.00, '2025-01-17 07:47:51'),
(42, 1, 'Standard Double Room', 'Bella', 'Aw aw', 2147483647, 'tawagan calapan city', 3600.00, '2025-01-17', '2025-01-23', '', '', 21600.00, '01:58:00', 'None', 'downpayment', 10800.00, 10800.00, '2025-01-17 19:58:32'),
(43, 1, 'Standard Double Room', 'Christian', 'Montenegro', 2147483647, 'tanggol', 3600.00, '2025-01-18', '2025-01-20', '', '', 7200.00, '16:27:00', 'Gcash', 'downpayment', 3600.00, 3600.00, '2025-01-19 00:26:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
