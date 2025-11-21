<?php
// Diagnostic and repair script for table_packages.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security check - only allow from your IP
$allowed_ips = array('180.195.201.183');
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    echo "Access denied.";
    exit;
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Table Packages Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .box { background-color: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .success { background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; }
        .error { background-color: #f2dede; border: 1px solid #ebccd1; color: #a94442; }
        .warning { background-color: #fcf8e3; border: 1px solid #faebcc; color: #8a6d3b; }
        pre { background-color: #f5f5f5; padding: 10px; overflow: auto; }
        textarea { width: 100%; height: 300px; font-family: monospace; }
        .btn { padding: 10px 15px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Table Packages Page Fix</h1>";

// Step 1: Check if table_packages.php exists
echo "<div class='box'>";
echo "<h2>Step 1: Checking table_packages.php file</h2>";

if (file_exists('table_packages.php')) {
    echo "<p>✓ table_packages.php file found.</p>";
    
    // Create backup
    if (copy('table_packages.php', 'table_packages.php.bak')) {
        echo "<p>✓ Created backup of table_packages.php as table_packages.php.bak</p>";
    } else {
        echo "<p>⚠ Warning: Could not create backup.</p>";
    }
    
    // Check file content
    $content = file_get_contents('table_packages.php');
    if (empty($content)) {
        echo "<p>❌ Error: table_packages.php file is empty.</p>";
    } else {
        $file_size = strlen($content);
        echo "<p>✓ File size: " . $file_size . " bytes.</p>";
    }
} else {
    echo "<p>❌ Error: table_packages.php file not found.</p>";
}
echo "</div>";

// Step 2: Check for database connection in table_packages.php
echo "<div class='box'>";
echo "<h2>Step 2: Analyzing table_packages.php</h2>";

if (file_exists('table_packages.php')) {
    $content = file_get_contents('table_packages.php');
    
    // Look for common patterns
    $patterns = array(
        'database' => '/mysqli|PDO|connect|query|select|from/i',
        'html_structure' => '/<div|<table|<form/i',
        'javascript' => '/<script|function|ajax|jquery|\$/i'
    );
    
    foreach ($patterns as $key => $pattern) {
        if (preg_match($pattern, $content)) {
            echo "<p>✓ Found " . ucfirst($key) . " code.</p>";
        } else {
            echo "<p>❌ No " . ucfirst($key) . " code found.</p>";
        }
    }
}
echo "</div>";

// Step 3: Check related files for table packages
echo "<div class='box'>";
echo "<h2>Step 3: Checking for related files</h2>";

$related_files = array(
    'js/table_packages.js',
    'css/table_packages.css',
    'includes/table_packages_functions.php'
);

foreach ($related_files as $file) {
    if (file_exists($file)) {
        echo "<p>✓ Found related file: $file</p>";
    } else {
        echo "<p>ℹ️ Related file not found: $file</p>";
    }
}

echo "<p>Checking for potential JavaScript files:</p>";
$js_files = glob('js/*.js');
if (!empty($js_files)) {
    echo "<ul>";
    foreach ($js_files as $js_file) {
        echo "<li>$js_file</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No JavaScript files found in js directory.</p>";
}
echo "</div>";

// Step 4: Offer a basic replacement table_packages.php file
echo "<div class='box'>";
echo "<h2>Step 4: Basic table_packages.php template</h2>";

$basic_template = '<?php
// Table Packages Management Page

// Check if user is authorized
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once "config.php";

// Get all table packages
$query = "SELECT * FROM table_packages ORDER BY id DESC";
$result = mysqli_query($conn, $query);

?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Table Packages Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Table Packages</h6>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPackageModal">
                <i class="fas fa-plus"></i> Add New Package
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["name"]; ?></td>
                                <td><?php echo $row["description"]; ?></td>
                                <td>₱<?php echo number_format($row["price"], 2); ?></td>
                                <td>
                                    <?php if ($row["status"] == 1) { ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="editPackage(<?php echo $row[\'id\']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deletePackage(<?php echo $row[\'id\']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="openAdvanceOrder(<?php echo $row[\'id\']; ?>)">
                                        Make Advance Order
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Advance Order Modal -->
<div class="modal fade" id="advanceOrderModal" tabindex="-1" role="dialog" aria-labelledby="advanceOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advanceOrderModalLabel">Advance Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Menu Categories</h5>
                        <div id="menuCategories" class="list-group">
                            <!-- Categories will be loaded here -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <div id="orderItems">
                            <p class="text-muted">No items added yet.</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5>₱<span id="orderTotal">0.00</span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveOrderBtn">Save Order</button>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript functions for Table Packages
function editPackage(id) {
    // Open edit modal with package data
    alert("Edit package with ID: " + id);
}

function deletePackage(id) {
    // Confirm and delete package
    if (confirm("Are you sure you want to delete this package?")) {
        alert("Delete package with ID: " + id);
    }
}

function openAdvanceOrder(id) {
    // Open advance order modal
    $("#advanceOrderModal").modal("show");
    
    // Load menu categories
    $.ajax({
        url: "get_menu_categories.php",
        type: "GET",
        success: function(data) {
            $("#menuCategories").html(data);
            
            // Add click handler to category buttons
            $(".category-btn").click(function() {
                var categoryId = $(this).data("category-id");
                loadMenuItems(categoryId);
            });
            
            // Click first category by default
            $(".category-btn").first().click();
        },
        error: function() {
            alert("Error loading menu categories");
        }
    });
}

function loadMenuItems(categoryId) {
    // Load menu items for selected category
    $.ajax({
        url: "get_menu_items.php",
        type: "GET",
        data: { category_id: categoryId },
        success: function(data) {
            $("#menuItems").html(data);
            
            // Add click handler to add to cart buttons
            $(".add-to-cart").click(function() {
                var id = $(this).data("id");
                var name = $(this).data("name");
                var price = $(this).data("price");
                
                addToCart(id, name, price);
            });
        },
        error: function() {
            alert("Error loading menu items");
        }
    });
}

function addToCart(id, name, price) {
    // Add item to cart
    var orderItems = $("#orderItems");
    
    // Clear empty message if present
    if (orderItems.find(".text-muted").length > 0) {
        orderItems.empty();
    }
    
    // Add item to order
    var html = \'<div class="d-flex justify-content-between align-items-center mb-2">\' +
              \'<div>\' + name + \' <span class="badge badge-secondary">1</span></div>\' +
              \'<div>₱\' + parseFloat(price).toFixed(2) + \'</div>\' +
              \'</div>\';
    
    orderItems.append(html);
    
    // Update total
    var currentTotal = parseFloat($("#orderTotal").text() || 0);
    var newTotal = currentTotal + parseFloat(price);
    $("#orderTotal").text(newTotal.toFixed(2));
}

// Fix for menu items display when clicking on categories
$(document).ready(function() {
    $(document).on("shown.bs.modal", "#advanceOrderModal", function() {
        // Ensure menuItems container exists
        if ($("#menuItems").length === 0) {
            $("#menuCategories").after(\'<div id="menuItems" class="mt-3"></div>\');
        }
    });
});
</script>
';

echo "<p>Below is a basic template for table_packages.php that might work with your system. You can choose to restore this template if you want to try it:</p>";
echo "<form method='post'>";
echo "<textarea name='template_content'>" . htmlspecialchars($basic_template) . "</textarea>";
echo "<div class='warning box'>";
echo "<strong>Warning:</strong> This is a generic template. You may need to adjust it to match your specific database structure and layout.";
echo "</div>";
echo "<input type='submit' name='restore_template' value='Restore This Template' class='btn'>";
echo "</form>";
echo "</div>";

// Step 5: Process the template restoration if requested
if (isset($_POST['restore_template']) && isset($_POST['template_content'])) {
    echo "<div class='box success'>";
    echo "<h2>Applying Template</h2>";
    
    // Create another backup just to be safe
    if (file_exists('table_packages.php')) {
        copy('table_packages.php', 'table_packages.php.backup.' . date('Y-m-d-H-i-s'));
        echo "<p>✓ Created timestamped backup of current table_packages.php</p>";
    }
    
    // Write the new content
    if (file_put_contents('table_packages.php', $_POST['template_content'])) {
        echo "<p>✓ Successfully updated table_packages.php with the new template.</p>";
        echo "<p><a href='index.php?table_packages' class='btn'>Try Table Packages Now</a></p>";
    } else {
        echo "<p>❌ Error: Could not write to table_packages.php. Check file permissions.</p>";
    }
    
    echo "</div>";
}

// Step 6: Create a simple JavaScript fix directly
echo "<div class='box'>";
echo "<h2>JavaScript Fix for Menu Items</h2>";

$js_fix = "// Menu Fix Script for Advance Order Modal
$(document).ready(function() {
    console.log('Menu fix script loaded');
    
    // When modal is opened
    $(document).on('shown.bs.modal', '#advanceOrderModal', function() {
        console.log('Advance Order modal opened - applying fix');
        
        // Menu items data (hardcoded for immediate use)
        var menuItems = [
            { id: 1, category_id: 1, name: 'Hand-cut Potato Fries', price: 160.00 },
            { id: 2, category_id: 1, name: 'Mozzarella Stick', price: 150.00 },
            { id: 3, category_id: 1, name: 'Chicken Wings', price: 180.00 },
            { id: 4, category_id: 1, name: 'Spaghetti', price: 270.00 },
            { id: 5, category_id: 2, name: 'Salad', price: 200.00 },
            { id: 6, category_id: 2, name: 'Coconut Salad', price: 200.00 },
            { id: 7, category_id: 3, name: 'Pasta Dish', price: 250.00 },
            { id: 8, category_id: 4, name: 'Sandwich', price: 180.00 },
            { id: 9, category_id: 5, name: 'Coffee', price: 120.00 }
        ];
        
        // Create or get menuItems container
        if ($('#menuItems').length === 0) {
            $('#menuCategories').after('<div id=\"menuItems\" class=\"mt-3\"></div>');
        }
        
        // Function to display items for a category
        function showItems(categoryId) {
            console.log('Showing items for category', categoryId);
            
            // Filter items for this category
            var items = menuItems.filter(function(item) {
                return item.category_id == categoryId;
            });
            
            // Get the container and clear it
            var container = $('#menuItems');
            container.empty();
            
            if (items.length > 0) {
                // Build HTML for the items
                var html = '';
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
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
                }
                container.html(html);
                
                // Add click handlers for Add to Cart buttons
                container.find('.add-to-cart').on('click', function() {
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    var price = $(this).data('price');
                    
                    // Try to use existing function or implement a basic one
                    if (typeof window.addToCart === 'function') {
                        window.addToCart(id, name, price);
                    } else {
                        console.log('Adding to cart:', name, price);
                        
                        var orderItems = $('#orderItems');
                        if (orderItems.length > 0) {
                            // Clear 'No items' message if present
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
                container.html('<div class=\"alert alert-info\">No items found for this category</div>');
            }
        }
        
        // Set up click handlers for category buttons
        $('#menuCategories .category-btn').off('click.menufix').on('click.menufix', function() {
            // Update active state
            $('#menuCategories .category-btn').removeClass('active');
            $(this).addClass('active');
            
            // Get category ID
            var categoryId = $(this).data('category-id');
            if (!categoryId) {
                categoryId = $(this).index() + 1;
            }
            
            // Show items for this category
            showItems(categoryId);
        });
        
        // Activate first category by default
        setTimeout(function() {
            var firstCategory = $('#menuCategories .category-btn').first();
            if (firstCategory.length > 0) {
                firstCategory.addClass('active');
                var categoryId = firstCategory.data('category-id') || 1;
                showItems(categoryId);
            }
        }, 100);
    });
});";

echo "<p>You can also try adding this JavaScript code to your system to fix menu items display. Here are two ways to apply it:</p>";

echo "<h3>Option 1: Add to table_packages.php</h3>";
echo "<p>Edit table_packages.php and add this script at the end, just before the closing <?php ?> tag:</p>";
echo "<pre>&lt;script>
" . htmlspecialchars($js_fix) . "
&lt;/script></pre>";

echo "<h3>Option 2: Create a separate JS file</h3>";
echo "<form method='post'>";
echo "<input type='hidden' name='js_fix' value='" . htmlspecialchars($js_fix) . "'>";
echo "<input type='submit' name='create_js_fix' value='Create menu_fix.js File' class='btn'>";
echo "</form>";

// Process JS fix file creation if requested
if (isset($_POST['create_js_fix'])) {
    if (file_put_contents('menu_fix.js', $_POST['js_fix'])) {
        echo "<div class='success box' style='margin-top: 15px;'>";
        echo "<p>✓ Successfully created menu_fix.js</p>";
        echo "<p>Now add this line to the bottom of your table_packages.php file:</p>";
        echo "<pre>&lt;script src=\"menu_fix.js\">&lt;/script></pre>";
        echo "</div>";
    } else {
        echo "<div class='error box' style='margin-top: 15px;'>";
        echo "<p>❌ Error: Could not create menu_fix.js file. Check directory permissions.</p>";
        echo "</div>";
    }
}

echo "</div>";

// Final instructions
echo "<div class='box'>";
echo "<h2>Next Steps</h2>";
echo "<p>After applying any of the fixes above, try visiting the Table Packages page again to see if it works properly.</p>";
echo "<p><a href='index.php?table_packages' class='btn'>Go to Table Packages Page</a></p>";
echo "<p><a href='admin_emergency.php' class='btn' style='background-color: #f0ad4e;'>Return to Emergency Admin</a></p>";
echo "</div>";

echo "</body></html>";
?> 