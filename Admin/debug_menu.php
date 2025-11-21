<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu Debug</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Menu Debug</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Test API Directly</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button id="testGetCategories" class="btn btn-primary">Test Categories</button>
                            <button id="testGetItems" class="btn btn-secondary ml-2">Test Items</button>
                        </div>
                        <div class="mt-3">
                            <h4>Response:</h4>
                            <pre id="apiResponse" class="border p-3" style="min-height: 200px; max-height: 400px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Menu Interface Test</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="menuCategories">
                            <!-- Categories will appear here -->
                        </div>
                        <div class="row" id="menuItems">
                            <!-- Menu items will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Console Output</h3>
                    </div>
                    <div class="card-body">
                        <pre id="consoleOutput" class="border p-3" style="min-height: 200px; max-height: 400px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Capture console.log
    (function() {
        const oldConsoleLog = console.log;
        console.log = function(...args) {
            oldConsoleLog.apply(console, args);
            const output = args.map(arg => {
                if (typeof arg === 'object') {
                    return JSON.stringify(arg, null, 2);
                } else {
                    return arg;
                }
            }).join(' ');
            $('#consoleOutput').append(output + '\n');
        };
    })();
    
    $(document).ready(function() {
        console.log('Debug page loaded');
        
        // Test categories API
        $('#testGetCategories').click(function() {
            console.log('Testing categories API...');
            $.get('get_menu_data.php', { action: 'categories' })
                .done(function(data) {
                    console.log('Categories received:', data);
                    $('#apiResponse').text(JSON.stringify(data, null, 2));
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching categories:', textStatus, errorThrown);
                    $('#apiResponse').text('Error: ' + textStatus + '\n' + errorThrown);
                });
        });
        
        // Test items API
        $('#testGetItems').click(function() {
            // First get categories to get the first category ID
            $.get('get_menu_data.php', { action: 'categories' })
                .done(function(categories) {
                    if (categories.length > 0) {
                        const categoryId = categories[0].id;
                        console.log('Testing items API with category ID:', categoryId);
                        
                        $.get('get_menu_data.php', { action: 'items', category_id: categoryId })
                            .done(function(data) {
                                console.log('Items received:', data);
                                $('#apiResponse').text(JSON.stringify(data, null, 2));
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                console.error('Error fetching items:', textStatus, errorThrown);
                                $('#apiResponse').text('Error: ' + textStatus + '\n' + errorThrown);
                            });
                    } else {
                        console.error('No categories found');
                        $('#apiResponse').text('Error: No categories found');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching categories:', textStatus, errorThrown);
                    $('#apiResponse').text('Error: ' + textStatus + '\n' + errorThrown);
                });
        });
        
        // Load the actual menu interface
        function loadMenuCategories() {
            console.log('Loading menu categories...');
            $.get('get_menu_data.php', { action: 'categories' })
                .done(function(categories) {
                    console.log('Categories received:', categories);
                    const container = $('#menuCategories');
                    container.empty();
                    
                    if (categories.length === 0) {
                        container.append('<div class="alert alert-warning">No menu categories found</div>');
                        return;
                    }
                    
                    categories.forEach((category, index) => {
                        container.append(`
                            <button class="btn ${index === 0 ? 'btn-primary' : 'btn-outline-secondary'} mr-2 mb-2" 
                                    data-category-id="${category.id}">
                                ${category.display_name || category.name}
                            </button>
                        `);
                    });
                    
                    // Set up click handler
                    container.find('button').click(function() {
                        container.find('button').removeClass('btn-primary').addClass('btn-outline-secondary');
                        $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                        loadMenuItems($(this).data('category-id'));
                    });
                    
                    if (categories.length > 0) {
                        loadMenuItems(categories[0].id);
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching categories:', textStatus, errorThrown);
                    $('#menuCategories').html('<div class="alert alert-danger">Error loading menu categories</div>');
                });
        }
        
        function loadMenuItems(categoryId) {
            console.log('Loading menu items for category:', categoryId);
            $.get('get_menu_data.php', { action: 'items', category_id: categoryId })
                .done(function(items) {
                    console.log('Items received:', items);
                    const container = $('#menuItems');
                    container.empty();
                    
                    if (items.length === 0) {
                        container.append('<div class="alert alert-warning">No menu items found for this category</div>');
                        return;
                    }
                    
                    items.forEach(item => {
                        container.append(`
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="${item.image_path || 'images/default-food.jpg'}" 
                                         class="card-img-top" 
                                         alt="${item.name}"
                                         style="height: 150px; object-fit: cover;"
                                         onerror="this.src='images/default-food.jpg'">
                                    <div class="card-body">
                                        <h5 class="card-title">${item.name}</h5>
                                        <p class="card-text text-success">₱${parseFloat(item.price).toFixed(2)}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching items:', textStatus, errorThrown);
                    $('#menuItems').html('<div class="alert alert-danger">Error loading menu items</div>');
                });
        }
        
        // Initialize
        loadMenuCategories();
    });
    </script>
</body>
</html> 