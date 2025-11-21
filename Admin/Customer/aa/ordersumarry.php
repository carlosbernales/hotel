<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order-details'])) {
    // Decode the JSON order details
    $orderDetails = json_decode($_POST['order-details'], true);
} else {
    echo "<p>No order details received.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-summary {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            margin-top: 50px;
            border: 1px solid gold;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1050;
        }
        hr {
            color: black;
        }
        .order-item {
            margin-bottom: 20px;
        }
        .addon-list {
            margin-top: 5px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php include ('nav.php'); ?>
<!-- Order Summary -->
<div class="container">
    <div class="order-summary">
        <h2 class="text-center">Order Summary</h2>
        
        <?php if (!empty($orderDetails)) : ?>
            <ul class="list-group" id="order-list">
                <?php foreach ($orderDetails as $index => $item) : ?>
                    <li class="list-group-item order-item" data-index="<?php echo $index; ?>">
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>
                        <p>Price: ₱ <?php echo number_format($item['price'], 2); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['qty']); ?></p>
                        <?php if (!empty($item['addons'])) : ?>
                            <div class="addon-list">
                                <strong>Add-ons:</strong>
                                <ul>
                                    <?php foreach ($item['addons'] as $addon) : ?>
                                        <li>
                                            <?php echo htmlspecialchars($addon['name']); ?> 
                                            (+₱ <?php echo number_format($addon['price'], 2); ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-item" data-index="<?php echo $index; ?>">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <hr>
            <p><strong>Total Items:</strong> <span id="total-items">
                <?php echo array_sum(array_column($orderDetails, 'qty')); ?>
            </span></p>
            <p><strong>Total Amount:</strong> ₱ <span id="total-amount">
                <?php 
                    $totalAmount = 0;
                    foreach ($orderDetails as $item) {
                        $itemTotal = $item['price'] * $item['qty'];
                        $addonsTotal = array_sum(array_column($item['addons'], 'price'));
                        $totalAmount += ($itemTotal + $addonsTotal);
                    }
                    echo number_format($totalAmount, 2); 
                ?>
            </span></p>

            <div class="mt-4">
                <label for="payment-method" class="form-label"><strong>Select Payment Method:</strong></label>
                <select class="form-select" id="payment-method">
                    <option value="none">Select Payment</option>
                    <option value="gcash">GCash</option>
                    <option value="maya">Maya</option>
                </select>
            </div>

            <div class="mt-4 text-center">
                <button id="back" class="btn btn-warning">Back to Menu</button>
                <button id="submit-order" class="btn btn-success">Submit Order</button>
            </div>

        <?php else : ?>
            <p>No items in the order.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Spinner -->
<div id="spinner" class="spinner-overlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Processing...</span>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let orderDetails = <?php echo json_encode($orderDetails); ?>;

    function updateUI() {
        const orderList = document.getElementById("order-list");
        const totalItems = document.getElementById("total-items");
        const totalAmount = document.getElementById("total-amount");

        orderList.innerHTML = "";
        let totalQty = 0;
        let totalPrice = 0;

        orderDetails.forEach((item, index) => {
            totalQty += item.qty;
            const itemTotal = item.price * item.qty + item.addons.reduce((sum, addon) => sum + addon.price, 0);
            totalPrice += itemTotal;

            const listItem = document.createElement("li");
            listItem.className = "list-group-item order-item";
            listItem.setAttribute("data-index", index);
            listItem.innerHTML = ` 
                <h5>${item.name}</h5>
                <p>Category: ${item.category}</p>
                <p>Price: ₱ ${item.price.toFixed(2)}</p>
                <p>Quantity: ${item.qty}</p>
                ${item.addons.length > 0 ? `
                    <div class="addon-list">
                        <strong>Add-ons:</strong>
                        <ul>
                            ${item.addons.map(addon => `<li>${addon.name} (+₱ ${addon.price.toFixed(2)})</li>`).join("")}
                        </ul>
                    </div>
                ` : ""}
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-item" data-index="${index}">Remove</button>
            `;
            orderList.appendChild(listItem);
        });

        totalItems.textContent = totalQty;
        totalAmount.textContent = totalPrice.toFixed(2);
        attachRemoveListeners();
    }

    function attachRemoveListeners() {
        document.querySelectorAll(".remove-item").forEach(button => {
            button.addEventListener("click", function () {
                const index = parseInt(this.getAttribute("data-index"));
                if (!isNaN(index)) {
                    orderDetails.splice(index, 1); 
                    updateUI();
                }
            });
        });
    }

    document.getElementById("submit-order").addEventListener("click", function () {
        const paymentMethod = document.getElementById("payment-method").value;

        if (paymentMethod === "none") {
            alert("Please select a payment method.");
            return;
        }

        const orderData = {
            user_id: 123, // Example user ID
            payment_method: paymentMethod,
            order_details: orderDetails,
        };

        const spinner = document.getElementById("spinner");
        spinner.style.display = "flex";

        fetch("submitorder.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(orderData),
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = "none"; 
            if (data.success) {
                alert("Order submitted successfully!");
                orderDetails = [];
                updateUI();
                window.location.href = "cafes.php";
            } else {
                alert("Error submitting order: " + data.message);
            }
        })
        .catch(error => {
            spinner.style.display = "none";
            alert("An error occurred while submitting the order.");
            console.error("Error:", error);
        });
    });

    updateUI(); // Initialize the UI
</script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
