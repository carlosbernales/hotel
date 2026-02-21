<?php
require_once 'db.php';

// Function to fetch all active discount types
function getDiscountTypes($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM discount_types WHERE is_active = 1 ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching discount types: " . $e->getMessage());
        return [];
    }
}

// Check if user is logged in and has cashier role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'cashier') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Point of Sale';
$currentPage = 'pos.php';

// Include header
include 'header.php';

// Fetch menu categories
$menuCategories = [];
try {
    $stmt = $pdo->query("SELECT * FROM menu_categories ORDER BY  name");
    $menuCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to load menu categories: " . $e->getMessage();
}

// Fetch menu items
try {
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE availability = '1' ORDER BY category_id, name");
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group items by category for easier display
    $groupedItems = [];
    foreach ($menuItems as $item) {
        $categoryId = $item['category_id'];
        if (!isset($groupedItems[$categoryId])) {
            $groupedItems[$categoryId] = [];
        }
        $groupedItems[$categoryId][] = $item;
    }
    $menuItems = $groupedItems;
} catch (PDOException $e) {
    $error = "Failed to load menu items: " . $e->getMessage();
}

// Fetch menu item addons
try {
    $stmt = $pdo->query("SELECT menu_item_id, id, name, price FROM menu_items_addons ORDER BY name");
    $addons = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    $menuItemAddons = [];
    
    // Reorganize add-ons by menu item ID
    foreach ($addons as $menuItemId => $addonList) {
        $menuItemAddons[$menuItemId] = $addonList;
    }
} catch (PDOException $e) {
    $error = "Failed to load menu item addons: " . $e->getMessage();
}
?>
<style>
    /* Primary color variables */
    :root {
        --primary-color: #b8860b;
        --primary-hover: #9a7209;
        --primary-light: rgba(184, 134, 11, 0.1);
        --primary-light-hover: rgba(184, 134, 11, 0.2);
    }
    
    /* Style for category tabs */
    .nav-tabs {
        border-bottom: 2px solid var(--primary-color);
        margin-bottom: 20px;
    }
    
    .nav-tabs .nav-link {
        color: #555;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 500;
        padding: 10px 20px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background-color: var(--primary-light);
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        background-color: transparent;
        border-color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        font-weight: 600;
    }
    
    /* Primary buttons */
    .btn-primary, .btn-success {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover, .btn-success:hover,
    .btn-primary:focus, .btn-success:focus,
    .btn-primary:active, .btn-success:active {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    /* Outline buttons */
    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Active states */
    .btn-check:checked + .btn-primary,
    .btn-check:active + .btn-primary,
    .btn-primary:active,
    .btn-primary.active,
    .show > .btn-primary.dropdown-toggle {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    /* Focus states */
    .btn-primary:focus,
    .btn-check:focus + .btn-primary,
    .btn-primary:focus-visible {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        box-shadow: 0 0 0 0.25rem rgba(184, 134, 11, 0.25);
    }
    
    /* Make sure the tab content is properly spaced */
    .tab-content {
        padding: 15px 0;
    }
    /* Modal Styling */
.modal {
    --modal-border-radius: 0.75rem;
    --modal-box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    --modal-transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.modal-content {
    border: none;
    border-radius: var(--modal-border-radius);
    box-shadow: var(--modal-box-shadow);
    overflow: hidden;
    transition: var(--modal-transition);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
    position: relative;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.75;
    transition: opacity 0.2s ease;
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
    letter-spacing: 0.3px;
}

.modal-body {
    padding: 1.75rem;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 1.25rem 1.5rem;
    border-bottom-left-radius: var(--modal-border-radius);
    border-bottom-right-radius: var(--modal-border-radius);
}

/* Order Summary Styling */
.order-summary {
    color: #333;
}

.order-summary h6 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.25rem;
    font-size: 1.1rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.order-summary h6:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 500;
    color: #555;
    white-space: nowrap;
    padding-right: 1rem;
    border: none;
}

.table td {
    border: none;
    padding: 0.5rem 0;
}

/* Enhanced Button Styling */
.btn {
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 0.85rem;
    border: none;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Primary Buttons */
.btn-primary,
.btn-success {
    background: linear-gradient(135deg, var(--primary-color), #d4a017);
    color: white;
    border: none;
    box-shadow: 0 4px 6px rgba(184, 134, 11, 0.2);
}

.btn-primary:hover,
.btn-success:hover,
.btn-primary:focus,
.btn-success:focus {
    background: linear-gradient(135deg, var(--primary-hover), #e6b422);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(184, 134, 11, 0.3);
}

.btn-primary:active,
.btn-success:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(184, 134, 11, 0.2);
}

/* Secondary Buttons */
.btn-secondary {
    background: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.btn-secondary:hover,
.btn-secondary:focus {
    background: #e9ecef;
    border-color: #ced4da;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    color: #212529;
}

.btn-secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Danger Buttons */
.btn-outline-danger,
.btn-danger {
    color: #dc3545;
    border: 1px solid #dc3545;
    background: transparent;
}

.btn-outline-danger:hover,
.btn-danger:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
}

/* Button Sizes */
.btn-sm {
    padding: 0.35rem 0.8rem;
    font-size: 0.75rem;
}

.btn-lg {
    padding: 0.8rem 2rem;
    font-size: 1rem;
}

/* Button Icons */
.btn i {
    margin-right: 6px;
    font-size: 0.9em;
}

/* Button Groups */
.btn-group .btn {
    margin: 0;
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.btn-group .btn:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

/* Quantity Buttons */
.btn-quantity {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px !important;
}

/* Remove Item Button */
.remove-item {
    transition: all 0.2s ease;
}

.remove-item:hover {
    transform: scale(1.2);
    color: #dc3545 !important;
}

/* Add to Cart Button */
#modal-add-to-cart {
    min-width: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Checkout Button */
#checkout-btn {
    padding: 0.8rem;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.8px;
}

/* Clear Cart Button */
#clear-cart {
    padding: 0.8rem;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.8px;
    transition: all 0.3s ease;
}

#clear-cart:hover {
    background-color: #dc3545;
    color: white !important;
    transform: translateY(-2px);
}

/* Modal Footer Buttons */
.modal-footer .btn {
    min-width: 120px;
}

/* Ripple Effect */
.btn-ripple {
    position: relative;
    overflow: hidden;
}

.btn-ripple:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%, -50%);
    transform-origin: 50% 50%;
}

@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 0.5;
    }
    100% {
        transform: scale(20, 20);
        opacity: 0;
    }
}

