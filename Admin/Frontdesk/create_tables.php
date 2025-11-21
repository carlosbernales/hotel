<?php
require_once 'db.php';

// Create table_reservations table
$sql_table_reservations = "CREATE TABLE IF NOT EXISTS table_reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    guest_count INT NOT NULL,
    table_type VARCHAR(50) NOT NULL,
    reservation_datetime DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Create reservation_orders table
$sql_reservation_orders = "CREATE TABLE IF NOT EXISTS reservation_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES table_reservations(reservation_id) ON DELETE CASCADE
)";

// Create menu_categories table if it doesn't exist
$sql_menu_categories = "CREATE TABLE IF NOT EXISTS menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($con->query($sql_menu_categories)) {
    echo "Table 'menu_categories' created successfully<br>";
} else {
    echo "Error creating menu_categories table: " . $con->error . "<br>";
}

// Insert default category if none exists
$check_categories = "SELECT COUNT(*) as count FROM menu_categories";
$result = $con->query($check_categories);
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $default_category = "INSERT INTO menu_categories (name) VALUES ('General')";
    if ($con->query($default_category)) {
        echo "Default category created successfully<br>";
    } else {
        echo "Error creating default category: " . $con->error . "<br>";
    }
}

// Create menu_items table if it doesn't exist
$sql_menu_items = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(id)
)";

if ($con->query($sql_menu_items)) {
    echo "Table 'menu_items' created successfully<br>";
} else {
    echo "Error creating menu_items table: " . $con->error . "<br>";
}

// Execute the queries
try {
    if ($con->query($sql_table_reservations)) {
        echo "Table 'table_reservations' created successfully<br>";
    }
    
    if ($con->query($sql_reservation_orders)) {
        echo "Table 'reservation_orders' created successfully<br>";
    }

    // Insert some sample menu items if the table is empty
    $check_menu = "SELECT COUNT(*) as count FROM menu_items";
    $result = $con->query($check_menu);
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        $sample_menu = "INSERT INTO menu_items (name, price, category) VALUES 
            ('Chicken Adobo', 150.00, 'Main Course'),
            ('Sinigang na Baboy', 180.00, 'Main Course'),
            ('Pancit Canton', 120.00, 'Noodles'),
            ('Leche Flan', 80.00, 'Dessert'),
            ('Halo-Halo', 90.00, 'Dessert'),
            ('Iced Tea', 45.00, 'Beverages'),
            ('Coke', 40.00, 'Beverages')";
            
        if ($con->query($sample_menu)) {
            echo "Sample menu items added successfully<br>";
        }
    }

    // Update any existing menu items to use the default category
    $update_items_query = "UPDATE menu_items mi 
        LEFT JOIN menu_categories mc ON mi.category_id = mc.id 
        SET mi.category_id = (SELECT id FROM menu_categories WHERE name = 'General' LIMIT 1) 
        WHERE mc.id IS NULL OR mi.category_id IS NULL";
    mysqli_query($con, $update_items_query);

    echo "All tables created successfully!";
    
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 