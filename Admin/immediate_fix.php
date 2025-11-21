<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Immediate Menu Fix</h1>";

// Create a simple direct JS fix that works immediately
$inlineScript = "// Direct category click handler with menu items
var menuItemsData = [
    { id: 1, category_id: 1, name: 'Hand-cut Potato Fries', description: 'Crispy potato fries', price: 160.00, image_path: 'images/menu/fries.jpg' },
    { id: 2, category_id: 1, name: 'Mozzarella Stick', description: 'Cheesy mozzarella sticks', price: 150.00, image_path: 'images/menu/mozzarella.jpg' },
    { id: 3, category_id: 1, name: 'Chicken Wings', description: 'Spicy chicken wings', price: 180.00, image_path: 'images/menu/wings.jpg' },
    { id: 4, category_id: 1, name: 'Spaghetti maccaroni', description: 'Delicious pasta dish', price: 270.00, image_path: 'images/menu/spaghetti.jpg' },
    { id: 5, category_id: 1, name: 'Carbonara', description: 'Creamy pasta dish', price: 120.00, image_path: 'images/menu/carbonara.jpg' },
    { id: 6, category_id: 2, name: 'Salad', description: 'Fresh garden salad', price: 200.00, image_path: 'images/menu/salad.jpg' },
    { id: 7, category_id: 2, name: 'Coconut Salad', description: 'Refreshing coconut salad', price: 200.00, image_path: 'images/menu/coconut_salad.jpg' },
    { id: 8, category_id: 3, name: 'Spaghetti', description: 'Classic Italian pasta', price: 300.00, image_path: 'images/menu/spaghetti.jpg' },
    { id: 9, category_id: 4, name: 'Egg Sandwich', description: 'Simple egg sandwich', price: 500.00, image_path: 'images/menu/sandwich.jpg' },
    { id: 10, category_id: 10, name: 'Matcha', description: 'Japanese green tea powder', price: 180.00, image_path: 'images/menu/matcha.jpg' }
];

// Immediate fix function - creates a direct binding
document.addEventListener('DOMContentLoaded', function() {
    console.log('Immediate fix loaded');
    
    // Apply fix when modal is opened
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Modal shown, applying fix');
        applyMenuFix();
    });
    
    // Also try to apply immediately if modal is already open
    setTimeout(function() {
        if ($('#advanceOrderModal').is(':visible')) {
            console.log('Modal already open, applying fix');
            applyMenuFix();
        }
    }, 500);
});

function applyMenuFix() {
    // Create menu items container if it doesn't exist
    if (!document.getElementById('menuItems')) {
        var menuCategoriesDiv = document.getElementById('menuCategories');
        if (menuCategoriesDiv) {
            var menuItemsDiv = document.createElement('div');
            menuItemsDiv.id = 'menuItems';
            menuItemsDiv.className = 'menu-items-grid';
            menuItemsDiv.style.display = 'grid';
            menuItemsDiv.style.gridTemplateColumns = 'repeat(auto-fill, minmax(200px, 1fr))';
            menuItemsDiv.style.gap = '15px';
            menuItemsDiv.style.marginTop = '20px';
            
            menuCategoriesDiv.parentNode.insertBefore(menuItemsDiv, menuCategoriesDiv.nextSibling);
        }
    }
    
    // Directly bind click handlers to all category buttons
    var buttons = document.querySelectorAll('#menuCategories .category-btn');
    console.log('Found ' + buttons.length + ' category buttons');
    
    for (var i = 0; i < buttons.length; i++) {
        var btn = buttons[i];
        console.log('Setting up button for category: ' + btn.textContent);
        
        // Remove any existing click handlers
        var newBtn = btn.cloneNode(true);
        if (btn.parentNode) {
            btn.parentNode.replaceChild(newBtn, btn);
        }
        
        // Add our direct click handler
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get category ID
            var categoryId = this.getAttribute('data-category-id');
            console.log('Category button clicked, ID: ' + categoryId);
            
            // Remove active class from all buttons
            var allButtons = document.querySelectorAll('#menuCategories .category-btn');
            for (var j = 0; j < allButtons.length; j++) {
                allButtons[j].classList.remove('active');
            }
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Display menu items for this category
            displayMenuItems(categoryId);
            
            return false;
        });
    }
    
    // Display items for the first category by default
    if (buttons.length > 0) {
        buttons[0].classList.add('active');
        var firstCategoryId = buttons[0].getAttribute('data-category-id');
        if (firstCategoryId) {
            displayMenuItems(firstCategoryId);
        }
    }
}

