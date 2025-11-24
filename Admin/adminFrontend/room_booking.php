<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "SELECT first_name, last_name, email, contact_number, is_verified FROM userss";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px;
    }

    .info-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
    }

    /* Fixed Cart Button */
    .btn-cart-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
        border: none;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .btn-cart-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.5);
    }

    .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .carousel-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .carousel-inner img {
        height: 200px;
        object-fit: cover;
    }

    .status-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-available {
        background: #28a745;
        color: white;
    }

    .status-occupied {
        background: #dc3545;
        color: white;
    }

    .status-maintenance {
        background: #ffc107;
        color: #333;
    }

    .room-details {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .room-type {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .price-tag {
        font-size: 1.2rem;
        color: #d4af37;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .price-tag small {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 400;
    }

    .room-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #555;
        font-size: 0.85rem;
    }

    .meta-item i {
        color: #d4af37;
        font-size: 1rem;
    }

    .room-description {
        color: #666;
        line-height: 1.5;
        margin-bottom: 12px;
        font-size: 0.85rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .bed-info {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .bed-info h6 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .bed-info p {
        margin: 0;
        color: #555;
        font-size: 0.85rem;
    }

    .room-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
        margin-top: auto;
    }

    .location-info {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .location-info i {
        color: #d4af37;
        margin-right: 5px;
    }

    .btn-add-to-list {
        background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-add-to-list:hover {
        background: linear-gradient(135deg, #b8941f 0%, #9a7a19 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }

    /* Sidebar Styles */
    .sidebar-cart {
        position: fixed;
        right: -400px;
        top: 0;
        width: 400px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
        transition: right 0.4s ease;
        z-index: 1050;
        overflow-y: auto;
    }

    .sidebar-cart.active {
        right: 0;
    }

    .sidebar-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-header h4 {
        margin: 0;
        font-weight: 700;
    }

    .close-sidebar {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .close-sidebar:hover {
        transform: rotate(90deg);
    }

    .sidebar-content {
        padding: 20px;
    }

    .cart-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        display: flex;
        gap: 15px;
    }

    .cart-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .cart-item-price {
        color: #d4af37;
        font-weight: 700;
    }

    .remove-item {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .cart-total {
        background: #2c3e50;
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .cart-total h5 {
        margin: 0 0 10px 0;
    }

    .total-amount {
        font-size: 1.8rem;
        font-weight: 700;
        color: #d4af37;
    }

    .btn-checkout {
        width: 100%;
        background: #d4af37;
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .btn-checkout:hover {
        background: #b8941f;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
    }

    .overlay.active {
        display: block;
    }

    .empty-cart {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-cart i {
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    /* Carousel Controls Custom Style */
    .carousel-control-prev,
    .carousel-control-next {
        width: 35px;
        height: 35px;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 15px;
        height: 15px;
    }

    .carousel-container:hover .carousel-control-prev,
    .carousel-container:hover .carousel-control-next {
        opacity: 1;
    }

    .carousel-control-prev {
        left: 10px;
    }

    .carousel-control-next {
        right: 10px;
    }

    @media (max-width: 768px) {
        .btn-cart-toggle {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            bottom: 20px;
            right: 20px;
        }
    }
</style>
<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom">
        <i class="fas fa-home"></i>
        <span>User Information</span>
    </div>

    <button class="btn-cart-toggle" onclick="openSidebar()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge" id="cartBadge">0</span>
    </button>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="info-card">
                <!-- Image Carousel -->
                <div class="carousel-container">
                    <span class="status-badge status-available">Available</span>
                    <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="0"
                                class="active"></button>
                            <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800"
                                    class="d-block w-100" alt="Room View 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800"
                                    class="d-block w-100" alt="Room View 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800"
                                    class="d-block w-100" alt="Room View 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="room-details">
                    <h2 class="room-type">Deluxe Suite</h2>
                    <div class="price-tag">
                        ₱5,500.00 <small>/ night</small>
                    </div>

                    <div class="room-meta">
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span><strong>Capacity:</strong> 4 Guests</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span><strong>Size:</strong> 45 m²</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-wifi"></i>
                            <span>Free WiFi</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-snowflake"></i>
                            <span>Air Conditioning</span>
                        </div>
                    </div>

                    <p class="room-description">
                        Experience luxury and comfort in our spacious Deluxe Suite. Featuring modern amenities,
                        elegant furnishings, and stunning views. Perfect for families or groups seeking a premium
                        accommodation experience with all the conveniences of home and the luxury of a boutique
                        hotel.
                    </p>

                    <div class="bed-info">
                        <h6><i class="fas fa-bed"></i> Bed Configuration</h6>
                        <p>1 King Size Bed + 1 Queen Size Bed</p>
                    </div>

                    <div class="room-footer">
                        <div class="location-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <strong>Room 205</strong> | 2nd Floor
                        </div>
                        <button class="btn-add-to-list" onclick="addToCart()">
                            <i class="fas fa-cart-plus"></i>
                            Add to List
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="info-card">
                <div class="carousel-container">
                    <span class="status-badge status-occupied">Occupied</span>
                    <div id="roomCarousel2" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800"
                                    class="d-block w-100" alt="Room View 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800"
                                    class="d-block w-100" alt="Room View 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1629140727571-9b5c6f6267b4?w=800"
                                    class="d-block w-100" alt="Room View 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel2"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel2"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="room-details">
                    <h2 class="room-type">Executive Room</h2>
                    <div class="price-tag">
                        ₱4,200.00 <small>/ night</small>
                    </div>

                    <div class="room-meta">
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>2 Guests</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>32 m²</span>
                        </div>
                    </div>

                    <p class="room-description">
                        Perfect for business travelers. This executive room features a work desk, ergonomic chair,
                        and high-speed internet access. Includes premium amenities and complimentary breakfast.
                    </p>

                    <div class="bed-info">
                        <h6><i class="fas fa-bed"></i> Bed Configuration</h6>
                        <p>1 Queen Size Bed</p>
                    </div>

                    <div class="room-footer">
                        <div class="location-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <strong>Room 310</strong> | 3rd Floor
                        </div>
                        <button class="btn-add-to-list" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fas fa-ban"></i>
                            Occupied
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="info-card">
                <div class="carousel-container">
                    <span class="status-badge status-available">Available</span>
                    <div id="roomCarousel3" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=800"
                                    class="d-block w-100" alt="Room View 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800"
                                    class="d-block w-100" alt="Room View 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://images.unsplash.com/photo-1590073844006-33379778ae09?w=800"
                                    class="d-block w-100" alt="Room View 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel3"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel3"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="room-details">
                    <h2 class="room-type">Standard Room</h2>
                    <div class="price-tag">
                        ₱2,800.00 <small>/ night</small>
                    </div>

                    <div class="room-meta">
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>2 Guests</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>25 m²</span>
                        </div>
                    </div>

                    <p class="room-description">
                        Comfortable and affordable accommodation with all essential amenities.
                        Perfect for couples or solo travelers looking for quality stay at great value.
                    </p>

                    <div class="bed-info">
                        <h6><i class="fas fa-bed"></i> Bed Configuration</h6>
                        <p>2 Single Beds</p>
                    </div>

                    <div class="room-footer">
                        <div class="location-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <strong>Room 108</strong> | 1st Floor
                        </div>
                        <button class="btn-add-to-list"
                            onclick="addToCart('Standard Room', 2800, '108', '1st Floor', 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=200')">
                            <i class="fas fa-cart-plus"></i>
                            Add to List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- Sidebar Cart -->
    <div class="sidebar-cart" id="sidebarCart">
        <div class="sidebar-header">
            <h4><i class="fas fa-shopping-cart"></i> Booking List</h4>
            <button class="close-sidebar" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-content" id="cartContent">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your booking list is empty</p>
            </div>
        </div>
    </div>
</div>

<?php include 'adminFrontend/footer.php'; ?>

<script>
    let cartItems = [];

    function addToCart() {
        const room = {
            id: Date.now(),
            name: 'Deluxe Suite',
            price: 5500,
            room_number: '205',
            floor: '2nd Floor',
            image: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=200'
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
                    </div>
                `;
            return;
        }

        const total = cartItems.reduce((sum, item) => sum + item.price, 0);

        cartContent.innerHTML = `
                ${cartItems.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">₱${item.price.toLocaleString()}</div>
                            <small>Room ${item.room_number} - ${item.floor}</small>
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
                </div>
            `;
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
        alert('Proceeding to checkout with ' + cartItems.length + ' room(s)');
    }
</script>