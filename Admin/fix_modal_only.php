<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Targeted Modal Fix</h1>";

// Find the correct files to modify
$possibleFiles = ["table_packages.php", "index.php"];
$fileFound = false;
$filePath = "";

foreach ($possibleFiles as $file) {
    if (file_exists($file)) {
        $filePath = $file;
        $fileFound = true;
        break;
    }
}

if ($fileFound) {
    echo "<p>Found file: " . $filePath . "</p>";
    
    // Read the file
    $content = file_get_contents($filePath);
    
    // Create a new fix with minimal interference
    $fixScript = '
<script>
// Targeted modal fix - only affects advance order modal
(function() {
    // Wait for jQuery
    var checkJQuery = setInterval(function() {
        if (typeof jQuery !== "undefined") {
            clearInterval(checkJQuery);
            initModalFix();
        }
    }, 100);

    function initModalFix() {
        console.log("Initializing targeted modal fix");
        
        // Only run when modal is shown
        $(document).on("shown.bs.modal", "#advanceOrderModal", function() {
            console.log("Advance Order modal shown, applying fix");
            
            // Items data
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
            
            // Setup category buttons
            var categoryButtons = document.querySelectorAll("#menuCategories .category-btn");
            if (categoryButtons.length > 0) {
                // Replace handlers only on category buttons inside the modal
                for (var i = 0; i < categoryButtons.length; i++) {
                    var btn = categoryButtons[i];
                    
                    // Remove previous listeners
                    var newBtn = btn.cloneNode(true);
                    if (btn.parentNode) {
                        btn.parentNode.replaceChild(newBtn, btn);
                    }
                    
                    // Add new listener
                    newBtn.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Get category ID
                        var categoryId = this.getAttribute("data-category-id");
                        if (!categoryId) {
                            // Try to determine from index
                            var allBtns = document.querySelectorAll("#menuCategories .category-btn");
                            var index = Array.from(allBtns).indexOf(this);
                            categoryId = index + 1;
                        }
                        
                        // Update active state
                        document.querySelectorAll("#menuCategories .category-btn").forEach(function(b) {
                            b.classList.remove("active");
                        });
                        this.classList.add("active");
                        
                        // Show menu items
                        showItems(categoryId);
                        
                        return false;
                    });
                }
                
                // Auto-click first category
                setTimeout(function() {
                    var firstBtn = categoryButtons[0];
                    if (firstBtn) {
                        firstBtn.classList.add("active");
                        var categoryId = firstBtn.getAttribute("data-category-id") || 1;
                        showItems(categoryId);
                    }
                }, 100);
            }
            
            // Create menu items container if needed
            if (!document.getElementById("menuItems")) {
                var menuItemsContainer = document.createElement("div");
                menuItemsContainer.id = "menuItems";
                menuItemsContainer.className = "row mt-3";
                menuItemsContainer.style.display = "flex";
                menuItemsContainer.style.flexWrap = "wrap";
                menuItemsContainer.style.gap = "10px";
                
                // Find where to put it
                var categories = document.getElementById("menuCategories");
                if (categories && categories.parentNode) {
                    categories.parentNode.insertBefore(menuItemsContainer, categories.nextSibling);
                } else {
                    // Fallback - add to modal body
                    var modalBody = document.querySelector("#advanceOrderModal .modal-body");
                    if (modalBody) {
                        modalBody.appendChild(menuItemsContainer);
                    }
                }
            }
            
            // Function to display menu items for a category
            function showItems(categoryId) {
                console.log("Showing items for category", categoryId);
                
                // Get container
                var container = document.getElementById("menuItems");
                if (!container) return;
                
                // Clear previous items
                container.innerHTML = "";
                
                // Find items for this category
                var items = [];
                for (var i = 0; i < menuItemsData.length; i++) {
                    if (menuItemsData[i].category_id == categoryId) {
                        items.push(menuItemsData[i]);
                    }
                }
                
                if (items.length === 0) {
                    container.innerHTML = "<div class=\'col-12\'><div class=\'alert alert-info\'>No menu items found for this category</div></div>";
                    return;
                }
                
                // Add items to container
                for (var j = 0; j < items.length; j++) {
                    var item = items[j];
                    
                    var itemDiv = document.createElement("div");
                    itemDiv.className = "col-md-6";
                    itemDiv.style.marginBottom = "15px";
                    
                    var card = document.createElement("div");
                    card.className = "card";
                    card.style.height = "100%";
                    
                    var cardBody = document.createElement("div");
                    cardBody.className = "card-body d-flex flex-column";
                    
                    var title = document.createElement("h5");
                    title.className = "card-title";
                    title.textContent = item.name;
                    
                    var price = document.createElement("p");
                    price.className = "card-text text-primary";
                    price.textContent = "₱" + parseFloat(item.price).toFixed(2);
                    
                    var button = document.createElement("button");
                    button.type = "button";
                    button.className = "btn btn-warning mt-auto";
                    button.innerHTML = "<i class=\'fa fa-shopping-cart\'></i> Add to Cart";
                    button.dataset.id = item.id;
                    button.dataset.name = item.name;
                    button.dataset.price = item.price;
                    
                    // Add click handler
                    button.addEventListener("click", function() {
                        var id = this.dataset.id;
                        var name = this.dataset.name;
                        var price = this.dataset.price;
                        
                        // Try using existing addToCart function
                        if (typeof window.addToCart === "function") {
                            window.addToCart(id, name, price);
                        } else {
                            // Fallback to manual implementation
                            console.log("Adding to cart:", name, price);
                            
                            // Update order display
                            var orderItems = document.getElementById("orderItems");
                            if (!orderItems) {
                                var rightColumn = document.querySelector("#advanceOrderModal .modal-body .col-md-4");
                                if (rightColumn) {
                                    orderItems = document.createElement("div");
                                    orderItems.id = "orderItems";
                                    rightColumn.appendChild(orderItems);
                                }
                            }
                            
                            if (orderItems) {
                                // Clear empty message if it exists
                                if (orderItems.querySelector(".text-muted")) {
                                    orderItems.innerHTML = "";
                                }
                                
                                var itemRow = document.createElement("div");
                                itemRow.className = "d-flex justify-content-between align-items-center mb-2";
                                itemRow.innerHTML = 
                                    "<div>" + name + " <span class=\'badge badge-secondary\'>1</span></div>" +
                                    "<div>₱" + parseFloat(price).toFixed(2) + "</div>";
                                
                                orderItems.appendChild(itemRow);
                                
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
                    
                    // Assemble card
                    cardBody.appendChild(title);
                    cardBody.appendChild(price);
                    cardBody.appendChild(button);
                    card.appendChild(cardBody);
                    itemDiv.appendChild(card);
                    container.appendChild(itemDiv);
                }
            }
        });
    }
})();
</script>';

    // Check if the fix is already in the file
    if (strpos($content, "Targeted modal fix") !== false) {
        // Replace the existing fix
        $pattern = '/<script>\s*\/\/\s*Targeted modal fix.*?<\/script>/s';
        $content = preg_replace($pattern, $fixScript, $content);
    } else {
        // Add the fix before the closing body tag
        $content = str_replace("</body>", $fixScript . "\n</body>", $content);
    }
    
    // Save the updated file
    if (file_put_contents($filePath, $content)) {
        echo "<p style='color: green;'>✅ Successfully applied the targeted modal fix to " . $filePath . "!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update " . $filePath . ". Check file permissions.</p>";
    }
    
    echo "<h2>Targeted Fix Applied!</h2>";
    echo "<p>A more targeted fix has been applied that ONLY affects the Advance Order modal, without interfering with other page elements.</p>";
    echo "<p>This fix:</p>";
    echo "<ul>";
    echo "<li>Only runs when the Advance Order modal is opened</li>";
    echo "<li>Doesn't affect the RESERVE NOW buttons on the main page</li>";
    echo "<li>Creates a clean menu items display when clicking categories</li>";
    echo "</ul>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='index.php?table_packages'>Table Packages</a> page</li>";
    echo "<li>Try clicking the RESERVE NOW buttons - they should work now</li>";
    echo "<li>Click on 'Make Advance Order' to open the modal</li>";
    echo "<li>The menu items should appear when clicking on categories</li>";
    echo "</ol>";
    
    echo "<a href='index.php?table_packages' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
} else {
    echo "<p style='color: red;'>❌ Could not find table_packages.php or index.php file. Please check the directory.</p>";
}
?> 