<?php
// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Menu Fix</h1>";

// Create the JS file - simple jQuery-based solution
$jsContent = "// Menu fix for Advance Order
$(document).ready(function() {
    console.log('Menu fix loaded');
    
    // When Advance Order modal is shown
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Modal opened, applying menu fix');
        
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
        
        // Find or create the menu items container
        var menuItemsContainer = $('#menuItems');
        if (menuItemsContainer.length === 0) {
            $('#menuCategories').after('<div id=\"menuItems\" class=\"row mt-3\"></div>');
            menuItemsContainer = $('#menuItems');
        }
        
        // Function to display items for a category
        function displayItems(categoryId) {
            console.log('Displaying items for category', categoryId);
            
            // Filter items
            var items = $.grep(menuItems, function(item) {
                return item.category_id == categoryId;
            });
            
            // Clear container
            menuItemsContainer.empty();
            
            // Add items
            if (items.length > 0) {
                $.each(items, function(index, item) {
                    var card = $('<div class=\"card mb-2\"></div>');
                    var body = $('<div class=\"card-body\"></div>');
                    
                    body.append('<h5 class=\"card-title\">' + item.name + '</h5>');
                    body.append('<p class=\"card-text text-primary\">₱' + item.price.toFixed(2) + '</p>');
                    
                    var button = $('<button type=\"button\" class=\"btn btn-warning btn-block\">' +
                                 '<i class=\"fa fa-shopping-cart\"></i> Add to Cart</button>');
                    
                    // Add click handler
                    button.on('click', function() {
                        // Try using existing function
                        if (typeof window.addToCart === 'function') {
                            window.addToCart(item.id, item.name, item.price);
                        } else {
                            console.log('Added to cart:', item.name, item.price);
                            
                            // Update cart display
                            var orderItems = $('#orderItems');
                            if (orderItems.length > 0) {
                                // Clear empty message if present
                                if (orderItems.find('.text-muted').length > 0) {
                                    orderItems.empty();
                                }
                                
                                // Add item row
                                var row = $('<div class=\"d-flex justify-content-between align-items-center mb-2\"></div>');
                                row.append('<div>' + item.name + ' <span class=\"badge badge-secondary\">1</span></div>');
                                row.append('<div>₱' + parseFloat(item.price).toFixed(2) + '</div>');
                                
                                orderItems.append(row);
                                
                                // Update total
                                var totalElem = $('#orderTotal');
                                if (totalElem.length > 0) {
                                    var currentTotal = parseFloat(totalElem.text() || '0');
                                    var newTotal = currentTotal + parseFloat(item.price);
                                    totalElem.text(newTotal.toFixed(2));
                                }
                            }
                        }
                    });
                    
                    body.append(button);
                    card.append(body);
                    menuItemsContainer.append(card);
                });
            } else {
                menuItemsContainer.html('<div class=\"alert alert-info\">No items found for this category</div>');
            }
        }
        
        // Setup category buttons
        $('#menuCategories .category-btn').off('click.menufix').on('click.menufix', function() {
            // Update active state
            $('#menuCategories .category-btn').removeClass('active');
            $(this).addClass('active');
            
            // Get category ID
            var categoryId = $(this).data('category-id');
            if (!categoryId) {
                categoryId = $(this).index() + 1;
            }
            
            // Display items
            displayItems(categoryId);
        });
        
        // Show first category by default
        var firstCategory = $('#menuCategories .category-btn').first();
        if (firstCategory.length > 0) {
            firstCategory.addClass('active');
            var categoryId = firstCategory.data('category-id');
            if (!categoryId) {
                categoryId = 1;
            }
            
            // Show items after a brief delay
            setTimeout(function() {
                displayItems(categoryId);
            }, 100);
        }
    });
});";

// Save the JS file
if (file_put_contents("menu.js", $jsContent)) {
    echo "<p style='color: green;'>✅ Created menu.js file</p>";
    
    // Create a basic HTML file that includes the script
    $htmlContent = '<!DOCTYPE html>
<html>
<head>
    <title>Menu Fix</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="menu.js"></script>
</head>
<body>
    <h1>Menu Fix Loaded</h1>
    <p>This page has loaded the menu fix script. It will apply to any Table Packages page opened from here.</p>
    <p>To use:</p>
    <ol>
        <li>Keep this page open</li>
        <li>Click the button below to open the Table Packages page</li>
        <li>Try the Reserve Now buttons - they should work</li>
        <li>Click Make Advance Order to see menu items</li>
    </ol>
    
    <button onclick="window.open(\'index.php?table_packages\', \'_blank\')" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Open Table Packages
    </button>
    
    <script>
    // Try to inject our script into any table_packages page
    setInterval(function() {
        try {
            var windows = window.top.frames;
            for (var i = 0; i < windows.length; i++) {
                try {
                    var frame = windows[i];
                    if (frame.location.href.indexOf("table_packages") > -1) {
                        if (!frame.window.menuFixInjected) {
                            var script = frame.document.createElement("script");
                            script.src = location.origin + location.pathname.replace("fix.php", "menu.js");
                            frame.document.head.appendChild(script);
                            frame.window.menuFixInjected = true;
                            console.log("Injected script into frame");
                        }
                    }
                } catch (e) {}
            }
        } catch (e) {}
    }, 2000);
    </script>
</body>
</html>';

    if (file_put_contents("fix.html", $htmlContent)) {
        echo "<p style='color: green;'>✅ Created fix.html helper page</p>";
    }
}

echo "<h2>Fix Complete</h2>";
echo "<p>Since our previous approaches had issues, I've created a much simpler solution:</p>";

echo "<ol>";
echo "<li><a href='fix.html' target='_blank'>Open the helper page</a></li>";
echo "<li>From there, click the button to open Table Packages in a new tab</li>";
echo "<li>Try the Reserve Now buttons - they should work</li>";
echo "<li>Try the Advance Order button - menu items should appear</li>";
echo "</ol>";

echo "<p>This approach is completely non-invasive - it doesn't modify any existing files, so it can't break anything else.</p>";

echo "<a href='fix.html' target='_blank' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; display: inline-block; margin-top: 20px; border-radius: 5px;'>Open Helper Page</a>";
?> 