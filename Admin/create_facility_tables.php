<?php
require_once 'db.php';

// Create facility_categories table
$sql_categories = "CREATE TABLE IF NOT EXISTS facility_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($con->query($sql_categories) === TRUE) {
    echo "Table 'facility_categories' created successfully<br>";
} else {
    echo "Error creating facility_categories table: " . $con->error . "<br>";
}

// Create facilities table
$sql_facilities = "CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES facility_categories(id)
)";

if ($con->query($sql_facilities) === TRUE) {
    echo "Table 'facilities' created successfully<br>";
} else {
    echo "Error creating facilities table: " . $con->error . "<br>";
}

// Insert default categories if the table is empty
$check_categories = "SELECT COUNT(*) as count FROM facility_categories";
$result = $con->query($check_categories);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $default_categories = "INSERT INTO facility_categories (name, display_order) VALUES 
        ('Parking', 1),
        ('Safety & Security', 2),
        ('Food & Drink', 3),
        ('Reception Services', 4),
        ('Languages Spoken', 5),
        ('Internet', 6),
        ('Bathroom', 7)";
    
    if ($con->query($default_categories) === TRUE) {
        echo "Default categories inserted successfully<br>";
    } else {
        echo "Error inserting default categories: " . $con->error . "<br>";
    }
}

echo "Database setup completed!";
?> 