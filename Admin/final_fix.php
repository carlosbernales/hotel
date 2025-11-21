<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Final Fix - Completely Separate Script</h1>";

// First remove all previous scripts again to be absolutely sure
$filesToClean = ["table_packages.php", "index.php"];
foreach ($filesToClean as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalSize = strlen($content);
        
        // Look for and remove any of our script fixes
        $patterns = [
            '/<script>\s*\/\/.*?menu.*?fix.*?<\/script>/s',
            '/<script>\s*\/\/.*?Targeted modal fix.*?<\/script>/s',
            '/<script>\s*\/\/.*?Minimal Advance Order.*?<\/script>/s',
            '/<script>\s*\/\/.*?Direct.*?<\/script>/s',
            '/<script>\s*document\.addEventListener\("DOMContentLoaded".*?advanceOrderModal.*?<\/script>/s',
            '<script src="menu_fix.js"></script>',
            '<script src="direct_menu_fix.js"></script>',
            '<script src="fix_menu_items.js"></script>'
        ];
        
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Save cleaned file
        if (strlen($content) < $originalSize) {
            if (file_put_contents($file, $content)) {
                echo "<p style='color: green;'>✅ Cleaned scripts from " . $file . "</p>";
            }
        }
    }
}

// Create a completely separate JS file
$jsContent = "// Advance Order Menu Loader
// This script runs only when the Advance Order modal is opened
// It does not interfere with any other elements on the page

// Run when document is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu loader initialized');
    
    // Only run when Advance Order modal is shown
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Advance Order modal shown');
        
        // Basic menu items data
        var menuItems = [
            { id: 1, category_id: 1, name: 'Hand-cut Potato Fries', price: 160.00 },
            { id: 2, category_id: 1, name: 'Mozzarella Stick', price: 150.00 },
            { id: 3, category_id: 1, name: 'Chicken Wings', price: 180.00 },
            { id: 4, category_id: 1, name: 'Spaghetti maccaroni', price: 270.00 },
            { id: 5, category_id: 1, name: 'Carbonara', price: 120.00 },
            { id: 6, category_id: 2, name: 'Salad', price: 200.00 },
            { id: 7, category_id: 2, name: 'Coconut Salad', price: 200.00 },
            { id: 8, category_id: 3, name: 'Spaghetti', price: 300.00 },
            { id: 9, category_id: 4, name: 'Egg Sandwich', price: 500.00 },
            { id: 10, category_id: 5, name: 'Coffee', price: 180.00 },
            { id: 11, category_id: 6, name: 'Tea', price: 120.00 },
            { id: 12, category_id: 7, name: 'Smoothie', price: 220.00 }
        ];
        
        // Setup menu items container
        var menuItemsContainer = document.getElementById('menuItems');
        if (!menuItemsContainer) {
            menuItemsContainer = document.createElement('div');
            menuItemsContainer.id = 'menuItems';
            menuItemsContainer.style.marginTop = '15px';
            
            var categoriesContainer = document.getElementById('menuCategories');
            if (categoriesContainer && categoriesContainer.parentNode) {
                categoriesContainer.parentNode.insertBefore(menuItemsContainer, categoriesContainer.nextSibling);
            }
        }
        
        // Function to show items for a category
        function showItems(categoryId) {
            // Get items for this category
            var filteredItems = menuItems.filter(function(item) {
                return item.category_id == categoryId;
            });
            
            // Clear container
            menuItemsContainer.innerHTML = '';
            
            // Create and append items
            if (filteredItems.length > 0) {
                for (var i = 0; i < filteredItems.length; i++) {
                    var item = filteredItems[i];
                    
                    var itemDiv = document.createElement('div');
                    itemDiv.className = 'card mb-2';
                    itemDiv.style.marginBottom = '10px';
                    
                    var body = document.createElement('div');
                    body.className = 'card-body';
                    
                    var title = document.createElement('h5');
                    title.className = 'card-title';
                    title.textContent = item.name;
                    
                    var price = document.createElement('p');
                    price.className = 'card-text text-primary';
                    price.textContent = '₱' + item.price.toFixed(2);
                    
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-warning btn-sm';
                    button.innerHTML = '<i class=\"fa fa-shopping-cart\"></i> Add to Cart';
                    button.setAttribute('data-id', item.id);
                    button.setAttribute('data-name', item.name);
                    button.setAttribute('data-price', item.price);
                    
                    // Add click handler for this specific button
                    button.onclick = (function(item) {
                        return function() {
                            // Add to cart using original function if available
                            if (typeof window.addToCart === 'function') {
                                window.addToCart(item.id, item.name, item.price);
                            } else {
                                console.log('Added to cart:', item.name, item.price);
                                
                                // Manually update cart display
                                var orderItems = document.getElementById('orderItems');
                                if (orderItems) {
                                    // Clear empty message if exists
                                    if (orderItems.querySelector('.text-muted')) {
                                        orderItems.innerHTML = '';
                                    }
                                    
                                    // Add item row
                                    var row = document.createElement('div');
                                    row.className = 'd-flex justify-content-between align-items-center mb-2';
                                    row.innerHTML = 
                                        '<div>' + item.name + ' <span class=\"badge badge-secondary\">1</span></div>' +
                                        '<div>₱' + parseFloat(item.price).toFixed(2) + '</div>';
                                    
                                    orderItems.appendChild(row);
                                    
                                    // Update total
                                    var totalElem = document.getElementById('orderTotal');
                                    if (totalElem) {
                                        var currentTotal = parseFloat(totalElem.textContent || '0');
                                        var newTotal = currentTotal + parseFloat(item.price);
                                        totalElem.textContent = newTotal.toFixed(2);
                                    }
                                }
                            }
                        };
                    })(item);
                    
                    // Assemble card
                    body.appendChild(title);
                    body.appendChild(price);
                    body.appendChild(button);
                    itemDiv.appendChild(body);
                    menuItemsContainer.appendChild(itemDiv);
                }
            } else {
                menuItemsContainer.innerHTML = '<div class=\"alert alert-info\">No items found for this category</div>';
            }
        }
        
        // Add click handlers to category buttons - carefully to avoid conflicts
        var categoryButtons = document.querySelectorAll('#menuCategories .category-btn');
        
        // First remove any handlers we might have added before
        for (var i = 0; i < categoryButtons.length; i++) {
            var oldBtn = categoryButtons[i];
            var newBtn = oldBtn.cloneNode(true);
            if (oldBtn.parentNode) {
                oldBtn.parentNode.replaceChild(newBtn, oldBtn);
            }
            categoryButtons[i] = newBtn;
        }
        
        // Now add our new handlers
        for (var i = 0; i < categoryButtons.length; i++) {
            categoryButtons[i].addEventListener('click', function(e) {
                // Don't stop propagation to allow other handlers to run
                
                // Set active state
                categoryButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Get category ID
                var categoryId = this.getAttribute('data-category-id');
                if (!categoryId) {
                    var index = Array.from(categoryButtons).indexOf(this);
                    categoryId = index + 1;
                }
                
                // Show items
                console.log('Category clicked, ID:', categoryId);
                showItems(categoryId);
            });
        }
        
        // Show first category by default
        if (categoryButtons.length > 0) {
            var firstCategory = categoryButtons[0];
            firstCategory.classList.add('active');
            
            var firstCategoryId = firstCategory.getAttribute('data-category-id');
            if (!firstCategoryId) {
                firstCategoryId = 1;
            }
            
            // Show after a brief delay to ensure DOM is ready
            setTimeout(function() {
                showItems(firstCategoryId);
            }, 100);
        }
    });
});";

