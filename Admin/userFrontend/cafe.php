<?php
session_start();

// Example: user_id is set when user logs in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to add items to cart.");
}

$user_id = $_SESSION['user_id'];

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
//////////////////////
$cartItems = [];
$totalItems = 0;
$totalAmount = 0;

$sql = "SELECT c.id, m.name, m.image_path, m.price, c.quantity
        FROM add_to_cart c
        JOIN menu_items m ON m.id = c.menu_fk_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Fetch add-ons for this cart item
    $addons = [];
    $addons_sql = "SELECT id, adds_fk_id, name, price, quantity FROM addcart_addson WHERE addCart_fk_id = ?";
    $addons_stmt = $conn->prepare($addons_sql);
    $addons_stmt->bind_param("i", $row['id']);
    $addons_stmt->execute();
    $addons_result = $addons_stmt->get_result();
    while ($addon = $addons_result->fetch_assoc()) {
        $addons[] = $addon;
    }

    $row['addons'] = $addons;

    $cartItems[] = $row;
    $totalItems += $row['quantity'];
    $totalAmount += $row['price'] * $row['quantity'];
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
                            <button class="category-btn <?= ($cat['id'] == $category_id) ? 'active' : '' ?>"
                                data-category="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['display_name']) ?>
                            </button>
                        </a>
                    <?php endforeach; ?>
                </div>


                <!-- MOBILE CATEGORY DROPDOWN -->
                <div class="d-md-none">
                    <div class="dropdown w-100">
                        <button class="btn btn-category-dropdown dropdown-toggle w-100" type="button"
                            id="mobileCategoryDropdown" data-bs-toggle="dropdown">
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
                                    <p class="menu-card-description"><?= htmlspecialchars($item['description']) ?></p>

                                    <?php if ($item['availability'] > 0): ?>
                                        <button class="add-to-cart-btn" data-user="<?= $user_id ?>"
                                            data-category="<?= $item['category_id'] ?>" data-menu="<?= $item['id'] ?>"
                                            data-price="<?= $item['price'] ?>" data-image="<?= $item['image_path'] ?>"
                                            data-quantity="1">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <button class="not-available-btn" disabled>Not Available</button>
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

                    <div id="orderItems" class="cart-items-container">
                        <?php if (empty($cartItems)): ?>
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <p>Your cart is empty</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-cart-id="<?= $item['id'] ?>">
                                    <img src="../../Admin/adminBackend/menu_item_images/<?= htmlspecialchars($item['image_path']) ?>"
                                        class="cart-item-img">
                                    <div class="cart-item-info">
                                        <p class="cart-item-name"><?= htmlspecialchars($item['name']) ?></p>
                                        <p class="cart-item-quantity">Qty: <span
                                                class="quantity"><?= $item['quantity'] ?></span></p>
                                        <p class="cart-item-price">₱ <?= number_format($item['price'] * $item['quantity'], 2) ?>
                                        </p>

                                        <div class="cart-item-controls">
                                            <button class="decrease-btn">-</button>
                                            <button class="increase-btn">+</button>
                                            <button class="remove-btn">x</button>
                                        </div>

                                        <!-- Add-ons -->
                                        <?php if (!empty($item['addons'])): ?>
                                            <div class="cart-addons">
                                                <?php foreach ($item['addons'] as $addon): ?>
                                                    <div class="cart-addon" data-addon-id="<?= $addon['id'] ?>"
                                                        data-parent-cart="<?= $item['id'] ?>">
                                                        <p class="addon-name"><?= htmlspecialchars($addon['name']) ?></p>
                                                        <p class="addon-quantity">Qty: <span
                                                                class="quantity"><?= $addon['quantity'] ?></span></p>
                                                        <p class="addon-price">₱
                                                            <?= number_format($addon['price'] * $addon['quantity'], 2) ?>
                                                        </p>
                                                        <div class="addon-controls">
                                                            <button class="addon-decrease">-</button>
                                                            <button class="addon-increase">+</button>
                                                            <button class="addon-remove">x</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>


                        <?php endif; ?>
                    </div>

                    <div class="order-summary">
                        <div class="order-total">
                            <span>Total Items:</span>
                            <span id="totalItems"><?= $totalItems ?></span>
                        </div>
                        <div class="order-total final">
                            <span>Total Amount:</span>
                            <span id="totalAmount">₱ <?= number_format($totalAmount, 2) ?></span>
                        </div>
                        <button class="place-order-btn" onclick="placeOrder()">
                            <i class="fas fa-check-circle me-2"></i> PLACE ORDER
                        </button>
                    </div>
                </div>
            </div>
            <div id="addonsModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h4>Add-ons</h4>
                    <div id="addonsList"></div>
                    <div class="modal-actions">
                        <button id="modalAddCart">Add to Cart</button>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
