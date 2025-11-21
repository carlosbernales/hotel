<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require 'db_con.php';

// Test connection
try {
    if (isset($pdo)) {
        echo "PDO database connection variable exists<br>";
        
        // Test if tables exist
        $stmt = $pdo->query("SHOW TABLES LIKE 'page_content'");
        if ($stmt->rowCount() > 0) {
            echo "page_content table exists<br>";
        } else {
            echo "page_content table does not exist<br>";
            
            // Create the table
            echo "Creating page_content table...<br>";
            $createSql = "CREATE TABLE IF NOT EXISTS page_content (
                id INT AUTO_INCREMENT PRIMARY KEY,
                page_name VARCHAR(50) NOT NULL,
                hero_title VARCHAR(255) NOT NULL,
                hero_subtitle TEXT,
                section_title VARCHAR(255),
                section_intro TEXT,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $pdo->exec($createSql);
            
            // Insert default data
            $insertSql = "INSERT INTO page_content (page_name, hero_title, hero_subtitle, section_title, section_intro) VALUES 
            ('contact', 'Get in Touch', 'We''d love to hear from you. Send us a message and we''ll respond as soon as possible.', 
            'Contact Us', 'Whether you have questions about our accommodations, want to make a special request, or need any assistance, our team is here to help. Reach out through any of the following channels.')";
            $pdo->exec($insertSql);
            echo "page_content table created with default data<br>";
        }
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'contact_info'");
        if ($stmt->rowCount() > 0) {
            echo "contact_info table exists<br>";
        } else {
            echo "contact_info table does not exist<br>";
            
            // Create the table
            echo "Creating contact_info table...<br>";
            $createSql = "CREATE TABLE IF NOT EXISTS contact_info (
                id INT AUTO_INCREMENT PRIMARY KEY,
                icon_class VARCHAR(50) NOT NULL,
                display_text VARCHAR(255) NOT NULL,
                link VARCHAR(255) NOT NULL,
                is_external TINYINT(1) DEFAULT 1,
                display_order INT DEFAULT 0,
                active TINYINT(1) DEFAULT 1
            )";
            $pdo->exec($createSql);
            
            // Insert default data
            $insertSql = "INSERT INTO contact_info (icon_class, display_text, link, is_external, display_order) VALUES
            ('fab fa-facebook', 'Casa Estela Boutique Hotel & Café', 'https://web.facebook.com/casaestelahotelcafe', 1, 1),
            ('fas fa-envelope', 'casaestelahotelcafe@gmail.com', 'mailto:casaestelahotelcafe@gmail.com', 0, 2),
            ('fas fa-phone', '0908 747 4892', 'tel:+09087474892', 0, 3),
            ('fab fa-twitter', '@casaestelahlcf', '#', 1, 4),
            ('fab fa-instagram', '@casaestelahotelcafe', 'https://www.instagram.com/casaestelahotelcafe', 1, 5)";
            $pdo->exec($insertSql);
            echo "contact_info table created with default data<br>";
        }
    } else {
        echo "PDO database connection variable does not exist<br>";
    }
    
    // Also check mysqli connection for compatibility
    if (isset($con)) {
        echo "MySQLi connection variable exists<br>";
    } else {
        echo "MySQLi connection variable does not exist<br>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}

include('config.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Test the rooms table
$query = "SELECT * FROM rooms";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "Found " . mysqli_num_rows($result) . " rooms<br>";

// Test room_types table
$query = "SELECT * FROM room_types";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "Found " . mysqli_num_rows($result) . " room types<br>";

// Show sample data
echo "<h3>Sample Room Types:</h3>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "Room Type: " . $row['room_type'] . "<br>";
}

mysqli_close($conn);
?>
