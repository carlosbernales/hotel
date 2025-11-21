<?php
// Session is already started in index.php
// Check if user is logged in and is a cashier
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    // Redirect to login page if not logged in or not a cashier
    header('Location: ../../login.php');
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];

// Include database connection
require_once 'db.php';

// Verify user exists and has appropriate role
$userQuery = "SELECT * FROM userss WHERE id = ? AND user_type = 'cashier'";
$stmt = $con->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    // Handle invalid user
    header('Location: ../../login.php');
    exit();
}

// Fetch menu categories
$categoryQuery = "SELECT * FROM menu_categories ORDER BY id";
$categoryResult = $con->query($categoryQuery);
$categories = [];
while($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch menu items with their categories and descriptions
$menuQuery = "SELECT mi.*, mc.name as category_name 
             FROM menu_items mi 
             JOIN menu_categories mc ON mi.category_id = mc.id";
$menuResult = $con->query($menuQuery);
$menuItems = [];
while($row = $menuResult->fetch_assoc()) {
    $menuItems[] = $row;
}

// Fetch menu item addons
$addonsQuery = "SELECT * FROM menu_items_addons";
$addonsResult = $con->query($addonsQuery);
$addons = [];
while($row = $addonsResult->fetch_assoc()) {
    $addons[$row['menu_item_id']][] = $row;
}

// Fetch tables from the database
$tables = [];
$tablesQuery = "SELECT * FROM table_number ORDER BY table_number ASC";
$tablesResult = $con->query($tablesQuery);

if ($tablesResult) {
    while($row = $tablesResult->fetch_assoc()) {
        $tables[] = [
            'id' => $row['id'],
            'table_number' => $row['table_number'],
            'is_occupied' => ($row['status'] === 'occupied')
        ];
    }
} else {
    // Fallback if table doesn't exist yet
    for ($i = 1; $i <= 10; $i++) {
        $tables[] = [
            'id' => $i,
            'table_number' => $i,
            'is_occupied' => false
        ];
    }
}

// Convert tables to JSON for JavaScript use
$tablesJson = json_encode($tables);

// Convert the PHP arrays to JSON for JavaScript use
$menuData = json_encode([
    'categories' => $categories,
    'items' => $menuItems,
    'addons' => $addons
]);

// Make the user ID available to JavaScript
?>
<script>
    const currentUserId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
    // Check if user is logged in
    if (!currentUserId) {
        // Redirect to login or show error
        window.location.href = 'login.php';
    }
    
    // Make tables data available to JavaScript
    const tablesData = <?php echo $tablesJson; ?>;
    
    // Function to mark a table as occupied
    function markTableAsOccupied(tableId) {
        // Make AJAX request to update table status in database
        fetch('update_table_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'table_id=' + tableId + '&status=occupied'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating table status:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Function to mark a table as available
    function markTableAsAvailable(tableId) {
        // Make AJAX request to update table status in database
        fetch('update_table_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'table_id=' + tableId + '&status=available'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating table status:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Simplified table data refresh function to only get available tables for the dropdown
    function refreshTablesData() {
        return fetch('get_tables.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data.tables;
                } else {
                    console.error('Error fetching tables:', data.message);
                    return tablesData; // Fall back to current data
                }
            })
            .catch(error => {
                console.error('Error:', error);
                return tablesData; // Fall back to current data
            });
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela POS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* Main Layout */
        .main-container {
            padding: 2rem;
            max-width: 100%;
            margin: 0 auto;
            margin-left: 60px;
            margin-right: 370px;
            position: relative;
        }

        /* Menu Categories */
        .menu-categories {
            margin-bottom: 2rem;
            width: 100%;
            z-index: 1;
        }

        .menu-categories h3 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
            padding-left: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .category-list {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-left: -170px;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .category-list li {
            padding: 1rem 2rem;
            cursor: pointer;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .category-list li:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .category-list li.active {
            background: #007bff;
            color: white;
        }

        /* Menu Content */
        .section-title {
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
            padding-left: 0;
            font-size: 2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
            padding-left: 0;
        }

        .menu-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .menu-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .menu-item-details {
            padding: 1.2rem;
            background: white;
        }

        .menu-item-details h3 {
            color: #333;
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
        }

        .menu-item-details p {
            color: #28a745;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .add-to-cart {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .add-to-cart:hover {
            background: #ffb300;
            transform: scale(1.05);
        }

        .add-to-cart i {
            font-size: 0.9rem;
        }

        /* Current Order Panel */
        .current-order {
            position: fixed;
            top: 65px;
            right: 0;
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
            overflow: hidden;
            z-index: 2;
        }

        .current-order h2 {
            padding: 1.2rem;
            margin: 0;
            border-bottom: 1px solid #eee;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .order-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .order-item {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .order-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .order-item-name {
            font-weight: 600;
            color: #333;
            font-size: 1.2rem;
        }

        .order-item-price {
            color: #28a745;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .order-item-category {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .quantity-btn.minus {
            background: #dc3545;
            color: white;
        }

        .quantity-btn.plus {
            background: #28a745;
            color: white;
        }

        .quantity-display {
            padding: 0 0.5rem;
            font-weight: 600;
        }

        .addons-section {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #eee;
        }

        .addons-section h4 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .addon-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.3rem;
        }

        .addon-item input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .order-summary {
            background: white;
            padding: 1rem;
            border-top: 1px solid #eee;
            margin-top: auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            color: #333;
            font-size: 1.2rem;
            font-weight: 500;
        }

        .place-order-btn {
            width: 100%;
            padding: 1rem;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 1.3rem;
            cursor: pointer;
            margin-top: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .place-order-btn:hover {
            background: #218838;
        }

        /* Customer Details Modal */
        .modal.fade .modal-dialog {
            transform: scale(0.7);
            transition: all 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Add this to your existing styles */
        .loading-animation {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Add these modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            position: relative;
            width: 500px;
            margin: 30px auto;
            background: #fff;
            border-radius: 5px;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #e5e5e5;
            text-align: right;
        }

        .order-items-summary {
            margin-bottom: 15px;
        }

        .order-summary-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-summary-item:last-child {
            border-bottom: none;
        }

        .close {
            font-size: 24px;
            font-weight: bold;
            line-height: 1;
            color: #000;
            opacity: 0.5;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .close:hover {
            opacity: 0.75;
        }

        /* Add to your existing styles */
        .discount-options {
            margin-top: 10px;
        }

        .form-check {
            margin-bottom: 8px;
        }

        .form-check-input {
            margin-right: 8px;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }

        /* Add this CSS for the loading animation */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .required-field {
            border-color: red !important;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 4px;
        }

        .confirm-order-popup {
            padding: 2rem;
            max-height: 90vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .confirmation-details {
            text-align: left;
            padding-bottom: 2rem;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            margin-left: 0px;
            border-bottom: 2px solid #eee;
        }
        .payment-details {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            margin-bottom: 2rem;
        }
        .total-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            margin-bottom: 1rem;
        }
        .swal2-actions {
            margin-top: 0;
            padding: 1rem;
            border-top: 1px solid #eee;
            width: 100%;
            justify-content: flex-end;
            gap: 1rem;
        }
        .swal2-confirm, .swal2-cancel {
            margin: 0 !important;
        }
        .swal2-popup {
            padding-bottom: 0;
        }
        .swal2-html-container {
            margin: 0;
            overflow-y: auto;
            max-height: calc(90vh - 150px);
        }

        /* Image Container */
        .menu-item-image {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .menu-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-item:hover .menu-item-image img {
            transform: scale(1.05);
        }

        /* Overlay */
        .menu-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .menu-item:hover .menu-item-overlay {
            opacity: 1;
        }

        /* Item Details */
        .menu-item-details {
            padding: 1.2rem;
            background: white;
        }

        .item-name {
            color: #333;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-price-wrapper {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .peso-sign {
            color: #28a745;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .item-price {
            color: #28a745;
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Add to Cart Button */
        .add-to-cart {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .add-to-cart:hover {
            background: #ffb300;
            transform: scale(1.05);
        }

        .add-to-cart i {
            font-size: 0.9rem;
        }

        /* Category List Styling */
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 1rem 1rem 250px;
            margin-bottom: 1.5rem;
            justify-content: center;
        }

        .category-list li {
            padding: 1rem 2rem;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-weight: 500;
        }

        .category-list li:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .category-list li.active {
            background: #007bff;
            color: white;
        }

        /* Section Title */
        .section-title {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin: 2.5rem 0;
            padding-left: 0px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding-left: 0;
            }
            
            .category-list {
                padding-left: 0;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 60px;
                margin-right: 0;
                padding: 1rem;
            }
            
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                padding-left: 0;
            }
            
            .category-list {
                padding-left: 0;
            }
            
            .section-title {
                padding-left: 0;
            }
        }

        /* Button Group in Overlay */
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            width: 80%;
        }

        /* Description Button */
        .view-description {
            background: #17a2b8;
                color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            gap: 0.8rem;
            width: 100%;
        }

        .view-description:hover {
            background: #138496;
            transform: scale(1.05);
        }

        /* Description Modal Styles */
        .description-modal {
            font-family: Arial, sans-serif;
        }

        .description-popup {
            padding: 2rem;
            border-radius: 15px;
        }

        .description-title {
            color: #333;
            font-size: 1.8rem;
            font-weight: 700;
                margin-bottom: 1rem;
            }
            
        .description-content {
            color: #666;
            font-size: 1.2rem;
            line-height: 1.6;
        }

        /* Adjust overlay to accommodate both buttons */
        .menu-item-overlay {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Description Preview */
        .item-description-preview {
            color: #666;
            font-size: 0.9rem;
            margin: 0.5rem 0;
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Description Modal Styles */
        .menu-description {
            color: #333;
            font-size: 1.1rem;
            line-height: 1.6;
            text-align: left;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .description-popup {
            max-width: 500px;
            padding: 2rem;
        }

        .description-title {
            color: #333;
            font-size: 1.8rem;
            font-weight: 700;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .description-content {
            margin: 0 !important;
            padding: 0 1rem;
        }

        /* View Description Button */
        /* Add-ons Modal Styles */
        .addons-modal {
            max-height: 60vh;
            overflow-y: auto;
            padding: 10px 5px;
        }
        
        .addons-list {
            margin-bottom: 20px;
        }
        
        .addon-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .addon-item:hover {
            background: #e9ecef;
        }
        
        .addon-info {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }
        
        .addon-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .addon-quantity {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }
        
        .addon-quantity .quantity-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            padding: 0;
        }
        
        .addon-quantity .quantity-btn:hover {
            background: #e9ecef;
        }
        
        .addon-quantity .quantity-display {
            min-width: 30px;
            text-align: center;
            margin: 0 5px;
        }
        
        .addons-summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .addons-summary h4 {
            margin-top: 0;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .selected-addon {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .addons-total {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #dee2e6;
            font-size: 1.1em;
        }
        
        .no-addons {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 10px 0;
        }
        
        /* Edit button styles */
        .edit-item-btn {
            background-color: #ffc107;
            color: #212529;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            margin: 0 5px;
            transition: background-color 0.2s;
        }
        
        .edit-item-btn:hover {
            background-color: #e0a800;
        }
        
        .edit-item-btn i {
            font-size: 12px;
        }
        
        /* Selected add-ons in order item */
        .selected-addons-list {
            margin-top: 8px;
            padding-left: 15px;
        }
        
        .selected-addon-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 13px;
            color: #495057;
        }
        
        .addon-price {
            color: #28a745;
            font-weight: 500;
        }
        
        .view-description {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .view-description:hover {
            background: #138496;
            transform: scale(1.05);
        }

        /* Add this to the didOpen callback in submitOrder function */
        .format-hint {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
            margin-top: 0.5rem;
        }

        .format-hint span {
            display: block;
            line-height: 1.4;
        }

        .format-hint {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .format-hint strong {
            color: #333;
            display: block;
            margin-bottom: 0.5rem;
        }

        .format-hint small {
            color: #666;
            display: block;
            line-height: 1.4;
        }

        #id-format-hint {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        /* Add these styles to the existing <style> section */
        .order-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }

        .remove-item-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .remove-item-btn:hover {
            background: #c82333;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Add these styles to your existing CSS */
        .package-description {
            display: none;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
        }

        #table-selection select {
            margin-bottom: 0.5rem !important;
        }

        /* Add these styles to your existing CSS */
        #table-selection {
            transition: all 0.3s ease;
        }

        #table-selection.show {
            display: block !important;
        }

        .package-description {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
            font-size: 0.875rem;
        }

        #swal-table {
            width: 100%;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
            margin-bottom: 0.5rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="main-container">
        <!-- Menu Categories as horizontal list -->
        <div class="menu-categories">
            <h3>Menu Categories</h3>
            <ul class="category-list">
                <?php foreach($categories as $category): ?>
                    <li data-category="<?php echo $category['id']; ?>" 
                        class="<?php echo $category['id'] == 1 ? 'active' : ''; ?>">
                        <?php echo $category['display_name']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Menu Content -->
        <h2 class="section-title">SMALL PLATES</h2>
        <div class="menu-grid" id="menu-items">
            <!-- Items will be loaded dynamically -->
        </div>

        <!-- Current Order Panel -->
        <div class="current-order">
            <h2>Current Order</h2>
            <div class="order-items">
                <!-- Order items will be dynamically added here -->
            </div>
            <div class="order-summary">
                <div class="total-row">
                    <span>Total Items:</span>
                    <span id="total-items">0</span>
                </div>
                <div class="total-row">
                    <span>Total Amount:</span>
                    <span id="total-amount">₱0.00</span>
                </div>
                <button class="place-order-btn" onclick="submitOrder()">PLACE ORDER</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Replace the static menu data with the PHP data
    const menuData = <?php echo $menuData; ?>;

    // Handle Add to Cart
    let order = [];

    // Add these variables at the top of your script
    let discountType = null;
    let discountAmount = 0;

    function displayMenuItems(categoryId) {
        const menuGrid = document.getElementById('menu-items');
        const items = menuData.items.filter(item => item.category_id == categoryId);
        
        // Find the selected category name
        const selectedCategory = menuData.categories.find(cat => cat.id == categoryId);
        
        // Update the section title
        document.querySelector('.section-title').textContent = selectedCategory.display_name.toUpperCase();
        
        menuGrid.innerHTML = items.map(item => `
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/Admin/${item.image_path}" alt="${item.name}" onerror="this.src='/Admin/uploads/menus/default-menu-item.jpg'">
                    <div class="menu-item-overlay">
                        <div class="button-group">
                            <button class="view-description" 
                                    onclick="showDescription('${item.name}', '${item.description ? item.description.replace(/'/g, "\\'"): 'No description available.'}')">
                                <i class="fa-solid fa-circle-info"></i> Details
                            </button>
                            <button class="add-to-cart" 
                                    data-item-id="${item.id}"
                                    data-item="${item.name}" 
                                    data-price="${item.price}" 
                                    data-category="${item.category_name}">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <div class="menu-item-details">
                    <h3 class="item-name">${item.name}</h3>
                    <p class="item-description-preview">${item.description ? (item.description.length > 50 ? item.description.substring(0, 50) + '...' : item.description) : ''}</p>
                    <div class="item-price-wrapper">
                        <span class="peso-sign">₱</span>
                        <span class="item-price">${parseFloat(item.price).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    let currentItemIndex = -1;
    
    function showAddonsModal(itemId, name, price, category, isExisting = false, index = -1) {
        const itemAddons = menuData.addons[itemId] || [];
        
        if (itemAddons.length === 0) {
            addToOrder(itemId, name, price, category, isExisting, index);
            return;
        }
        
        currentItemIndex = index !== -1 ? index : order.length;
        
        Swal.fire({
            title: `Customize ${name}`,
            html: `
                <div class="addons-modal">
                    <div class="addons-list">
                        ${itemAddons.map(addon => `
                            <div class="addon-item">
                                <div class="addon-info">
                                    <input type="checkbox" 
                                           id="modal-addon-${addon.id}"
                                           class="addon-checkbox"
                                           data-id="${addon.id}"
                                           data-name="${addon.name}"
                                           data-price="${addon.price}">
                                    <label for="modal-addon-${addon.id}">
                                        ${addon.name} (+₱${parseFloat(addon.price).toFixed(2)})
                                    </label>
                                </div>
                                <div class="addon-quantity">
                                    <button type="button" class="quantity-btn minus" onclick="updateAddonQty('${addon.id}', -1)">-</button>
                                    <span id="addon-qty-${addon.id}" class="quantity-display">0</span>
                                    <button type="button" class="quantity-btn plus" onclick="updateAddonQty('${addon.id}', 1)">+</button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="addons-summary">
                        <h4>Selected Add-ons:</h4>
                        <div id="selected-addons">No add-ons selected</div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: isExisting ? 'Update Item' : 'Add to Cart',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            didOpen: () => {
                // Initialize checkboxes and quantities
                if (isExisting && index !== -1) {
                    const item = order[index];
                    item.addons.forEach(addon => {
                        const checkbox = document.getElementById(`modal-addon-${addon.id}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            const qtyDisplay = document.getElementById(`addon-qty-${addon.id}`);
                            if (qtyDisplay) qtyDisplay.textContent = addon.qty || 1;
                        }
                    });
                    updateSelectedAddons();
                }
                
                // Add event listeners for checkboxes
                document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedAddons);
                });
            },
            preConfirm: () => {
                const selectedAddons = [];
                document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
                    const qty = parseInt(document.getElementById(`addon-qty-${checkbox.dataset.id}`).textContent) || 1;
                    selectedAddons.push({
                        id: checkbox.dataset.id,
                        name: checkbox.dataset.name,
                        price: parseFloat(checkbox.dataset.price),
                        qty: qty
                    });
                });
                
                if (isExisting && index !== -1) {
                    // Update existing item
                    order[index].addons = selectedAddons;
                    updateOrder();
                    saveFormData();
                    return false;
                } else {
                    // Add new item
                    return { selectedAddons };
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                addToOrder(itemId, name, price, category, false, -1, result.value.selectedAddons);
            }
        });
    }
    
    function updateAddonQty(addonId, change) {
        const qtyDisplay = document.getElementById(`addon-qty-${addonId}`);
        if (!qtyDisplay) return;
        
        let qty = parseInt(qtyDisplay.textContent) || 0;
        qty += change;
        if (qty < 0) qty = 0;
        
        qtyDisplay.textContent = qty;
        updateSelectedAddons();
    }
    
    function updateSelectedAddons() {
        const selectedContainer = document.getElementById('selected-addons');
        if (!selectedContainer) return;
        
        const selectedAddons = [];
        let totalAddonPrice = 0;
        
        document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
            const qty = parseInt(document.getElementById(`addon-qty-${checkbox.dataset.id}`).textContent) || 1;
            const price = parseFloat(checkbox.dataset.price) * qty;
            totalAddonPrice += price;
            selectedAddons.push(`
                <div class="selected-addon">
                    <span>${checkbox.dataset.name} x${qty}</span>
                    <span>₱${price.toFixed(2)}</span>
                </div>
            `);
        });
        
        if (selectedAddons.length === 0) {
            selectedContainer.innerHTML = '<div class="no-addons">No add-ons selected</div>';
        } else {
            selectedContainer.innerHTML = `
                ${selectedAddons.join('')}
                <div class="addons-total">
                    <strong>Total Add-ons:</strong>
                    <strong>₱${totalAddonPrice.toFixed(2)}</strong>
                </div>
            `;
        }
    }
    
    function addToOrder(itemId, name, price, category, isExisting = false, index = -1, selectedAddons = []) {
        if (isExisting && index !== -1) {
            // Update existing item
            order[index].qty++;
        } else {
            // Add new item
            order.push({
                id: itemId,
                name: name,
                price: parseFloat(price),
                category: category,
                qty: 1,
                addons: selectedAddons,
                availableAddons: menuData.addons[itemId] || []
            });
        }
        
        updateOrder();
        saveFormData();
        
        // Show success message
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `${name} ${isExisting ? 'updated in' : 'added to'} cart`,
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
    }

    function updateOrder() {
        const orderList = document.querySelector('.order-items');
        
        orderList.innerHTML = order.map((item, index) => `
            <div class="order-item">
                <div class="order-item-header">
                    <span class="order-item-name">${item.name}</span>
                    <span class="order-item-price">₱${(item.price * item.qty).toFixed(2)}</span>
                </div>
                <div class="order-item-category">
                    <i class="fa-solid fa-tag"></i> ${item.category}
                </div>
                <div class="order-item-controls">
                    <div class="quantity-controls">
                        <button class="quantity-btn minus" onclick="decrementQty(${index})">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <span class="quantity-display">${item.qty}</span>
                        <button class="quantity-btn plus" onclick="incrementQty(${index})">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <button class="edit-item-btn" onclick="editItem(${index})">
                        <i class="fa-solid fa-pencil"></i> Edit
                    </button>
                    <button class="remove-item-btn" onclick="removeItem(${index})">
                        <i class="fa-solid fa-trash"></i> Remove
                    </button>
                </div>
                ${item.availableAddons.length > 0 ? `
                    <div class="addons-section">
                        <h4><i class="fa-solid fa-utensils"></i> Add-ons:</h4>
                        ${item.addons.length > 0 ? `
                            <div class="selected-addons-list">
                                ${item.addons.map(addon => `
                                    <div class="selected-addon-item">
                                        <span>${addon.name} x${addon.qty || 1}</span>
                                        <span class="addon-price">+₱${(addon.price * (addon.qty || 1)).toFixed(2)}</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : 'No add-ons selected'}
                    </div>
                ` : ''}
            </div>
        `).join('');

        updateTotals();
    }

    function toggleAddon(itemIndex, addonId, addonName, addonPrice) {
        const item = order[itemIndex];
        const addonIndex = item.addons.findIndex(a => a.id === addonId);
        
        if (addonIndex === -1) {
            item.addons.push({ 
                id: addonId, 
                name: addonName, 
                price: addonPrice,
                qty: 1
            });
        } else {
            item.addons.splice(addonIndex, 1);
        }
        
        updateOrder();
        saveFormData();
    }

    function updateTotals() {
        const totalItems = document.getElementById('total-items');
        const totalAmount = document.getElementById('total-amount');
        
        const totalQty = order.reduce((total, item) => total + item.qty, 0);
        const subtotal = calculateSubtotal();
        const discount = discountAmount > 0 ? subtotal * discountAmount : 0;
        const finalTotal = subtotal - discount;

        totalItems.textContent = totalQty;
        totalAmount.textContent = `₱${finalTotal.toFixed(2)}`;
    }

    function incrementQty(index) {
        order[index].qty++;
        updateOrder();
        saveFormData();
    }

    function decrementQty(index) {
        if (order[index].qty > 1) {
            order[index].qty--;
            updateOrder();
            saveFormData();
        }
    }
    
    function editItem(index) {
        const item = order[index];
        showAddonsModal(item.id, item.name, item.price, item.category, true, index);
    }

    // Document Ready function
    document.addEventListener('DOMContentLoaded', function() {
        // Initial load of available tables
        refreshTablesData();
        // Display initial category
        displayMenuItems(menuData.categories[0].id);
        
        // Category selection
        const categoryButtons = document.querySelectorAll('.category-list li');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Display items for selected category
                displayMenuItems(this.dataset.category);
            });
        });
        
        // Add to cart
        document.getElementById('menu-items').addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-cart')) {
                const button = e.target;
                addToOrder(
                    button.dataset.itemId,
                    button.dataset.item,
                    button.dataset.price,
                    button.dataset.category
                );
            }
        });

        // Handle discount radio button changes
        document.querySelectorAll('input[name="discount"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const idNumberGroup = document.getElementById('id-number-group');
                if (this.value === 'senior' || this.value === 'pwd') {
                    idNumberGroup.style.display = 'block';
                } else {
                    idNumberGroup.style.display = 'none';
                }
            });
        });
    });

    // Update the submitOrder function to use SweetAlert2 directly
    function submitOrder() {
        if (order.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Cart',
                text: 'Please add items to your cart before placing an order.'
            });
            return;
        }

        const savedData = loadFormData();

        // Show order summary and collect information using SweetAlert2
        Swal.fire({
            title: 'Order Summary',
            html: `
                <div class="order-summary-content">
                    ${generateOrderSummaryHTML()}
                    <div class="form-group mt-3">
                        <label for="swal-contact">Contact Number (Optional)</label>
                        <input type="tel" id="swal-contact" class="swal2-input" placeholder="Enter contact number (optional)">
                    </div>
                    <div class="form-group mt-3">
                        <label for="swal-nickname">Nickname (Optional)</label>
                        <input type="text" id="swal-nickname" class="swal2-input" placeholder="Enter customer nickname">
                    </div>
                    <div class="form-group mt-3">
                        <label for="swal-discount">Discount Type</label>
                        <select id="swal-discount" class="swal2-input">
                            <option value="none">No Discount</option>
                            <option value="senior">Senior Citizen</option>
                            <option value="pwd">PWD</option>
                        </select>
                    </div>
                    <div class="form-group mt-3" id="swal-id-group" style="display: none;">
                        <label for="swal-id">ID Number</label>
                        <input type="text" 
                               id="swal-id" 
                               class="swal2-input" 
                               placeholder="Enter ID Number">
                        <div id="id-format-hint" class="format-hint mt-2">
                            <div id="senior-format">
                                <strong>Senior Citizen ID Format:</strong> SCDFI-XXXXXXXX<br>
                                <small class="text-muted">Example: SCDFI-12345ABC</small>
                            </div>
                            <div id="pwd-format">
                                <strong>PWD ID Format:</strong> RR-PPMM-BBB-NNNNNNN<br>
                                <small class="text-muted">
                                    RR = Region Code (e.g., NC)<br>
                                    PPMM = Province/Municipality Code (e.g., 1234)<br>
                                    BBB = Barangay Code (e.g., ABC)<br>
                                    NNNNNNN = PWD Number (e.g., 1234567)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="swal-payment">Payment Method</label>
                        <select id="swal-payment" class="swal2-input">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="swal-order-type">Order Type</label>
                        <select id="swal-order-type" class="swal2-input" required>
                            <option value="">Select Order Type</option>
                            <option value="dine-in">Dine-in</option>
                            <option value="takeout">Takeout</option>
                        </select>
                    </div>
                    <div class="form-group mt-3" id="table-selection" style="display: none;">
                        <label for="swal-table">Select Table</label>
                        <select id="swal-table" class="swal2-input">
                            <option value="">Select Table Number</option>
                            <?php foreach($tables as $table): ?>
                                <option value="<?php echo $table['id']; ?>">
                                    Table <?php echo $table['table_number']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="table-loading" style="display: none; text-align: center; margin-top: 10px;">
                            <i class="fas fa-spinner fa-spin"></i> Loading available tables...
                        </div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Place Order',
            cancelButtonText: 'Cancel',
            customClass: {
                container: 'custom-swal-container',
                popup: 'custom-swal-popup',
                header: 'custom-swal-header',
                title: 'custom-swal-title',
                closeButton: 'custom-swal-close',
                content: 'custom-swal-content',
                input: 'custom-swal-input',
                actions: 'custom-swal-actions',
                confirmButton: 'custom-swal-confirm',
                cancelButton: 'custom-swal-cancel'
            },
            width: '500px',
            didOpen: () => {
                // Restore saved form data if available
                if (savedData) {
                    document.getElementById('swal-contact').value = savedData.contactNumber || '';
                    document.getElementById('swal-nickname').value = savedData.nickname || '';
                    document.getElementById('swal-discount').value = savedData.discountType || 'none';
                    document.getElementById('swal-id').value = savedData.idNumber || '';
                    document.getElementById('swal-payment').value = savedData.paymentMethod || '';
                    document.getElementById('swal-order-type').value = savedData.orderType || '';
                    document.getElementById('swal-table').value = savedData.tableNumber || '';
                    
                    // Trigger both discount type and order type change events
                    const event = new Event('change');
                    document.getElementById('swal-discount').dispatchEvent(event);
                    document.getElementById('swal-order-type').dispatchEvent(event);
                }

                // Add event listeners for form fields
                const formFields = ['swal-contact', 'swal-nickname', 'swal-discount', 'swal-id', 'swal-payment', 'swal-order-type', 'swal-table'];
                formFields.forEach(fieldId => {
                    document.getElementById(fieldId)?.addEventListener('change', saveFormData);
                    document.getElementById(fieldId)?.addEventListener('input', saveFormData);
                });

                // Add event listener for order type selection
                document.getElementById('swal-order-type').addEventListener('change', function() {
                    const tableSelection = document.getElementById('table-selection');
                    if (this.value === 'dine-in') {
                        tableSelection.style.display = 'block';
                        // Populate table dropdown with available tables
                        populateTableDropdown();
                    } else {
                        tableSelection.style.display = 'none';
                        document.getElementById('swal-table').value = '';
                    }
                    saveFormData();
                });
                
                // Function to populate table dropdown with available tables
                function populateTableDropdown() {
                    const tableSelect = document.getElementById('swal-table');
                    const tableLoading = document.getElementById('table-loading');
                    
                    // Show loading indicator
                    tableLoading.style.display = 'block';
                    
                    // Clear existing options except the first one
                    while (tableSelect.options.length > 1) {
                        tableSelect.remove(1);
                    }
                    
                    // Fetch fresh table data from server
                    refreshTablesData().then(updatedTables => {
                        console.log('Populating dropdown with tables:', updatedTables);
                        
                        // Add available tables to dropdown
                        let availableTablesCount = 0;
                        
                        updatedTables.forEach(table => {
                            const tableId = table.id.toString();
                            // Only show tables that are not currently occupied
                            if (!table.is_occupied) {
                                const option = document.createElement('option');
                                option.value = tableId;
                                option.textContent = 'Table ' + table.table_number;
                                tableSelect.appendChild(option);
                                availableTablesCount++;
                            }
                        });
                        
                        // If no available tables, add a message
                        if (availableTablesCount === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No tables available';
                            option.disabled = true;
                            tableSelect.appendChild(option);
                        }
                        
                        // Hide loading indicator
                        tableLoading.style.display = 'none';
                    }).catch(error => {
                        console.error('Error populating table dropdown:', error);
                        
                        // Add a message about the error
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Error loading tables';
                        option.disabled = true;
                        tableSelect.appendChild(option);
                        
                        // Hide loading indicator
                        tableLoading.style.display = 'none';
                    });
                }

                // Trigger order type change event if dine-in is selected
                const orderType = document.getElementById('swal-order-type').value;
                if (orderType === 'dine-in') {
                    const tableSelection = document.getElementById('table-selection');
                    tableSelection.style.display = 'block';
                }

                // Add custom styles to the modal
                const style = document.createElement('style');
                style.textContent = `
                    .custom-swal-popup {
                        padding: 1.5rem;
                        max-height: 90vh;
                        overflow-y: auto;
                    }
                    .custom-swal-title {
                        font-size: 1.5rem;
                        color: #333;
                        margin-bottom: 1rem;
                        padding-bottom: 0.5rem;
                        border-bottom: 2px solid #eee;
                    }
                    .custom-swal-content {
                        margin: 1rem 0;
                    }
                    .order-summary-content {
                        max-height: 60vh;
                        overflow-y: auto;
                        padding-right: 10px;
                    }
                    .order-items-summary {
                        background: #f8f9fa;
                        padding: 1rem;
                        border-radius: 8px;
                        margin-bottom: 1.5rem;
                    }
                    .swal2-input, .swal2-select {
                        width: 100% !important;
                        margin: 0.5rem 0 !important;
                    }
                    .btn-group {
                        display: flex;
                        gap: 0.5rem;
                        margin: 0.5rem 0;
                        flex-wrap: wrap;
                    }
                    .btn-outline-primary {
                        flex: 1;
                        min-width: 120px;
                    }
                    .form-group {
                        margin-bottom: 1.5rem;
                    }
                    .form-group label {
                        display: block;
                        margin-bottom: 0.5rem;
                        color: #555;
                        font-weight: 500;
                    }
                    .custom-swal-confirm {
                        background-color: #28a745 !important;
                        padding: 10px 24px !important;
                    }
                    .custom-swal-cancel {
                        background-color: #dc3545 !important;
                        padding: 10px 24px !important;
                    }
                    .order-summary-item {
                        padding: 0.75rem;
                        border-bottom: 1px solid #dee2e6;
                    }
                    .order-summary-item:last-child {
                        border-bottom: none;
                    }
                    .addons-summary {
                        margin-top: 0.5rem;
                        padding-left: 1rem;
                    }
                    .small.text-muted {
                        color: #6c757d;
                        font-size: 0.875rem;
                    }
                `;
                document.head.appendChild(style);

                // Add event listener for discount type changes
                document.getElementById('swal-discount').addEventListener('change', function() {
                    const idGroup = document.getElementById('swal-id-group');
                    const idFormatHint = document.getElementById('id-format-hint');
                    const seniorFormat = document.getElementById('senior-format');
                    const pwdFormat = document.getElementById('pwd-format');
                    
                    if (this.value === 'senior' || this.value === 'pwd') {
                        idGroup.style.display = 'block';
                        idFormatHint.style.display = 'block';
                        
                        // Show relevant format hint
                        if (this.value === 'senior') {
                            seniorFormat.style.display = 'block';
                            pwdFormat.style.display = 'none';
                        } else {
                            seniorFormat.style.display = 'none';
                            pwdFormat.style.display = 'block';
                        }
                    } else {
                        idGroup.style.display = 'none';
                        idFormatHint.style.display = 'none';
                    }
                    saveFormData();
                });

                // Trigger the change event if a discount type is already selected
                const discountSelect = document.getElementById('swal-discount');
                if (discountSelect.value === 'senior' || discountSelect.value === 'pwd') {
                    discountSelect.dispatchEvent(new Event('change'));
                }
            },
            preConfirm: () => {
                const contactNumber = document.getElementById('swal-contact').value;
                const nickname = document.getElementById('swal-nickname').value;
                const paymentMethod = document.getElementById('swal-payment').value;
                const discountType = document.getElementById('swal-discount').value;
                const idNumber = document.getElementById('swal-id').value;
                const orderType = document.getElementById('swal-order-type').value;
                const tableNumber = document.getElementById('swal-table').value;

                // Validate form
                const errors = validateOrderInputs(contactNumber, paymentMethod, discountType, idNumber, orderType);
                if (errors.length > 0) {
                    Swal.showValidationMessage(errors.join('<br>'));
                    return false;
                }

                return {
                    customerName: nickname || 'N/A',
                    contactNumber,
                    nickname,
                    paymentMethod,
                    discountType,
                    idNumber,
                    orderType,
                    tableNumber
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processOrderSubmission(result.value);
                // Clear saved data after successful submission
                clearFormData();
            }
        });
    }

    // Helper function to generate order summary HTML
    function generateOrderSummaryHTML() {
        const summaryItems = order.map(item => `
            <div class="order-summary-item">
                <div class="d-flex justify-content-between">
                    <span><strong>${item.name}</strong> x ${item.qty}</span>
                    <span>₱${(item.price * item.qty).toFixed(2)}</span>
                </div>
                ${item.addons.length > 0 ? `
                    <div class="addons-summary">
                        ${item.addons.map(addon => `
                            <div class="small text-muted">+ ${addon.name} (₱${addon.price.toFixed(2)})</div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `).join('');

        const totalQty = order.reduce((total, item) => total + item.qty, 0);
        const subtotal = calculateSubtotal();

        return `
            <div class="order-items-summary">
                ${summaryItems}
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total Items:</strong>
                    <span>${totalQty}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <strong>Total Amount:</strong>
                    <span>₱${subtotal.toFixed(2)}</span>
                </div>
            </div>
        `;
    }

    // Helper function to validate order inputs
    function validateOrderInputs(contactNumber, paymentMethod, discountType, idNumber, orderType) {
        const errors = [];

        if (contactNumber && !/^09\d{9}$/.test(contactNumber)) {
            errors.push('If provided, contact number must be a valid 11-digit number starting with 09');
        }

        if (!paymentMethod) {
            errors.push('Payment method is required');
        }

        if (discountType !== 'none' && !idNumber) {
            errors.push('ID number is required when applying a discount');
        }

        if (discountType !== 'none' && idNumber) {
            if (discountType === 'senior' && !/^SCDFI-[A-Z0-9]{8}$/i.test(idNumber)) {
                errors.push('Invalid Senior Citizen ID format (SCDFI-XXXXXXXX)');
            } else if (discountType === 'pwd' && !/^[A-Z]{2}-\d{4}-[A-Z]{3}-\d{7}$/i.test(idNumber)) {
                errors.push('Invalid PWD ID format (RR-PPMM-BBB-NNNNNNN)');
            }
        }

        if (!orderType) {
            errors.push('Please select an order type (Dine-in or Takeout)');
        }

        if (orderType === 'dine-in') {
            const tableNumber = document.getElementById('swal-table').value;
            if (!tableNumber) {
                errors.push('Please select a table number for dine-in orders');
            }
        }

        return errors;
    }

    // Function to process the order submission
    function processOrderSubmission(formData) {
        // If table was selected, mark it as occupied
        if (formData.orderType === 'dine-in' && formData.tableNumber) {
            markTableAsOccupied(formData.tableNumber);
        }
        
        // Calculate totals
        const subtotal = calculateSubtotal();
        let discount = 0;
        
        // Calculate discount based on type (20% for both PWD and Senior)
        if (formData.discountType === 'pwd' || formData.discountType === 'senior') {
            discount = subtotal * 0.20; // 20% discount
        }
        
        // Calculate VAT (12% of subtotal after discount)
        const vatRate = 0.12; // 12% VAT
        const vatAmount = (subtotal - discount) * vatRate;
        
        // Calculate final total (subtotal - discount + VAT)
        const finalTotal = subtotal - discount + vatAmount;

        // Show confirmation modal
        Swal.fire({
            title: 'Confirm Order Details',
            html: `
                <div class="confirmation-details">
                    <div class="order-section">
                        <h3 class="section-title">Order Items</h3>
                        <div class="items-list">
                            ${order.map(item => `
                                <div class="item-detail">
                                    <div class="d-flex justify-content-between">
                                        <span class="item-name">${item.name} × ${item.qty}</span>
                                        <span class="item-price">₱${(item.price * item.qty).toFixed(2)}</span>
                                    </div>
                                    ${item.addons.length > 0 ? `
                                        <div class="addons-list">
                                            ${item.addons.map(addon => `
                                                <div class="addon-detail">
                                                    <small>+ ${addon.name}</small>
                                                    <small>₱${addon.price.toFixed(2)}</small>
                                                </div>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="customer-section">
                        <h3 class="section-title">Order Details</h3>
                        ${formData.nickname ? `
                            <div class="detail-row">
                                <span class="detail-label">Nickname:</span>
                                <span class="detail-value">${formData.nickname}</span>
                            </div>
                        ` : ''}
                        <div class="detail-row">
                            <span class="detail-label">Contact Number:</span>
                            <span class="detail-value">${formData.contactNumber || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Method:</span>
                            <span class="detail-value">${formData.paymentMethod}</span>
                        </div>
                        ${formData.discountType !== 'none' ? `
                            <div class="detail-row">
                                <span class="detail-label">Discount Type:</span>
                                <span class="detail-value">${formData.discountType.toUpperCase()}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">ID Number:</span>
                                <span class="detail-value">${formData.idNumber || 'N/A'}</span>
                            </div>
                        ` : ''}
                        ${formData.orderType === 'dine-in' ? `
                            <div class="detail-row">
                                <span class="detail-label">Table Number:</span>
                                <span class="detail-value">Table ${formData.tableNumber}</span>
                            </div>
                        ` : ''}
                    </div>
                    <div class="total-section">
                        <div class="detail-row">
                            <span class="detail-label">Subtotal:</span>
                            <span class="detail-value">₱${subtotal.toFixed(2)}</span>
                        </div>
                        ${discount > 0 ? `
                            <div class="detail-row discount">
                                <span class="detail-label">Discount (20%):</span>
                                <span class="detail-value">-₱${discount.toFixed(2)}</span>
                            </div>
                        ` : ''}
                        <div class="detail-row">
                            <span class="detail-label">VAT (12%):</span>
                            <span class="detail-value">₱${vatAmount.toFixed(2)}</span>
                        </div>
                        <div class="detail-row total">
                            <span class="detail-label">Final Total (VAT Included):</span>
                            <span class="detail-value">₱${finalTotal.toFixed(2)}</span>
                        </div>
                        <div class="payment-details">
                            <div class="detail-row">
                                <span class="detail-label">Amount Paid:</span>
                                <div class="amount-input">
                                    <span class="peso-sign">₱</span>
                                    <input type="number" id="amount-paid" class="form-control" placeholder="Enter amount" step="0.01" min="${finalTotal}">
                                </div>
                            </div>
                            <div class="detail-row change-amount" style="display: none;">
                                <span class="detail-label">Change:</span>
                                <span class="detail-value" id="change-amount">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            customClass: {
                container: 'confirm-order-modal',
                popup: 'confirm-order-popup',
                content: 'confirm-order-content'
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm Order',
            cancelButtonText: 'Edit Order',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            width: '600px',
            didOpen: () => {
                // Add custom styles for the confirmation modal
                const style = document.createElement('style');
                style.textContent = `
                    .confirm-order-popup {
                        padding: 2rem;
                        max-height: 90vh;
                        overflow-y: auto;
                        display: flex;
                        flex-direction: column;
                    }
                    .confirmation-details {
                        text-align: left;
                        padding-bottom: 2rem;
                        margin-bottom: 1rem;
                    }
                    .section-title {
                        font-size: 1.2rem;
                        color: #333;
                        margin-bottom: 1rem;
                        padding-bottom: 0.5rem;
                        border-bottom: 2px solid #eee;
                    }
                    .payment-details {
                        margin-top: 1rem;
                        padding-top: 1rem;
                        border-top: 2px solid #eee;
                        margin-bottom: 2rem;
                    }
                    .total-section {
                        margin-top: 1rem;
                        padding-top: 1rem;
                        border-top: 2px solid #eee;
                        margin-bottom: 1rem;
                    }
                    .swal2-actions {
                        margin-top: 0;
                        padding: 1rem;
                        border-top: 1px solid #eee;
                        width: 100%;
                        justify-content: flex-end;
                        gap: 1rem;
                    }
                    .swal2-confirm, .swal2-cancel {
                        margin: 0 !important;
                    }
                    .swal2-popup {
                        padding-bottom: 0;
                    }
                    .swal2-html-container {
                        margin: 0;
                        overflow-y: auto;
                        max-height: calc(90vh - 150px);
                    }
                    .amount-input {
                        display: flex;
                        align-items: center;
                        position: relative;
                        flex: 1;
                        margin-left: 10px;
                    }
                    .peso-sign {
                        position: absolute;
                        left: 10px;
                        z-index: 1;
                        color: #495057;
                    }
                    #amount-paid {
                        padding-left: 25px;
                        width: 150px;
                        margin-left: auto;
                    }
                    .change-amount {
                        font-weight: bold;
                        color: #28a745;
                    }
                    .change-amount .detail-value {
                        font-size: 1.1em;
                    }
                    .items-list {
                        margin-bottom: 2rem;
                    }
                    .item-detail {
                        padding: 0.5rem 0;
                        border-bottom: 1px solid #eee;
                    }
                    .item-name {
                        font-weight: 500;
                    }
                    .addons-list {
                        padding-left: 1.5rem;
                        margin-top: 0.25rem;
                    }
                    .addon-detail {
                        display: flex;
                        justify-content: space-between;
                        color: #666;
                    }
                    .customer-section {
                        margin: 2rem 0;
                        padding: 1rem;
                        background: #f8f9fa;
                        border-radius: 8px;
                    }
                    .detail-row {
                        display: flex;
                        justify-content: space-between;
                        margin: 0.5rem 0;
                        padding: 0.5rem 0;
                    }
                    .detail-label {
                        font-weight: 500;
                        color: #555;
                    }
                    .total-section .detail-row.total {
                        font-size: 1.2rem;
                        font-weight: bold;
                        color: #28a745;
                        border-top: 1px solid #eee;
                        margin-top: 0.5rem;
                        padding-top: 0.5rem;
                    }
                    .discount {
                        color: #dc3545;
                    }
                `;
                document.head.appendChild(style);

                // Add event listener for amount paid input
                const amountPaidInput = document.getElementById('amount-paid');
                const changeDisplay = document.getElementById('change-amount');
                const changeRow = document.querySelector('.change-amount');

                amountPaidInput.addEventListener('input', function() {
                    const amountPaid = parseFloat(this.value) || 0;
                    const change = amountPaid - finalTotal;
                    
                    if (amountPaid >= finalTotal) {
                        changeRow.style.display = 'flex';
                        changeDisplay.textContent = `₱${change.toFixed(2)}`;
                        this.classList.remove('is-invalid');
                    } else {
                        changeRow.style.display = 'none';
                        this.classList.add('is-invalid');
                    }
                });
            },
            preConfirm: () => {
                const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
                if (amountPaid < finalTotal) {
                    Swal.showValidationMessage('Please enter an amount equal to or greater than the total amount');
                    return false;
                }
                return {
                    ...formData,
                    amountPaid: amountPaid,
                    change: amountPaid - finalTotal
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading animation
        Swal.fire({
                    title: 'Processing Order',
            html: `
                <div class="processing-order">
                    <div class="loading-spinner"></div>
                    <p class="mt-3">Please wait while we process your order...</p>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false
        });

                // Get current time
        const now = new Date();
        const pickup_time = now.toTimeString().split(' ')[0];

        // Prepare order data
        const orderData = {
            subtotal_amount: parseFloat(subtotal),
            vat_amount: parseFloat(vatAmount),
            discount_amount: parseFloat(discount || 0),
            total_amount: parseFloat(finalTotal),
            contact_number: result.value.contactNumber || '',
            nickname: result.value.nickname || '',
            payment_method: result.value.paymentMethod,
            discount_type: result.value.discountType || 'none',
            id_number: result.value.idNumber || '',
            discount_amount: parseFloat(discount || 0),
            amount_paid: parseFloat(result.value.amountPaid),
            change_amount: parseFloat(result.value.change),
            status: 'processing',
            booking_type: 'walk-in',
            order_type: result.value.orderType,
            table_package_id: result.value.tableNumber,
            table_name: result.value.orderType === 'dine-in' ? `Table ${result.value.tableNumber}` : null,
            type_of_order: result.value.orderType,
            items: order.map(item => ({
                item_name: item.name,
                quantity: parseInt(item.qty),
                unit_price: parseFloat(item.price),
                addons: item.addons.map(addon => ({
                    addon_name: addon.name,
                    addon_price: parseFloat(addon.price)
                }))
            })),
            user_id: currentUserId
        };

        // Send order to server with error handling
        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
        .then(data => {
            if (data.status === 'success') {
                        // Show success message
                Swal.fire({
                    icon: 'success',
                            title: 'Order Placed Successfully!',
                    html: `
                        <div class="order-success">
                            <p>Order ID: #${data.orderId}</p>
                            <p>Order Type: ${result.value.orderType === 'dine-in' ? 'Dine-in' : 'Takeout'}</p>
                            ${result.value.orderType === 'dine-in' ? `<p>Table Number: Table ${result.value.tableNumber}</p>` : ''}
                                    <p>Payment Method: ${result.value.paymentMethod}</p>
                                    <p>Subtotal: ₱${subtotal.toFixed(2)}</p>
                                    ${discount > 0 ? `<p>Discount (20%): -₱${discount.toFixed(2)}</p>` : ''}
                                    <p>VAT (12%): ₱${vatAmount.toFixed(2)}</p>
                                    <p>Amount Paid: ₱${result.value.amountPaid.toFixed(2)}</p>
                                    <p>Change: ₱${result.value.change.toFixed(2)}</p>
                                    <p>Total Amount (VAT Included): ₱${finalTotal.toFixed(2)}</p>
                            <small class="text-muted">Please wait for your order to be prepared.</small>
                        </div>
                    `,
                    confirmButtonText: 'Print Receipt',
                    showCancelButton: true,
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open receipt in new window for printing
                        const receiptWindow = window.open(`generate_receipt.php?order_id=${data.orderId}`, '_blank', 'width=400,height=600');
                    }
                    // Reset order and refresh page
                    order = [];
                    updateOrder();
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to place order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Order Processing Error',
                text: 'There was a problem processing your order. Please try again.',
                        footer: `<small>Error details: ${error.message}</small>`,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
            });
        });
    }
        });
    }

    // Add this function to calculate subtotal
    function calculateSubtotal() {
        return order.reduce((total, item) => {
            const itemTotal = item.price * item.qty;
            const addonsTotal = item.addons.reduce((sum, addon) => sum + (addon.price * (addon.qty || 1) * item.qty), 0);
            return total + itemTotal + addonsTotal;
        }, 0);
    }

    // Helper function to mark fields as required
    function markFieldAsRequired(fieldId, errorMessage) {
        const field = document.getElementById(fieldId);
        field.classList.add('required-field');
        
        // Add error message below the field
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = errorMessage;
        field.parentNode.appendChild(errorDiv);
    }

    // Add this function to show description
    function showDescription(name, description) {
        Swal.fire({
            title: name,
            html: `<div class="menu-description">${description}</div>`,
            icon: null,
            confirmButtonText: 'Close',
            customClass: {
                container: 'description-modal',
                popup: 'description-popup',
                title: 'description-title',
                htmlContainer: 'description-content'
            }
        });
    }

    // Function to save form data to localStorage
    function saveFormData() {
        const formData = {
            contactNumber: document.getElementById('swal-contact')?.value,
            nickname: document.getElementById('swal-nickname')?.value,
            discountType: document.getElementById('swal-discount')?.value,
            idNumber: document.getElementById('swal-id')?.value,
            paymentMethod: document.getElementById('swal-payment')?.value,
            orderType: document.getElementById('swal-order-type')?.value,
            tableNumber: document.getElementById('swal-table')?.value,
            order: order
        };
        localStorage.setItem('posFormData', JSON.stringify(formData));
    }

    // Function to load saved form data
    function loadFormData() {
        const savedData = localStorage.getItem('posFormData');
        if (savedData) {
            const formData = JSON.parse(savedData);
            
            // Restore order items
            if (formData.order && formData.order.length > 0) {
                order = formData.order;
                updateOrder();
            }
            
            // Return the form data to be used when opening the modal
            return formData;
        }
        return null;
    }

    // Function to clear saved form data
    function clearFormData() {
        localStorage.removeItem('posFormData');
    }

    // Add event listener for page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load saved order data
        const savedData = loadFormData();
        if (savedData && savedData.order) {
            order = savedData.order;
            updateOrder();
        }
        
        // ... rest of the existing DOMContentLoaded code ...
    });

    // Add event listener for beforeunload to save current state
    window.addEventListener('beforeunload', function() {
        if (order.length > 0) {
            saveFormData();
        }
    });

    // Add the removeItem function
    function removeItem(index) {
        Swal.fire({
            title: 'Remove Item?',
            text: `Do you want to remove ${order[index].name} from your order?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                order.splice(index, 1);
                updateOrder();
                saveFormData();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Item removed from cart',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
    </script>
</body>
</html>