let cart = [];

function addToCart(roomName, price, roomTypeId, capacity) {
    let existingRoom = cart.find(item => item.roomNumber === roomTypeId);
    
    if (existingRoom) {
        Swal.fire({
            icon: 'warning',
            title: 'Already in Cart',
            text: 'This room is already in your list!',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    cart.push({ 
        name: roomName, 
        price: price,
        originalPrice: price,
        quantity: 1,
        roomNumber: roomTypeId,
        capacity: capacity,
        extraGuestFee: 0
    });
    
    updateCart();
    updateCartCount();
    
    Swal.fire({
        icon: 'success',
        title: 'Added to List',
        text: 'Room has been added to your list!',
        timer: 1500,
        showConfirmButton: false
    });

    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    cartModal.show();
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const totalPriceElement = document.getElementById('totalPrice');
    
    if (!cartItems || !totalPriceElement) {
        console.error('Required elements not found');
        return;
    }

    let totalPrice = 0;
    const numberOfGuests = parseInt(document.getElementById('numberOfGuests')?.value) || 0;

    cartItems.innerHTML = '';

    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="text-center text-muted p-4">Your cart is empty</div>';
        totalPriceElement.textContent = "0.00";
        return;
    }

    const checkInDate = document.getElementById('checkInDate')?.value;
    const checkOutDate = document.getElementById('checkOutDate')?.value;

    if (checkInDate && checkOutDate) {
        updateCartWithDates(cartItems, totalPriceElement, checkInDate, checkOutDate, numberOfGuests);
    } else {
        updateCartBasic(cartItems, totalPriceElement);
    }
    
    if (typeof updatePaymentDisplay === 'function') {
        updatePaymentDisplay();
    }
}

function updateCartWithDates(cartItems, totalPriceElement, checkInDate, checkOutDate, numberOfGuests) {
    const checkIn = new Date(checkInDate);
    const checkOut = new Date(checkOutDate);
    const numberOfDays = Math.ceil((checkOut - checkIn) / (1000 * 3600 * 24));

    if (numberOfDays > 0) {
        const totalCapacity = cart.reduce((sum, item) => sum + item.capacity, 0);
        const extraGuests = Math.max(0, numberOfGuests - totalCapacity);
        const extraGuestFeePerNight = extraGuests * 1000;

        let totalPrice = 0;
        cart.forEach((item, index) => {
            const roomPrice = item.price * item.quantity * numberOfDays;
            const extraGuestCharge = extraGuestFeePerNight * numberOfDays;
            const totalItemPrice = roomPrice + extraGuestCharge;

            cartItems.innerHTML += generateCartItemHtml(item, numberOfDays, extraGuests, extraGuestFeePerNight, totalItemPrice, index);
            totalPrice += totalItemPrice;
        });

        totalPriceElement.textContent = totalPrice.toLocaleString();
    }
}

function updateCartBasic(cartItems, totalPriceElement) {
    let totalPrice = 0;
    cart.forEach((item, index) => {
        cartItems.innerHTML += generateBasicCartItemHtml(item, index);
        totalPrice += item.price;
    });
    totalPriceElement.textContent = totalPrice.toLocaleString();
}

function generateCartItemHtml(item, numberOfDays, extraGuests, extraGuestFeePerNight, totalItemPrice, index) {
    return `
        <div class="cart-item mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${item.name}</h6>
                    <p class="text-muted mb-0">₱${item.price.toLocaleString()} × ${numberOfDays} night(s)</p>
                    ${extraGuests > 0 ? `
                        <p class="text-danger mb-0">
                            Extra Guest Fee: ₱${extraGuestFeePerNight.toLocaleString()} × ${numberOfDays} night(s)
                        </p>
                    ` : ''}
                    <small class="text-muted">Max Capacity: ${item.capacity} guests</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">₱${totalItemPrice.toLocaleString()}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function generateBasicCartItemHtml(item, index) {
    return `
        <div class="cart-item mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">${item.name}</h6>
                    <p class="text-muted mb-0">₱${item.price.toLocaleString()} per night</p>
                    <small class="text-muted">Max Capacity: ${item.capacity} guests</small>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
} 