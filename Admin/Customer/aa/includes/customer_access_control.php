<?php

require_once 'includes/security_helper.php';

$currentFile = basename($_SERVER['PHP_SELF']);

$publicFiles = ['home.php', 'index.php', 'roomss.php', 'about.php', 'contact.php', 'cafes.php', 'events.php', 'table.php'];

$customerOnlyFiles = ['profile.php', 'cart.php', 'booking_summary.php', 'confirm_booking.php', 'wishlist.php', 'cancel_order.php'];

$ajaxFiles = ['ajax/', 'check_', 'update_', 'delete_', 'add_'];

$isAjaxFile = false;
foreach ($ajaxFiles as $prefix) {
    if (strpos($currentFile, $prefix) === 0) {
        $isAjaxFile = true;
        break;
    }
}

if ($isAjaxFile) {
    // For AJAX files, return JSON error response for unauthorized access
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit();
    }
    
    if (getUserRole() !== 'customer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }
} elseif (in_array($currentFile, $customerOnlyFiles)) {
    requireCustomer();
} elseif (in_array($currentFile, $publicFiles)) {
    if (isLoggedIn() && getUserRole() !== 'customer') {
        $userRole = getUserRole();
        switch($userRole) {
            case 'admin':
                header('Location: /Admin/index.php?dashboard');
                break;
            case 'cashier':
                header('Location: /Admin/Cashier/index.php?pos');
                break;
            case 'frontdesk':
                header('Location: /Admin/Frontdesk/index.php?dashboard');
                break;
            default:
                header('Location: /Admin/Customer/aa/login.php');
        }
        exit();
    }
}

if (isLoggedIn() && getUserRole() !== 'customer') {
    error_log("Security alert: Non-customer user (role: " . getUserRole() . ") attempted to access customer area: $currentFile");
}

?>
