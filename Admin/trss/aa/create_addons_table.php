<?php
require_once 'db_con.php';

$sql = "CREATE TABLE IF NOT EXISTS menu_item_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
)";

if ($con->query($sql) === TRUE) {
    echo "Menu item add-ons table created successfully";
} else {
    echo "Error creating table: " . $con->error;
} 