.btn-ripple:focus:not(:active)::after {
    animation: ripple 0.6s ease-out;
}

/* Form Elements */
.form-control,
.form-select {
    border: 1px solid #ddd;
    padding: 0.6rem 0.75rem;
    border-radius: 0.4rem;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(184, 134, 11, 0.25);
}

/* Responsive Adjustments */
@media (min-width: 576px) {
    .modal-dialog {
        max-width: 500px;
        margin: 1.75rem auto;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal.fade .modal-dialog {
    animation: fadeIn 0.3s ease-out;
}
</style>

<div class="container-fluid py-4">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Menu Items -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Menu Items</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($menuCategories)): ?>
                        <!-- Category Tabs -->
                        <ul class="nav nav-tabs mb-3" id="menuTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                                    All Items
                                </button>
                            </li>
                            <?php foreach ($menuCategories as $category): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" 
                                            id="cat-<?php echo $category['id']; ?>-tab" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#cat-<?php echo $category['id']; ?>" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="cat-<?php echo $category['id']; ?>" 
                                            aria-selected="false">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="menuTabsContent">
                            <!-- All Items Tab -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="row">
                                    <?php foreach ($menuItems as $categoryId => $items): ?>
                                        <?php foreach ($items as $item): ?>
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <div class="card h-100 menu-item" 
                                                     data-id="<?php echo $item['id']; ?>" 
                                                     data-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     data-price="<?php echo $item['price']; ?>">
                                                    <div class="card-body">
                                                        <div class="text-center mb-2">
                                                            <?php if (!empty($item['image_path'])): ?>
                                                                <img src="../../Admin/adminBackend/menu_item_images/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                                     class="img-fluid rounded" 
                                                                     style="max-height: 150px; width: 100%; object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                                                    <i class="fas fa-utensils fa-3x text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                        <p class="text-muted small mb-2">
                                                            <?php echo !empty($item['description']) ? htmlspecialchars($item['description']) : '&nbsp;'; ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-primary">₱<?php echo number_format($item['price'], 2); ?></span>
                                                            <button class="btn btn-sm btn-outline-primary add-to-cart">
                                                                <i class="fas fa-plus"></i> Add
                                                            </button>
                                                        </div>
                                                        
                                                        <?php if (isset($menuItemAddons[$item['id']])): ?>
                                                            <div class="addons-container mt-2" style="display: none;">
                                                                <p class="small mb-1"><strong>Add-ons:</strong></p>
                                                                <?php foreach ($menuItemAddons[$item['id']] as $addon): ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input addon-checkbox" 
                                                                               type="checkbox" 
                                                                               value="<?php echo $addon['id']; ?>" 
                                                                               id="addon-<?php echo $addon['id']; ?>" 
                                                                               data-price="<?php echo $addon['price']; ?>">
                                                                        <label class="form-check-label small" for="addon-<?php echo $addon['id']; ?>">
                                                                            <?php echo htmlspecialchars($addon['name']); ?> 
                                                                            (+₱<?php echo number_format($addon['price'], 2); ?>)
                                                                        </label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Category Tabs -->
                            <?php foreach ($menuCategories as $category): ?>
                                <?php if (isset($menuItems[$category['id']])): ?>
                                    <div class="tab-pane fade" id="cat-<?php echo $category['id']; ?>" role="tabpanel" aria-labelledby="cat-<?php echo $category['id']; ?>-tab">
                                        <div class="row">
                                            <?php foreach ($menuItems[$category['id']] as $item): ?>
                                                <div class="col-md-4 col-sm-6 mb-3">
                                                    <div class="card h-100 menu-item" 
                                                         data-id="<?php echo $item['id']; ?>" 
                                                         data-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         data-price="<?php echo $item['price']; ?>">
                                                        <div class="card-body">
                                                            <div class="text-center mb-2">
                                                                <?php if (!empty($item['image_path'])): ?>
                                                                    <img src="../../Admin/adminBackend/menu_item_images/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                                         class="img-fluid rounded" 
                                                                         style="max-height: 150px; width: 100%; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                                                        <i class="fas fa-utensils fa-3x text-muted"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <h6 class="card-title mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                            <p class="text-muted small mb-2">
                                                                <?php echo !empty($item['description']) ? htmlspecialchars($item['description']) : '&nbsp;'; ?>
                                                            </p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold text-primary">₱<?php echo number_format($item['price'], 2); ?></span>
                                                                <button class="btn btn-sm btn-outline-primary add-to-cart">
                                                                    <i class="fas fa-plus"></i> Add
                                                                </button>
                                                            </div>
                                                            
                                                            <?php if (isset($menuItemAddons[$item['id']])): ?>
                                                                <div class="addons-container mt-2" style="display: none;">
                                                                    <p class="small mb-1"><strong>Add-ons:</strong></p>
                                                                    <?php foreach ($menuItemAddons[$item['id']] as $addon): ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input addon-checkbox" 
                                                                                   type="checkbox" 
                                                                                   value="<?php echo $addon['id']; ?>" 
                                                                                   id="addon-<?php echo $addon['id']; ?>" 
                                                                                   data-price="<?php echo $addon['price']; ?>">
                                                                            <label class="form-check-label small" for="addon-<?php echo $addon['id']; ?>">
                                                                                <?php echo htmlspecialchars($addon['name']); ?> 
                                                                                (+₱<?php echo number_format($addon['price'], 2); ?>)
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No menu categories found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
<!-- Add-ons Modal -->
<div class="modal fade" id="addonsModal" tabindex="-1" aria-labelledby="addonsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addonsModalLabel">Select Add-ons</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-item-info" class="mb-3">
                    <h6 id="modal-item-name"></h6>
                    <p class="text-muted" id="modal-item-price"></p>
                </div>
                <div id="modal-addons-list">
                    <!-- Add-ons will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="modal-add-to-cart">Add to Cart</button>
            </div>
        </div>
    </div>
</div>

        <!-- Add this right before the closing </div> of the main container -->
<div class="col-md-4">
    <div class="card shadow-sm h-100">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Order Summary</h5>
        </div>
        <div class="card-body p-0">
            <div id="cart-items" class="p-3" style="max-height: 400px; overflow-y: auto;">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p>Your cart is empty</p>
                </div>
            </div>
            <div class="border-top p-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">₱0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold" id="cart-total">₱0.00</span>
                </div>
                <button class="btn btn-primary w-100 mb-2" id="checkout-btn" disabled>
                    <i class="fas fa-credit-card me-2"></i>Proceed
                </button>
                <button class="btn btn-outline-danger w-100" id="clear-cart" disabled>
                    <i class="fas fa-trash-alt me-2"></i>Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Order Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="order-summary">
                    <h6>Order Items</h6>
                    <div id="modal-order-items" class="mb-3">
                        <!-- Order items will be populated here -->
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span id="modal-subtotal">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <span id="modal-total" class="fw-bold">₱0.00</span>
                    </div>
                    <div class="mb-3">
                        <label for="order-type" class="form-label">Type of Order</label>
                        <select class="form-select mb-3" id="order-type" required>
                            <option value="">Select type of order </option>
                            <option value="dine-in">Dine-in</option>
                            <option value="takeout">Takeout</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="table-type" class="form-label">Table Type</label>
                        <select class="form-select mb-2" id="table-type" required>
                            <option value="">Select Table Type</option>
                            <?php
                            try {
                                // Debug: Check if $pdo is set
                                if (!isset($pdo)) {
                                    throw new Exception('Database connection not established');
                                }
                                
                                // Debug: Check if table exists
                                $tableExists = $pdo->query("SHOW TABLES LIKE 'table_types'")->rowCount() > 0;
                                if (!$tableExists) {
                                    throw new Exception('table_types table does not exist');
                                }
                                
                                // Get all table types
                                $stmt = $pdo->query("SELECT * FROM table_types ORDER BY table_name");
                                $tableTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (empty($tableTypes)) {
                                    echo "<option value=''>No table types found</option>";
                                } else {
                                    foreach ($tableTypes as $type) {
                                        $capacity = isset($type['capacity']) ? $type['capacity'] : 0;
                                        echo "<option value='" . $type['id'] . "'>" . 
                                             htmlspecialchars($type['table_name']) . " (Max " . $capacity . " persons)</option>";
                                    }
                                }
                            } catch (Exception $e) {
                                error_log("Error in table type dropdown: " . $e->getMessage());
                                echo "<option value=''>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="table-number" class="form-label">Table Number</label>
                        <select class="form-select mb-3" id="table-number" required disabled>
                            <option value="">Select Table Type First</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="guest-type" class="form-label">Guest Type</label>
                        <select class="form-select mb-2" id="guest-type" required>
                            <option value="regular">Regular (No Discount)</option>
                            <?php
                            try {
                                $discountTypes = getDiscountTypes($pdo);
                                error_log("Loaded discount types: " . print_r($discountTypes, true));
                                foreach ($discountTypes as $type) {
                                    $discountPercent = number_format($type['percentage'], 0);
                                    echo sprintf(
                                        '<option value="%s" data-requires-id="1">%s (%s%% off)</option>',
                                        htmlspecialchars(strtolower(str_replace(' ', '_', $type['name']))),
                                        htmlspecialchars($type['name']),
                                        $discountPercent
                                    );
                                }
                            } catch (Exception $e) {
                                error_log("Error loading discount types: " . $e->getMessage());
                                // Fallback to default options if there's an error
                                echo '<option value="senior">Senior Citizen (20% off)</option>';
                                echo '<option value="pwd">PWD (20% off)</option>';
                                echo '<option value="student">Student (10% off)</option>';
                            }
                            ?>
                        </select>
                        <div id="id-number-container" class="mt-2" style="display: none;">
                            <label for="guest-id-number" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="guest-id-number" placeholder="Enter ID number" required>
                            <div class="form-text">Please provide a valid ID number for verification.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="payment-method" class="form-label">Payment Method</label>
                        <select class="form-select mb-3" id="payment-method" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                        </select>
                        
                        <!-- Amount Received and Change Fields -->
                        <div id="payment-amount-fields">
                            <div class="mb-2">
                                <label for="amount-received" class="form-label">Amount Received</label>
                                <input type="number" class="form-control" id="amount-received" step="0.01" min="0" value="0.00" required>
                            </div>
                            <div class="mb-3">
                                <label for="change" class="form-label">Change</label>
                                <input type="text" class="form-control" id="change" value="₱0.00" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-order">Proceed to summary</button>
            </div>
        </div>
    </div>
</div>

<!-- Final Order Summary Modal -->
<div class="modal fade" id="finalSummaryModal" tabindex="-1" aria-labelledby="finalSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finalSummaryModalLabel">Order Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="order-summary">
                    <h6>Order Summary</h6>
                    <div id="final-modal-order-items" class="mb-3">
                        <!-- Order items will be populated here -->
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span id="final-modal-subtotal">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <span id="final-modal-total" class="fw-bold">₱0.00</span>
                    </div>
                    
                    <div class="order-details">
                        <h6>Order Details</h6>
                        <table class="table table-sm">
                            <tr id="final-order-type-row">
                                <th>Order Type:</th>
                                <td id="final-order-type"></td>
                            </tr>
                            <tr id="final-table-type-row" style="display: none;">
                                <th>Table Type:</th>
                                <td id="final-table-type"></td>
                            </tr>
                            <tr id="final-table-number-row" style="display: none;">
                                <th>Table Number:</th>
                                <td id="final-table-number"></td>
                            </tr>
                            <tr>
                                <th>Guest Type:</th>
                                <td id="final-guest-type"></td>
                            </tr>
                            <tr id="final-id-number-row" style="display: none;">
                                <th>ID Number:</th>
                                <td id="final-id-number"></td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td id="final-payment-method"></td>
                            </tr>
                            <tr>
                                <th>Amount Received:</th>
                                <td id="final-amount-received">₱0.00</td>
                            </tr>
                            <tr class="table-active">
                                <th>Change:</th>
                                <td id="final-change" class="fw-bold">₱0.00</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back to Order</button>
                <button type="button" class="btn btn-success" id="confirm-print-btn">Confirm & Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add this inside your existing script tag
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const guestTypeSelect = document.getElementById('guest-type');
    const idNumberContainer = document.getElementById('id-number-container');
    const idNumberInput = document.getElementById('guest-id-number');
    
    // Handle guest type change
    guestTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const requiresId = selectedOption.dataset.requiresId === '1';
        
        // Show/hide ID number field based on selection
        if (this.value !== 'regular' && requiresId) {
            idNumberContainer.style.display = 'block';
            idNumberInput.required = true;
        } else {
            idNumberContainer.style.display = 'none';
            idNumberInput.required = false;
            idNumberInput.value = ''; // Clear the ID number when hiding the field
        }
        
        // Update the order total when guest type changes
        updateCheckoutModal();
    });
    
    // Update when ID number changes
    idNumberInput.addEventListener('input', function() {
        updateCheckoutModal();
    });

    // Show checkout modal when Proceed button is clicked
    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (cart.length === 0) return;
        
        const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        updateCheckoutModal();
        // Reset table type and number when opening modal
        document.getElementById('table-type').value = '';
        document.getElementById('table-number').innerHTML = '<option value="">Select Table Type First</option>';
        document.getElementById('table-number').disabled = true;
        modal.show();
    });
    
    // Handle order type change to show/hide table selection
    document.getElementById('order-type').addEventListener('change', function() {
        const orderType = this.value;
        const tableTypeGroup = document.querySelector('label[for="table-type"]').closest('.mb-3');
        const tableNumberGroup = document.querySelector('label[for="table-number"]').closest('.mb-3');
        const isDineIn = orderType === 'dine-in';
        
        // Show/hide table selection based on order type
        tableTypeGroup.style.display = isDineIn ? 'block' : 'none';
        tableNumberGroup.style.display = isDineIn ? 'block' : 'none';
        
        // Update required attribute
        document.getElementById('table-type').required = isDineIn;
        document.getElementById('table-number').required = isDineIn;
        
        // Reset table selection if not dine-in
        if (!isDineIn) {
            document.getElementById('table-type').value = '';
            document.getElementById('table-number').innerHTML = '<option value="">Select Table Type First</option>';
            document.getElementById('table-number').disabled = true;
        }
    });
    
    // Trigger the change event once on modal open to set initial state
    document.getElementById('checkoutModal').addEventListener('shown.bs.modal', function() {
        document.getElementById('order-type').dispatchEvent(new Event('change'));
    });

    // Handle table type change to load available table numbers
    document.getElementById('table-type').addEventListener('change', function() {
        const tableTypeId = this.value;
        const tableNumberSelect = document.getElementById('table-number');
        
        if (!tableTypeId) {
            tableNumberSelect.innerHTML = '<option value="">Select Table Type First</option>';
            tableNumberSelect.disabled = true;
            return;
        }
        
        // Show loading state
        tableNumberSelect.innerHTML = '<option value="">Loading table numbers...</option>';
        tableNumberSelect.disabled = false;
        
        // Fetch available table numbers for the selected table type
        fetch(`get_available_tables.php?table_type_id=${tableTypeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.tables.length > 0) {
                    let options = '<option value="">Select Table Number</option>';
                    data.tables.forEach(table => {
                        options += `<option value="${table.id}">Table ${table.table_number} (${table.status})</option>`;
                    });
                    tableNumberSelect.innerHTML = options;
                } else {
                    tableNumberSelect.innerHTML = '<option value="">No available tables found</option>';
                }
                tableNumberSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching table numbers:', error);
                tableNumberSelect.innerHTML = '<option value="">Error loading table numbers</option>';
                tableNumberSelect.disabled = false;
            });
    });

    // Handle amount received input
    document.getElementById('amount-received').addEventListener('input', function() {
        updateChangeAmount();
    });

    // Handle payment method change
    document.getElementById('payment-method').addEventListener('change', function() {
        const amountReceived = document.getElementById('amount-received');
        const changeField = document.getElementById('change');
        const paymentMethod = this.value;
        
        // Reset amount received and change when payment method changes
        amountReceived.value = '0.00';
        changeField.value = '₱0.00';
        
        // Update placeholders based on payment method
        if (paymentMethod === 'cash') {
            amountReceived.placeholder = 'Enter cash amount';
            document.querySelector('label[for="amount-received"]').textContent = 'Amount Received';
            document.querySelector('label[for="change"]').style.display = 'block';
            changeField.style.display = 'block';
        } else {
            amountReceived.placeholder = `Enter ${paymentMethod} amount`;
            document.querySelector('label[for="amount-received"]').textContent = 'Amount Paid';
            document.querySelector('label[for="change"]').style.display = 'none';
            changeField.style.display = 'none';
        }
        
        // Update the order total
        updateCheckoutModal();
    });

    // Confirm order button
    document.getElementById('confirm-order').addEventListener('click', function() {
        // Get all order details
        const orderType = document.getElementById('order-type').value;
        const tableType = document.getElementById('table-type');
        const tableNumber = document.getElementById('table-number');
        const guestType = document.getElementById('guest-type');
        const idNumber = document.getElementById('guest-id-number');
        const paymentMethod = document.getElementById('payment-method');
        const amountReceived = document.getElementById('amount-received');
        const change = document.getElementById('change');
        
        // Get order items and totals
        const orderItems = document.getElementById('modal-order-items').innerHTML;
        const subtotal = document.getElementById('modal-subtotal').textContent;
        const total = document.getElementById('modal-total').textContent;
        
        // Populate the final summary modal
        document.getElementById('final-modal-order-items').innerHTML = orderItems;
        document.getElementById('final-modal-subtotal').textContent = subtotal;
        document.getElementById('final-modal-total').textContent = total;
        document.getElementById('final-order-type').textContent = orderType === 'dine-in' ? 'Dine-in' : 'Takeout';
        
        // Show/hide table info based on order type
        if (orderType === 'dine-in') {
            document.getElementById('final-table-type').textContent = tableType.options[tableType.selectedIndex].text;
            document.getElementById('final-table-number').textContent = tableNumber.options[tableNumber.selectedIndex].text;
            document.getElementById('final-table-type-row').style.display = 'block';
            document.getElementById('final-table-number-row').style.display = 'block';
        } else {
            document.getElementById('final-table-type-row').style.display = 'none';
            document.getElementById('final-table-number-row').style.display = 'none';
        }
        
        // Set guest type and ID number if applicable
        document.getElementById('final-guest-type').textContent = guestType.options[guestType.selectedIndex].text;
        
        if (idNumber && idNumber.value) {
            document.getElementById('final-id-number').textContent = idNumber.value;
            document.getElementById('final-id-number-row').style.display = 'block';
        } else {
            document.getElementById('final-id-number-row').style.display = 'none';
        }
        
        // Set payment info
        document.getElementById('final-payment-method').textContent = paymentMethod.options[paymentMethod.selectedIndex].text;
        document.getElementById('final-amount-received').textContent = amountReceived.value ? '₱' + parseFloat(amountReceived.value).toFixed(2) : '₱0.00';
        document.getElementById('final-change').textContent = change.value;
        
        // Hide current modal and show summary modal
        const checkoutModal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
        checkoutModal.hide();
        
        const summaryModal = new bootstrap.Modal(document.getElementById('finalSummaryModal'));
        summaryModal.show();
    });
    
    // Handle back to order button in summary modal
    document.querySelector('#finalSummaryModal .btn-secondary').addEventListener('click', function() {
        const summaryModal = bootstrap.Modal.getInstance(document.getElementById('finalSummaryModal'));
        summaryModal.hide();
        
        const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        checkoutModal.show();
    });
    
    // Handle process order button in summary modal
    document.getElementById('process-final-order').addEventListener('click', function() {
        // Process the order (you'll need to implement this)
        processOrder();
    });
});

function updateCheckoutModal() {
    const orderItemsContainer = document.getElementById('modal-order-items');
    const subtotalElement = document.getElementById('modal-subtotal');
    const totalElement = document.getElementById('modal-total');
    const guestTypeSelect = document.getElementById('guest-type');
    
    // Clear previous items
    orderItemsContainer.innerHTML = '';
    
    // Calculate subtotal
    let subtotal = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        // Add addons price if any
        const addonsTotal = item.addons ? item.addons.reduce((sum, addon) => sum + (addon.price * item.quantity), 0) : 0;
        subtotal += itemTotal + addonsTotal;
        
        // Create order item element
        const itemElement = document.createElement('div');
        itemElement.className = 'd-flex justify-content-between mb-2';
        itemElement.innerHTML = `
            <div>
                <span class="fw-medium">${item.name} x${item.quantity}</span>
                ${item.addons && item.addons.length > 0 ? 
                    `<div class="text-muted small">${item.addons.map(a => `${a.name} (₱${a.price.toFixed(2)})`).join(', ')}</div>` : 
                    ''
                }
            </div>
            <div>₱${(itemTotal + addonsTotal).toFixed(2)}</div>
        `;
        orderItemsContainer.appendChild(itemElement);
    });
    
    // Calculate discount based on guest type
    const guestType = guestTypeSelect.value;
    const idNumber = document.getElementById('guest-id-number').value;
    const selectedOption = guestTypeSelect.options[guestTypeSelect.selectedIndex];
    let discount = 0;
    let discountText = '';
    
    // Check if this is a discount type that requires ID
    const requiresId = selectedOption.dataset.requiresId === '1';
    
    // Validate ID number for discount-eligible guest types
    if (guestType !== 'regular' && requiresId && !idNumber) {
        const guestTypeName = selectedOption.text.split(' (')[0]; // Get just the name part
        showAlert(`Please provide an ID number for ${guestTypeName}`, 'warning');
        return 0; // Return 0 to prevent order submission
    }
    
    // If it's not the regular option, calculate discount based on the selected discount type
    if (guestType !== 'regular') {
        // Extract the discount percentage from the option text (e.g., "20%" from "Senior Citizen (20% off)")
        const match = selectedOption.text.match(/\((\d+)%/);
        if (match && match[1]) {
            const discountPercent = parseFloat(match[1]) / 100;
            discount = subtotal * discountPercent;
            discountText = ` (${match[1]}% off)`;
        }
    }
    
    const total = subtotal - discount;
    
    // Create discount row if there's a discount
    if (discount > 0) {
        const discountRow = document.createElement('div');
        discountRow.className = 'd-flex justify-content-between mb-2';
        discountRow.innerHTML = `
            <div class="text-success">Discount${discountText}:</div>
            <div class="text-success">-₱${discount.toFixed(2)}</div>
        `;
        orderItemsContainer.appendChild(discountRow);
    }
    
    // Update subtotal and total
    subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
    totalElement.textContent = `₱${total.toFixed(2)}`;
    
    // Update change amount when total changes
    updateChangeAmount();
    
    // Return the total for other functions to use
    return total;
}

function updateChangeAmount() {
    const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
    const total = parseFloat(document.getElementById('modal-total').textContent.replace('₱', '').replace(',', ''));
    const change = amountReceived - total;
    
    const changeField = document.getElementById('change');
    
    // Update change field
    changeField.value = `₱${change >= 0 ? change.toFixed(2) : '0.00'}`;
    
    // Highlight change field if amount received is less than total
    if (amountReceived > 0 && amountReceived < total) {
        changeField.classList.add('text-danger');
    } else {
        changeField.classList.remove('text-danger');
    }
    
    // Update confirm button state
    const confirmBtn = document.getElementById('confirm-order');
    const paymentMethod = document.getElementById('payment-method').value;
    
    if (paymentMethod === 'cash') {
        // For cash payments, require amount received to be at least the total
        confirmBtn.disabled = amountReceived < total;
    } else {
        // For other payment methods, just require some amount to be entered
        confirmBtn.disabled = amountReceived <= 0;
    }
}

function processOrder() {
    // Get guest type and ID number if applicable
    const guestType = document.getElementById('guest-type').value;
    let idNumber = '';
    
    if (['senior', 'pwd', 'student'].includes(guestType)) {
        idNumber = document.getElementById('guest-id-number').value.trim();
        if (!idNumber) {
            showAlert('Please provide an ID number for ' + 
                     document.getElementById('guest-type').options[document.getElementById('guest-type').selectedIndex].text.split(' ')[0], 
                     'warning');
            return;
        }
    }
    
    // Here you would typically send the order to your server
    // Include idNumber in the data sent to the server
    const orderData = {
        guestType: guestType,
        idNumber: idNumber,
        items: cart,
        // Add other order details here
    };
    
    console.log('Order data:', orderData); // For debugging

    // Hide the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('checkoutModal'));
    modal.hide();
}
</script>

<!-- Add this JavaScript before the closing </body> tag -->
<script>
let cart = [];

// Function to update cart display
function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const clearCartBtn = document.getElementById('clear-cart');
    
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Your cart is empty</p>
            </div>
        `;
        subtotalElement.textContent = '₱0.00';
        totalElement.textContent = '₱0.00';
        checkoutBtn.disabled = true;
        clearCartBtn.disabled = true;
        return;
    }
    
    // Calculate totals
    const subtotal = cart.reduce((sum, item) => {
        const itemTotal = item.price + (item.addons ? item.addons.reduce((addonSum, addon) => addonSum + addon.price, 0) : 0);
        return sum + (itemTotal * item.quantity);
    }, 0);
    const total = subtotal; // Add tax, discount, etc. if needed
    
    // Update cart items
    cartItems.innerHTML = cart.map((item, index) => {
        let addonsHTML = '';
        if (item.addons && item.addons.length > 0) {
            addonsHTML = '<div class="small text-muted mt-1">';
            item.addons.forEach(addon => {
                addonsHTML += `+ ${addon.name} (₱${addon.price.toFixed(2)})<br>`;
            });
            addonsHTML += '</div>';
        }
        
        const itemTotal = item.price + (item.addons ? item.addons.reduce((sum, addon) => sum + addon.price, 0) : 0);
        
        return `
            <div class="cart-item mb-3 pb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${item.name}</h6>
                        ${addonsHTML}
                        <div class="d-flex align-items-center mt-2">
                            <button class="btn btn-sm btn-outline-secondary btn-quantity" data-index="${index}" data-action="decrease">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-primary btn-quantity" data-index="${index}" data-action="increase">+</button>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">₱${(itemTotal * item.quantity).toFixed(2)}</div>
                        <button class="btn btn-sm btn-link text-danger p-0 remove-item" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    // Update totals
    subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
    totalElement.textContent = `₱${total.toFixed(2)}`;
    
    // Enable buttons
    checkoutBtn.disabled = false;
    clearCartBtn.disabled = false;
    
    // Add event listeners for quantity buttons and remove buttons
    document.querySelectorAll('.btn-quantity').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const action = this.dataset.action;
            updateCartItemQuantity(index, action);
        });
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            removeFromCart(index);
        });
    });
}

// Function to add item to cart
function addToCart(item) {
    const existingItemIndex = cart.findIndex(cartItem => 
        cartItem.id === item.id && 
        JSON.stringify(cartItem.addons || []) === JSON.stringify(item.addons || [])
    );
    
    if (existingItemIndex > -1) {
        // Item already in cart, increase quantity
        cart[existingItemIndex].quantity++;
    } else {
        // Add new item to cart
        cart.push({
            id: item.id,
            name: item.name,
            price: parseFloat(item.price),
            quantity: 1,
            addons: item.addons || []
        });
    }
    
    updateCartDisplay();
    showAlert('Item added to cart', 'success');
}

// Function to update item quantity
function updateCartItemQuantity(index, action) {
    if (action === 'increase') {
        cart[index].quantity++;
    } else if (action === 'decrease' && cart[index].quantity > 1) {
        cart[index].quantity--;
    } else {
        Swal.fire({
            title: 'Remove Item',
            text: 'Are you sure you want to remove this item?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                removeFromCart(index);
            }
        });
        return;
    }
    
    updateCartDisplay();
}

// Function to remove item from cart
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
    Swal.fire({
        icon: 'warning',
        title: 'Item removed from cart',
        showConfirmButton: false,
        timer: 1500
    });
}

// Event delegation for add to cart buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-to-cart')) {
        e.preventDefault();
        const card = e.target.closest('.card');
        const addonsContainer = card.querySelector('.addons-container');
        
        // Get item information
        const item = {
            id: card.dataset.id,
            name: card.dataset.name,
            price: parseFloat(card.dataset.price),
            addons: []
        };
        
        // Check if this menu item has add-ons
        if (addonsContainer && addonsContainer.querySelector('.addon-checkbox')) {
            // Show modal with add-ons
            showAddonsModal(item, card);
        } else {
            // No add-ons, add directly to cart
            addToCart(item);
        }
    }
});

// Function to show add-ons modal
function showAddonsModal(item, card) {
    const modal = new bootstrap.Modal(document.getElementById('addonsModal'));
    const modalItemName = document.getElementById('modal-item-name');
    const modalItemPrice = document.getElementById('modal-item-price');
    const modalAddonsList = document.getElementById('modal-addons-list');
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart');
    
    // Set item information
    modalItemName.textContent = item.name;
    modalItemPrice.textContent = `₱${item.price.toFixed(2)}`;
    
    // Clear and populate add-ons list
    modalAddonsList.innerHTML = '';
    const addonCheckboxes = card.querySelectorAll('.addon-checkbox');
    
    if (addonCheckboxes.length > 0) {
        const addonsHTML = '<p class="small mb-2"><strong>Available Add-ons:</strong></p>';
        let checkboxesHTML = '';
        
        addonCheckboxes.forEach(checkbox => {
            const label = checkbox.nextElementSibling;
            const addonName = label.textContent.split('+')[0].trim();
            const addonPrice = parseFloat(checkbox.dataset.price);
            
            checkboxesHTML += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-check-label" for="modal-addon-${checkbox.value}">
                        ${addonName} (+₱${addonPrice.toFixed(2)})
                    </label>
                    <div class="input-group input-group-sm w-auto">
                        <button class="btn btn-outline-secondary btn-addon-quantity" type="button" data-action="decrease" data-id="${checkbox.value}">-</button>
                        <input type="text" class="form-control text-center modal-addon-quantity" id="modal-addon-${checkbox.value}" value="0" data-price="${addonPrice}" data-id="${checkbox.value}" readonly style="width: 50px;">
                        <button class="btn btn-outline-secondary btn-addon-quantity" type="button" data-action="increase" data-id="${checkbox.value}">+</button>
                    </div>
                </div>
            `;
        });
        
        modalAddonsList.innerHTML = addonsHTML + checkboxesHTML;

        // Add event listeners for new quantity buttons
        document.querySelectorAll('.btn-addon-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const input = document.querySelector(`.modal-addon-quantity[data-id="${this.dataset.id}"]`);
                let quantity = parseInt(input.value);
                if (this.dataset.action === 'increase') {
                    quantity++;
                } else if (this.dataset.action === 'decrease' && quantity > 0) {
                    quantity--;
                }
                input.value = quantity;
            });
        });

    } else {
        modalAddonsList.innerHTML = '<p class="text-muted">No add-ons available for this item.</p>';
    }
    
    // Handle modal add to cart button
    modalAddToCartBtn.onclick = function() {
        const selectedAddons = [];
        document.querySelectorAll('.modal-addon-quantity').forEach(input => {
            const quantity = parseInt(input.value);
            if (quantity > 0) {
                const addonId = input.dataset.id;
                const addonName = document.querySelector(`label[for="modal-addon-${addonId}"]`).textContent.split('+')[0].trim();
                const addonPrice = parseFloat(input.dataset.price);
                for (let i = 0; i < quantity; i++) {
                    selectedAddons.push({
                        id: addonId,
                        name: addonName,
                        price: addonPrice
                    });
                }
            }
        });
        
        // Add item with selected add-ons to cart
        const itemWithAddons = {
            ...item,
            addons: selectedAddons
        };
        
        addToCart(itemWithAddons);
        
        // Close modal and reset quantities
        modal.hide();
        document.querySelectorAll('.modal-addon-quantity').forEach(input => {
            input.value = 0;
        });
    };
    
    // Show modal
    modal.show();
}

