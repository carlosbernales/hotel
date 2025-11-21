-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 04:55 PM
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
-- Table structure for table `admin_status`
--

CREATE TABLE `admin_status` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_status`
--

INSERT INTO `admin_status` (`id`, `admin_id`, `last_active`, `is_online`) VALUES
(1, 1, '2025-04-08 04:34:52', 0),
(2, 5, '2025-04-08 04:34:52', 0);

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
(11, 16, 8, 1, 270.00, '2025-02-18 12:54:14'),
(12, 17, 1, 3, 120.00, '2025-03-18 02:52:29'),
(13, 17, 424, 1, 270.00, '2025-03-18 02:52:29');

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
  `booking_reference` varchar(255) NOT NULL,
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
  `room_type_id` int(11) DEFAULT NULL,
  `room_quantity` int(11) DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `extra_charges` decimal(10,2) DEFAULT 0.00 COMMENT 'Extra charges for additional guests or services',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT NULL,
  `remaining_balance` int(11) NOT NULL,
  `discount_type` varchar(50) DEFAULT NULL COMMENT 'Type of discount applied (e.g., Senior, PWD, Student, Promo)',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Amount of discount applied',
  `discount_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage of discount if applicable',
  `payment_reference` varchar(50) NOT NULL,
  `payment_proof` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `booking_reference`, `user_id`, `first_name`, `last_name`, `booking_type`, `email`, `contact`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `room_type_id`, `room_quantity`, `payment_option`, `payment_method`, `total_amount`, `extra_charges`, `status`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `discount_type`, `discount_amount`, `discount_percentage`, `payment_reference`, `payment_proof`) VALUES
