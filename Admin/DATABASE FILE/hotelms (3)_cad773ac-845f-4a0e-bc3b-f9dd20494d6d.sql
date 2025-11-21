-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 04:18 PM
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
(2, 'About Casa Estela ', 'Casa Estela Boutique Hotel, located along Gov. B. Marsigan St. in Brgy. Libis, Calapan City, Oriental Mindoro, is a charming establishment that combines historic charm with modern elegance. Originally a private residence, this two-story building was transformed into a boutique hotel starting in 2017 by Engr. Estela Macapagal, a businessperson and contractor. Renovations were undertaken with the help of her son, Marc Jill M. Dimapilis, a civil engineer who also contributed to the hotel\'s culinary and service concepts.\r\n', '2025-04-21 08:51:15');

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
(1, 'single bed', 10, 10, '2025-05-13 06:44:25', 1000.00),
(2, 'Queens Bed', 10, 10, '2025-05-13 06:44:32', 1000.00);

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
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `number_of_guests` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_quantity` int(11) DEFAULT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
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
  `discount_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `booking_reference`, `user_id`, `first_name`, `last_name`, `email`, `contact`, `booking_type`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `room_type_id`, `room_quantity`, `payment_option`, `payment_method`, `total_amount`, `status`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `user_types`, `num_adults`, `num_children`, `extra_bed`, `discount_type`, `discount_percentage`, `discount_amount`) VALUES
