-- Clear existing rooms
TRUNCATE TABLE rooms;

-- Insert various room types
INSERT INTO rooms (name, description, bed_type, capacity, price, total_rooms, image, has_aircon, has_wifi, has_tv) VALUES
-- Standard Rooms
('Standard Single Room', 'Comfortable single room perfect for solo travelers, featuring essential amenities and a cozy atmosphere', 
 'Single Bed', 1, 2500.00, 5, 'single.jpg', TRUE, TRUE, TRUE),

('Standard Double Room', 'Cozy room with modern amenities perfect for couples or solo travelers who prefer more space', 
 'Double Bed', 2, 3200.00, 8, 'double.jpg', TRUE, TRUE, TRUE),

('Twin Room', 'Practical room with two single beds, ideal for friends or business travelers', 
 'Two Single Beds', 2, 3400.00, 6, 'twin.jpg', TRUE, TRUE, TRUE),

-- Deluxe Rooms
('Deluxe Double Room', 'Spacious room with premium furnishings and a comfortable seating area', 
 'Queen Bed', 2, 4000.00, 4, 'deluxe_double.jpg', TRUE, TRUE, TRUE),

('Deluxe Twin Room', 'Elegant room with two comfortable beds and additional amenities', 
 'Two Double Beds', 2, 4200.00, 4, 'deluxe_twin.jpg', TRUE, TRUE, TRUE),

-- Family Rooms
('Family Suite', 'Spacious suite perfect for families, featuring separate sleeping areas and extra comfort', 
 'One Queen + Two Single Beds', 4, 5500.00, 3, 'family_suite.jpg', TRUE, TRUE, TRUE),

('Family Connecting Room', 'Two connected rooms ideal for larger families or groups, offering privacy and convenience', 
 'Two Queen Beds', 4, 6000.00, 2, 'connecting.jpg', TRUE, TRUE, TRUE),

('Grand Family Suite', 'Our largest family accommodation with multiple rooms and premium amenities', 
 'One King + Two Double Beds', 6, 7500.00, 2, 'grand_family.jpg', TRUE, TRUE, TRUE),

-- Premium Rooms
('Executive Suite', 'Luxury suite with separate living area and premium amenities for the discerning traveler', 
 'King Bed', 2, 6500.00, 2, 'executive.jpg', TRUE, TRUE, TRUE),

('Honeymoon Suite', 'Romantic suite with special amenities and decorations, perfect for couples', 
 'King Bed', 2, 7000.00, 1, 'honeymoon.jpg', TRUE, TRUE, TRUE),

-- Accessible Room
('Accessible Room', 'Specially designed room with accessibility features and modern amenities', 
 'Two Double Beds', 2, 3500.00, 2, 'accessible.jpg', TRUE, TRUE, TRUE),

-- Budget Options
('Economy Room', 'Budget-friendly room with essential amenities for the cost-conscious traveler', 
 'Double Bed', 2, 2000.00, 5, 'economy.jpg', TRUE, TRUE, TRUE);

-- Update room descriptions with amenities
UPDATE rooms SET description = CONCAT(description, '. Amenities include: air conditioning, free high-speed WiFi, flat-screen TV, desk, private bathroom, and daily housekeeping.')
WHERE has_aircon = TRUE AND has_wifi = TRUE AND has_tv = TRUE;
