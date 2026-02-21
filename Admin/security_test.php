<?php
/**
 * Security Test Script
 * This script tests the role-based access control implementation
 */

// Load security helper
require_once 'includes/security_helper.php';

echo "<h1>Security Test Results</h1>";

// Test 1: Check if user is logged in
echo "<h2>Test 1: Login Status</h2>";
echo "Is logged in: " . (isLoggedIn() ? "Yes" : "No") . "<br>";

if (isLoggedIn()) {
    echo "User ID: " . getUserId() . "<br>";
    echo "User Role: " . getUserRole() . "<br>";
    
    // Test 2: Role validation
    echo "<h2>Test 2: Role Validation</h2>";
    
    // Test customer role access
    $customerAccess = requireRole('customer', false);
    echo "Customer access: " . ($customerAccess ? "Allowed" : "Denied") . "<br>";
    
    // Test admin role access
    $adminAccess = requireRole('admin', false);
    echo "Admin access: " . ($adminAccess ? "Allowed" : "Denied") . "<br>";
    
    // Test cashier role access
    $cashierAccess = requireRole('cashier', false);
    echo "Cashier access: " . ($cashierAccess ? "Allowed" : "Denied") . "<br>";
    
    // Test staff role access
    $staffAccess = requireRole(['admin', 'cashier', 'frontdesk'], false);
    echo "Staff access: " . ($staffAccess ? "Allowed" : "Denied") . "<br>";
    
    // Test 3: CSRF Token
    echo "<h2>Test 3: CSRF Protection</h2>";
    $csrfToken = getCSRFToken();
    echo "CSRF Token generated: " . substr($csrfToken, 0, 8) . "...<br>";
    echo "CSRF Token validation: " . (validateCSRF($csrfToken) ? "Valid" : "Invalid") . "<br>";
    
} else {
    echo "<p>User is not logged in. Please login to run full security tests.</p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
}

// Test 4: Session Security
echo "<h2>Test 4: Session Security</h2>";
echo "Session status: " . session_status() . "<br>";
echo "Session ID: " . session_id() . "<br>";

echo "<h2>Security Test Complete</h2>";
echo "<p>Check the server error logs for any security violations during testing.</p>";

?>
