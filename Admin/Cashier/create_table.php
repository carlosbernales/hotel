<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotelms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create table_number table
$sql1 = "CREATE TABLE IF NOT EXISTS table_number (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
    occupied_at TIMESTAMP NULL DEFAULT NULL,
    order_id INT NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Execute the query
if ($conn->query($sql1) === TRUE) {
    echo "Table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// SQL to insert initial data (tables 1-10) if they don't exist
$sql2 = "INSERT INTO table_number (table_number, status) 
         SELECT * FROM (
             SELECT 1, 'available' UNION SELECT 2, 'available' UNION 
             SELECT 3, 'available' UNION SELECT 4, 'available' UNION 
             SELECT 5, 'available' UNION SELECT 6, 'available' UNION 
             SELECT 7, 'available' UNION SELECT 8, 'available' UNION 
             SELECT 9, 'available' UNION SELECT 10, 'available'
         ) AS temp
         WHERE NOT EXISTS (
             SELECT table_number FROM table_number WHERE table_number = temp.table_number
         )";

// Execute the query
if ($conn->query($sql2) === TRUE) {
    echo "Initial data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $conn->error . "<br>";
}

$conn->close();
?>
