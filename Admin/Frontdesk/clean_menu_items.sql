-- Create a temporary table to store unique items
CREATE TEMPORARY TABLE temp_menu_items AS
SELECT MIN(id) as id, category_id, name, price, image_path
FROM menu_items
GROUP BY category_id, name, price;

-- Delete all records from menu_items
TRUNCATE TABLE menu_items;

-- Insert back only the unique records
INSERT INTO menu_items (id, category_id, name, price, image_path)
SELECT id, category_id, name, price, image_path
FROM temp_menu_items
ORDER BY category_id, name;

-- Drop the temporary table
DROP TEMPORARY TABLE temp_menu_items;

-- Update auto_increment value
ALTER TABLE menu_items AUTO_INCREMENT = 1;

-- Delete menu items that don't belong to our valid categories
DELETE FROM menu_items 
WHERE category_id NOT IN (
    SELECT id FROM menu_categories 
    WHERE name IN ('small-plates', 'soup-salad', 'pasta', 'sandwiches', 'coffee', 'iceblend', 'tea', 'otherdrinks')
); 