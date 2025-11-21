-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Insert sample messages
INSERT INTO messages (sender_id, receiver_id, subject, message, is_read) VALUES
(1, 2, 'Welcome to Front Desk', 'Welcome to the team! Please review the standard operating procedures.', 0),
(1, 3, 'Cash Register Setup', 'Please ensure all cash registers are properly configured for the new day.', 0),
(2, 1, 'Daily Report', 'Here is the daily report for your review.', 1),
(3, 1, 'Cash Count', 'Daily cash count completed and verified.', 1); 