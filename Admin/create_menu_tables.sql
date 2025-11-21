-- Create menu_categories table
CREATE TABLE IF NOT EXISTS menu_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    display_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create menu_items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id)
);

-- Create menu_item_addons table
CREATE TABLE IF NOT EXISTS menu_item_addons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

-- Insert sample menu categories
INSERT INTO menu_categories (name, display_name) VALUES
('appetizers', 'Appetizers'),
('main_course', 'Main Course'),
('desserts', 'Desserts'),
('beverages', 'Beverages');

-- Insert sample menu items
INSERT INTO menu_items (category_id, name, description, price, image_path) VALUES
-- Appetizers
(1, 'Calamari Rings', 'Crispy fried squid rings served with marinara sauce', 250.00, 'images/menu/calamari.jpg'),
(1, 'Buffalo Wings', 'Spicy chicken wings with blue cheese dip', 280.00, 'images/menu/wings.jpg'),

-- Main Course
(2, 'Grilled Pork Chop', 'Tender pork chop with mashed potatoes and vegetables', 350.00, 'images/menu/porkchop.jpg'),
(2, 'Beef Steak', 'Premium beef steak with mushroom sauce', 450.00, 'images/menu/steak.jpg'),

-- Desserts
(3, 'Chocolate Cake', 'Rich chocolate cake with vanilla ice cream', 180.00, 'images/menu/chocolate_cake.jpg'),
(3, 'Leche Flan', 'Classic Filipino caramel custard', 120.00, 'images/menu/leche_flan.jpg'),

-- Beverages
(4, 'Fresh Fruit Shake', 'Choice of mango, strawberry, or banana', 120.00, 'images/menu/fruit_shake.jpg'),
(4, 'Iced Tea', 'Homemade iced tea', 80.00, 'images/menu/iced_tea.jpg');

-- Insert sample add-ons
INSERT INTO menu_item_addons (menu_item_id, name, price) VALUES
(1, 'Extra Sauce', 30.00),
(2, 'Extra Blue Cheese Dip', 40.00),
(3, 'Extra Gravy', 35.00),
(4, 'Mushroom Sauce', 45.00); 