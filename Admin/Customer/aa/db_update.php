<?php
require_once 'db_con.php';

// Add is_active column to menu_items table
$checkColumnQuery = "SHOW COLUMNS FROM menu_items LIKE 'is_active'";
$columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;

if (!$columnExists) {
    $addColumnQuery = "ALTER TABLE menu_items ADD COLUMN is_active TINYINT(1) DEFAULT 1";
    if (mysqli_query($con, $addColumnQuery)) {
        echo "Added is_active column to menu_items table.<br>";
        
        // Set all existing items to active
        $updateQuery = "UPDATE menu_items SET is_active = 1";
        if (mysqli_query($con, $updateQuery)) {
            echo "Set all existing menu items to active.<br>";
        } else {
            echo "Error updating menu items: " . mysqli_error($con) . "<br>";
        }
    } else {
        echo "Error adding is_active column: " . mysqli_error($con) . "<br>";
    }
}

// Add order_type column to orders table
$checkColumnQuery = "SHOW COLUMNS FROM orders LIKE 'order_type'";
$columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;

if (!$columnExists) {
    $addColumnQuery = "ALTER TABLE orders ADD COLUMN order_type VARCHAR(50) DEFAULT 'regular'";
    if (mysqli_query($con, $addColumnQuery)) {
        echo "Added order_type column to orders table.<br>";
    } else {
        echo "Error adding order_type column: " . mysqli_error($con) . "<br>";
    }
}

// Add table_id column to orders table to link with reservations
$checkColumnQuery = "SHOW COLUMNS FROM orders LIKE 'table_id'";
$columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;

if (!$columnExists) {
    $addColumnQuery = "ALTER TABLE orders ADD COLUMN table_id INT DEFAULT NULL";
    if (mysqli_query($con, $addColumnQuery)) {
        echo "Added table_id column to orders table.<br>";
    } else {
        echo "Error adding table_id column: " . mysqli_error($con) . "<br>";
    }
}

// Update the menu items query in cafes.php
echo "Please update your cafes.php file to use this menu items query:<br>";
echo "<pre>
\$items_query = \"SELECT mi.*, mc.name as category_name, mc.display_name as category_display_name 
                FROM menu_items mi 
                JOIN menu_categories mc ON mi.category_id = mc.id 
                WHERE mi.is_active = 1
                ORDER BY mi.category_id, mi.name\";
</pre><br>";

// Update the table bookings query
echo "Please update your table.php file to use this reservations query:<br>";
echo "<pre>
\$tables_query = \"SELECT * FROM reservations 
                 WHERE status = 'available' 
                 AND reservation_date >= CURDATE()
                 ORDER BY reservation_date, time_start\";
</pre><br>";

// Check if the orders table exists
$checkTableQuery = "SHOW TABLES LIKE 'orders'";
$tableExists = mysqli_query($con, $checkTableQuery)->num_rows > 0;

if (!$tableExists) {
    // Create the orders table with all required columns
    $createTableQuery = "CREATE TABLE orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        extra_fee DECIMAL(10,2) DEFAULT 0.00,
        final_total DECIMAL(10,2) DEFAULT 0.00,
        payment_method VARCHAR(50) NOT NULL,
        payment_status VARCHAR(50) DEFAULT 'pending',
        payment_reference VARCHAR(100) NULL,
        payment_proof VARCHAR(255) NULL,
        order_type VARCHAR(50) DEFAULT 'regular',
        table_id INT DEFAULT NULL,
        order_status VARCHAR(50) DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $createTableQuery)) {
        die("Error creating orders table: " . mysqli_error($con));
    }
    echo "Orders table created successfully.<br>";
} else {
    // Add missing columns one by one if the table exists
    
    // Check for payment_status column
    $checkColumnQuery = "SHOW COLUMNS FROM orders LIKE 'payment_status'";
    $columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;
    
    if (!$columnExists) {
        $addColumnQuery = "ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending'";
        if (mysqli_query($con, $addColumnQuery)) {
            echo "Added payment_status column to orders table.<br>";
        } else {
            echo "Error adding payment_status column: " . mysqli_error($con) . "<br>";
        }
    }
    
    // Check for payment_reference column
    $checkColumnQuery = "SHOW COLUMNS FROM orders LIKE 'payment_reference'";
    $columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;
    
    if (!$columnExists) {
        $addColumnQuery = "ALTER TABLE orders ADD COLUMN payment_reference VARCHAR(100) NULL";
        if (mysqli_query($con, $addColumnQuery)) {
            echo "Added payment_reference column to orders table.<br>";
        } else {
            echo "Error adding payment_reference column: " . mysqli_error($con) . "<br>";
        }
    }
    
    // Check for payment_proof column
    $checkColumnQuery = "SHOW COLUMNS FROM orders LIKE 'payment_proof'";
    $columnExists = mysqli_query($con, $checkColumnQuery)->num_rows > 0;
    
    if (!$columnExists) {
        $addColumnQuery = "ALTER TABLE orders ADD COLUMN payment_proof VARCHAR(255) NULL";
        if (mysqli_query($con, $addColumnQuery)) {
            echo "Added payment_proof column to orders table.<br>";
        } else {
            echo "Error adding payment_proof column: " . mysqli_error($con) . "<br>";
        }
    }
    
    // Other existing column checks...
}

// Check if the order_items table exists
$checkTableQuery = "SHOW TABLES LIKE 'order_items'";
$tableExists = mysqli_query($con, $checkTableQuery)->num_rows > 0;

if (!$tableExists) {
    // Create order_items table
    $createTableQuery = "CREATE TABLE order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        item_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        addons TEXT NULL
    )";
    
    if (!mysqli_query($con, $createTableQuery)) {
        die("Error creating order_items table: " . mysqli_error($con));
    }
    echo "Order_items table created successfully.<br>";
}

echo "Database structure has been updated. <a href='cafes.php'>Go back to ordering page</a><br>";
?> 