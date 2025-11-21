<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Check if we have a connection
if (!isset($con) || !$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully.<br>";

// Check if the order_items table exists
$checkTableQuery = "SHOW TABLES LIKE 'order_items'";
$tableExists = mysqli_query($con, $checkTableQuery);

if (!$tableExists) {
    die("Error checking if table exists: " . mysqli_error($con));
}

if (mysqli_num_rows($tableExists) > 0) {
    echo "The order_items table exists.<br>";
    
    // Check the structure of the table
    $describeTable = "DESCRIBE order_items";
    $result = mysqli_query($con, $describeTable);
    
    if (!$result) {
        die("Error describing table: " . mysqli_error($con));
    }
    
    echo "<h3>order_items table structure:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check if price column exists
    $priceColumnExists = false;
    
    // Reset the result pointer
    mysqli_data_seek($result, 0);
    
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] == 'price') {
            $priceColumnExists = true;
            echo "<p>The 'price' column exists in the order_items table.</p>";
            break;
        }
    }
    
    if (!$priceColumnExists) {
        echo "<p>Warning: The 'price' column does not exist in the order_items table.</p>";
    }
    
} else {
    echo "The order_items table does not exist.<br>";
    
    // Create the table if it doesn't exist
    $createTableQuery = "CREATE TABLE order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        item_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $createTableQuery)) {
        echo "Created order_items table successfully.<br>";
    } else {
        echo "Error creating order_items table: " . mysqli_error($con) . "<br>";
    }
}

// Display sample data if the table exists
$query = "SELECT * FROM order_items LIMIT 10";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h3>Sample data from order_items:</h3>";
    echo "<table border='1'>";
    
    // Get column names
    $firstRow = mysqli_fetch_assoc($result);
    echo "<tr>";
    foreach (array_keys($firstRow) as $column) {
        echo "<th>$column</th>";
    }
    echo "</tr>";
    
    // Reset pointer and display all data
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No data found in the order_items table or error: " . mysqli_error($con);
}

// Close the connection
mysqli_close($con);
?> 