// Cart Management
let cart = [];

// Helper Functions
function showSuccessMessage(title, text) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        timer: 1500,
        showConfirmButton: false
    });
}

function showErrorMessage(title, text) {
    Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonColor: '#ffc107'
    });
}

function showCartModal() {
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    cartModal.show();
}

// Cart Functions
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
    
    Swal.fire({
        icon: 'success',
        title: 'Added to List',
        text: 'Room has been added to your list!',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        updateCart();
        updateCartCount();
    });
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const totalPriceElement = document.getElementById('totalPrice');
    
    if (!cartItems || !totalPriceElement) return;

    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="text-center text-muted p-4">Your cart is empty</div>';
        totalPriceElement.textContent = "0.00";
        return;
    }

    let totalPrice = 0;
    cartItems.innerHTML = '';

    cart.forEach((item, index) => {
        totalPrice += item.price;
        cartItems.innerHTML += `
            <div class="cart-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">${item.name}</h6>
                        <p class="text-muted mb-0">₱${item.price.toLocaleString()} per night</p>
                        <small class="text-muted">Max Capacity: ${item.capacity} guests</small>
                    </div>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    totalPriceElement.textContent = totalPrice.toLocaleString();
}

function updateCartCount() {
    const count = cart.length;
    const cartCount = document.getElementById('cartCount');
    const cartCountMobile = document.getElementById('cartCount-mobile');
    
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'block' : 'none';
    }
    
    if (cartCountMobile) {
        cartCountMobile.textContent = count;
        cartCountMobile.style.display = count > 0 ? 'block' : 'none';
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
    showSuccessMessage('Removed', 'Room has been removed from your list');
}

// Generate HTML Functions
function generateRoomsHtml(totalRoomCapacity) {
    let html = '';
    let subtotal = 0;

    cart.forEach(room => {
        const pricePerNight = room.price;
        html += `
            <div class="selected-room mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${room.name}</strong>
                        <br>
                        Base Price: ₱${pricePerNight.toLocaleString()} per night
                        <br>
                        <small class="text-muted">Max Capacity: ${room.capacity} guests</small>
                    </div>
                </div>
            </div>
        `;
        subtotal += pricePerNight;
    });

    html += generateGuestInputHtml(totalRoomCapacity);
    html += generatePriceComputationHtml(subtotal);

    return html;
}

function generateGuestInputHtml(totalRoomCapacity) {
    return `
        <div class="mt-4">
            <h6>Guest Information</h6>
            <div class="mb-3">
                <label for="numberOfGuests" class="form-label">Number of Guests</label>
                <input type="number" 
                       class="form-control" 
                       id="numberOfGuests" 
                       min="1" 
                       required>
                <small class="text-muted" id="capacityInfo"></small>
                <div id="extraGuestWarning" class="mt-2" style="display: none;"></div>
            </div>
            
            <div id="guestNamesContainer">
                <!-- Guest name fields will be added here -->
            </div>
        </div>
    `;
}

function generateGuestNameFields(numberOfGuests) {
    return `
        <h6 class="mt-3 mb-2">Guest Names</h6>
        <small class="text-muted mb-2 d-block">Please enter the names of all guests</small>
        ${Array.from({ length: numberOfGuests }, (_, i) => `
            <div class="mb-2">
                <label class="form-label">Guest ${i + 1} Name</label>
                <input type="text" class="form-control guest-name" 
                       id="guestName${i}" 
                       name="guestName${i}" 
                       placeholder="Enter guest name"
                       required>
            </div>
        `).join('')}
    `;
}

function generatePriceComputationHtml(subtotal) {
    return `
        <div class="price-computation mt-3 p-3 bg-light">
            <div class="computation-item d-flex justify-content-between mb-2">
                <span>Subtotal per night:</span>
                <span>₱${subtotal.toLocaleString()}</span>
            </div>
            <div id="nightsCalculation" class="computation-item d-flex justify-content-between mb-2">
                <span>Number of nights:</span>
                <span>0</span>
            </div>
            <div id="extraGuestCalculation" class="computation-item d-flex justify-content-between mb-2" style="display: none;">
                <span>Extra Guest Charges:</span>
                <span>₱0.00</span>
            </div>
            <div class="border-top pt-2 mt-2">
                <div class="computation-item d-flex justify-content-between fw-bold">
                    <span>Total Amount:</span>
                    <span id="computedTotal">₱0.00</span>
                </div>
            </div>
            <div id="downpaymentSection" class="mt-2 d-none">
                <div class="computation-item d-flex justify-content-between text-success">
                    <span>Downpayment Amount (50%):</span>
                    <span id="downpaymentAmount">₱0.00</span>
                </div>
                <div class="computation-item d-flex justify-content-between text-danger">
                    <span>Remaining Balance:</span>
                    <span id="remainingBalance">₱0.00</span>
                </div>
            </div>
        </div>
    `;
}

// Calculation Functions
function calculateAndDisplayTotals(numberOfGuests, totalRoomCapacity, nights, extraGuests) {
    const subtotalPerNight = cart.reduce((sum, room) => sum + room.price, 0);
    const extraGuestFees = extraGuests * 1000 * nights;
    const totalAmount = (subtotalPerNight * nights) + extraGuestFees;

    updatePriceDisplay(subtotalPerNight, nights, extraGuests, extraGuestFees, totalAmount);
}

function updatePriceDisplay(subtotalPerNight, nights, extraGuests, extraGuestFees, totalAmount) {
    document.getElementById('nightsCalculation').innerHTML = `
        <span>Number of nights:</span>
        <span>${nights}</span>
    `;

    const extraGuestCalculation = document.getElementById('extraGuestCalculation');
    if (extraGuests > 0) {
        extraGuestCalculation.innerHTML = `
            <div class="d-flex justify-content-between text-danger">
                <span>Extra Guest Fees (${extraGuests} guests × ₱1,000 × ${nights} nights):</span>
                <span>₱${extraGuestFees.toLocaleString()}</span>
            </div>
        `;
        extraGuestCalculation.style.display = 'block';
    } else {
        extraGuestCalculation.style.display = 'none';
    }

    document.getElementById('computedTotal').textContent = `₱${totalAmount.toLocaleString()}`;
    updateDownpaymentDisplay(totalAmount);
}

function updateDownpaymentDisplay(totalAmount) {
    const paymentOption = document.getElementById('paymentOption').value;
    const downpaymentSection = document.getElementById('downpaymentSection');
    
    if (paymentOption === 'downpayment') {
        const downpayment = totalAmount * 0.5;
        document.getElementById('downpaymentAmount').textContent = `₱${downpayment.toLocaleString()}`;
        document.getElementById('remainingBalance').textContent = `₱${downpayment.toLocaleString()}`;
        downpaymentSection.classList.remove('d-none');
    } else {
        downpaymentSection.classList.add('d-none');
    }
}

// Booking Process Functions
function processBooking() {
    if (cart.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Cart',
            text: 'Please add rooms before proceeding.',
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    const cartModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    cartModal.hide();

    const bookingFormModal = new bootstrap.Modal(document.getElementById('bookingFormModal'));
    bookingFormModal.show();

    // Initialize with 1 guest
    const numberOfGuestsInput = document.getElementById('numberOfGuests');
    if (numberOfGuestsInput) {
        numberOfGuestsInput.value = 1;
        updateGuestNameFields();
    }

    initializeDateHandlers();
}

function updateBookingForm(totalRoomCapacity) {
    const selectedRoomsContainer = document.getElementById('selectedRooms');
    if (selectedRoomsContainer) {
        let roomsHtml = generateSelectedRoomsHtml(totalRoomCapacity);
        selectedRoomsContainer.innerHTML = roomsHtml;
    }
}

function calculateNights(checkInDate, checkOutDate) {
    const start = new Date(checkInDate);
    const end = new Date(checkOutDate);
    const timeDiff = end - start;
    return Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
}

function updateGuestNameFields() {
    const numberOfGuests = parseInt(document.getElementById('numberOfGuests').value) || 0;
    const container = document.getElementById('guestNamesContainer');
    const totalRoomCapacity = cart.reduce((sum, room) => sum + room.capacity, 0);
    
    // Update capacity info
    document.getElementById('capacityInfo').textContent = `Total room capacity: ${totalRoomCapacity} guests`;
    
    // Clear previous fields
    container.innerHTML = '';
    
    // Calculate extra guests
    const extraGuests = Math.max(0, numberOfGuests - totalRoomCapacity);
    const extraGuestWarning = document.getElementById('extraGuestWarning');
    
    // Show/hide extra guest warning and update price
    if (extraGuests > 0) {
        const extraCharge = extraGuests * 1000;
        extraGuestWarning.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Additional charge of ₱1,000 per extra guest (${extraGuests} extra guests = ₱${extraCharge.toLocaleString()})
            </div>
        `;
        extraGuestWarning.style.display = 'block';
    } else {
        extraGuestWarning.style.display = 'none';
    }

    // Generate guest name fields
    if (numberOfGuests > 0) {
        for (let i = 0; i < numberOfGuests; i++) {
            const guestDiv = document.createElement('div');
            guestDiv.className = 'mb-3';
            guestDiv.innerHTML = `
                <div class="form-group">
                    <label class="form-label">
                        Guest ${i + 1} Name
                        ${i >= totalRoomCapacity ? 
                            '<span class="badge bg-warning text-dark ms-2">Extra Guest</span>' : 
                            ''}
                    </label>
                    <input type="text" 
                           class="form-control guest-name-input" 
                           name="guest_names[]" 
                           placeholder="Enter guest name"
                           required>
                </div>
            `;
            container.appendChild(guestDiv);
        }
    }

    updateTotalPrice(numberOfGuests, totalRoomCapacity);
}

