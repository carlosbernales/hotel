-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 06:36 AM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_initial` varchar(1) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `first_name`, `middle_initial`, `last_name`, `age`, `address`, `position`, `email`, `contact`, `profile_image`, `created_at`) VALUES
(1, 'admin', '$2y$10$VLIHEg53Wc4m28V.iwDDyuLW8f2IsAFhYRc02.1yTyNHSzxr754Uy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 18:15:04'),
(2, 'admin@example.com', '$2y$10$dlPR3HFncDBOCr/zZz4SH.QAGLbJwLgU4zqSFtof432mJGdqmFqfa', 'Alfred Hendrik', 'A', 'Aceveda', 20, 'Balite Calapan City Oriental Mindoro', 'Manager', 'admin@example.com', NULL, NULL, '2025-02-16 18:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `advance_orders`
--

CREATE TABLE `advance_orders` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advance_orders`
--

INSERT INTO `advance_orders` (`id`, `booking_id`, `menu_item_id`, `quantity`, `price`, `created_at`) VALUES
(1, 10, 1, 1, 120.00, '2025-02-17 16:48:09'),
(2, 11, 1, 1, 120.00, '2025-02-17 17:03:53'),
(3, 13, 1, 1, 120.00, '2025-02-17 22:36:45'),
(4, 14, 1, 1, 120.00, '2025-02-18 06:15:17'),
(5, 14, 2, 1, 150.00, '2025-02-18 06:15:17'),
(6, 14, 3, 1, 180.00, '2025-02-18 06:15:17'),
(7, 15, 1, 1, 120.00, '2025-02-18 11:46:08'),
(8, 16, 1, 1, 120.00, '2025-02-18 12:54:14'),
(9, 16, 2, 1, 150.00, '2025-02-18 12:54:14'),
(10, 16, 3, 1, 180.00, '2025-02-18 12:54:14'),
(11, 16, 8, 1, 270.00, '2025-02-18 12:54:14');

-- --------------------------------------------------------

--
-- Table structure for table `advance_order_addons`
--

