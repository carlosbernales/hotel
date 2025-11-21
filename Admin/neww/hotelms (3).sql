-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2024 at 06:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotelms`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `check_in` varchar(100) DEFAULT NULL,
  `check_out` varchar(100) NOT NULL,
  `total_price` int(10) NOT NULL,
  `remaining_price` int(10) NOT NULL,
  `payment_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `customer_id`, `room_id`, `booking_date`, `check_in`, `check_out`, `total_price`, `remaining_price`, `payment_status`) VALUES
(1, 1, 5, '2017-11-13 05:45:17', '13-11-2017', '15-11-2017', 3000, 3000, 0),
(2, 2, 2, '2017-11-13 05:46:04', '13-11-2017', '16-11-2017', 6000, 0, 1),
(3, 3, 2, '2017-11-11 06:49:19', '11-11-2017', '14-11-2017', 6000, 3000, 0),
(4, 4, 7, '2017-11-09 06:50:24', '11-11-2017', '15-11-2017', 10000, 10000, 0),
(5, 6, 9, '2021-04-08 09:45:56', '08-04-2021', '10-04-2021', 3000, 3000, 0);

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
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_no` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_card_type_id` int(10) NOT NULL,
  `id_card_no` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `contact_no`, `email`, `id_card_type_id`, `id_card_no`, `address`) VALUES
(1, 'Alfred Aceveda', 7540001240, 'alfred@gmail.com', 1, '422510099122', '3166 Rockford Road'),
(2, 'John Rich Alveyra', 2870214970, 'johnrich@gmail.com', 2, '422510099122', '1954 Armory Road'),
(3, 'Aries King Nieto', 1247778460, 'ariesking@gmail.com', 1, '422510099122', '4879 Shearwood Forest Drive'),
(4, 'Aizzy Villanueva', 1478546500, 'aizzy@gmail.com', 3, '0', '926 Richland Avenue\n'),
(5, 'Christian Realisan', 2671249780, 'christian@gmail.com', 1, '422510099122', '4698 Columbia Road\n'),
(6, 'Fammela De Guzman', 1245554780, 'fammela@gmail.com', 4, 'AASS 12454784541', '4764 Warner Street\n'),
(7, 'Myra Kristine Grace Luceno', 2450006974, 'myra@gmail.com', 1, '457896000002', '1680  Brownton Road'),
(8, 'Earl Aceveda', 2457778450, 'earl@gmail.com', 1, '147000245810', '766  Lodgeville Road');

-- --------------------------------------------------------

--
-- Table structure for table `emp_history`
--

CREATE TABLE `emp_history` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `from_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `to_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `emp_history`
--

