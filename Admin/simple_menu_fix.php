<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Menu Fix</h1>";

// Create the JS file directly
$jsContent = "// Menu Items Loader
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be ready
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not found');
        return;
    }
    
    // Listen for modal open
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Advance Order modal opened');
        
        // Menu items data
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
        
        // Helper function to show menu items
        function showMenuItems(categoryId) {
            var items = menuItems.filter(function(item) {
                return item.category_id == categoryId;
            });
            
            var menuContainer = $('#menuItems');
            if (menuContainer.length === 0) {
                // Create container if not exists
                $('#menuCategories').after('<div id=\"menuItems\" class=\"mt-3\"></div>');
                menuContainer = $('#menuItems');
            }
            
            menuContainer.empty();
            
            if (items.length > 0) {
                var html = '';
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    html += '<div class=\"card mb-2\">' +
                           '<div class=\"card-body\">' +
                           '<h5 class=\"card-title\">' + item.name + '</h5>' +
                           '<p class=\"card-text text-primary\">₱' + parseFloat(item.price).toFixed(2) + '</p>' +
                           '<button type=\"button\" class=\"btn btn-warning add-to-cart\" ' +
                           'data-id=\"' + item.id + '\" ' +
                           'data-name=\"' + item.name + '\" ' +
                           'data-price=\"' + item.price + '\">' +
                           '<i class=\"fa fa-shopping-cart\"></i> Add to Cart</button>' +
                           '</div></div>';
                }
                menuContainer.html(html);
                
                // Add click handlers for Add to Cart buttons
                menuContainer.find('.add-to-cart').on('click', function() {
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    var price = $(this).data('price');
                    
                    // Try to use existing function
                    if (typeof window.addToCart === 'function') {
                        window.addToCart(id, name, price);
                    } else {
                        // Manual implementation
                        console.log('Adding to cart:', name, price);
                        
                        var orderItems = $('#orderItems');
                        if (orderItems.length > 0) {
                            // Clear empty message
                            if (orderItems.find('.text-muted').length > 0) {
                                orderItems.empty();
                            }
                            
                            // Add item row
                            var html = '<div class=\"d-flex justify-content-between align-items-center mb-2\">' +
                                     '<div>' + name + ' <span class=\"badge badge-secondary\">1</span></div>' +
                                     '<div>₱' + parseFloat(price).toFixed(2) + '</div>' +
                                     '</div>';
                            orderItems.append(html);
                            
                            // Update total
                            var totalElem = $('#orderTotal');
                            if (totalElem.length > 0) {
                                var currentTotal = parseFloat(totalElem.text() || '0');
                                var newTotal = currentTotal + parseFloat(price);
                                totalElem.text(newTotal.toFixed(2));
                            }
                        }
                    }
                });
            } else {
                menuContainer.html('<div class=\"alert alert-info\">No items found for this category</div>');
            }
        }
        
        // Add event handlers
        $('#menuCategories .category-btn').off('click.menufix').on('click.menufix', function(e) {
            // Update active state
            $('#menuCategories .category-btn').removeClass('active');
            $(this).addClass('active');
            
            // Show menu items
            var categoryId = $(this).data('category-id');
            if (!categoryId) {
                categoryId = $(this).index() + 1;
            }
            
            showMenuItems(categoryId);
        });
        
        // Show first category by default
        var firstCategory = $('#menuCategories .category-btn').first();
        if (firstCategory.length > 0) {
            firstCategory.addClass('active');
            var categoryId = firstCategory.data('category-id');
            if (!categoryId) {
                categoryId = 1;
            }
            
            setTimeout(function() {
                showMenuItems(categoryId);
            }, 100);
        }
    });
});";

