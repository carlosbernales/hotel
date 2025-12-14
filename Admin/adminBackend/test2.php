<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Table Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        /* --- Root and Typography Updates for Elegance --- */
        :root {
            --gold: #D4AF37;
            /* Original Gold */
            --dark-bg: #1c1c1c;
            /* Deeper, more luxurious dark background */
            --light-bg: #f3f3f3;
            /* Softer white/light gray */
            --text-color: #333;
            --light-text: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            /* Modern, clean font */
            overflow-x: hidden;
            background: var(--light-bg);
            /* Use soft light background */
            color: var(--text-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Playfair Display', serif;
            /* Elegant heading font */
            font-weight: 700;
        }

        /* --- Top Navbar Refinement --- */
        .top-navbar {
            background: var(--dark-bg);
            /* Darker background for contrast and luxury */
            padding: 15px 30px;
            /* More padding */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            /* Deeper shadow */
        }

        .top-navbar .navbar-brand {
            color: var(--gold);
            /* Gold brand text */
            font-family: 'Playfair Display', serif;
            font-weight: bold;
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        .top-navbar .nav-icons {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .top-navbar .nav-icons a {
            color: var(--light-text);
            /* Light icons for contrast */
            font-size: 1.3rem;
            position: relative;
            transition: color 0.3s, transform 0.2s;
            text-decoration: none;
        }

        .top-navbar .nav-icons a:hover {
            color: var(--gold);
            /* Gold hover effect */
            transform: scale(1.1);
        }

        .notification-badge,
        .cart-count {
            top: -5px;
            right: -5px;
            background: #e74c3c;
            /* A more elegant red for alerts */
            color: white;
            border: 2px solid var(--dark-bg);
            /* Border for better contrast */
        }

        .main-content {
            margin-top: 80px;
            /* Adjust for taller navbar */
            padding: 40px 30px;
            min-height: calc(100vh - 80px);
        }

        /* --- Filter Section Refinement (Gold Accent) --- */
        .filter-section {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            /* Softer shadow */
        }

        .filter-btn {
            padding: 10px 25px;
            border-radius: 5px;
            /* Square/sleek button */
            border: 1px solid var(--gold);
            background: white;
            color: var(--text-color);
            font-weight: 500;
            margin: 5px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-btn:hover {
            background: rgba(212, 175, 55, 0.1);
            /* Subtle gold background hover */
            color: var(--gold);
        }

        .filter-btn.active {
            background: var(--gold);
            color: white;
            border-color: var(--gold);
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.4);
        }

        #globalBookingDateTime {
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        /* --- Table Card Refinement --- */
        .table-card {
            background: white;
            border-radius: 15px;
            /* More rounded corners */
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            margin-bottom: 25px;
            height: 100%;
        }

        .table-card:hover {
            transform: translateY(-8px);
            /* Deeper lift on hover */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .table-card.unavailable {
            opacity: 0.5;
            /* More visible difference for unavailable */
            filter: grayscale(30%);
        }

        .table-image-container {
            height: 220px;
        }

        .table-image {
            transition: transform 0.5s;
        }

        .table-card:hover .table-image {
            transform: scale(1.05);
            /* Zoom image on hover */
        }

        .table-status-badge {
            top: 15px;
            right: 15px;
            padding: 8px 18px;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .status-available {
            background: #27ae60;
            /* Darker, richer green */
        }

        .status-unavailable {
            background: #c0392b;
            /* Darker, richer red */
        }

        .table-body {
            padding: 25px;
        }

        .table-name {
            font-size: 1.5rem;
            color: var(--dark-bg);
            margin-bottom: 15px;
        }

        .table-info {
            gap: 20px;
            color: #777;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .table-info i {
            color: var(--gold);
            font-size: 1.1rem;
        }

        .table-description {
            color: #888;
            font-size: 0.95rem;
            line-height: 1.5;
            min-height: 45px;
        }

        .available-tables-count {
            font-weight: 500;
            color: #27ae60;
        }

        .btn-add-to-cart {
            padding: 14px;
            background: var(--gold);
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-add-to-cart:hover:not(:disabled) {
            background: #b8941f;
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(212, 175, 55, 0.5);
        }

        /* --- Cart Sidebar Refinement --- */
        .cart-sidebar {
            right: -450px;
            /* Wider sidebar */
            width: 450px;
            box-shadow: -5px 0 20px rgba(0, 0, 0, 0.15);
        }

        .cart-header {
            padding: 25px 30px;
            background: var(--dark-bg);
            /* Dark header */
        }

        .cart-header h4 {
            font-size: 1.5rem;
            color: var(--gold);
            /* Gold title */
        }

        .close-cart:hover {
            color: #dc3545;
        }

        .cart-body {
            padding: 30px 25px;
        }

        .cart-item {
            background: var(--light-bg);
            padding: 18px;
            border-radius: 10px;
            border-left: 5px solid var(--gold);
            /* Gold accent stripe */
        }

        .cart-item-info h6 {
            color: var(--dark-bg);
            font-weight: 600;
        }

        .cart-item-info small {
            color: #777;
        }

        .cart-footer {
            padding: 25px;
            border-top: 1px solid #e0e0e0;
        }

        .cart-summary-row {
            margin-bottom: 12px;
            font-size: 1.05rem;
        }

        .cart-summary-row.total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold);
            border-top: 1px dashed #e0e0e0;
            padding-top: 15px;
            margin-top: 15px;
        }

        .btn-checkout {
            padding: 18px;
            background: #27ae60;
            font-size: 1.2rem;
            border-radius: 5px;
        }

        .btn-checkout:hover:not(:disabled) {
            background: #219653;
        }

        .btn-advance-orders {
            background: var(--gold);
            color: var(--dark-bg);
            font-weight: 600;
        }

        .btn-advance-orders:hover {
            background: #b8941f;
            color: white;
        }

        .btn-disabled {
            background-color: #9bb7d6 !important;
        }

        /* --- Modal Refinement --- */
        #bookingInfoModal .modal-header {
            background: var(--gold) !important;
            color: var(--dark-bg);
        }

        #bookingInfoModal .modal-title {
            color: var(--dark-bg);
            font-weight: 700;
            font-size: 1.5rem;
        }

        #bookingInfoModal .btn-close-white {
            filter: invert(1);
        }

        .modal-body .form-label {
            font-weight: 500;
            color: var(--dark-bg);
        }

        .modal-footer .btn-primary {
            background: #27ae60;
            border-color: #27ae60;
        }

        .modal-footer .btn-primary:hover {
            background: #219653;
            border-color: #219653;
        }

        /* Responsive fixes */
        @media (max-width: 992px) {
            .cart-sidebar {
                width: 350px;
                right: -350px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px 15px;
            }

            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .filter-section .ms-auto {
                margin-top: 15px;
                margin-left: 0 !important;
            }

            .filter-section .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }

            #globalBookingDateTime {
                max-width: 100% !important;
            }
        }
    </style>

