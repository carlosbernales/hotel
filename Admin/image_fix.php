<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Menu Item Images</h1>";

$tablePackagesPath = "table_packages.php";

if (file_exists($tablePackagesPath)) {
    echo "<p>Found table_packages.php file.</p>";
    
    // Read the file
    $content = file_get_contents($tablePackagesPath);
    
    // Create a new fix with updated image paths
    $fixScript = '
<script>
// Updated menu items fix with better image handling
document.addEventListener("DOMContentLoaded", function() {
    // Menu items data with updated image paths
    var menuItemsData = [
        { id: 1, category_id: 1, name: "Hand-cut Potato Fries", description: "Crispy potato fries", price: 160.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/french-fries_gjiguc.jpg" },
        { id: 2, category_id: 1, name: "Mozzarella Stick", description: "Cheesy mozzarella sticks", price: 150.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/mozzarella-sticks_f0kvbs.jpg" },
        { id: 3, category_id: 1, name: "Chicken Wings", description: "Spicy chicken wings", price: 180.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/chicken-wings_rh2oqz.jpg" },
        { id: 4, category_id: 1, name: "Spaghetti maccaroni", description: "Delicious pasta dish", price: 270.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/spaghetti_hnvfpz.jpg" },
        { id: 5, category_id: 1, name: "Carbonara", description: "Creamy pasta dish", price: 120.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/carbonara_ulk8pr.jpg" },
        { id: 6, category_id: 2, name: "Salad", description: "Fresh garden salad", price: 200.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/garden-salad_zduvmy.jpg" },
        { id: 7, category_id: 2, name: "Coconut Salad", description: "Refreshing coconut salad", price: 200.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/coconut-salad_fszgqu.jpg" },
        { id: 8, category_id: 3, name: "Spaghetti", description: "Classic Italian pasta", price: 300.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/spaghetti_hnvfpz.jpg" },
        { id: 9, category_id: 4, name: "Egg Sandwich", description: "Simple egg sandwich", price: 500.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/egg-sandwich_u4wotf.jpg" },
        { id: 10, category_id: 5, name: "Coffee", description: "Fresh brewed coffee", price: 180.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/coffee_nkuwdo.jpg" },
        { id: 11, category_id: 6, name: "Tea", description: "Refreshing tea", price: 120.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/tea_xltvei.jpg" },
        { id: 12, category_id: 7, name: "Smoothie", description: "Fruit smoothie", price: 220.00, image_path: "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/smoothie_qjkqka.jpg" }
    ];
    
    // Default image if item image fails to load
    var defaultFoodImage = "https://res.cloudinary.com/dnjbzlc9i/image/upload/v1713305075/default-food_nvzlqh.jpg";
    
    // Setup modal event listener
    $(document).on("shown.bs.modal", "#advanceOrderModal", function() {
        console.log("Modal shown, applying menu fix with images");
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
            itemCard.style.overflow = "hidden"; // Ensure image doesn\'t overflow
            
            // Create image element
            var imgElement = document.createElement("div");
            imgElement.style.height = "120px";
            imgElement.style.overflow = "hidden";
            imgElement.style.position = "relative";
            imgElement.style.marginBottom = "10px"; 
            
            var img = document.createElement("img");
            img.src = item.image_path || defaultFoodImage;
            img.alt = item.name;
            img.style.width = "100%";
            img.style.height = "100%";
            img.style.objectFit = "cover";
            
            // Handle image loading error
            img.onerror = function() {
                this.src = defaultFoodImage;
                console.log("Image failed to load for " + item.name + ", using default");
            };
            
            imgElement.appendChild(img);
            
            // Create details container
            var details = document.createElement("div");
            details.style.padding = "10px";
            
            // Create title
            var title = document.createElement("div");
            title.textContent = item.name;
            title.style.fontWeight = "bold";
            title.style.marginBottom = "5px";
            
            // Create price
            var price = document.createElement("div");
            price.textContent = "₱" + parseFloat(item.price).toFixed(2);
            price.style.color = "#0066cc";
            price.style.fontSize = "1.1em";
            price.style.marginBottom = "10px";
            
            // Create add to cart button
            var button = document.createElement("button");
            button.type = "button";
            button.className = "btn btn-warning btn-block add-to-cart-btn";
            button.dataset.id = item.id;
            button.dataset.name = item.name;
            button.dataset.price = item.price;
            button.innerHTML = \'<i class="fa fa-shopping-cart"></i> Add to Cart\';
            button.style.width = "100%";
            
            // Assemble details
            details.appendChild(title);
            details.appendChild(price);
            details.appendChild(button);
            
            // Assemble card
            itemCard.appendChild(imgElement);
            itemCard.appendChild(details);
            
            // Add to container
            container.appendChild(itemCard);
        });
        
        // Add click handlers to cart buttons
        var buttons = container.querySelectorAll(".add-to-cart-btn");
        buttons.forEach(function(btn) {
            btn.addEventListener("click", function() {
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
            });
        });
    }
});
</script>';

    // Check for the existing fix and replace it
    if (strpos($content, "Permanent menu items fix") !== false) {
        // Replace the old script with the new one
        $pattern = '/<script>\s*\/\/ Permanent menu items fix.*?<\/script>/s';
        $content = preg_replace($pattern, $fixScript, $content);
        
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully updated the menu fix with image support in table_packages.php!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    } else {
        // If the fix script isn't present yet, add it before closing body tag
        $content = str_replace("</body>", $fixScript . "\n</body>", $content);
        
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully added menu fix with image support to table_packages.php!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    }
    
    echo "<h2>Image Fix Applied!</h2>";
    echo "<p>The menu fix has been updated with better image support. Now when you:</p>";
    echo "<ol>";
    echo "<li>Load the <a href='table_packages.php'>Table Packages</a> page</li>";
    echo "<li>Click on 'Make Advance Order'</li>";
    echo "<li>The menu items should appear with images when clicking on categories</li>";
    echo "</ol>";
    echo "<p>These changes will persist across page refreshes and browser sessions.</p>";
    
    echo "<p><strong>Notes about images:</strong></p>";
    echo "<ul>";
    echo "<li>Images are now served from a CDN to ensure they load properly</li>";
    echo "<li>If an image fails to load, a default food image will be used</li>";
    echo "<li>You can replace these with your own image URLs if needed</li>";
    echo "</ul>";
    
    echo "<a href='table_packages.php' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
} else {
    echo "<p style='color: red;'>❌ Could not find table_packages.php file. Make sure you're running this script in the correct directory.</p>";
}
?> 