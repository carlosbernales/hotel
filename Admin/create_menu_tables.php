<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'menu_setup_errors.log');

try {
    // Create menu_categories table
    $sql_categories = "CREATE TABLE IF NOT EXISTS menu_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        display_name VARCHAR(100),
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $sql_categories)) {
        throw new Exception("Error creating menu_categories table: " . mysqli_error($con));
    }
    
    // Create menu_items table
    $sql_items = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES menu_categories(id)
    )";
    
    if (!mysqli_query($con, $sql_items)) {
        throw new Exception("Error creating menu_items table: " . mysqli_error($con));
    }
    
    // Create menu_item_addons table
    $sql_addons = "CREATE TABLE IF NOT EXISTS menu_item_addons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_item_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    )";
    
    if (!mysqli_query($con, $sql_addons)) {
        throw new Exception("Error creating menu_item_addons table: " . mysqli_error($con));
    }
    
    // Check if menu_categories is empty
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM menu_categories");
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        // Insert default categories
        $insert_categories = "INSERT INTO menu_categories (name, display_name) VALUES 
            ('main_course', 'Main Course'),
            ('appetizers', 'Appetizers'),
            ('desserts', 'Desserts'),
            ('beverages', 'Beverages')";
        
        if (!mysqli_query($con, $insert_categories)) {
            throw new Exception("Error inserting default categories: " . mysqli_error($con));
        }
        
        // Insert some sample menu items
        $insert_items = "INSERT INTO menu_items (category_id, name, description, price) VALUES 
            (1, 'Grilled Chicken', 'Tender grilled chicken with herbs', 250.00),
            (1, 'Beef Steak', 'Premium beef steak with mushroom sauce', 350.00),
            (2, 'Caesar Salad', 'Fresh lettuce with caesar dressing', 180.00),
            (3, 'Chocolate Cake', 'Rich chocolate cake with ganache', 150.00),
            (4, 'Fresh Juice', 'Selection of fresh fruit juices', 80.00)";
        
        if (!mysqli_query($con, $insert_items)) {
            throw new Exception("Error inserting sample menu items: " . mysqli_error($con));
        }
    }
    
    echo "Menu tables created successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    error_log("Menu setup error: " . $e->getMessage());
}
?> 