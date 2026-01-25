<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$path = "../Admin/adminBackend/room_type_images/";

if (isset($_POST['check_in']) && isset($_POST['check_out'])) {

    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

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

        (
        SELECT COUNT(*) 
        FROM room_numbers rn_total
        WHERE rn_total.room_type_id = rt.room_type_id
            AND rn_total.status = 'active'
        ) AS total_rooms,

        (
        SELECT COUNT(DISTINCT rn_used.room_number_id)
        FROM booked_rooms br_used
        JOIN bookings b_used ON b_used.booking_id = br_used.booking_id
        JOIN room_numbers rn_used ON rn_used.room_number_id = br_used.room_number_fk_id
        WHERE rn_used.room_type_id = rt.room_type_id
            AND rn_used.status = 'active'
            AND b_used.status IN ('pending','accepted','checkin','rescheduled', 'reserved')
            AND NOT (b_used.check_out < '$check_in' OR b_used.check_in > '$check_out')
        ) AS unavailable_rooms
    FROM room_types rt
    WHERE rt.status = 'active'
    HAVING (total_rooms - unavailable_rooms) > 0
    ORDER BY rt.room_type_id ASC
    ";

    $result = $conn->query($sql);
}

?>

<?php
if (!empty($_SESSION['cea_success'])) {
    $title = $_SESSION['cea_success']['title'];
    $message = $_SESSION['cea_success']['message'];

    echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            CasaEstelaAlert.show(
                'success',
                " . json_encode($title) . ",
                " . json_encode($message) . "
            );
        });
    </script>";

    unset($_SESSION['cea_success']);
}
?>


