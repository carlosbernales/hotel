<?php
// Start output buffering
ob_start();
session_start();

require_once 'db_con.php';

// Get all URL parameters
$urlParams = [];
foreach ($_GET as $key => $value) {
    $urlParams[$key] = is_array($value) ? array_map('htmlspecialchars', $value) : htmlspecialchars($value);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to continue with payment';
    $_SESSION['message_type'] = 'error';
    header('Location: login.php');
    exit();
}

// Get booking details from URL parameters with fallbacks
$packageName = $_GET['packageName'] ?? $_GET['package_name'] ?? '';
$eventDate = $_GET['eventDate'] ?? $_GET['event_date'] ?? '';
$startTime = $_GET['startTime'] ?? $_GET['start_time'] ?? '';
$endTime = $_GET['endTime'] ?? $_GET['end_time'] ?? '';

// Calculate duration from start and end time
$duration = 0;
if (!empty($startTime) && !empty($endTime)) {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $interval = $start->diff($end);
    $duration = $interval->h + ($interval->i / 60); // Convert minutes to decimal
}
$numberOfGuests = isset($_GET['numberOfGuests']) ? intval($_GET['numberOfGuests']) :
                 (isset($_GET['number_of_guests']) ? intval($_GET['number_of_guests']) : 0);
$eventType = $_GET['eventType'] ?? $_GET['event_type'] ?? '';
$otherEventType = $_GET['otherEventType'] ?? $_GET['other_event_type'] ?? '';
$totalAmount = isset($_GET['totalAmount']) ? floatval($_GET['totalAmount']) :
              (isset($_GET['total_amount']) ? floatval($_GET['total_amount']) :
              (isset($_GET['packagePrice']) ? floatval($_GET['packagePrice']) : 0));
$paymentMethod = $_GET['paymentMethod'] ?? $_GET['payment_method'] ?? 'gcash';
$paymentType = $_GET['paymentType'] ?? $_GET['payment_type'] ?? 'full';
$referenceNumber = '';

// Calculate extra guests and charges
$extraGuests = 0;
$extraGuestCharge = 0;
$overtimeHours = 0;
$overtimeCharge = 0;

// Calculate extra guests (assuming 50 is the base number of guests)
if ($numberOfGuests > 50) {
    $extraGuests = $numberOfGuests - 50;
    $extraGuestCharge = $extraGuests * 1000; // ₱1,000 per extra guest
}

// Calculate overtime if duration is more than 4 hours
if ($duration > 4) {
    $overtimeHours = $duration - 4;
    $overtimeCharge = $overtimeHours * 1000; // Assuming ₱1,000 per overtime hour
}

// Store booking data in session
$_SESSION['booking_summary'] = [
    'package_name' => "$packageName",
    'event_date' => "$eventDate",
    'start_time' => "$startTime",
    'end_time' => "$endTime",
    'duration' => $duration,
    'number_of_guests' => $numberOfGuests,
    'extra_guests' => $extraGuests,
    'extra_guest_charge' => $extraGuestCharge,
    'overtime_hours' => $overtimeHours,
    'overtime_charge' => $overtimeCharge,
    'event_type' => "$eventType",
    'other_event_type' => "$otherEventType",
    'total_amount' => $totalAmount,
    'payment_method' => "$paymentMethod",
    'payment_type' => "$paymentType",
    'reference_number' => "$referenceNumber",
    'booking_timestamp' => date('Y-m-d H:i:s')
];

// Debug: Log received parameters
error_log('Received parameters: ' . print_r([
    'packageName' => $packageName,
    'eventDate' => $eventDate,
    'startTime' => $startTime,
    'endTime' => $endTime,
    'numberOfGuests' => $numberOfGuests,
    'eventType' => $eventType,
    'otherEventType' => $otherEventType,
    'totalAmount' => $totalAmount
], true));

// Function to fetch menu items by their IDs
function getMenuItemsByIds($pdo, $itemIds) {
    if (empty($itemIds)) {
        return [];
    }
    
    // Convert comma-separated string to array if needed
    $ids = is_array($itemIds) ? $itemIds : explode(',', $itemIds);
    
    // Remove any whitespace and ensure we have valid IDs
    $ids = array_map('trim', $ids);
    $ids = array_filter($ids, 'is_numeric');
    
    if (empty($ids)) {
        return [];
    }
    
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "SELECT id, name, price, category_id 
            FROM menu_items 
            WHERE id IN ($placeholders)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error fetching menu items: ' . $e->getMessage());
        return [];
    }
}