function displayMenuItems(categoryId) {
    console.log('Displaying menu items for category: ' + categoryId);
    var container = document.getElementById('menuItems');
    if (!container) {
        console.error('menuItems container not found');
        alert('Error: Menu items container not found. Please try refreshing the page.');
        return;
    }
    
    // Clear the container
    container.innerHTML = '';
    
    // Find items for this category
    var items = [];
    for (var i = 0; i < menuItemsData.length; i++) {
        if (menuItemsData[i].category_id == categoryId) {
            items.push(menuItemsData[i]);
        }
    }
    
    console.log('Found ' + items.length + ' items for category ' + categoryId);
    
    // Display items
    if (items.length > 0) {
        for (var j = 0; j < items.length; j++) {
            var item = items[j];
            
            // Create item card element
            var card = document.createElement('div');
            card.className = 'menu-item-card';
            card.style.border = '1px solid #ddd';
            card.style.borderRadius = '5px';
            card.style.overflow = 'hidden';
            
            // Create item image
            var img = document.createElement('img');
            img.src = item.image_path || 'images/menu/default-food.jpg';
            img.className = 'menu-item-image';
            img.alt = item.name;
            img.style.width = '100%';
            img.style.height = '150px';
            img.style.objectFit = 'cover';
            img.onerror = function() { this.src = 'images/menu/default-food.jpg'; };
            
            // Create item details div
            var details = document.createElement('div');
            details.className = 'menu-item-details';
            details.style.padding = '10px';
            
            // Create item title
            var title = document.createElement('div');
            title.className = 'menu-item-title';
            title.textContent = item.name;
            title.style.fontWeight = 'bold';
            title.style.marginBottom = '5px';
            
            // Create item price
            var price = document.createElement('div');
            price.className = 'menu-item-price';
            price.textContent = '₱' + parseFloat(item.price).toFixed(2);
            price.style.color = '#0066cc';
            price.style.fontSize = '1.1em';
            price.style.marginBottom = '10px';
            
            // Create add to cart button
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-warning btn-block add-to-cart-btn';
            button.dataset.id = item.id;
            button.dataset.name = item.name;
            button.dataset.price = item.price;
            button.innerHTML = '<i class=\"fa fa-shopping-cart\"></i> Add to Cart';
            button.style.width = '100%';
            button.style.padding = '8px';
            
            // Add click handler to button
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name');
                var price = this.getAttribute('data-price');
                
                // Use existing addToCart function if available
                if (typeof addToCart === 'function') {
                    addToCart(id, name, price);
                } else {
                    console.log('Added to cart: ' + name + ' - ₱' + price);
                    
                    // Add item to order display
                    var orderItems = document.getElementById('orderItems');
                    if (orderItems) {
                        // Clear 'empty' message if it exists
                        if (orderItems.querySelector('.text-muted')) {
                            orderItems.innerHTML = '';
                        }
                        
                        // Create item row
                        var row = document.createElement('div');
                        row.className = 'order-item d-flex justify-content-between align-items-center mb-2';
                        row.innerHTML = 
                            '<div>' + name + ' <span class=\"badge badge-secondary\">1</span></div>' +
                            '<div>₱' + parseFloat(price).toFixed(2) + '</div>';
                        
                        orderItems.appendChild(row);
                        
                        // Update total
                        var totalElem = document.getElementById('orderTotal');
                        if (totalElem) {
                            var currentTotal = parseFloat(totalElem.textContent || '0');
                            var newTotal = currentTotal + parseFloat(price);
                            totalElem.textContent = newTotal.toFixed(2);
                        }
                    }
                }
            });
            
            // Assemble the card
            details.appendChild(title);
            details.appendChild(price);
            details.appendChild(button);
            
            card.appendChild(img);
            card.appendChild(details);
            
            // Add to container
            container.appendChild(card);
        }
    } else {
        // No items found
        container.innerHTML = '<div class=\"col-12 text-center p-3\">' +
                            '<div class=\"alert alert-info\">No menu items found for this category</div>' +
                            '</div>';
    }
}

