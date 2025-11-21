let cart = [];

// Cart Management Functions
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
    
    // Show success message and animate badge
    Swal.fire({
        icon: 'success',
        title: 'Added to List',
        text: 'Room has been added to your list!',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        updateCart();
        animateCartBadge();
    });
}

// New function to animate the cart badge
function animateCartBadge() {
    const cartCount = document.getElementById('cartCount');
    const cartCountMobile = document.getElementById('cartCount-mobile');
    const count = cart.length;

    // Function to animate a single badge
    function animateBadge(badge) {
        if (!badge) return;

        // Update count
        badge.textContent = count;

        // Show badge if there are items
        if (count > 0) {
            badge.style.display = 'flex';
            
            // Add animation classes
            badge.classList.add('badge-pop');
            badge.classList.add('badge-bounce');

            // Remove animation classes after animation completes
            setTimeout(() => {
                badge.classList.remove('badge-pop');
                badge.classList.remove('badge-bounce');
            }, 500);
        } else {
            badge.style.display = 'none';
        }
    }

    // Animate both badges
    animateBadge(cartCount);
    animateBadge(cartCountMobile);
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const totalPriceElement = document.getElementById('totalPrice');
    
    if (!cartItems || !totalPriceElement) {
        console.error('Required elements not found');
        return;
    }

    let totalPrice = 0;
    cartItems.innerHTML = cart.length === 0 ? 
        '<div class="text-center text-muted p-4">Your cart is empty</div>' :
        cart.map((item, index) => `
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
        `).join('');

    totalPrice = cart.reduce((sum, item) => sum + item.price, 0);
    totalPriceElement.textContent = totalPrice.toLocaleString();
    
    updateCartCount();
}

function updateCartCount() {
    const count = cart.length;
    const cartCount = document.getElementById('cartCount');
    const cartCountMobile = document.getElementById('cartCount-mobile');
    
    function updateBadge(badge) {
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
    
    updateBadge(cartCount);
    updateBadge(cartCountMobile);
}

// Update the updatePriceSummary function
function updatePriceSummary() {
    const guestType = document.getElementById('guestType1')?.value;
    const subtotal = calculateSubtotal();
    let discount = 0;
    
    // Calculate 20% discount if senior/PWD
    if (guestType === 'senior' || guestType === 'pwd') {
        discount = subtotal * 0.20;
    }
    
    const totalAfterDiscount = subtotal - discount;
    const paymentOption = document.getElementById('paymentOption')?.value;
    
    // Update the price summary display
    const priceSummaryHtml = `
        <div class="price-summary p-3 bg-light rounded">
            <h6 class="mb-3">Price Summary</h6>
            
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <span>₱${subtotal.toLocaleString()}</span>
            </div>
            
            ${discount > 0 ? `
            <div class="d-flex justify-content-between mb-2 text-success">
                <span>Discount (20%):</span>
                <span>-₱${discount.toLocaleString()}</span>
            </div>
            ` : ''}
            
            <div class="d-flex justify-content-between mb-2 fw-bold border-top pt-2">
                <span>Total Amount:</span>
                <span>₱${totalAfterDiscount.toLocaleString()}</span>
            </div>
            
            <div class="payment-option-section mt-3">
                <div class="form-group">
                    <label for="paymentOption" class="form-label">Payment Option:</label>
                    <select class="form-select" id="paymentOption" onchange="updatePriceSummary()">
                        <option value="full">Full Payment</option>
                        <option value="downpayment">Downpayment (50%)</option>
                    </select>
                </div>
            </div>
            
            ${paymentOption === 'downpayment' ? `
            <div class="downpayment-details mt-3 border-top pt-2">
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Downpayment Amount (50%):</span>
                    <span>₱${(totalAfterDiscount * 0.5).toLocaleString()}</span>
                </div>
                <div class="d-flex justify-content-between text-danger">
                    <span>Remaining Balance:</span>
                    <span>₱${(totalAfterDiscount * 0.5).toLocaleString()}</span>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i> 
                    The remaining balance will be paid upon check-in
                </small>
            </div>
            ` : ''}
        </div>
    `;
    
    // Update the price summary container
    const priceSummaryContainer = document.getElementById('priceSummaryContainer');
    if (priceSummaryContainer) {
        priceSummaryContainer.innerHTML = priceSummaryHtml;
        
        // Restore the selected payment option
        const newPaymentOption = priceSummaryContainer.querySelector('#paymentOption');
        if (newPaymentOption && paymentOption) {
            newPaymentOption.value = paymentOption;
        }
    }
}

// Helper function to calculate subtotal
function calculateSubtotal() {
    const nights = calculateNights(
        document.getElementById('checkIn')?.value,
        document.getElementById('checkOut')?.value
    ) || 1;
    
    return cart.reduce((total, item) => total + (item.price * nights), 0);
}

// Add event listeners for price updates
document.addEventListener('DOMContentLoaded', function() {
    // Listen for payment option changes
    const paymentOption = document.getElementById('paymentOption');
    if (paymentOption) {
        paymentOption.addEventListener('change', updatePriceSummary);
    }
    
    // Listen for guest type changes
    const guestType = document.getElementById('guestType1');
    if (guestType) {
        guestType.addEventListener('change', updatePriceSummary);
    }
    
    // Listen for date changes
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');
    if (checkIn) checkIn.addEventListener('change', updatePriceSummary);
    if (checkOut) checkOut.addEventListener('change', updatePriceSummary);
    
    // Initial price summary update
    updatePriceSummary();
});

// Rest of your existing functions... 