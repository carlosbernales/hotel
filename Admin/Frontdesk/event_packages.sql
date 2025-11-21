-- Create event_packages table if it doesn't exist
CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    menu TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    max_guests INT NOT NULL,
    venue VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample packages for 30 PAX
INSERT INTO event_packages (name, menu, price, max_guests, venue) VALUES
('Package A', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks', 28000.00, 30, 'Cafe'),
('Package B', '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 33000.00, 30, 'Cafe'),
('Package C', '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2 Desserts, Drinks', 46000.00, 30, 'Cafe');

-- Insert sample packages for 50 PAX
INSERT INTO event_packages (name, menu, price, max_guests, venue) VALUES
('Package A - Garden', '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks', 47500.00, 50, 'Garden'),
('Package B - Garden', '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks', 55000.00, 50, 'Garden'),
('Package C - Garden', '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station, Salad Bar, Rice, 2 Desserts, Drinks', 76800.00, 50, 'Garden'); 