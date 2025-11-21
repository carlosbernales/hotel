-- Fix NULL constraints and default values
ALTER TABLE bookings
  MODIFY COLUMN booking_reference varchar(255) NOT NULL,
  MODIFY COLUMN first_name varchar(50) NOT NULL,
  MODIFY COLUMN last_name varchar(50) NOT NULL,
  MODIFY COLUMN email varchar(255) NOT NULL,
  MODIFY COLUMN contact varchar(20) NOT NULL,
  MODIFY COLUMN check_in date NOT NULL,
  MODIFY COLUMN check_out date NOT NULL,
  MODIFY COLUMN number_of_guests int(11) NOT NULL DEFAULT 1,
  MODIFY COLUMN room_type_id int(11) NOT NULL,
  MODIFY COLUMN payment_option varchar(50) NOT NULL DEFAULT 'full',
  MODIFY COLUMN payment_method varchar(50) NOT NULL,
  MODIFY COLUMN total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
  MODIFY COLUMN status varchar(50) NOT NULL DEFAULT 'pending',
  MODIFY COLUMN nights int(11) NOT NULL DEFAULT 1,
  MODIFY COLUMN downpayment_amount decimal(10,2) NOT NULL DEFAULT 0.00,
  MODIFY COLUMN remaining_balance decimal(10,2) NOT NULL DEFAULT 0.00;

-- Add constraints for valid values
ALTER TABLE bookings
  ADD CONSTRAINT chk_payment_option CHECK (payment_option IN ('full', 'downpayment')),
  ADD CONSTRAINT chk_payment_method CHECK (payment_method IN ('GCash', 'Cash', 'Credit Card', 'Bank Transfer')),
  ADD CONSTRAINT chk_status CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed')),
  ADD CONSTRAINT chk_positive_amount CHECK (total_amount >= 0),
  ADD CONSTRAINT chk_positive_guests CHECK (number_of_guests > 0),
  ADD CONSTRAINT chk_positive_nights CHECK (nights > 0);

-- Add indexes for better performance
ALTER TABLE bookings
  ADD INDEX idx_booking_reference (booking_reference),
  ADD INDEX idx_check_in (check_in),
  ADD INDEX idx_check_out (check_out),
  ADD INDEX idx_status (status),
  ADD INDEX idx_email (email); 