CREATE TABLE `advance_order_addons` (
  `id` int(11) NOT NULL,
  `advance_order_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('gcash','maya','general') NOT NULL,
  `created_at` datetime NOT NULL,
  `valid_until` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `type`, `created_at`, `valid_until`) VALUES
(1, 'Payment Disruption', 'Para sa mga abno jan, wala muna hong gagamit ng gcash at maya at may sira ho.', 'general', '2025-01-27 23:24:36', '2025-01-28 23:24:00');

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
(1, 1, 5, '2017-11-13 05:45:17', '13-11-2017', '15-11-2017', 3000, 0, 0),
(2, 2, 2, '2017-11-13 05:46:04', '13-11-2017', '16-11-2017', 6000, 0, 1),
(3, 3, 2, '2017-11-11 06:49:19', '11-11-2017', '14-11-2017', 6000, 3000, 0),
(4, 4, 7, '2017-11-09 06:50:24', '11-11-2017', '15-11-2017', 10000, 10000, 0),
(5, 6, 9, '2021-04-08 09:45:56', '08-04-2021', '10-04-2021', 3000, 3000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `booking_type` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `number_of_guests` int(11) DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `extra_charges` decimal(10,2) DEFAULT 0.00 COMMENT 'Extra charges for additional guests or services',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT NULL,
  `remaining_balance` int(11) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) NOT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `discount_type` varchar(50) DEFAULT NULL COMMENT 'Type of discount applied (e.g., Senior, PWD, Student, Promo)',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Amount of discount applied',
  `discount_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage of discount if applicable',
  `original_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Original amount before discount'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `first_name`, `last_name`, `booking_type`, `email`, `contact`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `payment_option`, `payment_method`, `total_amount`, `extra_charges`, `status`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `cancellation_reason`, `cancelled_at`, `rejection_reason`, `rejected_at`, `discount_type`, `discount_amount`, `discount_percentage`, `original_amount`) VALUES
(30, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-15', '02:48:44', 1, 'full', 'Cash', 7400, 0.00, 'Rejected', '2025-02-12 18:48:44', 2, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(31, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-18', '03:01:57', 1, 'full', 'Cash', 18500, 0.00, 'Rejected', '2025-02-12 19:01:57', 5, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 18500.00),
(32, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-18', '03:05:04', 1, 'full', 'Cash', 18500, 0.00, 'Rejected', '2025-02-12 19:05:04', 5, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 18500.00),
(33, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-15', '03:10:44', 1, 'full', 'Cash', 7400, 0.00, 'Rejected', '2025-02-12 19:10:44', 2, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(34, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-15', '03:14:06', 2, 'full', 'Cash', 9000, 0.00, 'Rejected', '2025-02-12 19:14:06', 2, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 9000.00),
(35, NULL, 'Christian', 'Realisan', 'Walkin', 'christianrealisan3@gmail.com', '9876543200', '2025-02-13', '2025-02-15', '04:57:12', 1, 'full', 'Cash', 4000, 0.00, 'Rejected', '2025-02-12 20:57:12', 2, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 4000.00),
(36, NULL, 'Fammela Nicole', 'De Guzman', 'Walkin', 'fammeladeguzman21@gmail.com', '09951779220', '2025-02-14', '2025-02-15', '17:34:00', 2, 'downpayment', 'Cash', 3000, 0.00, 'Checked Out', '2025-02-13 09:34:00', 1, 1500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 3000.00),
(58, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-13', '2025-02-14', '14:00:00', 1, 'full', 'gcash', 5700, 0.00, 'pending', '2025-02-13 15:40:41', 1, 5700.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 5700.00),
(59, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-13', '2025-02-15', '14:00:00', 2, 'full', 'cash', 4000, 0.00, 'pending', '2025-02-13 15:42:18', 2, 4000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 4000.00),
(60, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-13', '2025-02-14', '14:00:00', 2, 'full', 'cash', 3700, 0.00, 'pending', '2025-02-13 15:45:26', 1, 3700.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 3700.00),
(61, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-14', '2025-02-27', '14:00:00', 2, 'downpayment', 'cash', 32500, 0.00, 'pending', '2025-02-13 15:57:06', 13, 16250.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 32500.00),
(62, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-27', '14:00:00', 2, 'full', 'gcash', 30000, 0.00, 'pending', '2025-02-13 16:00:26', 12, 30000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 30000.00),
(63, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-22', '2025-02-26', '14:00:00', 1, '', 'Cash', 8000, 0.00, 'Checked Out', '2025-02-13 16:35:56', 4, 8000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 8000.00),
(64, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-17', '14:00:00', 2, 'full', 'cash', 4000, 0.00, 'Archived', '2025-02-13 16:36:42', 2, 4000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 4000.00),
(65, NULL, 'Chanchan', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-22', '14:00:00', 2, 'full', 'cash', 25900, 0.00, 'pending', '2025-02-13 16:44:35', 7, 25900.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(66, NULL, 'Chano', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-03-06', '14:00:00', 2, 'downpayment', 'cash', 70300, 0.00, 'pending', '2025-02-13 16:48:39', 19, 35150.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 70300.00),
(67, NULL, 'Chan', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-22', '14:00:00', 2, 'downpayment', 'cash', 25900, 0.00, 'pending', '2025-02-13 16:54:12', 7, 12950.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(68, NULL, 'Cha', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-22', '14:00:00', 2, 'full', 'Cash', 25900, 0.00, 'Checked Out', '2025-02-13 16:56:21', 7, 25900.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(69, NULL, 'h', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-25', '14:00:00', 2, 'full', 'cash', 37000, 0.00, 'pending', '2025-02-13 16:57:27', 10, 37000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 37000.00),
(70, NULL, 'h', 'Pogi', 'Online', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-20', '14:00:00', 2, 'full', 'cash', 18500, 0.00, 'pending', '2025-02-13 16:58:34', 5, 18500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 18500.00),
(71, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-15', '2025-02-18', '14:00:00', 2, 'downpayment', 'cash', 11100, 0.00, 'pending', '2025-02-13 16:59:13', 3, 5550.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(72, NULL, 'Chano', 'Pogi', 'Walkin', 'GFD@gmail.com', '9876543200', '2025-02-22', '2025-03-04', '00:59:47', 1, 'downpayment', 'Cash', 37000, 0.00, 'Rejected', '2025-02-13 16:59:47', 10, 18500.00, 0, NULL, NULL, 'Duplicate Booking', NULL, NULL, 0.00, 0.00, 37000.00),
(73, NULL, 'Chano', 'Pogi', 'Walkin', 'GFD@gmail.com', '9876543200', '2025-02-22', '2025-03-04', '00:59:47', 1, 'downpayment', 'Maya', 37000, 0.00, 'Checked Out', '2025-02-13 16:59:47', 10, 18500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 37000.00),
(74, NULL, 'Chanchan', 'Pogi', 'Walkin', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-03-05', '01:00:29', 1, 'downpayment', 'Cash', 66600, 0.00, 'pending', '2025-02-13 17:00:29', 18, 33300.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 66600.00),
(75, NULL, 'alfred', 'aceveda', 'Walkin', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-21', '01:06:23', 1, 'downpayment', 'Cash', 22200, 0.00, 'confirmed', '2025-02-13 17:06:23', 6, 11100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 22200.00),
(76, NULL, 'alfredo', 'aceveda', 'Walkin', 'GFD@gmail.com', '9876543200', '2025-02-15', '2025-02-27', '01:06:53', 1, 'downpayment', 'GCash', 44400, 0.00, 'Rejected', '2025-02-13 17:06:53', 12, 22200.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 44400.00),
(77, NULL, 'Aizzyy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-15', '2025-02-22', '14:00:00', 2, 'full', 'cash', 25900, 0.00, 'Archived', '2025-02-13 17:07:33', 7, 25900.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(80, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-03-06', NULL, 2, 'downpayment', 'gcash', 74000, 0.00, 'Rejected', '2025-02-13 17:25:01', 0, 37000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 74000.00),
(81, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-02-22', NULL, 2, 'full', 'gcash', 29600, 0.00, 'Checked Out', '2025-02-13 17:32:07', 0, 29600.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 29600.00),
(82, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-02-26', NULL, 2, 'downpayment', 'cash', 44400, 0.00, 'pending', '2025-02-13 17:37:33', 0, 22200.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 44400.00),
(83, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-02-21', NULL, 2, 'downpayment', 'cash', 25900, 0.00, 'pending', '2025-02-13 17:39:59', 0, 12950.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(84, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-03-05', NULL, 2, 'downpayment', 'gcash', 70300, 0.00, 'pending', '2025-02-13 17:41:37', 0, 35150.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 70300.00),
(85, NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-02-27', NULL, 2, 'full', 'cash', 48100, 0.00, 'pending', '2025-02-13 17:41:59', 0, 48100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 48100.00),
(86, 1, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-02-27', NULL, 2, 'downpayment', 'cash', 48100, 0.00, 'cancelled', '2025-02-13 17:44:21', 0, 24050.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 48100.00),
(87, 1, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9876543200', '2025-02-14', '2025-03-08', NULL, 2, 'full', 'cash', 81400, 0.00, 'cancelled', '2025-02-13 17:50:56', 0, 81400.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 81400.00),
(88, 1, 'Myra', 'Luceno', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-15', '2025-02-20', NULL, 1, 'full', 'gcash', 28500, 0.00, 'cancelled', '2025-02-15 11:40:16', 0, 28500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 28500.00),
(89, 3, 'Myra', 'Luceno', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-15', '2025-02-17', NULL, 1, 'full', 'cash', 4000, 0.00, 'Rejected', '2025-02-15 14:58:51', 0, 4000.00, 0, NULL, NULL, 'Room Unavailable', NULL, NULL, 0.00, 0.00, 4000.00),
(90, 3, 'Myra', 'Luceno', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-16', '2025-02-25', NULL, 1, 'downpayment', 'cash', 18000, 0.00, 'cancelled', '2025-02-15 16:08:35', 0, 9000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 18000.00),
(91, NULL, 'Myra', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-25', '22:45:23', 1, 'downpayment', 'Cash', 29600, 0.00, 'confirmed', '2025-02-16 14:45:23', 8, 14800.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 29600.00),
(92, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-28', '2025-03-01', '23:30:57', 1, 'downpayment', 'Cash', 2000, 0.00, 'Checked Out', '2025-02-16 15:30:57', 1, 1000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 2000.00),
(93, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-25', '23:32:36', 1, 'full', 'Cash', 29600, 0.00, 'Checked Out', '2025-02-16 15:32:36', 8, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 29600.00),
(94, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-25', '23:36:20', 1, 'full', 'Cash', 29600, 0.00, 'Checked Out', '2025-02-16 15:36:20', 8, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 29600.00),
(95, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-26', '23:38:58', 1, 'full', 'Cash', 33300, 0.00, 'Checked Out', '2025-02-16 15:38:58', 9, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 33300.00),
(96, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-03-05', '23:50:40', 1, 'full', 'Cash', 32000, 0.00, 'Checked Out', '2025-02-16 15:50:40', 16, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 32000.00),
(97, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-25', '23:59:02', 1, 'full', 'Cash', 25900, 0.00, 'Checked Out', '2025-02-16 15:59:02', 7, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25900.00),
(98, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-03-06', '00:04:15', 1, 'full', 'Maya', 59200, 0.00, 'Checked Out', '2025-02-16 16:04:15', 16, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 59200.00),
(99, NULL, 'hanna', 'Luceno', 'Walkin', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-19', '19:59:30', 1, 'downpayment', 'Cash', 2000, 0.00, 'Rejected', '2025-02-17 11:59:30', 1, 1000.00, 0, NULL, NULL, 'Room Unavailable', NULL, NULL, 0.00, 0.00, 2000.00),
(100, 4, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-20', NULL, 5, 'downpayment', 'cash', 11100, 0.00, 'finished', '2025-02-17 12:11:57', 0, 5550.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(101, 4, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-17', '2025-02-21', NULL, 5, 'downpayment', 'Maya', 24000, 0.00, 'Checked Out', '2025-02-17 12:26:01', 0, 12000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 24000.00),
(102, 3, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-20', NULL, 1, 'full', 'cash', 7400, 0.00, 'Checked Out', '2025-02-17 20:24:55', 0, 7400.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(103, 3, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-20', NULL, 1, 'downpayment', 'gcash', 7400, 0.00, 'cancelled', '2025-02-17 20:39:41', 0, 3700.00, 0, 'change_of_plans', '2025-02-18 06:37:21', '', NULL, NULL, 0.00, 0.00, 7400.00),
(104, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-21', NULL, 1, 'full', 'cash', 7500, 0.00, 'cancelled', '2025-02-17 20:47:12', 0, 7500.00, 0, 'change_of_plans', '2025-02-18 04:49:02', '', NULL, NULL, 0.00, 0.00, 7500.00),
(105, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-20', NULL, 1, 'full', 'gcash', 5000, 0.00, 'Rejected', '2025-02-17 20:53:08', 0, 5000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 5000.00),
(106, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-03-08', NULL, 1, 'full', 'cash', 45000, 0.00, 'Checked Out', '2025-02-17 21:02:39', 0, 45000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 45000.00),
(107, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-19', NULL, 1, 'full', 'cash', 3700, 0.00, 'cancelled', '2025-02-17 21:05:15', 0, 3700.00, 0, 'change_of_plans', '2025-02-18 05:05:32', '', NULL, NULL, 0.00, 0.00, 3700.00),
(108, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-19', NULL, 1, 'downpayment', 'cash', 2000, 0.00, 'Rejected', '2025-02-17 21:12:15', 0, 1000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 2000.00),
(109, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-21', NULL, 1, 'full', 'cash', 6000, 0.00, 'cancelled', '2025-02-17 21:13:01', 0, 6000.00, 0, 'change_of_plans', '2025-02-18 05:13:10', '', NULL, NULL, 0.00, 0.00, 6000.00),
(110, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-03-28', NULL, 1, 'downpayment', 'cash', 140600, 0.00, 'cancelled', '2025-02-17 21:28:09', 0, 70300.00, 0, 'change_of_plans', '2025-02-18 05:30:04', '', NULL, NULL, 0.00, 0.00, 140600.00),
(111, 2, 'Kenjo', 'Marimon', 'Online', 'aizzyvillanueva43@gmail.com', '09363950698', '2025-02-18', '2025-02-19', NULL, 1, 'full', 'cash', 3700, 0.00, 'cancelled', '2025-02-17 21:32:43', 0, 3700.00, 0, 'change_of_plans', '2025-02-18 05:33:06', '', NULL, NULL, 0.00, 0.00, 3700.00),
(112, 1, 'Robin', 'Almarez', 'Online', 'christianrealisan3@gmail.com', '0987654321', '2025-02-18', '2025-04-29', NULL, 1, 'downpayment', 'gcash', 175000, 0.00, 'cancelled', '2025-02-17 22:01:11', 0, 87500.00, 0, 'change_of_plans', '2025-02-18 06:01:45', '', NULL, NULL, 0.00, 0.00, 175000.00),
(113, 1, 'Robin', 'Almarez', 'Online', 'christianrealisan3@gmail.com', '0987654321', '2025-02-18', '2025-02-19', NULL, 1, 'full', 'gcash', 2500, 0.00, 'Checked Out', '2025-02-17 22:02:39', 0, 2500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 2500.00),
(114, 3, 'Robin', 'Almarez', 'Online', 'christianrealisan3@gmail.com', '0987654321', '2025-02-18', '2025-02-28', NULL, 1, 'downpayment', 'GCash', 25000, 0.00, 'Checked Out', '2025-02-17 23:33:19', 0, 12500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 25000.00),
(115, 1, 'Christian', 'Realisan', 'Online', 'aizzyvillanueva43@gmail.com', '0987654321', '2025-02-19', '2025-02-22', NULL, 1, 'full', 'Cash', 7500, 0.00, 'Checked Out', '2025-02-18 06:17:34', 0, 7500.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7500.00),
(116, NULL, 'patrick', 'cusi', 'Walkin', 'aizzyvillanueva43@gmail.com', '0987654321', '2025-02-19', '2025-02-27', '14:35:39', 1, 'full', 'Maya', 20000, 0.00, 'Checked Out', '2025-02-18 06:35:39', 8, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 20000.00),
(117, NULL, 'patrick', 'cusi', 'Walkin', 'aizzyvillanueva43@gmail.com', '0987654321', '2025-02-19', '2025-02-26', '15:11:00', 1, 'full', 'GCash', 17500, 0.00, 'Checked Out', '2025-02-18 07:11:00', 7, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 17500.00),
(118, 1, 'patrick', 'cusi', 'Online', 'fammeladeguzman21@gmail.com', '09363950698', '2025-02-19', '2025-02-28', NULL, 2, 'full', 'cash', 55800, 0.00, 'cancelled', '2025-02-18 11:42:58', 0, 55800.00, 0, 'change_of_plans', '2025-02-18 19:43:28', '', NULL, NULL, 0.00, 0.00, 55800.00),
(119, NULL, 'PAtrick ', 'Cusi', 'Walkin', 'alfredaceveda.3@gmail.com', '09812345678', '2025-02-18', '2025-02-19', '19:55:07', 1, 'full', 'Cash', 2500, 0.00, 'Checked Out', '2025-02-18 11:55:07', 1, 0.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 2500.00),
(120, 1, 'patrick', 'cusi', 'Online', 'alfredaceveda.3@gmail.com', '09363950698', '2025-02-01', '2025-02-28', NULL, 1, 'downpayment', 'cash', 54000, 0.00, 'pending', '2025-02-18 12:18:14', 0, 27000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 54000.00),
(121, 5, '', '', 'Online', '', '', '2025-03-05', '2025-03-06', NULL, 1, 'downpayment', 'cash', 2000, 0.00, 'pending', '2025-03-05 11:57:47', 0, 1000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 2000.00),
(122, 5, '', '', 'Online', '', '', '2025-03-05', '2025-03-08', NULL, 1, 'full', 'gcash', 11100, 0.00, 'pending', '2025-03-05 11:59:22', 0, 11100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(123, 5, 'Christian', '0', 'Online', 'christianrealisan45@gmail.com', '2147483647', '2025-03-05', '2025-03-07', NULL, 1, 'full', 'cash', 7400, 0.00, 'pending', '2025-03-05 12:07:08', 0, 7400.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(126, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-07', NULL, 1, 'full', 'cash', 7400, 0.00, 'pending', '2025-03-05 13:20:23', 0, 7400.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(127, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-07', NULL, 3, 'downpayment', 'cash', 7400, 0.00, 'pending', '2025-03-05 13:22:36', 0, 3700.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(128, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-08', NULL, 1, 'full', 'cash', 11100, 0.00, 'pending', '2025-03-05 13:35:20', 0, 11100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(129, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-08', NULL, 1, 'full', 'gcash', 11100, 0.00, 'pending', '2025-03-05 13:42:59', 0, 11100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(130, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-07', NULL, 1, 'full', 'gcash', 7400, 0.00, 'pending', '2025-03-05 13:51:23', 0, 7400.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 7400.00),
(131, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-06', NULL, 1, 'full', 'gcash', 3700, 0.00, 'pending', '2025-03-05 13:59:43', 0, 3700.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 3700.00),
(132, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-08', NULL, 1, 'full', 'gcash', 11100, 0.00, 'pending', '2025-03-05 14:07:16', 0, 11100.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 11100.00),
(133, 8, 'Christian', 'Realisan', 'Online', 'christianrealisan45aa@gmail.com', '09412222222', '2025-03-05', '2025-03-07', NULL, 1, 'full', 'gcash', 5000, 0.00, 'pending', '2025-03-05 14:31:39', 0, 5000.00, 0, NULL, NULL, '', NULL, NULL, 0.00, 0.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `booking_cancellations`
--

CREATE TABLE `booking_cancellations` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `cancelled_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_cancellations`
--

INSERT INTO `booking_cancellations` (`id`, `booking_id`, `user_id`, `reason`, `cancelled_at`) VALUES
(1, 104, 2, 'change_of_plans', '2025-02-18 04:49:02'),
(2, 107, 2, 'change_of_plans', '2025-02-18 05:05:32'),
(3, 109, 2, 'change_of_plans', '2025-02-18 05:13:10'),
(4, 110, 2, 'change_of_plans', '2025-02-18 05:30:04'),
(5, 111, 2, 'change_of_plans', '2025-02-18 05:33:06'),
(6, 112, 1, 'change_of_plans', '2025-02-18 06:01:45'),
(7, 103, 3, 'change_of_plans', '2025-02-18 06:37:21'),
(8, 118, 1, 'change_of_plans', '2025-02-18 19:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `booking_display_settings`
--

CREATE TABLE `booking_display_settings` (
  `id` int(11) NOT NULL,
  `booking_type` enum('room','table','event') NOT NULL,
  `display_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`display_fields`)),
  `image_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`image_settings`)),
  `layout_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_display_settings`
--

INSERT INTO `booking_display_settings` (`id`, `booking_type`, `display_fields`, `image_settings`, `layout_order`, `created_at`, `updated_at`) VALUES
(1, 'room', 'null', '{\"width\":\"\",\"height\":\"\",\"enable\":false}', 0, '2025-02-08 17:52:53', '2025-02-08 17:52:53'),
(2, 'room', '[]', '{\"width\":\"\",\"height\":\"\",\"enable\":false}', 0, '2025-02-08 17:53:33', '2025-02-08 17:53:33'),
(3, 'room', '[]', '{\"width\":\"\",\"height\":\"\",\"enable\":false}', 0, '2025-02-08 17:53:35', '2025-02-08 17:53:35'),
(4, 'table', '[\"name\",\"price\",\"capacity\"]', '{\"width\":\"\",\"height\":\"\",\"enable\":true}', 0, '2025-02-08 17:53:57', '2025-02-08 17:53:57'),
(5, 'room', '[\"name\",\"price\"]', '{\"width\":\"\",\"height\":\"\",\"enable\":false}', 0, '2025-02-08 17:54:15', '2025-02-08 17:54:15'),
(6, 'room', '[\"name\",\"price\"]', '{\"width\":\"\",\"height\":\"\",\"enable\":false}', 0, '2025-02-08 18:55:29', '2025-02-08 18:55:29');

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
-- Table structure for table `dining_tables`
--

CREATE TABLE `dining_tables` (
  `id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `table_type` enum('Couple','Friends','Family','Package A','Package B','Package C') NOT NULL,
  `category` enum('regular','ultimate') NOT NULL DEFAULT 'regular',
  `capacity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('available','occupied') DEFAULT 'available',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dining_tables`
