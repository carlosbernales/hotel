CREATE TABLE event_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert the event packages
INSERT INTO event_packages (name, price, description, is_available) VALUES
(
    'Venue Rental Only',
    20000.00,
    '5-hour venue rental\nTables and Tiffany chairs',
    TRUE
),
(
    'Standard Package',
    47500.00,
    'Up to 30 Pax\n5-hour venue rental\nBasic sound system\nStandard decoration\nBasic catering service',
    TRUE
),
(
    'Premium Package',
    55000.00,
    'Up to 30 Pax\n5-hour venue rental\nPremium sound system\nEnhanced decoration\nPremium catering service\nEvent coordinator',
    TRUE
),
(
    'Deluxe Package',
    76800.00,
    'Up to 30 Pax\n5-hour venue rental\nProfessional DJ\nLuxury decoration\nPremium catering service\nEvent coordinator\nPhoto/Video coverage',
    TRUE
); 