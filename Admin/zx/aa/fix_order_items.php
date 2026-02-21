<?php
require_once 'db_con.php';

// Check if the order_items table exists
$checkTableQuery = "SHOW TABLES LIKE 'order_items'";
$tableExists = mysqli_query($con, $checkTableQuery)->num_rows > 0;

if (!$tableExists) {
    // Create order_items table with correct structure
    $createTableQuery = "CREATE TABLE order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        item_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        addons TEXT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $createTableQuery)) {
        echo "Created order_items table successfully.<br>";
    } else {
        echo "Error creating order_items table: " . mysqli_error($con) . "<br>";
    }
} else {
    // Check and update existing table structure
    echo "Checking order_items table structure...<br>";
    
    // Get current columns
    $columnsQuery = "SHOW COLUMNS FROM order_items";
    $columnsResult = mysqli_query($con, $columnsQuery);
    $columns = [];
    
    while ($column = mysqli_fetch_assoc($columnsResult)) {
        $columns[$column['Field']] = $column;
    }
    
    // Check required columns
    $requiredColumns = [
        'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
        'order_id' => 'INT NOT NULL',
        'item_name' => 'VARCHAR(255) NOT NULL',
        'quantity' => 'INT NOT NULL',
        'price' => 'DECIMAL(10,2) NOT NULL',
        'addons' => 'TEXT NULL'
    ];
    
    foreach ($requiredColumns as $columnName => $columnType) {
        if (!isset($columns[$columnName])) {
            $addColumnQuery = "ALTER TABLE order_items ADD COLUMN $columnName $columnType";
            if (mysqli_query($con, $addColumnQuery)) {
                echo "Added missing column $columnName to order_items table.<br>";
            } else {
                echo "Error adding column $columnName: " . mysqli_error($con) . "<br>";
            }
        }
    }
    
    // Check foreign key constraint
    $fkQuery = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'order_items' 
                AND REFERENCED_TABLE_NAME = 'orders'";
    $fkResult = mysqli_query($con, $fkQuery);
    
    if (mysqli_num_rows($fkResult) == 0) {
        // Add foreign key if it doesn't exist
        $addFkQuery = "ALTER TABLE order_items 
                       ADD CONSTRAINT fk_order_items_order_id 
                       FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE";
        
        if (mysqli_query($con, $addFkQuery)) {
            echo "Added foreign key constraint to order_items table.<br>";
        } else {
            echo "Error adding foreign key constraint: " . mysqli_error($con) . "<br>";
        }
    }
}

echo "Order items table structure has been verified and updated.<br>";
echo "<a href='cafes.php'>Return to ordering page</a>";
?> 