(204, '', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:01:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:01:24', 1, 2000.00, 0, NULL, 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea67f3ea93f_Screenshot (234).png'),
(205, '', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:02:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:02:41', 1, 2000.00, 0, NULL, 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6841129fd_Screenshot (234).png'),
(206, '', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:05:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:05:32', 1, 2000.00, 0, NULL, 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea68eca6348_Screenshot (234).png'),
(208, 'BK202503311213498375', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:13:00', 1, NULL, NULL, 'partial', 'gcash', 1000, 0.00, 'finished', '2025-03-31 10:13:49', 1, 0.00, 1000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6add0fc9f_Screenshot (223).png'),
(209, 'BK202503311216476620', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:16:00', 1, NULL, NULL, 'partial', 'gcash', 800, 0.00, 'finished', '2025-03-31 10:16:47', 1, 0.00, 800, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6b8f2e5e6_Screenshot (234).png'),
(210, 'BK202503311219262992', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:19:00', 1, NULL, NULL, 'full', 'gcash', 1600, 0.00, 'finished', '2025-03-31 10:19:26', 1, 0.00, 1600, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6c2e27543_Screenshot (235).png'),
(211, 'BK202503311223221933', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:23:00', 1, NULL, NULL, 'partial', 'gcash', 1000, 0.00, 'finished', '2025-03-31 10:23:22', 1, 0.00, 1000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6d1a7aaac_Screenshot (235).png'),
(212, 'BK202503311228238151', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:28:00', 1, NULL, NULL, 'full', 'gcash', 1600, 0.00, 'finished', '2025-03-31 10:28:23', 1, 0.00, 1600, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6e471eb63_Screenshot (234).png'),
(213, 'BK202503311229097388', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:28:00', 1, NULL, NULL, 'partial', 'maya', 1000, 0.00, 'finished', '2025-03-31 10:29:09', 1, 0.00, 1000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6e74f1982_Screenshot (234).png'),
(214, 'BK202503311232333420', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:32:00', 1, NULL, NULL, 'partial', 'gcash', 1000, 0.00, 'finished', '2025-03-31 10:32:33', 1, 0.00, 1000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea6f415ce74_Screenshot (233).png'),
(215, 'BK202503311236237651', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:36:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:36:23', 1, 0.00, 2000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea7027b6fc6_Screenshot (234).png'),
(216, 'BK202503311242158817', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:42:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:42:15', 1, 0.00, 2000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea71875fa72_Screenshot (234).png'),
(217, 'BK202503311246144237', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:46:00', 1, NULL, NULL, 'full', 'gcash', 2000, 0.00, 'finished', '2025-03-31 10:46:14', 1, 0.00, 2000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea72763f672_Screenshot (234).png'),
(218, 'BK202503311252471896', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:52:00', 1, NULL, NULL, 'full', 'maya', 3700, 0.00, 'finished', '2025-03-31 10:52:47', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea73ffce860_Screenshot (234).png'),
(219, 'BK202503311254495475', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-03-31', '2025-04-01', '23:54:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 10:54:49', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea74799a58f_Screenshot (234).png'),
(220, 'BK202503311300213459', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '00:00:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 11:00:21', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ea75c583086_Screenshot (234).png'),
(221, 'BK202503312215202227', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:15:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-03-31 20:15:20', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eaf7d83c739_Screenshot (224).png'),
(222, 'BK202503312223388134', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:23:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-03-31 20:23:38', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eaf9caaddfd_Screenshot (234).png'),
(223, 'BK202503312227199085', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:27:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 20:27:19', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eafaa715c94_Screenshot (234).png'),
(224, 'BK202503312232342703', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:32:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 20:32:34', 1, 0.00, 3700, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67eafbe231db8_Screenshot (244).png'),
(225, 'BK202503312240004586', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:39:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 20:40:00', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eafda02b853_Screenshot (234).png'),
(226, 'BK202503312244498688', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '09:44:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 20:44:49', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eafec18c55d_Screenshot (234).png'),
(227, 'BK202503312303046732', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:02:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 21:03:04', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb030852d11_Screenshot (235).png'),
(228, 'BK202503312306502612', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:06:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 21:06:50', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb03ea6268c_Screenshot (234).png'),
(229, 'BK202503312309233575', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:09:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-03-31 21:09:23', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb04836a6f9_Screenshot (233).png'),
(230, 'BK202503312314235603', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:14:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 21:14:23', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb05af609dc_Screenshot (234).png'),
(231, 'BK202503312318247523', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:18:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-03-31 21:18:24', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb06a0d126c_Screenshot (233).png'),
(233, 'BK202503312327196862', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '10:27:00', 1, NULL, NULL, 'full', 'gcash', 7400, 0.00, 'finished', '2025-03-31 21:27:19', 1, 0.00, 7400, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb08b74ed05_Screenshot (234).png'),
(236, 'BK202504010035217345', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '11:35:00', 1, NULL, NULL, 'partial', 'gcash', 3700, 0.00, 'finished', '2025-03-31 22:35:21', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb18a9a9162_Screenshot (245).png'),
(237, 'BK202504010052541924', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '11:52:00', 1, NULL, NULL, 'full', 'gcash', 7400, 0.00, 'finished', '2025-03-31 22:52:54', 1, 0.00, 7400, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67eb1cc62a384_Screenshot (234).png'),
(238, 'BK202504011009087241', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:08:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:09:08', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eb9f24cbc6c_Screenshot (232).png'),
(239, 'BK202504011013595507', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:13:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:13:59', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba04784160_Screenshot (233).png'),
(240, 'BK202504011024034384', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:23:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:24:03', 1, 0.00, 3700, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67eba2a3a5a5b_Screenshot (234).png'),
(241, 'BK202504011028059913', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:27:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:28:05', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba39543f52_Screenshot (233).png'),
(242, 'BK202504011032203985', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:32:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:32:20', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba494a4b79_Screenshot (274).png'),
(243, 'BK202504011041387604', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:41:00', 1, NULL, NULL, 'full', 'maya', 3700, 0.00, 'finished', '2025-04-01 08:41:38', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba6c2160d5_Screenshot (234).png'),
(244, 'BK202504011047106588', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:47:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:47:10', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba80e5e592_Screenshot (233).png'),
(245, 'BK202504011050584376', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:50:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 08:50:58', 1, 0.00, 3700, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67eba8f243f68_Screenshot (234).png'),
(246, 'BK202504011054392084', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '21:54:00', 1, NULL, NULL, 'full', 'maya', 3700, 0.00, 'finished', '2025-04-01 08:54:39', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67eba9cfa41ec_Screenshot (235).png'),
(247, 'BK202504011106322566', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:06:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:06:32', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebac98d648a_Screenshot (234).png'),
(248, 'BK202504011107109758', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:06:00', 1, NULL, NULL, 'full', 'gcash', 7400, 0.00, 'finished', '2025-04-01 09:07:10', 1, 0.00, 7400, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebacbe27782_Screenshot (244).png'),
(249, 'BK202504011110416707', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:10:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:10:41', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebad9149572_Screenshot (234).png'),
(250, 'BK202504011112055800', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:11:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:12:05', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebade5258f4_Screenshot (224).png'),
(251, 'BK202504011115252873', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:15:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:15:25', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebaeadaf025_Screenshot (234).png'),
(252, 'BK202504011117203984', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:17:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-04-01 09:17:20', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebaf209641f_Screenshot (234).png'),
(253, 'BK202504011123013983', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '0000-00-00', '0000-00-00', NULL, 1, NULL, NULL, 'full', 'cash', 0, 0.00, 'finished', '2025-04-01 09:23:01', 0, 0.00, 0, '', 0.00, 0.00, '', ''),
(254, 'BK202504011123142807', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '0000-00-00', '0000-00-00', NULL, 1, NULL, NULL, 'full', 'cash', 0, 0.00, 'finished', '2025-04-01 09:23:14', 0, 0.00, 0, '', 0.00, 0.00, '', ''),
(255, 'BK202504011127555597', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '0000-00-00', '0000-00-00', NULL, 1, NULL, NULL, 'full', 'cash', 0, 0.00, 'finished', '2025-04-01 09:27:55', 0, 0.00, 0, '', 0.00, 0.00, '', ''),
(256, 'BK202504011128261291', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:28:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-04-01 09:28:26', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebb1badb07e_Screenshot (234).png'),
(257, 'BK202504011148461943', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:48:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:48:46', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebb67de7078_Screenshot (234).png'),
(258, 'BK202504011152268486', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '22:52:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 09:52:26', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebb75ae2cd7_Screenshot (234).png'),
(259, 'BK202504011202514197', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '23:02:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 10:02:51', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebb9cb4b20c_Screenshot (236).png'),
(260, 'BK202504011206274628', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-01', '2025-04-02', '23:06:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 10:06:27', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ebbaa36fe92_Screenshot (234).png'),
(261, 'BK202504011745579188', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '04:45:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 15:45:57', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec0a35a8e07_Screenshot (234).png'),
(262, 'BK202504011750541445', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '04:50:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 15:50:54', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec0b5dc738a_Screenshot (234).png'),
(263, 'BK202504011751392438', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '04:51:00', 1, NULL, NULL, 'partial', 'gcash', 1850, 0.00, 'finished', '2025-04-01 15:51:39', 1, 0.00, 1850, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec0b8b4fd0b_Screenshot (234).png'),
(264, 'BK202504011756517890', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '04:56:00', 1, NULL, NULL, 'full', 'maya', 3700, 0.00, 'finished', '2025-04-01 15:56:51', 1, 0.00, 3700, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67ec0cc2f34cd_Screenshot (223).png'),
(265, 'BK202504011802261489', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '05:02:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 16:02:26', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec0e120a2b5_Screenshot (233).png'),
(266, 'BK202504011812285375', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '05:12:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 16:12:28', 1, 0.00, 3700, '', 0.00, 0.00, '11111111111111111', 'uploads/payment_proofs/67ec106c8b117_Screenshot (234).png'),
(267, 'BK202504011816283271', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '05:16:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 16:16:28', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec115c794d1_Screenshot (233).png'),
(268, 'BK202504011854444468', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '05:54:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 16:54:44', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec1a54c8e0f_Screenshot (224).png'),
(269, 'BK202504011902372372', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '06:02:00', 1, NULL, NULL, 'full', 'gcash', 3700, 0.00, 'finished', '2025-04-01 17:02:37', 1, 0.00, 3700, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ec1c2d6a55c_Screenshot (224).png'),
(270, 'BK202504020648212710', 5, 'Christian', 'Realisan', 'Online', 'christianrealisan45@gmail.com', '09123456789', '2025-04-02', '2025-04-03', '17:48:00', 1, NULL, NULL, 'full', 'gcash', 4000, 0.00, 'finished', '2025-04-02 04:48:21', 1, 0.00, 4000, '', 0.00, 0.00, '5656565464564', 'uploads/payment_proofs/67ecc195084db_Screenshot (233).png'),
(280, 'BK-20250403-C84F14', 3, 'Fammela Nicole Jumig De Guzman', '0', NULL, 'fammeladeguzman21@gmail.com', '0', '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:01:47', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(281, 'BK-20250403-CEDE42', 3, 'Fammela Nicole Jumig De Guzman', '0', NULL, 'fammeladeguzman21@gmail.com', '0', '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:01:49', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(283, 'BK-20250403-341668', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:26:18', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(284, 'BK-20250403-842C49', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:26:20', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(285, 'BK-20250403-5D66C1', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:27:42', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(286, 'BK-20250403-881118', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'maya', 18000, 0.00, 'finished', '2025-04-03 17:30:34', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(287, 'BK-20250403-BEAF28', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'maya', 18000, 0.00, 'finished', '2025-04-03 17:30:36', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(288, 'BK-20250403-E0A5BA', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:33:34', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(289, 'BK-20250403-1EDE30', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:33:36', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(290, 'BK-20250403-909750', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 18000, 0.00, 'finished', '2025-04-03 17:35:55', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(291, 'BK-20250403-24F7DD', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 12000, 0.00, 'finished', '2025-04-03 17:36:31', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(292, 'BK-20250403-47BC5E', 3, NULL, NULL, NULL, 'fammeladeguzman21@gmail.com', NULL, '2025-04-04', '2025-04-07', '23:23:00', 2, NULL, NULL, 'Full Payment', 'gcash', 12000, 0.00, 'finished', '2025-04-03 17:37:07', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(293, 'BK-20250404-07AE9B', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 14800, 0.00, 'finished', '2025-04-04 03:52:00', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404055200_67ef5760b073b.png'),
(294, 'BK-20250404-5C9E82', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 14800, 0.00, 'finished', '2025-04-04 03:52:28', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404055228_67ef577c6678c.png'),
(295, 'BK-20250404-50CC1C', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 14800, 0.00, 'finished', '2025-04-04 03:52:32', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404055232_67ef578048a6d.png'),
(296, 'BK-20250404-1AD0AB', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'gcash', 3700, 0.00, 'finished', '2025-04-04 03:53:00', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404055300_67ef579caf340.png'),
(297, 'BK-20250404-1AC7FE', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'gcash', 3700, 0.00, 'finished', '2025-04-04 03:53:02', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404055302_67ef579ed93f1.png'),
(309, 'BK-20250404-0AB1C9', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'gcash', 11100, 0.00, 'finished', '2025-04-04 04:33:03', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404063303_67ef60ff37533.png'),
(310, 'BK-20250404-2919CD', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'gcash', 11100, 0.00, 'finished', '2025-04-04 04:33:10', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404063310_67ef6106783b0.png'),
(311, 'BK-20250404-7E77E5', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'gcash', 11100, 0.00, 'finished', '2025-04-04 04:33:18', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404063318_67ef610e991d4.png'),
(312, 'BK-20250404-7A304B', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 11100, 0.00, 'finished', '2025-04-04 04:34:09', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404063409_67ef614102e9c.png'),
(313, 'BK-20250404-98FD21', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 11100, 0.00, 'finished', '2025-04-04 04:34:15', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404063414_67ef6146c8ee0.png'),
(314, 'BK-20250404-395290', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Full Payment', 'maya', 11100, 0.00, 'finished', '2025-04-04 04:43:24', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404064324_67ef636ca19d3.png'),
(315, 'BK-20250404-C3F107', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 11840, 0.00, 'finished', '2025-04-04 04:54:49', 1, NULL, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250404065449_67ef6619bbd5b.png'),
(316, 'BK-20250404-D19FAE', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 11840, 0.00, 'finished', '2025-04-04 04:59:22', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(317, 'BK-20250404-3FF8D7', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 11840, 0.00, 'finished', '2025-04-04 04:59:25', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(318, 'BK-20250404-613A4D', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 11100, 0.00, 'finished', '2025-04-04 05:15:09', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(319, 'BK-20250404-3DC335', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 11100, 0.00, 'finished', '2025-04-04 05:15:12', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(320, 'BK-20250404-7936E0', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:20:25', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(321, 'BK-20250404-DBF96E', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:20:28', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(322, 'BK-20250404-5EEF5B', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:20:34', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(323, 'BK-20250404-AC1C67', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:29:12', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(324, 'BK-20250404-F9A85C', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:29:52', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(325, 'BK-20250404-CD6EEC', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:33:30', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(326, 'BK-20250404-3CA23F', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:39:39', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(327, 'BK-20250404-B021C2', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:39:42', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(328, 'BK-20250404-339D8C', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:40:45', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(329, 'BK-20250404-675CDC', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:25', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(330, 'BK-20250404-D7090C', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:27', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(331, 'BK-20250404-36C793', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:30', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(332, 'BK-20250404-C314BA', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:32', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(333, 'BK-20250404-F7ECC8', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:35', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(334, 'BK-20250404-8DFDC3', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:37', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(335, 'BK-20250404-B00DF7', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:41', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(336, 'BK-20250404-83993A', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:41:43', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(337, 'BK-20250404-DDBA9F', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:46:11', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(338, 'BK-20250404-85815E', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:46:15', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(339, 'BK-20250404-08DB50', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:46:44', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(340, 'BK-20250404-AC4BDA', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:46:47', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(341, 'BK-20250404-0CC835', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:49:20', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(342, 'BK-20250404-634720', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:49:24', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(343, 'BK-20250404-50B53E', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:49:26', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(344, 'BK-20250404-7B2229', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:49:28', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(345, 'BK-20250404-B07576', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:49:31', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(346, 'BK-20250404-BBED51', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:50:50', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(347, 'BK-20250404-4435C2', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:50:53', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(348, 'BK-20250404-8F7376', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:50:56', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(349, 'BK-20250404-5B396C', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:56:24', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(350, 'BK-20250404-C73881', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '06:51:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 14800, 0.00, 'finished', '2025-04-04 05:56:28', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(351, 'BK-20250404-26D16A', 3, '', '', 'Online', 'fammeladeguzman21@gmail.com', '', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, 0.00, 'finished', '2025-04-04 06:00:58', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(352, 'BK-20250404-CA3585', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:03:43', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(353, 'BK-20250404-067773', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:09:03', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(354, 'BK-20250404-F23E50', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:09:06', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(355, 'BK-20250404-B52CCE', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:12:21', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(356, 'BK-20250404-856AD3', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:12:26', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(357, 'BK-20250404-549BF0', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:12:30', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(358, 'BK-20250404-07B67F', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:12:32', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(359, 'BK-20250404-2067B5', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:12:36', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(361, 'BK-20250404-796791', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 7500, 0.00, 'finished', '2025-04-04 06:22:19', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(362, 'BK-20250404-C91D20', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-30', '19:00:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 384800, 0.00, 'finished', '2025-04-04 06:23:58', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(363, 'BK-20250404-6749CA', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 2000, 0.00, 'finished', '2025-04-04 06:24:34', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(364, 'BK-20250404-B46327', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 2000, 0.00, 'finished', '2025-04-04 06:24:45', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(365, 'BK-20250404-1AEC1E', 3, 'Kenjo ', 'Marimon', 'Online', 'fammeladeguzman21@gmail.com', '', '2025-04-04', '2025-04-05', '19:00:00', 1, NULL, NULL, 'Full Payment', 'gcash', 4000, 0.00, 'finished', '2025-04-04 06:39:28', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(366, 'BK-20250405-6D2DEF', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 8880, 0.00, 'pending', '2025-04-05 11:14:24', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(367, 'BK-20250405-0E04AE', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 8880, 0.00, 'pending', '2025-04-05 11:14:34', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(368, 'BK-20250405-7F1BE4', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 8880, 0.00, 'pending', '2025-04-05 11:14:38', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(369, 'BK-20250405-009551', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 8880, 0.00, 'pending', '2025-04-05 11:14:41', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(370, 'BK-20250405-F602CD', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 8880, 0.00, 'pending', '2025-04-05 11:19:43', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(371, 'BK-20250405-AA6F37', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 11:21:40', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(372, 'BK-20250405-2C48DC', 8, 'Christian', 'Realisan', NULL, 'christianrealisan45aa@gmail.com', '09412222222', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 11:28:42', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(373, 'BK-20250405-8656B2', 8, 'Christian', 'Realisan', NULL, 'christianrealisan45aa@gmail.com', '09412222222', '2025-04-06', '2025-05-09', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 122100, 0.00, 'pending', '2025-04-05 11:30:51', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(374, 'BK-20250405-974B0C', 8, 'Christian', 'Realisan', NULL, 'christianrealisan45aa@gmail.com', '09412222222', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, NULL, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 11:38:47', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(377, 'BK-20250405-808F22', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 12:50:56', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(378, 'BK-20250405-9ED129', 8, 'Kenjo', 'Marimon', NULL, 'christianrealisan45aa@gmail.com', '', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 12:51:23', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(379, 'BK-20250405-BBDD0C', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 2960, 0.00, 'pending', '2025-04-05 14:04:40', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(380, 'BK-20250405-1A9647', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, 1, 'Partial Payment', 'gcash', 2960, 0.00, 'pending', '2025-04-05 14:04:45', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(381, 'BK-20250405-EE02C9', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, 1, 'Partial Payment', 'gcash', 2960, 0.00, 'pending', '2025-04-05 14:04:48', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(382, 'BK-20250405-BA373F', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, 1, 'Partial Payment', 'gcash', 2960, 0.00, 'pending', '2025-04-05 14:07:15', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(383, 'BK-20250405-4F50DA', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-08', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 0, 0.00, 'pending', '2025-04-05 14:07:51', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(384, 'BK-20250405-27B961', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-08', '23:53:00', 1, NULL, 1, 'Partial Payment', 'gcash', 0, 0.00, 'pending', '2025-04-05 14:07:57', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(385, 'BK-20250405-46B2B3', 8, 'Christian', 'Realisan', NULL, 'christianrealisan45aa@gmail.com', '09412222222', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 3, 'Partial Payment', 'gcash', 11100, 0.00, 'pending', '2025-04-05 14:09:11', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(386, 'BK-20250405-B77333', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 14:11:25', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(387, 'BK-20250405-3130A8', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, NULL, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 14:11:27', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(388, 'BK-20250405-1C8A09', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '23:53:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-05 14:27:04', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(389, 'BK-20250405-EBD5B6', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-07', '03:29:00', 1, 1, 1, 'Full Payment', 'maya', 3700, 0.00, 'pending', '2025-04-05 14:30:11', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(390, 'BK-20250405-4A14A6', NULL, '', '', NULL, '', '', '2025-04-06', '2025-04-08', '01:48:00', 1, 1, 3, 'Partial Payment', 'gcash', 22200, 0.00, 'pending', '2025-04-05 21:48:32', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(391, 'BK-20250406-A79AC1', 8, 'Christian', 'Realisan', NULL, 'christianrealisan45aa@gmail.com', '09412222222', '2025-04-06', '2025-04-10', '21:17:00', 2, 1, 1, 'Partial Payment', 'gcash', 14800, 0.00, 'pending', '2025-04-06 08:18:07', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(392, 'BK-20250406-5E0496', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '21:43:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 08:43:22', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(393, 'BK-20250406-DB6042', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '21:43:00', 1, NULL, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 08:43:26', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(394, 'BK-20250406-507BA7', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '21:43:00', 1, NULL, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 08:43:29', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(395, 'BK-20250406-1435E7', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-05-01', '21:47:00', 1, 1, 1, 'Partial Payment', 'gcash', 74000, 0.00, 'pending', '2025-04-06 08:47:52', 0, NULL, 0, NULL, 0.00, 0.00, '', ''),
(396, 'BK-20250406-DCFF2B', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-25', '21:56:00', 1, 1, 1, 'Partial Payment', 'gcash', 56240, 0.00, 'pending', '2025-04-06 08:58:42', 19, 28120.00, 28120, NULL, 14060.00, 0.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406105842_67f242420e36a.png'),
(397, 'BK-20250406-CA566E', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-25', '21:56:00', 1, NULL, 1, 'Partial Payment', 'gcash', 56240, 0.00, 'pending', '2025-04-06 08:58:45', 19, 28120.00, 28120, NULL, 14060.00, 0.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406105845_67f242450af0a.png'),
(398, 'BK-20250406-917B48', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-22', '12:03:00', 1, 1, 3, 'Partial Payment', 'gcash', 142080, 0.00, 'pending', '2025-04-06 09:04:08', 16, 71040.00, 71040, 'PWD', 28416.00, 20.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406110408_67f243889f77b.png');
INSERT INTO `bookings` (`booking_id`, `booking_reference`, `user_id`, `first_name`, `last_name`, `booking_type`, `email`, `contact`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `room_type_id`, `room_quantity`, `payment_option`, `payment_method`, `total_amount`, `extra_charges`, `status`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `discount_type`, `discount_amount`, `discount_percentage`, `payment_reference`, `payment_proof`) VALUES
(399, 'BK-20250406-AC4148', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-22', '12:03:00', 1, NULL, 1, 'Partial Payment', 'gcash', 142080, 0.00, 'pending', '2025-04-06 09:05:25', 16, 71040.00, 71040, 'PWD', 28416.00, 20.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406110525_67f243d5658b2.png'),
(400, 'BK-20250406-546552', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-18', '22:06:00', 1, 1, 2, 'Partial Payment', 'gcash', 71040, 0.00, 'pending', '2025-04-06 09:06:24', 12, 35520.00, 35520, 'Senior Citizen', 14208.00, 20.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406110624_67f2441075ca2.png'),
(401, 'BK-20250406-B199B8', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '22:20:00', 1, 1, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 09:20:27', 1, 1850.00, 1850, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406112026_67f2475ae7674.png'),
(402, 'BK-20250406-36B83B', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '22:20:00', 1, NULL, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 09:20:31', 1, 1850.00, 1850, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406112030_67f2475ef18b8.png'),
(403, 'BK-20250406-F0D915', NULL, 'christiandfdfgg', 'realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-06', '2025-04-07', '22:20:00', 1, NULL, 1, 'Partial Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-06 09:20:34', 1, 1850.00, 1850, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_guest_20250406112034_67f2476218a05.png'),
(404, 'BK-20250406-626B38', 8, '', '', NULL, '', '', '2025-04-06', '2025-04-09', '12:17:00', 1, 1, 1, 'Full Payment', 'gcash', 11100, 0.00, 'pending', '2025-04-06 10:17:36', 3, 11100.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_8_20250406121736_67f254c00e230.png'),
(405, 'BK-20250406-BE2BCF', 8, '', '', NULL, '', '', '2025-04-06', '2025-04-09', '12:17:00', 1, NULL, 1, 'Full Payment', 'gcash', 11100, 0.00, 'pending', '2025-04-06 10:17:38', 3, 11100.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_8_20250406121738_67f254c2b5562.png'),
(406, 'BK-20250408-B3804C', NULL, 'Christian', 'Realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-08', '2025-04-09', '16:40:00', 1, 1, 2, 'Partial Payment', 'gcash', 5920, 0.00, 'pending', '2025-04-08 03:40:25', 1, 2960.00, 2960, 'PWD', 1184.00, 20.00, '5434535', 'uploads/payment_proofs/payment_guest_20250408054025_67f49aa9d4671.jpg'),
(407, 'BK-20250408-E6EE96', NULL, 'Christian', 'Realisan', NULL, 'chano@gmail.com', '09123456789', '2025-04-08', '2025-04-09', '16:40:00', 1, NULL, 1, 'Partial Payment', 'gcash', 5920, 0.00, 'pending', '2025-04-08 03:40:29', 1, 2960.00, 2960, 'PWD', 1184.00, 20.00, '5434535', 'uploads/payment_proofs/payment_guest_20250408054029_67f49aad67273.jpg'),
(408, 'BK-20250408-0D67AF', 3, '', '', NULL, '', '', '2025-04-08', '2025-04-09', '16:58:00', 1, 1, 1, 'Full Payment', 'gcash', 3700, 0.00, 'pending', '2025-04-08 03:59:06', 1, 3700.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250408055905_67f49f09c8bb7.jpg'),
(409, 'BK-20250408-22222D', 3, '', '', NULL, '', '', '2025-04-08', '2025-05-09', '19:08:00', 6, 1, 1, 'Full Payment', 'gcash', 115700, 0.00, 'pending', '2025-04-08 04:08:46', 31, 115700.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250408060846_67f4a14edc073.jpg'),
(410, 'BK-20250408-4DA8E9', 3, '', '', NULL, '', '', '2025-04-08', '2025-05-09', '19:08:00', 6, NULL, 1, 'Full Payment', 'gcash', 115700, 0.00, 'pending', '2025-04-08 04:08:51', 31, 115700.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250408060851_67f4a153147d0.jpg'),
(411, 'BK-20250408-7D9F43', 3, '', '', NULL, '', '', '2025-04-08', '2025-04-09', '17:22:00', 6, 1, 1, 'Full Payment', 'gcash', 6700, 0.00, 'pending', '2025-04-08 04:22:36', 1, 6700.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250408062236_67f4a48c8d735.jpg'),
(412, 'BK-20250408-0E3C1C', 3, '', '', NULL, '', '', '2025-04-08', '2025-04-09', '17:22:00', 6, NULL, 1, 'Full Payment', 'gcash', 6700, 0.00, 'pending', '2025-04-08 04:22:42', 1, 6700.00, 0, NULL, 0.00, 0.00, '5434535', 'uploads/payment_proofs/payment_3_20250408062242_67f4a492b4a59.jpg');

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
-- Table structure for table `booking_list`
--

CREATE TABLE `booking_list` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sender_type` enum('user','admin','system') NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply_to_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `message`, `sender_type`, `read_status`, `created_at`, `reply_to_id`) VALUES
(1, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 04:35:02', NULL),
(2, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:35:02', NULL),
(3, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 04:35:02', NULL),
(4, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:35:09', NULL),
(5, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:35:09', NULL),
(6, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:35:09', NULL),
(7, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:37:46', NULL),
(8, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:37:46', NULL),
(9, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:37:46', NULL),
(10, 'guest_y71mi6rv0', 'To make a booking, please visit our Rooms page or click the \"Book Now\" button.', 'system', 0, '2025-04-08 04:37:46', NULL),
(11, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:45:28', NULL),
(12, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:45:28', NULL),
(13, 'guest_y71mi6rv0', 'price', 'user', 0, '2025-04-08 04:45:33', NULL),
(14, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:45:33', NULL),
(15, 'guest_y71mi6rv0', 'hi', 'user', 0, '2025-04-08 04:45:41', NULL),
(16, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:45:41', NULL),
(17, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 04:49:18', NULL),
(18, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 04:49:18', NULL),
(19, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 05:04:40', NULL),
(20, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 05:04:40', NULL),
(21, 'guest_y71mi6rv0', 'book', 'user', 0, '2025-04-08 05:04:46', NULL),
(22, 'guest_y71mi6rv0', 'Hello po good evening ', 'admin', 0, '2025-04-08 05:04:46', NULL),
(23, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 05:13:37', NULL),
(24, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 05:13:37', NULL),
(25, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 05:20:19', NULL),
(26, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 05:20:19', NULL),
(27, 'guest_y71mi6rv0', 'hello', 'user', 0, '2025-04-08 09:24:29', NULL),
(28, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 09:24:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `icon_class` varchar(50) NOT NULL,
  `display_text` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `is_external` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `icon_class`, `display_text`, `link`, `is_external`, `display_order`, `active`) VALUES
(1, 'fab fa-facebook', 'Casa Estela Boutique Hotel & Cafés', 'https://web.facebook.com/casaestelahotelcafe', 1, 1, 1),
(2, 'fas fa-envelope', 'casaestelahotelcafe@gmail.com', 'mailto:casaestelahotelcafe@gmail.com', 0, 2, 1),
(3, 'fas fa-phone', '0908 747 4892', 'tel:+09087474892', 0, 3, 1),
(4, 'fab fa-twitter', '@casaestelahlcf', '#', 1, 4, 1),
(5, 'fab fa-instagram', '@casaestelahotelcafe', 'https://www.instagram.com/casaestelahotelcafe', 1, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Christian', 'Realisan', 'chano@gmail.com', 'Hi, I would like to inquire about the Family Room...', 'new', '2025-03-18 05:35:12', '2025-03-18 05:35:12'),
(2, 'Christian', 'Realisan', 'chano@gmail.com', 'Hi, I would like to inquire about the Family Room...', 'new', '2025-03-18 10:42:37', '2025-03-18 10:42:37'),
(3, 'Christian', 'Realisan', 'chano@gmail.com', 'add', 'new', '2025-03-24 22:21:51', '2025-03-24 22:21:51'),
(4, 'Christian', 'Realisan', 'chano@gmail.com', 'i WANT TO BOOK ROOMS FOR 2NIGHTS', 'new', '2025-03-27 07:43:42', '2025-03-27 07:43:42'),
(5, 'Christian', 'Realisan', 'chano@gmail.com', 'aaa', 'new', '2025-04-08 04:23:33', '2025-04-08 04:23:33');

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
  `extra_guests` int(11) DEFAULT 0,
  `extra_guest_charge` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `remaining_balance` decimal(10,2) NOT NULL,
  `reservation_date` date NOT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `reserve_type` varchar(50) DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_source` varchar(50) DEFAULT 'Regular Booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `user_id`, `package_name`, `package_price`, `base_price`, `overtime_hours`, `overtime_charge`, `extra_guests`, `extra_guest_charge`, `total_amount`, `paid_amount`, `remaining_balance`, `reservation_date`, `event_type`, `start_time`, `end_time`, `number_of_guests`, `payment_method`, `payment_type`, `reference_number`, `payment_proof`, `booking_status`, `reserve_type`, `created_at`, `updated_at`, `booking_source`) VALUES
('', 5, 'Package A', 0.00, 47500.00, 3, 6000.00, 0, 0.00, 53500.00, 26750.00, 0.00, '2025-04-05', NULL, '14:41:00', '22:41:00', 33, 'maya', 'downpayment', '54646464646', 'uploads/payment_proofs/payment_67e2b29f980c5.png', 'finished', 'Regular', '2025-03-25 13:41:51', '2025-03-25 21:01:46', 'Regular'),
('45645645645', 5, 'Package A', 61500.00, 47500.00, NULL, 14000.00, 0, 0.00, 61500.00, 30750.00, 30750.00, '2025-03-29', NULL, '09:46:00', '21:46:00', 22, 'gcash', 'downpayment', '45645645645', 'uploads/payment_proofs/payment_67e316438ec18.png', 'finished', NULL, '2025-03-25 20:46:59', '2025-03-28 11:52:25', 'Regular Booking'),
('4564564564555', 5, 'Package A', 79500.00, 63500.00, 8, 16000.00, 0, 0.00, 79500.00, 79500.00, 0.00, '2025-03-31', NULL, '09:48:00', '22:48:00', 37, 'gcash', 'full', '4564564564555', 'uploads/payment_proofs/payment_67e316cc9e0b4.png', 'finished', NULL, '2025-03-25 20:49:16', '2025-03-25 21:02:01', 'Regular Booking'),
('TB20250213191100509', 1, 'Standard Package', 73500.00, 47500.00, 13, 26000.00, 0, 0.00, 73500.00, 36750.00, 36750.00, '2025-02-14', NULL, '02:10:00', '20:10:00', 30, 'cash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-02-13 18:11:00', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250215175540283', 3, 'Standard Package', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-02-16', NULL, '00:55:00', '04:55:00', 30, 'cash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-02-15 16:55:41', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250217230534902', 1, 'Standard Package', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-02-18', NULL, '06:05:00', '12:05:00', 29, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-02-17 22:05:34', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250218075209221', 1, 'Venue Rental Only', 36000.00, 20000.00, 8, 16000.00, 0, 0.00, 36000.00, 36000.00, 0.00, '2025-02-19', NULL, '02:51:00', '14:56:00', 3, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-02-18 06:52:09', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250218125006736', 1, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-10-07', NULL, '14:22:00', '15:10:00', 3, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-02-18 11:50:06', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250218140704928', 1, 'Premium Package', 65000.00, 55000.00, 5, 10000.00, 0, 0.00, 65000.00, 65000.00, 0.00, '2025-02-19', NULL, '07:05:00', '17:05:00', 30, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-02-18 13:07:04', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250307160503961', 5, 'Standard Package', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-07', NULL, '16:04:00', '19:04:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-07 15:05:03', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250307161612625', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-07', NULL, '23:16:00', '23:20:00', 22, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-07 15:16:12', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250316195555440', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 10000.00, 10000.00, '2025-03-16', NULL, '11:55:00', '14:55:00', 50, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-16 18:55:55', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250316230628509', 5, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-03-16', NULL, '15:06:00', '18:06:00', 50, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-16 22:06:28', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250316230744339', 5, 'Package B', 55000.00, 55000.00, 0, 0.00, 0, 0.00, 55000.00, 27500.00, 27500.00, '2025-03-16', NULL, '15:07:00', '18:07:00', 33, 'bank', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-16 22:07:44', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317004031985', 5, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-22', NULL, '16:40:00', '22:40:00', 50, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-16 23:40:31', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250317005409848', 5, 'Venue Rental Only', 24000.00, 22000.00, 1, 2000.00, 0, 0.00, 24000.00, 24000.00, 0.00, '2025-03-16', NULL, '16:53:00', '22:53:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-16 23:54:09', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317011912887', 5, 'Package C', 76800.00, 76800.00, 0, 0.00, 0, 0.00, 76800.00, 38400.00, 38400.00, '2025-03-17', NULL, '17:18:00', '17:19:00', 12, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:19:12', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317012503178', 5, 'Venue Rental Only', 30000.00, 20000.00, 5, 10000.00, 0, 0.00, 30000.00, 30000.00, 0.00, '2025-03-17', NULL, '10:24:00', '20:24:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:25:03', '2025-03-18 05:04:20', 'Regular Booking'),
('TB20250317013025687', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-18', NULL, '17:30:00', '20:30:00', 34, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:30:25', '2025-03-18 21:20:11', 'Regular Booking'),
('TB20250317013543414', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-18', NULL, '18:35:00', '19:35:00', 44, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:35:43', '2025-03-18 21:20:11', 'Regular Booking'),
('TB20250317014201141', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-19', NULL, '17:41:00', '18:41:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:42:01', '2025-03-19 06:39:16', 'Regular Booking'),
('TB20250317014854709', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-03-19', NULL, '17:48:00', '22:48:00', 33, 'cash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 00:48:54', '2025-03-19 19:53:04', 'Regular Booking'),
('TB20250317064234820', 5, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-17', NULL, '06:42:00', '11:42:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 05:42:34', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317070722156', 5, 'Package A', 65500.00, 47500.00, 9, 18000.00, 0, 0.00, 65500.00, 65500.00, 0.00, '2025-03-17', NULL, '08:07:00', '22:07:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 06:07:22', '2025-03-18 05:08:18', 'Regular Booking'),
('TB20250317074854260', 5, 'Venue Rental Only', 32000.00, 20000.00, 6, 12000.00, 0, 0.00, 32000.00, 32000.00, 0.00, '2025-03-17', NULL, '07:48:00', '18:48:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 06:48:54', '2025-03-18 01:53:45', 'Regular Booking'),
('TB20250317081910282', 5, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-03-17', NULL, '08:18:00', '10:19:00', 23, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 07:19:10', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317081950574', 5, 'Package A', 53500.00, 47500.00, 3, 6000.00, 0, 0.00, 53500.00, 26750.00, 26750.00, '2025-03-18', NULL, '09:19:00', '17:19:00', 44, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 07:19:50', '2025-03-18 21:20:11', 'Regular Booking'),
('TB20250317082530169', 5, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 10000.00, 10000.00, '2025-03-17', NULL, '09:25:00', '11:25:00', 44, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 07:25:30', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317084059437', 5, 'Package C', 76800.00, 76800.00, 0, 0.00, 0, 0.00, 76800.00, 38400.00, 38400.00, '2025-03-18', NULL, '06:40:00', '09:40:00', 50, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 07:40:59', '2025-03-18 21:20:11', 'Regular Booking'),
('TB20250317183036277', 8, 'Package A', 57500.00, 47500.00, 5, 10000.00, 0, 0.00, 57500.00, 57500.00, 0.00, '2025-03-17', NULL, '06:30:00', '16:30:00', 44, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 17:30:36', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317183157736', 8, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-17', NULL, '10:31:00', '15:31:00', 44, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 17:31:57', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317184217302', 8, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-17', NULL, '10:42:00', '16:42:00', 44, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 17:42:17', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317184918533', 8, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-17', NULL, '10:49:00', '15:49:00', 50, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 17:49:18', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317185539299', 8, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-17', NULL, '10:55:00', '15:55:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 17:55:39', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317192624624', 8, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-17', NULL, '11:26:00', '17:26:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:26:24', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317194925616', 8, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-17', NULL, '11:49:00', '17:49:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:49:25', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317195013180', 8, 'Package A', 57500.00, 47500.00, 5, 10000.00, 0, 0.00, 57500.00, 28750.00, 28750.00, '2025-03-17', NULL, '11:50:00', '21:50:00', 33, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:50:13', '2025-03-18 05:04:20', 'Regular Booking'),
('TB20250317195424328', 8, 'Package B', 59000.00, 55000.00, 2, 4000.00, 0, 0.00, 59000.00, 59000.00, 0.00, '2025-03-17', NULL, '11:54:00', '18:54:00', 43, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:54:24', '2025-03-18 05:04:20', 'Regular Booking'),
('TB20250317195557430', 8, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 11000.00, 11000.00, '2025-03-17', NULL, '11:55:00', '17:55:00', 44, 'bank', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:55:57', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250317195822191', 8, 'Package B', 55000.00, 55000.00, 0, 0.00, 0, 0.00, 55000.00, 55000.00, 0.00, '2025-03-17', NULL, '11:58:00', '15:58:00', 50, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-17 18:58:22', '2025-03-18 01:38:08', 'Regular Booking'),
('TB20250318023312686', 8, 'Package C', 76800.00, 76800.00, 0, 0.00, 0, 0.00, 76800.00, 38400.00, 38400.00, '2025-03-18', NULL, '18:33:00', '21:33:00', 33, 'gcash', 'downpayment', NULL, NULL, 'finished', 'Regular', '2025-03-18 01:33:12', '2025-03-18 01:35:59', 'Regular Booking'),
('TB20250318063938418', 9, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-18', NULL, '18:39:00', '22:39:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-18 05:39:38', '2025-03-18 21:20:11', 'Regular Booking'),
('TB20250319205344887', 9, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-03-20', NULL, '08:53:00', '14:53:00', 50, 'bank', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-19 19:53:44', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250319205611656', 9, 'Package C', 76800.00, 76800.00, 0, 0.00, 0, 0.00, 76800.00, 76800.00, 0.00, '2025-03-20', NULL, '08:56:00', '13:56:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-19 19:56:11', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250320001730399', 9, 'Package A', 57500.00, 47500.00, 5, 10000.00, 0, 0.00, 57500.00, 57500.00, 0.00, '2025-03-20', NULL, '12:17:00', '22:17:00', 33, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-19 23:17:30', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250320003131203', 9, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-20', NULL, '12:31:00', '16:31:00', 44, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-19 23:31:31', '2025-03-20 04:34:50', 'Regular Booking'),
('TB20250320003603965', 9, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-20', NULL, '12:35:00', '17:35:00', 50, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-19 23:36:03', '2025-03-19 23:55:47', 'Regular Booking'),
('TB20250320040206925', 9, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-03-20', NULL, '16:01:00', '22:01:00', 22, 'gcash', 'full', NULL, NULL, 'finished', 'Regular', '2025-03-20 03:02:06', '2025-03-20 04:32:31', 'Regular Booking'),
('TB20250320050901247', 9, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-03-28', NULL, '17:08:00', '22:08:00', 44, 'cash', 'downpayment', NULL, NULL, 'finished', NULL, '2025-03-20 04:09:01', '2025-03-20 04:32:38', 'Regular Booking'),
('TB20250320050957811', 9, 'Package B', 57000.00, 55000.00, 1, 2000.00, 0, 0.00, 57000.00, 28500.00, 28500.00, '2025-03-31', NULL, '17:09:00', '23:00:00', 43, 'bank', 'downpayment', NULL, NULL, 'finished', NULL, '2025-03-20 04:09:57', '2025-03-20 04:32:42', 'Regular Booking'),
('TB20250320051202362', 9, 'Package B', 55000.00, 55000.00, 0, 0.00, 0, 0.00, 55000.00, 55000.00, 0.00, '2025-03-31', NULL, '17:11:00', '22:11:00', 33, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-20 04:12:02', '2025-03-20 04:32:45', 'Regular Booking'),
('TB20250320052528495', 9, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-21', NULL, '17:25:00', '22:25:00', 33, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-20 04:25:28', '2025-03-20 04:32:49', 'Regular Booking'),
('TB20250320053713109', 9, 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 10000.00, 10000.00, '2025-03-21', NULL, '17:36:00', '22:36:00', 33, 'gcash', 'downpayment', NULL, NULL, 'finished', NULL, '2025-03-20 04:37:13', '2025-03-20 05:19:13', 'Regular Booking'),
('TB20250320054810170', 9, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-03-21', NULL, '17:47:00', '22:48:00', 44, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-20 04:48:10', '2025-03-25 20:59:22', 'Regular Booking'),
('TB20250320054957804', 9, 'Venue Rental Only', 28000.00, 20000.00, 4, 8000.00, 0, 0.00, 28000.00, 14000.00, 14000.00, '2025-03-22', NULL, '13:49:00', '22:49:00', 33, 'gcash', 'downpayment', NULL, NULL, 'finished', NULL, '2025-03-20 04:49:57', '2025-03-25 20:59:28', 'Regular Booking'),
('TB20250320060354697', 9, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-03-20', NULL, '17:03:00', '22:53:00', 50, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-20 05:03:54', '2025-03-20 05:20:04', 'Regular Booking'),
('TB20250320061728718', 9, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-28', NULL, '16:17:00', '22:17:00', 34, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-20 05:17:28', '2025-03-25 20:59:33', 'Regular Booking'),
('TB20250325073734422', 5, 'Package C', 80800.00, 76800.00, 2, 4000.00, 0, 0.00, 80800.00, 80800.00, 0.00, '2025-03-26', NULL, '15:37:00', '22:37:00', 44, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-25 06:37:34', '2025-03-25 20:59:38', 'Regular Booking'),
('TB20250325142626321', 5, 'Package A', 51500.00, 47500.00, 2, 4000.00, 0, 0.00, 51500.00, 51500.00, 0.00, '2025-03-27', NULL, '14:25:00', '20:26:00', 44, 'gcash', 'full', NULL, NULL, 'finished', NULL, '2025-03-25 13:26:26', '2025-03-25 20:59:42', 'Regular Booking'),
('TB20250325143259164', 5, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 23750.00, 23750.00, '2025-04-04', NULL, '14:32:00', '19:32:00', 44, 'maya', 'downpayment', NULL, NULL, 'finished', NULL, '2025-03-25 13:32:59', '2025-03-25 13:34:46', 'Regular Booking'),
('TB20250325215821392', 5, 'Package A', 75500.00, 61500.00, 7, 14000.00, 0, 0.00, 75500.00, 37750.00, 37750.00, '2025-04-04', NULL, '09:56:00', '21:56:00', 23, 'gcash', 'downpayment', '111111111111111111', 'uploads/payment_proofs/payment_67e318ed1e627_20250325_215821.png', 'finished', NULL, '2025-03-25 20:58:21', '2025-03-25 21:02:05', 'Regular Booking'),
('TB20250325234243146', 5, 'Package A', 59500.00, 47500.00, 6, 12000.00, 0, 0.00, 59500.00, 59500.00, 0.00, '2025-03-26', NULL, '11:42:00', '22:42:00', 50, 'gcash', 'full', '54646464646', 'uploads/payment_proofs/payment_67e33163546d8_20250325_234243.png', 'finished', NULL, '2025-03-25 22:42:43', '2025-03-26 00:07:39', 'Regular Booking'),
('TB20250326010654163', 5, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 24750.00, 24750.00, '2025-03-27', NULL, '13:06:00', '19:06:00', 22, 'gcash', 'downpayment', '54646464646', 'uploads/payment_proofs/payment_67e3451eb2022_20250326_010654.png', 'finished', NULL, '2025-03-26 00:06:54', '2025-03-26 07:02:22', 'Regular Booking'),
('TB20250326063522778', 5, 'Package A', 67500.00, 57500.00, 5, 10000.00, 0, 0.00, 67500.00, 67500.00, 0.00, '2025-03-29', 'Wedding', '12:34:00', '22:34:00', 50, 'gcash', 'full', '54646464646', 'uploads/payment_proofs/payment_67e3921aa84ad_20250326_063522.png', 'finished ', NULL, '2025-03-26 05:35:22', '2025-03-26 07:02:30', 'Regular Booking'),
('TB20250326080453909', 5, 'Venue Rental Only', 34000.00, 20000.00, 7, 14000.00, 0, 0.00, 34000.00, 34000.00, 0.00, '2025-03-26', 'Wedding', '08:04:00', '20:04:00', 45, 'gcash', 'full', '111111111111111111', 'uploads/payment_proofs/payment_67e3a7159e714_20250326_080453.png', 'finished', NULL, '2025-03-26 07:04:53', '2025-03-26 07:05:26', 'Regular Booking'),
('TB20250326080627185', 5, 'Package A', 63500.00, 47500.00, 8, 16000.00, 0, 0.00, 63500.00, 31750.00, 31750.00, '2025-03-26', 'Corporate', '08:05:00', '20:08:00', 33, 'gcash', 'downpayment', '111111111111111111', 'uploads/payment_proofs/payment_67e3a773ad08c_20250326_080627.png', 'completed', NULL, '2025-03-26 07:06:27', '2025-03-26 07:46:45', 'Regular Booking'),
('TB20250326235941982', 2, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 49500.00, 0.00, '2025-03-27', 'Wedding', '06:58:00', '12:04:00', 34, 'gcash', 'full', '111111111111111111', 'uploads/payment_proofs/payment_67e486ddabeb0_20250326_235941.png', 'completed', NULL, '2025-03-26 22:59:41', '2025-03-28 11:52:53', 'Regular Booking'),
('TB20250327014233827', 2, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 24750.00, 24750.00, '2025-03-28', 'Birthday', '13:41:00', '19:41:00', 34, 'gcash', 'downpayment', '111111111111111111', 'uploads/payment_proofs/payment_67e49ef949938_20250327_014233.png', 'completed', NULL, '2025-03-27 00:42:33', '2025-03-28 11:53:01', 'Regular Booking'),
('TB20250327080453334', 1, 'Venue Rental Only', 52000.00, 36000.00, 8, 16000.00, 0, 0.00, 52000.00, 52000.00, 0.00, '2025-03-29', 'Wedding', '08:02:00', '20:03:00', 50, 'gcash', 'full', '111111111111111111', 'uploads/payment_proofs/payment_67e4f895cd7b3_20250327_080453.png', 'completed', NULL, '2025-03-27 07:04:53', '2025-03-28 11:53:17', 'Regular Booking'),
('TB20250328082618928', 5, 'Package B', 69000.00, 55000.00, 7, 14000.00, 0, 0.00, 69000.00, 69000.00, 0.00, '2025-03-29', 'Wedding', '09:25:00', '21:25:00', 33, 'gcash', 'full', '45645645645', 'uploads/payment_proofs/payment_67e64f1ac2b72_20250328_082618.png', 'completed', NULL, '2025-03-28 07:26:18', '2025-03-28 11:53:24', 'Regular Booking'),
('TB20250328104140708', 5, 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-03-30', 'Date', '10:41:00', '15:41:00', 44, 'gcash', 'full', '54646464646', 'uploads/payment_proofs/payment_67e66ed40c582_20250328_104140.png', 'completed', NULL, '2025-03-28 09:41:40', '2025-03-28 11:53:34', 'Regular Booking'),
('TB20250328125424893', 5, 'Package A', 65500.00, 59500.00, 3, 6000.00, 0, 0.00, 65500.00, 32750.00, 32750.00, '2025-03-31', 'Wedding', '12:53:00', '20:53:00', 22, 'gcash', 'downpayment', '54646464646', 'uploads/payment_proofs/payment_67e68df0bc65f_20250328_125424.png', 'pending', NULL, '2025-03-28 11:54:24', '2025-03-28 11:54:24', 'Regular Booking'),
('TB20250329071549350', 5, 'Package A', 79500.00, 63500.00, 8, 16000.00, 0, 0.00, 79500.00, 79500.00, 0.00, '2025-03-30', 'Gender Reveal ', '07:14:00', '19:15:00', 33, 'gcash', 'full', '111111111111111111', 'uploads/payment_proofs/payment_67e79015ecc4a_20250329_071549.png', 'pending', NULL, '2025-03-29 06:15:50', '2025-03-29 06:15:50', 'Regular Booking'),
('TB20250403040019264', 3, 'Package A', 51500.00, 49500.00, 1, 2000.00, 0, 0.00, 51500.00, 25750.00, 25750.00, '2025-04-03', 'Wedding', '14:59:00', '20:59:00', 34, 'gcash', 'downpayment', '54646464646', 'uploads/payment_proofs/payment_67edebb315313_20250403_040019.png', 'pending', NULL, '2025-04-03 02:00:19', '2025-04-03 02:00:19', 'Regular Booking'),
('TB20250403133947974', 3, 'Package A', 55500.00, 47500.00, 4, 8000.00, 0, 0.00, 55500.00, 55500.00, 0.00, '2025-04-04', 'Wedding', '12:39:00', '21:39:00', 34, 'gcash', 'full', '45645645645', 'uploads/payment_proofs/payment_67ee73831bc47_20250403_133947.png', 'pending', NULL, '2025-04-03 11:39:47', '2025-04-03 11:39:47', 'Regular Booking'),
('TB20250403134048948', 3, 'Venue Rental Only', 26000.00, 20000.00, 3, 6000.00, 0, 0.00, 26000.00, 26000.00, 0.00, '2025-04-05', 'Corporate', '12:40:00', '20:40:00', 22, 'gcash', 'full', '45645645645', 'uploads/payment_proofs/payment_67ee73c009fd6_20250403_134048.png', 'pending', NULL, '2025-04-03 11:40:48', '2025-04-03 11:40:48', 'Regular Booking'),
('TB20250408054641864', 3, 'Package A', 49500.00, 47500.00, 1, 2000.00, 0, 0.00, 49500.00, 24750.00, 24750.00, '2025-04-08', 'Corporate', '16:46:00', '22:46:00', 50, 'gcash', 'downpayment', '111111111111111111', 'uploads/payment_proofs/payment_67f49c21c410e_20250408_054641.jpg', 'pending', NULL, '2025-04-08 03:46:41', '2025-04-08 03:46:41', 'Regular Booking'),
('TB20250408054754717', 3, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-04-10', 'Wedding', '16:47:00', '22:47:00', 44, 'gcash', 'full', '111111111111111111', 'uploads/payment_proofs/payment_67f49c6acc435_20250408_054754.jpg', 'pending', NULL, '2025-04-08 03:47:54', '2025-04-08 03:47:54', 'Regular Booking'),
('TB20250408054934849', 3, 'Venue Rental Only', 22000.00, 20000.00, 1, 2000.00, 0, 0.00, 22000.00, 22000.00, 0.00, '2025-04-09', 'Wedding', '16:49:00', '22:49:00', 44, 'gcash', 'full', '45645645645', 'uploads/payment_proofs/payment_67f49cce94414_20250408_054934.jpg', 'pending', NULL, '2025-04-08 03:49:34', '2025-04-08 03:49:34', 'Regular Booking');

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
  `image_path` varchar(255) DEFAULT NULL,
  `image_path2` varchar(255) DEFAULT NULL,
  `image_path3` varchar(255) DEFAULT NULL,
  `max_guests` int(11) NOT NULL DEFAULT 30,
  `duration` int(11) NOT NULL DEFAULT 5 COMMENT 'Duration in hours',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 1,
  `menu_items` text DEFAULT NULL,
  `max_pax` int(11) DEFAULT 50,
  `time_limit` varchar(50) DEFAULT '5 hours',
  `notes` text DEFAULT NULL,
  `status` enum('Available','Occupied') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_packages`
--

INSERT INTO `event_packages` (`id`, `name`, `price`, `description`, `image_path`, `image_path2`, `image_path3`, `max_guests`, `duration`, `created_at`, `is_available`, `menu_items`, `max_pax`, `time_limit`, `notes`, `status`) VALUES
(1, 'Venue Rental Only', 20000.00, '5-hour venue rental\nTables and Tiffany chairs', 'images/hall.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, NULL, 50, '5 hours', NULL, 'Available'),
(2, 'Package A', 47500.00, '5-hour venue rental      Tables     and Tiffany chairs', 'images/hall.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 0, '1 Appetizers, 2 Pasta, 2 Mains, Salad Bar, Rice , Drinks', 50, '5 hours', NULL, 'Available'),
(3, 'Package B', 55000.00, '5-hour venue rental\nTables and  Tiffany chairs', 'images/hall2.jpg', 'images/hall.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, ' 2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert,  Drinks ', 50, '5 hours', '**Assumes 5,000g (100g per person) of Wagyu steak will be served.', 'Available'),
(4, 'Package C', 76800.00, '5-hour venue rental\nTables and Tiffany chairs', 'images/hall3.jpg', 'images/hall2.jpg', 'images/hall.jpg', 30, 5, '2025-02-12 02:48:46', 1, '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2desserts, Drinks ', 50, '5 hours', NULL, 'Available');

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
  `guest_type` enum('regular','pwd','senior') NOT NULL DEFAULT 'regular',
  `id_number` varchar(50) DEFAULT NULL,
  `id_image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `first_name`, `last_name`, `guest_type`, `id_number`, `id_image_path`, `created_at`) VALUES
(1, 126, 'Christian', 'Realisan', 'senior', 'SCDFI-22233323', 'id_67c84f97d2ec8_Screenshot (2).png', '2025-03-05 13:20:23'),
(2, 127, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-05 13:22:36'),
(3, 127, 'Christian', 'Realisan', 'pwd', '1222-2222-2222', 'id_67c8501c5f772_Screenshot (2).png', '2025-03-05 13:22:36'),
(4, 127, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-05 13:22:36'),
(5, 128, 'Christian', 'Realisan', 'pwd', '2224-4444-3333', 'id_67c85318a5814_Screenshot (1).png', '2025-03-05 13:35:20'),
(6, 129, 'Christian', 'Realisan', 'senior', 'SCDFI-22222222', 'id_67c854e34d743_Screenshot (2).png', '2025-03-05 13:42:59'),
(7, 130, 'Christian', 'Realisan', 'senior', 'SCDFI-22222222', 'id_67c856db36331_Screenshot (2).png', '2025-03-05 13:51:23'),
(8, 131, 'Christian', 'Realisan', 'pwd', '2222-2222-2222', 'id_67c858cfe1e51_Screenshot (2).png', '2025-03-05 13:59:43'),
(9, 132, 'Christian', 'Realisan', 'pwd', '3333-3333-3333', 'id_67c85a94a4f65_Screenshot (6).png', '2025-03-05 14:07:16'),
(10, 133, 'Christian', 'Realisan', 'senior', 'SCDFI-11111111', 'id_67c8604bda6fd_Screenshot (6).png', '2025-03-05 14:31:39'),
(14, 138, 'Christian', 'Realisan', 'pwd', '11-1111-111-1111111', NULL, '2025-03-24 07:55:33'),
(15, 139, 'Christian', 'Realisan', 'pwd', '11-1111-111-1111111', NULL, '2025-03-24 08:16:50'),
(16, 140, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 09:01:25'),
(17, 142, 'Christian', 'Realisan', 'pwd', '11-1111-111-1111111', NULL, '2025-03-24 10:11:55'),
(18, 143, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:29:53'),
(19, 143, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:29:53'),
(20, 144, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:35:37'),
(21, 144, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:35:37'),
(22, 145, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:37:43'),
(23, 145, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:37:43'),
(24, 146, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:39:05'),
(25, 146, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:39:05'),
(26, 147, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:43:22'),
(27, 147, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:43:22'),
(28, 148, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:44:04'),
(29, 148, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:44:04'),
(30, 149, 'Christian', 'Realisan', 'pwd', '11-1111-111-1122333', NULL, '2025-03-24 10:48:16'),
(31, 149, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 10:48:16'),
(32, 151, 'Christian', 'Realisan', 'senior', 'SCDPO-22222222', NULL, '2025-03-24 11:37:08'),
(33, 151, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 11:37:08'),
(34, 152, 'Christian', 'Realisan', 'senior', 'SCDPO-22222222', NULL, '2025-03-24 11:41:00'),
(35, 152, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 11:41:00'),
(36, 153, 'Christian', 'Realisan', 'senior', 'SCDPO-22222222', NULL, '2025-03-24 11:43:06'),
(37, 154, 'Christian', 'Realisan', 'senior', 'SCDPO-22222222', NULL, '2025-03-24 11:57:09'),
(38, 155, 'Christian', 'Realisan', 'regular', '', NULL, '2025-03-24 21:53:12'),
(39, 156, 'Christian', 'Realisan', 'pwd', '11-1111-111-1122333', NULL, '2025-03-24 21:54:39'),
(40, 157, 'Christian', 'Realisan', 'pwd', '11-1111-111-1122333', NULL, '2025-03-24 21:58:47'),
(41, 158, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 22:00:36'),
(42, 159, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 22:26:42'),
(43, 160, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 23:09:31'),
(44, 161, 'Christian', 'Realisan', 'regular', 'SCDPO-22222222', NULL, '2025-03-24 23:14:57'),
(45, 162, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-24 23:18:00'),
(46, 163, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 11:50:58'),
(47, 164, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 12:28:17'),
(48, 165, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 12:31:43'),
(50, 167, 'Christian', 'Realisan', 'pwd', '11-1111-111-1122333', NULL, '2025-03-26 22:39:16'),
(51, 168, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 22:42:49'),
(52, 169, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 22:47:08'),
(53, 170, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 22:49:59'),
(54, 171, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 22:56:00'),
(55, 172, 'Christian', 'Realisan', 'regular', NULL, NULL, '2025-03-26 23:04:50'),
(56, 173, 'Bella', 'Aw aw', 'pwd', '11-1111-111-1122333', NULL, '2025-03-27 07:14:27'),
(57, 174, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-27 07:25:13'),
(58, 174, 'Kenjo', 'Marimon', 'regular', NULL, NULL, '2025-03-27 07:25:13'),
(59, 174, 'Alfred ', 'Aceveda', 'regular', NULL, NULL, '2025-03-27 07:25:13'),
(60, 174, 'Myra ', 'Luceno', 'regular', NULL, NULL, '2025-03-27 07:25:13'),
(61, 175, 'Aiza ', 'Villanueva', 'regular', '1111111111111111', NULL, '2025-03-28 03:42:39'),
(62, 176, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-28 03:48:35'),
(63, 177, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-28 07:03:03'),
(64, 178, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-28 07:06:57'),
(65, 179, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-29 06:36:43'),
(66, 180, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-29 06:42:21'),
(67, 181, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-29 06:44:40'),
(68, 182, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-29 06:48:41'),
(69, 183, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 05:20:19'),
(70, 184, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 05:22:50'),
(71, 185, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 05:23:46'),
(72, 186, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 05:24:25'),
(73, 187, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 05:31:49'),
(74, 188, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:02:37'),
(75, 189, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:07:17'),
(76, 190, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:10:02'),
(77, 191, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:32:44'),
(78, 192, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:38:17'),
(79, 193, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 06:51:09'),
(80, 194, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 07:17:31'),
(81, 195, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 07:20:37'),
(82, 196, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 07:26:56'),
(83, 197, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 09:04:05'),
(88, 202, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 09:56:50'),
(89, 203, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 09:59:53'),
(90, 204, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:01:24'),
(91, 205, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:02:41'),
(92, 206, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:05:32'),
(94, 208, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:13:49'),
(95, 209, 'Aiza ', 'Villanueva', 'pwd', '11-1111-111-1122333', NULL, '2025-03-31 10:16:47'),
(96, 210, 'Aiza ', 'Villanueva', 'pwd', '11-1111-111-1122333', NULL, '2025-03-31 10:19:26'),
(97, 211, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:23:22'),
(98, 212, 'Aiza ', 'Villanueva', 'pwd', '11-1111-111-1122333', NULL, '2025-03-31 10:28:23'),
(99, 213, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:29:09'),
(100, 214, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:32:33'),
(101, 215, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:36:23'),
(102, 216, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:42:15'),
(103, 217, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:46:14'),
(104, 218, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:52:47'),
(105, 219, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 10:54:49'),
(106, 220, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 11:00:21'),
(107, 221, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:15:20'),
(108, 222, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:23:38'),
(109, 223, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:27:19'),
(110, 224, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:32:34'),
(111, 225, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:40:00'),
(112, 226, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 20:44:49'),
(113, 227, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:03:04'),
(114, 228, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:06:50'),
(115, 229, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:09:23'),
(116, 230, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:14:23'),
(117, 231, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:18:24'),
(119, 233, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 21:27:19'),
(122, 236, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 22:35:21'),
(123, 237, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-03-31 22:52:54'),
(124, 238, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:09:08'),
(125, 239, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:13:59'),
(126, 240, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:24:03'),
(127, 241, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:28:05'),
(128, 242, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:32:20'),
(129, 243, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:41:38'),
(130, 244, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:47:10'),
(131, 245, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:50:58'),
(132, 246, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 08:54:39'),
(133, 247, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:06:33'),
(134, 248, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:07:10'),
(135, 249, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:10:41'),
(136, 250, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:12:05'),
(137, 251, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:15:25'),
(138, 252, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:17:20'),
(139, 256, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:28:26'),
(140, 257, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:48:46'),
(141, 258, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 09:52:26'),
(142, 259, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 10:02:51'),
(143, 260, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 10:06:27'),
(144, 261, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 15:45:57'),
(145, 262, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 15:50:54'),
(146, 263, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 15:51:39'),
(147, 264, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 15:56:51'),
(148, 265, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 16:02:26'),
(149, 266, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 16:12:28'),
(150, 267, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 16:16:28'),
(151, 268, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 16:54:44'),
(152, 269, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-01 17:02:37'),
(153, 270, 'Aiza ', 'Villanueva', 'regular', NULL, NULL, '2025-04-02 04:48:21'),
(172, 280, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:01:47'),
(173, 280, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:01:47'),
(174, 281, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:01:49'),
(175, 281, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:01:49'),
(178, 283, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:26:19'),
(179, 283, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:26:19'),
(180, 284, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:26:20'),
(181, 284, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:26:20'),
(182, 285, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:27:42'),
(183, 285, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:27:42'),
(184, 286, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:30:34'),
(185, 286, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:30:34'),
(186, 287, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:30:36'),
(187, 287, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:30:36'),
(188, 288, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:33:34'),
(189, 288, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:33:34'),
(190, 289, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:33:36'),
(191, 289, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:33:36'),
(192, 290, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:35:55'),
(193, 290, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:35:55'),
(194, 291, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:36:31'),
(195, 291, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:36:31'),
(196, 292, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-03 17:37:07'),
(197, 292, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-03 17:37:07'),
(198, 293, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 03:52:00'),
(199, 294, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 03:52:28'),
(200, 295, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 03:52:32'),
(201, 296, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 03:53:00'),
(202, 297, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 03:53:02'),
(214, 309, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:33:03'),
(215, 310, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:33:10'),
(216, 311, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:33:18'),
(217, 312, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:34:09'),
(218, 313, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:34:15'),
(219, 314, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 04:43:25'),
(220, 315, 'Kenjo ', 'Marimon', 'pwd', '1111111111111111111111111111111', NULL, '2025-04-04 04:54:49'),
(221, 316, 'Kenjo ', 'Marimon', 'pwd', '1111111111111111111111111111111', NULL, '2025-04-04 04:59:22'),
(222, 317, 'Kenjo ', 'Marimon', 'pwd', '1111111111111111111111111111111', NULL, '2025-04-04 04:59:25'),
(223, 318, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:15:09'),
(224, 319, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:15:12'),
(225, 320, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:20:25'),
(226, 321, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:20:28'),
(227, 322, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:20:34'),
(228, 323, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:29:12'),
(229, 324, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:29:53'),
(230, 325, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:33:30'),
(231, 326, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:39:39'),
(232, 327, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:39:42'),
(233, 328, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:40:45'),
(234, 329, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:25'),
(235, 330, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:27'),
(236, 331, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:30'),
(237, 332, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:32'),
(238, 333, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:35'),
(239, 334, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:37'),
(240, 335, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:41'),
(241, 336, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:41:43'),
(242, 337, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:46:11'),
(243, 338, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:46:15'),
(244, 339, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:46:44'),
(245, 340, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:46:47'),
(246, 341, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:49:20'),
(247, 342, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:49:24'),
(248, 343, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:49:26'),
(249, 344, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:49:28'),
(250, 345, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:49:31'),
(251, 346, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:50:50'),
(252, 347, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:50:53'),
(253, 348, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:50:56'),
(254, 349, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:56:24'),
(255, 350, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 05:56:28'),
(256, 352, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:03:43'),
(257, 353, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:09:03'),
(258, 354, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:09:06'),
(259, 355, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:12:21'),
(260, 356, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:12:26'),
(261, 357, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:12:30'),
(262, 358, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:12:32'),
(263, 359, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:12:36'),
(265, 361, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:22:19'),
(266, 362, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:23:58'),
(267, 363, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:24:34'),
(268, 364, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:24:45'),
(269, 365, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-04 06:39:28'),
(270, 366, 'Kenjo', 'Marimon', 'pwd', '444444444444', NULL, '2025-04-05 11:14:24'),
(271, 367, 'Kenjo', 'Marimon', 'pwd', '444444444444', NULL, '2025-04-05 11:14:34'),
(272, 368, 'Kenjo', 'Marimon', 'pwd', '444444444444', NULL, '2025-04-05 11:14:38'),
(273, 369, 'Kenjo', 'Marimon', 'pwd', '444444444444', NULL, '2025-04-05 11:14:41'),
(274, 370, 'Kenjo', 'Marimon', 'pwd', '444444444444', NULL, '2025-04-05 11:19:43'),
(275, 371, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 11:21:40'),
(276, 372, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 11:28:42'),
(277, 373, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 11:30:51'),
(278, 374, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 11:38:47'),
(281, 377, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 12:50:56'),
(282, 378, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 12:51:23'),
(283, 379, 'Kenjo', 'Marimon', '', '7788888888888', NULL, '2025-04-05 14:04:40'),
(284, 380, 'Kenjo', 'Marimon', '', '7788888888888', NULL, '2025-04-05 14:04:45'),
(285, 381, 'Kenjo', 'Marimon', '', '7788888888888', NULL, '2025-04-05 14:04:48'),
(286, 382, 'Kenjo', 'Marimon', '', '7788888888888', NULL, '2025-04-05 14:07:15'),
(287, 383, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:07:51'),
(288, 384, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:07:57'),
(289, 385, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:09:11'),
(290, 386, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:11:25'),
(291, 387, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:11:27'),
(292, 388, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:27:04'),
(293, 389, 'Kenjo', 'Marimon', 'regular', '', NULL, '2025-04-05 14:30:11'),
(294, 390, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-05 21:48:32'),
(295, 391, 'Kenjo ', 'Marimon', 'regular', '', NULL, '2025-04-06 08:18:08'),
(296, 391, 'aa', 'aa', 'regular', '', NULL, '2025-04-06 08:18:08'),
(297, 392, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 08:43:22'),
(298, 393, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 08:43:26'),
(299, 394, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 08:43:29'),
(300, 395, 'Christian', 'Realisan', 'pwd', '555555555555555', NULL, '2025-04-06 08:47:53'),
(301, 396, 'Christian', 'Realisan', 'pwd', '44444444444444', NULL, '2025-04-06 08:58:42'),
(302, 397, 'Christian', 'Realisan', 'pwd', '44444444444444', NULL, '2025-04-06 08:58:45'),
(303, 398, 'Christian', 'Realisan', 'pwd', '44444444444444', NULL, '2025-04-06 09:04:08'),
(304, 399, 'Christian', 'Realisan', 'pwd', '44444444444444', NULL, '2025-04-06 09:05:25'),
(305, 400, 'Christian', 'Realisan', '', '44444444444444', NULL, '2025-04-06 09:06:24'),
(306, 401, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 09:20:27'),
(307, 402, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 09:20:31'),
(308, 403, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-06 09:20:34'),
(309, 404, 'aa', 'aa', 'regular', '', NULL, '2025-04-06 10:17:36'),
(310, 405, 'aa', 'aa', 'regular', '', NULL, '2025-04-06 10:17:38'),
(311, 406, 'christiandfdfgg', 'realisan', 'pwd', '555555555555555', NULL, '2025-04-08 03:40:25'),
(312, 407, 'christiandfdfgg', 'realisan', 'pwd', '555555555555555', NULL, '2025-04-08 03:40:29'),
(313, 408, 'christiandfdfgg', 'realisan', 'regular', '', NULL, '2025-04-08 03:59:06'),
(314, 409, 'christiandfdfgg', 'realisan', 'regular', '', NULL, '2025-04-08 04:08:46'),
(315, 409, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-08 04:08:46'),
(316, 409, 'Myra ', 'Luceno', 'regular', '', NULL, '2025-04-08 04:08:46'),
(317, 409, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:08:46'),
(318, 409, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:08:46'),
(319, 409, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:08:46'),
(320, 410, 'christiandfdfgg', 'realisan', 'regular', '', NULL, '2025-04-08 04:08:51'),
(321, 410, 'Alfred ', 'Aceveda', 'regular', '', NULL, '2025-04-08 04:08:51'),
(322, 410, 'Myra ', 'Luceno', 'regular', '', NULL, '2025-04-08 04:08:51'),
(323, 410, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:08:51'),
(324, 410, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:08:51'),
(325, 410, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:08:51'),
(326, 411, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:22:36'),
(327, 411, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:22:36'),
(328, 411, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:22:36'),
(329, 411, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:22:36'),
(330, 411, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:22:36'),
(331, 411, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:22:36'),
(332, 412, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:22:42'),
(333, 412, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:22:42'),
(334, 412, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:22:42'),
(335, 412, 'piiii', 'tryuti', 'regular', '', NULL, '2025-04-08 04:22:42'),
(336, 412, 'k;opp', 'hgjj', 'regular', '', NULL, '2025-04-08 04:22:42'),
(337, 412, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:22:42');

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
(8, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b4289215e15.jpg');

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
(6, 3, 'Extra Ranch', 30.00),
(7, 1, 'Extra Sauce', 20.00),
(8, 1, 'Extra Cheese', 30.00),
(9, 2, 'Extra Spicy', 15.00),
(10, 2, 'Extra Rice', 25.00);

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
(33, 3, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 103, 1, '2025-02-17 22:37:21'),
(34, 3, 'New Booking Confirmation', 'Your booking #114 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 114, 1, '2025-02-17 23:33:19'),
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
(45, 8, 'New Booking Confirmation', 'Your booking #126 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 126, 1, '2025-03-05 13:20:23'),
(46, 8, 'New Booking Confirmation', 'Your booking #127 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 127, 1, '2025-03-05 13:22:36'),
(47, 8, 'New Booking Confirmation', 'Your booking #128 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 128, 1, '2025-03-05 13:35:20'),
(48, 8, 'New Booking Confirmation', 'Your booking #129 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 129, 1, '2025-03-05 13:42:59'),
(49, 8, 'New Booking Confirmation', 'Your booking #130 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 130, 1, '2025-03-05 13:51:23'),
(50, 8, 'New Booking Confirmation', 'Your booking #131 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 131, 1, '2025-03-05 13:59:43'),
(51, 8, 'New Booking Confirmation', 'Your booking #132 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 132, 1, '2025-03-05 14:07:16'),
(52, 8, 'New Booking Confirmation', 'Your booking #133 has been confirmed. Check-in date: Mar 05, 2025', 'booking', 'fas fa-calendar-check', 133, 1, '2025-03-05 14:31:39'),
(53, 8, '', 'Your order has been placed successfully. Please pick up at 20:48', 'order', '', NULL, 1, '2025-03-18 01:48:33'),
(54, 8, '', 'Your order has been placed successfully. Please pick up at 08:05 PM', 'order', '', NULL, 1, '2025-03-18 02:05:36'),
(55, 8, '', 'Your order has been placed successfully. Please pick up at 08:29 PM', 'order', '', NULL, 1, '2025-03-18 02:29:45'),
(56, 8, '', 'Your order has been placed successfully. Please pick up at 10:11 PM', 'order', '', NULL, 1, '2025-03-18 04:11:39'),
(57, 8, '', 'Your order has been placed successfully. Please pick up at 10:22 PM', 'order', '', NULL, 1, '2025-03-18 04:22:54'),
(58, 8, '', 'Your order has been placed successfully. Please pick up at 02:20 PM', 'order', '', NULL, 1, '2025-03-18 00:20:17'),
(59, 8, '', 'Your order has been placed successfully. Please pick up at 02:20 PM', 'order', '', NULL, 1, '2025-03-18 00:20:46'),
(60, 8, '', 'Your order has been placed successfully. Please pick up at 02:37 PM', 'order', '', NULL, 1, '2025-03-18 00:37:37');

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
  `table_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `change_amount` int(11) NOT NULL,
  `extra_fee` int(11) NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_reference` varchar(100) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  `payment_proof` varchar(255) NOT NULL,
  `pickup_notes` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `final_total` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_type` enum('none','senior_citizen','pwd') DEFAULT 'none',
  `discount_amount` int(11) NOT NULL,
  `id_number` varchar(55) NOT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `table_id`, `customer_name`, `contact_number`, `total_amount`, `amount_paid`, `change_amount`, `extra_fee`, `order_type`, `payment_method`, `payment_reference`, `payment_status`, `remaining_balance`, `payment_proof`, `pickup_notes`, `status`, `final_total`, `order_date`, `discount_type`, `discount_amount`, `id_number`, `completed_at`) VALUES
(70, 3, 0, '', 0, 170.00, 0, 0, 0, 'regular', 'gcash', '5434535', 'Partially Paid', 0.00, 'payment_1744112543_67f50b9f020f3.jpg', '', 'completed', 170, '2025-04-08 11:42:23', 'none', 0, '0', '2025-04-09 00:43:19'),
(71, 3, 0, '', 0, 170.00, 0, 0, 0, 'regular', 'gcash', '5434535', 'Partially Paid', 85.00, 'payment_1744114720_67f51420832ea.jpg', '', 'finished', 170, '2025-04-08 12:18:40', 'none', 0, '0', '2025-04-09 01:20:54'),
(72, 0, 0, '', 2147483647, 150.00, 888, 738, 0, 'walk-in', 'maya', '', '', 0.00, '', '', 'finished', 0, '2025-04-08 12:25:17', 'none', 0, '0', '2025-04-09 01:25:39'),
(73, 3, 0, '', 2147483647, 600.00, 0, 0, 0, 'walk-in', 'maya', '', 'Pending', 0.00, '', '', 'finished', 0, '2025-04-08 12:43:41', '', 90, '0', '2025-04-09 01:44:24'),
(74, 3, 0, '', 2147483647, 692.00, 0, 0, 0, 'walk-in', 'gcash', '', 'processing', 0.00, '', '', 'finished', 0, '2025-04-08 12:46:38', '', 48, '0', '2025-04-09 02:17:38'),
(75, 3, 0, '', 2147483647, 120.00, 0, 0, 0, 'walk-in', 'bank', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:01:01', '', 30, '0', NULL),
(76, 3, 0, '', 2147483647, 240.00, 0, 0, 0, 'walk-in', 'gcash', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:06:16', '', 60, '0', NULL),
(77, 0, 0, '', 2147483647, 576.00, 0, 0, 0, 'walk-in', 'bank', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:07:13', '', 144, '0', NULL),
(78, 0, 0, '', 2147483647, 1200.00, 0, 0, 0, 'walk-in', 'gcash', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:11:39', '', 300, '0', NULL),
(79, 0, 0, '', 2147483647, 240.00, 0, 0, 0, 'walk-in', 'maya', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:11:55', '', 60, '0', NULL),
(80, 0, 0, '', 2147483647, 112.00, 0, 0, 0, 'walk-in', 'bank', '', 'processing', 0.00, '', '', 'rejected', 0, '2025-04-08 13:14:16', '', 28, '0', NULL),
(81, 0, 0, '', 2147483647, 96.00, 0, 0, 0, 'walk-in', 'maya', '', 'processing', 0.00, '', '', 'finished', 0, '2025-04-08 13:15:25', '', 24, '0', '2025-04-09 02:26:46'),
(82, 0, 0, '', 2147483647, 192.00, 0, 0, 0, 'walk-in', 'maya', '', 'pending', 0.00, '', '', 'rejected', 0, '2025-04-08 13:19:30', '', 48, 'SCDFI-33333333', NULL),
(83, 0, 0, '', 2147483647, 240.00, 0, 0, 0, 'walk-in', 'gcash', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:23:58', '', 60, 'SCDFI-33333333', '2025-04-09 02:26:50'),
(84, 0, 0, '', 2147483647, 240.00, 0, 0, 0, 'walk-in', 'bank', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:27:22', '', 60, 'SCDFI-33333333', '2025-04-09 02:37:39'),
(85, 0, 0, '', 2147483647, 120.00, 0, 0, 0, 'walk-in', 'gcash', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:40:36', '', 24, 'SCDFI-33333333', '2025-04-09 02:41:29'),
(86, 0, 0, '', 2147483647, 108.00, 0, 0, 0, 'walk-in', 'maya', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:45:39', '', 27, 'SCDFI-33333333', '2025-04-09 03:06:14'),
(87, 0, 0, '', 2147483647, 150.00, 0, 0, 0, 'walk-in', 'maya', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:52:00', 'none', 0, 'SCDFI-33333333', '2025-04-09 03:06:21'),
(88, 0, 0, '', 2147483647, 240.00, 300, 60, 0, 'walk-in', 'bank', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 13:56:29', '', 60, 'SCDFI-33333333', '2025-04-09 03:06:31'),
(89, 0, 0, '', 2147483647, 150.00, 200, 50, 0, 'walk-in', 'gcash', '', 'pending', 0.00, '', '', 'finished', 0, '2025-04-08 14:05:20', 'none', 0, '', '2025-04-09 03:06:18'),
(90, 0, 0, '', 2147483647, 240.00, 500, 260, 0, 'walk-in', 'maya', '', 'pending', 0.00, '', '', 'processing', 0, '2025-04-08 14:11:57', '', 60, 'SCDFI-33333333', NULL),
(91, 3, 0, '', 0, 500.00, 0, 0, 0, 'regular', 'gcash', '5434535', 'Partially Paid', 250.00, 'payment_1744121561_67f52ed9967a3.jpg', '', 'Pending', 500, '2025-04-08 14:12:41', 'none', 0, '', NULL),
(92, 3, 44, '', 0, 150.00, 0, 0, 0, 'advance', 'gcash', '5434535', 'Partially Paid', 0.00, 'payment_1744123908_67f538045f82b.png', '', 'Pending', 150, '2025-04-08 14:51:48', 'none', 0, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `quantity`, `unit_price`) VALUES
(74, 70, 'Hand-cut Potato Fries', 1, 120.00),
(75, 71, 'Hand-cut Potato Fries', 1, 120.00),
(76, 72, 'Mozzarella Stick', 1, 150.00),
(77, 73, 'Mozzarella Stick', 4, 150.00),
(78, 74, 'Hand-cut Potato Fries', 2, 120.00),
(79, 75, 'Mozzarella Stick', 1, 150.00),
(80, 76, 'Mozzarella Stick', 2, 150.00),
(81, 77, 'Hand-cut Potato Fries', 1, 120.00),
(82, 77, 'Mozzarella Stick', 1, 150.00),
(83, 77, 'Chicken Wings', 1, 180.00),
(84, 77, 'Spaghetti maccaroni', 1, 270.00),
(85, 78, 'Mozzarella Stick', 10, 150.00),
(86, 79, 'Mozzarella Stick', 2, 150.00),
(87, 80, 'Hand-cut Potato Fries', 1, 120.00),
(88, 81, 'Hand-cut Potato Fries', 1, 120.00),
(89, 74, 'Egg Sandwich - ₱500.00', 1, 500.00),
(90, 82, 'Hand-cut Potato Fries', 2, 120.00),
(91, 83, 'Mozzarella Stick', 2, 150.00),
(92, 84, 'Spaghetti', 1, 300.00),
(93, 85, 'Hand-cut Potato Fries', 1, 120.00),
(95, 86, 'Hand-cut Potato Fries', 1, 120.00),
(96, 87, 'Mozzarella Stick', 1, 150.00),
(97, 88, 'Mozzarella Stick', 2, 150.00),
(98, 89, 'Mozzarella Stick', 1, 150.00),
(99, 90, 'Spaghetti', 1, 300.00),
(100, 91, 'Egg Sandwich', 1, 500.00),
(101, 92, 'Mozzarella Stick', 1, 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `addon_name` varchar(100) NOT NULL,
  `addon_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_addons`
--

INSERT INTO `order_item_addons` (`id`, `order_item_id`, `addon_name`, `addon_price`) VALUES
(20, 74, 'Extra Sauce', 20.00),
(21, 74, 'Extra Cheese', 30.00),
(22, 75, 'Extra Sauce', 20.00),
(23, 75, 'Extra Cheese', 30.00),
(24, 87, 'cheese', 20.00),
(25, 95, 'HAtdog', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `hero_title` varchar(255) NOT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `section_title` varchar(255) DEFAULT NULL,
  `section_intro` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_content`
--

INSERT INTO `page_content` (`id`, `page_name`, `hero_title`, `hero_subtitle`, `section_title`, `section_intro`, `last_updated`) VALUES
(1, 'contact', 'Get in Touch', 'We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.', 'Contact Us', 'Whether you have questions about our accommodations, want to make a special request, or need any assistance, our team is here to help. Reach out through any of the following channels.', '2025-03-18 05:25:25');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `qr_code_image` varchar(255) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `display_name`, `qr_code_image`, `account_name`, `account_number`, `is_active`) VALUES
(1, 'gcash', 'GCash', 'uploads/payment_qr_codes/gcashpay.jpg', 'JOEY ANNE M.', '09391837722', 1),
(2, 'maya', 'Maya', 'uploads/payment_qr_codes/mayapay.jpg', 'RAQUEL MACAPAGAL', '09606924460', 1);

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
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `total_rooms` int(11) NOT NULL DEFAULT 0,
  `available_rooms` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Available'
) ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `total_rooms`, `available_rooms`, `status`) VALUES
(1, 1, 10, 2, 'Available'),
(2, 2, 5, 0, 'Available'),
(3, 3, 3, 0, 'Available');

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `before_update_rooms` BEFORE UPDATE ON `rooms` FOR EACH ROW BEGIN
    IF NEW.available_rooms > NEW.total_rooms THEN
        SET NEW.available_rooms = NEW.total_rooms;
    END IF;
    IF NEW.available_rooms < 0 THEN
        SET NEW.available_rooms = 0;
    END IF;
END
$$
DELIMITER ;

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
  `number_of_nights` int(11) DEFAULT 1,
  `is_processed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `booking_id`, `room_type_id`, `room_name`, `room_price`, `room_quantity`, `number_of_days`, `subtotal`, `created_at`, `guest_count`, `extra_guest_fee`, `number_of_nights`, `is_processed`) VALUES
(1, 7, 1, 'Standard Double Room', 1500.00, 1, 9, 13500.00, '2025-02-09 22:44:23', 0, 0.00, 1, 0),
(2, 8, 1, 'Standard Double Room', 1500.00, 1, 9, 13500.00, '2025-02-09 22:44:23', 0, 0.00, 1, 0),
(3, 9, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-09 22:45:43', 0, 0.00, 1, 0),
(4, 9, 1, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-09 22:45:43', 0, 0.00, 1, 0),
(5, 10, 2, 'Deluxe Family Room', 2000.00, 1, 8, 16000.00, '2025-02-09 23:19:02', 0, 0.00, 1, 0),
(6, 11, 2, 'Deluxe Family Room', 2000.00, 1, 8, 16000.00, '2025-02-09 23:19:02', 0, 0.00, 1, 0),
(7, 12, 2, 'Deluxe Family Room', 2000.00, 1, 10, 20000.00, '2025-02-09 23:38:38', 0, 0.00, 1, 0),
(8, 12, 3, 'Family Room', 2500.00, 1, 10, 25000.00, '2025-02-09 23:38:38', 0, 0.00, 1, 0),
(9, 13, 2, 'Deluxe Family Room', 2000.00, 1, 0, 0.00, '2025-02-09 23:48:14', 0, 0.00, 1, 0),
(10, 14, 3, 'Family Room', 2500.00, 1, 8, 20000.00, '2025-02-09 23:50:43', 0, 0.00, 1, 0),
(11, 15, NULL, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-10 00:41:19', 0, 0.00, 1, 0),
(12, 16, NULL, 'Family Room', 2500.00, 1, 9, 22500.00, '2025-02-10 01:08:27', 0, 0.00, 1, 0),
(13, 17, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 01:12:16', 0, 0.00, 1, 0),
(14, 18, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 01:12:16', 0, 0.00, 1, 0),
(15, 19, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 01:16:08', 0, 0.00, 1, 0),
(16, 20, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 01:20:12', 0, 0.00, 1, 0),
(17, 20, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 01:20:12', 0, 0.00, 1, 0),
(18, 21, NULL, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-10 03:05:36', 0, 0.00, 1, 0),
(19, 22, NULL, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-10 03:08:55', 0, 0.00, 1, 0),
(20, 23, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:09:50', 0, 0.00, 1, 0),
(21, 24, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:09:50', 0, 0.00, 1, 0),
(22, 25, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:12:32', 0, 0.00, 1, 0),
(23, 26, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:12:33', 0, 0.00, 1, 0),
(24, 27, NULL, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-10 03:17:53', 0, 0.00, 1, 0),
(25, 28, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:23:15', 0, 0.00, 1, 0),
(26, 29, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 03:23:16', 0, 0.00, 1, 0),
(27, 30, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 03:56:24', 0, 0.00, 1, 0),
(28, 31, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 03:58:07', 0, 0.00, 1, 0),
(29, 32, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 04:01:43', 0, 0.00, 1, 0),
(30, 33, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 04:03:31', 0, 0.00, 1, 0),
(31, 34, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:12:29', 0, 0.00, 1, 0),
(32, 35, NULL, 'Standard Double Room', 1500.00, 1, 6, 9000.00, '2025-02-10 04:50:24', 0, 0.00, 1, 0),
(33, 36, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:57:04', 0, 0.00, 1, 0),
(34, 37, NULL, 'Standard Double Room', 1500.00, 1, 1, 1500.00, '2025-02-10 04:57:04', 0, 0.00, 1, 0),
(35, 38, NULL, 'Standard Double Room', 1500.00, 1, 2, 3000.00, '2025-02-10 05:16:41', 0, 0.00, 1, 0),
(36, 39, NULL, 'Standard Double Room', 1500.00, 1, 6, 9000.00, '2025-02-12 02:18:44', 0, 0.00, 1, 0),
(37, 40, 2, 'Deluxe Family Room', 1600.00, 1, 2, 3200.00, '2025-02-12 08:09:12', 0, 0.00, 1, 0),
(38, 30, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-12 18:48:44', 1, 0.00, 2, 0),
(39, 31, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-12 19:01:57', 1, 0.00, 5, 0),
(40, 32, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-12 19:05:04', 1, 0.00, 5, 0),
(41, 33, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-12 19:10:44', 1, 0.00, 2, 0),
(42, 34, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-12 19:14:06', 1, 0.00, 2, 0),
(43, 34, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-12 19:14:06', 1, 0.00, 2, 0),
(44, 35, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-12 20:57:12', 1, 0.00, 2, 0),
(45, 36, 2, 'Deluxe Family Room', 2000.00, 1, 1, 3000.00, '2025-02-13 09:34:00', 2, 1000.00, 1, 0),
(51, 72, 1, 'Standard Double Room', 3700.00, 1, 10, 37000.00, '2025-02-13 16:59:47', 1, 0.00, 10, 0),
(52, 73, 1, 'Standard Double Room', 3700.00, 1, 10, 37000.00, '2025-02-13 16:59:47', 1, 0.00, 10, 0),
(53, 74, 1, 'Standard Double Room', 3700.00, 1, 18, 66600.00, '2025-02-13 17:00:29', 1, 0.00, 18, 0),
(54, 75, 1, 'Standard Double Room', 3700.00, 1, 6, 22200.00, '2025-02-13 17:06:23', 1, 0.00, 6, 0),
(55, 76, 1, 'Standard Double Room', 3700.00, 1, 12, 44400.00, '2025-02-13 17:06:53', 1, 0.00, 12, 0),
(56, 80, 1, 'Standard Double Room', 3700.00, 1, 20, 74000.00, '2025-02-13 17:25:01', 0, 0.00, 20, 0),
(57, 81, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-13 17:32:07', 0, 0.00, 8, 0),
(58, 82, 1, 'Standard Double Room', 3700.00, 1, 12, 44400.00, '2025-02-13 17:37:33', 0, 0.00, 12, 0),
(59, 83, 1, 'Standard Double Room', 3700.00, 1, 7, 25900.00, '2025-02-13 17:39:59', 0, 0.00, 7, 0),
(60, 84, 1, 'Standard Double Room', 3700.00, 1, 19, 70300.00, '2025-02-13 17:41:37', 0, 0.00, 19, 0),
(61, 85, 1, 'Standard Double Room', 3700.00, 1, 13, 48100.00, '2025-02-13 17:41:59', 0, 0.00, 13, 0),
(62, 86, 1, 'Standard Double Room', 3700.00, 1, 13, 48100.00, '2025-02-13 17:44:21', 0, 0.00, 1, 0),
(63, 87, 1, 'Standard Double Room', 3700.00, 1, 22, 81400.00, '2025-02-13 17:50:57', 0, 0.00, 1, 0),
(64, 88, 1, 'Standard Double Room', 3700.00, 1, 5, 18500.00, '2025-02-15 11:40:16', 0, 0.00, 1, 0),
(65, 88, 2, 'Deluxe Family Room', 2000.00, 1, 5, 10000.00, '2025-02-15 11:40:16', 0, 0.00, 1, 0),
(66, 89, 2, 'Deluxe Family Room', 2000.00, 1, 2, 4000.00, '2025-02-15 14:58:51', 0, 0.00, 1, 0),
(67, 90, 2, 'Deluxe Family Room', 2000.00, 1, 9, 18000.00, '2025-02-15 16:08:35', 0, 0.00, 1, 0),
(68, 91, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 14:45:23', 1, 0.00, 8, 0),
(69, 92, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-16 15:30:57', 1, 0.00, 1, 0),
(70, 93, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 15:32:36', 1, 0.00, 8, 0),
(71, 94, 1, 'Standard Double Room', 3700.00, 1, 8, 29600.00, '2025-02-16 15:36:20', 1, 0.00, 8, 0),
(72, 95, 1, 'Standard Double Room', 3700.00, 1, 9, 33300.00, '2025-02-16 15:38:58', 1, 0.00, 9, 0),
(73, 96, 2, 'Deluxe Family Room', 2000.00, 1, 16, 32000.00, '2025-02-16 15:50:40', 1, 0.00, 16, 0),
(74, 97, 1, 'Standard Double Room', 3700.00, 1, 7, 25900.00, '2025-02-16 15:59:02', 1, 0.00, 7, 0),
(75, 98, 1, 'Standard Double Room', 3700.00, 1, 16, 59200.00, '2025-02-16 16:04:15', 1, 0.00, 16, 0),
(76, 99, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-17 11:59:30', 1, 0.00, 1, 0),
(77, 100, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-02-17 12:11:57', 0, 0.00, 1, 0),
(78, 101, 2, 'Deluxe Family Room', 2000.00, 1, 4, 8000.00, '2025-02-17 12:26:01', 0, 0.00, 1, 0),
(79, 102, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-17 20:24:55', 0, 0.00, 1, 0),
(80, 103, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-02-17 20:39:41', 0, 0.00, 1, 0),
(81, 104, 3, 'Family Room', 2500.00, 1, 3, 7500.00, '2025-02-17 20:47:12', 0, 0.00, 1, 0),
(82, 105, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-02-17 20:53:08', 0, 0.00, 1, 0),
(83, 106, 3, 'Family Room', 2500.00, 1, 18, 45000.00, '2025-02-17 21:02:39', 0, 0.00, 1, 0),
(84, 107, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-02-17 21:05:15', 0, 0.00, 1, 0),
(85, 108, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-02-17 21:12:15', 0, 0.00, 1, 0),
(86, 109, 2, 'Deluxe Family Room', 2000.00, 1, 3, 6000.00, '2025-02-17 21:13:01', 0, 0.00, 1, 0),
(87, 110, 1, 'Standard Double Room', 3700.00, 1, 38, 140600.00, '2025-02-17 21:28:09', 0, 0.00, 1, 0),
(88, 111, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-02-17 21:32:43', 0, 0.00, 1, 0),
(89, 112, 3, 'Family Room', 2500.00, 1, 70, 175000.00, '2025-02-17 22:01:11', 0, 0.00, 1, 0),
(90, 113, 3, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-17 22:02:39', 0, 0.00, 1, 0),
(91, 114, 3, 'Family Room', 2500.00, 1, 10, 25000.00, '2025-02-17 23:33:19', 0, 0.00, 1, 0),
(92, 115, 3, 'Family Room', 2500.00, 1, 3, 7500.00, '2025-02-18 06:17:34', 0, 0.00, 1, 0),
(93, 116, 3, 'Family Room', 2500.00, 1, 8, 20000.00, '2025-02-18 06:35:39', 1, 0.00, 8, 0),
(94, 117, 3, 'Family Room', 2500.00, 1, 7, 17500.00, '2025-02-18 07:11:00', 1, 0.00, 7, 0),
(95, 118, 3, 'Family Room', 2500.00, 1, 9, 22500.00, '2025-02-18 11:42:58', 0, 0.00, 1, 0),
(96, 118, 1, 'Standard Double Room', 3700.00, 1, 9, 33300.00, '2025-02-18 11:42:58', 0, 0.00, 1, 0),
(97, 119, 3, 'Family Room', 2500.00, 1, 1, 2500.00, '2025-02-18 11:55:07', 1, 0.00, 1, 0),
(98, 120, 2, 'Deluxe Family Room', 2000.00, 1, 27, 54000.00, '2025-02-18 12:18:14', 0, 0.00, 1, 0),
(99, 121, 2, 'Deluxe Family Room', 2000.00, 1, 1, 2000.00, '2025-03-05 11:57:47', 0, 0.00, 1, 0),
(100, 122, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 11:59:22', 0, 0.00, 1, 0),
(101, 123, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 12:07:08', 0, 0.00, 1, 0),
(102, 126, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:20:23', 0, 0.00, 1, 0),
(103, 127, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:22:36', 0, 0.00, 1, 0),
(104, 128, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 13:35:20', 0, 0.00, 1, 0),
(105, 129, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 13:42:59', 0, 0.00, 1, 0),
(106, 130, 1, 'Standard Double Room', 3700.00, 1, 2, 7400.00, '2025-03-05 13:51:23', 0, 0.00, 1, 0),
(107, 131, 1, 'Standard Double Room', 3700.00, 1, 1, 3700.00, '2025-03-05 13:59:43', 0, 0.00, 1, 0),
(108, 132, 1, 'Standard Double Room', 3700.00, 1, 3, 11100.00, '2025-03-05 14:07:16', 0, 0.00, 1, 0),
(109, 133, 3, 'Family Room', 2500.00, 1, 2, 5000.00, '2025-03-05 14:31:39', 0, 0.00, 1, 0);

--
-- Triggers `room_bookings`
--
DELIMITER $$
CREATE TRIGGER `after_booking_insert` AFTER INSERT ON `room_bookings` FOR EACH ROW BEGIN
    UPDATE room_types
    SET available_rooms = GREATEST(available_rooms - 1, 0)
    WHERE room_type_id = NEW.room_type_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`image_id`, `room_type_id`, `image_path`) VALUES
(1, 1, 'uploads/rooms/standard.jpg'),
(2, 1, 'uploads/rooms/5.jpg'),
(3, 2, 'uploads/rooms/5.jpg'),
(4, 2, 'uploads/rooms/5.jpg'),
(5, 1, 'uploads/rooms/5.jpg'),
(6, 1, 'uploads/rooms/5.jpg'),
(7, 2, 'uploads/rooms/5.jpg'),
(8, 2, 'uploads/rooms/5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `room_inquiries`
--

CREATE TABLE `room_inquiries` (
  `inquiry_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `guests` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_reviews`
--

CREATE TABLE `room_reviews` (
  `review_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL,
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_reviews`
--

INSERT INTO `room_reviews` (`review_id`, `room_type_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(4, 3, 9, 1.0, 'dfdfd', '2025-03-20 13:14:10'),
(5, 2, 9, 4.0, 'good', '2025-03-20 13:17:37'),
(6, 1, 9, 5.0, 'Gooods', '2025-03-20 13:21:38'),
(7, 3, 5, 5.0, 'goods', '2025-03-23 09:03:57'),
(8, 1, 5, 5.0, 'vcvcv', '2025-03-24 10:10:12'),
(9, 3, 1, 5.0, 'gOODS\r\n', '2025-03-27 07:06:17'),
(13, 2, 5, 5.0, 'fgf', '2025-04-01 17:03:08'),
(14, 3, 3, 5.0, 'aaaaaaaaaa', '2025-04-03 07:38:32'),
(15, 2, 3, 5.0, 'aaaaaaaaaaaaaaaa', '2025-04-03 07:38:47'),
(16, 2, 8, 1.0, 'Ang bantot ng unan', '2025-04-05 02:47:57'),
(17, 3, 8, 5.0, 'aaa', '2025-04-08 03:31:00');

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
  `rating` decimal(3,1) DEFAULT 0.0,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `discount_valid_until` date DEFAULT NULL,
  `rating_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`, `image2`, `image3`, `discount_percent`, `discount_valid_until`, `rating_count`) VALUES
(1, 'Standard Double Roomssssssss', 3700.00, 3, 'Cozy and comfortable, our standard room comes with 2 single beds, ideal for friends or business travelers.', '2 Single Beds', 5.0, 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 20, '2025-02-25', 2),
(2, 'Deluxe Family Room', 2000.00, 1, 'Our deluxe room offers a queen bed and a single bed, perfect for small families or groups.', '1 Queen Bed, 1 Single Bed', 3.8, 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 20, '2025-02-23', 4),
(3, 'Family Room', 2500.00, 5, 'Perfect for families, this spacious room features 1 queen bed and 2 single beds.', '1 Queen Bed, 2 Single Beds', 4.2, 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 'uploads/rooms/standard.jpg', 25, '2025-02-12', 5);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `total_amount`, `payment_method`, `created_at`, `order_date`) VALUES
(1, 13, 150.00, 'gcash', '2025-02-17 19:51:30', '2025-04-07 13:16:04'),
(2, 12, 150.00, 'gcash', '2025-02-17 19:51:47', '2025-04-07 13:16:04'),
(3, 18, 235.00, 'gcash', '2025-02-17 19:55:03', '2025-04-07 13:16:04'),
(4, 19, 150.00, 'gcash', '2025-02-17 20:01:01', '2025-04-07 13:16:04'),
(5, 20, 200.00, 'gcash', '2025-02-17 22:35:04', '2025-04-07 13:16:04'),
(6, 21, 150.00, 'gcash', '2025-02-18 06:10:10', '2025-04-07 13:16:04'),
(7, 22, 360.00, 'maya', '2025-02-18 06:10:55', '2025-04-07 13:16:04'),
(8, 23, 355.00, 'gcash', '2025-02-18 06:12:33', '2025-04-07 13:16:04'),
(9, 25, 300.00, 'gcash', '2025-02-18 12:38:51', '2025-04-07 13:16:04');

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
-- Table structure for table `seasonal_effects`
--

CREATE TABLE `seasonal_effects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `effect_type` enum('snow','hearts','fireworks') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasonal_effects`
--

INSERT INTO `seasonal_effects` (`id`, `name`, `start_date`, `end_date`, `effect_type`, `is_active`, `created_at`) VALUES
(1, 'Christmas Snow', '2025-04-01', '2025-04-30', 'snow', NULL, '2025-04-07 13:54:09'),
(2, 'Valentine Hearts', '2025-04-01', '2025-04-30', 'hearts', 0, '2025-04-07 13:54:09'),
(3, 'New Year Fireworks', '2025-04-01', '2025-04-30', 'fireworks', 1, '2025-04-07 13:54:09');

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
  `package_type` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_bookings`
--

INSERT INTO `table_bookings` (`id`, `user_id`, `package_name`, `contact_number`, `email_address`, `booking_date`, `booking_time`, `num_guests`, `special_requests`, `payment_method`, `total_amount`, `amount_paid`, `change_amount`, `payment_status`, `status`, `package_type`, `payment_reference`, `payment_proof`, `cancellation_reason`, `cancelled_at`, `created_at`) VALUES
(1, NULL, '', '', '', '2025-12-12', '11:11:00', 2, '', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Checked Out', 'Couple', NULL, NULL, NULL, NULL, '2025-02-08 10:26:37'),
(2, NULL, '', '', '', '2025-02-19', '14:22:00', 2, '', 'cash', 1500.00, 0.00, 0.00, 'Pending', 'Pending', '0', NULL, NULL, NULL, NULL, '2025-02-08 12:45:35'),
(3, NULL, '', '', '', '2025-02-22', '11:11:00', 2, '', 'cash', 9000.00, 0.00, 0.00, 'Pending', 'Archived', '0', NULL, NULL, NULL, NULL, '2025-02-08 12:46:26'),
(4, NULL, '', '', '', '2025-02-22', '10:07:00', 2, 'pwede palagyan ng chicaron', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Pending', 'Couple', NULL, NULL, NULL, NULL, '2025-02-08 14:08:34'),
(5, NULL, '', '', '', '2025-01-01', '11:11:00', 2, '', 'Cash', 10000.00, 10000.00, 0.00, 'Pending', 'Pending', 'Family Table', NULL, NULL, NULL, NULL, '2025-02-09 08:40:14'),
(6, NULL, '', '', '', '2025-02-13', '20:28:00', 2, '', 'Cash', 199.00, 200.00, 1.00, 'Pending', 'Checked Out', 'Couple', NULL, NULL, NULL, NULL, '2025-02-12 12:27:32'),
(7, NULL, '', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-17', '22:35:00', 2, NULL, 'GCash', 199.00, 0.00, 0.00, 'Pending', 'Pending', 'Koupals', NULL, NULL, NULL, NULL, '2025-02-17 14:35:25'),
(9, 4, '', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-17', '23:43:00', 2, NULL, 'GCash', 149.50, 74.75, 0.00, 'Partially Paid', 'Pending', 'Friends', NULL, NULL, NULL, NULL, '2025-02-17 15:43:25'),
(10, 4, 'Koupals', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '00:47:00', 2, NULL, 'Cash', 219.50, 109.75, 0.00, 'Partially Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-17 16:48:09'),
(11, 4, 'Koupals', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '01:03:00', 2, NULL, 'GCash', 219.50, 169.75, 0.00, 'Partially Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-17 17:03:53'),
(12, 4, 'Friends', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-02-18', '01:05:00', 2, NULL, 'GCash', 149.50, 74.75, 0.00, 'Partially Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-17 17:05:49'),
(13, 3, 'Koupals', '9876543200', 'chsdjf@gmail.com', '2025-02-18', '06:35:00', 2, NULL, 'GCash', 319.00, 319.00, 0.00, 'Fully Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-17 22:36:45'),
(14, 1, 'Koupals', '09876543200', 'aizzyvillanueva43@gmail.com', '2025-02-18', '14:14:00', 2, NULL, 'Cash', 549.50, 499.75, 0.00, 'Partially Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-18 06:15:17'),
(15, 1, 'Package A', '09951779220', 'alfred@gmail.com', '2025-02-11', '19:44:00', 30, NULL, 'GCash', 10120.00, 5120.00, 0.00, 'Partially Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-18 11:46:08'),
(16, 1, 'Koupals', '09951779220', 'alfred@gmail.com', '2025-02-19', '20:44:00', 2, NULL, 'GCash', 919.00, 919.00, 0.00, 'Fully Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-02-18 12:54:14'),
(17, 8, 'Friends', '987654098765', 'cjxfd@gmail.com', '2025-03-17', '19:51:00', 3, NULL, 'Cash', 26929.00, 26929.00, 0.00, 'Fully Paid', 'Pending', NULL, NULL, NULL, NULL, NULL, '2025-03-18 02:52:28'),
(18, 5, 'Pacakge B', '', '', '2025-04-05', '14:19:00', 33, NULL, '1', 66000.00, 66000.00, 0.00, 'Paid', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/67f08529333e3_Screenshot (1023).png', NULL, NULL, '2025-04-05 01:19:37'),
(21, 5, 'Pacakge B', '09123456789', 'christianrealisan45@gmail.com', '2025-04-05', '14:19:00', 33, NULL, '1', 36000.00, 36000.00, 0.00, 'Paid', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/payment_67f088cdde71f.png', NULL, NULL, '2025-04-05 01:35:09'),
(23, 5, 'Friends', '09123456789', 'christianrealisan45@gmail.com', '2025-04-05', '14:37:00', 0, '', 'maya', 150.00, 150.00, 0.00, 'Pending', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/payment_67f095481a132.png', NULL, NULL, '2025-04-05 02:28:24'),
(24, 5, 'Friends', '09123456789', 'christianrealisan45@gmail.com', '2025-04-05', '14:37:00', 0, '', 'gcash', 150.00, 150.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', 'payment_1743820811_67f0980bedcc1.png', NULL, NULL, '2025-04-05 02:40:11'),
(25, 5, 'Friends', '09123456789', 'christianrealisan45@gmail.com', '2025-04-05', '14:37:00', 0, '', 'maya', 500.00, 500.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 02:43:50'),
(26, 8, 'Pacakge B', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '16:36:00', 222, NULL, '1', 225000.00, 225000.00, 0.00, 'Paid', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/payment_67f0a557ad913.png', NULL, NULL, '2025-04-05 03:36:55'),
(27, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '17:52:00', 0, '', 'gcash', 120.00, 120.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 06:52:38'),
(28, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '17:52:00', 0, '', 'gcash', 120.00, 120.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 06:57:22'),
(35, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '17:52:00', 0, '', 'gcash', 150.00, 150.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 07:36:05'),
(36, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '17:52:00', 0, '', 'maya', 120.00, 120.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 09:08:41'),
(37, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '17:52:00', 0, '', 'gcash', 120.00, 120.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-05 09:51:49'),
(38, 8, 'Pacakge B', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '11:54:00', 2, NULL, '1', 33000.00, 33000.00, 0.00, 'Paid', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/payment_67f10c0fe33f5.png', NULL, NULL, '2025-04-05 10:55:11'),
(39, 8, 'Pacakge B', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-05', '11:54:00', 2, NULL, '1', 33000.00, 33000.00, 0.00, 'Paid', 'Pending', 'Ultimate', '5434535', 'uploads/payment_proofs/payment_67f10c1007c09.png', NULL, NULL, '2025-04-05 10:55:12'),
(40, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-07', '14:04:00', 0, '', 'gcash', 120.00, 120.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-06 13:03:57'),
(41, 8, 'Friends', '09412222222', 'christianrealisan45aa@gmail.com', '2025-04-08', '16:42:00', 0, '', 'gcash', 140.00, 140.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-08 03:42:49'),
(42, 3, 'Friends', '09123456789', 'fammeladeguzman21@gmail.com', '2025-04-08', '18:54:00', 0, '', 'gcash', 300.00, 150.00, 0.00, 'Partially Paid', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-08 05:55:20'),
(43, 3, 'Friends', '09123456789', 'fammeladeguzman21@gmail.com', '2025-04-09', '12:36:00', 0, '', 'gcash', 500.00, 500.00, 0.00, 'Processing', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-08 11:36:58'),
(44, 3, 'Friends', '09123456789', 'fammeladeguzman21@gmail.com', '2025-04-09', '15:51:00', 0, '', 'gcash', 150.00, 75.00, 0.00, 'Partially Paid', 'Pending', 'Ultimate', '5434535', NULL, NULL, NULL, '2025-04-08 14:51:48');

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
  `price` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `menu_items` varchar(255) NOT NULL,
  `available_tables` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_packages`
--

INSERT INTO `table_packages` (`id`, `package_name`, `price`, `capacity`, `description`, `menu_items`, `available_tables`, `image_path`, `image1`, `image2`, `image3`, `image4`, `image5`, `status`) VALUES
(1, 'Couples', 'Free', 2, 'Perfect for kupals', '', 0, 'images/couple.jpg', NULL, NULL, NULL, NULL, NULL, 'active'),
(2, 'Friends', 'Free', 4, 'Ideal for small groups', '', 0, 'images/friends.jpg', NULL, NULL, NULL, NULL, NULL, 'active'),
(3, 'Family', 'Free', 10, 'Great for family gatherings', '', 0, 'images/family.jpg', NULL, NULL, NULL, NULL, NULL, 'active'),
(7, 'Package A', '20000', 30, 'Basic package for large groups', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks', 2, 'images/table2.jpg', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active'),
(8, 'Pacakge B', '33000', 30, 'Premium package with extra services', 'Appetizer, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 2, 'images/table1.jpg', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active'),
(9, 'Package C', '45000', 30, 'All-inclusive luxury package', '3 Appetizer, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2 Desserts, Drinks', 1, 'images/table3.jpg', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active');

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
  `profile_photo` varchar(255) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `firstname`, `lastname`, `phone`, `profile_photo`, `email`, `password`, `is_verified`, `verification_code`, `verification_expiry`, `created_at`, `updated_at`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Christian Realisan', '0', '0', NULL, 'christianrealisan3@gmail.com', '$2y$10$wTeWBkmwQn7UUuxW0ahQveDtRIhPULLjCBbND2MZ.mdXcnVvoxv8e', 1, '986507', '2025-02-12 16:24:15', '2025-02-12 15:22:15', '2025-02-12 15:22:51', NULL, NULL),
(2, 'Mang Juan', 'Realisan', '09123456789', NULL, 'mangjuan@gmail.com', '$2y$10$mbQd/yeOWp3qy90mTAiUnOCnVnl5o33rYSnDtGXRPx4kxf1ZE7m0.', 1, '382013', '2025-02-12 16:31:55', '2025-02-12 15:29:55', '2025-03-27 00:34:29', NULL, NULL),
(3, 'Fammela Nicole Jumig', 'De Guzman', '09123456789', '67f49f61e9037_1744084833.jpg', 'fammeladeguzman21@gmail.com', '$2y$10$Ic53xTZkXkmyqtCTpdq.Y.uauCDKuC48etMP4ojLRFK8JLz6ZzRRi', 1, '484717', '2025-02-13 09:51:10', '2025-02-13 08:49:10', '2025-04-08 04:00:33', NULL, NULL),
(4, 'Kenjo M. Marimon', '0', '0', NULL, 'aizzyvillanueva43@gmail.com', '$2y$10$7tm0ztu9lKpjfwTWO2Cc8.9yp/ATXCDTv6UzJB3piqf6ebL7Kcsfq', 1, '188543', '2025-02-17 13:10:04', '2025-02-17 12:08:04', '2025-02-17 12:09:01', NULL, NULL),
(5, 'Christian', 'Realisan', '09123456789', '67e3a6c5748cb_1742972613.png', 'christianrealisan45@gmail.com', '$2y$10$MbTmvDOYxK55V0Xqg.pS7.jKtGD/eUUk9eE1Fx4d8.6ZM8a3eu1Bu', 1, '847337', '2025-03-05 12:45:53', '2025-03-05 11:43:53', '2025-03-26 07:03:33', NULL, NULL),
(6, 'Christian', '0', '2147483647', NULL, 'christianrealisan45g@mail.com', '$2y$10$J5itO4uk1BdPdJOWs.eybuW2a7JkwUUYXPbqZqxANyrsJsoMMS/A6', 1, '092675', '2025-03-05 13:32:54', '2025-03-05 12:17:20', '2025-03-05 12:18:15', NULL, NULL),
(7, 'Christian', '0', '09466666666', NULL, 'christianrealisan45a@gmail.com', '$2y$10$9dxUhVbiPnQRfqBOTzKgieF7KRM1l6Bhj8jzXppN6.YwtojKbLumW', 0, '654991', '2025-03-07 13:12:47', '2025-03-05 12:27:04', '2025-03-07 12:10:47', NULL, NULL),
(8, 'Christian', 'Realisan', '09412222222', '67d7df6325677_1742200675.jpg', 'christianrealisan45aa@gmail.com', '$2y$10$K9JUOICaUG27CApObtuIYeGDXnrxGmE5gn9AbhhabBCdXjDLrsrha', 1, '182129', '2025-03-05 13:32:26', '2025-03-05 12:30:26', '2025-03-17 08:37:55', NULL, NULL),
(9, 'Christian', 'Realisan', '09123456789', '67d9e3ca294dc_1742332874.png', 'chano@gmail.com', '$2y$10$QR.rzLDEvtPNSJP93V0jRuJTNgRMWQQXK3ziytO3kCDQ.5m7eN1jm', 0, '009749', '2025-03-18 02:34:36', '2025-03-18 01:32:36', '2025-03-18 21:21:57', NULL, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `code` varchar(6) NOT NULL,
  `type` enum('email','phone') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `user_id`, `email`, `phone`, `code`, `type`, `created_at`, `expires_at`, `is_used`) VALUES
(1, NULL, 'christianrealisan45@gmail.com', NULL, '403249', 'email', '2025-04-07 23:47:41', '2025-04-07 23:49:41', 0),
(2, NULL, 'christianrealisan45@gmail.com', NULL, '410346', 'email', '2025-04-07 23:48:06', '2025-04-07 23:50:06', 0),
(3, NULL, 'christianrealisan45aa@gmail.com', NULL, '092089', 'email', '2025-04-07 23:51:44', '2025-04-07 23:53:44', 0),
(4, NULL, 'christianrealisan45aa@gmail.com', NULL, '416394', 'email', '2025-04-07 23:52:09', '2025-04-07 23:54:09', 0),
(5, NULL, 'christianrealisan45aa@gmail.com', NULL, '576384', 'email', '2025-04-08 00:11:05', '2025-04-08 00:13:05', 0),
(6, NULL, 'christianrealisan45aa@gmail.com', NULL, '843370', 'email', '2025-04-08 00:11:29', '2025-04-08 00:13:29', 0),
(7, NULL, 'christianrealisan1225@gmail.com', NULL, '868224', 'email', '2025-04-08 00:12:25', '2025-04-08 00:14:25', 0),
(8, NULL, 'christianrealisan1225@gmail.com', NULL, '666952', 'email', '2025-04-08 00:15:52', '2025-04-08 00:17:52', 1),
(9, NULL, 'christianrealisan1225@gmail.com', NULL, '316476', 'email', '2025-04-08 00:19:19', '2025-04-08 00:21:19', 1),
(10, NULL, 'christianrealisan1225@gmail.com', NULL, '111793', 'email', '2025-04-08 00:32:41', '2025-04-08 00:34:41', 1),
(11, NULL, 'christianrealisan1225@gmail.com', NULL, '907136', 'email', '2025-04-08 00:33:05', '2025-04-08 00:35:05', 0),
(12, NULL, 'christianrealisan1225@gmail.com', NULL, '411886', 'email', '2025-04-08 00:38:07', '2025-04-08 00:40:07', 0),
(13, NULL, 'christianrealisan1225@gmail.com', NULL, '834644', 'email', '2025-04-08 00:38:59', '2025-04-08 00:40:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `verification_methods`
--

CREATE TABLE `verification_methods` (
  `id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `maintenance_message` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_methods`
--

INSERT INTO `verification_methods` (`id`, `method_name`, `is_active`, `maintenance_message`, `last_updated`) VALUES
(1, 'email', 1, 'Under Maintenance', '2025-04-07 22:52:55'),
(2, 'phone', NULL, 'Phone verification is currently under maintenance. Please try email verification.', '2025-04-07 22:53:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_status`
--
ALTER TABLE `admin_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin` (`admin_id`);

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
-- Indexes for table `booking_list`
--
ALTER TABLE `booking_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_time` (`user_id`,`created_at`),
  ADD KEY `reply_to_id` (`reply_to_id`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
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
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_inquiries`
--
ALTER TABLE `room_inquiries`
  ADD PRIMARY KEY (`inquiry_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_review` (`room_type_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `seasonal_effects`
--
ALTER TABLE `seasonal_effects`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `phone` (`phone`),
  ADD KEY `code` (`code`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `verification_methods`
--
ALTER TABLE `verification_methods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_status`
--
ALTER TABLE `admin_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `advance_orders`
--
ALTER TABLE `advance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=413;

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
-- AUTO_INCREMENT for table `booking_list`
--
ALTER TABLE `booking_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=338;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=431;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_inquiries`
--
ALTER TABLE `room_inquiries`
  MODIFY `inquiry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_reviews`
--
ALTER TABLE `room_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
-- AUTO_INCREMENT for table `seasonal_effects`
--
ALTER TABLE `seasonal_effects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `verification_methods`
--
ALTER TABLE `verification_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `booking_list`
--
ALTER TABLE `booking_list`
  ADD CONSTRAINT `booking_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `booking_list_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`reply_to_id`) REFERENCES `chat_messages` (`id`);

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
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD CONSTRAINT `order_item_addons_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  ADD CONSTRAINT `reservation_orders_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `table_reservations` (`reservation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_orders_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `room_inquiries`
--
ALTER TABLE `room_inquiries`
  ADD CONSTRAINT `room_inquiries_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`),
  ADD CONSTRAINT `room_inquiries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD CONSTRAINT `room_reviews_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`),
  ADD CONSTRAINT `room_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
