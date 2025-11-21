-- Update the bookings table collation
ALTER TABLE bookings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Update specific columns that need the same collation
ALTER TABLE bookings 
    MODIFY room_number varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY status varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY booking_reference varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY first_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY last_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY booking_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY email varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY contact varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY payment_option varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY payment_method varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY discount_type varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY payment_reference varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY payment_proof varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY user_types enum('admin','frontdesk') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY extra_bed varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY accepted_at varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 