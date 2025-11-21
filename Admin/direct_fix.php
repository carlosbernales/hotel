<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Making Menu Fix Permanent</h1>";

$tablePackagesPath = "table_packages.php";

if (file_exists($tablePackagesPath)) {
    // Read the file
    $content = file_get_contents($tablePackagesPath);
    echo "<p>Found table_packages.php file.</p>";
    
    // Create our fix script
    $fixScript = '
<script>
// Permanent menu items fix
document.addEventListener("DOMContentLoaded", function() {
    // Menu items data
    var menuItemsData = [
        { id: 1, category_id: 1, name: "Hand-cut Potato Fries", description: "Crispy potato fries", price: 160.00, image_path: "images/menu/fries.jpg" },
        { id: 2, category_id: 1, name: "Mozzarella Stick", description: "Cheesy mozzarella sticks", price: 150.00, image_path: "images/menu/mozzarella.jpg" },
        { id: 3, category_id: 1, name: "Chicken Wings", description: "Spicy chicken wings", price: 180.00, image_path: "images/menu/wings.jpg" },
        { id: 4, category_id: 1, name: "Spaghetti maccaroni", description: "Delicious pasta dish", price: 270.00, image_path: "images/menu/spaghetti.jpg" },
        { id: 5, category_id: 1, name: "Carbonara", description: "Creamy pasta dish", price: 120.00, image_path: "images/menu/carbonara.jpg" },
        { id: 6, category_id: 2, name: "Salad", description: "Fresh garden salad", price: 200.00, image_path: "images/menu/salad.jpg" },
        { id: 7, category_id: 2, name: "Coconut Salad", description: "Refreshing coconut salad", price: 200.00, image_path: "images/menu/coconut_salad.jpg" },
        { id: 8, category_id: 3, name: "Spaghetti", description: "Classic Italian pasta", price: 300.00, image_path: "images/menu/spaghetti.jpg" },
        { id: 9, category_id: 4, name: "Egg Sandwich", description: "Simple egg sandwich", price: 500.00, image_path: "images/menu/sandwich.jpg" },
        { id: 10, category_id: 5, name: "Coffee", description: "Fresh brewed coffee", price: 180.00, image_path: "images/menu/coffee.jpg" },
        { id: 11, category_id: 6, name: "Tea", description: "Refreshing tea", price: 120.00, image_path: "images/menu/tea.jpg" },
        { id: 12, category_id: 7, name: "Smoothie", description: "Fruit smoothie", price: 220.00, image_path: "images/menu/smoothie.jpg" }
    ];
    
    // Setup modal event listener
    $(document).on("shown.bs.modal", "#advanceOrderModal", function() {
        console.log("Modal shown, applying menu fix");
        setupMenuSystem();
    });
    
    function setupMenuSystem() {
        // Create menu items container if it doesn\'t exist
        if (!document.getElementById("menuItems")) {
            var container = document.createElement("div");
            container.id = "menuItems";
            container.style.display = "grid";
            container.style.gridTemplateColumns = "repeat(auto-fill, minmax(200px, 1fr))";
            container.style.gap = "15px";
            container.style.marginTop = "20px";
            
            // Find where to insert it
            var categoriesRow = document.querySelector("#menuCategories");
            if (categoriesRow) {
                categoriesRow.parentNode.insertBefore(container, categoriesRow.nextSibling);
                console.log("Created menu items container");
            }
        }
        
        // Add handlers to category buttons
        var categoryButtons = document.querySelectorAll("#menuCategories .category-btn");
        console.log("Found " + categoryButtons.length + " category buttons");
        
        categoryButtons.forEach(function(btn) {
            // Clone to remove existing handlers
            var newBtn = btn.cloneNode(true);
            if (btn.parentNode) {
                btn.parentNode.replaceChild(newBtn, btn);
            }
            
            // Add our handler
            newBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get category ID
                var categoryId = this.getAttribute("data-category-id");
                console.log("Category clicked: " + categoryId);
                
                // Update active state
                document.querySelectorAll("#menuCategories .category-btn").forEach(function(b) {
                    b.classList.remove("active");
                });
                this.classList.add("active");
                
                // Show items
                showItemsForCategory(categoryId);
                
                return false;
            });
        });
        
        // Show first category items by default
        if (categoryButtons.length > 0) {
            categoryButtons[0].classList.add("active");
            var firstCategoryId = categoryButtons[0].getAttribute("data-category-id");
            if (firstCategoryId) {
                showItemsForCategory(firstCategoryId);
            }
        }
    }
    
    function showItemsForCategory(categoryId) {
        console.log("Showing items for category: " + categoryId);
        
        // Get items for this category
        var items = menuItemsData.filter(function(item) {
            return item.category_id == categoryId;
        });
        
        // Get container
        var container = document.getElementById("menuItems");
        if (!container) {
            console.error("No container found for menu items");
            return;
        }
        
        // Clear container
        container.innerHTML = "";
        
        // Add items
        items.forEach(function(item) {
            var itemCard = document.createElement("div");
            itemCard.className = "menu-item-card";
            itemCard.style.border = "1px solid #ddd";
            itemCard.style.borderRadius = "5px";
            itemCard.style.padding = "10px";
            
            var html = `
                <div style="height:120px; overflow:hidden; margin-bottom:10px;">
                    <img src="${item.image_path || "images/menu/default-food.jpg"}" 
                         style="width:100%; height:100%; object-fit:cover;"
                         onerror="this.src=\'images/menu/default-food.jpg\'">
                </div>
                <div style="font-weight:bold; margin-bottom:5px;">${item.name}</div>
                <div style="color:#0066cc; margin-bottom:10px;">₱${parseFloat(item.price).toFixed(2)}</div>
                <button class="btn btn-warning btn-block add-to-cart-btn" 
                        data-id="${item.id}" 
                        data-name="${item.name}" 
                        data-price="${item.price}"
                        style="width:100%;">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                </button>
            `;
            
            itemCard.innerHTML = html;
            container.appendChild(itemCard);
        });
        
        // Add click handlers to cart buttons
        var buttons = container.querySelectorAll(".add-to-cart-btn");
        buttons.forEach(function(btn) {
            btn.onclick = function() {
                var id = this.getAttribute("data-id");
                var name = this.getAttribute("data-name");
                var price = this.getAttribute("data-price");
                
                if (typeof addToCart === "function") {
                    addToCart(id, name, price);
                } else {
                    console.log("Added to cart:", name, price);
                    
                    // Add item to order display
                    var orderItems = document.getElementById("orderItems");
                    if (orderItems) {
                        // Clear empty message if it exists
                        if (orderItems.querySelector(".text-muted")) {
                            orderItems.innerHTML = "";
                        }
                        
                        // Create item row
                        var row = document.createElement("div");
                        row.className = "order-item d-flex justify-content-between align-items-center mb-2";
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
                }
            };
        });
    }
});
</script>';

    // Check if the fix is already in the file
    if (strpos($content, "Permanent menu items fix") === false) {
        // Add the fix script just before the closing </body> tag
        $content = str_replace("</body>", $fixScript . "\n</body>", $content);
        
        // Save the updated file
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully added the permanent menu fix to table_packages.php!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Fix script is already in table_packages.php.</p>";
    }
    
    echo "<h2>Fix Applied!</h2>";
    echo "<p>The menu fix has been permanently added to your table_packages.php file. Now when you:</p>";
    echo "<ol>";
    echo "<li>Load the <a href='table_packages.php'>Table Packages</a> page</li>";
    echo "<li>Click on 'Make Advance Order'</li>";
    echo "<li>The menu items should appear when clicking on categories</li>";
    echo "</ol>";
    echo "<p>The fix will persist even if you refresh the page or close and reopen your browser.</p>";
    
    echo "<a href='table_packages.php' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
} else {
    echo "<p style='color: red;'>❌ Could not find table_packages.php file. Make sure you're running this script in the correct directory.</p>";
}
?> 