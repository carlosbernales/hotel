// Function to check room availability for selected dates
function checkRoomAvailability() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (!checkInInput || !checkOutInput) {
        return;
    }

    const checkIn = checkInInput.value;
    const checkOut = checkOutInput.value;

    // Check if both dates are selected
    if (!checkIn || !checkOut) {
        clearAvailabilityStatus();
        return;
    }

    // Validate dates
    const checkInDate = new Date(checkIn);
    const checkOutDate = new Date(checkOut);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (checkInDate < today) {
        showAvailabilityAlert('error', 'Invalid Date', 'Check-in date cannot be in the past');
        return;
    }

    if (checkOutDate <= checkInDate) {
        showAvailabilityAlert('error', 'Invalid Dates', 'Check-out date must be after check-in date');
        return;
    }

    // Show loading indicator
    showAvailabilityLoading();

    // Make AJAX request to check availability using GET with query parameters
    const url = `check_room_availability.php?check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            displayAvailabilityStatus(data);
        })
        .catch(error => {
            console.error('Error checking availability:', error);
            showAvailabilityAlert('error', 'Error', 'Error checking room availability. Please try again.');
        });
}

// Function to display availability status
function displayAvailabilityStatus(data) {
    const availabilityContainer = document.getElementById('availabilityStatus');

    if (data.success) {
        const { summary } = data;
        const totalAvailable = summary.total_available_rooms || 0;

        if (totalAvailable === 0) {
            // All rooms fully booked
            updateAvailabilityDisplay(
                'alert-danger',
                '<i class="fas fa-times-circle"></i>',
                'Fully Booked',
                'Sorry, all rooms are fully booked for your selected dates. Please choose different dates or contact us for alternative arrangements.',
                true // disable booking
            );

            showAvailabilityAlert('error', 'Fully Booked', 'All rooms are fully booked for the selected dates.');

        } else {
            // Some availability
            const availableTypes = summary.available_types || [];
            const partiallyBookedTypes = summary.partially_booked_types || [];

            if (availableTypes.length > 0 && partiallyBookedTypes.length === 0) {
                // All selected room types fully available
                updateAvailabilityDisplay(
                    'alert-success',
                    '<i class="fas fa-check-circle"></i>',
                    'Available',
                    `Great! ${totalAvailable} room${totalAvailable > 1 ? 's' : ''} available. Available types: ${availableTypes.join(', ')}`,
                    false // enable booking
                );

                showAvailabilityAlert('success', 'Available', `${totalAvailable} room${totalAvailable > 1 ? 's' : ''} available for your selected dates.`);

            } else {
                // Partial availability
                updateAvailabilityDisplay(
                    'alert-warning',
                    '<i class="fas fa-exclamation-triangle"></i>',
                    'Limited Availability',
                    `${totalAvailable} room${totalAvailable > 1 ? 's' : ''} available. ${availableTypes.length > 0 ? 'Fully available: ' + availableTypes.join(', ') + '. ' : ''}${partiallyBookedTypes.length > 0 ? 'Limited availability: ' + partiallyBookedTypes.join(', ') + '.' : ''}`,
                    false // enable booking (user can still book available rooms)
                );

                showAvailabilityAlert('warning', 'Limited Availability', `${totalAvailable} room${totalAvailable > 1 ? 's' : ''} available for your selected dates.`);
            }
        }
    } else {
        updateAvailabilityDisplay(
            'alert-danger',
            '<i class="fas fa-exclamation-circle"></i>',
            'Check Failed',
            data.message || 'Could not check room availability',
            true // disable booking
        );

        showAvailabilityAlert('error', 'Check Failed', data.message || 'Could not check room availability');
    }
}

// Function to update availability display
function updateAvailabilityDisplay(alertClass, iconHtml, title, message, disableBooking) {
    const availabilityStatus = document.getElementById('availabilityStatus');
    const availabilityIcon = document.getElementById('availabilityIcon');
    const availabilityTitle = document.getElementById('availabilityTitle');
    const availabilityMessage = document.getElementById('availabilityMessage');

    if (availabilityStatus && availabilityIcon && availabilityTitle && availabilityMessage) {
        availabilityStatus.className = `alert ${alertClass}`;
        availabilityStatus.style.display = 'block';
        availabilityIcon.innerHTML = iconHtml;
        availabilityTitle.textContent = title;
        availabilityMessage.textContent = message;

        // Enable/disable booking form
        if (disableBooking) {
            disableBookingForm();
        } else {
            enableBookingForm();
        }
    }
}

// Function to show availability loading state
function showAvailabilityLoading() {
    updateAvailabilityDisplay(
        'alert-info',
        '<i class="fas fa-spinner fa-spin"></i>',
        'Checking Availability...',
        'Please wait while we check room availability for your selected dates.',
        false
    );
}

// Function to clear availability status
function clearAvailabilityStatus() {
    const availabilityStatus = document.getElementById('availabilityStatus');
    if (availabilityStatus) {
        availabilityStatus.style.display = 'none';
    }
    enableBookingForm();
}

// Function to show availability alerts using SweetAlert2
function showAvailabilityAlert(type, title, message) {
    if (typeof Swal !== 'undefined') {
        let icon = 'info';
        switch (type) {
            case 'success': icon = 'success'; break;
            case 'warning': icon = 'warning'; break;
            case 'error': icon = 'error'; break;
        }

        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: type === 'error' ? 5000 : 3000,
            timerProgressBar: true
        });
    } else {
        // Fallback to browser alert if Swal is not available
        alert(`${title}: ${message}`);
    }
}

// Function to disable booking form when no availability
function disableBookingForm() {
    const bookingForm = document.getElementById('bookingForm');
    const submitBtn = bookingForm ? bookingForm.querySelector('button[type="submit"]') : null;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-ban"></i> Booking Not Available';
        submitBtn.classList.remove('btn-warning');
        submitBtn.classList.add('btn-secondary');
    }
}

// Function to enable booking form
function enableBookingForm() {
    const bookingForm = document.getElementById('bookingForm');
    const submitBtn = bookingForm ? bookingForm.querySelector('button[type="submit"]') : null;

    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Complete Booking';
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-warning');
    }
}

// Add event listeners for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (checkInInput) {
        checkInInput.addEventListener('change', checkRoomAvailability);
    }

    if (checkOutInput) {
        checkOutInput.addEventListener('change', checkRoomAvailability);
    }

    // Also check on input events for real-time feedback
    if (checkInInput) {
        checkInInput.addEventListener('input', function() {
            // Clear status if dates are incomplete
            if (!this.value || !checkOutInput.value) {
                clearAvailabilityStatus();
            } else {
                // Check availability if both dates are filled
                checkRoomAvailability();
            }
        });
    }

    if (checkOutInput) {
        checkOutInput.addEventListener('input', function() {
            // Clear status if dates are incomplete
            if (!this.value || !checkInInput.value) {
                clearAvailabilityStatus();
            } else {
                // Check availability if both dates are filled
                checkRoomAvailability();
            }
        });
    }

    // Initial check if dates are already filled when page loads
    if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
        checkRoomAvailability();
    }
});

// Function to display availability status
function displayAvailabilityStatus(data) {
    const dateRow = document.querySelector('.row.mb-3').parentNode;
    let statusDiv = document.getElementById('availabilityStatus');

    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'availabilityStatus';
        statusDiv.className = 'col-12 mt-2';
        dateRow.appendChild(statusDiv);
    }

    if (data.success) {
        if (data.summary.total_available_rooms > 0) {
            statusDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Rooms Available!</strong> ${data.summary.total_available_rooms} room(s) available for your selected dates.
                    ${data.message}
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>No rooms available</strong> for your selected dates.
                    ${data.message}
                </div>
            `;

            // Disable the booking form submit button
            const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-ban"></i> No Rooms Available';
            }
        }
    } else {
        statusDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                ${data.message}
            </div>
        `;

        // Disable the booking form submit button
        const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-ban"></i> Booking Unavailable';
        }
    }
}

// Function to show availability loading state
function showAvailabilityLoading() {
    const dateRow = document.querySelector('.row.mb-3').parentNode;
    let statusDiv = document.getElementById('availabilityStatus');

    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'availabilityStatus';
        statusDiv.className = 'col-12 mt-2';
        dateRow.appendChild(statusDiv);
    }

    statusDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin"></i>
            Checking room availability...
        </div>
    `;
}

