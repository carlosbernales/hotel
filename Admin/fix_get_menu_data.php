<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing get_menu_data.php</h1>";

// First, let's find the correct db.php file and extract the connection code
$dbFile = "db.php";
if (!file_exists($dbFile)) {
    $dbFile = "../db.php";
}

if (file_exists($dbFile)) {
    echo "<p>Found database connection file: $dbFile</p>";
    
    $dbContents = file_get_contents($dbFile);
    echo "<p>Creating get_menu_data.php using the database connection from $dbFile</p>";
    
    // Create the get_menu_data.php file
    $getMenuDataContent = '<?php
// Include the database connection
require_once "' . $dbFile . '";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "";

switch ($action) {
    case "categories":
        // Get all menu categories
        $sql = "SELECT id, name, display_name FROM menu_categories ORDER BY id";
        $result = mysqli_query($con, $sql);
        
        $categories = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = [
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "display_name" => $row["display_name"]
                ];
            }
        } else {
            // Log the error
            error_log("Error fetching categories: " . mysqli_error($con));
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
        if ($stmt) {
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
        } else {
            // Log the error
            error_log("Error preparing statement: " . mysqli_error($con));
            echo json_encode([]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid action"]);
        break;
}
?>';

    // Write to file
    if (file_put_contents("get_menu_data.php", $getMenuDataContent)) {
        echo "<p style='color: green;'>✅ Successfully created get_menu_data.php</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create get_menu_data.php. Check file permissions.</p>";
    }
    
    // Now test the API directly
    echo "<h2>Testing the API</h2>";
    echo "<p>Attempting to get categories...</p>";
    
    // Use a direct database connection to test
    require_once $dbFile;
    
    // Check if table exists and has data
    $checkCat = mysqli_query($con, "SELECT COUNT(*) as count FROM menu_categories");
    if ($checkCat) {
        $catCount = mysqli_fetch_assoc($checkCat)['count'];
        echo "<p>Number of categories in database: $catCount</p>";
        
        if ($catCount == 0) {
            echo "<p style='color: red;'>⚠️ No menu categories found in the database!</p>";
            echo "<h3>Adding sample categories and menu items</h3>";
            
            // Add categories
            $insertCategories = "INSERT INTO menu_categories (name, display_name) VALUES 
                ('main_dishes', 'Main Dishes'),
                ('appetizers', 'Appetizers'),
                ('desserts', 'Desserts'),
                ('beverages', 'Beverages')";
                
            if (mysqli_query($con, $insertCategories)) {
                echo "<p>✅ Added sample categories</p>";
                
                // Get new category IDs
                $getCategories = mysqli_query($con, "SELECT id, name FROM menu_categories");
                $categories = [];
                while ($row = mysqli_fetch_assoc($getCategories)) {
                    $categories[$row['name']] = $row['id'];
                }
                
                // Add sample items
                if (!empty($categories)) {
                    $insertItems = "INSERT INTO menu_items (category_id, name, description, price, image_path) VALUES ";
                    
                    $items = [];
                    if (isset($categories['main_dishes'])) {
                        $items[] = "({$categories['main_dishes']}, 'Grilled Chicken', 'Tender grilled chicken with herbs', 350.00, 'images/menu/grilled_chicken.jpg')";
                        $items[] = "({$categories['main_dishes']}, 'Pasta Carbonara', 'Creamy pasta with bacon', 300.00, 'images/menu/pasta_carbonara.jpg')";
                    }
                    
                    if (isset($categories['appetizers'])) {
                        $items[] = "({$categories['appetizers']}, 'Calamari', 'Crispy fried calamari rings', 220.00, 'images/menu/calamari.jpg')";
                    }
                    
                    if (isset($categories['desserts'])) {
                        $items[] = "({$categories['desserts']}, 'Cheesecake', 'Creamy New York style cheesecake', 180.00, 'images/menu/cheesecake.jpg')";
                    }
                    
                    if (isset($categories['beverages'])) {
                        $items[] = "({$categories['beverages']}, 'Iced Tea', 'Refreshing iced tea', 90.00, 'images/menu/iced_tea.jpg')";
                    }
                    
                    if (!empty($items)) {
                        $insertItems .= implode(", ", $items);
                        
                        if (mysqli_query($con, $insertItems)) {
                            echo "<p>✅ Added sample menu items</p>";
                        } else {
                            echo "<p style='color: red;'>❌ Error adding sample menu items: " . mysqli_error($con) . "</p>";
                        }
                    }
                }
            } else {
                echo "<p style='color: red;'>❌ Error adding sample categories: " . mysqli_error($con) . "</p>";
            }
        } else {
            echo "<p>✅ Categories exist in the database</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error checking categories: " . mysqli_error($con) . "</p>";
    }
    
    echo "<h2>Adding debug console logs to table_packages.php</h2>";
    
    // Find the loadMenuCategories function and add more debug logging
    $tablePackagesPath = "table_packages.php";
    if (file_exists($tablePackagesPath)) {
        $tpContent = file_get_contents($tablePackagesPath);
        
        // Add debugging to the loadMenuCategories function
        $tpContent = str_replace(
            "function loadMenuCategories() {",
            "function loadMenuCategories() {
        console.log('Starting to load menu categories...');
        // Log current URL to verify path is correct
        console.log('Current URL:', window.location.href);
        console.log('Fetching from:', 'get_menu_data.php?action=categories');",
            $tpContent
        );
        
        // Add error handling to AJAX calls
        $tpContent = str_replace(
            "$.get('get_menu_data.php', { action: 'categories' }, function(categories) {",
            "$.get('get_menu_data.php', { action: 'categories' })
            .done(function(categories) {",
            $tpContent
        );
        
        // Add failure handling
        $tpContent = str_replace(
            "if (categories.length > 0) {
                loadMenuItems(categories[0].id);
            }
        });",
            "if (categories.length > 0) {
                loadMenuItems(categories[0].id);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching categories:', textStatus, errorThrown);
            const container = $('#menuCategories');
            container.html('<div class=\"alert alert-danger\">Error loading menu. Please try again later.</div>');
        });",
            $tpContent
        );
        
        // Save the updated file
        if (file_put_contents($tablePackagesPath, $tpContent)) {
            echo "<p>✅ Added debugging to table_packages.php</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Could not find table_packages.php</p>";
    }
    
    echo "<h2>Fix Complete!</h2>";
    echo "<p>You should now be able to use the Advance Order feature. Return to <a href='table_packages.php'>table_packages.php</a> and try again.</p>";
} else {
    echo "<p style='color: red;'>❌ Could not find database connection file (db.php).</p>";
    echo "<p>Please check your server configuration and try again.</p>";
}
?> 