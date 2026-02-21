<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['userid']);
?>
<script> 
// Booking List Functionality
function showBookingList() {
    // Include SweetAlert2 if not already loaded
    if (typeof Swal === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);
        script.onload = function() {
            showBookingModal();
        };
    } else {
        showBookingModal();
    }
}

function showBookingModal() {
    // Add custom styles for the booking modal
    const style = document.createElement('style');
    style.textContent = `
        .booking-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.2s ease;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .booking-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .booking-card .card-body {
            padding: 16px;
        }
        .booking-card .room-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .booking-card .room-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        .booking-card .room-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .booking-card .room-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f3f5;
        }
        .booking-card .room-added {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .booking-card .room-capacity {
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            align-items: center;
        }
        .booking-card .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .booking-card .quantity-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            background: white;
            transition: all 0.2s;
        }
        .booking-card .quantity-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }
        .booking-card .quantity-input {
            width: 40px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 0;
            font-size: 0.9rem;
        }
        .booking-card .remove-btn {
            color: #dc3545;
            background: none;
            border: none;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .booking-card .remove-btn:hover {
            background: rgba(220, 53, 69, 0.1);
        }
        #totalSection {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }
        #totalSection h5 {
            margin: 0;
            font-size: 1.1rem;
        }
        #totalAmount {
            font-size: 1.3rem;
            color: #2c3e50;
        }
        .booking-actions {
            display: flex;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
            margin-top: 16px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 20px 0;
        }
        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 16px;
        }
        .empty-state p {
            color: #6c757d;
            margin-bottom: 8px;
        }
        .empty-state small {
            color: #adb5bd;
            font-size: 0.9rem;
        }
    `;
    document.head.appendChild(style);

    Swal.fire({
        title: '<i class="fas fa-bed me-2"></i> Your Booking List',
        html: `
            <div class="container-fluid p-0">
                <div class="booking-summary">
                    <div id="bookingListContent">
                        <div class="empty-state">
                            <i class="fas fa-bed"></i>
                            <p class="mb-1">No rooms added to your list yet</p>
                            <small>Click "ADD TO LIST" on any room to get started</small>
                        </div>
                    </div>
                    
                    <div id="totalSection" class="d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Total Amount</h5>
                                <small class="text-muted">per night</small>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0"><span id="totalAmount" class="text-primary">₱0</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="booking-actions">
                    <button class="btn btn-outline-secondary" onclick="clearBookingList()">
                        <i class="fas fa-trash me-1"></i> Clear List
                    </button>
                    <button class="btn btn-primary" onclick="proceedToBooking()">
                        <i class="fas fa-arrow-right me-1"></i> Proceed to Booking
                    </button>
                </div>
            </div>
        `,
        icon: false,
        showCloseButton: true,
        showConfirmButton: false,
        width: '600px',
        backdrop: `
            rgba(0,0,123,0.1)
            url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23667eea' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10zm10 0c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10z'/%3E%3C/g%3E%3C/svg%3E")
            left top
            repeat
        `,
        didOpen: () => {
            // Load booking list when modal opens
            loadBookingList();
        }
    });
}

