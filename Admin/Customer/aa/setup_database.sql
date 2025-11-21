-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS casa_estela;
USE casa_estela;

-- Create tables
CREATE TABLE menu_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES menu_categories(id)
);

CREATE TABLE menu_item_addons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

-- Insert sample data
INSERT INTO menu_categories (name, display_name) VALUES
('small-plates', 'SMALL PLATES'),
('soup-salad', 'SOUP & SALAD'),
('pasta', 'PASTA'),
('sandwiches', 'SANDWICHES'),
('coffee', 'COFFEE & LATTE'),
('iceblend', 'ICE BLENDED'),
('tea', 'TEA'),
('otherdrinks', 'OTHER DRINKS');

-- Insert sample menu items
INSERT INTO menu_items (category_id, name, price, image_path) VALUES
(1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(1, 'Chicken Wings', 180.00, 'images/wings.jpg');

-- Insert sample add-ons
INSERT INTO menu_item_addons (menu_item_id, name, price) VALUES
(1, 'Cheese', 30.00),
(1, 'Mayo', 50.00),
(2, 'Extra Sauce', 20.00),
(2, 'Extra Mozzarella', 40.00),
(3, 'Buffalo Sauce', 25.00),
(3, 'Extra Ranch', 30.00); 