function updateTotalPrice(numberOfGuests, totalRoomCapacity) {
    const extraGuests = Math.max(0, numberOfGuests - totalRoomCapacity);
    const extraGuestFees = extraGuests * 1000; // Flat rate of ₱1,000 per extra guest
    const subtotalPerNight = cart.reduce((sum, room) => sum + room.price, 0);
    
    // Get check-in and check-out dates
    const checkIn = document.getElementById('checkIn')?.value;
    const checkOut = document.getElementById('checkOut')?.value;
    
    if (checkIn && checkOut) {
        const nights = calculateNights(checkIn, checkOut);
        const roomTotal = subtotalPerNight * nights;
        const totalAmount = roomTotal + extraGuestFees; // Add flat extra guest fees

        // Update display
        document.getElementById('subtotalAmount').textContent = subtotalPerNight.toLocaleString();
        document.getElementById('nightsCalculation').innerHTML = `
            <span>Number of nights:</span>
            <span>${nights}</span>
        `;

        const extraGuestCalculation = document.getElementById('extraGuestCalculation');
        if (extraGuests > 0) {
            extraGuestCalculation.innerHTML = `
                <span>Extra Guest Charges (${extraGuests} × ₱1,000):</span>
                <span>₱${extraGuestFees.toLocaleString()}</span>
            `;
            extraGuestCalculation.style.display = 'flex';
        } else {
            extraGuestCalculation.style.display = 'none';
        }

        document.getElementById('computedTotal').textContent = `₱${totalAmount.toLocaleString()}`;
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const numberOfGuestsInput = document.getElementById('numberOfGuests');
    if (numberOfGuestsInput) {
        numberOfGuestsInput.addEventListener('input', updateGuestNameFields);
    }

    // Add listeners for date changes
    const checkInInput = document.getElementById('checkIn');
    const checkOutInput = document.getElementById('checkOut');
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', () => {
            const numberOfGuests = parseInt(document.getElementById('numberOfGuests').value) || 0;
            const totalRoomCapacity = cart.reduce((sum, room) => sum + room.capacity, 0);
            updateTotalPrice(numberOfGuests, totalRoomCapacity);
        });
        checkOutInput.addEventListener('change', () => {
            const numberOfGuests = parseInt(document.getElementById('numberOfGuests').value) || 0;
            const totalRoomCapacity = cart.reduce((sum, room) => sum + room.capacity, 0);
            updateTotalPrice(numberOfGuests, totalRoomCapacity);
        });
    }
});

