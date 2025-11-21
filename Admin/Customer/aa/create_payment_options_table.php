<?php
require 'db_con.php';

try {
    // Create payment_options table
    $sql = "CREATE TABLE IF NOT EXISTS payment_options (
        payment_option_id INT AUTO_INCREMENT PRIMARY KEY,
        option_name VARCHAR(100) NOT NULL,
        percentage DECIMAL(5,2) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Insert default payment options if table is empty
    $checkSql = "SELECT COUNT(*) FROM payment_options";
    $count = $pdo->query($checkSql)->fetchColumn();
    
    if ($count == 0) {
        $insertSql = "INSERT INTO payment_options (option_name, percentage) VALUES 
            ('Full Payment', 100.00),
            ('50% Downpayment', 50.00),
            ('30% Downpayment', 30.00)";
        $pdo->exec($insertSql);
    }
    
    echo "Payment options table created and populated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