// Function to group menu items by category
function groupMenuItemsByCategory($menuItems, $pdo) {
    if (empty($menuItems)) {
        return [];
    }
    
    $grouped = [];
    
    // First, get all category names
    $categoryIds = array_unique(array_column($menuItems, 'category_id'));
    $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
    $sql = "SELECT id, name FROM menu_categories WHERE id IN ($placeholders)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($categoryIds);
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[$row['id']] = $row['name'];
        }
        
        // Group items by category
        foreach ($menuItems as $item) {
            $categoryId = $item['category_id'];
            $categoryName = $categories[$categoryId] ?? 'Other';
            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = [];
            }
            $grouped[$categoryName][] = $item;
        }
        
        return $grouped;
    } catch (PDOException $e) {
        error_log('Error grouping menu items by category: ' . $e->getMessage());
        return [];
    }
}

// Fetch event package details from database
$packageDetails = [];
if (!empty($packageName)) {
    try {
        if (!isset($pdo)) {
            throw new Exception('Database connection is not available');
        }
        
        // First try exact match on package_name
        $stmt = $pdo->prepare("SELECT * FROM event_packages WHERE name = ?");
        $stmt->execute([$packageName]);
        $packageDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If not found, try case-insensitive search
        if (empty($packageDetails)) {
            throw new Exception('Package not found in the database');
        }
        
    } catch (Exception $e) {
        $errorMsg = 'An error occurred while processing your request. Please try again later.';
        echo '<div class="alert alert-danger">' . $errorMsg . '</div>';
        error_log('Payment Process Error: ' . $e->getMessage());
    }
}


echo '</div>'; // Close debug-info div

// If no package found, use basic info from URL parameters
if (empty($packageDetails) && !empty($packageName)) {
    $packageDetails = [
        'package_name' => $packageName,
        'description' => 'Custom package',
        'price_per_pax' => $totalAmount / max(1, $numberOfGuests),
        'inclusions' => 'Please contact us for details',
        'min_pax' => 1,
        'max_pax' => 100,
        'duration_hours' => 4 // Default duration
    ];
}

// Calculate duration
$duration = 0;
if ($startTime && $endTime) {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $interval = $start->diff($end);
    $duration = $interval->h + ($interval->i / 60);
}
?>
<?php
// Check for payment success
$paymentSuccess = isset($_GET['payment']) && $_GET['payment'] === 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - Casa Estela Events</title>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- PayMongo JS -->
    <script src="https://js.paymongo.com/v1/paymongo.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
    :root {
        --primary: #6c5ce7;
        --primary-light: #a29bfe;
        --secondary: #00b894;
        --dark: #2d3436;
        --light: #f8f9fa;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f6fa;
        color: var(--dark);
        line-height: 1.6;
    }
    
    .payment-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 15px;
    }
    
    .payment-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .payment-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        padding: 1.5rem 2rem;
        text-align: center;
    }
    
    .payment-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 1.75rem;
    }
    
    .payment-body {
        padding: 2rem;
    }
    
    .section-title {
        color: var(--dark);
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f1f1;
        position: relative;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 60px;
        height: 2px;
        background: var(--primary);
    }
    
    .booking-summary {
        background: #f8f9fa;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .summary-item:last-child {
        border-bottom: none;
    }
    
    .summary-label {
        font-weight: 500;
        color: #666;
    }
    
    .summary-value {
        font-weight: 500;
        color: var(--dark);
    }
    
    .payment-method {
        margin-bottom: 2rem;
    }
    
    .payment-option {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }
    
    .payment-option:hover {
        border-color: var(--primary-light);
        transform: translateY(-2px);
    }
    
    .payment-option.active {
        border-color: var(--primary);
        background-color: rgba(108, 92, 231, 0.05);
    }
    
    .payment-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        background: #f5f6fa;
        font-size: 1.5rem;
    }
    
    .payment-option.active .payment-icon {
        background: var(--primary);
        color: white;
    }
    
    .payment-info h5 {
        margin: 0 0 0.25rem 0;
        font-weight: 600;
    }
    
    .payment-info p {
        margin: 0;
        color: #666;
        font-size: 0.9rem;
    }
    
    .payment-details {
        background: #f9f9f9;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px dashed #ddd;
    }
    
    .payment-instructions {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary);
    }
    
    .btn-pay {
        background: var(--primary);
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-pay:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
    }
    
    .btn-pay:active {
        transform: translateY(0);
    }
    
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #555;
    }
    
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .form-check-label {
        color: #555;
    }
    
    .terms-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    
    .terms-link:hover {
        text-decoration: underline;
    }
    
    .highlight {
        background-color: rgba(0, 184, 148, 0.1);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
        color: var(--secondary);
    }
    
    .success-icon {
        font-size: 4rem;
        color: var(--secondary);
        margin-bottom: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .payment-container {
            padding: 0 1rem;
        }
        
        .payment-body {
            padding: 1.5rem 1rem;
        }
        
        .payment-header h2 {
            font-size: 1.5rem;
        }
    }
