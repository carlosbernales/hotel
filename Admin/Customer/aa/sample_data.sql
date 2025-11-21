-- Insert categories
INSERT INTO menu_categories (name, display_name) VALUES
('small-plates', 'SMALL PLATES'),
('soup-salad', 'SOUP & SALAD'),
('pasta', 'PASTA'),
('sandwiches', 'SANDWICHES'),
('coffee', 'COFFEE & LATTE'),
('iceblend', 'ICE BLENDED'),
('tea', 'TEA'),
('otherdrinks', 'OTHER DRINKS');

-- Insert menu items
INSERT INTO menu_items (category_id, name, price, image_path) VALUES
(1, 'Hand-cut Potato Fries', 120.00, 'images/fries.jpg'),
(1, 'Mozzarella Stick', 150.00, 'images/mozzarella.jpg'),
(1, 'Chicken Wings', 180.00, 'images/wings.jpg');

-- Insert add-ons
INSERT INTO menu_item_addons (menu_item_id, name, price) VALUES
(1, 'Cheese', 30.00),
(1, 'Mayo', 50.00),
(2, 'Extra Sauce', 20.00),
(2, 'Extra Mozzarella', 40.00),
(3, 'Buffalo Sauce', 25.00),
(3, 'Extra Ranch', 30.00); 