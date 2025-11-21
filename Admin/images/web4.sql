-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 06:27 AM
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
-- Database: `web4`
--

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
(4, 2, 'Pogi', 'pogs@gmail.com', 'Kulang ang manok ', '2024-12-20 08:34:55');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `userid`, `payment_method`, `total_price`, `created_at`, `status`) VALUES
(1734545954, 1, 'gcash', 450.00, '2024-12-18 18:19:14', 'Cancelled'),
(1734552193, 1, 'gcash', 150.00, '2024-12-18 20:03:13', 'Cancelled'),
(1734553418, 1, 'gcash', 150.00, '2024-12-18 20:23:38', 'Finished'),
(1734593828, 1, 'gcash', 150.00, '2024-12-19 07:37:08', 'Cancelled'),
(1734601528, 1, 'gcash', 1200.00, '2024-12-19 09:45:28', 'Cancelled'),
(1734687009, 2, 'maya', 1238.00, '2024-12-20 09:30:09', 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addons`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `item_name`, `category`, `price`, `qty`, `addons`) VALUES
(1, 1734545954, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(2, 1734545954, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(3, 1734545954, 'Chicken Wings', 'small-plates', 180.00, 1, NULL),
(4, 1734552193, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(5, 1734553418, 'Spaghetti', 'pasta', 150.00, 1, NULL),
(6, 1734593828, 'Mozzarella Stick', 'small-plates', 150.00, 1, NULL),
(7, 1734601528, 'Mozzarella Stick', 'small-plates', 150.00, 8, NULL),
(8, 1734687009, 'Hand-cut Potato Fries', 'small-plates', 120.00, 1, NULL),
(9, 1734687009, 'Sopas', 'soup-salad', 120.00, 1, NULL),
(10, 1734687009, 'Italian Pasta', 'pasta', 499.00, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `firstname`, `lastname`, `email`, `username`, `profile_photo`, `password`) VALUES
(1, 'Christian', 'Realisan', 'christianrealisan1225@gmail.com', 'chano', 'chano.jpg', '$2y$10$7ZwbHmRIosZjojTluPy/e.QdZsVyOgRldeHdERtuCP1ZOMVB0cz56'),
(2, 'christian', 'realisan', 'christianrealisan13@gmail.com', 'Panget', '', '$2y$10$henHJh4oWayCIGRM3UGHK.3LPs5fmk0tmwII5oj8WVXgODc.mTXjS'),
(3, 'Tanggol ', 'Montenegro', 'tanggol@email.com', 'tanggol', '', '$2y$10$Hx2E5/0aCZra/E3VukJKFOrhnLtAYhXKs5nSB8F8D6dCjeoL49yiC');

--
-- Indexes for dumped tables
--

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
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1734687010;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
