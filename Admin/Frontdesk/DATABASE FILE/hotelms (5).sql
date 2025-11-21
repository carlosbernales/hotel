-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 12:49 AM
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
(3, 13, 1, 1, 120.00, '2025-02-17 22:36:45');

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
(6, 'libreng tuli', 'fas fa-fa sale'),
(7, 'toothbrush', '');

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
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nights` int(11) NOT NULL,
  `downpayment_amount` decimal(10,2) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) NOT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `early_checkin` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `checked_in_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `first_name`, `last_name`, `booking_type`, `email`, `contact`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `payment_option`, `payment_method`, `total_amount`, `status`, `created_at`, `nights`, `downpayment_amount`, `cancellation_reason`, `cancelled_at`, `rejection_reason`, `rejected_at`, `early_checkin`, `updated_at`, `checked_in_at`) VALUES
(164, NULL, 'hanna', 'deslie', 'Walkin', 'aizzyvillanueva43@gmail.com', '09992', '2025-04-05', '2025-04-23', '05:03:22', 1, 'full', 'Cash', 22500, 'Checked In', '2025-04-03 21:03:22', 18, 0.00, NULL, NULL, '', NULL, 0, '2025-04-03 21:03:33', NULL),
(165, NULL, 'hanna', 'deslie', 'Walkin', 'aizzyvillanueva43@gmail.com', '09992', '2025-04-05', '2025-04-17', '05:18:13', 1, 'full', 'Cash', 15000, 'Checked In', '2025-04-03 21:18:13', 12, 0.00, NULL, NULL, '', NULL, 0, '2025-04-03 21:22:12', NULL);

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
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` enum('Morning (8:00 AM - 1:00 PM)','Afternoon (2:00 PM - 7:00 PM)','Evening (6:00 PM - 11:00 PM)') NOT NULL,
  `num_guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `package_name` varchar(100) NOT NULL,
  `package_type` enum('30 PAX','50 PAX') NOT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `customer_name`, `contact_number`, `email`, `event_date`, `event_time`, `num_guests`, `special_requests`, `package_name`, `package_type`, `package_price`, `status`, `created_at`) VALUES
(1, 'Alfred hendrik Aceveda', '09362715617', 'aizzyvillanueva43@gmail.com', '2025-04-05', 'Morning (8:00 AM - 1:00 PM)', 30, '', 'Package A (30 PAX)', '30 PAX', 0.00, 'Rejected', '2025-04-03 20:26:34'),
(2, 'Christian Realisan', '912345678787', 'christianrealisan45@gmail.com', '2025-04-24', 'Afternoon (2:00 PM - 7:00 PM)', 45, '', 'Package B (30 PAX)', '30 PAX', 0.00, 'Rejected', '2025-04-03 20:39:06'),
(3, 'myra luceno', '09412222222', 'christianrealisan45a@gmail.com', '2025-04-25', 'Evening (6:00 PM - 11:00 PM)', 30, '', 'Package B (30 PAX)', '30 PAX', 0.00, 'Rejected', '2025-04-03 20:42:22'),
(4, 'myra luceno', '09412222222', 'christianrealisan45a@gmail.com', '2025-04-17', 'Morning (8:00 AM - 1:00 PM)', 30, '', 'Package C (30 PAX)', '30 PAX', 0.00, 'Rejected', '2025-04-03 20:46:46'),
(5, 'myra luceno', '09412222222', 'christianrealisan45a@gmail.com', '2025-05-02', 'Afternoon (2:00 PM - 7:00 PM)', 30, '', 'Package C', '30 PAX', 46000.00, 'Confirmed', '2025-04-03 20:51:52');

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
  `package_id` varchar(10) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `package_type` enum('30 PAX','50 PAX') NOT NULL,
  `menu` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `max_guests` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_names`
--

CREATE TABLE `guest_names` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `guest_name`, `created_at`) VALUES
(1, 164, 'a', '2025-04-03 21:03:22'),
(2, 165, 'totoy', '2025-04-03 21:18:13');

-- --------------------------------------------------------

--
-- Table structure for table `hotelms_payments`
--

CREATE TABLE `hotelms_payments` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotelms_payments`
--

