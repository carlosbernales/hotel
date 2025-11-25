<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$sql = "
SELECT 
    rt.room_type_id,
    rt.room_type,
    rt.price,
    rt.capacity,
    rt.description,
    rt.beds,
    rt.image,
    rt.image2,
    rt.image3,
    rn.room_number,
    rn.floor_number,
    rt.status AS room_type_status,
    rn.status AS room_number_status
FROM room_types rt
INNER JOIN room_numbers rn 
    ON rn.room_type_id = rt.room_type_id
WHERE rt.status = 'active'
  AND rn.status = 'active'
ORDER BY rt.room_type_id ASC
";



$result = $conn->query($sql);
$path = "../Admin/adminBackend/room_type_images/";
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
        <div class="breadcrumb-custom d-flex justify-content-between align-items-center p-3">

        <!-- LEFT SIDE -->
        <div>
            <i class="fas fa-home"></i>
            <span>User Information</span>
        </div>

        <!-- RIGHT SIDE -->
        <form action="#" method="POST" class="d-flex gap-2">

            <!-- Check-in Date -->
            <input type="date" name="check_in" class="form-control" required>

            <!-- Check-out Date -->
            <input type="date" name="check_out" class="form-control" required>

            <!-- Button -->
            <button type="submit" class="btn btn-primary">
                Check Availability
            </button>
        </form>

    </div>


    <button class="btn-cart-toggle" onclick="openSidebar()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge" id="cartBadge">0</span>
    </button>



<div class="row">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $img1 = $path . $row['image'];
        $img2 = $path . $row['image2'];
        $img3 = $path . $row['image3'];
?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="info-card">

            <!-- Status Badge (always active because SQL filters) -->
            <div class="carousel-container">
                <span class="status-badge status-available">Available</span>

                <!-- Image Carousel -->
                <div id="carousel<?= $row['room_type_id'] ?>" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        
                        <div class="carousel-item active">
                            <img src="<?= $img1 ?>" class="d-block w-100">
                        </div>

                        <?php if (!empty($row['image2'])) { ?>
                        <div class="carousel-item">
                            <img src="<?= $img2 ?>" class="d-block w-100">
                        </div>
                        <?php } ?>

                        <?php if (!empty($row['image3'])) { ?>
                        <div class="carousel-item">
                            <img src="<?= $img3 ?>" class="d-block w-100">
                        </div>
                        <?php } ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $row['room_type_id'] ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $row['room_type_id'] ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                </div>
            </div>

            <!-- Room Details -->
            <div class="room-details">
                <h2 class="room-type"><?= $row['room_type'] ?></h2>

                <div class="price-tag">
                    ₱<?= number_format($row['price']) ?> <small>/ night</small>
                </div>

                <div class="room-meta">
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span><strong>Capacity:</strong> <?= $row['capacity'] ?> Guests</span>
                    </div>
                </div>

                <p class="room-description"><?= $row['description'] ?></p>

                <div class="bed-info">
                    <h6><i class="fas fa-bed"></i> Beds</h6>
                    <p><?= $row['beds'] ?></p>
                </div>

                <div class="room-footer">
                    <div class="location-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Room <?= $row['room_number'] ?></strong> |
                        <?= $row['floor_number'] ?> Floor
                    </div>

                    <!-- Always enabled because room is active -->
                    <button class="btn-add-to-list"
                        onclick="addToCart(
                            '<?= $row['room_type'] ?>',
                            <?= $row['price'] ?>,
                            '<?= $row['room_number'] ?>',
                            '<?= $row['floor_number'] ?>',
                            '<?= $img1 ?>'
                        )">
                        <i class="fas fa-cart-plus"></i> Add to List
                    </button>
                </div>
            </div>

        </div>
    </div>
<?php
    }
}
?>
</div>




    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

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

function addToCart(name, price, room_number, floor, image) {
    const room = {
        id: Date.now(),
        name,
        price,
        room_number,
        floor,
        image
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

    const total = cartItems.reduce((sum, item) => sum + item.price, 0);

    document.getElementById("cartBadge").innerText = cartItems.length;

    cartContent.innerHTML = `
        ${cartItems.map(item => `
            <div class="cart-item">
                <img src="${item.image}" class="cart-item-image">
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
        </div>`;
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