// Form validation functions
function validateBookingForm() {
    let isValid = true;
    let firstInvalidField = null;
    
    // Clear previous validation messages
    document.querySelectorAll('.invalid-feedback').forEach(msg => {
        msg.style.display = 'none';
    });
    
    // Validate required fields
    document.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
            firstInvalidField = firstInvalidField || field;
        }
    });
    
    if (!isValid) {
        firstInvalidField.focus();
        Swal.fire({
            icon: 'error',
            title: 'Form Validation Error',
            text: 'Please fill in all required fields.',
            confirmButtonColor: '#ffc107'
        });
    }
    
    return isValid;
}

// Price calculation functions
function updatePriceCalculation() {
    const checkIn = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    if (!checkIn || !checkOut) return;
    
    const nights = calculateNights(checkIn, checkOut);
    const baseTotal = calculateBaseTotal(nights);
    const extraCharges = calculateExtraCharges(nights);
    const discounts = calculateDiscounts(baseTotal + extraCharges.total);
    const finalTotal = baseTotal + extraCharges.total - discounts.total;
    
    updatePriceDisplay(baseTotal, nights, extraCharges, discounts, finalTotal);
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeModals();
});

// Export functions for use in HTML
window.addToCart = addToCart;
window.validateBookingForm = validateBookingForm;
window.updatePriceCalculation = updatePriceCalculation;

// Add this to your existing booking.js file or where you handle the booking submission

function handleBookingSuccess(response) {
    try {
        // Parse response if it's a string
        const result = typeof response === 'string' ? JSON.parse(response) : response;
        
        if (result.success) {
            // Show success modal
            Swal.fire({
                icon: 'success',
                title: 'Booking Successful!',
                text: 'Thank you for your booking! Check your email for confirmation details.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update notification badge if user is logged in
                    if (response.notification_count !== undefined) {
                        const notifBadge = document.querySelector('.notification-badge');
                        if (notifBadge) {
                            notifBadge.textContent = response.notification_count;
                            notifBadge.style.display = response.notification_count > 0 ? 'block' : 'none';
                        }
                    }
                    // Redirect to booking list or home page
                    window.location.href = 'mybookings.php';
                }
            });
        } else {
            throw new Error(result.message || 'Unknown error occurred');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Booking Failed',
            text: error.message || 'There was an error processing your booking. Please try again later.'
        });
    }
}

// Add error handling to the form submission
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const response = await fetch('process_booking.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        handleBookingSuccess(result);
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Submission Error',
            text: 'There was an error submitting your booking. Please try again later.'
        });
    }
});

// Add the rest of your JavaScript functions here 