-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 06:32 AM
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
  `booking_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `guest_names` varchar(255) NOT NULL,
  `arrival_time` time NOT NULL,
  `payment_option` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `check_in`, `check_out`, `name`, `contact_number`, `address`, `number_of_guests`, `guest_names`, `arrival_time`, `payment_option`, `payment_method`, `total_price`, `created_at`, `userid`) VALUES
(17, 2, '2025-01-20', '2025-01-25', 'John Doe', '1234567890', '123 Main Street', 4, 'Guest1,Guest2,Guest3,Guest4', '14:00:00', 'Full Payment', 'Credit Card', 400.00, '2025-01-21 23:41:33', 0),
(18, 4, '2025-01-20', '2025-01-25', 'John Doe', '1234567890', '123 Main Street', 4, 'Guest1,Guest2,Guest3,Guest4', '14:00:00', 'Full Payment', 'Credit Card', 400.00, '2025-01-21 23:41:41', 0),
(19, 5, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'Cgagag', '18:46:00', 'downpayment', 'cash', 4200.00, '2025-01-21 23:47:48', 0),
(20, 8, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 2, 'Chano ,HAHAHA', '16:50:00', 'downpayment', 'credit_card', 20900.00, '2025-01-21 23:49:43', 0),
(21, 9, '2025-01-21', '2025-01-31', 'christian realisan', '987654098765', 'tawagan calapan city', 2, 'Chano ,HAHAHA', '04:00:00', 'downpayment', 'cash', 168000.00, '2025-01-21 23:59:13', 0),
(22, 11, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'HAHAHA', '17:11:00', 'downpayment', 'cash', 14400.00, '2025-01-22 00:11:19', 0),
(23, 13, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'HAHAHA', '17:11:00', 'downpayment', 'cash', 14400.00, '2025-01-22 00:16:02', 0),
(24, 14, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, '', '17:26:00', 'downpayment', 'credit_card', 4200.00, '2025-01-22 00:26:21', 0),
(25, 15, '2025-01-21', '2025-01-23', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'sass', '16:31:00', 'downpayment', 'credit_card', 8400.00, '2025-01-22 00:29:48', 0),
(26, 16, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'QQQQ', '18:30:00', 'downpayment', 'credit_card', 6400.00, '2025-01-22 00:30:58', 0),
(27, 18, '2025-01-21', '2025-01-22', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'weee', '19:18:00', 'downpayment', 'credit_card', 9300.00, '2025-01-22 01:18:58', 0),
(28, 19, '2025-01-22', '2025-01-23', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'QQQ', '18:33:00', 'fullypaid', 'credit_card', 8400.00, '2025-01-23 02:31:51', 0),
(29, 20, '2025-01-22', '2025-01-24', 'christian realisan', '987654098765', 'tawagan calapan city', 1, 'aaa', '22:11:00', 'downpayment', 'online_transfer', 33600.00, '2025-01-23 03:11:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_price` decimal(10,2) NOT NULL,
  `room_quantity` int(11) NOT NULL,
  `remaining_balance` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `room_name`, `room_price`, `room_quantity`, `remaining_balance`, `total_price`) VALUES
(1, 'Room A', 100.00, 1, 400.00, 400.00),
(2, 'Room B', 150.00, 2, 400.00, 400.00),
(3, 'Room A', 100.00, 1, 400.00, 400.00),
(4, 'Room B', 150.00, 2, 400.00, 400.00),
(5, 'Family Room', 4200.00, 1, 4200.00, 4200.00),
(6, 'Standard Double Room', 3200.00, 1, 20900.00, 20900.00),
(7, 'Family Room', 4200.00, 3, 20900.00, 20900.00),
(8, 'Deluxe Family Room', 5100.00, 1, 20900.00, 20900.00),
(9, 'Family Room', 4200.00, 4, 168000.00, 168000.00),
(10, 'Family Room', 4200.00, 1, 14400.00, 14400.00),
(11, 'Deluxe Family Room', 5100.00, 2, 14400.00, 14400.00),
(12, 'Family Room', 4200.00, 1, 14400.00, 14400.00),
(13, 'Deluxe Family Room', 5100.00, 2, 14400.00, 14400.00),
(14, 'Family Room', 4200.00, 1, 4200.00, 4200.00),
(15, 'Family Room', 4200.00, 1, 4200.00, 8400.00),
(16, 'Standard Double Room', 3200.00, 2, 3200.00, 6400.00),
(17, 'Family Room', 4200.00, 1, 4650.00, 9300.00),
(18, 'Deluxe Family Room', 5100.00, 1, 4650.00, 9300.00),
(19, 'Family Room', 4200.00, 2, 8400.00, 8400.00),
(20, 'Family Room', 4200.00, 4, 16800.00, 33600.00);

