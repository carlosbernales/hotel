<?php
require_once 'db.php';

// Create event_bookings table
$create_event_bookings = "CREATE TABLE IF NOT EXISTS event_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    event_time VARCHAR(50) NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    package_name VARCHAR(100) NOT NULL,
    package_type ENUM('30 PAX', '50 PAX') NOT NULL,
    package_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('Pending', 'Confirmed', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Create event_packages table
$create_event_packages = "CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id VARCHAR(10) NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    package_type ENUM('30 PAX', '50 PAX') NOT NULL,
    menu TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    max_guests INT NOT NULL,
    duration INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute the table creation queries
if (mysqli_query($con, $create_event_bookings)) {
    echo "Event bookings table created successfully<br>";
} else {
    echo "Error creating event bookings table: " . mysqli_error($con) . "<br>";
}

// Add package_price column if it doesn't exist
$check_column = "SHOW COLUMNS FROM event_bookings LIKE 'package_price'";
$result = mysqli_query($con, $check_column);
if (mysqli_num_rows($result) == 0) {
    $add_column = "ALTER TABLE event_bookings ADD COLUMN package_price DECIMAL(10,2) NOT NULL DEFAULT 0.00";
    if (mysqli_query($con, $add_column)) {
        echo "Package price column added successfully<br>";
    } else {
        echo "Error adding package price column: " . mysqli_error($con) . "<br>";
    }
}

if (mysqli_query($con, $create_event_packages)) {
    echo "Event packages table created successfully<br>";
} else {
    echo "Error creating event packages table: " . mysqli_error($con) . "<br>";
}

// Insert default package data
$packages = [
    // 30 PAX Packages
    [
        'id' => '30A',
        'name' => 'Package A',
        'type' => '30 PAX',
        'menu' => '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks',
        'price' => 28000,
        'max_guests' => 30,
        'duration' => 4,
        'notes' => '*This package is only available up to 2:00pm only'
    ],
    [
        'id' => '30B',
        'name' => 'Package B',
        'type' => '30 PAX',
        'menu' => '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks',
        'price' => 33000,
        'max_guests' => 30,
        'duration' => 4,
        'notes' => NULL
    ],
    [
        'id' => '30C',
        'name' => 'Package C',
        'type' => '30 PAX',
        'menu' => '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station**, Salad Bar, Rice, 2 Desserts, Drinks',
        'price' => 46000,
        'max_guests' => 30,
        'duration' => 4,
        'notes' => '**Assumes 3,000g (100g per person) of Wagyu steak will be served'
    ],
    // 50 PAX Packages
    [
        'id' => '50A',
        'name' => 'Package A',
        'type' => '50 PAX',
        'menu' => '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks',
        'price' => 47500,
        'max_guests' => 50,
        'duration' => 5,
        'notes' => '*This package is only available up to 2:00pm only'
    ],
    [
        'id' => '50B',
        'name' => 'Package B',
        'type' => '50 PAX',
        'menu' => '2 Appetizers, 2 Pasta, 3 Mains, Salad Bar, Rice, 1 Dessert, Drinks',
        'price' => 55000,
        'max_guests' => 50,
        'duration' => 5,
        'notes' => NULL
    ],
    [
        'id' => '50C',
        'name' => 'Package C',
        'type' => '50 PAX',
        'menu' => '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station**, Salad Bar, Rice, 2 Desserts, Drinks',
        'price' => 76800,
        'max_guests' => 50,
        'duration' => 5,
        'notes' => '**Assumes 5,000g (100g per person) of Wagyu steak will be served'
    ]
];

// Insert package data
$insert_package = "INSERT INTO event_packages (package_id, package_name, package_type, menu, price, max_guests, duration, notes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $insert_package);

foreach ($packages as $package) {
    mysqli_stmt_bind_param($stmt, 'ssssdiis', 
        $package['id'],
        $package['name'],
        $package['type'],
        $package['menu'],
        $package['price'],
        $package['max_guests'],
        $package['duration'],
        $package['notes']
    );
    mysqli_stmt_execute($stmt);
}

echo "Default packages inserted successfully";
?> 