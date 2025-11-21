-- Create room_numbers table
CREATE TABLE IF NOT EXISTS `room_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `floor` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Available','Occupied','Maintenance') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `room_type_id` (`room_type_id`),
  CONSTRAINT `room_numbers_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add any necessary data migration from the old rooms table
INSERT INTO room_numbers (room_number, room_type_id, status, created_at, updated_at)
SELECT CONCAT('R', id) as room_number, id as room_type_id, 
       CASE 
           WHEN available_rooms > 0 THEN 'Available'
           ELSE 'Occupied'
       END as status,
       NOW() as created_at,
       NOW() as updated_at
FROM room_types
WHERE is_deleted = 0;

-- Update the reservations table to reference room_numbers
ALTER TABLE `reservations` 
ADD COLUMN `room_number_id` INT NULL AFTER `room_type_id`,
ADD CONSTRAINT `fk_reservation_room_number` 
    FOREIGN KEY (`room_number_id`) REFERENCES `room_numbers` (`id`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

-- Update the rooms table to remove the room_number column if it exists
ALTER TABLE `rooms` 
DROP COLUMN IF EXISTS `room_number`;

-- Add indexes for better performance
CREATE INDEX idx_room_number_status ON room_numbers(room_number, status);
CREATE INDEX idx_room_type_status ON room_numbers(room_type_id, status);
