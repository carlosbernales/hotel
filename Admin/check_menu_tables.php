<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check menu_categories table
$categoriesSql = "SELECT COUNT(*) as count FROM menu_categories";
$categoriesResult = mysqli_query($con, $categoriesSql);
$categoriesCount = mysqli_fetch_assoc($categoriesResult)['count'];

// Check menu_items table
$itemsSql = "SELECT COUNT(*) as count FROM menu_items";
$itemsResult = mysqli_query($con, $itemsSql);
$itemsCount = mysqli_fetch_assoc($itemsResult)['count'];

// Get sample data
$sampleCategoriesSql = "SELECT * FROM menu_categories LIMIT 3";
$sampleCategoriesResult = mysqli_query($con, $sampleCategoriesSql);

$sampleItemsSql = "SELECT * FROM menu_items LIMIT 3";
$sampleItemsResult = mysqli_query($con, $sampleItemsSql);

echo "<h2>Database Check Results</h2>";
echo "<p>Categories Count: " . $categoriesCount . "</p>";
echo "<p>Menu Items Count: " . $itemsCount . "</p>";

echo "<h3>Sample Categories:</h3>";
if (mysqli_num_rows($sampleCategoriesResult) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($sampleCategoriesResult)) {
        echo "<li>ID: " . $row['id'] . ", Name: " . $row['name'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No categories found!</p>";
}

echo "<h3>Sample Menu Items:</h3>";
if (mysqli_num_rows($sampleItemsResult) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($sampleItemsResult)) {
        echo "<li>ID: " . $row['id'] . ", Name: " . $row['name'] . ", Category ID: " . $row['category_id'] . ", Price: " . $row['price'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No menu items found!</p>";
}

// Check for console errors by implementing a test function
echo "<script>
function testMenuDataFetch() {
    console.log('Testing menu data fetch...');
    fetch('get_menu_data.php?action=categories')
        .then(response => response.json())
        .then(data => {
            console.log('Categories data:', data);
            if(data.length > 0) {
                fetch('get_menu_data.php?action=items&category_id=' + data[0].id)
                    .then(response => response.json())
                    .then(items => {
                        console.log('Items data:', items);
                    })
                    .catch(error => console.error('Error fetching items:', error));
            }
        })
        .catch(error => console.error('Error fetching categories:', error));
}
testMenuDataFetch();
</script>";

require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if menu_items table exists
$check_table_sql = "SHOW TABLES LIKE 'menu_items'";
$table_result = mysqli_query($con, $check_table_sql);

if (mysqli_num_rows($table_result) == 0) {
    // Table doesn't exist, create it
    echo "<p>Creating menu_items table...</p>";
    
    $create_table_sql = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $create_table_sql)) {
        echo "<p>menu_items table created successfully!</p>";
    } else {
        echo "<p>Error creating menu_items table: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>menu_items table already exists.</p>";
    
    // Check table structure
    $table_structure = mysqli_query($con, "DESCRIBE menu_items");
    echo "<p><strong>Current table structure:</strong></p>";
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($table_structure)) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "</li>";
    }
    echo "</ul>";
}

// Check if menu_items_addons table exists
$check_addons_table_sql = "SHOW TABLES LIKE 'menu_items_addons'";
$addons_table_result = mysqli_query($con, $check_addons_table_sql);

if (mysqli_num_rows($addons_table_result) == 0) {
    // Table doesn't exist, create it
    echo "<p>Creating menu_items_addons table...</p>";
    
    $create_addons_table_sql = "CREATE TABLE IF NOT EXISTS menu_items_addons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_item_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $create_addons_table_sql)) {
        echo "<p>menu_items_addons table created successfully!</p>";
    } else {
        echo "<p>Error creating menu_items_addons table: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>menu_items_addons table already exists.</p>";
    
    // Check table structure
    $addons_table_structure = mysqli_query($con, "DESCRIBE menu_items_addons");
    echo "<p><strong>Current addons table structure:</strong></p>";
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($addons_table_structure)) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "</li>";
    }
    echo "</ul>";
}

echo "<p><a href='cafe_management.php'>Return to Cafe Management</a></p>";
?> 