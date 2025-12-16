<?php
$advanceOrder = $_SESSION['advance_order'] ?? null;

$tableNames = [];
if ($advanceOrder && !empty($advanceOrder['tables'])) {
    include __DIR__ . '/../adminBackend/mydb.php';

    $typeIds = array_map('intval', $advanceOrder['tableTypes']);
    $result = $conn->query("SELECT id, table_name FROM table_types WHERE id IN (" . implode(',', $typeIds) . ")");

    while ($row = $result->fetch_assoc()) {
        $tableNames[$row['id']] = $row['table_name'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Menu POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --gold: #D4AF37;
            --dark-bg: #2c2c2c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        .top-navbar {
            background: linear-gradient(135deg, var(--gold) 0%, #b8941f 100%);
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .top-navbar .navbar-brand {
            color: #2c2c2c;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .top-navbar .nav-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-navbar .nav-icons a {
            color: #2c2c2c;
            font-size: 1.2rem;
            position: relative;
            transition: transform 0.2s;
            text-decoration: none;
        }

        .top-navbar .nav-icons a:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .main-content {
            margin-top: 50px;
            padding: 30px;
            min-height: calc(100vh - 50px);
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-section h5 {
            color: #2c2c2c;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .filter-btn {
            padding: 10px 24px;
            border-radius: 25px;
            border: 2px solid var(--gold);
            background: white;
            color: #2c2c2c;
            font-weight: 500;
            margin: 5px;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .menu-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .menu-image-container {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        }

        .menu-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .menu-card:hover .menu-image {
            transform: scale(1.05);
        }

        .menu-price-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 10px 18px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 700;
            background: var(--gold);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .menu-body {
            padding: 20px;
        }

        .menu-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .menu-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            min-height: 40px;
            line-height: 1.5;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 12px;
            background: var(--gold);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-add-to-cart:hover:not(:disabled) {
            background: #b8941f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
        }

        .btn-add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .cart-sidebar {
            position: fixed;
            right: -420px;
            top: 50px;
            width: 420px;
            height: calc(100vh - 50px);
            background: white;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            transition: right 0.3s ease;
            z-index: 1025;
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.open {
            right: 0;
        }

        .cart-header {
            padding: 20px;
            background: var(--gold);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .close-cart {
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            transition: transform 0.2s;
            line-height: 1;
        }

        .close-cart:hover {
            transform: scale(1.1);
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .cart-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }

        .cart-item-info h6 {
            margin: 0 0 5px 0;
            color: #2c2c2c;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .cart-item-info small {
            color: #666;
            font-size: 0.9rem;
        }

        .cart-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 5px 10px;
            border-radius: 25px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .qty-btn {
            background: var(--gold);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .qty-btn:hover {
            background: #b8941f;
            transform: scale(1.1);
        }

        .quantity {
            font-weight: 600;
            font-size: 1.1rem;
            min-width: 30px;
            text-align: center;
            color: #2c2c2c;
        }

        .item-total {
            font-weight: 700;
            color: var(--gold);
            font-size: 1.15rem;
        }

        .cart-footer {
            padding: 20px;
            border-top: 2px solid #f0f0f0;
            background: #fafafa;
        }

        .cart-summary {
            margin-bottom: 15px;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #2c2c2c;
            font-size: 1rem;
        }

        .cart-summary-row.total {
            font-size: 1.4rem;
            font-weight: 700;
            padding-top: 12px;
            border-top: 2px solid #e0e0e0;
            color: var(--gold);
        }

        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-checkout:hover:not(:disabled) {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-cart i {
            font-size: 4.5rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        .empty-cart p {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .cart-icon-container {
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Modal Improvements */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 20px 24px;
            background: var(--gold);
            color: white;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .addon-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .addon-item strong {
            color: #2c2c2c;
            font-size: 1rem;
        }

        .addon-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .addon-controls button {
            background: var(--gold);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .addon-controls button:hover {
            background: #b8941f;
            transform: scale(1.1);
        }

        .addon-controls span {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .order-info-section {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .order-info-section h6 {
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 1.1rem;
        }

        .order-info-section p,
        .order-info-section ul {
            margin-bottom: 8px;
            color: #2c2c2c;
        }

        .order-info-section ul {
            padding-left: 20px;
        }

        .cart-addons {
            margin-left: 20px;
            margin-top: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .cart-addons li {
            margin-bottom: 4px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gold);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .filter-section {
                overflow-x: auto;
                white-space: nowrap;
            }

            .menu-card {
                margin-bottom: 15px;
            }

            .top-navbar .navbar-brand {
                font-size: 0.9rem;
            }
        }

        .cart-body::-webkit-scrollbar {
            width: 8px;
        }

        .cart-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .cart-body::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 4px;
        }

        .cart-body::-webkit-scrollbar-thumb:hover {
            background: #b8941f;
        }
    </style>
</head>

<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="#" onclick="toggleCart()" class="cart-icon-container">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
                <a href="#"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </nav>

    <?php
    include __DIR__ . '/../adminBackend/mydb.php';


    $categories = $conn->query("SELECT id, display_name FROM menu_categories ORDER BY display_name");

    $items = $conn->query("SELECT * FROM menu_items ORDER BY category_id");

    $addonsQuery = $conn->query("SELECT * FROM menu_items_addons ORDER BY menu_item_id");
    $addons = [];
    while ($a = $addonsQuery->fetch_assoc()) {
        $addons[$a['menu_item_id']][] = $a;
    }
    ?>

    <main class="main-content">
        <div class="filter-section">
            <h5><i class="fas fa-filter"></i> Filter by Category</h5>

            <div id="categoryFilters">
                <button class="filter-btn active" onclick="filterByCategory('all')">All Items</button>

                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <button class="filter-btn" onclick="filterByCategory(<?= $cat['id'] ?>)">
                        <?= htmlspecialchars($cat['display_name']) ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="row" id="menuItems">
            <?php while ($item = $items->fetch_assoc()): ?>

                <?php
                $img = "../Admin/adminBackend/menu_item_images/" . $item['image_path'];
                $isAvailable = $item['availability'] == 1;
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 menu-item" data-category="<?= $item['category_id'] ?>">
                    <div class="menu-card">
                        <div class="menu-image-container">
                            <img src="../Admin/adminBackend/menu_item_images/<?= htmlspecialchars($item['image_path']) ?>"
                                class="menu-image" alt="<?= htmlspecialchars($item['name']) ?>"
                                onerror="this.onerror=null; this.src='../Admin/adminBackend/menu_item_images/default.png';">
                            <div class="menu-price-badge">₱<?= number_format($item['price'], 2) ?></div>
                        </div>

                        <div class="menu-body">
                            <div class="menu-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="menu-description"><?= htmlspecialchars($item['description']) ?></div>

                            <button class="btn-add-to-cart" <?= $isAvailable ? "onclick='addToCart({$item['id']})'" : "disabled" ?>>
                                <i class="fas fa-plus-circle"></i>
                                <?= $isAvailable ? "Add to Cart" : "Unavailable" ?>
                            </button>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>
    </main>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h4><i class="fas fa-shopping-cart"></i> Your Order</h4>
            <button class="close-cart" onclick="toggleCart()">×</button>
        </div>
        <div class="cart-body" id="cartBody">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <small>Add items to get started</small>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">₱0.00</span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total:</span>
                    <span id="total">₱0.00</span>
                </div>
            </div>
            <button class="btn-checkout" onclick="checkout()" id="checkoutBtn" disabled>
                <i class="fas fa-check-circle"></i> Proceed to Checkout
            </button>
        </div>
    </div>

    <!-- Addon Modal -->
    <div class="modal fade" id="addonModal" tabindex="-1" aria-labelledby="addonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addonModalLabel"><i class="fas fa-plus-circle"></i> Select Addons</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeAddonModal()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="addonContent">
                    <!-- Addons injected by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddonModal()">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        style="background: var(--gold); border-color: var(--gold);"
                        onclick="confirmAddons()">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Advance Order Modal -->
    <div class="modal fade" id="advanceOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar-check"></i> Advance Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="advanceOrderContent">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" style="background: var(--gold); border-color: var(--gold);"
                        id="confirmAdvanceOrder">
                        <i class="fas fa-check-circle"></i> Confirm Order
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const itemAddons = <?= json_encode($addons); ?>;
        let cart = [];
        let pendingItem = null;
        let bootstrapAddonModal = new bootstrap.Modal(document.getElementById('addonModal'), {});
        let bootstrapAdvanceModal = new bootstrap.Modal(document.getElementById('advanceOrderModal'));

        // ---------------- FILTER ----------------
        function filterByCategory(cat, e) {
            document.querySelectorAll('#categoryFilters button').forEach(btn => btn.classList.remove('active'));
            if (e) e.currentTarget.classList.add('active');
            else {
                if (cat === 'all') document.querySelector('#categoryFilters button').classList.add('active');
            }
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.category == cat) ? 'block' : 'none';
            });
        }

        // ---------------- ADD TO CART ----------------
        function addToCart(id) {
            const card = document.querySelector(`button[onclick = "addToCart(${id})"]`).closest('.menu-card');
            const name = card.querySelector('.menu-name').innerText;
            const price = parseFloat(card.querySelector('.menu-price-badge').innerText.replace('₱', '').replace(',', ''));

            if (itemAddons[id] && itemAddons[id].length > 0) {
                pendingItem = { id, name, price };
                openAddonModal(id);
                return;
            }
            addMainItem({ id, name, price }, []);
        }

        function addMainItem(itemData, selectedAddons = []) {
            const existing = cart.find(i => i.id === itemData.id && JSON.stringify(i.addons) === JSON.stringify(selectedAddons));
            if (existing) existing.qty++;
            else cart.push({ id: itemData.id, name: itemData.name, price: itemData.price, qty: 1, addons: selectedAddons });
            updateCart();
            document.getElementById('cartSidebar').classList.add('open');
        }

        // ---------------- ADDON MODAL ----------------
        function openAddonModal(id) {
            const content = document.getElementById('addonContent');
            const addons = itemAddons[id];
            let html = '';
            addons.forEach(a => {
                html += `<div class="addon-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>${a.name}</strong>
                    <div class="text-muted">₱${parseFloat(a.price).toFixed(2)}</div>
                </div>
                <div class="addon-controls">
                    <button onclick="addonMinus(${a.id})">−</button>
                    <span id="addonQty_${a.id}">0</span>
                    <button onclick="addonPlus(${a.id})">+</button>
                </div>
            </div > `;
            });
            content.innerHTML = html;
            bootstrapAddonModal.show();
        }

        function closeAddonModal() {
            bootstrapAddonModal.hide();
            pendingItem = null;
        }

        function addonPlus(id) {
            document.getElementById('addonQty_' + id).innerText = parseInt(document.getElementById('addonQty_' + id).innerText) + 1;
        }

        function addonMinus(id) {
            const el = document.getElementById('addonQty_' + id);
            if (parseInt(el.innerText) > 0) el.innerText = parseInt(el.innerText) - 1;
        }

        function confirmAddons() {
            const addons = itemAddons[pendingItem.id];
            let selected = [];
            addons.forEach(a => {
                const qty = parseInt(document.getElementById('addonQty_' + a.id).innerText);
                if (qty > 0) selected.push({ addon_id: a.id, name: a.name, price: parseFloat(a.price), qty });
            });
            addMainItem(pendingItem, selected);
            closeAddonModal();
        }

        // ---------------- UPDATE CART ----------------
        function updateCart() {
            const cartBody = document.getElementById('cartBody');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const cartCount = document.getElementById('cartCount');

            let totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
            cartCount.innerText = totalItems;

            if (cart.length === 0) {
                cartBody.innerHTML = `<div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty</p>
            <small>Add items to get started</small>
        </div > `;
                checkoutBtn.disabled = true;
                document.getElementById('subtotal').innerText = '₱0.00';
                document.getElementById('total').innerText = '₱0.00';
                cartCount.style.display = 'none';
                return;
            }

            cartCount.style.display = 'flex';
            checkoutBtn.disabled = false;

            let html = '';
            cart.forEach((item, idx) => {
                let itemSubtotal = item.price * item.qty;
                item.addons.forEach(a => itemSubtotal += a.price * a.qty * item.qty);

                html += `
            <div class="cart-item">
                <div class="cart-item-header">
                    <div class="cart-item-info">
                        <h6>${item.name}</h6>
                        <small>₱${item.price.toFixed(2)} each</small>
                    </div>
                </div>
            ${item.addons.length > 0 ? `<ul class="cart-addons">${item.addons.map(a => `<li>${a.name} × ${a.qty} – ₱${(a.price * a.qty * item.qty).toFixed(2)}</li>`).join('')}</ul>` : ''}
        <div class="cart-item-controls">
            <div class="quantity-controls">
                <button class="qty-btn" onclick="decreaseQty(${idx})">−</button>
                <span class="quantity">${item.qty}</span>
                <button class="qty-btn" onclick="increaseQty(${idx})">+</button>
            </div>
            <div class="item-total">₱${itemSubtotal.toFixed(2)}</div>
        </div>
        </div > `;
            });

            cartBody.innerHTML = html;

            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.price * item.qty;
                item.addons.forEach(a => subtotal += a.price * a.qty * item.qty);
            });
            document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('total').innerText = '₱' + subtotal.toFixed(2);
        }

        function increaseQty(idx) {
            cart[idx].qty++;
            updateCart();
        }

        function decreaseQty(idx) {
            if (cart[idx].qty > 1) {
                cart[idx].qty--;
            } else {
                cart.splice(idx, 1);
            }
            updateCart();
        }

        function toggleCart() {
            document.getElementById('cartSidebar').classList.toggle('open');
        }

        // ---------------- ADVANCE ORDER MODAL ----------------
        document.getElementById('checkoutBtn').addEventListener('click', () => {
            <?php if ($advanceOrder): ?>
                const order = <?= json_encode($advanceOrder); ?>;
                const tableNames = <?= json_encode($tableNames ?? []) ?>;

                let html = `
                <div class="order-info-section">
                        <h6><i class="fas fa-user"></i> Customer Information</h6>
                        <p><strong>Name:</strong> ${order.first} ${order.last}</p>
                        <p><strong>Contact:</strong> ${order.contact}</p>
                        <p><strong>Booking Date & Time:</strong> ${order.datetime}</p>
                    </div >
            
                    <div class="order-info-section">
                        <h6><i class="fas fa-table"></i> Selected Tables</h6>
                        <ul>
                            ${order.tables.map((t, i) => `
                        <li>Table #${t} - ${tableNames[order.tableTypes[i]] ?? 'Unknown'}</li>`).join('')}
                        </ul>
                    </div>
            
                    <div class="order-info-section">
                        <h6><i class="fas fa-utensils"></i> Order Items</h6>
                `;

                if (cart.length === 0) {
                    html += '<p class="text-muted">No items in cart.</p>';
                } else {
                    html += '<ul>';
                    let subtotal = 0;
                    cart.forEach(item => {
                        let itemTotal = item.price * item.qty;
                        html += `<li><strong>${item.name}</strong> × ${item.qty} – ₱${itemTotal.toFixed(2)}`;
                        if (item.addons.length > 0) {
                            html += '<ul style="margin-top: 5px;">';
                            item.addons.forEach(a => {
                                let addonTotal = a.price * a.qty * item.qty;
                                itemTotal += addonTotal;
                                html += `<li>${a.name} × ${a.qty} – ₱${addonTotal.toFixed(2)}</li>`;
                            });
                            html += '</ul>';
                        }
                        html += '</li>';
                        subtotal += itemTotal;
                    });
                    html += '</ul>';
                    html += `<div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e0e0e0;">
                        <h5 style="color: var(--gold); margin: 0;"><strong>Total: ₱${subtotal.toFixed(2)}</strong></h5>
                    </div>`;
                }
                html += '</div>';

                document.getElementById('advanceOrderContent').innerHTML = html;
                bootstrapAdvanceModal.show();
            <?php else: ?>
                alert('No advance order in session!');
            <?php endif; ?>
        });

        // ---------------- CONFIRM ADVANCE ORDER ----------------
        document.getElementById('confirmAdvanceOrder').addEventListener('click', () => {

            const order = <?= json_encode($advanceOrder); ?>;
            const formData = new FormData();

            formData.append('first', order.first);
            formData.append('last', order.last);
            formData.append('contact', order.contact);
            formData.append('datetime', order.datetime);

            let subtotal = 0;
            cart.forEach(item => {
                let itemTotal = item.price * item.qty;
                item.addons.forEach(a => {
                    itemTotal += a.price * a.qty * item.qty;
                });
                subtotal += itemTotal;

                formData.append('cartItems[]', JSON.stringify({
                    id: item.id,
                    qty: item.qty,
                    addons: item.addons.map(a => ({
                        addon_id: a.addon_id,
                        qty: a.qty
                    }))
                }));
            });

            // SEND SUBTOTAL AS TOTAL
            formData.append('total', subtotal.toFixed(2));

            fetch('../Admin/adminBackend/booking_save_order_advance.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert('Advance order saved successfully!');
                        window.location.href = '../Admin/index.php?table-booking';
                    } else {
                        alert(res.message || 'Error saving order');
                    }
                })
                .catch(err => {
                    alert('An error occurred while saving the order.');
                    console.error(err);
                });
        });


    </script>

</body>

</html>