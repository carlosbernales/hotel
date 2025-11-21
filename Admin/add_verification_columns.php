<?php
require_once 'Customer/aa/includes/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->connect();

    // Add verification_code column if it doesn't exist
    $sql = "ALTER TABLE userss 
            ADD COLUMN IF NOT EXISTS verification_code VARCHAR(6),
            ADD COLUMN IF NOT EXISTS verification_expiry DATETIME,
            ADD COLUMN IF NOT EXISTS is_verified BOOLEAN DEFAULT FALSE";
    
    $conn->exec($sql);
    echo "Verification columns added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 