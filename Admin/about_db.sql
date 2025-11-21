-- Create table for main about content
CREATE TABLE IF NOT EXISTS about_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL DEFAULT 'About Casa Estela',
    description TEXT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create table for slideshow images
CREATE TABLE IF NOT EXISTS about_slideshow (
    id INT PRIMARY KEY AUTO_INCREMENT,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NOT NULL,
    display_order INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table for location information
CREATE TABLE IF NOT EXISTS location_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    map_zoom_level INT DEFAULT 15,
    contact_phone VARCHAR(50),
    contact_email VARCHAR(255),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default about content
INSERT INTO about_content (title, description) VALUES (
    'About Casa Estela',
    'Welcome to Casa Estela, where comfort meets elegance. Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you''re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'
);

-- Insert default slideshow images
INSERT INTO about_slideshow (image_path, alt_text, display_order) VALUES
    ('images/garden.jpg', 'Image 1', 1),
    ('images/hall3.jpg', 'Image 2', 2),
    ('images/garden.jpg', 'Image 3', 3),
    ('images/hall.jpg', 'Image 4', 4),
    ('images/gard.jpg', 'Image 5', 5),
    ('images/garden1.jpg', 'Image 6', 6),
    ('images/family.jpg', 'Image 7', 7);

-- Insert default location info
INSERT INTO location_info (address, latitude, longitude, map_zoom_level, contact_phone, contact_email) VALUES (
    'Casa Estela Boutique Hotel & Cafe, Calapan City, Oriental Mindoro',
    13.414545,
    121.183802,
    15,
    '+63 XXX XXX XXXX',
    'info@casaestela.com'
); 