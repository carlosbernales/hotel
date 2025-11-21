<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check table existence
$tables = ['menu_categories', 'menu_items'];
$tableStatus = [];

foreach ($tables as $table) {
    $checkTable = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    $tableStatus[$table] = mysqli_num_rows($checkTable) > 0;
}

// Check table structure if they exist
$tableStructure = [];
foreach ($tables as $table) {
    if ($tableStatus[$table]) {
        $structure = mysqli_query($con, "DESCRIBE $table");
        $columns = [];
        while ($row = mysqli_fetch_assoc($structure)) {
            $columns[] = $row;
        }
        $tableStructure[$table] = $columns;
    }
}

// Output results
echo "<h2>Database Structure Check</h2>";

// Table existence
echo "<h3>Table Existence:</h3>";
echo "<ul>";
foreach ($tableStatus as $table => $exists) {
    echo "<li>$table: " . ($exists ? "Exists" : "Not Found") . "</li>";
}
echo "</ul>";

// Table structure
echo "<h3>Table Structure:</h3>";
foreach ($tableStructure as $table => $columns) {
    echo "<h4>$table</h4>";
    if (count($columns) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "<td>{$column['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No columns found!</p>";
    }
}

// Create the tables if they don't exist
if (!$tableStatus['menu_categories']) {
    echo "<h3>Creating menu_categories table...</h3>";
    $createCategoriesTable = "
    CREATE TABLE menu_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        display_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($con, $createCategoriesTable)) {
        echo "<p>menu_categories table created successfully!</p>";
        
        // Insert sample categories
        $insertCategories = "
        INSERT INTO menu_categories (name, display_name) VALUES 
        ('main_dishes', 'Main Dishes'),
        ('appetizers', 'Appetizers'),
        ('desserts', 'Desserts'),
        ('beverages', 'Beverages')";
        
        if (mysqli_query($con, $insertCategories)) {
            echo "<p>Sample categories added!</p>";
        } else {
            echo "<p>Error adding sample categories: " . mysqli_error($con) . "</p>";
        }
    } else {
        echo "<p>Error creating menu_categories table: " . mysqli_error($con) . "</p>";
    }
}

if (!$tableStatus['menu_items']) {
    echo "<h3>Creating menu_items table...</h3>";
    $createItemsTable = "
    CREATE TABLE menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES menu_categories(id)
    )";
    
    if (mysqli_query($con, $createItemsTable)) {
        echo "<p>menu_items table created successfully!</p>";
        
        // Get category IDs
        $getCategories = mysqli_query($con, "SELECT id, name FROM menu_categories");
        $categories = [];
        while ($row = mysqli_fetch_assoc($getCategories)) {
            $categories[$row['name']] = $row['id'];
        }
        
        // Insert sample items if categories exist
        if (!empty($categories)) {
            $insertItems = "
            INSERT INTO menu_items (category_id, name, description, price, image_path) VALUES 
            ({$categories['main_dishes']}, 'Grilled Chicken', 'Tender grilled chicken with herbs', 350.00, 'images/menu/grilled_chicken.jpg'),
            ({$categories['main_dishes']}, 'Pasta Carbonara', 'Creamy pasta with bacon', 300.00, 'images/menu/pasta_carbonara.jpg'),
            ({$categories['appetizers']}, 'Calamari', 'Crispy fried calamari rings', 220.00, 'images/menu/calamari.jpg'),
            ({$categories['desserts']}, 'Cheesecake', 'Creamy New York style cheesecake', 180.00, 'images/menu/cheesecake.jpg'),
            ({$categories['beverages']}, 'Iced Tea', 'Refreshing home-brewed iced tea', 90.00, 'images/menu/iced_tea.jpg')";
            
            if (mysqli_query($con, $insertItems)) {
                echo "<p>Sample menu items added!</p>";
            } else {
                echo "<p>Error adding sample menu items: " . mysqli_error($con) . "</p>";
            }
        } else {
            echo "<p>No categories found to add sample items</p>";
        }
    } else {
        echo "<p>Error creating menu_items table: " . mysqli_error($con) . "</p>";
    }
}
?> 