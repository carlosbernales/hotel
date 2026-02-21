-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 21, 2026 at 01:14 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u763377220_hotelms`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'About Casa Estela',
  `description` text NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `title`, `description`, `last_updated`) VALUES
(2, 'About Casa Estela', '                                                                                                                                                            Casa Estela Boutique Hotel, located along Gov. B. Marsigan St. in Brgy. Libis, Calapan City, Oriental Mindoro, is a charming establishment that combines historic charm with modern elegance. Originally a private residence, this two-story building was transformed into a boutique hotel starting in 2017 by Engr. Estela Macapagal, a businessperson and contractor. Renovations were undertaken with the help of her son, Marc Jill M. Dimapilis, a civil engineer who also contributed to the hotel\'s culinary and service concepts.\r\n                                                                                                                                                ', '2026-01-16 12:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `about_slideshow`
--

CREATE TABLE `about_slideshow` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_slideshow`
--

INSERT INTO `about_slideshow` (`id`, `image_path`, `alt_text`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'images/garden.jpg', 'Image 1', 1, 1, '2025-04-11 21:31:45'),
(2, 'images/hall3.jpg', 'Image 2', 2, 1, '2025-04-11 21:31:45'),
(3, 'images/garden.jpg', 'Image 3', 3, 1, '2025-04-11 21:31:45'),
(4, 'images/hall.jpg', 'Image 4', 4, 1, '2025-04-11 21:31:45'),
(5, 'images/gard.jpg', 'Image 5', 5, 1, '2025-04-11 21:31:45'),
(6, 'images/garden1.jpg', 'Image 6', 6, 1, '2025-04-11 21:31:45'),
(8, 'images/67f9965701f70.gif', 'basta', 7, 1, '2025-04-11 22:23:19');

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'admin@example.com', '$2y$10$dlPR3HFncDBOCr/zZz4SH.QAGLbJwLgU4zqSFtof432mJGdqmFqfa', 'Alfred Hendrik', 'A', 'Aceveda', 20, 'Balite Calapan City Oriental Mindoro', 'Manager', 'admin@example.com', NULL, NULL, '2025-02-16 18:25:44'),
(1, 'admin', '$2y$10$VLIHEg53Wc4m28V.iwDDyuLW8f2IsAFhYRc02.1yTyNHSzxr754Uy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 18:15:04'),
(2, 'admin@example.com', '$2y$10$dlPR3HFncDBOCr/zZz4SH.QAGLbJwLgU4zqSFtof432mJGdqmFqfa', 'Alfred Hendrik', 'A', 'Aceveda', 20, 'Balite Calapan City Oriental Mindoro', 'Manager', 'admin@example.com', NULL, NULL, '2025-02-16 18:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 17, 424, 1, 270.00, '2025-03-18 02:52:29'),
(14, 6, 3, 1, 0.00, '2025-04-09 16:48:53'),
(15, 8, 3, 1, 0.00, '2025-04-09 16:55:34'),
(16, 9, 3, 2, 0.00, '2025-04-09 16:59:46'),
(17, 11, 3, 1, 0.00, '2025-04-09 17:22:28'),
(18, 12, 3, 1, 0.00, '2025-04-09 17:30:07'),
(19, 18, 3, 1, 0.00, '2025-04-10 13:59:22'),
(0, 2, 1, 1, 0.00, '2025-04-16 01:12:31'),
(0, 11, 0, 1, 0.00, '2025-04-21 08:20:48'),
(0, 10, 432, 2, 0.00, '2025-05-17 00:55:20'),
(0, 11, 0, 5, 0.00, '2025-05-17 00:58:47'),
(0, 12, 432, 1, 0.00, '2025-05-17 01:06:19'),
(0, 13, 2, 1, 0.00, '2025-05-17 01:16:36'),
(0, 13, 432, 1, 0.00, '2025-05-17 01:16:37'),
(0, 14, 3, 1, 0.00, '2025-05-17 02:02:07'),
(0, 15, 1, 1, 0.00, '2025-05-17 02:06:18'),
(0, 1, 3, 1, 0.00, '2025-05-17 02:28:14'),
(0, 2, 2, 1, 0.00, '2025-05-17 02:29:05'),
(0, 3, 3, 1, 0.00, '2025-05-17 03:06:46'),
(0, 4, 3, 1, 0.00, '2025-05-17 03:09:48');

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
(5, 'Hot Shower', 'fa-shower'),
(8, 'Bath Thub', NULL);

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
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `available_quantity` int(11) NOT NULL DEFAULT 0,
  `total_quantity` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `item_type`, `available_quantity`, `total_quantity`, `last_updated`, `price`) VALUES
(1, 'Single Bed', 10, 10, '2025-12-07 09:04:28', 1000.00),
(2, 'Queens Bed', 10, 10, '2025-05-13 06:44:32', 1000.00),
(3, 'Toothbrush', 1, 2, '2026-02-13 15:31:20', 0.00),
(4, 'Towel', 0, 0, '2025-12-04 05:56:50', 0.00),
(5, 'Slippers', 0, 0, '2025-12-04 05:56:50', 0.00),
(6, 'Shampoo', 0, 0, '2025-12-04 05:56:50', 0.00),
(7, 'Soap', 0, 0, '2025-12-04 05:56:50', 0.00),
(8, 'Hair Dryer', 0, 0, '2025-12-04 05:56:50', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `booked_rooms`
--

CREATE TABLE `booked_rooms` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_number_fk_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_type_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booked_rooms`
--

INSERT INTO `booked_rooms` (`id`, `booking_id`, `room_number_fk_id`, `room_type_id`, `room_type_name`, `price`, `created_at`) VALUES
(1, 1, 5, 2, 'Double Occupancy', 5000.00, '2026-02-20 16:20:03'),
(2, 1, 5, 2, 'Double Occupancy', 5000.00, '2026-02-20 16:20:03'),
(3, 1, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:20:03'),
(4, 1, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:20:03'),
(5, 1, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:20:03'),
(6, 1, 2, 4, 'Family', 4500.00, '2026-02-20 16:20:03'),
(7, 1, 2, 4, 'Family', 4500.00, '2026-02-20 16:20:03'),
(8, 1, 2, 4, 'Family', 4500.00, '2026-02-20 16:20:03'),
(9, 2, 5, 2, 'Double Occupancy', 5000.00, '2026-02-20 16:36:04'),
(10, 2, 5, 2, 'Double Occupancy', 5000.00, '2026-02-20 16:36:04'),
(11, 2, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:36:04'),
(12, 2, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:36:04'),
(13, 2, 1, 3, 'Triple Occupancy', 1700.00, '2026-02-20 16:36:04'),
(14, 2, 2, 4, 'Family', 4500.00, '2026-02-20 16:36:04'),
(15, 2, 2, 4, 'Family', 4500.00, '2026-02-20 16:36:04'),
(16, 2, 2, 4, 'Family', 4500.00, '2026-02-20 16:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `booking_reference` varchar(255) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `number_of_guests` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_quantity` int(11) DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT NULL,
  `remaining_balance` decimal(10,2) DEFAULT NULL,
  `user_types` enum('admin','frontdesk') NOT NULL DEFAULT 'frontdesk',
  `num_adults` int(11) DEFAULT 0,
  `num_children` int(11) DEFAULT 0,
  `extra_bed` varchar(50) DEFAULT NULL,
  `discount_type` varchar(50) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `rejection_reason` text DEFAULT NULL,
  `payment_amount` double(10,2) DEFAULT NULL,
  `payment_change` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `booking_reference`, `user_id`, `first_name`, `last_name`, `email`, `contact`, `booking_type`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `room_type_id`, `room_quantity`, `payment_option`, `payment_method`, `total_amount`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `user_types`, `num_adults`, `num_children`, `extra_bed`, `discount_type`, `discount_percentage`, `discount_amount`, `status`, `rejection_reason`, `payment_amount`, `payment_change`) VALUES