-- --------------------------------------------------------

--
-- Table structure for table `cafetable`
--

CREATE TABLE `cafetable` (
  `table_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `reservation_date` date NOT NULL,
  `capacity` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `total_price` int(11) NOT NULL,
  `remaining_balance` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cafetable`
--

INSERT INTO `cafetable` (`table_id`, `userid`, `package_name`, `package_price`, `reservation_date`, `capacity`, `start_time`, `end_time`, `payment_method`, `payment_type`, `total_price`, `remaining_balance`, `created_at`, `updated_at`) VALUES
(1, 17, 'Couple', 0.00, '2024-12-05', 11, '19:38:00', '23:39:00', 'GCash', '', 0, NULL, '2024-12-06 03:39:03', '2024-12-06 03:39:03'),
(2, 17, 'Couple', 199.00, '2024-12-05', 11, '19:42:00', '21:42:00', 'GCash', '', 0, NULL, '2024-12-06 03:42:59', '2024-12-06 03:42:59'),
(3, 17, 'Package A', 27999.00, '2024-12-05', 13, '19:43:00', '23:43:00', 'GCash', '', 0, NULL, '2024-12-06 03:43:40', '2024-12-06 03:43:40'),
(4, 17, 'Couple', 199.00, '2024-12-06', 8, '02:16:00', '04:16:00', 'Maya', 'Downpayment', 0, NULL, '2024-12-06 09:16:23', '2024-12-06 09:16:23'),
(5, 17, 'Package B', 32999.00, '2024-12-06', 27, '01:25:00', '05:25:00', 'GCash', 'Downpayment', 0, 16499.50, '2024-12-06 09:25:29', '2024-12-06 09:25:29'),
(6, 17, 'Couple', 199.00, '2024-12-06', 13, '01:58:00', '04:58:00', 'GCash', 'Downpayment', 0, 99.50, '2024-12-06 09:58:57', '2024-12-06 09:58:57'),
(7, 17, 'Package B', 32999.00, '2024-12-06', 50, '04:50:00', '08:32:00', 'GCash', 'Downpayment', 0, 16499.50, '2024-12-06 10:32:23', '2024-12-06 10:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `feedbackid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`feedbackid`, `userid`, `name`, `email`, `message`, `created_at`) VALUES
(30, 2, 'christian realisan', 'cshsdssso@gmail.com', 'i have a concern\r\n', '2024-11-27 18:36:05'),
(31, 2, 'christian realisan', 'chano@gmail.com', 'adadad', '2024-11-27 18:39:01'),
(32, 2, 'christian realisan', 'chano@gmail.com', 'afasdfa', '2024-11-27 18:42:37'),
(33, 2, 'christian realisan', 'chano@gmail.com', 'afdaf', '2024-11-27 18:44:11'),
(34, 2, 'fdfdfdfdsf', 'chano@gmail.com', 'dfsdfsdf', '2024-11-27 18:45:03'),
(35, 2, '', '', '', '2024-11-27 18:47:44'),
(36, 2, '', '', '', '2024-11-27 18:47:50'),
(37, 2, '', '', '', '2024-11-27 18:49:38'),
(38, 2, 'christian realisan', 'cshsdssso@gmail.com', 'sdfdff', '2024-11-27 18:52:54'),
(39, 2, 'christian realisan', 'cshsdssso@gmail.com', 'fddfd', '2024-11-27 18:53:12'),
(40, 2, 'christian realisan', 'cshsdssso@gmail.com', 'iloveyou\r\n', '2024-11-27 18:56:33'),
(41, 2, 'christian realisan', 'chano@gmail.com', 'wrer', '2024-11-27 19:07:33'),
(42, 2, 'christian realisan', 'chano@gmail.com', 'eyyyyyyyyyyyyyyyyyyyy', '2024-11-27 19:07:57'),
(43, 5, 'christian realisan', 'chano@gmail.com', 'eyyyy', '2024-11-27 19:15:21'),
(44, 5, 'christian realisan', 'chano@gmail.com', 'eyyyyyyyyyyy', '2024-11-27 20:17:54'),
(45, 5, 'christian realisan', 'chano@gmail.com', 'aaaaaaaaaaaaaaa', '2024-11-27 20:28:46'),
(46, 5, 'christian realisan', 'chano@gmail.com', 'iloveyou\r\n', '2024-11-28 09:57:32'),
(47, 5, 'christian realisan', 'chano@gmail.com', 'iloveyou\r\n', '2024-11-28 19:14:13'),
(48, 2, 'christian realisan', 'chano@gmail.com', 'gggggggggggggggggggggg', '2024-11-29 04:53:57'),
(49, 2, 'christian realisan', 'chano@gmail.com', 'kamusta ka aking mahal', '2024-12-01 01:56:55'),
(50, 9, 'christian realisan', 'chano@gmail.com', 'hello', '2024-12-02 05:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `userid`, `id`, `payment_method`, `total_price`, `created_at`) VALUES
(1, 17, 1, 'gcash', 300.00, '2024-12-04 11:32:19'),
(2, 17, 2, 'gcash', 450.00, '2024-12-04 11:58:27'),
(3, 17, 5, 'gcash', 270.00, '2024-12-04 12:09:31'),
(4, 17, 7, 'gcash', 540.00, '2024-12-04 12:10:06'),
(5, 17, 8, 'gcash', 330.00, '2024-12-04 12:10:34'),
(7, 17, 12, 'gcash', 330.00, '2024-12-04 12:20:02'),
(8, 17, 14, 'gcash', 440.00, '2024-12-04 12:20:59'),
(12, 17, 30, 'gcash', 270.00, '2024-12-04 12:36:57'),
(15, 17, 39, 'gcash', 330.00, '2024-12-04 12:50:43'),
(16, 17, 41, 'gcash', 600.00, '2024-12-04 12:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addons`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `item_name`, `category`, `price`, `qty`, `addons`) VALUES
(1, 'Mozzarella Stick', 'small-plates', 150.00, 2, NULL),
(2, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(3, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(4, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(5, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(6, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(7, 'Chicken Wings', 'small-plates', 180.00, 3, NULL),
(8, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(9, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(12, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(13, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(14, 'Hand-cut Potato Fries', 'small-plates', 120.00, 2, '[{\"name\":\"Cheese\",\"price\":30}]'),
(15, 'Mozzarella Stick', 'small-plates', 150.00, 1, '[{\"name\":\"Extra Sauce\",\"price\":20}]'),
(30, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(31, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(39, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(40, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(41, 'Mozzarella Stick', 'small-plates', 150.00, 2, '[{\"name\":\"Extra Sauce\",\"price\":20},{\"name\":\"Extra Mozzarella\",\"price\":40},{\"name\":\"Extra Sauce\",\"price\":20},{\"name\":\"Extra Mozzarella\",\"price\":40}]'),
(42, 'Chicken Wings', 'small-plates', 180.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_price` decimal(10,2) NOT NULL,
  `room_description` text DEFAULT NULL,
  `room_capacity` int(11) NOT NULL,
  `room_availability` enum('Available','Not Available') DEFAULT 'Available',
  `num_rooms` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_name`, `room_price`, `room_description`, `room_capacity`, `room_availability`, `num_rooms`, `status`, `updated_at`) VALUES
(1, 101, 'Standard Double Room', 3200.00, NULL, 2, 'Not Available', 0, 'Available', '2025-01-22 06:35:23'),
(2, 101, 'Standard Double Room', 3400.00, NULL, 2, 'Not Available', 0, 'Available', '2025-01-22 06:53:56'),
(3, 101, 'Standard Double Room', 3400.00, NULL, 2, 'Not Available', 0, 'Available', '2025-01-22 07:04:12'),
(4, 101, 'Standard Double Room', 3400.00, NULL, 2, 'Not Available', 0, 'Available', '2025-01-22 07:40:08'),
(5, 101, 'Standard Double Room', 3800.00, NULL, 2, 'Available', 0, 'Available', '2025-01-22 07:40:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `usertype` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `can_order_from_cafe` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `firstname`, `lastname`, `email`, `phone`, `usertype`, `username`, `address`, `password`, `profile_photo`, `date`, `can_order_from_cafe`) VALUES
(1, 'christian realisan realisan', 'realisan', 'chano1@gmail.com', '09123456789', '', 'Chano123', 'qw', '$2y$10$pHlfXD/5ZsNr.IZLyjcEFuF.HWuR9q3LJzHIFGDQ4uXCwQ/IbLB1O', 'profile_1.jpg', '2025-01-17 20:02:54', 0),
(2, 'jomelasaa', 'ilagan', 'jomel@gmail.com', NULL, '', 'jomel', 'san vicente', '$2y$10$/hf8cGCUVraYjA8v3zrI0u3AlGhpaCVIsK/tWF4LA6Q4sooG3i2ES', '', '2024-12-02 02:30:55', 0),
(5, 'Casa Estela', 'realisan', 'casaestela@gmail.com', NULL, '', 'Casa', 'qw', '$2y$10$NZh4Yn6adkw5OfSjF.NI1u7.dfuig5ysYkSgGRhGg5wgaRxK2e8mq', 'f58674ef-ab5d-40dd-8f45-040c6444616d.jpg', '2024-11-28 19:13:26', 0),
(6, 'Bella', 'Aw aw', 'bella@gmail.com', NULL, '', 'bella', 'tawagan calapan city', '$2y$10$utKKj8/rzrNQJHe1yk8mvOH7ZgRQosLcaAma3tVS6mYN8lGQJeJty', '', '2024-11-29 03:26:28', 0),
(7, 'Bella', 'Aw aw', 'bellas@gmail.com', NULL, '', 'bellaa', 'tawagan calapan city', '$2y$10$XeOuAfLOX4FkeCJrYohr4.wYrirZH.JOnYKrIRf.Dygo1SizPBwaq', '', '2024-11-29 04:35:04', 0),
(8, 'Bella', 'Aw aw', 'bellsas@gmail.com', NULL, '', 'bellaas', 'tawagan calapan city', '$2y$10$3O74n1oWfVgZjJs5JR5FHeDU6QMe3MRmqxEgJ9SNv9ytRVy8B5o4q', '', '2024-11-29 04:37:06', 0),
(9, 'chano', 'realisan', 'christianrealisan3@gmail.com', NULL, '', 'christian', 'tawagan calapan city', '$2y$10$X7sTwS8woECms3xZxCh1kePeOxPaBYcIlbPU2pvEmWvryf3T5fWE.', 'malupiton.jpg', '2024-12-02 05:06:38', 0),
(10, 'christian', 'realisan', 'w@gmail.com', NULL, '', 'christian1', 'Casa', '$2y$10$ejbubVRy8q9i/aU5PrsiVuhHJxxVfdPLl8cT3/kBFdZgLK.OzH27G', '233.svg', '2024-12-02 07:46:14', 0),
(11, 'Christian', 'Realisan', 'chanoooo@gmail.com', NULL, '', 'Pangetsibaby', 'tawagan calapan city', '$2y$10$YFdMeN4A.MIRMlrCtBR6duMwUBQxARO2i5dFV35QcNiXmzaoV7F7S', '', '2024-12-03 07:21:43', 0),
(12, 'Christian', 'Realisan', 'chano12oo@gmail.com', NULL, '', 'Pangetsibabyaa', 'tawagan calapan city', '$2y$10$PrbHFAC328KHgclAroI/bO8cyhep.mDmAtp9A5TGA1d7XLj6t3lma', '', '2024-12-03 07:50:46', 0),
(13, 'Christian', 'Realisan', 'chano1as2oo@gmail.com', NULL, '', 'Pangetsibabyaaa', 'bella', '$2y$10$Eq61bGWmVTdNTFTOJqb7W.lsjC2jJdQLf8xBZtaz8G3rOBsbBRuRG', '', '2024-12-03 08:01:23', 0),
(14, 'Christian', 'Realisan', 'chansaoooo@gmail.com', NULL, '', 'Pangetsibabyaaaa', 'tawagan calapan city', '$2y$10$CoTazEdNtYCgNdK1e1Iy8eEej4VKzGUk36LgEPytZxQPUrY1FKove', '', '2024-12-03 08:02:50', 0),
(15, 'Christian', 'Realisan', 'ssfdafsdfdf@gmail.com', NULL, '', 'magandasibaby', 'tawagan calapan city', '$2y$10$Nm6R8GUsujfhSzrnvF9OWuKVvvV.c4p1VdspHsoEOfLxC/tuOHz.y', '', '2024-12-03 08:24:42', 0),
(16, 'Christian', 'Realisan', 'dsaa@gmail.com', NULL, '', 'Pangetsi', 'tawagan calapan city', '$2y$10$DlKF87FgyqOUl0JUoSTPSe.yGjsz2qHF6lnx9RsafiLFYWZJ2cvpu', '', '2024-12-03 08:41:47', 0),
(17, 'Sheriedell', 'Mendoza', 'mendozasheriedell@gmail.com', NULL, '', 'pogisichano', 'tawagan calapan city', '$2y$10$amhxYD0eKlLDrm0KFGSO6eGoERHWk8Xgse04591Kjbn9lHqdube0.', '30e47a1d-dba5-4886-a550-f5e4863bd69e (1).jpg', '2024-12-03 11:08:48', 0),
(18, 'Sheriedell', 'Mendoza', 'mendozasheriedell17@gmail.com', NULL, '', 'pogisichanoo', 'tawagan calapan city', '$2y$10$oOyamBAlDmXOeYbyd2.vU.zDFHFKkS42CrIXfrCvosImPKcgSkja6', '', '2024-12-03 08:58:45', 0),
(19, 'Sheriedell', 'Mendoza', 'mendozasheriedell1a17@gmail.com', NULL, '', 'pogisichanaa', 'tawagan calapan city', '$2y$10$d/C/jtMc3.LrLNZ5JKyGJ.5Ff8q.tViy4xneHz0ttozlCnnTZHefG', '', '2024-12-03 09:10:43', 0),
(20, 'Sheriedell', 'Mendoza', 'mendozasaheriedell117@gmail.com', NULL, '', 'pogi', 'tawagan calapan city', '$2y$10$TC.RVi4Mga/3EMf0WBTR7.YN.QqYobUyVSp2Ny7J1ZtUAnbwhk0vy', '', '2024-12-03 09:14:43', 0),
(21, 'Tanggol ', 'Montenegro', 'tanggola@email.com', '09123456789', '', 'tanggolaa', 'tanggol', '$2y$10$EHz33AFjUc0n..7XTA.DO..he9lsaLzYUxAH97WLaSyUUU/aEjag2', '', '2025-01-17 05:31:18', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booking_details` (`booking_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cafetable`
--
ALTER TABLE `cafetable`
  ADD PRIMARY KEY (`table_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`feedbackid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cafetable`
--
ALTER TABLE `cafetable`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1737534796;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_details` FOREIGN KEY (`booking_id`) REFERENCES `booking_details` (`id`);

--
-- Constraints for table `cafetable`
--
ALTER TABLE `cafetable`
  ADD CONSTRAINT `cafetable_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `contactus`
--
ALTER TABLE `contactus`
  ADD CONSTRAINT `contactus_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
