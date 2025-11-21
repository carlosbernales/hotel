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

// Fetch menu items with their categories
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

// Convert the PHP arrays to JSON for JavaScript use
$menuData = json_encode([
    'categories' => $categories,
    'items' => $menuItems,
    'addons' => $addons
]);
?>

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
            margin-bottom: 1rem;
            text-align: center;
            padding-left: 300px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .category-list {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding-left: 250px;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .category-list li {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
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
            padding-left: 300px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
            padding-left: 250px;
            width: 100%;
            z-index: 1;
        }

        .menu-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .menu-item-details {
            padding: 1rem;
            text-align: center;
        }

        .menu-item-details h3 {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
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
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            transition: background-color 0.2s;
        }

        .add-to-cart:hover {
            background: #ffb300;
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
            padding: 1rem;
            margin: 0;
            border-bottom: 1px solid #eee;
            font-size: 1.2rem;
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
        }

        .order-item-price {
            color: #28a745;
            font-weight: 600;
        }

        .order-item-category {
            color: #666;
            font-size: 0.9rem;
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
            margin-bottom: 0.5rem;
            color: #333;
        }

        .place-order-btn {
            width: 100%;
            padding: 0.8rem;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
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
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
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
        
        menuGrid.innerHTML = items.map(item => `
            <div class="menu-item">
                <img src="/Admin/uploads/menus/${item.image_path || 'default.jpg'}" alt="${item.name}">
                <div class="menu-item-details">
                    <h3>${item.name}</h3>
                    <p>₱${parseFloat(item.price).toFixed(2)}</p>
                    <button class="add-to-cart" 
                            data-item-id="${item.id}"
                            data-item="${item.name}" 
                            data-price="${item.price}" 
                            data-category="${item.category_name}">
                        Add to Cart
                    </button>
                </div>
            </div>
        `).join('');
    }

    function addToOrder(itemId, name, price, category) {
        const existingItem = order.find(item => item.id === itemId);
        const itemAddons = menuData.addons[itemId] || [];
        
        if (existingItem) {
            existingItem.qty++;
        } else {
            order.push({
                id: itemId,
                name: name,
                price: parseFloat(price),
                category: category,
                qty: 1,
                addons: [],
                availableAddons: itemAddons
            });
        }
        updateOrder();
        
        // Add SweetAlert toast notification
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `${name} added to cart`,
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
                    Category: ${item.category}
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn minus" onclick="decrementQty(${index})">-</button>
                    <span class="quantity-display">${item.qty}</span>
                    <button class="quantity-btn plus" onclick="incrementQty(${index})">+</button>
                </div>
                ${item.availableAddons.length > 0 ? `
                    <div class="addons-section">
                        <h4>Add-ons:</h4>
                        ${item.availableAddons.map(addon => `
                            <div class="addon-item">
                                <input type="checkbox" 
                                       id="addon-${index}-${addon.id}"
                                       onchange="toggleAddon(${index}, ${addon.id}, '${addon.name}', ${addon.price})"
                                       ${item.addons.some(a => a.id === addon.id) ? 'checked' : ''}>
                                <label for="addon-${index}-${addon.id}">
                                    ${addon.name} (+₱${parseFloat(addon.price).toFixed(2)})
                                </label>
                            </div>
                        `).join('')}
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
            item.addons.push({ id: addonId, name: addonName, price: addonPrice });
        } else {
            item.addons.splice(addonIndex, 1);
        }
        
        updateTotals();
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
    }

    function decrementQty(index) {
        if (order[index].qty > 1) {
            order[index].qty--;
            updateOrder();
        } else {
            // Confirm before removing item
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
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Display initial category
        displayMenuItems(menuData.categories[0].id);
        
        // Category selection
        document.querySelector('.category-list').addEventListener('click', function(e) {
            if (e.target.matches('li')) {
                document.querySelectorAll('.category-list li').forEach(li => li.classList.remove('active'));
                e.target.classList.add('active');
                displayMenuItems(e.target.dataset.category);
            }
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

        // Show order summary and collect information using SweetAlert2
        Swal.fire({
            title: 'Order Summary',
            html: `
                <div class="order-summary-content">
                    ${generateOrderSummaryHTML()}
                    <div class="form-group mt-3">
                        <label for="swal-contact">Contact Number</label>
                        <input type="tel" id="swal-contact" class="swal2-input" placeholder="Enter contact number">
                    </div>
                    <div class="form-group mt-3">
                        <label>Discount Type</label><br>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="discount-type" id="no-discount" value="none" checked>
                            <label class="btn btn-outline-primary" for="no-discount">No Discount</label>
                            
                            <input type="radio" class="btn-check" name="discount-type" id="senior-discount" value="senior">
                            <label class="btn btn-outline-primary" for="senior-discount">Senior Citizen</label>
                            
                            <input type="radio" class="btn-check" name="discount-type" id="pwd-discount" value="pwd">
                            <label class="btn btn-outline-primary" for="pwd-discount">PWD</label>
                        </div>
                    </div>
                    <div class="form-group mt-3" id="swal-id-group" style="display: none;">
                        <label for="swal-id">ID Number</label>
                        <input type="text" id="swal-id" class="swal2-input" placeholder="Enter ID number">
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
                // Add event listeners for discount type radio buttons
                document.querySelectorAll('input[name="discount-type"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const idGroup = document.getElementById('swal-id-group');
                        idGroup.style.display = this.value !== 'none' ? 'block' : 'none';
                    });
                });

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
            },
            preConfirm: () => {
                const contactNumber = document.getElementById('swal-contact').value;
                const paymentMethod = document.getElementById('swal-payment').value;
                const discountType = document.querySelector('input[name="discount-type"]:checked').value;
                const idNumber = document.getElementById('swal-id').value;

                // Validate form
                const errors = validateOrderInputs(contactNumber, paymentMethod, discountType, idNumber);
                if (errors.length > 0) {
                    Swal.showValidationMessage(errors.join('<br>'));
                    return false;
                }

                return {
                    customerName: 'N/A',
                    contactNumber,
                    paymentMethod,
                    discountType,
                    idNumber
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processOrderSubmission(result.value);
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
    function validateOrderInputs(contactNumber, paymentMethod, discountType, idNumber) {
        const errors = [];

        if (!contactNumber) {
            errors.push('Contact number is required');
        } else if (!/^09\d{9}$/.test(contactNumber)) {
            errors.push('Please enter a valid 11-digit phone number starting with 09');
        }

        if (!paymentMethod) {
            errors.push('Payment method is required');
        }

        if (discountType !== 'none' && idNumber) {
            if (discountType === 'senior' && !/^SCDFI-[A-Z0-9]{8}$/i.test(idNumber)) {
                errors.push('Invalid Senior Citizen ID format (SCDFI-XXXXXXXX)');
            } else if (discountType === 'pwd' && !/^\d{4}-\d{4}-\d{4}$/.test(idNumber)) {
                errors.push('Invalid PWD ID format (XXXX-XXXX-XXXX)');
            }
        }

        return errors;
    }

    // Function to process the order submission
    function processOrderSubmission(formData) {
        // Calculate totals
        const subtotal = calculateSubtotal();
        let discount = 0;
        
        // Calculate discount based on type (20% for both PWD and Senior)
        if (formData.discountType === 'pwd' || formData.discountType === 'senior') {
            discount = subtotal * 0.20; // 20% discount
        }
        
        const finalTotal = subtotal - discount;

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
                        <div class="detail-row">
                            <span class="detail-label">Contact Number:</span>
                            <span class="detail-value">${formData.contactNumber}</span>
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
                                <span class="detail-value">${formData.idNumber}</span>
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
                        <div class="detail-row total">
                            <span class="detail-label">Final Total:</span>
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
            total_amount: parseFloat(finalTotal),
            contact_number: result.value.contactNumber,
            payment_method: result.value.paymentMethod,
            discount_type: result.value.discountType || 'none',
            id_number: result.value.idNumber || '',
            discount_amount: parseFloat(discount || 0),
            amount_paid: parseFloat(result.value.amountPaid),
            change_amount: parseFloat(result.value.change),
            status: 'processing',
            booking_type: 'walk-in',
            items: order.map(item => ({
                item_name: item.name,
                quantity: parseInt(item.qty),
                unit_price: parseFloat(item.price),
                addons: item.addons.map(addon => ({
                    addon_name: addon.name,
                    addon_price: parseFloat(addon.price)
                }))
            }))
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
                                    
                                    <p>Payment Method: ${result.value.paymentMethod}</p>
                                    
                                    <p>Amount Paid: ₱${result.value.amountPaid.toFixed(2)}</p>
                                    <p>Change: ₱${result.value.change.toFixed(2)}</p>
                                    <p>Total Amount: ₱${finalTotal.toFixed(2)}</p>
                            <small class="text-muted">Please wait for your order to be prepared.</small>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
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
            const addonsTotal = item.addons.reduce((sum, addon) => sum + addon.price, 0) * item.qty;
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
    </script>
        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>