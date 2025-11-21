-- Create about_content table if it doesn't exist
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Update the content to match what's in the database
UPDATE about_content 
SET title = 'About Us',
    description = 'Welcome to Casa Estela, where comfort meets elegance.

Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you''re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'
WHERE id = 2;

-- If no content exists, insert it
INSERT INTO about_content (title, description)
SELECT 'About Us', 'Welcome to Casa Estela, where comfort meets elegance.

Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you''re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'
WHERE NOT EXISTS (SELECT 1 FROM about_content WHERE id = 2); 