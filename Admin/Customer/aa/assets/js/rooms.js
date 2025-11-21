// Cart functionality
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
    
    Swal.fire({
        icon: 'success',
        title: 'Added to List',
        text: 'Room has been added to your list!',
        timer: 1500,
        showConfirmButton: false
    });

    updateCart();
    updateCartCount();

    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    cartModal.show();
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
    
    Swal.fire({
        icon: 'success',
        title: 'Removed',
        text: 'Room has been removed from your list',
        timer: 1500,
        showConfirmButton: false
    });
}

// Room display and updates
function updateRooms() {
    $.post('get_rooms.php', function(response) {
        const rooms = JSON.parse(response);
        const roomsContainer = $('.row');
        roomsContainer.empty();

        rooms.forEach(room => {
            roomsContainer.append(generateRoomCard(room));
        });
    }).fail(function(xhr, status, error) {
        console.error('Error fetching rooms:', error);
        alert('Failed to load rooms. Please try again later.');
    });
}

function generateRoomCard(room) {
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-img-wrapper" style="height: 200px; overflow: hidden;">
                    <img src="${room.image_url}" 
                         class="card-img-top" 
                         alt="${room.room_name}"
                         onerror="this.src='assets/img/rooms/default-room.jpg'"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${room.room_name}</h5>
                    <div class="star-rating text-warning">
                        ${generateStarRating(room.rating || 0)}
                        <span class="rating-count text-muted">(${room.rating_count || 0} reviews)</span>
                    </div>
                    <p class="card-text">₱${parseFloat(room.room_price).toLocaleString()} per night</p>
                    <p class="${room.room_availability > 0 ? 'text-success' : 'text-danger'}">
                        ${room.room_availability > 0 ? `${room.room_availability} rooms left` : 'NOT AVAILABLE'}
                    </p>
                    <p class="text-muted">Max capacity: ${room.room_capacity} guests</p>
                    <div class="room-amenities mb-3">
                        <i class="fas fa-snowflake" title="Air Conditioning"></i>
                        <i class="fas fa-bath" title="Private Bathroom"></i>
                        <i class="fas fa-tv" title="Flat-screen TV"></i>
                        <i class="fas fa-wifi" title="Free WiFi"></i>
                    </div>
                    <div class="mt-auto">
                        <button class="btn btn-warning w-100 mb-2" onclick="viewRoomDetails('${room.room_id}')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn btn-warning w-100" 
                                onclick="addToCart('${room.room_name}', ${room.room_price}, ${room.room_id}, ${room.room_capacity})"
                                ${room.room_availability > 0 ? '' : 'disabled'}>
                            <i class="fas fa-plus"></i> Add to List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function generateStarRating(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
        } else if (i - 0.5 <= rating) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        } else {
            stars += '<i class="far fa-star"></i>';
        }
    }
    return stars;
}

// Room functionality
function viewRoomDetails(roomId) {
    const modal = new bootstrap.Modal(document.getElementById(`roomModal${roomId}`));
    modal.show();
}

// Initialize on document ready
$(document).ready(function() {
    updateRooms();
    initializeModals();
});

// Initialize modals
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        new bootstrap.Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });
    });
}); 