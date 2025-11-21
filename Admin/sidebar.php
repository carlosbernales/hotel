<?php
// Start output buffering
ob_start();

require_once 'includes/init.php';

// Get current page information
$currentPage = basename($_SERVER['PHP_SELF']);
$isBookingPage = in_array($currentPage, ['reservation.php', 'table_packages.php', 'event_booking.php']);
$isSettingsPage = in_array($currentPage, ['booking_settings.php', 'room_management.php', 'table_management.php']);

// Rest of the PHP logic here...

// End output buffering and display the HTML
ob_end_flush();
?>

<div class="sidebar">
    <div class="logo">
        <img src="img/Casa.jfif" alt="Logo" class="logo-img">
        <div class="admin-text">Admin</div>
    </div>

    <div class="nav-wrapper">
        <ul class="nav-links">
            <li>
                <a href="index.php?dashboard">
                    <em class="fa fa-dashboard"></em>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="index.php?UserInfo">
                    <em class="fa fa-user"></em>
                    <span>Customer Info</span>
                </a>
            </li>
            
            <!-- Bookings Dropdown -->
            <li class="parent">
                <a href="#" class="has-dropdown">
                    <em class="fa fa-calendar"></em>
                    <span>Bookings</span>
                    <em class="fa fa-chevron-down"></em>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="index.php?roomcards">
                            <em class="fa fa-bed"></em>
                            <span>Room Booking</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?table_packages">
                            <em class="fa fa-utensils"></em>
                            <span>Table Booking</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?event_booking">
                            <em class="fa fa-calendar-check"></em>
                            <span>Event Booking</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="index.php?booking_status">
                    <em class="fa fa-info-circle"></em>
                    <span>Booking Status</span>
                </a>
            </li>
            <li>
                <a href="advance_orders_list.php">
                    <em class="fa fa-cutlery"></em>
                    <span>Table Orders</span>
                </a>
            </li>
            <li>
                <a href="checked_in.php">
                    <em class="fa fa-sign-in"></em>
                    <span>Checked In</span>
                </a>
            </li>
            <li>
                <a href="checked_out.php">
                    <em class="fa fa-sign-out"></em>
                    <span>Checked Out</span>
                </a>
            </li>
            <li>
                <a href="index.php?staff_mang">
                    <em class="fa fa-users"></em>
                    <span>Staff Section</span>
                </a>
            </li>
            <li>
                <a href="index.php?complain">
                    <em class="fa fa-comments"></em>
                    <span>Feedback</span>
                </a>
            </li>
            <li>
                <a href="index.php?sales_report">
                    <em class="fa fa-line-chart"></em>
                    <span>Sales Report</span>
                </a>
            </li>
           

            <!-- Settings Dropdown -->
            <li class="parent">
                <a href="#" class="has-dropdown">
                    <em class="fa fa-cog"></em>
                    <span>Settings</span>
                    <em class="fa fa-chevron-down"></em>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="index.php?booking_settings">
                            <em class="fa fa-sliders"></em>
                            <span>Display Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?room_management">
                            <em class="fa fa-bed"></em>
                            <span>Room Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="table_management.php">
                            <em class="fa fa-utensils"></em>
                            <span>Table Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="cafe_management.php">
                            <em class="fa fa-coffee"></em>
                            <span>Cafe Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="event_management.php">
                            <em class="fa fa-calendar"></em>
                            <span>Event Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="facilities_management.php">
                            <em class="fa fa-building"></em>
                            <span>Facilities Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="manage_policies.php">
                            <em class="fa fa-info-circle"></em>
                            <span>Information and Policies</span>
                        </a>
                    </li>
                    <li>
                        <a href="discount_settings.php">
                            <em class="fa fa-percent"></em>
                            <span>Discount Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="verification_settings.php">
                            <em class="fa fa-check-circle"></em>
                            <span>Verification Type Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="contact_settings.php">
                            <em class="fa fa-address-book"></em>
                            <span>Contact Information</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?Paymentss">
                            <em class="fa fa-money"></em>
                            <span>Payment Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="run_table_orders_fix.php">
                            <em class="fa fa-wrench"></em>
                            <span>Table Orders Fix</span>
                        </a>
                    </li>
                    
                   
                </ul>
            </li>
           
            <!-- Payments Dropdown -->
            
            
        </ul>
    </div>
</div>

<div class="content-wrapper">
    <!-- Your content here -->
</div>

<style>
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    width: 240px;
    background: #333;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.main-wrapper {
    display: flex;
    min-height: 100vh;
    padding-top: 60px;
}

.content-wrapper {
    flex: 1;
    margin-left: 240px;
    position: relative;
    z-index: 800;
    min-width: 0;
}

.logo {
    padding: 20px 0;
    text-align: center;
    flex-shrink: 0;
}

.logo-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.admin-text {
    color: #DAA520;
    font-size: 16px;
    font-weight: bold;
}

.nav-wrapper {
    flex-grow: 1;
    overflow-y: auto;
}

.nav-links {
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-links li a {
    display: flex;
    align-items: center;
    padding: 12px 25px;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.nav-links li a:hover,
.nav-links li a.active {
    background: #DAA520;
    color: #fff;
}

.nav-links li a em {
    min-width: 25px;
    text-align: center;
    color: #DAA520;
    font-size: 16px;
    margin-right: 10px;
}

.nav-links li a:hover em,
.nav-links li a.active em {
    color: #fff;
}

.nav-links li a span {
    font-size: 14px;
}

.submenu {
    display: none;
    background: #2b2b2b;
    transition: all 0.3s ease;
}

.parent.active > a {
    background: #2b2b2b;
    color: #DAA520;
}

.parent.active > a em {
    color: #DAA520;
}

.has-dropdown {
    position: relative;
    cursor: pointer;
}

.has-dropdown .fa-chevron-down {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    transition: transform 0.3s ease;
}

.has-dropdown .fa-chevron-down.rotate {
    transform: translateY(-50%) rotate(180deg);
}

.parent {
    position: relative;
}

.submenu a {
    padding: 12px 25px 12px 50px !important;
    font-size: 14px;
    color: #999;
    display: block;
    transition: all 0.3s ease;
}

.submenu a:hover,
.submenu a.active {
    background: #333;
    color: #DAA520 !important;
}

.submenu a em {
    margin-right: 10px;
    color: #999;
}

.submenu a:hover em,
.submenu a.active em {
    color: #DAA520;
}

/* Ensure dropdowns work properly */
.nav-links .parent > a {
    padding-right: 40px !important;
}

.nav-links .submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    transition: none !important;
}

@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }
}

/* Added styles to fix submenu icons */
.sidebar ul.nav .parent > a { 
    padding: 10px 20px;
}
.sidebar ul.nav ul.children {
    padding: 0;
    margin: 0;
    background: #2f353a;
}
.sidebar ul.nav ul.children li a {
    padding: 10px 20px 10px 40px;
    color: #a8a8a8;
    display: block;
    position: relative;
}
.sidebar ul.nav ul.children li a:hover,
.sidebar ul.nav ul.children li a.active {
    color: #d4af37;
    background: transparent;
}
.sidebar ul.nav ul.children li a em {
    width: 20px;
    display: inline-block;
    text-align: center;
    margin-right: 5px;
}
.sidebar ul.nav ul.children li a.active em {
    color: #d4af37;
}
</style>

<script src="js/sidebar.js"></script>


