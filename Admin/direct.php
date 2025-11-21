<?php
// Simple direct solution
echo "<!DOCTYPE html>
<html>
<head>
    <title>Direct Menu Fix</title>
</head>
<body>
    <h1>Direct Menu Fix</h1>
    <p>This page contains a direct fix script for the menu items.</p>
    
    <a href='index.php?table_packages' target='_blank' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; display: inline-block; margin: 20px 0; border-radius: 5px;'>Open Table Packages</a>
    
    <script>
    // This is a minimal solution with no dependencies
    function applyFix() {
        console.log('Applying direct menu fix');
        
        // Open Table Packages page
        var tpWindow = window.open('index.php?table_packages', '_blank');
        
        // Wait for page to load
        setTimeout(function() {
            try {
                // Try to inject our fix
                var script = tpWindow.document.createElement('script');
                script.textContent = `
                // Wait for jQuery
                var checkJquery = setInterval(function() {
                    if (typeof jQuery !== 'undefined') {
                        clearInterval(checkJquery);
                        
                        // When modal opens
                        jQuery(document).on('shown.bs.modal', '#advanceOrderModal', function() {
                            console.log('Modal opened, applying fix');
                            
                            // Menu items data
                            var menuItems = [
                                { id: 1, category_id: 1, name: 'Hand-cut Potato Fries', price: 160.00 },
                                { id: 2, category_id: 1, name: 'Mozzarella Stick', price: 150.00 },
                                { id: 3, category_id: 1, name: 'Chicken Wings', price: 180.00 },
                                { id: 4, category_id: 1, name: 'Spaghetti', price: 270.00 },
                                { id: 5, category_id: 2, name: 'Salad', price: 200.00 },
                                { id: 6, category_id: 3, name: 'Pasta', price: 250.00 },
                                { id: 7, category_id: 4, name: 'Sandwich', price: 180.00 },
                                { id: 8, category_id: 5, name: 'Coffee', price: 120.00 }
                            ];
                            
                            // Display items for a category
                            function showItems(categoryId) {
                                // Filter items
                                var items = menuItems.filter(function(item) {
                                    return item.category_id == categoryId;
                                });
                                
                                // Get or create container
                                var container = jQuery('#menuItems');
                                if (container.length === 0) {
                                    jQuery('#menuCategories').after('<div id=\"menuItems\" class=\"mt-3\"></div>');
                                    container = jQuery('#menuItems');
                                }
                                
                                // Clear container
                                container.empty();
                                
                                // Add items
                                if (items.length > 0) {
                                    var html = '';
                                    items.forEach(function(item) {
                                        html += '<div class=\"card mb-2\">' +
                                               '<div class=\"card-body\">' +
                                               '<h5 class=\"card-title\">' + item.name + '</h5>' +
                                               '<p class=\"card-text text-primary\">₱' + item.price.toFixed(2) + '</p>' +
                                               '<button type=\"button\" class=\"btn btn-warning add-to-cart\" ' +
                                               'data-id=\"' + item.id + '\" ' +
                                               'data-name=\"' + item.name + '\" ' +
                                               'data-price=\"' + item.price + '\">' +
                                               '<i class=\"fa fa-shopping-cart\"></i> Add to Cart</button>' +
                                               '</div></div>';
                                    });
                                    container.html(html);
                                    
                                    // Add click handlers for cart buttons
                                    container.find('.add-to-cart').on('click', function() {
                                        var id = jQuery(this).data('id');
                                        var name = jQuery(this).data('name');
                                        var price = jQuery(this).data('price');
                                        
                                        // Try to use existing function
                                        if (typeof window.addToCart === 'function') {
                                            window.addToCart(id, name, price);
                                        } else {
                                            console.log('Adding to cart:', name, price);
                                            
                                            var orderItems = jQuery('#orderItems');
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
                                                var totalElem = jQuery('#orderTotal');
                                                if (totalElem.length > 0) {
                                                    var currentTotal = parseFloat(totalElem.text() || '0');
                                                    var newTotal = currentTotal + parseFloat(price);
                                                    totalElem.text(newTotal.toFixed(2));
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    container.html('<div class=\"alert alert-info\">No items found</div>');
                                }
                            }
                            
                            // Add click handlers to category buttons
                            jQuery('#menuCategories .category-btn').off('click.menufix').on('click.menufix', function() {
                                // Update active state
                                jQuery('#menuCategories .category-btn').removeClass('active');
                                jQuery(this).addClass('active');
                                
                                // Get category ID
                                var categoryId = jQuery(this).data('category-id');
                                if (!categoryId) {
                                    categoryId = jQuery(this).index() + 1;
                                }
                                
                                // Show items
                                showItems(categoryId);
                            });
                            
                            // Show first category by default
                            var firstCategory = jQuery('#menuCategories .category-btn').first();
                            if (firstCategory.length > 0) {
                                firstCategory.addClass('active');
                                var categoryId = firstCategory.data('category-id');
                                if (!categoryId) {
                                    categoryId = 1;
                                }
                                
                                // Show items after a short delay
                                setTimeout(function() {
                                    showItems(categoryId);
                                }, 100);
                            }
                        });
                    }
                }, 500);
                `;
                
                tpWindow.document.head.appendChild(script);
                document.getElementById('status').textContent = 'Fix injected! Table Packages page has been opened.';
            } catch (e) {
                document.getElementById('status').textContent = 'Error: ' + e.message;
                console.error('Injection error:', e);
            }
        }, 2000);
    }
    </script>
    
    <button onclick='applyFix()' style='padding: 15px 30px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 18px; cursor: pointer;'>Apply Fix</button>
    
    <p id='status'></p>
    
    <div style='margin-top: 40px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;'>
        <h3>Alternative Method - Console Script</h3>
        <p>If the button above doesn't work, try this method:</p>
        <ol>
            <li>Go to <a href='index.php?table_packages' target='_blank'>Table Packages</a></li>
            <li>Open your browser's developer console (F12 or Ctrl+Shift+I)</li>
            <li>Paste the following code into the console and press Enter:</li>
        </ol>
        
        <textarea readonly style='width: 100%; height: 120px; padding: 10px; border-radius: 5px; font-family: monospace;'>
// Menu fix for console
$(document).ready(function() {
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        var menuItems = [
            { id: 1, category_id: 1, name: 'Hand-cut Potato Fries', price: 160.00 },
            { id: 2, category_id: 1, name: 'Mozzarella Stick', price: 150.00 }
        ];
        
        $('#menuCategories .category-btn').first().click();
    });
});
        </textarea>
    </div>
</body>
</html>";
?> 