<?php
session_start();
include 'includes/database.php';
include 'nav.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Casa Antonio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Your Cart</h2>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="roomss.php">Browse Rooms</a>
            </div>
        <?php else: ?>
            <form action="roompayment.php" method="POST">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($item['roomName']); ?></h5>
                                            <p class="text-muted mb-0">Room <?php echo htmlspecialchars($item['roomNumber']); ?></p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 me-3">₱<?php echo number_format($item['price'], 2); ?></h5>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromCart(<?php echo $index; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-body">
                                <h5>Booking Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customerName" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="customerName" name="name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="customerEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="customerEmail" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customerPhone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="customerPhone" name="contact_number" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="guestCount" class="form-label">Number of Guests</label>
                                            <input type="number" class="form-control" id="guestCount" name="number_of_guests" min="1" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="checkIn" class="form-label">Check-in Date</label>
                                            <input type="date" class="form-control" id="checkIn" name="check_in" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="checkOut" class="form-label">Check-out Date</label>
                                            <input type="date" class="form-control" id="checkOut" name="check_out" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Subtotal</span>
                                    <span>₱<?php 
                                        $total = array_sum(array_column($_SESSION['cart'], 'price'));
                                        echo number_format($total, 2);
                                    ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total</strong>
                                    <strong>₱<?php echo number_format($total, 2); ?></strong>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Proceed to Payment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeFromCart(index) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ index: index })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Set minimum dates for check-in and check-out
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkIn').min = today;
            document.getElementById('checkOut').min = today;
            
            // Update check-out min date when check-in is selected
            document.getElementById('checkIn').addEventListener('change', function() {
                document.getElementById('checkOut').min = this.value;
            });
        });
    </script>
</body>
</html>
