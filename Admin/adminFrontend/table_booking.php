<?php
$orderSuccess = isset($_GET['order_success']) && $_GET['order_success'] == '1';
?>

<?php
$bookingSuccess = $_SESSION['booking_success'] ?? null;
unset($_SESSION['booking_success']);

$bookingError = $_SESSION['booking_error'] ?? null;
unset($_SESSION['booking_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Table Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/table_booking.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">
</head>

<style>
    /* Casa Estela Theme Colors */
    :root {
        --gold-primary: #b89535;
        --gold-hover: #9a7b2a;
        --dark-text: #2c2c2c;
        --soft-bg: #f8f9fa;
    }

    .custom-modal {
        border-radius: 15px !important;
        overflow: hidden;
    }

    .modal-header-gold {
        background-color: var(--gold-primary) !important;
        border-bottom: none;
        letter-spacing: 1px;
    }

    .custom-input {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .custom-input:focus {
        border-color: var(--gold-primary);
        box-shadow: 0 0 0 0.25rem rgba(184, 149, 53, 0.25);
    }

    .btn-gold {
        background-color: var(--gold-primary);
        color: white;
        border: none;
        border-radius: 8px;
        transition: background 0.3s ease;
    }

    .btn-gold:hover {
        background-color: var(--gold-hover);
        color: white;
        transform: translateY(-1px);
    }

    .form-label {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

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
                
            </div>
        </div>
    </nav>

    <?php
    include 'adminBackend/mydb.php';
    $sql = "SELECT * FROM table_types";
    $result = $conn->query($sql);
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="position-relative">

            <!-- Close / Back Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close"
                onclick="window.location.href='../Admin/index.php?room_booking'">
            </button>

            <!-- Existing filter section -->
            <div class="filter-section d-flex flex-wrap align-items-center gap-2 mb-3">
                <button class="filter-btn active" data-filter="all">All Tables</button>
                <button class="filter-btn" data-filter="available">Available Only</button>
                <button class="filter-btn" data-filter="unavailable">Unavailable</button>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <input type="datetime-local" id="globalBookingDateTime" class="form-control"
                        style="max-width: 250px;">

                    <button class="btn btn-info me-5" id="checkGlobalAvailabilityBtn" style="width: 200px;">
                        Check Availability
                    </button>
                </div>
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
            <div class="modal-content border-0 shadow-lg custom-modal">
                <div class="modal-header modal-header-gold text-white">
                    <h5 class="modal-title fw-bold">Booking Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="bookingTablesContainer">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">First Name</label>
                            <input type="text" id="firstName" class="form-control custom-input mb-3" placeholder="Juan">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Last Name</label>
                            <input type="text" id="lastName" class="form-control custom-input mb-3"
                                placeholder="Dela Cruz">
                        </div>
                    </div>

                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" id="email" class="form-control custom-input mb-3"
                        placeholder="example@mail.com">

                    <label class="form-label small fw-bold text-muted">Contact Number</label>
                    <input type="text" id="contactNumber" class="form-control custom-input mb-3"
                        placeholder="0917 XXX XXXX">

                    <label class="form-label small fw-bold text-muted">Booking Date & Time</label>
                    <input type="datetime-local" id="bookingDateTime" class="form-control custom-input mb-4 bg-light"
                        readonly>

                    <div id="availableTablesWrapper" class="p-3 rounded-3"
                        style="background-color: #fdfaf0; border: 1px dashed #d1b876;">
                        <p class="small text-center text-muted mb-0">Available tables will be listed here.</p>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button class="btn btn-gold w-100 fw-bold py-2" id="submitBookingInfo">CONFIRM BOOKING</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const tablesContainer = document.getElementById("tablesContainer");
        // Backup original tables HTML
        const originalTablesHTML = tablesContainer.innerHTML;

    </script>

    <script>
        let cart = [];
        let availabilityChecked = false;

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

            if (!availabilityChecked) {
                CasaEstelaAlert.show('warning', 'Action Required', 'Please check availability first before adding to cart.');
                return;
            }

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
                                    const inCart = cart.find(c => c.selectedTableId == item.id);
                                    let label = `Table #${item.table_number}`;
                                    let disabled = '';

                                    if (item.status === 'unavailable') {
                                        label += ' (Unavailable)';
                                        disabled = 'disabled';
                                    }
                                    else if (!item.is_available && !inCart) {
                                        if (item.next_available_datetime) {
                                            const dt = new Date(item.next_available_datetime);
                                            let hours = dt.getHours();
                                            const minutes = String(dt.getMinutes()).padStart(2, '0');
                                            const ampm = hours >= 12 ? 'PM' : 'AM';
                                            hours = hours % 12;
                                            hours = hours ? hours : 12; // convert 0 to 12
                                            label += ` (Available at ${hours}:${minutes} ${ampm})`;
                                        } else {
                                            label += ' (Unavailable)';
                                        }
                                        disabled = 'disabled';
                                    }
                                    return `<option value="${item.id}" ${disabled}>${label}</option>`;
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
            document.getElementById('cartToggle').addEventListener('click', function (e) {
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
                    const globalDT = document.getElementById("globalBookingDateTime").value;
                    if (globalDT) {
                        document.getElementById("bookingDateTime").value = globalDT;
                    }
                    loadAvailableTableNumbers(globalDT);
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
                // 1. Collect form values
                const first = document.getElementById("firstName").value.trim();
                const last = document.getElementById("lastName").value.trim();
                const email = document.getElementById("email").value.trim();
                const contact = document.getElementById("contactNumber").value.trim();
                const dt = document.getElementById("bookingDateTime").value.trim();

                // 2. Get selected tables
                const selectedTableNumbers = Array.from(document.querySelectorAll('.availableTableSelect'))
                    .map(sel => parseInt(sel.value))
                    .filter(v => !isNaN(v));

                const typeIds = cart.map(t => t.typeId);

                // 3. Validate form
                if (!first || !last || !email || !contact || !dt || selectedTableNumbers.length !== cart.length) {
                    CasaEstelaAlert.show('warning', 'Incomplete Form', 'Complete all fields and select available tables.');
                    return;
                }

                CasaEstelaModal.confirm(
                    'Casa Estela Confirmation',
                    'Are you sure you want to confirm this booking?',
                    function () {
                        const action = document.getElementById("bookingInfoModal").dataset.action;
                        const formData = new URLSearchParams();
                        formData.append("first", first);
                        formData.append("last", last);
                        formData.append("email", email);
                        formData.append("contact", contact);
                        formData.append("datetime", dt);
                        typeIds.forEach(id => formData.append("tableTypes[]", id));
                        selectedTableNumbers.forEach(num => formData.append("tables[]", num));

                        if (action === "checkout") {
                            fetch("../Admin/adminBackend/booking_save_order.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: formData
                            })
                                .then(res => res.json())
                                .then(res => {
                                    if (res.status === "success") {
                                        CasaEstelaModal.show(
                                            'success',
                                            'Casa Estela Confirmation',
                                            `Your booking was successful! Order ID: ${res.order_id}`,
                                            function () {
                                                window.location.href = "../Admin/index.php?table-booking";
                                            }
                                        );
                                    } else {
                                        CasaEstelaModal.show(
                                            'error',
                                            'Booking Failed',
                                            res.msg || 'Unable to complete booking.',
                                            function () {
                                                CasaEstelaModal.close(this);
                                            }
                                        );
                                    }
                                })
                                .catch(err => console.error("Checkout error:", err));
                        }

                        if (action === "advance") {
                            fetch("../Admin/adminBackend/booking_store_advance.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: formData
                            })
                                .then(res => res.json())
                                .then(res => {
                                    if (res.status === "success") {
                                        window.location.href = res.redirect || "/";
                                    } else {
                                        CasaEstelaAlert.show('error', 'Error', res.msg || 'Unable to store advance order.');
                                    }
                                })
                                .catch(err => console.error("Advance order error:", err));
                        }
                    },
                    function () {
                        CasaEstelaAlert.show('info', 'Cancelled', 'Your booking was not submitted.');
                    }
                );
            });

        }
        init();
        document.getElementById("checkAvailabilityBtn").addEventListener("click", function () {
            const dt = document.getElementById("bookingDateTime").value.trim();
            if (!dt) {
                alert("Please select a booking date & time first.");
                return;
            }
            const typeIds = cart.map(t => t.typeId);
            if (!typeIds.length) {
                CasaEstelaAlert.show('info', 'Notice', 'No table types selected in your cart.');
                return;
            }
            const formData = new URLSearchParams();
            formData.append("datetime", dt);
            typeIds.forEach(id => formData.append("type_ids[]", id));
            fetch("../Admin/adminBackend/table_check_availability.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    const wrapper = document.getElementById("availabilityResult");
                    if (data.available.length) {
                        wrapper.innerHTML = `<span class="text-success">Tables available: ${data.available.join(", ")}</span>`;
                    } else {
                        wrapper.innerHTML = `<span class="text-danger">No tables available at the selected time.</span>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    CasaEstelaAlert.show('error', 'Error', 'Error checking availability.');
                });
        });
    </script>
    <script>
        function loadAvailableCounts(datetime = null) {
            const cards = document.querySelectorAll("#tablesContainer .table-card");
            const typeIds = Array.from(cards).map(c => parseInt(c.dataset.typeId));
            if (!typeIds.length) return;
            const formData = new URLSearchParams();
            typeIds.forEach(id => formData.append("type_ids[]", id));
            if (datetime) formData.append("datetime", datetime);
            fetch("../Admin/adminBackend/table_check_availability.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    cards.forEach(card => {
                        const typeId = parseInt(card.dataset.typeId);
                        const countDiv = document.getElementById(`available-count-${typeId}`);
                        const count = data.counts?.[typeId] ?? data.available?.length ?? 0;
                        countDiv.innerHTML = `<strong>Available Tables:</strong> ${count}`;
                        const badge = card.querySelector(".table-status-badge");
                        const btn = card.querySelector(".btn-add-to-cart");
                        const originalStatus = card.dataset.originalStatus || card.dataset.status; // PHP initial status
                        card.dataset.originalStatus = originalStatus;
                        if (originalStatus !== 'active') {
                            card.dataset.status = 'inactive';
                            badge.textContent = 'UNAVAILABLE';
                            badge.classList.remove("status-available");
                            badge.classList.add("status-unavailable");
                            btn.disabled = true;
                            btn.classList.add('btn-disabled');
                        } else if (count > 0) {
                            card.dataset.status = 'active';
                            badge.textContent = 'AVAILABLE';
                            badge.classList.remove("status-unavailable");
                            badge.classList.add("status-available");
                            btn.disabled = false;
                            btn.classList.remove('btn-disabled');
                        } else {
                            card.dataset.status = 'inactive';
                            badge.textContent = 'UNAVAILABLE';
                            badge.classList.remove("status-available");
                            badge.classList.add("status-unavailable");
                            btn.disabled = true;
                            btn.classList.add('btn-disabled');
                        }
                    });
                    const activeFilter = document.querySelector(".filter-btn.active").dataset.filter;
                    filterTables(activeFilter);
                })
                .catch(err => console.error(err));
        }
        loadAvailableCounts();
        document.getElementById("checkGlobalAvailabilityBtn").addEventListener("click", function () {
            const btn = this;
            const dt = document.getElementById("globalBookingDateTime").value.trim();
            if (!dt) {
                alert("Please select a valid date & time.");
                return;
            }
            const originalBtnText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...`;
            availabilityChecked = true;
            tablesContainer.innerHTML = originalTablesHTML;
            const cards = tablesContainer.querySelectorAll(".table-card");
            const typeIds = Array.from(cards).map(c => parseInt(c.dataset.typeId));
            if (!typeIds.length) {
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
                return;
            }
            const formData = new URLSearchParams();
            formData.append("datetime", dt);
            typeIds.forEach(id => formData.append("type_ids[]", id));
            fetch("../Admin/adminBackend/table_check_availability.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;

                    if (data.cafe_conflict) {
                        tablesContainer.innerHTML = `
                            <div class="col-12 d-flex justify-content-center align-items-center animate__animated animate__fadeIn" style="min-height: 400px; width: 100%;">
                                <div class="card border-0 shadow-sm" style="max-width: 500px; border-radius: 20px; background: #ffffff; border: 1px solid #e0e0e0;">
                                    
                                    <div class="text-center pt-5 pb-4">
                                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                            style="width: 80px; height: 80px; background-color: #fdf7e7; border-radius: 50%; color: #b89535;">
                                            <i class="fas fa-calendar-check" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h3 class="fw-bold" style="color: #333; letter-spacing: -0.5px;">Schedule Adjustment</h3>
                                    </div>

                                    <div class="card-body px-5 pb-5 text-center">
                                        <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.6;">
                                            ${data.cafe_conflict}
                                        </p>
                                        
                                        <div class="p-3 mb-4" style="background: #f8f9fa; border-left: 4px solid #17a2b8; border-radius: 8px;">
                                            <small class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Recommended Slot</small>
                                            <span class="h5 fw-bold" style="color: #17a2b8;">${data.cafe_available_window}</span>
                                        </div>

                                        <button class="btn w-100 py-3 text-white shadow-sm" 
                                                style="background-color: #219ebc; border-radius: 10px; font-weight: 600; border: none; transition: all 0.3s ease;"
                                                onmouseover="this.style.backgroundColor='#1a7a91'"
                                                onmouseout="this.style.backgroundColor='#219ebc'"
                                                onclick="location.reload()">
                                            <i class="fas fa-redo-alt me-2 small"></i> Select another schedule
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        `;
                        return;
                    }
                    loadAvailableCounts(dt);
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                    alert("Error connecting to server. Please check your connection.");
                });
        });
        document.getElementById("globalBookingDateTime").addEventListener("change", function () {
            const selected = new Date(this.value);
            const now = new Date();
            if (selected < now) {
                CasaEstelaAlert.show('warning', 'Invalid Date', 'You cannot select a past date or time.');
                this.value = "";
                return;
            }
            availabilityChecked = false;
            resetCart();
            tablesContainer.innerHTML = originalTablesHTML;
            loadAvailableCounts(this.value);
        });
        function resetCart() {
            cart = [];
            updateCart();
            updateCartCount();
            document.getElementById('cartSidebar').classList.remove('open');
        }
    </script>
    <script>
        wrapper.querySelectorAll('.availableTableSelect').forEach(select => {
            select.addEventListener('change', function () {
                const tableTypeId = parseInt(this.dataset.tableType);
                const selectedId = parseInt(this.value);
                const cartItem = cart.find(c => c.typeId === tableTypeId);
                if (cartItem) cartItem.selectedTableId = selectedId;
            });
        });
    </script>

    <script>
        function setMinGlobalDateTime() {
            const input = document.getElementById("globalBookingDateTime");
            const now = new Date();

            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            input.min = minDateTime;
        }
        setMinGlobalDateTime();
    </script>

    <script>
        // ----- Casa Estela Inline Alerts -----
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

        // ----- Casa Estela Modal System -----
        const CasaEstelaModal = {
            show: function (type, title, message, onConfirm = null, showCancel = false) {
                const icons = {
                    success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };

                const overlay = document.createElement('div');
                overlay.className = 'cea-modal-overlay';
                overlay.innerHTML = `
                <div class="cea-modal-dialog">
                    <div class="cea-modal-body">
                        <div class="cea-modal-icon-wrapper cea-modal-icon-wrapper-${type}">
                            ${icons[type]}
                        </div>
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            ${showCancel ? '<button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.close(this)">Cancel</button>' : ''}
                            <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">${showCancel ? 'Confirm' : 'OK'}</button>
                        </div>
                    </div>
                </div>
            `;
                overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
                document.body.appendChild(overlay);

                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) CasaEstelaModal.close(overlay);
                });
            },

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
                if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') btn.ceConfirmCallback();
                this.close(btn);
            },

            handleCancel: function (btn) {
                if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') btn.ceCancelCallback();
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!empty($bookingSuccess)): ?>
                CasaEstelaAlert.show(
                    'success',
                    'Casa Estela Confirmation',
                    '<?= $bookingSuccess["message"] ?>',
                    7000
                );
            <?php endif; ?>

            <?php if (!empty($bookingError)): ?>
                CasaEstelaAlert.show(
                    'error',
                    'Booking Failed',
                    '<?= $bookingError ?>',
                    7000
                );
            <?php endif; ?>
        });
    </script>

    <script>
        <?php if ($orderSuccess): ?>
            document.addEventListener('DOMContentLoaded', () => {
                CasaEstelaAlert.show('success', 'Order Confirmed', 'Advance order saved successfully!');
            });
        <?php endif; ?>
    </script>



</body>

</html>