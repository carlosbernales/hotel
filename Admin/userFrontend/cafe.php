<?php
include '../adminBackend/mydb.php';
include '../userFrontend/header.php';

// ------------------------
// 1. GET ALL CATEGORIES
// ------------------------
$categories = [];
$sql = "SELECT * FROM menu_categories ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// ------------------------
// 2. GET SELECTED CATEGORY ID
// ------------------------
$category_id = isset($_GET['category']) ? intval($_GET['category']) : ($categories[0]['id'] ?? 0);

// ------------------------
// 3. GET MENU ITEMS BASED ON CATEGORY CLICKED
// ------------------------
$menu_items = [];
$sql_items = "SELECT * FROM menu_items WHERE category_id = $category_id ORDER BY id ASC";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    while ($row = $result_items->fetch_assoc()) {
        $menu_items[] = $row;
    }
}
?>

    <div class="pos-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-3 mb-4">
                   <div class="menu-sidebar d-none d-md-block">
    <h4>Category</h4>

    <?php foreach ($categories as $cat): ?>
        <a href="?category=<?= $cat['id'] ?>">
            <button 
                class="category-btn <?= ($cat['id'] == $category_id) ? 'active' : '' ?>"
                data-category="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['display_name']) ?>
            </button>
        </a>
    <?php endforeach; ?>
</div>


<!-- MOBILE CATEGORY DROPDOWN -->
<div class="d-md-none">
    <div class="dropdown w-100">
        <button class="btn btn-category-dropdown dropdown-toggle w-100" 
                type="button" 
                id="mobileCategoryDropdown" 
                data-bs-toggle="dropdown">
            <i class="fas fa-bars me-2"></i>
            <span id="selectedCategory">
                <?= htmlspecialchars(
                        $categories[array_search($category_id, array_column($categories, 'id'))]['display_name']
                    ) ?>
            </span>
        </button>

        <ul class="dropdown-menu w-100 category-dropdown-menu">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a class="dropdown-item <?= ($cat['id'] == $category_id) ? 'active' : '' ?>" 
                       href="?category=<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['display_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
                </div>

                <!-- Menu Items -->
                <div class="col-lg-7 col-md-6">
                    <div class="store-hours">
                        <h5><i class="fas fa-clock me-2"></i>Store Hours</h5>
                        <p><strong>Operating Hours:</strong> 6:30 AM - 11:00 PM</p>
                        <p><strong>Current Time:</strong> <span id="currentTime">2:08 PM</span></p>
                    </div>

                    <div class="menu-section">
                        <h2 class="section-title">
                            <?= htmlspecialchars($categories[array_search($category_id, array_column($categories, 'id'))]['display_name']) ?>
                        </h2>

                        <div class="menu-grid">
                            <?php foreach ($menu_items as $item): ?>
                                <div class="menu-card">
                                    <img src="../../Admin/adminBackend/menu_item_images/<?= htmlspecialchars($item['image_path']) ?>" 
                                        alt="<?= htmlspecialchars($item['name']) ?>">


                                    <div class="menu-card-body">
                                        <h5 class="menu-card-title"><?= htmlspecialchars($item['name']) ?></h5>

                                        <p class="menu-card-price">₱<?= number_format($item['price'], 2) ?></p>

                                        <p class="menu-card-description">
                                            <?= htmlspecialchars($item['description']) ?>
                                        </p>

                                        <?php if ($item['availability'] > 0): ?>
                                            <button class="add-to-cart-btn" 
                                                    onclick="addToCart('<?= $item['name'] ?>', <?= $item['price'] ?>)">
                                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="not-available-btn" disabled>
                                                Not Available
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Current Order -->
                <div class="col-lg-3 col-md-3">
                    <div class="order-panel">
                        <h4>Current Order</h4>
                        <div id="orderItems">
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <p>Your cart is empty</p>
                            </div>
                        </div>
                        <div class="order-summary">
                            <div class="order-total">
                                <span>Total Items:</span>
                                <span id="totalItems">0</span>
                            </div>
                            <div class="order-total final">
                                <span>Total Amount:</span>
                                <span id="totalAmount">₱ 0.00</span>
                            </div>
                            <button class="place-order-btn" onclick="placeOrder()">
                                <i class="fas fa-check-circle me-2"></i>PLACE ORDER
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include '../userFrontend/footer.php';
?>
