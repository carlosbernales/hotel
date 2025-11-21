<?php
require_once 'includes/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();

    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Users table created successfully\n";

    // Insert default user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $sql = "INSERT INTO users (id, username, email, password) 
                VALUES (1, 'default_user', 'default@example.com', 'default_password')";
        $pdo->exec($sql);
        echo "Default user created successfully\n";
    }

    echo "Database setup completed successfully!";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