<link rel="stylesheet" href="../Admin/adminFrontend/css/room_booking.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center p-3">
        <div>
            <i class="fas fa-home"></i>
            <span>Room Booking</span>
        </div>

        <form action="" method="POST" class="availability-form d-flex gap-3 align-items-end">
            <div class="date-input-wrapper">
                <input type="date" name="check_in" id="check_in" class="form-control" required
                    value="<?= isset($_POST['check_in']) ? htmlspecialchars($_POST['check_in']) : '' ?>">
            </div>

            <div class="date-input-wrapper">
                <input type="date" name="check_out" id="check_out" class="form-control" required
                    value="<?= isset($_POST['check_out']) ? htmlspecialchars($_POST['check_out']) : '' ?>">
            </div>

            <button type="submit" class="btn btn-check-availability">
                <i class="fas fa-search me-2"></i>Check Availability
            </button>
        </form>

    </div>
    <button class="btn-cart-toggle" onclick="openSidebar()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge" id="cartBadge">0</span>
    </button>
    <div class="row">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $total_rooms = isset($row['total_rooms']) ? intval($row['total_rooms']) : 0;
                $unavailable_rooms = isset($row['unavailable_rooms']) ? intval($row['unavailable_rooms']) : 0;

                if ($total_rooms === 0 && !isset($row['total_rooms'])) {
                    $q = $conn->prepare("SELECT COUNT(*) FROM room_numbers WHERE room_type_id = ? AND status = 'active'");
                    $q->bind_param("i", $row['room_type_id']);
                    $q->execute();
                    $q->bind_result($count_total);
                    $q->fetch();
                    $q->close();
                    $total_rooms = intval($count_total);
                }

                if (!isset($row['unavailable_rooms'])) {
                    $unavailable_rooms = 0;
                    if (!empty($check_in) && !empty($check_out)) {
                        $qr = $conn->prepare("
                                SELECT COUNT(DISTINCT rn.room_number_id)
                                FROM room_numbers rn
                                INNER JOIN booked_rooms br ON br.room_number_fk_id = rn.room_number_id
                                INNER JOIN bookings b ON b.booking_id = br.booking_id
                                WHERE rn.room_type_id = ?
                                AND rn.status = 'active'
                                AND b.status IN ('pending','accepted')
                                AND NOT (b.check_out < ? OR b.check_in > ?)
                            ");
                        $qr->bind_param("iss", $row['room_type_id'], $check_out, $check_in);
                        $qr->execute();
                        $qr->bind_result($cnt_unavail);
                        $qr->fetch();
                        $qr->close();
                        $unavailable_rooms = intval($cnt_unavail);
                    }
                }

                $available = $total_rooms - $unavailable_rooms;
                if ($available < 0)
                    $available = 0;

                $img1 = $path . $row['image'];
                $img2 = $path . $row['image2'];
                $img3 = $path . $row['image3'];
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="info-card">
                        <div class="carousel-container">
                            <span class="status-badge <?= $available > 0 ? 'status-available' : 'status-unavailable' ?>">
                                <?= $available > 0 ? 'Available' : 'Unavailable' ?>
                            </span>
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
                                <button class="carousel-control-prev" type="button"
                                    data-bs-target="#carousel<?= $row['room_type_id'] ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    data-bs-target="#carousel<?= $row['room_type_id'] ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>

                        <div class="room-details">
                            <h2 class="room-type"><?= $row['room_type'] ?></h2>
                            <div class="price-tag">₱<?= number_format($row['price']) ?> <small>/ night</small></div>
                            <div class="room-meta">
                                <div class="meta-item"><i class="fas fa-users"></i> <span><strong>Capacity:</strong>
                                        <?= $row['capacity'] ?> Guests</span></div>
                            </div>
                            <p class="room-description">Description: <?= $row['description'] ?></p>
                            <div class="bed-info">
                                <h6><i class="fas fa-bed"></i> Beds</h6>
                                <p><?= $row['beds'] ?></p>
                            </div>
                            <div class="room-footer">
                                <div class="location-info">
                                    <i></i>
                                    <strong>Available Rooms: <?= $available ?></strong>
                                </div>

                                <button class="btn-add-to-list" <?= $available > 0 ? "" : "disabled" ?> onclick="addToCart(
                                        '<?= addslashes($row['room_type']) ?>',
                                        <?= floatval($row['price']) ?>,
                                        '<?= isset($row['room_number']) ? addslashes($row['room_number']) : '' ?>',
                                        '<?= isset($row['floor_number']) ? addslashes($row['floor_number']) : '' ?>',
                                        '<?= addslashes($img1) ?>',
                                        <?= intval($row['capacity']) ?>,
                                        <?= intval($row['room_type_id']) ?>,
                                        <?= $available ?>       
                                    )">
                                    <i class="fas fa-cart-plus"></i> <?= $available > 0 ? 'Add to List' : 'No rooms' ?>
                                </button>

                                <button class="btn btn-view-details" data-bs-toggle="modal"
                                    data-bs-target="#detailsModal<?= $row['room_type_id'] ?>">
                                    <i class="fas fa-info-circle"></i> View Details
                                </button>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="detailsModal<?= $row['room_type_id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content room-details-modal">
                            <div class="modal-header-custom">
                                <div class="header-content">
                                    <h5 class="modal-title-custom">
                                        <i class="fas fa-door-open"></i>
                                        <?= $row['room_type'] ?>
                                    </h5>
                                    <span class="price-badge-modal">₱<?= number_format($row['price']) ?> <small>/
                                            night</small></span>
                                </div>
                                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="modal-body-custom">
                                <!-- Carousel -->
                                <div id="carouselDetails<?= $row['room_type_id'] ?>" class="carousel slide room-carousel-modal"
                                    data-bs-ride="carousel">
                                    <div class="carousel-indicators modal-indicators">
                                        <button type="button" data-bs-target="#carouselDetails<?= $row['room_type_id'] ?>"
                                            data-bs-slide-to="0" class="active"></button>
                                        <?php if (!empty($row['image2'])): ?>
                                            <button type="button" data-bs-target="#carouselDetails<?= $row['room_type_id'] ?>"
                                                data-bs-slide-to="1"></button>
                                        <?php endif; ?>
                                        <?php if (!empty($row['image3'])): ?>
                                            <button type="button" data-bs-target="#carouselDetails<?= $row['room_type_id'] ?>"
                                                data-bs-slide-to="2"></button>
                                        <?php endif; ?>
                                    </div>

                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="<?= $path . $row['image'] ?>" class="d-block w-100" alt="Room Image 1">
                                        </div>
                                        <?php if (!empty($row['image2'])): ?>
                                            <div class="carousel-item">
                                                <img src="<?= $path . $row['image2'] ?>" class="d-block w-100" alt="Room Image 2">
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($row['image3'])): ?>
                                            <div class="carousel-item">
                                                <img src="<?= $path . $row['image3'] ?>" class="d-block w-100" alt="Room Image 3">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <button class="carousel-control-prev modal-carousel-control" type="button"
                                        data-bs-target="#carouselDetails<?= $row['room_type_id'] ?>" data-bs-slide="prev">
                                        <span class="carousel-control-icon-modal"><i class="fas fa-chevron-left"></i></span>
                                    </button>
                                    <button class="carousel-control-next modal-carousel-control" type="button"
                                        data-bs-target="#carouselDetails<?= $row['room_type_id'] ?>" data-bs-slide="next">
                                        <span class="carousel-control-icon-modal"><i class="fas fa-chevron-right"></i></span>
                                    </button>
                                </div>

                                <div class="room-info-grid-modal">
                                    <div class="info-card-modal">
                                        <div class="info-icon-modal">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="info-content-modal">
                                            <span class="info-label-modal">Capacity</span>
                                            <span class="info-value-modal"><?= $row['capacity'] ?> Guests</span>
                                        </div>
                                    </div>

                                    <div class="info-card-modal">
                                        <div class="info-icon-modal">
                                            <i class="fas fa-bed"></i>
                                        </div>
                                        <div class="info-content-modal">
                                            <span class="info-label-modal">Beds</span>
                                            <span class="info-value-modal"><?= $row['beds'] ?></span>
                                        </div>
                                    </div>

                                    <div class="info-card-modal">
                                        <div class="info-icon-modal">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <div class="info-content-modal">
                                            <span class="info-label-modal">Price</span>
                                            <span class="info-value-modal">₱<?= number_format($row['price']) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="room-description-modal">
                                    <h6 class="section-title-modal">
                                        <i class="fas fa-align-left"></i> Description
                                    </h6>
                                    <p><?= $row['description'] ?></p>
                                </div>

                                <div class="room-amenities-modal">
                                    <h6 class="section-title-modal">
                                        <i class="fas fa-star"></i> Amenities
                                    </h6>
                                    <div class="amenities-grid-modal">
                                        <div class="amenity-item-modal">
                                            <i class="fas fa-wifi"></i>
                                            <span>Free WiFi</span>
                                        </div>
                                        <div class="amenity-item-modal">
                                            <i class="fas fa-snowflake"></i>
                                            <span>Air Conditioning</span>
                                        </div>
                                        <div class="amenity-item-modal">
                                            <i class="fas fa-tv"></i>
                                            <span>Flat Screen TV</span>
                                        </div>
                                        <div class="amenity-item-modal">
                                            <i class="fas fa-shopping-basket"></i>
                                            <span>Mini Fridge</span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <?php
            }
        } elseif (isset($_POST['check_in'])) {
            echo '<div class="availability-message message-no-rooms">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>No Rooms Available</strong>
                                <p class="mb-0 mt-2">Sorry, there are no rooms available for the selected dates. Please try different dates.</p>
                            </div>';
        } else {
            echo '<div class="availability-message message-check-availability">
                            <i class="fas fa-calendar-check"></i>
                            <strong>Ready to Book?</strong>
                            <p class="mb-0 mt-2">Please select your check-in and check-out dates to see available rooms.</p>
                        </div>';
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

<?php
////////////////////////////////
$bedOptions = "<option value='' selected disabled>Select Extra Bed</option>";
$bedQuery = $conn->query("SELECT id, item_type, price FROM beds");
while ($row = $bedQuery->fetch_assoc()) {
    $bedOptions .= "<option value='{$row['id']}' data-price='{$row['price']}'>{$row['item_type']} (₱{$row['price']})</option>";
}
?>

<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content checkout-modal">
            <div class="modal-header checkout-header">
                <h5 class="modal-title">
                    <i class="fas fa-bookmark"></i> Complete Your Booking
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body checkout-body">
                <form id="checkoutForm" action="../Admin/adminBackend/book_room.php" method="POST"
                    enctype="multipart/form-data">

                    <div class="section-header">
                        <i class="fas fa-user"></i> Guest Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact</label>
                            <input type="text" name="contact" class="form-control custom-input" required>
                        </div>
                    </div>

                    <div class="section-header">
                        <i class="fas fa-calendar-check"></i> Booking Details
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Check-in</label>
                            <input type="date" name="check_in" id="modal_check_in" class="form-control custom-input"
                                readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Check-out</label>
                            <input type="date" name="check_out" id="modal_check_out" class="form-control custom-input"
                                readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="number_of_guests" class="form-control custom-input" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Adults</label>
                                <input type="number" name="num_adults" class="form-control custom-input" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Children</label>
                                <input type="number" name="num_children" class="form-control custom-input" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Room Quantity</label>
                                <input type="number" name="room_quantity" id="room_quantity"
                                    class="form-control custom-input" readonly>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">Extra Bed</label>
                            <div id="extraBedContainer" class="row"></div>
                        </div>



                        <input type="hidden" id="total_capacity" name="total_capacity">
                    </div>

                    <div class="section-header">
                        <i class="fas fa-users"></i> Guest Details
                    </div>
                    <div class="col-12 mb-4">
                        <div id="guestList" class="guest-list-container"></div>
                    </div>

                    <div class="section-header">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control custom-input" required>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="GCash">GCash</option>
                                <option value="Paypal">Paypal</option>
                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="text" name="total_amount" id="total_amount"
                                class="form-control custom-input total-amount" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Discount (%)</label>
                            <input type="text" id="total_discount_percent" name="total_discount_percent"
                                class="form-control custom-input" readonly value="0%">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Discount Amount</label>
                            <input type="text" id="total_discount_amount" name="total_discount_amount"
                                class="form-control custom-input" readonly value="₱0">
                        </div>
                    </div>

                    <input type="hidden" name="cart_items" id="cart_items">

                    <div class="col-12 mt-4 text-center">
                        <button type="button" class="btn btn-confirm" onclick="submitCheckout()">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'adminFrontend/footer.php'; ?>

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

        // ADULTS
        for (let i = 0; i < adults; i++) {
            guestList.innerHTML += createGuestInput(counter, "Adult");
            counter++;
        }

        // CHILDREN
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

</script>


<script>
    // ---------------- CASA ESTELA ALERT SYSTEM ----------------
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

    // ---------------- CASA ESTELA MODAL SYSTEM ----------------
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