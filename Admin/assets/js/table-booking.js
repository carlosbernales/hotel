function submitBooking(event) {
    event.preventDefault();
    
    // Get the form element
    const form = event.target;
    
    // Create FormData object
    const formData = new FormData(form);
    
    // Validate payment method
    const paymentMethod = formData.get('payment_method');
    if (!paymentMethod) {
        alert('Please select a payment method');
        return;
    }

    // Debug log
    console.log('Form Data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    // Submit the form using fetch
    fetch('/index.php/TableBookingController/processBooking', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            alert(data.message);
        } else {
            alert('Booking successful!');
            window.location.href = '/index.php/booking-confirmation';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your booking. Please try again.');
    });
}

function closeBookingModal() {
    // Close the modal or redirect as needed
    window.location.href = '/index.php/table-packages';
}

// Add event listener when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tableBookingForm');
    if (form) {
        form.addEventListener('submit', submitBooking);
    }
}); 