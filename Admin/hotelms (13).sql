-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 07:10 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.4

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `title`, `description`, `last_updated`) VALUES
(2, 'About Us', 'abnoy', '2025-04-11 22:45:10');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Table structure for table `admin_status`
--

CREATE TABLE `admin_status` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `advance_orders`
--

INSERT INTO `advance_orders` (`id`, `booking_id`, `menu_item_id`, `quantity`, `price`, `created_at`) VALUES
(1, 10, 1, 1, '120.00', '2025-02-17 16:48:09'),
(2, 11, 1, 1, '120.00', '2025-02-17 17:03:53'),
(3, 13, 1, 1, '120.00', '2025-02-17 22:36:45'),
(4, 14, 1, 1, '120.00', '2025-02-18 06:15:17'),
(5, 14, 2, 1, '150.00', '2025-02-18 06:15:17'),
(6, 14, 3, 1, '180.00', '2025-02-18 06:15:17'),
(7, 15, 1, 1, '120.00', '2025-02-18 11:46:08'),
(8, 16, 1, 1, '120.00', '2025-02-18 12:54:14'),
(9, 16, 2, 1, '150.00', '2025-02-18 12:54:14'),
(10, 16, 3, 1, '180.00', '2025-02-18 12:54:14'),
(11, 16, 8, 1, '270.00', '2025-02-18 12:54:14'),
(12, 17, 1, 3, '120.00', '2025-03-18 02:52:29'),
(13, 17, 424, 1, '270.00', '2025-03-18 02:52:29'),
(14, 6, 3, 1, '0.00', '2025-04-09 16:48:53'),
(15, 8, 3, 1, '0.00', '2025-04-09 16:55:34'),
(16, 9, 3, 2, '0.00', '2025-04-09 16:59:46'),
(17, 11, 3, 1, '0.00', '2025-04-09 17:22:28'),
(18, 12, 3, 1, '0.00', '2025-04-09 17:30:07'),
(19, 18, 3, 1, '0.00', '2025-04-10 13:59:22');

-- --------------------------------------------------------

--
-- Table structure for table `advance_order_addons`
--

