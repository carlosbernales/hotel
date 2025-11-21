<?php
// Ultra-basic fix - just displays instructions
?>
<!DOCTYPE html>
<html>
<head>
    <title>Console Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .code { background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; margin: 15px 0; white-space: pre-wrap; }
        h1 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Console Fix for Menu Items</h1>
    <p>Follow these simple steps to fix the menu items:</p>
    
    <ol>
        <li>Click this button: <a href="index.php?table_packages" target="_blank" class="btn">Open Table Packages</a></li>
        <li>When the page loads, press F12 or right-click and select "Inspect" to open developer tools</li>
        <li>Click on the "Console" tab</li>
        <li>Copy and paste this entire code block into the console:</li>
    </ol>
    
    <div class="code">// Menu Fix Script
$(document).ready(function() {
    console.log("Menu fix script loaded");
    
    // When modal opens
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log("Modal opened");
        
        // Menu items
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
            { id: 10, category_id: 5, name: 'Coffee', price: 180.00 }
        ];
        
        // Find or create menu container
        var menuContainer = $('#menuItems');
        if (menuContainer.length === 0) {
            $('#menuCategories').after('<div id="menuItems" class="mt-3"></div>');
            menuContainer = $('#menuItems');
        }
        
        // Function to show items
        function showItems(categoryId) {
            console.log("Showing items for category", categoryId);
            
            // Filter items
            var items = menuItems.filter(function(item) {
                return item.category_id == categoryId;
            });
            
            // Clear container
            menuContainer.empty();
            
            // Add items
            if (items.length > 0) {
                var html = '';
                items.forEach(function(item) {
                    html += '<div class="card mb-2">' +
                           '<div class="card-body">' +
                           '<h5 class="card-title">' + item.name + '</h5>' +
                           '<p class="card-text text-primary">₱' + item.price.toFixed(2) + '</p>' +
                           '<button type="button" class="btn btn-warning add-to-cart" ' +
                           'data-id="' + item.id + '" ' +
                           'data-name="' + item.name + '" ' +
                           'data-price="' + item.price + '">' +
                           '<i class="fa fa-shopping-cart"></i> Add to Cart</button>' +
                           '</div></div>';
                });
                menuContainer.html(html);
                
                // Add click handlers
                menuContainer.find('.add-to-cart').on('click', function() {
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    var price = $(this).data('price');
                    
                    if (typeof window.addToCart === 'function') {
                        window.addToCart(id, name, price);
                    } else {
                        console.log('Adding to cart:', name, price);
                        
                        var orderItems = $('#orderItems');
                        if (orderItems.length > 0) {
                            if (orderItems.find('.text-muted').length > 0) {
                                orderItems.empty();
                            }
                            
                            var html = '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                     '<div>' + name + ' <span class="badge badge-secondary">1</span></div>' +
                                     '<div>₱' + price.toFixed(2) + '</div>' +
                                     '</div>';
                            orderItems.append(html);
                            
                            var totalElem = $('#orderTotal');
                            if (totalElem.length > 0) {
                                var currentTotal = parseFloat(totalElem.text() || '0');
                                var newTotal = currentTotal + price;
                                totalElem.text(newTotal.toFixed(2));
                            }
                        }
                    }
                });
            } else {
                menuContainer.html('<div class="alert alert-info">No items found</div>');
            }
        }
        
        // Setup category buttons
        $('#menuCategories .category-btn').off('click.menufix').on('click.menufix', function() {
            $('#menuCategories .category-btn').removeClass('active');
            $(this).addClass('active');
            
            var categoryId = $(this).data('category-id');
            if (!categoryId) {
                categoryId = $(this).index() + 1;
            }
            
            showItems(categoryId);
        });
        
        // Show first category
        var firstBtn = $('#menuCategories .category-btn').first();
        if (firstBtn.length > 0) {
            firstBtn.addClass('active');
            showItems(1);
        }
    });
});</div>
    
    <ol start="5">
        <li>Press Enter to run the script</li>
        <li>Click the "Make Advance Order" button to open the modal</li>
        <li>Click on any category to see menu items</li>
    </ol>
    
    <p><strong>Note:</strong> You'll need to run this script again if you reload the page or navigate away. This is a temporary solution until we can implement a permanent fix.</p>
</body>
</html> 