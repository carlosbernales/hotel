<?php
include __DIR__ . '/../adminBackend/mydb.php';

$advanceOrderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$advanceOrder = null;
$tableNames = [];

if ($advanceOrderId > 0) {
    $stmtOrder = $conn->prepare("SELECT * FROM orders_table WHERE id = ?");
    $stmtOrder->bind_param("i", $advanceOrderId);
    $stmtOrder->execute();
    $resOrder = $stmtOrder->get_result();
    $advanceOrder = $resOrder->fetch_assoc();

    $stmtTables = $conn->prepare("
        SELECT t.id, t.table_name, ot.table_number 
        FROM orders_table_type ot
        JOIN table_types t ON ot.table_type_fk_id = t.id
        WHERE ot.table_booking_fk_id = ?
    ");
    $stmtTables->bind_param("i", $advanceOrderId);
    $stmtTables->execute();
    $resTables = $stmtTables->get_result();
    while ($row = $resTables->fetch_assoc()) {
        $tableNames[] = [
            'id' => $row['id'],
            'name' => $row['table_name'],
            'number' => $row['table_number']
        ];
    }
}

$orderItems = [];

if ($advanceOrderId > 0) {
    $stmtItems = $conn->prepare("SELECT id, item_name, quantity, unit_price FROM order_items WHERE order_fk_id = ?");
    $stmtItems->bind_param("i", $advanceOrderId);
    $stmtItems->execute();
    $resItems = $stmtItems->get_result();

    while ($item = $resItems->fetch_assoc()) {
        $stmtAddons = $conn->prepare("SELECT id, addon_name, quantity, price FROM order_item_addons WHERE order_item_fk_id = ?");
        $stmtAddons->bind_param("i", $item['id']);
        $stmtAddons->execute();
        $resAddons = $stmtAddons->get_result();

        $addonsArr = [];
        while ($a = $resAddons->fetch_assoc()) {
            $addonsArr[] = [
                'addon_id' => $a['id'],
                'name' => $a['addon_name'],
                'qty' => $a['quantity'],
                'price' => $a['price']
            ];
        }

        $orderItems[] = [
            'id' => $item['id'],
            'name' => $item['item_name'],
            'qty' => $item['quantity'],
            'price' => $item['unit_price'],
            'addons' => $addonsArr
        ];
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
    <link rel="stylesheet" href="../Admin/adminFrontend/css/table_order_addOrder.css">
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
        <div class="filter-section" style="position: relative;">
            <a href="index.php?table-booking-acptd" class="close-filter-btn">&times;</a>

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
                <i class="fas fa-check-circle"></i> CheckOut
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
                        <i class="fas fa-check-circle"></i> Confirm Additional Order
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const itemAddons = <?= json_encode($addons); ?>;
        const advanceOrder = <?= json_encode($advanceOrder ?? null); ?>;
        const tableNames = <?= json_encode($tableNames ?? []); ?>;
        const existingOrderItems = <?= json_encode($orderItems ?? []); ?>;


        let cart = [];
        let pendingItem = null;
        let bootstrapAddonModal = new bootstrap.Modal(document.getElementById('addonModal'));
        let bootstrapAdvanceModal = new bootstrap.Modal(document.getElementById('advanceOrderModal'));

        // ---------------- FILTER ----------------
        function filterByCategory(cat, e) {
            document.querySelectorAll('#categoryFilters button').forEach(btn => btn.classList.remove('active'));
            if (e) e.currentTarget.classList.add('active');
            else if (cat === 'all') document.querySelector('#categoryFilters button').classList.add('active');

            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.category == cat) ? 'block' : 'none';
            });
        }

        // ---------------- ADD TO CART ----------------
        function addToCart(id) {
            const card = document.querySelector(`button[onclick="addToCart(${id})"]`).closest('.menu-card');
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
            const addons = itemAddons[id] || [];
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
                    </div>`;
            });
            content.innerHTML = html;
            bootstrapAddonModal.show();
        }

        function closeAddonModal() {
            bootstrapAddonModal.hide();
            pendingItem = null;
        }

        function addonPlus(id) {
            const el = document.getElementById('addonQty_' + id);
            el.innerText = parseInt(el.innerText) + 1;
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
            if (cartCount) cartCount.innerText = totalItems;

            if (cart.length === 0) {
                cartBody.innerHTML = `<div class="empty-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    <p>Your cart is empty</p>
                                    <small>Add items to get started</small>
                                </div>`;
                checkoutBtn.disabled = true;
                document.getElementById('subtotal').innerText = '₱0.00';
                document.getElementById('total').innerText = '₱0.00';
                if (cartCount) cartCount.style.display = 'none';
                return;
            }

            if (cartCount) cartCount.style.display = 'flex';
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
                </div>`;
            });
            cartBody.innerHTML = html;

            let subtotal = 0;
            cart.forEach(item => {
                let itemTotal = item.price * item.qty;
                item.addons.forEach(a => { itemTotal += a.price * a.qty * item.qty });
                subtotal += itemTotal;
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
            if (!advanceOrder) {
                alert('No advance order found!');
                return;
            }

            const sectionStyle = "margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #d4af37;";
            const headerStyle = "font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; display: flex; align-items: center; gap: 8px;";
            const flexRow = "display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.95rem;";
            const addonStyle = "font-size: 0.85rem; color: #666; margin-left: 20px; list-style: none; padding-left: 10px; border-left: 1px dashed #ccc;";

            let html = `
                <div style="${sectionStyle}">
                    <h6 style="${headerStyle}"><i class="fas fa-user"></i> Customer Information</h6>
                    <div style="${flexRow}"><span>Name:</span> <strong>${advanceOrder.firstname} ${advanceOrder.lastname}</strong></div>
                    <div style="${flexRow}"><span>Contact:</span> <strong>${advanceOrder.contact}</strong></div>
                    <div style="${flexRow}"><span>Booking:</span> <strong>${advanceOrder.date_time}</strong></div>
                </div>

                <div style="${sectionStyle}">
                    <h6 style="${headerStyle}"><i class="fas fa-table"></i> Selected Tables</h6>
                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                        ${tableNames.map(t => `<span style="background:#eee; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem;">Table #${t.number} - ${t.name}</span>`).join('')}
                    </div>
                </div>

                <div style="${sectionStyle}">
                    <h6 style="${headerStyle}"><i class="fas fa-utensils"></i> Order Items</h6>
            `;

            let subtotal = 0;

            if (existingOrderItems.length === 0 && cart.length === 0) {
                html += '<p style="text-align:center; color:#999; padding: 10px;">No items in cart.</p>';
            } else {
                const renderItem = (item, isNew = false) => {
                    let itemTotal = (parseFloat(item.price) || 0) * (item.qty || 1);
                    let itemHtml = `
                <div style="${flexRow} margin-top: 10px;">
                    <span>
                        <strong>${item.name}</strong> 
                        ${isNew ? '<span style="background:#28a745; color:white; font-size:10px; padding:2px 5px; border-radius:10px; vertical-align:middle;">NEW</span>' : ''}
                        <br><small style="color:#777;">${item.qty} × ₱${parseFloat(item.price).toFixed(2)}</small>
                    </span>
                    <span style="font-weight:600;">₱${itemTotal.toFixed(2)}</span>
                </div>`;

                    if (item.addons && item.addons.length > 0) {
                        item.addons.forEach(a => {
                            let aTotal = a.price * a.qty;
                            itemHtml += `
                        <div style="${addonStyle}">
                            <div style="${flexRow}">
                                <span>+ ${a.name} <small>(x${a.qty})</small></span>
                                <span>₱${aTotal.toFixed(2)}</span>
                            </div>
                        </div>`;
                            itemTotal += aTotal;
                        });
                    }
                    subtotal += itemTotal;
                    return itemHtml;
                };

                existingOrderItems.forEach(item => { html += renderItem(item, false); });
                cart.forEach(item => { html += renderItem(item, true); });
            }

            const downpayment = parseFloat(advanceOrder.downpayment || 0);
            const dpMethod = advanceOrder.dp_payment_method || 'N/A';
            const remainingBalance = subtotal - downpayment;

            html += `
                </div> <div style="margin-top:20px; padding: 15px; background: #fff; border: 2px solid #eee; border-radius: 8px;">
                    <div style="${flexRow} font-size: 1.1rem;">
                        <span>Subtotal:</span>
                        <span>₱${subtotal.toFixed(2)}</span>
                    </div>
                    <div style="${flexRow} color: #dc3545;">
                        <span>Downpayment (${dpMethod}):</span>
                        <span>- ₱${downpayment.toFixed(2)}</span>
                    </div>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 2px dashed #eee; ${flexRow} font-size: 1.25rem; color: #b8860b;">
                        <strong>Remaining Balance:</strong>
                        <strong>₱${remainingBalance.toFixed(2)}</strong>
                    </div>
                </div>
            `;

            document.getElementById('advanceOrderContent').innerHTML = html;
            bootstrapAdvanceModal.show();
        });


        // ---------------- CONFIRM ADVANCE ORDER ----------------
        document.getElementById('confirmAdvanceOrder').addEventListener('click', () => {
            if (!advanceOrder) return;

            if (cart.length === 0) {
                alert('No new items to add.');
                return;
            }

            const advanceOrderId = advanceOrder.id;

            const formData = new FormData();
            formData.append('order_id', advanceOrderId);

            cart.forEach(item => {
                formData.append('cartItems[]', JSON.stringify({
                    item_name: item.name,
                    unit_price: item.price,
                    quantity: item.qty,
                    addons: item.addons.map(a => ({
                        addon_name: a.name,
                        price: a.price,
                        quantity: a.qty
                    }))
                }));
            });

            fetch('../Admin/adminBackend/save_new_order_items.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert('Order confirmed successfully!');
                        window.location.href = 'index.php?table-booking-acptd';
                    } else {
                        alert(res.message || 'Error saving order');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error saving order.');
                });
        });
    </script>
</body>

</html>