function loadBookingList() {
    // Get booking list from localStorage or session
    const bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    const contentDiv = document.getElementById('bookingListContent');
    const totalSection = document.getElementById('totalSection');
    const totalAmountSpan = document.getElementById('totalAmount');
    let totalAmount = 0;
    
    if (bookingList.length === 0) {
        contentDiv.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bed"></i>
                <p class="mb-1">No rooms added to your list yet</p>
                <small>Click "ADD TO LIST" on any room to get started</small>
            </div>
        `;
        totalSection.classList.add('d-none');
    } else {
        let html = '<div class="row g-3">';
        bookingList.forEach((room, index) => {
            const roomPrice = parseFloat(room.price) || 500;
            const roomCapacity = parseInt(room.capacity) || 10;
            const quantity = parseInt(room.quantity) || 1;
            const roomTotal = roomPrice * quantity;
            totalAmount += roomTotal;
            const addedDate = room.addedAt ? new Date(room.addedAt).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) : 'Just now';
            
            html += `
                <div class="col-12">
                    <div class="booking-card">
                        <div class="card-body">
                            <div class="room-header">
                                <h3 class="room-title">${room.roomType}</h3>
                                <button class="remove-btn" onclick="event.stopPropagation(); removeFromList(${index})" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="room-price">
                                    ₱${roomPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <span class="text-muted" style="font-size: 0.9rem; font-weight: normal;">/ night</span>
                                </div>
                            </div>
                            
                            <div class="room-meta">
                                <div class="room-capacity">
                                    <i class="fas fa-users me-1"></i>
                                    <span>Max ${roomCapacity} ${roomCapacity > 1 ? 'guests' : 'guest'}</span>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <span class="me-2 text-muted small">Quantity:</span>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="event.stopPropagation(); updateQuantity(${index}, ${quantity - 1})" ${quantity === 1 ? 'disabled' : ''}>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input" value="${quantity}" min="1" readonly>
                                        <button class="quantity-btn" onclick="event.stopPropagation(); updateQuantity(${index}, ${quantity + 1})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="room-added text-end mt-2">
                                <small class="text-muted">Added: ${addedDate}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        contentDiv.innerHTML = html;
        
        // Show total section and update amount
        totalSection.classList.remove('d-none');
        totalAmountSpan.textContent = `₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        // Update booking badge
        updateBookingBadge();
    }
}

function updateQuantity(index, newQuantity) {
    let bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    
    if (bookingList[index]) {
        const currentQuantity = bookingList[index].quantity;
        newQuantity = parseInt(newQuantity);
        
        // Only check availability when incrementing
        if (newQuantity > currentQuantity) {
            // Check room availability before updating quantity
            checkRoomAvailability(bookingList[index].roomTypeId, (availableRooms) => {
                if (newQuantity > availableRooms) {
                    Swal.fire({
                        title: 'Not Enough Rooms',
                        text: `Only ${availableRooms} room(s) of this type are available.`,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    // Reset to available quantity or current quantity, whichever is smaller
                    newQuantity = Math.min(availableRooms, currentQuantity);
                }
                
                // Ensure quantity doesn't go below 1
                newQuantity = Math.max(1, newQuantity);
                
                bookingList[index].quantity = newQuantity;
                bookingList[index].addedAt = new Date().toISOString();
                localStorage.setItem('bookingList', JSON.stringify(bookingList));
                
                // Reload the list to update totals
                loadBookingList();
            });
        } else {
            // For decrementing, no need to check availability
            newQuantity = Math.max(1, newQuantity);
            bookingList[index].quantity = newQuantity;
            bookingList[index].addedAt = new Date().toISOString();
            localStorage.setItem('bookingList', JSON.stringify(bookingList));
            loadBookingList();
        }
    }
}

function checkRoomAvailability(roomTypeId, callback) {
    fetch('check_room_availability.php?room_type_id=' + roomTypeId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                callback(data.available_rooms);
            } else {
                console.error('Error checking room availability:', data.message);
                callback(0);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            callback(0);
        });
}

function removeFromList(index) {
    let bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    const removedRoom = bookingList[index];
    bookingList.splice(index, 1);
    localStorage.setItem('bookingList', JSON.stringify(bookingList));
    
    // Show notification
    Swal.fire({
        title: 'Removed!',
        text: `${removedRoom.roomType} has been removed from your list`,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
    
    // Reload the list
    loadBookingList();
    updateBookingBadge();
}

function clearBookingList() {
    Swal.fire({
        title: 'Clear Booking List?',
        text: 'Are you sure you want to remove all rooms from your list?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('bookingList');
            loadBookingList();
            updateBookingBadge();
            
            Swal.fire({
                title: 'Cleared!',
                text: 'Your booking list has been cleared',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function updateBookingBadge() {
    const bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    const badge = document.getElementById('bookingBadge');
    
    if (badge) {
        if (bookingList.length > 0) {
            // Calculate total quantity instead of just count of items
            const totalQuantity = bookingList.reduce((total, room) => {
                return total + (parseInt(room.quantity) || 1);
            }, 0);
            
            badge.textContent = totalQuantity;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function proceedToBooking() {
    // Don't close the modal
    // Get booking list from localStorage
    const bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    
    if (bookingList.length === 0) {
        Swal.fire({
            title: 'Empty Booking List',
            text: 'Please add rooms to your booking list first.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Calculate total amount
    let totalAmount = 0;
    bookingList.forEach(room => {
        const roomPrice = parseFloat(room.price) || 500;
        const quantity = parseInt(room.quantity) || 1;
        totalAmount += roomPrice * quantity;
    });
    
    // Show booking form modal
    Swal.fire({
        title: '<div class="d-flex align-items-center"><i class="fas fa-calendar-check me-2"></i> Complete Your Booking</div>',
        html: `
            <div class="booking-form-container" style="max-height: 70vh; overflow-y: auto;">
                <form id="bookingForm" class="needs-validation" novalidate>
                    <?php if (!$isLoggedIn): ?>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="fas fa-user-circle me-2"></i>Your Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="guestFirstName" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="guestFirstName" name="guest_firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guestLastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="guestLastName" name="guest_lastname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guestEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="guestEmail" name="guest_email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guestPhone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="guestPhone" name="guest_phone" required>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="checkInDate" class="form-label fw-medium">Check-in Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" class="form-control" id="checkInDate" required>
                            </div>
                            <div class="invalid-feedback">Please select check-in date</div>
                        </div>
                        <div class="col-md-6">
                            <label for="checkOutDate" class="form-label fw-medium">Check-out Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" class="form-control" id="checkOutDate" required>
                            </div>
                            <div class="invalid-feedback">Please select check-out date</div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="numAdults" class="form-label fw-medium">Number of Adults <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                <input type="number" class="form-control" id="numAdults" name="numAdults" min="1" value="1" required onchange="updateAdultFields()">
                                <span class="input-group-text">Adults</span>
                            </div>
                            <div class="invalid-feedback">Please select number of adults</div>
                            <!-- Dynamic Adult Fields Container -->
                            <div id="adultFieldsContainer" class="mt-3">
                                <!-- Adult fields will be added here dynamically -->
                                <div class="adult-field mb-3 p-3 border rounded">
                                    <h6 class="mb-3">Adult 1 Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="adult1_firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="adult1_firstname" name="adults[0][firstname]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adult1_lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="adult1_lastname" name="adults[0][lastname]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adult1_age" class="form-label">Age <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="adult1_age" name="adults[0][age]" min="18" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adult1_usertype" class="form-label">User Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="adult1_usertype" name="adults[0][usertype]" required>
                                                <option value="" disabled selected>Select user type</option>
                                                <option value="regular">Regular</option>
                                                <option value="senior">Senior Citizen</option>
                                                <option value="pwd">PWD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="numChildren" class="form-label fw-medium">Number of Children <span class="text-muted">(Optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-child"></i></span>
                                <input type="number" class="form-control" id="numChildren" name="numChildren" min="0" value="0" onchange="updateChildrenFields()">
                                <span class="input-group-text">Children</span>
                            </div>
                            <!-- Dynamic Children Fields Container -->
                            <div id="childrenFieldsContainer" class="mt-3">
                                <!-- Children fields will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paymentOption" class="form-label fw-medium">Payment Option <span class="text-danger">*</span></label>
                                <select class="form-select" id="paymentOption" name="paymentOption" required onchange="updatePaymentDetailsField()">
                                    <option value="" selected disabled>Select payment option</option>
                                    <option value="down_payment">Down Payment (₱1,500)</option>
                                    <option value="full_payment">Full Payment</option>
                                    <option value="custom_payment">Custom Payment</option>
                                </select>
                                <div class="invalid-feedback">Please select a payment option</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="paymentMethodField">
                                <label for="paymentMethod" class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                    <option value="" selected disabled>Select payment method</option>
                                    <option value="maya">Maya</option>
                                    <option value="gcash">GCash</option>
                                </select>
                                <div class="invalid-feedback">Please select a payment method</div>
                                <div id="paymentDetailsContainer" class="mt-3 p-3 bg-light rounded-3" style="display: none;">
                                    <div id="referenceField" class="mb-3">
                                        <label for="paymentDetails" class="form-label fw-medium">Payment Reference <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="paymentDetails" name="paymentDetails" placeholder="Enter reference number" required>
                                        <div class="invalid-feedback">Please enter a reference number</div>
                                        <small class="form-text text-muted">Enter transaction/reference number</small>
                                    </div>
                                    <div id="amountField" class="mb-2" style="display: none;">
                                        <label for="paymentAmount" class="form-label fw-medium">Amount to Pay <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter amount" min="1" step="0.01">
                                            <div class="invalid-feedback">Please enter a valid amount</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-summary mt-4 bg-light p-4 rounded-3 shadow-sm">
                        <h5 class="mb-4 pb-2 border-bottom d-flex align-items-center">
                            <i class="fas fa-receipt me-2 text-primary"></i> Booking Summary
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody id="bookingSummaryContent">
                                    <tr class="border-bottom">
                                        <td colspan="2" class="pb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                                                    <i class="fas fa-user me-1"></i> <span id="guestTypeDisplay">Regular</span> Guest
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    ${bookingList.map((room, index) => {
                                        const roomPrice = parseFloat(room.price) || 0;
                                        const quantity = parseInt(room.quantity) || 1;
                                        
                                        return `
                                            <tr>
                                                <td>${room.roomType || 'Standard Room'} (${quantity})</td>
                                                <td class="text-end room-price" data-price="${roomPrice}">
                                                    ₱${roomPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per night
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Room Total (<span class="nights-count">1</span> night<span class="nights-plural"></span>)</td>
                                                <td class="text-end room-total">
                                                    ₱${roomPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                                </td>
                                                
                                            </tr>
                                        `;
                                    }).join('')}
                                    <tr class="border-top">
                                        <td class="pt-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-moon me-2 text-primary"></i>
                                                <span>Number of Nights</span>
                                            </div>
                                        </td>
                                        <td class="text-end pt-3">
                                            <span class="fw-bold"><span class="nights-count">1</span> night<span class="nights-plural"></span></span>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="pt-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag me-2 text-primary"></i>
                                                <span class="fw-semibold">Total Amount:</span>
                                            </div>
                                        </td>
                                        <td class="text-end pt-3">
                                            <h5 class="mb-0 text-primary fw-bold" id="totalAmount">₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                                        </td>
                                    </tr>
                                    <tr id="paymentDetailsRow" class="border-top" style="display: none;">
                                        <td class="pt-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-credit-card me-2 text-primary"></i>
                                                <span class="fw-semibold">Payment Option:</span>
                                            </div>
                                        </td>
                                        <td class="text-end pt-3">
                                            <div id="selectedPaymentOption" class="fw-medium mb-1"></div>
                                            <div id="amountToPay" class="fw-bold text-success"></div>
                                            <div id="remainingBalance" class="text-muted small mt-1"></div>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td colspan="2" class="pt-3">
                                            <div class="alert alert-light border d-flex align-items-center mb-0 py-2" role="alert">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                <div class="small">
                                                    <div class="fw-medium">Check-in/Check-out Times:</div>
                                                    <div>Check-in: 1:00 PM | Check-out: 11:00 AM</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        `,
        icon: false,
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-arrow-right me-2"></i> Proceed to Summary',
        cancelButtonText: 'Back to List',
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        width: '700px',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            const form = document.getElementById('bookingForm');
            if (!form.checkValidity()) {
                // Add was-validated class to show validation messages
                form.classList.add('was-validated');
                
                // Scroll to the first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
                
                // Prevent form submission
                return false;
            }
            
            // Show loading state on the button
            const confirmButton = document.querySelector('.swal2-confirm');
            if (confirmButton) {
                confirmButton.disabled = true;
                confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            }
            
            // Return a promise that resolves after 3 seconds
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        checkInDate: document.getElementById('checkInDate').value,
                        checkOutDate: document.getElementById('checkOutDate').value,
                        paymentOption: document.getElementById('paymentOption').value,
                        paymentMethod: document.getElementById('paymentMethod').value,
                        paymentDetails: document.getElementById('paymentDetails')?.value || '',
                        paymentAmount: document.getElementById('paymentAmount')?.value || ''
                    });
                }, 3000);
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
        allowEscapeKey: () => !Swal.isLoading(),
        showCancelButton: true,
        showConfirmButton: true,
        showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // Get form data
            const bookingForm = document.getElementById('bookingForm');
            const formData = new FormData(bookingForm);
            
            // Add guest information to form data if user is not logged in
            <?php if (!$isLoggedIn): ?>
            formData.append('guest_firstname', document.getElementById('guestFirstName').value);
            formData.append('guest_lastname', document.getElementById('guestLastName').value);
            formData.append('guest_email', document.getElementById('guestEmail').value);
            formData.append('guest_phone', document.getElementById('guestPhone').value);
            <?php endif; ?>
            
            // Get booking list from localStorage
            const bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
            
            // Get guest information
            const numAdults = parseInt(document.getElementById('numAdults').value) || 0;
            const numChildren = parseInt(document.getElementById('numChildren').value) || 0;
            
            // Collect adult details
            const adults = [];
            for (let i = 1; i <= numAdults; i++) {
                adults.push({
                    firstName: document.getElementById(`adult${i}_firstname`).value,
                    lastName: document.getElementById(`adult${i}_lastname`).value,
                    age: document.getElementById(`adult${i}_age`).value,
                    userType: document.getElementById(`adult${i}_usertype`).value
                });
            }
            
            // Collect children details
            const children = [];
            for (let i = 1; i <= numChildren; i++) {
                children.push({
                    firstName: document.getElementById(`child${i}_firstname`).value,
                    lastName: document.getElementById(`child${i}_lastname`).value,
                    age: document.getElementById(`child${i}_age`).value
                });
            }
            
            // Calculate number of nights
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            const nights = calculateNights(checkInDate, checkOutDate);
            
            // Get payment information
            const paymentOption = document.getElementById('paymentOption').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const paymentDetails = document.getElementById('paymentDetails')?.value || '';
            const totalAmount = document.getElementById('totalAmount')?.textContent.replace(/[^0-9.]/g, '') || '0';
            
            // Calculate payment amounts
            let amountToPay = 0;
            let remainingBalance = 0;
            
            if (paymentOption === 'down_payment') {
                amountToPay = 1500; // Down payment amount
                remainingBalance = parseFloat(totalAmount) - amountToPay;
            } else if (paymentOption === 'full_payment') {
                amountToPay = parseFloat(totalAmount);
                remainingBalance = 0;
            } else if (paymentOption === 'custom_payment') {
                amountToPay = parseFloat(document.getElementById('paymentAmount')?.value || '0');
                remainingBalance = Math.max(0, parseFloat(totalAmount) - amountToPay);
            }
            
            // Prepare data for submission
            const bookingData = {
                // Booking dates
                checkInDate,
                checkOutDate,
                nights,
                
                // Guest information
                numAdults,
                numChildren,
                adults,
                children,
                
                // Room information
                rooms: bookingList.map(room => ({
                    roomTypeId: room.roomTypeId,
                    room_type_name: room.roomType || room.name, // Use roomType or name as fallback
                    name: room.roomType || room.name, // Keep name for backward compatibility
                    price: parseFloat(room.price) || 0,
                    capacity: room.capacity || 2, // Default capacity if not specified
                    quantity: room.quantity || 1,
                    subtotal: (parseFloat(room.price) || 0) * (room.quantity || 1)
                })),
                
                // Payment information
                paymentOption,
                paymentMethod,
                paymentDetails,
                amountToPay,
                remainingBalance,
                
                // Booking summary
                totalAmount: parseFloat(totalAmount),
                currency: '₱',
                timestamp: new Date().toISOString()
            };
            
            // Generate a temporary booking reference
            const bookingRef = 'BOOK-' + Math.random().toString(36).substr(2, 8).toUpperCase();
            
            // Store sensitive data in sessionStorage
            const sensitiveData = {
                paymentDetails: bookingData.paymentDetails,
                adults: bookingData.adults,
                children: bookingData.children,
                timestamp: new Date().getTime()
            };
            
            // Store with a short expiration (15 minutes)
            sessionStorage.setItem(`booking_${bookingRef}`, JSON.stringify(sensitiveData));
            
            // Create URL parameters for non-sensitive data
            const params = new URLSearchParams();
            
            // Add booking reference
            params.append('ref', bookingRef);
            
            // Add booking details
            params.append('check_in', encodeURIComponent(bookingData.checkInDate));
            params.append('check_out', encodeURIComponent(bookingData.checkOutDate));
            params.append('nights', bookingData.nights);
            
            // Add guest counts
            params.append('adults', bookingData.numAdults);
            params.append('children', bookingData.numChildren);
            
            // Add guest information if not logged in
            <?php if (!$isLoggedIn): ?>
            params.append('first_name', encodeURIComponent(document.getElementById('guestFirstName')?.value || ''));
            params.append('last_name', encodeURIComponent(document.getElementById('guestLastName')?.value || ''));
            params.append('email', encodeURIComponent(document.getElementById('guestEmail')?.value || ''));
            params.append('phone', encodeURIComponent(document.getElementById('guestPhone')?.value || ''));
            <?php endif; ?>
            
            // Add adult details
            for (let i = 0; i < adults.length; i++) {
                const adult = adults[i];
                params.append(`adult_${i+1}_firstname`, encodeURIComponent(adult.firstName || ''));
                params.append(`adult_${i+1}_lastname`, encodeURIComponent(adult.lastName || ''));
                params.append(`adult_${i+1}_age`, encodeURIComponent(adult.age || ''));
                params.append(`adult_${i+1}_usertype`, encodeURIComponent(adult.userType || 'regular'));
            }
            
            // Add children details
            for (let i = 0; i < children.length; i++) {
                const child = children[i];
                params.append(`child_${i+1}_firstname`, encodeURIComponent(child.firstName || ''));
                params.append(`child_${i+1}_lastname`, encodeURIComponent(child.lastName || ''));
                params.append(`child_${i+1}_age`, encodeURIComponent(child.age || ''));
            }
            
            // Add room information
            bookingData.rooms.forEach((room, index) => {
                params.append(`room_${index}_type`, encodeURIComponent(room.room_type_name || room.name || 'Room'));
                params.append(`room_${index}_qty`, room.quantity);
                params.append(`room_${index}_price`, room.price);
            });
            
            // Add payment information
            params.append('payment_option', encodeURIComponent(bookingData.paymentOption || ''));
            params.append('payment_method', encodeURIComponent(bookingData.paymentMethod || ''));
            params.append('total_amount', bookingData.totalAmount || 0);
            params.append('amount_due', bookingData.amountToPay || 0);
            params.append('balance', bookingData.remainingBalance || 0);
            
            // Redirect to summary page with URL parameters
            window.location.href = `room_booking_summary.php?${params.toString()}`;
            
            // Prevent any default form submission
            return false;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // If user clicks back, show the booking list again
            showBookingList();
        }
    });
}


function calculateNights(checkIn, checkOut) {
    if (!checkIn || !checkOut) return 0;
    
    const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    const startDate = new Date(checkIn);
    const endDate = new Date(checkOut);
    
    // Reset time part to avoid timezone issues
    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);
    
    // Calculate the difference in days
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / oneDay);
    
    return Math.max(1, diffDays);
}

// Function to update booking summary when dates change
function updateBookingSummary() {
    const checkInInput = document.getElementById('checkInDate');
    const checkOutInput = document.getElementById('checkOutDate');
    
    if (!checkInInput || !checkOutInput) return;
    
    const checkIn = checkInInput.value;
    const checkOut = checkOutInput.value;
    
    if (!checkIn || !checkOut) return;
    
    // Calculate number of nights
    const nights = calculateNights(checkIn, checkOut);
    
    // Update the nights count display
    document.querySelectorAll('.nights-count').forEach(element => {
        element.textContent = nights;
    });
    
    // Update the nights plural
    document.querySelectorAll('.nights-plural').forEach(element => {
        element.textContent = nights !== 1 ? 's' : '';
    });
    
    // Update room totals and calculate grand total
    const roomPriceElements = document.querySelectorAll('.room-price');
    let totalAmount = 0;
    
    roomPriceElements.forEach(priceElement => {
        const pricePerNight = parseFloat(priceElement.dataset.price) || 0;
        const roomTotal = pricePerNight * nights;
        const roomTotalElement = priceElement.closest('tr').nextElementSibling.querySelector('.room-total');
        
        if (roomTotalElement) {
            roomTotalElement.textContent = `₱${roomTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            totalAmount += roomTotal;
        }
    });
    
    // Update the total amount display
    const totalAmountElement = document.getElementById('totalAmount');
    if (totalAmountElement) {
        totalAmountElement.textContent = `₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
    
    // Update the remaining balance
    const remainingBalanceElement = document.querySelector('td:contains("Remaining Balance:")')?.nextElementSibling;
    if (remainingBalanceElement) {
        const remainingBalance = totalAmount - 1500; // Assuming 1500 is the down payment
        remainingBalanceElement.textContent = `₱${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
}

// Function to handle date changes
function handleDateChange() {
    const checkInInput = document.getElementById('checkInDate');
    const checkOutInput = document.getElementById('checkOutDate');
    
    if (!checkInInput || !checkOutInput) return;
    
    // Update check-out min date when check-in date changes
    if (checkInInput.value) {
        const nextDay = new Date(checkInInput.value);
        nextDay.setDate(nextDay.getDate() + 1);
        const nextDayStr = nextDay.toISOString().split('T')[0];
        checkOutInput.min = nextDayStr;
        
        // If current check-out is before new min date, update it
        if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
            checkOutInput.value = nextDayStr;
        }
    }
    
    // Only update if both dates are valid
    if (checkInInput.value && checkOutInput.value) {
        updateBookingSummary();
    }
}

// Add event listeners for date changes using event delegation
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for date input changes
    document.addEventListener('change', function(e) {
        if (e.target && (e.target.id === 'checkInDate' || e.target.id === 'checkOutDate')) {
            handleDateChange();
        }
    });
    
    // Set initial min dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Format date as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set initial values when the booking modal is shown
    $(document).on('shown.bs.modal', '.swal2-container', function() {
        const checkInInput = document.getElementById('checkInDate');
        const checkOutInput = document.getElementById('checkOutDate');
        
        if (checkInInput) {
            checkInInput.min = formatDate(today);
            if (!checkInInput.value) {
                checkInInput.value = formatDate(today);
            }
            
            // Add event listener for check-in date changes
            checkInInput.onchange = handleDateChange;
        }
        
        if (checkOutInput) {
            checkOutInput.min = formatDate(tomorrow);
            if (!checkOutInput.value) {
                checkOutInput.value = formatDate(tomorrow);
            }
            
            // Add event listener for check-out date changes
            checkOutInput.onchange = updateBookingSummary;
        }
        
        // Initial update of booking summary
        updateBookingSummary();
    });
    
    // Also try to set up the dates when the page first loads
    const checkInInput = document.getElementById('checkInDate');
    const checkOutInput = document.getElementById('checkOutDate');
    
    if (checkInInput && checkOutInput) {
        checkInInput.onchange = handleDateChange;
        checkOutInput.onchange = updateBookingSummary;
        
        // Set initial values if they're not set
        if (!checkInInput.value) {
            checkInInput.value = formatDate(today);
            checkInInput.min = formatDate(today);
        }
        
        if (!checkOutInput.value) {
            checkOutInput.value = formatDate(tomorrow);
            checkOutInput.min = formatDate(tomorrow);
        }
        
        // Initial update
        updateBookingSummary();
    }
});

