/**
 * Menu Fix Direct - Fixes the menu items display in the Advance Order modal
 * This script creates a container for menu items, displays items based on selected categories,
 * and handles cart functionality
 */

// Initialize the menu fix when the modal is shown
$(document).ready(function() {
    $(document).on('shown.bs.modal', '#advanceOrderModal', initMenuFix);
});

function initMenuFix() {
    console.log("Menu fix initialized");
    
    // Create menu items container if it doesn't exist
    createMenuItemsContainer();
    
    // Bind click handler to category buttons
    $('.category-btn').off('click').on('click', function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        var categoryId = $(this).data('category-id');
        displayMenuItemsForCategory(categoryId);
    });
    
    // Trigger click on first category button
    setTimeout(function() {
        $('.category-btn:first').click();
    }, 100);
}

function createMenuItemsContainer() {
    // Create menu items container if it doesn't exist
    if ($('#menuItemsContainer').length === 0) {
        console.log("Creating menu items container");
        var container = $('<div id="menuItemsContainer" class="row menu-items-container mt-3" style="margin: 10px 0; overflow-y: auto; max-height: 350px;"></div>');
        $('.menu-categories').after(container);
    }
}

function displayMenuItemsForCategory(categoryId) {
    console.log("Displaying menu items for category: " + categoryId);
    
    // Get the container
    var container = $('#menuItemsContainer');
    container.empty();
    
    // Filter menu items by category
    var items = menuItems.filter(function(item) {
        return item.category_id == categoryId;
    });
    
    // Display menu items
    if (items.length > 0) {
        var row = $('<div class="row w-100"></div>');
        container.append(row);
        
        items.forEach(function(item) {
            var itemHtml = createMenuItemHtml(item);
            row.append(itemHtml);
        });
        
        // Bind add to cart buttons
        $('.add-to-cart-btn').on('click', function() {
            var itemId = $(this).data('item-id');
            addToCart(itemId);
        });
    } else {
        container.html('<div class="col-12 text-center"><p>No menu items found for this category.</p></div>');
    }
}

function createMenuItemHtml(item) {
    // Default image path or use placeholder
    var imagePath = item.image_path || 'assets/img/default-food.jpg';
    
    return `
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-img-container" style="height: 140px; overflow: hidden; background-color: #f8f9fa; text-align: center;">
                    <img src="${imagePath}" class="card-img-top" alt="${item.name}" 
                         onerror="this.src='assets/img/default-food.jpg'; this.onerror=null;" 
                         style="height: 140px; object-fit: cover; max-width: 100%;">
                </div>
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">${item.name}</h5>
                    <p class="card-text" style="font-size: 13px; height: 40px; overflow: hidden;">${item.description}</p>
                    <p class="card-text text-primary font-weight-bold">₱${item.price}</p>
                    <button class="btn btn-sm btn-warning w-100 add-to-cart-btn" data-item-id="${item.id}">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `;
}

function getCart() {
    var cart = localStorage.getItem('advanceOrderCart');
    if (cart) {
        return JSON.parse(cart);
    }
    return [];
}

function addToCart(itemId) {
    console.log("Adding item to cart: " + itemId);
    
    var cart = getCart();
    var item = menuItems.find(function(item) {
        return item.id == itemId;
    });
    
    if (item) {
        // Check if item already exists in cart
        var existingItem = cart.find(function(cartItem) {
            return cartItem.id == itemId;
        });
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: 1
            });
        }
        
        // Save cart to localStorage
        localStorage.setItem('advanceOrderCart', JSON.stringify(cart));
        
        // Update cart display
        updateCartDisplay();
        
        // Show success message
        alert(item.name + ' added to cart');
    }
}

