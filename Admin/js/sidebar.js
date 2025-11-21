$(document).ready(function() {
    // Remove any existing click handlers and old functions
    $('.has-dropdown').off('click');
    
    // Simple dropdown toggle for both Bookings and Settings
    $('.has-dropdown').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $parentLi = $(this).closest('.parent');
        var $submenu = $parentLi.find('.submenu');
        var $arrow = $(this).find('.fa-chevron-down');
        
        // Close other dropdowns
        $('.parent').not($parentLi).find('.submenu').slideUp();
        $('.parent').not($parentLi).find('.fa-chevron-down').removeClass('rotate');
        $('.parent').not($parentLi).removeClass('active');
        
        // Toggle current dropdown
        $submenu.slideToggle();
        $arrow.toggleClass('rotate');
        $parentLi.toggleClass('active');
    });

    // Keep active menu open
    var currentUrl = window.location.href;
    $('.submenu a').each(function() {
        var menuUrl = $(this).attr('href');
        if (currentUrl.indexOf(menuUrl) !== -1) {
            var $parentLi = $(this).closest('.parent');
            $(this).addClass('active');
            $parentLi.addClass('active');
            $parentLi.find('.submenu').show();
            $parentLi.find('.fa-chevron-down').addClass('rotate');
        }
    });

    // Prevent submenu clicks from closing dropdown
    $('.submenu').on('click', function(e) {
        e.stopPropagation();
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.sidebar').length) {
            $('.submenu').slideUp();
            $('.fa-chevron-down').removeClass('rotate');
            $('.parent').removeClass('active');
        }
    });

    // Initialize active states for both Bookings and Settings
    if (currentUrl.includes('reservation') || 
        currentUrl.includes('table_packages') || 
        currentUrl.includes('event_booking')) {
        var $bookingsParent = $('.parent:has(a:contains("Bookings"))');
        $bookingsParent.addClass('active');
        $bookingsParent.find('.submenu').show();
        $bookingsParent.find('.fa-chevron-down').addClass('rotate');
    }

    if (currentUrl.includes('settings') || 
        currentUrl.includes('management')) {
        var $settingsParent = $('.parent:has(a:contains("Settings"))');
        $settingsParent.addClass('active');
        $settingsParent.find('.submenu').show();
        $settingsParent.find('.fa-chevron-down').addClass('rotate');
    }
});