CREATE TABLE `advance_order_addons` (
  `id` int(11) NOT NULL,
  `advance_order_id` int(11) NOT NULL,
  `addon_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `total_amount` decimal(10,2) DEFAULT NULL,
  `extra_charges` decimal(10,2) DEFAULT 0.00 COMMENT 'Extra charges for additional guests or services',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT NULL,
  `remaining_balance` decimal(10,2) DEFAULT NULL,
  `discount_type` varchar(50) DEFAULT NULL COMMENT 'Type of discount applied (e.g., Senior, PWD, Student, Promo)',
  `discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Amount of discount applied',
  `discount_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage of discount if applicable',
  `payment_reference` varchar(50) NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `user_types` enum('admin','frontdesk') NOT NULL DEFAULT 'frontdesk'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_extensions`
--

INSERT INTO `booking_extensions` (`id`, `booking_id`, `original_checkout`, `new_checkout`, `days_extended`, `additional_cost`, `payment_method`, `extension_date`) VALUES
(1, 132, '2025-03-28', '2025-03-30', 2, '2500.00', 'GCash', '2025-03-27 13:45:05'),
(2, 132, '2025-03-30', '2025-04-01', 2, '2500.00', 'Cash', '2025-03-27 13:46:02'),
(3, 100, '2025-02-20', '2025-02-22', 2, '7400.00', 'Cash', '2025-03-27 18:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `booking_list`
--

CREATE TABLE `booking_list` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `checked_in`
--

INSERT INTO `checked_in` (`id`, `first_name`, `last_name`, `contact_number`, `email`, `room_type_id`, `room_type`, `check_in_date`, `check_out_date`, `nights_staying`, `number_of_guests`, `special_requests`, `payment_method`, `total_amount`, `status`, `created_at`) VALUES
(7, 'Alleah', 'basta', '91234567878', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', '1250.00', 'active', '2025-04-05 13:11:37'),
(8, 'Alleah', 'basta', '91234567878', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', '1250.00', 'active', '2025-04-05 13:12:42'),
(9, 'myra', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-05', '2025-04-06', 1, 1, '', 'Cash', '1250.00', 'active', '2025-04-05 13:14:28'),
(10, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 1, 'Standard Double Room', '2025-04-07', '2025-04-08', 1, 1, '', 'Cash', '3700.00', 'active', '2025-04-05 16:24:32'),
(11, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 2, 'Deluxe Family Room', '2025-04-07', '2025-04-14', 7, 1, '', 'Cash', '14000.00', 'active', '2025-04-05 16:28:42'),
(12, 'Alleah', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 2, 'Deluxe Family Room', '2025-04-07', '2025-04-14', 7, 1, '', 'Cash', '14000.00', 'active', '2025-04-05 16:31:46'),
(13, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-12', '2025-04-14', 2, 1, '', 'Cash', '2500.00', 'active', '2025-04-05 17:57:13'),
(14, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-06', '2025-04-06', 0, 1, '', 'Cash', '0.00', 'active', '2025-04-05 19:14:16'),
(15, 'Alleaha', 'luceno', '0999999999', 'christianrealisan45@gmail.com', 3, 'Family Room', '2025-04-06', '2025-04-06', 0, 1, '', 'Cash', '0.00', 'active', '2025-04-05 19:19:41');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(7, 'Christian', 'Realisan', 'christianrealisan45@gmail.com', 'hi', 'new', '2025-04-11 09:32:59', '2025-04-11 09:32:59');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'John Doe', 'john@email.com', '1234567890', '2025-04-05 12:25:39'),
(2, 'Jane Smith', 'jane@email.com', '2345678901', '2025-04-05 12:25:39'),
(3, 'Mike Johnson', 'mike@email.com', '3456789012', '2025-04-05 12:25:39'),
(4, 'Sarah Williams', 'sarah@email.com', '4567890123', '2025-04-05 12:25:39'),
(5, 'Robert Brown', 'robert@email.com', '5678901234', '2025-04-05 12:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `daily_occupancy`
--

CREATE TABLE `daily_occupancy` (
  `date` date NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `occupied_rooms` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `daily_revenue`
--

INSERT INTO `daily_revenue` (`date`, `total_amount`, `booking_count`, `created_at`) VALUES
('2025-04-08', '117000.00', 4, '2025-04-07 19:49:51'),
('2025-04-09', '29250.00', 1, '2025-04-08 19:24:08'),
('2025-04-09', '500000.00', 1, '2025-04-09 12:51:09'),
('2025-04-09', '220000.00', 1, '2025-04-09 12:53:50'),
('2025-04-09', '236000.00', 2, '2025-04-09 13:58:08'),
('2025-04-10', '40000.00', 1, '2025-04-10 15:13:15'),
('2025-04-10', '80000.00', 2, '2025-04-10 15:29:08');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dining_tables`
--

INSERT INTO `dining_tables` (`id`, `table_name`, `table_type`, `category`, `capacity`, `price`, `status`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Package D', 'Family', 'ultimate', 48, '70000.00', 'available', 'uploads/tables/67a862a913c4b.jpg', '2025-02-09 08:09:13', '2025-02-09 08:09:13'),
(2, 'Family Table', 'Family', 'regular', 7, '10000.00', 'available', 'uploads/tables/67a862e402fde.jpg', '2025-02-09 08:10:12', '2025-02-09 08:10:12'),
(3, 'Package D', 'Family', 'ultimate', 12, '100000.00', 'available', 'uploads/tables/67aa27b98c995.jpg', '2025-02-10 16:22:17', '2025-02-10 16:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `disable_reasons`
--

CREATE TABLE `disable_reasons` (
  `id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `discount_types`
--

INSERT INTO `discount_types` (`id`, `name`, `percentage`, `description`, `is_active`, `created_at`) VALUES
(1, 'senior', '10.00', 'Senior Citizen Discount', 1, '2025-04-13 02:58:18'),
(2, 'pwd', '10.00', 'Person with Disability Discount', 1, '2025-04-13 02:58:18'),
(3, 'student', '10.00', 'Student Discount', 1, '2025-04-13 02:58:18');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` varchar(20) NOT NULL,
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
  `reference_number` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `reserve_type` varchar(50) DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_source` varchar(50) DEFAULT 'Regular Booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event_packages`
--

INSERT INTO `event_packages` (`id`, `name`, `price`, `description`, `image_path`, `image_path2`, `image_path3`, `max_guests`, `duration`, `created_at`, `is_available`, `menu_items`, `max_pax`, `time_limit`, `notes`, `status`) VALUES
(1, 'Venue Rental Only', '20000.00', '5-hour venue rental\nTables and Tiffany chairs', 'images/hall.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, NULL, 50, '5 hours', NULL, 'Available'),
(2, 'Package A', '47500.00', '5-hour venue rental      Tables     and Tiffany chairs', 'images/hall.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 0, '1 Appetizers, 2 Pasta, 2 Mains, Salad Bar, Rice , Drinks', 50, '5 hours', NULL, ''),
(3, 'Package B', '55000.00', '5-hour venue rental\nTables and  Tiffany chairs', 'images/hall2.jpg', 'images/hall.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, ' 2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert,  Drinks ', 50, '5 hours', '**Assumes 5,000g (100g per person) of Wagyu steak will be served.', 'Available'),
(4, 'Package C', '76800.00', '5-hour venue rental\nTables and Tiffany chairs', 'images/hall3.jpg', 'images/hall2.jpg', 'images/hall.jpg', 30, 5, '2025-02-12 02:48:46', 1, '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2desserts, Drinks ', 50, '5 hours', NULL, 'Available');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(28, 3, 'hotdog ko', 'check', 4, 1, '2025-04-09 11:13:58', '2025-04-09 11:13:58'),
(29, 4, 'Kiss', 'check', 4, 1, '2025-04-09 13:55:56', '2025-04-09 13:55:56');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Stand-in structure for view `featured_rooms_view`
-- (See below for the actual view)
--
CREATE TABLE `featured_rooms_view` (
`id` int(11)
,`room_type` varchar(100)
,`start_date` date
,`end_date` date
,`created_at` timestamp
,`image_path` varchar(255)
);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(337, 412, 'hjkhkhjk', 'gjkghk', 'regular', '', NULL, '2025-04-08 04:22:42'),
(338, 413, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-09 12:51:09'),
(339, 1, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-09 12:53:50'),
(340, 362415, 'Alfred', 'Aceveda', '', '12345678', NULL, '2025-04-09 13:58:08'),
(341, 362421, 'Aa', 'aa', 'regular', '', NULL, '2025-04-10 15:13:15'),
(342, 362426, 'Aa', 'aa', 'regular', '', NULL, '2025-04-10 15:29:08'),
(343, 1, 'Alfred', 'Aceveda', 'pwd', '12345678', NULL, '2025-04-10 15:38:33'),
(344, 2, 'Alfred', 'Aceveda', 'pwd', '12345678', NULL, '2025-04-10 15:42:18'),
(345, 3, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 15:46:53'),
(346, 4, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 15:51:19'),
(347, 5, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 15:53:49'),
(348, 6, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:00:36'),
(349, 7, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:04:09'),
(350, 8, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:09:39'),
(351, 9, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:15:33'),
(352, 10, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:24:27'),
(353, 11, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:28:12'),
(354, 12, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 16:32:43'),
(355, 14, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 17:18:50'),
(356, 1, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 18:01:14'),
(357, 2, 'Alfred', 'Aceveda', 'pwd', '12345678', NULL, '2025-04-10 18:20:40'),
(358, 9, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 19:15:34'),
(359, 10, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 19:21:27'),
(360, 1, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 20:15:02'),
(361, 3, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 21:37:05'),
(362, 4, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 22:01:51'),
(363, 5, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 22:10:26'),
(364, 6, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 22:14:28'),
(365, 8, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-10 22:38:41'),
(366, 9, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-11 06:37:40'),
(367, 10, 'patrick', 'chua', '', '', NULL, '2025-04-11 06:50:20'),
(368, 2, 'patrick', 'chua', 'regular', '', NULL, '2025-04-11 06:56:39'),
(369, 1, 'patrick', 'chua', 'regular', '', NULL, '2025-04-11 08:52:22'),
(370, 3, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-11 12:21:09'),
(371, 4, 'Alfred', 'Aceveda', 'regular', '', NULL, '2025-04-11 12:31:59'),
(372, 2, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:09:40'),
(373, 3, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:19:51'),
(374, 4, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:23:11'),
(375, 5, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:28:23'),
(376, 6, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:30:09'),
(377, 7, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 02:32:01'),
(378, 8, 'Christian', 'Realisan', 'regular', '', NULL, '2025-04-12 05:04:52'),
(379, 1, 'christiandfdfgg', 'Realisan', 'regular', '', NULL, '2025-04-12 05:06:25'),
(380, 2, 'christiandfdfgg', 'realisan', 'regular', '', NULL, '2025-04-12 06:18:43'),
(381, 3, 'christiandfdfgg', 'realisan', 'regular', '', NULL, '2025-04-12 06:22:42'),
(382, 4, 'casa', 'estela', 'regular', '', NULL, '2025-04-12 22:54:46'),
(383, 5, 'casa', 'estela', 'regular', '', NULL, '2025-04-12 23:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_policies`
--

CREATE TABLE `hotel_policies` (
  `id` int(11) NOT NULL,
  `policy_type` varchar(50) NOT NULL,
  `policy_content` text NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `id_card_type`
--

CREATE TABLE `id_card_type` (
  `id_card_type_id` int(10) NOT NULL,
  `id_card_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `category`, `price`) VALUES
(1, 'Americano', 'Beverages', '120.00'),
(2, 'Cappuccino', 'Beverages', '140.00'),
(3, 'Club Sandwich', 'Food', '180.00'),
(4, 'Caesar Salad', 'Food', '220.00'),
(5, 'Chocolate Cake', 'Desserts', '150.00'),
(6, 'Americano', 'Beverages', '120.00'),
(7, 'Cappuccino', 'Beverages', '140.00'),
(8, 'Club Sandwich', 'Food', '180.00'),
(9, 'Caesar Salad', 'Food', '220.00'),
(10, 'Chocolate Cake', 'Desserts', '150.00'),
(11, 'Americano', 'Beverages', '120.00'),
(12, 'Cappuccino', 'Beverages', '140.00'),
(13, 'Club Sandwich', 'Food', '180.00'),
(14, 'Caesar Salad', 'Food', '220.00'),
(15, 'Chocolate Cake', 'Desserts', '150.00'),
(16, 'Americano', 'Beverages', '120.00'),
(17, 'Cappuccino', 'Beverages', '140.00'),
(18, 'Club Sandwich', 'Food', '180.00'),
(19, 'Caesar Salad', 'Food', '220.00'),
(20, 'Chocolate Cake', 'Desserts', '150.00'),
(21, 'Americano', 'Beverages', '120.00'),
(22, 'Cappuccino', 'Beverages', '140.00'),
(23, 'Club Sandwich', 'Food', '180.00'),
(24, 'Caesar Salad', 'Food', '220.00'),
(25, 'Chocolate Cake', 'Desserts', '150.00');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `location_info`
--

INSERT INTO `location_info` (`id`, `address`, `latitude`, `longitude`, `map_zoom_level`, `contact_phone`, `contact_email`, `last_updated`) VALUES
(1, 'Casa Estela Boutique Hotel & Cafe, Calapan City, Oriental Mindoro', '13.41454500', '121.18380200', 15, '+63 XXX XXX XXXX', 'info@casaestela.com', '2025-04-11 21:31:45');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `maintenance_settings`
--

INSERT INTO `maintenance_settings` (`id`, `is_enabled`, `start_time`, `end_time`, `message`, `allowed_ips`, `last_updated`) VALUES
(1, 1, '2025-04-13 08:05:38', '2025-04-13 08:10:38', 'Scheduled maintenance in progress. We will be back online at 6:00 PM EST.', '127.0.0.1,192.168.1.1', '2025-04-13 00:03:39');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `price`, `image_path`) VALUES
(1, 1, 'Hand-cut Potato Fries', '160.00', 'uploads/menus/menu_67fb1c3483bf7.png'),
(2, 1, 'Mozzarella Stick', '150.00', 'uploads/menus/menu_67f927b196b54.jpg'),
(3, 1, 'Chicken Wings', '180.00', 'uploads/menus/menu_67f927bc54efc.jpg'),
(4, 2, 'Salad', '200.00', 'uploads/menus/menu_67fb19926baf0.png'),
(5, 2, 'Coconut Salad', '200.00', 'images/menu_67b41c8d869f9.jpg'),
(6, 3, 'Spaghetti', '300.00', 'images/menu_67b427110a801.jpg'),
(7, 4, 'Egg Sandwich', '500.00', ''),
(8, 1, 'Spaghetti maccaroni', '270.00', 'images/menu_67b4289215e15.jpg'),
(431, 6, 'Matcha', '180.00', ''),
(432, 1, 'Carbonara', '120.00', '');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items_addons`
--

CREATE TABLE `menu_items_addons` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_items_addons`
--

INSERT INTO `menu_items_addons` (`id`, `menu_item_id`, `name`, `price`) VALUES
(0, 1, 'HAtdog', '15.00'),
(0, 1, 'cheese', '20.00'),
(0, 10, 'Gravy', '20.00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_addons`
--

CREATE TABLE `menu_item_addons` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_item_addons`
--

INSERT INTO `menu_item_addons` (`id`, `menu_item_id`, `name`, `price`) VALUES
(1, 1, 'Cheese', '30.00'),
(2, 1, 'Mayo', '50.00'),
(3, 2, 'Extra Sauce', '20.00'),
(4, 2, 'Extra Mozzarella', '40.00'),
(5, 3, 'Buffalo Sauce', '25.00'),
(6, 3, 'Extra Ranch', '30.00'),
(7, 1, 'Extra Sauce', '20.00'),
(8, 1, 'Extra Cheese', '30.00'),
(9, 2, 'Extra Spicy', '15.00'),
(10, 2, 'Extra Rice', '25.00');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `sender_type`, `read_status`, `created_at`) VALUES
(1, 5, 'open pa po kayo ?', 'user', 0, '2025-04-12 06:31:08'),
(2, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:31:08'),
(3, 5, 'book', 'user', 0, '2025-04-12 06:34:16'),
(4, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:34:16'),
(5, 5, 'book', 'user', 0, '2025-04-12 06:35:58'),
(6, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:35:58'),
(7, 33, 'open pa po kayo ?', 'user', 0, '2025-04-12 09:54:24'),
(8, 33, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 09:54:24'),
(9, 31, 'open pa po kayo ?', 'user', 0, '2025-04-12 10:11:04'),
(10, 31, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 10:11:04'),
(11, 1, 'ay hoy', 'user', 0, '2025-04-13 01:57:28'),
(12, 1, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-04-13 01:57:28');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(110, 1, 'New Booking Confirmation', 'Your booking #BK-20250413-747C66 has been confirmed. Check-in date: 2025-04-17, Check-out date: 2025-04-18', 'booking', 'fas fa-calendar-check', 5, 0, '2025-04-12 23:49:04'),
(111, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 0, '2025-04-13 00:48:47'),
(112, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 0, '2025-04-13 00:54:21'),
(113, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 0, '2025-04-13 02:18:09'),
(114, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 0, '2025-04-13 02:18:41');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `image`, `discount`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Weekend Getaway', 'images/offer_67f946a852068.jpg', '0% OFF', 'Perfect weekend escape with breakfast included', 1, '2025-03-05 11:14:57', '2025-04-11 16:44:11'),
(2, 'Family', 'images/couple.jpg', '100% OFF', 'Special rate for family stays with complimentary activities', 1, '2025-03-05 11:14:57', '2025-04-11 16:43:43'),
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
  `completed_at` datetime DEFAULT NULL,
  `updated_at` date NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `quantity`, `unit_price`) VALUES
(74, 70, 'Hand-cut Potato Fries', 1, '120.00'),
(75, 71, 'Hand-cut Potato Fries', 1, '120.00'),
(76, 72, 'Mozzarella Stick', 1, '150.00'),
(77, 73, 'Mozzarella Stick', 4, '150.00'),
(78, 74, 'Hand-cut Potato Fries', 2, '120.00'),
(79, 75, 'Mozzarella Stick', 1, '150.00'),
(80, 76, 'Mozzarella Stick', 2, '150.00'),
(81, 77, 'Hand-cut Potato Fries', 1, '120.00'),
(82, 77, 'Mozzarella Stick', 1, '150.00'),
(83, 77, 'Chicken Wings', 1, '180.00'),
(84, 77, 'Spaghetti maccaroni', 1, '270.00'),
(85, 78, 'Mozzarella Stick', 10, '150.00'),
(86, 79, 'Mozzarella Stick', 2, '150.00'),
(87, 80, 'Hand-cut Potato Fries', 1, '120.00'),
(88, 81, 'Hand-cut Potato Fries', 1, '120.00'),
(89, 74, 'Egg Sandwich - ₱500.00', 1, '500.00'),
(90, 82, 'Hand-cut Potato Fries', 2, '120.00'),
(91, 83, 'Mozzarella Stick', 2, '150.00'),
(92, 84, 'Spaghetti', 1, '300.00'),
(93, 85, 'Hand-cut Potato Fries', 1, '120.00'),
(95, 86, 'Hand-cut Potato Fries', 1, '120.00'),
(96, 87, 'Mozzarella Stick', 1, '150.00'),
(97, 88, 'Mozzarella Stick', 2, '150.00'),
(98, 89, 'Mozzarella Stick', 1, '150.00'),
(99, 90, 'Spaghetti', 1, '300.00'),
(100, 91, 'Egg Sandwich', 1, '500.00'),
(101, 92, 'Mozzarella Stick', 1, '150.00'),
(102, 93, 'Hand-cut Potato Fries', 1, '120.00'),
(103, 94, 'Mozzarella Stick', 1, '150.00'),
(104, 95, 'Hand-cut Potato Fries', 1, '120.00'),
(105, 96, 'Egg Sandwich', 1, '500.00'),
(106, 93, 'Salad - ₱200.00', 1, '200.00'),
(107, 97, 'Matcha', 1, '180.00'),
(108, 98, 'Hand-cut Potato Fries', 1, '160.00'),
(109, 98, 'Matcha', 1, '180.00'),
(110, 99, 'Chicken Wings', 1, '180.00'),
(111, 100, 'Hand-cut Potato Fries', 1, '160.00'),
(112, 101, 'Hand-cut Potato Fries', 1, '160.00'),
(113, 1, 'Hand-cut Potato Fries', 1, '160.00'),
(114, 1, 'Salad - ₱200.00', 1, '200.00'),
(115, 2, 'Hand-cut Potato Fries', 1, '160.00'),
(116, 2, 'Salad - ₱200.00', 1, '200.00'),
(117, 3, 'Hand-cut Potato Fries', 1, '160.00'),
(118, 4, 'Hand-cut Potato Fries', 1, '160.00'),
(119, 5, 'Hand-cut Potato Fries', 1, '160.00'),
(120, 6, 'Chicken Wings', 1, '180.00'),
(121, 1, 'Chicken Wings', 1, '180.00'),
(122, 2, 'Spaghetti maccaroni', 1, '270.00'),
(123, 1, 'Chicken Wings - ₱180.00', 1, '180.00'),
(124, 3, 'Chicken Wings', 1, '180.00'),
(125, 4, 'Chicken Wings', 1, '180.00'),
(126, 4, 'Mozzarella Stick - ₱150.00', 1, '150.00'),
(127, 4, 'Salad - ₱200.00', 1, '200.00'),
(128, 5, 'Mozzarella Stick', 1, '150.00'),
(129, 6, 'Chicken Wings', 1, '180.00'),
(130, 7, 'Spaghetti maccaroni', 1, '270.00'),
(131, 8, 'Chicken Wings', 1, '180.00'),
(132, 9, 'Mozzarella Stick', 1, '150.00'),
(133, 10, 'Mozzarella Stick', 1, '150.00'),
(134, 11, 'Mozzarella Stick', 1, '150.00'),
(135, 12, 'Mozzarella Stick', 1, '150.00'),
(136, 13, 'Spaghetti', 1, '300.00'),
(137, 14, 'Mozzarella Stick', 1, '150.00'),
(138, 15, 'Chicken Wings', 1, '180.00'),
(139, 16, 'Mozzarella Stick', 1, '150.00'),
(140, 17, 'Mozzarella Stick', 1, '150.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addons`
--

CREATE TABLE `order_item_addons` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `addon_name` varchar(100) NOT NULL,
  `addon_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_item_addons`
--

INSERT INTO `order_item_addons` (`id`, `order_item_id`, `addon_name`, `addon_price`) VALUES
(20, 74, 'Extra Sauce', '20.00'),
(21, 74, 'Extra Cheese', '30.00'),
(22, 75, 'Extra Sauce', '20.00'),
(23, 75, 'Extra Cheese', '30.00'),
(24, 87, 'cheese', '20.00'),
(25, 95, 'HAtdog', '15.00'),
(26, 104, 'Extra Cheese', '30.00'),
(27, 108, 'Cheese', '30.00'),
(28, 117, 'Mayo', '50.00'),
(29, 118, 'Cheese', '30.00'),
(30, 123, 'Buffalo Sauce', '25.00'),
(31, 126, 'Extra Sauce', '20.00');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_payments`
--

INSERT INTO `order_payments` (`payment_id`, `order_id`, `amount`, `payment_method`, `payment_date`) VALUES
(1, 1, '80.00', 'maya', '2025-04-11 22:12:24'),
(2, 4, '95.00', 'cash', '2025-04-11 22:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `package_durations`
--

CREATE TABLE `package_durations` (
  `id` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `package_max_guests`
--

CREATE TABLE `package_max_guests` (
  `id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `package_menu_items`
--

CREATE TABLE `package_menu_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `package_notes`
--

CREATE TABLE `package_notes` (
  `id` int(11) NOT NULL,
  `note_type` enum('30PAX','50PAX') NOT NULL,
  `note_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `total_rooms` int(11) NOT NULL DEFAULT 0,
  `available_rooms` int(11) NOT NULL DEFAULT 0,
  `status` enum('Available','Not Available') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `total_rooms`, `available_rooms`, `status`) VALUES
(8, 8, 9, 3, 'Available'),
(9, 10, 9, 0, 'Available');

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
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_reviews`
--

INSERT INTO `room_reviews` (`review_id`, `room_type_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(4, 3, 9, '1.0', 'dfdfd', '2025-03-20 13:14:10'),
(5, 2, 9, '4.0', 'good', '2025-03-20 13:17:37'),
(6, 1, 9, '5.0', 'Gooods', '2025-03-20 13:21:38'),
(7, 3, 5, '5.0', 'goods', '2025-03-23 09:03:57'),
(8, 1, 5, '5.0', 'vcvcv', '2025-03-24 10:10:12'),
(9, 3, 1, '5.0', 'gOODS\r\n', '2025-03-27 07:06:17'),
(13, 2, 5, '5.0', 'fgf', '2025-04-01 17:03:08'),
(14, 3, 3, '5.0', 'aaaaaaaaaa', '2025-04-03 07:38:32'),
(15, 2, 3, '5.0', 'aaaaaaaaaaaaaaaa', '2025-04-03 07:38:47'),
(16, 2, 8, '1.0', 'Ang bantot ng unan', '2025-04-05 02:47:57'),
(17, 3, 8, '5.0', 'aaa', '2025-04-08 03:31:00'),
(18, 2, 1, '5.0', 'okay', '2025-04-11 11:02:32'),
(19, 8, 5, '5.0', 'Good morning', '2025-04-12 10:43:17'),
(20, 9, 34, '5.0', 'Ang ganda ng room ', '2025-04-12 11:32:28'),
(21, 9, 31, '3.0', 'not goods', '2025-04-12 11:42:08'),
(22, 10, 31, '5.0', 'e4e', '2025-04-12 11:42:53'),
(23, 8, 31, '4.0', 'Nice!', '2025-04-12 11:47:28');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`, `image2`, `image3`, `discount_percent`, `discount_valid_until`, `rating_count`, `status`) VALUES
(3, 'Standard Double Room', '50000.00', 5, 'Good for ', '1 Queen Bed, 2 Single Beds', '5.0', '../aa/uploads/rooms/room_type_67f7d91008cbe.jpg', '../aa/uploads/rooms/room_type_67f7d91008f9a_2.jpg', '../aa/uploads/rooms/room_type_67f7d9100930c_3.jpg', 20, '2025-04-10', 0, 'inactive'),
(6, 'Deluxe Family Room', '4500.00', 0, 'Spacious room perfect for families', '1 Queen Bed, 1 Single Bed', '4.5', '../aa/uploads/rooms/room_type_67f7ff869fee9.png', NULL, NULL, 0, NULL, 0, 'inactive'),
(8, 'chano', '123456.00', 9, 'haha', '2 Single Beds, 1 master bed', '4.5', '../aa/uploads/rooms/room_type_67faf9599cec3.png', '../aa/uploads/rooms/room_type_67f96e51c39c7_2.jpg', '../aa/uploads/rooms/room_type_67f96e51c3ccd_3.jpg', 0, NULL, 2, 'active'),
(9, 'Standard Double Room', '2000.00', 9, '2', '2 Single Beds, 1 master bed', '4.0', '../aa/uploads/rooms/room_type_67f9739f87dcc.jpg', '../aa/uploads/rooms/room_type_67f9739f87ef4_2.jpg', '../aa/uploads/rooms/room_type_67f9739f8ba32_3.jpg', 0, NULL, 2, 'active'),
(10, 'Deluxe Family Room', '4500.00', 10, 'maganda', '2 Single Beds, 1 master bed', '5.0', '../aa/uploads/rooms/room_type_67faf9942624a.jpg', '../aa/uploads/rooms/room_type_67fa0b2be9514_2.jpg', '../aa/uploads/rooms/room_type_67fa0b2be97ea_3.jpg', 0, NULL, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `total_amount`, `payment_method`, `created_at`, `order_date`) VALUES
(1, 13, '150.00', 'gcash', '2025-02-17 19:51:30', '2025-04-07 13:16:04'),
(2, 12, '150.00', 'gcash', '2025-02-17 19:51:47', '2025-04-07 13:16:04'),
(3, 18, '235.00', 'gcash', '2025-02-17 19:55:03', '2025-04-07 13:16:04'),
(4, 19, '150.00', 'gcash', '2025-02-17 20:01:01', '2025-04-07 13:16:04'),
(5, 20, '200.00', 'gcash', '2025-02-17 22:35:04', '2025-04-07 13:16:04'),
(6, 21, '150.00', 'gcash', '2025-02-18 06:10:10', '2025-04-07 13:16:04'),
(7, 22, '360.00', 'maya', '2025-02-18 06:10:55', '2025-04-07 13:16:04'),
(8, 23, '355.00', 'gcash', '2025-02-18 06:12:33', '2025-04-07 13:16:04'),
(9, 25, '300.00', 'gcash', '2025-02-18 12:38:51', '2025-04-07 13:16:04');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `table_cancellations`
--

INSERT INTO `table_cancellations` (`id`, `booking_id`, `user_id`, `reason`, `cancelled_at`) VALUES
(1, 28, 5, 'found_better_option', '2025-04-12 17:32:48');

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
  `status` enum('active','inactive') DEFAULT 'active',
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `table_packages`
--

INSERT INTO `table_packages` (`id`, `package_name`, `price`, `capacity`, `description`, `menu_items`, `available_tables`, `image_path`, `image1`, `image2`, `image3`, `image4`, `image5`, `status`, `reason`) VALUES
(1, 'Couples', '0', 2, 'Perfect for couples\r\n', '', 0, 'uploads/table_packages/67fb15eb1f794.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(2, 'Friends', '0', 10, 'Ideal for small groups', '', 0, 'uploads/table_packages/67fb0cd104e49.webp', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(3, 'Family', '0', 10, 'Great for family gatherings', '', 0, 'uploads/table_packages/67fb10c391a74.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(7, 'Package A', '20000', 30, 'Basic package for large groups', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks', 2, 'uploads/table_packages/67fb10116989f.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(8, 'Pacakge B', '33000', 30, 'Premium package with extra services', 'Appetizer, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 2, 'uploads/table_packages/67fb10ded4762.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(9, 'Package C', '45000', 30, 'All-inclusive luxury package', '3 Appetizer, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2 Desserts, Drinks', 1, 'uploads/table_packages/67fb120e4bfc4.png', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `user_type` enum('admin','frontdesk','cashier') NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `userss`
--

INSERT INTO `userss` (`id`, `first_name`, `last_name`, `email`, `contact_number`, `address`, `password`, `actual_password`, `user_type`, `name`) VALUES
(1, 'Alfred hendrik', 'Aceveda', 'admin@example.com', '09362715617', 'Balite Calapan City Oriental Mindoro', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', NULL, 'admin', NULL),
(2, NULL, NULL, 'frontdesk@example.com', NULL, NULL, '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', NULL, 'frontdesk', NULL),
(3, NULL, NULL, 'cashier@example.com', NULL, NULL, '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', NULL, 'cashier', NULL),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$9Of5FaVHvCt/YsEnryDRnOjxkRE6oS1BhqvJnl/YJ4ZL4RnZo6sVK', NULL, 'admin', NULL),
(6, 'Aizzy', 'Villanueva', 'aizzyvillanueva34@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$r1X5exzjzJcmM.v3uGBNXeXiN.QkoU1QOIYDIG.7UjmZG.qmxt0hy', NULL, 'admin', NULL),
(7, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '912345678787', 'wawa calapan city', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', NULL, 'admin', NULL),
(8, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '912345678787', 'tawagan', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', 'chanopassword', 'admin', NULL),
(9, 'Aizzy', 'Villanueva', 'aizzyvillanueva5@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$6FJdPRpNRHB5rzVQ6L8EO.7xNMFRTQ.qS84uCvwkqB/nmB1aAx5Fy', '020104', 'admin', NULL),
(10, 'Fammela', 'De Guzman', 'fammeladeguzman21@gmail.com', '09362715617', 'Wawa, Calapan City', '$2y$10$fo1HrZCUvSrEh8InzHzuoORab6vzOZayXnF2iLrmLQeBmye/mNSl.', 'fammelapassword', 'frontdesk', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `verification_types`
--

INSERT INTO `verification_types` (`id`, `type`, `is_enabled`, `disable_message`, `last_updated`) VALUES
(1, 'SMS', 1, NULL, '2025-04-11 09:47:29'),
(2, 'Email', 0, 'basta', '2025-04-11 09:47:26');

-- --------------------------------------------------------

--
-- Structure for view `featured_rooms_view`
--
DROP TABLE IF EXISTS `featured_rooms_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `featured_rooms_view`  AS  select `fr`.`id` AS `id`,`rt`.`room_type` AS `room_type`,`fr`.`start_date` AS `start_date`,`fr`.`end_date` AS `end_date`,`fr`.`created_at` AS `created_at`,`fr`.`image_path` AS `image_path` from (`featured_rooms` `fr` join `room_types` `rt` on(`fr`.`room_type_id` = `rt`.`room_type_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_slideshow`
--
ALTER TABLE `about_slideshow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD KEY `idx_activities_type` (`activity_type`);

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `idx_bookings_dates` (`check_in`,`check_out`),
  ADD KEY `idx_bookings_status` (`status`);

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
-- Indexes for table `discount_types`
--
ALTER TABLE `discount_types`
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
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `idx_guest_type` (`guest_type`),
  ADD KEY `idx_booking_guest` (`booking_id`);

--
-- Indexes for table `hotel_policies`
--
ALTER TABLE `hotel_policies`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `location_info`
--
ALTER TABLE `location_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_settings`
--
ALTER TABLE `maintenance_settings`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `package_durations`
--
ALTER TABLE `package_durations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_max_guests`
--
ALTER TABLE `package_max_guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_menu_items`
--
ALTER TABLE `package_menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_notes`
--
ALTER TABLE `package_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

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
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `idx_room_type` (`room_type_id`);

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
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD PRIMARY KEY (`service_booking_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

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
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `verification_methods`
--
ALTER TABLE `verification_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verification_types`
--
ALTER TABLE `verification_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `about_slideshow`
--
ALTER TABLE `about_slideshow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin_status`
--
ALTER TABLE `admin_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `advance_orders`
--
ALTER TABLE `advance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `discount_types`
--
ALTER TABLE `discount_types`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `facility_categories`
--
ALTER TABLE `facility_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=384;

--
-- AUTO_INCREMENT for table `hotel_policies`
--
ALTER TABLE `hotel_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `location_info`
--
ALTER TABLE `location_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_settings`
--
ALTER TABLE `maintenance_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `package_durations`
--
ALTER TABLE `package_durations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_max_guests`
--
ALTER TABLE `package_max_guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_menu_items`
--
ALTER TABLE `package_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_notes`
--
ALTER TABLE `package_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `room_transfer_logs`
--
ALTER TABLE `room_transfer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `seasonal_discounts`
--
ALTER TABLE `seasonal_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seasonal_effects`
--
ALTER TABLE `seasonal_effects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_bookings`
--
ALTER TABLE `service_bookings`
  MODIFY `service_booking_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_cancellations`
--
ALTER TABLE `table_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `table_packages`
--
ALTER TABLE `table_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `verification_methods`
--
ALTER TABLE `verification_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `verification_types`
--
ALTER TABLE `verification_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD CONSTRAINT `order_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `service_bookings`
--
ALTER TABLE `service_bookings`
  ADD CONSTRAINT `service_bookings_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `service_bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
