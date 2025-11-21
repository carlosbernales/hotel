<?php
session_start();
require "db.php";

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<script>alert('Invalid order ID'); window.location.href='ProcessingOrder.php';</script>";
    exit;
}

$order_id = $_GET['order_id'];

// Fetch the original order details to display
$orderQuery = "SELECT orders.*, CONCAT(users.firstname, ' ', users.lastname) as customer_name 
               FROM orders 
               LEFT JOIN users ON orders.user_id = users.id 
               WHERE orders.id = ?";
$stmt = $connection->prepare($orderQuery);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();
$orderData = $orderResult->fetch_assoc();

if (!$orderData) {
    echo "<script>alert('Order not found'); window.location.href='ProcessingOrder.php';</script>";
    exit;
}

// Fetch all available menu items
$menuQuery = "SELECT * FROM menu_items WHERE status = 'active' ORDER BY category, name";
$menuItems = $connection->query($menuQuery);

// Fetch all available add-ons
$addonsQuery = "SELECT * FROM addons WHERE status = 'active'";
$addons = $connection->query($addonsQuery);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_items'])) {
    // Start transaction
    $connection->begin_transaction();
    
    try {
        // Get the original order total
        $originalTotal = $orderData['total_amount'];
        $additionalTotal = 0;
        
        // Process each added item
        foreach ($_POST['items'] as $itemId => $itemData) {
            if ($itemData['quantity'] > 0) {
                $quantity = intval($itemData['quantity']);
                
                // Get item price from database to ensure accuracy
                $itemQuery = "SELECT price FROM menu_items WHERE id = ?";
                $stmt = $connection->prepare($itemQuery);
                $stmt->bind_param("i", $itemId);
                $stmt->execute();
                $itemResult = $stmt->get_result();
                $itemInfo = $itemResult->fetch_assoc();
                
                if (!$itemInfo) continue;
                
                $unitPrice = $itemInfo['price'];
                $itemTotal = $unitPrice * $quantity;
                $additionalTotal += $itemTotal;
                
                // Insert the order item
                $insertItemQuery = "INSERT INTO order_items (order_id, item_id, item_name, quantity, unit_price) 
                                   VALUES (?, ?, (SELECT name FROM menu_items WHERE id = ?), ?, ?)";
                $stmt = $connection->prepare($insertItemQuery);
                $stmt->bind_param("iiidi", $order_id, $itemId, $itemId, $quantity, $unitPrice);
                $stmt->execute();
                $orderItemId = $connection->insert_id;
                
                // Process addons if any
                if (isset($itemData['addons']) && is_array($itemData['addons'])) {
                    foreach ($itemData['addons'] as $addonId) {
                        // Get addon price from database to ensure accuracy
                        $addonQuery = "SELECT name, price FROM addons WHERE id = ?";
                        $stmt = $connection->prepare($addonQuery);
                        $stmt->bind_param("i", $addonId);
                        $stmt->execute();
                        $addonResult = $stmt->get_result();
                        $addonInfo = $addonResult->fetch_assoc();
                        
                        if (!$addonInfo) continue;
                        
                        $addonPrice = $addonInfo['price'];
                        $additionalTotal += $addonPrice * $quantity;
                        
                        // Insert the addon
                        $insertAddonQuery = "INSERT INTO order_item_addons (order_item_id, addon_id, addon_name, addon_price) 
                                           VALUES (?, ?, ?, ?)";
                        $stmt = $connection->prepare($insertAddonQuery);
                        $stmt->bind_param("iisd", $orderItemId, $addonId, $addonInfo['name'], $addonPrice);
                        $stmt->execute();
                    }
                }
            }
        }
        
        // Update the order total
        $newTotal = $originalTotal + $additionalTotal;
        
        // Calculate new discount if applicable
        $discountType = $orderData['discount_type'];
        $discountAmount = $orderData['discount_amount'];
        
        if ($discountType === 'senior_citizen' || $discountType === 'pwd') {
            $discountAmount = $newTotal * 0.20; // Recalculate 20% discount
        }
        
        $updateOrderQuery = "UPDATE orders SET total_amount = ?, discount_amount = ? WHERE id = ?";
        $stmt = $connection->prepare($updateOrderQuery);
        $stmt->bind_param("ddi", $newTotal, $discountAmount, $order_id);
        $stmt->execute();
        
        // Commit transaction
        $connection->commit();
        
        echo "<script>alert('Additional items added successfully!'); window.location.href='ProcessingOrder.php';</script>";
        exit;
    } catch (Exception $e) {
        // Rollback if anything fails
        $connection->rollback();
        echo "<script>alert('Error adding items: " . $e->getMessage() . "'); window.location.href='ProcessingOrder.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Additional Orders</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
</head>
<body>
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="ProcessingOrder.php">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
                </a></li>
                <li><a href="ProcessingOrder.php">Processing Orders</a></li>
                <li class="active">Add Additional Orders</li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Add Items to Order #<?php echo htmlspecialchars($order_id); ?></h4>
                        <p>Customer: <?php echo htmlspecialchars($orderData['customer_name'] ?? 'N/A'); ?></p>
                        <p>Current Total: ₱<?php echo htmlspecialchars(number_format($orderData['total_amount'], 2)); ?></p>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Select Additional Items</h4>
                                    
                                    <?php
                                    $currentCategory = '';
                                    while ($item = $menuItems->fetch_assoc()) {
                                        if ($item['category'] != $currentCategory) {
                                            if ($currentCategory != '') {
                                                echo '</div>'; // Close previous category
                                            }
                                            $currentCategory = $item['category'];
                                            echo '<h5>' . htmlspecialchars(ucfirst($currentCategory)) . '</h5>';
                                            echo '<div class="category-items">';
                                        }
                                    ?>
                                        <div class="panel panel-default menu-item">
                                            <div class="panel-heading">
                                                <h5 class="panel-title">
                                                    <?php echo htmlspecialchars($item['name']); ?> - 
                                                    ₱<?php echo htmlspecialchars(number_format($item['price'], 2)); ?>
                                                </h5>
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label>Quantity:</label>
                                                    <input type="number" class="form-control" 
                                                           name="items[<?php echo $item['id']; ?>][quantity]" 
                                                           min="0" value="0">
                                                </div>
                                                
                                                <?php
                                                // Reset addon cursor and fetch all addons for each item
                                                $addons->data_seek(0);
                                                if ($addons->num_rows > 0) {
                                                    echo '<div class="form-group">';
                                                    echo '<label>Add-ons:</label>';
                                                    
                                                    while ($addon = $addons->fetch_assoc()) {
                                                        echo '<div class="checkbox">';
                                                        echo '<label>';
                                                        echo '<input type="checkbox" name="items[' . $item['id'] . '][addons][]" value="' . $addon['id'] . '">';
                                                        echo htmlspecialchars($addon['name']) . ' (+₱' . htmlspecialchars(number_format($addon['price'], 2)) . ')';
                                                        echo '</label>';
                                                        echo '</div>';
                                                    }
                                                    
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    if ($currentCategory != '') {
                                        echo '</div>'; // Close last category
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" name="add_items" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Items to Order
                                </button>
                                <a href="ProcessingOrder.php" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Processing Orders
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Enable/disable addon checkboxes based on quantity
            $('input[type="number"]').on('change', function() {
                var quantity = $(this).val();
                var addonCheckboxes = $(this).closest('.panel-body').find('input[type="checkbox"]');
                
                if (parseInt(quantity) > 0) {
                    addonCheckboxes.prop('disabled', false);
                } else {
                    addonCheckboxes.prop('disabled', true).prop('checked', false);
                }
            });
            
            // Initialize all addon checkboxes as disabled
            $('input[type="checkbox"]').prop('disabled', true);
        });
    </script>
</body>
</html> 