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

    .location-info {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        color: #555;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .location-info i {
        color: #d4af37;
        font-size: 1rem;
    }

    .room-footer {
        padding-top: 12px;
        border-top: 1px solid #e9ecef;
        margin-top: auto;
    }

    .btn-add-to-list {
        background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
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
<!-- Availability Check Form with Custom Styling -->
<style>
    .availability-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .availability-form {
        max-width: 900px;
        margin-left: auto;
        margin-right: 0;
    }

    .availability-form .form-control {
        border: 2px solid #d4af37;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .availability-form .form-control:focus {
        border-color: #c19b2e;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        outline: none;
    }

    .availability-form .btn-check-availability {
        background: linear-gradient(135deg, #d4af37 0%, #c19b2e 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .availability-form .btn-check-availability:hover {
        background: linear-gradient(135deg, #c19b2e 0%, #a88728 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    .availability-message {
        text-align: center;
        padding: 2rem;
        border-radius: 12px;
        margin: 2rem auto;
        max-width: 600px;
        font-size: 1.1rem;
        animation: fadeIn 0.5s ease;
    }

    .message-no-rooms {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        border: 2px solid #ffc107;
        color: #856404;
    }

    .message-no-rooms i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
        color: #ffc107;
    }

    .message-check-availability {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 2px solid #17a2b8;
        color: #0c5460;
    }

    .message-check-availability i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.5rem;
        color: #17a2b8;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .date-input-wrapper {
        position: relative;
        flex: 1;
    }

    .date-input-wrapper label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .availability-form {
            flex-direction: column;
        }

        .availability-form .btn-check-availability {
            width: 100%;
        }
    }
</style>


