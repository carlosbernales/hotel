CREATE TABLE IF NOT EXISTS `facility_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'check',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `facility_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial categories
INSERT INTO `facility_categories` (`name`, `display_order`) VALUES
('Parking', 1),
('Safety & Security', 2),
('Food & Drink', 3),
('Reception Services', 4),
('Languages Spoken', 5),
('Internet', 6),
('Bathroom', 7);

-- Insert initial facilities
INSERT INTO `facilities` (`category_id`, `name`, `display_order`) VALUES
-- Parking
((SELECT id FROM facility_categories WHERE name = 'Parking'), 'Free private parking spaces', 1),
((SELECT id FROM facility_categories WHERE name = 'Parking'), 'Valet parking', 2),
((SELECT id FROM facility_categories WHERE name = 'Parking'), 'Parking garage', 3),
((SELECT id FROM facility_categories WHERE name = 'Parking'), 'Accessible parking', 4),

-- Safety & Security
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), 'Fire extinguishers', 1),
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), 'CCTV', 2),
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), 'Smoke alarms', 3),
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), 'Security alarm', 4),
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), 'Key card access', 5),
((SELECT id FROM facility_categories WHERE name = 'Safety & Security'), '24-hour security', 6),

-- Food & Drink
((SELECT id FROM facility_categories WHERE name = 'Food & Drink'), 'Coffee house', 1),
((SELECT id FROM facility_categories WHERE name = 'Food & Drink'), 'Snack bar', 2),
((SELECT id FROM facility_categories WHERE name = 'Food & Drink'), 'Restaurant', 3),

-- Reception Services
((SELECT id FROM facility_categories WHERE name = 'Reception Services'), 'Private check-in/check-out', 1),
((SELECT id FROM facility_categories WHERE name = 'Reception Services'), 'Luggage storage', 2),
((SELECT id FROM facility_categories WHERE name = 'Reception Services'), '24-hour front desk', 3),

-- Languages Spoken
((SELECT id FROM facility_categories WHERE name = 'Languages Spoken'), 'English', 1),
((SELECT id FROM facility_categories WHERE name = 'Languages Spoken'), 'Filipino', 2),

-- Internet
((SELECT id FROM facility_categories WHERE name = 'Internet'), 'Free Wi-Fi', 1),

-- Bathroom
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Toilet paper', 1),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Bidet', 2),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Slippers', 3),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Private bathroom', 4),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Toilet', 5),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Hairdryer', 6),
((SELECT id FROM facility_categories WHERE name = 'Bathroom'), 'Shower', 7); 