INSERT INTO `hotelms_payments` (`id`, `booking_id`, `amount`, `payment_method`, `payment_date`, `created_at`) VALUES
(8, '100', 12950.00, 'Cash', '2025-03-28 02:49:04', '2025-03-27 18:49:04'),
(9, '145', 2000.00, 'Cash', '2025-03-28 13:43:52', '2025-03-28 05:43:52'),
(10, '101', 12000.00, 'Cash', '2025-03-29 02:12:32', '2025-03-28 18:12:32');

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
(10, 'Appetizers', ''),
(11, 'Salads', ''),
(12, 'Main Courses', ''),
(13, 'Sandwiches', ''),
(14, 'Desserts', ''),
(15, 'Beverages', ''),
(16, 'Specials', ''),
(17, 'Side Dishes', ''),
(18, 'Breakfast', ''),
(19, 'Snacks', ''),
(20, 'Appetizers', ''),
(21, 'Salads', ''),
(22, 'Main Courses', ''),
(23, 'Sandwiches', ''),
(24, 'Desserts', ''),
(25, 'Beverages', ''),
(26, 'Specials', ''),
(27, 'Side Dishes', ''),
(28, 'Breakfast', ''),
(29, 'Snacks', ''),
(30, 'Appetizers', ''),
(31, 'Salads', ''),
(32, 'Main Courses', ''),
(33, 'Sandwiches', ''),
(34, 'Desserts', ''),
(35, 'Beverages', ''),
(36, 'Specials', ''),
(37, 'Side Dishes', ''),
(38, 'Breakfast', ''),
(39, 'Snacks', ''),
(40, 'Appetizers', ''),
(41, 'Salads', ''),
(42, 'Main Courses', ''),
(43, 'Sandwiches', ''),
(44, 'Desserts', ''),
(45, 'Beverages', ''),
(46, 'Specials', ''),
(47, 'Side Dishes', ''),
(48, 'Breakfast', ''),
(49, 'Snacks', ''),
(50, 'Appetizers', ''),
(51, 'Salads', ''),
(52, 'Main Courses', ''),
(53, 'Sandwiches', ''),
(54, 'Desserts', ''),
(55, 'Beverages', ''),
(56, 'Specials', ''),
(57, 'Side Dishes', ''),
(58, 'Breakfast', ''),
(59, 'Snacks', ''),
(60, 'Appetizers', ''),
(61, 'Salads', ''),
(62, 'Main Courses', ''),
(63, 'Sandwiches', ''),
(64, 'Desserts', ''),
(65, 'Beverages', ''),
(66, 'Specials', ''),
(67, 'Side Dishes', ''),
(68, 'Breakfast', ''),
(69, 'Snacks', ''),
(70, 'Appetizers', ''),
(71, 'Salads', ''),
(72, 'Main Courses', ''),
(73, 'Sandwiches', ''),
(74, 'Desserts', ''),
(75, 'Beverages', ''),
(76, 'Specials', ''),
(77, 'Side Dishes', ''),
(78, 'Breakfast', ''),
(79, 'Snacks', ''),
(80, 'Appetizers', ''),
(81, 'Salads', ''),
(82, 'Main Courses', ''),
(83, 'Sandwiches', ''),
(84, 'Desserts', ''),
(85, 'Beverages', ''),
(86, 'Specials', ''),
(87, 'Side Dishes', ''),
(88, 'Breakfast', ''),
(89, 'Snacks', ''),
(90, 'Appetizers', ''),
(91, 'Salads', ''),
(92, 'Main Courses', ''),
(93, 'Sandwiches', ''),
(94, 'Desserts', ''),
(95, 'Beverages', ''),
(96, 'Specials', ''),
(97, 'Side Dishes', ''),
(98, 'Breakfast', ''),
(99, 'Snacks', ''),
(100, 'Appetizers', ''),
(101, 'Salads', ''),
(102, 'Main Courses', ''),
(103, 'Sandwiches', ''),
(104, 'Desserts', ''),
(105, 'Beverages', ''),
(106, 'Specials', ''),
(107, 'Side Dishes', ''),
(108, 'Breakfast', ''),
(109, 'Snacks', ''),
(110, 'Appetizers', ''),
(111, 'Salads', ''),
(112, 'Main Courses', ''),
(113, 'Sandwiches', ''),
(114, 'Desserts', ''),
(115, 'Beverages', ''),
(116, 'Specials', ''),
(117, 'Side Dishes', ''),
(118, 'Breakfast', ''),
(119, 'Snacks', ''),
(120, 'Appetizers', ''),
(121, 'Salads', ''),
(122, 'Main Courses', ''),
(123, 'Sandwiches', ''),
(124, 'Desserts', ''),
(125, 'Beverages', ''),
(126, 'Specials', ''),
(127, 'Side Dishes', ''),
(128, 'Breakfast', ''),
(129, 'Snacks', ''),
(130, 'Appetizers', ''),
(131, 'Salads', ''),
(132, 'Main Courses', ''),
(133, 'Sandwiches', ''),
(134, 'Desserts', ''),
(135, 'Beverages', ''),
(136, 'Specials', ''),
(137, 'Side Dishes', ''),
(138, 'Breakfast', ''),
(139, 'Snacks', ''),
(140, 'Appetizers', ''),
(141, 'Salads', ''),
(142, 'Main Courses', ''),
(143, 'Sandwiches', ''),
(144, 'Desserts', ''),
(145, 'Beverages', ''),
(146, 'Specials', ''),
(147, 'Side Dishes', ''),
(148, 'Breakfast', ''),
(149, 'Snacks', ''),
(150, 'Appetizers', ''),
(151, 'Salads', ''),
(152, 'Main Courses', ''),
(153, 'Sandwiches', ''),
(154, 'Desserts', ''),
(155, 'Beverages', ''),
(156, 'Specials', ''),
(157, 'Side Dishes', ''),
(158, 'Breakfast', ''),
(159, 'Snacks', ''),
(160, 'Appetizers', ''),
(161, 'Salads', ''),
(162, 'Main Courses', ''),
(163, 'Sandwiches', ''),
(164, 'Desserts', ''),
(165, 'Beverages', ''),
(166, 'Specials', ''),
(167, 'Side Dishes', ''),
(168, 'Breakfast', ''),
(169, 'Snacks', ''),
(170, 'Appetizers', ''),
(171, 'Salads', ''),
(172, 'Main Courses', ''),
(173, 'Sandwiches', ''),
(174, 'Desserts', ''),
(175, 'Beverages', ''),
(176, 'Specials', ''),
(177, 'Side Dishes', ''),
(178, 'Breakfast', ''),
(179, 'Snacks', ''),
(180, 'Appetizers', ''),
(181, 'Salads', ''),
(182, 'Main Courses', ''),
(183, 'Sandwiches', ''),
(184, 'Desserts', ''),
(185, 'Beverages', ''),
(186, 'Specials', ''),
(187, 'Side Dishes', ''),
(188, 'Breakfast', ''),
(189, 'Snacks', '');

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
(5, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(6, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(7, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(8, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(9, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(10, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(11, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(12, 4, 'Egg Sandwich', 500.00, ''),
(13, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(15, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(16, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(17, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(18, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(19, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(20, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(21, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(22, 4, 'Egg Sandwich', 500.00, ''),
(23, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(25, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(26, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(27, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(28, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(29, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(30, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(31, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(32, 4, 'Egg Sandwich', 500.00, ''),
(33, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(35, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(36, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(37, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(38, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(39, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(40, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(41, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(42, 4, 'Egg Sandwich', 500.00, ''),
(43, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(45, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(46, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(47, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(48, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(49, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(50, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(51, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(52, 4, 'Egg Sandwich', 500.00, ''),
(53, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(55, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(56, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(57, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(58, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(59, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(60, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(61, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(62, 4, 'Egg Sandwich', 500.00, ''),
(63, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(65, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(66, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(67, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(68, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(69, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(70, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(71, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(72, 4, 'Egg Sandwich', 500.00, ''),
(73, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(75, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(76, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(77, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(78, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(79, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(80, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(81, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(82, 4, 'Egg Sandwich', 500.00, ''),
(83, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(85, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(86, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(87, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(88, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(89, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(90, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(91, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(92, 4, 'Egg Sandwich', 500.00, ''),
(93, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(95, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(96, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(97, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(98, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(99, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(100, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(101, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(102, 4, 'Egg Sandwich', 500.00, ''),
(103, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(105, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(106, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(107, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(108, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(109, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(110, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(111, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(112, 4, 'Egg Sandwich', 500.00, ''),
(113, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(115, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(116, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(117, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(118, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(119, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(120, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(121, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(122, 4, 'Egg Sandwich', 500.00, ''),
(123, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(124, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(125, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(126, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(127, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(128, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(129, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(130, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(131, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(132, 4, 'Egg Sandwich', 500.00, ''),
(133, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(134, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(135, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(136, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(137, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(138, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(139, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(140, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(141, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(142, 4, 'Egg Sandwich', 500.00, ''),
(143, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(144, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(145, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(146, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(147, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(148, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(149, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(150, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(151, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(152, 4, 'Egg Sandwich', 500.00, ''),
(153, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(154, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(155, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(156, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(157, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(158, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(159, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(160, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(161, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(162, 4, 'Egg Sandwich', 500.00, ''),
(163, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(164, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(165, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(166, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(167, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(168, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(169, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(170, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(171, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(172, 4, 'Egg Sandwich', 500.00, ''),
(173, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(174, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(175, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(176, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(177, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(178, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(179, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(180, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(181, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(182, 4, 'Egg Sandwich', 500.00, ''),
(183, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(184, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(185, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(186, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(187, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(188, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(189, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(190, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(191, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(192, 4, 'Egg Sandwich', 500.00, ''),
(193, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(194, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(195, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(196, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(197, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(198, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(199, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(200, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(201, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(202, 4, 'Egg Sandwich', 500.00, ''),
(203, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(204, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(205, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(206, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(207, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(208, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(209, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(210, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(211, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(212, 4, 'Egg Sandwich', 500.00, ''),
(213, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(214, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(215, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(216, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(217, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(218, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(219, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(220, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(221, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(222, 4, 'Egg Sandwich', 500.00, ''),
(223, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(224, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(225, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(226, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(227, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(228, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(229, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(230, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(231, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(232, 4, 'Egg Sandwich', 500.00, ''),
(233, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(234, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(235, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(236, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(237, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(238, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(239, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(240, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(241, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(242, 4, 'Egg Sandwich', 500.00, ''),
(243, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(244, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(245, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(246, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(247, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(248, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(249, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(250, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(251, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(252, 4, 'Egg Sandwich', 500.00, ''),
(253, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(254, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(255, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(256, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(257, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(258, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(259, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(260, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(261, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(262, 4, 'Egg Sandwich', 500.00, ''),
(263, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(264, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(265, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(266, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(267, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(268, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(269, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(270, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(271, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(272, 4, 'Egg Sandwich', 500.00, ''),
(273, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(274, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(275, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(276, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(277, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(278, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(279, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(280, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(281, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(282, 4, 'Egg Sandwich', 500.00, ''),
(283, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(284, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg'),
(285, 1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(286, 1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(287, 1, 'Chicken Wings', 180.00, 'images/wings.jpg'),
(288, 1, 'Spaghetti maccaroni', 270.00, 'images/menu_67b42892f15e15.jpg'),
(289, 2, 'Salad', 200.00, 'images/menu_67b3c1aec5d2f.jpg'),
(290, 2, 'Coconut Salad', 200.00, 'images/menu_67b41cbd8599f.jpg'),
(291, 3, 'Spaghetti', 300.00, 'images/menu_67b427110a801.jpg'),
(292, 4, 'Egg Sandwich', 500.00, ''),
(293, 8, 'coleslismo', 30.00, 'images/menu_67b428e783085.jpg'),
(294, 10, 'Fried', 200.00, 'images/menu_67b47642aa845.jpg');

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
(0, 1, 'HAtdog', 15.00);

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
(1, 18, 2, 'Inquiries', 'kupal ka ba bossing', 0, '2025-02-16 19:31:22'),
(0, 1, 2, 'Welcome to Front Desk', 'Welcome to the team! Please review the standard operating procedures.', 0, '2025-04-02 13:01:44'),
(0, 1, 3, 'Cash Register Setup', 'Please ensure all cash registers are properly configured for the new day.', 0, '2025-04-02 13:01:44'),
(0, 2, 1, 'Daily Report', 'Here is the daily report for your review.', 1, '2025-04-02 13:01:44'),
(0, 3, 1, 'Cash Count', 'Daily cash count completed and verified.', 1, '2025-04-02 13:01:44'),
(0, 1, 1, 'Scheduled Maintenance Notice', 'Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding.', 0, '2025-04-02 13:25:58'),
(0, 1, 4, 'Scheduled Maintenance Notice', 'Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding.', 0, '2025-04-02 13:31:46'),
(0, 1, 1, 'Scheduled Maintenance Notice', 'Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding.', 0, '2025-04-02 13:31:46'),
(0, 1, 3, 'Scheduled Maintenance Notice', 'Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding.', 0, '2025-04-02 13:31:46'),
(0, 1, 2, 'Scheduled Maintenance Notice', 'Dear valued guest,\n\nWe wish to inform you that maintenance work is scheduled in your area on [DATE] from [TIME]. We will ensure minimal disruption to your stay.\n\nWe appreciate your understanding.', 0, '2025-04-02 13:31:46');

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
(29, 1, 'New Booking Confirmation', 'Your booking #112 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 112, 0, '2025-02-17 22:01:12'),
(30, 1, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 112, 0, '2025-02-17 22:01:45'),
(31, 1, 'New Booking Confirmation', 'Your booking #113 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 113, 0, '2025-02-17 22:02:39'),
(32, 3, '', 'Your order has been placed successfully. Please pick up at 06:34', 'order', '', NULL, 1, '2025-02-17 22:34:40'),
(33, 3, '', 'Your room booking has been cancelled successfully.', 'booking_cancelled', '', 103, 0, '2025-02-17 22:37:21'),
(34, 3, 'New Booking Confirmation', 'Your booking #114 has been confirmed. Check-in date: Feb 18, 2025', 'booking', 'fas fa-calendar-check', 114, 0, '2025-02-17 23:33:19'),
(35, 0, 'New Room Booking', 'New room booking from Alleah Villanuev for check-in on Apr 4, 2025', 'booking', '', NULL, 0, '2025-04-02 16:08:32'),
(36, 0, 'New Room Booking', 'New room booking from hanna deslie for check-in on Apr 19, 2025', 'booking', '', NULL, 0, '2025-04-02 20:19:37'),
(37, 0, 'New Room Booking', 'New room booking from hanna deslie for check-in on Apr 5, 2025', 'booking', '', NULL, 0, '2025-04-03 21:03:22'),
(38, 0, 'New Room Booking', 'New room booking from hanna deslie for check-in on Apr 5, 2025', 'booking', '', NULL, 0, '2025-04-03 21:18:13');

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
(20, 3, 200.00, '', '06:34:00', '', 'gcash', 'finished', '2025-02-17 22:34:40', 'Walk-in');

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
(22, 20, 'Hand-cut Potato Fries', 1, 120.00, 120.00);

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
(7, 22, 'Mayo', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `booking_type` enum('room','table','event') NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('gcash','maya','cash') NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_type_details` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL,
  `gcash_qr` varchar(255) DEFAULT NULL,
  `maya_qr` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `gcash_qr`, `maya_qr`, `updated_at`) VALUES
(1, 'uploads/qr_codes/gcash_qr.jpg', NULL, '2025-03-27 13:32:43');

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
(3, 3, 3, 1, 'Available'),
(10, 1, 1, 0, 'available'),
(11, 2, 6, 0, 'Occupied'),
(12, 6, 3, 0, 'Available');

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
(1, 164, 3, 'Family Room', 1250.00, 1, 18, 22500.00, '2025-04-03 21:03:22', 1, 0.00, 18),
(2, 165, 3, 'Family Room', 1250.00, 1, 12, 15000.00, '2025-04-03 21:18:13', 1, 0.00, 12);

-- --------------------------------------------------------

--
-- Table structure for table `room_transfer_logs`
--

CREATE TABLE `room_transfer_logs` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `old_room_type_id` int(11) NOT NULL,
  `new_room_type_id` int(11) NOT NULL,
  `transfer_reason` text NOT NULL,
  `transfer_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 'Myra lounge', 2000.00, 2, 'basta', '2 Single Beds, 1 master bed', 2.0, 'uploads/rooms/room_type_67e4fab560f63.jpg', 0, NULL);

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
(1, 7),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(6, 1),
(6, 2),
(6, 6);

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
(5, 20, 200.00, 'gcash', '2025-02-17 22:35:04');

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
(8, 'kupal discount', 50.00, '2025-03-27', '2025-03-31', 3, 'basta', 1, '2025-03-26 15:54:06');

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
(13, 3, 'BK000013', 'Aizzy', 'Koupals', '9876543200', 'chsdjf@gmail.com', '2025-02-18', '06:35:00', 2, NULL, 'GCash', 319.00, 319.00, 0.00, 'Fully Paid', 'Pending', '2025-02-17 22:36:45', NULL, NULL, NULL);

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

--
-- Dumping data for table `table_reservations`
--

INSERT INTO `table_reservations` (`reservation_id`, `customer_name`, `contact_number`, `guest_count`, `table_type`, `reservation_datetime`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Christian Realisan', '912345678787', 2, 'Couple', '2025-03-28 14:22:00', 'confirmed', '2025-03-26 17:58:59', '2025-03-26 17:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
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

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_verified`, `verification_code`, `verification_expiry`, `created_at`, `updated_at`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Christian Realisan', 'christianrealisan3@gmail.com', '$2y$10$wTeWBkmwQn7UUuxW0ahQveDtRIhPULLjCBbND2MZ.mdXcnVvoxv8e', 1, '986507', '2025-02-12 16:24:15', '2025-02-12 15:22:15', '2025-02-12 15:22:51', NULL, NULL),
(2, 'Mang Juan', 'mangjuan@gmail.com', '$2y$10$mbQd/yeOWp3qy90mTAiUnOCnVnl5o33rYSnDtGXRPx4kxf1ZE7m0.', 1, '382013', '2025-02-12 16:31:55', '2025-02-12 15:29:55', '2025-02-12 16:47:42', NULL, NULL),
(3, 'Fammela Nicole Jumig De Guzman', 'fammeladeguzman21@gmail.com', '$2y$10$Ic53xTZkXkmyqtCTpdq.Y.uauCDKuC48etMP4ojLRFK8JLz6ZzRRi', 1, '484717', '2025-02-13 09:51:10', '2025-02-13 08:49:10', '2025-02-13 08:49:44', NULL, NULL),
(4, 'Kenjo M. Marimon', 'aizzyvillanueva43@gmail.com', '$2y$10$7tm0ztu9lKpjfwTWO2Cc8.9yp/ATXCDTv6UzJB3piqf6ebL7Kcsfq', 1, '188543', '2025-02-17 13:10:04', '2025-02-17 12:08:04', '2025-02-17 12:09:01', NULL, NULL);

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
  `user_type` enum('admin','frontdesk','cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userss`
--

INSERT INTO `userss` (`id`, `first_name`, `last_name`, `email`, `contact_number`, `address`, `password`, `actual_password`, `user_type`) VALUES
(1, 'Alfred hendrik', 'Aceveda', 'admin@example.com', '09362715617', 'Balite Calapan City Oriental Mindoro', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', NULL, 'admin'),
(2, NULL, NULL, 'frontdesk@example.com', NULL, NULL, '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', NULL, 'frontdesk'),
(3, NULL, NULL, 'cashier@example.com', NULL, NULL, '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', NULL, 'cashier'),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$9Of5FaVHvCt/YsEnryDRnOjxkRE6oS1BhqvJnl/YJ4ZL4RnZo6sVK', NULL, 'admin'),
(6, 'Aizzy', 'Villanueva', 'aizzyvillanueva34@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$r1X5exzjzJcmM.v3uGBNXeXiN.QkoU1QOIYDIG.7UjmZG.qmxt0hy', NULL, 'admin'),
(7, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '912345678787', 'wawa calapan city', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', NULL, 'admin'),
(8, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '912345678787', 'tawagan', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', 'chanopassword', 'admin');

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
  ADD KEY `room_id` (`room_id`);

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
-- Indexes for table `booking_extensions`
--
ALTER TABLE `booking_extensions`
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
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `guest_names`
--
ALTER TABLE `guest_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `hotelms_payments`
--
ALTER TABLE `hotelms_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_policies`
--
ALTER TABLE `hotel_policies`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_settings` (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `advance_order_addons`
--
ALTER TABLE `advance_order_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `booking_cancellations`
--
ALTER TABLE `booking_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_display_settings`
--
ALTER TABLE `booking_display_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `booking_extensions`
--
ALTER TABLE `booking_extensions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_images`
--
ALTER TABLE `event_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_packages`
--
ALTER TABLE `event_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hotelms_payments`
--
ALTER TABLE `hotelms_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hotel_policies`
--
ALTER TABLE `hotel_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `menu_item_addons`
--
ALTER TABLE `menu_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservation_orders`
--
ALTER TABLE `reservation_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `room_transfer_logs`
--
ALTER TABLE `room_transfer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `seasonal_discounts`
--
ALTER TABLE `seasonal_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `featured_rooms`
--
ALTER TABLE `featured_rooms`
  ADD CONSTRAINT `featured_rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`);

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
