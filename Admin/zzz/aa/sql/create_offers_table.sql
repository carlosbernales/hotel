CREATE TABLE IF NOT EXISTS `offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `discount` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial data
INSERT INTO `offers` (`title`, `image`, `discount`, `description`, `active`) VALUES
('Weekend Getaway', 'images/family.jpg', '20% OFF', 'Perfect weekend escape with breakfast included', 1),
('Family Package', 'images/couple.jpg', '15% OFF', 'Special rate for family stays with complimentary activities', 1),
('Extended Stay', 'images/4.jpg', '25% OFF', 'Stay longer, save more with our weekly rates', 1); 