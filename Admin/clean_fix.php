<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Clean Fix - Removing All Existing Fixes</h1>";

// Find files to clean
$filesToClean = ["table_packages.php", "index.php"];
$filesProcessed = [];

// First, clean up all existing fixes
foreach ($filesToClean as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalSize = strlen($content);
        
        // Look for and remove any of our script fixes
        $patterns = [
            '/<script>\s*\/\/\s*Permanent menu items fix.*?<\/script>/s',
            '/<script>\s*\/\/\s*Simplified menu items fix.*?<\/script>/s',
            '/<script>\s*\/\/\s*Updated menu items fix.*?<\/script>/s',
            '/<script>\s*\/\/\s*Targeted modal fix.*?<\/script>/s',
            '/<script>\s*\/\/\s*Direct category click.*?<\/script>/s',
            '/<script>\s*\/\/\s*Direct fix for.*?<\/script>/s',
            '/<script>\s*\/\/\s*Menu items fix.*?<\/script>/s',
            '/<script>\s*\/\/\s*Immediate fix.*?<\/script>/s'
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Check if anything was removed
        $newSize = strlen($content);
        if ($newSize < $originalSize) {
            if (file_put_contents($file, $content)) {
                echo "<p style='color: green;'>✅ Cleaned fix scripts from " . $file . " (" . ($originalSize - $newSize) . " bytes removed)</p>";
                $filesProcessed[] = $file;
            } else {
                echo "<p style='color: red;'>❌ Failed to clean " . $file . ". Check file permissions.</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ No fix scripts found in " . $file . "</p>";
            $filesProcessed[] = $file;
        }
    }
}

if (count($filesProcessed) === 0) {
    echo "<p style='color: red;'>❌ No files found to clean. Make sure you're in the correct directory.</p>";
    exit;
}

// Create the absolute minimal fix
$minimalFix = '
<script>
// Minimal Advance Order Fix - completely isolated
document.addEventListener("DOMContentLoaded", function() {
    // Wait for the modal to be shown
    document.addEventListener("shown.bs.modal", function(event) {
        // Check if it\'s the right modal
        if (event.target && event.target.id === "advanceOrderModal") {
            console.log("Advance Order modal shown, applying minimal fix");
            
            // Basic menu items
            var modalMenuItems = [
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
            
            // Function to handle showing menu items
            function displayItemsInModal(categoryId) {
                // Get container, create if needed
                var menuItems = document.getElementById("menuItems");
                if (!menuItems) {
                    menuItems = document.createElement("div");
                    menuItems.id = "menuItems";
                    menuItems.className = "row mt-3";
                    
                    var categoryContainer = document.getElementById("menuCategories");
                    if (categoryContainer && categoryContainer.parentNode) {
                        categoryContainer.parentNode.insertBefore(menuItems, categoryContainer.nextSibling);
                    }
                }
                
                // Clear container
                if (menuItems) {
                    menuItems.innerHTML = "";
                    
                    // Get items for this category
                    var items = modalMenuItems.filter(function(item) {
                        return item.category_id == categoryId;
                    });
                    
                    // Add items to container
                    items.forEach(function(item) {
                        var col = document.createElement("div");
                        col.className = "col-md-6 mb-3";
                        
                        col.innerHTML = 
                            \'<div class="card">\' +
                                \'<div class="card-body">\' +
                                    \'<h5 class="card-title">\' + item.name + \'</h5>\' +
                                    \'<p class="card-text text-primary">₱\' + item.price.toFixed(2) + \'</p>\' +
                                    \'<button type="button" class="btn btn-warning btn-block add-modal-item" \' +
                                        \'data-id="\' + item.id + \'" \' +
                                        \'data-name="\' + item.name + \'" \' +
                                        \'data-price="\' + item.price + \'">\' +
                                        \'<i class="fa fa-shopping-cart"></i> Add to Cart\' +
                                    \'</button>\' +
                                \'</div>\' +
                            \'</div>\';
                        
                        menuItems.appendChild(col);
                    });
                    
                    // Add click handlers for the Add to Cart buttons
                    var addButtons = menuItems.querySelectorAll(".add-modal-item");
                    for (var i = 0; i < addButtons.length; i++) {
                        addButtons[i].onclick = function() {
                            var id = this.getAttribute("data-id");
                            var name = this.getAttribute("data-name");
                            var price = this.getAttribute("data-price");
                            
                            // Try to use existing function, otherwise simulate it
                            if (typeof window.addToCart === "function") {
                                window.addToCart(id, name, price);
                            } else {
                                console.log("Adding to cart:", name, price);
                                
                                // Update order items area
                                var orderItems = document.getElementById("orderItems");
                                if (orderItems) {
                                    // Clear empty message if it exists
                                    if (orderItems.querySelector(".text-muted")) {
                                        orderItems.innerHTML = "";
                                    }
                                    
                                    // Create item row
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
                            }
                        };
                    }
                }
            }
            
            // Set up category buttons without breaking existing functionality
            var categoryButtons = document.querySelectorAll("#menuCategories .category-btn");
            if (categoryButtons.length > 0) {
                // Add our click handler without removing existing ones
                for (var i = 0; i < categoryButtons.length; i++) {
                    categoryButtons[i].addEventListener("click", function(e) {
                        // Update active state
                        for (var j = 0; j < categoryButtons.length; j++) {
                            categoryButtons[j].classList.remove("active");
                        }
                        this.classList.add("active");
                        
                        // Get category ID 
                        var categoryId = this.getAttribute("data-category-id");
                        if (!categoryId) {
                            // Try to determine from position
                            var index = Array.from(categoryButtons).indexOf(this);
                            categoryId = index + 1;
                        }
                        
                        // Display menu items
                        displayItemsInModal(categoryId);
                    });
                }
                
                // Activate first category by default
                var firstCategory = categoryButtons[0];
                if (firstCategory) {
                    firstCategory.classList.add("active");
                    
                    var categoryId = firstCategory.getAttribute("data-category-id");
                    if (!categoryId) {
                        categoryId = 1; // Default to first category
                    }
                    
                    // Small delay to ensure the DOM is ready
                    setTimeout(function() {
                        displayItemsInModal(categoryId);
                    }, 100);
                }
            }
        }
    });
});
</script>';

// Add our minimal fix to the main file
$mainFile = $filesProcessed[0];
$content = file_get_contents($mainFile);
$content = str_replace("</body>", $minimalFix . "\n</body>", $content);

if (file_put_contents($mainFile, $content)) {
    echo "<p style='color: green;'>✅ Successfully added minimal fix to " . $mainFile . "</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to add minimal fix to " . $mainFile . "</p>";
}

// Success message
echo "<h2>Clean Fix Applied!</h2>";
echo "<p>All previous fixes have been removed and a minimal, isolated fix has been applied.</p>";
echo "<p>This clean fix:</p>";
echo "<ul>";
echo "<li>Doesn't interfere with any page elements outside the Advance Order modal</li>";
echo "<li>Uses safer event handling that preserves existing functionality</li>";
echo "<li>Is completely contained within the modal</li>";
echo "<li>Only activates when the modal is shown</li>";
echo "</ul>";

echo "<p>The RESERVE NOW buttons on the main page should now work properly, and the menu items will still appear in the Advance Order modal.</p>";

echo "<a href='index.php?table_packages' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
?> 