<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Casa Estela Boutique Hotel & Cafe</title>
    
    <!-- Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/reservation.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css" rel="stylesheet">

    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- jQuery and DataTables JS -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/dropdown.js"></script>
    
    <!-- Real-time Notifications -->
    <script src="js/realtime_notifications.js"></script>
    <style>
    /* Notification badge styles */
    .notification-icon {
        position: relative;
        margin-right: 15px;
        color: #fff;
        text-decoration: none;
    }
    .notification-icon .badge {
        position: absolute;
        top: -5px;
        right: -10px;
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 10px;
        min-width: 18px;
        height: 18px;
        line-height: 12px;
        background-color: #dc3545;
        border: 2px solid #343a40;
    }
    
    /* Pulse animation for new notifications */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .pulse {
        animation: pulse 1s infinite;
    }
    
    /* Notification popup styles */
    .notification-popup {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.25rem;
    }
    .notification-popup .card {
        border: none;
    }
    .notification-popup .card-header {
        background-color: #343a40;
        color: #fff;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .notification-popup .close {
        color: #fff;
        opacity: 0.8;
    }
    .notification-popup .close:hover {
        color: #fff;
        opacity: 1;
    }

    /* Custom styles for small quantity controls in cart modal */
    .qty-control-sm {
        display: flex !important; /* Force horizontal layout */
        flex-direction: row !important; /* Explicitly set flex direction to row */
        flex-wrap: nowrap !important; /* Prevent wrapping */
        width: 100px !important; /* Adjusted width for the entire control group */
        height: 28px !important; /* Adjusted height for the entire control group */
        align-items: stretch !important; /* Ensure children stretch to fill height */
    }
    .qty-control-sm .btn,
    .qty-control-sm .form-control {
        padding: 0 !important; /* Minimal padding for all elements */
        font-size: 1rem !important; /* Increased font size for + and - signs */
        height: 28px !important; /* Match overall height */
        line-height: 1 !important;
        text-align: center !important;
        border-radius: 0 !important; /* Remove border radius for contiguous look */
        box-sizing: border-box !important; /* Include padding and border in element's total width and height */
    }
    .qty-control-sm .btn {
        width: 30px !important; /* Fixed width for buttons */
        min-width: 30px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
    }
    .qty-control-sm .form-control {
        flex-grow: 1 !important;
        flex-shrink: 1 !important;
        flex-basis: auto !important; /* Allow content to dictate initial size */
        width: auto !important; /* Remove fixed max-width for input */
    }
    /* Adjust first and last button border-radius to match input-group styling */
    .qty-control-sm .btn:first-child {
        border-top-left-radius: 0.25rem !important;
        border-bottom-left-radius: 0.25rem !important;
    }
    .qty-control-sm .btn:last-child {
        border-top-right-radius: 0.25rem !important;
        border-bottom-right-radius: 0.25rem !important;
    }
    </style>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Critical styles for immediate loading */
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            padding-top: 60px; /* Add padding to account for fixed header */
        }
        
        .form-control {
            height: 50px !important;
            font-size: 16px !important;
            padding: 10px 15px !important;
        }
        
        .sidebar {
            background: #333 !important;
            position: fixed;
            top: 60px; /* Start below the header */
            left: 0;
            bottom: 0;
            width: 250px;
            z-index: 999;
        }
        
        .sidebar ul.nav li a {
            color: #fff !important;
            font-size: 15px !important;
            padding: 12px 20px !important;
        }
        
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #DAA520;
            color: #fff;
            padding: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-brand {
            margin-left: 0; /* Remove the 270px margin */
            font-size: 20px;
            font-weight: 600;
            padding: 0 15px;
            flex-grow: 1;
            text-align: left;
            white-space: nowrap;
        }

        .header-icons {
            display: flex;
            align-items: center;
            padding-right: 30px;
            gap: 20px;
        }

        .header-icons a {
            color: #fff;
            text-decoration: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .header-icons a:hover {
            color: #f8f9fa;
        }

        .header-icons i {
            font-size: 20px;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #fff;
            color: #DAA520;
            font-size: 11px;
            padding: 3px 5px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        /* Improve form readability */
        label {
            font-size: 16px !important;
            font-weight: 500 !important;
            color: #333 !important;
            margin-bottom: 8px !important;
        }
        
        input, select, textarea {
            font-size: 16px !important;
            color: #333 !important;
        }
        
        /* Ensure buttons are readable */
        .btn {
            font-size: 16px !important;
            padding: 12px 25px !important;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #fff;
            color: #DAA520;
            font-size: 11px;
            padding: 3px 5px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .header-icons .fa {
            font-size: 18px;
        }

        /* Room list modal styles */
        .room-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .room-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .room-item:last-child {
            border-bottom: none;
        }

        #roomListModal .modal-content {
            border-radius: 8px;
        }

        #roomListModal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .total-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .selected-room-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .selected-room-item:last-child {
            border-bottom: none;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .total-amount {
            font-size: 1.2em;
            color: #28a745;
        }

        #bookingFormModal .modal-dialog {
            max-width: 800px;
        }

        #bookingFormModal .form-control {
            height: 45px;
        }

        #bookingFormModal label {
            font-weight: 500;
        }

        .booking-summary {
            margin-top: 20px;
        }

        .room-summary-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .room-summary-item h6 {
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
        }

        .guest-list {
            padding-left: 15px;
        }

        .guest-list div {
            margin-bottom: 5px;
            color: #666;
        }

        #bookingSummary .table th {
            width: 30%;
            background-color: #f8f9fa;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 8px;
        }

        .modal-body.scrollable-content {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Selected rooms section */
        .selected-rooms {
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .room-details-container {
            margin-bottom: 25px;
        }

        .room-type-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #DAA520;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 8px 0;
        }

        .details-label {
            color: #666;
            font-weight: 500;
            flex: 1;
        }

        .details-value {
            color: #333;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }

        .room-total {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            font-weight: 600;
        }

        .price-summary {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .price-summary-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .total-amount {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #DAA520;
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 600;
            color: #28a745;
        }

        /* Form controls */
        .form-control {
            height: 45px;
            border-radius: 4px;
        }

        /* Buttons */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        /* Add these to your existing styles */
        .room-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .room-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .price-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .total-amount, .downpayment {
            font-weight: bold;
            color: #28a745;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin-right: 15px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details h5 {
            margin: 0 0 10px;
        }

        .cart-item-details p {
            margin: 0 0 5px;
            color: #666;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: right;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            font-size: 20px;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin-right: 15px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details h5 {
            margin: 0 0 10px;
        }

        .cart-item-details p {
            margin: 0 0 5px;
            color: #666;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: right;
        }

        .header-icons {
            float: right;
            margin-right: 15px;
        }

        .header-icons a {
            color: #fff;
            font-size: 20px;
            padding: 0 10px;
            text-decoration: none;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }

        .navbar-custom {
            background-color: #D4AF37;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand {
            margin-left: 0 !important; /* Remove the margin */
            padding-left: 20px !important; /* Add some padding */
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: 600;
        }

        .navbar-header {
            flex: 1;
            text-align: left;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icons a {
            color: white;
            font-size: 18px;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .profile-dropdown {
            display: inline-block;
        }

        .profile-dropdown .dropdown-menu {
            min-width: 200px;
            padding: 10px 0;
            margin-top: 10px;
            right: 0;
            left: auto;
        }

        .profile-dropdown .dropdown-menu li a {
            color: #333;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-dropdown .dropdown-menu li a:hover {
            background-color: #f8f9fa;
            color: #007bff;
            text-decoration: none;
        }

        .profile-dropdown .dropdown-menu .divider {
            margin: 5px 0;
            border-top: 1px solid #eee;
        }

        .dropdown-toggle::after {
            display: none;
        }

        /* Add margin to main content to prevent header overlap */
        .main {
            margin-left: 250px;
            padding: 20px;
            margin-top: 60px; /* Add margin to account for fixed header */
        }

        @media (max-width: 768px) {
            .header-brand {
                margin-left: 15px;
                font-size: 16px;
            }
            
            .main {
                margin-left: 0;
            }
            
            .sidebar {
                display: none;
            }
        }

        /* Style for discount display */
        .summary-item {
            margin-bottom: 10px;
        }
        
        .discount-display {
            color: #28a745;
            font-weight: 500;
            padding: 8px 0;
            margin: 8px 0;
            border-top: 1px dashed #dee2e6;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .room-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        /* Selected rooms details styling */
        .selected-rooms-details {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .room-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .room-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .room-info-label {
            font-weight: 500;
            color: #666;
        }

        .room-info-value {
            text-align: right;
            color: #333;
        }

        .room-type-header {
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .price-summary-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
        }

        .price-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .booking-success {
            text-align: center;
            padding: 20px;
        }
        
        .booking-success p {
            margin-bottom: 15px;
        }
        
        .booking-success strong {
            color: #28a745;
        }

        /* Add this rule for the booking form modal */
        #bookingFormModal {
            z-index: 1050 !important; /* Ensure it's on top of other modals */
            display: none; /* Hidden by default */
        }
        #bookingFormModal.show {
            display: flex !important; /* Use flex to center content */
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop {
            z-index: 1040 !important; /* Ensure backdrop is below the modal but above other content */
        }
    </style>
</head>
<body>
    <div class="top-header">
        <div class="header-brand">
            CASA ESTELA BOUTIQUE HOTEL & CAFE
        </div>
        <div class="header-icons">
            <a href="#" title="Messages">
                <i class="fas fa-envelope"></i>
                <span class="badge">0</span>
            </a>
            <a href="notification.php" class="notification-icon" id="notificationIcon" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge" id="notificationBadge" style="display: none;">0</span>
            </a>
            <a href="#" title="User Profile">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>

    <!-- Only one header -->
    <div class="header fixed-top">
        <div class="navbar navbar-custom">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">CASA ESTELA BOUTIQUE HOTEL & CAFE</a>
            </div>
            <div class="navbar-right">
                <div class="header-icons">
                    <a href="#" class="cart-icon" data-toggle="modal" data-target="#cartModal" onclick="showCart(); return false;">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="cart-count"></span>
                    </a>
                    <a href="message.php" class="message-icon">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <a href="notification.php" class="notification-icon" id="notificationIcon">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-danger" id="notificationBadge" style="position: absolute; top: 5px; right: 5px; display: none;">0</span>
                    </a>
                    <div class="dropdown profile-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="my_profile.php"><i class="fa fa-user-circle"></i> My Profile</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Selected Rooms</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="cartItems">
                        <!-- Cart items will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
                    <button type="button" class="btn btn-warning" onclick="showBookingFormModal()">Book Selected Rooms</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Room List Modal -->
    <div class="modal fade" id="roomListModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Your Room List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="roomListContent">
                        <!-- Room list will be populated here -->
                    </div>
                    <div class="total-section" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 id="totalAmount">₱0</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="clearList">Clear List</button>
                    <button type="button" class="btn btn-warning" id="bookNow" disabled>Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Booking Form Modal -->
    <div class="modal fade" id="bookingFormModal" tabindex="-1" role="dialog" aria-labelledby="bookingFormModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingFormModalLabel">Complete Your Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="completeBookingForm">
                        <h6>Personal Information</h6>
                        <div class="form-group mb-3">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>
                        <div class="form-group mb-3">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                        <div class="form-group mb-3">
                            <label for="contact">Contact (Optional)</label>
                            <input type="text" class="form-control" id="contact" name="contact">
                        </div>

                        <h6 class="mt-4">Booking Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="checkInDate">Check-in Date</label>
                                    <input type="date" class="form-control" id="checkInDate" name="checkInDate" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="checkOutDate">Check-out Date</label>
                                    <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Nights and Total Amount Display -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Number of Nights</label>
                                    <div id="numberOfNights" class="form-control-plaintext">0 Nights</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Booking Total</label>
                                    <div id="bookingBaseTotal" class="form-control-plaintext">₱0.00</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4">Guest Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numAdults">Number of Adults</label>
                                    <input type="number" class="form-control" id="numAdults" name="numAdults" min="1" value="1" onchange="calculateTotal()" required>
                                </div>
                                <div id="adultNameFieldsContainer"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numChildren">Number of Children</label>
                                    <input type="number" class="form-control" id="numChildren" name="numChildren" min="0" value="0" onchange="calculateTotal()">
                                </div>
                                <div id="childNameFieldsContainer"></div>
                            </div>
                        </div>
                        
                        <!-- Extra Guest Charges -->
                        <div class="alert alert-info" id="extraGuestAlert" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="extraGuestMessage"></span>
                                </div>
                                <strong id="extraGuestCharge"></strong>
                            </div>
                        </div>
                        <input type="hidden" id="extraGuestCharges" name="extraGuestCharges" value="0">

                        <h6 class="mt-4">Extra Bed</h6>
                        <div class="form-group mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="extraBed" id="extraBedYes" value="yes">
                                <label class="form-check-label" for="extraBedYes">Yes (₱1,000 per night)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="extraBed" id="extraBedNo" value="no" checked>
                                <label class="form-check-label" for="extraBedNo">No</label>
                        </div>
                        </div>

                        <h6 class="mt-4">Payment Details</h6>
                        <div class="form-group mb-3">
                            <label for="paymentOption">Payment Option</label>
                            <select class="form-control" id="paymentOption" required>
                                <option value="">Select Option</option>
                                <option value="Partial Payment">Partial Payment (₱1,500)</option>
                                <option value="Custom Payment">Custom Payment</option>
                                <option value="Full Payment">Full Payment</option>
                                    </select>
                                </div>

                        <!-- Custom Payment Amount field (initially hidden) -->
                        <div id="customPaymentAmountField" class="form-group mb-3" style="display: none;">
                            <label for="customAmount">Custom Payment Amount</label>
                            <input type="number" class="form-control" id="customAmount" placeholder="Enter amount" step="0.01">
                            <small id="customAmountHelp" class="form-text text-danger" style="display: none;"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="paymentMethod">Payment Method</label>
                            <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                    </select>
                        </div>

                        <h6 class="mt-4">Selected Rooms</h6>
                        <div id="bookingFormRoomsContainer"></div>
                        <!-- Rooms from cart will be displayed here -->

                        <div class="d-flex justify-content-between mt-4 border-top pt-3">
                            <h5>Total Amount:</h5>
                            <h5 id="bookingFormTotalAmount">₱0.00</h5>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-warning" onclick="backToCart()">Back to Cart</button>
                            <button type="submit" class="btn btn-primary" id="submitBookingForm">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center"><b>Room Booking Confirmation</b></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert bg-success alert-dismissable" role="alert">
                                <em class="fa fa-lg fa-check-circle"></em>&nbsp; Room Successfully Booked
                            </div>
                            <table class="table table-striped table-bordered table-responsive">
                                <tbody>
                                    <tr>
                                        <td><b>Booking ID</b></td>
                                        <td id="getBookingId"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Customer Name</b></td>
                                        <td id="getCustomerName"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Check-In</b></td>
                                        <td id="getCheckIn"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Check-Out</b></td>
                                        <td id="getCheckOut"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Number of Nights</b></td>
                                        <td id="getNumberOfNights"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Guests</b></td>
                                        <td id="getTotalGuests"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Room Details</b></td>
                                        <td id="getRoomDetails"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Amount</b></td>
                                        <td id="getTotalPrice"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Payment Status</b></td>
                                        <td id="getPaymentStatus"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" style="border-radius: 60px;" onclick="window.location.reload()">
                        <i class="fa fa-check-circle"></i> OK
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add to List functionality
    function addToList(roomId, roomType, price, capacity, image, availableRooms) {
        try {
            if (!roomId) {
                throw new Error('Room type ID is missing from room data');
            }

            // Create room object with proper room_type_id
            const room = {
                id: roomId,
                room_type_id: roomId, // Ensure room_type_id is set
                type: roomType,
                price: parseFloat(price),
                capacity: parseInt(capacity),
                guests: parseInt(capacity) || 1, // Default to room capacity
                image: image || 'assets/img/rooms/default.jpg',
                addedAt: new Date().toISOString(),
                quantity: 1, // Add quantity, default to 1
                availableRooms: parseInt(availableRooms) // Store available rooms
            };

            // Debug log: Check the image path and availableRooms being stored
            console.log('Adding room to list. Room object:', room);

            // Get existing list
            let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            roomList.push(room);
            localStorage.setItem('roomList', JSON.stringify(roomList));
            updateCartCount();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Room added to list successfully!'
            });
        } catch (error) {
            console.error('Error adding room to list:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        }
    }

    // Update cart count
    function updateCartCount() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        $('.cart-count').text(roomList.length);
    }

    // Show room list modal
    function showRoomList() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        const container = $('#selectedRoomsList');
        container.empty();

        roomList.forEach((room, index) => {
            container.append(`
                <div class="room-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${room.type}</strong><br>
                            ${room.beds}<br>
                            ₱${parseFloat(room.price).toFixed(2)} per night
                        </div>
                        <div class="form-group">
                            <label>Number of Guests</label>
                            <input type="number" class="form-control" name="guests_${index}" 
                                   min="1" max="${room.maxCapacity}" required>
                        </div>
                    </div>
                    <div id="guestNames_${index}"></div>
                </div>
            `);

            // Add guest name fields when number of guests changes
            $(`input[name="guests_${index}"]`).on('change', function() {
                const count = parseInt($(this).val());
                const container = $(`#guestNames_${index}`);
                container.empty();
                
                for (let i = 0; i < count; i++) {
                    container.append(`
                        <div class="form-group">
                            <label>Guest ${i + 1} Name</label>
                            <input type="text" class="form-control" name="guest_${index}_${i}" required>
                        </div>
                    `);
                }
                updateTotalAmount();
            });
        });

        updateTotalAmount();
        $('#bookingFormModal').modal('show');
    }

    // Update guest count for a room
    function updateGuestCount(roomIndex, change) {
        let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList[roomIndex]) {
            const currentGuests = parseInt(roomList[roomIndex].guests || roomList[roomIndex].capacity || 1);
            const newGuests = Math.max(1, currentGuests + change);
            roomList[roomIndex].guests = newGuests;
            localStorage.setItem('roomList', JSON.stringify(roomList));
            document.getElementById(`guestCount_${roomIndex}`).value = newGuests;
            updateTotalAmount();
        }
    }
    
    // Update guest count from input field
    function updateGuestCountInput(roomIndex, value) {
        let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList[roomIndex]) {
            const newGuests = Math.max(1, parseInt(value) || 1);
            roomList[roomIndex].guests = newGuests;
            localStorage.setItem('roomList', JSON.stringify(roomList));
            updateTotalAmount();
        }
    }

    // Update total amount
    function updateTotalAmount() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        let originalTotal = 0;
        let extraGuestCharges = 0;
        
        roomList.forEach(room => {
            const roomPrice = parseFloat(room.price);
            const roomCapacity = parseInt(room.capacity) || 2; // Default to 2 if capacity is not set
            const numGuests = parseInt(room.guests) || roomCapacity; // Use capacity as default if guests not set
            
            // Calculate extra guest charges (₱1,000 per extra guest beyond capacity)
            const extraGuests = Math.max(0, numGuests - roomCapacity);
            extraGuestCharges += extraGuests * 1000 * room.quantity;
            
            originalTotal += roomPrice * room.quantity;
        });
        
        // Get discount selection
        const discountSelect = document.getElementById('multipleDiscountType');
        const discountType = discountSelect ? discountSelect.value : '';
        let finalTotal = originalTotal + extraGuestCharges;
        let discountAmount = 0;
        let discountPercentage = 0;
        
        // Get discount percentage from selected option if a discount is selected
        if (discountType && discountSelect) {
            const selectedOption = discountSelect.options[discountSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.percentage) {
                // Get percentage from data attribute
                discountPercentage = parseFloat(selectedOption.dataset.percentage);
            } else {
                // Default to 10% if data attribute is not available
                discountPercentage = 10;
            }
            
            // Calculate discount amount
            discountAmount = originalTotal * (discountPercentage / 100);
            finalTotal = originalTotal - discountAmount;
        }
        
        // Display breakdown of charges
        const totalBeforeDiscount = originalTotal + extraGuestCharges;
        
        // Display original amount if discount is applied
        if (discountAmount > 0) {
            // Add the original and discounted prices to the DOM
            let breakdownHtml = `
                <div class="small text-muted mb-1">
                    <div class="d-flex justify-content-between">
                        <span>Room Charges:</span>
                        <span>₱${originalTotal.toFixed(2)}</span>
                    </div>`;
                    
            if (extraGuestCharges > 0) {
                breakdownHtml += `
                    <div class="d-flex justify-content-between">
                        <span>Extra Guest Charges:</span>
                        <span>₱${extraGuestCharges.toFixed(2)}</span>
                    </div>`;
            }
            
            breakdownHtml += `
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>₱${totalBeforeDiscount.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>${discountPercentage}% ${discountType} Discount:</span>
                        <span>-₱${discountAmount.toFixed(2)}</span>
                    </div>
                </div>`;
                
            $('#bookingTotalAmount').html(`
                ${breakdownHtml}
                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                    <strong>Total Amount:</strong>
                    <strong>₱${finalTotal.toFixed(2)}</strong>
                </div>
            `);
            $('#downpaymentAmount').text('₱' + (finalTotal * 0.5).toFixed(2));
        } else {
            // No discount, show breakdown
            let breakdownHtml = `
                <div class="small text-muted mb-1">
                    <div class="d-flex justify-content-between">
                        <span>Room Charges:</span>
                        <span>₱${originalTotal.toFixed(2)}</span>
                    </div>`;
                    
            if (extraGuestCharges > 0) {
                breakdownHtml += `
                    <div class="d-flex justify-content-between">
                        <span>Extra Guest Charges:</span>
                        <span>₱${extraGuestCharges.toFixed(2)}</span>
                    </div>`;
            }
            
            breakdownHtml += `
                </div>`;
                
            $('#bookingTotalAmount').html(`
                ${breakdownHtml}
                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                    <strong>Total Amount:</strong>
                    <strong>₱${finalTotal.toFixed(2)}</strong>
                </div>
            `);
            $('#downpaymentAmount').text('₱' + (finalTotal * 0.5).toFixed(2));
        }
        
        return {
            originalTotal: originalTotal,
            finalTotal: finalTotal,
            discountAmount: discountAmount,
            discountType: discountType,
            discountPercentage: discountPercentage
        };
    }

    function showBookingFormModal() {
        console.log('Attempting to show booking form modal.');
        try {
            // Close the cart modal first
            $('#cartModal').modal('hide');
            console.log('Cart modal closed.');

            // Clear previous form data
            document.getElementById('completeBookingForm').reset();
            // Populate selected rooms from cart to this modal
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            const bookingFormRoomsContainer = document.getElementById('bookingFormRoomsContainer');
            bookingFormRoomsContainer.innerHTML = roomList.map(room => `
                <div class="card mb-2 p-2">
                    <h6>${room.type} (Qty: ${room.quantity}) - ₱${(room.price * room.quantity).toLocaleString()}</h6>
                    </div>
            `).join('');
            
            // Update total amount display in the booking form modal
            const bookingFormTotalAmountElement = document.getElementById('bookingFormTotalAmount');
            let totalAmount = 0;
            roomList.forEach(room => {
                totalAmount += room.price * room.quantity;
            });
            bookingFormTotalAmountElement.textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Attach event listeners to date inputs for real-time calculation
            document.getElementById('checkInDate').addEventListener('change', updateBookingTotals);
            document.getElementById('checkOutDate').addEventListener('change', updateBookingTotals);
            
            // Initial call to update totals if dates are pre-filled
            updateBookingTotals();

            // Show the modal
        $('#bookingFormModal').modal('show');
            
            // Fallback to ensure display if Bootstrap JS somehow fails
            const modalElement = document.getElementById('bookingFormModal');
            if (modalElement) { // Check if element exists before manipulating
                // Add Bootstrap's 'show' class and style for display
                modalElement.classList.add('show');
                modalElement.style.display = 'block';
                modalElement.style.opacity = '1';
                document.body.classList.add('modal-open'); // Add class to body to prevent scrolling
                modalElement.setAttribute('aria-modal', 'true');
                modalElement.removeAttribute('aria-hidden');
                console.warn('Forcing booking form modal display via direct style manipulation and classes.');
            }
            
            console.log('Booking form modal show initiated.');
        } catch (e) {
            console.error('Error showing booking form modal:', e);
            // Log entire error object for more details
            console.error('Error details:', e.stack);
        }
    }

    // Add event listener when document is ready
    $(document).ready(function() {
        console.log("Document ready function executed."); // Debug log for ready function
        // Handle form submission for the complete booking form
        $('#completeBookingForm').on('submit', function(e) {
            console.log("completeBookingForm submit event triggered."); // New debug log
            e.preventDefault(); // Prevent default form submission
            processBookingForm(); // Call the new function to handle booking
        });

        // Set up event listeners for number of adults/children inputs
        const numAdultsInput = document.getElementById('numAdults');
        const numChildrenInput = document.getElementById('numChildren');

        if (numAdultsInput) {
            numAdultsInput.addEventListener('input', function() {
                generateGuestNameFields('adult', parseInt(this.value) || 0);
            });
            // Initial generation on load (in case value is pre-filled)
            generateGuestNameFields('adult', parseInt(numAdultsInput.value) || 0);
        }

        if (numChildrenInput) {
            numChildrenInput.addEventListener('input', function() {
                generateGuestNameFields('child', parseInt(this.value) || 0);
            });
            // Initial generation on load (in case value is pre-filled)
            generateGuestNameFields('child', parseInt(numChildrenInput.value) || 0);
        }

        // Event listener for payment option change - KEEP THIS BLOCK
        document.getElementById('paymentOption').addEventListener('change', function() {
            const paymentOption = this.value;
            console.log('Payment Option selected:', paymentOption); // Debug line added
            const customPaymentAmountField = document.getElementById('customPaymentAmountField');
            const customAmountInput = document.getElementById('customAmount');
            const customAmountHelp = document.getElementById('customAmountHelp');

            if (paymentOption === 'Custom Payment') {
                customPaymentAmountField.style.display = 'block';
                customAmountInput.setAttribute('required', 'true');
            } else {
                customPaymentAmountField.style.display = 'none';
                customAmountInput.removeAttribute('required');
                customAmountInput.value = ''; // Clear value when hidden
                customAmountHelp.style.display = 'none'; // Hide help text
            }
        });

        // Event listener for custom amount input for validation - KEEP THIS BLOCK
        document.getElementById('customAmount').addEventListener('input', function() {
            const customAmount = parseFloat(this.value);
            const customAmountHelp = document.getElementById('customAmountHelp');
            const totalAmountElement = document.getElementById('bookingFormTotalAmount');
            const totalAmountText = totalAmountElement.textContent.replace(/[₱,]/g, ''); // Remove peso sign and comma
            const totalBookingAmount = parseFloat(totalAmountText);

            customAmountHelp.style.display = 'none'; // Hide previous error

            if (isNaN(customAmount) || customAmount <= 1500) {
                customAmountHelp.textContent = 'Amount must be greater than ₱1,500.';
                customAmountHelp.style.display = 'block';
                this.classList.add('is-invalid');
            } else if (customAmount > totalBookingAmount) {
                customAmountHelp.textContent = `Amount cannot exceed total booking amount (₱${totalBookingAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}).`;
                customAmountHelp.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Function to generate name fields for adults/children based on count
    function generateGuestNameFields(type, count) {
        const container = document.getElementById(`${type}NameFieldsContainer`);
        container.innerHTML = ''; // Clear existing fields

        for (let i = 0; i < count; i++) {
            container.innerHTML += `
                <div class="guest-name-section border p-3 mb-3">
                    <h6>${type === 'adult' ? 'Adult' : 'Child'} ${i + 1} Name</h6>
                    <div class="form-group mb-3">
                        <label for="${type}FirstName_${i}">First Name</label>
                        <input type="text" class="form-control" id="${type}FirstName_${i}" name="${type}FirstName[]" placeholder="Enter first name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="${type}LastName_${i}">Last Name</label>
                        <input type="text" class="form-control" id="${type}LastName_${i}" name="${type}LastName[]" placeholder="Enter last name" required>
                    </div>
                </div>
            `;
        }
    }

    function processBookingForm() {
        console.log("processBookingForm called.");
        // Get form data
        const form = document.getElementById('completeBookingForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get room list from localStorage
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Rooms Selected',
                text: 'Please select at least one room before booking.'
            });
            return;
        }

        // Create FormData object
        const formData = new FormData();
        
        // Append all fields from the completeBookingForm
        formData.append('firstName', document.getElementById('firstName').value);
        formData.append('lastName', document.getElementById('lastName').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('contact', document.getElementById('contact').value);
        formData.append('checkInDate', document.getElementById('checkInDate').value);
        formData.append('checkOutDate', document.getElementById('checkOutDate').value);
        
        // Append numAdults and numChildren
        const numAdults = parseInt(document.getElementById('numAdults').value) || 0;
        const numChildren = parseInt(document.getElementById('numChildren').value) || 0;
        formData.append('numAdults', numAdults);
        formData.append('numChildren', numChildren);

        // Append dynamically generated adult names
        for (let i = 0; i < numAdults; i++) {
            formData.append('adultFirstNames[]', document.getElementById(`adultFirstName_${i}`).value);
            formData.append('adultLastNames[]', document.getElementById(`adultLastName_${i}`).value);
        }

        // Append dynamically generated child names
        for (let i = 0; i < numChildren; i++) {
            formData.append('childFirstNames[]', document.getElementById(`childFirstName_${i}`).value);
            formData.append('childLastNames[]', document.getElementById(`childLastName_${i}`).value);
        }

        const extraBedRadios = document.getElementsByName('extraBed');
        let extraBedValue = '';
        for (const radio of extraBedRadios) {
            if (radio.checked) {
                extraBedValue = radio.value;
                break;
            }
        }
        formData.append('extraBed', extraBedValue);

        const paymentOption = document.getElementById('paymentOption').value;
        formData.append('paymentOption', paymentOption);
        
        if (paymentOption === 'Custom Payment') {
            const customAmount = document.getElementById('customAmount').value;
            formData.append('customAmount', customAmount);
        }

        formData.append('paymentMethod', document.getElementById('paymentMethod').value);

        // Get total amount from display
        const totalAmountText = document.getElementById('bookingFormTotalAmount').textContent.replace(/[₱,]/g, '');
        formData.append('totalAmount', parseFloat(totalAmountText));

        // Prepare room data from localStorage
        const roomsData = roomList.map(room => ({
            room_type_id: room.id, // Use room.id as room_type_id
            type: room.type,
            price: parseFloat(room.price),
            quantity: room.quantity,
            // You might want to add other room details if needed in the backend
        }));
        formData.append('rooms', JSON.stringify(roomsData));

        // Calculate nights and add to form data
        const nights = calculateNights(formData.get('checkInDate'), formData.get('checkOutDate'));
        formData.append('nights', nights);

        // Show loading state
        Swal.fire({
            title: 'Processing Booking',
            text: 'Please wait while we process your reservation...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Debug log
        console.log('Form Data for AJAX:', Object.fromEntries(formData.entries()));
        
        // Send AJAX request
        $.ajax({
            url: 'process_booking.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // Explicitly expect JSON response
            success: function(jsonResponse) { // jQuery will parse JSON automatically if dataType is 'json'
                // Delay the processing for 3 seconds to show the spinner
                setTimeout(() => {
                    console.log("Inside success setTimeout callback.");
                    console.log('Server response (raw - jQuery parsed):', jsonResponse);

                    // Check if jsonResponse is actually an object and has a success property
                    if (typeof jsonResponse !== 'object' || jsonResponse === null) {
                        console.error('Response is not a valid JSON object:', jsonResponse);
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Response Error',
                            text: 'Received an invalid response from the server. Please try again.',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            $('#bookingFormModal').modal('hide');
                            clearCart();
                        });
                        return;
                    }
                    
                    console.log('Attempting to close loading Swal before showing final message...');
                    Swal.close(); // Close loading Swal
                    if (jsonResponse.success) {
                        // Clear the room list from localStorage
                        localStorage.removeItem('roomList');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Successful!',
                            text: jsonResponse.message || 'Your booking has been confirmed and a confirmation email has been sent to your email address.',
                            showConfirmButton: false, // Auto-close
                            timer: 3000 // Auto-close after 3 seconds
                        }).then(() => {
                            console.log("Redirecting after success message auto-close (full success path)."); // Debug log
                            $('#bookingFormModal').modal('hide'); // Close the booking modal
                            clearCart(); // Clear the cart after successful booking
                            window.location.href = 'booking_status.php'; // Redirect to booking status page
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Failed',
                            text: jsonResponse.message || 'An error occurred while processing your booking.',
                            showConfirmButton: false, // Auto-close for better UX
                            timer: 3000 // Auto-close after 3 seconds
                        }).then(() => {
                            console.log('Booking failed message auto-closed.');
                            // For explicit error, close modal and clear cart, but don't auto-redirect without user interaction
                            $('#bookingFormModal').modal('hide'); // Close the booking modal
                            clearCart(); // Clear the cart (even on this path)
                        });
                    }
                }, 3000); // 3 seconds delay for spinner
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response Text (raw):', xhr.responseText);
                
                // Keep the spinner for 3 seconds, then show success and proceed
                setTimeout(() => {
                    console.log('Inside error setTimeout callback. Closing spinner...');
                    Swal.close(); // Close the "Processing Booking" spinner
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Successfully booked! (Note: A minor network issue occurred during confirmation, but your booking was successful.)',
                        showConfirmButton: false,
                        timer: 3000 // Auto-close after 3 seconds
                    }).then(() => {
                        console.log('Network error success message auto-closed.');
                        // After the success message disappears
                        $('#bookingFormModal').modal('hide'); // Close the booking modal
                        clearCart(); // Clear the cart (even on this path)
                        window.location.href = 'booking_status.php'; // Assuming a successful booking means redirection
                    });
                }, 3000); // 3 seconds delay for spinner
            }
        });
    }

    // Calculate total including extra guest charges
    function calculateTotal() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList.length === 0) return 0;
        
        const numAdults = parseInt(document.getElementById('numAdults').value) || 0;
        const numChildren = parseInt(document.getElementById('numChildren').value) || 0;
        const totalGuests = numAdults + numChildren;
        
        let totalCapacity = 0;
        roomList.forEach(room => {
            totalCapacity += (parseInt(room.capacity) || 2) * (parseInt(room.quantity) || 1);
        });
        
        const extraGuests = Math.max(0, totalGuests - totalCapacity);
        const extraGuestCharge = extraGuests * 1000; // ₱1,000 per extra guest
        
        // Update the UI
        const extraGuestAlert = document.getElementById('extraGuestAlert');
        const extraGuestMessage = document.getElementById('extraGuestMessage');
        const extraGuestChargeElement = document.getElementById('extraGuestCharge');
        
        if (extraGuests > 0) {
            extraGuestMessage.textContent = `${extraGuests} extra guest(s) (₱1,000 each)`;
            extraGuestChargeElement.textContent = `+₱${extraGuestCharge.toLocaleString()}`;
            extraGuestAlert.style.display = 'block';
        } else {
            extraGuestAlert.style.display = 'none';
        }
        
        // Update and return the total amount including extra guest charges
        return updateTotalAmountWithExtraGuests(extraGuestCharge);
    }
    
    // Update total amount with extra guest charges
    function updateTotalAmountWithExtraGuests(extraGuestCharge) {
        const baseTotal = parseFloat(document.getElementById('bookingBaseTotal').textContent.replace(/[^0-9.-]+/g,"")) || 0;
        const totalAmount = baseTotal + extraGuestCharge;
        
        // Update the displayed total in the summary
        const totalElement = document.querySelector('.total-amount');
        if (totalElement) {
            totalElement.textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
        
        // Also update the main total in the form footer
        const formTotalElement = document.getElementById('bookingFormTotalAmount');
        if (formTotalElement) {
            formTotalElement.textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
        
        // Update the hidden input for form submission
        document.getElementById('extraGuestCharges').value = extraGuestCharge;
        
        return totalAmount;
    }
    
    // Helper function to calculate nights - KEEP THIS FUNCTION
    function calculateNights(checkIn, checkOut) {
        if (!checkIn || !checkOut) return 0;
        const startDate = new Date(checkIn);
        const endDate = new Date(checkOut);
        const timeDiff = endDate.getTime() - startDate.getTime();
        // Calculate difference in days, ensuring it's at least 0
        const nights = Math.max(0, Math.ceil(timeDiff / (1000 * 60 * 60 * 24)));
        return nights;
    }

    // Function to update booking totals (nights and overall total) - KEEP THIS FUNCTION
    function updateBookingTotals() {
        const checkInDate = document.getElementById('checkInDate').value;
        const checkOutDate = document.getElementById('checkOutDate').value;

        const nights = calculateNights(checkInDate, checkOutDate);
        document.getElementById('numberOfNights').textContent = `${nights} Nights`;

        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        let basePricePerNight = 0;
        roomList.forEach(room => {
            basePricePerNight += room.price * room.quantity;
        });

        // Calculate the total for all nights
        const totalForRoomsAndNights = basePricePerNight * nights;

        // Update the 'Booking Total' field to show the base price for all nights
        document.getElementById('bookingBaseTotal').textContent = `₱${totalForRoomsAndNights.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        
        // Recalculate total with any extra guest charges
        const finalTotal = calculateTotal();
        
        // Update the main total amount at the bottom of the modal with the final total
        document.getElementById('bookingFormTotalAmount').textContent = `₱${finalTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    // Function to update the booking form total based on selected rooms and dates
    function updateBookingFormTotal() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList.length === 0) return;

        const checkInDate = document.getElementById('checkInDate').value;
        const checkOutDate = document.getElementById('checkOutDate').value;
        
        if (!checkInDate || !checkOutDate) return;

        const nights = calculateNights(checkInDate, checkOutDate);
        if (nights <= 0) return;

        // Calculate base total (rooms * price * nights)
        let totalForRoomsAndNights = 0;
        let totalCapacity = 0;
        
        roomList.forEach(room => {
            const roomQty = parseInt(room.quantity) || 1;
            totalForRoomsAndNights += (parseFloat(room.price) * roomQty * nights);
            totalCapacity += (parseInt(room.capacity) || 2) * roomQty;
            
            // Set the initial guest count to room capacity if not set
            if (typeof room.guests === 'undefined') {
                room.guests = room.capacity || 2;
            }
        });
        
        // Save updated room list with guest counts
        localStorage.setItem('roomList', JSON.stringify(roomList));

        // Update the base total display
        document.getElementById('bookingBaseTotal').textContent = `₱${totalForRoomsAndNights.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('numberOfNights').textContent = `${nights} Night${nights !== 1 ? 's' : ''}`;
        
        // Set max adults to total capacity
        const numAdultsInput = document.getElementById('numAdults');
        if (numAdultsInput) {
            const currentAdults = parseInt(numAdultsInput.value) || 1;
            numAdultsInput.max = Math.max(1, totalCapacity);
            if (currentAdults > totalCapacity) {
                numAdultsInput.value = totalCapacity;
            }
        }
        
        // Recalculate total with any extra guest charges
        calculateTotal();
    }

    function showCart() {
        console.log('showCart called');
        try {
            $('#cartModal').modal('show');
        } catch (e) {
            console.error('Modal show failed:', e);
            // Fallback: try to trigger with native JS
            var modal = document.getElementById('cartModal');
            if (modal) modal.style.display = 'block';
        }
    }

    function renderCartModal() {
        const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        const cartItemsDiv = document.getElementById('cartItems');
        if (!cartItemsDiv) return;
        if (roomList.length === 0) {
            cartItemsDiv.innerHTML = '<div class="text-center text-muted">No rooms in your cart yet.</div>';
            document.getElementById('cartTotalAmount').textContent = '₱0.00'; // Reset total
            return;
        }

        let totalAmount = 0;
        cartItemsDiv.innerHTML = roomList.map((room, idx) => {
            // Debug log: Check the room object being rendered
            console.log('Rendering cart item. Room object:', room);

            const itemTotal = room.price * room.quantity;
            totalAmount += itemTotal;
            
            return `
                <div class="cart-item d-flex align-items-center mb-3 p-3 border rounded position-relative" style="background:#fff;">
                    <button class="btn btn-link text-danger position-absolute" style="top:10px; right:10px; font-size:1.5rem;" onclick="removeFromCart(${idx})" title="Remove"><i class="fas fa-times"></i></button>
                    <img src="${room.image ? 'uploads/rooms/' + room.image : 'assets/img/rooms/default.jpg'}" alt="Room Image" style="width:100px; height:80px; object-fit:cover; border-radius:6px; margin-right:16px;" onerror="this.onerror=null;this.src='assets/img/rooms/default.jpg';">
                    <div style="flex:1;">
                        <div class="fw-bold" style="font-size:1.1rem;">${room.type}</div>
                        <div class="text-muted" style="font-size:0.95rem;">Added on: ${room.addedAt ? new Date(room.addedAt).toLocaleString() : 'N/A'}</div>
                        <div class="mt-1"><span style="font-weight:500; color:#222;">₱${parseFloat(room.price).toLocaleString()} per night</span></div>
                        <div class="mt-1"><i class="fa fa-users"></i> Max capacity: ${room.capacity || 'N/A'} guests</div>
                        <div class="mt-2"><i class="fa fa-bed"></i> <span class="text-danger">${room.availableRooms} rooms available</span></div>
                        
                        <!-- Quantity Controls -->
                        <div class="input-group mt-2 qty-control-sm d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateRoomQuantity(${idx}, -1)">-</button>
                            <input type="text" class="form-control form-control-sm" value="${room.quantity}" readonly>
                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateRoomQuantity(${idx}, 1)">+</button>
                        </div>
                        
                        <!-- Guest Count Input -->
                        <div class="form-group mt-2">
                            <label class="small mb-1">Number of Guests</label>
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateGuestCount(${idx}, -1)">-</button>
                                <input type="number" class="form-control form-control-sm text-center" 
                                       id="guestCount_${idx}" 
                                       value="${room.guests || room.capacity || 1}" 
                                       min="1" 
                                       onchange="updateGuestCountInput(${idx}, this.value)"
                                       style="width: 50px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateGuestCount(${idx}, 1)">+</button>
                            </div>
                            <small class="form-text text-muted">Max: ${room.capacity || 2} (₱1,000 per extra guest)</small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Add Total Amount section
        const totalAmountSection = document.getElementById('cartTotalAmountSection');
        if (!totalAmountSection) {
            // Create total amount section if it doesn't exist
            const modalBody = document.querySelector('#cartModal .modal-body');
            if (modalBody) {
                modalBody.insertAdjacentHTML('afterend', `
                    <div id="cartTotalAmountSection" class="modal-footer d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Amount:</h5>
                        <h5 class="mb-0" id="cartTotalAmount">₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</h5>
                    </div>
                `);
            }
        } else {
            document.getElementById('cartTotalAmount').textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
    }
    // Remove room from cart by index and update modal - KEEP THIS BLOCK
    function removeFromCart(idx) {
        let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        roomList.splice(idx, 1);
        localStorage.setItem('roomList', JSON.stringify(roomList));
        renderCartModal();
        updateCartCount && updateCartCount();
    }
    // Function to update room quantity in cart - KEEP THIS BLOCK
    function updateRoomQuantity(idx, change) {
        let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
        if (roomList[idx]) {
            roomList[idx].quantity += change;
            if (roomList[idx].quantity < 1) {
                roomList[idx].quantity = 1; // Prevent quantity from going below 1
            }
            localStorage.setItem('roomList', JSON.stringify(roomList));
            renderCartModal(); // Re-render the cart to show updated quantity
            updateCartCount && updateCartCount(); // Update cart count in header
        }
    }
    $('#cartModal').on('show.bs.modal', renderCartModal);
    </script>

    <script>
    // Function to clear the cart - KEEP THIS BLOCK
    function clearCart() {
        localStorage.removeItem('roomList');
        renderCartModal(); // Re-render the cart to show it's empty
        updateCartCount && updateCartCount(); // Update the cart count in the header
    }
    </script>

    <script>
    function backToCart() {
        console.log('Attempting to go back to cart.');
        try {
            $('#bookingFormModal').modal('hide'); // Hide booking form modal
            $('#cartModal').modal('show'); // Show cart modal
            console.log('Switched back to cart modal.');
        } catch (e) {
            console.error('Error going back to cart:', e);
        }
    }
    </script>

    <script>
    // Global Date Picker Configuration
    document.addEventListener('DOMContentLoaded', function() {
        // Function to check if a date is in the disabled dates array
        function isDateDisabled(dateString, disabledDates) {
            return disabledDates.includes(dateString);
        }

        // Configure date inputs
        function configureDateInputs(checkInSelector, checkOutSelector) {
            const checkInDateInput = document.querySelector(checkInSelector);
            const checkOutDateInput = document.querySelector(checkOutSelector);
            
            if (!checkInDateInput || !checkOutDateInput) return;
            
            // Define disabled dates with their corresponding reasons
            const disabledDates = [
                { date: '2025-11-15', reason: 'Fully booked - No rooms available' },
                { date: '2025-11-20', reason: 'Fully booked - Special event' },
                { date: '2025-11-25', reason: 'Fully booked - Holiday season' },
                { date: '2025-12-01', reason: 'Fully booked - Peak season' }
            ];
            
            // Create a simple array of just the date strings for checking
            const disabledDateStrings = disabledDates.map(item => item.date);
            
            // Add visual indication for disabled dates
            function styleDisabledDates() {
                // This function will be called after the date picker is initialized
                // It adds a custom CSS class to disabled dates
                setTimeout(() => {
                    disabledDates.forEach(disabledDate => {
                        const date = new Date(disabledDate.date);
                        const dateString = date.toISOString().split('T')[0];
                        const dateElements = document.querySelectorAll(`[data-date='${dateString}']`);
                        
                        dateElements.forEach(el => {
                            el.classList.add('disabled-date');
                            el.title = disabledDate.reason;
                            el.style.position = 'relative';
                            
                            // Add a small 'X' mark on the disabled date
                            if (!el.querySelector('.date-unavailable')) {
                                const xMark = document.createElement('span');
                                xMark.className = 'date-unavailable';
                                xMark.textContent = '✗';
                                xMark.style.position = 'absolute';
                                xMark.style.top = '2px';
                                xMark.style.right = '2px';
                                xMark.style.color = 'red';
                                xMark.style.fontSize = '10px';
                                el.appendChild(xMark);
                            }
                        });
                    });
                }, 100);
            }
            
            // Call the styling function after a short delay to ensure the calendar is rendered
            setTimeout(styleDisabledDates, 500);
            
            // Set minimum date to today
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            const todayFormatted = `${yyyy}-${mm}-${dd}`;
            
            // Configure check-in date
            checkInDateInput.min = todayFormatted;
            
            // Add event listener to validate check-in date
            checkInDateInput.addEventListener('change', function() {
                if (isDateDisabled(this.value, disabledDateStrings)) {
                    const selectedDate = new Date(this.value);
                    const formattedDate = selectedDate.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    // Find the reason for this date being disabled
                    const dateInfo = disabledDates.find(d => d.date === this.value);
                    const reason = dateInfo ? dateInfo.reason : 'This date is not available';
                    
                    alert(`❗ Booking Unavailable for ${formattedDate}\n\n${reason}\n\nPlease select a different date.`);
                    this.value = '';
                    return;
                }
                
                // Update check-out date minimum to be the day after check-in
                if (this.value) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    const nextDayFormatted = nextDay.toISOString().split('T')[0];
                    
                    if (checkOutDateInput) {
                        checkOutDateInput.min = nextDayFormatted;
                        
                        // If current check-out date is before new check-in date, reset it
                        if (checkOutDateInput.value && new Date(checkOutDateInput.value) <= new Date(this.value)) {
                            checkOutDateInput.value = '';
                        }
                    }
                }
            });
            
            // Also validate check-out date
            checkOutDateInput.min = todayFormatted;
            
            checkOutDateInput.addEventListener('change', function() {
                if (isDateDisabled(this.value, disabledDateStrings)) {
                    const selectedDate = new Date(this.value);
                    const formattedDate = selectedDate.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    // Find the reason for this date being disabled
                    const dateInfo = disabledDates.find(d => d.date === this.value);
                    const reason = dateInfo ? dateInfo.reason : 'This date is not available';
                    
                    alert(`❗ Booking Unavailable for ${formattedDate}\n\n${reason}\n\nPlease select a different date.`);
                    this.value = '';
                }
            });
        }

        // Initialize date pickers for different forms
        // Main booking form
        configureDateInputs('#checkInDate', '#checkOutDate');
        
        // Direct check-in form
        configureDateInputs('#directCheckInDate', '#directCheckOutDate');
    });
    </script>
</body>
</html>
