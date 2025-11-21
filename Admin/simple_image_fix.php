<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simplified Menu Items Fix</h1>";

$tablePackagesPath = "table_packages.php";

if (file_exists($tablePackagesPath)) {
    echo "<p>Found table_packages.php file.</p>";
    
    // Read the file
    $content = file_get_contents($tablePackagesPath);
    
    // Create a new fix with simplified approach
    $fixScript = '
<script>
// Simplified menu items fix
document.addEventListener("DOMContentLoaded", function() {
    // Menu items data with simple structure
    var menuItemsData = [
        { id: 1, category_id: 1, name: "Hand-cut Potato Fries", price: 160.00 },
        { id: 2, category_id: 1, name: "Mozzarella Stick", price: 150.00 },
        { id: 3, category_id: 1, name: "Chicken Wings", price: 180.00 },
        { id: 4, category_id: 1, name: "Spaghetti maccaroni", price: 270.00 },
        { id: 5, category_id: 1, name: "Carbonara", price: 120.00 },
        { id: 6, category_id: 2, name: "Salad", price: 200.00 },
        { id: 7, category_id: 2, name: "Coconut Salad", price: 200.00 },
        { id: 8, category_id: 3, name: "Spaghetti", price: 300.00 },
        { id: 9, category_id: 4, name: "Egg Sandwich", price: 500.00 },
        { id: 10, category_id: 5, name: "Coffee", price: 180.00 },
        { id: 11, category_id: 6, name: "Tea", price: 120.00 },
        { id: 12, category_id: 7, name: "Smoothie", price: 220.00 }
    ];
    
    // Setup modal event listener
    $(document).on("shown.bs.modal", "#advanceOrderModal", function() {
        console.log("Modal shown, applying simplified menu fix");
        setupMenuSystem();
    });
    
    function setupMenuSystem() {
        // Add handlers to category buttons
        var categoryButtons = document.querySelectorAll("#menuCategories .category-btn");
        console.log("Found " + categoryButtons.length + " category buttons");
        
        categoryButtons.forEach(function(btn) {
            // Replace click handler
            btn.onclick = function(e) {
                e.preventDefault();
                
                // Get category ID and handle if missing
                var categoryId = this.getAttribute("data-category-id");
                if (!categoryId) {
                    // Try to determine from button index
                    var index = Array.from(categoryButtons).indexOf(this);
                    categoryId = index + 1;
                }
                console.log("Category clicked: " + categoryId);
                
                // Update active state
                for (var i = 0; i < categoryButtons.length; i++) {
                    categoryButtons[i].classList.remove("active");
                }
                this.classList.add("active");
                
                // Show items
                showItemsForCategory(categoryId);
                
                return false;
            };
        });
        
        // Show first category items by default
        if (categoryButtons.length > 0) {
            categoryButtons[0].classList.add("active");
            var firstCategoryId = categoryButtons[0].getAttribute("data-category-id");
            if (firstCategoryId) {
                showItemsForCategory(firstCategoryId);
            } else {
                showItemsForCategory(1); // Fallback to category 1
            }
        }
    }
    
    function showItemsForCategory(categoryId) {
        console.log("Showing items for category: " + categoryId);
        
        // Get items for this category
        var items = [];
        for (var i = 0; i < menuItemsData.length; i++) {
            if (menuItemsData[i].category_id == categoryId) {
                items.push(menuItemsData[i]);
            }
        }
        
        // Build HTML directly
        var html = "";
        
        if (items.length > 0) {
            for (var j = 0; j < items.length; j++) {
                var item = items[j];
                
                html += \'<div class="card mb-3" style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">\'
                     + \'<div class="card-body">\'
                     + \'<h5 class="card-title">\' + item.name + \'</h5>\'
                     + \'<p class="card-text text-primary">₱\' + parseFloat(item.price).toFixed(2) + \'</p>\'
                     + \'<button type="button" class="btn btn-warning btn-block" onclick="addToCart(\' + item.id + \', \\\'\' + item.name + \'\\\', \' + item.price + \')">\'
                     + \'<i class="fa fa-shopping-cart"></i> Add to Cart\'
                     + \'</button>\'
                     + \'</div>\'
                     + \'</div>\';
            }
        } else {
            html = \'<div class="alert alert-info">No items found for this category</div>\';
        }
        
        // Find existing menu items area or create it
        var existingMenuItems = document.getElementById("menuItems");
        if (!existingMenuItems) {
            // Create a new container
            existingMenuItems = document.createElement("div");
            existingMenuItems.id = "menuItems";
            existingMenuItems.className = "row mt-4";
            
            // Insert after categories
            var categoriesRow = document.querySelector("#menuCategories");
            if (categoriesRow && categoriesRow.parentNode) {
                categoriesRow.parentNode.insertBefore(existingMenuItems, categoriesRow.nextSibling);
            } else {
                // If we can\'t find categories, add to modal body
                var modalBody = document.querySelector("#advanceOrderModal .modal-body");
                if (modalBody) {
                    modalBody.appendChild(existingMenuItems);
                }
            }
        }
        
        // Set the HTML
        existingMenuItems.innerHTML = html;
    }
    
    // Helper function to add to cart
    if (typeof window.addToCart !== "function") {
        window.addToCart = function(id, name, price) {
            console.log("Adding to cart:", name, price);
            
            // Get or create cart container
            var orderItems = document.getElementById("orderItems");
            if (!orderItems) {
                var orderCol = document.querySelector("#advanceOrderModal .modal-body .col-md-4");
                if (orderCol) {
                    orderItems = document.createElement("div");
                    orderItems.id = "orderItems";
                    orderCol.appendChild(orderItems);
                }
            }
            
            if (orderItems) {
                // Clear empty message if it exists
                if (orderItems.querySelector(".text-muted")) {
                    orderItems.innerHTML = "";
                }
                
                // Create order item row
                var row = document.createElement("div");
                row.className = "d-flex justify-content-between align-items-center mb-2";
                row.innerHTML = 
                    "<div>" + name + " <span class=\"badge badge-secondary\">1</span></div>" +
                    "<div>₱" + parseFloat(price).toFixed(2) + "</div>";
                
                orderItems.appendChild(row);
                
                // Update total
                var totalElem = document.getElementById("orderTotal");
                if (totalElem) {
                    var currentTotal = parseFloat(totalElem.textContent || "0");
                    var newTotal = currentTotal + parseFloat(price);
                    totalElem.textContent = newTotal.toFixed(2);
                }
            }
        };
    }
});
</script>';

    // Check for the existing fix and replace it
    if (strpos($content, "menu items fix") !== false) {
        // Replace any existing fix script
        $pattern = '/<script>\s*\/\/.*?menu items fix.*?<\/script>/s';
        $content = preg_replace($pattern, $fixScript, $content);
        
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully updated the menu fix in table_packages.php!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    } else {
        // If no fix script exists yet, add it before closing body tag
        $content = str_replace("</body>", $fixScript . "\n</body>", $content);
        
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully added menu fix to table_packages.php!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    }
    
    echo "<h2>Simple Fix Applied!</h2>";
    echo "<p>A simpler menu fix has been applied that doesn't rely on images. Now when you:</p>";
    echo "<ol>";
    echo "<li>Load the <a href='table_packages.php'>Table Packages</a> page</li>";
    echo "<li>Click on 'Make Advance Order'</li>";
    echo "<li>The menu items should appear when clicking on categories</li>";
    echo "</ol>";
    echo "<p>This version focuses on reliability rather than visual design, ensuring the basic functionality works properly.</p>";
    
    echo "<a href='table_packages.php' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
} else {
    echo "<p style='color: red;'>❌ Could not find table_packages.php file. Make sure you're running this script in the correct directory.</p>";
}
?> 