<!-- Add-ons Modal -->


<?php
include '../userFrontend/footer.php';
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderItems = document.getElementById('orderItems');
        const addonsModal = document.getElementById('addonsModal');
        const addonsList = document.getElementById('addonsList');

        // ---- Update totals dynamically ----
        function updateCartTotals() {
            let totalItems = 0, totalAmount = 0;

            orderItems.querySelectorAll('.cart-item').forEach(item => {
                const qty = parseInt(item.querySelector('.quantity').textContent);
                const unitPrice = parseFloat(item.querySelector('.cart-item-price').dataset.unitPrice);
                totalItems += qty;
                totalAmount += unitPrice * qty;

                item.querySelectorAll('.cart-addon').forEach(addon => {
                    const addonQty = parseInt(addon.querySelector('.quantity').textContent);
                    const addonPrice = parseFloat(addon.querySelector('.addon-price').dataset.unitPrice);
                    totalItems += addonQty;
                    totalAmount += addonPrice * addonQty;
                });
            });

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalAmount').textContent = `₱ ${totalAmount.toFixed(2)}`;
        }

        // ---- Add main item to cart ----
        async function addToCart(user_id, category_fk_id, menu_fk_id, price, quantity, image) {
            const res = await fetch('../userBackend/save_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ user_id, category_fk_id, menu_fk_id, price, quantity, image })
            });
            const data = await res.json();
            let cartItem = orderItems.querySelector(`[data-cart-id='${data.cart_id}']`);

            if (cartItem) {
                const qtyElem = cartItem.querySelector('.quantity');
                qtyElem.textContent = parseInt(qtyElem.textContent) + quantity;
                cartItem.querySelector('.cart-item-price').textContent = `₱ ${(price * parseInt(qtyElem.textContent)).toFixed(2)}`;
            } else {
                const div = document.createElement('div');
                div.className = 'cart-item';
                div.dataset.cartId = data.cart_id;
                div.innerHTML = `
                <img src="../../Admin/adminBackend/menu_item_images/${image}" class="cart-item-img">
                <div class="cart-item-info">
                    <p class="cart-item-name">${document.querySelector(`.add-to-cart-btn[data-menu="${menu_fk_id}"]`).closest('.menu-card-body').querySelector('.menu-card-title').textContent}</p>
                    <p class="cart-item-quantity">Qty: <span class="quantity">${quantity}</span></p>
                    <p class="cart-item-price" data-unit-price="${price}">₱ ${price.toFixed(2)}</p>
                    <div class="cart-item-controls">
                        <button class="decrease-btn">-</button>
                        <button class="increase-btn">+</button>
                        <button class="remove-btn">x</button>
                    </div>
                    <div class="cart-addons"></div>
                </div>
            `;
                orderItems.appendChild(div);
            }
            updateCartTotals();
            return data.cart_id;
        }

        // ---- Add add-on to cart dynamically ----
        function addAddonToCart(addCart_fk_id, addon) {
            // Find the parent cart item
            const cartItem = orderItems.querySelector(`[data-cart-id='${addCart_fk_id}']`);
            if (!cartItem) return;

            const addonsContainer = cartItem.querySelector('.cart-addons');

            // Check if the add-on already exists for this cart item
            let existing = addonsContainer.querySelector(`[data-addon-id='${addon.addon_id}']`);

            if (existing) {
                // Update quantity and price dynamically
                const qtyElem = existing.querySelector('.quantity');
                const newQty = parseInt(qtyElem.textContent) + addon.quantity;
                qtyElem.textContent = newQty;

                const priceElem = existing.querySelector('.addon-price');
                priceElem.textContent = `₱ ${(addon.price * newQty).toFixed(2)}`;
            } else {
                // Create new add-on element
                const div = document.createElement('div');
                div.className = 'cart-addon';
                div.dataset.addonId = addon.addon_id;
                div.dataset.parentCart = addCart_fk_id;
                div.innerHTML = `
            <p class="addon-name">${addon.name}</p>
            <p class="addon-quantity">Qty: <span class="quantity">${addon.quantity}</span></p>
            <p class="addon-price" data-unit-price="${addon.price}">₱ ${(addon.price * addon.quantity).toFixed(2)}</p>
            <div class="addon-controls">
                <button class="addon-decrease">-</button>
                <button class="addon-increase">+</button>
                <button class="addon-remove">x</button>
            </div>
        `;
                addonsContainer.appendChild(div);
            }

            updateCartTotals();
        }


        // ---- Open add-ons modal ----
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const user_id = this.dataset.user;
                const menu_fk_id = this.dataset.menu;
                const category_fk_id = this.dataset.category;
                const price = parseFloat(this.dataset.price);
                const image = this.dataset.image;

                fetch('../userBackend/get_addons.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ menu_fk_id })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.addons.length > 0) {
                            addonsList.innerHTML = '';
                            data.addons.forEach(addon => {
                                const div = document.createElement('div');
                                div.className = 'addon-item';
                                div.dataset.id = addon.id;
                                div.dataset.name = addon.name;
                                div.dataset.price = addon.price;
                                div.innerHTML = `
                            <span>${addon.name} - ₱${addon.price}</span>
                            <span class="addon-controls">
                                <button class="minus-btn">-</button>
                                <span class="addon-qty">0</span>
                                <button class="plus-btn">+</button>
                            </span>
                        `;
                                addonsList.appendChild(div);
                            });
                            addonsModal.style.display = 'flex';
                            addonsModal.dataset.user = user_id;
                            addonsModal.dataset.menu = menu_fk_id;
                            addonsModal.dataset.category = category_fk_id;
                            addonsModal.dataset.price = price;
                            addonsModal.dataset.image = image;
                        } else {
                            addToCart(user_id, category_fk_id, menu_fk_id, price, 1, image);
                        }
                    });
            });
        });

        // ---- Modal + / - buttons ----
        addonsList.addEventListener('click', function (e) {
            const item = e.target.closest('.addon-item');
            if (!item) return;
            const qtyElem = item.querySelector('.addon-qty');
            let qty = parseInt(qtyElem.textContent);
            if (e.target.classList.contains('plus-btn')) qty++;
            if (e.target.classList.contains('minus-btn')) qty = Math.max(0, qty - 1);
            qtyElem.textContent = qty;
        });

        // ---- Close modal ----
        document.querySelector('.close-modal').addEventListener('click', () => addonsModal.style.display = 'none');

        // ---- Add selected add-ons to cart ----
        document.getElementById('modalAddCart').addEventListener('click', () => {
            const user_id = addonsModal.dataset.user;
            const menu_fk_id = addonsModal.dataset.menu;
            const category_fk_id = addonsModal.dataset.category;
            const price = parseFloat(addonsModal.dataset.price);
            const image = addonsModal.dataset.image;

            addToCart(user_id, category_fk_id, menu_fk_id, price, 1, image).then(addCart_fk_id => {
                addonsList.querySelectorAll('.addon-item').forEach(addon => {
                    const qty = parseInt(addon.querySelector('.addon-qty').textContent);
                    if (qty > 0) {
                        fetch('../userBackend/save_cart_addons.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({
                                addCart_fk_id,
                                adds_fk_id: addon.dataset.id,
                                name: addon.dataset.name,
                                price: addon.dataset.price,
                                quantity: qty
                            })
                        }).then(res => res.json())
                            .then(data => {
                                if (data.status) {
                                    addAddonToCart(addCart_fk_id, {
                                        addon_id: addon.dataset.id,
                                        name: addon.dataset.name,
                                        price: parseFloat(addon.dataset.price),
                                        quantity: qty
                                    });
                                }
                            });
                    }
                });
                addonsModal.style.display = 'none';
            });
        });

        // ---- Delegate main item and add-on controls ----
        orderItems.addEventListener('click', function (e) {
            const target = e.target;

            // Main items
            const cartItem = target.closest('.cart-item');
            if (cartItem) {
                const cart_id = cartItem.dataset.cartId;
                let action = '';
                if (target.classList.contains('increase-btn')) action = 'increase';
                if (target.classList.contains('decrease-btn')) action = 'decrease';
                if (target.classList.contains('remove-btn')) action = 'remove';
                if (action) {
                    fetch('../userBackend/update_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ cart_id, action })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (action === 'remove' || data.quantity <= 0) cartItem.remove();
                            else {
                                cartItem.querySelector('.quantity').textContent = data.quantity;
                                cartItem.querySelector('.cart-item-price').textContent = `₱ ${(data.price * data.quantity).toFixed(2)}`;
                            }
                            updateCartTotals();
                        });
                }
            }

            // Add-ons
            const addonDiv = target.closest('.cart-addon');
            if (addonDiv) {
                const addon_id = addonDiv.dataset.addonId;
                let action = '';
                if (target.classList.contains('addon-increase')) action = 'increase';
                if (target.classList.contains('addon-decrease')) action = 'decrease';
                if (target.classList.contains('addon-remove')) action = 'remove';
                if (action) {
                    fetch('../userBackend/update_cart_addons.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ addon_id, action })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (action === 'remove' || data.quantity <= 0) addonDiv.remove();
                            else {
                                addonDiv.querySelector('.quantity').textContent = data.quantity;
                                addonDiv.querySelector('.addon-price').textContent = `₱ ${(data.price * data.quantity).toFixed(2)}`;
                            }
                            updateCartTotals();
                        });
                }
            }
        });

        // ---- Initialize totals ----
        updateCartTotals();
    });
</script>