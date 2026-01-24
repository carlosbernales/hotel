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
    <link rel="stylesheet" href="../Admin/adminFrontend/css/advance_orders.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">

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
                    <p><strong>Email:</strong> ${order.email}</p>
                    <p><strong>Booking Date & Time:</strong> ${order.datetime}</p>
                </div>

                <div class="order-info-section">
                    <h6><i class="fas fa-table"></i> Selected Tables</h6>
                    <ul>
                        ${order.tables.map((t, i) => `
                            <li>Table #${t} - ${tableNames[order.tableTypes[i]] ?? 'Unknown'}</li>
                        `).join('')}
                    </ul>
                </div>

                <div class="order-info-section">
                    <h6><i class="fas fa-utensils"></i> Order Items</h6>
                `;

                let subtotal = 0;

                if (cart.length === 0) {
                    html += '<p class="text-muted">No items in cart.</p>';
                } else {
                    html += '<ul>';
                    cart.forEach(item => {
                        let itemTotal = item.price * item.qty;
                        html += `<li><strong>${item.name}</strong> × ${item.qty} – ₱${itemTotal.toFixed(2)}`;

                        if (item.addons.length > 0) {
                            html += '<ul style="margin-top:5px;">';
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
                }

                const defaultDownpayment = subtotal / 2;

                html += `
                    <div style="margin-top:15px;padding-top:15px;border-top:2px solid #e0e0e0;">
                        <h5 style="color: var(--gold);">
                            <strong>Total: ₱<span id="orderTotal">${subtotal.toFixed(2)}</span></strong>
                        </h5>
                    </div>
                </div>

                <div class="order-info-section mt-3">
                    <h6><i class="fas fa-credit-card"></i> Payment Details</h6>

                    <div class="mb-3">
                        <label class="form-label"><strong>Payment Method</strong></label>
                        <select class="form-select" id="paymentMethod">
                            <option value="" disabled selected>Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Payment Type</strong></label>
                        <select class="form-select" id="paymentType" onchange="handlePaymentTypeChange()">
                            <option value="full">Full Payment</option>
                            <option value="half" selected>Half (50%)</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Downpayment (₱)</strong></label>
                        <input
                            type="number"
                            class="form-control"
                            id="downpayment"
                            min="0"
                            max="${subtotal}"
                            step="0.01"
                            value="${defaultDownpayment.toFixed(2)}"
                            readonly>
                        <small class="text-muted" id="dpNote">
                            Default is 50% of total
                        </small>
                    </div>
                </div>
                `;

                document.getElementById('advanceOrderContent').innerHTML = html;
                bootstrapAdvanceModal.show();

            <?php else: ?>
                alert('No advance order in session!');
            <?php endif; ?>
        });


        // ---------------- CONFIRM ADVANCE ORDER ----------------
        document.getElementById('confirmAdvanceOrder').addEventListener('click', () => {

            const paymentMethod = document.getElementById('paymentMethod')?.value;
            const downpayment = parseFloat(document.getElementById('downpayment')?.value || 0);
            const total = parseFloat(document.getElementById('orderTotal')?.textContent || 0);

            if (!paymentMethod) {
                CasaEstelaAlert.show('warning', 'Payment Method Required', 'Please select a payment method.');
                return;
            }

            if (downpayment <= 0 || downpayment > total) {
                CasaEstelaAlert.show('warning', 'Invalid Downpayment', 'Please enter a valid downpayment amount.');
                return;
            }

            // Show Casa Estela confirmation modal
            CasaEstelaModal.confirm(
                'Confirm Advance Order?',
                'Are you sure you want to confirm this advance order? This action cannot be undone.',
                function () {
                    // On Confirm: save the order
                    const order = <?= json_encode($advanceOrder); ?>;
                    const formData = new FormData();

                    formData.append('first', order.first);
                    formData.append('last', order.last);
                    formData.append('email', order.email);
                    formData.append('contact', order.contact);
                    formData.append('datetime', order.datetime);

                    formData.append('downpayment', downpayment.toFixed(2));
                    formData.append('dp_payment_method', paymentMethod);

                    let subtotal = 0;
                    cart.forEach(item => {
                        let itemTotal = item.price * item.qty;
                        item.addons.forEach(a => itemTotal += a.price * a.qty * item.qty);
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

                    formData.append('total', subtotal.toFixed(2));

                    fetch('../Admin/adminBackend/booking_save_order_advance.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') {
                                window.location.href = '../Admin/index.php?table-booking&order_success=1';
                            } else {
                                CasaEstelaAlert.show('error', 'Error', res.message || 'Error saving order.');
                            }
                        })
                        .catch(err => {
                            CasaEstelaAlert.show('error', 'Error', 'An error occurred while saving the order.');
                            console.error(err);
                        });
                },
                function () {
                    CasaEstelaAlert.show('info', 'Cancelled', 'Advance order was not confirmed.');
                }
            );
        });

        function handlePaymentTypeChange() {
            const paymentType = document.getElementById('paymentType').value;
            const downpaymentInput = document.getElementById('downpayment');
            const total = parseFloat(document.getElementById('orderTotal').textContent);
            const note = document.getElementById('dpNote');

            if (paymentType === 'full') {
                downpaymentInput.value = total.toFixed(2);
                downpaymentInput.readOnly = true;
                note.innerText = 'Full payment required';
            }
            else if (paymentType === 'half') {
                downpaymentInput.value = (total / 2).toFixed(2);
                downpaymentInput.readOnly = true;
                note.innerText = '50% downpayment required';
            }
            else if (paymentType === 'custom') {
                downpaymentInput.readOnly = false;
                note.innerText = 'Enter custom downpayment amount';
            }
        }

    </script>

    <script>
        // ---------------- CASA ESTELA ALERT SYSTEM ----------------
        const CasaEstelaAlert = {
            show: function (type, title, message, duration = 5000) {
                const icons = {
                    success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const alert = document.createElement('div');
                alert.className = `cea-inline-alert cea-inline-alert-${type}`;
                alert.innerHTML = `
                <div class="cea-inline-alert-icon">${icons[type]}</div>
                <div class="cea-inline-alert-content">
                    <div class="cea-inline-alert-title">${title}</div>
                    <div class="cea-inline-alert-message">${message}</div>
                </div>
                <button class="cea-inline-alert-close" onclick="this.parentElement.classList.add('cea-inline-alert-closing'); setTimeout(() => this.parentElement.remove(), 300)">×</button>
            `;

                document.body.appendChild(alert);

                if (duration > 0) {
                    setTimeout(() => {
                        alert.classList.add('cea-inline-alert-closing');
                        setTimeout(() => alert.remove(), 300);
                    }, duration);
                }
            }
        };

        // ---------------- CASA ESTELA MODAL SYSTEM ----------------
        const CasaEstelaModal = {
            confirm: function (title, message, onConfirm, onCancel = null) {
                const overlay = document.createElement('div');
                overlay.className = 'cea-modal-overlay';
                overlay.innerHTML = `
                <div class="cea-modal-dialog cea-modal-confirm">
                    <div class="cea-modal-body">
                        <div class="cea-modal-icon-wrapper">
                            <svg class="cea-icon-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            <button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.handleCancel(this)">Cancel</button>
                            <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">Confirm</button>
                        </div>
                    </div>
                </div>
            `;
                overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
                overlay.querySelector('.cea-modal-button-secondary').ceCancelCallback = onCancel;
                document.body.appendChild(overlay);
            },

            handleConfirm: function (btn) {
                if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') {
                    btn.ceConfirmCallback();
                }
                this.close(btn);
            },

            handleCancel: function (btn) {
                if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') {
                    btn.ceCancelCallback();
                }
                this.close(btn);
            },

            close: function (element) {
                const overlay = element.closest ? element.closest('.cea-modal-overlay') : element;
                if (overlay) {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 200);
                }
            }
        };
    </script>

</body>

</html>