// Clear cart button
document.getElementById('clear-cart').addEventListener('click', function() {
    showConfirm('Are you sure you want to clear the cart?', 'Clear Cart').then((result) => {
        if (result) {
            cart = [];
            updateCartDisplay();
            showAlert('Cart cleared', 'info');
        }
    });
});


// Initialize cart display
updateCartDisplay();
// Update the processOrder function to handle order submission and receipt printing
function processOrder() {
    // Get all order details
    const orderType = document.getElementById('order-type').value;
    const tableType = document.getElementById('table-type');
    const tableNumber = document.getElementById('table-number');
    const guestType = document.getElementById('guest-type');
    const idNumber = document.getElementById('guest-id-number');
    const paymentMethod = document.getElementById('payment-method');
    const amountReceived = document.getElementById('amount-received');
    const change = document.getElementById('change');
    
    // Validate required fields
    if (orderType === 'dine-in' && (!tableType.value || !tableNumber.value)) {
        showAlert('Please select both table type and table number for dine-in orders.', 'warning');
        return;
    }
    
    if (guestType.value !== 'regular' && !idNumber.value) {
        const guestTypeName = guestType.options[guestType.selectedIndex].text.split(' (')[0];
        showAlert(`Please provide an ID number for ${guestTypeName}`, 'warning');
        return;
    }
    
    const totalAmount = parseFloat(document.getElementById('modal-total').textContent.replace('₱', ''));
    if (parseFloat(amountReceived.value) < totalAmount) {
        showAlert('Amount received is less than the total amount.', 'warning');
        return;
    }
    
    // Prepare order data
    const orderData = {
        order_type: orderType,
        table_type_id: tableType.value,
        table_number: tableNumber.value,
        guest_type: guestType.value,
        id_number: idNumber.value,
        payment_method: paymentMethod.value,
        amount_received: parseFloat(amountReceived.value) || 0,
        change: parseFloat(change.value.replace('₱', '').replace(',', '')) || 0,
        items: cart,
        subtotal: parseFloat(document.getElementById('modal-subtotal').textContent.replace('₱', '')),
        total: totalAmount,
        discount: parseFloat(document.getElementById('modal-discount')?.textContent.replace('₱', '')) || 0
    };
    
    // Show loading state
    const confirmBtn = document.getElementById('confirm-print-btn');
    const originalBtnText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
    
    // Send order to server
    fetch('pos_insert_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest' // Add this header to identify AJAX requests
        },
        body: JSON.stringify(orderData)
    })
    .then(response => {
        // First, check if the response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If not JSON, get the response as text to see what's wrong
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned an invalid response. Please check the console for details.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            showAlert('Order placed successfully!', 'success');
            
            // Print receipt
            printReceipt(data.order_number || data.order_id);
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('finalSummaryModal'));
            if (modal) modal.hide();
            
            // Reset the form and cart
            resetOrderForm();
        } else {
            throw new Error(data.message || 'Failed to place order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error processing order: ' + (error.message || 'Unknown error occurred. Please try again.'), 'danger');
    })
    .finally(() => {
        // Reset button state
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalBtnText;
    });
}

