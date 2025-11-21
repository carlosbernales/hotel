<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Menu Items Issue</h1>";

// Find the database connection file
$dbFile = "db.php";
if (!file_exists($dbFile)) {
    $dbFile = "../db.php";
}

if (file_exists($dbFile)) {
    // Connect to database
    require_once $dbFile;
    
    // First check if we can get categories
    echo "<h2>Checking categories in database</h2>";
    $catQuery = "SELECT * FROM menu_categories LIMIT 5";
    $catResult = mysqli_query($con, $catQuery);
    
    if ($catResult && mysqli_num_rows($catResult) > 0) {
        echo "<p>✅ Categories found:</p>";
        echo "<ul>";
        while ($cat = mysqli_fetch_assoc($catResult)) {
            echo "<li>ID: {$cat['id']}, Name: {$cat['name']}, Display Name: {$cat['display_name']}</li>";
        }
        echo "</ul>";
        
        // Get first category ID for testing
        mysqli_data_seek($catResult, 0);
        $firstCat = mysqli_fetch_assoc($catResult);
        $firstCatId = $firstCat['id'];
        
        // Check if we can get menu items for this category
        echo "<h2>Checking menu items for category ID {$firstCatId}</h2>";
        
        // First try direct SQL
        $itemsQuery = "SELECT * FROM menu_items WHERE category_id = {$firstCatId} LIMIT 5";
        $itemsResult = mysqli_query($con, $itemsQuery);
        
        if ($itemsResult && mysqli_num_rows($itemsResult) > 0) {
            echo "<p>✅ Menu items found for category {$firstCatId}:</p>";
            echo "<ul>";
            while ($item = mysqli_fetch_assoc($itemsResult)) {
                echo "<li>ID: {$item['id']}, Name: {$item['name']}, Price: {$item['price']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ No menu items found for category {$firstCatId}</p>";
            echo "<p>Error: " . mysqli_error($con) . "</p>";
            
            // Try to add some menu items for this category
            echo "<h3>Adding sample menu items for category {$firstCatId}</h3>";
            
            $insertItems = "INSERT INTO menu_items (category_id, name, description, price, image_path) VALUES 
                ({$firstCatId}, 'Sample Item 1', 'Description for sample item 1', 250.00, 'images/menu/sample1.jpg'),
                ({$firstCatId}, 'Sample Item 2', 'Description for sample item 2', 180.00, 'images/menu/sample2.jpg'),
                ({$firstCatId}, 'Sample Item 3', 'Description for sample item 3', 300.00, 'images/menu/sample3.jpg')";
                
            if (mysqli_query($con, $insertItems)) {
                echo "<p>✅ Added sample menu items for category {$firstCatId}</p>";
            } else {
                echo "<p style='color: red;'>❌ Error adding sample menu items: " . mysqli_error($con) . "</p>";
            }
        }
        
        // Check if the get_menu_data.php API works for items
        echo "<h2>Testing the API endpoint for menu items</h2>";
        echo "<p>Attempting to call get_menu_data.php?action=items&category_id={$firstCatId}</p>";
        
        $apiUrl = "get_menu_data.php?action=items&category_id={$firstCatId}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "<p>Response code: {$httpCode}</p>";
        echo "<pre>Response: " . htmlspecialchars($response) . "</pre>";
        
        // Fix the loadMenuItems function in table_packages.php
        echo "<h2>Updating loadMenuItems function in table_packages.php</h2>";
        
        $tablePackagesPath = "table_packages.php";
        if (file_exists($tablePackagesPath)) {
            $tpContent = file_get_contents($tablePackagesPath);
            
            // Add debugging to loadMenuItems function
            $tpContent = str_replace(
                "function loadMenuItems(categoryId) {",
                "function loadMenuItems(categoryId) {
        console.log('Loading menu items for category:', categoryId);
        console.log('Fetching from:', 'get_menu_data.php?action=items&category_id=' + categoryId);",
                $tpContent
            );
            
            // Add error handling to the items AJAX call
            $tpContent = str_replace(
                "$.get('get_menu_data.php', { action: 'items', category_id: categoryId }, function(items) {",
                "$.get('get_menu_data.php', { action: 'items', category_id: categoryId })
            .done(function(items) {",
                $tpContent
            );
            
            // Add failure handling and better empty state
            $tpContent = str_replace(
                "items.forEach(item => {
                container.append(",
                "if (items && items.length > 0) {
                items.forEach(item => {
                container.append(",
                $tpContent
            );
            
            $tpContent = str_replace(
                "});
        });",
                "});
            } else {
                container.append(`
                    <div class=\"col-12 text-center p-3\">
                        <div class=\"alert alert-info\">No menu items found for this category</div>
                    </div>
                `);
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching menu items:', textStatus, errorThrown);
            const container = $('#menuItems');
            container.html('<div class=\"alert alert-danger\">Error loading menu items. Please try again later.</div>');
        });",
                $tpContent
            );
            
            // Fix any additional issues with the get_menu_data.php file
            echo "<h2>Updating get_menu_data.php with improved error handling</h2>";
            
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
        $categoryId = isset($_GET["category_id"]) ? (int)$_GET["category_id"] : 0;
        
        // Log the incoming request for debugging
        error_log("Loading menu items for category ID: " . $categoryId);
        
        // Get menu items for the selected category
        $sql = "SELECT id, category_id, name, description, price, image_path 
               FROM menu_items 
               WHERE category_id = ?
               ORDER BY name";
        
        $stmt = mysqli_prepare($con, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $categoryId);
            $executed = mysqli_stmt_execute($stmt);
            
            if ($executed) {
                $result = mysqli_stmt_get_result($stmt);
                
                $items = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $items[] = [
                        "id" => $row["id"],
                        "name" => $row["name"],
                        "description" => $row["description"] ?? "",
                        "price" => $row["price"],
                        "image_path" => $row["image_path"] ?? ""
                    ];
                }
                
                // Log the number of items found
                error_log("Found " . count($items) . " items for category ID: " . $categoryId);
                
                echo json_encode($items);
            } else {
                // Log the error
                error_log("Error executing statement: " . mysqli_error($con));
                echo json_encode([]);
            }
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
            
            // Write the updated get_menu_data.php file
            if (file_put_contents("get_menu_data.php", $getMenuDataContent)) {
                echo "<p>✅ Updated get_menu_data.php with improved error handling</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update get_menu_data.php</p>";
            }
            
            // Save the updated table_packages.php file
            if (file_put_contents($tablePackagesPath, $tpContent)) {
                echo "<p>✅ Updated table_packages.php with improved loadMenuItems function</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update table_packages.php</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Could not find table_packages.php</p>";
        }
        
        echo "<h2>Checking menu_items table structure</h2>";
        $tableStructureQuery = "DESCRIBE menu_items";
        $tableStructureResult = mysqli_query($con, $tableStructureQuery);
        
        if ($tableStructureResult) {
            echo "<p>Menu items table structure:</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($column = mysqli_fetch_assoc($tableStructureResult)) {
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
            echo "<p style='color: red;'>❌ Error checking menu_items table structure: " . mysqli_error($con) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ No categories found in the database!</p>";
    }
    
    echo "<h2>Fix Complete!</h2>";
    echo "<p>You should now be able to see menu items. Return to <a href='table_packages.php'>table_packages.php</a> and try again.</p>";
} else {
    echo "<p style='color: red;'>❌ Could not find database connection file (db.php).</p>";
}
?> 