(59, 'BK-68463FFD3830B', 39, 'Panget', 'Realisan', 'chanomabalo@gmail.com', '09459732538', 'Online', '2025-06-09', '2025-06-10', NULL, 1, 8, 1, 'Partial Payment', 'gcash', 6100.00, 'pending', '2025-06-09 01:59:25', 1, 1500.00, 4600.00, 'frontdesk', 1, 0, '1', NULL, NULL, NULL),
(60, 'BK-68464286733CA', 39, 'Panget', 'Realisan', 'chanomabalo@gmail.com', '09459732538', 'Online', '2025-06-09', '2025-06-11', NULL, 1, 8, 1, 'Partial Payment', 'gcash', 10200.00, 'pending', '2025-06-09 02:10:14', 2, 1500.00, 8700.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(61, 'BK-6849197B7DB98', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-11', '2025-06-13', NULL, 1, 8, 1, 'Partial Payment', 'gcash', 20400.00, 'pending', '2025-06-11 05:51:55', 2, 1500.00, 18900.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(62, 'BK-6849BC6E28AE4', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 4080.00, 'pending', '2025-06-11 17:27:10', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(63, 'BK-6849C81E1BAF5', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 4080.00, 'pending', '2025-06-11 18:17:02', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(64, 'BK-6849D6C1E98CA', NULL, 'Chano', 'cutie', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 4080.00, 'pending', '2025-06-11 19:19:29', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(65, 'BK-6849DC826DECE', NULL, 'Chano', 'cutie', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-14', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 8960.00, 'pending', '2025-06-11 19:44:02', 2, 1500.00, 7460.00, 'frontdesk', 1, 0, 'yes', NULL, NULL, NULL),
(66, 'BK-6849DE066057C', NULL, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 3360.00, 'pending', '2025-06-11 19:50:30', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(67, 'BK-6849DF6A99113', NULL, '', '', NULL, NULL, 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', '', 3360.00, 'pending', '2025-06-11 19:56:26', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '0', NULL, NULL, NULL),
(68, 'BK-6849DF6D4538C', NULL, '', '', NULL, NULL, 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', '', 3360.00, 'pending', '2025-06-11 19:56:29', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '0', NULL, NULL, NULL),
(69, 'BK-6849E00B79943', NULL, '', '', NULL, NULL, 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', '', 3360.00, 'pending', '2025-06-11 19:59:07', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '0', NULL, NULL, NULL),
(70, 'BK-6849E2D68E3B0', NULL, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 3360.00, 'pending', '2025-06-11 20:11:02', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(71, 'BK-6849E39CD5BEB', NULL, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 3360.00, 'pending', '2025-06-11 20:14:20', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(72, 'BK-6849E4C658224', NULL, 'christian realisan', 'christian realisan', 'christianrealisan3@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 4080.00, 'pending', '2025-06-11 20:19:18', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(73, 'BK-6849E97CBC5DA', NULL, 'christian realisan', 'christian realisan', 'christianrealisan3@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 5100.00, 'pending', '2025-06-11 20:39:24', 1, 1500.00, 3600.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(74, 'BK-6849EA8C58C97', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, NULL, 1, 'Partial Payment', 'gcash', 4080.00, 'pending', '2025-06-11 20:43:56', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(75, 'BK-6849F1545BECB', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, 8, 1, 'Full Payment', '', 4080.00, 'pending', '2025-06-11 21:12:52', 1, 1500.00, 2580.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(76, 'BK-6849F3EA850EF', NULL, 'christian', 'Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-12', '2025-06-13', NULL, 2, 9, 1, 'Partial Payment', 'gcash', 8960.00, 'pending', '2025-06-11 21:23:54', 1, 1500.00, 7460.00, 'frontdesk', 1, 1, '0', NULL, NULL, NULL),
(77, 'BK-684AF5D26942A', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, 9, 1, 'Partial Payment', 'gcash', 10200.00, 'pending', '2025-06-12 15:44:18', 1, 1500.00, 8700.00, 'frontdesk', 1, 0, '0', '', 0.00, 1020.00),
(78, 'BK-684AF8CE7BF63', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-12', '2025-06-13', NULL, 1, 9, 1, 'Partial Payment', 'gcash', 5100.00, 'pending', '2025-06-12 15:57:02', 1, 1500.00, 3600.00, 'frontdesk', 1, 0, '0', '', 0.00, 0.00),
(79, 'BK-684B03C0A3F14', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-06-13', '2025-06-14', NULL, 1, 8, 1, 'Partial Payment', 'gcash', 10200.00, 'pending', '2025-06-12 16:43:44', 1, 1500.00, 8700.00, 'frontdesk', 1, 0, '', NULL, NULL, NULL),
(80, 'BK-684B74AAECC95', 3, 'Cashier', 'Realisan', 'cashier@example.com', '09123456789', 'Online', '2025-06-13', '2025-06-14', NULL, 1, 3, 1, 'Partial Payment', 'gcash', 3360.00, 'pending', '2025-06-13 00:45:30', 1, 1500.00, 1860.00, 'frontdesk', 1, 0, '', '', 0.00, 0.00),
(81, 'BK-685B99AB563C4', NULL, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-25', '2025-06-28', NULL, 1, 3, 1, 'Partial Payment', 'maya', 22200.00, 'pending', '2025-06-25 06:39:39', 3, 1500.00, 20700.00, 'frontdesk', 1, 0, '', '', 0.00, 0.00),
(82, 'BK-685BBB113581F', NULL, 'christian realisan Christian Realisan', 'christian realisan Christian Realisan', 'chanomabalo@gmail.com', '9123456789', 'Online', '2025-06-26', '2025-06-28', NULL, 1, 3, 1, 'Partial Payment', 'gcash', 8400.00, 'pending', '2025-06-25 09:02:09', 2, 1500.00, 6900.00, 'frontdesk', 1, 0, '', NULL, 0.00, 0.00),
(83, 'BK-6870683CBE785', NULL, 'adad', 'adad', 'adad@gmail.com', '9222222222', 'Online', '2025-07-11', '2025-07-12', NULL, 1, 3, 1, 'Partial Payment', 'gcash', 4200.00, 'pending', '2025-07-11 01:26:20', 1, 1500.00, 2700.00, 'frontdesk', 1, 0, '', NULL, 0.00, 0.00),
(84, 'BK-689D706FAECD8', NULL, 'christian realisan', 'Realisan', 'chano@gmail.com', '9123456784', 'Online', '2025-08-14', '2025-08-16', NULL, 1, 3, 1, 'Partial Payment', 'maya', 9400.00, 'pending', '2025-08-14 05:13:20', 2, 1500.00, 7900.00, 'frontdesk', 1, 0, '1', NULL, 0.00, 0.00),
(85, 'BK-68D0B7BAD1187', NULL, 'christian realisan', 'Realisan', 'chano@gmail.com', '9123456784', 'Online', '2025-09-22', '2025-09-25', NULL, 1, 3, 1, 'Partial Payment', 'gcash', 22200.00, 'pending', '2025-09-22 02:43:06', 3, 1500.00, 20700.00, 'frontdesk', 1, 0, '', NULL, 0.00, 0.00),
(86, 'BK-68F61F125ED8C', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-10-20', '2025-10-22', NULL, 1, 11, 1, 'Partial Payment', 'gcash', 14800.00, 'pending', '2025-10-20 11:38:04', 2, 1500.00, 13300.00, 'frontdesk', 1, 0, '', NULL, 0.00, 0.00),
(87, 'BK-68F61FEDE15C7', 40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', 'Online', '2025-10-20', '2025-10-21', NULL, 1, 3, 1, 'Partial Payment', 'gcash', 9400.00, 'pending', '2025-10-20 11:41:33', 1, 1500.00, 7900.00, 'frontdesk', 1, 0, '1', NULL, 0.00, 0.00);

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
(1, 'fab fa-facebook', 'Casa Estela Boutique Hotel & Cafés', 'https://web.facebook.com/casaestelahotelcafe', 1, 1, 1),
(2, 'fas fa-envelope', 'casaestelahotelcafe@gmail.com', 'mailto:casaestelahotelcafe@gmail.com', 0, 2, 1),
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
  `percentage` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_types`
--

INSERT INTO `discount_types` (`id`, `name`, `percentage`, `description`, `is_active`, `created_at`) VALUES
(1, 'senior', 10.00, 'Senior Citizen Discount', 1, '2025-04-13 02:58:18'),
(2, 'pwP', 10.00, 'Person with Disability Discount', 1, '2025-04-13 02:58:18'),
(3, 'student', 10.00, 'Student Discount', 1, '2025-04-13 02:58:18');

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
  `id` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` int(255) NOT NULL,
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
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `reserve_type` varchar(50) DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_source` varchar(50) DEFAULT 'Regular Booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `user_id`, `customer_name`, `package_name`, `package_price`, `base_price`, `overtime_hours`, `overtime_charge`, `extra_guests`, `extra_guest_charge`, `total_amount`, `paid_amount`, `remaining_balance`, `reservation_date`, `event_type`, `event_date`, `start_time`, `end_time`, `number_of_guests`, `payment_method`, `payment_type`, `booking_status`, `reserve_type`, `created_at`, `updated_at`, `booking_source`) VALUES
(1, 40, 0, 'Package A', 58500.00, 47500.00, 0, 6000.00, 0, 0.00, 58500.00, 29250.00, 29250.00, '2025-06-13', 'Birthday', '2025-06-25', '10:37:00', '18:37:00', 55, 'gcash', 'downpayment', 'pending', 'Regular', '2025-06-13 02:38:35', '2025-06-13 02:38:35', 'Website Booking');

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
(1, 'Venue Rental Only', 20000.00, '5-hour venue rental\r\nTables and Tiffany chairs', 'uploads/event_packages/682691d939a20.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 0, NULL, 50, '5 hours', NULL, 'Available'),
(2, 'Package A', 47500.00, '5-hour venue rental      Tables     and Tiffany chairs', 'uploads/event_packages/682692c15a2eb.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 0, '1 Appetizers, 2 Pasta, 2 Mains, Salad Bar, Rice , Drinks', 50, '5 hours', NULL, 'Available'),
(3, 'Package B', 55000.00, '5-hour venue rental\r\nTables and  Tiffany chairs', 'uploads/event_packages/682693e94762a.jpg', 'images/hall.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 0, ' 2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert,  Drinks ', 50, '5 hours', '**Assumes 5,000g (100g per person) of Wagyu steak will be served.', 'Available'),
(4, 'Package C', 76800.00, '5-hour venue rental\r\nTables and Tiffany chairs', 'uploads/event_packages/682693d33161e.jpg', 'images/hall2.jpg', 'images/hall.jpg', 30, 5, '2025-02-12 02:48:46', 1, '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2desserts, Drinks ', 50, '5 hours', NULL, 'Available'),
(5, 'Package F', 50000.00, '<br />\r\n<b>Deprecated</b>:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in <b>/home/u429956055/domains/e-akomoda.site/public_html/Admin/edit_event_package.php</b> on line <b>215</b><br />\r\n', 'uploads/event_packages/682692cbc2261.jpg', NULL, NULL, 50, 5, '2025-04-17 06:57:47', 0, NULL, 50, '5 hours', NULL, 'Available');

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
(26, 7, 'Shower', 'check', 7, 1, '2025-03-05 11:21:30', '2025-03-05 11:21:30'),
(27, 1, 'may tanod', 'check', 5, 1, '2025-04-09 11:13:27', '2025-04-09 11:13:27'),
(29, 4, 'Kiss', 'check', 4, 1, '2025-04-09 13:55:56', '2025-04-09 13:55:56'),
(0, 1, 'May bayad', 'check', 6, 1, '2025-04-21 08:50:34', '2025-04-21 08:50:34');

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
  `guest_type` enum('regular','pwd','senior') NOT NULL DEFAULT 'regular',
  `age` int(11) DEFAULT NULL,
  `image_proof` varchar(44) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `first_name`, `last_name`, `guest_type`, `age`, `image_proof`, `created_at`) VALUES
(1, 67, '', '', '', NULL, NULL, '2025-06-11 19:56:26'),
(2, 68, '', '', '', NULL, NULL, '2025-06-11 19:56:29'),
(3, 69, '', '', '', NULL, NULL, '2025-06-11 19:59:07');

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
(7, 'tea', 'TEA'),
(8, 'otherdrinks', 'OTHER DRINKS');

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
(1, 1, 'Hand-cut Potato Fries', 160.00, 'uploads/menus/menu_68120be0791bf.png', 'Crispy fries with seasoning', 0),
(2, 1, 'Mozzarella Stick', 150.00, 'uploads/menus/menu_6810015e57a69.jpg', 'Fried mozzarella with marinara sauce', 0),
(3, 1, 'Chicken Wings', 180.00, 'uploads/menus/menu_67f927bc54efc.jpg', 'Spicy chicken wings', 1),
(4, 2, 'Salad', 200.00, 'uploads/menus/menu_67fb19926baf0.png', 'Fresh garden salad with dressing', 1),
(5, 2, 'Coconut Salad', 200.00, 'uploads/menus/menu_67ff5aa2dae0f.jpg', 'Tropical coconut salad', 1),
(6, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg', 'Classic spaghetti with tomato sauce', 1),
(7, 4, 'Egg Sandwich', 500.00, '', 'Grilled egg sandwich', 1),
(8, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b4289215e15.jpg', 'Spaghetti with creamy sauce', 1),
(431, 6, 'Matcha', 180.00, '', 'Matcha green tea drink', 1),
(432, 1, 'Carbonara', 120.00, '', 'Carbonara pasta with bacon and cheese', 1);

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
(3, 3, 'Gravy', 20.00);

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
(1, 5, 'open pa po kayo ?', 'user', 0, '2025-04-12 06:31:08', 'unread'),
(2, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:31:08', 'unread'),
(3, 5, 'book', 'user', 0, '2025-04-12 06:34:16', 'unread'),
(4, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:34:16', 'unread'),
(5, 5, 'book', 'user', 0, '2025-04-12 06:35:58', 'unread'),
(6, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:35:58', 'unread'),
(7, 33, 'open pa po kayo ?', 'user', 0, '2025-04-12 09:54:24', 'unread'),
(8, 33, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 09:54:24', 'unread'),
(9, 31, 'open pa po kayo ?', 'user', 0, '2025-04-12 10:11:04', 'unread'),
(10, 31, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 10:11:04', 'unread'),
(11, 1, 'ay hoy', 'user', 0, '2025-04-13 01:57:28', 'unread'),
(12, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-13 01:57:28', 'unread'),
(13, 29, 'Hello', 'user', 0, '2025-04-15 05:29:41', 'unread'),
(14, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-15 05:29:41', 'unread'),
(15, 29, 'book', 'user', 0, '2025-04-16 03:57:45', 'unread'),
(16, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-16 03:57:45', 'unread'),
(17, 1, 'open pa po kayo ?', 'user', 0, '2025-04-16 09:49:01', 'unread'),
(18, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-16 09:49:01', 'unread'),
(19, 1, 'open pa po kayo ?', 'user', 0, '2025-04-16 09:50:01', 'unread'),
(20, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-16 09:50:01', 'unread'),
(21, 1, 'open pa po kayo ?', 'user', 0, '2025-04-19 10:31:58', 'unread'),
(22, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-19 10:31:58', 'unread'),
(23, 1, 'book', 'user', 0, '2025-04-19 10:32:03', 'unread'),
(24, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-19 10:32:03', 'unread'),
(25, 14, 'Hi', 'user', 0, '2025-04-21 06:57:37', 'unread'),
(26, 14, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-21 06:57:37', 'unread'),
(27, 14, 'Hello', 'user', 0, '2025-04-21 06:57:45', 'unread'),
(28, 14, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-21 06:57:45', 'unread'),
(29, 38, 'book', 'user', 0, '2025-04-23 23:18:32', 'unread'),
(30, 38, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-23 23:18:32', 'unread'),
(41, 39, 'HEllo', 'user', 0, '2025-05-16 06:16:44', 'unread'),
(42, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 06:16:44', 'unread'),
(43, 39, 'hi', 'user', 0, '2025-05-16 06:43:24', 'unread'),
(44, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 06:43:24', 'unread'),
(45, 39, 'hi', 'user', 0, '2025-05-16 07:02:01', 'unread'),
(46, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:02', 'unread'),
(47, 39, 'hi', 'user', 0, '2025-05-16 07:02:14', 'unread'),
(48, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:14', 'unread'),
(49, 39, 'hi', 'user', 0, '2025-05-16 07:02:21', 'unread'),
(50, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:21', 'unread'),
(51, 39, 'hi', 'user', 0, '2025-05-16 07:02:24', 'unread'),
(52, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:24', 'unread'),
(53, 39, 'aa', 'user', 0, '2025-05-16 07:02:32', 'unread'),
(54, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:02:32', 'unread'),
(55, 39, 'aa', 'user', 0, '2025-05-16 07:24:08', 'unread'),
(56, 39, 'aa', 'user', 0, '2025-05-16 07:27:10', 'unread'),
(57, 39, 'aa', 'user', 0, '2025-05-16 07:38:09', 'unread'),
(58, 39, 'hello good evening', 'user', 0, '2025-05-16 08:03:07', 'unread'),
(59, 39, 'hello good evening', 'user', 0, '2025-05-16 08:03:10', 'unread'),
(60, 39, 'hello good evening', 'user', 0, '2025-05-16 08:05:02', 'unread'),
(61, 39, 'hello good evening', 'user', 0, '2025-05-16 08:05:05', 'unread'),
(62, 39, 'hello good evening', 'user', 0, '2025-05-16 08:06:41', 'unread'),
(63, 39, 'hello good evening', 'user', 0, '2025-05-16 08:09:16', 'unread'),
(64, 39, 'hello good evening', 'user', 0, '2025-05-16 08:43:26', 'unread'),
(65, 39, 'hello good evening', 'user', 0, '2025-05-16 08:43:41', 'unread'),
(66, 39, 'hello good evening', 'user', 0, '2025-05-16 10:25:27', 'unread'),
(67, 1, 'HEllo', 'user', 0, '2025-05-17 03:23:28', 'unread'),
(68, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-17 03:23:28', 'unread'),
(69, 1, 'hi', 'user', 0, '2025-05-21 04:17:18', 'unread'),
(70, 1, 'HEllo', 'user', 0, '2025-05-21 04:19:04', 'unread'),
(71, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-21 04:19:04', 'unread'),
(72, 39, 'as', 'user', 0, '2025-05-28 13:24:42', 'unread'),
(73, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 13:24:42', 'unread'),
(74, 39, 'ff', 'user', 0, '2025-05-28 13:24:57', 'unread'),
(75, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 13:24:57', 'unread'),
(76, 39, 'Montenegro', 'user', 0, '2025-05-28 13:28:09', 'unread'),
(77, 39, 'Abordo', 'user', 0, '2025-05-28 13:28:19', 'unread'),
(78, 39, 'asa', 'user', 0, '2025-05-28 17:46:24', 'unread'),
(79, 39, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-28 17:46:24', 'unread'),
(80, 39, '77', 'user', 0, '2025-05-28 17:48:45', 'unread'),
(81, 39, 'h', 'user', 0, '2025-05-28 17:49:14', 'unread'),
(82, 39, 'l', 'user', 0, '2025-06-03 08:59:19', 'unread');

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
(60, 8, '', 'Your order has been placed successfully. Please pick up at 02:37 PM', 'order', '', NULL, 1, '2025-03-18 00:37:37'),
(70, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 0, '2025-04-10 21:48:38'),
(71, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 0, '2025-04-10 21:48:58'),
(72, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 0, '2025-04-10 22:01:57'),
(73, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 0, '2025-04-10 22:02:48'),
(74, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 0, '2025-04-10 22:05:04'),
(75, 0, '', 'Booking #5 has been checked in', 'booking', '', 5, 0, '2025-04-10 22:10:34'),
(76, 0, '', 'Booking #6 has been checked in', 'booking', '', 6, 0, '2025-04-10 22:14:40'),
(77, 0, '', 'Early checkout processed. Amount to return: ₱220,000.00', 'booking', '', 6, 0, '2025-04-10 22:30:03'),
(78, 0, '', 'Booking #6 has been ', 'booking', '', 6, 0, '2025-04-10 22:30:03'),
(79, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 0, '2025-04-10 22:31:26'),
(80, 0, '', 'Early checkout processed. Amount to return: ₱49,500.00', 'booking', '', 4, 0, '2025-04-10 22:31:33'),
(81, 0, '', 'Booking #4 has been ', 'booking', '', 4, 0, '2025-04-10 22:31:34'),
(82, 0, '', 'Early checkout processed. Amount to return: ₱49,500.00', 'booking', '', 4, 0, '2025-04-10 22:31:36'),
(83, 0, '', 'Booking #4 has been ', 'booking', '', 4, 0, '2025-04-10 22:31:36'),
(84, 0, '', 'Early checkout processed. Amount to return: ₱260,000.00', 'booking', '', 5, 0, '2025-04-10 22:34:32'),
(85, 0, '', 'Booking #7 has been checked in', 'booking', '', 7, 0, '2025-04-10 22:35:59'),
(86, 0, '', 'Early checkout processed. Amount to return: ₱135,000.00', 'booking', '', 7, 0, '2025-04-10 22:37:22'),
(87, 0, '', 'Booking #9 has been checked in', 'booking', '', 9, 0, '2025-04-11 06:37:54'),
(88, 0, '', 'Booking #11 has been checked in', 'booking', '', 11, 0, '2025-04-11 06:54:04'),
(89, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 0, '2025-04-11 06:55:08'),
(90, 0, '', 'Early checkout processed. Amount to return: ₱8,100.00', 'booking', '', 1, 0, '2025-04-11 08:43:15'),
(91, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 0, '2025-04-11 08:44:59'),
(92, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 0, '2025-04-11 09:00:39'),
(93, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 0, '2025-04-11 09:04:32'),
(94, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 0, '2025-04-11 09:10:49'),
(95, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 0, '2025-04-11 09:14:09'),
(96, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 0, '2025-04-11 09:18:19'),
(97, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 0, '2025-04-12 04:44:01'),
(98, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 0, '2025-04-12 04:45:31'),
(99, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 0, '2025-04-12 04:47:23'),
(100, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-503357 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 5, 0, '2025-04-12 02:28:23'),
(101, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-8E7A2C has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 6, 0, '2025-04-12 02:30:09'),
(102, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-999830 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 7, 0, '2025-04-12 02:32:01'),
(103, 0, '', 'Table Reservation #27 has been cancelled. Reason: booking_mistake', 'table_cancelled', '', NULL, 0, '2025-04-12 04:37:56'),
(104, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-C25499 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 8, 0, '2025-04-12 05:04:52'),
(105, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-129B01 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 1, 0, '2025-04-12 05:06:25'),
(106, 0, '', 'Table Reservation #31 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 0, '2025-04-12 05:47:17'),
(107, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-E26DF1 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 2, 0, '2025-04-12 06:18:43'),
(108, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-66AFDF has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 3, 0, '2025-04-12 06:22:42'),
(109, 5, 'New Booking Confirmation', 'Your booking #BK-20250413-355ED6 has been confirmed. Check-in date: 2025-04-13, Check-out date: 2025-04-17', 'booking', 'fas fa-calendar-check', 4, 0, '2025-04-12 22:54:46'),
(110, 1, 'New Booking Confirmation', 'Your booking #BK-20250413-747C66 has been confirmed. Check-in date: 2025-04-17, Check-out date: 2025-04-18', 'booking', 'fas fa-calendar-check', 5, 1, '2025-04-12 23:49:04'),
(111, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 0, '2025-04-13 00:48:47'),
(112, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 0, '2025-04-13 00:54:21'),
(113, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 0, '2025-04-13 02:18:09'),
(114, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 0, '2025-04-13 02:18:41'),
(115, 29, 'New Booking Confirmation', 'Your booking #BK-20250415-625949 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-05-01', 'booking', 'fas fa-calendar-check', 7, 0, '2025-04-15 08:05:50'),
(116, 29, 'New Booking Confirmation', 'Your booking #BK-20250415-A4A7B0 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-04-16', 'booking', 'fas fa-calendar-check', 8, 0, '2025-04-15 08:11:59'),
(117, 31, 'New Booking Confirmation', 'Your booking #BK-20250415-472828 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-04-16', 'booking', 'fas fa-calendar-check', 9, 0, '2025-04-15 08:56:55'),
(118, 36, 'New Booking Confirmation', 'Your booking #BK-20250415-C71161 has been confirmed. Check-in date: 2025-04-16, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 10, 0, '2025-04-15 13:17:48'),
(119, 0, '', 'Table Reservation #1 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 0, '2025-04-16 04:25:53'),
(120, 0, '', 'Table Reservation #3 has been cancelled. Reason: booking_mistake', 'table_cancelled', '', NULL, 0, '2025-04-16 09:38:05'),
(121, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 0, '2025-04-16 23:26:55'),
(122, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 0, '2025-04-16 23:44:35'),
(123, 0, '', 'Table Reservation #10 has been cancelled. Reason: emergency', 'table_cancelled', '', NULL, 0, '2025-04-19 10:36:31'),
(124, 0, '', 'Room transfer processed for booking #7. Transferred to new room type.', 'room_transfer', '', 7, 0, '2025-04-21 08:42:48'),
(125, 32, 'New Booking Confirmation', 'Your booking #BK-20250421-109C53 has been confirmed. Check-in date: 2025-04-21, Check-out date: 2025-04-23', 'booking', 'fas fa-calendar-check', 26, 0, '2025-04-21 08:59:19'),
(126, 38, 'New Booking Confirmation', 'Your booking #BK-20250428-40ACB4 has been confirmed. Check-in date: 2025-04-28, Check-out date: 2025-05-08', 'booking', 'fas fa-calendar-check', 27, 0, '2025-04-27 22:35:35'),
(231, 8, 'New Order Placed', 'Order #46 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 46, 1, '2025-05-02 10:21:22'),
(232, 8, 'New Order Placed', 'Order #47 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 47, 1, '2025-05-02 10:53:30'),
(233, 8, 'New Order Placed', 'Order #49 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 49, 1, '2025-05-03 09:41:40'),
(234, 8, 'New Order Placed', 'Order #50 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 50, 1, '2025-05-03 09:43:58'),
(235, 8, 'New Order Placed', 'Order #51 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 51, 1, '2025-05-03 09:57:03'),
(236, 8, 'New Order Placed', 'Order #52 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 52, 1, '2025-05-03 10:04:02'),
(237, 8, 'New Order Placed', 'Order #53 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 53, 1, '2025-05-03 10:21:12'),
(238, 8, 'New Order Placed', 'Order #54 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 54, 1, '2025-05-03 10:22:34'),
(239, 8, 'New Order Placed', 'Order #55 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 55, 1, '2025-05-03 11:44:04'),
(240, 8, 'New Order Placed', 'Order #56 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 56, 1, '2025-05-03 14:45:23'),
(241, 8, 'New Order Placed', 'Order #57 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 57, 1, '2025-05-03 15:05:59'),
(242, 8, 'New Order Placed', 'Order #58 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 58, 1, '2025-05-04 03:22:31'),
(243, 8, 'New Order Placed', 'Order #59 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 59, 1, '2025-05-04 03:27:27'),
(244, 8, 'New Order Placed', 'Order #60 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 60, 1, '2025-05-04 03:46:12'),
(245, 8, 'New Order Placed', 'Order #61 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 61, 1, '2025-05-04 03:50:39'),
(246, 8, '', 'Your order #58 has been rejected. Reason: Invalid Payment', 'order_rejected', '', NULL, 0, '2025-05-04 05:22:30'),
(247, 8, '', 'Your order #55 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 0, '2025-05-04 05:26:08'),
(248, 8, '', 'Your order #57 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 0, '2025-05-04 05:26:16'),
(249, 8, '', 'Your order #61 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 0, '2025-05-04 05:26:23'),
(250, 8, '', 'Your order #60 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 0, '2025-05-04 05:26:30'),
(251, 8, '', 'Your order #56 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 0, '2025-05-04 05:26:54'),
(252, 38, 'New Order Placed', 'Order #7 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 7, 1, '2025-05-07 02:59:08'),
(253, 38, 'New Order Placed', 'Order #8 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 8, 1, '2025-05-07 03:01:00'),
(254, 38, 'New Order Placed', 'Order #14 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 14, 1, '2025-05-07 23:12:30'),
(255, 38, 'New Order Placed', 'Order #15 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 15, 1, '2025-05-07 23:29:41'),
(256, 38, '', 'Your order #23 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 0, '2025-05-08 05:31:41'),
(257, 38, '', 'Your order #22 has been rejected. Reason: System error occurred', 'order_rejected', '', NULL, 0, '2025-05-08 05:31:48'),
(258, 38, '', 'Your order #21 has been rejected. Reason: Incomplete payment', 'order_rejected', '', NULL, 0, '2025-05-08 05:31:54'),
(259, 38, '', 'Your order #20 has been rejected. Reason: Incomplete payment', 'order_rejected', '', NULL, 0, '2025-05-08 05:31:59'),
(260, 38, '', 'Your order #18 has been rejected. Reason: Incomplete payment', 'order_rejected', '', NULL, 0, '2025-05-08 05:32:04'),
(261, 38, 'New Order Placed', 'Order #25 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 25, 1, '2025-05-08 06:11:48'),
(262, 38, '', 'Your order #25 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 0, '2025-05-08 06:13:26'),
(263, 38, '', 'Your order #19 has been rejected. Reason: System error occurred', 'order_rejected', '', NULL, 0, '2025-05-08 06:13:33'),
(264, 38, '', 'Your order #17 has been rejected. Reason: Incomplete payment', 'order_rejected', '', NULL, 0, '2025-05-08 06:13:39'),
(265, 38, '', 'Your order #16 has been rejected. Reason: Incomplete payment', 'order_rejected', '', NULL, 0, '2025-05-08 06:13:45'),
(266, 38, '', 'Your order #24 has been rejected. Reason: System error occurred', 'order_rejected', '', NULL, 0, '2025-05-08 06:13:50'),
(267, 38, 'New Order Placed', 'Order #26 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 26, 1, '2025-05-08 06:14:14'),
(268, 38, 'New Order Placed', 'Order #28 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 28, 1, '2025-05-08 08:33:35'),
(269, 0, '', 'Table Reservation #53 has been cancelled. Reason: booking_mistake', 'table_cancelled', '', NULL, 0, '2025-05-08 08:51:03'),
(270, 0, '', 'Table Reservation #37 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 0, '2025-05-08 22:04:44'),
(271, 0, '', 'Table Reservation #38 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 0, '2025-05-08 22:04:56'),
(272, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-EC40F8 has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 28, 0, '2025-05-08 22:42:28'),
(273, 38, 'New Order Placed', 'Order #36 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 36, 1, '2025-05-09 01:46:41'),
(274, 38, '', 'Your order #34 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 0, '2025-05-09 01:48:03'),
(275, 38, '', 'Your order #33 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 0, '2025-05-09 01:48:08'),
(276, 38, 'New Order Placed', 'Order #6 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 6, 1, '2025-05-09 01:53:27'),
(277, 38, 'New Order Placed', 'Order #7 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 7, 1, '2025-05-09 01:54:48'),
(278, 38, 'New Order Placed', 'Order #2 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 2, 1, '2025-05-09 02:00:14'),
(279, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-1B6396 has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 29, 0, '2025-05-09 02:16:14'),
(280, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-B6B4E4 has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 30, 0, '2025-05-09 02:17:43'),
(281, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-DFF81A has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-14', 'booking', 'fas fa-calendar-check', 31, 0, '2025-05-09 02:30:37'),
(282, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-E944CD has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-10', 'booking', 'fas fa-calendar-check', 32, 0, '2025-05-09 02:31:28'),
(283, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-ACC109 has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-10', 'booking', 'fas fa-calendar-check', 33, 0, '2025-05-09 02:36:51'),
(284, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-67D38A has been confirmed. Check-in date: 2025-05-09, Check-out date: 2025-05-11', 'booking', 'fas fa-calendar-check', 34, 0, '2025-05-09 05:07:08'),
(285, 38, 'New Order Placed', 'Order #3 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 3, 1, '2025-05-09 05:34:01'),
(286, 38, 'New Order Placed', 'Order #6 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 6, 1, '2025-05-09 10:44:56'),
(287, 38, 'New Booking Confirmation', 'Your booking #BK-20250510-462F2A has been confirmed. Check-in date: 2025-05-10, Check-out date: 2025-05-11', 'booking', 'fas fa-calendar-check', 35, 0, '2025-05-10 01:36:11'),
(288, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-442E86 has been confirmed. Check-in date: 2025-05-11, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 36, 0, '2025-05-11 07:49:05'),
(289, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-65EA5D has been confirmed. Check-in date: 2025-05-11, Check-out date: 2025-05-15', 'booking', 'fas fa-calendar-check', 1, 0, '2025-05-11 08:54:31'),
(290, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-09C811 has been confirmed. Check-in date: 2025-05-11, Check-out date: 2025-05-14', 'booking', 'fas fa-calendar-check', 2, 0, '2025-05-11 09:00:49'),
(291, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-CC8357 has been confirmed. Check-in date: 2025-05-11, Check-out date: 2025-05-12', 'booking', 'fas fa-calendar-check', 3, 0, '2025-05-11 09:10:15'),
(292, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-AADD5F has been confirmed. Check-in date: 2025-05-12, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 4, 0, '2025-05-11 11:30:06'),
(293, 38, 'New Booking Confirmation', 'Your booking #BK-20250511-AC437F has been confirmed. Check-in date: 2025-05-12, Check-out date: 2025-05-13', 'booking', 'fas fa-calendar-check', 5, 0, '2025-05-11 11:48:51'),
(294, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-2CDF9D has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 6, 1, '2025-05-14 13:25:14'),
(295, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-35116A has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 7, 1, '2025-05-14 13:29:56'),
(296, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-224DFA has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 1, 1, '2025-05-14 13:32:02'),
(297, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-128321 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 2, 1, '2025-05-14 13:37:31'),
(298, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-965995 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-14 13:43:55'),
(299, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-168953 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 4, 1, '2025-05-14 13:48:35'),
(300, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-2ADDD5 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 5, 1, '2025-05-14 13:54:32'),
(301, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-1072C9 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 6, 1, '2025-05-14 13:55:13'),
(302, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-541F8B has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 7, 1, '2025-05-14 13:56:01'),
(303, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-05AA07 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 8, 1, '2025-05-14 13:57:08'),
(304, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-E2596E has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 9, 1, '2025-05-14 13:59:08'),
(305, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-3A13F0 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 10, 1, '2025-05-14 13:59:53'),
(306, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-78C417 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 11, 1, '2025-05-14 14:03:48'),
(307, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-9B10F5 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 12, 1, '2025-05-14 14:11:44'),
(308, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-DDB161 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 13, 1, '2025-05-14 14:17:06'),
(309, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-DA09EB has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 14, 1, '2025-05-14 14:22:52'),
(310, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-DB1BF4 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 15, 1, '2025-05-14 14:28:36'),
(311, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-727DE2 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 16, 1, '2025-05-14 14:31:49'),
(312, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-F6403F has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 17, 1, '2025-05-14 14:40:01'),
(313, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-727331 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 18, 1, '2025-05-14 14:40:32'),
(314, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-8716FB has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-27', 'booking', 'fas fa-calendar-check', 19, 1, '2025-05-14 14:41:29'),
(315, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-883AE3 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 20, 1, '2025-05-14 14:48:19'),
(316, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-950013 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 21, 1, '2025-05-14 14:50:21'),
(317, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-A05C6C has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 22, 1, '2025-05-14 14:55:16'),
(318, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-D4CBC4 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-24', 'booking', 'fas fa-calendar-check', 23, 1, '2025-05-14 14:58:22'),
(319, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-1F8B82 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 24, 1, '2025-05-14 14:59:31'),
(320, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-3C4076 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 25, 1, '2025-05-14 15:00:41'),
(321, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-318D0D has been confirmed. Check-in date: 2025-05-14, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 26, 1, '2025-05-14 15:04:00'),
(322, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-BFBD61 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 27, 1, '2025-05-14 15:19:23'),
(323, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-4633E3 has been confirmed. Check-in date: 2025-05-17, Check-out date: 2025-05-27', 'booking', 'fas fa-calendar-check', 28, 1, '2025-05-14 15:21:14'),
(324, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-C32AF9 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-27', 'booking', 'fas fa-calendar-check', 29, 1, '2025-05-14 15:21:56'),
(325, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-08FFCC has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-17', 'booking', 'fas fa-calendar-check', 30, 1, '2025-05-14 15:26:03'),
(326, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-4B5C97 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 31, 1, '2025-05-14 15:29:39'),
(327, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-7D7766 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 32, 1, '2025-05-14 15:32:28'),
(328, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-AB167E has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 33, 1, '2025-05-14 15:34:05'),
(329, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-4A65CB has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-27', 'booking', 'fas fa-calendar-check', 34, 1, '2025-05-14 15:53:20'),
(330, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-E87E54 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 35, 1, '2025-05-14 15:58:13'),
(331, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-23F304 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 36, 1, '2025-05-14 16:05:24'),
(332, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-E6C1E5 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 37, 1, '2025-05-14 16:06:35'),
(333, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-14F4E0 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 38, 1, '2025-05-14 16:07:12'),
(334, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-053D4C has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 39, 1, '2025-05-14 16:17:46'),
(335, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-A4A344 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 40, 1, '2025-05-14 16:25:25'),
(336, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-3B8E9B has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 1, 1, '2025-05-14 16:29:31'),
(337, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-697D58 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 2, 1, '2025-05-14 16:30:31'),
(338, 1, 'New Booking Confirmation', 'Your booking #BK-20250514-2E8E8E has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-14 16:31:24'),
(339, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-0AB923 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 4, 0, '2025-05-15 06:05:59'),
(340, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-47FCDC has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 5, 0, '2025-05-15 06:06:45'),
(341, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-D35654 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 6, 0, '2025-05-15 06:10:36'),
(342, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-ABBA93 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 7, 0, '2025-05-15 06:14:55'),
(343, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-2EE377 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 8, 0, '2025-05-15 06:34:20'),
(344, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-7C01D0 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-17', 'booking', 'fas fa-calendar-check', 9, 0, '2025-05-15 06:55:36'),
(345, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-2C6BF4 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 10, 0, '2025-05-15 07:01:21'),
(346, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-10FFF9 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 11, 0, '2025-05-15 07:17:22'),
(347, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-DB5120 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-17', 'booking', 'fas fa-calendar-check', 12, 0, '2025-05-15 07:19:06'),
(348, 38, 'New Booking Confirmation', 'Your booking #BK-20250515-E43384 has been confirmed. Check-in date: 2025-05-15, Check-out date: 2025-05-16', 'booking', 'fas fa-calendar-check', 13, 0, '2025-05-15 07:21:18'),
(349, 3, 'New Order Placed', 'Order #13 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 13, 1, '2025-05-15 07:09:45'),
(350, 3, 'New Order Placed', 'Order #15 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 15, 1, '2025-05-15 07:43:16'),
(351, 1, 'New Booking Confirmation', 'Your booking #BK-20250517-730583 has been confirmed. Check-in date: 2025-05-17, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 14, 1, '2025-05-17 15:45:29'),
(352, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-EA8EBF has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 15, 1, '2025-05-18 06:24:00'),
(353, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-3FCCF3 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 16, 1, '2025-05-18 06:29:03'),
(354, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-DFB9F5 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 17, 1, '2025-05-18 06:30:04'),
(355, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-CAB9BA has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 18, 1, '2025-05-18 06:31:49'),
(356, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-ECFAE4 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 19, 1, '2025-05-18 06:32:45'),
(357, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-C96EAC has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 20, 1, '2025-05-18 06:34:34'),
(358, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-FF5C0A has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 21, 1, '2025-05-18 06:40:20'),
(359, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-576050 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 22, 1, '2025-05-18 13:09:42'),
(360, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-317018 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 23, 1, '2025-05-18 13:13:44'),
(361, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-C24A2A has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 24, 1, '2025-05-18 13:15:57'),
(362, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-E30E24 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 25, 1, '2025-05-18 13:17:06'),
(363, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-66ED84 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-29', 'booking', 'fas fa-calendar-check', 26, 1, '2025-05-18 13:18:18'),
(364, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-01D19A has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 27, 1, '2025-05-18 13:28:55'),
(365, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-F13DF0 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 28, 1, '2025-05-18 13:30:29'),
(366, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-A67945 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 29, 1, '2025-05-18 13:32:54'),
(367, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-D17675 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 30, 1, '2025-05-18 13:47:42'),
(368, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-F25C29 has been confirmed. Check-in date: 2025-05-20, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 31, 1, '2025-05-18 13:49:06'),
(369, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-0A51D6 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 32, 1, '2025-05-18 13:51:30'),
(370, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-70063A has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 33, 1, '2025-05-18 14:02:20'),
(371, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-22870C has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 34, 1, '2025-05-18 14:12:33'),
(372, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-2ADE8C has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 35, 1, '2025-05-18 14:16:45'),
(373, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-AD3E95 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 36, 1, '2025-05-18 14:32:50'),
(374, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-77E039 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-30', 'booking', 'fas fa-calendar-check', 37, 1, '2025-05-18 14:45:23'),
(375, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-616590 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 38, 1, '2025-05-18 14:48:50'),
(376, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-446A86 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 39, 1, '2025-05-18 14:50:39'),
(377, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-4AFF57 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 40, 1, '2025-05-18 14:53:26'),
(378, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-D88DF7 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 41, 1, '2025-05-18 14:54:04'),
(379, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-71484E has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 42, 1, '2025-05-18 14:58:04'),
(380, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-C4356B has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 1, 1, '2025-05-18 15:22:26'),
(381, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-B8E11C has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 2, 1, '2025-05-18 15:27:30'),
(382, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-2D137C has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-18 15:45:19'),
(383, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-6DBCF5 has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 4, 1, '2025-05-18 15:53:30'),
(384, 1, 'New Booking Confirmation', 'Your booking #BK-20250518-8621FB has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 5, 1, '2025-05-18 15:58:22'),
(385, 39, 'New Booking Confirmation', 'Your booking #BK-20250528-FA41B8 has been confirmed. Check-in date: 2025-05-29, Check-out date: 2025-05-30', 'booking', 'fas fa-calendar-check', 8, 1, '2025-05-28 19:13:54'),
(386, 39, 'New Order Placed', 'Order #9 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 9, 1, '2025-05-29 11:36:18'),
(387, 39, 'New Order Placed', 'Order #10 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 10, 1, '2025-05-29 11:40:22'),
(388, 39, 'New Order Placed', 'Order #14 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 14, 1, '2025-06-01 08:46:02'),
(389, 39, 'New Order Placed', 'Order #15 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 15, 1, '2025-06-01 08:50:48'),
(390, 39, 'New Order Placed', 'Order #19 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 19, 1, '2025-06-01 14:11:33'),
(391, 39, 'New Order Placed', 'Order #23 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 23, 1, '2025-06-02 00:32:38'),
(392, 39, 'New Order Placed', 'Order #24 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 24, 1, '2025-06-02 01:43:42'),
(393, 39, 'New Order Placed', 'Order #27 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 27, 1, '2025-06-02 02:21:03'),
(394, 39, 'New Booking Confirmation', 'Your booking #BK-20250603-62871C has been confirmed. Check-in date: 2025-06-03, Check-out date: 2025-06-04', 'booking', 'fas fa-calendar-check', 9, 1, '2025-06-03 01:31:43');

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
(1, 'Weekend Getaway', 'images/offer_67f946a852068.jpg', '', 'Perfect weekend escape with breakfast included', 1, '2025-03-05 11:14:57', '2025-05-26 16:23:51'),
(2, 'Family', 'images/couple.jpg', '', 'Special rate for family stays with complimentary activities', 1, '2025-03-05 11:14:57', '2025-05-26 16:23:55'),
(3, 'Events', 'images/4.jpg', '', 'Stay longer, save more with our weekly rates', 1, '2025-03-05 11:14:57', '2025-05-31 16:06:56');

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
(1, 40, 0, 'christian realisan Christian Realisan realisan', 2147483647, '', 330.00, 330, 0, 0, 'regular', 'gcash', 'full', 'paid', 0.00, 'Completed', NULL, 330, '2025-06-12 07:38:33', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'regular', NULL),
(2, 40, 0, 'christian realisan Christian Realisan realisan', 2147483647, '', 300.00, 300, 0, 0, 'advance', 'gcash, cash', 'partial', 'Paid', 0.00, 'processing', '', 300, '2025-06-12 18:05:47', 'none', 0, '', NULL, '2025-06-13', NULL, NULL, 1, NULL, 'Table 8', 'advance', 'Cashier Realisan'),
(3, 40, 0, 'christian realisan Christian Realisan realisan', 2147483647, '', 300.00, 150, 0, 0, 'advance', 'gcash', 'partial', 'paid', 150.00, 'Pending', NULL, 300, '2025-06-12 18:15:09', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'advance', NULL),
(4, 40, 0, 'christian realisan Christian Realisan realisan', 2147483647, '', 180.00, 90, 0, 0, 'advance', 'gcash', 'partial', 'paid', 90.00, 'Pending', NULL, 180, '2025-06-12 23:32:28', 'none', 0, '', NULL, '0000-00-00', NULL, NULL, 1, NULL, 'N/A', 'advance', NULL),
(5, 3, 5, '', 0, '', 360.00, 400, 40, 0, 'walk-in', 'gcash', '', '', 0.00, 'finished', NULL, 0, '2025-06-12 23:50:09', 'none', 0, '', '2025-06-13 08:07:29', '2025-06-13', NULL, NULL, 0, NULL, 'Table 5', 'dine-in', NULL);

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
(1, 1, 'Carbonara', 1, 120.00),
(2, 1, 'Chicken Wings', 1, 180.00),
(3, 2, 'Carbonara', 1, 120.00),
(4, 2, 'Chicken Wings', 1, 180.00),
(5, 3, 'Carbonara', 1, 120.00),
(6, 3, 'Chicken Wings', 1, 180.00),
(7, 4, 'Chicken Wings', 1, 180.00),
(8, 5, 'Chicken Wings', 2, 180.00);

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
(2, 2, 'Extra Ranchs', 30.00);

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

--
-- Dumping data for table `order_payments`
--

INSERT INTO `order_payments` (`payment_id`, `order_id`, `amount`, `payment_method`, `payment_date`) VALUES
(1, 1, 80.00, 'maya', '2025-04-11 22:12:24'),
(2, 4, 95.00, 'cash', '2025-04-11 22:14:07'),
(0, 45, 90.00, 'gcash', '2025-05-01 01:03:05'),
(0, 4, 80.00, 'maya', '2025-05-01 12:03:12'),
(0, 42, 60.00, 'cash', '2025-05-02 20:52:06'),
(0, 46, 240.00, 'bank', '2025-05-03 09:04:31'),
(0, 47, 90.00, 'maya', '2025-05-03 09:06:05'),
(0, 50, 90.00, 'cash', '2025-05-03 23:17:11'),
(0, 49, 90.00, 'gcash', '2025-05-03 23:31:51'),
(0, 14, 240.00, 'cash', '2025-05-08 12:13:13'),
(0, 14, 9.00, 'cash', '2025-05-08 12:13:43'),
(0, 14, 1.00, 'gcash', '2025-05-08 12:13:55'),
(0, 9, 135.00, 'gcash', '2025-05-09 23:56:07'),
(0, 15, 80.00, 'cash', '2025-05-15 22:13:06'),
(0, 2, 90.00, 'cash', '2025-05-27 00:12:12'),
(0, 3, 90.00, 'gcash', '2025-06-01 01:59:54'),
(0, 9, 230.00, 'gcash', '2025-06-01 02:00:11'),
(0, 19, 60.00, 'maya', '2025-06-01 22:13:07'),
(0, 12, 90.00, 'maya', '2025-06-02 01:13:52'),
(0, 20, 90.00, 'cash', '2025-06-02 09:39:23'),
(0, 23, 90.00, 'cash', '2025-06-02 09:39:39'),
(0, 14, 170.00, 'cash', '2025-06-02 09:39:52'),
(0, 27, 150.00, 'cash', '2025-06-02 10:24:52'),
(0, 2, 150.00, 'cash', '2025-06-13 08:08:02');

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

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `rate`, `status`, `created_at`, `room_type_id`) VALUES
(1, '101', 'Double Occupancy', 3200.00, 'available', '2025-06-06 00:52:38', 1),
(2, '102', 'Family Suite', 5100.00, 'available', '2025-06-06 00:52:38', 2),
(3, '103', 'Deluxe', 4200.00, 'maintenance', '2025-06-06 00:52:38', 3);

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
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `room_number_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `floor_number` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`room_number_id`, `room_type_id`, `room_number`, `floor_number`, `status`, `created_at`, `updated_at`) VALUES
(3, 11, '103', 2, 'active', '2025-06-04 18:21:57', '2025-06-12 17:16:01'),
(4, 8, '104', NULL, 'active', '2025-06-04 18:23:53', '2025-06-06 00:57:37'),
(5, 8, '202', NULL, 'active', '2025-06-04 18:50:39', '2025-06-06 00:57:40'),
(6, 8, '203', 1, 'active', '2025-06-04 18:52:14', '2025-06-12 17:16:13'),
(7, 3, '301', NULL, 'active', '2025-06-04 19:03:57', '2025-06-05 15:05:18');

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
(0, 9, 39, 4.0, 'u', '2025-06-06 00:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `room_transfers`
--

CREATE TABLE `room_transfers` (
  `transfer_id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `old_room_type_id` varchar(50) NOT NULL,
  `new_room_type_id` varchar(50) NOT NULL,
  `transfer_reason` text NOT NULL,
  `price_difference` decimal(10,2) NOT NULL,
  `transfer_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_transfers`
--

INSERT INTO `room_transfers` (`transfer_id`, `booking_id`, `old_room_type_id`, `new_room_type_id`, `transfer_reason`, `price_difference`, `transfer_date`, `created_at`) VALUES
(1, '10', '8', '11', 'Transfer test', -3600.00, '2025-04-17 03:36:41', '2025-04-17 03:36:41'),
(2, '7', '8', '9', 'comfortability', -8000.00, '2025-04-21 08:42:48', '2025-04-21 08:42:48');

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
(3, 'Triple Occupancy', 4200.00, 3, 'Room', '1 Single Bed & 1 Queen Bed or 3 Single Beds', 4.4, 'room_3_1_1749748535.jpg', '/uploads/rooms/room_type_67f9739f87ef4_2.jpg', '/uploads/rooms/room_type_67f9739f8ba32_3.jpg', 0, NULL, 5, 'active'),
(8, 'Family', 5100.00, 4, 'Room', '2 Queen Beds', 4.5, 'room_8_1_1749748512.jpg', '/uploads/rooms/room_type_67f96e51c39c7_2.jpg', '/uploads/rooms/room_type_67f96e51c3ccd_3.jpg', 0, NULL, 4, ''),
(11, 'Double Occupancy', 3200.00, 3, 'Room', '2 Single Beds, 1 Queen bed', 3.7, 'room_11_1_1749748500.jpg', NULL, NULL, 0, NULL, 3, 'active');

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
  `downpayment_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `status` varchar(20) DEFAULT 'Pending',
  `package_type` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_option` varchar(50) DEFAULT NULL,
  `amount_to_pay` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `reservation_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_bookings`
--

INSERT INTO `table_bookings` (`id`, `user_id`, `package_name`, `contact_number`, `email_address`, `booking_date`, `booking_time`, `num_guests`, `special_requests`, `payment_method`, `total_amount`, `downpayment_amount`, `amount_paid`, `change_amount`, `payment_status`, `status`, `package_type`, `payment_reference`, `payment_proof`, `cancellation_reason`, `cancelled_at`, `created_at`, `payment_option`, `amount_to_pay`, `name`, `reservation_type`) VALUES
(1, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Confirmed', 'Standard', 'BOOK-68486E61DC5E7', NULL, NULL, NULL, '2025-06-10 17:41:53', 'partial', NULL, NULL, 'Online'),
(2, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Pending', 'Standard', 'BOOK-684882D67CC27', NULL, NULL, NULL, '2025-06-10 19:09:10', 'partial', NULL, NULL, 'Online'),
(3, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Pending', 'Standard', 'BOOK-6848830E94E2B', NULL, NULL, NULL, '2025-06-10 19:10:06', 'partial', NULL, NULL, 'Online'),
(4, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Pending', 'Standard', 'BOOK-6848834B6F71E', NULL, NULL, NULL, '2025-06-10 19:11:07', 'partial', NULL, NULL, 'Online'),
(5, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Pending', 'Standard', 'BOOK-684883A2C92CC', NULL, NULL, NULL, '2025-06-10 19:12:34', 'partial', NULL, NULL, 'Online'),
(6, 40, 'Package A', '', '', '2025-06-10', '16:04:00', 41, NULL, 'gcash ?payment=succe', 31000.00, 15500.00, 15500.00, 0.00, 'Paid', 'Pending', 'Standard', 'BOOK-684883F36B111', NULL, NULL, NULL, '2025-06-10 19:13:55', 'partial', NULL, NULL, 'Online');

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
  `table_number` int(11) NOT NULL,
  `status` enum('available','occupied') NOT NULL DEFAULT 'available',
  `occupied_at` timestamp NULL DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_number`
--

INSERT INTO `table_number` (`id`, `table_number`, `status`, `occupied_at`, `order_id`, `updated_at`) VALUES
(1, 1, 'available', NULL, NULL, '2025-05-29 14:33:48'),
(2, 2, 'occupied', '2025-06-02 02:25:31', 27, '2025-06-02 02:25:31'),
(3, 3, 'available', NULL, NULL, '2025-05-29 11:44:22'),
(4, 4, 'available', NULL, NULL, '2025-05-29 11:44:14'),
(5, 5, 'occupied', '2025-06-12 23:50:04', NULL, '2025-06-12 23:50:04'),
(6, 6, 'occupied', '2025-06-02 02:25:23', 25, '2025-06-02 02:25:23'),
(7, 7, 'occupied', '2025-06-02 02:25:27', 26, '2025-06-02 02:25:27'),
(8, 8, 'occupied', '2025-06-13 00:07:47', 2, '2025-06-13 00:07:47'),
(9, 9, 'available', NULL, NULL, '2025-06-02 02:15:07'),
(10, 10, 'available', NULL, NULL, '2025-06-02 02:15:12');

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
(1, 'Couples', 0, 5, 'Perfect for couples\r\n', '', 0, 'uploads/table_packages/6823f400cf4c2.jpg', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
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
(1, 'Alfred hendrik', 'Aceveda', 'admin@example.com', '09362715617', 'Balite Calapan City Oriental Mindoro', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', NULL, NULL, 'customer', NULL, NULL, NULL, 0, 'd3ab8c68d4af772a776130cb755d22611faafa41a9bc56af2b46aae42d220527', '2025-04-24 13:48:57'),
(2, NULL, NULL, 'frontdesk@example.com', NULL, NULL, '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', NULL, NULL, 'frontdesk', NULL, NULL, NULL, 0, NULL, NULL),
(3, 'Cashier', 'Realisan', 'cashier@example.com', '09123456789', 'TAwagan', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', NULL, 'uploads/profile/3.jpg', 'cashier', NULL, NULL, NULL, 1, '225874', '2025-05-31 21:24:35'),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$9Of5FaVHvCt/YsEnryDRnOjxkRE6oS1BhqvJnl/YJ4ZL4RnZo6sVK', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(6, 'Aizzy', 'Villanueva', 'aizzyvillanueva34@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$r1X5exzjzJcmM.v3uGBNXeXiN.QkoU1QOIYDIG.7UjmZG.qmxt0hy', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(7, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '912345678787', 'wawa calapan city', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(8, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '912345678787', 'tawagan', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', 'chanopassword', '681607ca1054f.jpg', 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(9, 'Aizzy', 'Villanueva', 'aizzyvillanueva5@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$6FJdPRpNRHB5rzVQ6L8EO.7xNMFRTQ.qS84uCvwkqB/nmB1aAx5Fy', '020104', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(10, 'Fammela', 'De Guzman', 'fammeladeguzman21@gmail.com', '09362715617', 'Wawa, Calapan City', '$2y$10$fo1HrZCUvSrEh8InzHzuoORab6vzOZayXnF2iLrmLQeBmye/mNSl.', 'fammelapassword', '../uploads/profile/profile_10_1746278895.png', 'cashier', NULL, NULL, NULL, 0, NULL, NULL),
(11, 'Alfred', 'Aceveda', 'alfredaceveda.3@gmail.com', '09363950698', NULL, '$2y$10$VtAD8.4Tl0ncmczJ5WgYcuB1sMQ1bU7TdjVDKTzZuC11MZRYoykny', NULL, NULL, '', NULL, '749192', '2025-04-14 09:50:14', 0, NULL, NULL),
(12, 'Alfred', 'Aceveda', 'cyvieshi@gmail.com', '09363950698', NULL, '$2y$10$HOX.EaHIlJlphxRlYhjs0OVQpDM.QdgAt.rCH7XaDc2zvZJE28d.m', NULL, NULL, '', NULL, '891266', '2025-04-14 09:51:28', 0, NULL, NULL),
(13, 'aizzy', 'villanueva', 'aizzy2004@gmail.com', '09127418448', NULL, '$2y$10$.VtKuRPB5t4v8XJ3pIe2Ye8Xq.JRF2nwv172DA0jz/dM7vYxU4J5u', NULL, NULL, '', NULL, '084580', '2025-04-14 10:04:38', 0, NULL, NULL),
(14, 'aizzy', 'villanueva', 'macapagalkenjo@gmail.com', '09127418448', NULL, '$2y$10$5JiAVwo4MtryVBR3aGnBpeAlGiVErB8ay/cc7kGjSot6FB6XY/io.', NULL, NULL, '', NULL, '397271', '2025-04-14 10:05:25', 0, NULL, NULL),
(15, 'Try ', 'Me', 'akop35310@gmail.com', '09123456789', NULL, '$2y$10$4ydNXyV33pq4RswCLlZSpO5kGwhX/FiYzG/1KlkkQb3OCJ82x3XB6', NULL, NULL, 'customer', NULL, '145658', '2025-04-14 10:05:54', 0, NULL, NULL),
(16, 'Christian', 'Realisan', 'myraluceno@gmail.com', '09234567878', NULL, '$2y$10$8Xe899PsTnwiHbdEjVPJJuYJWP1bO8oK81pWIQcHo.xya7glCf4Z6', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(17, 'Christian', 'Realisan', 'enhymwaa@gmail.com', '09234567878', NULL, '$2y$10$HAPeuFiTcMK48yQ/ZHbRrORn8xBMrLUfwBd2wc8B6H.JvVAe16OKq', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(18, 'Fammela ', 'De Guzman ', 'mystery.woman1242@gmail.com', '09951779220', NULL, '$2y$10$fbPxCZiKhXdP9nPYQYN7jeBDGFVowdvBZ6LBTS9IWbSLm4Eda5VZ2', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(30, 'Lab', 'Mo', 'christianrealisan25@gmail.com', '09123456799', NULL, '$2y$10$dLY3pYMuNR.NjqljNfbnoe3UEBUviSwMhstRFPmaaAad6l3vkk1wW', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(31, 'Lab', 'Mo', 'christianrealisan3@gmail.com', '09123456799', NULL, '$2y$10$q7nJc4vJVZHpHThMmQLUX.xdo8MlOsOonVZeivevY6q9rdKTt9OGS', NULL, NULL, 'customer', NULL, NULL, NULL, 0, '124413', '2025-05-11 09:06:24'),
(32, 'Poldo', 'Almoguera', 'poldorivera07@gmail.com', '09937167503', NULL, '$2y$10$jccQt4zE6XpLLyLjofHVJu4N4FIdk4Q/cVaJF7f5jm4.VjL7wMR2a', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(33, 'Fammela', 'De Guzman', 'mysterywoman1242@gmail.com', '09363960987', NULL, '$2y$10$NK5wJrgzRWWcic//rxwiQeqZec2gSUBEee4CusxokGWd9xmw1rzxu', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(34, 'Christian', 'Realisan', 'chano@gmail.com', '09123456789', NULL, '$2y$10$ibcgOUmMfiJlMiaYfT5fP.N5WVpQozypiPi7cTtl6H5DBJuypmTSu', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(35, 'Myra Kristine Grace ', 'Luceño', 'myraluceno59@gmail.com', '09638322673', NULL, '$2y$10$aB1qDp6yq48CThqcCG2.S.9JtJFmkAA75eqL8/bFUXi9sMSiGJwZq', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(36, 'Myra', 'Aceveda', 'myra2006@gmail.com', '09638322673', NULL, '$2y$10$PWhwxvCUIqKngu7Vr3unQ.lHzYNSbO9mIQ8dvF9CZ.KPalEIX9.UG', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(40, 'christian realisan Christian Realisan', 'realisan', 'chanomabalo@gmail.com', '09124343343', NULL, '$2y$10$9NAc1tYNKs/dyzPvRqBuhu7sI51uMmW2xu0zR318t./NU2zBCDR0i', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL);

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
(2, 'chanomabalo@gmail.com', '328548', '2025-04-12 21:05:07', 0, '2025-04-12 07:50:07');

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
(1, 'SMS', 1, NULL, '2025-04-11 09:47:29'),
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
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_bookings_dates` (`check_in`,`check_out`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `cashier`
--
ALTER TABLE `cashier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`transfer_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`room_type_id`);

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
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `cashier`
--
ALTER TABLE `cashier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_packages`
--
ALTER TABLE `event_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fix_booking_ids_log`
--
ALTER TABLE `fix_booking_ids_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

--
-- AUTO_INCREMENT for table `menu_items_addons`
--
ALTER TABLE `menu_items_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=395;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `resetpass`
--
ALTER TABLE `resetpass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `room_number_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room_transfers`
--
ALTER TABLE `room_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `table_bookings`
--
ALTER TABLE `table_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `table_number`
--
ALTER TABLE `table_number`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `table_packages`
--
ALTER TABLE `table_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
