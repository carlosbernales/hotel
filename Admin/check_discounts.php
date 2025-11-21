<?php
require_once "db.php";

// Create discount_types table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS discount_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

// Insert default discount types if they don't exist
$check_types = "SELECT COUNT(*) as count FROM discount_types";
$result = $con->query($check_types);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
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
    echo "Default discount types added successfully!\n";
} else {
    // Update existing records to ensure they are active
    $update_query = "UPDATE discount_types SET is_active = 1 WHERE name IN ('senior', 'pwd', 'student')";
    $con->query($update_query);
    echo "Existing discount types updated!\n";
}

// Display current discount types
$select_query = "SELECT * FROM discount_types";
$result = $con->query($select_query);

echo "\nCurrent discount types:\n";
while ($row = $result->fetch_assoc()) {
    echo sprintf(
        "Name: %s, Percentage: %.2f%%, Active: %s\n",
        $row['name'],
        $row['percentage'],
        $row['is_active'] ? 'Yes' : 'No'
    );
}
?> 