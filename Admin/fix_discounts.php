<?php
require_once "db.php";

// Drop the existing table
$drop_table = "DROP TABLE IF EXISTS discount_types";
$con->query($drop_table);

// Create the table with proper structure
$create_table = "CREATE TABLE discount_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

// Insert default discount types
$default_types = [
    ['senior', 10.00, 'Senior Citizen Discount', 1],
    ['pwd', 10.00, 'Person with Disability Discount', 1],
    ['student', 10.00, 'Student Discount', 1]
];

$insert_query = "INSERT INTO discount_types (name, percentage, description, is_active) VALUES (?, ?, ?, ?)";
$stmt = $con->prepare($insert_query);

foreach ($default_types as $type) {
    $stmt->bind_param("sdsi", $type[0], $type[1], $type[2], $type[3]);
    $stmt->execute();
}

// Display current discount types
$select_query = "SELECT * FROM discount_types";
$result = $con->query($select_query);

echo "Current discount types:\n";
while ($row = $result->fetch_assoc()) {
    echo sprintf(
        "Name: %s, Percentage: %.2f%%, Active: %s\n",
        $row['name'],
        $row['percentage'],
        $row['is_active'] ? 'Yes' : 'No'
    );
}
?> 