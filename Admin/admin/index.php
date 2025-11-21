<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Check if dashboard parameter is set
if (isset($_GET['dashboard'])) {
    // Include header and sidebar components
    include('header.php');
    include('sidebar.php');
    
    // Dashboard content will go here
    echo '<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">';
    echo '<div class="row">';
    echo '<ol class="breadcrumb">';
    echo '<li><a href="#"><i class="fa fa-home"></i></a></li>';
    echo '<li class="active">Dashboard</li>';
    echo '</ol>';
    echo '</div>';
    
    echo '<div class="row">';
    echo '<div class="col-lg-12">';
    echo '<h1 class="page-header">Admin Dashboard</h1>';
    echo '</div>';
    echo '</div>';
    
    // Dashboard widgets and statistics
    echo '<div class="row">';
    
    // Bookings widget
    echo '<div class="col-md-6 col-lg-3">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<div class="row">';
    echo '<div class="col-xs-3"><i class="fa fa-calendar fa-5x"></i></div>';
    echo '<div class="col-xs-9 text-right">';
    echo '<div class="huge">12</div>';
    echo '<div>New Bookings</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<a href="booking_status.php">';
    echo '<div class="panel-footer"><span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>';
    echo '<div class="clearfix"></div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    
    // Rooms widget
    echo '<div class="col-md-6 col-lg-3">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<div class="row">';
    echo '<div class="col-xs-3"><i class="fa fa-bed fa-5x"></i></div>';
    echo '<div class="col-xs-9 text-right">';
    echo '<div class="huge">24</div>';
    echo '<div>Rooms Available</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<a href="room_management.php">';
    echo '<div class="panel-footer"><span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>';
    echo '<div class="clearfix"></div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    
    // Messages widget
    echo '<div class="col-md-6 col-lg-3">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<div class="row">';
    echo '<div class="col-xs-3"><i class="fa fa-envelope fa-5x"></i></div>';
    echo '<div class="col-xs-9 text-right">';
    echo '<div class="huge">7</div>';
    echo '<div>New Messages</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<a href="message.php">';
    echo '<div class="panel-footer"><span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>';
    echo '<div class="clearfix"></div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    
    // Revenue widget
    echo '<div class="col-md-6 col-lg-3">';
    echo '<div class="panel panel-default">';
    echo '<div class="panel-heading">';
    echo '<div class="row">';
    echo '<div class="col-xs-3"><i class="fa fa-money fa-5x"></i></div>';
    echo '<div class="col-xs-9 text-right">';
    echo '<div class="huge">₱42K</div>';
    echo '<div>Revenue</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<a href="sales_report.php">';
    echo '<div class="panel-footer"><span class="pull-left">View Details</span> <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>';
    echo '<div class="clearfix"></div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // End dashboard widgets row
    
    echo '</div>'; // End main content div
} else {
    // If dashboard parameter is not set, redirect to include it
    header("Location: index.php?dashboard");
    exit();
}
?> 