INSERT INTO `emp_history` (`id`, `emp_id`, `shift_id`, `from_date`, `to_date`, `created_at`) VALUES
(1, 1, 1, '2017-11-13 05:39:06', '2017-11-15 02:22:26', '2017-11-13 05:39:06'),
(2, 2, 3, '2017-11-13 05:39:39', '2017-11-15 02:22:43', '2017-11-13 05:39:39'),
(3, 3, 1, '2017-11-13 05:40:18', '2017-11-15 02:22:49', '2017-11-13 05:40:18'),
(4, 4, 1, '2017-11-13 05:40:56', '2017-11-15 02:22:35', '2017-11-13 05:40:56'),
(11, 1, 2, '2017-11-15 06:52:26', '2017-11-17 02:23:05', '2017-11-15 06:52:26'),
(12, 4, 3, '2017-11-15 06:52:35', NULL, '2017-11-15 06:52:35'),
(13, 2, 3, '2017-11-15 06:52:43', NULL, '2017-11-15 06:52:43'),
(14, 3, 3, '2017-11-15 06:52:49', NULL, '2017-11-15 06:52:49'),
(15, 1, 3, '2017-11-17 06:53:05', NULL, '2017-11-17 06:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `Customer_name` varchar(100) NOT NULL,
  `feedback_type` varchar(100) NOT NULL,
  `feedback` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolve_status` tinyint(1) NOT NULL,
  `resolve_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remarks` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `Customer_name`, `feedback_type`, `feedback`, `created_at`, `resolve_status`, `resolve_date`, `remarks`) VALUES
(1, 'Alfred Hendrik Aceveda\n', 'View', 'Ampangit', '2020-07-16 06:51:24', 1, '2020-07-17 06:51:58', 'Fixed'),
(2, 'Aizzy Villanueva\n', 'AirCon', 'Ang hina', '2020-10-01 06:51:44', 1, '2020-10-03 07:06:02', 'Fixed'),
(3, 'Fammela De Guzman\n', 'Amoy', 'Ambaho', '2018-04-01 07:01:17', 1, '2018-04-01 07:01:52', 'Pending'),
(5, 'Christian Realisan', 'Electric Fan', 'Ang hina ng ikot', '2021-04-09 08:38:19', 1, '2021-04-09 08:38:39', 'Fixed');

-- --------------------------------------------------------

--
-- Table structure for table `id_card_type`
--

CREATE TABLE `id_card_type` (
  `id_card_type_id` int(10) NOT NULL,
  `id_card_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `id_card_type`
--

INSERT INTO `id_card_type` (`id_card_type_id`, `id_card_type`) VALUES
(1, 'National Identity Card'),
(2, 'Voter Id Card'),
(3, 'Passport'),
(4, 'Driving License'),
(5, 'Postal Identity Card');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint(20) NOT NULL,
  `userid` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `userid`, `payment_method`, `total_price`, `created_at`, `status`) VALUES