</head>



<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="#" id="cartToggle">
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
    include 'adminBackend/mydb.php';
    $sql = "SELECT * FROM table_types";

    $result = $conn->query($sql);
    ?>

    <main class="main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-end">
                </div>
            </div>
        </div>

        <div class="filter-section d-flex flex-wrap align-items-center gap-2 mb-3">
            <button class="filter-btn active" data-filter="all">All Tables</button>
            <button class="filter-btn" data-filter="available">Available Only</button>
            <button class="filter-btn" data-filter="unavailable">Unavailable</button>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <input type="datetime-local" id="globalBookingDateTime" class="form-control" style="max-width: 250px;">
                <button class="btn btn-info" id="checkGlobalAvailabilityBtn">Check Availability</button>
            </div>
        </div>



        <div class="row" id="tablesContainer">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $statusClass = ($row['status'] == 'active') ? "status-available" : "status-unavailable";

                    $imageDir = "../Admin/adminBackend/table_types_images/";
                    $mainImage = (!empty($row['img1'])) ? $imageDir . $row['img1'] : "default.jpg";
                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="table-card" data-table-id="<?= $row['id'] ?>" data-type-id="<?= $row['id'] ?>"
                            data-status="<?= $row['status'] ?>" data-capacity="<?= $row['capacity'] ?>">


                            <div class="table-image-container">
                                <img src="<?= $mainImage ?>" alt="<?= $row['table_name'] ?>" class="table-image">

                                <span class="table-status-badge <?= $statusClass ?>">
                                    <?= strtoupper($row['status']) ?>
                                </span>
                            </div>

                            <div class="table-body">
                                <h5 class="table-name"><?= $row['table_name'] ?></h5>

                                <div class="table-info">
                                    <span><i class="fas fa-users"></i> <?= $row['capacity'] ?> people</span>
                                </div>

                                <p class="table-description">
                                    <?= $row['description'] ?>
                                </p>
                                <div class="available-tables-count mb-2" id="available-count-<?= $row['id'] ?>">
                                    Loading available tables...
                                </div>


                                <?php
                                $btnDisabled = ($row['status'] !== 'active') ? 'disabled' : '';
                                $btnClass = ($row['status'] !== 'active') ? 'btn-disabled' : '';
                                ?>
                                <button class="btn-add-to-cart <?= $btnClass ?>" onclick="addToCart(<?= $row['id'] ?>)"
                                    <?= $btnDisabled ?>>
                                    <i class="fas fa-plus-circle"></i> Add to Booking
                                </button>

                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<p>No tables found.</p>";
            }
            ?>
        </div>

    </main>

    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h4><i class="fas fa-shopping-cart"></i> Booking Cart</h4>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-body" id="cartBody">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your booking cart is empty</p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Total Tables:</span>
                    <span id="totalTables">0</span>
                </div>
                <div class="cart-summary-row">
                    <span>Total Capacity:</span>
                    <span id="totalCapacity">0 people</span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total Items:</span>
                    <span id="totalItems">0</span>
                </div>
            </div>
            <button class="btn-checkout" id="checkoutBtn" disabled>
                <i class="fas fa-check-circle"></i> Confirm Booking
            </button>

            <button class="btn-advance-orders" id="advanceOrdersBtn" disabled>
                <i class="fas fa-list-alt"></i> Advance Orders
            </button>

        </div>
    </div>


    <div class="modal fade" id="bookingInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Customer Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="bookingTablesContainer">
                    <label class="form-label">First Name</label>
                    <input type="text" id="firstName" class="form-control mb-2">

                    <label class="form-label">Last Name</label>
                    <input type="text" id="lastName" class="form-control mb-2">

                    <label class="form-label">Contact Number</label>
                    <input type="text" id="contactNumber" class="form-control mb-2">

                    <label class="form-label">Booking Date & Time</label>
                    <input type="datetime-local" id="bookingDateTime" class="form-control mb-3">

                    <div id="availableTablesWrapper"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="submitBookingInfo">Submit</button>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let cart = [];

        function init() {
            setupEventListeners();
            loadAvailableCounts(); // Initial load
        }

        function getTableData(tableId) {
            const card = document.querySelector(`[data-table-id="${tableId}"]`);
            if (!card) return null;

            return {
                id: parseInt(tableId),
                name: card.querySelector('.table-name').textContent,
                capacity: parseInt(card.dataset.capacity),
                status: card.dataset.status,
                typeId: parseInt(card.dataset.typeId)
            };
        }

        function addToCart(tableId) {
            const table = getTableData(tableId);
            if (!table || table.status === 'unavailable') return;

            if (cart.find(item => item.id === tableId)) {
                alert('This table is already in your booking cart!');
                return;
            }

            cart.push(table);
            updateCart();
            updateCartCount();

            document.getElementById('cartSidebar').classList.add('open');
        }

        function removeFromCart(tableId) {
            cart = cart.filter(item => item.id !== tableId);
            updateCart();
            updateCartCount();
        }

        function updateCart() {
            const cartBody = document.getElementById('cartBody');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const advanceBtn = document.getElementById('advanceOrdersBtn');

            if (cart.length === 0) {
                cartBody.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your booking cart is empty</p>
            </div>
        `;
                checkoutBtn.disabled = true;
                advanceBtn.disabled = true;
            } else {
                cartBody.innerHTML = cart.map(table => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <h6>${table.name}</h6>
                    <small><i class="fas fa-users"></i> ${table.capacity} people</small>
                </div>
                <button class="remove-item" onclick="removeFromCart(${table.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

                checkoutBtn.disabled = false;
                advanceBtn.disabled = false;
            }

            updateCartSummary();
        }

        function updateCartSummary() {
            const totalTables = cart.length;
            const totalCapacity = cart.reduce((sum, table) => sum + table.capacity, 0);

            document.getElementById('totalTables').textContent = totalTables;
            document.getElementById('totalCapacity').textContent = `${totalCapacity} people`;
            document.getElementById('totalItems').textContent = totalTables;
        }

        function updateCartCount() {
            if (document.getElementById('cartCount')) {
                document.getElementById('cartCount').textContent = cart.length;
            }
        }

        function filterTables(filter) {
            const allCards = document.querySelectorAll('.table-card');

            allCards.forEach(card => {
                const parent = card.closest('.col-lg-4');
                const status = card.dataset.status;

                let show = true;

                if (filter === 'available') show = status === 'active';
                if (filter === 'unavailable') show = status === 'inactive';

                parent.style.display = show ? 'block' : 'none';
            });
        }

        /* LOAD AVAILABLE REAL TABLE NUMBERS */
        function loadAvailableTableNumbers(datetime = null) {
            const wrapper = document.getElementById("availableTablesWrapper");
            wrapper.innerHTML = '';

            const typeIds = cart.map(t => t.typeId);

            const formData = new URLSearchParams();
            typeIds.forEach(id => formData.append("type_ids[]", id));
            if (datetime) formData.append("datetime", datetime);

            fetch("../Admin/adminBackend/booking_fetch_table_numbers.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    cart.forEach(table => {
                        const tableOptions = data.filter(d => d.table_type_fk_id == table.typeId);

                        const div = document.createElement('div');
                        div.classList.add('mb-3');

                        div.innerHTML = `
                <label class="form-label">Select Table for ${table.name}</label>
                <select class="form-control availableTableSelect" data-table-type="${table.typeId}">
                    ${tableOptions.length ?
                                tableOptions.map(item => {
                                    // Enable if either available OR already in cart
                                    const inCart = cart.find(c => c.selectedTableId == item.id);
                                    const disabled = (!item.is_available && !inCart) ? 'disabled' : '';
                                    const label = (!item.is_available && !inCart) ? '(Unavailable)' : '';
                                    return `<option value="${item.id}" ${disabled}>Table #${item.table_number} ${label}</option>`;
                                }).join('') :
                                `<option value="">No tables available</option>`
                            }
                </select>
            `;

                        wrapper.appendChild(div);
                    });
                })
                .catch(err => console.error(err));
        }



        function setupEventListeners() {
            const bookingModalEl = document.getElementById("bookingInfoModal");
            const bookingModal = new bootstrap.Modal(bookingModalEl);

            document.getElementById('cartToggle').addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('cartSidebar').classList.toggle('open');
            });

            document.getElementById('closeCart').addEventListener('click', () => {
                document.getElementById('cartSidebar').classList.remove('open');
            });

            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterTables(this.dataset.filter);
                });
            });

            document.getElementById("checkoutBtn").addEventListener("click", function () {
                if (cart.length > 0) {
                    // Set booking datetime from global input
                    const globalDT = document.getElementById("globalBookingDateTime").value;
                    if (globalDT) {
                        document.getElementById("bookingDateTime").value = globalDT;
                    }

                    loadAvailableTableNumbers(globalDT); // Pass datetime
                    bookingModal.show();
                    bookingModalEl.dataset.action = "checkout";
                }
            });

            document.getElementById("advanceOrdersBtn").addEventListener("click", function () {
                if (cart.length > 0) {
                    const globalDT = document.getElementById("globalBookingDateTime").value;
                    if (globalDT) {
                        document.getElementById("bookingDateTime").value = globalDT;
                    }

                    loadAvailableTableNumbers(globalDT);
                    bookingModal.show();
                    bookingModalEl.dataset.action = "advance";
                }
            });

            document.getElementById("submitBookingInfo").addEventListener("click", function () {

                const first = document.getElementById("firstName").value.trim();
                const last = document.getElementById("lastName").value.trim();
                const contact = document.getElementById("contactNumber").value.trim();
                const dt = document.getElementById("bookingDateTime").value.trim();

                const selectedTableNumbers = Array.from(document.querySelectorAll('.availableTableSelect'))
                    .map(sel => sel.value)
                    .filter(v => v !== "");

                const typeIds = cart.map(t => t.typeId);

                if (!first || !last || !contact || !dt || selectedTableNumbers.length !== cart.length) {
                    alert("Complete all fields and select available tables.");
                    return;
                }

                const action = bookingModalEl.dataset.action;

                let bodyData =
                    `first=${encodeURIComponent(first)}` +
                    `&last=${encodeURIComponent(last)}` +
                    `&contact=${encodeURIComponent(contact)}` +
                    `&datetime=${encodeURIComponent(dt)}` +
                    typeIds.map(id => `&tableTypes[]=${id}`).join('') +
                    selectedTableNumbers.map(num => `&tables[]=${num}`).join('');

                if (action === "checkout") {
                    fetch("../Admin/adminBackend/booking_save_order.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: bodyData
                    })
                        .then(async res => {
                            const text = await res.text();
                            console.log("RAW RESPONSE:", text); // ← SEE THE ERROR
                            return JSON.parse(text); // Try to parse manually
                        })
                        .then(res => {
                            console.log(res);
                            if (res.status === "success") {
                                alert("Booking confirmed successfully!");
                                cart = [];
                                updateCart();
                                updateCartCount();
                                bookingModal.hide();
                                loadAvailableCounts(dt); // Re-check availability after booking
                            } else {
                                alert("Error confirming booking: " + res.message);
                            }
                        })
                        .catch(err => {
                            console.error("Error during checkout fetch:", err);
                            alert("An error occurred during checkout.");
                        });

                }

                if (action === "advance") {
                    fetch("../Admin/adminBackend/booking_store_advance.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: bodyData
                    })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === "success") {
                                window.location.href = res.redirect;
                            } else {
                                alert("Error storing advance order.");
                            }
                        })
                        .catch(err => {
                            console.error("Error during advance order fetch:", err);
                            alert("An error occurred during advance order submission.");
                        });
                }

            });
        }

        // Global Availability Check Logic
        document.getElementById("checkGlobalAvailabilityBtn").addEventListener("click", function () {
            const dt = document.getElementById("globalBookingDateTime").value.trim();
            if (!dt) {
                alert("Please select a booking date & time first.");
                return;
            }
            // Trigger the count update across all table cards
            loadAvailableCounts(dt);
        });

        // Booking Time Change Listener in Modal
        document.getElementById("bookingDateTime").addEventListener("change", function () {
            const dt = this.value.trim();
            if (dt && cart.length > 0) {
                loadAvailableTableNumbers(dt);
            }
        });

        init();

        // This function now handles updating the "Available tables" count on all cards
        function loadAvailableCounts(datetime = null) {
            const cards = document.querySelectorAll("#tablesContainer .table-card");
            const typeIds = Array.from(cards).map(c => parseInt(c.dataset.typeId));
            if (!typeIds.length) return;

            const formData = new URLSearchParams();
            typeIds.forEach(id => formData.append("type_ids[]", id));
            if (datetime) formData.append("datetime", datetime);

            // Set initial loading state
            cards.forEach(card => {
                const countDiv = card.querySelector('.available-tables-count');
                countDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
                countDiv.style.color = '#ffc107'; // Yellow for checking
            });

            fetch("../Admin/adminBackend/table_check_availability.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    cards.forEach(card => {
                        const typeId = parseInt(card.dataset.typeId);
                        const countDiv = document.getElementById(`available-count-${typeId}`);
                        const addButton = card.querySelector('.btn-add-to-cart');

                        // Get the count for the specific table type
                        const count = data.counts?.[typeId] ?? 0;

                        countDiv.textContent = `${count} table(s) available`;

                        // Update color and button status based on count
                        if (count > 0) {
                            countDiv.style.color = '#27ae60'; // Green
                            addButton.disabled = false;
                            addButton.classList.remove('btn-disabled');
                        } else {
                            countDiv.style.color = '#c0392b'; // Red
                            addButton.disabled = true;
                            addButton.classList.add('btn-disabled');
                        }
                    });
                })
                .catch(err => {
                    console.error("Error loading counts:", err);
                    cards.forEach(card => {
                        const countDiv = card.querySelector('.available-tables-count');
                        countDiv.textContent = 'Error loading count';
                        countDiv.style.color = '#c0392b';
                    });
                });
        }
    </script>


</body>

</html>