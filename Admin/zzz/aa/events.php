
<?php 
require_once 'db_con.php';
session_start();

// For backward compatibility, set $conn to the PDO instance if not already set
if (!isset($conn) && isset($pdo)) {
    $conn = $pdo;
}

// Debug: Check database connection
if (!isset($conn)) {
    die('Database connection not established');
}

// Debug: Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'event_packages'");
if ($tableCheck->rowCount() == 0) {
    die('event_packages table does not exist');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Celebrations - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="assets/css/events-tables.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Loading spinner styles */
        .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            vertical-align: text-top;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
            display: none;
        }
        
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        
        .btn-loading .spinner-border {
            display: inline-block;
        }
        
        .btn-loading:disabled {
            cursor: wait;
        }
        
        :root {
            --primary-color: #d4af37;
            --primary-hover: #c19b2e;
            --primary-light: rgba(212, 175, 55, 0.1);
            --bs-body-font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        /* Modal Styling */
        .modal {
            font-family: 'Poppins', sans-serif;
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1.2rem 1.5rem;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
        }
        
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .modal-body {
            padding: 1.8rem;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1.2rem 1.8rem;
            background-color: #f9f9f9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        
        .btn-close {
            opacity: 0.8;
            transition: all 0.2s ease;
        }
        
        .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        /* Custom scrollbar for modals */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }
        
        /* Check Availability Section */
        .availability-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 2.5rem 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin: 2rem 0 3rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .availability-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .availability-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .availability-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .availability-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .availability-title i {
            color: var(--primary-color);
        }
        
        .datetime-input {
            position: relative;
        }
        
        .datetime-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .form-control.datetime {
            padding-left: 45px;
            height: 50px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control.datetime:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }
        
        .btn-check-availability {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            height: 50px;
        }
        
        .btn-check-availability:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        
        .availability-status {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 10px;
        }
        
        .availability-status.available {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .availability-status.unavailable {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .gallery-section {
            padding: 4rem 0;
            background-color: #f8f9fa;
        }
        
        .gallery-container {
            margin-top: 2rem;
        }
        
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
            transition: all 0.4s ease;
            height: 220px;
            background: #f8f9fa;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 100%;
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 15px 20px 20px;
            text-align: center;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.4s ease;
        }
        
        .gallery-item:hover .gallery-caption {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            background: white;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        
        .price {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }
        
        .features-list li {
            padding: 0.5rem 0;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .features-list li:last-child {
            border-bottom: none;
        }
        
        .features-list i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        .btn-book {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-book:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            color: white;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #2c3e50;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            margin: 1rem auto 0;
            border-radius: 2px;
        }
        
        .main-content {
            margin-top: 80px;
        }

        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/hall.jpg');
            background-size: cover;
            background-position: center;
            padding: 170px 0 70px 0;
            color: white;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .page-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .page-header .lead {
            font-size: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .header-btn {
            display: inline-block;
            padding: 15px 35px;
            background-color: #d4af37;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 25px;
            transition: all 0.3s ease;
            border: 2px solid #d4af37;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .header-btn:hover {
            background-color: transparent;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-top: 60px;
            }
            .page-header {
                padding: 40px 0;
            }
            .header-btn {
                padding: 12px 28px;
                font-size: 14px;
            }
            .page-header h1 {
                font-size: 2.5rem;
            }
            
            .page-header .lead {
                font-size: 1.2rem;
            }
            
            .custom-card {
                padding: 1.5rem;
            }
            
            .gallery-item {
                height: 250px;
            }
        }
        @media (max-width: 576px) {
            
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 120px 0 50px 0; 
            color: white;
            position: relative;
            overflow: hidden;
        }
        .page-header h1 {
            font-size: 2rem;
        }
        .section-title {
            font-size: 2rem;
        }
        .custom-card .price {
            font-size: 2rem;
        }
        }
        
        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            min-width: 300px;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .alert pre {
            margin: 0;
            white-space: pre-wrap;
            font-family: inherit;
        }

        .custom-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .custom-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .card-body {
            padding: 1.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .custom-card .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d4af37;
            margin-bottom: 2rem;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .features-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: #666;
            position: relative;
            padding-left: 25px;
        }

        .features-list li:before {
            content: '✓';
            color: #d4af37;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Check Availability Section */
        .availability-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
            position: relative;
            overflow: hidden;
        }

        .availability-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(90deg, #d4af37, #f8f9fa);
        }

        .availability-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .availability-card {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .availability-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .availability-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #d4af37, #f8d56b);
        }

        .availability-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #2c3e50;
            position: relative;
            display: inline-block;
        }

        .availability-title i {
            margin-right: 12px;
            color: #d4af37;
        }

        .availability-subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .datetime-input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .datetime-input {
            position: relative;
            width: 100%;
        }

        .datetime-input i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 1.25rem;
            color: #6c757d;
            font-size: 1.1rem;
            z-index: 2;
        }

        .form-control.datetime {
            padding: 0.85rem 1.5rem 0.85rem 3.5rem;
            height: auto;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control.datetime:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
            background-color: #fff;
        }

        .btn-check-availability {
            background: linear-gradient(135deg, #d4af37 0%, #e6c66b 100%);
            color: #fff;
            border: none;
            padding: 0.9rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-check-availability:hover {
            background: linear-gradient(135deg, #c19b2e 0%, #d4af37 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
        
        /* Booked Badge Styles */
        .booked-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .package-card {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .package-card.booked {
            opacity: 0.8;
            filter: grayscale(20%);
        }
        
        /* Disabled button styles */
        .btn-book:disabled,
        .btn-book.disabled,
        .package-card.booked .btn-book {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.7;
            pointer-events: none;
        }
        
        .btn-book:disabled:hover,
        .btn-book.disabled:hover,
        .package-card.booked .btn-book:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-check-availability:active {
            transform: translateY(0);
        }
        
        /* Availability Badge Styles */
        .availability-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .badge-available {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-available:hover {
            background-color: #c3e6cb;
            transform: translateY(-1px);
        }
        
        .badge-unavailable {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .badge-unavailable:hover {
            background-color: #f5c6cb;
        }
        
        .badge-booked {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
            animation: pulse 2s infinite;
        }
        
        .badge-booked:hover {
            background-color: #b8daff;
        }
        
        .badge-unknown {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }
        
        /* Style for booked package cards */
        .booked-package {
            opacity: 0.9;
            position: relative;
        }
        
        .booked-package::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.03);
            pointer-events: none;
            border-radius: 10px;
        }
        
        .availability-badge i {
            margin-right: 5px;
            font-size: 0.8rem;
        }
        
        /* Ensure card has relative positioning for absolute badge */
        .package-card {
            position: relative;
        }

        .btn-check-availability i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .availability-status {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #d4af37;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .availability-status i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            color: #d4af37;
        }

        #statusMessage {
            font-size: 0.95rem;
            color: #495057;
        }
        
        /* Hide package sections by default */
        .package-section {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .availability-card {
                padding: 1.75rem;
            }
            
            .availability-title {
                font-size: 1.5rem;
            }
            
            .btn-check-availability {
                padding: 0.75rem 1.5rem;
            }
        }
      
</style>
</head>
<body>
<?php include 'message_box.php'; ?>

    
    <?php 
    if (isset($_SESSION['message'])) {
        $alertType = isset($_SESSION['success']) && $_SESSION['success'] ? 'success' : 'danger';
        $message = $_SESSION['message'];
        
        // Display Bootstrap alert with simple header
        echo "
        <div class='alert alert-{$alertType} alert-dismissible fade show' role='alert'>
            <h4 class='alert-heading'>Successfully Booked!</h4>
            <hr>
            <pre>{$message}</pre>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        
        // Clear session variables
        unset($_SESSION['success']);
        unset($_SESSION['message']);
    }
    ?>
    
    <?php include('nav.php'); ?>

    <!-- Header Section -->
    <header class="page-header">
        <div class="container">
            <h1 class="animate__animated animate__fadeInDown">Celebrate Your Special Moments</h1>
            <p class="lead mt-3 animate__animated animate__fadeInUp">Create unforgettable memories with our exclusive event packages</p>
            <a href="#packages" class="header-btn">
                Book Your Event Now
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Event Types Section -->
        <section class="mb-5">
            <h2 class="text-center mb-4">Perfect for Every Occasion</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="custom-card text-center slide-up">
                        <div class="card-body">
                            <i class="fas fa-ring fa-3x mb-3" style="color: var(--primary-color)"></i>
                            <h3>Weddings</h3>
                            <p>Intimate ceremonies and receptions for your special day</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-card text-center slide-up">
                        <div class="card-body">
                            <i class="fas fa-birthday-cake fa-3x mb-3" style="color: var(--primary-color)"></i>
                            <h3>Birthdays</h3>
                            <p>Memorable celebrations for all ages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-card text-center slide-up">
                        <div class="card-body">
                            <i class="fas fa-handshake fa-3x mb-3" style="color: var(--primary-color)"></i>
                            <h3>Corporate Events</h3>
                            <p>Professional settings for your business gatherings</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    
    <!-- Check Availability Section -->
    <section class="availability-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="availability-card">
                        <div class="text-center mb-4">
                            <h2 class="availability-title">
                                <i class="far fa-calendar-check"></i>
                                Check Event Availability
                            </h2>
                            <p class="availability-subtitle">Plan your special event with us - Check available dates and times</p>
                        </div>
                        
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <div class="datetime-input-group">
                                    <div class="datetime-input">
                                        <i class="far fa-calendar-alt"></i>
                                        <input type="datetime-local" 
                                               class="form-control datetime" 
                                               id="eventDateTime" 
                                               min="<?php echo date('Y-m-d\TH:i'); ?>" 
                                               value=""
                                               aria-label="Select date and time for your event"
                                               placeholder="Select date and time">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" 
                                        class="btn btn-check-availability" 
                                        id="checkAvailabilityBtn"
                                        aria-label="Check availability for selected date and time"
                                        onclick="checkAvailability()">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="availabilitySpinner"></span>
                                    <i class="fas fa-search"></i> Check Availability
                                </button>
                            </div>
                        </div>
                        
                        <div class="availability-status mt-4" id="availabilityStatus" role="alert" aria-live="polite">
                            <i class="fas fa-info-circle"></i>
                            <span id="statusMessage">Please select a date and time to check availability for your event.</span>
                        </div>
                        
                        <!-- Booked Packages Container -->
                        <div id="bookedPackagesContainer" class="mt-4" style="display: none;">
                            <h5 class="mb-3">Booked Packages for Selected Time:</h5>
                            <div id="bookedPackagesList" class="list-group">
                                <!-- Booked packages will be listed here -->
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center text-muted small">
                            <i class="fas fa-clock me-1"></i> Events can be scheduled between 8:00 AM to 10:00 PM
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
        <!-- Event Packages Section -->
        <section class="gallery-section package-section" id="packages">
            <div class="container">

                <div class="text-center mb-5">
                    <h2 class="section-title">Our Garden Event Packages</h2>
                    <div class="divider mx-auto my-3" style="width: 100px; height: 3px; background-color: var(--primary-color);"></div>
                    <p class="lead">Choose the perfect package for your special occasion</p>

                    <p class="text-muted small">Showing packages specifically for our garden venue</p>
                </div>
                <div class="row g-4">
                <?php
                // Fetch garden event packages
                $query = "SELECT * FROM event_packages WHERE status = 'Available' AND LOWER(place) = 'garden' ORDER BY price ASC";
                $result = $conn->query($query);
                
                if ($result && $result->rowCount() > 0) {
                    while ($package = $result->fetch(PDO::FETCH_ASSOC)) {
                        // Get the first image if available
                        $image = !empty($package['image_path']) ? $package['image_path'] : 'images/default-event.jpg';
                        ?>
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                            <div class="custom-card package-card h-100 d-flex flex-column" data-package-id="<?php echo $package['id']; ?>">
                                <div class="gallery-item flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="img-fluid w-100">
                                    <div class="gallery-caption">
                                        <h5 class="text-white mb-0"><?php echo htmlspecialchars($package['name']); ?></h5>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title h4"><?php echo htmlspecialchars($package['name']); ?></h3>
                                    <div class="price mb-3">₱<?php echo number_format($package['price'], 2); ?></div>
                                    
                                    <?php if (!empty($package['description'])): ?>
                                        <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($package['description'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="features mb-4">
                                        <ul class="features-list">
                                            <?php if (!empty($package['max_pax'])): ?>
                                                <li><i class="fas fa-users me-2"></i> Up to <?php echo $package['max_pax']; ?> guests</li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['time_limit'])): ?>
                                                <li><i class="far fa-clock me-2"></i> <?php echo htmlspecialchars($package['time_limit']); ?> duration</li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['place'])): ?>
                                                <li><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($package['place']); ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['menu_items'])): ?>
                                                <li class="d-flex align-items-start">
                                                    <i class="fas fa-utensils me-2 mt-1"></i>
                                                    <span>Includes: <?php echo htmlspecialchars($package['menu_items']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-auto pt-3">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-primary btn-book" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                                data-package-id="<?php echo $package['id']; ?>" 
                                                data-package-name="<?php echo htmlspecialchars($package['name']); ?>"
                                                data-package-price="<?php echo number_format($package['price'], 2); ?>"
                                                data-package-place="<?php echo strtolower($package['place']); ?>"
                                                data-max-guests="<?php echo $package['max_pax'] ?? 0; ?>">
                                                Book Now <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-details w-100" data-bs-toggle="modal" data-bs-target="#packageDetailsModal" data-package-id="<?php echo $package['id']; ?>" data-package-name="<?php echo htmlspecialchars($package['name']); ?>">
                                                <i class="fas fa-info-circle me-2"></i>View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-12 text-center"><p class="lead">No event packages available at the moment. Please check back later.</p></div>';
                }
                ?>
                </div>
            </div>
        </section>

        <!-- Cafe Event Packages Section -->
        <section class="gallery-section package-section" id="cafe-packages">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title">Our Cafe Event Packages</h2>
                    <div class="divider mx-auto my-3" style="width: 100px; height: 3px; background-color: var(--primary-color);"></div>
                    <p class="lead">Perfect for intimate gatherings and coffee lovers</p>
                    <p class="text-muted small">Showing packages specifically for our cafe venue</p>
                </div>
                <div class="row g-4">
                <?php
                // Fetch cafe event packages
                $query = "SELECT * FROM event_packages WHERE status = 'Available' AND LOWER(place) = 'cafe' ORDER BY price ASC";
                $result = $conn->query($query);
                
                if ($result && $result->rowCount() > 0) {
                    while ($package = $result->fetch(PDO::FETCH_ASSOC)) {
                        // Get the first image if available
                        $image = !empty($package['image_path']) ? '../../../Admin/adminBackend/event_packages_images/' . basename($package['image_path']) : 'images/default-event.jpg';
                        ?>
                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                            <div class="custom-card package-card h-100 d-flex flex-column" data-package-id="<?php echo $package['id']; ?>">
                                <div class="gallery-item flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($package['name']); ?>" class="img-fluid w-100">
                                    <div class="gallery-caption">
                                        <h5 class="text-white mb-0"><?php echo htmlspecialchars($package['name']); ?></h5>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title h4"><?php echo htmlspecialchars($package['name']); ?></h3>
                                    <div class="price mb-3">₱<?php echo number_format($package['price'], 2); ?></div>
                                    
                                    <?php if (!empty($package['description'])): ?>
                                        <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($package['description'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="features mb-4">
                                        <ul class="features-list">
                                            <?php if (!empty($package['max_pax'])): ?>
                                                <li><i class="fas fa-users me-2"></i> Up to <?php echo $package['max_pax']; ?> guests</li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['time_limit'])): ?>
                                                <li><i class="far fa-clock me-2"></i> <?php echo htmlspecialchars($package['time_limit']); ?> duration</li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['place'])): ?>
                                                <li><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($package['place']); ?></li>
                                            <?php endif; ?>
                                            <?php if (!empty($package['menu_items'])): ?>
                                                <li class="d-flex align-items-start">
                                                    <i class="fas fa-utensils me-2 mt-1"></i>
                                                    <span>Includes: <?php echo htmlspecialchars($package['menu_items']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-auto pt-3">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-primary btn-book" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                                data-package-id="<?php echo $package['id']; ?>" 
                                                data-package-name="<?php echo htmlspecialchars($package['name']); ?>"
                                                data-package-price="<?php echo number_format($package['price'], 2); ?>"
                                                data-package-place="<?php echo strtolower($package['place']); ?>"
                                                data-max-guests="<?php echo $package['max_pax'] ?? 0; ?>">
                                                Book Now <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-details w-100" data-bs-toggle="modal" data-bs-target="#packageDetailsModal" data-package-id="<?php echo $package['id']; ?>" data-package-name="<?php echo htmlspecialchars($package['name']); ?>">
                                                <i class="fas fa-info-circle me-2"></i>View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-12 text-center"><p class="lead">No cafe event packages available at the moment. Please check back later.</p></div>';
                }
                ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="bookingModalLabel">Book Event Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bookingForm" action="event_payment_process.php" method="GET" onsubmit="return validateGuestCount()">
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="bookingPackageId">
                        <input type="hidden" id="maxGuests" value="0">
                        <input type="hidden" id="packageNameInput" name="package_name">
                        <input type="hidden" id="packagePriceInput" name="package_price">
                        <input type="hidden" id="eventPlaceInput" name="event_place">
                        <input type="hidden" id="eventTypeInput" name="event_type">
                        <input type="hidden" id="paymentOptionInput" name="payment_option">
                        <input type="hidden" id="paymentMethodInput" name="payment_method">
                        <div class="mb-3">
                            <label class="form-label">Package</label>
                            <div class="form-control bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span id="packageName" class="fw-bold"></span>
                                    <span id="packagePrice" class="text-primary fw-bold"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="eventDate" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="eventDate" name="event_date" required disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="eventTime" class="form-label">Event Time</label>
                                <input type="time" class="form-control" id="eventTime" name="event_time" required disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="eventType" class="form-label">Event Type</label>
                                <select class="form-select" id="eventType" name="event_type" required>
                                    <option value="">Select Event Type</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="birthday">Birthday</option>
                                    <option value="corporate">Corporate Event</option>
                                    <option value="family">Family Gathering</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="eventPlace" class="form-label">Event Place</label>
                                <select class="form-select" id="eventPlace" name="event_place" required>
                                    <option value="">Select Event Place</option>
                                    <option value="garden">Garden</option>
                                    <option value="cafe">Cafe</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="guestCount" class="form-label">Number of Guests</label>
                                <input type="number" class="form-control" id="guestCount" name="guest_count" min="1" required>
                                <input type="hidden" id="maxGuests" value="0">
                                <div class="invalid-feedback" id="guestCountFeedback">
                                    Number of guests exceeds the maximum allowed for this package.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="paymentMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" selected>Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paymentOption" class="form-label">Payment Option</label>
                            <select class="form-select" id="paymentOption" name="payment_option" required>
                                <option value="">Select Payment Option</option>
                                <option value="full_payment">Full Payment</option>
                                <option value="down_payment">Down Payment (50%)</option>
                            </select>
                        </div>
                        
                        <!-- Payment Breakdown Section -->
                        <div id="paymentBreakdown" class="border rounded p-3 mb-3" style="display: none;">
                            <h6 class="mb-3">Payment Breakdown</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Package Price:</span>
                                <span id="breakdownPackagePrice">₱0.00</span>
                            </div>
                            <div id="downPaymentRow" class="d-flex justify-content-between mb-2">
                                <span>Down Payment (50%):</span>
                                <span id="breakdownDownPayment">₱0.00</span>
                            </div>
                            <div id="balanceRow" class="d-flex justify-content-between mb-2">
                                <span>Remaining Balance :</span>
                                <span id="breakdownBalance">₱0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Amount to Pay Now:</span>
                                <span id="breakdownTotal">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="proceedToPaymentBtn">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Package Details Modal -->
    <div class="modal fade" id="packageDetailsModal" tabindex="-1" aria-labelledby="packageDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="packageDetailsModalLabel">Package Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="packageDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading package details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="bookNowBtn" class="btn btn-primary">
                        Book Now <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS Bundle with Popper -->
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
async function checkAvailability() {
    const btn = document.getElementById('checkAvailabilityBtn');
    const spinner = document.getElementById('availabilitySpinner');
    const icon = btn.querySelector('.fa-search');
    const statusMessage = document.getElementById('statusMessage');
    const eventDateTime = document.getElementById('eventDateTime').value;
    
    // Validate date and time
    if (!eventDateTime) {
        statusMessage.textContent = 'Please select a date and time to check availability.';
        statusMessage.parentElement.className = 'availability-status mt-4 alert alert-warning';
        document.getElementById('bookedPackagesContainer').style.display = 'none';
        return;
    }
    
    // Show spinner and update UI
    spinner.style.display = 'inline-block';
    icon.style.display = 'none';
    btn.disabled = true;
    statusMessage.textContent = 'Checking availability...';
    statusMessage.parentElement.className = 'availability-status mt-4 alert alert-info';
    
    try {
        // Make AJAX call to check availability
        const response = await fetch('events_check_availability.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `event_datetime=${encodeURIComponent(eventDateTime)}`
        });

        const result = await response.json();
        
        // Log the full response for debugging
        console.log('Server response:', result);
        
        // Get the container elements
        const bookedContainer = document.getElementById('bookedPackagesContainer');
        const bookedList = document.getElementById('bookedPackagesList');
        
        // Clear previous results
        bookedList.innerHTML = '';
        
        // Update UI based on response
        if (result.status === 'success') {
            // Auto-populate event date and time fields in booking modal
            const eventDate = document.getElementById('eventDate');
            const eventTime = document.getElementById('eventTime');
            
            if (eventDateTime && eventDate && eventTime) {
                // Parse the selected datetime
                const selectedDate = new Date(eventDateTime);
                const dateStr = selectedDate.toISOString().split('T')[0]; // YYYY-MM-DD format
                const timeStr = selectedDate.toTimeString().slice(0, 5); // HH:MM format
                
                // Set the values
                eventDate.value = dateStr;
                eventTime.value = timeStr;
                
                // Enable the fields so they can be submitted
                eventDate.disabled = false;
                eventTime.disabled = false;
                
                console.log('Auto-populated event date:', dateStr, 'and time:', timeStr);
            }
            
            // Show package sections and scroll to them (user clicked the button)
            showPackageSections(true);
            
            if (result.data && result.data.status === 'available') {
                // All packages are available
                statusMessage.textContent = 'All packages are available for the selected time!';
                statusMessage.parentElement.className = 'availability-status mt-4 alert alert-success';
                bookedContainer.style.display = 'none';
            } 
            else if (result.data && result.data.status === 'partial' && result.data.booked_packages) {
                // Some packages are booked
                statusMessage.textContent = 'Some packages are already booked for the selected time.';
                statusMessage.parentElement.className = 'availability-status mt-4 alert alert-warning';
                
                // Show booked packages
                result.data.booked_packages.forEach(packageName => {
                    // Find package card by matching the package name in the card title
                    const packageCards = document.querySelectorAll('.package-card');
                    let packageCard = null;
                    
                    packageCards.forEach(card => {
                        const cardTitle = card.querySelector('.card-title')?.textContent?.trim();
                        if (cardTitle === packageName) {
                            packageCard = card;
                        }
                    });
                    
                    if (packageCard) {
                        const listItem = document.createElement('div');
                        listItem.className = 'list-group-item list-group-item-danger d-flex justify-content-between align-items-center';
                        listItem.innerHTML = `
                            <span><i class="fas fa-calendar-times me-2"></i>${packageName}</span>
                            <span class="badge bg-danger rounded-pill">Booked</span>
                        `;
                        bookedList.appendChild(listItem);
                        
                        // Mark the package card as booked
                        packageCard.classList.add('booked-package');
                    } else {
                        // If package card not found, still show it in the list
                        const listItem = document.createElement('div');
                        listItem.className = 'list-group-item list-group-item-danger d-flex justify-content-between align-items-center';
                        listItem.innerHTML = `
                            <span><i class="fas fa-calendar-times me-2"></i>${packageName}</span>
                            <span class="badge bg-danger rounded-pill">Booked</span>
                        `;
                        bookedList.appendChild(listItem);
                    }
                });
                
                // Show the container
                bookedContainer.style.display = 'block';
            } 
            else if (result.data && result.data.status === 'Booked') {
                // All packages are booked
                statusMessage.textContent = 'All packages are booked for the selected time. Please choose a different time.';
                statusMessage.parentElement.className = 'availability-status mt-4 alert alert-danger';
                bookedContainer.style.display = 'none';
            }
        } else {
            throw new Error(result.message || 'Failed to check availability');
        }
        
    } catch (error) {
        console.error('Error checking availability:', error);
        statusMessage.textContent = 'An error occurred while checking availability. Please try again.';
        statusMessage.parentElement.className = 'availability-status mt-4 alert alert-danger';
        document.getElementById('bookedPackagesContainer').style.display = 'none';
    } finally {
        // Always hide spinner and re-enable button
        spinner.style.display = 'none';
        icon.style.display = 'inline-block';
        btn.disabled = false;
    }
}
    </script>
    <script>
    // Function to check package availability for a specific datetime
    async function checkPackageAvailability(packageId, dateTime = null) {
        try {
            const card = document.querySelector(`[data-package-id="${packageId}"]`);
            if (!card) return false;
            
            // Get or create the badge
            let badge = document.getElementById(`availability-badge-${packageId}`);
            if (!badge) {
                badge = document.createElement('div');
                badge.id = `availability-badge-${packageId}`;
                card.style.position = 'relative';
                card.insertBefore(badge, card.firstChild);
            }
            
            // If no specific datetime provided, use the one from the input
            let checkDateTime = dateTime;
            if (!checkDateTime) {
                const dateTimeInput = document.getElementById('eventDateTime');
                checkDateTime = dateTimeInput ? dateTimeInput.value : '';
            }
            
            // If still no datetime, use default (tomorrow at 10:00)
            if (!checkDateTime) {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                const dateStr = tomorrow.toISOString().split('T')[0];
                checkDateTime = `${dateStr}T10:00`;
            }
            
            // Set initial loading state
            badge.className = 'availability-badge badge-unknown';
            badge.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Send request to check availability
            const response = await fetch('events_check_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `event_datetime=${encodeURIComponent(checkDateTime)}`
            });
            
            const result = await response.json();
            let isAvailable = false;
            let nextAvailable = '';
            let isBooked = false;
            
            if (result.status === 'success' && result.data) {
                isAvailable = result.data.status === 'available';
                nextAvailable = result.data.next_available || '';
                // Check if this package is in the booked_packages array
                if (result.data.booked_packages && Array.isArray(result.data.booked_packages)) {
                    isBooked = result.data.booked_packages.includes(parseInt(packageId));
                }
            }
            
            // Update badge based on availability and booking status
            if (isBooked) {
                // Package is booked for the selected time
                badge.className = 'availability-badge badge-booked';
                badge.innerHTML = '<i class="fas fa-calendar-check"></i> Booked';
                card.style.display = 'block';
                // Add a class to the card to indicate it's booked
                card.classList.add('booked-package');
                // Disable the book button if it exists
                const bookBtn = card.querySelector('.btn-book');
                if (bookBtn) {
                    bookBtn.disabled = true;
                    bookBtn.classList.add('disabled');
                    bookBtn.setAttribute('aria-disabled', 'true');
                    bookBtn.setAttribute('title', 'This package is already booked for the selected time');
                }
            } else if (isAvailable) {
                // Package is available
                badge.className = 'availability-badge badge-available';
                badge.innerHTML = '<i class="fas fa-check-circle"></i> Available';
                card.style.display = 'block';
                // Remove booked class if it exists
                card.classList.remove('booked-package');
                // Enable the book button if it exists
                const bookBtn = card.querySelector('.btn-book');
                if (bookBtn) {
                    bookBtn.disabled = false;
                    bookBtn.classList.remove('disabled');
                    bookBtn.removeAttribute('aria-disabled');
                    bookBtn.removeAttribute('title');
                }
            } else {
                // Package is unavailable but has next available time
                badge.className = 'availability-badge badge-unavailable';
                if (nextAvailable) {
                    const nextTime = new Date(nextAvailable);
                    const timeStr = nextTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true});
                    badge.innerHTML = `<i class="fas fa-clock"></i> Next: ${timeStr}`;
                    
                    // Enable book button for next available time
                    const bookBtn = card.querySelector('.btn-book');
                    if (bookBtn) {
                        bookBtn.disabled = false;
                        bookBtn.classList.remove('disabled');
                        bookBtn.removeAttribute('aria-disabled');
                        bookBtn.removeAttribute('title');
                        // Update button to book for next available time
                        bookBtn.setAttribute('title', `Book for ${timeStr}`);
                    }
                } else {
                    badge.innerHTML = '<i class="fas fa-times-circle"></i> Closed';
                }
                // Disable book button when status is Closed (no next available)
                if (!nextAvailable) {
                    const bookBtn = card.querySelector('.btn-book');
                    if (bookBtn) {
                        bookBtn.disabled = true;
                        bookBtn.classList.add('disabled');
                        bookBtn.setAttribute('aria-disabled', 'true');
                        bookBtn.setAttribute('title', 'This package is closed for the selected time');
                    }
                }
                // Hide the card if we're filtering by time
                if (dateTime) {
                    card.style.display = 'none';
                }
                // Remove booked class if it exists
                card.classList.remove('booked-package');
            }
            
            return isAvailable;
            
        } catch (error) {
            console.error('Error checking package availability:', error);
            const badge = document.getElementById(`availability-badge-${packageId}`);
            if (badge) {
                badge.className = 'availability-badge badge-unknown';
                badge.innerHTML = '<i class="fas fa-question-circle"></i>';
            }
            return false;
        }
    }
    
    // Function to show all package sections
    function showPackageSections(scrollToPackages = false) {
        document.querySelectorAll('.package-section').forEach(section => {
            section.style.display = 'block';
        });
        
        // Only scroll if explicitly requested (when user clicks Check Availability button)
        if (scrollToPackages) {
            const firstSection = document.querySelector('.package-section');
            if (firstSection) {
                firstSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
    
    // Function to check all packages for a specific datetime
    async function checkAllPackagesAvailability(dateTime = null) {
        // Show package sections when checking availability
        showPackageSections();
        
        const packageCards = document.querySelectorAll('[data-package-id]');
        const checkPromises = [];
        
        // Show loading state for all cards
        packageCards.forEach(card => {
            const packageId = card.getAttribute('data-package-id');
            const badge = document.getElementById(`availability-badge-${packageId}`) || 
                         (() => {
                             const b = document.createElement('div');
                             b.id = `availability-badge-${packageId}`;
                             card.style.position = 'relative';
                             card.insertBefore(b, card.firstChild);
                             return b;
                         })();
            badge.className = 'availability-badge badge-unknown';
            badge.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
        
        // Check availability for all packages
        packageCards.forEach(card => {
            const packageId = card.getAttribute('data-package-id');
            checkPromises.push(checkPackageAvailability(packageId, dateTime));
        });
        
        return Promise.all(checkPromises);
    }
    
    // Function to refresh availability after booking
    async function refreshAvailabilityAfterBooking() {
        const dateTimeInput = document.getElementById('eventDateTime');
        if (dateTimeInput && dateTimeInput.value) {
            await checkAllPackagesAvailability(dateTimeInput.value);
            
            // Show a success message
            const statusMessage = document.getElementById('statusMessage');
            if (statusMessage) {
                statusMessage.innerHTML = '<div class="alert alert-success">Booking successful! Availability has been updated.</div>';
                // Hide the message after 5 seconds
                setTimeout(() => {
                    statusMessage.innerHTML = '';
                }, 5000);
            }
        }
    }
    
    // Call refreshAvailabilityAfterBooking if we detect a successful booking from URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('booking') === 'success') {
            refreshAvailabilityAfterBooking();
            // Clean up the URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });
    
    // Check availability for all packages when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Don't initialize packages on page load anymore
        // They will be shown after availability check
        
        // Add event listener to datetime input
        const dateTimeInput = document.getElementById('eventDateTime');
        if (dateTimeInput) {
            dateTimeInput.addEventListener('change', function() {
                if (this.value) {
                    checkAllPackagesAvailability(this.value);
                } else {
                    // If input is cleared, show all packages and check default availability
                    document.querySelectorAll('[data-package-id]').forEach(card => {
                        card.style.display = 'block';
                    });
                    checkAllPackagesAvailability();
                }
            });
        }
        
        // Handle Book Now button click
        const bookButtons = document.querySelectorAll('.btn-book');
        
        // Add click event to each button
        bookButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get package details from data attributes
                const packageId = this.getAttribute('data-package-id');
                const packageName = this.getAttribute('data-package-name');
                // Remove commas and convert to number
                const packagePrice = parseFloat(this.getAttribute('data-package-price').replace(/,/g, ''));
                const packagePlace = this.getAttribute('data-package-place');
                const maxGuests = this.getAttribute('data-max-guests');
                
                // Set the values in the modal
                document.getElementById('packageName').textContent = packageName;
                document.getElementById('packagePrice').textContent = '₱' + packagePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                // Set the hidden input fields
                document.getElementById('bookingPackageId').value = packageId;
                document.getElementById('packageNameInput').value = packageName;
                document.getElementById('packagePriceInput').value = packagePrice;
                document.getElementById('eventPlaceInput').value = packagePlace;
                document.getElementById('maxGuests').value = maxGuests;
                
                // Set the event place dropdown
                const eventPlaceSelect = document.getElementById('eventPlace');
                for (let i = 0; i < eventPlaceSelect.options.length; i++) {
                    if (eventPlaceSelect.options[i].value === packagePlace) {
                        eventPlaceSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Set minimum date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('eventDate').min = today;
                
                // Reset and show payment breakdown
                updatePaymentBreakdown(packagePrice, 'full_payment');
            });
        });
        
        // Handle payment option change
        const paymentOption = document.getElementById('paymentOption');
        if (paymentOption) {
            paymentOption.addEventListener('change', function() {
                const packagePrice = document.getElementById('packagePriceInput').value;
                updatePaymentBreakdown(packagePrice, this.value);
            });
        }
        
        // Function to update payment breakdown
        function updatePaymentBreakdown(price, paymentOption) {
            // Ensure price is a number
            const packagePrice = typeof price === 'string' ? parseFloat(price.replace(/,/g, '')) : parseFloat(price);
            
            // Get DOM elements
            const paymentBreakdown = document.getElementById('paymentBreakdown');
            const downPaymentRow = document.getElementById('downPaymentRow');
            const balanceRow = document.getElementById('balanceRow');
            
            // Show payment breakdown section
            paymentBreakdown.style.display = 'block';
            
            // Update package price in breakdown
            document.getElementById('breakdownPackagePrice').textContent = '₱' + packagePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            if (paymentOption === 'down_payment') {
                const downPayment = packagePrice * 0.5;
                const balance = packagePrice - downPayment;
                
                // Update down payment and balance values
                document.getElementById('breakdownDownPayment').textContent = '₱' + downPayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('breakdownBalance').textContent = '₱' + balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('breakdownTotal').textContent = '₱' + downPayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                // Show down payment and balance rows
                downPaymentRow.style.display = 'flex';
                balanceRow.style.display = 'flex';
            } else {
                // For full payment, only show the total
                document.getElementById('breakdownTotal').textContent = '₱' + packagePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                // Hide down payment and balance rows
                downPaymentRow.style.display = 'none';
                balanceRow.style.display = 'none';
                
                // Clear the values to be safe
                document.getElementById('breakdownDownPayment').textContent = '₱0.00';
                document.getElementById('breakdownBalance').textContent = '₱0.00';
            }
        }
        
        // Add form submission handler for spinner
        const bookingForm = document.getElementById('bookingForm');
        const proceedBtn = document.getElementById('proceedToPaymentBtn');
        
        if (bookingForm && proceedBtn) {
            bookingForm.addEventListener('submit', function(e) {
                // Prevent default form submission
                e.preventDefault();
                
                // Show spinner and disable button
                const spinner = proceedBtn.querySelector('.spinner-border');
                proceedBtn.disabled = true;
                spinner.style.display = 'inline-block';
                
                // Add 3 seconds delay before form submission
                setTimeout(() => {
                    // Re-enable the form and submit it
                    bookingForm.submit();
                }, 3000);
                
                // Prevent default form submission
                return false;
            });
        }
        
        // Handle guest count validation
        const guestCountInput = document.getElementById('guestCount');
        if (guestCountInput) {
            guestCountInput.addEventListener('input', function() {
                validateGuestCount();
            });
        }
        
        // Function to validate guest count
        window.validateGuestCount = function() {
            const guestCount = parseInt(guestCountInput.value) || 0;
            const maxGuests = parseInt(document.getElementById('maxGuests').value) || 0;
            const feedback = document.getElementById('guestCountFeedback');
            
            if (maxGuests > 0 && guestCount > maxGuests) {
                guestCountInput.classList.add('is-invalid');
                feedback.style.display = 'block';
                return false;
            } else {
                guestCountInput.classList.remove('is-invalid');
                feedback.style.display = 'none';
                return true;
            }
        };
        
        // Handle package details modal
        const packageDetailsModal = document.getElementById('packageDetailsModal');
        if (packageDetailsModal) {
            packageDetailsModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const packageId = button.getAttribute('data-package-id');
                const packageName = button.getAttribute('data-package-name');
                
                if (packageId || packageName) {
                    loadPackageDetails(packageId, packageName);
                }
            });
        }
        
        // Function to load package details
        function loadPackageDetails(packageId, packageName) {
            const modalContent = document.getElementById('packageDetailsContent');
            
            // Show loading state
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading package details...</p>
                </div>
            `;
            
            // Fetch package details
            fetch('get_package_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    packageId: packageId,
                    packageName: packageName 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayPackageDetails(data.package);
                } else {
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${data.message || 'Failed to load package details'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load package details. Please try again.
                    </div>
                `;
            });
        }
        
        // Function to display package details
        function displayPackageDetails(package) {
            const modalContent = document.getElementById('packageDetailsContent');
            
            let menuItemsHtml = '';
            if (package.menu_items && Object.keys(package.menu_items).length > 0) {
                menuItemsHtml = '<div class="mb-4"><h6 class="mb-3"><i class="fas fa-utensils me-2"></i>Menu Items</h6>';
                Object.entries(package.menu_items).forEach(([category, items]) => {
                    if (items.length > 0) {
                        menuItemsHtml += `
                            <div class="mb-3">
                                <h6 class="text-muted small mb-2">${category}</h6>
                                <ul class="list-unstyled">
                                    ${items.map(item => `<li class="mb-1">• ${item}</li>`).join('')}
                                </ul>
                            </div>
                        `;
                    }
                });
                menuItemsHtml += '</div>';
            }
            
            let detailsHtml = '';
            if (package.details && package.details.length > 0) {
                detailsHtml = '<div class="mb-4"><h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Package Details</h6><ul class="list-unstyled">';
                package.details.forEach(detail => {
                    detailsHtml += `<li class="mb-2"><i class="fas fa-${detail.icon} me-2 text-primary"></i>${detail.text}</li>`;
                });
                detailsHtml += '</ul></div>';
            }
            
            let notesHtml = '';
            if (package.notes && package.notes.length > 0) {
                notesHtml = '<div class="mb-4"><h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i>Important Notes</h6><ul class="list-unstyled small text-muted">';
                package.notes.forEach(note => {
                    notesHtml += `<li class="mb-1">• ${note}</li>`;
                });
                notesHtml += '</ul></div>';
            }
            
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <img src="../../../Admin/adminBackend/event_packages_images/${package.image_path.split('/').pop()}" alt="${package.name}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-3">${package.name}</h4>
                        <div class="price mb-3">₱${package.price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        <p class="text-muted">${package.description || 'No description available'}</p>
                        ${detailsHtml}
                        ${menuItemsHtml}
                        ${notesHtml}
                    </div>
                </div>
            `;
        }
    });

    </script>


</body>
</html>
