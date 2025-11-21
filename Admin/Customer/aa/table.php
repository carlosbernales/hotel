<?php

try {
    // Get database connection
    require 'db_con.php';
    $db = $pdo; // Assuming your db_con.php creates a $conn PDO object
    echo "<!-- Database connection successful -->";

    // Fetch Regular Packages with full details
    $regularQuery = "SELECT 
        id,
        package_name,
        description,
        capacity,
        NULL as price, /* Changed to always return NULL for price */
        image_path,
        available_tables,
        menu_items, /* Added menu_items column */
        status,
        reason
    FROM table_packages 
    WHERE capacity < 30";

    $regularStmt = $db->prepare($regularQuery);
    $regularStmt->execute();
    $regularPackages = $regularStmt->fetchAll();
    
    echo "<!-- Regular packages count: " . count($regularPackages) . " -->";

    // Fetch Ultimate Packages with full details
    $ultimateQuery = "SELECT 
        id,
        package_name,
        description,
        capacity,
        COALESCE(price, 0) as price,
        image_path,
        available_tables,
        menu_items,
        image1,
        image2,
        image3,
        image4,
        image5,
        status,
        reason
    FROM table_packages 
    WHERE capacity >= 30";

    $ultimateStmt = $db->prepare($ultimateQuery);
    $ultimateStmt->execute();
    $ultimatePackages = $ultimateStmt->fetchAll();
    
    echo "<!-- Ultimate packages count: " . count($ultimatePackages) . " -->";

    // Fetch menu items with their categories
    $menuQuery = "SELECT mi.*, mc.name as category_name 
                 FROM menu_items mi 
                 JOIN menu_categories mc ON mi.category_id = mc.id";
    $menuStmt = $db->prepare($menuQuery);
    $menuStmt->execute();
    $menuItems = $menuStmt->fetchAll();
    
    echo "<!-- Menu items count: " . count($menuItems) . " -->";

    // Fetch menu item addons
    $addonsQuery = "SELECT * FROM menu_item_addons";
    $addonsStmt = $db->prepare($addonsQuery);
    $addonsStmt->execute();
    $menuAddons = $addonsStmt->fetchAll();
    
    echo "<!-- Menu addons count: " . count($menuAddons) . " -->";

    // Fetch payment methods
    $paymentMethodsQuery = "SELECT id, name, display_name 
                           FROM payment_methods 
                           WHERE is_active = 1";
    $paymentMethodsStmt = $db->prepare($paymentMethodsQuery);
    $paymentMethodsStmt->execute();
    $paymentMethods = $paymentMethodsStmt->fetchAll();

    // At the top of your file, after fetching menu items
    $menuByCategory = [];
    foreach ($menuItems as $item) {
        $category = $item['category_name'];
        if (!isset($menuByCategory[$category])) {
            $menuByCategory[$category] = [];
        }
        $menuByCategory[$category][] = $item;
    }

    // Add this after the database queries
    echo "<!-- Debug image paths: -->";
    foreach ($regularPackages as $package) {
        echo "<!-- Regular package image: " . htmlspecialchars($package['image_path']) . " -->";
    }
    foreach ($ultimatePackages as $package) {
        echo "<!-- Ultimate package image: " . htmlspecialchars($package['image_path']) . " -->";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

// Check if nav.php exists and is readable
if (file_exists('nav.php')) {
    echo "<!-- nav.php exists -->";
} else {
    echo "<!-- nav.php not found -->";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .package-section {
            margin-top: 110px;
            padding: 2.5rem;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        /* Styles for package images */
        img[src*="../../uploads/table_packages"] {
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        img[src*="../../uploads/table_packages"]:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        #modalImage {
            max-height: 80vh;
            object-fit: contain;
        }

        .header-title {
            font-size: 2.25rem;
            color: #b6860a;
            text-align: center;
            font-weight: 700;
            margin-bottom: 2.5rem;
            position: relative;
            text-transform: capitalize;
        }

        .header-title:after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #b6860a, #d4a012);
            border-radius: 2px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card img {
            height: 280px;
            object-fit: cover;
        }

        .card-body {
            padding: 2rem;
            background: white;
            position: relative;
        }

        .card-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.2rem;
            font-size: 1.4rem;
            line-height: 1.4;
        }

        .card-text {
            color: #666;
            margin-bottom: 0.8rem;
            line-height: 1.6;
        }

        .card-text.text-warning {
            font-size: 1.25rem;
            margin: 1rem 0;
            background: -webkit-linear-gradient(45deg, #b6860a, #d4a012);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-text.text-warning i {
            color: #b6860a;
            -webkit-text-fill-color: initial;
        }

        .btn-warning {
            background-color: #e6b800;
            border: none;
            color: white;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-advanced {
            float: right;
            background-color: #fff;
            color: #b6860a;
            border: 2px solid #b6860a;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .notes-section {
            background: linear-gradient(145deg, #fff9e6, #fffdf2);
            border-radius: 20px;
            padding: 2.5rem;
            margin-top: 3rem;
            box-shadow: 0 10px 30px rgba(182, 134, 10, 0.1);
            border: 1px solid rgba(182, 134, 10, 0.1);
        }

        .notes-section h5 {
            color: #b6860a;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notes-section h5:before {
            content: "📝";
            font-size: 1.4rem;
        }

        .notes-section ul {
            padding-left: 1.8rem;
            margin: 0;
        }

        .notes-section li {
            color: #555;
            margin-bottom: 1rem;
            position: relative;
            line-height: 1.6;
            padding-left: 0.5rem;
        }

        .notes-section li:before {
            content: "•";
            color: #b6860a;
            font-weight: bold;
            position: absolute;
            left: -1.2rem;
            font-size: 1.4rem;
        }

        .text-danger {
            font-size: 0.95rem;
            font-style: italic;
            color: #dc3545;
            margin-top: 1rem;
            padding: 0.8rem;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 10px;
            display: inline-block;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(45deg, #b6860a, #d4a012);
            color: white;
            border: none;
            padding: 1.8rem;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .carousel {
            border-radius: 15px;
            overflow: hidden;
            margin: -1rem -1rem 1.5rem -1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .carousel-item img {
            height: 450px;
            object-fit: cover;
        }

        .form-control {
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            border: 2px solid #eee;
        }

        .form-control:focus {
            border-color: #b6860a;
            box-shadow: 0 0 0 0.2rem rgba(182, 134, 10, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        /* Button styles */
        .btn {
            padding: 8px 16px !important;
            font-size: 14px !important;
        }

        .btn-primary-custom {
            padding: 8px 20px !important;
            font-size: 14px !important;
            background-color: #d4af37;
            border: 2px solid #d4af37;
            color: white;
            border-radius: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-secondary-custom {
            padding: 6px 15px !important;
            font-size: 13px !important;
            background-color: #6c757d;
            border: 1px solid #6c757d;
            color: white;
            border-radius: 20px;
        }

        .action-buttons .btn {
            margin: 0 3px;
            padding: 6px 12px !important;
            font-size: 13px !important;
        }

        @media (max-width: 768px) {
            .package-section {
                padding: 1.5rem;
                margin: 1rem;
            }

            .header-title {
                font-size: 1.8rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .carousel-item img {
                height: 300px;
            }

            .btn-warning, .btn-advanced {
                width: 100%;
                margin-bottom: 1rem;
            }

            .btn {
                padding: 6px 14px !important;
                font-size: 13px !important;
            }
            
            .btn-primary-custom {
                padding: 7px 18px !important;
                font-size: 13px !important;
            }
        }

        .payment-details {
            font-size: 1rem;
        }

        .payment-details .card-title {
            color: #333;
            margin-bottom: 1rem;
        }

        .payment-details hr {
            margin: 1rem 0;
        }

        #downpaymentDetails {
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 4px;
            margin: 0.5rem 0;
        }

        .modal-body .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .modal-body .card-body {
            padding: 1rem;
        }

        .policy-content {
            font-size: 0.95rem;
        }

        .policy-content h5 {
            color: #b6860a;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
        }

        .policy-content h5:first-child {
            margin-top: 0;
        }

        .policy-content ul {
            padding-left: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .policy-content li {
            margin-bottom: 0.8rem;
            color: #555;
            line-height: 1.5;
        }

        .form-check-label a {
            color: #b6860a;
            text-decoration: none;
        }

        .form-check-label a:hover {
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: #b6860a;
            border-color: #b6860a;
        }

        .booking-summary-popup {
            max-width: 600px !important;
            font-family: 'Poppins', sans-serif;
        }

        .booking-summary-popup .swal2-html-container {
            margin: 1em 1.6em 0.3em;
        }

        .booking-summary-popup .table {
            margin-bottom: 0;
        }

        .booking-summary-popup .table td {
            padding: 0.5rem 1rem;
            vertical-align: middle;
        }

        .booking-summary-popup .text-warning {
            color: #d4af37 !important;
        }

        .booking-summary-popup hr {
            margin: 1.5rem 0;
            opacity: 0.15;
        }

        .booking-summary-popup .text-success {
            color: #28a745 !important;
        }

        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .btn-primary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .menu-items {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .menu-item {
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .menu-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px 0 0 8px;
        }

        .item-quantity {
            width: 50px !important;
            text-align: center;
        }

        .btn-group .btn-outline-warning.active {
            background-color: #d4af37;
            color: white;
            border-color: #d4af37;
        }

        .menu-categories {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .addon-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .addon-checkbox:checked {
            background-color: #d4af37;
            border-color: #d4af37;
        }

        #addonsModal .card {
            border: 1px solid rgba(0,0,0,.125);
            transition: all 0.3s ease;
        }

        #addonsModal .card:hover {
            border-color: #d4af37;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #addonsModal .card-title {
            font-size: 1rem;
            font-weight: 600;
        }

        #addonsModal .form-check {
            margin: 0;
            padding: 0;
        }

        .package-details {
            padding: 1rem;
        }

        .package-details .package-image {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .package-details h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #b6860a;
        }

        .package-details p {
            line-height: 1.6;
        }

        .package-details strong {
            color: #2c3e50;
        }

        .package-details .text-muted {
            color: #6c757d !important;
        }

        .modal-dialog {
            max-width: 600px;
        }

        .package-details {
            background: #fff;
        }

        .package-details h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #b6860a;
        }

        .package-details h6 {
            font-weight: 600;
            color: #2c3e50;
        }

        .package-details .text-muted {
            color: #6c757d !important;
        }

        .modal-header.bg-warning {
            background-color: #b6860a !important;
        }

        .modal-dialog {
            max-width: 500px;
        }

        #detailsImage {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #dee2e6;
        }

        /* Modal width adjustments */
        .modal-lg {
            max-width: 800px; /* Increased from default 500px */
        }

        .modal-xl {
            max-width: 1140px; /* Increased from default 800px */
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .modal-lg, .modal-xl {
                max-width: 95%;
                margin: 1rem auto;
            }
        }

        /* Modal content styling */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .modal-body {
            padding: 2rem;
        }

        .menu-items-container {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Add-ons Modal Styling */
        .addon-item .card {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .addon-item .card:hover {
            border-color: #ffc107;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .addon-item .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0;
            cursor: pointer;
        }

        .addon-item .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .addon-item .form-check-label {
            font-size: 1rem;
            cursor: pointer;
            padding-left: 0.5rem;
        }

        .addon-name {
            color: #2c3e50;
            font-weight: 500;
        }

        .addon-price {
            font-size: 1rem;
            color: #ffc107;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #e0a800;
        }

        .cart-summary {
            position: sticky;
            top: 1rem;
        }

        .cart-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .cart-item-price {
            color: #28a745;
            font-weight: 600;
        }

        .cart-item-addons {
            font-size: 0.85rem;
            color: #6c757d;
            padding-left: 1rem;
        }

        .cart-item-remove {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.1rem;
        }

        #cartItems {
            max-height: 400px;
            overflow-y: auto;
        }

        .package-inclusions {
            list-style: none;
            padding-left: 0;
        }

        .package-inclusions li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .package-inclusions li:before {
            content: "✓";
            color: #b6860a;
            position: absolute;
            left: 0;
        }

        #ultimatePackageModal .card {
            border: 1px solid rgba(182, 134, 10, 0.2);
            transition: all 0.3s ease;
        }

        #ultimatePackageModal .card:hover {
            border-color: #b6860a;
            box-shadow: 0 2px 4px rgba(182, 134, 10, 0.1);
        }

        #ultimatePackageModal .card-title {
            color: #b6860a;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        #ultimatePackageModal .list-unstyled li {
            margin-bottom: 0.5rem;
            color: #666;
        }

        #paymentProofSection {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        #paymentPreview {
            text-align: center;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
        }

        #removeProof {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .input-group-text {
            background-color: #b6860a;
            color: white;
            border-color: #b6860a;
            cursor: pointer;
        }

        .input-group-text:hover {
            background-color: #95700a;
        }

        .payment-breakdown {
            font-size: 0.95rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .payment-breakdown hr {
            margin: 0.8rem 0;
            opacity: 0.15;
        }

        .payment-breakdown .text-warning {
            color: #b6860a !important;
            font-size: 1.1rem;
        }

        .payment-breakdown .text-danger {
            color: #dc3545 !important;
            font-size: 1.1rem;
        }

        .payment-breakdown .text-success {
            color: #28a745 !important;
            font-size: 1.1rem;
        }

        .payment-breakdown small.text-muted {
            font-size: 0.85rem;
            font-style: italic;
        }

        .booking-summary {
            font-family: 'Poppins', sans-serif;
            padding: 1rem;
        }

        .booking-summary p {
            margin-bottom: 0.5rem;
        }

        .booking-summary h6 {
            color: #b6860a;
            font-weight: 600;
            margin-top: 1rem;
        }

        .booking-summary hr {
            margin: 1rem 0;
            opacity: 0.15;
        }

        .booking-summary .fw-bold {
            color: #28a745;
            margin-top: 1rem;
        }

        .swal2-popup {
            font-family: 'Poppins', sans-serif;
        }

        .swal2-html-container {
            text-align: left;
        }

        /* Add these styles to your existing CSS */
        .menu-list {
            margin: 0;
            padding: 0;
        }

        .menu-list .menu-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .menu-list .menu-item:last-child {
            border-bottom: none;
        }

        .menu-list .menu-item:before {
            content: "•";
            color: #b6860a;
            margin-right: 10px;
        }

        #packageImageCarousel {
            background-color: #000;
        }
        
        #packageImageCarousel .carousel-item {
            height: 400px;
        }
        
        #packageImageCarousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        #packageImageCarousel .carousel-control-prev,
        #packageImageCarousel .carousel-control-next {
            background: rgba(0,0,0,0.5);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 10px;
        }
        
        #packageImageCarousel .carousel-indicators {
            margin-bottom: 0;
        }
        
        #packageImageCarousel .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 4px;
        }

        .status-banner {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .status-banner .badge {
            font-size: 0.9rem;
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
        }
        .package-image {
            height: 200px;
            object-fit: cover;
        }
        .alert {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .package-details {
            font-size: 0.9rem;
            color: #666;
        }
        .card {
            position: relative;
        }
        <?php if ($package['status'] === 'inactive'): ?>
        .card {
            opacity: 0.85;
        }
        <?php endif; ?>

        .login-message {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .login-required-popup {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .login-content h4 {
            color: #333;
            margin-bottom: 1rem;
        }

        .login-buttons {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .login-buttons .btn {
            padding: 0.5rem 1.5rem;
        }
    </style>
</head>
<body>
    <?php include 'nav.php';?>
    <?php include 'message_box.php'; ?>
    
    <div id="alertPlaceholder" class="container mt-3"></div>
    
    <div class="container">
        <!-- Table Packages Section -->
        <div class="package-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="header-title">Table Packages</h2>
            </div>
            
            <div class="row g-4">
                <?php foreach ($regularPackages as $package): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if ($package['status'] === 'inactive'): ?>
                        <div class="status-banner">
                            <span class="badge badge-warning">Currently Unavailable</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="position-relative overflow-hidden">
                            <img src="../../uploads/table_packages/<?php echo basename($package['image_path']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($package['package_name']); ?>">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($package['package_name']); ?></h5>
                            <p class="card-text">
                                <i class="fas fa-users me-2"></i>Capacity: <?php echo htmlspecialchars($package['capacity']); ?>
                            </p>
                            <?php if (isset($package['price'])): ?>
                            <p class="card-text text-warning fw-bold mb-3">
                                <i class="fas fa-tag me-2"></i>₱<?php echo number_format((float)$package['price'], 2); ?>
                            </p>
                            <?php endif; ?>

                            <?php if ($package['status'] === 'inactive'): ?>
                            <div class="alert alert-warning mt-2 mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Notice:</strong><br>
                                <?php echo htmlspecialchars($package['reason']); ?>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex flex-column align-items-center gap-2">
                                <button type="button" 
                                        class="btn btn-outline-warning view-details-btn w-100"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#packageDetailsModal"
                                        data-package-id="<?php echo $package['id']; ?>"
                                        data-package-name="<?php echo htmlspecialchars($package['package_name']); ?>"
                                        data-package-description="<?php echo htmlspecialchars($package['description']); ?>"
                                        data-package-capacity="<?php echo $package['capacity']; ?>"
                                        data-package-image="<?php echo basename($package['image_path']); ?>">
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </button>
                                <?php if ($package['status'] === 'active'): ?>
                                <button type="button" 
                                        class="btn btn-warning reserve-btn w-100"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reservationModal"
                                        data-package-id="<?php echo $package['id']; ?>"
                                        data-package-name="<?php echo htmlspecialchars($package['package_name']); ?>"
                                        data-package-price="<?php echo $package['price']; ?>"
                                        data-package-capacity="<?php echo $package['capacity']; ?>">
                                    <i class="fas fa-calendar-check me-2"></i>Reserve
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-ban me-2"></i>Currently Unavailable
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
            </div>
        </div>
        

        <!-- Ultimate Food Package Section -->
        <div class="package-section">
            <h2 class="header-title">Intimate Food Package 30 PAX for Cafe Events </h2>
            <div class="row g-4">
                <?php foreach ($ultimatePackages as $package): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if ($package['status'] === 'inactive'): ?>
                        <div class="status-banner">
                            <span class="badge badge-warning">Currently Unavailable</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="position-relative overflow-hidden">
                            <img src="../../uploads/table_packages/<?php echo basename($package['image_path']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($package['package_name']); ?>">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($package['package_name']); ?></h5>
                            <p class="card-text">
                                <i class="fas fa-users me-2"></i>Capacity: Up to <?php echo htmlspecialchars($package['capacity']); ?>
                            </p>
                            <?php if ($package['capacity'] >= 30 && isset($package['price']) && is_numeric($package['price'])): ?>
                            <p class="card-text text-warning fw-bold mb-3">
                                <i class="fas fa-tag me-2"></i>₱<?php echo number_format((float)$package['price'], 2); ?>
                            </p>
                            <?php endif; ?>

                            <?php if ($package['status'] === 'inactive'): ?>
                            <div class="alert alert-warning mt-2 mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Notice:</strong><br>
                                <?php echo htmlspecialchars($package['reason']); ?>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex flex-column align-items-center gap-2">
                                <button type="button" 
                                        class="btn btn-outline-warning view-details-btn w-100"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ultimatePackageModal"
                                        data-package-id="<?php echo $package['id']; ?>"
                                        data-package-name="<?php echo htmlspecialchars($package['package_name']); ?>"
                                        data-package-description="<?php echo htmlspecialchars($package['description']); ?>"
                                        data-package-capacity="<?php echo $package['capacity']; ?>"
                                        data-package-image="<?php echo basename($package['image_path']); ?>"
                                        data-package-price="<?php echo $package['price']; ?>"
                                        data-package-menu-items="<?php echo htmlspecialchars($package['menu_items']); ?>">
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </button>
                                <?php if ($package['status'] === 'active'): ?>
                                <button type="button" 
                                        class="btn btn-warning reserve-btn w-100"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reservationModal"
                                        data-package-id="<?php echo $package['id']; ?>"
                                        data-package-name="<?php echo htmlspecialchars($package['package_name']); ?>"
                                        data-package-price="<?php echo $package['price']; ?>"
                                        data-package-capacity="<?php echo $package['capacity']; ?>">
                                    <i class="fas fa-calendar-check me-2"></i>Reserve
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-ban me-2"></i>Currently Unavailable
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Notes Section -->
            <div class="notes-section">
                <h5><i class="fas fa-info-circle me-2"></i>NOTES:</h5>
                <ul class="list-unstyled">
                    <li>Exclusive use of café dining area for 4 hours</li>
                    <li>Corkage fee charges on each outside purchase (food, cake, beverage, and alcoholic drinks)</li>
                    <li>50% partial payment on selected package upon one-week reservation is non-refundable</li>
                </ul>
                <p class="text-danger mt-3">
                    <i class="fas fa-exclamation-circle me-2"></i>* This package is only available until 2:00 PM<br>
                    <i class="fas fa-exclamation-circle me-2"></i>** Assumes 3,000g (100g per person) of Wagyu Steak will be served
                </p>
            </div>
        </div>
    </div>

    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-list me-2"></i>Menu Selection
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add your menu content here -->
                    <div class="menu-categories mb-4">
                        <h6 class="text-warning mb-3">Categories</h6>
                        <div class="btn-group mb-3" role="group">
                            <button type="button" class="btn btn-outline-warning active" data-category="all">All</button>
                            <button type="button" class="btn btn-outline-warning" data-category="appetizers">Appetizers</button>
                            <button type="button" class="btn btn-outline-warning" data-category="main">Main Course</button>
                            <button type="button" class="btn btn-outline-warning" data-category="desserts">Desserts</button>
                            <button type="button" class="btn btn-outline-warning" data-category="beverages">Beverages</button>
                            </div>
                        </div>

                    <div class="menu-items">
                        <!-- Menu items will be loaded here -->
                        <p class="text-center text-muted">Loading menu items...</p>
                            </div>
                            </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <strong>Total Order: </strong>
                            <span id="totalOrderAmount">₱0.00</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning" id="confirmOrder">Confirm Order</button>
                            </div>
                            </div>
                        </div>
                            </div>
                            </div>
                        </div>

    <!-- Menu Items Modal -->
    <div class="modal fade" id="menuItemsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Select Menu Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                <div class="modal-body">
                    <!-- Category Filter -->
                    <div class="category-filter mb-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-warning active" data-category="all">All</button>
                            <?php foreach ($menuByCategory as $category => $items): ?>
                                <button type="button" class="btn btn-outline-warning" 
                                        data-category="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </button>
                            <?php endforeach; ?>
                            </div>
                        </div>

                    <div class="row">
                        <!-- Menu Items Section -->
                        <div class="col-md-8">
                            <div class="menu-items-container">
                                <?php foreach ($menuByCategory as $category => $items): ?>
                                    <div class="category-section mb-4" data-category="<?php echo htmlspecialchars($category); ?>">
                                        <h5 class="category-title mb-3"><?php echo htmlspecialchars($category); ?></h5>
                                        <div class="row g-3">
                                            <?php foreach ($items as $item): ?>
                                                <div class="col-md-6">
                                                    <div class="card menu-item">
                                                        <div class="row g-0">
                                                            <div class="col-4">
                                                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                                     class="img-fluid rounded-start" 
                                                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                                     style="height: 100px; object-fit: cover;">
                                                            </div>
                                                            <div class="col-8">
                                    <div class="card-body">
                                                                    <h6 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                                    <p class="card-text text-success mb-2">₱<?php echo number_format($item['price'], 2); ?></p>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <button type="button" 
                                                                                class="btn btn-warning btn-sm add-to-cart"
                                                                                data-item-id="<?php echo $item['id']; ?>"
                                                                                data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                                                data-item-price="<?php echo $item['price']; ?>">
                                                                            <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-outline-warning btn-sm show-addons"
                                                                                data-item-id="<?php echo $item['id']; ?>">
                                                                            Add-ons
                                                                        </button>
                                            </div>
                                                </div>
                                                </div>
                                                </div>
                                            </div>
                                                </div>
                                            <?php endforeach; ?>
                                                </div>
                                            </div>
                                <?php endforeach; ?>
                                            </div>
                                        </div>

                        <!-- Order Summary Section -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Order Summary</h6>
                                    </div>
                                <div class="card-body">
                                    <div id="cartItems">
                                        <div class="text-muted text-center" id="emptyCartMessage">
                                            No items in cart
                                </div>
                            </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total:</span>
                                        <span id="cartTotal">₱0.00</span>
                        </div>
                            </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-warning" id="confirmMenuItems">
                        Confirm Orders <span id="cartItemCount" class="badge bg-light text-dark ms-2">0</span>
                    </button>
                </div>
            </div>
                            </div>
                        </div>

    <!-- Update the add-ons modal -->
    <div class="modal fade" id="addonsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Select Add-ons</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="addonsList">
                        <?php foreach ($menuAddons as $addon): ?>
                            <div class="addon-item mb-3">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input addon-checkbox" 
                                                           type="checkbox" 
                                                           value="<?php echo $addon['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($addon['name']); ?>"
                                                           data-price="<?php echo $addon['price']; ?>"
                                                           id="addon<?php echo $addon['id']; ?>">
                                                    <label class="form-check-label ms-2" for="addon<?php echo $addon['id']; ?>">
                                                        <span class="addon-name"><?php echo htmlspecialchars($addon['name']); ?></span>
                                    </label>
                                </div>
                            </div>
                                            <span class="addon-price text-success fw-bold">₱<?php echo number_format($addon['price'], 2); ?></span>
                        </div>
                            </div>
                        </div>
                </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-warning" id="confirmAddons">Add Selected</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the Package Details Modal -->
    <div class="modal fade" id="packageDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Package Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="package-details">
                        <!-- Package Info -->
                            <div class="mb-4">
                                <h4 id="detailsPackageName" class="text-warning mb-0"></h4>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="mb-2">Capacity:</h6>
                                <p id="detailsCapacity" class="text-muted"></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="mb-2">Description:</h6>
                                <p id="detailsDescription" class="text-muted"></p>
                            </div>
                        </div>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning reserve-package-btn">
                        <i class="fas fa-calendar-check me-2"></i>Reserve Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the reservation modal content for ultimate packages -->
    <div class="modal fade" id="reservationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-calendar-check me-2"></i>Package Reservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reservationForm">
                        <input type="hidden" id="packageId" name="package_id">
                        <input type="hidden" id="basePrice" name="base_price">
                        <input type="hidden" id="isUltimatePackage" name="is_ultimate" value="0">
                        <input type="hidden" id="packageName" name="package_name">
                        
                        <!-- Package Info -->
                        <div class="mb-4">
                            <h6 class="package-name mb-2"></h6>
                    </div>

                        <!-- Date and Guests -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" id="reservationDate" name="date" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" class="form-control" id="guestCount" name="guest_count" 
                                       min="1" required>
                                <small class="text-muted">Max: <span id="maxGuests"></span></small>
        </div>
    </div>

                        <!-- Time Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Arrival Time</label>
                                <input type="time" 
                                       class="form-control" 
                                       id="arrivalTime" 
                                       name="arrival_time" 
                                       min="06:30"
                                       max="23:00"
                                       required>
                                <small class="text-muted">Operating hours: 6:30 AM - 11:00 PM</small>
                </div>
                            <div class="col-md-6" id="durationSection" style="display: none;">
                                <label class="form-label">Duration</label>
                                <select class="form-select" id="duration" name="duration">
                                    <option value="4">4 hours (Standard)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Payment Section (Only visible for Ultimate Package) -->
                        <div id="ultimatePaymentSection" style="display: none;">
                            <hr class="my-4">
                            <h6 class="mb-3">Payment Details</h6>
                            
                            <!-- Payment Option -->
                            <div class="mb-3">
                                <label class="form-label">Payment Option</label>
                                <select class="form-select" id="paymentOption" name="payment_option" required>
                                    <option value="">Select Payment Option</option>
                                    <option value="full">Full Payment</option>
                                    <option value="partial">50% Partial Payment</option>
                                </select>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <?php foreach ($paymentMethods as $method): ?>
                                    <option value="<?php echo htmlspecialchars($method['name']); ?> "><?php echo htmlspecialchars($method['display_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Payment Details Display -->
                            <div id="paymentDetails" class="card mb-3" style="display: none;">
                                <div class="card-body">

                                </div>
                            </div>

                            <!-- Payment Proof Upload -->
                            <div class="mb-3" id="paymentProofSection" style="display: none;">
                                <label class="form-label">Payment Proof</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="paymentProof" name="payment_proof" 
                                           accept="image/*">
                                    <label class="input-group-text" for="paymentProof">
                                        <i class="fas fa-upload"></i>
                                    </label>
                                </div>
                                <div id="paymentPreview" class="mt-2" style="display: none;">
                                    <img src="" alt="Payment Proof Preview" class="img-fluid" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeProof">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <small class="text-muted">Please upload a screenshot or photo of your payment</small>
                            </div>

                            <!-- Payment Reference -->
                            <div class="mb-3" id="referenceNumberSection" style="display: none;">
                                <label class="form-label">Payment Reference Number</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="referenceNumber" 
                                       name="reference_number"
                                       placeholder="Enter reference number"
                                       >
                                <small class="text-muted reference-format">
                                    GCash: 13 digits | Maya: 12 characters (letters and numbers)
                                </small>
                            </div>
                        </div>

                        <!-- Add this before the form's closing tag in the reservation modal -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="policyCheck" required>
                                <label class="form-check-label" for="policyCheck">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#policyModal">Terms & Policies</a>
                                </label>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmReservation">
                        Confirm Reservation
                    </button>
                        </div>
                    </div>
                </div>
            </div>

    <!-- First, add the advance order confirmation modal -->
    <div class="modal fade" id="advanceOrderConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Advance Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you want to add advance orders?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="noAdvanceOrder">No</button>
                    <button type="button" class="btn btn-warning" id="yesAdvanceOrder">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this new modal for booking summary -->
    <div class="modal fade" id="bookingSummaryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-clipboard-check me-2"></i>Booking Summary
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="booking-summary">
                        <h6 class="mb-3">Reservation Details</h6>
                        <p><strong>Package:</strong> <span id="summaryPackage"></span></p>
                        <p><strong>Date:</strong> <span id="summaryDate"></span></p>
                        <p><strong>Time:</strong> <span id="summaryTime"></span></p>
                        <p><strong>Duration:</strong> <span id="summaryDuration"></span></p>
                        <p><strong>Number of Guests:</strong> <span id="summaryGuests"></span></p>
                        
                        <hr>
                        <h6 class="mb-3">Cost Breakdown</h6>
                        <p><strong>Base Price:</strong> <span id="summaryBasePrice"></span></p>
                        <div id="summaryExtraHours" style="display: none;">
                            <p><strong>Extra Hours:</strong> <span id="summaryHoursCost"></span></p>
                    </div>
                        <div id="summaryExtraGuests" style="display: none;">
                            <p><strong>Extra Guests:</strong> <span id="summaryGuestsCost"></span></p>
                        </div>
                        <p class="fw-bold text-success">Total Amount: <span id="summaryTotal"></span></p>
                        
                        <div id="summaryPayment"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Make Changes</button>
                    <button type="button" class="btn btn-warning" id="confirmBookingBtn">
                        <i class="fas fa-check me-2"></i>Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this after the existing modals -->
    <div class="modal fade" id="ultimatePackageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-star me-2"></i>Intimate Package Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="package-details">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 id="ultimatePackageName" class="text-warning mb-3"></h4>
                                <div class="mb-3">
                                    <h6 class="mb-2">Capacity:</h6>
                                    <p id="ultimateCapacity" class="text-muted"></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="mb-2">Package Includes:</h6>
                                    <ul class="package-inclusions">
                                        <li>Exclusive use of café dining area for 4 hours</li>
                                        <li>Complete table setup and decorations</li>
                                        <li>Dedicated service staff</li>
                                        <li>Premium dining experience</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update the menu section in the ultimate package modal -->
                        <div class="mt-4">
                            <h6 class="mb-2">Menu Selection:</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Included Menu Items</h6>
                                            <ul id="ultimateMenuItems" class="list-unstyled menu-list">
                                                <!-- Menu items will be populated here -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Important Notes:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Package is only available until 2:00 PM</li>
                                    <li>Corkage fee applies for outside food and beverages</li>
                                    <li>50% non-refundable deposit required for reservation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" id="ultimateReserveBtn">
                        <i class="fas fa-calendar-check me-2"></i>Make Reservation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal for displaying package images in full size -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="imageModalLabel">Package Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="Package image" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Image Modal for displaying package images in full size -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="imageModalLabel">Package Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="Package image" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add this new modal for policies -->
    <div class="modal fade" id="policyModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-file-contract me-2"></i>Reservation Policies
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="policy-content">
                        <h5>Reservation Terms</h5>
                        <ul>
                            <li>Reservations are confirmed only upon payment of the required amount.</li>
                            <li>For intimate packages, 50% downpayment is non-refundable.</li>
                            <li>Remaining balance must be paid on the day of the event.</li>
                            <li>Changes to the reservation must be made at least 48 hours before the event.</li>
                        </ul>

                        <h5>Cancellation Policy</h5>
                        <ul>
                            <li>Cancellations made 7 days before the event: 80% refund</li>
                            <li>Cancellations made 3-6 days before: 50% refund</li>
                            <li>Cancellations made less than 48 hours: No refund</li>
                        </ul>

                        <h5>Usage Guidelines</h5>
                        <ul>
                            <li>Maximum duration for standard reservation is 4 hours</li>
                            <li>Additional hours will be charged ₱2,000 per hour</li>
                            <li>Corkage fees apply for outside food and beverages</li>
                            <li>Damage to café property will be charged accordingly</li>
                        </ul>

                        <h5>Other Terms</h5>
                        <ul>
                            <li>The café reserves the right to refuse service to anyone</li>
                            <li>Prices and policies are subject to change without prior notice</li>
                            <li>By making a reservation, you agree to all terms and conditions</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="package_image_modal.js"></script>
    <script>
        // Add this helper function at the start of your JavaScript
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Add this function at the start of your JavaScript code
        function closeAllModals() {
            const modals = ['reservationModal', 'policyModal', 'bookingSummaryModal', 'ultimatePackageModal'];
            modals.forEach(modalId => {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }

        // Function to display package image in modal
        function displayPackageImage(imageUrl, title = 'Package Image') {
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');
            
            // Set the image source and title
            modalImage.src = imageUrl;
            modalTitle.textContent = title;
            
            // Show the modal
            imageModal.show();
        }
        
        // Add click event listener to package images
        document.addEventListener('click', function(event) {
            const target = event.target;
            
            // Check if the clicked element is a package image
            if (target.tagName === 'IMG' && target.classList.contains('package-image')) {
                event.preventDefault();
                displayPackageImage(target.src, target.alt || 'Package Image');
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Update the reservation modal handler
            const reservationModal = document.getElementById('reservationModal');
            if (reservationModal) {
                reservationModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const packageId = button.getAttribute('data-package-id');
                    const packageName = button.getAttribute('data-package-name');
                    const packageCapacity = button.getAttribute('data-package-capacity');
                    const packagePrice = button.getAttribute('data-package-price');
                    
                    // Set form values
                    document.getElementById('packageId').value = packageId;
                    document.getElementById('packageName').value = packageName;
                    document.getElementById('basePrice').value = packagePrice;
                    document.querySelectorAll('.package-name').forEach(el => el.textContent = packageName);
                    
                    // Set max guests
                    document.getElementById('maxGuests').textContent = packageCapacity;
                    document.getElementById('guestCount').max = packageCapacity;

                    // Show/hide sections based on package type
                    const isUltimate = parseInt(packageCapacity) >= 30;
                    document.getElementById('isUltimatePackage').value = isUltimate ? "1" : "0";
                    document.getElementById('ultimatePaymentSection').style.display = isUltimate ? "block" : "none";
                    document.getElementById('durationSection').style.display = isUltimate ? "block" : "none";

                    // Set fixed duration for regular packages
                    if (!isUltimate) {
                        document.getElementById('duration').value = '4';
                    }
                });
            }

            // Handle payment method change
            document.getElementById('paymentMethod').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const paymentDetails = document.getElementById('paymentDetails');
                const referenceSection = document.getElementById('referenceNumberSection');
                const proofSection = document.getElementById('paymentProofSection');
                
                if (this.value) {
                    // Update payment details
                    document.getElementById('qrCode').src = selectedOption.dataset.qr;
                    document.getElementById('accountName').textContent = selectedOption.dataset.name;
                    document.getElementById('accountNumber').textContent = selectedOption.dataset.account;
                    
                    // Show payment details, reference number section, and proof upload
                    paymentDetails.style.display = 'block';
                    referenceSection.style.display = 'block';
                    proofSection.style.display = 'block';
                } else {
                    paymentDetails.style.display = 'none';
                    referenceSection.style.display = 'none';
                    proofSection.style.display = 'none';
                }
            });

            // Handle payment option change
            document.getElementById('paymentOption').addEventListener('change', function() {
                updateTotalAmount();
            });

            // Add duration change handler
            document.getElementById('duration').addEventListener('change', function() {
                updateTotalAmount();
            });

            // Update the updateTotalAmount function to include extra guest charges
            function updateTotalAmount() {
                const basePrice = parseFloat(document.getElementById('basePrice').value);
                const isUltimate = document.getElementById('isUltimatePackage').value === "1";
                const paymentOption = document.getElementById('paymentOption').value;
                const amountDisplay = document.getElementById('amountToPay');
                
                let totalPrice = basePrice;
                let extraHoursCost = 0;
                let extraGuestsCost = 0;

                // Calculate extra costs only for ultimate packages
                if (isUltimate) {
                    // Calculate extra hours cost
                    const duration = parseInt(document.getElementById('duration').value);
                    const extraHours = Math.max(0, duration - 4);
                    extraHoursCost = extraHours * 2000;

                    // Calculate extra guest charges
                    const capacity = parseInt(document.getElementById('maxGuests').textContent);
                    const actualGuests = parseInt(document.getElementById('guestCount').value) || 0;
                    const extraGuests = Math.max(0, actualGuests - capacity);
                    extraGuestsCost = extraGuests * 1000;

                    totalPrice = basePrice + extraHoursCost + extraGuestsCost;
                }
                
                if (paymentOption === 'partial') {
                    const partialAmount = totalPrice * 0.5;
                    const remainingBalance = totalPrice - partialAmount;
                    amountDisplay.innerHTML = `
                        <div class="mb-2">Base Price: ₱${formatNumber(basePrice.toFixed(2))}</div>
                        ${isUltimate && extraHoursCost > 0 ? `<div class="mb-2">Extra Hours: ₱${formatNumber(extraHoursCost.toFixed(2))}</div>` : ''}
                        ${isUltimate && extraGuestsCost > 0 ? `<div class="mb-2">Extra Guests: ₱${formatNumber(extraGuestsCost.toFixed(2))}</div>` : ''}
                        <div class="mb-2">Total Price: ₱${formatNumber(totalPrice.toFixed(2))}</div>
                        <hr>
                        <div class="text-warning fw-bold">Required Downpayment: ₱${formatNumber(partialAmount.toFixed(2))}</div>
                        <div class="text-danger">Remaining Balance: ₱${formatNumber(remainingBalance.toFixed(2))}</div>
                        <small class="text-muted d-block mt-2">* Remaining balance to be paid on the day of the event</small>
                    `;
                } else if (paymentOption === 'full') {
                    amountDisplay.innerHTML = `
                        <div class="mb-2">Base Price: ₱${formatNumber(basePrice.toFixed(2))}</div>
                        ${isUltimate && extraHoursCost > 0 ? `<div class="mb-2">Extra Hours: ₱${formatNumber(extraHoursCost.toFixed(2))}</div>` : ''}
                        ${isUltimate && extraGuestsCost > 0 ? `<div class="mb-2">Extra Guests: ₱${formatNumber(extraGuestsCost.toFixed(2))}</div>` : ''}
                        <hr>
                        <div class="fw-bold text-success">Total Amount: ₱${formatNumber(totalPrice.toFixed(2))}</div>
                    `;
                }
            }

            // Update the guest count change handler
            document.getElementById('guestCount').addEventListener('input', function() {
                const capacity = parseInt(document.getElementById('maxGuests').textContent);
                const guests = parseInt(this.value) || 0;
                const isUltimate = document.getElementById('isUltimatePackage').value === "1";
                
                if (isUltimate && guests > capacity) {
                    // Show warning about extra charges only for ultimate packages
                    Swal.fire({
                        icon: 'info',
                        title: 'Extra Guest Charges',
                        text: `Additional charge of ₱1,000 per person will apply for guests exceeding the package capacity of ${capacity}.`,
                        confirmButtonColor: '#b6860a'
                    });
                } else if (!isUltimate && guests > capacity) {
                    // Show warning about capacity limit for regular packages
                    Swal.fire({
                        icon: 'warning',
                        title: 'Capacity Limit Reached',
                        text: `This package has a maximum capacity of ${capacity} guests.`,
                        confirmButtonColor: '#b6860a'
                    });
                    this.value = capacity; // Reset to maximum capacity
                }
                
                // Update total amount if payment option is selected
                if (document.getElementById('paymentOption').value) {
                    updateTotalAmount();
                }
            });

            // Handle payment proof upload
            const paymentProof = document.getElementById('paymentProof');
            const paymentPreview = document.getElementById('paymentPreview');
            const previewImage = paymentPreview.querySelector('img');
            const removeProofBtn = document.getElementById('removeProof');

            paymentProof.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const reader = new FileReader();

                    // Check file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Please select an image under 5MB',
                            confirmButtonColor: '#b6860a'
                        });
                        this.value = '';
                        return;
                    }

                    // Check file type
                    if (!file.type.startsWith('image/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type',
                            text: 'Please select an image file',
                            confirmButtonColor: '#b6860a'
                        });
                        this.value = '';
                        return;
                    }

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        paymentPreview.style.display = 'block';
                    };

                    reader.readAsDataURL(file);
                }
            });

            removeProofBtn.addEventListener('click', function() {
                paymentProof.value = '';
                paymentPreview.style.display = 'none';
                previewImage.src = '';
            });

            // Update the confirm reservation button click handler
            document.getElementById('confirmReservation').addEventListener('click', function() {
                const form = document.getElementById('reservationForm');
                const isUltimate = document.getElementById('isUltimatePackage').value === "1";
                const policyCheck = document.getElementById('policyCheck');
                
                // Validate policy check
                if (!policyCheck.checked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Policy Agreement Required',
                        text: 'Please read and agree to our policies before proceeding',
                        confirmButtonColor: '#b6860a'
                    });
                    return;
                }


                // Get all reservation details
            const packageName = document.querySelector('.package-name').textContent;
            const date = document.getElementById('reservationDate').value;
            const time = document.getElementById('arrivalTime').value;
            const duration = document.getElementById('duration').value;
            const guests = document.getElementById('guestCount').value;
                const basePrice = parseFloat(document.getElementById('basePrice').value);
                
                // Calculate extra costs
                const extraHours = Math.max(0, parseInt(duration) - 4);
                const extraHoursCost = extraHours * 2000;
                
                const capacity = parseInt(document.getElementById('maxGuests').textContent);
                const extraGuests = Math.max(0, parseInt(guests) - capacity);
                const extraGuestsCost = extraGuests * 1000;
                
                const totalPrice = basePrice + extraHoursCost + extraGuestsCost;
                
                // Get payment details for ultimate packages
                let paymentDetails = '';
                if (isUltimate) {
                    const paymentOption = document.getElementById('paymentOption').value;
                    const paymentMethod = document.getElementById('paymentMethod').options[document.getElementById('paymentMethod').selectedIndex].text;
                    const referenceNumber = document.getElementById('referenceNumber').value;
                    
                    const partialAmount = totalPrice * 0.5;
                    const remainingBalance = totalPrice - partialAmount;
                    
                    paymentDetails = `
                        <hr>
                        <h6 class="mb-3">Payment Information</h6>
                        <p><strong>Payment Option:</strong> ${paymentOption === 'full' ? 'Full Payment' : '50% Partial Payment'}</p>
                        <p><strong>Payment Method:</strong> ${paymentMethod}</p>
                        <p><strong>Reference Number:</strong> ${referenceNumber}</p>
                        <p><strong>Amount Paid:</strong> ₱${formatNumber(paymentOption === 'full' ? totalPrice.toFixed(2) : partialAmount.toFixed(2))}</p>
                        ${paymentOption === 'partial' ? `<p class="text-danger"><strong>Remaining Balance:</strong> ₱${formatNumber(remainingBalance.toFixed(2))}</p>` : ''}
                    `;
                }

                // Format date and time
                const formattedDate = new Date(date).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                const formattedTime = new Date(`2000/01/01 ${time}`).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true
                });

                // Update summary modal content
            document.getElementById('summaryPackage').textContent = packageName;
                document.getElementById('summaryDate').textContent = formattedDate;
                document.getElementById('summaryTime').textContent = formattedTime;
                document.getElementById('summaryDuration').textContent = `${duration} hours`;
            document.getElementById('summaryGuests').textContent = guests;
                document.getElementById('summaryBasePrice').textContent = `₱${formatNumber(basePrice.toFixed(2))}`;
                
                // Handle extra hours
                const extraHoursDiv = document.getElementById('summaryExtraHours');
                const extraHoursCostSpan = document.getElementById('summaryHoursCost');
                if (extraHours > 0) {
                    extraHoursDiv.style.display = 'block';
                    extraHoursCostSpan.textContent = `₱${formatNumber(extraHoursCost.toFixed(2))} (${extraHours} hours)`;
            } else {
                    extraHoursDiv.style.display = 'none';
                }
                
                // Handle extra guests
                const extraGuestsDiv = document.getElementById('summaryExtraGuests');
                const extraGuestsCostSpan = document.getElementById('summaryGuestsCost');
                if (extraGuests > 0) {
                    extraGuestsDiv.style.display = 'block';
                    extraGuestsCostSpan.textContent = `₱${formatNumber(extraGuestsCost.toFixed(2))} (${extraGuests} guests)`;
            } else {
                    extraGuestsDiv.style.display = 'none';
                }
                
                document.getElementById('summaryTotal').textContent = `₱${formatNumber(totalPrice.toFixed(2))}`;
                
                // Handle payment details for ultimate packages
                const paymentSummary = document.getElementById('summaryPayment');
                if (isUltimate) {
                    const paymentOption = document.getElementById('paymentOption').value;
                    const paymentMethod = document.getElementById('paymentMethod').options[document.getElementById('paymentMethod').selectedIndex].text;
                    const referenceNumber = document.getElementById('referenceNumber').value;
                    const partialAmount = totalPrice * 0.5;
                    const remainingBalance = totalPrice - partialAmount;
                    
                    paymentSummary.innerHTML = `
                        <hr>
                        <h6 class="mb-3">Payment Information</h6>
                        <p><strong>Payment Option:</strong> ${paymentOption === 'full' ? 'Full Payment' : '50% Partial Payment'}</p>
                        <p><strong>Payment Method:</strong> ${paymentMethod}</p>
                        <p><strong>Reference Number:</strong> ${referenceNumber}</p>
                        <p><strong>Amount Paid:</strong> ₱${formatNumber(paymentOption === 'full' ? totalPrice.toFixed(2) : partialAmount.toFixed(2))}</p>
                        ${paymentOption === 'partial' ? `<p class="text-danger"><strong>Remaining Balance:</strong> ₱${formatNumber(remainingBalance.toFixed(2))}</p>` : ''}
                    `;
                    
                    // For ultimate packages, go straight to booking summary
                    closeAllModals();
                    const summaryModal = new bootstrap.Modal(document.getElementById('bookingSummaryModal'));
                    summaryModal.show();
            } else {
                    paymentSummary.innerHTML = '';

                    // For regular packages, show advance order confirmation dialog
                closeAllModals();

                // Show advance order confirmation dialog
                Swal.fire({
                    title: 'Advance Order',
                    text: 'Do you want to make an advance order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, make advance order',
                    cancelButtonText: 'No, continue to summary',
                    confirmButtonColor: '#b6860a',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Store booking details in session storage
                        const bookingDetails = {
                            packageId: document.getElementById('packageId').value,
                            packageName: document.querySelector('.package-name').textContent,
                            date: document.getElementById('reservationDate').value,
                            time: document.getElementById('arrivalTime').value,
                            duration: document.getElementById('duration').value,
                            guests: document.getElementById('guestCount').value,
                            isUltimate: "0",
                                basePrice: document.getElementById('basePrice').value,
                                totalAmount: totalPrice
                        };

                        // Store booking details
                        sessionStorage.setItem('pendingBooking', JSON.stringify(bookingDetails));
                        
                            // Redirect to cafe page with booking details in URL parameters
                            const params = new URLSearchParams({
                                booking: 'true',
                                package_id: bookingDetails.packageId,
                                package_name: bookingDetails.packageName,
                                date: bookingDetails.date,
                                time: bookingDetails.time,
                                guests: bookingDetails.guests,
                                total: bookingDetails.totalAmount
                            });
                            window.location.href = 'cafes.php?' + params.toString();
            } else {
                        // Continue with showing booking summary
                        closeAllModals();

                        // Show the summary modal
                        const summaryModal = new bootstrap.Modal(document.getElementById('bookingSummaryModal'));
                        summaryModal.show();
                    }
                });
                }
            });

            // Update the policy modal link handler
            document.querySelector('[data-bs-target="#policyModal"]').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Close all modals before showing policy modal
                closeAllModals();
                
                // Show policy modal
                const policyModal = new bootstrap.Modal(document.getElementById('policyModal'));
                policyModal.show();
            });

            // Update the policy modal's "I Understand" button
            document.querySelector('#policyModal .btn-warning').addEventListener('click', function() {
                // Close policy modal
                closeAllModals();
                
                // Reopen reservation modal
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            reservationModal.show();
        });

            // Add time validation
            const arrivalTimeInput = document.getElementById('arrivalTime');
            arrivalTimeInput.addEventListener('change', function() {
                const selectedTime = this.value;
                if (!selectedTime) return; // Don't validate if empty

                const [hours, minutes] = selectedTime.split(':');
                const selectedDateTime = new Date();
                selectedDateTime.setHours(parseInt(hours), parseInt(minutes));

                const openingTime = new Date();
                openingTime.setHours(6, 30); // 6:30 AM

                const closingTime = new Date();
                closingTime.setHours(23, 0); // 11:00 PM

                // Store the current value
                const currentValue = this.value;

                // Check if selected time is within operating hours
                if (selectedDateTime < openingTime || selectedDateTime > closingTime) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time',
                        text: 'Please select a time between 6:30 AM and 11:00 PM',
                        confirmButtonColor: '#b6860a'
                    }).then(() => {
                        // Focus back on the input after the alert is closed
                        this.focus();
                    });
                    return;
                }

                // Check if selected time plus duration exceeds closing time
                const duration = parseInt(document.getElementById('duration').value);
                const endDateTime = new Date(selectedDateTime);
                endDateTime.setHours(endDateTime.getHours() + duration);

                if (endDateTime > closingTime) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Duration',
                        text: 'Your reservation would exceed our closing time (11:00 PM). Please select an earlier time or shorter duration.',
                        confirmButtonColor: '#b6860a'
                    }).then(() => {
                        // Focus back on the input after the alert is closed
                        this.focus();
                    });
                    return;
                }
            });

            // Add duration change validation
            document.getElementById('duration').addEventListener('change', function() {
                const arrivalTime = document.getElementById('arrivalTime').value;
                if (arrivalTime) {
                    const [hours, minutes] = arrivalTime.split(':');
                    const startDateTime = new Date();
                    startDateTime.setHours(parseInt(hours), parseInt(minutes));

                    const duration = parseInt(this.value);
                    const endDateTime = new Date(startDateTime);
                    endDateTime.setHours(endDateTime.getHours() + duration);

                    const closingTime = new Date();
                    closingTime.setHours(23, 0); // 11:00 PM

                    if (endDateTime > closingTime) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Duration',
                            text: 'Your reservation would exceed our closing time (11:00 PM). Please select an earlier time or shorter duration.',
                            confirmButtonColor: '#b6860a'
                        });
                        this.value = '4'; // Reset to standard duration
                        updateTotalAmount(); // Update the total amount
                        return;
                }
            }
        });

            // Update the confirm booking button handler
            document.getElementById('confirmBookingBtn').addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission
                const form = document.getElementById('reservationForm');
                const formData = new FormData(form);
                const params = new URLSearchParams();
                
                // Get package name from the page
                const packageName = document.querySelector('.package-name')?.textContent;
                if (packageName) {
                    params.append('package_name', packageName);
                }
                
                // Add all form data to URL parameters
                for (let [key, value] of formData.entries()) {
                    // Skip file inputs as they can't be sent via URL
                    if (key !== 'payment_proof' && value) {
                        params.append(encodeURIComponent(key), encodeURIComponent(value));
                    }
                }
                
                // Validate required fields
                const requiredFields = ['package_id', 'date', 'guest_count', 'arrival_time'];
                const missingFields = [];
                requiredFields.forEach(field => {
                    if (!formData.get(field)) {
                        missingFields.push(field.replace('_', ' '));
                    }
                });
                
                if (missingFields.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: `Please fill in the following fields: ${missingFields.join(', ')}`,
                        confirmButtonColor: '#b6860a'
                    });
                    return;
                }
                
                // Get the base URL and add parameters
                const baseUrl = 'table_payment_process.php';
                const url = `${baseUrl}?${params.toString()}`;
                
                // Redirect to payment page with URL parameters
                window.location.href = url;
            });

            // Update the "Make Changes" button in booking summary
            document.querySelector('#bookingSummaryModal .btn-secondary').addEventListener('click', function() {
                // Close summary modal
                closeAllModals();
                
                // Reopen reservation modal
                const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
                reservationModal.show();
            });

            // Add this in the DOMContentLoaded event listener
            const packageDetailsModal = document.getElementById('packageDetailsModal');
            if (packageDetailsModal) {
                packageDetailsModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    
                    // Get data from button attributes
                    const packageName = button.getAttribute('data-package-name');
                    const description = button.getAttribute('data-package-description');
                    const capacity = button.getAttribute('data-package-capacity');
                    const tables = button.getAttribute('data-package-tables');
                    const imagePath = button.getAttribute('data-package-image');
                    const price = button.getAttribute('data-package-price');

                    // Update modal content
                    document.getElementById('detailsImage').src = imagePath;
                    document.getElementById('detailsPackageName').textContent = packageName;
                    document.getElementById('detailsCapacity').textContent = `${capacity} persons`;
                    document.getElementById('detailsTables').textContent = `${tables} tables`;
                    document.getElementById('detailsDescription').textContent = description;
                    
                    // Format and display price
                    const formattedPrice = new Intl.NumberFormat('en-PH', {
                        style: 'currency',
                        currency: 'PHP'
                    }).format(price);
                    document.getElementById('detailsPrice').textContent = formattedPrice;
                });
            }
        });

        // Update the ultimate reserve button handler
        document.getElementById('ultimateReserveBtn').addEventListener('click', function() {
            // Close ultimate package modal
            closeAllModals();
            
            // Show reservation modal
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            reservationModal.show();
        });

        // Add this validation function
        function validateReferenceNumber(method, number) {
            if (method === 'GCash') {
                // GCash: 13 digits
                return /^\d{13}$/.test(number);
            } else if (method === 'Maya') {
                // Maya: 12 characters (letters and numbers)
                return /^[A-Za-z0-9]{12}$/.test(number);
            }
            return true; // For other payment methods
        }

        // Update the payment method change handler
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const selectedMethod = this.options[this.selectedIndex].text;
            const referenceInput = document.getElementById('referenceNumber');
            
            // Update placeholder and pattern based on payment method
            if (selectedMethod === 'GCash') {
                referenceInput.placeholder = '13-digit reference number';
                referenceInput.pattern = '\\d{13}';
                referenceInput.maxLength = 13;
            } else if (selectedMethod === 'Maya') {
                referenceInput.placeholder = '12-character reference number';
                referenceInput.pattern = '[A-Za-z0-9]{12}';
                referenceInput.maxLength = 12;
            }
        });

        // Add reference number input validation
        document.getElementById('referenceNumber').addEventListener('input', function() {
            const selectedMethod = document.getElementById('paymentMethod').options[document.getElementById('paymentMethod').selectedIndex].text;
            const referenceNumber = this.value;
            
            if (selectedMethod === 'GCash') {
                // Only allow digits for GCash
                this.value = this.value.replace(/[^\d]/g, '');
            } else if (selectedMethod === 'Maya') {
                // Allow letters and numbers for Maya, convert to uppercase
                this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
            }
        });

        // Update the confirm reservation button handler to include reference number validation
        document.getElementById('confirmReservation').addEventListener('click', function() {
            // ... existing validation code ...

            if (isUltimate) {
                const paymentMethod = document.getElementById('paymentMethod').options[document.getElementById('paymentMethod').selectedIndex].text;
                const referenceNumber = document.getElementById('referenceNumber').value;

                if (!validateReferenceNumber(paymentMethod, referenceNumber)) {
                    let errorMessage = '';
                    if (paymentMethod === 'GCash') {
                        errorMessage = 'Please enter a valid 13-digit GCash reference number';
                    } else if (paymentMethod === 'Maya') {
                        errorMessage = 'Please enter a valid 12-character Maya reference number';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Reference Number',
                        text: errorMessage,
                        confirmButtonColor: '#b6860a'
                    });
                    return;
                }
            }

            // ... rest of the confirmation code ...
        });

        // Update the modal event handler for ultimate package
        const ultimatePackageModal = document.getElementById('ultimatePackageModal');
        if (ultimatePackageModal) {
            ultimatePackageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                // Get data from button attributes
                const packageName = button.getAttribute('data-package-name');
                const description = button.getAttribute('data-package-description');
                const capacity = button.getAttribute('data-package-capacity');
                const imagePath = button.getAttribute('data-package-image');
                const price = button.getAttribute('data-package-price');
                const menuItems = button.getAttribute('data-package-menu-items');
                
                console.log('Menu items from data attribute:', menuItems); // Debug log
                
                // Update package info
                document.getElementById('ultimatePackageName').textContent = packageName;
                document.getElementById('ultimateCapacity').textContent = capacity + ' persons';
                
                // Populate carousel with main image only
                const carouselInner = document.querySelector('#packageImageCarousel .carousel-inner');
                if (carouselInner) {
                    carouselInner.innerHTML = ''; // Clear existing items
                    
                    // Add main image as the only slide
                    if (imagePath) {
                        const mainSlide = document.createElement('div');
                        mainSlide.className = 'carousel-item active';
                        mainSlide.innerHTML = `<img src="../../uploads/table_packages/${imagePath}" class="d-block w-100" alt="${packageName}" style="height: 300px; object-fit: cover;">`;
                        carouselInner.appendChild(mainSlide);
                    }
                }


                // Update menu items display
                const menuItemsContainer = document.getElementById('ultimateMenuItems');
                if (menuItemsContainer) {
                    if (menuItems) {
                        try {
                            // Check if menuItems is a JSON string or a simple string
                            let menuItemsArray = [];
                            try {
                                // Try to parse as JSON first
                                const parsedMenu = JSON.parse(menuItems);
                                if (Array.isArray(parsedMenu)) {
                                    menuItemsArray = parsedMenu;
                                } else if (typeof parsedMenu === 'string') {
                                    // If it's a string, split by comma
                                    menuItemsArray = parsedMenu.split(',').map(item => item.trim());
                                }
                            } catch (e) {
                                // If not valid JSON, treat as comma-separated string
                                menuItemsArray = menuItems.split(',').map(item => item.trim());
                            }
                            
                            // Create list items
                            const menuList = menuItemsArray
                                .filter(item => item) // Remove empty items
                                .map(item => `<li class="menu-item"><i class="fas fa-utensils me-2"></i>${item}</li>`)
                                .join('');
                                
                            menuItemsContainer.innerHTML = menuList || '<li class="text-muted">No menu items specified for this package.</li>';
                        } catch (error) {
                            console.error('Error processing menu items:', error);
                            menuItemsContainer.innerHTML = '<li class="text-muted">Error loading menu items. Please contact support.</li>';
                        }
                    } else {
                        menuItemsContainer.innerHTML = '<li class="text-muted">No menu items available for this package.</li>';
                    }
                }
            });
        }

        // Update the handleReserveClick function
        function handleReserveClick(event) {
            event.preventDefault();
            
            // Make an AJAX call to check login status
            fetch('check_login.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.loggedIn) {
                        // Store current URL in session storage to redirect back after login
                        sessionStorage.setItem('redirectAfterLogin', window.location.href);
                        
                        // Show login required message
                        const loginMessage = document.createElement('div');
                        loginMessage.className = 'login-message';
                        loginMessage.innerHTML = `
                            <div class="login-required-popup">
                                <div class="login-content">
                                    <h4>Login Required</h4>
                                    <p>Please login to make a reservation</p>
                                    <div class="login-buttons">
                                        <button class="btn btn-warning" onclick="window.location.href='login.php'">Login Now</button>
                                        <button class="btn btn-secondary" onclick="closeLoginMessage()">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(loginMessage);
                    } else {
                        // User is logged in, proceed with reservation
                        const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
                        reservationModal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while checking login status');
                });
        }

        // Add function to close login message
        function closeLoginMessage() {
            const loginMessage = document.querySelector('.login-message');
            if (loginMessage) {
                loginMessage.remove();
            }
        }

        // Add CSS for the login message popup
        const style = document.createElement('style');
        style.textContent = `
            .login-message {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }

            .login-required-popup {
                background: white;
                padding: 2rem;
                border-radius: 10px;
                text-align: center;
                max-width: 400px;
                width: 90%;
            }

            .login-content h4 {
                color: #333;
                margin-bottom: 1rem;
            }

            .login-buttons {
                margin-top: 1.5rem;
                display: flex;
                gap: 1rem;
                justify-content: center;
            }

            .login-buttons .btn {
                padding: 0.5rem 1.5rem;
            }
        `;
        document.head.appendChild(style);

        // Handle package details modal
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all buttons that open the package details modal
            const detailButtons = document.querySelectorAll('[data-bs-target="#packageDetailsModal"]');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get package details from data attributes
                    const packageName = this.getAttribute('data-package-name');
                    const description = this.getAttribute('data-package-description');
                    const capacity = this.getAttribute('data-package-capacity');
                    
                    // Get main image path
                    const mainImage = this.getAttribute('data-package-image');
                    
                    // Update modal content
                    document.getElementById('detailsPackageName').textContent = packageName;
                    document.getElementById('detailsCapacity').textContent = capacity + ' persons';
                    document.getElementById('detailsDescription').textContent = description || 'No description available';
                    
                    // Clear existing carousel items
                    const carouselInner = document.querySelector('#packageDetailsCarousel .carousel-inner');
                    carouselInner.innerHTML = '';
                    
                    // Add main image to carousel
                    if (mainImage) {
                        const mainImageItem = document.createElement('div');
                        mainImageItem.className = 'carousel-item active';
                        mainImageItem.innerHTML = `<img src="../../uploads/table_packages/${mainImage}" class="d-block w-100" alt="${packageName}" style="height: 300px; object-fit: cover;">`;
                        carouselInner.appendChild(mainImageItem);
                    }
                    
                    // Show the modal
                    const packageModal = new bootstrap.Modal(document.getElementById('packageDetailsModal'));
                    packageModal.show();
                });
            });
            
            // Update all reserve buttons
            const reserveButtons = document.querySelectorAll('.reserve-btn');
            reserveButtons.forEach(button => {
                button.addEventListener('click', handleReserveClick);
            });

            // Update ultimate reserve button
            const ultimateReserveBtn = document.getElementById('ultimateReserveBtn');
            if (ultimateReserveBtn) {
                ultimateReserveBtn.removeEventListener('click', null); // Remove existing handler
                ultimateReserveBtn.addEventListener('click', handleReserveClick);
            }
        });
    </script>
</body>
</html>
