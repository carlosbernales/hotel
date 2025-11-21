-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 11, 2025 at 01:18 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u429956055_Hotelms`
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
(0, 12, 0, 1, 0.00, '2025-04-23 07:41:52'),
(0, 14, 0, 3, 0.00, '2025-04-28 14:13:49'),
(0, 1, 0, 1, 0.00, '2025-04-30 02:14:53');

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
-- Table structure for table `advance_table_orders`
--

CREATE TABLE `advance_table_orders` (
  `id` int(11) NOT NULL,
  `table_booking_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `order_items` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_option` varchar(50) NOT NULL DEFAULT 'full',
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `amount_to_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advance_table_orders`
--

INSERT INTO `advance_table_orders` (`id`, `table_booking_id`, `customer_name`, `order_items`, `total_amount`, `payment_option`, `payment_method`, `amount_to_pay`, `created_at`) VALUES
(1, 26, 'Test Customer 2025-05-18 22:05:30', '[{\"id\":1,\"name\":\"Test Food Item\",\"price\":75.5,\"quantity\":2},{\"id\":2,\"name\":\"Test Drink Item\",\"price\":24.5,\"quantity\":2}]', 200.00, 'full', 'cash', 200.00, '2025-05-18 14:05:30'),
(8, 38, 'Clifford Musni', '[{\"id\":2,\"name\":\"GAMBAS AND CHORIZO\",\"price\":368,\"quantity\":1}]', 368.00, 'down', 'cash', 184.00, '2025-05-19 02:14:03'),
(9, 40, 'Myra Cabal', '[{\"id\":432,\"name\":\"FRIED CALAMARI\",\"price\":242,\"quantity\":1},{\"id\":4,\"name\":\"MOZZARELLA STICKS\",\"price\":285,\"quantity\":1},{\"id\":5,\"name\":\"SIRRACHA BUFFALO WINGS 6 PCS\",\"price\":377,\"quantity\":1}]', 904.00, 'full', 'gcash', 904.00, '2025-05-19 02:19:05'),
(10, 44, 'Rhovelyn De Guzman', '[{\"id\":432,\"name\":\"FRIED CALAMARI\",\"price\":242,\"quantity\":1},{\"id\":8,\"name\":\"TRUFFLE PARMESAN POTATO CHIPS\",\"price\":281,\"quantity\":1}]', 523.00, 'full', 'cash', 523.00, '2025-06-01 08:04:24'),
(11, 45, 'Jamelyn M Manongsong', '[{\"id\":8,\"name\":\"TRUFFLE PARMESAN POTATO CHIPS\",\"price\":281,\"quantity\":1},{\"id\":469,\"name\":\"CHICKEN PESTO PANINI\",\"price\":316,\"quantity\":1}]', 597.00, 'down', 'cash', 298.50, '2025-06-02 06:12:29'),
(12, 49, 'Realisana', '[{\"id\":432,\"name\":\"FRIED CALAMARI\",\"price\":242,\"quantity\":1}]', 242.00, 'full', 'cash', 242.00, '2025-06-03 16:16:29'),
(13, 50, 'Esme', '[{\"id\":5,\"name\":\"SIRRACHA BUFFALO WINGS 6 PCS\",\"price\":377,\"quantity\":1},{\"id\":469,\"name\":\"CHICKEN PESTO PANINI\",\"price\":316,\"quantity\":1},{\"id\":7,\"name\":\"CHICKEN WITH PARMESAN SHAVINGS\",\"price\":328,\"quantity\":1}]', 1021.00, 'full', 'cash', 1021.00, '2025-06-06 17:59:45'),
(14, 51, 'Aizzy Villanueva', '[{\"id\":8,\"name\":\"TRUFFLE PARMESAN POTATO CHIPS\",\"price\":281,\"quantity\":1}]', 281.00, 'down', 'cash', 140.50, '2025-06-09 04:10:12');

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `name`, `icon`, `price`) VALUES
(7, 'Soap', NULL, 0.00),
(8, 'bed', NULL, 1000.00),
(9, 'Toothpaste', NULL, 0.00),
(10, 'Bath Robes', NULL, 0.00),
(11, 'Slippers', NULL, 0.00);

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
  `room_number` varchar(20) DEFAULT NULL,
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
  `user_types` enum('admin','frontdesk') NOT NULL DEFAULT 'frontdesk',
  `num_adults` int(11) NOT NULL,
  `num_children` int(11) NOT NULL,
  `extra_bed` varchar(255) NOT NULL,
  `accepted_at` varchar(255) DEFAULT NULL,
  `amount_paid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `booking_reference`, `user_id`, `first_name`, `last_name`, `booking_type`, `email`, `contact`, `check_in`, `check_out`, `arrival_time`, `number_of_guests`, `room_type_id`, `room_number`, `room_quantity`, `payment_option`, `payment_method`, `total_amount`, `extra_charges`, `status`, `created_at`, `nights`, `downpayment_amount`, `remaining_balance`, `discount_type`, `discount_amount`, `discount_percentage`, `payment_reference`, `payment_proof`, `user_types`, `num_adults`, `num_children`, `extra_bed`, `accepted_at`, `amount_paid`) VALUES
