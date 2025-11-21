// Wait for the page to load
document.addEventListener('DOMContentLoaded', function() {
    setupDropdown();
});

function setupDropdown() {
    try {
        // Get the dropdown elements
        var dropdownToggle = document.querySelector('.dropdown-toggle');
        var dropdownMenu = document.querySelector('.dropdown-menu');
        var dropdownArrow = document.querySelector('.dropdown-arrow');

        // If any of the required elements are missing, exit the function
        if (!dropdownToggle || !dropdownMenu || !dropdownArrow) {
            return;
        }

        // Add click event to toggle
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Toggle the menu
            if (dropdownMenu.style.display === 'block') {
                dropdownMenu.style.display = 'none';
                if (dropdownArrow) dropdownArrow.classList.remove('rotated');
            } else {
                dropdownMenu.style.display = 'block';
                if (dropdownArrow) dropdownArrow.classList.add('rotated');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (dropdownToggle && dropdownMenu && 
                !dropdownToggle.contains(e.target) && 
                !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
                if (dropdownArrow) dropdownArrow.classList.remove('rotated');
            }
        });

        // Keep dropdown open if we're on a booking page
        var currentUrl = window.location.href;
        if ((currentUrl.includes('reservation') || 
             currentUrl.includes('table_booking') || 
             currentUrl.includes('event_booking')) &&
             dropdownMenu && dropdownArrow) {
            dropdownMenu.style.display = 'block';
            dropdownArrow.classList.add('rotated');
        }
    } catch (error) {
        console.error('Error initializing dropdown:', error);
    }
}
