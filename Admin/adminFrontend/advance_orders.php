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
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 25px;
            border: 2px solid var(--gold);
            background: white;
            color: #2c2c2c;
            font-weight: 500;
            margin: 5px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gold);
            color: white;
        }

        .menu-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
            cursor: pointer;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .menu-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .menu-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 700;
            background: var(--gold);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .menu-body {
            padding: 20px;
        }

        .menu-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
        }

        .menu-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 12px;
            background: var(--gold);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-add-to-cart:hover {
            background: #b8941f;
            transform: translateY(-2px);
        }

        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 50px;
            width: 400px;
            height: calc(100vh - 50px);
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
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
        }

        .close-cart {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .cart-item-info h6 {
            margin: 0 0 5px 0;
            color: #2c2c2c;
            font-size: 1rem;
        }

        .cart-item-info small {
            color: #666;
        }

        .remove-item {
            background: #dc3545;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.8rem;
        }

        .remove-item:hover {
            background: #c82333;
        }

        .cart-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            background: var(--gold);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: #b8941f;
        }

        .quantity {
            font-weight: 600;
            font-size: 1.1rem;
            min-width: 30px;
            text-align: center;
        }

        .item-total {
            font-weight: 700;
            color: var(--gold);
            font-size: 1.1rem;
        }

        .cart-footer {
            padding: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .cart-summary {
            margin-bottom: 15px;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #2c2c2c;
        }

        .cart-summary-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            padding-top: 10px;
            border-top: 2px solid #f0f0f0;
            color: var(--gold);
        }

        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-checkout:hover:not(:disabled) {
            background: #218838;
        }

        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
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
            width: 6px;
        }

        .cart-body::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 3px;
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
    session_start();

    include __DIR__ . '/../adminBackend/mydb.php';

    $advanceOrder = $_SESSION['advance_order'] ?? null;

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
            <h5 class="mb-3"><i class="fas fa-filter"></i> Filter by Category</h5>

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

    <div class="modal fade" id="addonModal" tabindex="-1" aria-labelledby="addonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addonModalLabel">Select Addons</h5>
                    <button type="button" class="btn-close" onclick="closeAddonModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="addonContent">
                    <!-- Addons injected by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddonModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddons()">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Advance Order Modal -->
    <div class="modal fade" id="advanceOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Advance Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="advanceOrderContent">
                    <!-- Cart details will be injected here -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="confirmAdvanceOrder">Confirm</button>
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
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.category == cat) ? 'block' : 'none';
            });
        }

        // ---------------- ADD TO CART ----------------
        function addToCart(id) {
            const card = document.querySelector(`button[onclick="addToCart(${id})"]`).closest('.menu-card');
            const name = card.querySelector('.menu-name').innerText;
            const price = parseFloat(card.querySelector('.menu-price-badge').innerText.replace('₱', ''));

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
                html += `<div class="d-flex justify-content-between align-items-center mb-2">
                        <div><strong>${a.name}</strong> – ₱${parseFloat(a.price).toFixed(2)}</div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="addonMinus(${a.id})">-</button>
                            <span id="addonQty_${a.id}" class="mx-2">0</span>
                            <button class="btn btn-sm btn-outline-secondary" onclick="addonPlus(${a.id})">+</button>
                        </div>
                    </div>`;
            });
            content.innerHTML = html;
            bootstrapAddonModal.show();
        }

        function closeAddonModal() { bootstrapAddonModal.hide(); pendingItem = null; }
        function addonPlus(id) { document.getElementById('addonQty_' + id).innerText = parseInt(document.getElementById('addonQty_' + id).innerText) + 1; }
        function addonMinus(id) { const el = document.getElementById('addonQty_' + id); if (parseInt(el.innerText) > 0) el.innerText = parseInt(el.innerText) - 1; }
        function confirmAddons() {
            const addons = itemAddons[pendingItem.id];
            let selected = [];
            addons.forEach(a => { const qty = parseInt(document.getElementById('addonQty_' + a.id).innerText); if (qty > 0) selected.push({ addon_id: a.id, name: a.name, price: parseFloat(a.price), qty }); });
            addMainItem(pendingItem, selected);
            closeAddonModal();
        }

        // ---------------- UPDATE CART ----------------
        function updateCart() {
            const cartBody = document.getElementById('cartBody');
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (cart.length === 0) {
                cartBody.innerHTML = `<div class="text-center text-muted"><p>Your cart is empty</p></div>`;
                checkoutBtn.disabled = true;
                document.getElementById('subtotal').innerText = '₱0.00';
                document.getElementById('total').innerText = '₱0.00';
                return;
            }
            checkoutBtn.disabled = false;
            cartBody.innerHTML = cart.map(item => `
            <div class="cart-item">
                <h6>Item ID: ${item.id}</h6>
                <div>${item.name} – ₱${item.price.toFixed(2)}</div>
                ${item.addons.length > 0 ? `<ul class="mb-1">${item.addons.map(a => `<li>${a.name} x${a.qty} – ₱${(a.price * a.qty).toFixed(2)}</li>`).join('')}</ul>` : ''}
                <div class="d-flex align-items-center mb-2">
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="decreaseQty(${item.id})">-</button>
                    <span>${item.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="increaseQty(${item.id})">+</button>
                </div>
            </div>`).join('');

            let subtotal = 0;
            cart.forEach(item => { subtotal += item.price * item.qty; item.addons.forEach(a => subtotal += a.price * a.qty); });
            document.getElementById('subtotal').innerText = '₱' + subtotal.toFixed(2);
            document.getElementById('total').innerText = '₱' + subtotal.toFixed(2);
        }

        function increaseQty(id) { const item = cart.find(i => i.id === id); item.qty++; updateCart(); }
        function decreaseQty(id) { const item = cart.find(i => i.id === id); if (item.qty > 1) item.qty--; else cart = cart.filter(i => i.id !== id); updateCart(); }
        function toggleCart() { document.getElementById('cartSidebar').classList.toggle('open'); }

        // ---------------- ADVANCE ORDER MODAL ----------------
        document.getElementById('checkoutBtn').addEventListener('click', () => {
            <?php if ($advanceOrder): ?>
                const order = <?= json_encode($advanceOrder); ?>;
                let html = `<p><strong>Name:</strong> ${order.first} ${order.last}</p>
                    <p><strong>Contact:</strong> ${order.contact}</p>
                    <p><strong>Booking Date & Time:</strong> ${order.datetime}</p>
                    <hr>
                    <h6>Tables:</h6>
                    <ul>
                        ${order.tables.map((t, i) => `<li>Table #${t} (Type ID: ${order.tableTypes[i]})</li>`).join('')}
                    </ul>`;
                document.getElementById('advanceOrderContent').innerHTML = html;
                bootstrapAdvanceModal.show();
            <?php else: ?>
                alert('No advance order in session!');
            <?php endif; ?>
        });

        // ---------------- CONFIRM ADVANCE ORDER ----------------
        document.getElementById('confirmAdvanceOrder').addEventListener('click', () => {
            const order = <?= json_encode($advanceOrder); ?>;
            const formData = new URLSearchParams();
            formData.append('first', order.first);
            formData.append('last', order.last);
            formData.append('contact', order.contact);
            formData.append('datetime', order.datetime);
            order.tables.forEach((t, i) => {
                formData.append('tableTypes[]', order.tableTypes[i]);
                formData.append('tables[]', t);
            });

            fetch('../Admin/adminBackend/booking_save_order.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert('Advance order saved successfully!');
                        bootstrapAdvanceModal.hide();
                        location.reload();
                    } else alert('Error saving advance order.');
                })
                .catch(err => console.error(err));
        });
    </script>





</body>

</html>