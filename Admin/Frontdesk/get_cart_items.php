<?php
session_start();

if (empty($_SESSION['cart'])): ?>
    <div class="empty-cart">
        <p>Your cart is empty</p>
    </div>
<?php else: ?>
    <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="cart-item">
            <div class="item-details">
                <h6><?php echo htmlspecialchars($item['room_type']); ?></h6>
                <p>₱<?php echo number_format($item['price'], 2); ?> per night</p>
            </div>
            <button class="remove-item" data-id="<?php echo $item['room_type_id']; ?>">
                <i class="fa fa-times"></i>
            </button>
        </div>
    <?php endforeach; ?>
    <div class="cart-footer">
        <a href="index.php?page=checkout" class="btn btn-primary btn-block">Proceed to Checkout</a>
    </div>
<?php endif; ?> 