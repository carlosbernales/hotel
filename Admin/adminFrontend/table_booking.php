<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Table Booking</title>
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

        /* Table Booking Specific Styles */

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

        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }

        .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .table-card.unavailable {
            opacity: 0.7;
        }

        .table-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .table-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .table-status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-available {
            background: #28a745;
            color: white;
        }

        .status-unavailable {
            background: #dc3545;
            color: white;
        }

        .table-body {
            padding: 20px;
        }

        .table-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
        }

        .table-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            color: #666;
        }

        .table-info i {
            color: var(--gold);
        }

        .table-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            min-height: 40px;
        }

        .unavailable-reason {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #856404;
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

        .btn-add-to-cart:hover:not(:disabled) {
            background: #b8941f;
            transform: translateY(-2px);
        }

        .btn-add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Cart Sidebar */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item-info h6 {
            margin: 0 0 5px 0;
            color: #2c2c2c;
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
        }

        .remove-item:hover {
            background: #c82333;
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
            font-size: 1.2rem;
            font-weight: 700;
            padding-top: 10px;
            border-top: 2px solid #f0f0f0;
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

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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

            .table-card {
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

    <style>
        .btn-advance-orders {
            width: 100%;
            margin-top: 10px;
            background: #0d6efd;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-advance-orders:hover {
            background: #0b5ed7;
        }

        .btn-advance-orders:disabled {
            background: #9bb7d6;
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>


</head>



<body>
    <!-- Top Navbar -->
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
    $sql = "SELECT * 
        FROM table_types";

    $result = $conn->query($sql);
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-end">
                    <!-- Removed Available / Occupied badges -->
                </div>
            </div>
        </div>

        <div class="filter-section">
            <button class="filter-btn active" data-filter="all">All Tables</button>
            <button class="filter-btn" data-filter="available">Available Only</button>
            <button class="filter-btn" data-filter="unavailable">Unavailable</button>
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


                            <!-- IMAGE -->
                            <div class="table-image-container">
                                <img src="<?= $mainImage ?>" alt="<?= $row['table_name'] ?>" class="table-image">

                                <span class="table-status-badge <?= $statusClass ?>">
                                    <?= strtoupper($row['status']) ?>
                                </span>
                            </div>

                            <!-- BODY -->
                            <div class="table-body">
                                <h5 class="table-name"><?= $row['table_name'] ?></h5>

                                <div class="table-info">
                                    <span><i class="fas fa-users"></i> <?= $row['capacity'] ?> people</span>
                                </div>

                                <p class="table-description">
                                    <?= $row['description'] ?>
                                </p>

                                <button class="btn-add-to-cart" onclick="addToCart(<?= $row['id'] ?>)">
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

    <!-- Cart Sidebar -->
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


    <!-- Booking Info Modal -->
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

                    <!-- Available tables for each type will be inserted here dynamically -->
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
        function loadAvailableTableNumbers() {
            const wrapper = document.getElementById("availableTablesWrapper");
            wrapper.innerHTML = '';

            const typeIds = cart.map(t => t.typeId);

            fetch("../Admin/adminBackend/booking_fetch_table_numbers.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: typeIds.map(id => `type_ids[]=${id}`).join("&")
            })
                .then(res => res.json())
                .then(data => {
                    cart.forEach(table => {
                        const available = data.filter(d => d.table_type_fk_id == table.typeId);

                        const div = document.createElement('div');
                        div.classList.add('mb-3');

                        div.innerHTML = `
                <label class="form-label">Available Table for ${table.name}</label>
                <select class="form-control availableTableSelect" data-table-type="${table.typeId}">
                    ${available.length ?
                                available.map(item => `<option value="${item.id}">Table #${item.table_number}</option>`).join('')
                                :
                                `<option value="">No available table numbers</option>`
                            }
                </select>
            `;

                        wrapper.appendChild(div);
                    });
                });
        }

        function setupEventListeners() {
            const bookingModalEl = document.getElementById("bookingInfoModal");
            const bookingModal = new bootstrap.Modal(bookingModalEl);

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
                    loadAvailableTableNumbers();
                    bookingModal.show();
                    bookingModalEl.dataset.action = "checkout";
                }
            });

            document.getElementById("advanceOrdersBtn").addEventListener("click", function () {
                if (cart.length > 0) {
                    loadAvailableTableNumbers();
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
                        });
                }

            });
        }

        init();

    </script>




</body>

</html>