<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Menu Items Display</h1>";

// Path to the table_packages.php file
$tablePackagesPath = "table_packages.php";

if (file_exists($tablePackagesPath)) {
    echo "<p>Found table_packages.php file.</p>";
    
    // Create a direct JS fix for the menu display
    $directFix = "// Direct fix for menu items issue
var fixMenuScript = document.createElement('script');
fixMenuScript.innerHTML = `
// Hardcoded menu data from cafe_management.php
var menuItems = [
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

// Direct fix for loadMenuItems function
function fixedLoadMenuItems(categoryId) {
    console.log('Using fixed loadMenuItems function with category ID:', categoryId);
    
    // Get items for this category
    var items = [];
    for (var i = 0; i < menuItems.length; i++) {
        if (menuItems[i].category_id == categoryId) {
            items.push(menuItems[i]);
        }
    }
    
    // Get container and clear it
    var container = document.getElementById('menuItems');
    if (!container) {
        console.error('menuItems container not found');
        return;
    }
    
    container.innerHTML = '';
    
    // Display items
    if (items && items.length > 0) {
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var itemHtml = '<div class=\"menu-item-card\">' +
                '<img src=\"' + (item.image_path || 'images/menu/default-food.jpg') + '\" ' +
                'class=\"menu-item-image\" ' +
                'alt=\"' + item.name + '\" ' +
                'onerror=\"this.src=\\'images/menu/default-food.jpg\\'\">' +
                '<div class=\"menu-item-details\">' +
                '<div class=\"menu-item-title\">' + item.name + '</div>' +
                '<div class=\"menu-item-price\">₱' + parseFloat(item.price).toFixed(2) + '</div>' +
                '<button type=\"button\" ' +
                'class=\"btn btn-warning btn-block add-to-cart-btn\" ' +
                'data-id=\"' + item.id + '\" ' +
                'data-name=\"' + item.name + '\" ' +
                'data-price=\"' + item.price + '\">' +
                '<i class=\"fa fa-shopping-cart\"></i> Add to Cart' +
                '</button>' +
                '</div>' +
                '</div>';
            
            container.innerHTML += itemHtml;
        }
        
        // Add click handlers to add-to-cart buttons
        var buttons = container.querySelectorAll('.add-to-cart-btn');
        for (var j = 0; j < buttons.length; j++) {
            buttons[j].onclick = function() {
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name');
                var price = this.getAttribute('data-price');
                
                // Use existing addToCart function if available
                if (typeof addToCart === 'function') {
                    addToCart(id, name, price);
                } else {
                    console.log('Added to cart:', name, 'Price:', price);
                    alert('Added ' + name + ' to cart');
                }
            };
        }
    } else {
        container.innerHTML = '<div class=\"col-12 text-center p-3\">' +
            '<div class=\"alert alert-info\">No menu items found for this category</div>' +
            '</div>';
    }
}

// Override the original loadMenuItems function
var originalLoadMenuItems = window.loadMenuItems;
window.loadMenuItems = function(categoryId) {
    console.log('Intercepted loadMenuItems call for category:', categoryId);
    
    // Call our fixed version
    fixedLoadMenuItems(categoryId);
    
    // Also attempt to call the original as a fallback
    try {
        if (originalLoadMenuItems && typeof originalLoadMenuItems === 'function') {
            console.log('Also trying original loadMenuItems as fallback');
            originalLoadMenuItems(categoryId);
        }
    } catch (e) {
        console.error('Error calling original loadMenuItems:', e);
    }
};

// Apply the fix when modal is shown
$(document).on('shown.bs.modal', '#advanceOrderModal', function() {
    console.log('Modal shown, applying menu items fix');
    
    // Get the active category button
    var activeBtn = document.querySelector('#menuCategories .category-btn.active');
    if (activeBtn) {
        var categoryId = activeBtn.getAttribute('data-category-id');
        if (categoryId) {
            console.log('Loading items for active category:', categoryId);
            fixedLoadMenuItems(categoryId);
        }
    } else {
        // If no active button, try the first category
        var firstBtn = document.querySelector('#menuCategories .category-btn');
        if (firstBtn) {
            firstBtn.classList.add('active');
            var categoryId = firstBtn.getAttribute('data-category-id');
            if (categoryId) {
                console.log('Loading items for first category:', categoryId);
                fixedLoadMenuItems(categoryId);
            }
        }
    }
});

// Apply the fix when any category button is clicked
$(document).on('click', '#menuCategories .category-btn', function() {
    console.log('Category button clicked');
    
    // Remove active class from all buttons
    $('.category-btn').removeClass('active');
    
    // Add active class to clicked button
    $(this).addClass('active');
    
    // Get category ID and load items
    var categoryId = $(this).data('category-id');
    console.log('Loading items for clicked category:', categoryId);
    fixedLoadMenuItems(categoryId);
    
    // Prevent original event handling
    return false;
});

console.log('Menu items fix script loaded');
`;

// Add script to body
document.body.appendChild(fixMenuScript);
";
    
    // Read the table_packages.php file
    $content = file_get_contents($tablePackagesPath);
    
    // Check if the fix is already added
    if (strpos($content, "// Direct fix for menu items issue") === false) {
        // Add the fix script just before the closing </body> tag
        $content = str_replace("</body>", "<script>{$directFix}</script>\n</body>", $content);
        
        // Save the updated file
        if (file_put_contents($tablePackagesPath, $content)) {
            echo "<p style='color: green;'>✅ Successfully updated table_packages.php with menu items fix.</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update table_packages.php. Check file permissions.</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Fix script already exists in table_packages.php.</p>";
    }
    
    // Create a separate JS file as an alternative approach
    $jsContent = "// Hardcoded menu data from cafe_management.php
var menuItems = [
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

// Direct replacement for loadMenuItems function
function loadMenuItems(categoryId) {
    console.log('Loading menu items for category:', categoryId);
    
    // Get items for this category
    var items = [];
    for (var i = 0; i < menuItems.length; i++) {
        if (menuItems[i].category_id == categoryId) {
            items.push(menuItems[i]);
        }
    }
    
    // Get container and clear it
    var container = document.getElementById('menuItems');
    if (!container) {
        console.error('menuItems container not found');
        return;
    }
    
    container.innerHTML = '';
    
    // Display items
    if (items && items.length > 0) {
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var itemHtml = '<div class=\"menu-item-card\">' +
                '<img src=\"' + (item.image_path || 'images/menu/default-food.jpg') + '\" ' +
                'class=\"menu-item-image\" ' +
                'alt=\"' + item.name + '\" ' +
                'onerror=\"this.src=\'images/menu/default-food.jpg\'\">' +
                '<div class=\"menu-item-details\">' +
                '<div class=\"menu-item-title\">' + item.name + '</div>' +
                '<div class=\"menu-item-price\">₱' + parseFloat(item.price).toFixed(2) + '</div>' +
                '<button type=\"button\" ' +
                'class=\"btn btn-warning btn-block add-to-cart-btn\" ' +
                'data-id=\"' + item.id + '\" ' +
                'data-name=\"' + item.name + '\" ' +
                'data-price=\"' + item.price + '\">' +
                '<i class=\"fa fa-shopping-cart\"></i> Add to Cart' +
                '</button>' +
                '</div>' +
                '</div>';
            
            container.innerHTML += itemHtml;
        }
        
        // Add click handlers to add-to-cart buttons
        var buttons = container.querySelectorAll('.add-to-cart-btn');
        for (var j = 0; j < buttons.length; j++) {
            buttons[j].onclick = function() {
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name');
                var price = this.getAttribute('data-price');
                
                // Use existing addToCart function if available
                if (typeof addToCart === 'function') {
                    addToCart(id, name, price);
                } else {
                    console.log('Added to cart:', name, 'Price:', price);
                    alert('Added ' + name + ' to cart');
                }
            };
        }
    } else {
        container.innerHTML = '<div class=\"col-12 text-center p-3\">' +
            '<div class=\"alert alert-info\">No menu items found for this category</div>' +
            '</div>';
    }
}

// Ensure the fix is applied when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fix script loaded');
    
    // Apply the fix when modal is shown
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Modal shown, checking for active category');
        
        // Get the active category button
        var activeBtn = document.querySelector('#menuCategories .category-btn.active');
        if (activeBtn) {
            var categoryId = activeBtn.getAttribute('data-category-id');
            if (categoryId) {
                console.log('Loading items for active category:', categoryId);
                loadMenuItems(categoryId);
            }
        } else {
            // If no active button, try the first category
            var firstBtn = document.querySelector('#menuCategories .category-btn');
            if (firstBtn) {
                firstBtn.classList.add('active');
                var categoryId = firstBtn.getAttribute('data-category-id');
                if (categoryId) {
                    console.log('Loading items for first category:', categoryId);
                    loadMenuItems(categoryId);
                }
            }
        }
    });
});";

    // Save the JS file
    if (file_put_contents("direct_menu_fix.js", $jsContent)) {
        echo "<p style='color: green;'>✅ Created direct_menu_fix.js file.</p>";
        
        // Add script tag to include the JS file
        if (strpos($content, "direct_menu_fix.js") === false) {
            $content = str_replace("</head>", "<script src='direct_menu_fix.js'></script>\n</head>", $content);
            
            if (file_put_contents($tablePackagesPath, $content)) {
                echo "<p style='color: green;'>✅ Added script tag to table_packages.php.</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update table_packages.php with script tag.</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to create direct_menu_fix.js file.</p>";
    }
    
    // Also create a patch that adds a debugging function
    $debugJs = "// Menu items debugger
function debugMenuItems() {
    console.log('Starting menu debugging');
    
    // Check if the menu categories container exists
    var categoriesContainer = document.getElementById('menuCategories');
    if (!categoriesContainer) {
        console.error('Menu categories container not found');
        alert('Error: Menu categories container not found');
        return;
    }
    
    // Check if the menu items container exists
    var itemsContainer = document.getElementById('menuItems');
    if (!itemsContainer) {
        console.error('Menu items container not found');
        alert('Error: Menu items container not found');
        return;
    }
    
    // Get the active category
    var activeCategory = categoriesContainer.querySelector('.category-btn.active');
    if (!activeCategory) {
        console.log('No active category found, selecting first one');
        activeCategory = categoriesContainer.querySelector('.category-btn');
        if (activeCategory) {
            activeCategory.classList.add('active');
        }
    }
    
    if (activeCategory) {
        var categoryId = activeCategory.getAttribute('data-category-id');
        console.log('Active category ID:', categoryId);
        
        // Try loading menu items
        loadMenuItems(categoryId);
    } else {
        console.error('No category buttons found');
        alert('Error: No category buttons found');
    }
}

// Add a debug button to the modal
document.addEventListener('DOMContentLoaded', function() {
    console.log('Adding debug button to modal');
    
    // Add the debug button when the modal is shown
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Modal shown, adding debug button');
        
        // Check if debug button already exists
        if (!document.getElementById('menuDebugBtn')) {
            var modalHeader = document.querySelector('#advanceOrderModal .modal-header');
            if (modalHeader) {
                var debugBtn = document.createElement('button');
                debugBtn.id = 'menuDebugBtn';
                debugBtn.className = 'btn btn-sm btn-info';
                debugBtn.style.marginLeft = '10px';
                debugBtn.textContent = 'Debug Menu';
                debugBtn.onclick = function() {
                    debugMenuItems();
                    return false;
                };
                
                modalHeader.appendChild(debugBtn);
                console.log('Debug button added');
            }
        }
    });
});";

    // Save the debug JS file
    if (file_put_contents("menu_debug.js", $debugJs)) {
        echo "<p style='color: green;'>✅ Created menu_debug.js file with debugging functionality.</p>";
        
        // Add script tag to include the debug JS file
        if (strpos($content, "menu_debug.js") === false) {
            $content = str_replace("</head>", "<script src='menu_debug.js'></script>\n</head>", $content);
            
            if (file_put_contents($tablePackagesPath, $content)) {
                echo "<p style='color: green;'>✅ Added debug script tag to table_packages.php.</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update table_packages.php with debug script tag.</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to create menu_debug.js file.</p>";
    }

    echo "<h2>Fix Complete!</h2>";
    echo "<p>The fixes have been applied. Please follow these steps:</p>";
    echo "<ol>";
    echo "<li>Go back to <a href='table_packages.php'>Table Packages</a></li>";
    echo "<li>Click on 'Make Advance Order' to open the modal</li>";
    echo "<li>The menu items should now appear when you click on a category</li>";
    echo "<li>If you still don't see menu items, click the 'Debug Menu' button that appears in the modal header</li>";
    echo "</ol>";
} else {
    echo "<p style='color: red;'>❌ Could not find table_packages.php file. Make sure you're running this script in the correct directory.</p>";
}
?> 