--

INSERT INTO `dining_tables` (`id`, `table_name`, `table_type`, `category`, `capacity`, `price`, `status`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Package D', 'Family', 'ultimate', 48, 70000.00, 'available', 'uploads/tables/67a862a913c4b.jpg', '2025-02-09 08:09:13', '2025-02-09 08:09:13'),
(2, 'Family Table', 'Family', 'regular', 7, 10000.00, 'available', 'uploads/tables/67a862e402fde.jpg', '2025-02-09 08:10:12', '2025-02-09 08:10:12'),
(3, 'Package D', 'Family', 'ultimate', 12, 100000.00, 'available', 'uploads/tables/67aa27b98c995.jpg', '2025-02-10 16:22:17', '2025-02-10 16:22:17');

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
(15, 1, 3, '2017-11-17 06:53:05', '2025-02-13 09:35:30', '2017-11-17 06:53:05'),
(22, 1, 2, '2025-02-13 09:35:30', NULL, '2025-02-13 09:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `overtime_hours` int(11) DEFAULT 0,
  `overtime_charge` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `remaining_balance` decimal(10,2) NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `user_id`, `package_name`, `package_price`, `base_price`, `overtime_hours`, `overtime_charge`, `total_amount`, `paid_amount`, `remaining_balance`, `reservation_date`, `start_time`, `end_time`, `number_of_guests`, `payment_method`, `payment_type`, `booking_status`, `created_at`, `updated_at`) VALUES
('TB20250213191100509', 1, 'Standard Package', 73500.00, 47500.00, 13, 26000.00, 73500.00, 36750.00, 36750.00, '2025-02-14', '02:10:00', '20:10:00', 30, 'cash', 'downpayment', 'pending', '2025-02-13 18:11:00', '2025-02-13 18:11:00'),
('TB20250215175540283', 3, 'Standard Package', 47500.00, 47500.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-02-16', '00:55:00', '04:55:00', 30, 'cash', 'downpayment', 'pending', '2025-02-15 16:55:41', '2025-02-15 16:55:41'),
('TB20250217230534902', 1, 'Standard Package', 49500.00, 47500.00, 1, 2000.00, 49500.00, 49500.00, 0.00, '2025-02-18', '06:05:00', '12:05:00', 29, 'gcash', 'full', 'pending', '2025-02-17 22:05:34', '2025-02-17 22:05:34'),
('TB20250218075209221', 1, 'Venue Rental Only', 36000.00, 20000.00, 8, 16000.00, 36000.00, 36000.00, 0.00, '2025-02-19', '02:51:00', '14:56:00', 3, 'cash', 'full', 'pending', '2025-02-18 06:52:09', '2025-02-18 06:52:09'),
('TB20250218125006736', 1, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-10-07', '14:22:00', '15:10:00', 3, 'cash', 'full', 'pending', '2025-02-18 11:50:06', '2025-02-18 11:50:06'),
('TB20250218140704928', 1, 'Premium Package', 65000.00, 55000.00, 5, 10000.00, 65000.00, 65000.00, 0.00, '2025-02-19', '07:05:00', '17:05:00', 30, 'cash', 'full', 'pending', '2025-02-18 13:07:04', '2025-02-18 13:07:04'),
('TB20250307160503961', 5, 'Standard Package', 47500.00, 47500.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-07', '16:04:00', '19:04:00', 22, 'gcash', 'full', 'pending', '2025-03-07 15:05:03', '2025-03-07 15:05:03'),
('TB20250307161612625', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-07', '23:16:00', '23:20:00', 22, 'cash', 'full', 'pending', '2025-03-07 15:16:12', '2025-03-07 15:16:12');

-- --------------------------------------------------------

--
-- Table structure for table `event_images`
--

CREATE TABLE `event_images` (
  `id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_images`
--

INSERT INTO `event_images` (`id`, `package_id`, `image_path`, `caption`, `is_featured`, `created_at`) VALUES
(1, NULL, 'images/hall.jpg', 'Elegant Wedding Reception', 1, '2025-02-12 02:48:47'),
(2, NULL, 'images/hall2.jpg', 'Garden Wedding Ceremony', 0, '2025-02-12 02:48:47'),
(3, NULL, 'images/hall3.jpg', 'Birthday Celebration Setup', 0, '2025-02-12 02:48:47'),
(4, NULL, 'images/gard.jpg', 'Corporate Event Space', 0, '2025-02-12 02:48:47'),
(5, NULL, 'images/garden1.jpg', 'Outdoor Reception Area', 0, '2025-02-12 02:48:47'),
(6, NULL, 'images/garden.jpg', 'Garden Party Setup', 0, '2025-02-12 02:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `event_packages`
--

CREATE TABLE `event_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `max_guests` int(11) NOT NULL DEFAULT 30,
  `duration` int(11) NOT NULL DEFAULT 5 COMMENT 'Duration in hours',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_packages`
--

INSERT INTO `event_packages` (`id`, `name`, `price`, `description`, `max_guests`, `duration`, `created_at`, `is_available`) VALUES
(1, 'Standard Package', 47500.00, 'Up to 30 Pax\n5-hour venue rental\nBasic sound system\nStandard decoration\nBasic catering service', 30, 5, '2025-02-12 02:48:46', 1),
(2, 'Premium Package', 55000.00, 'Up to 30 Pax\n5-hour venue rental\nPremium sound system\nEnhanced decoration\nPremium catering service\nEvent coordinator', 30, 5, '2025-02-12 02:48:46', 1),
(3, 'Deluxe Package', 76800.00, 'Up to 30 Pax\n5-hour venue rental\nProfessional DJ\nLuxury decoration\nPremium catering service\nEvent coordinator\nPhoto/Video coverage', 30, 5, '2025-02-12 02:48:46', 1),
(4, 'Venue Rental Only', 20000.00, '5-hour venue rental\nTables and Tiffany chairs', 30, 5, '2025-02-12 02:48:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'check',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `category_id`, `name`, `icon`, `display_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Free', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:25:09'),
(2, 1, 'Valet parking', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(3, 1, 'Parking garage', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(4, 1, 'Accessible parking', 'check', 4, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(5, 2, 'Fire extinguishers', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(6, 2, 'CCTV', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(7, 2, 'Smoke alarms', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(8, 2, 'Security alarm', 'check', 4, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(9, 2, 'Key card access', 'check', 5, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(10, 2, '24-hour security', 'check', 6, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(11, 3, 'Coffee house', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(12, 3, 'Snack bar', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(13, 3, 'Restaurant', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(14, 4, 'Private check-in/check-out', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(15, 4, 'Luggage storage', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(16, 4, '24-hour front desk', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(17, 5, 'English', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(18, 5, 'Filipino', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(19, 6, 'Free Wi-Fi', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(20, 7, 'Toilet paper', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(21, 7, 'Bidet', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(22, 7, 'Slippers', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(23, 7, 'Private bathroom', 'check', 4, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(24, 7, 'Toilet', 'check', 5, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(25, 7, 'Hairdryer', 'check', 6, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(26, 7, 'Shower', 'check', 7, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `facility_categories`
--

CREATE TABLE `facility_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_categories`
--

INSERT INTO `facility_categories` (`id`, `name`, `display_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Parking', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(2, 'Safety & Security', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(3, 'Food & Drink', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(4, 'Reception Services', 4, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(5, 'Languages Spoken', 5, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(6, 'Internet', 6, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(7, 'Bathroom', 7, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `featured_rooms`
--

CREATE TABLE `featured_rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_names`
--

CREATE TABLE `guest_names` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `guest_type` enum('regular','pwd','senior') NOT NULL DEFAULT 'regular',
  `id_number` varchar(50) DEFAULT NULL,
  `id_image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `first_name`, `last_name`, `age`, `guest_type`, `id_number`, `id_image_path`, `created_at`) VALUES
(1, 126, 'Christian', 'Realisan', 68, 'senior', 'SCDFI-22233323', 'id_67c84f97d2ec8_Screenshot (2).png', '2025-03-05 13:20:23'),
(2, 127, 'Christian', 'Realisan', 59, 'regular', NULL, NULL, '2025-03-05 13:22:36'),
(3, 127, 'Christian', 'Realisan', 22, 'pwd', '1222-2222-2222', 'id_67c8501c5f772_Screenshot (2).png', '2025-03-05 13:22:36'),
(4, 127, 'Christian', 'Realisan', 1, 'regular', NULL, NULL, '2025-03-05 13:22:36'),
(5, 128, 'Christian', 'Realisan', 44, 'pwd', '2224-4444-3333', 'id_67c85318a5814_Screenshot (1).png', '2025-03-05 13:35:20'),
(6, 129, 'Christian', 'Realisan', 66, 'senior', 'SCDFI-22222222', 'id_67c854e34d743_Screenshot (2).png', '2025-03-05 13:42:59'),
(7, 130, 'Christian', 'Realisan', 66, 'senior', 'SCDFI-22222222', 'id_67c856db36331_Screenshot (2).png', '2025-03-05 13:51:23'),
(8, 131, 'Christian', 'Realisan', 44, 'pwd', '2222-2222-2222', 'id_67c858cfe1e51_Screenshot (2).png', '2025-03-05 13:59:43'),
(9, 132, 'Christian', 'Realisan', 22, 'pwd', '3333-3333-3333', 'id_67c85a94a4f65_Screenshot (6).png', '2025-03-05 14:07:16'),
(10, 133, 'Christian', 'Realisan', 44, 'senior', 'SCDFI-11111111', 'id_67c8604bda6fd_Screenshot (6).png', '2025-03-05 14:31:39');

-- --------------------------------------------------------

--
-- Table structure for table `housekeeping_requests`
--

CREATE TABLE `housekeeping_requests` (
  `request_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `name`, `email`, `subject`, `message`, `room_type`, `created_at`) VALUES
(1, 'christian realisan', 'chano@gmail.com', 'Inquiry about Standard Double Room', 'hello', '', '2025-02-12 02:43:26'),
(2, 'christian realisan', 'chano@gmail.com', 'Inquiry about Standard Double Room', 'Hi, I would like to inquire about the Standard Double Room...', 'Standard Double Room', '2025-02-12 02:43:57');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `category`, `price`) VALUES
(1, 'Americano', 'Beverages', 120.00),
(2, 'Cappuccino', 'Beverages', 140.00),
(3, 'Club Sandwich', 'Food', 180.00),
(4, 'Caesar Salad', 'Food', 220.00),
(5, 'Chocolate Cake', 'Desserts', 150.00),
(6, 'Americano', 'Beverages', 120.00),
(7, 'Cappuccino', 'Beverages', 140.00),
(8, 'Club Sandwich', 'Food', 180.00),
(9, 'Caesar Salad', 'Food', 220.00),
(10, 'Chocolate Cake', 'Desserts', 150.00),
(11, 'Americano', 'Beverages', 120.00),
(12, 'Cappuccino', 'Beverages', 140.00),
(13, 'Club Sandwich', 'Food', 180.00),
(14, 'Caesar Salad', 'Food', 220.00),
(15, 'Chocolate Cake', 'Desserts', 150.00),
(16, 'Americano', 'Beverages', 120.00),
(17, 'Cappuccino', 'Beverages', 140.00),
(18, 'Club Sandwich', 'Food', 180.00),
(19, 'Caesar Salad', 'Food', 220.00),
(20, 'Chocolate Cake', 'Desserts', 150.00),
(21, 'Americano', 'Beverages', 120.00),
(22, 'Cappuccino', 'Beverages', 140.00),
(23, 'Club Sandwich', 'Food', 180.00),
(24, 'Caesar Salad', 'Food', 220.00),
(25, 'Chocolate Cake', 'Desserts', 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `display_name`) VALUES
(1, 'small-plates', 'SMALL PLATES'),
(2, 'soup-salad', 'SOUP & SALAD'),
(3, 'pasta', 'PASTA'),
(4, 'sandwiches', 'SANDWICHES'),
(5, 'coffee', 'COFFEE & LATTE'),
(6, 'iceblend', 'ICE BLENDED'),
(7, 'tea', 'TEA'),
(8, 'otherdrinks', 'OTHER DRINKS'),
(9, 'Christian Realisan', ''),
(10, 'SIKen', '');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `price`, `image_path`) VALUES
(1, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(2, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(3, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(4, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(5, 2, 'Coconut Salad', 200.00, 'images/menu_67b41c8d869f9.jpg'),
(6, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(7, 4, 'Egg Sandwich', 500.00, ''),
(8, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b4289215e15.jpg'),
(9, 8, 'cokemismo', 30.00, 'images/menu_67b428e783085.jpg'),
(10, 10, 'Fried', 200.00, 'images/menu_67b476422a845.jpg'),
(11, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(12, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(13, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(14, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(15, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(16, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(17, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(18, 4, 'Egg Sandwich', 500.00, ''),
(19, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(20, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(21, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(22, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(23, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(24, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(25, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(26, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(27, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(28, 4, 'Egg Sandwich', 500.00, ''),
(29, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(30, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(31, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(32, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(33, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(34, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(35, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(36, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(37, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(38, 4, 'Egg Sandwich', 500.00, ''),
(39, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(40, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(41, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(42, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(43, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(44, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(45, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(46, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(47, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(48, 4, 'Egg Sandwich', 500.00, ''),
(49, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(50, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(51, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(52, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(53, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(54, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(55, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(56, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(57, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(58, 4, 'Egg Sandwich', 500.00, ''),
(59, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(60, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(61, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(62, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(63, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(64, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(65, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(66, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(67, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(68, 4, 'Egg Sandwich', 500.00, ''),
(69, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(70, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(71, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(72, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(73, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(74, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(75, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(76, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(77, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(78, 4, 'Egg Sandwich', 500.00, ''),
(79, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(80, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(81, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(82, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(83, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(84, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(85, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(86, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(87, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(88, 4, 'Egg Sandwich', 500.00, ''),
(89, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(90, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(91, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(92, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(93, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(94, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(95, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(96, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(97, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(98, 4, 'Egg Sandwich', 500.00, ''),
(99, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(100, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(101, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(102, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(103, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(104, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(105, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(106, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(107, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(108, 4, 'Egg Sandwich', 500.00, ''),
(109, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(110, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(111, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(112, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(113, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(114, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(115, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(116, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(117, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(118, 4, 'Egg Sandwich', 500.00, ''),
(119, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(120, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(121, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(122, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(123, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(124, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(125, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(126, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(127, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(128, 4, 'Egg Sandwich', 500.00, ''),
(129, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(130, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(131, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(132, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(133, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(134, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(135, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(136, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(137, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(138, 4, 'Egg Sandwich', 500.00, ''),
(139, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(140, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(141, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(142, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(143, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(144, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(145, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(146, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(147, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(148, 4, 'Egg Sandwich', 500.00, ''),
(149, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(150, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(151, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(152, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(153, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(154, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(155, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(156, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(157, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(158, 4, 'Egg Sandwich', 500.00, ''),
(159, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(160, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(161, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(162, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(163, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(164, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(165, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(166, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(167, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(168, 4, 'Egg Sandwich', 500.00, ''),
(169, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(170, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(171, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(172, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(173, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(174, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(175, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(176, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(177, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(178, 4, 'Egg Sandwich', 500.00, ''),
(179, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(180, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(181, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(182, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(183, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(184, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(185, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(186, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(187, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(188, 4, 'Egg Sandwich', 500.00, ''),
(189, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(190, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(191, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(192, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(193, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(194, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(195, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(196, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(197, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(198, 4, 'Egg Sandwich', 500.00, ''),
(199, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(200, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(201, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(202, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(203, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(204, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(205, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(206, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(207, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(208, 4, 'Egg Sandwich', 500.00, ''),
(209, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(210, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(211, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(212, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(213, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(214, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(215, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(216, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(217, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(218, 4, 'Egg Sandwich', 500.00, ''),
(219, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(220, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(221, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(222, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(223, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(224, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(225, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(226, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(227, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(228, 4, 'Egg Sandwich', 500.00, ''),
(229, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(230, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(231, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(232, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(233, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(234, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(235, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(236, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(237, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(238, 4, 'Egg Sandwich', 500.00, ''),
(239, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(240, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(241, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(242, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(243, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(244, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(245, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(246, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(247, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(248, 4, 'Egg Sandwich', 500.00, ''),
(249, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(250, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(251, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(252, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(253, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(254, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(255, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(256, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(257, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(258, 4, 'Egg Sandwich', 500.00, ''),
(259, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(260, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(261, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(262, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(263, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(264, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(265, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(266, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(267, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(268, 4, 'Egg Sandwich', 500.00, ''),
(269, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(270, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(271, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(272, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(273, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(274, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(275, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(276, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(277, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(278, 4, 'Egg Sandwich', 500.00, ''),
(279, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(280, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(281, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(282, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(283, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(284, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(285, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(286, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(287, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(288, 4, 'Egg Sandwich', 500.00, ''),
(289, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(290, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(291, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(292, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(293, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(294, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(295, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(296, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(297, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(298, 4, 'Egg Sandwich', 500.00, ''),
(299, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(300, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(301, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(302, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(303, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(304, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(305, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(306, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(307, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(308, 4, 'Egg Sandwich', 500.00, ''),
(309, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(310, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(311, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(312, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(313, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(314, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(315, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(316, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(317, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(318, 4, 'Egg Sandwich', 500.00, ''),
(319, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(320, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(321, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(322, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(323, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(324, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(325, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(326, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(327, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(328, 4, 'Egg Sandwich', 500.00, ''),
(329, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(330, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(331, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(332, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(333, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(334, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(335, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(336, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(337, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(338, 4, 'Egg Sandwich', 500.00, ''),
(339, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(340, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(341, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(342, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(343, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(344, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(345, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(346, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(347, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(348, 4, 'Egg Sandwich', 500.00, ''),
(349, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(350, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(351, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(352, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(353, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(354, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(355, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(356, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(357, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(358, 4, 'Egg Sandwich', 500.00, ''),
(359, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(360, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(361, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(362, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(363, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(364, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(365, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(366, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(367, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(368, 4, 'Egg Sandwich', 500.00, ''),
(369, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(370, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(371, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(372, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(373, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(374, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(375, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(376, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(377, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(378, 4, 'Egg Sandwich', 500.00, ''),
(379, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(380, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(381, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(382, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(383, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(384, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(385, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(386, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(387, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(388, 4, 'Egg Sandwich', 500.00, ''),
(389, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(390, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(391, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(392, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(393, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(394, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(395, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(396, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(397, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(398, 4, 'Egg Sandwich', 500.00, ''),
(399, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(400, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(401, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(402, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(403, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(404, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(405, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(406, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(407, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(408, 4, 'Egg Sandwich', 500.00, ''),
(409, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(410, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(411, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(412, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(413, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(414, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(415, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(416, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(417, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(418, 4, 'Egg Sandwich', 500.00, ''),
(419, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(420, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items_addons`
--

CREATE TABLE `menu_items_addons` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items_addons`
--

INSERT INTO `menu_items_addons` (`id`, `menu_item_id`, `name`, `price`) VALUES
(0, 1, 'HAtdog', 15.00),
(0, 1, 'cheese', 20.00),
(0, 10, 'Gravy', 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_addons`
--

CREATE TABLE `menu_item_addons` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_item_addons`
--

INSERT INTO `menu_item_addons` (`id`, `menu_item_id`, `name`, `price`) VALUES
(1, 1, 'Cheese', 30.00),
(2, 1, 'Mayo', 50.00),
(3, 2, 'Extra Sauce', 20.00),
(4, 2, 'Extra Mozzarella', 40.00),
(5, 3, 'Buffalo Sauce', 25.00),
(6, 3, 'Extra Ranch', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 18, 2, 'Inquiries', 'kupal ka ba bossing', 0, '2025-02-16 19:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `icon`, `reference_id`, `is_read`, `created_at`) VALUES
(1, 1, 'Booking Confirmed', 'Your booking #86 has been confirmed. Check-in date: Feb 14, 2025', 'booking', 'fas fa-calendar-check', 86, 1, '2025-02-13 17:44:21'),
(2, 1, 'New Booking Confirmation', 'Your booking #87 has been confirmed. Check-in date: Feb 14, 2025', 'booking', 'fas fa-calendar-check', 87, 1, '2025-02-13 17:50:57'),
(3, 1, 'New Booking Confirmation', 'Your booking #88 has been confirmed. Check-in date: Feb 15, 2025', 'booking', 'fas fa-calendar-check', 88, 1, '2025-02-15 11:40:16'),
(4, 3, 'New Booking Confirmation', 'Your booking #89 has been confirmed. Check-in date: Feb 15, 2025', 'booking', 'fas fa-calendar-check', 89, 1, '2025-02-15 14:58:51'),
(5, 3, '', 'Your order has been placed successfully. Please pick up at 12:05', 'order', '', NULL, 1, '2025-02-15 15:06:00'),
(6, 3, '', 'Your order has been placed successfully. Please pick up at 23:47', 'order', '', NULL, 1, '2025-02-15 15:47:34'),
(7, 3, 'New Booking Confirmation', 'Your booking #90 has been confirmed. Check-in date: Feb 16, 2025', 'booking', 'fas fa-calendar-check', 90, 1, '2025-02-15 16:08:35'),
(8, 3, '', 'Your order has been placed successfully. Please pick up at 00:18', 'order', '', NULL, 1, '2025-02-15 16:18:19'),
(9, 3, '', 'Your order has been placed successfully. Please pick up at 00:29', 'order', '', NULL, 1, '2025-02-15 16:29:49'),
(10, 1, '', 'Your order has been placed successfully. Please pick up at 20:11', 'order', '', NULL, 1, '2025-02-16 12:11:20'),
(11, 1, '', 'Your order has been placed successfully. Please pick up at 21:19', 'order', '', NULL, 1, '2025-02-16 13:19:26'),
(12, 1, '', 'Your order has been placed successfully. Please pick up at 21:19', 'order', '', NULL, 1, '2025-02-16 13:22:02'),
(13, 1, '', 'Your order has been placed successfully. Please pick up at 21:28', 'order', '', NULL, 1, '2025-02-16 13:28:44'),
(14, 1, '', 'Your order has been placed successfully. Please pick up at 01:28', 'order', '', NULL, 1, '2025-02-16 17:29:07'),
(15, 4, 'New Booking Confirmation', 'Your booking #100 has been confirmed. Check-in date: Feb 17, 2025', 'booking', 'fas fa-calendar-check', 100, 1, '2025-02-17 12:11:57'),
(16, 4, 'New Booking Confirmation', 'Your booking #101 has been confirmed. Check-in date: Feb 17, 2025', 'booking', 'fas fa-calendar-check', 101, 1, '2025-02-17 12:26:01'),
(17, 3, '', 'Your order has been placed successfully. Please pick up at 03:54', 'order', '', NULL, 1, '2025-02-17 19:54:48'),
(18, 3, 'New Booking Confirmation', 'Your booking #102 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 102, 1, '2025-02-17 20:24:55'),
(19, 3, 'New Booking Confirmation', 'Your booking #103 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 103, 1, '2025-02-17 20:39:41'),
(20, 2, 'New Booking Confirmation', 'Your booking #104 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 104, 1, '2025-02-17 20:47:12'),
(21, 2, 'New Booking Confirmation', 'Your booking #105 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 105, 1, '2025-02-17 20:53:08'),
(22, 2, 'New Booking Confirmation', 'Your booking #106 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 106, 1, '2025-02-17 21:02:39'),
(23, 2, 'New Booking Confirmation', 'Your booking #107 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 107, 1, '2025-02-17 21:05:15'),
(24, 2, 'New Booking Confirmation', 'Your booking #108 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 108, 1, '2025-02-17 21:12:15'),
(25, 2, 'New Booking Confirmation', 'Your booking #109 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 109, 1, '2025-02-17 21:13:01'),
(26, 2, 'New Booking Confirmation', 'Your booking #110 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 110, 1, '2025-02-17 21:28:09'),
(27, 2, 'New Booking Confirmation', 'Your booking #111 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 111, 1, '2025-02-17 21:32:43'),
(28, 2, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 111, 1, '2025-02-17 21:33:06'),
(29, 1, 'New Booking Confirmation', 'Your booking #112 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 112, 1, '2025-02-17 22:01:12'),
(30, 1, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 112, 1, '2025-02-17 22:01:45'),
(31, 1, 'New Booking Confirmation', 'Your booking #113 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 113, 1, '2025-02-17 22:02:39'),
(32, 3, '', 'Your order has been placed successfully. Please pick up at 06:34', 'order', '', NULL, 1, '2025-02-17 22:34:40'),
(33, 3, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 103, 0, '2025-02-17 22:37:21'),
(34, 3, 'New Booking Confirmation', 'Your booking #114 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 114, 0, '2025-02-17 23:33:19'),
(35, 1, '', 'Your order has been placed successfully. Please pick up at 15:11', 'order', '', NULL, 1, '2025-02-18 06:11:42'),
(36, 1, 'New Booking Confirmation', 'Your booking #115 has been confirmed. Check-in date: Feb 19, 2025', 'booking', 'fas fa-calendar-check', 115, 1, '2025-02-18 06:17:34'),
(37, 1, 'New Booking Confirmation', 'Your booking #118 has been confirmed. Check-in date: Feb 19, 2025', 'booking', 'fas fa-calendar-check', 118, 1, '2025-02-18 11:42:58'),
(38, 1, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 118, 1, '2025-02-18 11:43:28'),
(39, 1, '', 'Your order has been placed successfully. Please pick up at 14:22', 'order', '', NULL, 1, '2025-02-18 11:48:16'),
(40, 1, 'New Booking Confirmation', 'Your booking #120 has been confirmed. Check-in date: Feb 01, 2025', 'booking', 'fas fa-calendar-check', 120, 1, '2025-02-18 12:18:14'),
(41, 1, '', 'Your order has been placed successfully. Please pick up at 11:11', 'order', '', NULL, 1, '2025-02-18 12:28:29'),
(42, 5, 'New Booking Confirmation', 'Your booking #121 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 121, 1, '2025-03-05 11:57:47'),
(43, 5, 'New Booking Confirmation', 'Your booking #122 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 122, 1, '2025-03-05 11:59:22'),
(44, 5, 'New Booking Confirmation', 'Your booking #123 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 123, 1, '2025-03-05 12:07:08'),
(45, 8, 'New Booking Confirmation', 'Your booking #126 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 126, 0, '2025-03-05 13:20:23'),
(46, 8, 'New Booking Confirmation', 'Your booking #127 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 127, 0, '2025-03-05 13:22:36'),
(47, 8, 'New Booking Confirmation', 'Your booking #128 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 128, 0, '2025-03-05 13:35:20'),
(48, 8, 'New Booking Confirmation', 'Your booking #129 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 129, 0, '2025-03-05 13:42:59'),
(49, 8, 'New Booking Confirmation', 'Your booking #130 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 130, 0, '2025-03-05 13:51:23'),
(50, 8, 'New Booking Confirmation', 'Your booking #131 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 131, 0, '2025-03-05 13:59:43'),
(51, 8, 'New Booking Confirmation', 'Your booking #132 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 132, 0, '2025-03-05 14:07:16'),
(52, 8, 'New Booking Confirmation', 'Your booking #133 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 133, 0, '2025-03-05 14:31:39');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `discount` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `image`, `discount`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Weekend Getaway', 'images/family.jpg', '10% OFF', 'Perfect weekend escape with breakfast included', 1, '2025-03-05 11:14:57', '2025-03-07 12:52:38'),
(2, 'Family', 'images/couple.jpg', '15% OFF', 'Special rate for family stays with complimentary activities', 1, '2025-03-05 11:14:57', '2025-03-05 11:16:13'),
(3, 'Extended Stay', 'images/4.jpg', '15% OFF', 'Stay longer, save more with our weekly rates', 1, '2025-03-05 11:14:57', '2025-03-07 12:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order-type` varchar(255) NOT NULL,
  `pickup_time` time NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_type` varchar(50) DEFAULT 'Walk-in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order-type`, `pickup_time`, `special_instructions`, `payment_method`, `status`, `created_at`, `order_type`) VALUES
(1, 3, 150.00, '', '22:36:00', '34rre', 'gcash', 'finished', '2025-02-15 14:36:34', 'Walk-in'),
(2, 3, 270.00, '', '22:36:00', '34rre', 'gcash', 'finished', '2025-02-15 14:41:54', 'Walk-in'),
(3, 3, 120.00, '', '12:05:00', '', 'gcash', 'finished', '2025-02-15 15:06:00', 'Walk-in'),
(4, 3, 120.00, '', '23:47:00', 'gh', 'gcash', 'finished', '2025-02-15 15:47:34', 'Walk-in'),
(5, 3, 120.00, '', '00:18:00', '', 'gcash', 'finished', '2025-02-15 16:18:19', 'Walk-in'),
(6, 3, 150.00, '', '00:29:00', 'sadas', 'gcash', 'finished', '2025-02-15 16:29:49', 'Walk-in'),
(7, 1, 120.00, '', '20:11:00', '', 'gcash', 'finished', '2025-02-16 12:11:20', 'Walk-in'),
(8, 1, 120.00, '', '21:19:00', '', 'gcash', 'finished', '2025-02-16 13:19:26', 'Walk-in'),
(9, 1, 120.00, '', '21:19:00', '', 'gcash', 'finished', '2025-02-16 13:22:02', 'Walk-in'),
(10, 1, 240.00, '', '21:28:00', '', 'maya', 'finished', '2025-02-16 13:28:44', 'Walk-in'),
(11, 1, 120.00, '', '01:28:00', '', 'gcash', 'finished', '2025-02-16 17:29:07', 'Walk-in'),
(12, 1, 150.00, '', '02:59:11', '', 'gcash', 'finished', '2025-02-16 18:59:11', 'Walk-in'),
(13, 1, 150.00, '', '02:59:21', '', 'gcash', 'finished', '2025-02-16 18:59:21', 'Walk-in'),
(14, 1, 150.00, '', '03:02:48', '', 'gcash', 'finished', '2025-02-16 19:02:48', 'Walk-in'),
(15, 1, 900.00, '', '03:03:14', '', 'gcash', 'finished', '2025-02-16 19:03:14', 'Walk-in'),
(16, 1, 330.00, '', '20:02:26', '', 'maya', 'finished', '2025-02-17 12:02:27', 'Walk-in'),
(17, 3, 150.00, '', '02:50:50', '', 'gcash', 'finished', '2025-02-17 18:50:50', 'Walk-in'),
(18, 3, 235.00, '', '03:54:00', '', 'gcash', 'finished', '2025-02-17 19:54:48', 'Walk-in'),
(19, 3, 150.00, '', '04:00:47', '', 'gcash', 'finished', '2025-02-17 20:00:47', 'Walk-in'),
(20, 3, 200.00, '', '06:34:00', '', 'gcash', 'finished', '2025-02-17 22:34:40', 'Walk-in'),
(21, 1, 150.00, '', '14:09:50', '', 'gcash', 'finished', '2025-02-18 06:09:50', 'Walk-in'),
(22, 1, 360.00, '', '14:10:44', '', 'maya', 'finished', '2025-02-18 06:10:44', 'Walk-in'),
(23, 1, 355.00, '', '15:11:00', '', 'gcash', 'finished', '2025-02-18 06:11:42', 'Walk-in'),
(24, 1, 205.00, '', '14:22:00', '', 'gcash', 'pending', '2025-02-18 11:48:16', 'Walk-in'),
(25, 1, 300.00, '', '11:11:00', '', 'gcash', 'finished', '2025-02-18 12:28:29', 'Walk-in');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(2, 2, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(3, 2, 'Mozzarella Stick', 1, 150.00, 150.00),
(4, 3, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(5, 4, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(6, 5, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(7, 6, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(8, 7, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(9, 8, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(10, 9, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(11, 10, 'Hand-cut Potato Fries', 2, 120.00, 240.00),
(12, 11, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(13, 12, 'Mozzarella Stick', 1, 150.00, 150.00),
(14, 13, 'Mozzarella Stick', 1, 150.00, 150.00),
(15, 14, 'Mozzarella Stick', 1, 150.00, 150.00),
(16, 15, 'Mozzarella Stick', 6, 150.00, 900.00),
(17, 16, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(18, 16, 'Chicken Wings', 1, 180.00, 180.00),
(19, 17, 'Mozzarella Stick', 1, 150.00, 150.00),
(20, 18, 'Chicken Wings', 1, 180.00, 180.00),
(21, 19, 'Mozzarella Stick', 1, 150.00, 150.00),
(22, 20, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(23, 21, 'Mozzarella Stick', 1, 150.00, 150.00),
(24, 22, 'Chicken Wings', 2, 180.00, 360.00),
(25, 23, 'Chicken Wings', 1, 180.00, 180.00),
(26, 23, 'Hand-cut Potato Fries', 1, 120.00, 120.00),
(27, 24, 'Chicken Wings', 1, 180.00, 180.00),
(28, 25, 'Hand-cut Potato Fries', 2, 120.00, 240.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `addon_name` varchar(255) NOT NULL,
  `addon_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_addons`
--

INSERT INTO `order_item_addons` (`id`, `order_item_id`, `addon_name`, `addon_price`) VALUES
(1, 1, 'Cheese', 30.00),
(2, 7, 'Cheese', 30.00),
(3, 17, 'Cheese', 30.00),
(4, 20, 'Buffalo Sauce', 25.00),
(5, 20, 'Extra Ranch', 30.00),
(6, 22, 'Cheese', 30.00),
(7, 22, 'Mayo', 50.00),
(8, 25, 'Buffalo Sauce', 25.00),
(9, 26, 'Cheese', 30.00),
(10, 27, 'Buffalo Sauce', 25.00),
(11, 28, 'Cheese', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_orders`
--

CREATE TABLE `reservation_orders` (
  `order_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `total_rooms` int(11) DEFAULT NULL,
  `available_rooms` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `total_rooms`, `available_rooms`, `status`) VALUES
(2, 1, NULL, NULL, 'Available'),
(3, 3, 5, 2, 'Occupied'),
(4, 1, NULL, NULL, 'Available'),
(6, 1, NULL, NULL, 'Available'),
(8, 2, NULL, NULL, 'Available'),
(10, 1, 5, 0, 'Available'),
(11, 2, 2, 0, 'Available'),
(12, 6, 1, 1, 'Available'),
(44, 3, NULL, NULL, 'Available'),
(55, 2, NULL, NULL, 'Available'),
(66, 2, NULL, NULL, 'Available'),
(77, 1, NULL, NULL, 'Available'),
(88, 1, NULL, NULL, 'Available'),
(99, 1, NULL, NULL, 'Available'),
(101, 1, NULL, NULL, 'Available'),
(102, 1, NULL, NULL, 'Available'),
(103, 1, NULL, NULL, 'Available'),
(156, 2, NULL, NULL, 'Available'),
(190, 1, NULL, NULL, 'Available'),
(191, 1, NULL, NULL, 'Available'),
(197, 1, NULL, NULL, 'Available'),
(198, 3, NULL, NULL, 'Available'),
(199, 2, NULL, NULL, 'Available'),
(201, 2, NULL, NULL, 'Available'),
(202, 2, NULL, NULL, 'Available'),
(240, 2, NULL, NULL, 'Available'),
(241, 3, NULL, NULL, 'Available'),
(255, 1, NULL, NULL, 'Available'),
(256, 1, NULL, NULL, 'Available'),
(257, 1, NULL, NULL, 'Available'),
(258, 2, NULL, NULL, 'Available'),
(259, 2, NULL, NULL, 'Available'),
(260, 3, NULL, NULL, 'Available'),
(301, 3, NULL, NULL, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `room_price` decimal(10,2) DEFAULT NULL,
  `room_quantity` int(11) DEFAULT NULL,
  `number_of_days` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `guest_count` int(11) NOT NULL,
  `extra_guest_fee` decimal(10,2) DEFAULT 0.00,
  `number_of_nights` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `booking_id`, `room_type_id`, `room_name`, `room_price`, `room_quantity`, `number_of_days`, `subtotal`, `created_at`, `guest_count`, `extra_guest_fee`, `number_of_nights`) VALUES
(1, 7, 1, 'Standard Double Room', 1500.00, 1, 9, 13500.00, '2025-02-09 22:44:23', 0, 0.00, 1),
(2, 8, 1, 'Standard Double Room', 1500.00, 1, 9, 13500.00, '2025-02-09 22:44:23', 0, 0.00, 1),
(3, 9, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-09 22:45:43', 0, 0.00, 1),
(4, 9, 1, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-09 22:45:43', 0, 0.00, 1),
(5, 10, 2, 'Deluxe Family Room', 2000.00, 1, 8, 16000.00, '2025-02-09 23:19:02', 0, 0.00, 1),
(6, 11, 2, 'Deluxe Family Room', 2000.00, 1, 8, 16000.00, '2025-02-09 23:19:02', 0, 0.00, 1),
(7, 12, 2, 'Deluxe Family Room', 2000.00, 1, 10, 20000.00, '2025-02-09 23:38:38', 0, 0.00, 1),
(8, 12, 3, 'Family Room', 2500.00, 1, 10, 25000.00, '2025-02-09 23:38:38', 0, 0.00, 1),
(9, 13, 2, 'Deluxe Family Room', 2000.00, 1, 0, 0.00, '2025-02-09 23:48:14', 0, 0.00, 1),
(10, 14, 3, 'Family Room', 2500.00, 1, 8, 20000.00, '2025-02-09 23:50:43', 0, 0.00, 1),
(11, 15, NULL, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-10 00:41:19', 0, 0.00, 1),
(12, 16, NULL, 'Family Room', 2500.00, 1, 9, 22500.00, '2025-02-10 01:08:27', 0, 0.00, 1),
(13, 17, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 01:12:16', 0, 0.00, 1),
(14, 18, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 01:12:16', 0, 0.00, 1),
(15, 19, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 01:16:08', 0, 0.00, 1),
(16, 20, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 01:20:12', 0, 0.00, 1),
(17, 20, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 01:20:12', 0, 0.00, 1),
(18, 21, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 03:05:36', 0, 0.00, 1),
(19, 22, NULL, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-10 03:08:55', 0, 0.00, 1),
(20, 23, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:09:50', 0, 0.00, 1),
(21, 24, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:09:50', 0, 0.00, 1),
(22, 25, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:12:32', 0, 0.00, 1),
(23, 26, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:12:33', 0, 0.00, 1),
(24, 27, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:17:53', 0, 0.00, 1),
(25, 28, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:23:15', 0, 0.00, 1),
(26, 29, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:23:16', 0, 0.00, 1),
(27, 30, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 03:56:24', 0, 0.00, 1),
(28, 31, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 03:58:07', 0, 0.00, 1),
(29, 32, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 04:01:43', 0, 0.00, 1),
(30, 33, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 04:03:31', 0, 0.00, 1),
(31, 34, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:12:29', 0, 0.00, 1),
(32, 35, NULL, 'Standard Double Room', 1500.00, 1, 6, 9000.00, '2025-02-10 04:50:24', 0, 0.00, 1),
(33, 36, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:57:04', 0, 0.00, 1),
(34, 37, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:57:04', 0, 0.00, 1),
(35, 38, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 05:16:41', 0, 0.00, 1),
(36, 39, NULL, 'Standard Double Room', 1500.00, 1, 6, 9000.00, '2025-02-12 02:18:44', 0, 0.00, 1),
(37, 40, 2, 'Deluxe Family Room', 1600.00, 1, 2, 3200.00, '2025-02-12 08:09:12', 0, 0.00, 1),
(38, 30, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-12 18:48:44', 1, 0.00, 2),
(39, 31, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-12 19:01:57', 1, 0.00, 5),
(40, 32, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-12 19:05:04', 1, 0.00, 5),
(41, 33, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-12 19:10:44', 1, 0.00, 2),
(42, 34, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-12 19:14:06', 1, 0.00, 2),
(43, 34, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-12 19:14:06', 1, 0.00, 2),
(44, 35, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-12 20:57:12', 1, 0.00, 2),
(45, 36, 2, 'Deluxe Family Room', 2000.00, 1, 1, 3000.00, '2025-02-13 09:34:00', 2, 1000.00, 1),
(51, 72, 1, 'Standard Double Room', 3700.00, 1, 10, 37000.00, '2025-02-13 16:59:47', 1, 0.00, 10),
(52, 73, 1, 'Standard Double Room', 3700.00, 1, 10, 37000.00, '2025-02-13 16:59:47', 1, 0.00, 10),
(53, 74, 1, 'Standard Double Room', 3700.00, 1, 18, 66600.00, '2025-02-13 17:00:29', 1, 0.00, 18),
(54, 75, 1, 'Standard Double Room', 3700.00, 1, 6, 22200.00, '2025-02-13 17:06:23', 1, 0.00, 6),
(55, 76, 1, 'Standard Double Room', 3700.00, 1, 12, 44400.00, '2025-02-13 17:06:53', 1, 0.00, 12),
(56, 80, 1, 'Standard Double Room', 3700.00, 1, 20, 74000.00, '2025-02-13 17:25:01', 0, 0.00, 20),
(57, 81, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-13 17:32:07', 0, 0.00, 8),
(58, 82, 1, 'Standard Double Room', 3700.00, 1, 12, 44400.00, '2025-02-13 17:37:33', 0, 0.00, 12),
(59, 83, 1, 'Standard Double Room', 3700.00, 1, 7, 25900.00, '2025-02-13 17:39:59', 0, 0.00, 7),
(60, 84, 1, 'Standard Double Room', 3700.00, 1, 19, 70300.00, '2025-02-13 17:41:37', 0, 0.00, 19),
(61, 85, 1, 'Standard Double Room', 3700.00, 1, 13, 48100.00, '2025-02-13 17:41:59', 0, 0.00, 13),
(62, 86, 1, 'Standard Double Room', 3700.00, 1, 13, 48100.00, '2025-02-13 17:44:21', 0, 0.00, 1),
(63, 87, 1, 'Standard Double Room', 3700.00, 1, 22, 81400.00, '2025-02-13 17:50:57', 0, 0.00, 1),
(64, 88, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-15 11:40:16', 0, 0.00, 1),
(65, 88, 2, 'Deluxe Family Room', 2000.00, 1, 5, 10000.00, '2025-02-15 11:40:16', 0, 0.00, 1),
(66, 89, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-15 14:58:51', 0, 0.00, 1),
(67, 90, 2, 'Deluxe Family Room', 2000.00, 1, 9, 18000.00, '2025-02-15 16:08:35', 0, 0.00, 1),
(68, 91, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 14:45:23', 1, 0.00, 8),
(69, 92, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-16 15:30:57', 1, 0.00, 1),
(70, 93, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 15:32:36', 1, 0.00, 8),
(71, 94, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 15:36:20', 1, 0.00, 8),
(72, 95, 1, 'Standard Double Room', 3700.00, 1, 9, 33300.00, '2025-02-16 15:38:58', 1, 0.00, 9),
(73, 96, 2, 'Deluxe Family Room', 2000.00, 1, 16, 32000.00, '2025-02-16 15:50:40', 1, 0.00, 16),
(74, 97, 1, 'Standard Double Room', 3700.00, 1, 7, 25900.00, '2025-02-16 15:59:02', 1, 0.00, 7),
(75, 98, 1, 'Standard Double Room', 3700.00, 1, 16, 59200.00, '2025-02-16 16:04:15', 1, 0.00, 16),
(76, 99, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-17 11:59:30', 1, 0.00, 1),
(77, 100, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-02-17 12:11:57', 0, 0.00, 1),
(78, 101, 2, 'Deluxe Family Room', 2000.00, 1, 4, 8000.00, '2025-02-17 12:26:01', 0, 0.00, 1),
(79, 102, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-17 20:24:55', 0, 0.00, 1),
(80, 103, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-17 20:39:41', 0, 0.00, 1),
(81, 104, 3, 'Family Room', 2500.00, 1, 3, 7500.00, '2025-02-17 20:47:12', 0, 0.00, 1),
(82, 105, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-17 20:53:08', 0, 0.00, 1),
(83, 106, 3, 'Family Room', 2500.00, 1, 18, 45000.00, '2025-02-17 21:02:39', 0, 0.00, 1),
(84, 107, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-02-17 21:05:15', 0, 0.00, 1),
(85, 108, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-17 21:12:15', 0, 0.00, 1),
(86, 109, 2, 'Deluxe Family Room', 2000.00, 1, 3, 6000.00, '2025-02-17 21:13:01', 0, 0.00, 1),
(87, 110, 1, 'Standard Double Room', 3700.00, 1, 38, 140600.00, '2025-02-17 21:28:09', 0, 0.00, 1),
(88, 111, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-02-17 21:32:43', 0, 0.00, 1),
(89, 112, 3, 'Family Room', 2500.00, 1, 70, 175000.00, '2025-02-17 22:01:11', 0, 0.00, 1),
(90, 113, 3, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-17 22:02:39', 0, 0.00, 1),
(91, 114, 3, 'Family Room', 2500.00, 1, 10, 25000.00, '2025-02-17 23:33:19', 0, 0.00, 1),
(92, 115, 3, 'Family Room', 2500.00, 1, 3, 7500.00, '2025-02-18 06:17:34', 0, 0.00, 1),
(93, 116, 3, 'Family Room', 2500.00, 1, 8, 20000.00, '2025-02-18 06:35:39', 1, 0.00, 8),
(94, 117, 3, 'Family Room', 2500.00, 1, 7, 17500.00, '2025-02-18 07:11:00', 1, 0.00, 7),
(95, 118, 3, 'Family Room', 2500.00, 1, 9, 22500.00, '2025-02-18 11:42:58', 0, 0.00, 1),
(96, 118, 1, 'Standard Double Room', 3700.00, 1, 9, 33300.00, '2025-02-18 11:42:58', 0, 0.00, 1),
(97, 119, 3, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-18 11:55:07', 1, 0.00, 1),
(98, 120, 2, 'Deluxe Family Room', 2000.00, 1, 27, 54000.00, '2025-02-18 12:18:14', 0, 0.00, 1),
(99, 121, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-03-05 11:57:47', 0, 0.00, 1),
(100, 122, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 11:59:22', 0, 0.00, 1),
(101, 123, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 12:07:08', 0, 0.00, 1),
(102, 126, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:20:23', 0, 0.00, 1),
(103, 127, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:22:36', 0, 0.00, 1),
(104, 128, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 13:35:20', 0, 0.00, 1),
(105, 129, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 13:42:59', 0, 0.00, 1),
(106, 130, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:51:23', 0, 0.00, 1),
(107, 131, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-03-05 13:59:43', 0, 0.00, 1),
(108, 132, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 14:07:16', 0, 0.00, 1),
(109, 133, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-03-05 14:31:39', 0, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_transfer_logs`
--

CREATE TABLE `room_transfer_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `old_room_id` int(11) NOT NULL,
  `new_room_id` int(11) NOT NULL,
  `transfer_reason` text NOT NULL,
  `transfer_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_transfer_logs`
--

INSERT INTO `room_transfer_logs` (`id`, `booking_id`, `old_room_id`, `new_room_id`, `transfer_reason`, `transfer_date`, `created_at`) VALUES
(1, 119, 3, 3, 'audgas', '2025-03-07 04:39:07', '2025-03-06 20:39:07');

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
  `image` varchar(255) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `discount_valid_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`, `discount_percent`, `discount_valid_until`) VALUES
(1, 'Standard Double Room', 3700.00, 21, 'Cozy and comfortable, our standard room comes with 2 single beds, ideal for friends or business travelers.', '2 Single Beds', 4.5, 'uploads/rooms/room_type_67addf8f186aa.jpg', 20, '2025-02-25'),
(2, 'Deluxe Family Room', 2000.00, 1, 'Our deluxe room offers a queen bed and a single bed, perfect for small families or groups.', '1 Queen Bed, 1 Single Bed', 4.8, 'uploads/rooms/room_type_67ade041022dc.jpg', 20, '2025-02-23'),
(3, 'Family Room', 2500.00, 5, 'Perfect for families, this spacious room features 1 queen bed and 2 single beds.', '1 Queen Bed, 2 Single Beds', 5.0, 'images/5.jpg', 25, '2025-02-12'),
(6, 'VIP', 3000.00, 3, 'GOod', '3 beds', 2.0, 'uploads/rooms/room_type_67b475d507ba7.png', 33, NULL),
(7, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(8, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(9, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(10, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(11, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(12, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(13, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(14, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(15, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(16, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(17, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(18, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(19, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(20, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(21, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(22, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(23, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(24, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(25, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(26, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(27, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(28, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(29, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(30, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(31, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(32, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(33, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(34, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(35, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(36, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(37, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(38, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(39, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(40, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(41, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(42, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(43, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(44, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(45, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(46, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(47, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(48, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(49, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(50, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(51, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(52, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(53, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(54, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(55, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(56, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(57, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(58, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(59, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(60, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(61, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(62, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(63, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(64, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(65, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(66, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(67, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(68, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(69, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL),
(70, 'Standard', 2500.00, 0, 'Standard room with basic amenities', NULL, NULL, NULL, 0, NULL),
(71, 'Deluxe', 3500.00, 0, 'Deluxe room with additional amenities', NULL, NULL, NULL, 0, NULL),
(72, 'Suite', 5000.00, 0, 'Luxury suite with all amenities', NULL, NULL, NULL, 0, NULL);

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
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `total_amount`, `payment_method`, `created_at`) VALUES
(1, 13, 150.00, 'gcash', '2025-02-17 19:51:30'),
(2, 12, 150.00, 'gcash', '2025-02-17 19:51:47'),
(3, 18, 235.00, 'gcash', '2025-02-17 19:55:03'),
(4, 19, 150.00, 'gcash', '2025-02-17 20:01:01'),
(5, 20, 200.00, 'gcash', '2025-02-17 22:35:04'),
(6, 21, 150.00, 'gcash', '2025-02-18 06:10:10'),
(7, 22, 360.00, 'maya', '2025-02-18 06:10:55'),
(8, 23, 355.00, 'gcash', '2025-02-18 06:12:33'),
(9, 25, 300.00, 'gcash', '2025-02-18 12:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_discounts`
--

CREATE TABLE `seasonal_discounts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasonal_discounts`
--

INSERT INTO `seasonal_discounts` (`id`, `name`, `discount_percentage`, `start_date`, `end_date`, `room_type_id`, `description`, `is_active`, `created_at`) VALUES
(1, 'Chrsitmas Discount', 10.00, '2025-03-10', '2025-04-02', NULL, 'Hi everyone, discount to', 1, '2025-03-08 17:37:57');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`) VALUES
(1, 'guest_id_upload_path', 'uploads/guest_ids/', '2025-03-05 13:19:05');

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
(1, 'Alfred Aceveda', 1, 2, 1, '422510099122', '4516 Spruce Drive\n', 3479454777, 21000, '2020-11-13 05:39:06', '2025-02-13 09:35:30'),
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
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_number` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `location` enum('Indoor','Outdoor','Balcony') NOT NULL,
  `status` enum('Available','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `table_bookings`
--

CREATE TABLE `table_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_id` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `num_guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `package_type` varchar(50) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_bookings`
--

INSERT INTO `table_bookings` (`id`, `user_id`, `booking_id`, `customer_name`, `package_name`, `contact_number`, `email_address`, `booking_date`, `booking_time`, `num_guests`, `special_requests`, `payment_method`, `total_amount`, `amount_paid`, `change_amount`, `payment_status`, `status`, `created_at`, `package_type`, `cancellation_reason`, `cancelled_at`) VALUES
(1, NULL, 'TBL-20250208-6326', 'robin almares', '', '', '', '2025-12-12', '11:11:00', 2, '', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Checked Out', '2025-02-08 10:26:37', 'Couple', NULL, NULL),
(2, NULL, '20250208-9710', 'Allaine Franz Aceveda', '', '', '', '2025-02-19', '14:22:00', 2, '', 'cash', 1500.00, 0.00, 0.00, 'Pending', 'Pending', '2025-02-08 12:45:35', '0', NULL, NULL),
(3, NULL, '20250208-7433', 'Allaine Franz Aceveda', '', '', '', '2025-02-22', '11:11:00', 2, '', 'cash', 9000.00, 0.00, 0.00, 'Pending', 'Archived', '2025-02-08 12:46:26', '0', NULL, NULL),
(4, NULL, 'TBL-20250208-6495', 'robin almares', '', '', '', '2025-02-22', '10:07:00', 2, 'pwede palagyan ng chicaron', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Pending', '2025-02-08 14:08:34', 'Couple', NULL, NULL),
(5, NULL, 'TBL-20250209-7538', 'alfred', '', '', '', '2025-01-01', '11:11:00', 2, '', 'Cash', 10000.00, 10000.00, 0.00, 'Pending', 'Pending', '2025-02-09 08:40:14', 'Family Table', NULL, NULL),
(6, NULL, 'TBL-20250212-9423', 'alfredoo', '', '', '', '2025-02-13', '20:28:00', 2, '', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Checked Out', '2025-02-12 12:27:32', 'Couple', NULL, NULL),
(7, NULL, 'TB-20250217-001', 'Kenjo M. Marimon', '', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-17', '22:35:00', 2, NULL, 'GCash', 199.00, 0.00, 0.00, 'Pending', 'Pending', '2025-02-17 14:35:25', 'Koupals', NULL, NULL),
(9, 4, 'TB-20250217-79d0', 'Kenjo M. Marimon', '', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-17', '23:43:00', 2, NULL, 'GCash', 149.50, 74.75, 0.00, 'Partially Paid', 'Pending', '2025-02-17 15:43:25', 'Friends', NULL, NULL),
(10, 4, 'BK000010', 'Kenjo M. Marimon', 'Koupals', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '00:47:00', 2, NULL, 'Cash', 219.50, 109.75, 0.00, 'Partially Paid', 'Pending', '2025-02-17 16:48:09', NULL, NULL, NULL),
(11, 4, 'BK000011', 'Kenjo M. Marimon', 'Koupals', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '01:03:00', 2, NULL, 'GCash', 219.50, 169.75, 0.00, 'Partially Paid', 'Pending', '2025-02-17 17:03:53', NULL, NULL, NULL),
(12, 4, 'BK000012', 'Kenjo M. Marimon', 'Friends', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '01:05:00', 2, NULL, 'GCash', 149.50, 74.75, 0.00, 'Partially Paid', 'Pending', '2025-02-17 17:05:49', NULL, NULL, NULL),
(13, 3, 'BK000013', 'Aizzy', 'Koupals', '9876543200', 'chsdjf@gmail.com', '2025-02-18', '06:35:00', 2, NULL, 'GCash', 319.00, 319.00, 0.00, 'Fully Paid', 'Pending', '2025-02-17 22:36:45', NULL, NULL, NULL),
(14, 1, 'BK000014', 'Aizzy', 'Koupals', '09876543200', 'aizzyvillanueva43@gmail.com', '2025-02-18', '14:14:00', 2, NULL, 'Cash', 549.50, 499.75, 0.00, 'Partially Paid', 'Pending', '2025-02-18 06:15:17', NULL, NULL, NULL),
(15, 1, 'BK000015', 'Fammela', 'Package A', '09951779220', 'alfred@gmail.com', '2025-02-11', '19:44:00', 30, NULL, 'GCash', 10120.00, 5120.00, 0.00, 'Partially Paid', 'Pending', '2025-02-18 11:46:08', NULL, NULL, NULL),
(16, 1, 'BK000016', 'Fammela', 'Koupals', '09951779220', 'alfred@gmail.com', '2025-02-19', '20:44:00', 2, NULL, 'GCash', 919.00, 919.00, 0.00, 'Fully Paid', 'Pending', '2025-02-18 12:54:14', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `table_cancellations`
--

CREATE TABLE `table_cancellations` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `cancelled_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `table_packages`
--

CREATE TABLE `table_packages` (
  `id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `available_tables` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_packages`
--

INSERT INTO `table_packages` (`id`, `package_name`, `price`, `capacity`, `description`, `available_tables`, `image_path`) VALUES
(1, 'Koupals', 199.00, 2, 'Perfect for kupals', 0, 'images/couple.jpg'),
(2, 'Friends', 299.00, 4, 'Ideal for small groups', 0, 'images/friends.jpg'),
(3, 'Family', 599.00, 10, 'Great for family gatherings', 0, 'images/family.jpg'),
(7, 'Package A', 20000.00, 30, 'Basic package for large groups', 2, 'images/table2.jpg'),
(8, 'Pacakge B', 33000.00, 40, 'Premium package with extra services', 2, 'images/table1.jpg'),
(9, 'Package C', 45000.00, 50, 'All-inclusive luxury package', 1, 'images/table3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `table_reservations`
--

CREATE TABLE `table_reservations` (
  `reservation_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `guest_count` int(11) NOT NULL,
  `table_type` varchar(50) NOT NULL,
  `reservation_datetime` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `phone`, `email`, `password`, `is_verified`, `verification_code`, `verification_expiry`, `created_at`, `updated_at`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Christian Realisan', '0', '0', 'christianrealisan3@gmail.com', '$2y$10$wTeWBkmwQn7UUuxW0ahQveDtRIhPULLjCBbND2MZ.mdXcnVvoxv8e', 1, '986507', '2025-02-12 16:24:15', '2025-02-12 15:22:15', '2025-02-12 15:22:51', NULL, NULL),
(2, 'Mang Juan', '0', '0', 'mangjuan@gmail.com', '$2y$10$mbQd/yeOWp3qy90mTAiUnOCnVnl5o33rYSnDtGXRPx4kxf1ZE7m0.', 1, '382013', '2025-02-12 16:31:55', '2025-02-12 15:29:55', '2025-02-12 16:47:42', NULL, NULL),
(3, 'Fammela Nicole Jumig De Guzman', '0', '0', 'fammeladeguzman21@gmail.com', '$2y$10$Ic53xTZkXkmyqtCTpdq.Y.uauCDKuC48etMP4ojLRFK8JLz6ZzRRi', 1, '484717', '2025-02-13 09:51:10', '2025-02-13 08:49:10', '2025-02-13 08:49:44', NULL, NULL),
(4, 'Kenjo M. Marimon', '0', '0', 'aizzyvillanueva43@gmail.com', '$2y$10$7tm0ztu9lKpjfwTWO2Cc8.9yp/ATXCDTv6UzJB3piqf6ebL7Kcsfq', 1, '188543', '2025-02-17 13:10:04', '2025-02-17 12:08:04', '2025-02-17 12:09:01', NULL, NULL),
(5, 'Christian', '0', '2147483647', 'christianrealisan45@gmail.com', '$2y$10$MbTmvDOYxK55V0Xqg.pS7.jKtGD/eUUk9eE1Fx4d8.6ZM8a3eu1Bu', 1, '847337', '2025-03-05 12:45:53', '2025-03-05 11:43:53', '2025-03-05 11:44:58', NULL, NULL),
(6, 'Christian', '0', '2147483647', 'christianrealisan45g@mail.com', '$2y$10$J5itO4uk1BdPdJOWs.eybuW2a7JkwUUYXPbqZqxANyrsJsoMMS/A6', 1, '092675', '2025-03-05 13:32:54', '2025-03-05 12:17:20', '2025-03-05 12:18:15', NULL, NULL),
(7, 'Christian', '0', '09466666666', 'christianrealisan45a@gmail.com', '$2y$10$9dxUhVbiPnQRfqBOTzKgieF7KRM1l6Bhj8jzXppN6.YwtojKbLumW', 0, '654991', '2025-03-07 13:12:47', '2025-03-05 12:27:04', '2025-03-07 12:10:47', NULL, NULL),
(8, 'Christian', 'Realisan', '09412222222', 'christianrealisan45aa@gmail.com', '$2y$10$K9JUOICaUG27CApObtuIYeGDXnrxGmE5gn9AbhhabBCdXjDLrsrha', 1, '182129', '2025-03-05 13:32:26', '2025-03-05 12:30:26', '2025-03-05 12:30:57', NULL, NULL);

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
(3, 'cashier@example.com', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', 'cashier'),
(5, 'admin@casa.com', '$2y$10$T6IeHR4px9rm6nrdiAuZ7ee25UL.5bFUAJ8MrSUCZ/63WdxygmWaa', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advance_orders`
--
ALTER TABLE `advance_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `advance_order_addons`
--
ALTER TABLE `advance_order_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advance_order_id` (`advance_order_id`),
  ADD KEY `addon_id` (`addon_id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `idx_check_in` (`check_in`),
  ADD KEY `idx_check_out` (`check_out`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `booking_cancellations`
--
ALTER TABLE `booking_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `booking_display_settings`
--
ALTER TABLE `booking_display_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_id_type` (`id_card_type_id`);

--
-- Indexes for table `dining_tables`
--
ALTER TABLE `dining_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_history`
--
ALTER TABLE `emp_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_images`
--
ALTER TABLE `event_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `event_packages`
--
ALTER TABLE `event_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `facility_categories`
--
ALTER TABLE `facility_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_guest_type` (`guest_type`);

--
-- Indexes for table `housekeeping_requests`
--
ALTER TABLE `housekeeping_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `id_card_type`
--
ALTER TABLE `id_card_type`
  ADD PRIMARY KEY (`id_card_type_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_transfer_logs`
--
ALTER TABLE `room_transfer_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `old_room_id` (`old_room_id`),
  ADD KEY `new_room_id` (`new_room_id`);

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
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `seasonal_discounts`
--
ALTER TABLE `seasonal_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

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
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `table_cancellations`
--
ALTER TABLE `table_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `table_packages`
--
ALTER TABLE `table_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `advance_orders`
--
ALTER TABLE `advance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `advance_order_addons`
--
ALTER TABLE `advance_order_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `booking_cancellations`
--
ALTER TABLE `booking_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `booking_display_settings`
--
ALTER TABLE `booking_display_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dining_tables`
--
ALTER TABLE `dining_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `emp_history`
--
ALTER TABLE `emp_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_images`
--
ALTER TABLE `event_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_packages`
--
ALTER TABLE `event_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `facility_categories`
--
ALTER TABLE `facility_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `housekeeping_requests`
--
ALTER TABLE `housekeeping_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `id_card_type`
--
ALTER TABLE `id_card_type`
  MODIFY `id_card_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `room_transfer_logs`
--
ALTER TABLE `room_transfer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `seasonal_discounts`
--
ALTER TABLE `seasonal_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_bookings`
--
ALTER TABLE `table_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `table_cancellations`
--
ALTER TABLE `table_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_packages`
--
ALTER TABLE `table_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advance_orders`
--
ALTER TABLE `advance_orders`
  ADD CONSTRAINT `advance_orders_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `table_bookings` (`id`),
  ADD CONSTRAINT `advance_orders_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `advance_order_addons`
--
ALTER TABLE `advance_order_addons`
  ADD CONSTRAINT `advance_order_addons_ibfk_1` FOREIGN KEY (`advance_order_id`) REFERENCES `advance_orders` (`id`),
  ADD CONSTRAINT `advance_order_addons_ibfk_2` FOREIGN KEY (`addon_id`) REFERENCES `menu_item_addons` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_cancellations`
--
ALTER TABLE `booking_cancellations`
  ADD CONSTRAINT `booking_cancellations_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `booking_cancellations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `facility_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  ADD CONSTRAINT `featured_rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD CONSTRAINT `guest_names_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`);

--
-- Constraints for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  ADD CONSTRAINT `menu_item_addons_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD CONSTRAINT `order_item_addons_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`);

--
-- Constraints for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  ADD CONSTRAINT `reservation_orders_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `table_reservations` (`reservation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_orders_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `room_transfer_logs`
--
ALTER TABLE `room_transfer_logs`
  ADD CONSTRAINT `room_transfer_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `room_transfer_logs_ibfk_2` FOREIGN KEY (`old_room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `room_transfer_logs_ibfk_3` FOREIGN KEY (`new_room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `seasonal_discounts`
--
ALTER TABLE `seasonal_discounts`
  ADD CONSTRAINT `seasonal_discounts_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD CONSTRAINT `table_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `table_cancellations`
--
ALTER TABLE `table_cancellations`
  ADD CONSTRAINT `table_cancellations_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `table_bookings` (`id`),
  ADD CONSTRAINT `table_cancellations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
