-- Table for page content
CREATE TABLE IF NOT EXISTS page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL,
    hero_title VARCHAR(255) NOT NULL,
    hero_subtitle TEXT,
    section_title VARCHAR(255),
    section_intro TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default values for contact page
INSERT INTO page_content (page_name, hero_title, hero_subtitle, section_title, section_intro) VALUES 
('contact', 'Get in Touch', 'We''d love to hear from you. Send us a message and we''ll respond as soon as possible.', 
'Contact Us', 'Whether you have questions about our accommodations, want to make a special request, or need any assistance, our team is here to help. Reach out through any of the following channels.');

-- Table for contact information items
CREATE TABLE IF NOT EXISTS contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon_class VARCHAR(50) NOT NULL,
    display_text VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
    is_external TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1
);

-- Insert default contact information
INSERT INTO contact_info (icon_class, display_text, link, is_external, display_order) VALUES
('fab fa-facebook', 'Casa Estela Boutique Hotel & Café', 'https://web.facebook.com/casaestelahotelcafe', 1, 1),
('fas fa-envelope', 'casaestelahotelcafe@gmail.com', 'mailto:casaestelahotelcafe@gmail.com', 0, 2),
('fas fa-phone', '0908 747 4892', 'tel:+09087474892', 0, 3),
('fab fa-twitter', '@casaestelahlcf', '#', 1, 4),
('fab fa-instagram', '@casaestelahotelcafe', 'https://www.instagram.com/casaestelahotelcafe', 1, 5); 