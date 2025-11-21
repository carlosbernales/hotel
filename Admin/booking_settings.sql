CREATE TABLE IF NOT EXISTS booking_display_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_type ENUM('room', 'table', 'event') NOT NULL,
    display_fields JSON NOT NULL,
    image_settings JSON NOT NULL,
    layout_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_booking_type (booking_type)
);
