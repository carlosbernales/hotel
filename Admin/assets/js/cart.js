function addToCart(roomId, roomType, price, capacity) {
    // Create cart item object
    const cartItem = {
        id: roomId,
        type: roomType,
        price: price,
        capacity: capacity
    };

    // Get existing cart from localStorage or initialize empty array
    let cart = JSON.parse(localStorage.getItem('hotelCart')) || [];
    
    // Add new item
    cart.push(cartItem);
    
    // Save back to localStorage
    localStorage.setItem('hotelCart', JSON.stringify(cart));
    
    // Update cart count in header
    updateCartCount();
    
    // Show success message
    alert('Room added to cart successfully!');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('hotelCart')) || [];
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = cart.length;
        cartCount.style.display = cart.length > 0 ? 'inline' : 'none';
    }
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', updateCartCount); 