function updateCartDisplay() {
    var cart = getCart();
    var cartContainer = $('#cartItems');
    
    if (cartContainer.length === 0) {
        // Create cart container if it doesn't exist
        var cartHtml = `
            <div class="mt-4">
                <h5>Your Order</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total</th>
                            <th id="cartTotal">₱0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
        $('#menuItemsContainer').after(cartHtml);
        cartContainer = $('#cartItems');
    }
    
    cartContainer.empty();
    
    var total = 0;
    
    cart.forEach(function(item) {
        var itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        cartContainer.append(`
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>₱${item.price}</td>
                <td>₱${itemTotal}</td>
            </tr>
        `);
    });
    
    $('#cartTotal').html('₱' + total);
}

// Sample menu items data - these will display in the modal
var menuItems = [
    {
        id: 1,
        category_id: 1, // SMALL PLATES category
        name: "Hand-cut Potato Fries",
        description: "Crispy hand-cut potato fries served with ketchup",
        price: 150,
        image_path: "assets/img/menu/fries.jpg"
    },
    {
        id: 2,
        category_id: 1, // SMALL PLATES category
        name: "Mozzarella Stick",
        description: "Breaded mozzarella sticks served with marinara sauce",
        price: 220,
        image_path: "assets/img/menu/mozzarella.jpg"
    },
    {
        id: 3,
        category_id: 1, // SMALL PLATES category
        name: "Fried Chicken Wings",
        description: "Crispy fried chicken wings with your choice of sauce",
        price: 250,
        image_path: "assets/img/menu/wings.jpg"
    },
    {
        id: 4,
        category_id: 1, // SMALL PLATES category
        name: "Nachos Supreme",
        description: "Corn tortilla chips topped with cheese, jalapeños, and salsa",
        price: 220,
        image_path: "assets/img/menu/nachos.jpg"
    },
    {
        id: 5,
        category_id: 2, // SOUP & SALAD category
        name: "Caesar Salad",
        description: "Fresh romaine lettuce with croutons and Caesar dressing",
        price: 180,
        image_path: "assets/img/menu/caesar.jpg"
    },
    {
        id: 6,
        category_id: 2, // SOUP & SALAD category
        name: "Garden Salad",
        description: "Mixed greens with fresh vegetables and vinaigrette",
        price: 160,
        image_path: "assets/img/menu/garden-salad.jpg"
    },
    {
        id: 7,
        category_id: 2, // SOUP & SALAD category
        name: "Pumpkin Soup",
        description: "Creamy pumpkin soup with herbs and spices",
        price: 160,
        image_path: "assets/img/menu/pumpkin-soup.jpg"
    },
    {
        id: 8,
        category_id: 3, // PASTA category
        name: "Spaghetti Carbonara",
        description: "Spaghetti with creamy egg and bacon sauce",
        price: 270,
        image_path: "assets/img/menu/carbonara.jpg"
    },
    {
        id: 9,
        category_id: 3, // PASTA category
        name: "Penne Arrabiata",
        description: "Penne pasta with spicy tomato sauce",
        price: 250,
        image_path: "assets/img/menu/arrabiata.jpg"
    },
    {
        id: 10,
        category_id: 3, // PASTA category
        name: "Fettuccine Alfredo",
        description: "Fettuccine pasta in creamy parmesan sauce",
        price: 280,
        image_path: "assets/img/menu/fettuccine.jpg"
    },
    {
        id: 11,
        category_id: 4, // SANDWICHES category
        name: "Club Sandwich",
        description: "Triple-decker sandwich with chicken, bacon, and egg",
        price: 220,
        image_path: "assets/img/menu/club-sandwich.jpg"
    },
    {
        id: 12,
        category_id: 4, // SANDWICHES category
        name: "Chicken Sandwich",
        description: "Grilled chicken breast with lettuce and mayo on ciabatta",
        price: 190,
        image_path: "assets/img/menu/chicken-sandwich.jpg"
    },
    {
        id: 13,
        category_id: 4, // SANDWICHES category
        name: "Vegetable Sandwich",
        description: "Grilled vegetables with pesto on whole grain bread",
        price: 170,
        image_path: "assets/img/menu/vegetable-sandwich.jpg"
    },
    {
        id: 14,
        category_id: 5, // COFFEE & LATTE category
        name: "Espresso",
        description: "Single shot of espresso",
        price: 100,
        image_path: "assets/img/menu/espresso.jpg"
    },
    {
        id: 15,
        category_id: 5, // COFFEE & LATTE category
        name: "Cappuccino",
        description: "Espresso with steamed milk and foam",
        price: 140,
        image_path: "assets/img/menu/cappuccino.jpg"
    },
    {
        id: 16,
        category_id: 5, // COFFEE & LATTE category
        name: "Latte",
        description: "Espresso with steamed milk",
        price: 140,
        image_path: "assets/img/menu/latte.jpg"
    },
    {
        id: 17,
        category_id: 6, // TEA category
        name: "Green Tea",
        description: "Traditional green tea",
        price: 100,
        image_path: "assets/img/menu/green-tea.jpg"
    },
    {
        id: 18,
        category_id: 6, // TEA category
        name: "Earl Grey",
        description: "Black tea flavored with bergamot",
        price: 100,
        image_path: "assets/img/menu/earl-grey.jpg"
    },
    {
        id: 19,
        category_id: 7, // OTHER DRINKS category
        name: "Fresh Orange Juice",
        description: "Freshly squeezed orange juice",
        price: 120,
        image_path: "assets/img/menu/orange-juice.jpg"
    },
    {
        id: 20,
        category_id: 7, // OTHER DRINKS category
        name: "Iced Lemon Tea",
        description: "Refreshing tea with lemon",
        price: 110,
        image_path: "assets/img/menu/iced-tea.jpg"
    }
]; 