-- First, make sure the table exists
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Delete existing content (if any)
DELETE FROM about_content;

-- Insert new content
INSERT INTO about_content (title, description) VALUES 
('About Casa Estela', 'Welcome to Casa Estela, where comfort meets elegance.

Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you''re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'); 