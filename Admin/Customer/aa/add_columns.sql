ALTER TABLE bookings
ADD COLUMN room_type_id INT AFTER number_of_guests,
ADD COLUMN room_quantity INT AFTER room_type_id,
ADD COLUMN nights INT AFTER check_out,
ADD COLUMN downpayment_amount DECIMAL(10,2) AFTER payment_option,
ADD COLUMN remaining_balance DECIMAL(10,2) AFTER downpayment_amount,
ADD COLUMN discount_type VARCHAR(50) AFTER remaining_balance,
ADD COLUMN discount_amount DECIMAL(10,2) AFTER discount_type,
ADD COLUMN discount_percentage DECIMAL(5,2) AFTER discount_amount,
ADD COLUMN payment_reference VARCHAR(100) AFTER total_amount,
ADD COLUMN payment_proof VARCHAR(255) AFTER payment_reference; 