:root {
    --primary: #d4af37;
    --primary-dark: #b5942a;
    --secondary: #2c3e50;
    --light: #f8f9fa;
    --dark: #343a40;
    --success: #28a745;
    --info: #17a2b8;
    --warning: #ffc107;
    --danger: #dc3545;
    --gray: #6c757d;
    --light-gray: #e9ecef;
    --border-radius: 10px;
    --box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

body {
    background-color: #f5f7fa;
    color: #333;
    font-family: 'Poppins', sans-serif;
}

.payment-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 15px;
}

.payment-card {
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
    border: none;
    transition: var(--transition);
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.payment-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.payment-header h2 {
    margin: 0;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.payment-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.payment-body {
    padding: 2rem;
}

.section-title {
    color: var(--secondary);
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--light-gray);
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 80px;
    height: 2px;
    background: var(--primary);
}

.booking-summary {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.summary-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.summary-card-header {
    background: #f8f9fa;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
}

.summary-card-header i {
    margin-right: 8px;
    color: var(--primary);
}

.summary-card-body {
    padding: 1.25rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f3f5;
}

.summary-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.summary-label {
    font-weight: 500;
    color: #6c757d;
    flex: 1;
}

.summary-value {
    font-weight: 500;
    color: #212529;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

.summary-divider {
    height: 1px;
    background: #e9ecef;
    margin: 1rem 0;
}

.total-amount {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #e9ecef !important;
}

.total-amount .summary-label {
    font-size: 1.1rem;
    color: #212529;
}

.total-amount .summary-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
}

.inclusions-list ul {
    list-style-type: none;
    padding-left: 0;
    margin: 0.5rem 0 0 0;
}

.inclusions-list li {
    position: relative;
    padding-left: 1.25rem;
    margin-bottom: 0.25rem;
    color: #495057;
}

.inclusions-list li:before {
    content: '•';
    color: var(--primary);
    font-weight: bold;
    position: absolute;
    left: 0;
}

.badge {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.35em 0.65em;
    border-radius: 50rem;
    background: var(--primary);
}

/* Debug Info */
.debug-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 2rem 0;
    border-left: 4px solid #dc3545;
}

.debug-info h5 {
    margin-top: 0;
    color: #dc3545;
    margin-bottom: 1rem;
    font-weight: 600;
}

.debug-info pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    max-height: 300px;
    overflow: auto;
    font-size: 0.85rem;
    margin: 0.5rem 0 1rem;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Alerts */
.alert {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    line-height: 1.5;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.alert-danger {
    color: #842029;
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

.alert-warning {
    color: #664d03;
    background-color: #fff3cd;
    border-color: #ffecb5;
}

.alert-info {
    color: #055160;
    background-color: #cff4fc;
    border-color: #b6effb;
}

.alert-secondary {
    color: #41464b;
    background-color: #e2e3e5;
    border-color: #d3d6d8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .summary-item {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .summary-value {
        text-align: left;
        margin-left: 0;
    }
    
    .summary-card-body {
        padding: 1rem;
    }
    
    .debug-info {
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .debug-info pre {
        font-size: 0.8rem;
        padding: 0.75rem;
    }
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    cursor: pointer;
    transition: var(--transition);
}

.payment-option:hover {
    border-color: var(--primary);
}

.payment-option.active {
    border-color: var(--primary);
    background-color: rgba(212, 175, 55, 0.05);
}

.payment-option input[type="radio"] {
    margin-right: 1rem;
    transform: scale(1.2);
}

.payment-option .payment-icon {
    width: 40px;
    height: 40px;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
}

.payment-option .payment-icon img {
    max-width: 100%;
    max-height: 100%;
}

.payment-option .payment-info {
    flex: 1;
}

.payment-option .payment-info h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.payment-option .payment-info p {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
}

.payment-option .fa-chevron-right {
    color: #adb5bd;
    font-size: 0.9rem;
    margin-left: 1rem;
}

.payment-option.active .fa-chevron-right {
    color: var(--primary);
}

/* Payment Details */
.payment-details {
    background-color: #f9faff;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin: 1.5rem 0;
    border: 1px dashed #d0d5ff;
}

.payment-instructions {
    background-color: white;
    border-left: 4px solid var(--primary);
    padding: 1rem 1.25rem;
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
    margin-bottom: 1.5rem;
}

.payment-instructions h5 {
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
}

.payment-instructions h5 i {
    margin-right: 0.5rem;
}

.payment-instructions ol {
    padding-left: 1.25rem;
    margin-bottom: 0;
}

.payment-instructions li {
    margin-bottom: 0.5rem;
}

.payment-instructions li:last-child {
    margin-bottom: 0;
}

/* Toast Notifications */
.toast {
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}

.toast.hide {
    display: none;
    opacity: 0;
}

.toast.show {
    display: block;
    opacity: 1;
}

.toast-body {
    display: flex;
    align-items: center;
}

.toast i {
    font-size: 1.25rem;
}

.highlight {
    background-color: #fff3cd;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    color: #856404;
}

/* Form Elements */
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #495057;
}

.form-control, .form-select {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: var(--transition);
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(74, 107, 255, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

/* Buttons */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    font-size: 1rem;
    margin-right: 0.5rem;
}

.btn-outline-secondary {
    border: 1px solid #dee2e6;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: var(--dark);
}

.btn-pay {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-pay:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 107, 255, 0.3);
    box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
}

.btn-pay i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .payment-container {
        margin: 1rem auto;
    }
    
    .payment-body {
        padding: 1.5rem 1rem;
    }
    
    .btn-pay {
        width: 100%;
        margin-top: 1rem;
        padding: 1rem;
        font-size: 1.1rem;
    }

    .success-icon {
        font-size: 4rem;
        color: #28a745;
    }

    .success-modal {
        font-size: 1.1rem;
    }
    }
</style>


<div class="payment-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="payment-card">
                <div class="payment-header">
                    <h2><i class="fas fa-credit-card me-2"></i> Complete Your Payment</h2>
                </div>
                <div class="payment-body">
                    <!-- Booking Summary -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="section-title mb-0">Booking Summary</h4>
                            <span class="badge bg-primary">#<?php echo uniqid('BOOK-'); ?></span>
                        </div>
                        
  
                            
                            <!-- Event Details -->
                            <div class="summary-card mb-3">
                                <div class="summary-card-header">
                                    <i class="fas fa-box me-2"></i>Package Details
                                </div>
                                <div class="summary-card-body">
                                    <div class="summary-item">
                                        <span class="summary-label">Package Name:</span>
                                        <span class="summary-value fw-bold"><?php echo htmlspecialchars($packageDetails['package_name'] ?? $packageName); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="summary-card mb-3">
                                <div class="summary-card-header">
                                    <i class="far fa-calendar-alt me-2"></i>Event Details
                                </div>
                                <div class="summary-card-body">
                                    <div class="summary-item">
                                        <span class="summary-label">Event Date:</span>
                                        <span class="summary-value">
                                            <?php echo $eventDate ? date('F j, Y (l)', strtotime($eventDate)) : 'To be scheduled'; ?>
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Event Time:</span>
                                        <span class="summary-value">
                                            <?php 
                                            $formattedTime = ($startTime && $endTime) ? 
                                                date('g:i A', strtotime($startTime)) . ' - ' . date('g:i A', strtotime($endTime)) : 
                                                'To be determined';
                                            echo htmlspecialchars($formattedTime);
                                            ?>
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Duration:</span>
                                        <span class="summary-value">
                                            <?php 
                                            $duration = $packageDetails['duration_hours'] ?? $duration;
                                            $includedHours = $packageDetails['included_hours'] ?? 4;
                                            echo $duration ? $duration . ' hours' : 'To be determined';
                                            
                                            // Display overtime information if applicable
                                            if ($duration > $includedHours) {
                                                $overtimeHours = $duration - $includedHours;
                                                echo ' <span class="text-danger">(' . $overtimeHours . ' hours overtime)</span>';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($eventType) || !empty($otherEventType)): ?>
                                    <div class="summary-item">
                                        <span class="summary-label">Event Type:</span>
                                        <span class="summary-value">
                                            <?php 
                                            $displayEventType = !empty($otherEventType) ? $otherEventType : $eventType;
                                            echo htmlspecialchars($displayEventType);
                                            ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="summary-item">
                                        <span class="summary-label">Number of Guests:</span>
                                        <span class="summary-value">
                                            <?php 
                                            echo $numberOfGuests ? number_format($numberOfGuests) . ' Person(s)' : 'To be confirmed';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Summary -->
                            <div class="summary-card">
                                <div class="summary-card-header">
                                    <i class="far fa-credit-card me-2"></i>Payment Summary
                                </div>
                                <div class="summary-card-body">
                                    <?php if (isset($packageDetails['price_per_pax']) && $numberOfGuests > 0): ?>
                                    <div class="summary-item">
                                        <span class="summary-label">Base Price (<?php echo number_format($numberOfGuests); ?> pax):</span>
                                        <span class="summary-value">₱<?php echo number_format($packageDetails['price_per_pax'] * $numberOfGuests, 2); ?></span>
                                    </div>
                                    <?php 
                                    // Calculate additional charges if any
                                    $additionalCharges = 0;
                                    $overtimeCharge = 0;
                                    
                                    // Check for overtime charges if duration exceeds included hours
                                    $includedHours = $packageDetails['included_hours'] ?? 4; // Default to 4 hours if not set
                                    $overtimeRate = $packageDetails['overtime_rate'] ?? 0;
                                    
                                    if (!empty($duration) && $duration > $includedHours) {
                                        $overtimeHours = $duration - $includedHours;
                                        $overtimeCharge = $overtimeHours * $overtimeRate;
                                        $additionalCharges += $overtimeCharge;
                                        ?>
                                        <div class="summary-item">
                                            <span class="summary-label">Overtime (<?php echo $overtimeHours; ?> hrs @ ₱<?php echo number_format($overtimeRate, 2); ?>/hr):</span>
                                            <span class="summary-value text-danger">+ ₱<?php echo number_format($overtimeCharge, 2); ?></span>
                                        </div>
                                        <?php
                                    }
                                    
                                    // Add any other additional charges here
                                    ?>
                                    
                                    <?php if ($additionalCharges > 0): ?>
                                    <div class="summary-divider"></div>
                                    <div class="summary-item">
                                        <span class="summary-label">Subtotal:</span>
                                        <span class="summary-value">₱<?php echo number_format(($packageDetails['price_per_pax'] * $numberOfGuests) + $additionalCharges, 2); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="summary-divider"></div>
                                    <?php endif; ?>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Payment Method:</span>
                                        <span class="summary-value text-capitalize"><?php echo str_replace('_', ' ', $paymentMethod); ?></span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Payment Type:</span>
                                        <span class="summary-value text-capitalize payment-type-display"><?php echo $paymentType === 'downpayment' ? 'Down Payment' : 'Full Payment'; ?></span>
                                    </div>
                                    <div class="summary-divider"></div>
                                    <?php 
                                    // Calculate payment amounts
                                    $downPaymentPercentage = $packageDetails['down_payment_percentage'] ?? 50; // Default to 50% if not set
                                    $downPaymentAmount = ($totalAmount * $downPaymentPercentage) / 100;
                                    $amountToPay = ($paymentType === 'downpayment') ? $downPaymentAmount : $totalAmount;
                                    $balanceAmount = $totalAmount - $amountToPay;
                                    ?>
                                    
                                    <div class="summary-item total-amount">
                                        <span class="summary-label">Total Amount:</span>
                                        <span class="summary-value fw-bold">₱<?php echo number_format($totalAmount, 2); ?></span>
                                    </div>
                                    
                                    <?php if ($paymentType === 'downpayment'): ?>
                                    <div class="down-payment-section">
                                        <div class="summary-item">
                                            <span class="summary-label">Down Payment (<?php echo $downPaymentPercentage; ?>%):</span>
                                            <span class="summary-value fw-bold text-primary down-payment-amount">₱<?php echo number_format($downPaymentAmount, 2); ?></span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label">Balance to be Paid:</span>
                                            <span class="summary-value balance-amount">₱<?php echo number_format($balanceAmount, 2); ?></span>
                                        </div>
                                        <div class="summary-divider"></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!$paymentSuccess): ?>
                                    <div class="summary-item total-amount">
                                        <span class="summary-label">Amount to Pay Now:</span>
                                        <span class="summary-value fw-bold amount-to-pay">₱<?php echo number_format($amountToPay, 2); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Payment Action Buttons -->
                                    <div class="d-grid gap-3 mt-4">
                                        <?php if ($paymentSuccess): ?>
                                            <!-- Payment Status -->
                                            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                                <i class="fas fa-check-circle me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <h5 class="alert-heading mb-1">Payment Status: <span class="badge bg-success">Paid</span></h5>
                                                    <p class="mb-0">Your payment has been processed successfully.</p>
                                                </div>
                                            </div>
                                            
                                            <button type="button" class="btn btn-success btn-lg w-100" id="finishBooking">
                                                <i class="fas fa-check-circle me-2"></i>Finish Booking
                                            </button>
                                            
                                            <a href="events.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-calendar-alt me-2"></i>View My Events
                                            </a>
                                        <?php else: ?>
                                            <form id="paymongoForm" action="event_paymongo_process.php" method="POST">
                                                <input type="hidden" name="packageName" value="<?php echo htmlspecialchars($packageName); ?>">
                                                <input type="hidden" name="packagePrice" value="<?php echo $totalAmount; ?>">
                                                <input type="hidden" name="eventDate" value="<?php echo htmlspecialchars($eventDate); ?>">
                                                <input type="hidden" name="startTime" value="<?php echo htmlspecialchars($startTime); ?>">
                                                <input type="hidden" name="endTime" value="<?php echo htmlspecialchars($endTime); ?>">
                                                <input type="hidden" name="paymentType" value="<?php echo $paymentType; ?>">
                                                <input type="hidden" name="amountToPay" value="<?php echo ($paymentType === 'downpayment') ? ($totalAmount * ($downPaymentPercentage / 100)) : $totalAmount; ?>">
                                                
                                                <button type="submit" class="btn btn-primary btn-lg w-100" id="payWithPaymongo">
                                                    <i class="fas fa-credit-card me-2"></i>Pay with PayMongo
                                                </button>
                                            </form>
                                            
                                            <a href="events.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Back to Events
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include PayMongo Checkout -->
<script src="https://js.paymongo.com/v1/paymongo.js"></script>
<script>