// Do a final check to see if categories are present but menu items are missing
setInterval(function() {
    if ($('#advanceOrderModal').is(':visible')) {
        var categoriesExist = $('#menuCategories button').length > 0;
        var itemsExist = $('#menuItems .menu-item-card').length > 0;
        
        if (categoriesExist && !itemsExist) {
            console.log('Categories exist but items are missing, reapplying fix');
            var activeBtn = document.querySelector('#menuCategories .category-btn.active');
            if (activeBtn) {
                var categoryId = activeBtn.getAttribute('data-category-id');
                if (categoryId) {
                    displayMenuItems(categoryId);
                }
            } else {
                var firstBtn = document.querySelector('#menuCategories .category-btn');
                if (firstBtn) {
                    firstBtn.classList.add('active');
                    var categoryId = firstBtn.getAttribute('data-category-id');
                    if (categoryId) {
                        displayMenuItems(categoryId);
                    }
                }
            }
        }
    }
}, 2000);";

// Create an HTML file that contains this script and can be loaded directly
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fix Menu Items</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Menu Items Fix</h1>
    <p>This page applies a fix to make menu items show up in the Advance Order modal.</p>
    <p>Please follow these steps:</p>
    <ol>
        <li>After loading this page, go back to <a href="table_packages.php" target="_blank">Table Packages</a></li>
        <li>Click on "Make Advance Order" to open the modal</li>
        <li>You should now see menu items appear when clicking on categories</li>
    </ol>
    
    <button onclick="window.location.href=\'table_packages.php\';" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; font-size: 16px;">Go to Table Packages</button>
    
    <script>
    ' . $inlineScript . '
    
    // Send this script to the main page as well
    try {
        if (window.opener && !window.opener.closed) {
            var script = document.createElement("script");
            script.textContent = ' . json_encode($inlineScript) . ';
            window.opener.document.body.appendChild(script);
            console.log("Script injected into opener window");
        }
    } catch(e) {
        console.error("Error injecting script to opener:", e);
    }
    </script>
</body>
</html>';

// Save the HTML file
if (file_put_contents("fix_menu_items.html", $html)) {
    echo "<p style='color: green;'>✅ Created fix_menu_items.html</p>";
    
    // Also create direct JS file that can be imported
    if (file_put_contents("fix_menu_items.js", $inlineScript)) {
        echo "<p style='color: green;'>✅ Created fix_menu_items.js</p>";
    }
    
    // Create a minimal PHP script to inject the fix directly into table_packages.php
    $directFixPhp = '<?php
// Direct fix for table_packages.php
$tablePackagesPath = "table_packages.php";

if (file_exists($tablePackagesPath)) {
    $content = file_get_contents($tablePackagesPath);
    
    // Add our script directly before the closing body tag
    $fixScript = \'' . $inlineScript . '\';
    
    // Check if the fix is already added
    if (strpos($content, "Immediate fix loaded") === false) {
        $content = str_replace("</body>", "<script>" . $fixScript . "</script>\n</body>", $content);
        
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style=\'color: green;\'>✅ Successfully updated table_packages.php with immediate menu items fix.</p>";
        } else {
            echo "<p style=\'color: red;\'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    } else {
        echo "<p style=\'color: blue;\'>ℹ️ Fix script already exists in table_packages.php.</p>";
    }
    
    echo "<p>Going back to Table Packages...</p>";
    echo "<script>setTimeout(function() { window.location.href = \'table_packages.php\'; }, 1500);</script>";
} else {
    echo "<p style=\'color: red;\'>❌ Could not find table_packages.php file.</p>";
}
?>';
    
    if (file_put_contents("inject_fix.php", $directFixPhp)) {
        echo "<p style='color: green;'>✅ Created inject_fix.php</p>";
    }
    
    echo "<h2>Fix Ready!</h2>";
    echo "<p>Try these options (in order of recommendation):</p>";
    echo "<ol>";
    echo "<li><a href='inject_fix.php' target='_blank'>Run the injector script</a> - Direct fix that updates table_packages.php</li>";
    echo "<li><a href='fix_menu_items.html' target='_blank'>Open the fix page</a> - Visit this page, then go back to table_packages.php</li>";
    echo "<li>Manually add this script tag to table_packages.php: <pre>&lt;script src='fix_menu_items.js'&gt;&lt;/script&gt;</pre></li>";
    echo "</ol>";
} else {
    echo "<p style='color: red;'>❌ Failed to create fix files. Check directory permissions.</p>";
}
?> 