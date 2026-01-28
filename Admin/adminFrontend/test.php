<script>
    let cartItems = [];
    let availabilityChecked = <?= isset($result) && $result ? 'true' : 'false' ?>;

    function addToCart(name, price, room_number, floor, image, capacity, room_type_id, available) {
        if (!availabilityChecked) {
            alert("Please check room availability first.");
            return;
        }

        const existingItem = cartItems.find(item => item.room_type_id === room_type_id);
        if (existingItem) {
            if (existingItem.quantity < existingItem.available) {
                existingItem.quantity += 1;
                updateCart();
                openSidebar();
            } else {
                alert("You cannot book more than the available rooms.");
            }
            return;
        }

        const room = {
            id: Date.now(),
            name,
            price,
            room_number,
            floor,
            image,
            capacity,
            room_type_id,
            quantity: 1,
            available
        };

        cartItems.push(room);
        updateCart();
        openSidebar();
    }

    function removeFromCart(id) {
        cartItems = cartItems.filter(item => item.id !== id);
        updateCart();
    }

    function updateCart() {
        const cartContent = document.getElementById('cartContent');

        if (cartItems.length === 0) {
            cartContent.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your booking list is empty</p>
            </div>`;
            document.getElementById("cartBadge").innerText = 0;
            return;
        }

        const nights = getNumberOfNights();
        const total = cartItems.reduce((sum, item) =>
            sum + (item.price * item.quantity * nights), 0
        );


        document.getElementById("cartBadge").innerText = cartItems.length;

        cartContent.innerHTML = `
        ${cartItems.map(item => `
            <div class="cart-item">
                <img src="${item.image}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">₱${item.price.toLocaleString()}</div>
                    <div class="cart-item-quantity mt-2">
                        <button onclick="decreaseQuantity(${item.id})" class="btn-qty">-</button>
                        <input type="text" value="${item.quantity}" readonly class="qty-input">
                        <button onclick="increaseQuantity(${item.id})" class="btn-qty">+</button>
                    </div>
                    <div class="mt-2">
                        <button class="remove-item" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `).join('')}
        <div class="cart-total">
            <h5>Total Amount</h5>
            <div class="total-amount">₱${total.toLocaleString()}</div>
            <button class="btn-checkout" onclick="checkout()">
                <i class="fas fa-check-circle"></i> Proceed to Checkout
            </button>
        </div>`;
        generateExtraBedDropdowns();
    }

    function generateExtraBedDropdowns() {
        const container = document.getElementById("extraBedContainer");
        container.innerHTML = "";

        const totalRooms = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        if (totalRooms === 0) return;

        for (let i = 1; i <= totalRooms; i++) {

            const col = document.createElement("div");
            col.classList.add("col-md-6", "mb-2");

            col.innerHTML = `
            <select class="form-control custom-input extra-bed-select" data-index="${i}">
                <?= $bedOptions ?>
            </select>
        `;

            container.appendChild(col);
        }

        document.querySelectorAll(".extra-bed-select").forEach(select => {
            select.addEventListener("change", updateTotalAmount);
        });
    }

    function increaseQuantity(id) {
        const item = cartItems.find(i => i.id === id);
        if (item.quantity < item.available) {
            item.quantity++;
            updateCart();
        } else {
            alert(`Cannot exceed available rooms: ${item.available}`);
        }
    }

    function decreaseQuantity(id) {
        const item = cartItems.find(i => i.id === id);
        if (item.quantity > 1) {
            item.quantity--;
            updateCart();
        }
    }

    function openSidebar() {
        document.getElementById('sidebarCart').classList.add('active');
        document.getElementById('overlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebarCart').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function checkout() {
        if (cartItems.length === 0) {
            alert("Your booking list is empty.");
            return;
        }

        const totalCapacity = cartItems.reduce((sum, item) => sum + item.capacity, 0);
        document.getElementById("total_capacity").value = totalCapacity;

        document.getElementById("room_quantity").value =
            cartItems.reduce((sum, item) => sum + item.quantity, 0);

        updateTotalAmount();

        const checkInInput = document.querySelector("input[name='check_in']");
        const checkOutInput = document.querySelector("input[name='check_out']");
        document.getElementById("modal_check_in").value = checkInInput?.value || "";
        document.getElementById("modal_check_out").value = checkOutInput?.value || "";

        const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        checkoutModal.show();
    }

    function updateGuestInputs() {
        const adults = parseInt(document.querySelector("input[name='num_adults']").value) || 0;
        const children = parseInt(document.querySelector("input[name='num_children']").value) || 0;
        const totalGuests = adults + children;

        document.querySelector("input[name='number_of_guests']").value = totalGuests;

        const guestList = document.getElementById("guestList");
        guestList.innerHTML = "";
        let counter = 1;

        for (let i = 0; i < adults; i++) {
            guestList.innerHTML += createGuestInput(counter, "Adult");
            counter++;
        }

        for (let i = 0; i < children; i++) {
            guestList.innerHTML += createGuestInput(counter, "Child");
            counter++;
        }
    }

    function createGuestInput(number, type) {
        return `
        <div class="row g-2 guest-item mb-2 p-2 border rounded">
            <div class="col-12 mb-1"><strong>Guest #${number}</strong></div>

            <div class="col-md-3">
                <label class="form-label">First Name</label>
                <input type="text" name="guest_firstname_${number}" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="guest_lastname_${number}" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Guest Type</label>
                <select class="form-control" disabled>
                    <option value="Adult" ${type === "Adult" ? "selected" : ""}>Adult</option>
                    <option value="Child" ${type === "Child" ? "selected" : ""}>Child</option>
                </select>
                <!-- Hidden input to submit value -->
                <input type="hidden" name="guest_type_${number}" value="${type}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Discount</label>
                <select name="guest_discount_${number}" class="form-control discount-select" data-number="${number}">
                    <option value="" selected>Select Discount</option>
                    <option value="PWD" data-percent="20">PWD (20%)</option>
                    <option value="Senior" data-percent="20">Senior (20%)</option>
                </select>
            </div>
        </div>
        `;
    }

    function validateCapacity(event) {
        const adults = parseInt(document.querySelector("input[name='num_adults']").value) || 0;
        const children = parseInt(document.querySelector("input[name='num_children']").value) || 0;
        const totalGuests = adults + children;
        const totalCapacity = parseInt(document.getElementById("total_capacity").value);

        if (totalGuests > totalCapacity) {
            alert("Guest count exceeds the room’s total capacity!");
            event.target.value = "";
        }

        updateGuestInputs();
    }

    document.querySelector("input[name='num_adults']").addEventListener("input", validateCapacity);
    document.querySelector("input[name='num_children']").addEventListener("input", validateCapacity);

    function getNumberOfNights() {
        const checkInValue = document.getElementById("check_in")?.value;
        const checkOutValue = document.getElementById("check_out")?.value;

        if (!checkInValue || !checkOutValue) return 1;

        const checkIn = new Date(checkInValue);
        const checkOut = new Date(checkOutValue);

        if (checkOut > checkIn) {
            return (checkOut - checkIn) / (1000 * 60 * 60 * 24);
        }
        return 1;
    }

    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("discount-select") || e.target.classList.contains("extra-bed-select")) {
            updateTotalAmount();
        }
    });

    function updateTotalAmount() {
        const nights = getNumberOfNights();

        let roomsTotal = cartItems.reduce((sum, item) =>
            sum + (Number(item.price) * Number(item.quantity) * nights)
            , 0);

        let extraBedTotal = 0;
        document.querySelectorAll(".extra-bed-select").forEach(select => {
            const option = select.options[select.selectedIndex];
            const price = option ? Number(option.dataset.price) || 0 : 0;
            extraBedTotal += price * nights;
        });

        let subtotal = roomsTotal + extraBedTotal;

        let discountPercent = 0;
        document.querySelectorAll(".discount-select").forEach(select => {
            if (discountPercent === 0) {
                const option = select.options[select.selectedIndex];
                discountPercent = option ? Number(option.dataset.percent) || 0 : 0;
            }
        });

        let discountAmount = subtotal * (discountPercent / 100);

        const totalAmount = subtotal - discountAmount;

        const totalAmountInput = document.getElementById("total_amount");
        if (totalAmountInput) totalAmountInput.value = "₱" + totalAmount.toLocaleString();

        const discountPercentInput = document.getElementById("total_discount_percent");
        if (discountPercentInput) discountPercentInput.value = discountPercent + "%";

        const discountAmountInput = document.getElementById("total_discount_amount");
        if (discountAmountInput) discountAmountInput.value = "₱" + discountAmount.toLocaleString();
    }
    updateTotalAmount();
    document.getElementById("modal_check_in").addEventListener("change", updateTotalAmount);
    document.getElementById("modal_check_out").addEventListener("change", updateTotalAmount);
    function submitCheckout() {
        let valid = true;

        document.querySelectorAll("#checkoutForm input").forEach(input => {
            input.style.borderColor = "";
        });

        const mainGuestFields = ['first_name', 'last_name', 'contact', 'num_adults'];
        for (let fieldName of mainGuestFields) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (!field || !field.value.trim()) {
                field.style.borderColor = "red";
                valid = false;
            }
        }
        const guestInputs = document.querySelectorAll("#guestList input[type='text']");
        guestInputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = "red";
                valid = false;
            }
        });

        if (!valid) return;

        document.querySelectorAll('input[name="extra_beds[]"]').forEach(el => el.remove());

        Array.from(document.querySelectorAll(".extra-bed-select"))
            .map(s => s.value)
            .filter(v => v)
            .forEach(v => {
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "extra_beds[]";
                hiddenInput.value = v;
                document.getElementById("checkoutForm").appendChild(hiddenInput);
            });

        document.getElementById("cart_items").value = JSON.stringify(cartItems);
        document.getElementById("checkoutForm").submit();
    }
    document.addEventListener("DOMContentLoaded", function () {
        const checkIn = document.getElementById("check_in");
        const checkOut = document.getElementById("check_out");

        const today = new Date().toISOString().split("T")[0];
        checkIn.setAttribute("min", today);
        checkOut.setAttribute("min", today);

        checkIn.addEventListener("change", function () {
            if (checkIn.value) {
                checkOut.min = checkIn.value;
                if (checkOut.value && checkOut.value < checkIn.value) {
                    checkOut.value = checkIn.value;
                }
            }
        });

        checkOut.addEventListener("change", function () {
            if (checkOut.value && checkIn.value && checkOut.value < checkIn.value) {
                alert("Check-out date cannot be before check-in date!");
                checkOut.value = checkIn.value;
            }
        });
    });

    document.getElementById('check_in').addEventListener('change', resetAvailability);
    document.getElementById('check_out').addEventListener('change', resetAvailability);

    function resetAvailability() {
        availabilityChecked = false;
        cartItems = [];
        updateCart();
    }



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