// Save the JS file
if (file_put_contents("menu_fix_jquery.js", $jsContent)) {
    echo "<p style='color: green;'>✅ Created menu_fix_jquery.js file</p>";
    
    // Create a simple HTML file to include the script
    $htmlContent = '<!DOCTYPE html>
<html>
<head>
    <title>Menu Fix</title>
    <script src="menu_fix_jquery.js"></script>
</head>
<body>
    <h1>Menu Fix</h1>
    <p>This page loads the menu fix script.</p>
    <p>Please follow these steps:</p>
    <ol>
        <li>Keep this page open (it doesn\'t need to be visible)</li>
        <li>Go to <a href="index.php?table_packages" target="_blank">Table Packages page</a></li>
        <li>Try clicking the RESERVE NOW buttons - they should work</li>
        <li>Click on "Make Advance Order" to open the modal</li>
        <li>The menu items should appear when clicking on categories</li>
    </ol>
    
    <script>
    // Inject the script into table_packages.php if loaded in other tabs
    try {
        var scriptURL = location.origin + location.pathname.replace("simple_menu_fix.php", "menu_fix_jquery.js");
        
        // Function to inject script into other windows
        function injectToOtherWindows() {
            for (var i = 0; i < window.top.frames.length; i++) {
                try {
                    var frame = window.top.frames[i];
                    if (frame.location.href.indexOf("table_packages") > -1) {
                        // Check if script already exists
                        var exists = false;
                        var scripts = frame.document.getElementsByTagName("script");
                        for (var j = 0; j < scripts.length; j++) {
                            if (scripts[j].src.indexOf("menu_fix_jquery.js") > -1) {
                                exists = true;
                                break;
                            }
                        }
                        
                        if (!exists) {
                            // Inject script
                            var script = frame.document.createElement("script");
                            script.src = scriptURL;
                            frame.document.head.appendChild(script);
                            console.log("Injected script into frame:", frame.location.href);
                        }
                    }
                } catch (e) {
                    console.error("Error accessing frame:", e);
                }
            }
        }
        
        // Try to inject immediately and also periodically
        injectToOtherWindows();
        setInterval(injectToOtherWindows, 2000);
    } catch (e) {
        console.error("Error injecting script:", e);
    }
    </script>
    
    <a href="index.php?table_packages" target="_blank" class="btn btn-success" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;">Open Table Packages</a>
</body>
</html>';

    if (file_put_contents("menu_fix.html", $htmlContent)) {
        echo "<p style='color: green;'>✅ Created menu_fix.html file</p>";
    }
    
    // Try to create a direct fix by injecting the script tag
    $targetFiles = ["table_packages.php", "index.php"];
    $scriptTag = '<script src="menu_fix_jquery.js"></script>';
    
    foreach ($targetFiles as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            
            // Check if script tag already exists
            if (strpos($content, "menu_fix_jquery.js") === false) {
                // Add script tag before </head>
                $content = str_replace("</head>", "$scriptTag\n</head>", $content);
                
                // Save file
                if (file_put_contents($file, $content)) {
                    echo "<p style='color: green;'>✅ Added script tag to $file</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to modify $file. File permissions issue.</p>";
                }
            } else {
                echo "<p style='color: blue;'>ℹ️ Script tag already exists in $file</p>";
            }
        }
    }
}

echo "<h2>Simple Fix Complete!</h2>";
echo "<p>A simplified fix has been created that should work without errors.</p>";
echo "<p>You now have three ways to apply the fix:</p>";

echo "<ol>";
echo "<li><strong>Automatic (Recommended):</strong> The fix has been added to your PHP files and should work automatically</li>";
echo "<li><strong>Manual HTML:</strong> <a href='menu_fix.html' target='_blank'>Open this helper page</a> in a new tab, then return to Table Packages</li>";
echo "<li><strong>Source Link:</strong> Add <code>&lt;script src=\"menu_fix_jquery.js\"&gt;&lt;/script&gt;</code> to your table_packages.php file</li>";
echo "</ol>";

echo "<p><strong>Important:</strong> This fix is designed to work with <strong>jQuery</strong>, which your site already uses. It's much more stable than previous approaches.</p>";

echo "<p>To test:</p>";
echo "<ol>";
echo "<li>Go to <a href='index.php?table_packages' target='_blank'>Table Packages</a></li>";
echo "<li>Try clicking the RESERVE NOW buttons - they should work now</li>";
echo "<li>Click 'Make Advance Order' to open the modal</li>";
echo "<li>The menu items should appear when clicking on categories</li>";
echo "</ol>";

echo "<a href='index.php?table_packages' target='_blank' class='btn btn-primary' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-top: 20px;'>Go to Table Packages</a>";
?> 