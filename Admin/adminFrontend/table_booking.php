<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Table Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/table_booking.css">
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
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Booking Information</h5>
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
                    <input type="datetime-local" id="bookingDateTime" class="form-control mb-3" readonly>

                    <!-- Available tables for each type will be inserted here dynamically -->
                    <div id="availableTablesWrapper"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="submitBookingInfo">Confirm Booking</button>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                alert("Please check availability first before adding to cart.");
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
                const first = document.getElementById("firstName").value.trim();
                const last = document.getElementById("lastName").value.trim();
                const contact = document.getElementById("contactNumber").value.trim();
                const dt = document.getElementById("bookingDateTime").value.trim();

                // Get selected table numbers from the modal selects
                const selectedTableNumbers = Array.from(document.querySelectorAll('.availableTableSelect'))
                    .map(sel => parseInt(sel.value))
                    .filter(v => !isNaN(v));

                const typeIds = cart.map(t => t.typeId);

                // Validate required fields
                if (!first || !last || !contact || !dt || selectedTableNumbers.length !== cart.length) {
                    alert("Complete all fields and select available tables.");
                    return;
                }

                const action = document.getElementById("bookingInfoModal").dataset.action; // "checkout" or "advance"

                // Build URL-encoded POST data
                const formData = new URLSearchParams();
                formData.append("first", first);
                formData.append("last", last);
                formData.append("contact", contact);
                formData.append("datetime", dt);
                typeIds.forEach(id => formData.append("tableTypes[]", id));
                selectedTableNumbers.forEach(num => formData.append("tables[]", num));

                // Handle checkout booking
                if (action === "checkout") {
                    fetch("../Admin/adminBackend/booking_save_order.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === "success") {
                                alert(`Booking successful! Order ID: ${res.order_id}`);
                                location.reload(); // or close modal and reset cart
                            } else {
                                alert("Booking failed: " + res.msg);
                            }
                        })
                        .catch(err => console.error("Checkout error:", err));
                }

                // Handle advance orders
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
                                alert("Error storing advance order: " + res.msg);
                            }
                        })
                        .catch(err => console.error("Advance order error:", err));
                }
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
                alert("No table types selected in your cart.");
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
                    alert("Error checking availability.");
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
            const dt = document.getElementById("globalBookingDateTime").value.trim();
            if (!dt) {
                alert("Select date & time");
                return;
            }

            availabilityChecked = true;
            loadAvailableCounts(dt);
        });
        document.getElementById("globalBookingDateTime").addEventListener("change", function () {
            const selected = new Date(this.value);
            const now = new Date();

            if (selected < now) {
                alert("You cannot select a past date or time.");
                this.value = "";
                return;
            }

            availabilityChecked = false;
            resetCart();
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
                // Save selected table in cart
                const cartItem = cart.find(c => c.typeId === tableTypeId);
                if (cartItem) cartItem.selectedTableId = selectedId;
            });
        });
    </script>

    <script>
        function setMinGlobalDateTime() {
            const input = document.getElementById("globalBookingDateTime");

            const now = new Date();

            // Convert to local datetime-local format (YYYY-MM-DDTHH:MM)
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

</body>

</html>