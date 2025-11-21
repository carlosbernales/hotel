-- Update image paths for menu items
UPDATE menu_items 
SET image_path = CASE category_id
    WHEN 1 THEN 'img/ce.jpg'  -- small-plates
    WHEN 2 THEN 'img/htl.png'  -- soup-salad
    WHEN 3 THEN 'img/Casa.jfif'  -- pasta
    WHEN 4 THEN 'img/ce.jpg'  -- sandwiches
    WHEN 5 THEN 'img/htl.png'  -- coffee
    WHEN 6 THEN 'img/Casa.jfif'  -- iceblend
    WHEN 7 THEN 'img/ce.jpg'  -- tea
    WHEN 8 THEN 'img/htl.png'  -- otherdrinks
    ELSE image_path
END; 