function setMinDates() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set min dates when the booking form is opened
    $(document).on('shown.bs.modal', '.swal2-container', function() {
        const checkInDate = document.getElementById('checkInDate');
        const checkOutDate = document.getElementById('checkOutDate');
        
        if (checkInDate) {
            checkInDate.min = formatDate(today);
            checkInDate.value = formatDate(today);
        }
        
        if (checkOutDate) {
            checkOutDate.min = formatDate(tomorrow);
            checkOutDate.value = formatDate(tomorrow);
        }
        
        // Update check-out date when check-in date changes
        if (checkInDate && checkOutDate) {
            checkInDate.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const nextDay = new Date(selectedDate);
                nextDay.setDate(selectedDate.getDate() + 1);
                checkOutDate.min = formatDate(nextDay);
                
                // If current check-out is before new min date, update it
                if (new Date(checkOutDate.value) < nextDay) {
                    checkOutDate.value = formatDate(nextDay);
                }
            });
        }
    });
}

// Initialize date pickers when the page loads
document.addEventListener('DOMContentLoaded', function() {
    setMinDates();
});

// Initialize booking badge on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBookingBadge();
});

// Function to calculate total amount from room prices and nights
function calculateTotalAmount() {
    const roomPriceElements = document.querySelectorAll('.room-price');
    const nights = parseInt(document.querySelector('.nights-count')?.textContent) || 1;
    let total = 0;
    
    roomPriceElements.forEach(priceElement => {
        const pricePerNight = parseFloat(priceElement.dataset.price) || 0;
        const quantity = parseInt(priceElement.closest('tr').querySelector('.quantity')?.textContent) || 1;
        total += pricePerNight * quantity * nights;
    });
    
    return total;
}