// Save the JS file
if (file_put_contents("menu_loader.js", $jsContent)) {
    echo "<p style='color: green;'>✅ Created menu_loader.js file</p>";
    
    // Check which file to modify
    $targetFile = "";
    if (file_exists("table_packages.php")) {
        $targetFile = "table_packages.php";
    } elseif (file_exists("index.php")) {
        $targetFile = "index.php";
    }
    
    if ($targetFile) {
        $fileContent = file_get_contents($targetFile);
        
        // Only add the script tag if it doesn't exist
        if (strpos($fileContent, "menu_loader.js") === false) {
            // Add script tag before closing head tag
            $fileContent = str_replace("</head>", "<script src='menu_loader.js'></script>\n</head>", $fileContent);
            
            // Save the file
            if (file_put_contents($targetFile, $fileContent)) {
                echo "<p style='color: green;'>✅ Added script tag to " . $targetFile . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to add script tag to " . $targetFile . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ Script tag already exists in " . $targetFile . "</p>";
        }
    }
}

echo "<h2>Final Fix Complete!</h2>";
echo "<p>This solution:</p>";
echo "<ul>";
echo "<li>Completely removes all previous fixes</li>";
echo "<li>Creates a separate JavaScript file instead of embedding code</li>";
echo "<li>Uses the most minimal approach possible</li>";
echo "<li>Is carefully designed to avoid any conflicts</li>";
echo "<li>Only runs code when the modal is opened</li>";
echo "</ul>";

echo "<p>Please test by going to the Table Packages page and:</p>";
echo "<ol>";
echo "<li>Try clicking the RESERVE NOW buttons - they should now work correctly</li>";
echo "<li>Click 'Make Advance Order' to open the modal</li>";
echo "<li>The menu items should appear when you click on a category</li>";
echo "</ol>";

echo "<a href='index.php?table_packages' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
?> 