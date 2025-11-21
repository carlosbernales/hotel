// Initialize variables
let currentOrder = [];
let selectedPaymentOption = null;

// Function to add item to cart
function addToCart(itemId, itemName, itemPrice) {
    console.log('Adding to cart:', itemId, itemName, itemPrice);
    
    // Remove peso sign and commas from price
    let price = parseFloat(itemPrice.replace('₱', '').replace(/,/g, ''));
    
    // Check if item already exists in cart
    let existingItem = currentOrder.find(item => item.id === itemId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        currentOrder.push({
            id: itemId,
            name: itemName,
            price: price,
            quantity: 1
        });
    }
    
    updateOrderDisplay();
}

// Function to display current order
function displayCurrentOrder() {
    const emptyMessage = document.getElementById('currentOrderEmpty');
    const orderItemsDiv = document.getElementById('currentOrderItems');
    const totalAmountSpan = document.getElementById('totalAmount');
    
    // Calculate total
    let total = 0;
    currentOrder.forEach(item => total += item.price * item.quantity);
    
    // Update total amount
    if (totalAmountSpan) {
        totalAmountSpan.textContent = total.toFixed(2);
    }
    
    // Show/hide appropriate sections
    if (currentOrder.length === 0) {
        emptyMessage.style.display = 'block';
        orderItemsDiv.style.display = 'none';
        return;
    }
    
    // Show order items
    emptyMessage.style.display = 'none';
    orderItemsDiv.style.display = 'block';
    
    // Generate items HTML
    let itemsHtml = '';
    currentOrder.forEach(item => {
        const itemTotal = item.price * item.quantity;
        itemsHtml += `
            <div class="order-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">${item.name}</h6>
                        <small class="text-muted">₱${item.price.toFixed(2)} × ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="btn-group btn-group-sm me-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <span class="btn btn-outline-secondary disabled">${item.quantity}</span>
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                        </div>
                        <div class="ms-2">
                            <strong>₱${itemTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    orderItemsDiv.innerHTML = itemsHtml;
    
    // Update payment amount if option is selected
    updatePaymentAmount(total);
}

// Function to update item quantity
function updateQuantity(itemId, change) {
    const item = currentOrder.find(item => item.id === itemId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            currentOrder = currentOrder.filter(i => i.id !== itemId);
        }
        displayCurrentOrder();
    }
}

// Function to update payment amount
function updatePaymentAmount(total) {
    const amountToPaySpan = document.getElementById('amountToPay');
    const paymentAmountInfo = document.getElementById('paymentAmountInfo');
    
    if (!selectedPaymentOption || !amountToPaySpan || !paymentAmountInfo) {
        return;
    }
    
    const amountToPay = selectedPaymentOption === 'down' ? total * 0.5 : total;
    amountToPaySpan.textContent = amountToPay.toFixed(2);
    paymentAmountInfo.style.display = 'block';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Payment option buttons
    const fullPaymentBtn = document.getElementById('fullPaymentBtn');
    const downPaymentBtn = document.getElementById('downPaymentBtn');
    
    if (fullPaymentBtn) {
        fullPaymentBtn.addEventListener('click', function() {
            selectedPaymentOption = 'full';
            fullPaymentBtn.classList.add('active');
            downPaymentBtn.classList.remove('active');
            const total = parseFloat(document.getElementById('totalAmount').textContent) || 0;
            updatePaymentAmount(total);
        });
    }
    
    if (downPaymentBtn) {
        downPaymentBtn.addEventListener('click', function() {
            selectedPaymentOption = 'down';
            downPaymentBtn.classList.add('active');
            fullPaymentBtn.classList.remove('active');
            const total = parseFloat(document.getElementById('totalAmount').textContent) || 0;
            updatePaymentAmount(total);
        });
    }
    
    // Initialize display
    displayCurrentOrder();
});

function updateOrderDisplay() {
    console.log('Updating order display');
    console.log('Current order:', currentOrder);
    
    const currentOrderItems = document.getElementById('currentOrderItems');
    const newTotalAmountSpan = document.getElementById('newTotalAmount');
    
    if (!currentOrderItems || !newTotalAmountSpan) {
        console.error('Required elements not found');
        return;
    }
    
    if (currentOrder.length === 0) {
        currentOrderItems.innerHTML = '<p class="text-muted">Your order is empty</p>';
        currentOrderItems.classList.add('d-none');
        newTotalAmountSpan.textContent = '0.00';
        return;
    }
    
    // Show the order items
    currentOrderItems.classList.remove('d-none');
    
    // Calculate total
    let total = 0;
    let orderHTML = '<div class="order-items-list">';
    
    currentOrder.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        orderHTML += `
            <div class="current-item mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="item-name">${item.name}</span>
                        <br>
                        <small class="text-muted">₱${item.price.toFixed(2)} x ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="quantity-control mr-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.id}', -1)">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.id}', 1)">+</button>
                        </div>
                        <span class="item-total">₱${itemTotal.toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    orderHTML += '</div>';
    currentOrderItems.innerHTML = orderHTML;
    
    // Update total amount display
    newTotalAmountSpan.textContent = total.toFixed(2);
}

// Event listener for payment option dropdown
document.getElementById('paymentOptionDropdown').addEventListener('change', function() {
    const totalAmount = parseFloat(document.getElementById('newTotalAmount').textContent);
    const amountToPay = this.value === 'down' ? totalAmount * 0.5 : totalAmount;
    
    // Update hidden fields
    document.getElementById('hiddenPaymentOption').value = this.value;
    document.getElementById('hiddenTotalAmount').value = totalAmount.toFixed(2);
});

// Event listener for payment method dropdown
document.getElementById('paymentMethod').addEventListener('change', function() {
    document.getElementById('hiddenPaymentMethod').value = this.value;
});

// Place order button click handler
document.getElementById('placeOrderBtn').addEventListener('click', function() {
    const paymentOption = document.getElementById('paymentOptionDropdown').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    
    if (currentOrder.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Order',
            text: 'Please add items to your order before proceeding.'
        });
        return;
    }
    
    if (!paymentOption) {
        Swal.fire({
            icon: 'error',
            title: 'Payment Option Required',
            text: 'Please select a payment option (Full Payment or Down Payment).'
        });
        return;
    }
    
    if (!paymentMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Payment Method Required',
            text: 'Please select a payment method.'
        });
        return;
    }
    
    // If all validations pass, close the modal and update the reservation details
    $('#advanceOrderModal').modal('hide');
    $('#reservationModal').modal('show');
});