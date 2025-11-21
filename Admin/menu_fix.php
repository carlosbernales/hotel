<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Menu System Fix Script</h1>";

// Check if tables exist
$checkCategoryTable = mysqli_query($con, "SHOW TABLES LIKE 'menu_categories'");
$categoryTableExists = mysqli_num_rows($checkCategoryTable) > 0;

$checkItemsTable = mysqli_query($con, "SHOW TABLES LIKE 'menu_items'");
$itemsTableExists = mysqli_num_rows($checkItemsTable) > 0;

echo "<h2>Table Status:</h2>";
echo "<p>menu_categories table exists: " . ($categoryTableExists ? "Yes" : "No") . "</p>";
echo "<p>menu_items table exists: " . ($itemsTableExists ? "Yes" : "No") . "</p>";

// Create tables if they don't exist
if (!$categoryTableExists) {
    echo "<h3>Creating menu_categories table...</h3>";
    $createCategoryTable = "CREATE TABLE menu_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        display_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($con, $createCategoryTable)) {
        echo "<p>menu_categories table created successfully!</p>";
        
        // Insert sample categories
        $insertCategories = "INSERT INTO menu_categories (name, display_name) VALUES 
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

// Create menu items table if it doesn't exist
if (!$itemsTableExists) {
    echo "<h3>Creating menu_items table...</h3>";
    $createItemsTable = "CREATE TABLE menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
            $insertItems = "INSERT INTO menu_items (category_id, name, description, price, image_path) VALUES ";
            
            $items = [];
            if (isset($categories['main_dishes'])) {
                $items[] = "({$categories['main_dishes']}, 'Grilled Chicken', 'Tender grilled chicken with herbs', 350.00, 'images/menu/grilled_chicken.jpg')";
                $items[] = "({$categories['main_dishes']}, 'Pasta Carbonara', 'Creamy pasta with bacon', 300.00, 'images/menu/pasta_carbonara.jpg')";
                $items[] = "({$categories['main_dishes']}, 'Beef Steak', 'Premium beef steak with sauce', 450.00, 'images/menu/beef_steak.jpg')";
            }
            
            if (isset($categories['appetizers'])) {
                $items[] = "({$categories['appetizers']}, 'Calamari', 'Crispy fried calamari rings', 220.00, 'images/menu/calamari.jpg')";
                $items[] = "({$categories['appetizers']}, 'Nachos', 'Crunchy nachos with cheese and salsa', 180.00, 'images/menu/nachos.jpg')";
            }
            
            if (isset($categories['desserts'])) {
                $items[] = "({$categories['desserts']}, 'Cheesecake', 'Creamy New York style cheesecake', 180.00, 'images/menu/cheesecake.jpg')";
                $items[] = "({$categories['desserts']}, 'Chocolate Cake', 'Rich chocolate cake with icing', 160.00, 'images/menu/chocolate_cake.jpg')";
            }
            
            if (isset($categories['beverages'])) {
                $items[] = "({$categories['beverages']}, 'Iced Tea', 'Refreshing home-brewed iced tea', 90.00, 'images/menu/iced_tea.jpg')";
                $items[] = "({$categories['beverages']}, 'Fresh Juice', 'Freshly squeezed fruit juice', 120.00, 'images/menu/fresh_juice.jpg')";
            }
            
            if (!empty($items)) {
                $insertItems .= implode(", ", $items);
                
                if (mysqli_query($con, $insertItems)) {
                    echo "<p>Sample menu items added!</p>";
                } else {
                    echo "<p>Error adding sample menu items: " . mysqli_error($con) . "</p>";
                }
            }
        } else {
            echo "<p>No categories found to add sample items</p>";
        }
    } else {
        echo "<p>Error creating menu_items table: " . mysqli_error($con) . "</p>";
    }
}

// Verify get_menu_data.php exists and is working
echo "<h2>Testing API Endpoints:</h2>";

// Test the get_menu_data.php script
$categoriesUrl = "get_menu_data.php?action=categories";
echo "<p>Testing categories endpoint: $categoriesUrl</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $categoriesUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>Response code: $httpCode</p>";
echo "<pre>Response data: " . htmlspecialchars($response) . "</pre>";

// Create get_menu_data.php if it doesn't exist or is not working
if ($httpCode != 200 || empty($response) || $response == "[]") {
    echo "<h3>Creating/Updating get_menu_data.php...</h3>";
    
    $getMenuDataContent = '<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

header("Content-Type: application/json");

$action = $_GET["action"] ?? "";

switch ($action) {
    case "categories":
        // Get all menu categories
        $sql = "SELECT id, name, display_name FROM menu_categories ORDER BY id";
        $result = mysqli_query($con, $sql);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "display_name" => $row["display_name"]
            ];
        }
        
        echo json_encode($categories);
        break;

    case "items":
        $categoryId = $_GET["category_id"] ?? 0;
        
        // Get menu items for the selected category
        $sql = "SELECT id, category_id, name, description, price, image_path 
               FROM menu_items 
               WHERE category_id = ?
               ORDER BY name";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "description" => $row["description"],
                "price" => $row["price"],
                "image_path" => $row["image_path"]
            ];
        }
        
        echo json_encode($items);
        break;

    default:
        echo json_encode(["error" => "Invalid action"]);
        break;
}
?>';

    // Save the file
    if (file_put_contents("get_menu_data.php", $getMenuDataContent)) {
        echo "<p>get_menu_data.php created/updated successfully!</p>";
    } else {
        echo "<p>Error creating/updating get_menu_data.php. Please check file permissions.</p>";
    }
}

echo "<h2>Fix Complete!</h2>";
echo "<p>You can now return to the table_packages.php page and try the Advance Order feature again.</p>";
?> 