<style>
    /* Checkout Modal Styling */
    .checkout-modal {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .checkout-header {
        background: linear-gradient(135deg, #c5a572 0%, #b8935a 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: none;
    }

    .checkout-header .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkout-body {
        padding: 30px;
        background-color: #f8f9fa;
    }

    .section-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: #c5a572;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .section-header i {
        font-size: 1.2rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .custom-input {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    .custom-input:focus {
        border-color: #c5a572;
        box-shadow: 0 0 0 0.2rem rgba(197, 165, 114, 0.25);
        outline: none;
    }

    .custom-input:read-only {
        background-color: #f1f3f5;
        cursor: not-allowed;
    }

    .custom-input.total-amount {
        font-size: 1.2rem;
        font-weight: 700;
        color: #c5a572;
        background-color: #2c3e50;
        border-color: #c5a572;
    }

    .guest-list-container {
        background: white;
        border-radius: 8px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        min-height: 80px;
    }

    .btn-confirm {
        background: linear-gradient(135deg, #c5a572 0%, #b8935a 100%);
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(197, 165, 114, 0.4);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(197, 165, 114, 0.6);
        background: linear-gradient(135deg, #b8935a 0%, #c5a572 100%);
    }

    .btn-confirm:active {
        transform: translateY(0);
    }

    .btn-close-white {
        filter: brightness(0) invert(1);
        opacity: 1;
    }

    .btn-close-white:hover {
        opacity: 0.8;
    }

    /* Custom scrollbar for modal */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c5a572;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #b8935a;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .checkout-body {
            padding: 20px;
        }

        .section-header {
            font-size: 1rem;
            padding: 10px 15px;
        }

        .checkout-header .modal-title {
            font-size: 1.2rem;
        }

        .btn-confirm {
            width: 100%;
            justify-content: center;
        }
    }
</style>

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
            FROM room_numbers rn
            WHERE rn.room_type_id = rt.room_type_id
              AND rn.status = 'active'
        ) AS total_rooms,
       
        (
            SELECT COUNT(DISTINCT rn2.room_number_id)
            FROM room_numbers rn2
            INNER JOIN booked_rooms br ON br.room_type_id = rn2.room_type_id
            INNER JOIN bookings b ON b.booking_id = br.booking_id
            WHERE rn2.room_type_id = rt.room_type_id
              AND rn2.status = 'active'
              AND b.status IN ('pending','accepted')
              AND (
                    b.check_in <= '$check_out'
                AND b.check_out >= '$check_in'
              )
        ) AS unavailable_rooms
    FROM room_types rt
    WHERE rt.status = 'active'
    HAVING (total_rooms - unavailable_rooms) > 0
    ORDER BY rt.room_type_id ASC
    ";

    $result = $conn->query($sql);
}

?>

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

                // ensure numeric values and avoid undefined index notices
                $total_rooms = isset($row['total_rooms']) ? intval($row['total_rooms']) : 0;
                $unavailable_rooms = isset($row['unavailable_rooms']) ? intval($row['unavailable_rooms']) : 0;

                // if your SQL did not include those aliases, compute them here (fallback)
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
                    // count booked rooms that conflict with requested dates (if check_in/check_out provided)
                    $unavailable_rooms = 0;
                    if (!empty($check_in) && !empty($check_out)) {
                        $qr = $conn->prepare("
                SELECT COUNT(DISTINCT rn.room_number_id)
                FROM room_numbers rn
                INNER JOIN booked_rooms br ON br.room_type_id = rn.room_type_id
                INNER JOIN bookings b ON b.booking_id = br.booking_id
                WHERE rn.room_type_id = ?
                  AND rn.status = 'active'
                  AND b.status IN ('pending','accepted')
                  AND (b.check_in <= ? AND b.check_out >= ?)
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

                // prepare images and other variables as before
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
                            <p class="room-description"><?= $row['description'] ?></p>
                            <div class="bed-info">
                                <h6><i class="fas fa-bed"></i> Beds</h6>
                                <p><?= $row['beds'] ?></p>
                            </div>
                            <div class="room-footer">
                                <div class="location-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Available Rooms: <?= $available ?></strong>
                                </div>

                                <button class="btn-add-to-list" <?= $available > 0 ? "" : "disabled" ?> onclick="addToCart(
                            '<?= addslashes($row['room_type']) ?>',
                            <?= floatval($row['price']) ?>,
                            '<?= isset($row['room_number']) ? addslashes($row['room_number']) : '' ?>',
                            '<?= isset($row['floor_number']) ? addslashes($row['floor_number']) : '' ?>',
                            '<?= addslashes($img1) ?>',
                            <?= intval($row['capacity']) ?>,
                            <?= intval($row['room_type_id']) ?>
                        )">
                                    <i class="fas fa-cart-plus"></i> <?= $available > 0 ? 'Add to List' : 'No rooms' ?>
                                </button>
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
            // Page loaded or reloaded without form submission
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

                    <!-- Guest Information Section -->
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

                    <!-- Booking Details Section -->
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

                        <div class="col-md-4">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" name="number_of_guests" class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Adults</label>
                            <input type="number" name="num_adults" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Children</label>
                            <input type="number" name="num_children" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Room Quantity</label>
                            <input type="number" name="room_quantity" id="room_quantity"
                                class="form-control custom-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Extra Bed</label>
                            <select name="extra_bed" id="extra_bed" class="form-control custom-input">
                                <?php echo $bedOptions; ?>
                            </select>
                        </div>

                        <input type="hidden" id="total_capacity" name="total_capacity">
                    </div>

                    <!-- Guest Details Section -->
                    <div class="section-header">
                        <i class="fas fa-users"></i> Guest Details
                    </div>
                    <div class="col-12 mb-4">
                        <div id="guestList" class="guest-list-container"></div>
                    </div>

                    <!-- Payment Section -->
                    <div class="section-header">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control custom-input" required>
                                <option value="gcash">GCash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cash">Cash</option>
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

                        <div class="col-md-6">
                            <label class="form-label">Payment</label>
                            <input type="number" id="payment_amount" name="payment_amount"
                                class="form-control custom-input" min="0" step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Change</label>
                            <input type="text" id="change_amount" name="change_amount" class="form-control custom-input"
                                readonly>
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

    function addToCart(name, price, room_number, floor, image, capacity, room_type_id) {
        if (!availabilityChecked) {
            alert("Please check room availability first.");
            return;
        }

        const exists = cartItems.some(item => item.room_type_id === room_type_id);
        if (exists) {
            alert("This room type is already in your booking list.");
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
            room_type_id
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
        if (cartItems.length === 0) {
            alert("Your booking list is empty.");
            return;
        }

        const totalCapacity = cartItems.reduce((sum, item) => sum + item.capacity, 0);
        document.getElementById("total_capacity").value = totalCapacity;

        const checkInInput = document.querySelector("input[name='check_in']");
        const checkOutInput = document.querySelector("input[name='check_out']");
        document.getElementById("modal_check_in").value = checkInInput?.value || "";
        document.getElementById("modal_check_out").value = checkOutInput?.value || "";

        document.getElementById("room_quantity").value = cartItems.length;

        const nights = getNumberOfNights();

        // Rooms total
        let roomsTotal = cartItems.reduce((sum, item) => sum + Number(item.price), 0) * nights;

        // Extra bed
        const extraBedSelect = document.getElementById("extra_bed");
        const extraBedPrice = Number(extraBedSelect?.selectedOptions[0]?.dataset.price) || 0;
        let extraBedTotal = extraBedPrice * nights;

        // Subtotal
        let subtotal = roomsTotal + extraBedTotal;

        // Discount
        let discountPercent = 0;
        document.querySelectorAll(".discount-select").forEach(select => {
            const percent = Number(select.selectedOptions[0]?.dataset.percent) || 0;
            if (percent > 0 && discountPercent === 0) discountPercent = percent; // apply only once
        });

        const discountAmount = subtotal * (discountPercent / 100);
        const totalAmount = subtotal - discountAmount;

        document.getElementById("total_discount_percent").value = discountPercent + "%";
        document.getElementById("total_discount_amount").value = "₱" + discountAmount.toLocaleString();
        document.getElementById("total_amount").value = totalAmount.toLocaleString();

        const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        checkoutModal.show();
    }



    function updateGuestInputs() {
        const adults = parseInt(document.querySelector("input[name='num_adults']").value) || 0;
        const children = parseInt(document.querySelector("input[name='num_children']").value) || 0;
        const totalGuests = adults + children;

        document.querySelector("input[name='number_of_guests']").value = totalGuests;

        const guestList = document.getElementById("guestList");
        guestList.innerHTML = ""; // clear first
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

        <div class="col-md-3">
            <label class="form-label">Proof Image</label>
            <input type="file" name="proof${number}" class="form-control" required>
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

    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("discount-select")) {
            updateTotalDiscount();
        }
    });

    function updateTotalDiscount() {
        let totalAmount = cartItems.reduce((sum, item) => sum + item.price, 0);

        const extraBedSelect = document.getElementById("extra_bed");
        const extraBedPrice = parseFloat(extraBedSelect?.selectedOptions[0]?.dataset.price) || 0;
        totalAmount += extraBedPrice;

        let discountApplied = false;
        let discountPercent = 0;

        document.querySelectorAll(".discount-select").forEach(select => {
            const percent = parseInt(select.selectedOptions[0]?.dataset.percent) || 0;
            if (percent > 0 && !discountApplied) {
                discountPercent = percent;
                discountApplied = true;
            }
        });

        const totalDiscountAmount = totalAmount * (discountPercent / 100);

        document.getElementById("total_discount_percent").value = discountPercent + "%";
        document.getElementById("total_discount_amount").value = "₱" + totalDiscountAmount.toLocaleString();
        document.getElementById("total_amount").value = (totalAmount - totalDiscountAmount).toLocaleString();
    }


    document.getElementById("extra_bed").addEventListener("change", function () {
        updateTotalAmount();
    });

    function getNumberOfNights() {
        const checkIn = new Date(document.getElementById("modal_check_in").value);
        const checkOut = new Date(document.getElementById("modal_check_out").value);
        if (checkIn && checkOut && checkOut > checkIn) {
            return (checkOut - checkIn) / (1000 * 60 * 60 * 24);
        }
        return 1;
    }

    function updateTotalAmount() {
        const nights = getNumberOfNights();

        let roomsTotal = cartItems.reduce((sum, item) => sum + Number(item.price), 0) * nights;

        const extraBedSelect = document.getElementById("extra_bed");
        const extraBedPrice = Number(extraBedSelect?.selectedOptions[0]?.dataset.price) || 0;
        let extraBedTotal = extraBedPrice * nights;

        let subtotal = roomsTotal + extraBedTotal;

        let discountPercent = 0;
        document.querySelectorAll(".discount-select").forEach(select => {
            const percent = Number(select.selectedOptions[0]?.dataset.percent) || 0;
            if (percent > 0 && discountPercent === 0) discountPercent = percent; // only once
        });

        const discountAmount = subtotal * (discountPercent / 100);
        const totalAmount = subtotal - discountAmount;

        document.getElementById("total_discount_percent").value = discountPercent + "%";
        document.getElementById("total_discount_amount").value = "₱" + discountAmount.toLocaleString();
        document.getElementById("total_amount").value = totalAmount.toLocaleString();
    }

    document.getElementById("extra_bed").addEventListener("change", updateTotalAmount);
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("discount-select")) {
            updateTotalAmount();
        }
    });

    document.getElementById("modal_check_in").addEventListener("change", updateTotalAmount);
    document.getElementById("modal_check_out").addEventListener("change", updateTotalAmount);


    function submitCheckout() {
        if (cartItems.length === 0) {
            alert("Your booking list is empty.");
            return;
        }

        document.getElementById("cart_items").value = JSON.stringify(cartItems);

        const totalGuests = parseInt(document.querySelector("input[name='num_adults']").value) +
            parseInt(document.querySelector("input[name='num_children']").value);
        if (totalGuests > parseInt(document.getElementById("total_capacity").value)) {
            alert("Guest count exceeds room capacity!");
            return;
        }

        const totalAmount = parseFloat(document.getElementById("total_amount").value.replace(/,/g, '')) || 0;
        const payment = parseFloat(document.getElementById("payment_amount").value) || 0;

        if (payment < totalAmount) {
            alert("Payment must be equal to or greater than the total amount.");
            return;
        }

        document.getElementById("checkoutForm").submit();
    }


    document.getElementById("payment_amount").addEventListener("input", function () {
        const totalAmount = parseFloat(
            document.getElementById("total_amount").value.replace(/,/g, '')
        ) || 0;

        const payment = parseFloat(this.value) || 0;
        const change = payment >= totalAmount ? payment - totalAmount : 0;

        document.getElementById("change_amount").value = "₱" + change.toLocaleString();
    });

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


</script>