(1733044042, 2, 'gcash', 355.00, '2024-12-01 01:07:22', 'Pending'),
(1733044110, 2, 'gcash', 170.00, '2024-12-01 01:08:30', 'Pending'),
(1733044239, 2, 'gcash', 150.00, '2024-12-01 01:10:39', 'Pending'),
(1733045599, 2, 'gcash', 170.00, '2024-12-01 01:33:19', 'Pending'),
(1733101353, 2, 'gcash', 120.00, '2024-12-01 17:02:33', 'Pending'),
(1733101413, 2, 'gcash', 170.00, '2024-12-01 17:03:33', 'Pending'),
(1733117307, 9, 'gcash', 270.00, '2024-12-01 21:28:27', 'Pending'),
(1733125700, 10, 'gcash', 150.00, '2024-12-01 23:48:20', 'Cancelled'),
(1733168433, 10, 'gcash', 330.00, '2024-12-02 11:40:33', 'Pending'),
(1733172182, 10, 'maya', 120.00, '2024-12-02 12:43:02', 'Pending'),
(1733192418, 10, 'maya', 180.00, '2024-12-02 18:20:18', 'Pending'),
(1733194706, 10, 'gcash', 150.00, '2024-12-02 18:58:26', 'Pending'),
(1733195099, 10, 'gcash', 120.00, '2024-12-02 19:04:59', 'Pending'),
(1733197194, 10, 'gcash', 150.00, '2024-12-02 19:39:54', 'Pending'),
(1733197623, 10, 'gcash', 150.00, '2024-12-02 19:47:03', 'Pending'),
(1733198063, 10, 'gcash', 150.00, '2024-12-02 19:54:23', 'Pending'),
(1733199390, 10, 'gcash', 150.00, '2024-12-02 20:16:30', 'Pending'),
(1733199468, 10, 'maya', 170.00, '2024-12-02 20:17:48', 'Pending'),
(1733199564, 10, 'maya', 205.00, '2024-12-02 20:19:24', 'Pending'),
(1733199737, 10, 'gcash', 150.00, '2024-12-02 20:22:17', 'Pending'),
(1733199878, 10, 'maya', 190.00, '2024-12-02 20:24:38', 'Pending'),
(1733201497, 10, 'gcash', 340.00, '2024-12-02 20:51:37', 'Pending'),
(1733201697, 10, 'maya', 150.00, '2024-12-02 20:54:57', 'Pending'),
(1733202007, 10, 'gcash', 1080.00, '2024-12-02 21:00:07', 'Pending'),
(1733202394, 10, 'gcash', 1200.00, '2024-12-02 21:06:34', 'Pending'),
(1733204002, 10, 'gcash', 1170.00, '2024-12-02 21:33:22', 'Cancelled'),
(1733204992, 10, 'gcash', 450.00, '2024-12-02 21:49:52', 'Cancelled'),
(1733205065, 10, 'gcash', 120.00, '2024-12-02 21:51:05', 'Cancelled'),
(1733205489, 10, 'gcash', 300.00, '2024-12-02 21:58:09', 'Cancelled'),
(1733223927, 17, 'gcash', 150.00, '2024-12-03 03:05:27', 'Cancelled'),
(1733223999, 17, 'gcash', 180.00, '2024-12-03 03:06:39', 'Cancelled'),
(1733254057, 17, 'gcash', 150.00, '2024-12-03 11:27:37', 'Cancelled'),
(1733281604, 17, 'gcash', 170.00, '2024-12-03 19:06:44', 'Cancelled'),
(1733295795, 23, 'gcash', 120.00, '2024-12-04 15:03:15', 'Pending'),
(1733295874, 23, 'maya', 450.00, '2024-12-04 15:04:34', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `addons` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `category`, `price`, `qty`, `addons`) VALUES
(21, 1733117307, 'Hand-cut Potato Fries', 'small-plates', 120.00, 2, '[{\"name\":\"Cheese\",\"price\":30}]'),
(22, 1733125700, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, '[{\"name\":\"Cheese\",\"price\":30}]'),
(23, 1733168433, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(24, 1733168433, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(25, 1733172182, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(26, 1733192418, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(27, 1733194706, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(28, 1733195099, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(29, 1733197194, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(30, 1733197623, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(31, 1733198063, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(32, 1733199390, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(33, 1733199468, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, '[{\"name\":\"Mayo\",\"price\":50}]'),
(34, 1733199564, 'Chicken Wings', 'small-plates', 180.00, 1, '[{\"name\":\"Buffalo Sauce\",\"price\":25}]'),
(35, 1733199737, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, '[{\"name\":\"Cheese\",\"price\":30}]'),
(36, 1733199878, 'Mozzarella Stick', 'small-plates', 150.00, 1, '[{\"name\":\"Extra Mozzarella\",\"price\":40}]'),
(37, 1733201497, 'Mozzarella Stick', 'small-plates', 150.00, 2, '[{\"name\":\"Extra Sauce\",\"price\":20},{\"name\":\"Extra Sauce\",\"price\":20}]'),
(38, 1733201697, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, '[{\"name\":\"Cheese\",\"price\":30}]'),
(39, 1733202007, 'Chicken Wings', 'small-plates', 180.00, 6, NULL),
(40, 1733202394, 'Hand-cut Potato Fries', 'small-plates', 120.00, 10, NULL),
(41, 1733204002, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(42, 1733204002, 'Mozzarella Stick', 'small-plates', 150.00, 7, NULL),
(43, 1733204992, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(44, 1733204992, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(45, 1733204992, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(46, 1733205065, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(47, 1733205489, 'Mozzarella Stick', 'small-plates', 150.00, 2, NULL),
(48, 1733223927, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(49, 1733223999, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(50, 1733254057, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, '[{\"name\":\"Cheese\",\"price\":30}]'),
(51, 1733281604, 'Mozzarella Stick', 'small-plates', 150.00, 1, '[{\"name\":\"Extra Sauce\",\"price\":20}]'),
(52, 1733295795, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(53, 1733295874, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(54, 1733295874, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(55, 1733295874, 'Chicken Wings', 'small-plates', 180.00, 1, NULL);

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
(40, 22, 'Standard Double Room', 'Fammela Nicole', 'De Guzman', 2147483647, 'Sitio 1', 3600.00, '2024-12-04', '2024-12-07', '', '', 10800.00, '02:25:00', 'Maya', 'fullyPaid', 0.00, 0.00, '2024-12-03 18:23:59'),
(41, 23, 'Standard Double Room', 'Loriedel', 'Masangkay', 0, 'pachoca', 3600.00, '2024-12-05', '2024-12-06', '', '', 3600.00, '14:53:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-04 06:53:27'),
(42, 23, 'Standard Double Room', 'thess', 'bengua', 0, 'city college', 3600.00, '2024-12-06', '2024-12-07', '', '', 3600.00, '17:00:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-04 06:59:33'),
(43, 23, 'Standard Double Room', 'aizzy', 'kupal', 0, 'baco', 3600.00, '2024-12-20', '2024-12-21', '', '', 3600.00, '03:05:00', 'Maya', 'fullyPaid', 0.00, 0.00, '2024-12-04 07:05:37'),
(44, 23, 'Standard Double Room', 'aizzy', 'kupal', 2147483647, 'baco', 3600.00, '2024-12-19', '2024-12-30', '', '', 39600.00, '18:15:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-04 07:12:27'),
(45, 24, 'Standard Double Room', 'ezekiel', 'arandia', 0, 'balite', 3600.00, '2024-12-05', '2024-12-06', '', '', 3600.00, '21:05:00', 'Gcash', 'fullyPaid', 0.00, 0.00, '2024-12-04 12:03:47');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(10) NOT NULL,
  `room_type_id` int(10) NOT NULL,
  `room_no` varchar(10) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `check_in_status` tinyint(1) NOT NULL,
  `check_out_status` tinyint(1) NOT NULL,
  `deleteStatus` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `room_type_id`, `room_no`, `status`, `check_in_status`, `check_out_status`, `deleteStatus`) VALUES
(1, 1, 'A-101', NULL, 0, 0, 0),
(2, 1, 'A-102', 1, 1, 1, 0),
(3, 1, 'A-103', NULL, 0, 0, 0),
(4, 1, 'A-104', NULL, 0, 0, 0),
(5, 1, 'A-105', 1, 0, 0, 0),
(6, 1, 'A-106', NULL, 0, 0, 1),
(7, 2, 'B-101', 1, 0, 0, 0),
(8, 2, 'B-102', NULL, 0, 0, 1),
(9, 2, 'B-103', 1, 0, 0, 0),
(10, 2, 'B-104', 1, 0, 0, 0),
(11, 3, 'C-100', 1, 0, 0, 0),
(27, 3, 'C-101', NULL, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_type`
--

CREATE TABLE `room_type` (
  `room_type_id` int(10) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `max_person` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room_type`
--

INSERT INTO `room_type` (`room_type_id`, `room_type`, `price`, `max_person`) VALUES
(1, 'Single', 1000, 6),
(2, 'Deluxe', 1500, 4),
(3, 'Family', 2000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `shift_id` int(10) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `shift_timing` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`shift_id`, `shift`, `shift_timing`) VALUES
(1, 'Morning', '5:00 AM - 10:00 AM'),
(2, 'Day', '10:00 AM - 4:00PM'),
(3, 'Evening', '4:00 PM - 10:00 PM'),
(4, 'Night', '10:00PM - 5:00AM');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `emp_id` int(11) NOT NULL,
  `emp_name` varchar(100) NOT NULL,
  `staff_type_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `id_card_type` int(11) NOT NULL,
  `id_card_no` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_no` bigint(20) NOT NULL,
  `salary` bigint(20) NOT NULL,
  `joining_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`emp_id`, `emp_name`, `staff_type_id`, `shift_id`, `id_card_type`, `id_card_no`, `address`, `contact_no`, `salary`, `joining_date`, `updated_at`) VALUES
(1, 'Alfred Aceveda', 1, 3, 1, '422510099122', '4516 Spruce Drive\n', 3479454777, 21000, '2020-11-13 05:39:06', '2021-04-08 17:36:16'),
(2, 'Aizzy Villanueva', 3, 3, 1, '422510099122', '2555 Hillside Drive', 1479994500, 12500, '2021-04-07 20:21:00', '2021-04-08 17:36:23'),
(3, 'Fammela De Guzman', 2, 3, 1, '422510099122', 'Ap #897-1459 Quam Avenue', 976543111, 25000, '2019-11-13 05:40:18', '2021-04-08 17:36:27'),
(4, 'Christian Realisan', 2, 3, 2, '0', '2272 Sun Valley Road\n', 7451112450, 31000, '2017-11-13 05:40:55', '2021-04-08 17:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `staff_type`
--

CREATE TABLE `staff_type` (
  `staff_type_id` int(10) NOT NULL,
  `staff_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff_type`
--

INSERT INTO `staff_type` (`staff_type_id`, `staff_type`) VALUES
(1, 'Manager'),
(2, 'Housekeeping Manager'),
(3, 'Front Desk Receptionist'),
(4, 'Cheif'),
(5, 'Waiter'),
(6, 'Room Attendant'),
(7, 'Concierge'),
(8, 'Hotel Maintenance Engineer'),
(9, 'Hotel Sales Manager');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `email`, `user_type`, `password`, `created_at`) VALUES
(2, 'Kristine Luceno', 'Kristine', 'Khristine@gmail.com', 'Admin', 'fb1e75090b96d2e0abec0ec980c2f392', '2015-11-12 12:49:22'),
(3, 'Alfred Aceveda', 'Alfred', 'alfred@gmail.com', 'Admin', 'd0a512f262ed34abed0c45cefe08c429', '2016-04-01 12:49:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contactnum` varchar(100) NOT NULL,
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

INSERT INTO `users` (`userid`, `firstname`, `lastname`, `email`, `contactnum`, `usertype`, `username`, `address`, `password`, `profile_photo`, `date`, `can_order_from_cafe`) VALUES
(1, 'christian', 'realisan', 'chano1@gmail.com', '0', '', 'Chano123', 'qw', '$2y$10$LPV5DK1OYhLagsxHzg1QOedV1eWYKGmvVqEFD596TqiGLqk4lR/hy', '', '2024-11-25 20:09:20', 0),
(2, 'jomelasaa', 'ilagan', 'jomel@gmail.com', '0', '', 'jomel', 'san vicente', '$2y$10$/hf8cGCUVraYjA8v3zrI0u3AlGhpaCVIsK/tWF4LA6Q4sooG3i2ES', '', '2024-12-02 02:30:55', 0),
(5, 'Casa Estela', 'realisan', 'casaestela@gmail.com', '0', '', 'Casa', 'qw', '$2y$10$NZh4Yn6adkw5OfSjF.NI1u7.dfuig5ysYkSgGRhGg5wgaRxK2e8mq', 'f58674ef-ab5d-40dd-8f45-040c6444616d.jpg', '2024-11-28 19:13:26', 0),
(6, 'Bella', 'Aw aw', 'bella@gmail.com', '0', '', 'bella', 'tawagan calapan city', '$2y$10$utKKj8/rzrNQJHe1yk8mvOH7ZgRQosLcaAma3tVS6mYN8lGQJeJty', '', '2024-11-29 03:26:28', 0),
(7, 'Bella', 'Aw aw', 'bellas@gmail.com', '0', '', 'bellaa', 'tawagan calapan city', '$2y$10$XeOuAfLOX4FkeCJrYohr4.wYrirZH.JOnYKrIRf.Dygo1SizPBwaq', '', '2024-11-29 04:35:04', 0),
(8, 'Bella', 'Aw aw', 'bellsas@gmail.com', '0', '', 'bellaas', 'tawagan calapan city', '$2y$10$3O74n1oWfVgZjJs5JR5FHeDU6QMe3MRmqxEgJ9SNv9ytRVy8B5o4q', '', '2024-11-29 04:37:06', 0),
(9, 'chano', 'realisan', 'christianrealisan3@gmail.com', '0', '', 'christian', 'tawagan calapan city', '$2y$10$X7sTwS8woECms3xZxCh1kePeOxPaBYcIlbPU2pvEmWvryf3T5fWE.', 'malupiton.jpg', '2024-12-02 05:06:38', 0),
(10, 'christian', 'realisan', 'w@gmail.com', '0', '', 'christian1', 'Casa', '$2y$10$ejbubVRy8q9i/aU5PrsiVuhHJxxVfdPLl8cT3/kBFdZgLK.OzH27G', '233.svg', '2024-12-02 07:46:14', 0),
(11, 'Christian', 'Realisan', 'chanoooo@gmail.com', '0', '', 'Pangetsibaby', 'tawagan calapan city', '$2y$10$YFdMeN4A.MIRMlrCtBR6duMwUBQxARO2i5dFV35QcNiXmzaoV7F7S', '', '2024-12-03 07:21:43', 0),
(12, 'Christian', 'Realisan', 'chano12oo@gmail.com', '0', '', 'Pangetsibabyaa', 'tawagan calapan city', '$2y$10$PrbHFAC328KHgclAroI/bO8cyhep.mDmAtp9A5TGA1d7XLj6t3lma', '', '2024-12-03 07:50:46', 0),
(13, 'Christian', 'Realisan', 'chano1as2oo@gmail.com', '0', '', 'Pangetsibabyaaa', 'bella', '$2y$10$Eq61bGWmVTdNTFTOJqb7W.lsjC2jJdQLf8xBZtaz8G3rOBsbBRuRG', '', '2024-12-03 08:01:23', 0),
(14, 'Christian', 'Realisan', 'chansaoooo@gmail.com', '0', '', 'Pangetsibabyaaaa', 'tawagan calapan city', '$2y$10$CoTazEdNtYCgNdK1e1Iy8eEej4VKzGUk36LgEPytZxQPUrY1FKove', '', '2024-12-03 08:02:50', 0),
(15, 'Christian', 'Realisan', 'ssfdafsdfdf@gmail.com', '0', '', 'magandasibaby', 'tawagan calapan city', '$2y$10$Nm6R8GUsujfhSzrnvF9OWuKVvvV.c4p1VdspHsoEOfLxC/tuOHz.y', '', '2024-12-03 08:24:42', 0),
(16, 'Christian', 'Realisan', 'dsaa@gmail.com', '0', '', 'Pangetsi', 'tawagan calapan city', '$2y$10$DlKF87FgyqOUl0JUoSTPSe.yGjsz2qHF6lnx9RsafiLFYWZJ2cvpu', '', '2024-12-03 08:41:47', 0),
(17, 'Sheriedell', 'Mendoza', 'mendozasheriedell@gmail.com', '2147483647', '', 'pogisichano', 'tawagan calapan city', '$2y$10$amhxYD0eKlLDrm0KFGSO6eGoERHWk8Xgse04591Kjbn9lHqdube0.', '30e47a1d-dba5-4886-a550-f5e4863bd69e (1).jpg', '2024-12-03 11:08:48', 0),
(18, 'Sheriedell', 'Mendoza', 'mendozasheriedell17@gmail.com', '0', '', 'pogisichanoo', 'tawagan calapan city', '$2y$10$oOyamBAlDmXOeYbyd2.vU.zDFHFKkS42CrIXfrCvosImPKcgSkja6', '', '2024-12-03 08:58:45', 0),
(19, 'Sheriedell', 'Mendoza', 'mendozasheriedell1a17@gmail.com', '2147483647', '', 'pogisichanaa', 'tawagan calapan city', '$2y$10$d/C/jtMc3.LrLNZ5JKyGJ.5Ff8q.tViy4xneHz0ttozlCnnTZHefG', '', '2024-12-03 09:10:43', 0),
(20, 'Sheriedell', 'Mendoza', 'mendozasaheriedell117@gmail.com', '2147483647', '', 'pogi', 'tawagan calapan city', '$2y$10$TC.RVi4Mga/3EMf0WBTR7.YN.QqYobUyVSp2Ny7J1ZtUAnbwhk0vy', '', '2024-12-03 09:14:43', 0),
(21, 'Myra Kristine Grace', 'Aceveda', 'myraaceveda@gmail.com', '09638322673', '', 'MyraAceveda', 'Kristine', '$2y$10$3lvOsufJfLtz5zcGy/WvpOSJfvg.xDGTdsepcm1ZiJKvBUobOZS5q', '', '2024-12-03 17:17:23', 0),
(22, 'Myra Kristine Grace', 'Aceveda', 'myraacevedaaa@gmail.com', '09638322673', '', 'MyraAcevedaa', 'wawa', '$2y$10$PTXjXvFwWxJ7Ht2UErffOOC8Wm2BHWyAacq1OdVsA1GeMH2UU3UD6', '', '2024-12-03 17:18:12', 0),
(23, 'Loriedel', 'Masangkay', 'Loriedel@gmail.com', '09123456777', '', 'loriedel123', 'pachoca', '$2y$10$QPJUxJw0NFmNO7cKZpMKI.O8amM6Z6NE09Z/ckbWIvEWKQ3nivX7q', '', '2024-12-04 06:50:55', 0),
(24, 'ezekiel', 'arandia', 'ezekiel@gmail.com', '09951779220', '', 'ezek', 'Sitio 1', '$2y$10$abeTMPHRhtOR4C5QCRBSA.CJ6NhKngnlfgIvi07It5L3WK23v7vg2', '', '2024-12-04 12:02:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `userss`
--

CREATE TABLE `userss` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','frontdesk','cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userss`
--

INSERT INTO `userss` (`id`, `email`, `password`, `user_type`) VALUES
(1, 'admin@example.com', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', 'admin'),
(2, 'frontdesk@example.com', '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', 'frontdesk'),
(3, 'cashier@example.com', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', 'cashier');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`feedbackid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_id_type` (`id_card_type_id`);

--
-- Indexes for table `emp_history`
--
ALTER TABLE `emp_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `id_card_type`
--
ALTER TABLE `id_card_type`
  ADD PRIMARY KEY (`id_card_type_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`emp_id`),
  ADD KEY `id_card_type` (`id_card_type`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `staff_type_id` (`staff_type_id`);

--
-- Indexes for table `staff_type`
--
ALTER TABLE `staff_type`
  ADD PRIMARY KEY (`staff_type_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `userss`
--
ALTER TABLE `userss`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_history`
--
ALTER TABLE `emp_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `id_card_type`
--
ALTER TABLE `id_card_type`
  MODIFY `id_card_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `room_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `shift_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `staff_type`
--
ALTER TABLE `staff_type`
  MODIFY `staff_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);

--
-- Constraints for table `contactus`
--
ALTER TABLE `contactus`
  ADD CONSTRAINT `contactus_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`id_card_type_id`) REFERENCES `id_card_type` (`id_card_type_id`);

--
-- Constraints for table `emp_history`
--
ALTER TABLE `emp_history`
  ADD CONSTRAINT `emp_history_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `staff` (`emp_id`),
  ADD CONSTRAINT `emp_history_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`shift_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_type` (`room_type_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`id_card_type`) REFERENCES `id_card_type` (`id_card_type_id`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shift` (`shift_id`),
  ADD CONSTRAINT `staff_ibfk_3` FOREIGN KEY (`staff_type_id`) REFERENCES `staff_type` (`staff_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
