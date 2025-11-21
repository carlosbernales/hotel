-- First, delete menu items that are not in these categories to maintain referential integrity
DELETE FROM menu_items 
WHERE category_id NOT IN (
    SELECT id FROM menu_categories 
    WHERE name IN ('small-plates', 'soup-salad', 'pasta', 'sandwiches', 'coffee', 'iceblend', 'tea', 'otherdrinks')
);

-- Then delete all categories except the ones we want to keep
DELETE FROM menu_categories 
WHERE name NOT IN ('small-plates', 'soup-salad', 'pasta', 'sandwiches', 'coffee', 'iceblend', 'tea', 'otherdrinks'); 