// Function to update payment details field based on selected payment option
function updatePaymentDetailsField() {
    const paymentOption = document.getElementById('paymentOption');
    const paymentMethod = document.getElementById('paymentMethod');
    const paymentDetailsContainer = document.getElementById('paymentDetailsContainer');
    const referenceField = document.getElementById('referenceField');
    const amountField = document.getElementById('amountField');
    const paymentDetails = document.getElementById('paymentDetails');
    const paymentAmount = document.getElementById('paymentAmount');
    const paymentDetailsRow = document.getElementById('paymentDetailsRow');
    const selectedPaymentOption = document.getElementById('selectedPaymentOption');
    const amountToPay = document.getElementById('amountToPay');
    const remainingBalance = document.getElementById('remainingBalance');
    
    if (!paymentOption || !paymentMethod || !paymentDetailsContainer) return;
    
    // Calculate total amount
    const totalAmount = calculateTotalAmount();
    const downPayment = 1500; // Fixed down payment amount
    
    // Show/hide fields based on payment option
    if (paymentOption.value === 'custom_payment') {
        referenceField.style.display = 'none';
        amountField.style.display = 'block';
        paymentDetails.required = false;
        paymentAmount.required = true;
        paymentAmount.placeholder = `Enter amount (max ₱${totalAmount.toLocaleString()})`;
        paymentAmount.max = totalAmount;
        
        // Update payment details in summary with colors
        selectedPaymentOption.innerHTML = '<span style="color: #4a6baf; font-weight: 600;">Custom Payment</span>';
        amountToPay.innerHTML = '';
        remainingBalance.textContent = '';
    } else {
        referenceField.style.display = 'block';
        amountField.style.display = 'none';
        paymentDetails.required = true;
        paymentAmount.required = false;
        
        // Show/hide payment details based on payment method
        if (paymentMethod.value === 'cash') {
            paymentDetailsContainer.style.display = 'none';
            paymentDetails.required = false;
        } else {
            paymentDetailsContainer.style.display = 'block';
            paymentDetails.required = true;
        }
    }
    
    // Handle payment option changes
    switch(paymentOption.value) {
        case 'down_payment':
            // Update payment details in summary with colors
            selectedPaymentOption.innerHTML = '<span style="color: #4a6baf; font-weight: 600;">Down Payment (₱1,500)</span>';
            amountToPay.innerHTML = `<div style="color: #2e7d32; font-weight: 500;">Amount to Pay: <span style="font-weight: 600;">₱1,500.00</span></div>`;
            remainingBalance.innerHTML = `<div style="color: #d32f2f;">Remaining Balance: <span style="font-weight: 500;">₱${(totalAmount - downPayment).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></div>`;
            
            // Update form fields
            paymentMethod.value = '';
            paymentDetailsContainer.style.display = 'none';
            referenceField.style.display = 'none';
            amountField.style.display = 'none';
            paymentDetails.required = false;
            paymentAmount.required = false;
            break;
            
        case 'full_payment':
            // Update payment details in summary with colors
            selectedPaymentOption.innerHTML = '<span style="color: #4a6baf; font-weight: 600;">Full Payment</span>';
            amountToPay.innerHTML = `<div style="color: #2e7d32; font-weight: 500;">Amount to Pay: <span style="font-weight: 600;">₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></div>`;
            remainingBalance.innerHTML = '<div style="color: #2e7d32;">Remaining Balance: <span style="font-weight: 500;">₱0.00</span></div>';
            
            // Update form fields
            paymentMethod.value = '';
            paymentDetailsContainer.style.display = 'none';
            referenceField.style.display = 'none';
            amountField.style.display = 'none';
            paymentDetails.required = false;
            paymentAmount.required = false;
            break;
            
        case 'custom_payment':
            // For custom payment, show amount input
            paymentMethod.value = '';
            paymentDetailsContainer.style.display = 'block';
            referenceField.style.display = 'none';
            amountField.style.display = 'block';
            paymentDetails.required = false;
            paymentAmount.required = true;
            paymentAmount.placeholder = `Enter amount (max ₱${totalAmount.toLocaleString()})`;
            paymentAmount.max = totalAmount;
            
            // Add event listener for custom amount input
            paymentAmount.oninput = function() {
                const customAmount = parseFloat(this.value) || 0;
                if (customAmount > totalAmount) {
                    this.setCustomValidity(`Amount cannot exceed ₱${totalAmount.toLocaleString()}`);
                    return;
                }
                this.setCustomValidity('');
                
                // Update remaining balance in real-time
                const remaining = totalAmount - customAmount;
                amountToPay.innerHTML = `<div style="color: #2e7d32; font-weight: 500;">Amount to Pay: <span style="font-weight: 600;">₱${customAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></div>`;
                const balanceColor = remaining > 0 ? '#d32f2f' : '#2e7d32';
                remainingBalance.innerHTML = `<div style="color: ${balanceColor};">Remaining Balance: <span style="font-weight: 500;">₱${remaining.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span></div>`;
            };
            break;
    }
    
    // Show the payment details row if a payment option is selected
    if (paymentOption.value) {
        paymentDetailsRow.style.display = '';
    } else {
        paymentDetailsRow.style.display = 'none';
    }
}

