<?php 
require_once 'db_con.php';
session_start();

// Initialize arrays
$packages = [];
$gallery_images = [];

try {
    // Fetch event packages from database with their current status
    $sql = "SELECT ep.*, 
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM event_bookings eb 
                    WHERE eb.package_name = ep.name 
                    AND eb.booking_status IN ('pending', 'confirmed')
                    AND eb.event_date = CURRENT_DATE
                    AND TIME(NOW()) BETWEEN eb.start_time AND eb.end_time
                    AND eb.booking_status != 'finished'
                ) THEN 'Currently Not Available'
                ELSE 'Available'
            END as status,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM event_bookings eb 
                    WHERE eb.package_name = ep.name 
                    AND eb.booking_status IN ('pending', 'confirmed')
                    AND eb.event_date = CURRENT_DATE
                    AND TIME(NOW()) BETWEEN eb.start_time AND eb.end_time
                    AND eb.booking_status != 'finished'
                ) THEN false
                ELSE true
            END as is_available
            FROM event_packages ep 
            ORDER BY ep.price ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $packages = $stmt->fetchAll();

    // Add debug logging
    foreach ($packages as $package) {
        error_log("Package: " . $package['name'] . " - Status: " . $package['status'] . " - Is Available: " . ($package['is_available'] ? 'true' : 'false'));
    }

    // Update status in database for each package
    foreach ($packages as $package) {
        $updateSQL = "UPDATE event_packages 
                     SET status = :status 
                     WHERE name = :name";
        $updateStmt = $pdo->prepare($updateSQL);
        $updateStmt->execute([
            'status' => $package['status'],
            'name' => $package['name']
        ]);
    }

    // Fetch gallery images
    $sql = "SELECT * FROM event_images ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $gallery_images = $stmt->fetchAll();

    // Fetch payment methods
    $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE is_active = 1");
    $stmt->execute();
    $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the error
    error_log("Database error: " . $e->getMessage());
    
    // If database query fails, use default packages
    $packages = [
        [
            'name' => 'Package A',
            'price' => 47500,
            'description' => "Up to 50 Pax\n5-hour venue rental\nBasic sound system\nStandard decoration\nBasic catering service"
        ],
        [
            'name' => 'Premium Package',
            'price' => 55000,
            'description' => "Up to 50 Pax\n5-hour venue rental\nPremium sound system\nEnhanced decoration\nPremium catering service\nEvent coordinator"
        ],
        [
            'name' => 'Deluxe Package',
            'price' => 76800,
            'description' => "Up to 50 Pax\n5-hour venue rental\nProfessional DJ\nLuxury decoration\nPremium catering service\nEvent coordinator\nPhoto/Video coverage"
        ],
        [
            'name' => 'Venue Rental Only',
            'price' => 20000,
            'description' => "Up to 50 Pax\n5-hour venue rental\nTables and Tiffany chairs"
        ]
    ];
    
    // Use default gallery images
    $gallery_images = [
        ['image_path' => 'images/hall.jpg', 'caption' => 'Elegant Wedding Reception'],
        ['image_path' => 'images/hall2.jpg', 'caption' => 'Garden Wedding Ceremony'],
        ['image_path' => 'images/hall3.jpg', 'caption' => 'Birthday Celebration Setup'],
        ['image_path' => 'images/gard.jpg', 'caption' => 'Corporate Event Space'],
        ['image_path' => 'images/garden1.jpg', 'caption' => 'Outdoor Reception Area'],
        ['image_path' => 'images/garden.jpg', 'caption' => 'Garden Party Setup']
    ];

    error_log("Error fetching payment methods: " . $e->getMessage());
    $payment_methods = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Celebrations - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="assets/css/events-tables.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .gallery-section {
            padding: 6rem 0;
            background-color: #f8f9fa;
        }
        
        .gallery-container {
            margin-top: 2rem;
        }
        
        .gallery-item {
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 20px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #333;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #d4af37;
            margin: 15px auto;
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .custom-card .card-title {
            color: #333;
            font-size: 1.8rem;
            font-weight: 700;
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

        .btn-primary-custom {
            background: #d4af37;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: #c19b2e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        /* SweetAlert2 Custom Styles */
        .custom-swal-popup {
            padding: 2em;
            border-radius: 15px;
        }
        
        .custom-swal-title {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 1em;
        }
        
        .custom-swal-html {
            text-align: left;
            margin: 1em 0;
        }
        
        .custom-swal-html p {
            margin: 0.5em 0;
            color: #666;
        }
        
        .custom-swal-html strong {
            color: #333;
            font-weight: 600;
        }
        
        .custom-swal-confirm-button {
            background-color: #d4af37 !important;
            border-radius: 50px !important;
            padding: 1em 2em !important;
        }

        .booking-summary {
            padding: 1.5rem;
        }

        .summary-header {
            text-align: center;
            position: relative;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .package-icon {
            width: 60px;
            height: 60px;
            background: #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .package-icon i {
            font-size: 24px;
            color: white;
        }

        .summary-details {
            padding: 1.5rem 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .summary-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
        }

        .summary-label i {
            color: #d4af37;
            width: 20px;
        }

        .summary-value {
            font-weight: 500;
            color: #333;
        }

        .summary-total {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.font-weight-bold {
            font-weight: bold;
            font-size: 1.1em;
        }

        .amount {
            font-family: monospace;
        }

        .border-top {
            border-top: 1px solid #dee2e6;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
        }

        .btn-primary-custom {
            padding: 0.75rem 2rem;
        }

        .swal2-popup {
            padding: 2.5rem;
            border-radius: 15px;
        }

        .swal2-title {
            color: #333;
            font-size: 1.75rem !important;
            font-weight: 600 !important;
        }

        .swal2-html-container {
            color: #666;
            font-size: 1.1rem !important;
            margin-top: 0.5rem !important;
        }

        .swal2-icon {
            border: none !important;
            margin: 1.5rem auto !important;
        }

        .swal2-icon.swal2-success {
            border: none !important;
        }

        .swal2-success-circular-line-left,
        .swal2-success-circular-line-right,
        .swal2-success-fix {
            background-color: transparent !important;
        }

        .swal2-success-ring {
            border: 0.25em solid rgba(212, 175, 55, 0.3) !important;
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: #d4af37 !important;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animated {
            animation-duration: 0.3s;
            animation-fill-mode: both;
        }

        .fadeInDown {
            animation-name: fadeInDown;
        }

        .faster {
            animation-duration: 0.3s;
        }

        .text-left {
            text-align: left;
            margin: 1.5rem 0;
        }

        .text-left p {
            margin: 0.5rem 0;
            color: #666;
        }

        .text-left strong {
            color: #333;
            margin-right: 0.5rem;
        }

        .swal2-popup {
            padding: 2rem;
        }

        .swal2-title {
            color: #333;
            font-size: 1.8rem !important;
            margin: 1rem 0 !important;
        }

        .swal2-html-container.text-left {
            margin: 1rem 0 !important;
        }

        /* Add these new styles */
        .package-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .custom-card {
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-10px);
        }

        .custom-card .card-body {
            padding: 1.5rem;
        }

        /* Add these new styles */
        .btn-outline-primary-custom {
            color: #d4af37;
            border: 2px solid #d4af37;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-primary-custom:hover {
            color: white;
            background: #d4af37;
            transform: translateY(-2px);
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Add these styles for the details modal */
        .package-details {
            text-align: left;
            padding: 1rem;
        }

        .package-details-title {
            color: #333;
            font-size: 2rem !important;
            margin-bottom: 1.5rem !important;
        }

        .package-features {
            margin-top: 1.5rem;
        }

        .package-features h5 {
            color: #333;
            font-weight: 600;
        }

        .package-features li {
            padding: 0.5rem 0;
            color: #666;
        }

        .package-features i {
            color: #d4af37;
        }

        .swal2-popup.package-details-modal {
            padding: 2rem;
        }

        /* Add to existing <style> section */
        .package-details-modal {
            padding: 0 !important;
            border-radius: 15px;
            overflow: hidden;
        }

        .package-details-container {
            text-align: left;
        }

        .package-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            margin-bottom: 0;
        }

        .price-tag {
            background: #d4af37;
            color: white;
            padding: 15px 20px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .package-section {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .package-section:last-child {
            border-bottom: none;
        }

        .section-title {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .menu-categories {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .menu-category {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .menu-category h6 {
            color: #d4af37;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .details-list, .notes-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .details-list li, .notes-list li {
            padding: 8px 0;
            color: #666;
        }

        .details-list i {
            color: #d4af37;
            width: 20px;
        }

        .notes-list li {
            position: relative;
            padding-left: 20px;
        }

        .notes-list li:before {
            content: "•";
            color: #d4af37;
            position: absolute;
            left: 0;
        }

        .package-status {
            text-align: center;
        }

        .package-status .badge {
            font-size: 1rem;
            padding: 8px 15px;
        }

        @media (max-width: 768px) {
            .menu-categories {
                grid-template-columns: 1fr;
            }
        }

        .btn-warning-custom {
            background: #ffc107;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            color: #000;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning-custom:hover {
            background: #ffb300;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .available-dates {
            padding: 20px;
            text-align: center;
        }

        .date-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .date-option button {
            padding: 15px;
            border: 1px solid #d4af37;
            background: white;
            transition: all 0.3s ease;
        }

        .date-option button:hover {
            background: #d4af37;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }

        .date-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .day-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .date-full {
            font-size: 0.9rem;
            color: #666;
        }

        .time-slot {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }

        .date-option button:hover .day-name,
        .date-option button:hover .date-full,
        .date-option button:hover .time-slot {
            color: white;
        }

        .available-dates-popup {
            border-radius: 15px;
        }

        .swal2-close {
            color: #d4af37 !important;
        }

        .swal2-close:hover {
            color: #333 !important;
        }

        .current-booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .countdown-timer {
            font-size: 1.5rem;
            font-weight: 600;
            color: #d4af37;
        }

        .time-remaining {
            display: inline-block;
            padding: 10px 20px;
            background: #fff;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .countdown-value {
            font-family: 'Courier New', monospace;
        }

        .available-dates-modal {
            
            align-items: center;
            justify-content: center;
        }

        .available-dates-popup {
            margin: 0 !important;
            border-radius: 15px;
            position: relative !important;
        }

        .date-options {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
            margin-right: -10px;
        }

        .date-options::-webkit-scrollbar {
            width: 6px;
        }

        .date-options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .date-options::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 10px;
        }

        .date-options::-webkit-scrollbar-thumb:hover {
            background: #b39030;
        }

        .swal2-close {
            position: absolute !important;
            right: 10px !important;
            top: 10px !important;
        }

        /* Ensure modals don't stack */
        .modal {
            z-index: 1050 !important;
        }

        .swal2-container {
            z-index: 1060 !important;
        }

        /* Update existing modal styles */
        .modal-backdrop {
            opacity: 0.5;
            z-index: 1040 !important;
        }

        .modal {
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050 !important;
        }

        .swal2-container {
            z-index: 1060 !important;
        }

        /* Add these new styles */
        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }

        .modal-dialog {
            margin: 1.75rem auto;
            max-width: 500px;
        }

        .package-modal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Update modal styles */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .available-date {
            text-align: center;
            padding: 20px;
        }

        .date-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .day-name {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .date-full {
            font-size: 1rem;
            color: #666;
        }

        #loadingMessage {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 9999;
        }

        .time-slot {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .current-booking-info p {
            margin-bottom: 0;
        }

        .overtime-info {
            font-size: 0.85rem;
            color: #666;
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Add to your existing styles */
        #reservationTypeIndicator {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #1976d2;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #reservationTypeIndicator i {
            font-size: 1.1rem;
        }

        .reserve-next-btn.btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        .reserve-next-btn.btn-secondary:hover {
            background-color: #5a6268;
            transform: none;
            box-shadow: none;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #1976d2;
        }

        .alert-info i {
            margin-right: 8px;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .btn-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            transform: none !important;
        }

        .package-unavailable-message {
            font-size: 0.8rem;
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            text-align: center;
        }

        .package-unavailable {
            opacity: 0.7;
            position: relative;
        }

        .package-unavailable::after {
            content: "Reserved";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        /* Add these new styles */
        .countdown-display {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            margin: 8px 0;
            font-family: 'Courier New', monospace;
            animation: pulse 2s infinite;
            width: 100%;
        }

        .countdown-timer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }

        .countdown-timer i {
            color: #d4af37;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        /* Add this CSS for the status badge */
        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 2;
            background-color: #28a745;
            color: white;
        }

        .status-badge i {
            color: white;
        }

        /* Add animation for status changes */
        @keyframes statusChange {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .status-badge.changing {
            animation: statusChange 0.3s ease;
        }

        .package-image {
            position: relative;
        }

        /* Add a semi-transparent overlay when package is occupied */
        .status-occupied + .package-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            pointer-events: none;
        }

        /* Add to your existing styles */
        .package-unavailable {
            position: relative;
            opacity: 0.7;
            pointer-events: none;
        }

        .package-unavailable::after {
            content: "This package is currently booked";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.1rem;
            z-index: 2;
            text-align: center;
            width: 80%;
        }

        .package-unavailable.currently-booked::after {
            content: "Currently Booked";
        }

        /* Exception for the Reserve Next button */
        .package-unavailable .reserve-next-btn {
            pointer-events: auto;
        }

        /* Add this CSS for error styling */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        /* Form validation styles */
        .is-invalid {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        /* Time input specific styles */
        input[type="time"].is-invalid::-webkit-calendar-picker-indicator {
            filter: invert(0.7) sepia(1) saturate(10000%) hue-rotate(320deg);
        }

        .btn-warning-custom {
            background-color: #ffc107;
            border: none;
            color: #000;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning-custom:hover {
            background-color: #ffb300;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .package-unavailable .btn-warning-custom {
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
            border-radius: 8px;
        }

        .alert-info i {
            color: #1976d2;
        }

        /* Add this section after the package details and before the button group */
        .booking-status-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .booking-dates, .vacant-dates-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .booking-date-item, .vacant-date-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .date-badge.booked {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .date-badge.available {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .no-bookings {
            padding: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }

        h6.text-muted {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .booking-status-container i {
            color: #6c757d;
        }

        /* Add to your existing styles */
        .existing-bookings {
            max-height: 200px;
            overflow-y: auto;
        }

        .booking-slot {
            margin-bottom: 8px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .booking-slot:last-child {
            margin-bottom: 0;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .text-left {
            text-align: left;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
        }

        .alert-danger {
            background-color: #ffebee;
            border-color: #ffcdd2;
            color: #c62828;
        }

        .text-warning {
            color: #f57c00 !important;
        }

        .booking-slot.alert-info {
            border-left: 4px solid #1976d2;
        }

        .booking-slot.alert-danger {
            border-left: 4px solid #d32f2f;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .booking-slot.alert-info {
            border-left: 4px solid #1976d2;
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .fas.fa-exclamation-circle {
            color: #dc3545;
        }

        /* Add to your existing styles */
        .booking-date-item {
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .booking-date-item:hover {
            transform: translateY(-2px);
        }

        .date-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 5px;
        }

        .date-badge.current-package {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .date-badge.other-package {
            background-color: #e3f2fd;
            color: #0d47a1;
            border: 1px solid #90caf9;
        }

        .booking-details {
            margin-left: 10px;
        }

        .booking-details strong {
            font-size: 0.9rem;
            color: #495057;
        }

        .booking-details small {
            font-size: 0.8rem;
        }

        .no-bookings {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }

        .booking-status-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .booking-dates {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .booking-dates::-webkit-scrollbar {
            width: 5px;
        }

        .booking-dates::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .booking-dates::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 10px;
        }

        /* Update the booking date styles */
        .booking-date-item {
            background: #fff;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .date-badge.booked {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            display: block;
            text-align: center;
        }

        .booking-status-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .booking-dates {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .no-bookings {
            text-align: center;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            color: #6c757d;
        }

        /* Add these styles to your existing style section */
        #paymentMethodDetails {
            transition: all 0.3s ease;
        }

        #paymentMethodDetails .card {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        #paymentMethodDetails .card-title {
            color: #333;
            font-weight: 600;
        }

        #qrCode {
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        #qrCode:hover {
            transform: scale(1.05);
        }

        .payment-info strong {
            color: #555;
            font-weight: 600;
        }

        /* Add or update these styles in your style section */
        #paymentMethodDetails {
            transition: all 0.3s ease;
            opacity: 0;
        }

        #paymentMethodDetails .card {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        #paymentMethodDetails .card-title {
            color: #333;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        #qrCode {
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        #qrCode:hover {
            transform: scale(1.05);
        }

        .payment-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .payment-info p {
            margin-bottom: 10px;
        }

        .payment-info strong {
            color: #555;
            font-weight: 600;
        }

        .payment-info i {
            color: #d4af37;
            width: 20px;
            text-align: center;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
        }

        .alert-info i {
            color: #1976d2;
        }

        /* Add these styles to your existing style section */
        #overtimeInfo .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        #overtimeInfo .card-header {
            background-color: #17a2b8;
            color: white;
            font-weight: 500;
            padding: 12px 20px;
        }

        #overtimeInfo .card-body {
            padding: 20px;
        }

        #overtimeInfo .text-info {
            color: #17a2b8 !important;
        }

        #overtimeInfo .text-success {
            color: #28a745 !important;
        }

        #overtimeInfo strong {
            color: #495057;
        }

        #overtimeInfo .alert-light {
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }

        #overtimeInfo small {
            color: #6c757d;
        }

        #overtimeInfo .fas {
            width: 20px;
            text-align: center;
        }

        /* Add these styles to your existing style section */
        .alert-info {
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            border-top: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }

        .alert-info i {
            color: #17a2b8;
            width: 20px;
            text-align: center;
        }

        .alert-info strong {
            color: #333;
        }

        .alert-info ul li {
            color: #666;
            font-size: 0.95rem;
            padding: 4px 0;
        }

        /* Add these styles to your existing style section */
        .booking-info-item {
            font-size: 0.9rem;
            color: #666;
            display: flex;
            align-items: center;
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
        }

        .booking-info-item i {
            font-size: 1rem;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .alert-info {
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            border-top: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 15px;
        }

        .alert-info strong {
            color: #333;
            font-size: 0.95rem;
        }

        /* Add these styles to your existing style section */
        .alert-info {
            transition: opacity 0.5s ease-out;
        }

        .alert-info:hover {
            opacity: 1 !important;
        }

        /* Add to your existing styles */
        .payment-proof-section {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
        }

        .payment-proof-section .form-label {
            font-weight: 500;
            color: #495057;
        }

        .payment-proof-section .form-control {
            border-color: #ced4da;
        }

        .payment-proof-section .form-control:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        #paymentProof {
            padding: 0.375rem;
            font-size: 0.9rem;
        }

        #referenceNumber {
            font-family: monospace;
            letter-spacing: 1px;
        }

        /* Add to your existing styles */
        .input-group .btn-outline-secondary {
            border-color: #ced4da;
            color: #6c757d;
        }

        .input-group .btn-outline-secondary:hover:not(:disabled) {
            background-color: #d4af37;
            border-color: #d4af37;
            color: white;
        }

        .input-group .btn-outline-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .input-group .btn-outline-secondary i {
            font-size: 0.9rem;
        }

        /* Add to your existing styles */
        .btn-link {
            color: #d4af37;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #b39030;
            text-decoration: underline;
        }

        .btn-link i {
            transition: transform 0.2s ease;
        }

        .btn-link:hover i {
            transform: scale(1.1);
        }

        #viewProofSummaryBtn {
            font-size: 0.9rem;
        }

        /* Add these CSS styles */
        .booking-date-item {
            background: #fff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .booking-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .time-slot {
            padding-left: 5px;
        }

        .status-badge {
            padding-left: 5px;
        }

        .status-badge i {
            font-size: 8px;
        }

        .date-badge.booked {
            width: 100%;
            text-align: left;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 8px 12px;
        }

        .booking-dates {
            max-height: 250px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .booking-dates::-webkit-scrollbar {
            width: 5px;
        }

        .booking-dates::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .booking-dates::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 10px;
        }

        /* Add these styles to your existing CSS */
        .booking-slot {
            margin-bottom: 10px;
            border-radius: 8px;
            border: none;
        }

        .booking-slot strong {
            color: #856404;
        }

        .existing-bookings {
            max-height: 200px;
            overflow-y: auto;
            margin: 15px 0;
        }

        .text-left {
            text-align: left;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .booking-status-container h6 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .btn-outline-warning {
            color: #d4af37;
            border-color: #d4af37;
        }

        .btn-outline-warning:hover {
            background-color: #d4af37;
            color: white;
        }

        .booking-date-item {
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .booking-date-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .date-badge.booked {
            display: block;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .time-slot {
            padding-left: 5px;
            color: #6c757d;
        }

        .no-bookings {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
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

        <!-- Gallery Section -->
        <section class="gallery-section">
            <div class="container">
                <h2 class="section-title">Event Gallery</h2>
                <div class="row gallery-container">
                    <?php foreach($gallery_images as $image): ?>
                    <div class="col-md-4">
                        <a href="<?php echo htmlspecialchars($image['image_path']); ?>" 
                           data-lightbox="event-gallery" 
                           data-title="<?php echo htmlspecialchars($image['caption']); ?>">
                            <div class="gallery-item">
                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['caption']); ?>">
                                <div class="gallery-caption"><?php echo htmlspecialchars($image['caption']); ?></div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Packages Section -->
        <section class="mb-5" id="packages">
            <div class="container">
            <h2 class="text-center mb-4">Our Event Packages</h2>
            <div class="row g-4">
                <?php foreach($packages as $package): ?>
                <div class="col-md-4">
                    <div class="custom-card h-100 fade-in <?php echo (isset($package['status']) && strtolower($package['status']) !== 'available') ? 'package-unavailable' : ''; ?>">
                        <!-- Status Badge -->
                        <div class="status-badge status-available">
                            <?php if ($package['status'] === 'Occupied'): ?>
                                <i class="fas fa-times-circle me-1"></i>Currently Not Available
                            <?php else: ?>
                                <i class="fas fa-check-circle me-1"></i>Available
                            <?php endif; ?>
                        </div>
                        
                        <!-- Update the image path handling -->
                        <?php
                        $imagePath = '';
                        if (!empty($package['image_path'])) {
                            // Check if the path starts with http(s)
                            if (preg_match('/^https?:\/\//', $package['image_path'])) {
                                $imagePath = $package['image_path'];
                            } else {
                                // Construct the correct absolute path from the root
                                $imagePath = '../../uploads/event_packages/' . basename($package['image_path']);
                            }
                        } else {
                            // Default image path
                            $imagePath = '../../uploads/event_packages/default.jpg';
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                             class="card-img-top package-image" 
                             alt="<?php echo htmlspecialchars($package['name']); ?>"
                             onerror="this.src='/casaaa/htdocs/Admin/uploads/event_packages/default.jpg'">
                        
                        <div class="card-body">
                            <!-- Package Title -->
                            <h4 class="card-title"><?php echo htmlspecialchars($package['name']); ?></h4>
                            
                            <!-- Package Price -->
                            <p class="price mb-3">₱<?php echo number_format($package['price'], 2); ?></p>
                            
                            <!-- Package Features -->
                            <ul class="features-list mb-4">
                                <?php 
                                        // Split description into features
                                $description = explode("\n", $package['description']);
                                foreach($description as $feature): 
                                            if(trim($feature)): // Only show non-empty features
                                ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                            </ul>

                            <!-- Replace the Current Bookings section in each package card -->
                            <div class="booking-status-container mb-3">
                                <h6 class="text-muted mb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-calendar-check me-2"></i>Booked Dates</span>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning" 
                                            onclick="viewBookedDates('<?php echo htmlspecialchars($package['name']); ?>')">
                                        <i class="fas fa-eye me-1"></i>View All
                                    </button>
                                </h6>
                                <?php
                                // Fetch booked dates count for this specific package
                                $sql = "SELECT COUNT(*) as booking_count
                                        FROM event_bookings eb
                                        WHERE eb.booking_status IN ('pending', 'confirmed')
                                        AND eb.package_name = :package_name
                                        AND eb.event_date >= CURRENT_DATE";
                                
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([':package_name' => $package['name']]);
                                $bookingCount = $stmt->fetch(PDO::FETCH_ASSOC)['booking_count'];
                                ?>
                                
                                <div class="text-center text-muted">
                                    <?php if ($bookingCount > 0): ?>
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Click "View All" to see <?php echo $bookingCount; ?> upcoming booking<?php echo $bookingCount > 1 ? 's' : ''; ?>
                                        </small>
                                    <?php else: ?>
                                        <small>No upcoming bookings for this package</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="button-group">
                                <!-- View Package Button -->
                                <button type="button" 
                                        class="btn btn-outline-primary-custom w-100 mb-2" 
                                        onclick="viewPackageDetails('<?php echo htmlspecialchars($package['name']); ?>')">
                                    <i class="fas fa-eye me-2"></i>View Package
                                </button>

                                <?php if ($package['status'] === 'Occupied'): ?>
                                    <!-- Not Available Button -->
                                    <button type="button" class="btn btn-secondary w-100 mb-2" disabled>
                                        Currently Not Available
                                    </button>
                                <?php else: ?>
                                    <!-- Book Now Button -->
                                    <button type="button" 
                                            class="btn btn-primary-custom w-100 mb-2" 
                                            onclick="openPackageModal('<?php echo htmlspecialchars($package['name']); ?>', <?php echo $package['price']; ?>)">
                                        <i class="fas fa-calendar-check me-2"></i>Book Now
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
    <?php include('footer.php'); ?>
    <!-- Booking Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Your Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bookingForm" action="event_functions.php" method="POST" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <!-- Add the feedback alerts container -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Important Information</strong>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    Operating hours: 6:30 AM - 11:00 PM
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Additional hours are charged at ₱2,000 per hour
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-users me-2"></i>
                                    Additional guests exceeding 50 are charged at ₱1,000 per person
                                </li>
                                <li>
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Save a screenshot of your payment for proof of transaction
                                </li>
                            </ul>
                        </div>

                        <div id="advanceBookingNote" class="alert alert-info mb-3" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Advance Booking:</strong> You are making a reservation for a future date.
                        </div>
                        
                        <input type="hidden" id="packageName" name="packageName">
                        <input type="hidden" id="packagePrice" name="packagePrice">
                        <input type="hidden" id="basePrice" name="basePrice">
                        <input type="hidden" id="overtimeHours" name="overtimeHours">
                        <input type="hidden" id="overtimeCharge" name="overtimeCharge">
                        <!-- Add this line with your other hidden inputs -->
                        <input type="hidden" id="bookingSource" name="bookingSource" value="Regular">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="eventDate" name="eventDate" required>
                                <div class="invalid-feedback">Please select a date.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" class="form-control" id="numberOfGuests" name="numberOfGuests" 
                                       min="1" max="50" required>
                                <div class="invalid-feedback">Please enter number of guests (1-50).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="startTime" name="startTime" required>
                                <small class="text-muted">Operating hours: 6:30 AM - 11:00 PM</small>
                                <div class="invalid-feedback">Please select a start time.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" id="endTime" name="endTime" required>
                                <small class="text-muted">Operating hours: 6:30 AM - 11:00 PM</small>
                                <div class="invalid-feedback">Please select an end time.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                    <option value="">Choose...</option>
                                    <?php 
                                    // Make sure $payment_methods is defined and is an array
                                    if (isset($payment_methods) && is_array($payment_methods)): 
                                        foreach ($payment_methods as $method): 
                                            if (isset($method['name']) && isset($method['display_name'])): ?>
                                                <option value="<?= htmlspecialchars($method['name']) ?>">
                                                    <?= htmlspecialchars($method['display_name']) ?>
                                                </option>
                                    <?php   endif;
                                        endforeach;
                                    endif; ?>
                                </select>
                                <div class="invalid-feedback">Please select a payment method.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" id="paymentType" name="paymentType" required>
                                    <option value="">Choose...</option>
                                    <option value="full">Full Payment</option>
                                    <option value="downpayment">Downpayment (50%)</option>
                                </select>
                                <div class="invalid-feedback">Please select a payment type.</div>
                            </div>
                            <!-- Add this after the number of guests field in the modal form -->
                            <div class="col-md-6">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" id="eventType" name="eventType" required>
                                    <option value="">Choose...</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Corporate">Corporate Event</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">Please select an event type.</div>
                            </div>
                            <!-- Add this div for "Other" event type specification -->
                            <div class="col-md-6" id="otherEventTypeContainer" style="display: none;">
                                <label class="form-label">Specify Event Type</label>
                                <input type="text" class="form-control" id="otherEventType" name="otherEventType">
                                <div class="invalid-feedback">Please specify the event type.</div>
                            </div>
                        </div>
                            <div class="col-12" id="paymentMethodDetails" style="display: none;">
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                        </h6>

                                        <!-- Update the payment proof section with preview functionality -->
                                        <div class="payment-proof-section mt-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Reference Number</label>
                                                    <input type="text" class="form-control" id="referenceNumber" name="referenceNumber">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Payment Screenshot</label>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control" id="paymentProof" name="paymentProof" accept="image/*">
                                                        <button class="btn btn-outline-secondary" type="button" id="viewProofBtn" disabled>
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12 text-end">
                                                    <button type="button" class="btn btn-primary-custom" id="proceedToPaymentBtn" onclick="proceedToPayment()">
                                                        <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                                                    </button>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                            </div>
   

                        <!-- Add this div for overtime information -->
                        <div id="overtimeInfo" style="display: none;" class="mt-3"></div>

                        <div class="mt-4">
                            <h5>Total Amount: <span id="totalAmount">₱0.00</span></h5>
                            <!-- Update the downpayment info div -->
                            <div id="downpaymentInfo" class="mt-3" style="display: none;"></div>
                        </div>

                        <!-- Add this reservation type indicator -->
                        <div id="reservationTypeIndicator" class="alert alert-info mb-3" style="display: none;">
                            <i class="fas fa-calendar-alt"></i> Advance Reservation
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary-custom" onclick="showSummaryModal()">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Replace the existing summaryModal -->
    <div class="modal fade" id="summaryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Booking Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="booking-summary">
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-box"></i>
                                <span>Package</span>
                            </div>
                            <div class="summary-value" id="summary-package"></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-calendar"></i>
                                <span>Date</span>
                            </div>
                            <div class="summary-value" id="summary-date"></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-clock"></i>
                                <span>Time</span>
                            </div>
                            <div class="summary-value" id="summary-time"></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-users"></i>
                                <span>Number of Guests</span>
                            </div>
                            <div class="summary-value" id="summary-guests"></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-credit-card"></i>
                                <span>Payment Details</span>
                            </div>
                            <div class="summary-value" id="summary-payment-method"></div>
                        </div>
                        <div class="summary-total">
                            <div id="summary-total-container">
                                <div class="total-row">
                                    <span>Total Amount</span>
                                    <span id="summary-total" class="amount"></span>
                                </div>
                            </div>
                            <div id="summary-downpayment-container" style="display: none;">
                                <div class="total-row">
                                    <span>Downpayment (50%)</span>
                                    <span id="summary-downpayment" class="amount"></span>
                                </div>
                                <div class="total-row">
                                    <span>Remaining Balance</span>
                                    <span id="summary-remaining" class="amount"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Add this to the summary modal content -->
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-receipt"></i>
                                <span>Reference Number</span>
                            </div>
                            <div class="summary-value" id="summary-reference"></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-image"></i>
                                <span>Payment Proof</span>
                            </div>
                            <div class="summary-value">
                                <button type="button" class="btn btn-link p-0" id="viewProofSummaryBtn">
                                    <i class="fas fa-eye me-1"></i>View Proof
                                </button>
                            </div>
                        </div>
                        <!-- Add this to the summary modal content -->
                        <div class="summary-row">
                            <div class="summary-label">
                                <i class="fas fa-calendar-day"></i>
                                <span>Event Type</span>
                            </div>
                            <div class="summary-value" id="summary-event-type"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary-custom" id="confirmBookingBtn" onclick="redirectToPayment()">
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Add this before the closing body tag -->
    <div class="modal fade" id="availabilityModal" tabindex="-1" aria-labelledby="availabilityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="availabilityModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="availabilityModalBody">
                </div>
            </div>
        </div>
    </div>

    <div id="loadingMessage" style="display: none;">
        Checking availability...
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize modals globally
        let packageModal;
        let summaryModal;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
            summaryModal = new bootstrap.Modal(document.getElementById('summaryModal'));

            // Set minimum date as today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('eventDate').min = today;

            // Handle payment type changes
            document.getElementById('paymentType').addEventListener('change', updatePaymentInfo);

            // Handle confirm booking
            document.getElementById('confirmBookingBtn').addEventListener('click', function() {
                const summaryModal = bootstrap.Modal.getInstance(document.getElementById('summaryModal'));
                summaryModal.hide();
                handleBookingSubmission();
            });

            // Add event listeners for real-time updates
            document.getElementById('startTime').addEventListener('change', updateModalDisplay);
            document.getElementById('endTime').addEventListener('change', updateModalDisplay);
            document.getElementById('paymentType').addEventListener('change', updateModalDisplay);

            // Initialize time restrictions
            validateOperatingHours();
            
            // Add event listeners for time inputs
            document.getElementById('startTime').addEventListener('change', function() {
                const endTimeInput = document.getElementById('endTime');
                if (this.value) {
                    // Set minimum end time to start time
                    endTimeInput.min = this.value;
                }
            });

            // Check availability for all packages on page load
            const packages = document.querySelectorAll('[data-package]');
            const checkedPackages = new Set();
            
            packages.forEach(element => {
                const packageName = element.getAttribute('data-package');
                if (!checkedPackages.has(packageName)) {
                    checkAndUpdatePackageAvailability(packageName);
                    checkedPackages.add(packageName);
                }
            });
        });

        // Update the openPackageModal function to check login first
        function openPackageModal(packageName, price, bookingType = 'Regular', preselectedDate = null) {
            // Check login status first
            fetch('check_login.php')
            .then(response => response.json())
            .then(data => {
                if (data.loggedIn) {
                    // User is logged in, proceed with booking
                    proceedWithBooking(packageName, price, bookingType, preselectedDate);
                } else {
                    // User is not logged in, show login prompt
                    Swal.fire({
                        title: 'Login Required',
                        text: 'Please login to book an event package',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Login Now',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Store current page URL in session storage
                            sessionStorage.setItem('redirectAfterLogin', window.location.href);
                            // Store package details to restore after login
                            sessionStorage.setItem('pendingPackageName', packageName);
                            sessionStorage.setItem('pendingPackagePrice', price);
                            sessionStorage.setItem('pendingBookingType', bookingType);
                            if (preselectedDate) {
                                sessionStorage.setItem('pendingPreselectedDate', preselectedDate);
                            }
                            // Redirect to login page with return URL
                            window.location.href = data.redirect;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        }

        // Add this function to handle the actual booking process
        function proceedWithBooking(packageName, price, bookingType = 'Regular', preselectedDate = null) {
            // First check if there's an advance booking
            fetch('check_events_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    packageName: packageName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_advance_booking) {
                    alert(data.message);
                    return;
                }
                
                // Continue with modal opening if no advance booking
                const modalElement = document.getElementById('packageModal');
                
                // Add hidden field for booking type
                let bookingTypeInput = document.getElementById('bookingType');
                if (!bookingTypeInput) {
                    bookingTypeInput = document.createElement('input');
                    bookingTypeInput.type = 'hidden';
                    bookingTypeInput.id = 'bookingType';
                    bookingTypeInput.name = 'bookingType';
                    document.getElementById('bookingForm').appendChild(bookingTypeInput);
                }
                bookingTypeInput.value = bookingType;
                
                // Show/hide advance booking note
                const advanceBookingNote = document.getElementById('advanceBookingNote');
                if (advanceBookingNote) {
                    advanceBookingNote.style.display = bookingType === 'Advance' ? 'block' : 'none';
                }
                
                // Set form values
                document.getElementById('packageName').value = packageName;
                document.getElementById('packagePrice').value = price;
                document.getElementById('basePrice').value = price; // Store original base price
                
                // Set date if provided (for advance booking)
                if (preselectedDate) {
                    const eventDateInput = document.getElementById('eventDate');
                    eventDateInput.value = preselectedDate;
                    eventDateInput.min = preselectedDate;
                } else {
                    // For regular booking, set minimum date as today
                    document.getElementById('eventDate').min = new Date().toISOString().split('T')[0];
                }
                
                // Show modal
                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                // Set the booking source
                document.getElementById('bookingSource').value = bookingType;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong! Please try again.');
            });
        }

        // Add this to check for pending booking after login
        document.addEventListener('DOMContentLoaded', function() {
            // Check for stored package details after login
            const pendingPackageName = sessionStorage.getItem('pendingPackageName');
            const pendingPackagePrice = sessionStorage.getItem('pendingPackagePrice');
            const pendingBookingType = sessionStorage.getItem('pendingBookingType');
            const pendingPreselectedDate = sessionStorage.getItem('pendingPreselectedDate');
            
            if (pendingPackageName && pendingPackagePrice) {
                // Clear stored data
                sessionStorage.removeItem('pendingPackageName');
                sessionStorage.removeItem('pendingPackagePrice');
                sessionStorage.removeItem('pendingBookingType');
                sessionStorage.removeItem('pendingPreselectedDate');
                
                // Proceed with booking
                proceedWithBooking(
                    pendingPackageName, 
                    parseFloat(pendingPackagePrice), 
                    pendingBookingType || 'Regular',
                    pendingPreselectedDate || null
                );
            }
        });

        // Update the MAX_GUESTS constant in the JavaScript section
        const MAX_GUESTS = 50;
        const MAX_HOURS = 5;
        const BASE_OVERTIME_RATE = 2000;
        const PREMIUM_OVERTIME_RATE = 3000;
        const PREMIUM_TIME_THRESHOLD = '14:00';

        // Add this right after your existing constants at the top of your JavaScript
        const OPENING_TIME = "06:30"; // 6:30 AM
        const CLOSING_TIME = "23:00"; // 11:00 PM

        // Add this constant to your existing constants at the top of your JavaScript
        const MAX_GUESTS_WITHOUT_CHARGE = 50;
        const EXTRA_GUEST_CHARGE = 1000;

        function validateBooking() {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;

            // Calculate duration in hours
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            const durationHours = (end - start) / (1000 * 60 * 60);

            if (durationHours <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'End time must be after start time.'
                });
                return false;
            }

            return true;
        }

        // Update the calculateTotalAmount function to include extra guest charges
        function calculateTotalAmount() {
            // Read from basePrice if it exists, otherwise fall back to packagePrice
            const basePriceInput = document.getElementById('basePrice');
            const packagePriceInput = document.getElementById('packagePrice');
            const basePrice = parseFloat(basePriceInput && basePriceInput.value ? basePriceInput.value : packagePriceInput.value);
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;

            // Calculate duration in hours
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            const durationHours = (end - start) / (1000 * 60 * 60);

            // Calculate overtime charges
            const overtimeHours = Math.max(0, durationHours - MAX_HOURS);
            const overtimeCharge = Math.ceil(overtimeHours) * BASE_OVERTIME_RATE;
            
            // Calculate extra guest charges
            const extraGuests = Math.max(0, guests - MAX_GUESTS_WITHOUT_CHARGE);
            const extraGuestCharge = extraGuests * EXTRA_GUEST_CHARGE;
            
            const totalAmount = basePrice + overtimeCharge + extraGuestCharge;

            return {
                basePrice,
                overtimeHours,
                overtimeCharge,
                extraGuests,
                extraGuestCharge,
                totalAmount
            };
        }

        // Update the showSummaryModal function to include extra guest charges in the summary
        function showSummaryModal() {
            if (!validateBookingForm()) {
                return;
            }

            try {
                // Get form values
                const packageName = document.getElementById('packageName').value;
                const eventDate = new Date(document.getElementById('eventDate').value)
                    .toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                const startTime = document.getElementById('startTime').value;
                const endTime = document.getElementById('endTime').value;
                const guests = document.getElementById('numberOfGuests').value;
                const paymentMethod = document.getElementById('paymentMethod').value;
                const paymentType = document.getElementById('paymentType').value;

                // Calculate amounts
                const { basePrice, overtimeHours, overtimeCharge, extraGuests, extraGuestCharge, totalAmount } = calculateTotalAmount();

                // Update summary modal content
                document.getElementById('summary-package').textContent = packageName;
                document.getElementById('summary-date').textContent = eventDate;
                document.getElementById('summary-time').textContent = `${formatTime(startTime)} - ${formatTime(endTime)}`;
                document.getElementById('summary-guests').textContent = guests;
                document.getElementById('summary-payment-method').textContent = `${paymentMethod} - ${paymentType}`;

                // Clear previous content
                const totalContainer = document.getElementById('summary-total-container');
                const downpaymentContainer = document.getElementById('summary-downpayment-container');
                totalContainer.innerHTML = '';

                // Add base price row
                const basePriceRow = document.createElement('div');
                basePriceRow.className = 'total-row';
                basePriceRow.innerHTML = `
                    <span>Base Price</span>
                    <span class="amount">₱${basePrice.toLocaleString()}</span>
                `;
                totalContainer.appendChild(basePriceRow);

                // Add overtime charges if applicable
                if (overtimeHours > 0) {
                    const overtimeRow = document.createElement('div');
                    overtimeRow.className = 'total-row';
                    overtimeRow.innerHTML = `
                        <span>Overtime Charge (${Math.ceil(overtimeHours)} hours)</span>
                        <span class="amount">₱${overtimeCharge.toLocaleString()}</span>
                    `;
                    totalContainer.appendChild(overtimeRow);
                }

                // Add extra guest charges if applicable
                if (extraGuests > 0) {
                    const extraGuestRow = document.createElement('div');
                    extraGuestRow.className = 'total-row';
                    extraGuestRow.innerHTML = `
                        <span>Extra Guest Charge (${extraGuests} guests × ₱${EXTRA_GUEST_CHARGE.toLocaleString()})</span>
                        <span class="amount">₱${extraGuestCharge.toLocaleString()}</span>
                    `;
                    totalContainer.appendChild(extraGuestRow);
                }

                // Add total amount row
                const totalRow = document.createElement('div');
                totalRow.className = 'total-row font-weight-bold mt-2 border-top pt-2';
                totalRow.innerHTML = `
                    <span>Total Amount</span>
                    <span class="amount">₱${totalAmount.toLocaleString()}</span>
                `;
                totalContainer.appendChild(totalRow);

                // Handle payment type display
                if (paymentType === 'downpayment') {
                    const downpayment = totalAmount * 0.5;
                    
                    // Clear and rebuild the downpayment container with breakdown
                    downpaymentContainer.innerHTML = '';
                    
                    // Add breakdown rows
                    const breakdownTitle = document.createElement('div');
                    breakdownTitle.className = 'total-row font-weight-bold';
                    breakdownTitle.innerHTML = `<span colspan="2" style="text-align: center; display: block; margin-bottom: 10px;">Payment Breakdown</span>`;
                    downpaymentContainer.appendChild(breakdownTitle);
                    
                    // Base price row
                    const basePriceRow = document.createElement('div');
                    basePriceRow.className = 'total-row';
                    basePriceRow.innerHTML = `
                        <span>Base Price</span>
                        <span class="amount">₱${basePrice.toLocaleString()}</span>
                    `;
                    downpaymentContainer.appendChild(basePriceRow);
                    
                    // Overtime charge row (if applicable)
                    if (overtimeHours > 0) {
                        const overtimeRow = document.createElement('div');
                        overtimeRow.className = 'total-row';
                        overtimeRow.innerHTML = `
                            <span>Overtime Charge (${Math.ceil(overtimeHours)} hours)</span>
                            <span class="amount">₱${overtimeCharge.toLocaleString()}</span>
                        `;
                        downpaymentContainer.appendChild(overtimeRow);
                    }
                    
                    // Extra guest charge row (if applicable)
                    if (extraGuests > 0) {
                        const extraGuestRow = document.createElement('div');
                        extraGuestRow.className = 'total-row';
                        extraGuestRow.innerHTML = `
                            <span>Extra Guest Charge (${extraGuests} guests × ₱${EXTRA_GUEST_CHARGE.toLocaleString()})</span>
                            <span class="amount">₱${extraGuestCharge.toLocaleString()}</span>
                        `;
                        downpaymentContainer.appendChild(extraGuestRow);
                    }
                    
                    // Total amount row
                    const totalAmountRow = document.createElement('div');
                    totalAmountRow.className = 'total-row font-weight-bold border-top pt-2 mt-2';
                    totalAmountRow.innerHTML = `
                        <span>Total Amount</span>
                        <span class="amount">₱${totalAmount.toLocaleString()}</span>
                    `;
                    downpaymentContainer.appendChild(totalAmountRow);
                    
                    // Downpayment row
                    const downpaymentRow = document.createElement('div');
                    downpaymentRow.className = 'total-row';
                    downpaymentRow.innerHTML = `
                        <span>Downpayment (50%)</span>
                        <span class="amount">₱${downpayment.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                    `;
                    downpaymentContainer.appendChild(downpaymentRow);
                    
                    // Remaining balance row
                    const remainingRow = document.createElement('div');
                    remainingRow.className = 'total-row';
                    remainingRow.innerHTML = `
                        <span>Remaining Balance</span>
                        <span class="amount">₱${downpayment.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                    `;
                    downpaymentContainer.appendChild(remainingRow);
                    
                    totalContainer.style.display = 'none';
                    downpaymentContainer.style.display = 'block';
                } else {
                    totalContainer.style.display = 'block';
                    downpaymentContainer.style.display = 'none';
                }

                // Update hidden form fields
                document.getElementById('packagePrice').value = totalAmount;
                document.getElementById('basePrice').value = basePrice;
                document.getElementById('overtimeHours').value = Math.ceil(overtimeHours);
                document.getElementById('overtimeCharge').value = overtimeCharge;
                
                // Add hidden field for extra guest charge if it doesn't exist
                let extraGuestChargeInput = document.getElementById('extraGuestCharge');
                if (!extraGuestChargeInput) {
                    extraGuestChargeInput = document.createElement('input');
                    extraGuestChargeInput.type = 'hidden';
                    extraGuestChargeInput.id = 'extraGuestCharge';
                    extraGuestChargeInput.name = 'extraGuestCharge';
                    document.getElementById('bookingForm').appendChild(extraGuestChargeInput);
                }
                extraGuestChargeInput.value = extraGuestCharge;

                // Close package modal and show summary modal
                packageModal.hide();
                summaryModal.show();

                // Add reference number to summary
                document.getElementById('summary-reference').textContent = 
                    document.getElementById('referenceNumber').value;

                // Store the payment proof file for preview
                const paymentProofFile = document.getElementById('paymentProof').files[0];
                
                // Handle View Proof button visibility based on payment type
                const viewProofRow = document.querySelector('.summary-row:has(#viewProofSummaryBtn)');
                if (viewProofRow) {
                    if (paymentType === 'downpayment') {
                        viewProofRow.style.display = 'none';
                    } else {
                        viewProofRow.style.display = 'flex';
                        // Add click handler for viewing proof in summary
                        document.getElementById('viewProofSummaryBtn').onclick = function() {
                            if (paymentProofFile) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    Swal.fire({
                                        title: 'Payment Proof',
                                        imageUrl: e.target.result,
                                        imageAlt: 'Payment Proof',
                                        width: 600,
                                        confirmButtonColor: '#d4af37',
                                        confirmButtonText: 'Close'
                                    });
                                };
                                reader.readAsDataURL(paymentProofFile);
                            }
                        };
                    }
                }

                // Add this to your showSummaryModal function
                document.getElementById('summary-event-type').textContent = 
                    document.getElementById('eventType').value === 'Other' 
                        ? document.getElementById('otherEventType').value 
                        : document.getElementById('eventType').value;

            } catch (error) {
                console.error('Error showing summary:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'There was an error showing the booking summary. Please try again.'
                });
            }
        }

        function formatTime(time) {
            return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        }

        function updatePaymentInfo() {
            const price = parseFloat(document.getElementById('packagePrice').value);
            const paymentType = document.getElementById('paymentType').value;
            const downpaymentInfo = document.getElementById('downpaymentInfo');
            
            document.getElementById('totalAmount').textContent = `₱${price.toLocaleString()}`;
            
            if (paymentType === 'downpayment') {
                const downpayment = price * 0.5;
                document.getElementById('downpaymentAmount').textContent = 
                    `₱${downpayment.toLocaleString()}`;
                downpaymentInfo.classList.remove('d-none');
            } else {
                downpaymentInfo.classList.add('d-none');
            }
        }


        // Add this function to update the modal display in real-time
        function updateModalDisplay() {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const guests = parseInt(document.getElementById('numberOfGuests').value) || 0;
            const paymentType = document.getElementById('paymentType').value;
            // Read from basePrice if it exists, otherwise fall back to packagePrice
            const basePriceInput = document.getElementById('basePrice');
            const packagePriceInput = document.getElementById('packagePrice');
            const basePrice = parseFloat(basePriceInput && basePriceInput.value ? basePriceInput.value : packagePriceInput.value);
            const totalAmountDisplay = document.getElementById('totalAmount');
            const downpaymentInfo = document.getElementById('downpaymentInfo');
            const overtimeInfo = document.getElementById('overtimeInfo');

            if (startTime && endTime) {
                // Convert times to Date objects for comparison
                const start = new Date(`2000-01-01T${startTime}`);
                const end = new Date(`2000-01-01T${endTime}`);
                const openingTime = new Date(`2000-01-01T${OPENING_TIME}`);
                const closingTime = new Date(`2000-01-01T${CLOSING_TIME}`);

                // Check if times are within operating hours
                if (start < openingTime || end > closingTime) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time Selection',
                        text: 'Our operating hours are from 6:30 AM to 11:00 PM.',
                        confirmButtonColor: '#d4af37'
                    });
                    // Reset time inputs
                    document.getElementById('startTime').value = '';
                    document.getElementById('endTime').value = '';
                    return;
                }

                // Calculate duration and charges
                const durationHours = Math.ceil((end - start) / (1000 * 60 * 60));
                const overtimeHours = Math.max(0, durationHours - MAX_HOURS);
                const overtimeCharge = overtimeHours * BASE_OVERTIME_RATE;
                
                // Calculate extra guest charges
                const extraGuests = Math.max(0, guests - MAX_GUESTS_WITHOUT_CHARGE);
                const extraGuestCharge = extraGuests * EXTRA_GUEST_CHARGE;
                
                const totalAmount = basePrice + overtimeCharge + extraGuestCharge;

                // Store overtime values in hidden fields
                document.getElementById('overtimeHours').value = overtimeHours;
                document.getElementById('overtimeCharge').value = overtimeCharge;

                // Update overtime information display
                if (overtimeHours > 0) {
                    overtimeInfo.innerHTML = `
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Extended Hours Booking</strong>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <span class="d-block">Additional Hours: ${overtimeHours}</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="d-block">Overtime Fee: ₱${overtimeCharge.toLocaleString()}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    overtimeInfo.style.display = 'block';
                } else {
                    overtimeInfo.style.display = 'none';
                }

                // Add extra guest information display
                let extraGuestInfo = document.getElementById('extraGuestInfo');
                if (!extraGuestInfo) {
                    extraGuestInfo = document.createElement('div');
                    extraGuestInfo.id = 'extraGuestInfo';
                    overtimeInfo.parentNode.insertBefore(extraGuestInfo, overtimeInfo.nextSibling);
                }

                if (extraGuests > 0) {
                    extraGuestInfo.innerHTML = `
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-users me-2"></i>
                                <strong>Extra Guest Charges</strong>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <span class="d-block">Extra Guests: ${extraGuests}</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="d-block">Additional Fee: ₱${extraGuestCharge.toLocaleString()}</span>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Additional charge of ₱1,000 per guest exceeding 50 guests
                            </small>
                        </div>
                    `;
                    extraGuestInfo.style.display = 'block';
                } else {
                    extraGuestInfo.style.display = 'none';
                }

                // Update total amount display
                totalAmountDisplay.textContent = `₱${totalAmount.toLocaleString()}`;
            }
        }

        // Add this function to validate operating hours
        function validateOperatingHours() {
            const startTime = document.getElementById('startTime');
            const endTime = document.getElementById('endTime');
            
            // Set operating hours
            startTime.min = "06:29";
            startTime.max = "23:00";
            endTime.min = "06:29";
            endTime.max = "23:00";
        }

        // Update the enablePackageButtons function
        function enablePackageButtons(packageName) {
            const packageCards = document.querySelectorAll('.custom-card');
            packageCards.forEach(card => {
                const packageBtn = card.querySelector(`[data-package="${packageName}"]`);
                if (packageBtn) {
                    const buttonGroup = packageBtn.closest('.button-group');
                    if (buttonGroup) {
                        // Show Book Now button
                        const bookNowBtn = buttonGroup.querySelector('.btn-primary-custom');
                        if (bookNowBtn) {
                            bookNowBtn.style.display = 'block';
                        }
                        
                        // Hide Not Available button
                        const notAvailableBtn = buttonGroup.querySelector('.btn-secondary');
                        if (notAvailableBtn) {
                            notAvailableBtn.style.display = 'none';
                        }
                        
                        // Hide Advance Booking button
                        const advanceBookingBtn = buttonGroup.querySelector('.btn-warning-custom');
                        if (advanceBookingBtn) {
                            advanceBookingBtn.style.display = 'none';
                        }
                    }
                }
            });
        }

        // Update the disablePackageButtons function
        function disablePackageButtons(packageName, message = 'Not Available') {
            const packageCards = document.querySelectorAll('.custom-card');
            packageCards.forEach(card => {
                const packageBtn = card.querySelector(`[data-package="${packageName}"]`);
                if (packageBtn) {
                    const buttonGroup = packageBtn.closest('.button-group');
                    if (buttonGroup) {
                        // Hide Book Now button
                        const bookNowBtn = buttonGroup.querySelector('.btn-primary-custom');
                        if (bookNowBtn) {
                            bookNowBtn.style.display = 'none';
                        }
                        
                        // Show Not Available button
                        const notAvailableBtn = buttonGroup.querySelector('.btn-secondary');
                        if (notAvailableBtn) {
                            notAvailableBtn.style.display = 'block';
                        }
                        
                        // Show Advance Booking button
                        const advanceBookingBtn = buttonGroup.querySelector('.btn-warning-custom');
                        if (advanceBookingBtn) {
                            advanceBookingBtn.style.display = 'block';
                        }
                    }
                }
            });
        }

        // Update the updateButtonCountdown function
        function updateButtonCountdown(countdownDiv, endDate) {
            function update() {
                const now = new Date();
                const timeDiff = endDate - now;
                
                if (timeDiff <= 0) {
                    countdownDiv.remove();
                    // Trigger immediate availability check when countdown ends
                    checkAndUpdatePackageAvailability();
                    return;
                }
                
                const hours = Math.floor(timeDiff / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                
                countdownDiv.innerHTML = `
                    <div class="countdown-timer">
                        <i class="fas fa-clock"></i>
                        Available in: ${hours}h ${minutes}m ${seconds}s
                    </div>
                `;
                setTimeout(update, 1000);
            }
            
            update();
        }

        // Update the checkAndUpdatePackageAvailability function
        function checkAndUpdatePackageAvailability() {
            fetch('check_events_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    checkAll: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const packages = document.querySelectorAll('[data-package]');
                    packages.forEach(element => {
                        const packageCard = element.closest('.custom-card');
                        const statusBadge = packageCard.querySelector('.status-badge');
                        const packageName = element.getAttribute('data-package');
                        
                        // Always show as Available
                            statusBadge.className = 'status-badge status-available';
                            statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Available';
                            enablePackageButtons(packageName);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Check availability more frequently
        setInterval(checkAndUpdatePackageAvailability, 5000); // Check every 5 seconds

        // Call this when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            checkAndUpdatePackageAvailability();
            // Check every 30 seconds
            setInterval(checkAndUpdatePackageAvailability, 30000);
        });

        // Add this function for form validation
        function validateBookingForm() {
            // Get form elements
            const eventDate = document.getElementById('eventDate');
            const numberOfGuests = document.getElementById('numberOfGuests');
            const startTime = document.getElementById('startTime');
            const endTime = document.getElementById('endTime');
            const paymentMethod = document.getElementById('paymentMethod');
            const paymentType = document.getElementById('paymentType');

            // Clear previous error messages
            clearErrorMessages();

            let isValid = true;
            const errors = [];

            // Validate Event Date
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(eventDate.value);
            selectedDate.setHours(0, 0, 0, 0);

            if (!eventDate.value) {
                errors.push("Please select an event date");
                showError(eventDate, "Please select an event date");
                isValid = false;
            } else if (selectedDate < today) {
                errors.push("Event date cannot be in the past");
                showError(eventDate, "Event date cannot be in the past");
                isValid = false;
            }

            // Validate Number of Guests
            if (!numberOfGuests.value) {
                errors.push("Please enter number of guests");
                showError(numberOfGuests, "Please enter number of guests");
                isValid = false;
            } else if (numberOfGuests.value < 1) {
                errors.push("Number of guests must be at least 1");
                showError(numberOfGuests, "Number of guests must be at least 1");
                isValid = false;
            }

            // Validate Time
            if (!startTime.value) {
                errors.push("Please select a start time");
                showError(startTime, "Please select a start time");
                isValid = false;
            }

            if (!endTime.value) {
                errors.push("Please select an end time");
                showError(endTime, "Please select an end time");
                isValid = false;
            }

            if (startTime.value && endTime.value) {
                const start = new Date(`2000-01-01T${startTime.value}`);
                const end = new Date(`2000-01-01T${endTime.value}`);
                const openingTime = new Date(`2000-01-01T${OPENING_TIME}`);
                const closingTime = new Date(`2000-01-01T${CLOSING_TIME}`);

                // Check operating hours
                if (start < openingTime || end > closingTime) {
                    errors.push("Operating hours are from 6:30 AM to 11:00 PM");
                    showError(document.getElementById('startTime'), "Invalid time selection");
                    showError(document.getElementById('endTime'), "Invalid time selection");
                    isValid = false;
                }

                // Check if end time is after start time
                if (end <= start) {
                    errors.push("End time must be after start time");
                    showError(endTime, "End time must be after start time");
                    isValid = false;
                }
            }

            // Validate Payment Method
            if (!paymentMethod.value) {
                errors.push("Please select a payment method");
                showError(paymentMethod, "Please select a payment method");
                isValid = false;
            }

            // Validate Payment Type
            if (!paymentType.value) {
                errors.push("Please select a payment type");
                showError(paymentType, "Please select a payment type");
                isValid = false;
            }

            // Validate Event Type
            const eventType = document.getElementById('eventType');
            if (!eventType.value) {
                errors.push("Please select an event type");
                showError(eventType, "Please select an event type");
                isValid = false;
            }

            // Validate Other Event Type if "Other" is selected
            if (eventType.value === 'Other') {
                const otherEventType = document.getElementById('otherEventType');
                if (!otherEventType.value.trim()) {
                    errors.push("Please specify the event type");
                    showError(otherEventType, "Please specify the event type");
                    isValid = false;
                }
            }

            if (!isValid) {
                // Show error summary using SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Form Validation Error',
                    html: errors.map(error => `<p>${error}</p>`).join(''),
                    confirmButtonColor: '#d4af37'
                });
            }

            return isValid;
        }

        // Helper function to show error message
        function showError(element, message) {
            // Add error class to form element
            element.classList.add('is-invalid');
            
            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            
            // Add error message after the element
            element.parentNode.appendChild(errorDiv);
        }

        // Helper function to clear error messages
        function clearErrorMessages() {
            // Remove all error classes
            document.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });
            
            // Remove all error messages
            document.querySelectorAll('.invalid-feedback').forEach(element => {
                element.remove();
            });
        }
    </script>
    <script>
        // Auto-hide alert after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(function() {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.remove();
                    }, 150);
                }, 5000);
            }
        });
    </script>
    <script>
        // Add this function to handle viewing package details
        function viewPackageDetails(packageName) {
            // Show loading state
            Swal.fire({
                title: 'Loading package details...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch package details
            fetch('get_package_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    packageName: packageName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const package = data.package;
                    
                    // Create menu items HTML if available
                    let menuItemsHtml = '';
                    if (Object.keys(package.menu_items).length > 0 && !package.is_venue_only) {
                        menuItemsHtml = `
                            <div class="package-section">
                                <h5 class="section-title">
                                    <i class="fas fa-utensils me-2"></i>Menu Inclusions
                                </h5>
                                <div class="menu-categories">
                                    ${Object.entries(package.menu_items).map(([category, items]) => `
                                        <div class="menu-category">
                                            <h6><i class="fas fa-${getCategoryIcon(category)} me-2"></i>${category}</h6>
                                            <ul>
                                                    ${items.map(item => `<li>${item}</li>`).join('')}
                                                </ul>
                                            </div>
                                        `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    // Show the modal with package details
                    Swal.fire({
                        title: package.name,
                        html: `
                            <div class="package-details-container">
                                
                                <div class="price-tag">
                                    <i class="fas fa-tag me-2"></i>₱${package.price.toLocaleString()}
                                </div>
                                
                    <div class="package-section">
                        <h5 class="section-title">
                                        <i class="fas fa-info-circle me-2"></i>Package Details
                        </h5>
                        <ul class="details-list">
                                        ${package.details.map(detail => 
                                `<li><i class="fas fa-${detail.icon} me-2"></i>${detail.text}</li>`
                            ).join('')}
                        </ul>
                    </div>

                                ${menuItemsHtml}
                                
                    <div class="package-section">
                        <h5 class="section-title">
                                        <i class="fas fa-exclamation-circle me-2"></i>Important Notes
                        </h5>
                        <ul class="notes-list">
                                        ${package.notes.map(note => `<li>${note}</li>`).join('')}
                        </ul>
                    </div>
                                
                                <div class="package-status mt-3">
                                    <span class="badge ${package.status.toLowerCase() === 'available' ? 'bg-success' : 'bg-danger'}">
                                        <i class="fas fa-${package.status.toLowerCase() === 'available' ? 'check-circle' : 'times-circle'} me-1"></i>
                                        ${package.status}
                                    </span>
                                </div>
                    </div>
                `,
                        width: '700px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                            popup: 'package-details-modal'
                }
            });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to load package details'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load package details'
                });
            });
        }

        // Helper function to get icons for menu categories
        function getCategoryIcon(category) {
            const icons = {
                'Appetizers': 'leaf',
                'Pasta': 'wheat-awn',
                'Mains': 'drumstick-bite',
                'Sides': 'bowl-food',
                'Desserts': 'ice-cream',
                'Drinks': 'glass-water'
            };
            return icons[category] || 'utensils';
        }

        function formatCategoryName(category) {
            return category.charAt(0).toUpperCase() + category.slice(1);
        }

        function calculateOvertimeCharge() {
            const endTime = document.getElementById('endTime').value;
            const baseEndTime = document.getElementById('baseEndTime').value;
            
            if (!endTime || !baseEndTime) return 0;
            
            const endDateTime = new Date(`2000-01-01 ${endTime}`);
            const baseEndDateTime = new Date(`2000-01-01 ${baseEndTime}`);
            const timeDiff = (endDateTime - baseEndDateTime) / (1000 * 60 * 60); // Convert to hours
            
            if (timeDiff <= 0) return 0;
            
            // Check if end time is after 2 PM
            const isPremiumTime = endTime >= PREMIUM_TIME_THRESHOLD;
            const overtimeRate = isPremiumTime ? PREMIUM_OVERTIME_RATE : BASE_OVERTIME_RATE;
            
            return Math.ceil(timeDiff) * overtimeRate;
        }
    </script>
    <script>
    function openAdvanceBookingModal(packageName, price) {
            // First check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                text: 'Please login to make an advance booking',
                    confirmButtonColor: '#d4af37'
                });
                return;
            <?php endif; ?>

        // Show calendar for advance booking
            Swal.fire({
            title: 'Select Booking Date',
            html: `
                <div class="mb-3">
                    <label class="form-label">Select your preferred date:</label>
                    <input type="date" id="advanceBookingDate" class="form-control" min="${getTomorrowDate()}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Continue',
            confirmButtonColor: '#d4af37',
            cancelButtonColor: '#6c757d',
                didOpen: () => {
                // Set minimum date as tomorrow
                const tomorrow = getTomorrowDate();
                document.getElementById('advanceBookingDate').min = tomorrow;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedDate = document.getElementById('advanceBookingDate').value;
                if (!selectedDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Date Required',
                        text: 'Please select a date for your booking',
                        confirmButtonColor: '#d4af37'
                    });
                    return;
                }

                // Check if date is already booked
                fetch('check_date_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    packageName: packageName,
                        date: selectedDate
                })
            })
            .then(response => response.json())
            .then(data => {
                    if (data.isBooked) {
                        // Check if bookings are in other packages
                        const otherPackageBookings = data.bookings.filter(booking => booking.package_name !== packageName);
                        const samePackageBookings = data.bookings.filter(booking => booking.package_name === packageName);

                        if (otherPackageBookings.length > 0) {
                            // Show warning for bookings in other packages without continue option
                        Swal.fire({
                                icon: 'warning',
                                title: 'Notice: Other Events Scheduled',
                            html: `
                                    <div class="text-left">
                                        <p>There are events scheduled in other packages on ${new Date(selectedDate).toLocaleDateString('en-US', { 
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}:</p>
                                        <div class="existing-bookings mt-3">
                                            ${otherPackageBookings.map(booking => `
                                                <div class="booking-slot alert alert-info">
                                                    <i class="fas fa-calendar-alt me-2"></i>
                                                    <strong>${booking.package_name}</strong><br>
                                                    Time: ${booking.start_time} - ${booking.end_time}
                                            </div>
                                        `).join('')}
                                    </div>
                                        <p class="mt-3 text-danger">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            For safety and quality of service, we cannot accommodate multiple events on the same date.
                                        </p>
                                </div>
                            `,
                                confirmButtonText: 'Choose Different Date',
                                confirmButtonColor: '#6c757d'
                            });
                        } else if (samePackageBookings.length > 0) {
                            // Show warning for same package conflicts
                            showSamePackageConflict(packageName, selectedDate, samePackageBookings, price);
                    }
                } else {
                        // Date is completely available
                        openPackageModal(packageName, price, selectedDate, 'Advance Booking');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                        text: 'Failed to check date availability. Please try again.',
                    confirmButtonColor: '#d4af37'
                });
            });
        }
        });
    }

    // Helper function to show same package conflict warning
    function showSamePackageConflict(packageName, selectedDate, bookings, price) {
        Swal.fire({
            icon: 'error',
            title: 'Time Slot Not Available',
            html: `
                <div class="text-left">
                    <p>This package is already booked for the following times on ${new Date(selectedDate).toLocaleDateString('en-US', { 
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}:</p>
                    <div class="existing-bookings mt-3">
                        ${bookings.map(booking => `
                            <div class="booking-slot alert alert-danger">
                                <i class="fas fa-clock me-2"></i>
                                Time: ${booking.start_time} - ${booking.end_time}
                            </div>
                        `).join('')}
                    </div>
                    <p class="mt-3">Please select a different date.</p>
                </div>
            `,
            confirmButtonText: 'Choose Different Date',
            confirmButtonColor: '#d4af37'
        });
    }

    function getTomorrowDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toISOString().split('T')[0];
        }
    </script>

    <script>
        // Function to handle the info notice timer
        function handleInfoNotice() {
            const infoNotices = document.querySelectorAll('.alert-info');
            infoNotices.forEach(infoNotice => {
                if (infoNotice) {
                    // Add fade out animation class
                    infoNotice.style.transition = 'opacity 0.5s ease-out';
                    
                    // Set timer to hide the notice
                    setTimeout(() => {
                        infoNotice.style.opacity = '0';
                        setTimeout(() => {
                            infoNotice.style.display = 'none';
                        }, 500);
                    }, 3000); // 3 seconds

                    // Show notice again when hovering
                    infoNotice.addEventListener('mouseenter', () => {
                        infoNotice.style.opacity = '1';
                        infoNotice.style.display = 'block';
                    });

                    // Hide notice when mouse leaves
                    infoNotice.addEventListener('mouseleave', () => {
                        infoNotice.style.opacity = '0';
                        setTimeout(() => {
                            infoNotice.style.display = 'none';
                        }, 500);
                    });
                }
            });
        }

        // Update the event listener to ensure it runs after the modal is fully shown
        document.getElementById('packageModal').addEventListener('shown.bs.modal', () => {
            setTimeout(handleInfoNotice, 100); // Small delay to ensure DOM is ready
        });
    </script>
    <script>
        // Add this to your existing JavaScript section
        document.getElementById('paymentProof').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const viewBtn = document.getElementById('viewProofBtn');
            const maxSize = 5 * 1024 * 1024; // 5MB

            // Enable/disable view button based on file selection
            viewBtn.disabled = !file;

            if (file) {
                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Please upload an image less than 5MB',
                        confirmButtonColor: '#d4af37'
                    });
                    this.value = '';
                    viewBtn.disabled = true;
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Please upload an image file',
                        confirmButtonColor: '#d4af37'
                    });
                    this.value = '';
                    viewBtn.disabled = true;
                    return;
                }
            }
        });

        // Add click handler for view button
        document.getElementById('viewProofBtn').addEventListener('click', function() {
            const fileInput = document.getElementById('paymentProof');
            const file = fileInput.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    Swal.fire({
                        title: 'Payment Proof Preview',
                        imageUrl: e.target.result,
                        imageAlt: 'Payment Proof',
                        width: 600,
                        confirmButtonColor: '#d4af37',
                        confirmButtonText: 'Close'
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script>
        // Add this function to check availability for a specific date
        function checkDateAvailability(packageName, selectedDate) {
            return fetch('check_date_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    packageName: packageName,
                    date: selectedDate
                })
            })
            .then(response => response.json())
            .then(data => {
                return {
                    isAvailable: !data.isBooked,
                    bookings: data.bookings || []
                };
            });
    }
    </script>
    <script>
        // Add this to your existing JavaScript section
    document.getElementById('eventType').addEventListener('change', function() {
        const otherContainer = document.getElementById('otherEventTypeContainer');
        const otherInput = document.getElementById('otherEventType');
        
        if (this.value === 'Other') {
            otherContainer.style.display = 'block';
            otherInput.required = true;
        } else {
            otherContainer.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    });
    </script>
    <script>
        // Add this to your existing JavaScript
        function checkDateAvailability(date) {
            const selectedDate = document.getElementById('eventDate').value;
            const packageName = document.getElementById('packageName').value;

            fetch('check_date_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    packageName: packageName,
                    date: selectedDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.isBooked) {
                    // Show warning about existing bookings
                    Swal.fire({
                        icon: 'warning',
                        title: 'Date Not Available',
                        html: `
                            <div class="text-left">
                                <p>This date is already booked:</p>
                                <div class="existing-bookings mt-3">
                                    ${data.bookings.map(booking => `
                                        <div class="booking-slot alert alert-warning">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <strong>${booking.package_name}</strong><br>
                                            Time: ${booking.start_time} - ${booking.end_time}<br>
                                            Status: ${booking.booking_status}
                                        </div>
                                    `).join('')}
                                </div>
                                <p class="mt-3">Please select a different date.</p>
                            </div>
                        `,
                        confirmButtonColor: '#d4af37'
                    });
                    // Clear the date input
                    document.getElementById('eventDate').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to check date availability',
                    confirmButtonColor: '#d4af37'
                });
            });
        }

        // Add event listener to date input
        document.getElementById('eventDate').addEventListener('change', function() {
            checkDateAvailability(this.value);
        });
    </script>
    <script>
        // Add this JavaScript function
        function viewBookedDates(packageName) {
            fetch('get_booked_dates.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    package_name: packageName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Booked Dates',
                        html: `
                            <div class="booking-status-container">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Upcoming Bookings for ${packageName}
                                </h6>
                                ${data.bookings.length > 0 ? `
                                    <div class="booking-dates">
                                        ${data.bookings.map(booking => `
                                            <div class="booking-date-item">
                                                <div class="booking-details">
                                                    <span class="date-badge booked">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        ${new Date(booking.event_date).toLocaleDateString('en-US', {
                                                            month: 'short',
                                                            day: 'numeric',
                                                            year: 'numeric',
                                                            weekday: 'short'
                                                        })}
                                                    </span>
                                                    <div class="time-slot">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            ${booking.start_time.slice(0, -3)} - ${booking.end_time.slice(0, -3)}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : `
                                    <div class="no-bookings">
                                        <small class="text-muted">No upcoming bookings for this package</small>
                                    </div>
                                `}
                            </div>
                        `,
                        width: '500px',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#d4af37',
                        showClass: {
                            popup: 'animate__animated animate__fadeIn'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch booked dates',
                        confirmButtonColor: '#d4af37'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch booked dates',
                    confirmButtonColor: '#d4af37'
                });
            });
        }
    </script>
    <script>
        // Add this function to check login before proceeding with event booking
        function checkLoginAndProceed(eventId, eventName) {
            // Check login status first
            fetch('check_login.php')
            .then(response => response.json())
            .then(data => {
                if (data.loggedIn) {
                    // User is logged in, proceed with event booking
                    proceedWithEventBooking(eventId, eventName);
                } else {
                    // User is not logged in, show login prompt
                    Swal.fire({
                        title: 'Login Required',
                        text: 'Please login to book an event',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Login Now',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Store current page URL in session storage
                            sessionStorage.setItem('redirectAfterLogin', window.location.href);
                            // Store event details to restore after login
                            sessionStorage.setItem('pendingEventId', eventId);
                            sessionStorage.setItem('pendingEventName', eventName);
                            // Redirect to login page with return URL
                            window.location.href = data.redirect;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            });
        }

        // Function to handle event booking
        function proceedWithEventBooking(eventId, eventName) {
            // Your existing event booking logic here
            document.getElementById('event_id').value = eventId;
            document.getElementById('event_name').value = eventName;
            
            // Show the booking modal
            const bookingModal = new bootstrap.Modal(document.getElementById('eventBookingModal'));
            bookingModal.show();
        }

        // Update your click handlers to use the new check login function
        document.addEventListener('DOMContentLoaded', function() {
            // Update any "Book Now" or booking buttons
            const bookingButtons = document.querySelectorAll('.book-event-btn');
            bookingButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const eventId = this.getAttribute('data-event-id');
                    const eventName = this.getAttribute('data-event-name');
                    checkLoginAndProceed(eventId, eventName);
                });
            });

            // Check for stored event details after login
            const pendingEventId = sessionStorage.getItem('pendingEventId');
            const pendingEventName = sessionStorage.getItem('pendingEventName');
            
            if (pendingEventId && pendingEventName) {
                // Clear stored data
                sessionStorage.removeItem('pendingEventId');
                sessionStorage.removeItem('pendingEventName');
                // Proceed with event booking
                proceedWithEventBooking(pendingEventId, pendingEventName);
            }
        });
    </script>
    <script>
        // Function to redirect to payment page with form data
        function redirectToPayment() {
            // Get the form element
            const form = document.getElementById('bookingForm');
            const formData = new FormData(form);
            
            // Convert form data to URL-encoded string
            const params = new URLSearchParams();
            
            // Add all form data to URL parameters
            for (let [key, value] of formData.entries()) {
                // Skip file inputs
                if (!(form[key] && form[key].type === 'file')) {
                    params.append(key, value);
                }
            }
            
            // Add any additional data that might be needed
            const packageName = document.getElementById('packageName').value;
            const eventDate = document.getElementById('eventDate').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const numberOfGuests = document.getElementById('numberOfGuests').value;
            const eventType = document.getElementById('eventType').value;
            const otherEventType = document.getElementById('otherEventType') ? document.getElementById('otherEventType').value : '';
            
            // Add these to the URL parameters
            if (packageName) params.set('packageName', packageName);
            if (eventDate) params.set('eventDate', eventDate);
            if (startTime) params.set('startTime', startTime);
            if (endTime) params.set('endTime', endTime);
            if (numberOfGuests) params.set('numberOfGuests', numberOfGuests);
            if (eventType) params.set('eventType', eventType);
            if (otherEventType) params.set('otherEventType', otherEventType);
            
            // Get the payment method details
            const paymentMethod = document.getElementById('paymentMethod') ? document.getElementById('paymentMethod').value : '';
            const paymentType = document.getElementById('paymentType') ? document.getElementById('paymentType').value : '';
            const referenceNumber = document.getElementById('referenceNumber') ? document.getElementById('referenceNumber').value : '';
            
            if (paymentMethod) params.set('paymentMethod', paymentMethod);
            if (paymentType) params.set('paymentType', paymentType);
            if (referenceNumber) params.set('referenceNumber', referenceNumber);
            
            // Get the total amount from the summary
            const totalAmountElement = document.getElementById('summary-total-amount');
            if (totalAmountElement) {
                const totalAmount = totalAmountElement.textContent.replace(/[^0-9.]/g, '');
                if (totalAmount) params.set('totalAmount', totalAmount);
            }
            
            // Build the URL with parameters
            const url = `event_payment_process.php?${params.toString()}`;
            
            // Redirect to the payment page
            window.location.href = url;
        }
        
        // Function to handle payment processing
        function proceedToPayment() {
            // Get form data
            const formData = new FormData(document.getElementById('bookingForm'));
            
            // Show loading state
            Swal.fire({
                title: 'Processing Payment',
                text: 'Please wait while we process your payment...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Payment successful
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: data.message || 'Your payment has been processed successfully!',
                        confirmButtonText: 'Continue'
                    }).then(() => {
                        // Redirect or show success message
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    });
                } else {
                    // Payment failed
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'There was an error processing your payment. Please try again.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your payment. Please try again.'
                });
            });
        }
    </script>
</body>
</html>