// Function to print receipt
function printReceipt(orderId) {
    // Open a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get current date and time
    const now = new Date();
    const dateTimeString = now.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    
    // Create receipt HTML with thermal receipt styling
    const receiptHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt #${orderId}</title>
            <style>
                @media print {
                    @page {
                        size: 80mm 297mm;
                        margin: 2mm;
                    }
                    body {
                        width: 76mm;
                        font-family: 'Courier New', monospace;
                        font-size: 9px;
                        line-height: 1.1;
                        margin: 0;
                        padding: 2mm;
                        background: white;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    * {
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
                
                body {
                    font-family: 'Courier New', monospace;
                    font-size: 9px;
                    line-height: 1.1;
                    width: 76mm;
                    margin: 0 auto;
                    padding: 2mm;
                    background: white;
                    border: 1px solid #ddd;
                }
                
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-bold { font-weight: bold; }
                .text-large { font-size: 12px; font-weight: bold; }
                .text-medium { font-size: 10px; font-weight: bold; }
                
                .receipt-header { text-align: center; margin-bottom: 10px; }
                .receipt-title { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
                .receipt-subtitle { font-size: 8px; margin-bottom: 1px; }
                
                .receipt-divider { 
                    border-top: 1px dashed #000; 
                    margin: 8px 0; 
                    height: 1px;
                }
                
                .receipt-item { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-bottom: 2px;
                    font-size: 8px;
                }
                
                .receipt-item-name { 
                    flex: 2; 
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                
                .receipt-item-qty { 
                    flex: 0.5; 
                    text-align: center; 
                }
                
                .receipt-item-price { 
                    flex: 1; 
                    text-align: right; 
                }
                
                .receipt-total { 
                    font-weight: bold; 
                    margin-top: 3px;
                    font-size: 9px;
                }
                
                .receipt-summary {
                    margin-top: 8px;
                    font-size: 8px;
                }
                
                .receipt-footer { 
                    margin-top: 10px; 
                    text-align: center; 
                    font-size: 7px;
                    font-style: italic;
                }
                
                .addon-item {
                    font-size: 7px;
                    margin-left: 8px;
                    margin-bottom: 1px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="receipt-header">
                <div class="receipt-title">Casa Estela Boutique Hotel & Cafe</div>
                <div class="receipt-subtitle">Gov B Marasigan St, Calapan City, Oriental Mindoro</div>
                <div class="receipt-subtitle">Phone: 0908 747 4892 | Email: casaestelaboutiquehotelandcafe@gmail.com</div>
                <div class="receipt-divider"></div>
                <div class="text-medium">ORDER #: ${orderId}</div>
                <div>${dateTimeString}</div>
                <div class="text-medium">CASHIER: ${document.getElementById('cashier-name')?.textContent || 'Guest'}</div>
                <div class="text-medium">GUEST TYPE: ${document.getElementById('guest-type')?.value || 'Regular'}</div>
                <div class="text-medium">DISCOUNT: ${(() => {
                    const guestTypeSelect = document.getElementById('guest-type');
                    const guestType = guestTypeSelect ? guestTypeSelect.value : 'regular';
                    const selectedOption = guestTypeSelect ? guestTypeSelect.options[guestTypeSelect.selectedIndex] : null;
                    
                    if (guestType !== 'regular' && selectedOption) {
                        const match = selectedOption.text.match(/\((\d+)%/);
                        return match ? `${match[1]}%` : '0%';
                    }
                    return '0%';
                })()}</div>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div id="receipt-items">
                ${generateReceiptItemsHtml()}
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-footer">
                <div>Thank you for dining with us!</div>
                <div>Please come again!</div>
                <div class="receipt-divider"></div>
                <div>** SOFTWARE POWERED BY **</div>
                <div>E Akomoda</div>
            </div>
        </body>
        </html>
    `;
    
    // Write the receipt content and print
    printWindow.document.open();
    printWindow.document.write(receiptHtml);
    printWindow.document.close();
    
    // Add event listener to ensure the print dialog shows after the content is loaded
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}

// Helper function to generate receipt items HTML
function generateReceiptItemsHtml() {
    let html = '';
    let subtotal = 0;
    
    // Add items
    cart.forEach(item => {
        const itemPrice = parseFloat(item.price);
        const addonsTotal = item.addons ? item.addons.reduce((sum, addon) => sum + parseFloat(addon.price), 0) : 0;
        const totalPrice = (itemPrice + addonsTotal) * item.quantity;
        subtotal += totalPrice;
        
        // Add main item
        html += `
            <div class="receipt-item">
                <div class="receipt-item-name">${item.name.toUpperCase()}</div>
                <div class="receipt-item-qty">${item.quantity}x</div>
                <div class="receipt-item-price">${totalPrice.toFixed(2)}</div>
            </div>
        `;
        
        // Add add-ons if any
        if (item.addons && item.addons.length > 0) {
            item.addons.forEach(addon => {
                html += `
                    <div class="receipt-item addon-item">
                        <div class="receipt-item-name">+ ${addon.name}</div>
                        <div class="receipt-item-price">${(addon.price * item.quantity).toFixed(2)}</div>
                    </div>
                `;
            });
        }
    });
    
    // Add subtotal
    html += `
        <div class="receipt-divider"></div>
        <div class="receipt-summary">
            <div class="receipt-item">
                <div>SUBTOTAL:</div>
                <div class="text-right">${subtotal.toFixed(2)}</div>
            </div>
    `;
    
    // Add discount - calculate based on guest type selection
    const guestTypeSelect = document.getElementById('guest-type');
    const guestType = guestTypeSelect ? guestTypeSelect.value : 'regular';
    const selectedOption = guestTypeSelect ? guestTypeSelect.options[guestTypeSelect.selectedIndex] : null;
    let discountAmount = 0;
    let discountTypeText = '0%';
    
    // Calculate discount based on guest type (same logic as updateOrderSummary)
    if (guestType !== 'regular' && selectedOption) {
        const match = selectedOption.text.match(/\((\d+)%/);
        if (match && match[1]) {
            const discountPercent = parseFloat(match[1]) / 100;
            discountAmount = subtotal * discountPercent;
            discountTypeText = `${match[1]}%`;
        }
    }
    
    if (discountAmount > 0) {
        html += `
            <div class="receipt-item">
                <div>DISCOUNT (${discountTypeText}):</div>
                <div class="text-right">-${discountAmount.toFixed(2)}</div>
            </div>
        `;
    }
    
    // Add tax (you can customize this)
    const tax = subtotal * 0.12; // 12% tax
    html += `
        <div class="receipt-item">
            <div>TAX (12%):</div>
            <div class="text-right">${tax.toFixed(2)}</div>
        </div>
    `;
    
    // Add total
    const total = subtotal - discountAmount;
    html += `
        <div class="receipt-divider"></div>
        <div class="receipt-item receipt-total">
            <div>TOTAL:</div>
            <div class="text-right">${total.toFixed(2)}</div>
        </div>
    `;
    
    // Add payment info
    const paymentMethod = document.getElementById('payment-method').value;
    const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
    const change = amountReceived - total;
    
    html += `
        <div class="receipt-divider"></div>
        <div class="receipt-summary">
            <div class="receipt-item">
                <div>PAYMENT:</div>
                <div class="text-right">${paymentMethod.toUpperCase()}</div>
            </div>
            <div class="receipt-item">
                <div>AMOUNT RECEIVED:</div>
                <div class="text-right">${amountReceived.toFixed(2)}</div>
            </div>
            <div class="receipt-item">
                <div>CHANGE:</div>
                <div class="text-right">${change.toFixed(2)}</div>
            </div>
        </div>
    `;
    
    return html;
}

// Function to reset the order form
function resetOrderForm() {
    // Clear the cart
    cart = [];
    updateCartDisplay();
    
    // Reset form fields
    document.getElementById('order-type').value = 'dine-in';
    document.getElementById('table-type').value = '';
    document.getElementById('table-number').innerHTML = '<option value="">Select Table Type First</option>';
    document.getElementById('table-number').disabled = true;
    document.getElementById('guest-type').value = 'regular';
    document.getElementById('guest-id-number').value = '';
    document.getElementById('id-number-container').style.display = 'none';
    document.getElementById('payment-method').value = 'cash';
    document.getElementById('amount-received').value = '';
    document.getElementById('change').value = '₱0.00';
    
    // Trigger change events to update UI
    document.getElementById('order-type').dispatchEvent(new Event('change'));
    document.getElementById('payment-method').dispatchEvent(new Event('change'));
}

// Add event listener for the confirm and print button
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...
    
    // Add this inside the DOMContentLoaded event listener
    document.getElementById('confirm-print-btn').addEventListener('click', function() {
        processOrder();
    });
});
</script>