// Function to update children fields based on number of children
function updateChildrenFields() {
    const numChildren = parseInt(document.getElementById('numChildren').value) || 0;
    const container = document.getElementById('childrenFieldsContainer');
    
    // Clear existing fields
    container.innerHTML = '';
    
    // Add children fields if needed
    for (let i = 1; i <= numChildren; i++) {
        const newField = `
            <div class="children-field mb-3 p-3 border rounded">
                <h6 class="mb-3">Child ${i} Details</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="child${i}_firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="child${i}_firstname" name="children[${i-1}][firstname]" required>
                    </div>
                    <div class="col-md-6">
                        <label for="child${i}_lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="child${i}_lastname" name="children[${i-1}][lastname]" required>
                    </div>
                    <div class="col-md-6">
                        <label for="child${i}_age" class="form-label">Age <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="child${i}_age" name="children[${i-1}][age]" min="0" max="17" required>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newField);
    }
}

// Function to update adult fields based on number of adults
function updateAdultFields() {
    const numAdults = parseInt(document.getElementById('numAdults').value) || 1;
    const container = document.getElementById('adultFieldsContainer');
    
    // Clear existing fields except the first one
    while (container.children.length > 1) {
        container.removeChild(container.lastChild);
    }
    
    // Update the first adult field number
    if (container.firstElementChild) {
        container.firstElementChild.querySelector('h6').textContent = 'Adult 1 Details';
    }
    
    // Add additional adult fields if needed
    for (let i = 2; i <= numAdults; i++) {
        const newField = `
            <div class="adult-field mb-3 p-3 border rounded">
                <h6 class="mb-3">Adult ${i} Details</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="adult${i}_firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="adult${i}_firstname" name="adults[${i-1}][firstname]" required>
                    </div>
                    <div class="col-md-6">
                        <label for="adult${i}_lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="adult${i}_lastname" name="adults[${i-1}][lastname]" required>
                    </div>
                    <div class="col-md-6">
                        <label for="adult${i}_age" class="form-label">Age <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="adult${i}_age" name="adults[${i-1}][age]" min="18" required>
                    </div>
                    <div class="col-md-6">
                        <label for="adult${i}_usertype" class="form-label">User Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="adult${i}_usertype" name="adults[${i-1}][usertype]" required>
                            <option value="" disabled selected>Select user type</option>
                            <option value="regular">Regular</option>
                            <option value="senior">Senior Citizen</option>
                            <option value="pwd">PWD</option>
                            <option value="student">Student</option>
                            <option value="foreigner">Foreigner</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newField);
    }
}

// Make the function globally available
window.showBookingList = showBookingList;
window.proceedToBooking = proceedToBooking;
window.updateAdultFields = updateAdultFields;
window.updatePaymentDetailsField = updatePaymentDetailsField;

</script>