(1, 'BOOK-8LK5JV3KBXI', 54, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'iansilang123@gmail.com', '09123454545', 'online', '2026-02-21 00:00:00', '2026-02-22 00:00:00', '14:00:00', 1, NULL, 8, 'down_payment', 'maya', 28600.00, '2026-02-20 16:20:03', 1, 1500.00, 27100.00, '', 1, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(2, 'BOOK-XNEDUENSE0C', 54, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'iansilang123@gmail.com', '09123454545', 'online', '2026-02-22 00:00:00', '2026-02-23 00:00:00', '14:00:00', 1, NULL, 8, 'down_payment', 'maya', 28600.00, '2026-02-20 16:36:04', 1, 1500.00, 27100.00, '', 1, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_amenities`
--

CREATE TABLE `booking_amenities` (
  `id` int(11) NOT NULL,
  `amenities_fk_id` int(11) NOT NULL,
  `booking_fk_id` int(11) NOT NULL,
  `bedOrNot` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `amenity_name` varchar(255) NOT NULL,
  `price` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_amenities`
--

INSERT INTO `booking_amenities` (`id`, `amenities_fk_id`, `booking_fk_id`, `bedOrNot`, `quantity`, `amenity_name`, `price`) VALUES
(1, 7, 7, NULL, 1, 'Soap', 0.00),
(3, 6, 7, NULL, 1, 'Shampoo', 0.00);

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
-- Table structure for table `booking_check_inout`
--

CREATE TABLE `booking_check_inout` (
  `id` int(11) NOT NULL,
  `booking_fk_id` int(11) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking_check_inout`
--

INSERT INTO `booking_check_inout` (`id`, `booking_fk_id`, `check_in`, `check_out`) VALUES
(1, 1, '2026-02-06 00:00:00', '2026-02-07 00:00:00'),
(2, 2, '2026-02-09 00:00:00', '2026-02-10 00:00:00'),
(3, 3, '2026-02-09 00:00:00', '2026-02-10 00:00:00'),
(4, 4, '2026-02-10 00:00:00', '2026-02-13 00:00:00'),
(5, 5, '2026-02-11 00:00:00', '2026-02-12 00:00:00'),
(6, 6, '2026-02-13 00:00:00', '2026-02-14 00:00:00'),
(7, 7, '2026-02-12 00:00:00', '2026-02-15 00:00:00'),
(8, 8, '2026-02-17 00:00:00', '2026-02-18 00:00:00'),
(9, 9, '2026-02-19 00:00:00', '2026-02-21 00:00:00'),
(10, 10, '2026-02-19 00:00:00', '2026-02-20 00:00:00'),
(11, 11, '2026-02-19 00:00:00', '2026-02-20 00:00:00'),
(12, 12, '2026-02-19 00:00:00', '2026-02-20 00:00:00'),
(13, 13, '2026-02-22 00:00:00', '2026-02-24 00:00:00'),
(14, 14, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(15, 15, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(16, 16, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(17, 17, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(18, 18, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(19, 19, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(20, 20, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(21, 21, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(22, 22, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(23, 23, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(24, 24, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(25, 1, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(26, 2, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(27, 3, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(28, 4, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(29, 5, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(30, 6, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(31, 7, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(32, 8, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(33, 9, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(34, 10, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(35, 11, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(36, 12, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(37, 13, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(38, 14, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(39, 1, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(40, 1, '2026-02-20 00:00:00', '2026-02-21 00:00:00'),
(41, 2, '2026-02-22 00:00:00', '2026-02-23 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `booking_display_settings`
--

CREATE TABLE `booking_display_settings` (
  `id` int(11) NOT NULL,
  `booking_type` enum('room','table','event') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `display_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `layout_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Table structure for table `booking_extensions`
--

CREATE TABLE `booking_extensions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `original_checkout` date NOT NULL,
  `new_checkout` date NOT NULL,
  `days_extended` int(11) NOT NULL,
  `additional_cost` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `extension_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_extensions`
--

INSERT INTO `booking_extensions` (`id`, `booking_id`, `original_checkout`, `new_checkout`, `days_extended`, `additional_cost`, `payment_method`, `extension_date`) VALUES
(1, 132, '2025-03-28', '2025-03-30', 2, 2500.00, 'GCash', '2025-03-27 13:45:05'),
(2, 132, '2025-03-30', '2025-04-01', 2, 2500.00, 'Cash', '2025-03-27 13:46:02'),
(3, 100, '2025-02-20', '2025-02-22', 2, 7400.00, 'Cash', '2025-03-27 18:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `booking_history`
--

CREATE TABLE `booking_history` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `performed_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_history`
--

INSERT INTO `booking_history` (`id`, `booking_id`, `action`, `details`, `performed_by`, `created_at`) VALUES
(1, '1', 'reschedule', 'Booking rescheduled from 2025-11-14 - 2025-11-15 to 2025-11-24 - 2025-11-25', 'Admin', '2025-11-20 11:32:30');

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
-- Table structure for table `cashier`
--

CREATE TABLE `cashier` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(28, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-08 09:24:29', NULL),
(29, 'guest_gyaa2qjw5', 'sghj', 'user', 0, '2025-04-11 11:30:27', NULL),
(30, 'guest_gyaa2qjw5', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-11 11:30:27', NULL),
(31, 'guest_gyaa2qjw5', 'ay hoy', 'user', 0, '2025-04-11 17:16:52', NULL),
(32, 'guest_gyaa2qjw5', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-11 17:16:52', NULL),
(33, 'guest_gyaa2qjw5', 'kumusta ka aking mahal', 'user', 0, '2025-04-11 17:17:00', NULL),
(34, 'guest_gyaa2qjw5', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-11 17:17:00', NULL),
(35, 'guest_y71mi6rv0', 'open pa po kayo ?', 'user', 0, '2025-04-12 06:25:22', NULL),
(36, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:25:22', NULL),
(37, 'guest_y71mi6rv0', 'open pa po kayo ?', 'user', 0, '2025-04-12 06:26:33', NULL),
(38, 'guest_y71mi6rv0', 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:26:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `checked_in`
--

CREATE TABLE `checked_in` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `nights_staying` int(11) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checked_in`
--

INSERT INTO `checked_in` (`id`, `first_name`, `last_name`, `contact_number`, `email`, `room_type_id`, `room_type`, `check_in_date`, `check_out_date`, `nights_staying`, `number_of_guests`, `special_requests`, `payment_method`, `total_amount`, `status`, `created_at`) VALUES
(7, 'Alleah', 'basta', '91234567878', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', 1250.00, 'active', '2025-04-05 13:11:37'),
(8, 'Alleah', 'basta', '91234567878', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', 1250.00, 'active', '2025-04-05 13:12:42'),
(9, 'myra', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', 1250.00, 'active', '2025-04-05 13:14:28'),
(10, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 1, 'Standard Double Room', '2025-04-07', '2025-04-08', 1, 1, '', 'Cash', 3700.00, 'active', '2025-04-05 16:24:32'),
(11, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 2, 'Deluxe Family Room', '2025-04-07', '2025-04-14', 7, 1, '', 'Cash', 14000.00, 'active', '2025-04-05 16:28:42'),
(12, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 2, 'Deluxe Family Room', '2025-04-07', '2025-04-14', 7, 1, '', 'Cash', 14000.00, 'active', '2025-04-05 16:31:46'),
(13, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-12', '2025-04-14', 2, 1, '', 'Cash', 2500.00, 'active', '2025-04-05 17:57:13'),
(14, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-06', '2025-04-06', 0, 1, '', 'Cash', 0.00, 'active', '2025-04-05 19:14:16'),
(15, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-06', '2025-04-06', 0, 1, '', 'Cash', 0.00, 'active', '2025-04-05 19:19:41');

-- --------------------------------------------------------

--
-- Table structure for table `checked_out`
--

CREATE TABLE `checked_out` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `room_type` varchar(50) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `checkout_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'Checked Out',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'fab fa-facebook', 'Casa Estela Boutique Hotel & Cafés', 'https://web.facebook.com/casaestelahotelcafe', 0, 1, 1),
(2, 'fas fa-envelope', 'casaestelahotelcafe@gmail.com', 'mailto:casaestelahotelcafe@gmail.com', 1, 2, 1),
(3, 'fas fa-phone', '0908 747 4892', 'tel:+09087474892', 0, 3, 1),
(4, 'fab fa-twitter', '@casaestelahlcf', '#', 1, 4, 1),
(5, 'fab fa-instagram', '@casaestelahotelcafe', 'https://www.instagram.com/casaestelahotelcafe', 1, 5, 1),
(6, 'fa fa-link', 'Casa Estella Drive', 'https://drive.google.com/drive/folders/16X2L2sQsh9kC_u62V0NeW_JBNw0HzdpX', 1, 99, 1);

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
(5, 'Christian', 'Realisan', 'chano@gmail.com', 'aaa', 'new', '2025-04-08 04:23:33', '2025-04-08 04:23:33'),
(6, 'Christian', 'Realisan', 'christianrealisan45@gmail.com', 'ooo', 'new', '2025-04-10 15:21:41', '2025-04-10 15:21:41'),
(7, 'Christian', 'Realisan', 'christianrealisan45@gmail.com', 'hi', 'new', '2025-04-11 09:32:59', '2025-04-11 09:32:59'),
(8, 'Anonymous', 'Ako', 'dyanlang@gmail.com', 'Nothing', 'new', '2025-04-15 05:34:52', '2025-04-15 05:34:52'),
(9, 'Christian', 'Realisan', 'chano@gmail.com', 'aaaaa', 'new', '2025-04-16 10:15:48', '2025-04-16 10:15:48'),
(17, 'Christian', 'Realisan', 'chano@gmail.com', '123', 'new', '2025-04-23 23:15:01', '2025-04-23 23:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_no` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_card_type_id` int(11) NOT NULL,
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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_vip` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `created_at`, `is_vip`) VALUES
(1, 'John Doe', 'john@email.com', '1234567890', '2025-04-05 12:25:39', 0),
(2, 'Jane Smith', 'jane@email.com', '2345678901', '2025-04-05 12:25:39', 0),
(3, 'Mike Johnson', 'mike@email.com', '3456789012', '2025-04-05 12:25:39', 0),
(4, 'Sarah Williams', 'sarah@email.com', '4567890123', '2025-04-05 12:25:39', 0),
(5, 'Robert Brown', 'robert@email.com', '5678901234', '2025-04-05 12:25:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `daily_occupancy`
--

CREATE TABLE `daily_occupancy` (
  `date` date NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `occupied_rooms` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_occupancy`
--

INSERT INTO `daily_occupancy` (`date`, `total_rooms`, `occupied_rooms`, `created_at`) VALUES
('2025-04-08', 4, 0, '2025-04-07 19:49:51'),
('2025-04-09', 4, 2, '2025-04-08 19:24:08'),
('2025-04-09', 1, 0, '2025-04-09 12:51:09'),
('2025-04-09', 1, 0, '2025-04-09 12:53:50'),
('2025-04-09', 2, 0, '2025-04-09 13:58:08'),
('2025-04-10', 2, 0, '2025-04-10 15:13:15'),
('2025-04-10', 3, 0, '2025-04-10 15:29:08');

-- --------------------------------------------------------

--
-- Table structure for table `daily_revenue`
--

CREATE TABLE `daily_revenue` (
  `date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_revenue`
--

INSERT INTO `daily_revenue` (`date`, `total_amount`, `booking_count`, `created_at`) VALUES
('2025-04-08', 117000.00, 4, '2025-04-07 19:49:51'),
('2025-04-09', 29250.00, 1, '2025-04-08 19:24:08'),
('2025-04-09', 500000.00, 1, '2025-04-09 12:51:09'),
('2025-04-09', 220000.00, 1, '2025-04-09 12:53:50'),
('2025-04-09', 236000.00, 2, '2025-04-09 13:58:08'),
('2025-04-10', 40000.00, 1, '2025-04-10 15:13:15'),
('2025-04-10', 80000.00, 2, '2025-04-10 15:29:08');

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
-- Table structure for table `disable_reasons`
--

CREATE TABLE `disable_reasons` (
  `id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disable_reasons`
--

INSERT INTO `disable_reasons` (`id`, `reason`, `is_active`, `created_at`) VALUES
(1, 'Service temporarily unavailable', 0, '2025-04-08 15:52:24'),
(2, 'System maintenance in progress', 0, '2025-04-08 15:52:24'),
(3, 'Technical issues with the service', 1, '2025-04-08 15:52:24'),
(4, 'Service provider connection error', 0, '2025-04-08 15:52:24'),
(5, 'Upgrading service features', 1, '2025-04-08 15:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `discount_types`
--

CREATE TABLE `discount_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_types`
--

INSERT INTO `discount_types` (`id`, `name`, `percentage`, `description`, `is_active`, `created_at`) VALUES
(1, 'senior', 10, 'Senior Citizen Discount', 1, '2025-04-13 02:58:18'),
(2, 'pwP', 10, 'Person with Disability Discount', 1, '2025-04-13 02:58:18'),
(3, 'student', 10, 'Student Discount', 1, '2025-04-13 02:58:18');

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
  `id` int(11) NOT NULL,
  `booking_refId` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `package_name` varchar(100) NOT NULL,
  `max_guest` int(11) DEFAULT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `overtime_hours` varchar(255) DEFAULT '0',
  `overtime_charge` decimal(10,2) DEFAULT 0.00,
  `extra_guests` int(11) DEFAULT 0,
  `extra_guest_charge` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `remaining_balance` decimal(10,2) DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `date_time_start` datetime NOT NULL,
  `date_time_end` datetime NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `reserve_type` varchar(50) DEFAULT 'Regular',
  `place` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `change_amount` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `booking_refId`, `user_id`, `package_id`, `customer_name`, `email`, `package_name`, `max_guest`, `package_price`, `overtime_hours`, `overtime_charge`, `extra_guests`, `extra_guest_charge`, `total_amount`, `paid_amount`, `remaining_balance`, `event_type`, `date_time_start`, `date_time_end`, `number_of_guests`, `payment_method`, `payment_type`, `booking_status`, `reserve_type`, `place`, `rejection_reason`, `change_amount`) VALUES
(1, 'EVT-20260209-69896D31563A4', 40, 9, 'christian realisan Christian Realisan realisan', NULL, 'Package F', NULL, 1000.00, '0', 0.00, 0, 0.00, 1000.00, 500.00, 500.00, 'birthday', '2026-02-09 13:01:00', '2026-02-09 17:01:00', 1, 'cash', 'down_payment', 'Ongoing', 'event', 'garden', NULL, NULL),
(2, 'EVT-20260211-698B5C2023FC8', 54, 9, 'christian realisan Christian Realisan christian realisan Christian Realisan', NULL, 'Package F', NULL, 1000.00, '0', 0.00, 0, 0.00, 1000.00, 500.00, 500.00, 'family', '2026-02-12 12:00:00', '2026-02-12 16:00:00', 2, 'cash', 'down_payment', 'Ongoing', 'event', 'garden', NULL, NULL),
(3, 'EVT-20260211-698B5C4EA5416', 54, 9, 'christian realisan Christian Realisan christian realisan Christian Realisan', NULL, 'Package F', NULL, 1000.00, '0', 0.00, 0, 0.00, 1000.00, 500.00, 500.00, 'family', '2026-02-12 12:00:00', '2026-02-12 16:00:00', 2, 'cash', 'down_payment', 'pending', 'event', 'garden', NULL, NULL),
(4, 'EVT-20260211-698BF6BB5BDBA', 54, 4, 'christian realisan Christian Realisan christian realisan Christian Realisan', NULL, 'Package C', NULL, 76800.00, '0', 0.00, 0, 0.00, 76800.00, 38400.00, 38400.00, 'corporate', '2026-02-11 11:14:00', '2026-02-11 15:14:00', 22, 'cash', 'down_payment', 'pending', 'event', 'garden', NULL, NULL),
(5, 'EVT-20260212-698D583C9742A', 55, 4, 'Fammela  De Guzman', NULL, 'Package C', NULL, 76800.00, '0', 0.00, 0, 0.00, 76800.00, 38400.00, 38400.00, 'family', '2026-02-14 10:30:00', '2026-02-14 14:30:00', 50, 'cash', 'down_payment', 'pending', 'event', 'garden', NULL, NULL);

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
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `place` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_packages`
--

INSERT INTO `event_packages` (`id`, `name`, `price`, `description`, `image_path`, `image_path2`, `image_path3`, `max_guests`, `duration`, `created_at`, `is_available`, `menu_items`, `max_pax`, `time_limit`, `notes`, `status`, `place`) VALUES
(1, 'Venue Rental Only', 20000.00, '5-hour venue rental\r\nTables and Tiffany chairs', '833746f73dfbbfeb5194b0b6.jpg', 'f47825776f7a7d130878f073.jpg', NULL, 30, 5, '2025-02-12 02:48:46', 0, '1 Appetizers', 50, '', '     HAHH', 'available', 'cafe'),
(2, 'Package A', 47500.00, '5-hour venue rental      Tables     and Tiffany chairs', 'b0b2e31222cf4f8c02d9c310.jpg', NULL, NULL, 30, 5, '2025-02-12 02:48:46', 0, '1 Appetizers,2 Pasta,2 Mains,Salad Bar,Rice,Drinks,2 Drinks', 30, '', '    Wala', 'available', 'cafe'),
(3, 'Package B', 55000.00, '5-hour venue rental\r\nTables and  Tiffany chairs', '417145ce8adce5e4a9e85f25.jpg', NULL, NULL, 40, 5, '2025-02-12 02:48:46', 0, '2 Appetizers,2 Pasta,3 Mains,Salad Bar,Rice,1 Dessert,Drinks', 40, '', '    **Assumes 5,000g (100g per person) of Wagyu steak will be served.', 'available', 'cafe'),
(4, 'Package C', 76800.00, '5-hour venue rental\r\nTables and Tiffany chairs', 'b1f5ed68d570fc613b082da2.jpg', NULL, NULL, 30, 5, '2025-02-12 02:48:46', 1, '3 Appetizers,2 Pasta,2 Mains,Wagyu Steak Station,Salad Bar,Rice,2desserts,Drinks', 50, '', ' ', 'available', 'garden'),
(9, 'Package F', 1000.00, 'Note', '8fb52dc2718d570082dfc257.jpg', NULL, NULL, 2, 1, '2025-12-20 10:11:26', 1, 'Salad,1 Appetizers', 2, '', ' Note', 'available', 'garden');

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
(13, 3, 'Restaurant', 'check', 3, 0, '2025-03-05 11:21:30', '2025-12-29 10:24:45'),
(14, 4, 'Private check-in/check-out', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(15, 4, 'Luggage storage', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(16, 4, '24-hour front desk', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(17, 5, 'English', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(18, 5, 'Filipino', 'check', 2, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(19, 6, 'Free Wi-Fi', 'check', 1, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(21, 7, 'Bidet', 'check', 2, 1, '2025-03-05 11:21:30', '2025-12-29 10:19:55'),
(22, 7, 'Slippers', 'check', 3, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(23, 7, 'Private bathroom', 'check', 4, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(24, 7, 'Toilet', 'check', 5, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(25, 7, 'Hairdryer', 'check', 6, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(26, 7, 'Shower', 'check', 7, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(27, 1, 'may tanod', 'check', 5, 1, '2025-04-09 11:13:27', '2025-04-09 11:13:27'),
(29, 4, 'Kiss', 'check', 4, 1, '2025-04-09 13:55:56', '2025-04-09 13:55:56'),
(30, 1, 'May bayad', 'check', 6, 1, '2025-04-21 08:50:34', '2025-04-21 08:50:34'),
(57, 2, 'Package F', 'check', 1, 0, '2025-12-29 10:04:42', '2025-12-29 10:04:42');

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
(1, 'Parking', 1, 0, '2025-03-05 11:21:30', '2025-12-29 11:25:15'),
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
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `featured_rooms`
--

INSERT INTO `featured_rooms` (`id`, `room_type_id`, `start_date`, `end_date`, `created_at`, `image_path`) VALUES
(0, 6, '2025-04-22', '2025-04-30', '2025-04-21 08:47:36', 'uploads/featured_rooms/featured_6806062833f4d.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','read','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `admin_response` text DEFAULT NULL,
  `resolve_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fix_booking_ids_log`
--

CREATE TABLE `fix_booking_ids_log` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fix_booking_ids_log`
--

INSERT INTO `fix_booking_ids_log` (`id`, `message`, `created_at`) VALUES
(1, 'Current booking_id column configuration: Array\n(\n    [Field] => booking_id\n    [Type] => int(11)\n    [Null] => NO\n    [Key] => \n    [Default] => \n    [Extra] => \n)\n', '2025-04-15 03:06:36'),
(2, 'Found 2 bookings with ID 0', '2025-04-15 03:06:36'),
(3, 'Next booking ID will be: 1', '2025-04-15 03:06:36'),
(4, 'Updated booking for Christian Realisan to ID: 1', '2025-04-15 03:06:36'),
(5, 'Updated booking for Christian Realisan to ID: 2', '2025-04-15 03:06:36'),
(6, 'Updated booking_id column to INT AUTO_INCREMENT PRIMARY KEY', '2025-04-15 03:06:36'),
(7, 'Set AUTO_INCREMENT value to 3', '2025-04-15 03:06:36');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
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
  `guest_type` varchar(255) NOT NULL DEFAULT 'Regular',
  `age` int(11) DEFAULT NULL,
  `image_proof` varchar(44) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `first_name`, `last_name`, `guest_type`, `age`, `image_proof`, `created_at`) VALUES
(1, 1, 'as', '12', 'regular', 34, NULL, '2026-02-20 16:20:03'),
(2, 2, 'as', '12', 'regular', 34, NULL, '2026-02-20 16:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_policies`
--

CREATE TABLE `hotel_policies` (
  `id` int(11) NOT NULL,
  `policy_type` varchar(50) NOT NULL,
  `policy_content` text NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_policies`
--

INSERT INTO `hotel_policies` (`id`, `policy_type`, `policy_content`, `last_updated`) VALUES
(1, '', '', '2025-04-11 21:40:11');

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
  `id_card_type_id` int(11) NOT NULL,
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
-- Table structure for table `location_info`
--

CREATE TABLE `location_info` (
  `id` int(11) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `map_zoom_level` int(11) DEFAULT 15,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_info`
--

INSERT INTO `location_info` (`id`, `address`, `latitude`, `longitude`, `map_zoom_level`, `contact_phone`, `contact_email`, `last_updated`) VALUES
(1, 'Casa Estela Boutique Hotel & Cafe, Calapan City, Oriental Mindoro', 13.41454500, 121.18380200, 15, '+63 XXX XXX XXXX', 'info@casaestela.com', '2025-04-11 21:31:45');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_settings`
--

CREATE TABLE `maintenance_settings` (
  `id` int(11) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `allowed_ips` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_settings`
--

INSERT INTO `maintenance_settings` (`id`, `is_enabled`, `start_time`, `end_time`, `message`, `allowed_ips`, `last_updated`) VALUES
(1, 0, '2025-04-13 08:05:38', '2025-04-13 08:10:38', 'Scheduled maintenance in progress. We will be back online at 6:00 PM EST.', '127.0.0.1,192.168.1.1', '2025-04-16 10:06:28');

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
(8, 'SMOOTHIE', 'SMOOTHIE'),
(18, 'COFFEE & LATTE', 'COFFEE & LATTE'),
(19, 'ALL DAY BREAKFAST', 'ALL DAY BREAKFAST'),
(20, 'PERFECT PLATTERS', 'PERFECT PLATTERS');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `price`, `image_path`, `description`, `availability`) VALUES
(1, 1, 'Hand-cut Potato Fries', 195.00, '5f3bfd0401f9a62f.jpg', 'Crispy fries with seasoning', 1),
(2, 1, 'Mozzarella Stick', 150.00, '3845216e60731b54.jpg', 'Fried mozzarella with marinara sauce', 1),
(3, 1, 'Siracha Buffalo Wings', 377.00, '6c71fbe6dc0478fa.jpg', 'Spicy chicken wings 6pcs', 1),
(4, 2, 'Mozzarella with Caramelized Walnuts and Apples', 344.00, 'd231c45641dbd5dd.jpg', 'Fresh garden salad with dressing', 1),
(5, 2, 'Chicken with Parmesan Shavings', 328.00, '8fa788798eca1d34.jpg', '', 1),
(6, 3, 'Carbonara', 327.00, 'e833c5e13846b37f.jpg', 'No Cream', 1),
(7, 4, 'Classic Cheeseburger', 370.00, '501ca9caad663e5b.jpg', '100% beef patty', 1),
(8, 3, 'Seafood Marinara', 334.00, '6ea67d2709ee8bd4.jpg', 'Seafood', 1),
(431, 6, 'Ube Ice Latte', 180.00, 'd4c99a9ba6da6dce.jpg', 'New in the Menu', 1),
(432, 3, 'Chicken Alfredo with Mushrooms', 328.00, '2433b28ba7211a65.jpg', 'Carbonara pasta with bacon and cheese', 1),
(433, 4, 'Philly Cheesesteak Panini', 307.00, 'menu_698f2ea6336fa7.35051650.jpg', 'Tenderloin steak strips, white onion , bell pepper , cheese slices, mayo , ciabatta bread', 1),
(434, 5, 'Espresso', 95.00, 'menu_698f2f2c07f656.68999045.jpg', '', 1),
(435, 5, 'Cappucino', 120.00, 'menu_698f2f607f8a41.81479806.jpg', '', 1),
(436, 19, 'Homemade Daing na Bangus', 312.00, 'menu_698f3011b24bc9.96237492.jpg', '', 1),
(437, 4, 'Katsu Sando', 294.00, 'menu_698f301c35e972.21014631.jpg', 'Pork Cutlet Sandwich', 1),
(438, 4, 'Crispy Chicken Siracha Sandwich', 323.00, 'menu_698f309fd9ada1.82785831.jpg', 'Spicy ', 1),
(439, 3, 'Alle Vongole', 321.00, 'menu_698f316bb25529.34631306.jpg', 'Mussels', 1),
(440, 3, 'Shrimp Aligio Olio', 348.00, 'menu_698f31f8a90514.80954000.jpg', 'Shrimp', 1),
(441, 20, 'Seafood Marinara & Chicken Pesto Panini', 338.00, 'menu_698f32a5124878.68798819.jpg', '', 1),
(442, 1, 'Fried Calamari', 242.00, 'menu_698f33e55683e1.48881763.jpg', '', 1),
(443, 2, 'Molo Soup', 158.00, 'menu_698f35119dfc17.29566440.jpg', 'Homemade shrimp and pork dumplings', 1),
(444, 6, 'Cookies & Cream', 195.00, 'menu_698f35ab415c39.07047552.jpg', '', 1),
(445, 6, 'Strawberry Milk', 165.00, 'menu_698f360ad525f4.92868473.jpg', '', 1),
(446, 8, 'Mango', 175.00, 'menu_698f36b5ee1984.36442832.jpg', '', 1),
(447, 8, 'Guyabano', 135.00, 'menu_698f37072a2971.14081935.jpg', '', 1);

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
(1, 3, 'HAtdog', 15.00),
(2, 3, 'cheese', 20.00),
(3, 3, 'Gravy', 20.00),
(4, 7, 'Extra beef patty', 160.00),
(5, 7, 'Extra cheese', 45.00);

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
(6, 3, 'Extra Ranchs', 30.00),
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
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender_type` enum('user','admin','system') NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `sender_type`, `read_status`, `created_at`, `status`) VALUES
(1, 5, 'open pa po kayo ?', 'user', 1, '2025-04-12 06:31:08', 'unread'),
(2, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-12 06:31:08', 'unread'),
(3, 5, 'book', 'user', 1, '2025-04-12 06:34:16', 'unread'),
(4, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-12 06:34:16', 'unread'),
(5, 5, 'book', 'user', 1, '2025-04-12 06:35:58', 'unread'),
(6, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-12 06:35:58', 'unread'),
(7, 33, 'open pa po kayo ?', 'user', 1, '2025-04-12 09:54:24', 'unread'),
(8, 33, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 09:54:24', 'unread'),
(9, 31, 'open pa po kayo ?', 'user', 1, '2025-04-12 10:11:04', 'unread'),
(10, 31, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 10:11:04', 'unread'),
(11, 1, 'ay hoy', 'user', 1, '2025-04-13 01:57:28', 'unread'),
(12, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-13 01:57:28', 'unread'),
(13, 29, 'Hello', 'user', 1, '2025-04-15 05:29:41', 'unread'),
(14, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-15 05:29:41', 'unread'),
(15, 29, 'book', 'user', 1, '2025-04-16 03:57:45', 'unread'),
(16, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-16 03:57:45', 'unread'),
(17, 1, 'open pa po kayo ?', 'user', 1, '2025-04-16 09:49:01', 'unread'),
(18, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-16 09:49:01', 'unread'),
(19, 1, 'open pa po kayo ?', 'user', 1, '2025-04-16 09:50:01', 'unread'),
(20, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-16 09:50:01', 'unread'),
(21, 1, 'open pa po kayo ?', 'user', 1, '2025-04-19 10:31:58', 'unread'),
(22, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-19 10:31:58', 'unread'),
(23, 1, 'book', 'user', 1, '2025-04-19 10:32:03', 'unread'),
(24, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-19 10:32:03', 'unread'),
(25, 14, 'Hi', 'user', 1, '2025-04-21 06:57:37', 'unread'),
(26, 14, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-21 06:57:37', 'unread'),
(27, 14, 'Hello', 'user', 1, '2025-04-21 06:57:45', 'unread'),
(28, 14, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-21 06:57:45', 'unread'),
(29, 38, 'book', 'user', 1, '2025-04-23 23:18:32', 'unread'),
(30, 38, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-23 23:18:32', 'unread'),
(41, 39, 'HEllo', 'user', 1, '2025-05-16 06:16:44', 'unread'),
(42, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 06:16:44', 'unread'),
(43, 39, 'hi', 'user', 1, '2025-05-16 06:43:24', 'unread'),
(44, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 06:43:24', 'unread'),
(45, 39, 'hi', 'user', 1, '2025-05-16 07:02:01', 'unread'),
(46, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:02', 'unread'),
(47, 39, 'hi', 'user', 1, '2025-05-16 07:02:14', 'unread'),
(48, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:14', 'unread'),
(49, 39, 'hi', 'user', 1, '2025-05-16 07:02:21', 'unread'),
(50, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:21', 'unread'),
(51, 39, 'hi', 'user', 1, '2025-05-16 07:02:24', 'unread'),
(52, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:24', 'unread'),
(53, 39, 'aa', 'user', 1, '2025-05-16 07:02:32', 'unread'),
(54, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:32', 'unread'),
(55, 39, 'aa', 'user', 1, '2025-05-16 07:24:08', 'unread'),
(56, 39, 'aa', 'user', 1, '2025-05-16 07:27:10', 'unread'),
(57, 39, 'aa', 'user', 1, '2025-05-16 07:38:09', 'unread'),
(58, 39, 'hello good evening', 'user', 1, '2025-05-16 08:03:07', 'unread'),
(59, 39, 'hello good evening', 'user', 1, '2025-05-16 08:03:10', 'unread'),
(60, 39, 'hello good evening', 'user', 1, '2025-05-16 08:05:02', 'unread'),
(61, 39, 'hello good evening', 'user', 1, '2025-05-16 08:05:05', 'unread'),
(62, 39, 'hello good evening', 'user', 1, '2025-05-16 08:06:41', 'unread'),
(63, 39, 'hello good evening', 'user', 1, '2025-05-16 08:09:16', 'unread'),
(64, 39, 'hello good evening', 'user', 1, '2025-05-16 08:43:26', 'unread'),
(65, 39, 'hello good evening', 'user', 1, '2025-05-16 08:43:41', 'unread'),
(66, 39, 'hello good evening', 'user', 1, '2025-05-16 10:25:27', 'unread'),
(67, 1, 'HEllo', 'user', 1, '2025-05-17 03:23:28', 'unread'),
(68, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-17 03:23:28', 'unread'),
(69, 1, 'hi', 'user', 1, '2025-05-21 04:17:18', 'unread'),
(70, 1, 'HEllo', 'user', 1, '2025-05-21 04:19:04', 'unread'),
(71, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-21 04:19:04', 'unread'),
(72, 39, 'as', 'user', 1, '2025-05-28 13:24:42', 'unread'),
(73, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 13:24:42', 'unread'),
(74, 39, 'ff', 'user', 1, '2025-05-28 13:24:57', 'unread'),
(75, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 13:24:57', 'unread'),
(76, 39, 'Montenegro', 'user', 1, '2025-05-28 13:28:09', 'unread'),
(77, 39, 'Abordo', 'user', 1, '2025-05-28 13:28:19', 'unread'),
(78, 39, 'asa', 'user', 1, '2025-05-28 17:46:24', 'unread'),
(79, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 17:46:24', 'unread'),
(80, 39, '77', 'user', 1, '2025-05-28 17:48:45', 'unread'),
(81, 39, 'h', 'user', 1, '2025-05-28 17:49:14', 'unread'),
(82, 39, 'l', 'user', 1, '2025-06-03 08:59:19', 'unread'),
(83, 5, 'yo', 'user', 1, '2025-11-06 05:29:20', 'unread'),
(84, 5, 'hello', 'user', 1, '2025-11-06 05:29:28', 'unread'),
(85, 5, 'snob yern', 'user', 1, '2025-11-06 05:29:44', 'unread'),
(86, 5, 'fdf', 'admin', 1, '2025-11-18 06:04:35', 'unread'),
(87, 5, 'dsd', 'admin', 1, '2025-12-30 04:18:09', 'unread'),
(88, 33, 'dsd', 'admin', 1, '2025-12-30 04:18:24', 'unread'),
(89, 45, 'Hello', 'user', 1, '2025-12-30 04:22:07', 'unread'),
(90, 45, 'Hi', 'admin', 1, '2025-12-30 04:22:15', 'unread'),
(91, 45, 'hello', 'user', 1, '2025-12-30 04:22:38', 'unread'),
(92, 45, 'hi', 'admin', 1, '2025-12-30 04:28:00', 'unread'),
(93, 45, 'hello', 'user', 1, '2025-12-30 04:28:08', 'unread'),
(94, 45, 'hello', 'user', 1, '2025-12-30 04:28:13', 'unread'),
(95, 45, '3', 'user', 1, '2025-12-30 04:28:27', 'unread'),
(96, 45, 'h', 'admin', 1, '2025-12-30 04:31:15', 'unread'),
(97, 45, 'h', 'user', 1, '2025-12-30 04:31:21', 'unread'),
(98, 45, 'h', 'user', 1, '2025-12-30 04:31:26', 'unread'),
(99, 45, 'd', 'user', 1, '2025-12-30 04:31:45', 'unread'),
(100, 45, 'd', 'user', 1, '2025-12-30 04:33:09', 'unread'),
(101, 45, 'ds', 'user', 1, '2025-12-30 04:33:12', 'unread'),
(102, 45, 'asd', 'user', 1, '2025-12-30 04:33:16', 'unread'),
(103, 45, 'asd', 'admin', 1, '2025-12-30 04:41:28', 'unread'),
(104, 45, 'ds', 'user', 1, '2025-12-30 04:41:36', 'unread'),
(105, 45, 'asd', 'user', 1, '2025-12-30 04:41:40', 'unread'),
(106, 45, 'asd', 'admin', 1, '2025-12-30 04:41:41', 'unread'),
(107, 45, 'asd', 'admin', 1, '2025-12-30 04:49:31', 'unread'),
(108, 45, 'asd', 'user', 1, '2025-12-30 04:49:52', 'unread'),
(109, 40, 'HEllo', 'user', 1, '2026-02-07 13:59:22', 'unread'),
(110, 40, 'aa', 'user', 1, '2026-02-07 13:59:31', 'unread'),
(111, 40, 'goo', 'user', 1, '2026-02-07 14:00:15', 'unread'),
(112, 40, 'hello good evening', 'user', 1, '2026-02-07 14:02:35', 'unread'),
(113, 40, 'asas', 'user', 1, '2026-02-09 07:26:23', 'unread'),
(114, 56, 'hello', 'user', 1, '2026-02-10 15:12:01', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_fk_id` int(11) DEFAULT NULL,
  `event_fk_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_processing` tinyint(1) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT NULL,
  `is_rejected` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `booking_fk_id`, `event_fk_id`, `order_id`, `title`, `message`, `type`, `is_read`, `created_at`, `is_processing`, `is_completed`, `is_rejected`) VALUES
(1, 40, NULL, NULL, 9, 'Order Completed', 'Your order #ORD-1770793549-2868 has been completed.', 'Order', 1, '2026-02-11 07:05:49', 0, 1, 0),
(2, 40, 5, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-QNDNJLQQNLY has been confirmed. Check-in date: 2026-02-11. Total amount: ₱1,000.00', 'Room Booking', 1, '2026-02-11 07:20:36', 1, NULL, NULL),
(3, 55, 6, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-DORQYEWWFXW has been confirmed. Check-in date: 2026-02-13. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-12 04:31:09', NULL, NULL, NULL),
(4, 55, NULL, 5, NULL, 'Event Booking Pending', 'Your booking for event \'Package C\' has been confirmed. Booking reference: EVT-20260212-698D583C9742A.', 'Event Booking', 0, '2026-02-12 04:34:07', NULL, NULL, NULL),
(5, 55, NULL, NULL, 12, 'Order Pending', 'Your order #ORD-1770871918-9814 has been paid successfully!', 'Order', 0, '2026-02-12 04:51:58', NULL, NULL, NULL),
(6, 57, 8, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-TXLNSICTRS has been confirmed. Check-in date: 2026-02-17. Total amount: ₱6,200.00', 'Room Booking', 0, '2026-02-17 07:14:22', NULL, NULL, NULL),
(7, 44, 13, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-7QORCFZA9A has been confirmed. Check-in date: 2026-02-22. Total amount: ₱3,400.00', 'Room Booking', 0, '2026-02-19 12:49:27', NULL, NULL, NULL),
(8, 58, 14, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-4NFD5UTPWDQ has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,000.00', 'Room Booking', 0, '2026-02-20 07:24:05', NULL, NULL, NULL),
(9, 58, 15, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-5QABKPPFEYC has been confirmed. Check-in date: 2026-02-20. Total amount: ₱5,000.00', 'Room Booking', 0, '2026-02-20 07:42:18', NULL, NULL, NULL),
(10, 58, 16, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-BIUSLDFS63A has been confirmed. Check-in date: 2026-02-20. Total amount: ₱5,000.00', 'Room Booking', 0, '2026-02-20 07:58:43', NULL, NULL, NULL),
(11, NULL, 17, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-MJUTZYORFT4 has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:11:19', NULL, NULL, NULL),
(12, NULL, 18, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-BMPEPDKLLYU has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:31:54', NULL, NULL, NULL),
(13, NULL, 19, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-TDO5OM6BAU has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:33:39', NULL, NULL, NULL),
(14, NULL, 20, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-HA6OPUY4NK4 has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:39:55', NULL, NULL, NULL),
(15, NULL, 21, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-W2XOUOJAB1Q has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:44:57', NULL, NULL, NULL),
(16, NULL, 22, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-06OBIL3D3EC has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 0, '2026-02-20 12:53:11', NULL, NULL, NULL),
(17, 54, 23, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-4K3AFI03K has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 1, '2026-02-20 12:57:11', NULL, NULL, NULL),
(18, 54, 24, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-U4XIR6XVRCW has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 1, '2026-02-20 13:09:47', NULL, NULL, NULL),
(19, 54, 1, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-OAINDCSTL8W has been confirmed. Check-in date: 2026-02-20. Total amount: ₱1,700.00', 'Room Booking', 1, '2026-02-20 13:11:27', NULL, NULL, NULL),
(20, 54, 2, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-0YTX91ZYHBU has been confirmed. Check-in date: 2026-02-20. Total amount: ₱5,000.00', 'Room Booking', 1, '2026-02-20 13:27:12', NULL, NULL, NULL),
(21, 54, 3, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-9HTCWFMYAC has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 14:15:44', NULL, NULL, NULL),
(22, 54, 4, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-JFS3TFWKRU has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 14:31:42', NULL, NULL, NULL),
(23, 54, 5, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-YPX4V3JW7QA has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 14:36:58', NULL, NULL, NULL),
(24, 54, 6, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-LORF0BKJYYO has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 14:59:40', NULL, NULL, NULL),
(25, 54, 7, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-JW3AAQJVUA has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:01:29', NULL, NULL, NULL),
(26, 54, 8, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-AK12N5QA8DC has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:05:14', NULL, NULL, NULL),
(27, 54, 9, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-F4WOQDGY3Z0 has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:08:28', NULL, NULL, NULL),
(28, 54, 10, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-4SEH5B2JJZ4 has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:12:44', NULL, NULL, NULL),
(29, 54, 11, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-HHQ6KSQPCOM has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:14:22', NULL, NULL, NULL),
(30, 54, 12, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-SBHPE27ZCG has been confirmed. Check-in date: 2026-02-20. Total amount: ₱4,500.00', 'Room Booking', 1, '2026-02-20 15:19:02', NULL, NULL, NULL),
(31, 54, 13, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-GYYBRIG3A has been confirmed. Check-in date: 2026-02-20. Total amount: ₱10,000.00', 'Room Booking', 0, '2026-02-20 16:03:21', NULL, NULL, NULL),
(32, 54, 14, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-J32PMINSQCO has been confirmed. Check-in date: 2026-02-20. Total amount: ₱5,000.00', 'Room Booking', 0, '2026-02-20 16:05:12', NULL, NULL, NULL),
(33, 54, 1, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-JAW5QLRQCG has been confirmed. Check-in date: 2026-02-20. Total amount: ₱28,600.00', 'Room Booking', 0, '2026-02-20 16:10:19', NULL, NULL, NULL),
(34, 54, 1, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-8LK5JV3KBXI has been confirmed. Check-in date: 2026-02-20. Total amount: ₱28,600.00', 'Room Booking', 0, '2026-02-20 16:20:03', NULL, NULL, NULL),
(35, 54, 2, NULL, NULL, 'Booking Confirmed', 'Your booking #BOOK-XNEDUENSE0C has been confirmed. Check-in date: 2026-02-22. Total amount: ₱28,600.00', 'Room Booking', 0, '2026-02-20 16:36:04', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `promo_type` varchar(255) DEFAULT NULL,
  `discount_start` date DEFAULT NULL,
  `discount_end` date DEFAULT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `image`, `price`, `discount`, `discounted_price`, `promo_type`, `discount_start`, `discount_end`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Weekend Getaway', 'offer_698f33c1751b85.60898150', 6000.00, 30.00, 4800.00, 'Table', '2026-03-01', '2026-06-30', 'Perfect weekend escape with breakfast included', 1, '2025-03-05 11:14:57', '2026-02-20 01:30:08'),
(2, 'Family', 'offer_6997ba7126fcb4.91974811.jpg', 0.00, NULL, NULL, 'Room', NULL, NULL, 'Special rate for family stays with complimentary activities', 1, '2025-03-05 11:14:57', '2026-02-20 01:35:45'),
(3, 'Events', 'offer_698f33c1751b85.60898150', 0.00, NULL, NULL, 'Events', NULL, NULL, 'Stay longer, save more with our weekly rates', 1, '2025-03-05 11:14:57', '2026-02-18 14:23:43'),
(5, 'Summer Special', 'offer_698f33c1751b85.60898150', 6000.00, 30.00, 4800.00, 'Room', '2026-03-01', '2026-06-30', 'Limited time summer promo', 1, '2026-02-16 14:57:08', '2026-02-20 01:30:21');

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
  `nickname` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `change_amount` int(11) NOT NULL,
  `extra_fee` int(11) NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_option` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `remaining_balance` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `final_total` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_type` varchar(255) DEFAULT 'none',
  `discount_amount` int(11) NOT NULL,
  `id_number` varchar(55) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `updated_at` date NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `notification_status` tinyint(1) DEFAULT 0,
  `cashier_id` int(11) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `type_of_order` varchar(50) DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `table_id`, `customer_name`, `contact_number`, `nickname`, `total_amount`, `amount_paid`, `change_amount`, `extra_fee`, `order_type`, `payment_method`, `payment_option`, `payment_status`, `remaining_balance`, `status`, `reject_reason`, `final_total`, `order_date`, `discount_type`, `discount_amount`, `id_number`, `completed_at`, `updated_at`, `cancellation_reason`, `cancelled_at`, `notification_status`, `cashier_id`, `table_name`, `type_of_order`, `processed_by`) VALUES
(1, 45, 0, 'Carlos  Bernales', 2147483647, '', 235.00, 118, 0, 0, 'regular', 'gcash', 'partial', 'paid', 117.50, 'Pending', NULL, 235, '2025-11-23 13:50:09', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'regular', NULL),
(2, 45, 0, 'Carlos  Bernales', 2147483647, '', 355.00, 178, 0, 0, 'advance', 'maya', 'partial', 'paid', 177.50, 'Pending', NULL, 355, '2025-11-23 13:51:52', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'advance', NULL),
(3, 45, 0, 'Carlos  Bernales', 2147483647, '', 235.00, 118, 0, 0, 'advance', 'gcash', 'partial', 'paid', 117.50, 'Pending', NULL, 235, '2025-11-23 14:08:10', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'advance', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders_table`
--

CREATE TABLE `orders_table` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `order_type` varchar(255) DEFAULT NULL,
  `discount_type` varchar(255) DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `total` double(10,2) DEFAULT NULL,
  `balance` double(10,2) DEFAULT NULL,
  `downpayment` double(10,2) DEFAULT NULL,
  `amount_paid` double(10,2) DEFAULT NULL,
  `change_amount` double(10,2) DEFAULT NULL,
  `remaining_balance` double(10,2) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_option` varchar(255) DEFAULT NULL,
  `dp_payment_method` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `type_of_order` varchar(255) DEFAULT NULL,
  `order_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders_table`
--

INSERT INTO `orders_table` (`id`, `user_id`, `order_id`, `firstname`, `lastname`, `contact`, `email`, `date_time`, `order_type`, `discount_type`, `discount_percentage`, `discount_amount`, `id_number`, `total`, `balance`, `downpayment`, `amount_paid`, `change_amount`, `remaining_balance`, `payment`, `payment_method`, `payment_option`, `dp_payment_method`, `status`, `reject_reason`, `type_of_order`, `order_at`) VALUES
(4, 40, 'ORD-1770613893-3784', 'christian realisan Christian Realisan', 'realisan', '09124343343', 'chanomabalo@gmail.com', '2026-02-09 12:59:00', 'advance', NULL, NULL, NULL, NULL, 180.00, 90.00, 90.00, 90.00, NULL, NULL, NULL, 'online', 'partial', NULL, 'pending', NULL, NULL, NULL),
(5, 40, 'ORD-1770618415-8992', 'christian realisan Christian Realisan', 'realisan', '09124343343', 'chanomabalo@gmail.com', '2026-02-09 14:25:00', 'advance', NULL, NULL, NULL, NULL, 1080.00, 540.00, 540.00, 540.00, NULL, NULL, NULL, 'online', 'partial', NULL, 'processing', NULL, NULL, NULL),
(8, 3, 'ORD-20260209-5630', NULL, NULL, NULL, NULL, '2026-02-09 06:46:21', 'Walkin', NULL, NULL, 0.00, NULL, 150.00, NULL, NULL, 500.00, 350.00, 10.00, NULL, 'cash', NULL, NULL, 'Completed', NULL, 'dine-in', '2026-02-09 06:46:21'),
(9, 40, 'ORD-1770793549-2868', NULL, NULL, NULL, NULL, '2026-02-11 07:05:49', 'Regular Order', NULL, NULL, NULL, NULL, 120.00, -20.00, 60.00, 140.00, 20.00, NULL, 'paid', 'Cash', 'downpayment', NULL, 'Completed', NULL, NULL, NULL),
(10, 55, 'TB-1770870706-7524', 'Fammela ', 'De Guzman ', '09362846372', 'allysonmildred696@gmail.com', '2026-02-12 12:31:00', 'Table Booking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(11, 55, 'ORD-1770870935-4196', '', '', '09362846372', 'allysonmildred696@gmail.com', '2026-02-14 12:30:00', 'advance', NULL, NULL, 0.00, NULL, 1212.00, -8.00, 0.00, 1220.00, 8.00, NULL, NULL, 'Cash', 'full', NULL, 'Completed', NULL, 'Dine-in', NULL),
(12, 55, 'ORD-1770871918-9814', NULL, NULL, NULL, NULL, '2026-02-12 12:51:58', 'Regular Order', NULL, NULL, NULL, NULL, 720.00, 0.00, 0.00, NULL, NULL, NULL, 'paid', 'gcash', 'downpayment', NULL, 'pending', NULL, NULL, NULL),
(13, NULL, 'ORD94296321889', 'FAMMELA', 'DE GUZMAN', '097826516731', 'fammeladeguzman21@gmail.com', '2026-02-14 10:00:00', NULL, NULL, NULL, NULL, NULL, 820.00, NULL, 410.00, NULL, NULL, 410.00, NULL, NULL, NULL, 'cash', 'Accepted', NULL, NULL, NULL),
(16, 40, 'TB-1770908100-8044', 'christian realisan Christian Realisan', 'realisan', '09124343343', 'chanomabalo@gmail.com', '2026-02-20 22:44:00', 'Table Booking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Rejected', 'as', NULL, NULL),
(17, 3, 'ORD-20260217-9931', NULL, NULL, NULL, NULL, '2026-02-17 13:37:55', 'Walkin', 'pwP', 10, 157.00, 'PWD4567890098765', 1413.00, NULL, NULL, 1500.00, 87.00, NULL, NULL, 'cash', NULL, NULL, 'Completed', NULL, 'dine-in', '2026-02-17 13:37:55'),
(18, 3, 'ORD-20260217-8453', NULL, NULL, NULL, NULL, '2026-02-17 13:40:30', 'Walkin', NULL, NULL, 0.00, '', 1796.00, NULL, NULL, 2000.00, 204.00, NULL, NULL, 'cash', NULL, NULL, 'processing', NULL, 'dine-in', '2026-02-17 13:40:30'),
(19, 3, 'ORD-20260217-6584', NULL, NULL, NULL, NULL, '2026-02-17 13:58:49', 'Walkin', NULL, NULL, 0.00, '', 1336.00, NULL, NULL, 1500.00, 164.00, NULL, NULL, 'cash', NULL, NULL, 'processing', NULL, 'dine-in', '2026-02-17 13:58:49'),
(20, 40, 'TB-1771550127-1317', 'christian realisan Christian Realisan', 'realisan', '09124343343', 'chanomabalo@gmail.com', '2026-02-20 09:14:00', 'Table Booking', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders_table_type`
--

CREATE TABLE `orders_table_type` (
  `id` int(11) NOT NULL,
  `table_booking_fk_id` int(11) NOT NULL,
  `table_type_fk_id` int(11) NOT NULL,
  `table_number_fk_id` int(11) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `table_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders_table_type`
--

INSERT INTO `orders_table_type` (`id`, `table_booking_fk_id`, `table_type_fk_id`, `table_number_fk_id`, `table_name`, `table_number`) VALUES
(1, 4, 4, 8, 'DAD', 8),
(2, 5, 2, 3, 'dasd', 3),
(3, 8, 2, 12, 'dasd', 11),
(4, 10, 4, 8, 'DAD', 8),
(5, 11, 4, 8, 'DAD', 8),
(6, 13, 2, 3, 'dasd', 3),
(7, 16, 4, 8, 'DAD', 8),
(8, 16, 2, 3, 'dasd', 3),
(9, 17, 4, 8, 'Couple', 8),
(10, 18, 2, 1, 'Friends', 1),
(11, 19, 4, 8, 'Couple', 8),
(12, 20, 4, 8, 'Couple', 8);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_fk_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_fk_id`, `item_name`, `quantity`, `unit_price`) VALUES
(4, 4, 'Matcha', 1, 180.00),
(5, 5, 'Matcha', 6, 180.00),
(12, 8, 'Mozzarella Stick', 1, 150.00),
(13, 9, 'Carbonara', 1, 120.00),
(17, 12, 'Carbonara', 1, 120.00),
(18, 12, 'Chicken Wings', 1, 180.00),
(19, 12, 'Mozzarella Stick', 1, 150.00),
(20, 12, 'Spaghetti maccaroni', 1, 270.00),
(21, 13, 'Mozzarella Stick', 1, 150.00),
(22, 13, 'Chicken Wings', 1, 180.00),
(23, 13, 'Spaghetti maccaroni', 1, 270.00),
(24, 13, 'Salad', 1, 200.00),
(25, 11, 'Matcha', 1, 180.00),
(26, 11, 'Spaghetti', 1, 300.00),
(27, 11, 'Carbonara', 1, 120.00),
(28, 11, 'Homemade Daing na Bangus', 1, 312.00),
(29, 11, 'Strawberry Milk', 1, 165.00),
(30, 11, 'Guyabano', 1, 135.00),
(31, 17, 'Fried Calamari', 1, 242.00),
(32, 17, 'Hand-cut Potato Fries', 1, 195.00),
(33, 17, 'Mozzarella Stick', 1, 150.00),
(34, 17, 'Chicken with Parmesan Shavings', 1, 328.00),
(35, 17, 'Alle Vongole', 1, 321.00),
(36, 17, 'Seafood Marinara', 1, 334.00),
(37, 18, 'Fried Calamari', 1, 242.00),
(38, 18, 'Hand-cut Potato Fries', 1, 195.00),
(39, 18, 'Molo Soup', 1, 158.00),
(40, 18, 'Mozzarella with Caramelized Walnuts and Apples', 1, 344.00),
(41, 18, 'Carbonara', 1, 327.00),
(42, 18, 'Classic Cheeseburger', 1, 370.00),
(43, 19, 'Mozzarella Stick', 1, 150.00),
(44, 19, 'Hand-cut Potato Fries', 1, 195.00),
(45, 19, 'Alle Vongole', 1, 321.00),
(46, 19, 'Classic Cheeseburger', 1, 370.00),
(47, 19, 'Espresso', 1, 95.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL,
  `order_item_fk_id` int(11) NOT NULL,
  `addon_name` varchar(100) NOT NULL,
  `price` double(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_addons`
--

INSERT INTO `order_item_addons` (`id`, `order_item_fk_id`, `addon_name`, `price`, `quantity`) VALUES
(1, 22, 'cheese', 20.00, 1),
(2, 42, 'Extra beef patty \n                                                                            ( (', 160.00, 1),
(3, 46, 'Extra beef patty \n                                                                            ( (', 160.00, 1),
(4, 46, 'Extra cheese \n                                                                            ( (', 45.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_durations`
--

CREATE TABLE `package_durations` (
  `id` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_max_guests`
--

CREATE TABLE `package_max_guests` (
  `id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_menu_items`
--

CREATE TABLE `package_menu_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_notes`
--

CREATE TABLE `package_notes` (
  `id` int(11) NOT NULL,
  `note_type` enum('30PAX','50PAX') NOT NULL,
  `note_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_reference` varchar(255) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_reference`, `booking_id`, `amount`, `payment_method`, `reference_number`, `payment_date`, `proof_file`) VALUES
(8, 'ROOM-27', 27, 113500.00, 'gcash', '1111111111111111111', '2025-05-09 10:30:10', '681d2262b2063.jpeg'),
(9, 'EVENT-11', 11, 30750.00, 'gcash', '555555555555', '2025-05-09 10:53:29', '681d27d964890.jpg'),
(10, 'EVENT-12', 12, 23750.00, 'gcash', '555555555555', '2025-05-09 11:11:54', '681d2c2a5e173.jpg'),
(11, 'ROOM-28', 28, 40100.00, 'gcash', '1111111111111111111', '2025-05-09 11:42:59', '681d3373996ba.jpg'),
(12, 'ROOM-4', 4, 0.00, 'gcash', '1111111111111111111', '2025-05-12 01:17:49', '6820956d8b6c9.jpg'),
(13, 'ROOM-5', 5, 1700.00, 'maya', '1111111111111111111', '2025-05-12 01:18:14', '682095861e09e.jpg'),
(14, 'ROOM-34', 34, 99300.00, 'gcash', '1111111111111111111', '2025-05-15 05:18:39', '6824c25f985ea.jpg'),
(15, 'ROOM-2', 2, 4600.00, 'gcash', '1111111111111111111', '2025-05-18 23:31:52', '6829fd68c4519.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `display_name`, `is_active`) VALUES
(1, 'gcash', 'GCash', 1),
(2, 'maya', 'Maya', 1);

-- --------------------------------------------------------

--
-- Table structure for table `promo_bookings`
--

CREATE TABLE `promo_bookings` (
  `id` int(11) NOT NULL,
  `booking_ref` varchar(20) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_firstname` varchar(100) NOT NULL,
  `guest_lastname` varchar(100) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_guests` int(11) NOT NULL DEFAULT 1,
  `room_type` varchar(100) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'xendit',
  `payment_option` enum('full','downpayment') NOT NULL DEFAULT 'full',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `booking_status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `promo_title` varchar(200) DEFAULT NULL,
  `promo_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_bookings`
--

INSERT INTO `promo_bookings` (`id`, `booking_ref`, `invoice_id`, `user_id`, `guest_firstname`, `guest_lastname`, `guest_email`, `guest_phone`, `check_in_date`, `check_out_date`, `number_of_guests`, `room_type`, `special_requests`, `payment_method`, `payment_option`, `amount_paid`, `total_amount`, `remaining_balance`, `payment_status`, `booking_status`, `promo_title`, `promo_price`, `created_at`, `updated_at`) VALUES
(1, 'PROMO-699487CA4D5A5', 'ROOM_69948700d305d_1771341568', NULL, 'Guest', 'User', 'guest@example.com', '', '2026-02-18', '2026-02-19', 1, 'Standard Room', '', 'Xendit', '', 6000.00, 6000.00, 0.00, 'paid', 'confirmed', 'Summer Special', 6000.00, '2026-02-17 23:22:50', NULL),
(2, 'PROMO-69948B1208797', 'ROOM_69948ae0e216a_1771342560', NULL, 'Guest', 'User', 'guest@example.com', '', '2026-02-18', '2026-02-19', 1, 'Standard Room', '', 'Xendit', '', 6000.00, 6000.00, 0.00, 'paid', 'confirmed', 'Summer Special', 6000.00, '2026-02-17 23:36:50', NULL),
(3, 'PROMO-69948C6958516', 'ROOM_69948c57cc139_1771342935', NULL, 'dadd', 'adr54', 'S@gmail.com', '', '2026-02-17', '2026-02-20', 2, '8', '', 'Xendit', 'downpayment', 9000.00, 18000.00, 9000.00, 'paid', 'confirmed', 'Summer Special', 0.00, '2026-02-17 23:42:33', NULL),
(4, 'PROMO-699560C79B8E2', 'ROOM_699560ab11a00_1771397291', NULL, 'dadd', 'adr54', 'S@gmail.com', '', '2026-02-18', '2026-02-21', 3, '3', 'sdad', 'Xendit', 'downpayment', 7200.00, 14400.00, 7200.00, 'paid', 'confirmed', 'Summer Special', 0.00, '2026-02-18 14:48:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_bookings`
--

CREATE TABLE `reschedule_bookings` (
  `id` int(11) NOT NULL,
  `booking_fk_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `date_resched` datetime NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reschedule_bookings`
--

INSERT INTO `reschedule_bookings` (`id`, `booking_fk_id`, `check_in`, `check_out`, `date_resched`, `reason`) VALUES
(1, 6, '2026-02-12', '2026-02-14', '2026-02-12 12:38:23', 'Early Arrival'),
(2, 5, '2026-02-11', '2026-02-12', '2026-02-17 15:45:41', ''),
(3, 5, '2026-02-28', '2026-03-01', '2026-02-17 15:46:35', 'HINDI SUMIPOT');

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
-- Table structure for table `resetpass`
--

CREATE TABLE `resetpass` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_token` varchar(64) NOT NULL,
  `reset_token_expires` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` tinyint(1) DEFAULT 0
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
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `room_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `room_number_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `floor_number` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`room_number_id`, `room_type_id`, `room_number`, `floor_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '1', 2, 'active', '2025-11-28 05:51:28', '2026-02-04 04:27:15'),
(2, 4, '2', 1, 'active', '2025-11-28 05:54:25', '2026-02-04 04:27:35'),
(3, 2, '3', 2, 'inactive', '2025-11-28 05:54:34', '2026-02-04 04:27:58'),
(4, 4, '4', 2, 'active', '2025-11-28 05:55:02', '2026-02-04 04:27:32'),
(5, 2, '101', 1, 'active', '2025-12-01 23:15:15', '2025-12-01 23:15:15'),
(6, 2, '103', 2, 'active', '2025-12-01 23:15:38', '2025-12-01 23:15:38'),
(8, 3, '203', 3, 'active', '2025-12-01 23:17:51', '2025-12-01 23:17:51'),
(9, 3, '303', 3, 'active', '2025-12-01 23:18:24', '2025-12-01 23:24:12'),
(10, 4, '302', 3, 'active', '2025-12-01 23:20:05', '2025-12-01 23:20:48');

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
(17, 3, 8, 5.0, 'aaa', '2025-04-08 03:31:00'),
(18, 2, 1, 5.0, 'okay', '2025-04-11 11:02:32'),
(19, 8, 5, 5.0, 'Good morning', '2025-04-12 10:43:17'),
(20, 9, 34, 5.0, 'Ang ganda ng room ', '2025-04-12 11:32:28'),
(21, 9, 31, 3.0, 'not goods', '2025-04-12 11:42:08'),
(22, 10, 31, 5.0, 'e4e', '2025-04-12 11:42:53'),
(23, 8, 31, 4.0, 'Nice!', '2025-04-12 11:47:28'),
(0, 9, 29, 5.0, 'Idol', '2025-04-15 13:15:44'),
(0, 11, 1, 1.0, 'Wow\r\n', '2025-04-21 06:54:52'),
(0, 8, 32, 5.0, 'Okay\r\n', '2025-04-21 08:55:33'),
(0, 9, 38, 5.0, 'j', '2025-05-13 12:28:02'),
(0, 11, 38, 5.0, 'j', '2025-05-13 12:28:17'),
(0, 8, 39, 4.0, 'a', '2025-06-02 03:03:48'),
(0, 11, 39, 5.0, 'a', '2025-06-03 13:34:05'),
(0, 9, 39, 4.0, 'u', '2025-06-06 00:43:23'),
(0, 3, 40, 4.0, 'asass', '2026-02-09 01:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `room_transfers`
--

CREATE TABLE `room_transfers` (
  `id` int(11) NOT NULL,
  `booked_room_fk_id` int(11) NOT NULL,
  `bookings_fk_id` int(11) NOT NULL,
  `room_number_fk_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_type_name` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL,
  `transfer_date` datetime DEFAULT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_transfers`
--

INSERT INTO `room_transfers` (`id`, `booked_room_fk_id`, `bookings_fk_id`, `room_number_fk_id`, `room_type_id`, `room_type_name`, `price`, `transfer_date`, `reason`) VALUES
(1, 9, 6, 8, 3, 'Triple Occupancy', 1700.00, '2026-02-12 12:39:39', 'aircon is not working'),
(2, 13, 9, 9, 3, 'Triple Occupancy', 1700.00, '2026-02-19 19:43:02', 'Too Small'),
(3, 14, 10, 4, 4, 'Family', 4500.00, '2026-02-19 20:10:43', 'gusto ko lang'),
(4, 15, 11, 5, 2, 'Double Occupancy', 1000.00, '2026-02-19 20:26:23', 'asdfghj'),
(5, 16, 12, 10, 4, 'Family', 4500.00, '2026-02-19 20:33:19', 'l');

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
  `rating_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`, `image2`, `image3`, `discount_percent`, `discount_valid_until`, `rating_count`, `status`) VALUES
(2, 'Double Occupancy', 5000.00, 2, 'A room designed for two guests. It may have one double bed or two single beds. Ideal for couples or two people sharing.', '2', 0.0, 'room_698f25cdaf4077.00273944.jpg', '', '', 0, NULL, 0, 'active'),
(3, 'Triple Occupancy', 1700.00, 3, 'A room designed for three guests. It may have one double bed and one single bed, or three single beds. Suitable for small groups or friends.', '3', 0.0, 'room_698f264c0a0bf4.82237096.jpg', '', '', 0, NULL, 0, 'active'),
(4, 'Family', 4500.00, 4, 'A larger room designed for families. It can accommodate parents and children comfortably, with multiple beds or extra space. Perfect for a family stay.', '2', 0.0, 'room_698f263fd7bec1.81792769.jpg', NULL, '', 0, NULL, 0, 'active');

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
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(9, 1),
(9, 2),
(9, 4),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5);

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
(1, 'Christmas Snow', '2025-04-01', '2025-04-30', 'snow', 1, '2025-04-07 13:54:09'),
(2, 'Valentine Hearts', '2025-04-01', '2025-04-30', 'hearts', 0, '2025-04-07 13:54:09'),
(3, 'New Year Fireworks', '2025-04-01', '2025-04-30', 'fireworks', 0, '2025-04-07 13:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_bookings`
--

CREATE TABLE `service_bookings` (
  `service_booking_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id` int(11) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `shift_timing` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `shift`, `shift_timing`) VALUES
(1, 'Morning', '5:00 AM - 10:00 AM'),
(2, 'Day', '10:00 AM - 4:00PM'),
(3, 'Evening', '4:00 PM - 10:00 PM'),
(4, 'Night', '10:00PM - 5:00AM');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `emp_name` varchar(100) NOT NULL,
  `staff_type_fk_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
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

INSERT INTO `staff` (`id`, `emp_name`, `staff_type_fk_id`, `shift_id`, `id_card_no`, `address`, `contact_no`, `salary`, `joining_date`, `updated_at`) VALUES
(1, 'Alfred Aceveda', 2, 1, '422510099122', '4516 Spruce Drive', 3479454777, 21000, '2020-11-13 05:39:06', '2026-01-16 09:01:09'),
(2, 'Aizzy', 7, 1, '422510099122', '2555 Hillside Drive', 1479994500, 12500, '2021-04-07 20:21:00', '2026-01-16 08:58:01'),
(3, 'Fammela De Guzman', 2, 3, '422510099122', 'Ap #897-1459 Quam Avenue', 976543111, 25000, '2019-11-13 05:40:18', '2021-04-08 17:36:27'),
(5, 'adsad', 1, 1, '3213121', 'dasdas', 1479994500, 2341433, '2026-01-16 09:04:52', '2026-01-16 09:04:52'),
(6, '2323', 1, 1, '422510099122', '2555 Hillside Drive', 1479994500, 232, '2026-01-16 09:05:26', '2026-01-16 09:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `staff_type`
--

CREATE TABLE `staff_type` (
  `id` int(11) NOT NULL,
  `staff_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff_type`
--

INSERT INTO `staff_type` (`id`, `staff_type`) VALUES
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
  `contact_number` varchar(20) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `num_guests` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'Pending',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `reservation_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `table_cancellations`
--

INSERT INTO `table_cancellations` (`id`, `booking_id`, `user_id`, `reason`, `cancelled_at`) VALUES
(1, 28, 5, 'found_better_option', '2025-04-12 17:32:48');

-- --------------------------------------------------------

--
-- Table structure for table `table_number`
--

CREATE TABLE `table_number` (
  `id` int(11) NOT NULL,
  `table_type_fk_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_number`
--

INSERT INTO `table_number` (`id`, `table_type_fk_id`, `table_number`, `status`) VALUES
(1, 2, 1, 'available'),
(2, 3, 2, 'available'),
(3, 2, 3, 'available'),
(4, 0, 4, 'available'),
(5, 3, 5, 'available'),
(6, 0, 6, 'available'),
(7, 0, 7, 'available'),
(8, 4, 8, 'available'),
(9, 0, 9, 'available'),
(10, 0, 10, 'available'),
(12, 2, 11, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `table_packages`
--

CREATE TABLE `table_packages` (
  `id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
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
  `status` enum('active','inactive') DEFAULT 'active',
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_packages`
--

INSERT INTO `table_packages` (`id`, `package_name`, `price`, `capacity`, `description`, `menu_items`, `available_tables`, `image_path`, `image1`, `image2`, `image3`, `image4`, `image5`, `status`, `reason`) VALUES
(2, 'Friends', 0, 10, 'Ideal for small groups', '', 0, 'uploads/table_packages/67fb0cd104e49.webp', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(3, 'Family', 0, 10, 'Great for family gatherings', '', 0, 'uploads/table_packages/67fb10c391a74.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(7, 'Package A', 20000, 30, 'Basic package for large groups', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks', 2, 'uploads/table_packages/67fb10116989f.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(8, 'Pacakge B', 33000, 30, 'Premium package with extra services', 'Appetizer, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 2, 'uploads/table_packages/67fb10ded4762.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(9, 'Package C', 45000, 30, 'All-inclusive luxury package', '3 Appetizer, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2 Desserts, Drinks', 1, 'uploads/table_packages/67fb120e4bfc4.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL);

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
-- Table structure for table `table_types`
--

CREATE TABLE `table_types` (
  `id` int(11) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `img1` text DEFAULT NULL,
  `img2` text DEFAULT NULL,
  `img3` text DEFAULT NULL,
  `img4` text DEFAULT NULL,
  `img5` text DEFAULT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `table_types`
--

INSERT INTO `table_types` (`id`, `table_name`, `capacity`, `img1`, `img2`, `img3`, `img4`, `img5`, `description`, `status`, `reason`) VALUES
(2, 'Friends', 5, 'acf61bb00b779e39.jpg', 'a48a4f4a38e7ebf0.PNG', '', '', '', 'Table for 3-5 person', 'active', NULL),
(3, 'Family', 10, '0fee7c3de01b10a9.jpg', '7904ce70b71572a6.png', '', '', '', 'Up to 10 pax', 'active', NULL),
(4, 'Couple', 2, 'c50b1b278841f889.jpg', '1572383940c02a1d.jpg', 'd4de153e1a812497.png', '', '', 'Table for 2 person', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `terms_and_conditions`
--

CREATE TABLE `terms_and_conditions` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `rule_text` text NOT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms_and_conditions`
--

INSERT INTO `terms_and_conditions` (`id`, `hotel_name`, `title`, `rule_text`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Casa Estela Boutique Hotel & Cafe', 'Hotel Day & Check-out', 'A hotel day starts at any time after 1:00 PM during the day of arrival and ends at 11:00 AM of the following day. Late check-outs shall be charged at Php 200.00 per hour for a maximum of two hours extension only. Exceeding beyond 2:00 PM will be considered as a one-night stay and will be charged automatically.', 1, 1, '2026-01-13 11:39:25', '2026-02-07 04:37:50'),
(2, 'Casa Estela Boutique Hotel & Cafe', 'Breakfast Schedule', 'Breakfast will be served between 7:00 AM and 8:00 AM only.', 2, 1, '2026-01-13 11:39:25', '2026-02-03 05:57:15'),
(3, 'Casa Estela Boutique Hotel & Cafe', 'Security Deposit', 'A refundable deposit of Php 500.00 is required upon issuance of the room key and card.', 3, 1, '2026-01-13 11:39:25', '2026-02-03 05:53:27'),
(4, 'Casa Estela Boutique Hotel & Cafe', 'Extra Amenities', 'Request for an extra pillow, duvet, bath towel or bath mat will incur an additional charge.', 4, 1, '2026-01-13 11:39:25', '2026-02-03 05:58:26'),
(5, 'Casa Estela Boutique Hotel & Cafe', 'Room Key Policy', 'Kindly deposit your room key and card at the front desk whenever you leave the hotel premises.', 5, 1, '2026-01-13 11:39:25', '2026-02-03 05:58:27'),
(6, 'Casa Estela Boutique Hotel & Cafe', 'Energy Saving', 'Kindly turn-off lights, water supply fixtures and electrical appliances before leaving the room and when not in use.', 6, 1, '2026-01-13 11:39:25', '2026-02-03 05:59:35'),
(7, 'Casa Estela Boutique Hotel & Cafe', 'Smoking Policy', 'Smoking inside the room is strictly prohibited. If found, a penalty of Php 500.00 will be imposed.', 7, 1, '2026-01-13 11:39:25', '2026-02-03 05:59:36'),
(8, 'Casa Estela Boutique Hotel & Cafe', 'Loss & Valuables', 'The management will not be held liable in any case of loss in personal property. A security box is available at the front desk where you can deposit your valuables. However, the management has the right to refuse to store money and other belongings if they pose a threat to safety, exceed the hotel standard value, or take up too much space.', 8, 1, '2026-01-13 11:39:25', '2026-02-03 05:59:40'),
(9, 'Casa Estela Boutique Hotel & Cafe', 'Property Damage', 'Damage to any hotel equipment, fixtures, or property shall result in corresponding financial charges.', 9, 1, '2026-01-13 11:39:25', '2026-02-03 05:59:38'),
(10, 'Casa Estela Boutique Hotel & Cafe', 'Lost & Found', 'Left and unclaimed items will be kept for a period of one (1) month from your departure date, unless otherwise instructed.', 10, 1, '2026-01-13 11:39:25', '2026-02-03 05:58:29'),
(11, 'Casa Estela Boutique Hotel & Cafe', 'Discount Policy', 'PWD and Senior Citizen discounts are applicable to cash payments only.', 11, 1, '2026-01-13 11:39:25', '2026-01-13 11:39:25'),
(12, 'Casa Estela Boutique Hotel & Cafe', 'Closing', 'Thank you for your cooperation.', 12, 1, '2026-01-13 11:39:25', '2026-01-13 11:39:25');

-- --------------------------------------------------------

--
-- Table structure for table `userss`
--

CREATE TABLE `userss` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `actual_password` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `user_type` enum('customer','admin','frontdesk','cashier') NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userss`
--

INSERT INTO `userss` (`id`, `first_name`, `last_name`, `email`, `contact_number`, `address`, `password`, `actual_password`, `profile_photo`, `user_type`, `name`, `verification_code`, `verification_expiry`, `is_verified`, `reset_token`, `reset_token_expires`) VALUES
(1, 'ADMIN', '', 'admin@example.com', '09362715617', 'Balite Calapan City Oriental Mindoro', '$2y$10$E0VVQ000TFvq6inV57SpT.FhiqJ4e5khughM2uepIKZSmjcdoOSQ2', NULL, 'user_6997b96b1ca7f5.20563243.jpg', 'admin', NULL, NULL, NULL, 0, 'd3ab8c68d4af772a776130cb755d22611faafa41a9bc56af2b46aae42d220527', '2025-04-24 13:48:57'),
(2, 'FRONTDESK', '', 'frontdesk@example.com', '9951776920', 'asdasd', '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', NULL, NULL, 'frontdesk', NULL, NULL, NULL, 1, NULL, NULL),
(3, 'Cashier', 'Realisan', 'cashier@example.com', '09123456789', 'TAwagan', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', NULL, 'uploads/profile/3.jpg', 'cashier', NULL, NULL, NULL, 1, '225874', '2025-05-31 21:24:35'),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$gKEFFH5imhdg01DgIoral.LnzHIAn5YMyhEhVonKrNcdRUGzL8efS', NULL, 'profile_690c175b3465a9.35460400.png', 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(7, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '912345678787', 'wawa calapan city', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(8, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '912345678787', 'tawagan', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', 'chanopassword', '681607ca1054f.jpg', 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(10, 'Fammela', 'De Guzman', 'fammeladeguzman21@gmail.com', '09362715617', 'Wawa, Calapan City', '$2y$10$SJOaFMSWJd7i98eTtHeBSe1uq/CZbJARFYZ/fGblsYFOFR9j.rYm2', 'fammelapassword', '../uploads/profile/profile_10_1746278895.png', 'cashier', NULL, NULL, NULL, 0, NULL, NULL),
(12, 'Alfred', 'Aceveda', 'cyvieshi@gmail.com', '09363950698', NULL, '$2y$10$HOX.EaHIlJlphxRlYhjs0OVQpDM.QdgAt.rCH7XaDc2zvZJE28d.m', NULL, NULL, '', NULL, '891266', '2025-04-14 09:51:28', 0, NULL, NULL),
(15, 'Try ', 'Me', 'akop35310@gmail.com', '09123456789', NULL, '$2y$10$4ydNXyV33pq4RswCLlZSpO5kGwhX/FiYzG/1KlkkQb3OCJ82x3XB6', NULL, NULL, 'customer', NULL, '145658', '2025-04-14 10:05:54', 0, NULL, NULL),
(16, 'Christian', 'Realisan', 'myraluceno@gmail.com', '09234567878', NULL, '$2y$10$8Xe899PsTnwiHbdEjVPJJuYJWP1bO8oK81pWIQcHo.xya7glCf4Z6', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(17, 'Christian', 'Realisan', 'enhymwaa@gmail.com', '09234567878', NULL, '$2y$10$HAPeuFiTcMK48yQ/ZHbRrORn8xBMrLUfwBd2wc8B6H.JvVAe16OKq', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(18, 'Fammela ', 'De Guzman ', 'mystery.woman1242@gmail.com', '09951779220', NULL, '$2y$10$fbPxCZiKhXdP9nPYQYN7jeBDGFVowdvBZ6LBTS9IWbSLm4Eda5VZ2', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(30, 'Lab', 'Mo', 'christianrealisan25@gmail.com', '09123456799', NULL, '$2y$10$dLY3pYMuNR.NjqljNfbnoe3UEBUviSwMhstRFPmaaAad6l3vkk1wW', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(31, 'Lab', 'Mo', 'christianrealisan3@gmail.com', '09123456799', NULL, '$2y$10$q7nJc4vJVZHpHThMmQLUX.xdo8MlOsOonVZeivevY6q9rdKTt9OGS', NULL, NULL, 'customer', NULL, NULL, NULL, 0, '124413', '2025-05-11 09:06:24'),
(32, 'Poldo', 'Almoguera', 'poldorivera07@gmail.com', '09937167503', NULL, '$2y$10$jccQt4zE6XpLLyLjofHVJu4N4FIdk4Q/cVaJF7f5jm4.VjL7wMR2a', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(33, 'Fammela', 'De Guzman', 'mysterywoman1242@gmail.com', '09363960987', NULL, '$2y$10$NK5wJrgzRWWcic//rxwiQeqZec2gSUBEee4CusxokGWd9xmw1rzxu', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(34, 'Christian', 'Realisan', 'chano@gmail.com', '09123456789', NULL, '$2y$10$ibcgOUmMfiJlMiaYfT5fP.N5WVpQozypiPi7cTtl6H5DBJuypmTSu', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(35, 'Myra Kristine Grace ', 'Luceño', 'myraluceno59@gmail.com', '09638322673', NULL, '$2y$10$aB1qDp6yq48CThqcCG2.S.9JtJFmkAA75eqL8/bFUXi9sMSiGJwZq', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(36, 'Myra', 'Aceveda', 'myra2006@gmail.com', '09638322673', NULL, '$2y$10$PWhwxvCUIqKngu7Vr3unQ.lHzYNSbO9mIQ8dvF9CZ.KPalEIX9.UG', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', NULL, '$2y$10$9NAc1tYNKs/dyzPvRqBuhu7sI51uMmW2xu0zR318t./NU2zBCDR0i', NULL, 'profile_69873b4b100877.51144206.jpg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(41, 'Elly', 'Mildred', 'ellymildred846@gmail.com', '09951779200', NULL, '$2y$10$cJzE7CDOXKXPvaif0cvB3eotY.ZUvd.8rjGgsWE86fU0SzV2DRjgy', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(43, 'Chan', 'Chan', 'chan.christians123@gmail.com', '09112222222', NULL, '$2y$10$yuRf9CSeCcdoBE7DThbiJuV0yj8.c4Lcv.KjVQVCL2HfumNEMqXbm', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(44, 'Henz', 'Ollywod', 'ollywodhenz@gmail.com', '09267615921', NULL, '$2y$10$QLMn8BsB8/4eHFPdyqXmye5IXWf1wJCXCDnRA/0CR1.DHp79Ud4ZO', NULL, 'profile_6922c38c6b37d9.85367249.jpg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(53, 'SAD', 'SAD', 'sarahelmenzo13@gmail.com', '09123456565', NULL, '$2y$10$Qh2vGWsid.Alb.nbWCeDQOoaw7UCKHKHDfcGUM2urCwqjFwr7IqKi', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(54, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'iansilang123@gmail.com', '09123454545', NULL, '$2y$10$xA9T29HVZY1M5Lh1vR5im.vcEYZzlxQzaYiGj2tz6Y6f/Puc5DC3m', NULL, 'profile_698b13b517de84.47203175.jpg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(55, 'Fammela ', 'De Guzman ', 'allysonmildred696@gmail.com', '09362846372', NULL, '$2y$10$zZJn0qbclQA62F8fE6YjkumBwEySVql5oQtwdsW7JkEugLCkricNS', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(56, 'Sean', 'Villanueva', 'villanuevasean083@gmail.com', '09632088569', NULL, '$2y$10$Ksm82AreUvGKhdIYCbnMLOCd7FVnyu25WbdFYwV47PeaeDnigENHe', NULL, 'profile_698b4a8ae99549.25368395.jpeg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(57, 'Keizy', 'Marimon', 'aizzy.olloka@gmail.com', '09127418448', NULL, '$2y$10$1jWcKUZx2311Rr9DUO8miOnpWWkPG7SsW6wody.v02666GwMqG1AG', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(58, 'robin', 'almarez', 'sir.robin.2024@gmail.com', '09219625377', NULL, '$2y$10$dDKFChiYXhZKAAnY1JCxzO4altdEhbV8yiYEXpYsYCRER4f1zYtHu', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_unified`
--

CREATE TABLE `users_unified` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','frontdesk','cashier','customer') NOT NULL DEFAULT 'customer',
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_unified`
--

INSERT INTO `users_unified` (`id`, `firstname`, `lastname`, `email`, `password`, `phone`, `address`, `profile_photo`, `user_type`, `is_verified`, `verification_code`, `verification_expiry`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`) VALUES
(1, 'Alfred hendrik', 'Aceveda', 'admin@example.com', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', '09362715617', 'Balite Calapan City Oriental Mindoro', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(2, 'Admin', '', 'frontdesk@example.com', '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', '', '', NULL, 'frontdesk', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(3, 'Admin', '', 'cashier@example.com', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', '', '', NULL, 'cashier', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(4, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '$2y$10$9Of5FaVHvCt/YsEnryDRnOjxkRE6oS1BhqvJnl/YJ4ZL4RnZo6sVK', '09362715617', 'Lumangbayan Calapan City', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva34@gmail.com', '$2y$10$r1X5exzjzJcmM.v3uGBNXeXiN.QkoU1QOIYDIG.7UjmZG.qmxt0hy', '09362715617', 'Lumangbayan Calapan City', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(6, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', '912345678787', 'wawa calapan city', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(7, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', '912345678787', 'tawagan', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(8, 'Aizzy', 'Villanueva', 'aizzyvillanueva5@gmail.com', '$2y$10$6FJdPRpNRHB5rzVQ6L8EO.7xNMFRTQ.qS84uCvwkqB/nmB1aAx5Fy', '09362715617', 'Lumangbayan Calapan City', NULL, 'admin', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19'),
(9, 'Fammela', 'De Guzman', 'fammeladeguzman21@gmail.com', '$2y$10$fo1HrZCUvSrEh8InzHzuoORab6vzOZayXnF2iLrmLQeBmye/mNSl.', '09362715617', 'Wawa, Calapan City', NULL, 'frontdesk', 1, NULL, NULL, NULL, NULL, '2025-04-14 07:38:19', '2025-04-14 07:38:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `email`, `code`, `expiry`, `used`, `created_at`) VALUES
(1, 'christianrealisan25@gmail.com', '724799', '2025-04-12 21:00:03', 1, '2025-04-12 07:45:03'),
(2, 'chanomabalo@gmail.com', '328548', '2025-04-12 21:05:07', 0, '2025-04-12 07:50:07'),
(0, '09951776920', '076912', '2025-11-23 08:18:18', 0, '2025-11-23 08:03:18');

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
(2, 'phone', 1, 'Phone verification is currently under maintenance. Please try email verification.', '2025-04-11 14:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `verification_types`
--

CREATE TABLE `verification_types` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_enabled` tinyint(1) DEFAULT 1,
  `disable_message` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_types`
--

INSERT INTO `verification_types` (`id`, `type`, `is_enabled`, `disable_message`, `last_updated`) VALUES
(1, 'SMS', 0, 'fd', '2025-12-29 07:29:40'),
(2, 'Email', 0, 'basta', '2025-04-11 09:47:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD KEY `idx_activities_type` (`activity_type`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booked_rooms`
--
ALTER TABLE `booked_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_id` (`booking_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_bookings_dates` (`check_in`,`check_out`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `booking_amenities`
--
ALTER TABLE `booking_amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_check_inout`
--
ALTER TABLE `booking_check_inout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_history`
--
ALTER TABLE `booking_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashier`
--
ALTER TABLE `cashier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD KEY `idx_customers_vip` (`is_vip`);

--
-- Indexes for table `discount_types`
--
ALTER TABLE `discount_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_packages`
--
ALTER TABLE `event_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_categories`
--
ALTER TABLE `facility_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fix_booking_ids_log`
--
ALTER TABLE `fix_booking_ids_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items_addons`
--
ALTER TABLE `menu_items_addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_status` (`status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_booking` (`booking_fk_id`),
  ADD KEY `fk_notifications_event` (`event_fk_id`),
  ADD KEY `fk_notifications_order` (`order_id`);

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
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indexes for table `orders_table`
--
ALTER TABLE `orders_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_table_type`
--
ALTER TABLE `orders_table_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_bookings`
--
ALTER TABLE `promo_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_ref` (`booking_ref`),
  ADD UNIQUE KEY `invoice_id` (`invoice_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guest_email` (`guest_email`),
  ADD KEY `check_in_date` (`check_in_date`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `booking_status` (`booking_status`);

--
-- Indexes for table `reschedule_bookings`
--
ALTER TABLE `reschedule_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resetpass`
--
ALTER TABLE `resetpass`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD PRIMARY KEY (`room_number_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_transfers`
--
ALTER TABLE `room_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_type`
--
ALTER TABLE `staff_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_booking` (`user_id`);

--
-- Indexes for table `table_number`
--
ALTER TABLE `table_number`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `table_packages`
--
ALTER TABLE `table_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_types`
--
ALTER TABLE `table_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms_and_conditions`
--
ALTER TABLE `terms_and_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userss`
--
ALTER TABLE `userss`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_unified`
--
ALTER TABLE `users_unified`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `booked_rooms`
--
ALTER TABLE `booked_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_amenities`
--
ALTER TABLE `booking_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `booking_check_inout`
--
ALTER TABLE `booking_check_inout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `booking_history`
--
ALTER TABLE `booking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cashier`
--
ALTER TABLE `cashier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `discount_types`
--
ALTER TABLE `discount_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_packages`
--
ALTER TABLE `event_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `facility_categories`
--
ALTER TABLE `facility_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `fix_booking_ids_log`
--
ALTER TABLE `fix_booking_ids_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=448;

--
-- AUTO_INCREMENT for table `menu_items_addons`
--
ALTER TABLE `menu_items_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders_table`
--
ALTER TABLE `orders_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders_table_type`
--
ALTER TABLE `orders_table_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `promo_bookings`
--
ALTER TABLE `promo_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reschedule_bookings`
--
ALTER TABLE `reschedule_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resetpass`
--
ALTER TABLE `resetpass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `room_number_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_transfers`
--
ALTER TABLE `room_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staff_type`
--
ALTER TABLE `staff_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `table_bookings`
--
ALTER TABLE `table_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_number`
--
ALTER TABLE `table_number`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `table_packages`
--
ALTER TABLE `table_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `table_types`
--
ALTER TABLE `table_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `terms_and_conditions`
--
ALTER TABLE `terms_and_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users_unified`
--
ALTER TABLE `users_unified`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userss` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`),
  ADD CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`),
  ADD CONSTRAINT `orders_ibfk_6` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`id`);

--
-- Constraints for table `resetpass`
--
ALTER TABLE `resetpass`
  ADD CONSTRAINT `resetpass_user_fk` FOREIGN KEY (`user_id`) REFERENCES `userss` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD CONSTRAINT `fk_user_booking` FOREIGN KEY (`user_id`) REFERENCES `userss` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