// Function to show availability error
function showAvailabilityError(message) {
    const dateRow = document.querySelector('.row.mb-3').parentNode;
    let statusDiv = document.getElementById('availabilityStatus');

    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'availabilityStatus';
        statusDiv.className = 'col-12 mt-2';
        dateRow.appendChild(statusDiv);
    }

    statusDiv.innerHTML = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            ${message}
        </div>
    `;

    // Disable the booking form submit button
    const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-ban"></i> Booking Unavailable';
    }
}

// Add event listeners for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (checkInInput) {
        checkInInput.addEventListener('change', checkRoomAvailability);
    }

    if (checkOutInput) {
        checkOutInput.addEventListener('change', checkRoomAvailability);
    }

    // Also check on input events for real-time feedback
    if (checkInInput) {
        checkInInput.addEventListener('input', function() {
            // Clear status if dates are incomplete
            if (!this.value || !checkOutInput.value) {
                const statusDiv = document.getElementById('availabilityStatus');
                if (statusDiv) {
                    statusDiv.remove();
                }
                // Re-enable submit button
                const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Complete Booking';
                }
            }
        });
    }

    if (checkOutInput) {
        checkOutInput.addEventListener('input', function() {
            // Clear status if dates are incomplete
            if (!this.value || !checkInInput.value) {
                const statusDiv = document.getElementById('availabilityStatus');
                if (statusDiv) {
                    statusDiv.remove();
                }
                // Re-enable submit button
                const submitBtn = document.querySelector('#bookingForm button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Complete Booking';
                }
            }
        });
    }
});