(1, 'BK20250609032406253', NULL, 'Kenjo', 'Realisana', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-09', '2025-07-10', NULL, 1, 1, '102', 1, 'full', 'Cash', 64000.00, 124000.00, 'Checked in', '2025-06-08 19:24:06', 15, NULL, 154000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 30000),
(2, 'BK20250609033031692', NULL, 'fammela', 'Deguzman', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 30000.00, 0.00, 'Rejected', '2025-06-08 19:30:31', 23, 0.00, 30000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 30000),
(3, 'BK20250609033334188', NULL, 'fammela', 'Deguzman', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 30000.00, 0.00, 'Rejected', '2025-06-08 19:33:34', 23, 0.00, 30000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 30000),
(4, 'BK20250609034144291', NULL, 'fammela', 'Deguzman', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-09', '2025-07-12', NULL, 1, 1, NULL, 1, 'full', 'Cash', 200000.00, 132000.00, 'Checked Out', '2025-06-08 19:41:44', 23, NULL, 170000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 200000),
(5, 'BK20250609034640436', NULL, 'fammela', 'Deguzman', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-09', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'Cash', 64000.00, 0.00, 'Checked Out', '2025-06-08 19:46:40', 23, NULL, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(6, 'BK20250609034750711', NULL, 'fammela', 'Deguzman', NULL, 'fammeladeguzman21@gmail.com', '912345678787', '2025-06-17', '2025-09-01', NULL, 1, 1, '101', 1, 'full', 'cash', 152000.00, 0.00, 'Checked Out', '2025-06-08 19:47:50', 23, 0.00, 94000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 58000),
(7, 'BK20250609043601877', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, '103', 1, 'full', 'cash', 46000.00, 0.00, 'Checked Out', '2025-06-08 20:36:01', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(8, 'BK20250609061649419', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 46000.00, 0.00, 'Rejected', '2025-06-08 22:16:49', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(9, 'BK20250609062232561', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 46000.00, 0.00, 'Rejected', '2025-06-08 22:22:32', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(10, 'BK20250609062656607', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 46000.00, 0.00, 'Rejected', '2025-06-08 22:26:56', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(11, 'BK20250609063222940', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 10, NULL, 1, 'full', 'cash', 46000.00, 0.00, 'Rejected', '2025-06-08 22:32:22', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(12, 'BK20250609065652119', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 46000.00, 0.00, 'Rejected', '2025-06-08 22:56:52', 23, 0.00, 46000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 46000),
(13, 'BK-68461F08E34BE', 1, 'Alfred hendrik', 'Aceveda', 'Online', 'admin@example.com', '09362715617', '2025-06-20', '2025-07-03', NULL, 1, 2, '434', 1, 'Partial Payment', 'Cash', 32500.00, 0.00, 'Checked Out', '2025-06-08 23:38:48', 13, 1500.00, 0.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL),
(14, 'BK20250609085211132', NULL, 'George', 'Khalifa', NULL, 'gorge@gmail.com', '093658698', '2025-06-10', '2025-07-10', NULL, 1, 1, '103', 1, 'full', 'GCash', 54000.00, 0.00, 'Checked Out', '2025-06-09 00:52:11', 30, 0.00, 60000.00, 'PWD', 6000.00, 10.00, '', '', 'frontdesk', 0, 0, '', NULL, 120000),
(15, 'BK-68465707C4A30', 57, 'Joanna', 'Hernandez', 'Online', 'joannamontero05@gmail.com', '09761564588', '2025-06-10', '2025-06-11', NULL, 1, 1, '104', 1, 'Partial Payment', 'Cash', 2000.00, 0.00, 'Checked Out', '2025-06-09 03:37:43', 1, 1500.00, 0.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL),
(16, 'BK-68465C6AD62AF', NULL, 'Aizzy', 'Villanueva', 'Online', 'aizzyvillanueva43@gmail.com', '9127418448', '2025-06-12', '2025-06-14', NULL, 2, 1, '104', 1, 'Partial Payment', 'Cash', 4000.00, 0.00, 'Checked Out', '2025-06-09 04:00:42', 2, 1500.00, 0.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 2, 0, '', NULL, NULL),
(17, 'BK-684709D512501', 66, 'Allyson', 'Carpio', 'Online', 'allysonmildred696@gmail.com', '09951779220', '2025-06-11', '2025-06-14', NULL, 3, 2, '301', 1, 'Custom Payment', 'Cash', 6750.00, 0.00, 'Checked Out', '2025-06-09 16:20:37', 3, 4000.00, 6750.00, 'PWD', 750.00, 10.00, '', '', 'frontdesk', 2, 1, '', NULL, NULL),
(18, 'BK-6847179B0EE30', 66, 'Allyson', 'Carpio', 'Online', 'allysonmildred696@gmail.com', '09951779220', '2025-06-11', '2025-06-13', NULL, 1, 2, '301', 1, 'Partial Payment', 'Cash', 5000.00, 0.00, 'Checked Out', '2025-06-09 17:19:23', 2, 1500.00, 5000.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL),
(19, 'BK-68471A45251E2', 66, 'Allyson', 'Carpio', 'Online', 'allysonmildred696@gmail.com', '09951779220', '2025-06-11', '2025-06-13', NULL, 1, 1, '101', 1, 'Partial Payment', 'Cash', 5760.00, 0.00, 'Checked Out', '2025-06-09 17:30:45', 2, 1500.00, 5760.00, 'PWD', 640.00, 10.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL),
(20, 'BK20250610020905719', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'gcash', 73600.00, 0.00, 'Rejected', '2025-06-09 18:09:05', 23, 0.00, 73600.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 73600),
(21, 'BK20250610021000300', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'gcash', 73600.00, 0.00, 'Rejected', '2025-06-09 18:10:00', 23, 0.00, 73600.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 73600),
(22, 'BK-20250609-ef6ab', NULL, 'Christian', 'Realisan', 'Walk-in', 'christianrealisan45@gmail.com', '912345678787', '2025-06-09', '2025-06-10', NULL, 1, 1, '103', 1, 'full', '', 3200.00, 0.00, 'Checked Out', '2025-06-09 19:47:47', 1, 0.00, 0.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, NULL),
(23, 'BK20250610035048365', NULL, NULL, NULL, NULL, 'christianrealisan45@gmail.com', '999999999', '1970-01-01', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 64892800.00, 0.00, 'Rejected', '2025-06-09 19:50:48', 20279, 0.00, 64892800.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 64892800),
(24, 'BK20250610035048406', NULL, NULL, NULL, NULL, 'christianrealisan45@gmail.com', '999999999', '1970-01-01', '2025-07-10', NULL, 1, 1, NULL, 1, 'full', 'cash', 64892800.00, 0.00, 'Rejected', '2025-06-09 19:50:48', 20279, 0.00, 64892800.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 64892800),
(25, 'BK20250610050802267', NULL, 'Aizzy', 'Villaluna', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-17', '2025-07-10', NULL, 1, 1, '101', 1, 'full', '', 73600.00, 0.00, 'Checked Out', '2025-06-09 21:08:02', 23, 0.00, 0.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 147200),
(26, 'BK-68476E253A8EB', 1, 'Alfred hendrik', 'Aceveda', 'Online', 'alfred@gmail.com', '09362715617', '2025-06-11', '2025-06-26', NULL, 1, 1, '202', 1, 'Partial Payment', 'gcash', 86400.00, 0.00, 'Checked in', '2025-06-09 23:28:37', 15, 1500.00, 94500.00, 'PWD', 9600.00, 10.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL),
(27, 'BK20250610112045174', NULL, 'malik', 'simbahan', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-11', '2025-07-10', NULL, 1, 1, '102', 1, 'full', 'gcash', 92800.00, 0.00, 'Checked In', '2025-06-10 03:20:45', 29, 0.00, 92800.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 92800),
(28, 'BK20250610114139919', NULL, 'malik', 'haha', NULL, 'christianrealisan45@gmail.com', '912345678787', '2025-06-11', '2025-07-10', NULL, 1, 2, '204', 1, 'full', 'Cash', 72500.00, 0.00, 'Checked Out', '2025-06-10 03:41:39', 29, 0.00, 72500.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 145000),
(29, 'BK20250611010308833', NULL, 'Gabriel ', 'Lumanglas', NULL, 'lumanglas@gmail.com', '09345812673', '2025-06-11', '2025-07-10', NULL, 1, 1, '103', 1, 'full', 'Cash', 96000.00, 0.00, 'Checked In', '2025-06-10 17:03:08', 28, NULL, 89600.00, NULL, 0.00, 0.00, '', '', 'frontdesk', 0, 0, '', NULL, 0),
(30, 'BK-684943F31E520', NULL, 'Shovel  Marie', 'Mabunao', 'Online', 'aizzyvillanueva43@gmail.com', '9636781352', '2025-06-12', '2025-06-14', NULL, 1, 2, '204', 1, 'Partial Payment', 'Cash', 4500.00, 0.00, 'Checked Out', '2025-06-11 08:53:07', 2, 1500.00, 3500.00, 'PWD', 500.00, 10.00, '', '', 'frontdesk', 1, 0, '', NULL, NULL);

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
(1, '2', 'reschedule', 'Booking rescheduled from 2025-05-06 - 2025-05-07 to 2025-05-14 - 2025-05-15', 'Admin', '2025-05-05 04:07:22'),
(2, '45', 'reschedule', 'Booking rescheduled from 2025-05-26 - 2025-05-28 to 2025-05-28 - 2025-05-30', 'Admin', '2025-05-21 04:08:54'),
(3, '45', 'reschedule', 'Booking rescheduled from 2025-05-28 - 2025-05-30 to 2025-05-22 - 2025-05-24', 'Admin', '2025-05-21 04:10:07'),
(4, '101', 'reschedule', 'Booking rescheduled from 2025-06-03 - 2025-06-05 to 2025-06-02 - 2025-06-04', 'Admin', '2025-06-02 13:25:49'),
(5, '7', 'reschedule', 'Booking rescheduled from 2025-06-17 - 2025-07-10 to 2025-06-10 - 2025-07-03', 'Admin', '2025-06-08 20:40:24'),
(6, '7', 'reschedule', 'Booking rescheduled from 2025-06-10 - 2025-07-03 to 2025-06-17 - 2025-07-10', 'Admin', '2025-06-08 20:40:39'),
(7, '16', 'reschedule', 'Booking rescheduled from 2025-06-10 - 2025-06-12 to 2025-06-12 - 2025-06-14', 'Admin', '2025-06-09 04:02:17');

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
(17, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', 'hello', 'new', '2025-04-30 03:23:30', '2025-04-30 03:23:30'),
(18, 'Christian', 'Realisan', 'christianrealisan45@gmail.com', 'Hello, bukas paba kayo?', 'new', '2025-05-10 02:49:31', '2025-05-10 02:49:31'),
(19, 'ZAP', 'ZAP', 'zaproxy@example.com', 'Zaproxy alias impedit expedita quisquam pariatur exercitationem. Nemo rerum eveniet dolores rem quia dignissimos.', 'new', '2025-05-14 05:11:27', '2025-05-14 05:11:27'),
(20, 'RobertPekJG', 'RobertPekJG', 'nomin.momin+376k3@mail.ru', 'Gwhduwdjiwhduwh uhiwdjiwjdeufhu jikodwfiewfiwjdjw jidwjosqdijwifi jiwdowidqoiowufeugewi uiwjdiwurfuwyruewiai fwhuwhwhfuwhduwijdw e-akomoda.site', 'new', '2025-06-01 06:05:03', '2025-06-01 06:05:03'),
(21, 'Essie', 'Keir', 'essie@jeev.net', 'Greetings,\r\n\r\nKudos on your new domain e-akomoda.site! It\'s a great feeling to embark on a new online venture.\r\n\r\nAs part of our program to support new domain owners, I\'m reaching out with a practical resource for e-akomoda.site.\r\n\r\nI\'m talking about our the Google Maps Lead Collector - a Chrome extension that allows you to locate local businesses in your area. \r\n\r\nI\'ve created a quick video that demonstrates how it works:\r\nhttps://www.youtube.com/watch?time_continue=20&v=1Dd7i4vNgu0\r\n\r\nThis complimentary tool allows you to:\r\n\r\nEasily obtain targeted business leads\r\nDiscover potential clients in specific industries\r\nStreamline your prospecting\r\nBegin building your business immediately\r\nYou\'ll get 50 leads per search with no limit on the number of searches!\r\nSimply grab your free copy from the video description.\r\n\r\nhttps://www.youtube.com/watch?time_continue=20&v=1Dd7i4vNgu0\r\n\r\nWishing you success with e-akomoda.site! Feel free to reach out with any questions.\r\n\r\nBest regards, \r\nEssie Keir \r\nDigital Marketing Specialist', 'new', '2025-06-04 10:29:17', '2025-06-04 10:29:17');

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
(2, 'pwd', 10.00, 'Person with Disability Discount', 1, '2025-04-13 02:58:18'),
(0, 'student discount', 10.00, 'Studyante', 0, '2025-06-02 14:09:13'),
(0, 'student', 10.00, 'only', 0, '2025-06-07 13:19:25');

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
  `customer_name` varchar(255) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `user_id`, `customer_name`, `package_name`, `package_price`, `base_price`, `overtime_hours`, `overtime_charge`, `extra_guests`, `extra_guest_charge`, `total_amount`, `paid_amount`, `remaining_balance`, `reservation_date`, `event_type`, `event_date`, `start_time`, `end_time`, `number_of_guests`, `payment_method`, `payment_type`, `reference_number`, `payment_proof`, `booking_status`, `reserve_type`, `created_at`, `updated_at`, `booking_source`) VALUES
('EVT20250517232642208', 1, 'Samantha Almeda', 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '0000-00-00', 'Birthday', '2025-05-20', '13:24:00', '16:24:00', 21, 'Cash', 'Full Payment', '', NULL, 'completed', 'Regular', '2025-05-17 15:26:42', '2025-06-07 16:46:04', 'admin'),
('EVT20250601161206970', 3, 'Keae Yakomoto', 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '0000-00-00', 'Birthday', '2025-06-08', '12:00:00', '17:00:00', 30, 'GCash', 'Full Payment', '', NULL, 'rejected', 'Regular', '2025-06-01 08:12:06', '2025-06-01 08:14:18', 'admin'),
('EVT20250601161527829', 2, 'Keae Yakomoto', 'Package B', 55000.00, 55000.00, 0, 0.00, 0, 0.00, 55000.00, 55000.00, 0.00, '0000-00-00', 'Birthday', '2025-06-08', '12:00:00', '17:00:00', 30, 'Cash', 'Full Payment', '', NULL, 'pending', 'Regular', '2025-06-01 08:15:27', '2025-06-01 08:15:27', 'admin'),
('TB20250517153010112', 62, '', 'Venue Rental Only', 20000.00, 20000.00, 0, 0.00, 0, 0.00, 20000.00, 20000.00, 0.00, '2025-05-19', 'Birthday', '0000-00-00', '11:29:00', '15:29:00', 50, 'gcash', 'full', '1023317335894', 'uploads/payment_proofs/payment_6828ab824ac6e_20250517_153010.jpg', 'rejected', NULL, '2025-05-17 15:30:10', '2025-06-02 02:31:15', 'Regular Booking'),
('TB20250519050238284', 63, '', 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-05-20', 'Birthday', '0000-00-00', '13:01:00', '18:00:00', 50, 'gcash', 'full', '989878987898', '../../uploads/payment_proofs/payment_682abb6e37b9b_20250519_050238.jpg', 'pending', NULL, '2025-05-19 05:02:38', '2025-05-19 05:02:38', 'Regular Booking'),
('TB20250519050959750', 63, '', 'Package A', 47500.00, 47500.00, 0, 0.00, 0, 0.00, 47500.00, 47500.00, 0.00, '2025-05-22', 'Birthday', '0000-00-00', '10:00:00', '15:00:00', 50, 'gcash', 'full', '10235478262628', '../../uploads/payment_proofs/payment_682abd27c9e9b_20250519_050959.jpeg', 'pending', NULL, '2025-05-19 05:09:59', '2025-05-19 05:09:59', 'Regular Booking');

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
(1, 'Venue Rental Only ', 20000.00, '5-hour venue rental\r\nTables and Tiffany chairs', 'uploads/event_packages/68062c9ec0b60.png', 'images/hall2.jpg', 'images/hall3.jpg', 30, 4, '2025-02-12 02:48:46', 1, NULL, 50, '5 hours', NULL, 'Available'),
(2, 'Package A', 47500.00, '5-hour venue rental      Tables     and Tiffany chairs', 'uploads/event_packages/67ff849fd3696.jpg', 'images/hall2.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, '1 Appetizers, 2 Pasta, 2 Mains, Salad Bar, Rice , Drinks', 50, '5 hours', NULL, 'Available'),
(3, 'Package B', 55000.00, '5-hour venue rental\r\nTables and  Tiffany chairs', 'uploads/event_packages/683cbff788f40.jpg', 'images/hall.jpg', 'images/hall3.jpg', 30, 5, '2025-02-12 02:48:46', 1, ' 2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert,  Drinks ', 50, '5 hours', '**Assumes 5,000g (100g per person) of Wagyu steak will be served.', 'Available'),
(4, 'Package C', 76800.00, '5-hour venue rental\r\nTables and Tiffany chairs', 'uploads/event_packages/68446de123d2f.jpg', 'images/hall2.jpg', 'images/hall.jpg', 30, 5, '2025-02-12 02:48:46', 1, '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2desserts, Drinks ', 50, '5 hours', NULL, 'Available'),
(12, 'Package Z', 3000.00, 'basta', 'uploads/event_packages/68446fd64e5ce.jpg', 'uploads/event_packages/68446fd64efb7.jpg', 'uploads/event_packages/68446fd65025c.jpg', 30, 4, '2025-06-07 16:59:02', 1, 'Standard Menu', 50, '5 hours', NULL, 'Available');

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
(5, 2, 'Fire extinguishers', 'check', 1, 1, '2025-03-05 11:21:30', '2025-06-01 06:06:33'),
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
(0, 9, '2025-05-04', '2025-05-05', '2025-05-02 16:39:44', 'uploads/featured_rooms/featured_6814f5504ccfa.jpg');

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
  `id_number` varchar(50) DEFAULT NULL,
  `id_image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_names`
--

INSERT INTO `guest_names` (`id`, `booking_id`, `first_name`, `last_name`, `guest_type`, `age`, `id_number`, `id_image_path`, `created_at`) VALUES
(1, 4, 'Jamil', 'Dt', 'regular', 0, '', NULL, '2025-05-13 05:21:58'),
(2, 3, 'Jessica ', 'Mendoza', 'regular', 0, '', NULL, '2025-05-13 16:25:26'),
(3, 3, 'Terrence ', 'Mendoza', 'regular', 0, '', NULL, '2025-05-13 16:25:26'),
(4, 4, 'Maria Quenn', 'Bautista', 'regular', 0, '', NULL, '2025-05-13 16:30:20'),
(5, 4, 'Errol', 'Acha', 'regular', 0, '', NULL, '2025-05-13 16:30:20'),
(6, 5, 'Ellyza', 'Madrid', 'regular', 0, '', NULL, '2025-05-13 16:38:49'),
(7, 7, 'Allyson', 'Carpio', 'regular', 0, '', NULL, '2025-05-13 16:53:28'),
(8, 7, 'Kiel Alexis', 'Carpio', '', 0, '', NULL, '2025-05-13 16:53:28'),
(9, 7, 'Kendrick', 'Carpio', 'regular', 0, '', NULL, '2025-05-13 16:53:28'),
(10, 16, 'AIIZZY', 'VILLANUEVA', 'regular', 0, '', NULL, '2025-05-13 18:13:38'),
(11, 35, 'Ellyza ', 'Madrid', 'regular', NULL, '1', NULL, '2025-05-18 15:16:34'),
(12, 36, 'Ellyza ', 'Madrid', 'regular', NULL, '1', NULL, '2025-05-18 15:51:15'),
(13, 37, 'Test ', 'Book', 'regular', NULL, '1', NULL, '2025-05-19 05:29:40'),
(14, 38, 'Geraldyn', 'Escarez', 'regular', NULL, '1', NULL, '2025-05-19 16:35:15'),
(15, 39, 'audrey', 'Luceno', 'regular', NULL, '1', NULL, '2025-05-20 04:59:24'),
(16, 46, 'Joanna', 'Marie', 'regular', NULL, '1', NULL, '2025-05-31 06:31:20'),
(17, 46, 'Leonard', 'Carpio', 'regular', NULL, '2', NULL, '2025-05-31 06:31:20'),
(18, 47, 'patrick', 'Villanueva', 'regular', NULL, '1', NULL, '2025-05-31 06:38:16'),
(19, 48, 'Luisa', 'Marasigan', 'regular', NULL, '1', NULL, '2025-05-31 06:51:51'),
(20, 88, 'Ralph ', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 10:41:09'),
(21, 88, 'Poldo', 'Almoguera', 'regular', NULL, '2', NULL, '2025-06-02 10:41:09'),
(22, 89, 'Precious', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 10:55:14'),
(23, 90, 'Teody', 'Maranao', 'regular', NULL, '1', NULL, '2025-06-02 11:00:59'),
(24, 91, 'Ashley', 'De guzman', 'regular', NULL, '1', NULL, '2025-06-02 11:07:22'),
(25, 92, 'Lander', 'Mabalo', 'regular', NULL, '1', NULL, '2025-06-02 11:11:26'),
(26, 93, 'Lander', 'Mabalo', 'regular', NULL, '1', NULL, '2025-06-02 11:14:37'),
(27, 94, 'Lander', 'Mabalo', 'regular', NULL, '1', NULL, '2025-06-02 11:21:46'),
(28, 95, 'Ashley', 'Maranao', 'regular', NULL, '1', NULL, '2025-06-02 11:25:27'),
(29, 96, 'Precious', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 11:32:42'),
(30, 97, 'Teody', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 11:36:31'),
(31, 98, 'Teody', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 11:49:57'),
(32, 99, 'Precious', 'Cleofe', 'regular', NULL, '1', NULL, '2025-06-02 12:08:03'),
(33, 100, 'jay ar', 'ylagan', 'regular', NULL, '1', NULL, '2025-06-02 13:09:47'),
(34, 100, 'Robin', 'Almarez', 'regular', NULL, '2', NULL, '2025-06-02 13:09:47'),
(35, 101, 'jay ar', 'ylagan', 'regular', NULL, '1', NULL, '2025-06-02 13:11:43'),
(36, 101, 'Robin', 'Almarez', 'regular', NULL, '2', NULL, '2025-06-02 13:11:43'),
(37, 143, 'Christian', 'Realisan', '', NULL, NULL, NULL, '2025-06-06 17:03:52'),
(38, 144, 'Allyson', 'Carpio', '', NULL, NULL, NULL, '2025-06-06 17:29:48'),
(39, 145, 'christian', 'realisan', '', NULL, NULL, NULL, '2025-06-06 17:34:18'),
(40, 146, 'Ellyza', 'Mildred', '', NULL, NULL, NULL, '2025-06-06 17:41:48'),
(41, 147, 'Test', 'BOok', '', NULL, NULL, NULL, '2025-06-06 17:47:07'),
(42, 150, 'Christian', 'Realisan', '', NULL, NULL, NULL, '2025-06-06 18:20:11'),
(43, 151, 'Test', 'BOok', '', NULL, NULL, NULL, '2025-06-06 18:34:24'),
(44, 152, 'Kenjo', 'Marimon', '', NULL, NULL, NULL, '2025-06-07 12:29:03'),
(45, 8, 'Alfred hendrik', 'Aceveda', '', NULL, NULL, NULL, '2025-06-08 15:34:21'),
(46, 13, 'Alfred hendrik', 'Aceveda', '', NULL, NULL, NULL, '2025-06-08 23:38:48'),
(47, 15, 'Joanna', 'Hernandez', '', NULL, NULL, NULL, '2025-06-09 03:37:43'),
(48, 16, 'Aizzy', 'Villanueva', '', NULL, NULL, NULL, '2025-06-09 04:00:42'),
(49, 17, 'Allyson', 'Carpio', '', NULL, NULL, NULL, '2025-06-09 16:20:37'),
(50, 18, 'Allyson', 'Carpio', '', NULL, NULL, NULL, '2025-06-09 17:19:23'),
(51, 19, 'Allyson', 'Carpio', '', NULL, NULL, NULL, '2025-06-09 17:30:45'),
(52, 26, 'Alfred hendrik', 'Aceveda', '', NULL, NULL, NULL, '2025-06-09 23:28:37'),
(53, 30, 'Shovel  Marie', 'Mabunao', '', NULL, NULL, NULL, '2025-06-11 08:53:07');

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
(1, 'Small-plates', 'SMALL PLATES'),
(2, 'Soup & Salad', 'SOUP & SALAD'),
(3, 'Pasta', 'PASTA'),
(4, 'Sandwiches', 'SANDWICHES'),
(7, 'Coffee & Latte (HOT / ICED)', 'COFFEE'),
(8, 'Tea', 'TEA'),
(9, 'Ice Blended', 'ICE BLENDED'),
(16, 'Smoothie', 'SMOOTHIE'),
(20, 'All Day Breakfast', 'ALL DAY BREAKFAST'),
(21, 'Beers', 'BEERS'),
(22, 'Liquors', 'LIQUORS'),
(23, 'Bread & Pastry', 'BREAD & PASTRY'),
(24, 'Main Course', 'MAIN COURSE'),
(25, 'All day dinner', 'All day dinner'),
(26, 'Aizzy', 'Aizzy'),
(27, 'Mix & Match', 'Mix & Match');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image_path`, `availability`, `is_available`) VALUES
(1, 2, 'CHICKEN ALFREDO WITH MUSHROOMS', '', 328.00, 'uploads/menus/menu_68064c5056c19.jpg', 1, 1),
(2, 1, 'Gambas and Chorizo', 'What', 368.00, 'uploads/menus/menu_6806492be850a.jpg', 1, 0),
(3, 1, 'HAND - CUT POTATO FRIES', '', 195.00, 'uploads/menus/menu_6814f79f042ba.jpg', 1, 1),
(4, 1, 'MOZZARELLA STICKS', '', 285.00, 'uploads/menus/menu_68064acbc96bf.jpg', 1, 1),
(5, 1, 'SIRRACHA BUFFALO WINGS 6 PCS', '', 377.00, 'uploads/menus/menu_68064b37539e7.jpg', 1, 1),
(6, 2, 'MOLO SOUP', '', 158.00, 'uploads/menus/menu_68175d5598fe5.jpg', 1, 1),
(7, 2, 'CHICKEN WITH PARMESAN SHAVINGS', '', 328.00, 'uploads/menus/menu_68175aab51646.jpg', 1, 1),
(8, 1, 'TRUFFLE PARMESAN POTATO CHIPS', 'hehe', 281.00, 'uploads/menus/menu_680649e5813d0.jpg', 1, 1),
(431, 3, 'SEAFOOD MARINARA', '', 334.00, 'uploads/menus/menu_68064c0df223e.jpg', 1, 1),
(432, 1, 'FRIED CALAMARI', '', 242.00, 'uploads/menus/menu_68064a2890b16.jpg', 1, 1),
(433, 2, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', '', 344.00, 'uploads/menus/menu_68175d9cb45bb.jpg', 1, 1),
(434, 7, 'Espresso (HOT)', '', 95.00, 'uploads/menus/menu_6817806f3aea9.png', 1, 1),
(435, 7, 'Espresso', '', 95.00, '', 1, 1),
(446, 8, 'STRAWBERRY', '', 115.00, '', 1, 1),
(447, 7, 'AMERICANO (HOT)', '', 110.00, '', 1, 1),
(448, 3, 'CARBONARA (NO CREAM)', '', 327.00, 'uploads/menus/menu_68175e160f358.jpg', 1, 1),
(458, 20, 'HOMEMADE TENDERLOIN BEEF TAPA', '', 336.00, 'uploads/menus/menu_6817618bade05.jpg', 1, 1),
(459, 20, 'HOMEMADE CHICKEN TOCINO', '', 304.00, '', 1, 1),
(460, 20, 'HOMEMADE DAING NA BANGUS', '', 312.00, 'uploads/menus/menu_68176014d82a1.jpg', 1, 1),
(461, 20, 'DAING NA BIYA', '', 311.00, 'uploads/menus/menu_68176061720c3.jpg', 1, 1),
(462, 20, 'PINOY STYLE LONGGANISA', '', 307.00, '', 1, 1),
(463, 20, 'DARK CHOCOLATE CHAMPORADO', '', 261.00, 'uploads/menus/menu_6817624868614.jpg', 1, 1),
(464, 20, 'BANANA WALNUT BRIOCHE FRENCH TOAST', '', 258.00, '', 1, 1),
(465, 20, 'AMERICAN BREAKFAST', '', 323.00, 'uploads/menus/menu_6817609189eee.jpg', 1, 1),
(466, 20, 'BUILD YOUR OWN OMELETTE', '', 236.00, '', 1, 1),
(467, 3, 'SHRIMP AGLIO OLIO', '', 348.00, 'uploads/menus/menu_681762fa482cd.jpg', 1, 1),
(468, 3, 'ALLE VONGOLE', '', 321.00, '', 1, 1),
(469, 4, 'CHICKEN PESTO PANINI', '', 316.00, 'uploads/menus/menu_681758e998e91.jpg', 1, 1),
(470, 4, 'PHILLY CHEESESTEAK PANINI', '', 307.00, 'uploads/menus/menu_681759c16d434.jpg', 1, 1),
(471, 8, 'PEPERMINT', '', 125.00, '', 1, 1),
(472, 8, 'HISBISCUS', '', 130.00, '', 1, 1),
(473, 8, 'CHAMOMILE', '', 115.00, '', 1, 1),
(474, 8, 'HONEY LEMON', '', 125.00, '', 1, 1),
(475, 8, 'JASMINE', '', 125.00, '', 1, 1),
(476, 7, 'AMERICANO (COLD)', '', 120.00, '', 1, 1),
(477, 7, 'CAPPUCCINO (HOT)', '', 120.00, 'uploads/menus/menu_68175f942d527.jpg', 1, 1),
(478, 7, 'CAPPUCCINO (COLD)', '', 130.00, '', 1, 1),
(479, 7, 'CARAMEL MACCHIATO (HOT)', '', 160.00, '', 1, 1),
(480, 7, 'CARAMEL MACCHIATO (ICED)', '', 195.00, '', 1, 1),
(481, 7, 'MOCHA', '', 155.00, 'uploads/menus/menu_681760d4a8507.jpg', 1, 1),
(482, 7, 'MOCHA (ICED)', '', 185.00, '', 1, 1),
(483, 7, 'MATCHA LATTE (HOT)', '', 130.00, '', 1, 1),
(484, 7, 'MATCHA LATTE (ICED)', '', 140.00, '', 1, 1),
(485, 9, 'COOKIES & CREAM', '', 195.00, 'uploads/menus/menu_6817645fb9a48.jpg', 1, 1),
(486, 9, 'STRAWBERRY MILK', '', 165.00, 'uploads/menus/menu_68175f284b1c0.jpg', 1, 1),
(487, 9, 'COFFEE JELLY', '', 170.00, '', 1, 1),
(488, 9, 'CARAMEL MACCHIATO ', '', 195.00, '', 1, 1),
(489, 9, 'JAVA CHIP', '', 185.00, '', 1, 1),
(490, 9, 'CHOCOLATE', '', 190.00, '', 1, 1),
(491, 9, 'MATCHA', '', 160.00, 'uploads/menus/menu_68176376c7716.jpg', 1, 1),
(492, 16, 'MANGO', '', 175.00, 'uploads/menus/menu_6817614a7e1ab.jpg', 1, 1),
(493, 16, 'GUYABANO', '', 135.00, 'uploads/menus/menu_681761bcad459.jpg', 1, 1),
(494, 24, 'HOUSE FRIED CHICKEN (2 PCS)', '', 319.00, 'uploads/menus/menu_68175cf5c5e0f.jpg', 1, 1),
(495, 24, 'HOUSE FRIED CHICKEN (3 PCS)', '', 395.00, 'uploads/menus/menu_68175eb6cf122.jpg', 1, 1),
(496, 24, 'CHICKEN PARMIGIANA', '', 387.00, 'uploads/menus/menu_68175c2d39636.jpg', 1, 1),
(497, 24, 'BAKED FISH IN PARCHMENT', '', 359.00, '', 1, 1),
(498, 24, 'PAN-FRIED PORK STEAK', '', 392.00, 'uploads/menus/menu_68175b20d6961.jpg', 1, 1),
(499, 24, 'USDA PRIME BEEF SALPICAO', '', 753.00, 'uploads/menus/menu_681764dd42206.jpg', 1, 1),
(500, 24, 'DRUNKEN PORK BELLY', '', 397.00, 'uploads/menus/menu_68175e81ba1b8.jpg', 1, 1),
(501, 24, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', '', 750.00, 'uploads/menus/menu_681762a6ae043.jpg', 1, 1),
(502, 24, 'USDA ANGUS RIBEYE (CHOICE GRADE) (100g)', '', 530.00, 'uploads/menus/menu_681762bb8e454.jpg', 1, 1),
(504, 21, 'HOEGAARDEN', '', 185.00, 'uploads/menus/menu_681753b81055f.png', 1, 1),
(505, 21, 'STELLA ARTOIS', '', 175.00, 'uploads/menus/menu_68175bccae591.png', 1, 1),
(506, 22, 'JOSE CUERVO SILVER', '', 130.00, '', 1, 1),
(507, 23, 'Carrot Cake Slice', '', 190.00, 'uploads/menus/menu_68176deb3f8a0.png', 1, 1),
(508, 4, 'CLASSIC CHEESEBURGER', '', 370.00, '', 1, 1),
(510, 1, 'pancit Bihon', '', 120.00, 'uploads/menus/menu_683d6882b6e79.jpg', 1, 1),
(511, 23, 'BLUEBERRY CHEESECAKE', '', 565.00, 'uploads/menus/menu_683d69215bb36.jpg', 1, 1),
(512, 23, 'DARK CHOCOLATE COOKIES', '', 500.00, 'uploads/menus/menu_683d694906f78.jpg', 1, 1),
(513, 23, 'REVEL', '', 520.00, 'uploads/menus/menu_683d695fa883b.jpg', 1, 1),
(514, 25, 'Fried Siken', '', 120.00, 'uploads/menus/menu_683dad3374dbb.png', 1, 1);

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
(1, 431, 'PARMESAN (10g)', 50.00),
(2, 431, 'GARLIC CIABATTA (2pcs)', 50.00),
(3, 1, 'PARMESAN (10g)', 50.00),
(4, 1, 'GARLIC CIABATTA BREAD (2pcs)', 50.00),
(5, 448, 'PARMESAN (10g)', 50.00),
(6, 448, 'GARLIC CIABATTA BREAD (2pcs)', 50.00),
(7, 467, 'PARMESAN (10g)', 50.00),
(8, 467, 'GARLIC CIABATTA BREAD (2pcs)', 50.00),
(9, 468, 'PARMESAN (10g)', 50.00),
(10, 468, 'GARLIC CIABATTA BREAD (2pcs)', 50.00),
(11, 514, 'gravy', 10.00),
(12, 514, 'ketchup', 5.00),
(13, 504, 'One bucket of ice cube', 50.00);

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
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender_type` enum('user','admin','system') NOT NULL,
  `read_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'unread',
  `Replies` varchar(255) NOT NULL,
  `audio_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `sender_type`, `read_status`, `created_at`, `status`, `Replies`, `audio_path`) VALUES
(1, 5, 'open pa po kayo ?', 'user', 1, '2025-04-12 06:31:08', 'unread', '', NULL),
(2, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:31:08', 'unread', '', NULL),
(3, 5, 'book', 'user', 1, '2025-04-12 06:34:16', 'unread', '', NULL),
(4, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:34:16', 'unread', '', NULL),
(5, 5, 'book', 'user', 1, '2025-04-12 06:35:58', 'unread', '', NULL),
(6, 5, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 06:35:58', 'unread', '', NULL),
(7, 33, 'open pa po kayo ?', 'user', 1, '2025-04-12 09:54:24', 'unread', '', NULL),
(8, 33, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 09:54:24', 'unread', '', NULL),
(10, 31, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-04-12 10:11:04', 'unread', '', NULL),
(11, 1, 'ay hoy', 'user', 1, '2025-04-13 01:57:28', 'unread', 'Admin to Unknown User: bakit', NULL),
(12, 1, 'ow', 'admin', 1, '2025-05-09 20:22:11', 'unread', '', NULL),
(13, 1, 'oy wazzup', 'admin', 1, '2025-05-10 02:56:06', 'unread', '', NULL),
(14, 1, 'musta', 'admin', 1, '2025-05-10 02:56:10', 'unread', '', NULL),
(15, 31, 'aa', 'admin', 1, '2025-05-16 02:17:05', 'unread', '', NULL),
(26, 29, 'hhyeee', 'user', 1, '2025-05-16 02:21:00', 'unread', '', NULL),
(27, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 02:21:00', 'unread', '', NULL),
(28, 29, 'why', 'admin', 1, '2025-05-16 02:22:49', 'read', '', NULL),
(29, 29, 'wala lang', 'user', 1, '2025-05-16 02:25:58', 'unread', '', NULL),
(30, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 02:25:58', 'unread', '', NULL),
(31, 29, 'open pa po kayo ?', 'user', 1, '2025-05-16 02:42:08', 'unread', '', NULL),
(32, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 02:42:08', 'unread', '', NULL),
(33, 29, 'open pa po kayo ?', 'user', 1, '2025-05-16 02:45:26', 'unread', '', NULL),
(34, 29, 'opo ang kulet', 'admin', 1, '2025-05-16 02:45:59', 'read', '', NULL),
(35, 29, 'ohh', 'admin', 1, '2025-05-16 02:52:45', 'read', '', NULL),
(36, 29, 'open pa po kayo ?', 'user', 1, '2025-05-16 02:55:31', 'unread', '', NULL),
(37, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 02:55:31', 'unread', '', NULL),
(38, 29, 'hhyeee', 'user', 1, '2025-05-16 02:56:11', 'unread', '', NULL),
(39, 29, 'hhyeee', 'user', 1, '2025-05-16 03:00:31', 'unread', '', NULL),
(40, 29, 'aa', 'admin', 1, '2025-05-16 03:00:44', 'read', '', NULL),
(41, 29, 'amo', 'user', 1, '2025-05-16 03:05:37', 'unread', '', NULL),
(42, 29, 'oh', 'admin', 1, '2025-05-16 03:06:01', 'read', '', NULL),
(43, 52, 'parang hotdog sa cafe lang nakakapag message. Aray kooOoOoo', 'user', 1, '2025-05-16 04:33:59', 'unread', '', NULL),
(44, 52, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-05-16 04:33:59', 'unread', '', NULL),
(45, 52, 'para kang balat', 'user', 1, '2025-05-16 04:34:40', 'unread', '', NULL),
(46, 52, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-05-16 04:34:40', 'unread', '', NULL),
(47, 29, 'Hello good afternoon', 'user', 1, '2025-05-16 04:34:56', 'unread', '', NULL),
(48, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 04:34:56', 'unread', '', NULL),
(49, 52, 'may footlong kayong tinda?', 'user', 1, '2025-05-16 04:37:17', 'unread', '', NULL),
(50, 52, 'Thank you for your message. Our team will get back to you soon.', 'system', 0, '2025-05-16 04:37:17', 'unread', '', NULL),
(51, 29, 'hi', 'user', 1, '2025-05-16 07:06:32', 'unread', '', NULL),
(52, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:06:32', 'unread', '', NULL),
(53, 29, 'hello', 'admin', 1, '2025-05-16 07:06:56', 'unread', '', NULL),
(54, 29, 'Hi pogi', 'user', 1, '2025-05-16 07:24:50', 'unread', '', NULL),
(55, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:24:50', 'unread', '', NULL),
(56, 29, 'hi', 'user', 1, '2025-05-16 07:27:50', 'unread', '', NULL),
(57, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:27:50', 'unread', '', NULL),
(58, 52, 'wala', 'admin', 1, '2025-05-16 07:28:27', 'unread', '', NULL),
(59, 29, 'hello good evening', 'user', 1, '2025-05-16 07:41:20', 'unread', '', NULL),
(60, 29, 'hi good evening', 'admin', 1, '2025-05-16 07:42:18', 'unread', '', NULL),
(61, 29, 'HEllo', 'user', 1, '2025-05-16 07:42:56', 'unread', '', NULL),
(62, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:42:56', 'unread', '', NULL),
(63, 29, 'HEllo', 'user', 1, '2025-05-16 07:45:40', 'unread', '', NULL),
(64, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-16 07:45:40', 'unread', '', NULL),
(65, 29, 'hii', 'user', 1, '2025-05-16 07:51:07', 'unread', '', NULL),
(66, 29, 'ano po oras arrival niyo?', 'admin', 1, '2025-05-16 07:51:36', 'unread', '', NULL),
(67, 29, 'Good afternoon po', 'user', 1, '2025-05-16 07:57:49', 'unread', '', NULL),
(68, 29, 'goo', 'user', 1, '2025-05-16 07:58:12', 'unread', '', NULL),
(69, 29, 'hello good evening', 'user', 1, '2025-05-16 08:00:06', 'unread', '', NULL),
(70, 29, 'hello good evening', 'user', 1, '2025-05-16 08:00:06', 'unread', '', NULL),
(71, 29, 'hello good evening', 'user', 1, '2025-05-16 08:00:10', 'unread', '', NULL),
(72, 29, 'Ano po oras closing niyo', 'user', 1, '2025-05-16 08:10:35', 'unread', '', NULL),
(73, 29, '11 pm po', 'admin', 1, '2025-05-16 08:11:25', 'unread', '', NULL),
(74, 29, 'bakit po', 'admin', 1, '2025-05-16 08:11:35', 'unread', '', NULL),
(75, 40, 'Meron pa po ba ice tea', 'user', 1, '2025-05-16 08:15:53', 'unread', '', NULL),
(76, 40, 'pa deliver po sa room', 'user', 1, '2025-05-16 08:16:12', 'unread', '', NULL),
(77, 40, 'hi', 'user', 1, '2025-05-16 08:17:13', 'unread', '', NULL),
(78, 40, 'Hello', 'admin', 1, '2025-05-16 08:17:34', 'unread', '', NULL),
(79, 40, 'Location po', 'admin', 1, '2025-05-16 08:17:43', 'unread', '', NULL),
(80, 29, 'Good afternoon po', 'user', 1, '2025-05-16 08:19:45', 'unread', '', NULL),
(81, 29, 'Voice message', 'user', 1, '2025-05-16 08:30:00', 'unread', '', 'uploads/audio/voice_29_1747384200.webm'),
(82, 29, 'Voice message', 'user', 1, '2025-05-16 08:30:46', 'unread', '', 'uploads/audio/voice_29_1747384246.webm'),
(83, 29, 'hey', 'user', 1, '2025-05-16 10:29:33', 'unread', '', NULL),
(84, 29, 'good evening', 'admin', 1, '2025-05-16 10:29:55', 'unread', '', NULL),
(85, 57, 'hello goodmorning', 'user', 1, '2025-05-21 04:15:19', 'unread', '', NULL),
(86, 57, 'pa resched po ng check in date may emergency lang po', 'user', 1, '2025-05-21 04:16:01', 'unread', '', NULL),
(87, 57, 'what date po ?', 'admin', 1, '2025-05-21 04:16:43', 'unread', '', NULL),
(88, 57, 'move lang po sa may 22 thankyou po', 'user', 1, '2025-05-21 04:18:34', 'unread', '', NULL),
(89, 57, 'okay po please wait for the email message for the awareness of rescheduling po thankyou :)', 'admin', 1, '2025-05-21 04:19:28', 'unread', '', NULL),
(90, 29, 'Hello good afternoon', 'user', 1, '2025-05-30 11:09:53', 'unread', '', NULL),
(91, 29, 'Thank you for your message. Our team will get back to you soon.', 'system', 1, '2025-05-30 11:09:53', 'unread', '', NULL),
(92, 67, 'hello goodmorning', 'user', 1, '2025-05-31 07:05:01', 'unread', '', NULL),
(93, 29, 'Hi', 'user', 1, '2025-05-31 15:09:34', 'unread', '', NULL),
(94, 29, 'hello', 'admin', 1, '2025-05-31 15:09:54', 'unread', '', NULL),
(95, 29, 'aa', 'admin', 1, '2025-05-31 15:10:08', 'unread', '', NULL),
(96, 66, 'hello', 'user', 0, '2025-06-09 16:24:04', 'unread', '', NULL);

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
(70, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-10 21:48:38'),
(71, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 1, '2025-04-10 21:48:58'),
(72, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 1, '2025-04-10 22:01:57'),
(73, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 1, '2025-04-10 22:02:48'),
(74, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 1, '2025-04-10 22:05:04'),
(75, 0, '', 'Booking #5 has been checked in', 'booking', '', 5, 1, '2025-04-10 22:10:34'),
(76, 0, '', 'Booking #6 has been checked in', 'booking', '', 6, 1, '2025-04-10 22:14:40'),
(77, 0, '', 'Early checkout processed. Amount to return: ₱220,000.00', 'booking', '', 6, 1, '2025-04-10 22:30:03'),
(78, 0, '', 'Booking #6 has been ', 'booking', '', 6, 1, '2025-04-10 22:30:03'),
(79, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-04-10 22:31:26'),
(80, 0, '', 'Early checkout processed. Amount to return: ₱49,500.00', 'booking', '', 4, 1, '2025-04-10 22:31:33'),
(81, 0, '', 'Booking #4 has been ', 'booking', '', 4, 1, '2025-04-10 22:31:34'),
(82, 0, '', 'Early checkout processed. Amount to return: ₱49,500.00', 'booking', '', 4, 1, '2025-04-10 22:31:36'),
(83, 0, '', 'Booking #4 has been ', 'booking', '', 4, 1, '2025-04-10 22:31:36'),
(84, 0, '', 'Early checkout processed. Amount to return: ₱260,000.00', 'booking', '', 5, 1, '2025-04-10 22:34:32'),
(85, 0, '', 'Booking #7 has been checked in', 'booking', '', 7, 1, '2025-04-10 22:35:59'),
(86, 0, '', 'Early checkout processed. Amount to return: ₱135,000.00', 'booking', '', 7, 1, '2025-04-10 22:37:22'),
(87, 0, '', 'Booking #9 has been checked in', 'booking', '', 9, 1, '2025-04-11 06:37:54'),
(88, 0, '', 'Booking #11 has been checked in', 'booking', '', 11, 1, '2025-04-11 06:54:04'),
(89, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 1, '2025-04-11 06:55:08'),
(90, 0, '', 'Early checkout processed. Amount to return: ₱8,100.00', 'booking', '', 1, 1, '2025-04-11 08:43:15'),
(91, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-11 08:44:59'),
(92, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 1, '2025-04-11 09:00:39'),
(93, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 1, '2025-04-11 09:04:32'),
(94, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-11 09:10:49'),
(95, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 1, '2025-04-11 09:14:09'),
(96, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 1, '2025-04-11 09:18:19'),
(97, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 1, '2025-04-12 04:44:01'),
(98, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 1, '2025-04-12 04:45:31'),
(99, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 1, '2025-04-12 04:47:23'),
(100, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-503357 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 5, 1, '2025-04-12 02:28:23'),
(101, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-8E7A2C has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 6, 1, '2025-04-12 02:30:09'),
(102, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-999830 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 7, 1, '2025-04-12 02:32:01'),
(103, 0, '', 'Table Reservation #27 has been cancelled. Reason: booking_mistake', 'table_cancelled', '', NULL, 1, '2025-04-12 04:37:56'),
(104, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-C25499 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 8, 1, '2025-04-12 05:04:52'),
(105, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-129B01 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-13', 'booking', 'fas fa-calendar-check', 1, 1, '2025-04-12 05:06:25'),
(106, 0, '', 'Table Reservation #31 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-04-12 05:47:17'),
(107, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-E26DF1 has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 2, 1, '2025-04-12 06:18:43'),
(108, 5, 'New Booking Confirmation', 'Your booking #BK-20250412-66AFDF has been confirmed. Check-in date: 2025-04-12, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 3, 1, '2025-04-12 06:22:42'),
(109, 5, 'New Booking Confirmation', 'Your booking #BK-20250413-355ED6 has been confirmed. Check-in date: 2025-04-13, Check-out date: 2025-04-17', 'booking', 'fas fa-calendar-check', 4, 1, '2025-04-12 22:54:46'),
(110, 1, 'New Booking Confirmation', 'Your booking #BK-20250413-747C66 has been confirmed. Check-in date: 2025-04-17, Check-out date: 2025-04-18', 'booking', 'fas fa-calendar-check', 5, 1, '2025-04-12 23:49:04'),
(111, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-13 00:48:47'),
(112, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-04-13 00:54:21'),
(113, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 1, '2025-04-13 02:18:09'),
(114, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 1, '2025-04-13 02:18:41'),
(115, 29, 'New Booking Confirmation', 'Your booking #BK-20250415-625949 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-05-01', 'booking', 'fas fa-calendar-check', 7, 1, '2025-04-15 08:05:50'),
(116, 29, 'New Booking Confirmation', 'Your booking #BK-20250415-A4A7B0 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-04-16', 'booking', 'fas fa-calendar-check', 8, 1, '2025-04-15 08:11:59'),
(117, 31, 'New Booking Confirmation', 'Your booking #BK-20250415-472828 has been confirmed. Check-in date: 2025-04-15, Check-out date: 2025-04-16', 'booking', 'fas fa-calendar-check', 9, 1, '2025-04-15 08:56:55'),
(118, 36, 'New Booking Confirmation', 'Your booking #BK-20250415-C71161 has been confirmed. Check-in date: 2025-04-16, Check-out date: 2025-04-22', 'booking', 'fas fa-calendar-check', 10, 1, '2025-04-15 13:17:48'),
(119, 0, '', 'Table Reservation #1 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-04-16 04:25:53'),
(120, 0, '', 'Table Reservation #3 has been cancelled. Reason: booking_mistake', 'table_cancelled', '', NULL, 1, '2025-04-16 09:38:05'),
(121, 0, '', 'Booking #2 has been checked out', 'booking', '', 2, 1, '2025-04-16 23:26:55'),
(122, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-04-16 23:44:35'),
(123, 0, '', 'Table Reservation #10 has been cancelled. Reason: emergency', 'table_cancelled', '', NULL, 1, '2025-04-19 10:36:31'),
(124, 0, '', 'Room transfer processed for booking #7. Transferred to new room type.', 'room_transfer', '', 7, 1, '2025-04-21 08:42:48'),
(125, 32, 'New Booking Confirmation', 'Your booking #BK-20250421-109C53 has been confirmed. Check-in date: 2025-04-21, Check-out date: 2025-04-23', 'booking', 'fas fa-calendar-check', 26, 1, '2025-04-21 08:59:19'),
(126, 14, 'New Booking Confirmation', 'Your booking #BK-20250421-889075 has been confirmed. Check-in date: 2025-04-22, Check-out date: 2025-04-25', 'booking', 'fas fa-calendar-check', 27, 1, '2025-04-21 11:47:13'),
(127, 14, 'New Booking Confirmation', 'Your booking #BK-20250421-2311AB has been confirmed. Check-in date: 2025-04-22, Check-out date: 2025-04-24', 'booking', 'fas fa-calendar-check', 28, 1, '2025-04-21 11:50:03'),
(128, 0, '', 'Booking #12 has been checked out', 'booking', '', 12, 1, '2025-04-21 12:00:20'),
(129, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 1, '2025-04-22 17:18:42'),
(130, 29, 'New Booking Confirmation', 'Your booking #BK-20250422-AD6E0E has been confirmed. Check-in date: 2025-04-23, Check-out date: 2025-04-26', 'booking', 'fas fa-calendar-check', 3, 1, '2025-04-22 17:20:28'),
(131, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-22 17:22:29'),
(132, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-04-22 17:22:52'),
(133, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 1, '2025-04-23 07:08:27'),
(134, 0, '', 'Table Reservation #13 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-04-24 05:15:02'),
(135, 38, 'New Booking Confirmation', 'Your booking #BK-20250424-2110D9 has been confirmed. Check-in date: 2025-04-25, Check-out date: 2025-04-27', 'booking', 'fas fa-calendar-check', 3, 1, '2025-04-24 14:12:08'),
(136, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 1, '2025-04-24 14:19:01'),
(137, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-04-24 14:47:06'),
(138, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-04-24 14:47:37'),
(139, 29, 'New Booking Confirmation', 'Your booking #BK-20250428-3A109F has been confirmed. Check-in date: 2025-04-28, Check-out date: 2025-04-29', 'booking', 'fas fa-calendar-check', 4, 1, '2025-04-28 03:45:29'),
(140, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 1, '2025-04-28 14:08:20'),
(141, 0, '', 'Booking #5 has been checked in', 'booking', '', 5, 1, '2025-04-28 14:42:33'),
(142, 0, '', 'Booking #5 has been checked out', 'booking', '', 5, 1, '2025-04-28 14:48:51'),
(143, 0, '', 'Booking #6 has been checked in', 'booking', '', 6, 1, '2025-04-28 15:00:00'),
(144, 0, '', 'Booking #4 has been checked out', 'booking', '', 4, 1, '2025-04-28 15:28:34'),
(145, 0, '', 'Booking #1 has been checked in', 'booking', '', 1, 1, '2025-04-30 02:20:21'),
(146, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 1, '2025-04-30 02:21:07'),
(147, 37, 'New Booking Confirmation', 'Your booking #BK-20250430-56884A has been confirmed. Check-in date: 2025-05-02, Check-out date: 2025-05-04', 'booking', 'fas fa-calendar-check', 2, 1, '2025-04-30 17:34:20'),
(148, 37, 'New Booking Confirmation', 'Your booking #BK-20250430-A8C86B has been confirmed. Check-in date: 2025-05-03, Check-out date: 2025-05-04', 'booking', 'fas fa-calendar-check', 3, 1, '2025-04-30 17:37:02'),
(149, 0, '', 'Booking #2 has been checked in', 'booking', '', 2, 1, '2025-05-02 15:55:40'),
(150, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-05-03 04:23:18'),
(256, 29, 'New Order Placed', 'Order #25 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 25, 1, '2025-05-03 08:22:02'),
(257, 40, 'New Order Placed', 'Order #27 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 27, 1, '2025-05-03 09:55:38'),
(258, 0, '', 'Booking #5 has been checked in', 'booking', '', 5, 1, '2025-05-03 11:44:01'),
(259, 0, '', 'Booking #5 has been checked out', 'booking', '', 5, 1, '2025-05-03 11:44:36'),
(260, 40, 'New Order Placed', 'Order #15 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 15, 1, '2025-05-04 14:35:08'),
(261, 38, 'New Order Placed', 'Order #16 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 16, 1, '2025-05-04 14:43:46'),
(262, 38, 'New Booking Confirmation', 'Your booking #BK-20250509-5698E6 has been confirmed. Check-in date: 2025-05-10, Check-out date: 2025-05-11', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-09 02:48:05'),
(263, 29, '', 'Your order #82 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-05-09 05:16:35'),
(264, 51, 'New Booking Confirmation', 'Your booking #BK-20250511-505E28 has been confirmed. Check-in date: 2025-05-14, Check-out date: 2025-05-15', 'booking', 'fas fa-calendar-check', 1, 1, '2025-05-11 01:27:59'),
(265, 52, 'New Booking Confirmation', 'Your booking #BK-20250512-50AE91 has been confirmed. Check-in date: 2025-05-19, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-12 07:06:05'),
(266, 29, '', 'Your order #88 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-05-12 14:24:57'),
(267, 53, 'New Booking Confirmation', 'Your booking #BK-20250513-CB0AEC has been confirmed. Check-in date: 2025-05-13, Check-out date: 2025-05-15', 'booking', 'fas fa-calendar-check', 4, 1, '2025-05-13 05:21:58'),
(268, 52, 'New Booking Confirmation', 'Your booking #BK-20250513-756732 has been confirmed. Check-in date: 2025-05-19, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 3, 1, '2025-05-13 16:25:26'),
(269, 51, 'New Booking Confirmation', 'Your booking #BK-20250513-FB2A11 has been confirmed. Check-in date: 2025-05-17, Check-out date: 2025-05-18', 'booking', 'fas fa-calendar-check', 4, 1, '2025-05-13 16:30:20'),
(270, 62, 'New Booking Confirmation', 'Your booking #BK-20250513-B9825B has been confirmed. Check-in date: 2025-05-23, Check-out date: 2025-05-26', 'booking', 'fas fa-calendar-check', 5, 1, '2025-05-13 16:38:49'),
(271, 63, 'New Booking Confirmation', 'Your booking #BK-20250513-CF5163 has been confirmed. Check-in date: 2025-06-10, Check-out date: 2025-06-14', 'booking', 'fas fa-calendar-check', 7, 1, '2025-05-13 16:53:28'),
(272, 0, '', 'Booking #5 has been checked in', 'booking', '', 5, 1, '2025-05-13 17:34:32'),
(273, 0, '', 'Booking #5 has been checked out', 'booking', '', 5, 1, '2025-05-13 17:34:47'),
(274, 0, '', 'Booking #1 has been checked out', 'booking', '', 1, 1, '2025-05-13 17:37:05'),
(275, 0, '', 'Booking #11 has been checked in', 'booking', '', 11, 1, '2025-05-13 18:06:18'),
(276, 0, '', 'Booking #11 has been checked out', 'booking', '', 11, 1, '2025-05-13 18:06:27'),
(277, 0, '', 'Booking #13 has been checked in', 'booking', '', 13, 1, '2025-05-13 18:07:03'),
(278, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 1, '2025-05-13 18:07:17'),
(279, 0, '', 'Booking #15 has been checked in', 'booking', '', 15, 1, '2025-05-13 18:10:26'),
(280, 0, '', 'Booking #15 has been checked out', 'booking', '', 15, 1, '2025-05-13 18:10:41'),
(281, 0, '', 'Booking #14 has been checked in', 'booking', '', 14, 1, '2025-05-13 18:22:16'),
(282, 0, '', 'Booking #14 has been checked out', 'booking', '', 14, 1, '2025-05-13 18:22:24'),
(283, 0, '', 'Booking #16 has been checked in', 'booking', '', 16, 1, '2025-05-13 18:37:19'),
(284, 0, '', 'Booking #16 has been checked out', 'booking', '', 16, 1, '2025-05-13 18:39:10'),
(285, 58, 'New Order Placed', 'Order #92 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 92, 1, '2025-05-14 07:24:11'),
(286, 0, '', 'Booking #19 has been checked in', 'booking', '', 19, 1, '2025-05-16 13:39:17'),
(287, 0, '', 'Booking #18 has been checked in', 'booking', '', 18, 1, '2025-05-16 13:39:54'),
(288, 0, '', 'Booking #3 has been checked in', 'booking', '', 3, 1, '2025-05-16 13:53:58'),
(289, 0, '', 'Booking #3 has been checked out', 'booking', '', 3, 1, '2025-05-16 13:56:05'),
(290, 0, '', 'Booking #4 has been checked in', 'booking', '', 4, 1, '2025-05-16 14:08:24'),
(291, 0, '', 'Booking #4 has been checked out', 'booking', '', 4, 1, '2025-05-16 14:08:45'),
(292, 0, '', 'Booking #7 has been checked in', 'booking', '', 7, 1, '2025-05-16 14:14:09'),
(293, 0, '', 'Booking #17 has been checked in', 'booking', '', 17, 1, '2025-05-16 14:16:12'),
(294, 0, '', 'Booking #20 has been checked in', 'booking', '', 20, 1, '2025-05-16 14:50:10'),
(295, 62, 'New Booking Confirmation', 'Your booking #BK-20250518-F576CE has been confirmed. Check-in date: 2025-05-18, Check-out date: 2025-05-19', 'booking', 'fas fa-calendar-check', 35, 1, '2025-05-18 15:16:34'),
(296, 62, 'New Booking Confirmation', 'Your booking #BK-20250518-E83E6C has been confirmed. Check-in date: 2025-05-19, Check-out date: 2025-05-20', 'booking', 'fas fa-calendar-check', 36, 1, '2025-05-18 15:51:15'),
(297, 0, '', 'Table Reservation #19 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-05-18 16:09:24'),
(298, 29, 'New Order Placed', 'Order #23 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 23, 1, '2025-05-19 02:22:08'),
(299, 0, '', 'Table Reservation #41 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-05-19 02:38:21'),
(300, 29, 'New Booking Confirmation', 'Your booking #BK-20250519-ECD4EA has been confirmed. Check-in date: 2025-05-19, Check-out date: 2025-05-21', 'booking', 'fas fa-calendar-check', 37, 1, '2025-05-19 05:29:40'),
(301, 61, 'New Booking Confirmation', 'Your booking #BK-20250519-D05C94 has been confirmed. Check-in date: 2025-05-21, Check-out date: 2025-05-22', 'booking', 'fas fa-calendar-check', 38, 1, '2025-05-19 16:35:15'),
(302, 63, 'New Booking Confirmation', 'Your booking #BK-20250520-6E7620 has been confirmed. Check-in date: 2025-05-22, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 39, 1, '2025-05-20 04:59:24'),
(303, 63, 'New Booking Confirmation', 'Your booking #BK-20250520-D8C111 has been confirmed. Check-in date: 2025-05-23, Check-out date: 2025-05-27', 'booking', 'fas fa-calendar-check', 40, 1, '2025-05-20 06:09:08'),
(304, 60, 'New Booking Confirmation', 'Your booking #BK-20250520-92DC4B has been confirmed. Check-in date: 2025-05-21, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 41, 1, '2025-05-20 07:55:19'),
(305, 63, 'New Booking Confirmation', 'Your booking #BK-20250520-512437 has been confirmed. Check-in date: 2025-05-22, Check-out date: 2025-06-02', 'booking', 'fas fa-calendar-check', 42, 1, '2025-05-20 07:57:26'),
(306, 60, 'New Booking Confirmation', 'Your booking #BK-20250520-295A9F has been confirmed. Check-in date: 2025-05-21, Check-out date: 2025-05-23', 'booking', 'fas fa-calendar-check', 43, 1, '2025-05-20 08:00:20'),
(307, 63, 'New Booking Confirmation', 'Your booking #BK-20250520-36B604 has been confirmed. Check-in date: 2025-05-21, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 44, 1, '2025-05-20 08:04:17'),
(308, 57, 'New Booking Confirmation', 'Your booking #BK-20250520-B19041 has been confirmed. Check-in date: 2025-05-26, Check-out date: 2025-05-28', 'booking', 'fas fa-calendar-check', 45, 1, '2025-05-20 08:04:54'),
(309, 0, '', 'Booking #7 has been checked out', 'booking', '', 7, 1, '2025-05-21 04:21:50'),
(310, 0, '', 'Booking #17 has been checked out', 'booking', '', 17, 1, '2025-05-21 04:22:11'),
(311, 0, '', 'Booking #20 has been checked out', 'booking', '', 20, 1, '2025-05-21 04:22:26'),
(312, 0, '', 'Booking #42 has been checked in', 'booking', '', 42, 1, '2025-05-25 13:49:25'),
(313, 0, '', 'Booking #42 has been checked out', 'booking', '', 42, 1, '2025-05-25 13:55:08'),
(314, 0, '', 'Booking #44 has been checked in', 'booking', '', 44, 1, '2025-05-29 17:34:47'),
(315, 0, '', 'Booking #44 has been checked out', 'booking', '', 44, 1, '2025-05-30 15:55:03'),
(316, 0, '', 'Booking #39 has been checked in', 'booking', '', 39, 1, '2025-05-30 15:57:38'),
(317, 0, '', 'Booking #39 has been checked out', 'booking', '', 39, 1, '2025-05-30 15:57:49'),
(318, 0, '', 'Booking #40 has been checked in', 'booking', '', 40, 1, '2025-05-30 15:58:32'),
(319, 0, '', 'Booking #10 has been checked in', 'booking', '', 10, 1, '2025-05-31 06:04:49'),
(320, 0, '', 'Booking #21 has been checked in', 'booking', '', 21, 1, '2025-05-31 06:12:41'),
(321, 57, 'New Booking Confirmation', 'Your booking #BK-20250531-72450F has been confirmed. Check-in date: 2025-06-01, Check-out date: 2025-06-03', 'booking', 'fas fa-calendar-check', 46, 1, '2025-05-31 06:31:20'),
(322, 3, 'New Booking Confirmation', 'Your booking #BK-20250531-29A910 has been confirmed. Check-in date: 2025-06-06, Check-out date: 2025-07-06', 'booking', 'fas fa-calendar-check', 47, 1, '2025-05-31 06:38:16'),
(323, 67, 'New Booking Confirmation', 'Your booking #BK-20250531-CE9E7D has been confirmed. Check-in date: 2025-06-01, Check-out date: 2025-06-02', 'booking', 'fas fa-calendar-check', 48, 1, '2025-05-31 06:51:51'),
(324, 67, 'New Order Placed', 'Order #61 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 61, 1, '2025-05-31 07:06:25'),
(325, 29, 'New Order Placed', 'Order #106 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 106, 1, '2025-06-01 08:36:44'),
(326, 29, 'New Order Placed', 'Order #107 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 107, 1, '2025-06-01 08:37:36'),
(327, 29, 'New Order Placed', 'Order #108 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 108, 1, '2025-06-01 08:40:32'),
(328, 29, 'New Order Placed', 'Order #109 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 109, 1, '2025-06-01 08:59:42'),
(329, 2, '', 'Your order #92 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-06-01 09:07:44'),
(330, 62, '', 'Your order #91 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:08'),
(331, 1, '', 'Your order #89 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:12'),
(332, 29, '', 'Your order #88 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:16'),
(333, 1, '', 'Your order #87 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:20'),
(334, 1, '', 'Your order #86 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:24'),
(335, 1, '', 'Your order #85 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:28'),
(336, 1, '', 'Your order #84 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:32'),
(337, 1, '', 'Your order #83 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:36'),
(338, 1, '', 'Your order #82 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:40'),
(339, 1, '', 'Your order #81 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:44'),
(340, 1, '', 'Your order #80 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:47'),
(341, 1, '', 'Your order #79 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:51'),
(342, 1, '', 'Your order #78 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:08:55'),
(343, 1, '', 'Your order #77 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:00'),
(344, 1, '', 'Your order #76 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:04'),
(345, 1, '', 'Your order #75 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:10'),
(346, 1, '', 'Your order #74 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:14'),
(347, 1, '', 'Your order #73 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:18'),
(348, 1, '', 'Your order #72 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:21'),
(349, 1, '', 'Your order #71 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:25'),
(350, 1, '', 'Your order #70 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:28'),
(351, 1, '', 'Your order #69 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:33'),
(352, 1, '', 'Your order #67 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:37'),
(353, 1, '', 'Your order #66 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:40'),
(354, 1, '', 'Your order #65 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:48'),
(355, 1, '', 'Your order #64 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:09:51'),
(362, 29, '', 'Your order #108 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:37:51'),
(363, 29, 'New Order Placed', 'Order #110 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 110, 1, '2025-06-01 09:39:12'),
(364, 29, '', 'Your order #107 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-06-01 09:40:46'),
(365, 29, '', 'Your order #106 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-01 09:40:53'),
(374, 0, '', 'Booking #46 has been checked in', 'booking', '', 46, 1, '2025-06-02 06:48:29'),
(375, 29, 'New Order Placed', 'Order #121 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 121, 1, '2025-06-02 07:00:07'),
(376, 29, '', 'Your order #121 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-06-02 07:32:38'),
(377, 1, 'New Order Placed', 'Order #122 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 122, 1, '2025-06-02 07:33:52'),
(378, 1, 'New Order Placed', 'Order #123 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 123, 1, '2025-06-02 07:42:35'),
(379, 3, '', 'Your order #120 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-02 07:50:12'),
(380, 2, '', 'Your order #119 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-02 07:50:16'),
(381, 29, '', 'Your order #124 has been rejected. Reason: Invalid payment proof provided', 'order_rejected', '', NULL, 1, '2025-06-02 07:50:21'),
(382, 1, '', 'Your order #123 has been rejected. Reason: Items are out of stock', 'order_rejected', '', NULL, 1, '2025-06-02 07:50:25'),
(383, 0, '', 'Table Reservation #48 has been cancelled. Reason: found_better_option', 'table_cancelled', '', NULL, 1, '2025-06-02 08:44:19'),
(384, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-BB431A has been confirmed. Check-in date: 2025-06-10, Check-out date: 2025-07-02', 'booking', 'fas fa-calendar-check', 92, 1, '2025-06-02 11:11:26'),
(385, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-AC8C78 has been confirmed. Check-in date: 2025-06-05, Check-out date: 2025-06-25', 'booking', 'fas fa-calendar-check', 93, 1, '2025-06-02 11:14:37'),
(386, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-46B428 has been confirmed. Check-in date: 2025-06-03, Check-out date: 2025-06-18', 'booking', 'fas fa-calendar-check', 94, 1, '2025-06-02 11:21:46'),
(387, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-A32C67 has been confirmed. Check-in date: 2025-06-02, Check-out date: 2025-06-05', 'booking', 'fas fa-calendar-check', 95, 1, '2025-06-02 11:25:27'),
(388, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-708D85 has been confirmed. Check-in date: 2025-06-02, Check-out date: 2025-07-18', 'booking', 'fas fa-calendar-check', 96, 1, '2025-06-02 11:32:42'),
(389, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-A907F2 has been confirmed. Check-in date: 2025-06-02, Check-out date: 2025-06-03', 'booking', 'fas fa-calendar-check', 97, 1, '2025-06-02 11:36:31'),
(390, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-DFA145 has been confirmed. Check-in date: 2025-06-02, Check-out date: 2025-06-26', 'booking', 'fas fa-calendar-check', 98, 1, '2025-06-02 11:49:57'),
(391, 29, 'New Booking Confirmation', 'Your booking #BK-20250602-874C52 has been confirmed. Check-in date: 2025-06-02, Check-out date: 2025-07-31', 'booking', 'fas fa-calendar-check', 99, 1, '2025-06-02 12:08:03'),
(392, 0, '', 'Booking #98 has been checked in', 'booking', '', 98, 1, '2025-06-02 13:33:11'),
(393, 0, '', 'Booking #98 has been checked out', 'booking', '', 98, 1, '2025-06-02 13:36:43'),
(394, 0, '', 'Booking #48 has been accepted', 'booking', '', 48, 1, '2025-06-02 16:26:42'),
(395, 0, '', 'Booking #71 has been accepted', 'booking', '', 71, 1, '2025-06-02 16:27:09'),
(396, 0, '', 'Booking #102 has been accepted', 'booking', '', 102, 1, '2025-06-02 16:44:55'),
(397, 0, '', 'Booking #12 has been accepted', 'booking', '', 12, 1, '2025-06-02 17:38:43'),
(398, 0, '', 'Booking #94 has been accepted', 'booking', '', 94, 1, '2025-06-04 06:24:53'),
(399, 0, '', 'Booking #109 has been accepted', 'booking', '', 109, 1, '2025-06-05 16:27:09'),
(400, 0, '', 'Booking #109 has been checked in', 'booking', '', 109, 1, '2025-06-05 16:27:36'),
(401, 0, '', 'Booking #109 has been checked out', 'booking', '', 109, 1, '2025-06-05 16:34:33'),
(402, 0, '', 'Booking #108 has been accepted', 'booking', '', 108, 1, '2025-06-05 17:00:57'),
(403, 0, '', 'Booking #108 has been checked in', 'booking', '', 108, 1, '2025-06-05 17:01:07'),
(404, 0, '', 'Booking #108 has been checked out', 'booking', '', 108, 1, '2025-06-05 17:01:18'),
(405, 66, 'New Order Placed', 'Order #128 has been successfully placed and is being processed.', 'order', 'fa-shopping-cart', 128, 1, '2025-06-06 05:23:53'),
(406, 0, '', 'Booking #114 has been accepted', 'booking', '', 114, 1, '2025-06-06 05:31:48'),
(407, 0, '', 'Booking #128 has been accepted', 'booking', '', 128, 1, '2025-06-06 11:40:20'),
(408, 0, '', 'Booking #132 has been accepted', 'booking', '', 132, 1, '2025-06-06 11:55:23'),
(409, 0, '', 'Booking #136 has been accepted', 'booking', '', 136, 1, '2025-06-06 12:57:03'),
(410, 0, '', 'Booking #131 has been accepted', 'booking', '', 131, 1, '2025-06-06 14:07:46'),
(411, 0, '', 'Booking #113 has been accepted', 'booking', '', 113, 1, '2025-06-06 14:08:29'),
(412, 0, '', 'Booking #126 has been accepted', 'booking', '', 126, 1, '2025-06-06 14:11:19'),
(413, 0, '', 'Booking #146 has been accepted', 'booking', '', 146, 1, '2025-06-06 17:52:58'),
(414, 0, '', 'Booking #144 has been accepted', 'booking', '', 144, 1, '2025-06-06 18:11:48'),
(415, 0, '', 'Booking #151 has been accepted', 'booking', '', 151, 1, '2025-06-06 18:41:49'),
(416, 0, '', 'Booking #152 has been accepted', 'booking', '', 152, 1, '2025-06-07 12:30:06'),
(417, 0, '', 'Booking #152 has been checked in', 'booking', '', 152, 1, '2025-06-07 12:30:38'),
(418, 0, '', 'Booking #153 has been accepted', 'booking', '', 153, 1, '2025-06-07 12:43:31'),
(419, 0, '', 'Booking #46 has been checked out', 'booking', '', 46, 1, '2025-06-07 12:45:11'),
(420, 0, '', 'Booking #152 has been checked out', 'booking', '', 152, 1, '2025-06-07 12:49:15'),
(421, 0, '', 'Booking #155 has been accepted', 'booking', '', 155, 0, '2025-06-07 13:07:14'),
(422, 0, '', 'Booking #6 has been accepted', 'booking', '', 6, 0, '2025-06-08 19:48:27'),
(423, 0, '', 'Booking #5 has been accepted', 'booking', '', 5, 0, '2025-06-08 20:18:44'),
(424, 0, '', 'Booking #4 has been accepted', 'booking', '', 4, 0, '2025-06-08 20:29:58'),
(425, 0, '', 'Booking #1 has been accepted', 'booking', '', 1, 0, '2025-06-08 20:32:13'),
(426, 0, '', 'Booking #7 has been accepted', 'booking', '', 7, 0, '2025-06-08 20:36:16'),
(427, 0, '', 'Booking #6 has been checked out', 'booking', '', 6, 0, '2025-06-08 21:26:48'),
(428, 0, '', 'Booking #5 has been checked out', 'booking', '', 5, 0, '2025-06-08 21:52:01'),
(429, 0, '', 'Booking #7 has been checked out', 'booking', '', 7, 0, '2025-06-08 22:05:30'),
(430, 0, '', 'Booking #13 has been accepted', 'booking', '', 13, 0, '2025-06-08 23:42:33'),
(431, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:04:57'),
(432, 0, '', 'Booking #13 has been accepted', 'booking', '', 13, 0, '2025-06-09 00:08:23'),
(433, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:10:23'),
(434, 0, '', 'Booking #13 has been accepted', 'booking', '', 13, 0, '2025-06-09 00:14:31'),
(435, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:14:46'),
(436, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:17:15'),
(437, 0, '', 'Booking #13 has been accepted', 'booking', '', 13, 0, '2025-06-09 00:19:14'),
(438, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:22:42'),
(439, 0, '', 'Booking #13 has been accepted', 'booking', '', 13, 0, '2025-06-09 00:28:34'),
(440, 0, '', 'Booking #13 has been checked out', 'booking', '', 13, 0, '2025-06-09 00:28:50'),
(441, 0, '', 'Booking #15 has been accepted', 'booking', '', 15, 0, '2025-06-09 03:39:58'),
(442, 0, '', 'Booking #15 has been checked out', 'booking', '', 15, 0, '2025-06-09 03:43:48'),
(443, 0, '', 'Booking #16 has been accepted', 'booking', '', 16, 0, '2025-06-09 04:02:00'),
(444, 0, '', 'Booking #16 has been checked out', 'booking', '', 16, 0, '2025-06-09 04:04:00'),
(445, 0, '', 'Booking #17 has been accepted', 'booking', '', 17, 0, '2025-06-09 17:08:15'),
(446, 0, '', 'Booking #18 has been accepted', 'booking', '', 18, 0, '2025-06-09 17:25:16'),
(447, 0, '', 'Booking #17 has been checked out', 'booking', '', 17, 0, '2025-06-09 17:25:47'),
(448, 0, '', 'Booking #19 has been accepted', 'booking', '', 19, 0, '2025-06-09 17:31:02'),
(449, 0, '', 'Booking #14 has been accepted', 'booking', '', 14, 0, '2025-06-09 18:25:40'),
(450, 0, '', 'Booking #18 has been checked out', 'booking', '', 18, 0, '2025-06-09 21:04:30'),
(451, 0, '', 'Booking #19 has been checked out', 'booking', '', 19, 0, '2025-06-09 21:05:17'),
(452, 0, '', 'Booking #25 has been accepted', 'booking', '', 25, 0, '2025-06-09 21:08:33'),
(453, 0, '', 'Booking #25 has been checked out', 'booking', '', 25, 0, '2025-06-09 21:17:00'),
(454, 0, '', 'Booking #22 has been checked out', 'booking', '', 22, 0, '2025-06-09 21:22:33'),
(455, 0, '', 'Booking #4 has been checked out', 'booking', '', 4, 0, '2025-06-09 21:48:05'),
(456, 0, '', 'Booking #14 has been checked out', 'booking', '', 14, 0, '2025-06-09 23:22:05'),
(457, 0, '', 'Booking #26 has been accepted', 'booking', '', 26, 0, '2025-06-09 23:29:15'),
(458, 0, '', 'Booking #27 has been accepted', 'booking', '', 27, 0, '2025-06-10 03:21:56'),
(459, 0, '', 'Booking #28 has been accepted', 'booking', '', 28, 0, '2025-06-10 06:05:30'),
(460, 0, '', 'Booking #28 has been checked out', 'booking', '', 28, 0, '2025-06-10 06:07:38'),
(461, 0, '', 'Booking #29 has been accepted', 'booking', '', 29, 0, '2025-06-10 17:03:55'),
(462, 0, '', 'Booking #30 has been accepted', 'booking', '', 30, 0, '2025-06-11 08:53:56'),
(463, 0, '', 'Booking #30 has been checked out', 'booking', '', 30, 0, '2025-06-11 09:30:44');

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
(2, 'Events', 'uploads/offers/offer_683b2ba1e767a.jpg', '0% OFF', 'Special rate for family stays with complimentary activities', 1, '2025-03-05 11:14:57', '2025-06-01 01:43:07'),
(3, 'Rooms', 'uploads/offers/offer_683b2b11d05e9.jpg', '15% OFF', 'Stay longer, save more with our weekly rates', 1, '2025-03-05 11:14:57', '2025-05-31 16:15:13'),
(9, 'Tables and Cafe', 'uploads/offers/offer_683b197658e8b.jpg', '0% OFF', 'Best for dates and celebration', 1, '2025-05-31 06:49:33', '2025-05-31 16:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `change_amount` int(11) NOT NULL,
  `extra_fee` int(11) NOT NULL,
  `order_type` varchar(255) NOT NULL,
  `table_name` varchar(55) DEFAULT NULL,
  `type_of_order` varchar(55) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_option` varchar(255) NOT NULL,
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
  `notification_status` int(11) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `updated_at` date NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `reject_reason` varchar(255) NOT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `table_id`, `customer_name`, `nickname`, `contact_number`, `total_amount`, `amount_paid`, `change_amount`, `extra_fee`, `order_type`, `table_name`, `type_of_order`, `payment_method`, `payment_option`, `payment_reference`, `payment_status`, `remaining_balance`, `payment_proof`, `pickup_notes`, `status`, `final_total`, `order_date`, `discount_type`, `discount_amount`, `id_number`, `notification_status`, `completed_at`, `updated_at`, `cancellation_reason`, `reject_reason`, `cancelled_at`, `processed_by`) VALUES
(1, 3, 0, '', '', 0, 390.00, 390, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:43:58', 'none', 0, '', 0, '2025-05-29 13:46:55', '2025-05-29', NULL, '', NULL, ''),
(2, 3, 4, '', '', 0, 529.00, 529, 0, 0, 'walk-in', 'Table 4', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:49:49', 'none', 0, '', 0, '2025-05-29 13:44:32', '2025-05-29', NULL, '', NULL, ''),
(3, 3, 0, '', '', 0, 1869.00, 2000, 131, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:50:23', 'none', 0, '', 0, '2025-05-29 13:47:43', '2025-05-29', NULL, '', NULL, ''),
(4, 3, 8, '', '', 0, 502.00, 502, 0, 0, 'walk-in', 'Table 8', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:50:56', 'none', 0, '', 0, '2025-05-29 13:48:46', '2025-05-29', NULL, '', NULL, ''),
(5, 3, 0, '', '', 0, 563.00, 563, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:51:16', 'none', 0, '', 0, '2025-05-29 13:46:16', '2025-05-29', NULL, '', NULL, ''),
(6, 3, 1, '', '', 0, 365.00, 365, 0, 0, 'walk-in', 'Table 1', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:51:42', 'none', 0, '', 0, '2025-05-29 13:44:17', '2025-05-29', NULL, '', NULL, ''),
(7, 3, 9, '', '', 0, 772.00, 772, 0, 0, 'walk-in', 'Table 9', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:52:12', 'none', 0, '', 0, '2025-05-29 13:44:21', '2025-05-29', NULL, '', NULL, ''),
(8, 3, 9, '', '', 0, 675.00, 675, 0, 0, 'walk-in', 'Table 9', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-15 05:54:17', 'none', 0, '', 0, '2025-05-29 13:46:29', '2025-05-29', NULL, '', NULL, ''),
(9, 3, 5, '', '', 0, 195.00, 195, 0, 0, 'walk-in', 'Table 5', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 05:56:37', 'none', 0, '', 0, '2025-05-29 13:46:46', '2025-05-29', NULL, '', NULL, ''),
(10, 3, 0, '', '', 0, 310.00, 1000, 690, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-22 05:57:52', 'none', 0, '', 0, '2025-05-29 13:46:41', '2025-05-29', NULL, '', NULL, ''),
(11, 3, 4, '', '', 0, 980.00, 980, 0, 0, 'walk-in', 'Table 4', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:13:22', 'none', 0, '', 0, '2025-05-29 13:46:20', '2025-05-29', NULL, '', NULL, ''),
(12, 3, 0, '', '', 0, 205.00, 205, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:13:57', 'none', 0, '', 0, '2025-05-29 13:46:12', '2025-05-29', NULL, '', NULL, ''),
(13, 3, 10, '', '', 0, 792.00, 792, 0, 0, 'walk-in', 'Table 10', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:15:04', 'none', 0, '', 0, '2025-05-29 13:48:11', '2025-05-29', NULL, '', NULL, ''),
(14, 3, 0, '', '', 0, 888.00, 888, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:16:03', 'none', 0, '', 0, '2025-05-29 13:49:34', '2025-05-29', NULL, '', NULL, ''),
(15, 3, 0, '', '', 0, 472.00, 472, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:16:48', 'none', 0, '', 0, '2025-05-29 13:49:18', '2025-05-29', NULL, '', NULL, ''),
(16, 3, 0, '', '', 0, 806.00, 806, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:17:30', 'none', 0, '', 0, '2025-05-29 13:47:04', '2025-05-29', NULL, '', NULL, ''),
(17, 3, 0, '', '', 0, 873.00, 873, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 12:18:48', 'none', 0, '', 0, '2025-05-29 13:48:42', '2025-05-29', NULL, '', NULL, ''),
(18, 3, 5, '', '', 0, 1160.00, 1160, 0, 0, 'walk-in', 'Table 5', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:19:17', 'none', 0, '', 0, '2025-05-29 13:46:24', '2025-05-29', NULL, '', NULL, ''),
(19, 3, 0, '', '', 0, 937.00, 937, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:19:48', 'none', 0, '', 0, '2025-05-29 13:48:05', '2025-05-29', NULL, '', NULL, ''),
(20, 3, 7, '', '', 0, 441.00, 441, 0, 0, 'walk-in', 'Table 7', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:20:18', 'none', 0, '', 0, '2025-05-29 13:49:42', '2025-05-29', NULL, '', NULL, ''),
(21, 3, 0, '', '', 0, 782.00, 782, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:20:53', 'none', 0, '', 0, '2025-05-29 13:47:48', '2025-05-29', NULL, '', NULL, ''),
(22, 3, 0, '', '', 0, 662.00, 662, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:21:35', 'none', 0, '', 0, '2025-05-29 13:48:51', '2025-05-29', NULL, '', NULL, ''),
(23, 3, 0, '', '', 0, 320.00, 320, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:22:03', 'none', 0, '', 0, '2025-05-29 13:46:36', '2025-05-29', NULL, '', NULL, ''),
(24, 3, 0, '', '', 0, 450.00, 450, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 12:22:40', 'none', 0, '', 0, '2025-05-29 13:48:16', '2025-05-29', NULL, '', NULL, ''),
(25, 3, 0, '', '', 0, 983.00, 983, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:23:05', 'none', 0, '', 0, '2025-05-29 13:47:24', '2025-05-29', NULL, '', NULL, ''),
(26, 3, 0, '', '', 0, 377.00, 377, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:23:31', 'none', 0, '', 0, '2025-05-29 13:49:14', '2025-05-29', NULL, '', NULL, ''),
(27, 3, 5, '', '', 0, 3318.00, 3318, 0, 0, 'walk-in', 'Table 5', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:24:32', 'none', 0, '', 0, '2025-05-29 13:44:26', '2025-05-29', NULL, '', NULL, ''),
(28, 3, 0, '', '', 0, 1748.00, 1748, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:25:05', 'none', 0, '', 0, '2025-05-29 13:49:23', '2025-05-29', NULL, '', NULL, ''),
(29, 3, 0, '', '', 0, 830.00, 830, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:25:33', 'none', 0, '', 0, '2025-05-29 13:47:18', '2025-05-29', NULL, '', NULL, ''),
(30, 3, 1, '', '', 0, 2349.00, 2349, 0, 0, 'walk-in', 'Table 1', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:27:52', 'none', 0, '', 0, '2025-05-29 13:49:51', '2025-05-29', NULL, '', NULL, ''),
(31, 3, 0, '', '', 0, 662.00, 662, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:28:22', 'none', 0, '', 0, '2025-05-29 13:48:00', '2025-05-29', NULL, '', NULL, ''),
(32, 3, 0, '', '', 0, 884.00, 884, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:28:59', 'none', 0, '', 0, '2025-05-29 13:44:42', '2025-05-29', NULL, '', NULL, ''),
(33, 3, 0, '', '', 0, 4558.00, 4558, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:29:30', 'none', 0, '', 0, '2025-05-29 13:46:50', '2025-05-29', NULL, '', NULL, ''),
(34, 3, 9, '', '', 0, 7980.00, 7980, 0, 0, 'walk-in', 'Table 9', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:29:55', 'none', 0, '', 0, '2025-05-29 13:48:25', '2025-05-29', NULL, '', NULL, ''),
(35, 3, 0, '', '', 0, 8708.00, 8708, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 12:30:43', 'none', 0, '', 0, '2025-05-29 13:44:48', '2025-05-29', NULL, '', NULL, ''),
(36, 3, 8, '', '', 0, 15813.00, 15813, 0, 0, 'walk-in', 'Table 8', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:31:28', 'none', 0, '', 0, '2025-05-29 13:49:56', '2025-05-29', NULL, '', NULL, ''),
(37, 3, 0, '', '', 0, 95.00, 95, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:33:28', 'none', 0, '', 0, '2025-05-29 13:46:07', '2025-05-29', NULL, '', NULL, ''),
(38, 3, 0, '', '', 0, 4275.00, 4275, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:34:13', 'none', 0, '', 0, '2025-05-29 13:48:20', '2025-05-29', NULL, '', NULL, ''),
(39, 3, 0, '', '', 0, 377.00, 377, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:34:29', 'none', 0, '', 0, '2025-05-29 13:49:09', '2025-05-29', NULL, '', NULL, ''),
(40, 3, 0, '', '', 0, 311.00, 311, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-22 12:34:58', 'none', 0, '', 0, '2025-05-29 13:44:37', '2025-05-29', NULL, '', NULL, ''),
(41, 3, 0, '', '', 0, 377.00, 377, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:35:16', 'none', 0, '', 0, '2025-05-29 13:49:04', '2025-05-29', NULL, '', NULL, ''),
(42, 3, 0, '', '', 0, 377.00, 377, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:36:23', 'none', 0, '', 0, '2025-05-29 13:48:59', '2025-05-29', NULL, '', NULL, ''),
(43, 3, 0, '', '', 0, 662.00, 662, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-20 12:36:45', 'none', 0, '', 0, '2025-05-29 13:47:55', '2025-05-29', NULL, '', NULL, ''),
(44, 3, 0, '', '', 0, 1140.00, 1140, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:37:23', 'none', 0, '', 0, '2025-05-29 13:48:38', '2025-05-29', NULL, '', NULL, ''),
(45, 3, 0, '', '', 0, 1216.00, 1216, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:37:42', 'none', 0, '', 0, '2025-05-29 13:46:59', '2025-05-29', NULL, '', NULL, ''),
(46, 3, 0, '', '', 0, 754.00, 754, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:38:01', 'none', 0, '', 0, '2025-05-29 13:49:28', '2025-05-29', NULL, '', NULL, ''),
(47, 3, 0, '', '', 0, 790.00, 790, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:38:23', 'none', 0, '', 0, '2025-05-29 13:47:08', '2025-05-29', NULL, '', NULL, ''),
(48, 3, 0, '', '', 0, 562.00, 562, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:38:40', 'none', 0, '', 0, '2025-05-29 13:49:47', '2025-05-29', NULL, '', NULL, ''),
(49, 3, 0, '', '', 0, 1140.00, 1140, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:38:58', 'none', 0, '', 0, '2025-05-29 13:48:32', '2025-05-29', NULL, '', NULL, ''),
(50, 3, 4, '', '', 0, 160.00, 160, 0, 0, 'walk-in', 'Table 4', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-21 12:39:29', 'none', 0, '', 0, '2025-05-29 13:47:13', '2025-05-29', NULL, '', NULL, ''),
(51, 3, 1, '', '', 0, 1712.00, 2000, 288, 0, 'walk-in', 'Table 1', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 14:17:14', 'none', 0, '', 0, '2025-05-30 11:14:25', '2025-05-30', NULL, '', NULL, ''),
(52, 3, 3, '', '', 0, 3780.00, 4000, 220, 0, 'walk-in', 'Table 3', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 14:19:31', 'none', 0, '', 0, '2025-05-30 11:15:12', '2025-05-30', NULL, '', NULL, ''),
(53, 3, 9, '', '', 0, 2039.00, 2039, 0, 0, 'walk-in', 'Table 9', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-19 14:20:45', 'none', 0, '', 0, '2025-05-30 11:14:30', '2025-05-30', NULL, '', NULL, ''),
(54, 3, 5, '', '', 0, 3627.00, 3627, 0, 0, 'walk-in', 'Table 5', 'dine-in', 'maya', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 14:22:10', 'none', 0, '', 0, '2025-05-30 11:14:39', '2025-05-30', NULL, '', NULL, ''),
(55, 3, 8, '', '', 0, 5895.00, 5895, 0, 0, 'walk-in', 'Table 8', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 14:23:22', 'none', 0, '', 0, '2025-05-30 11:15:18', '2025-05-30', NULL, '', NULL, ''),
(56, 3, 1, '', '', 0, 2039.00, 2039, 0, 0, 'walk-in', 'Table 1', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 14:43:53', 'none', 0, '', 0, '2025-05-29 14:45:12', '2025-05-29', NULL, '', NULL, ''),
(57, 3, 7, 'Hazel', '', 2147483647, 598.00, 600, 2, 0, 'walk-in', 'Table 7', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 02:33:02', 'none', 0, '', 0, '2025-05-30 11:14:35', '2025-05-30', NULL, '', NULL, ''),
(58, 3, 10, '', '', 0, 1187.00, 1200, 13, 0, 'walk-in', 'Table 10', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 02:33:55', 'none', 0, '', 0, '2025-05-30 11:15:38', '2025-05-30', NULL, '', NULL, ''),
(59, 3, 1, '', '', 0, 920.00, 1000, 80, 0, 'walk-in', 'Table 1', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 02:34:45', 'none', 0, '', 0, '2025-05-30 11:15:31', '2025-05-30', NULL, '', NULL, ''),
(60, 3, 3, 'Trishia', '', 0, 1187.00, 1187, 0, 0, 'walk-in', 'Table 3', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 11:13:37', 'none', 0, '', 0, '2025-05-30 11:15:24', '2025-05-30', NULL, '', NULL, ''),
(61, 3, 0, '', '', 0, 1819.00, 0, 0, 0, 'regular', 'Table 2', '', 'gcash', '', '4015841898432', 'Processing', 0.00, '', '', 'finished', 1819, '2025-04-17 07:06:23', 'none', 0, '', 0, '2025-06-01 08:17:36', '2025-06-01', NULL, '', NULL, ''),
(62, 3, 43, 'Alma  Enciso ', 'Alma', 2147483647, 1698.00, 1698, 849, 0, 'advance', 'Table 9', '', 'gcash, gcash', '', '4015841898432', 'Paid', 0.00, '', '', 'finished', 1698, '2025-04-25 07:09:43', 'none', 0, 'ORD683aab375c1f1', 0, '2025-06-01 08:18:03', '2025-06-01', NULL, '', NULL, ''),
(63, 50, 1, '', '', 936284637, 2320.00, 2320, 0, 0, 'advance', NULL, '', 'gcash', '', '', '', 0.00, '', '', 'confirmed', 0, '2025-05-11 01:40:48', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, ''),
(64, 1, 5, 'Roxanne Ramirez', '', 910765289, 534.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-13 18:23:19', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(65, 1, 7, 'Gabriel Matibag', '', 928226745, 1090.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-15 04:50:46', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(66, 1, 8, 'Nicolas Abelgas', '', 936395069, 523.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-15 05:03:43', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(67, 1, 9, 'Patrick Delas Alas', '', 919562244, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-15 05:15:28', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(68, 62, 19, '', '', 935289517, 377.00, 377, 0, 0, 'advance', NULL, '', 'gcash', '', '', '', 0.00, '', '', 'cancelled', 0, '2025-05-18 03:14:09', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, ''),
(69, 1, 20, 'Ahjay Russel Alveyra', '', 929225667, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-18 11:09:14', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(70, 1, 21, 'Sasa Palemo', '', 929432675, 281.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-18 11:14:41', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(71, 1, 22, 'Daniel Granil', '', 999877876, 968.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-01 11:37:36', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(72, 1, 23, 'Samantha Almeda', '', 999877876, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-05-07 12:48:18', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(73, 1, 24, 'Samantha Almeda', '', 999877876, 2092.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 12:56:47', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(74, 1, 25, 'Samantha Almeda', '', 999877876, 726.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 13:10:08', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(75, 1, 26, 'Ajhay Razzel', '', 936395069, 484.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 13:57:01', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(76, 1, 27, 'Nicolas Abelgas', '', 987654311, 484.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 14:08:29', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(77, 1, 28, 'Daniel Granil', '', 987654311, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 14:29:47', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(78, 1, 29, 'Daniel Granil', '', 999877876, 484.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-22 14:50:22', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(79, 1, 30, 'Samantha Almeda', '', 999877876, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 14:59:05', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(80, 1, 31, 'Samantha Almeda', '', 999877876, 484.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 15:06:45', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Invalid payment proof provided', NULL, ''),
(81, 1, 32, 'Samantha Almeda', '', 987654311, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 15:12:30', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(82, 1, 33, 'Samantha Almeda', '', 999877876, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 15:25:18', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(83, 1, 34, 'Daniel Granil', '', 987654311, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 16:56:12', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(84, 1, 35, 'Nicolas Abelgas', '', 987654311, 242.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-21 17:02:50', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(85, 1, 36, 'Patrick Delas Alas', '', 929225667, 726.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-20 17:06:38', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(86, 1, 37, 'Reyzhelle Mendoza', '', 919227185, 484.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-20 17:19:41', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(87, 1, 38, 'Clifford Musni', '', 945641986, 368.00, 0, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-20 02:14:03', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(88, 29, 39, '', '', 912741844, 368.00, 184, 184, 0, 'advance', NULL, '', 'maya', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-20 02:18:28', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(89, 1, 40, 'Myra Cabal', '', 915857394, 904.00, 0, 0, 0, 'advance', NULL, '', 'gcash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-19 02:19:05', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(90, 29, 41, '', '', 912741844, 377.00, 189, 189, 0, 'advance', NULL, '', 'gcash', '', '', '', 0.00, '', '', 'cancelled', 0, '2025-04-19 02:19:55', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, ''),
(91, 62, 42, '', '', 935289517, 1500.00, 1500, 0, 0, 'advance', NULL, '', 'gcash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-19 02:26:53', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(92, 2, 44, 'Rhovelyn De Guzman', '', 2147483647, 523.00, 523, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-04-18 16:04:24', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Invalid payment proof provided', NULL, ''),
(93, 3, 6, 'Jonas', '', 0, 1339.00, 1339, 0, 0, 'walk-in', 'Table 6', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 08:16:00', 'none', 0, '', 0, '2025-06-01 08:17:39', '2025-06-01', NULL, '', NULL, ''),
(94, 3, 4, 'Robert', '', 0, 1641.00, 1641, 0, 0, 'walk-in', 'Table 4', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 08:19:16', 'none', 0, '', 0, '2025-06-01 08:30:11', '2025-06-01', NULL, '', NULL, ''),
(95, 3, 5, '', '', 0, 2412.00, 2412, 0, 0, 'walk-in', 'Table 5', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 08:20:05', 'none', 0, '', 0, '2025-06-01 08:29:58', '2025-06-01', NULL, '', NULL, ''),
(96, 3, 8, '', '', 0, 900.00, 1000, 100, 0, 'walk-in', 'Table 8', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 08:20:35', 'none', 0, '', 0, '2025-06-01 08:30:27', '2025-06-01', NULL, '', NULL, ''),
(97, 3, 0, '', '', 0, 924.00, 924, 0, 0, 'walk-in', NULL, 'takeout', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-18 08:21:14', 'none', 0, '', 0, '2025-06-01 08:30:02', '2025-06-01', NULL, '', NULL, ''),
(98, 3, 7, '', '', 0, 2491.00, 2500, 9, 0, 'walk-in', 'Table 7', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-17 08:23:54', 'none', 0, '', 0, '2025-06-01 08:30:22', '2025-06-01', NULL, '', NULL, ''),
(99, 3, 6, '', '', 0, 1275.00, 1275, 0, 0, 'walk-in', 'Table 6', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 08:25:46', 'none', 0, '', 0, '2025-06-01 08:30:07', '2025-06-01', NULL, '', NULL, ''),
(100, 3, 0, '', '', 0, 974.00, 1000, 26, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 08:27:18', 'none', 0, '', 0, '2025-06-01 08:29:51', '2025-06-01', NULL, '', NULL, ''),
(101, 3, 0, 'Trishia', '', 0, 904.00, 904, 0, 0, 'walk-in', NULL, 'takeout', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-09 08:28:30', 'none', 0, '', 0, '2025-06-01 08:30:18', '2025-06-01', NULL, '', NULL, ''),
(102, 3, 4, '', '', 0, 1097.00, 1100, 3, 0, 'walk-in', 'Table 4', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 08:29:11', 'none', 0, '', 0, '2025-06-01 08:30:15', '2025-06-01', NULL, '', NULL, ''),
(103, 3, 0, '', '', 0, 360.00, 400, 40, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 08:29:39', 'none', 0, '', 0, '2025-06-01 08:30:29', '2025-06-01', NULL, '', NULL, ''),
(104, 3, 5, '', '', 0, 3259.00, 3300, 41, 0, 'walk-in', 'Table 5', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-16 08:31:40', 'none', 0, '', 0, '2025-06-01 09:45:24', '2025-06-01', NULL, '', NULL, ''),
(105, 3, 8, '', '', 0, 462.00, 500, 38, 0, 'walk-in', 'Table 8', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-13 08:32:17', 'none', 0, '', 0, '2025-06-01 09:44:25', '2025-06-01', NULL, '', NULL, ''),
(106, 29, 0, '', '', 0, 1252.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '1111111', 'Processing', 0.00, 'payment_1748767004_683c111cbce2c.png', '', 'rejected', 1252, '2025-04-15 08:36:44', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(107, 29, 0, '', '', 0, 1000.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', 'qwww222222222', 'Partially Paid', 485.50, 'payment_1748767056_683c11504d998.png', '', 'rejected', 971, '2025-04-17 08:37:36', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Invalid payment proof provided', NULL, ''),
(108, 29, 0, '', '', 0, 1081.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '4016136056722', 'Processing', 0.00, 'payment_491218076_1058852319507229_6780498318321328849_n.png', '', 'rejected', 1000, '2025-04-24 08:40:32', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(109, 3, 0, '', '', 0, 4000.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '4027464293841', 'Processing', 0.00, 'payment_1748768382_683c167eb731a.jpg', '', 'finished', 4000, '2025-04-15 08:59:42', 'none', 0, '', 0, '2025-04-15 09:44:59', '2025-06-01', NULL, '', NULL, ''),
(110, 3, 0, '', '', 0, 1000.00, 0, 0, 0, 'regular', 'Table 2', '', 'gcash', '', '4027464293841', 'Processing', 0.00, 'payment_1748770752_683c1fc0a5248.jpg', '', 'finished', 1028, '2025-04-17 17:55:12', 'none', 0, '', 0, '2025-04-17 09:45:16', '2025-04-17', NULL, '', NULL, ''),
(111, 42, 3, '', '', 0, 1887.00, 2000, 113, 0, 'walk-in', 'Table 3', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-01 10:09:48', 'none', 0, '', 0, '2025-06-01 10:15:06', '2025-06-01', NULL, '', NULL, ''),
(112, 42, 9, '', '', 0, 1637.00, 1700, 63, 0, 'walk-in', 'Table 9', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-02 10:10:26', 'none', 0, '', 0, '2025-06-01 10:15:01', '2025-06-01', NULL, '', NULL, ''),
(113, 42, 1, '', '', 0, 285.00, 289, 4, 0, 'walk-in', 'Table 1', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-07 10:11:18', 'none', 0, '', 0, '2025-06-01 10:14:57', '2025-06-01', NULL, '', NULL, ''),
(114, 42, 0, 'Nicole', '', 0, 1441.00, 1441, 0, 0, 'walk-in', NULL, 'takeout', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-04-26 10:12:05', 'none', 0, '', 0, '2025-06-01 10:15:12', '2025-06-01', NULL, '', NULL, ''),
(115, 42, 10, '', '', 0, 507.00, 510, 3, 0, 'walk-in', 'Table 10', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-01 10:12:30', 'none', 0, '', 0, '2025-06-01 10:15:04', '2025-06-01', NULL, '', NULL, ''),
(116, 42, 4, '', '', 0, 1029.00, 1029, 0, 0, 'walk-in', 'Table 4', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-01 10:14:06', 'none', 0, '', 0, '2025-06-01 10:15:15', '2025-06-01', NULL, '', NULL, ''),
(117, 42, 8, '', '', 0, 974.00, 974, 0, 0, 'walk-in', 'Table 8', 'dine-in', 'gcash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-01 10:14:38', 'none', 0, '', 0, '2025-06-01 10:15:09', '2025-06-01', NULL, '', NULL, ''),
(118, 42, 5, 'Myla', '', 0, 1016.00, 1020, 4, 0, 'walk-in', 'Table 5', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-05-09 10:20:10', 'pwd', 254, 'MM-6349-BFD-7534806', 0, '2025-06-01 10:20:22', '2025-06-01', NULL, '', NULL, ''),
(119, 2, 45, 'Jamelyn M Manongsong', '', 2147483647, 597.00, 299, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'rejected', 0, '2025-06-02 14:12:29', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(120, 3, 46, 'Alma  Enciso ', 'Alma', 2147483647, 242.00, 242, 0, 0, 'advance', NULL, '', 'gcash', '', '2929839292', 'Processing', 0.00, 'payment_1748847334_683d4ae661ffe.jpg', '', 'rejected', 242, '2025-06-02 06:55:34', 'none', 0, 'ORD683d4ae662335', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, ''),
(121, 29, 0, '', '', 0, 242.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '233344454', 'Partially Paid', 121.00, 'payment_1748847607_683d4bf773728.jpg', '', 'rejected', 242, '2025-06-02 07:00:07', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Invalid payment proof provided', NULL, ''),
(122, 1, 0, '', '', 0, 610.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '4027464293841', 'Partially Paid', 305.00, 'payment_1748849632_683d53e0b4347.jpg', '', 'processing', 610, '2025-06-02 07:33:52', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, 'Jhaira Abordo'),
(123, 1, 0, '', '', 0, 220.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '4027464293841', 'Partially Paid', 110.00, 'payment_1748850155_683d55eb8f289.jpg', '', 'rejected', 220, '2025-06-02 07:42:35', 'none', 0, '', 0, NULL, '0000-00-00', NULL, 'Items are out of stock', NULL, NULL),
(124, 29, 47, 'Christian Realisan', 'Christian', 2147483647, 393.00, 197, 197, 0, 'advance', NULL, '', 'gcash', '', '17273732871', 'Partially Paid', 196.50, 'payment_1748850587_683d579b291eb.jpeg', '', 'rejected', 393, '2025-06-02 07:49:47', 'none', 0, 'ORD683d579b29487', 0, NULL, '0000-00-00', NULL, 'Invalid payment proof provided', NULL, NULL),
(125, 29, 48, 'Christian Realisan', 'Christian', 2147483647, 805.00, 805, 0, 0, 'advance', NULL, '', 'gcash', '', '4027464293841', 'Processing', 0.00, 'payment_1748850723_683d58232d190.jpg', '', 'cancelled', 805, '2025-06-02 07:52:03', 'none', 0, 'ORD683d58232d46a', 0, NULL, '0000-00-00', NULL, '', NULL, NULL),
(126, 3, 8, '', '', 0, 488.00, 500, 12, 0, 'walk-in', 'Table 8', 'dine-in', 'cash', '', '', '', 0.00, '', '', 'finished', 0, '2025-06-02 14:16:17', '', 122, 'SCDFI-12345678', 0, '2025-06-02 14:20:16', '2025-06-02', NULL, '', NULL, NULL),
(127, 1, 49, 'Realisana', '', 2147483647, 242.00, 242, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'processing', 0, '2025-06-04 00:16:29', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, 'Alma  Enciso'),
(128, 66, 0, '', '', 0, 829.00, 0, 0, 0, 'regular', NULL, '', 'gcash', '', '1023317335894', 'Processing', 0.00, 'payment_1749187433_68427b69b5a76.jpg', '', 'processing', 829, '2025-06-06 05:23:53', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, 'Alma  Enciso'),
(129, 29, 0, 'Christian Realisan', '', 2147483647, 610.00, 305, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'partial', '', 'paid', 305.00, '', '', 'Cancelled', 610, '2025-06-06 12:28:45', 'none', 0, '', 1, NULL, '0000-00-00', 'Long Wait Time', '', '2025-06-06 12:29:09', NULL),
(130, 66, 0, 'Allyson Carpio', '', 2147483647, 804.00, 804, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 804, '2025-06-06 12:31:59', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(131, 66, 0, 'Allyson Carpio', '', 2147483647, 785.00, 393, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'partial', '', 'paid', 392.50, '', '', 'processing', 785, '2025-06-06 14:02:27', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, 'Alma  Enciso'),
(132, 57, 0, 'Joanna Hernandez', '', 2147483647, 565.00, 565, 0, 0, 'regular', 'N/A', 'regular', 'maya', 'full', '', 'paid', 0.00, '', '', 'Completed', 565, '2025-06-06 14:15:32', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(133, 57, 0, 'Joanna Hernandez', '', 2147483647, 242.00, 242, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 242, '2025-06-06 14:17:57', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(134, 66, 0, 'Allyson Carpio', '', 2147483647, 965.00, 965, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 965, '2025-06-06 14:20:43', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(135, 1, 50, 'Esme', '', 2147483647, 1021.00, 1021, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'pending', 0, '2025-06-07 01:59:45', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, NULL),
(136, 29, 0, 'Christian Realisan', '', 2147483647, 242.00, 121, 0, 0, 'advance', 'N/A', 'advance', 'gcash', 'partial', '', 'paid', 121.00, '', '', 'Pending', 242, '2025-06-07 13:37:55', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(137, 3, 0, 'Joanna Hernandez', '', 2147483647, 368.00, 368, 0, 0, 'advance', 'N/A', 'advance', 'gcash, cash', 'partial', '', 'Paid', 0.00, '', '', 'finished', 368, '2025-06-07 13:43:48', 'none', 0, '', 1, '2025-06-07 13:45:17', '2025-06-07', NULL, '', NULL, 'Alma  Enciso'),
(138, 57, 0, 'Joanna Hernandez', '', 2147483647, 437.00, 219, 0, 0, 'advance', 'N/A', 'advance', 'gcash', 'partial', '', 'paid', 218.50, '', '', 'processing', 437, '2025-06-07 13:47:10', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, 'Alma  Enciso'),
(139, 66, 0, 'Allyson Carpio', '', 2147483647, 760.00, 760, 0, 0, 'advance', 'N/A', 'advance', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 760, '2025-06-07 16:52:46', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(140, 29, 0, 'Christian Realisan', '', 2147483647, 242.00, 121, 0, 0, 'regular', 'N/A', 'regular', 'gcash', 'partial', '', 'paid', 121.00, '', '', 'Pending', 242, '2025-06-08 06:41:11', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(141, 1, 51, 'Aizzy Villanueva', '', 2147483647, 281.00, 141, 0, 0, 'advance', NULL, '', 'cash', '', '', '', 0.00, '', '', 'pending', 0, '2025-06-09 12:10:12', 'none', 0, '', 0, NULL, '0000-00-00', NULL, '', NULL, NULL),
(142, 66, 0, 'Allyson Carpio', '', 2147483647, 327.00, 327, 0, 0, 'advance', 'N/A', 'advance', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 327, '2025-06-09 17:13:59', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL),
(143, 66, 0, 'Allyson Carpio', '', 2147483647, 377.00, 377, 0, 0, 'advance', 'N/A', 'advance', 'gcash', 'full', '', 'paid', 0.00, '', '', 'Completed', 377, '2025-06-10 16:58:31', 'none', 0, '', 1, NULL, '0000-00-00', NULL, '', NULL, NULL);

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
(1, 1, 'HAND - CUT POTATO FRIES', 2, 195.00),
(2, 2, 'COOKIES & CREAM', 1, 195.00),
(3, 2, 'SEAFOOD MARINARA', 1, 334.00),
(4, 3, 'MOZZARELLA STICKS', 1, 285.00),
(5, 3, 'HOMEMADE DAING NA BANGUS', 1, 312.00),
(6, 3, 'HOMEMADE CHICKEN TOCINO', 1, 304.00),
(7, 3, 'BANANA WALNUT BRIOCHE FRENCH TOAST', 1, 258.00),
(8, 3, 'STELLA ARTOIS', 2, 175.00),
(9, 3, 'COFFEE JELLY', 1, 170.00),
(10, 3, 'CHOCOLATE', 1, 190.00),
(11, 4, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(12, 4, 'COOKIES & CREAM', 1, 195.00),
(13, 5, 'GAMBAS AND CHORIZO', 1, 368.00),
(14, 5, 'HAND - CUT POTATO FRIES', 1, 195.00),
(15, 6, 'CARAMEL MACCHIATO ', 1, 195.00),
(16, 6, 'COFFEE JELLY', 1, 170.00),
(17, 7, 'Carrot Cake Slice', 1, 190.00),
(18, 7, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(19, 7, 'CAPPUCCINO (HOT)', 1, 120.00),
(20, 7, 'MOCHA', 1, 155.00),
(21, 8, 'GAMBAS AND CHORIZO', 1, 368.00),
(22, 8, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(23, 9, 'HAND - CUT POTATO FRIES', 1, 195.00),
(24, 10, 'GUYABANO', 1, 135.00),
(25, 10, 'MANGO', 1, 175.00),
(26, 11, 'GAMBAS AND CHORIZO', 1, 368.00),
(27, 11, 'HAND - CUT POTATO FRIES', 1, 195.00),
(28, 11, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(29, 11, 'AMERICANO (HOT)', 1, 110.00),
(30, 12, 'Espresso', 1, 95.00),
(31, 12, 'AMERICANO (HOT)', 1, 110.00),
(32, 13, 'MOZZARELLA STICKS', 1, 285.00),
(33, 13, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(34, 13, 'HISBISCUS', 1, 130.00),
(35, 14, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(36, 14, 'FRIED CALAMARI', 1, 242.00),
(37, 14, 'Carrot Cake Slice', 1, 190.00),
(38, 14, 'MANGO', 1, 175.00),
(39, 15, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(40, 15, 'Espresso (HOT)', 1, 95.00),
(41, 16, 'HOMEMADE DAING NA BANGUS', 1, 312.00),
(42, 16, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(43, 16, 'MOLO SOUP', 1, 158.00),
(44, 17, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(45, 17, 'HAND - CUT POTATO FRIES', 1, 195.00),
(46, 17, 'SEAFOOD MARINARA', 1, 334.00),
(47, 18, 'GAMBAS AND CHORIZO', 1, 368.00),
(48, 18, 'MOZZARELLA STICKS', 1, 285.00),
(49, 18, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(50, 18, 'JOSE CUERVO SILVER', 1, 130.00),
(51, 19, 'MOZZARELLA STICKS', 1, 285.00),
(52, 19, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(53, 19, 'CAPPUCCINO (HOT)', 1, 120.00),
(54, 19, 'MOCHA', 1, 155.00),
(55, 20, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(56, 20, 'MATCHA', 1, 160.00),
(57, 21, 'MOZZARELLA STICKS', 1, 285.00),
(58, 21, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(59, 21, 'CHOCOLATE', 1, 190.00),
(60, 22, 'SEAFOOD MARINARA', 1, 334.00),
(61, 22, 'CHICKEN ALFREDO WITH MUSHROOMS', 1, 328.00),
(62, 23, 'GUYABANO', 1, 135.00),
(63, 23, 'HOEGAARDEN', 1, 185.00),
(64, 24, 'MOZZARELLA STICKS', 1, 285.00),
(65, 24, 'STRAWBERRY MILK', 1, 165.00),
(66, 25, 'MOZZARELLA STICKS', 1, 285.00),
(67, 25, 'DAING NA BIYA', 1, 311.00),
(68, 25, 'CHICKEN PARMIGIANA', 1, 387.00),
(69, 26, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(70, 27, 'COFFEE JELLY', 1, 170.00),
(71, 27, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(72, 27, 'DARK CHOCOLATE CHAMPORADO', 1, 261.00),
(73, 27, 'BANANA WALNUT BRIOCHE FRENCH TOAST', 1, 258.00),
(74, 27, 'AMERICAN BREAKFAST', 1, 323.00),
(75, 27, 'HOUSE FRIED CHICKEN (3 PCS)', 1, 395.00),
(76, 27, 'HOUSE FRIED CHICKEN (2 PCS)', 1, 319.00),
(77, 27, 'BUILD YOUR OWN OMELETTE', 1, 236.00),
(78, 27, 'MOLO SOUP', 1, 158.00),
(79, 27, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(80, 27, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(81, 27, 'Carrot Cake Slice', 1, 190.00),
(82, 28, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(83, 28, 'MOZZARELLA STICKS', 1, 285.00),
(84, 28, 'HAND - CUT POTATO FRIES', 1, 195.00),
(85, 28, 'GAMBAS AND CHORIZO', 1, 368.00),
(86, 28, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(87, 28, 'FRIED CALAMARI', 1, 242.00),
(88, 29, 'MOLO SOUP', 1, 158.00),
(89, 29, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(90, 29, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(91, 30, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', 1, 750.00),
(92, 30, 'USDA ANGUS RIBEYE (CHOICE GRADE) (100g)', 1, 530.00),
(93, 30, 'FRIED CALAMARI', 1, 242.00),
(94, 30, 'MOZZARELLA STICKS', 1, 285.00),
(95, 30, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(96, 30, 'STRAWBERRY MILK', 1, 165.00),
(97, 31, 'MOZZARELLA STICKS', 1, 285.00),
(98, 31, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(99, 32, 'DAING NA BIYA', 1, 311.00),
(100, 32, 'HOMEMADE DAING NA BANGUS', 1, 312.00),
(101, 32, 'DARK CHOCOLATE CHAMPORADO', 1, 261.00),
(102, 33, 'HAND - CUT POTATO FRIES', 1, 195.00),
(103, 33, 'MOZZARELLA STICKS', 1, 285.00),
(104, 33, 'FRIED CALAMARI', 1, 242.00),
(105, 33, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(106, 33, 'GAMBAS AND CHORIZO', 1, 368.00),
(107, 33, 'TRUFFLE PARMESAN POTATO CHIPS', 11, 281.00),
(108, 34, 'MOZZARELLA STICKS', 28, 285.00),
(109, 35, 'DAING NA BIYA', 28, 311.00),
(110, 36, 'USDA PRIME BEEF SALPICAO', 21, 753.00),
(111, 37, 'Espresso', 1, 95.00),
(112, 38, 'MOZZARELLA STICKS', 15, 285.00),
(113, 39, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(114, 40, 'DAING NA BIYA', 1, 311.00),
(115, 41, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(116, 42, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(117, 43, 'MOZZARELLA STICKS', 1, 285.00),
(118, 43, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(119, 44, 'MOZZARELLA STICKS', 4, 285.00),
(120, 45, 'HOMEMADE CHICKEN TOCINO', 4, 304.00),
(121, 46, 'SIRRACHA BUFFALO WINGS 6 PCS', 2, 377.00),
(122, 47, 'HOUSE FRIED CHICKEN (3 PCS)', 2, 395.00),
(123, 48, 'TRUFFLE PARMESAN POTATO CHIPS', 2, 281.00),
(124, 49, 'MOZZARELLA STICKS', 4, 285.00),
(125, 50, 'MATCHA', 1, 160.00),
(126, 51, 'MOZZARELLA STICKS', 1, 285.00),
(127, 51, 'HAND - CUT POTATO FRIES', 1, 195.00),
(128, 51, 'SEAFOOD MARINARA', 1, 334.00),
(129, 51, 'SHRIMP AGLIO OLIO', 1, 348.00),
(130, 51, 'STRAWBERRY MILK', 1, 165.00),
(131, 51, 'COOKIES & CREAM', 1, 195.00),
(132, 51, 'Carrot Cake Slice', 1, 190.00),
(133, 52, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(134, 52, 'FRIED CALAMARI', 1, 242.00),
(135, 52, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(136, 52, 'CHICKEN ALFREDO WITH MUSHROOMS', 1, 328.00),
(137, 52, 'ALLE VONGOLE', 1, 321.00),
(138, 52, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(139, 52, 'CHICKEN PESTO PANINI', 1, 316.00),
(140, 52, 'JASMINE', 1, 125.00),
(141, 52, 'PEPERMINT', 1, 125.00),
(142, 52, 'GUYABANO', 1, 135.00),
(143, 52, 'HOUSE FRIED CHICKEN (2 PCS)', 1, 319.00),
(144, 52, 'USDA PRIME BEEF SALPICAO', 1, 753.00),
(145, 52, 'CAPPUCCINO (COLD)', 1, 130.00),
(146, 53, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(147, 53, 'SEAFOOD MARINARA', 1, 334.00),
(148, 53, 'ALLE VONGOLE', 1, 321.00),
(149, 53, 'MOCHA (ICED)', 1, 185.00),
(150, 53, 'HONEY LEMON', 1, 125.00),
(151, 53, 'CHICKEN PARMIGIANA', 1, 387.00),
(152, 53, 'BAKED FISH IN PARCHMENT', 1, 359.00),
(153, 54, 'GAMBAS AND CHORIZO', 1, 368.00),
(154, 54, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(155, 54, 'MOLO SOUP', 1, 158.00),
(156, 54, 'CARBONARA (NO CREAM)', 1, 327.00),
(157, 54, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', 1, 750.00),
(158, 54, 'USDA PRIME BEEF SALPICAO', 1, 753.00),
(159, 54, 'DRUNKEN PORK BELLY', 1, 397.00),
(160, 54, 'MANGO', 1, 175.00),
(161, 54, 'CHOCOLATE', 1, 190.00),
(162, 54, 'STRAWBERRY MILK', 1, 165.00),
(163, 55, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', 2, 750.00),
(164, 55, 'USDA PRIME BEEF SALPICAO', 1, 753.00),
(165, 55, 'HOUSE FRIED CHICKEN (2 PCS)', 1, 319.00),
(166, 55, 'PAN-FRIED PORK STEAK', 1, 392.00),
(167, 55, 'DRUNKEN PORK BELLY', 1, 397.00),
(168, 55, 'CHICKEN PARMIGIANA', 1, 387.00),
(169, 55, 'CHOCOLATE', 1, 190.00),
(170, 55, 'COOKIES & CREAM', 1, 195.00),
(171, 55, 'STRAWBERRY MILK', 1, 165.00),
(172, 55, 'CHICKEN PESTO PANINI', 1, 316.00),
(173, 55, 'SIRRACHA BUFFALO WINGS 6 PCS', 2, 377.00),
(174, 55, 'MOZZARELLA STICKS', 1, 285.00),
(175, 55, 'FRIED CALAMARI', 1, 242.00),
(176, 56, 'GAMBAS AND CHORIZO', 2, 368.00),
(177, 56, 'HAND - CUT POTATO FRIES', 1, 195.00),
(178, 56, 'COOKIES & CREAM', 2, 195.00),
(179, 56, 'SHRIMP AGLIO OLIO', 1, 348.00),
(180, 56, 'CLASSIC CHEESEBURGER', 1, 370.00),
(181, 57, 'GAMBAS AND CHORIZO', 1, 368.00),
(182, 57, 'Espresso', 1, 95.00),
(183, 57, 'GUYABANO', 1, 135.00),
(184, 58, 'MOZZARELLA STICKS', 1, 285.00),
(185, 58, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(186, 58, 'STELLA ARTOIS', 3, 175.00),
(187, 59, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(188, 59, 'DARK CHOCOLATE CHAMPORADO', 1, 261.00),
(189, 59, 'AMERICAN BREAKFAST', 1, 323.00),
(190, 60, 'HAND - CUT POTATO FRIES', 1, 195.00),
(191, 60, 'MOZZARELLA STICKS', 1, 285.00),
(192, 60, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(193, 60, 'STRAWBERRY MILK', 2, 165.00),
(194, 61, 'MOZZARELLA STICKS', 1, 285.00),
(195, 61, 'CHICKEN PARMIGIANA', 1, 387.00),
(196, 61, 'DRUNKEN PORK BELLY', 1, 397.00),
(197, 61, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', 1, 750.00),
(198, 62, 'HOUSE FRIED CHICKEN (3 PCS)', 1, 395.00),
(199, 62, 'USDA PRIME BEEF SALPICAO', 1, 753.00),
(200, 62, 'MATCHA', 1, 160.00),
(201, 62, 'COOKIES & CREAM', 2, 195.00),
(202, 92, 'Couples', 1, 523.00),
(203, 93, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(204, 93, 'HOMEMADE DAING NA BANGUS', 1, 312.00),
(205, 93, 'DARK CHOCOLATE CHAMPORADO', 1, 261.00),
(206, 93, 'CAPPUCCINO (HOT)', 1, 120.00),
(207, 93, 'GUYABANO', 1, 135.00),
(208, 93, 'MANGO', 1, 175.00),
(209, 94, 'HOEGAARDEN', 1, 185.00),
(210, 94, 'STELLA ARTOIS', 1, 175.00),
(211, 94, 'SIRRACHA BUFFALO WINGS 6 PCS', 2, 377.00),
(212, 94, 'FRIED CALAMARI', 1, 242.00),
(213, 94, 'MOZZARELLA STICKS', 1, 285.00),
(214, 95, 'SIRRACHA BUFFALO WINGS 6 PCS', 2, 377.00),
(215, 95, 'CARBONARA (NO CREAM)', 1, 327.00),
(216, 95, 'SHRIMP AGLIO OLIO', 1, 348.00),
(217, 95, 'COOKIES & CREAM', 1, 195.00),
(218, 95, 'STRAWBERRY MILK', 1, 165.00),
(219, 95, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(220, 95, 'CHICKEN PESTO PANINI', 1, 316.00),
(221, 96, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(222, 96, 'FRIED CALAMARI', 1, 242.00),
(223, 96, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(224, 97, 'PHILLY CHEESESTEAK PANINI', 2, 307.00),
(225, 97, 'GUYABANO', 1, 135.00),
(226, 97, 'MANGO', 1, 175.00),
(227, 98, 'CARBONARA (NO CREAM)', 1, 327.00),
(228, 98, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(229, 98, 'USDA PRIME BEEF SALPICAO', 1, 753.00),
(230, 98, 'USDA ANGUS RIBEYE (PRIME GRADE) (100g)', 1, 750.00),
(231, 98, 'STRAWBERRY MILK', 1, 165.00),
(232, 98, 'MATCHA', 1, 160.00),
(233, 99, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(234, 99, 'CARBONARA (NO CREAM)', 1, 327.00),
(235, 99, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(236, 99, 'STRAWBERRY MILK', 1, 165.00),
(237, 99, 'COOKIES & CREAM', 1, 195.00),
(238, 100, 'STRAWBERRY MILK', 1, 165.00),
(239, 100, 'COOKIES & CREAM', 1, 195.00),
(240, 100, 'PHILLY CHEESESTEAK PANINI', 2, 307.00),
(241, 101, 'MOZZARELLA STICKS', 1, 285.00),
(242, 101, 'FRIED CALAMARI', 1, 242.00),
(243, 101, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(244, 102, 'HOEGAARDEN', 2, 185.00),
(245, 102, 'STELLA ARTOIS', 2, 175.00),
(246, 102, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(247, 103, 'STRAWBERRY MILK', 1, 165.00),
(248, 103, 'COOKIES & CREAM', 1, 195.00),
(249, 104, 'MOZZARELLA STICKS', 1, 285.00),
(250, 104, 'SIRRACHA BUFFALO WINGS 6 PCS', 2, 377.00),
(251, 104, 'FRIED CALAMARI', 1, 242.00),
(252, 104, 'CARBONARA (NO CREAM)', 2, 327.00),
(253, 104, 'STRAWBERRY MILK', 2, 165.00),
(254, 104, 'COOKIES & CREAM', 1, 195.00),
(255, 104, 'PHILLY CHEESESTEAK PANINI', 2, 307.00),
(256, 104, 'HOEGAARDEN', 1, 185.00),
(257, 105, 'CARBONARA (NO CREAM)', 1, 327.00),
(258, 105, 'GUYABANO', 1, 135.00),
(259, 106, 'FRIED CALAMARI', 1, 242.00),
(260, 106, 'MOZZARELLA STICKS', 1, 285.00),
(261, 106, 'COOKIES & CREAM', 1, 195.00),
(262, 106, 'MATCHA', 1, 160.00),
(263, 106, 'CLASSIC CHEESEBURGER', 1, 370.00),
(264, 107, 'CARBONARA (NO CREAM)', 1, 327.00),
(265, 107, 'SEAFOOD MARINARA', 1, 334.00),
(266, 107, 'GUYABANO', 1, 135.00),
(267, 107, 'MANGO', 1, 175.00),
(268, 108, 'CARBONARA (NO CREAM)', 1, 327.00),
(269, 108, 'FRIED CALAMARI', 1, 242.00),
(270, 108, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(271, 108, 'GUYABANO', 1, 135.00),
(272, 109, 'FRIED CALAMARI', 1, 242.00),
(273, 109, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(274, 109, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(275, 109, 'CHICKEN ALFREDO WITH MUSHROOMS', 1, 328.00),
(276, 109, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(277, 109, 'MOLO SOUP', 1, 158.00),
(278, 109, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(279, 109, 'CARBONARA (NO CREAM)', 1, 327.00),
(280, 109, 'CLASSIC CHEESEBURGER', 1, 370.00),
(281, 109, 'CHICKEN PESTO PANINI', 1, 316.00),
(282, 109, 'GUYABANO', 1, 135.00),
(283, 109, 'MANGO', 1, 175.00),
(284, 109, 'COOKIES & CREAM', 1, 195.00),
(285, 109, 'MATCHA', 1, 160.00),
(286, 109, 'SEAFOOD MARINARA', 1, 334.00),
(287, 110, 'FRIED CALAMARI', 1, 242.00),
(288, 110, 'TRUFFLE PARMESAN POTATO CHIPS', 1, 281.00),
(289, 110, 'GUYABANO', 1, 135.00),
(290, 110, 'MANGO', 1, 175.00),
(291, 110, 'HAND - CUT POTATO FRIES', 1, 167.00),
(292, 111, 'MOZZARELLA STICKS', 1, 285.00),
(293, 111, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(294, 111, 'FRIED CALAMARI', 1, 242.00),
(295, 111, 'STRAWBERRY MILK', 1, 165.00),
(296, 111, 'COOKIES & CREAM', 1, 195.00),
(297, 111, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(298, 111, 'CHICKEN PESTO PANINI', 1, 316.00),
(299, 112, 'HOMEMADE TENDERLOIN BEEF TAPA', 1, 336.00),
(300, 112, 'HOMEMADE CHICKEN TOCINO', 1, 304.00),
(301, 112, 'HOMEMADE DAING NA BANGUS', 1, 312.00),
(302, 112, 'CAPPUCCINO (HOT)', 1, 120.00),
(303, 112, 'Espresso', 1, 95.00),
(304, 112, 'AMERICANO (HOT)', 1, 110.00),
(305, 112, 'STRAWBERRY MILK', 1, 165.00),
(306, 112, 'COOKIES & CREAM', 1, 195.00),
(307, 113, 'Carrot Cake Slice', 1, 190.00),
(308, 113, 'Espresso', 1, 95.00),
(309, 114, 'PHILLY CHEESESTEAK PANINI', 3, 307.00),
(310, 114, 'COOKIES & CREAM', 1, 195.00),
(311, 114, 'STRAWBERRY MILK', 1, 165.00),
(312, 114, 'MATCHA', 1, 160.00),
(313, 115, 'JOSE CUERVO SILVER', 1, 130.00),
(314, 115, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(315, 116, 'STRAWBERRY MILK', 1, 165.00),
(316, 116, 'COOKIES & CREAM', 1, 195.00),
(317, 116, 'SHRIMP AGLIO OLIO', 1, 348.00),
(318, 116, 'ALLE VONGOLE', 1, 321.00),
(319, 117, 'PHILLY CHEESESTEAK PANINI', 2, 307.00),
(320, 117, 'STRAWBERRY MILK', 1, 165.00),
(321, 117, 'COOKIES & CREAM', 1, 195.00),
(322, 118, 'MOLO SOUP', 1, 158.00),
(323, 118, 'CHICKEN WITH PARMESAN SHAVINGS', 1, 328.00),
(324, 118, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(325, 118, 'FRIED CALAMARI', 1, 242.00),
(326, 118, 'STRAWBERRY MILK', 1, 165.00),
(327, 119, 'Friends', 1, 597.00),
(328, 120, 'FRIED CALAMARI', 1, 242.00),
(329, 121, 'FRIED CALAMARI', 1, 242.00),
(330, 122, 'GAMBAS AND CHORIZO', 1, 368.00),
(331, 122, 'FRIED CALAMARI', 1, 242.00),
(332, 123, 'HAND - CUT POTATO FRIES', 1, 195.00),
(333, 124, 'GAMBAS AND CHORIZO', 1, 368.00),
(334, 125, 'FRIED CALAMARI', 1, 242.00),
(335, 125, 'GAMBAS AND CHORIZO', 1, 368.00),
(336, 125, 'HAND - CUT POTATO FRIES', 1, 195.00),
(337, 126, 'Fried Siken', 2, 120.00),
(338, 126, 'COOKIES & CREAM', 1, 195.00),
(339, 126, 'STRAWBERRY MILK', 1, 165.00),
(340, 127, 'Couples', 1, 242.00),
(341, 128, 'MOLO SOUP', 1, 158.00),
(342, 128, 'MOZZARELLA WITH CARAMELIZED WALNUTS AND APPLES', 1, 344.00),
(343, 128, 'CARBONARA (NO CREAM)', 1, 327.00),
(344, 129, 'FRIED CALAMARI', 1, 242.00),
(345, 129, 'GAMBAS AND CHORIZO', 1, 368.00),
(346, 130, 'pancit Bihon', 1, 120.00),
(347, 130, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(348, 130, 'PHILLY CHEESESTEAK PANINI', 1, 307.00),
(349, 131, 'GAMBAS AND CHORIZO', 1, 368.00),
(350, 131, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(351, 132, 'FRIED CALAMARI', 1, 242.00),
(352, 132, 'AMERICAN BREAKFAST', 1, 323.00),
(353, 133, 'FRIED CALAMARI', 1, 242.00),
(354, 134, 'HISBISCUS', 1, 130.00),
(355, 134, 'MATCHA', 1, 160.00),
(356, 134, 'SHRIMP AGLIO OLIO', 1, 348.00),
(357, 134, 'CARBONARA (NO CREAM)', 1, 327.00),
(358, 135, 'Couples', 1, 1021.00),
(359, 136, 'FRIED CALAMARI', 1, 242.00),
(360, 137, 'Gambas and Chorizo', 1, 368.00),
(361, 138, 'FRIED CALAMARI', 1, 242.00),
(362, 138, 'HAND - CUT POTATO FRIES', 1, 195.00),
(363, 139, 'Gambas and Chorizo', 1, 368.00),
(364, 139, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00),
(365, 140, 'FRIED CALAMARI', 1, 242.00),
(366, 141, 'Friends', 1, 281.00),
(367, 142, 'CARBONARA (NO CREAM)', 1, 327.00),
(368, 143, 'SIRRACHA BUFFALO WINGS 6 PCS', 1, 377.00);

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
(1, 332, 'Buffalo Sauce', 25.00),
(2, 333, 'Extra Rice', 25.00),
(3, 337, 'ketchup', 5.00),
(4, 349, 'Extra Mozzarella', 40.00),
(5, 363, 'Extra Spicy', 15.00);

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
(0, 11, 112.50, 'maya', '2025-05-03 08:14:43'),
(0, 89, 329.00, 'maya', '2025-05-15 06:09:20'),
(0, 62, 849.00, 'gcash', '2025-06-01 08:17:56'),
(0, 137, 184.00, 'cash', '2025-06-07 13:45:02');

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
(0, '', 6, 4000.00, 'Cash', NULL, '2025-06-08 20:54:25', NULL);

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
  `description` text DEFAULT NULL,
  `floor` varchar(10) DEFAULT NULL,
  `total_rooms` int(11) NOT NULL DEFAULT 0,
  `available_rooms` int(11) NOT NULL DEFAULT 0,
  `status` enum('Available','Not Available') DEFAULT 'Available',
  `room_number` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `beds` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, '101', 1, 'active', '2025-06-09 15:57:41', '2025-06-11 13:12:36'),
(4, 1, '102', 2, 'available', '2025-06-09 17:54:01', '2025-06-11 09:35:23'),
(5, 1, '103', 3, 'occupied', '2025-06-09 17:54:25', '2025-06-10 17:04:39'),
(6, 1, '202', 3, 'occupied', '2025-06-10 06:06:30', '2025-06-11 13:12:36'),
(7, 2, '204', 1, 'active', '2025-06-10 06:07:12', '2025-06-11 13:01:59'),
(8, 2, '203', 2, 'available', '2025-06-10 17:08:52', '2025-06-10 17:10:15');

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
(1, 9, 49, 5.0, 'Maganda, lupet', '2025-05-10 04:10:10'),
(2, 1, 49, 5.0, 'linis, good quality', '2025-05-10 04:40:42'),
(3, 1, 38, 5.0, 'Test', '2025-05-11 11:28:05'),
(4, 1, 53, 4.0, 'nice try diddy', '2025-05-13 05:15:03'),
(5, 1, 64, 5.0, 'ilove it ', '2025-05-14 05:04:09'),
(6, 2, 29, 5.0, 'Nice Iloveyouuu ', '2025-05-16 08:06:20'),
(7, 3, 3, 5.0, 'nice\r\n', '2025-05-29 05:52:24'),
(8, 2, 57, 2.0, 'luv it', '2025-06-10 07:30:46');

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
  `rating` decimal(3,1) DEFAULT 5.0,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `discount_valid_until` date DEFAULT NULL,
  `rating_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `room_type`, `price`, `capacity`, `description`, `beds`, `rating`, `image`, `image2`, `image3`, `discount_percent`, `discount_valid_until`, `rating_count`, `status`, `created_at`) VALUES
(1, 'Double Occupancy', 3200.00, 2, 'For double person', '1 MasterBed', 0.0, 'room_6847040b435d5.jpg', '', '', 0, NULL, 5, 'active', '2025-06-09'),
(2, 'Standard Double Room', 2500.00, 2, 'Good', '1 Single bed', 3.5, 'room_68470434411e0.jpg', '', '', 0, NULL, 2, 'active', '2025-06-09'),
(7, 'Triple Occupancy', 5200.00, 5, 'Hi', '2 Single Beds, 1 master bed', 5.0, 'room_68471fa1804d7.jpg', '', '', 0, NULL, 5, '', '2025-06-09');

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
(1, 7),
(1, 8),
(1, 9),
(7, 9),
(1, 11),
(7, 11);

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
  `joining_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shift_start` time DEFAULT NULL,
  `shift_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`emp_id`, `emp_name`, `staff_type_id`, `shift_id`, `id_card_type`, `id_card_no`, `address`, `contact_no`, `joining_date`, `updated_at`, `shift_start`, `shift_end`) VALUES
(1, 'Maureen Aizel Almazan', 2, 0, 0, '', 'Suqui , Calapan City', 9636781352, '2025-05-14 16:14:50', '2025-06-01 06:05:04', '14:00:00', '23:00:00'),
(2, 'RIca Faith', 2, 0, 0, '', 'Libis, Calapan City', 9412222222, '2025-05-14 16:07:14', '2025-05-14 18:50:28', '06:00:00', '14:01:00'),
(4, 'Alma Enciso', 3, 0, 0, '', 'Suqui, Calapan City', 9695214382, '2025-05-14 18:49:35', '2025-05-14 18:49:35', '06:00:00', '14:00:00'),
(5, 'Jhaira Abordo', 3, 0, 0, '', 'Libis, Calapan City', 9127418448, '2025-05-21 04:07:24', '2025-05-21 04:07:24', '14:00:00', '23:00:00'),
(6, 'Corazon De lara', 1, 0, 0, '', 'Lalud', 9195672453, '2025-06-03 16:17:18', '2025-06-03 16:17:18', '01:15:00', '09:15:00');

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
(2, 'Assitant Manager'),
(3, 'Cashier'),
(4, 'Head Pastry '),
(5, 'Barista'),
(6, 'Stock Controller'),
(7, 'Head Chef'),
(8, 'Assistant Chef'),
(9, 'Kitchen Assistant');

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
(1, 50, 'Couples', '09362846372', 'johnoliver.02051990@gmail.com', '2025-05-02', '10:30:00', 2, NULL, 'gcash', 2320.00, 0.00, 2320.00, 0.00, 'Processing', 'Confirmed', 'Ultimate', '1025861199411', 'payment_1746927648_682000202f118.png', NULL, NULL, '2025-05-11 01:40:48', 'full', NULL, NULL, NULL),
(5, 1, 'Couples', '09107652897', 'roxanner@gmail.com', '2025-05-04', '16:00:00', 2, '', 'cash', 534.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-13 18:23:19', 'full', '0', 'Roxanne Ramirez', NULL),
(7, 1, 'Family', '09282267452', 'Gab_01@gmail.com', '2025-05-04', '12:53:00', 10, '', 'cash', 1090.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-15 04:50:46', 'full', '0', 'Gabriel Matibag', NULL),
(8, 1, 'Couples', '09363950699', 'NicolasAbelgas2201@gmail.com', '2025-05-05', '15:01:00', 2, '', 'cash', 523.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-15 05:03:43', 'full', '0', 'Nicolas Abelgas', NULL),
(9, 1, 'Couples', '09195622446', 'Pat22@gmail.com', '2025-05-06', '15:13:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-15 05:15:28', 'full', '0', 'Patrick Delas Alas', NULL),
(19, 62, 'Couples', '09352895173', 'ellymildred846@gmail.com', '2025-05-19', '10:00:00', 2, NULL, 'gcash', 377.00, 0.00, 377.00, 0.00, 'Processing', 'cancelled', 'Ultimate', '1023317335894', 'payment_1747538049_68295081346f6.jpeg', NULL, NULL, '2025-05-18 03:14:09', 'full', NULL, NULL, NULL),
(20, 1, 'Couples', '09292256671', 'Ajhayajhay11@gmail.com', '2025-05-19', '22:07:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 11:09:14', 'full', '0', 'Ahjay Russel Alveyra', NULL),
(21, 1, 'Couples', '09294326751', 'Imursasa@gmail.com', '2025-05-27', '20:12:00', 2, '', 'cash', 281.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 11:14:41', 'full', '0', 'Sasa Palemo', NULL),
(22, 1, 'Couples', '09998778768', 'Leinadrous@gmail.com', '2025-06-02', '21:35:00', 2, '', 'cash', 968.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 11:37:36', 'full', '0', 'Daniel Granil', NULL),
(23, 1, 'Couples', '09998778768', 'Sam006@gmail.com', '2025-06-05', '21:46:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 12:48:18', 'full', '0', 'Samantha Almeda', NULL),
(24, 1, 'Couples', '09998778768', 'Sam006@gmail.com', '2025-05-19', '20:56:00', 2, '', 'cash', 2092.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 12:56:47', 'full', '0', 'Samantha Almeda', NULL),
(25, 1, 'Couples', '09998778768', 'Sam006@gmail.com', '2025-05-27', '10:08:00', 2, '', 'cash', 726.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 13:10:08', 'full', '0', 'Samantha Almeda', NULL),
(26, 1, 'Family', '09363950699', 'Ajhay24@gmail.com', '2025-06-03', '22:55:00', 10, '', 'cash', 484.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 13:57:01', 'full', '0', 'Ajhay Razzel', NULL),
(27, 1, 'Family', '0987654311', 'NicolasAbelgas2201@gmail.com', '2025-05-27', '22:08:00', 10, '', 'cash', 484.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 14:08:29', 'full', '0', 'Nicolas Abelgas', NULL),
(28, 1, 'Couples', '0987654311', 'Leinadrous@gmail.com', '2025-05-28', '12:27:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 14:29:47', 'full', '0', 'Daniel Granil', NULL),
(29, 1, 'Couples', '09998778768', 'Leinadrous@gmail.com', '2025-06-03', '12:48:00', 2, '', 'cash', 484.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 14:50:22', 'full', '0', 'Daniel Granil', NULL),
(30, 1, 'Couples', '09998778768', 'Sam006@gmail.com', '2025-05-27', '12:57:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 14:59:05', 'full', '0', 'Samantha Almeda', NULL),
(31, 1, 'Couples', '09998778768', 'christianrealisan45@gmail.com', '2025-06-05', '13:03:00', 2, '', 'cash', 484.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 15:06:45', 'full', '0', 'Samantha Almeda', NULL),
(32, 1, 'Couples', '0987654311', 'christianrealisan45@gmail.com', '2025-06-05', '13:10:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 15:12:30', 'full', '0', 'Samantha Almeda', NULL),
(33, 1, 'Family', '09998778768', 'christianrealisan45@gmail.com', '2025-06-04', '13:23:00', 10, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 15:25:18', 'full', '242.00', 'Samantha Almeda', NULL),
(34, 1, 'Family', '0987654311', 'Leinadrous@gmail.com', '2025-05-29', '14:54:00', 10, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 16:56:12', 'full', '242.00', 'Daniel Granil', NULL),
(35, 1, 'Family', '0987654311', 'NicolasAbelgas2201@gmail.com', '2025-06-04', '14:00:00', 10, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 17:02:50', 'full', '242.00', 'Nicolas Abelgas', NULL),
(36, 1, 'Couples', '09292256671', 'Pat22@gmail.com', '2025-06-04', '14:04:00', 2, '', 'cash', 726.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-18 17:06:38', 'full', '726.00', 'Patrick Delas Alas', NULL),
(37, 1, 'Family', '09192271856', 'ReyzhelleMendoza@gmail.com', '2025-06-04', '14:17:00', 10, '', 'cash', 484.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Family', NULL, NULL, NULL, NULL, '2025-05-18 17:19:41', 'full', '484.00', 'Reyzhelle Mendoza', NULL),
(38, 1, 'Couples', '09456419862', 'cliffordmusni@gmail.com', '2025-05-21', '11:13:00', 2, '', 'cash', 368.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-19 02:14:03', 'down', '184.00', 'Clifford Musni', NULL),
(39, 29, 'Family', '09127418448', 'chanomabalo@gmail.com', '2025-05-19', '10:17:00', 10, NULL, 'maya', 368.00, 184.00, 184.00, 184.00, 'Partially Paid', 'Pending', 'Ultimate', 'qqqqqqqqqq2', 'payment_1747621108_682a94f44e85f.jpg', NULL, NULL, '2025-05-19 02:18:28', 'partial', NULL, NULL, NULL),
(40, 1, 'Couples', '09158573942', 'cabalmyra@gmail.com', '2025-05-20', '10:18:00', 2, '', 'gcash', 904.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-05-19 02:19:05', 'full', '904.00', 'Myra Cabal', NULL),
(41, 29, 'Family', '09127418448', 'chanomabalo@gmail.com', '2025-05-19', '10:17:00', 10, NULL, 'gcash', 377.00, 188.50, 188.50, 188.50, 'Partially Paid', 'cancelled', 'Ultimate', 'qqqqqqqqqq2', 'payment_1747621195_682a954bad212.png', NULL, NULL, '2025-05-19 02:19:55', 'partial', NULL, NULL, NULL),
(42, 62, 'Couples', '09352895173', 'ellymildred846@gmail.com', '2025-05-20', '10:23:00', 2, NULL, 'gcash', 1500.00, 0.00, 1500.00, 0.00, 'Processing', 'Pending', 'Ultimate', '102345678906', 'payment_1747621613_682a96ed17286.jpg', NULL, NULL, '2025-05-19 02:26:53', 'full', NULL, NULL, NULL),
(43, 3, 'Couples', '09127418448', 'cashier@example.com', '2025-05-31', '17:08:00', 2, NULL, 'gcash', 1698.00, 849.00, 849.00, 849.00, 'Partially Paid', 'Pending', 'Ultimate', '4015841898432', 'payment_1748675383_683aab375bee9.jpg', NULL, NULL, '2025-05-31 07:09:43', 'partial', NULL, NULL, NULL),
(44, 2, 'Couples', '09563740492', 'babylynjumig@gmail.com', '2025-06-02', '16:03:00', 2, '', 'cash', 523.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-06-01 08:04:24', 'full', '523.00', 'Rhovelyn De Guzman', NULL),
(45, 2, 'Friends', '09924401104', 'jamelynmanongsong2@gmail.com', '2025-06-02', '17:00:00', 4, '', 'cash', 597.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Friends', NULL, NULL, NULL, NULL, '2025-06-02 06:12:29', 'down', '298.50', 'Jamelyn M Manongsong', NULL),
(46, 3, 'Couples', '09127418448', 'cashier@example.com', '2025-06-11', '16:51:00', 2, NULL, 'gcash', 242.00, 0.00, 242.00, 0.00, 'Processing', 'Pending', 'Ultimate', '2929839292', 'payment_1748847334_683d4ae661ffe.jpg', NULL, NULL, '2025-06-02 06:55:34', 'full', NULL, NULL, NULL),
(47, 29, 'Couples', '09127418448', 'chanomabalo@gmail.com', '2025-06-02', '15:49:00', 2, NULL, 'gcash', 393.00, 196.50, 196.50, 196.50, 'Partially Paid', 'Pending', 'Ultimate', '17273732871', 'payment_1748850587_683d579b291eb.jpeg', NULL, NULL, '2025-06-02 07:49:47', 'partial', NULL, NULL, NULL),
(48, 29, 'Friends', '09127418448', 'chanomabalo@gmail.com', '2025-06-02', '15:51:00', 3, NULL, 'gcash', 805.00, 0.00, 805.00, 0.00, 'Processing', 'cancelled', 'Ultimate', '4027464293841', 'payment_1748850723_683d58232d190.jpg', NULL, NULL, '2025-06-02 07:52:03', 'full', NULL, NULL, NULL),
(49, 1, 'Couples', '09195672453', 'alfredaceveda.3@gmail.com', '2025-07-03', '15:14:00', 2, '', 'cash', 242.00, 0.00, 0.00, 0.00, 'Pending', 'Pending', 'Couples', NULL, NULL, NULL, NULL, '2025-06-03 16:16:29', 'full', '242.00', 'Realisana', NULL),
(50, 1, 'Couples', '09361496784', 'ellymildred846@gmail.com', '2025-06-10', '16:00:00', 2, '', 'cash', 1021.00, 0.00, 0.00, 0.00, 'Pending', 'Confirmed', 'Couples', NULL, NULL, NULL, NULL, '2025-06-06 17:59:45', 'full', '1021.00', 'Esme', NULL),
(51, 1, 'Friends', '09127418448', 'aizzyvillanueva43@gmail.com', '2025-06-10', '14:09:00', 4, '', 'cash', 281.00, 0.00, 0.00, 0.00, 'Pending', 'Confirmed', 'Friends', NULL, NULL, NULL, NULL, '2025-06-09 04:10:12', 'down', '140.50', 'Aizzy Villanueva', NULL);

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
(1, 1, 'available', NULL, NULL, '2025-06-01 10:13:11'),
(2, 2, 'available', NULL, NULL, '2025-06-01 10:13:16'),
(3, 3, 'available', NULL, NULL, '2025-06-01 10:13:19'),
(4, 4, 'available', NULL, NULL, '2025-06-01 10:23:12'),
(5, 5, 'available', NULL, NULL, '2025-06-01 10:23:15'),
(6, 6, 'available', NULL, NULL, '2025-06-01 10:13:26'),
(7, 7, 'available', NULL, NULL, '2025-06-01 10:13:28'),
(8, 8, 'occupied', '2025-06-02 22:15:27', NULL, '2025-06-02 14:15:27'),
(9, 9, 'available', NULL, NULL, '2025-06-01 10:13:32'),
(10, 10, 'available', NULL, NULL, '2025-06-01 10:13:34');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_packages`
--

INSERT INTO `table_packages` (`id`, `package_name`, `price`, `capacity`, `description`, `menu_items`, `available_tables`, `image_path`, `image1`, `image2`, `image3`, `image4`, `image5`, `status`, `reason`) VALUES
(1, 'Couples', '0', 2, 'Perfect for couples', '1 Appetizer, 2 Mains', 0, 'uploads/table_packages/6822f49b3312f.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(3, 'Family', '0', 10, 'Great for family gatherings', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Dessert', 3, 'uploads/table_packages/6822f4bb526f1.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL),
(4, 'Package A', '20000', 30, 'Basic package for large groups', '1 Appetizer, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert', 2, 'uploads/table_packages/682336a850747.jpg', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(5, 'Package B', '25000', 38, 'Premium package with extra services', '2 Appetizer, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 1, 'uploads/table_packages/682336b4d84c6.webp', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(6, 'Package C', '40000', 50, 'All-inclusive luxury package', '3 Appetizer, 2 Pasta, 2 Mains, Wagyu, Steak Station, Salad Bar, Desserts, Drinks', 2, 'uploads/table_packages/682336c0eed71.jpg', 'uploads/tables/package1.jpg', 'uploads/tables/package2.jpg', 'uploads/tables/package3.jpg', 'uploads/tables/package4.jpg', 'uploads/tables/package5.jpg', 'active', NULL),
(7, 'Friends', '0', 4, 'Good for bonding with friends', '', 2, 'uploads/table_packages/6822f543b97a1.png', NULL, NULL, NULL, NULL, NULL, 'active', NULL);

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
(1, 'Alfred hendrik', 'Aceveda', 'alfred@gmail.com', '09362715617', 'Balite Calapan City Oriental Mindoro', '$2y$10$efqc04ABmXuKUBzj1BRlmuytMLjir6CJ.LrTm2qw81/rS94GcA54u', NULL, NULL, 'admin', NULL, NULL, NULL, 0, '532296', '2025-05-14 12:45:29'),
(2, NULL, NULL, 'frontdesk@example.com', NULL, NULL, '$2y$10$gKCFBo96Q51u5PeLc3ZT6OnrMg47XQpYTKECADPF6skWy5ipIgdgG', NULL, NULL, 'frontdesk', NULL, NULL, NULL, 0, NULL, NULL),
(3, 'Alma', ' Enciso ', 'cashier@example.com', '09127418448', 'Libis, Calapan City', '$2y$10$kwMXcUy2XFwfJ1IyAKXHCe.MLTdIGUwJrZSOSF5gw2vJ6gzE6oO86', NULL, '6834118ad33cf.jpg', 'cashier', NULL, NULL, NULL, 0, NULL, NULL),
(5, 'Aizzy', 'Villanueva', 'aizzyvillanueva43@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$9Of5FaVHvCt/YsEnryDRnOjxkRE6oS1BhqvJnl/YJ4ZL4RnZo6sVK', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(6, 'Aizzy', 'Villanueva', 'aizzyvillanueva34@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$r1X5exzjzJcmM.v3uGBNXeXiN.QkoU1QOIYDIG.7UjmZG.qmxt0hy', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(7, 'Fammela', 'De Guzman', 'Fammela45@gmail.com', '912345678787', 'wawa calapan city', '$2y$10$Z94WFz0rzhGwbouxahK5CekTfN.237R11cWycWsRMZJMeYFK78e8i', NULL, NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(8, 'chano', 'Realisan', 'christianrealisan40@gmail.com', '912345678787', 'tawagan', '$2y$10$zWivDB8Tvv9d4o42LtPGsuRS087Ox8M2LFz6F6zfYvyxy74E0vOzu', 'chanopassword', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(9, 'Aizzy', 'Villanueva', 'aizzyvillanueva5@gmail.com', '09362715617', 'Lumangbayan Calapan City', '$2y$10$6FJdPRpNRHB5rzVQ6L8EO.7xNMFRTQ.qS84uCvwkqB/nmB1aAx5Fy', '020104', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(10, 'Fammela', 'De Guzman', 'fammeladeguzman21@gmail.com', '09362715617', 'Wawa, Calapan City', '$2y$10$fo1HrZCUvSrEh8InzHzuoORab6vzOZayXnF2iLrmLQeBmye/mNSl.', 'fammelapassword', NULL, 'frontdesk', NULL, NULL, NULL, 0, '387855', '2025-06-06 01:09:27'),
(11, 'Alfred', 'Aceveda', 'alfredaceveda.3@gmail.com', '09363950698', NULL, '$2y$10$VtAD8.4Tl0ncmczJ5WgYcuB1sMQ1bU7TdjVDKTzZuC11MZRYoykny', NULL, NULL, '', NULL, '749192', '2025-04-14 09:50:14', 0, NULL, NULL),
(12, 'Alfred', 'Aceveda', 'cyvieshi@gmail.com', '09363950698', NULL, '$2y$10$HOX.EaHIlJlphxRlYhjs0OVQpDM.QdgAt.rCH7XaDc2zvZJE28d.m', NULL, NULL, '', NULL, '891266', '2025-04-14 09:51:28', 0, NULL, NULL),
(13, 'aizzy', 'villanueva', 'aizzy2004@gmail.com', '09127418448', NULL, '$2y$10$.VtKuRPB5t4v8XJ3pIe2Ye8Xq.JRF2nwv172DA0jz/dM7vYxU4J5u', NULL, NULL, '', NULL, '084580', '2025-04-14 10:04:38', 0, NULL, NULL),
(14, 'aizzy', 'villanueva', 'macapagalkenjo@gmail.com', '09127418448', NULL, '$2y$10$5JiAVwo4MtryVBR3aGnBpeAlGiVErB8ay/cc7kGjSot6FB6XY/io.', NULL, NULL, '', NULL, '397271', '2025-04-14 10:05:25', 0, NULL, NULL),
(16, 'Christian', 'Realisan', 'myraluceno@gmail.com', '09234567878', NULL, '$2y$10$8Xe899PsTnwiHbdEjVPJJuYJWP1bO8oK81pWIQcHo.xya7glCf4Z6', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(17, 'Christian', 'Realisan', 'enhymwaa@gmail.com', '09234567878', NULL, '$2y$10$HAPeuFiTcMK48yQ/ZHbRrORn8xBMrLUfwBd2wc8B6H.JvVAe16OKq', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(18, 'Fammela ', 'De Guzman ', 'mystery.woman1242@gmail.com', '09951779221', NULL, '$2y$10$fbPxCZiKhXdP9nPYQYN7jeBDGFVowdvBZ6LBTS9IWbSLm4Eda5VZ2', NULL, NULL, '', NULL, NULL, NULL, 0, NULL, NULL),
(29, 'Christian', 'Realisan', 'chanomabalo@gmail.com', '09127418448', NULL, '$2y$10$gdVoAFsxeffZVL4FBPpNqO5gAdrA6xDNnRY4PXL9NAvjeHhVZWN2.', NULL, 'profile_683d70f3c26f72.16541920.jpeg', 'admin', NULL, NULL, NULL, 1, '647593', '2025-05-31 23:00:08'),
(30, 'Julie', 'Cruz', 'juliecruz12@gmail.com', '09123456799', NULL, '$2y$10$dLY3pYMuNR.NjqljNfbnoe3UEBUviSwMhstRFPmaaAad6l3vkk1wW', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(31, 'Sherie', 'Morales', 'sheriemorales2@gmail.com', '09123456799', NULL, '$2y$10$q7nJc4vJVZHpHThMmQLUX.xdo8MlOsOonVZeivevY6q9rdKTt9OGS', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(32, 'Poldo', 'Almoguera', 'poldorivera07@gmail.com', '09937167503', NULL, '$2y$10$jccQt4zE6XpLLyLjofHVJu4N4FIdk4Q/cVaJF7f5jm4.VjL7wMR2a', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(33, 'Fammela', 'De Guzman', 'mysterywoman1242@gmail.com', '09363960987', NULL, '$2y$10$NK5wJrgzRWWcic//rxwiQeqZec2gSUBEee4CusxokGWd9xmw1rzxu', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(34, 'Christian', 'Realisan', 'chano@gmail.com', '09123456789', NULL, '$2y$10$ibcgOUmMfiJlMiaYfT5fP.N5WVpQozypiPi7cTtl6H5DBJuypmTSu', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(35, 'Myra Kristine Grace ', 'Luceño', 'myraluceno59@gmail.com', '09638322673', NULL, '$2y$10$aB1qDp6yq48CThqcCG2.S.9JtJFmkAA75eqL8/bFUXi9sMSiGJwZq', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(36, 'Myra', 'Aceveda', 'myra2006@gmail.com', '09638322673', NULL, '$2y$10$PWhwxvCUIqKngu7Vr3unQ.lHzYNSbO9mIQ8dvF9CZ.KPalEIX9.UG', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(37, 'Raquel', 'Macapagal', 'raquelmacapagal101089@gmail.com', '09632088569', NULL, '$2y$10$FLP0fDOU.u7KyibhDu.gKulGewBNF8.rBJC51BH31V3uzOsZvbnWa', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(38, 'David', 'Pontanares', 'davidpontanares@gmail.com', '09510977912', NULL, '$2y$10$Iv8XAoCWzpA6om.2iwBxqu.rP3K5JpBAjhiEeqMXNo7Z8WL7OWi8q', NULL, NULL, 'customer', NULL, NULL, NULL, 0, NULL, NULL),
(40, 'Lea', 'Mendoza', 'aizacatapangvillanueva2004@gmail.com', '09128383948', NULL, '$2y$10$hROyiJ2eKei2rzKVcrqqtOe1yJgd0OGc1JghLdnyNu1iKwbJCWf26', NULL, '682a1d97d1055.png', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(41, 'Jhaira', 'Abordo', '', NULL, '', '$2y$10$wDKnU0Z9NDmK0UC8GHxYuOjIqANJ4RCkGvnZV2LMHIMDLwbMT9AgS\n', NULL, NULL, 'cashier', NULL, NULL, NULL, 0, NULL, NULL),
(42, 'Jhaira', 'Abordo', 'jhairaabordo@gmail.com', '09123456789', 'Libis Calapan City Oriental Mindoro\r\n', '$2y$10$N14QZWUoNNnjEr/ZtMuLdefbFX.C.RGwTqgMrO4Z9vkXryI0ZL.Im', NULL, 'uploads/profile/42.jpg', 'cashier', NULL, NULL, NULL, 1, NULL, NULL),
(43, 'Aizzy', 'Villanueva', 'AizzyVillanueva43@example.com', '09362715617', NULL, 'Aizzykupal', 'Aizzykupal', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(44, 'Christian', 'Realisan', 'admin123@example.com', '912345678787', NULL, 'Bastapassword', 'Bastapassword', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(45, 'Christian', 'Realisan', 'admin1@example.com', '912345678787', NULL, 'adminpassword', 'adminpassword', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(46, 'Fammela', 'De Guzman', 'fammela@example.com', '09362715617', NULL, 'fammela1', 'fammela1', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(47, 'Fammela', 'De Guzman', 'fammela2@example.com', '09362715617', NULL, '$2y$10$KYCm9UZyOa5Fi3GR59azNe.BDI7ICAm4MmEGQzEQXoqL2fC/604VW', 'fammela1', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(48, 'Maureen', 'Almazan', 'casaestelahotelcafe@gmail.com', '09087474892', NULL, '$2y$10$0opBBp4BFWgjWSxUfc1i6eCcAwEHaExHWozd3eBU9bUn38CfefZ1S', 'casaestela', NULL, 'admin', NULL, NULL, NULL, 0, NULL, NULL),
(49, 'Alfred', 'Aceveda', 'allainecutiee@gmail.com', '09362715617', NULL, '$2y$10$soGC7uJGWT2nLEKqMH0zquBDwbsze3HF3O28VrxDTFbVuCEz9sCuu', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(50, 'John Oliver', 'Bautista ', 'johnoliver.02051990@gmail.com', '09362846372', NULL, '$2y$10$dFKCt2/0cWn71MdJt/hZG.cd4cFH9C/8zr7dQUyZc4.7vEImKqtja', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(51, 'Queen', 'Bautista ', 'Bautista.MariaQueen@gmail.com', '09543685263', NULL, '$2y$10$m0nZSjGeML2huX5le10PdO5H8fJp8NZ0AkHK4W5excQ.HRsHU9Zxu', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(52, 'Jessica ', 'Mendoza ', 'jessicamarieee19@gmail.com', '09453676951', NULL, '$2y$10$kwiqTjPmgRopcrzQVfwJEef6nCbDFaXQ9aTCWJCP4z8tRAWHYu2yy', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(53, 'Jamil M', 'Dela Torre', 'jamildt08@gmail.com', '09632078510', NULL, '$2y$10$1URtXf20fsbiweROhEUEY.sc7Khg82U07uuawU8zD1poanMZOPbMm', NULL, '6822d33bb68b3.jpeg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(54, 'Rizza-Mae', 'Manuel', 'rizzaamores02@gmail.com', '09544504494', NULL, '$2y$10$IQrHAH9kQL17.YBM.YviXOWT/37uUfmObm5dllY./suoc10qjUiPK', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(55, 'John Aerol', 'Jabat', 'johnaeroljabat1018@gmail.com', '09485598856', NULL, '$2y$10$tFbiVjCNs4PAxu6UyCndL.ECNK.5qrt/oEjIED9dMbIwfS.3DemoG', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(56, 'Ruel', 'Rabino', 'rabinoruel1975@gmail.com', '09543267865', NULL, '$2y$10$Y.rwGZpD0BRwcAAGrVIRVueXHEH9cu82GW5rzEONi0f8h6pxKPYQ2', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(57, 'Joanna', 'Hernandez', 'joannamontero05@gmail.com', '09761564588', NULL, '$2y$10$SASxuH0yj.XiFKZf.C3lpO3fzvV/Met/e92lU9wbgcjajighvYbfa', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(58, 'Marcia', 'Guacamelo', 'guacamelomarcia@gmail.com', '09121245636', NULL, '$2y$10$NdfRKEBifGqaJi1xeikMvu233nkXRyXYyitENXRnF9mueVV0f04qW', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(59, 'Shovel  Marie', 'Mabunao', 'shovelmariemabunao@gmail.com', '09636781352', NULL, '$2y$10$Ol2oVQwzqA2k.3FsKsWbOO92zTjhkC0t3hQqqmyi.vdcU/Urh7yQG', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(60, 'Fredencia', 'Lagmat', 'lagmatfredencia@gmail.com', '09509674214', NULL, '$2y$10$6AYAZLusQ18wAFYx/.Pu0.XJMFPSxCrAg2Ev0lZqZJonyUaQ/NmDW', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(61, 'Geraldyn Des', 'Escarez', 'escarezdescruz95@gmail.com', '09105128641', NULL, '$2y$10$C59ddKvFiqajARB7TaPL9ucEdZSZTz5Dv1DTu/XZ.38OVb4PRD6Ge', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(62, 'Ellyza', 'Madrid', 'ellymildred846@gmail.com', '09352895173', NULL, '$2y$10$eO4rvfakIDlpklfJngljP.QRvsHkyrjQeq0yhEzHjy3860l4mpTdK', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(64, 'REY MAR', 'GUNDAY', 'cortezedwin36@gmail.com', '09478215589', NULL, '$2y$10$C/LSDKD9L31aIbkmcJBf4e3sgCZyyrosjh4Q8LQ3aANJ/eFowSDEy', NULL, '68242394e392c.jpg', 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(65, 'Christian', 'Realisan', 'akop35310@gmail.com', '09459732538', NULL, '$2y$10$SmZk9hIdm5zsVmc1BE1BSu.xoREmStzeJ5toex2mvpw0JfJ3ozNQS', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(66, 'Allyson', 'Carpio', 'allysonmildred696@gmail.com', '09951779220', NULL, '$2y$10$CBQgR8Yz1I1D.1vq2VWsjerEChtkQh/6FQqMnaptsFOi4Drrs.atK', NULL, NULL, 'admin', NULL, NULL, NULL, 1, '393878', '2025-06-06 01:25:54'),
(67, 'Aizzy', 'Villanueva', 'aizzycv@gmail.com', '09480625737', NULL, '$2y$10$us8X2bXQzRMEH9bD2N2nD.BAzDvMvHudZGmQ49Vuhrd051O8bfeUq', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL),
(69, 'Ian', 'Silang', 'iansilang123@gmail.com', '09459732578', NULL, '$2y$10$p6VI2vOCtgs22GGv07VTXO.I986Z.97USGmfx7bi1nLcP6VGGiX7q', NULL, NULL, 'customer', NULL, NULL, NULL, 1, NULL, NULL);

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
(0, '09459732538', '128434', '2025-05-23 19:23:32', 0, '2025-05-23 19:08:32'),
(0, '09951779220', '928194', '2025-05-31 06:49:48', 0, '2025-05-31 06:34:48'),
(0, '09480625737', '174299', '2025-05-31 06:57:06', 0, '2025-05-31 06:42:06');

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
(1, 'SMS', 0, 'Under Maintenance', '2025-05-09 04:08:16'),
(2, 'Email', 1, NULL, '2025-05-09 04:07:43');

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
-- Indexes for table `advance_table_orders`
--
ALTER TABLE `advance_table_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_booking_id` (`table_booking_id`);

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
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_bookings_dates` (`check_in`,`check_out`),
  ADD KEY `idx_bookings_status` (`status`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_room_type_id` (`room_type_id`);

--
-- Indexes for table `booking_history`
--
ALTER TABLE `booking_history`
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
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`,`user_id`);

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
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD PRIMARY KEY (`room_number_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD PRIMARY KEY (`review_id`);

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
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `staff_type`
--
ALTER TABLE `staff_type`
  ADD PRIMARY KEY (`staff_type_id`);

--
-- Indexes for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advance_table_orders`
--
ALTER TABLE `advance_table_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `booking_history`
--
ALTER TABLE `booking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `event_packages`
--
ALTER TABLE `event_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `fix_booking_ids_log`
--
ALTER TABLE `fix_booking_ids_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `guest_names`
--
ALTER TABLE `guest_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=515;

--
-- AUTO_INCREMENT for table `menu_items_addons`
--
ALTER TABLE `menu_items_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=464;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=369;

--
-- AUTO_INCREMENT for table `order_item_addons`
--
ALTER TABLE `order_item_addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `room_number_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_reviews`
--
ALTER TABLE `room_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_transfers`
--
ALTER TABLE `room_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `table_bookings`
--
ALTER TABLE `table_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `table_number`
--
ALTER TABLE `table_number`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `table_packages`
--
ALTER TABLE `table_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users_unified`
--
ALTER TABLE `users_unified`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_room_type_id` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `userss` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD CONSTRAINT `room_numbers_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