// Handle PayMongo form submission
$('#paymongoForm').on('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const payButton = $('#payWithPaymongo');
    const originalText = payButton.html();
    payButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');
    
    // Submit the form
    this.submit();
    
    // Re-enable button after 5 seconds in case of error
    setTimeout(() => {
        payButton.prop('disabled', false).html(originalText);
    }, 5000);
});

// Initialize to show the correct payment method details on page load
// Function to update payment summary based on payment type
function updatePaymentSummary() {
    const paymentType = document.getElementById('paymentType').value;
    const totalAmount = parseFloat(<?php echo $totalAmount; ?>);
    const downPaymentPercentage = parseFloat(<?php echo $downPaymentPercentage; ?>);
    
    // Calculate amounts
    const downPaymentAmount = (totalAmount * downPaymentPercentage) / 100;
    const amountToPay = (paymentType === 'downpayment') ? downPaymentAmount : totalAmount;
    const balanceAmount = totalAmount - amountToPay;
    
    // Update payment type display
    document.querySelector('.payment-type-display').textContent = 
        (paymentType === 'downpayment' ? 'Down Payment' : 'Full Payment');
    
    // Update amount to pay
    document.querySelector('.amount-to-pay').textContent = '₱' + amountToPay.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Toggle down payment details
    const downPaymentSection = document.querySelector('.down-payment-section');
    if (paymentType === 'downpayment') {
        downPaymentSection.style.display = 'block';
        document.querySelector('.down-payment-amount').textContent = '₱' + downPaymentAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.querySelector('.balance-amount').textContent = '₱' + balanceAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    } else {
        downPaymentSection.style.display = 'none';
    }
}
</script>
<!-- jQuery, Popper.js, Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($paymentSuccess): ?>
<script>
// Function to handle finish booking
function finishBooking() {
    // Show loading state
    const finishBtn = $('#finishBooking');
    const originalText = finishBtn.html();
    finishBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');
    
    // Calculate duration in hours
    const startTime = new Date('<?php echo $eventDate; ?> <?php echo $startTime; ?>');
    const endTime = new Date('<?php echo $eventDate; ?> <?php echo $endTime; ?>');
    const durationHours = (endTime - startTime) / (1000 * 60 * 60);
    
    // Calculate overtime
    const includedHours = <?php echo $packageDetails['included_hours'] ?? 0; ?>;
    const overtimeRate = <?php echo $packageDetails['overtime_rate'] ?? 0; ?>;
    const overtimeHours = Math.max(0, durationHours - includedHours);
    const overtimeCharge = overtimeHours * overtimeRate;
    
    // Get the booking details
    const bookingData = {
        packageName: '<?php echo addslashes($packageName); ?>',
        packagePrice: parseFloat(<?php echo $totalAmount; ?>).toFixed(2),
        basePrice: parseFloat(<?php echo $packageDetails['base_price'] ?? $totalAmount; ?>).toFixed(2),
        eventDate: '<?php echo $eventDate; ?>',
        startTime: '<?php echo $startTime; ?>',
        endTime: '<?php echo $endTime; ?>',
        numberOfGuests: parseInt(<?php echo $numberOfGuests ?? 1; ?>),
        eventType: '<?php echo !empty($eventType) ? addslashes($eventType) : "General"; ?>',
        otherEventType: '<?php echo !empty($otherEventType) ? addslashes($otherEventType) : ""; ?>',
        paymentMethod: 'Gcash',
        paymentType: '<?php echo $paymentType; ?>',
        overtimeHours: parseFloat(overtimeHours).toFixed(2),
        overtimeCharge: parseFloat(overtimeCharge).toFixed(2),
        extraGuests: parseInt(<?php echo $extraGuests ?? 0; ?>),
        extraGuestCharge: parseFloat(<?php echo $extraGuestCharge ?? 0; ?>).toFixed(2)
    };
    
    // Log the complete booking data for debugging
    console.log('Complete booking data:', bookingData);
    console.log('Event Type:', bookingData.eventType);
    console.log('Number of Guests:', bookingData.numberOfGuests);
    console.log('Other Event Type:', bookingData.otherEventType);
    
    // Build URL with parameters
    const params = new URLSearchParams();
    
    // Explicitly append all required parameters to ensure none are missed
    params.append('packageName', bookingData.packageName);
    params.append('packagePrice', bookingData.packagePrice);
    params.append('eventDate', bookingData.eventDate);
    params.append('startTime', bookingData.startTime);
    params.append('endTime', bookingData.endTime);
    params.append('numberOfGuests', bookingData.numberOfGuests);
    params.append('eventType', bookingData.eventType);
    
    // Add optional parameters if they exist
    if (bookingData.otherEventType) {
        params.append('otherEventType', bookingData.otherEventType);
    }
    
    // Add payment and calculation parameters
    params.append('paymentMethod', bookingData.paymentMethod);
    params.append('paymentType', bookingData.paymentType);
    // Ensure all required parameters are included
    const requestData = {
        packageName: bookingData.packageName,
        packagePrice: bookingData.packagePrice,
        basePrice: bookingData.basePrice,
        eventDate: bookingData.eventDate,
        startTime: bookingData.startTime,
        endTime: bookingData.endTime,
        numberOfGuests: bookingData.numberOfGuests,
        eventType: bookingData.eventType,
        otherEventType: bookingData.otherEventType || '',
        paymentMethod: bookingData.paymentMethod,
        paymentType: bookingData.paymentType,
        overtimeHours: bookingData.overtimeHours,
        overtimeCharge: bookingData.overtimeCharge,
        extraGuests: bookingData.extraGuests,
        extraGuestCharge: bookingData.extraGuestCharge
    };
    
    console.log('Sending booking data:', requestData);
    
    // Send AJAX request to save booking
    $.ajax({
        url: 'event_finish_booking.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        success: function(response) {
            if (response.success) {
                // Show success message
                // Format the event type display
                const eventTypeDisplay = response.data.eventType || 'Not specified';
                const numberOfGuests = response.data.numberOfGuests || 0;
                const extraGuests = response.data.extraGuests || 0;
                const totalGuests = parseInt(numberOfGuests) + parseInt(extraGuests);
                
                Swal.fire({
                    title: 'Booking Confirmed!',
                    html: `
                        <div class="text-left">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Your booking has been confirmed successfully!
                            </div>
                            
                            <div class="booking-details">
                                <h5 class="mb-3">Booking Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Reference #:</strong><br>${response.booking_reference || 'N/A'}</p>
                                        <p><strong>Package:</strong><br>${response.data.packageName || 'N/A'}</p>
                                        <p><strong>Event Type:</strong><br>${eventTypeDisplay}</p>
                                        <p><strong>Total Guests:</strong><br>${totalGuests} (${numberOfGuests} base ${numberOfGuests > 1 ? 'guests' : 'guest'}${
                                            extraGuests > 0 ? ` + ${extraGuests} additional ${extraGuests > 1 ? 'guests' : 'guest'}` : ''
                                        })</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Date:</strong><br>${response.data.reservationDate || 'N/A'}</p>
                                        <p><strong>Time:</strong><br>${response.data.time || 'N/A'}</p>
                                        ${response.data.overtimeHours > 0 ? 
                                            `<p><strong>Overtime:</strong><br>${response.data.overtimeHours} hours (₱${parseFloat(response.data.overtimeCharge || 0).toLocaleString('en-US', {minimumFractionDigits: 2})})</p>` : ''
                                        }
                                    </div>
                                </div>
                                
                                <div class="payment-summary mt-4 pt-3 border-top">
                                    <h5 class="mb-3">Payment Summary</h5>
                                    <div class="row">
                                        <div class="col-md-8">Base Package (${numberOfGuests} ${numberOfGuests > 1 ? 'guests' : 'guest'})</div>
                                        <div class="col-md-4 text-end">₱${parseFloat(response.data.basePrice || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                                        
                                        ${extraGuests > 0 ? `
                                            <div class="col-md-8">Additional Guests (${extraGuests} ${extraGuests > 1 ? 'guests' : 'guest'})</div>
                                            <div class="col-md-4 text-end">₱${parseFloat(response.data.extraGuestCharge || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                                        ` : ''}
                                        
                                        ${response.data.overtimeHours > 0 ? `
                                            <div class="col-md-8">Overtime (${response.data.overtimeHours} hours)</div>
                                            <div class="col-md-4 text-end">₱${parseFloat(response.data.overtimeCharge || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                                        ` : ''}
                                        
                                        <div class="col-md-8 mt-2"><strong>Total Amount:</strong></div>
                                        <div class="col-md-4 text-end mt-2"><strong>₱${parseFloat(response.data.totalAmount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></div>
                                        
                                        <div class="col-md-8">Paid Amount (${response.data.paymentType === 'full' ? 'Full Payment' : 'Down Payment'}):</div>
                                        <div class="col-md-4 text-end">₱${parseFloat(response.data.paidAmount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                                        
                                        ${response.data.remainingBalance > 0 ? `
                                            <div class="col-md-8">Remaining Balance:</div>
                                            <div class="col-md-4 text-end">₱${parseFloat(response.data.remainingBalance || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'View My Bookings',
                    allowOutsideClick: false
                }).then((result) => {
                    // Redirect to events page after user clicks the button
                    window.location.href = 'events.php?booking_success=1';
                });
            } else {
                // Show error message
                Swal.fire({
                    title: 'Error',
                    text: response.message || 'Failed to complete booking. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                finishBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText,
                statusCode: xhr.status,
                responseHeaders: xhr.getAllResponseHeaders()
            });
            
            let errorMessage = 'An error occurred while processing your request. Please try again.';
            let detailedError = '';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response && response.error) {
                    errorMessage = response.error.message || errorMessage;
                    detailedError = response.error.details || '';
                }
            } catch (e) {
                // If we can't parse the response, show the raw response
                console.error('Error parsing error response:', e);
                detailedError = xhr.responseText || 'No response from server';
            }
            
            // Show more detailed error in development
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                errorMessage += '\n\n' + 
                    'Status: ' + xhr.status + ' ' + status + '\n' +
                    'Response: ' + (detailedError || 'No details available');
            }
            
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK',
                width: '600px'
            });
            
            finishBtn.prop('disabled', false).html(originalText);
        }
    });
}

// Show success message
$(document).ready(function() {
    Swal.fire({
        title: 'Payment Successful!',
        text: 'Your payment has been processed successfully.',
        icon: 'success',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Continue',
        allowOutsideClick: false
    });
    
    // Hide amount to pay section
    $('.amount-to-pay').closest('.summary-item').hide();
    
    // Attach click handler to Finish Booking button
    $('#finishBooking').on('click', finishBooking);
});
</script>
<?php endif; ?>
</body>
</html>