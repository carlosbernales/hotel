<?php
// Include database connection
require_once 'db.php';

// SQL to create the advance_table_orders table
$sql = file_get_contents('create_advance_order_table.sql');

// Execute the SQL
if ($con->multi_query($sql)) {
    echo "Advance table orders table created successfully";
} else {
    echo "Error creating table: " . $con->error;
}
?> 